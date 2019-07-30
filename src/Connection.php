<?php

namespace Pheanstalk;

use DOMXPath as DOMXPath;
use Pheanstalk\Socket\NativeSocket;

/**
 * A connection to a beanstalkd server.
 *
 * @author  Paul Annesley
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class Connection
{
    const CRLF = "\r\n";
    const CRLF_LENGTH = 2;
    const DEFAULT_CONNECT_TIMEOUT = 2;

    // responses which are global errors, mapped to their exception short-names
    private static $_errorResponses = [
        Response::RESPONSE_OUT_OF_MEMORY                    => 'OutOfMemory',
        Response::RESPONSE_INTERNAL_ERROR                   => 'InternalError',
        Response::RESPONSE_DRAINING                         => 'Draining',
        Response::RESPONSE_BAD_FORMAT                       => 'BadFormat',
        Response::RESPONSE_UNKNOWN_COMMAND                  => 'UnknownCommand',
        Response::RESPONSE_WORKFLOW_ALREADY_EXISTS          => 'DuplicateEntry',
        Response::RESPONSE_SERVER_ERROR                     => '',
    ];

    // responses which are followed by data
    private static $_dataResponses = [
        Response::RESPONSE_RESERVED,
        Response::RESPONSE_FOUND,
        Response::RESPONSE_OK,
    ];

    private $_socket;
    private $_hostname;
    private $_port;
    private $_connectTimeout;
    private $_connectPersistent;
    protected $user;
    protected $password;

    /**
     * @param string $hostname
     * @param string $user
     * @param string $password
     * @param int    $port
     * @param float  $connectTimeout
     * @param bool   $connectPersistent
     */
    public function __construct($hostname, $user = null, $password = null, $port = 5000, $connectTimeout = null, $connectPersistent = false)
    {
        if (is_null($connectTimeout) || !is_numeric($connectTimeout)) {
            $connectTimeout = self::DEFAULT_CONNECT_TIMEOUT;
        }

        $this->_hostname = $hostname;
        $this->_port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->_connectTimeout = $connectTimeout;
        $this->_connectPersistent = $connectPersistent;
    }

    /**
     * @return bool
     */
    public function hasSocket()
    {
        return isset($this->_socket);
    }

    /**
     * Disconnect the socket.
     * Subsequent socket operations will create a new connection.
     */
    public function disconnect()
    {
        $this->getSocket()->disconnect();
        $this->_socket = null;
    }

    /**
     * @param Command $command
     *
     * @throws Exception\ClientException
     *
     * @return mixed
     */
    public function dispatchCommand(Command $command)
    {
        $socket = $this->getSocket();

        $dom = $this->build_query($command->getGroup(), $command->getAction(), $command->getFilters(), $command->getParameters());
        $xml = $dom->saveXML();
        $socket->write($xml);

        $responseLine = $socket->getLine();
        $xml = new \SimpleXMLElement($responseLine);
        $json = json_encode($xml);
        $responseLine = json_decode($json, true);
        $responseName = preg_replace('#^(\S+).*$#s', '$1', $responseLine["@attributes"]['status']);
        if ($responseName === "KO") {
            $exceptionType = $responseLine['@attributes']['error-code'] ?? Response::RESPONSE_SERVER_ERROR;
            $exception = sprintf(
                '\Pheanstalk\Exception\Server%sException',
                self::$_errorResponses[$exceptionType] ?? ''
            );
            throw new $exception(sprintf(
                "%s while executing %s:%s",
                $responseLine['@attributes']['error'],
                $command->getGroup(),
                $command->getAction()
            ));
        }


        $data = $responseLine;


        return $command
            ->getResponseParser()
            ->parseResponse($responseLine["@attributes"]['status'], $data);
    }

    /**
     * Returns the connect timeout for this connection.
     *
     * @return float
     */
    public function getConnectTimeout()
    {
        return $this->_connectTimeout;
    }

    /**
     * Returns the host for this connection.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->_hostname;
    }

    /**
     * Returns the port for this connection.
     *
     * @return int
     */
    public function getPort()
    {
        return $this->_port;
    }

    // ----------------------------------------

    /**
     * Socket handle for the connection to beanstalkd.
     *
     * @throws Exception\ConnectionException
     *
     * @return Socket
     */
    public function getSocket()
    {
        if (!isset($this->_socket)) {
            $this->_socket = new NativeSocket(
                $this->_hostname,
                $this->_port,
                $this->_connectTimeout,
                $this->_connectPersistent
            );
            $response = $this->_socket->getLine();
            $xml = new \SimpleXMLElement($response);
            $challenge = (string) $xml['challenge'];
            if (!empty($challenge)) {
                $hmac = hash_hmac("sha1", hex2bin($challenge), sha1($this->password, true));
                $dom = $this->build_query('auth', false, ["response" => $hmac, "user" => $this->user]);
                $this->_socket->write($dom->saveXML());
                $recv = $this->_socket->getLine();
            }
        }

        return $this->_socket;
    }

    /**
     * @param NativeSocket $socket
     */
    public function setSocket(NativeSocket $socket)
    {
        $this->_socket = $socket;
    }

    /**
     * @param       $name
     * @param bool  $action
     * @param array $attributes
     * @param array $parameters
     *
     * @return \DOMDocument
     */
    protected function build_query($name, $action = false, $attributes = [], $parameters = [])
    {
        $dom = new \DOMDocument("1.0", "utf-8");
        $root = $dom->createElement($name);
        if ($action) {
            $root->setAttribute('action', $action);
        }
        foreach ($attributes as $key => $value) {
            $root->setAttribute($key, $value);
        }
        foreach ($parameters as $parameter => $value) {
            $param = $dom->createElement('parameter');
            $param->setAttribute('name', $parameter);
            $param->setAttribute('value', $value);
            $root->appendChild($param);
        }
        $dom->appendChild($root);
        return $dom;
    }

    /**
     * Checks connection to the beanstalkd socket.
     *
     * @return true|false
     */
    public function isServiceListening()
    {
        try {
            $this->getSocket();

            return true;
        } catch (Exception\ConnectionException $e) {
            return false;
        }
    }
}

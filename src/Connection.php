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
    private static $_errorResponses = array(
        Response::RESPONSE_OUT_OF_MEMORY                    => 'OutOfMemory',
        Response::RESPONSE_INTERNAL_ERROR                   => 'InternalError',
        Response::RESPONSE_DRAINING                         => 'Draining',
        Response::RESPONSE_BAD_FORMAT                       => 'BadFormat',
        Response::RESPONSE_UNKNOWN_COMMAND                  => 'UnknownCommand',
        Response::RESPONSE_WORKFLOW_ALREADY_EXISTS          => 'DuplicateEntry',
        Response::RESPONSE_SERVER_ERROR                     => '',
    );

    // responses which are followed by data
    private static $_dataResponses = array(
        Response::RESPONSE_RESERVED,
        Response::RESPONSE_FOUND,
        Response::RESPONSE_OK,
    );

    private $_socket;
    private $_hostname;
    private $_port;
    private $_connectTimeout;
    private $_connectPersistent;

    /**
     * @param string $hostname
     * @param int    $port
     * @param float  $connectTimeout
     * @param bool   $connectPersistent
     */
    public function __construct($hostname, $port, $connectTimeout = null, $connectPersistent = false)
    {
        if (is_null($connectTimeout) || !is_numeric($connectTimeout)) {
            $connectTimeout = self::DEFAULT_CONNECT_TIMEOUT;
        }

        $this->_hostname = $hostname;
        $this->_port = $port;
        $this->_connectTimeout = $connectTimeout;
        $this->_connectPersistent = $connectPersistent;
    }

    /**
     * Sets a manually created socket, used for unit testing.
     *
     * @param Socket $socket
     *
     * @return $this
     */
    public function setSocket(Socket $socket)
    {
        $this->_socket = $socket;

        return $this;
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
        $this->_getSocket()->disconnect();
        $this->_socket = null;
    }

    /**
     * Connect the socket.
     * Subsequent socket operations will create a new connection.
     */
    public function connect()
    {
        $this->_getSocket()->disconnect();
        $this->_socket = null;
    }

    /**
     * @param object $command Command
     *
     * @throws Exception\ClientException
     *
     * @return object Response
     */
    public function dispatchCommand($command)
    {
        $socket = $this->_getSocket();

        $name = $command->getCommandLine();

        if ($command->hasData()) {
            $action = ($command->getData()['action']) ?? false;
            $attributes = ($command->getData()['attributes']) ?? [];
            $parameters = ($command->getData()['parameters']) ?? [];
        } else {
            $action = false;
            $attributes = [];
            $parameters = [];
        }
        $dom = $this->build_query($name, $action, $attributes, $parameters);
        $xml = $dom->saveXML();
        $socket->write($xml);

        $responseLine = $socket->getLine();
        $xml = simplexml_load_string($responseLine);
        $json = json_encode($xml);
        $responseLine = json_decode($json,TRUE);
//        dump($responseLine);
        $responseName = preg_replace('#^(\S+).*$#s', '$1', $responseLine["@attributes"]['status']);
        if ($responseName === "KO") {
            $exceptionType = $responseLine['@attributes']['error-code'] ?? Response::RESPONSE_SERVER_ERROR;
            $exception = sprintf(
                '\Pheanstalk\Exception\Server%sException',
                self::$_errorResponses[$exceptionType] ?? ''
            );
            throw new $exception(sprintf(
                "%s while executing %s : %s",
                $responseLine['@attributes']['error'],
                $command,
                $action
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
    private function _getSocket()
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
            $hmac = hash_hmac("sha1", hex2bin($challenge), sha1('admin', true));
            $dom = $this->build_query('auth', false, ["response" => $hmac, "user" => 'admin']);
            $this->_socket->write($dom->saveXML());
            $recv = $this->_socket->getLine();
        }

        return $this->_socket;
    }

    protected function build_query($name, $action = false, $attributes = [], $parameters = []){
        $dom = new \DOMDocument("1.0", "utf-8");
        $root = $dom->createElement($name);
        if($action)
            $root->setAttribute('action', $action);
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
            $this->_getSocket();

            return true;
        } catch (Exception\ConnectionException $e) {
            return false;
        }
    }
}

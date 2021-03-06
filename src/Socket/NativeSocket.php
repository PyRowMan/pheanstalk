<?php

namespace Pheanstalk\Socket;

use Pheanstalk\Exception;
use Pheanstalk\Socket;

/**
 * A Socket implementation around a fsockopen() stream.
 *
 * @author  Paul Annesley
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class NativeSocket implements Socket
{
    /**
     * The default timeout for a blocking read on the socket.
     */
    const SOCKET_TIMEOUT = 1;

    /**
     * Number of retries for attempted writes which return zero length.
     */
    const WRITE_RETRIES = 8;

    private $_socket;

    /**
     * @param string $host
     * @param int    $port
     * @param int    $connectTimeout
     * @param bool   $connectPersistent
     */
    public function __construct($host, $port, $connectTimeout, $connectPersistent)
    {
        if ($connectPersistent) {
            $this->_socket = $this->_wrapper()
                ->pfsockopen($host, $port, $errno, $errstr, $connectTimeout);
        } else {
            $this->_socket = $this->_wrapper()
                ->fsockopen($host, $port, $errno, $errstr, $connectTimeout);
        }

        if (!$this->_socket) {
            throw new Exception\ConnectionException($errno, $errstr." (connecting to $host:$port)");
        }

        $this->_wrapper()
            ->stream_set_timeout($this->_socket, self::SOCKET_TIMEOUT);
    }

    /* (non-phpdoc)
     * @see Socket::write()
     */
    public function write($data)
    {
        $history = new WriteHistory(self::WRITE_RETRIES);

        for ($written = 0, $fwrite = 0; $written < strlen($data); $written += $fwrite) {
            $fwrite = $this->_wrapper()
                ->fwrite($this->_socket, substr($data, $written));

            $history->log($fwrite);

            if ($history->isFullWithNoWrites()) {
                throw new Exception\SocketException(sprintf(
                    'fwrite() failed to write data after %u tries',
                    self::WRITE_RETRIES
                ));
            }
        }
    }

    /**
     * Request a socket until the returned datas are a valid xml string
     * @param null $length
     *
     * @return string
     * @throws Exception\SocketException
     */
    public function getLine($length = null)
    {
        $timeout = (int) 5;
        $timer   = microtime(true);
        $data = '';
        libxml_use_internal_errors(true);
        do {
            libxml_clear_errors();
                $data .= isset($length) ?
                    $this->_wrapper()->fgets($this->_socket, $length) : $this->_wrapper()->fgets($this->_socket);
            simplexml_load_string($data);
            if (!empty(libxml_get_errors()) && microtime(true) - $timer > $timeout) {
                $this->disconnect();
                throw new Exception\SocketException('Socket timed out!');
            } elseif (empty(libxml_get_errors())) {
                try {
                    $xml = new \SimpleXMLElement($data);
                } catch (\Exception $e) {
                    $error = (string) (isset($xml['error'])) ? $xml['error'] : 'Socket closed by server!';
                    throw new Exception\SocketException($error);
                }
            }
        } while (!empty(libxml_get_errors()));

        libxml_clear_errors();

        return rtrim($data);
    }

    public function disconnect()
    {
        $this->_wrapper()->fclose($this->_socket);
    }

    // ----------------------------------------

    /**
     * Wrapper class for all stream functions.
     * Facilitates mocking/stubbing stream operations in unit tests.
     */
    private function _wrapper()
    {
        return StreamFunctions::instance();
    }
}

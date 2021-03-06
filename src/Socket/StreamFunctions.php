<?php

namespace Pheanstalk\Socket;

/**
 * Wrapper around PHP stream functions.
 *
 * Facilitates mocking/stubbing stream operations in unit tests.
 *
 * @author  Paul Annesley
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class StreamFunctions
{
    private static $_instance;

    /**
     * Singleton accessor.
     */
    public static function instance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    // ----------------------------------------

    public function fgets($handle, $length = null)
    {
        if (isset($length)) {
            return fgets($handle, $length);
        } else {
            return fgets($handle);
        }
    }

    public function fsockopen($hostname, $port = -1, &$errno = null, &$errstr = null, $timeout = null)
    {
        return @fsockopen($hostname, $port, $errno, $errstr, $timeout);
    }

    public function pfsockopen($hostname, $port = -1, &$errno = null, &$errstr = null, $timeout = null)
    {
        return @pfsockopen($hostname, $port, $errno, $errstr, $timeout);
    }

    public function fwrite($handle, $string)
    {
        return fwrite($handle, $string);
    }

    public function fclose($handle)
    {
        fclose($handle);
    }

    public function stream_set_timeout($stream, $seconds, $microseconds = 0)
    {
        return stream_set_timeout($stream, $seconds, $microseconds);
    }
}

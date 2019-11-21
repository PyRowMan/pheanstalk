<?php

namespace Pheanstalk\Parser;

use Pheanstalk\ResponseParser;

/**
 * A response parser for commands that return a subset of XML.
 *
 * Expected response is 'OK', 'NOT_FOUND' response is also handled.
 * Parser expects either a YAML list or dictionary, depending on mode.
 *
 * @author  Paul Annesley
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class RequestOkResponseParser implements ResponseParser
{

    /**
     * @param string $responseLine
     * @param array $responseData
     *
     * @return bool
     */
    public function parseResponse($responseLine, $responseData)
    {
        return $responseLine === 'OK';
    }
}

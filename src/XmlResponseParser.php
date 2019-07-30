<?php

namespace Pheanstalk;

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
class XmlResponseParser implements \Pheanstalk\ResponseParser
{

    /**
     * @param string $responseLine
     * @param array $responseData
     *
     * @throws Exception\ServerException
     * @return array
     */
    public function parseResponse($responseLine, $responseData)
    {
        if ($responseLine == Response::RESPONSE_NOT_FOUND) {
            throw new Exception\ServerException(sprintf(
                'Server reported %s',
                $responseLine
            ));
        }
        unset($responseData['@attributes']);
        return $responseData;
    }
}

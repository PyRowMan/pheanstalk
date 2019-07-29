<?php

namespace Pheanstalk\Command;

use Pheanstalk\Exception;
use Pheanstalk\Response;
use Pheanstalk\XmlResponseParser;

/**
 * The 'peek', 'peek-ready', 'peek-delayed' and 'peek-buried' commands.
 *
 * The peek commands let the client inspect a job in the system. There are four
 * variations. All but the first (peek) operate only on the currently used tube.
 *
 * @author  Paul Annesley
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class PeekCommand extends AbstractCommand implements \Pheanstalk\ResponseParser
{

    /**
     * @inheritDoc
     */
    public function getGroup(): string
    {
        return 'status';
    }

    /**
     * @inheritDoc
     */
    public function getAction(): string
    {
        return 'query';
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return [
            'type' => "workflows"
        ];
    }

    /**
     * @inheritDoc
     */
    public function getResponseParser()
    {
        return $this;
    }

    /**
     * @param string $responseLine
     * @param string $responseData
     *
     * @return object
     * @throws Exception\ServerException
     */
    public function parseResponse($responseLine, $responseData)
    {
        unset($responseData['@attributes']);
        if (!isset($responseData['workflow'])) {
                $message = "There are no workflow yet";

            throw new Exception\ServerException($message);
        }

        $responseData = $responseData['workflow'];
        $responseData = array_column($responseData, '@attributes');
        $dates = array_column($responseData, 'start_time');
        $mostRecent = [];
        foreach ($responseData as $date) {
            $curDate = strtotime($date['start_time']);
            if (!isset($mostRecent['start_time']) || $curDate < strtotime($mostRecent['start_time'])) {
                $mostRecent = $date;
            }
        }
        if (empty($responseData)) {
            return $this->parseResponse($responseLine, $responseData);
        }
        return $this->_createResponse(
            Response::RESPONSE_FOUND,
            [
                'id'      => (int) $mostRecent['id'],
                'jobdata' => $mostRecent,
            ]
        );
    }
}

<?php

namespace Pheanstalk\Command;

use Pheanstalk\Exception;
use Pheanstalk\Response;
use Pheanstalk\ResponseParser;

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
class PeekCommand extends AbstractCommand implements ResponseParser
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
     * @return array|bool
     * @throws Exception\ServerException
     */
    public function parseResponse($responseLine, $responseData)
    {
        unset($responseData['@attributes']);
        if (!isset($responseData['workflow'])) {
            return false;
        }
        $responseData = $responseData['workflow'];
        if (isset($responseData['@attributes'])) {
            return $this->constructResponse($responseData['@attributes']);
        }
        $responseData = array_column($responseData, '@attributes');
        $mostRecent = $this->getMostRecentFromArray($responseData);
        return $this->constructResponse($mostRecent);
    }

    /**
     * @param $response
     *
     * @return array
     */
    protected function constructResponse($response)
    {
        return [
            Response::RESPONSE_FOUND,
            [
                'id'      => (int) $response['id'],
                'jobdata' => $response,
            ]
        ];
    }

    /**
     * @param array $datas
     *
     * @return array
     */
    protected function getMostRecentFromArray(array $datas)
    {
        $mostRecent = [];
        foreach ($datas as $data) {
            $curDate = strtotime($data['start_time']);
            if (!isset($mostRecent['start_time']) || $curDate < strtotime($mostRecent['start_time'])) {
                $mostRecent = $data;
            }
        }
        return $mostRecent;
    }
}

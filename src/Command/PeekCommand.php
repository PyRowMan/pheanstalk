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
class PeekCommand
    extends AbstractCommand
    implements \Pheanstalk\ResponseParser
{
    const TYPE_ID = 'id';
    const TYPE_READY = 'ready';
    const TYPE_DELAYED = 'delayed';
    const TYPE_BURIED = 'buried';

    private $_subcommands = array(
        self::TYPE_READY,
        self::TYPE_DELAYED,
        self::TYPE_BURIED,
    );

    private $_subcommand;
    private $_jobId;

    /**
     * @param mixed $peekSubject Job ID or self::TYPE_*
     */
    public function __construct($peekSubject)
    {
        if (is_int($peekSubject) || ctype_digit($peekSubject)) {
            $this->_jobId = $peekSubject;
        } elseif (in_array($peekSubject, $this->_subcommands)) {
            $this->_subcommand = $peekSubject;
        } else {
            throw new Exception\CommandException(sprintf(
                'Invalid peek subject: %s', $peekSubject
            ));
        }
    }

    /* (non-phpdoc)
     * @see Command::getCommandLine()
     */
    public function getCommandLine()
    {
//        return isset($this->_jobId) ?
//            sprintf('peek %u', $this->_jobId) :
//            sprintf('peek-%s', $this->_subcommand);
        return 'status';
    }

    /* (non-phpdoc)
     * @see Command::getResponseParser()
     */
    public function getResponseParser()
    {
        return $this;
    }

    public function getData()
    {
        return [
            'action' => 'query',
            "attributes" => [
                'type' => "workflows"
            ],
//            "parameters" => [
//                "STATUS" => "EXECUTING"
//            ]
        ];
    }

    public function hasData()
    {
        return true;
    }

    /* (non-phpdoc)
     * @see ResponseParser::parseResponse()
     */
    public function parseResponse($responseLine, $responseData)
    {
        unset($responseData['@attributes']);
        if (!isset($responseData['workflow'])) {
            if (isset($this->_jobId)) {
                $message = sprintf(
                    '%s: Job %u does not exist.',
                    $responseLine,
                    $this->_jobId
                );
            } else {
                $message = sprintf(
                    "%s: There are no jobs in the '%s' status",
                    $responseLine,
                    $this->_subcommand
                );
            }

            throw new Exception\ServerException($message);
        } elseif (preg_match('#^OK$#', $responseLine, $matches)) {
            $responseData = $responseData['workflow'];
            $responseData = array_column($responseData, '@attributes');
            $dates = array_column($responseData, 'start_time');
            $mostRecent = [];
            foreach($responseData as $date){
                $curDate = strtotime($date['start_time']);
                if (!isset($mostRecent['start_time']) || $curDate > strtotime($mostRecent['start_time'])) {
                    $mostRecent = $date;
                }
            }
            if (empty($responseData)) return $this->parseResponse($responseLine, $responseData);
            return $this->_createResponse(
                Response::RESPONSE_FOUND,
                array(
                    'id'      => (int) $mostRecent['id'],
                    'jobdata' => $mostRecent,
                )
            );
        }
    }
}

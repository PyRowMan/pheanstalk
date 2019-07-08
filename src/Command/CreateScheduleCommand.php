<?php

namespace Pheanstalk\Command;

use Pheanstalk\Exception;
use Pheanstalk\Response;
use Pheanstalk\Structure\TimeSchedule;
use Pheanstalk\Structure\Workflow;
use Pheanstalk\XmlResponseParser;

/**
 * The 'CreateSchedule' command.
 *
 * Inserts a new workflow_schedule into the client.
 *
 *
 * @author  Valentin Corre
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class CreateScheduleCommand extends AbstractCommand implements \Pheanstalk\ResponseParser
{

    const FAILURE_TYPE_CONTINUE = "CONTINUE";
    const FAILURE_TYPE_SUSPEND = "SUSPEND";

    /** @var Workflow $workflow */
    private $workflow;

    private $schedule;

    private $onFailure;

    /** @var bool $active */
    private $active;

    /** @var string|null $comment */
    private $comment;

    /**
     * CreateScheduleCommand constructor.
     *
     * @param Workflow     $workflow
     * @param TimeSchedule $schedule
     * @param string       $onFailure
     * @param bool         $active
     * @param string|null         $comment
     */
    public function __construct(Workflow $workflow, TimeSchedule $schedule, $onFailure = self::FAILURE_TYPE_CONTINUE, $active = true, $comment = null)
    {
        $this->workflow = $workflow;
        $this->schedule = $schedule;
        $this->onFailure = $onFailure ?? self::FAILURE_TYPE_CONTINUE;
        $this->active = $active ?? true ;
        $this->comment = $comment ?? $this->workflow->getComment();
    }

    /* (non-phpdoc)
     * @see Command::getCommandLine()
     */
    public function getCommandLine()
    {
        return 'workflow_schedule';
    }

    public function getData()
    {
        return [
            'action' => 'create',
            'attributes' => [
                'workflow_id' => $this->workflow->getId(),
                'schedule' => $this->schedule->__toString(),
                "onfailure" => $this->onFailure,
                'active' => $this->active,
                'comment' => $this->comment,
                'node' => "any"
            ]
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
        return (int) $responseData['@attributes']['schedule-id'];
    }
}

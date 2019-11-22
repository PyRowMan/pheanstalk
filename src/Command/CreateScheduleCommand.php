<?php

namespace Pheanstalk\Command;

use Pheanstalk\ResponseParser;
use Pheanstalk\Structure\Schedule;
use Pheanstalk\Structure\TimeSchedule;
use Pheanstalk\Structure\Workflow;

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
class CreateScheduleCommand extends AbstractCommand implements ResponseParser
{

    const FAILURE_TYPE_CONTINUE = "CONTINUE";
    const FAILURE_TYPE_SUSPEND = "SUSPEND";

    /** @var Schedule $schedule */
    private $schedule;


    /**
     * CreateScheduleCommand constructor.
     *
     * @param Schedule     $schedule
     */
    public function __construct(Schedule $schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * @inheritDoc
     */
    public function getGroup(): string
    {
        return 'workflow_schedule';
    }

    /**
     * @inheritDoc
     */
    public function getAction(): string
    {
        return 'create';
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return[
            'workflow_id' => $this->schedule->getWorkflow(),
            'schedule' => $this->schedule->getSchedule()->__toString(),
            "onfailure" => $this->schedule->getOnFailure(),
            'active' => $this->schedule->isActive(),
            'comment' => $this->schedule->getComment(),
            'node' => $this->schedule->getNode()
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
     * @inheritDoc
     */
    public function parseResponse($responseLine, $responseData)
    {
        $this->schedule->setId((int) $responseData['@attributes']['schedule-id']);
        return $this->schedule;
    }
}

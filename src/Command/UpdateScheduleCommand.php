<?php

namespace Pheanstalk\Command;

use Pheanstalk\ResponseParser;
use Pheanstalk\Structure\Schedule;
use Pheanstalk\Structure\Workflow;

/**
 * The 'update' command.
 *
 * Update an existing Schedule.
 *
 * @author  Valentin Corre
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class UpdateScheduleCommand extends AbstractCommand implements ResponseParser
{
    /** @var Schedule $schedule */
    private $schedule;

    /**
     * @param Schedule $schedule
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
        return 'edit';
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return [
            'id' => $this->schedule->getId(),
            'workflow_id' => $this->schedule->getWorkflow(),
            'schedule' => $this->schedule->getSchedule()->__toString(),
            'onfailure' => $this->schedule->getOnFailure(),
            'user' => $this->schedule->getUser(),
            'host' => $this->schedule->getHost(),
            'active' => $this->schedule->isActive(),
            "comment" => $this->schedule->getComment(),
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
        return $this->schedule;
    }
}

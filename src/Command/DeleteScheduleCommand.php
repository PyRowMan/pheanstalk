<?php

namespace Pheanstalk\Command;

use Pheanstalk\Parser\RequestOkResponseParser;
use Pheanstalk\Structure\Schedule;
use Pheanstalk\Structure\Workflow;

/**
 * The 'deleteSchedule' command.
 *
 * Permanently deletes an already-reserved job.
 *
 * @author  Valentin Corre
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class DeleteScheduleCommand extends AbstractCommand
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
        return 'delete';
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return [
            'id' => $this->schedule->getId()
        ];
    }

    /**
     * @inheritDoc
     */
    public function getResponseParser()
    {
        return new RequestOkResponseParser();
    }
}

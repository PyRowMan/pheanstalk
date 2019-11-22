<?php

namespace Pheanstalk\Command;

use Pheanstalk\Parser\RequestOkResponseParser;
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
    /** @var int $scheduleId */
    private $scheduleId;

    /**
     * @param int $scheduleId
     */
    public function __construct(int $scheduleId)
    {
        $this->scheduleId = $scheduleId;
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
            'id' => $this->scheduleId
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

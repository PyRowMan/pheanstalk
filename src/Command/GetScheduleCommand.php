<?php

namespace Pheanstalk\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Pheanstalk\ResponseParser;
use Pheanstalk\Structure\Job;
use Pheanstalk\Structure\Schedule;
use Pheanstalk\Structure\Task;
use Pheanstalk\Structure\TimeSchedule;
use Pheanstalk\Structure\Workflow;

/**
 * The 'GetSchedule' command.
 *
 * Retrieve a scheduled workflow by its id, if there is no workflow for the id given in the construct, returns false
 *
 * @author  Valentin Corre
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class GetScheduleCommand extends AbstractCommand implements ResponseParser
{

    /** @var int $scheduleId */
    private $scheduleId;

    /**
     * GetWorkflowCommand constructor.
     *
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
        return 'get';
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
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseResponse($responseLine, $responseData)
    {
        $scheduleDatas = $responseData['workflow_schedule'];
        $scheduleDatas = $scheduleDatas['@attributes'] ?? $scheduleDatas;
        return new Schedule(
            (int) $scheduleDatas['workflow_id'],
            (new TimeSchedule())->__fromString($scheduleDatas['schedule']),
            $scheduleDatas['onfailure'],
            $scheduleDatas['active'],
            $scheduleDatas['comment'],
            $scheduleDatas['user'],
            $scheduleDatas['host'],
            $scheduleDatas['node']
        );
    }
}

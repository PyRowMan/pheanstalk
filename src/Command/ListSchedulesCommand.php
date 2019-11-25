<?php

namespace Pheanstalk\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Pheanstalk\ResponseParser;
use Pheanstalk\Structure\Workflow;

/**
 * The 'ListSchedulesCommand' command.
 *
 * List workflow schedules .
 *
 *
 * @author  Valentin Corre
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class ListSchedulesCommand extends GetScheduleCommand implements ResponseParser
{

    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function getGroup(): string
    {
        return 'workflow_schedules';
    }

    /**
     * @inheritDoc
     */
    public function getAction(): string
    {
        return 'list';
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
        $schedules = [];
        foreach ($responseData['workflow_schedule'] as $scheduleDatas) {
            $scheduleDatas = $scheduleDatas['@attributes'] ?? $scheduleDatas;
            $this->scheduleId = $scheduleDatas['id'];
            $schedule = $this->createScheduleFromArray($scheduleDatas);
            $schedules[] = $schedule;
        }
        return new ArrayCollection($schedules);
    }
}

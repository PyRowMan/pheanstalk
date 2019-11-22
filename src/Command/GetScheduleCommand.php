<?php

namespace Pheanstalk\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Pheanstalk\ResponseParser;
use Pheanstalk\Structure\Job;
use Pheanstalk\Structure\Task;
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
        var_dump($responseData);exit;
//        $workflow = $responseData['workflow'];
//        $jobs = $workflow['workflow']['subjobs'];
//        $workflow = $workflow['@attributes'] ?? $workflow;
//        $jobObjects = [];
//        foreach ($jobs as $job) {
//            $taskObjects = [];
//            foreach ($job['tasks'] as $task) {
//                $task = $task['@attributes'];
//                $taskObjects[] = new Task($task['path'], $task['queue'], $task['use-agent'], $task['user'], $task['host'], $task['output-method'], $task['parameters-mode']);
//            }
//            $jobObjects[] = new Job(new ArrayCollection($taskObjects));
//        }
//        $this->workflow->setJobs(new ArrayCollection($jobObjects));
//
//        return $this->workflow;
    }
}

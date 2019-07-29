<?php

namespace Pheanstalk\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Pheanstalk\Structure\Job;
use Pheanstalk\Structure\Task;
use Pheanstalk\Structure\Workflow;

/**
 * The 'GetWorkflow' command.
 *
 * Retrieve a workflow by its id, if there is no workflow for the id given in the construct, returns false
 *
 * @author  Valentin Corre
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class GetWorkflowCommand extends AbstractCommand implements \Pheanstalk\ResponseParser
{

    /** @var Workflow $workflow */
    private $workflow;

    /**
     * GetWorkflowCommand constructor.
     *
     * @param Workflow $workflow
     */
    public function __construct(Workflow $workflow)
    {
        $this->workflow = $workflow;
    }

    /**
     * @inheritDoc
     */
    public function getGroup(): string
    {
        return 'workflow';
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
            'id' => $this->workflow->getId()
        ];
    }

    /**
     * @inheritDoc
     */
    public function parseResponse($responseLine, $responseData)
    {
        $workflow = $responseData['workflow'];
        $jobs = $workflow['workflow']['subjobs'];
        $workflow = $workflow['@attributes'] ?? $workflow;
        $jobObjects = [];
        foreach ($jobs as $job) {
            $taskObjects = [];
            foreach ($job['tasks'] as $task) {
                $task = $task['@attributes'];
                $taskObjects[] = new Task($task['path'], $task['queue'], $task['use-agent'], $task['user'], $task['host'], $task['output-method'], $task['parameters-mode']);
            }
            $jobObjects[] = new Job(new ArrayCollection($taskObjects));
        }
        $this->workflow->setJobs(new ArrayCollection($jobObjects));

        return $this->workflow;
    }
}

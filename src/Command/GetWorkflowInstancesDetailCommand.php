<?php

namespace Pheanstalk\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Pheanstalk\Structure\JobInstance;
use Pheanstalk\Structure\TaskInstance;
use Pheanstalk\Structure\Workflow;
use Pheanstalk\Structure\WorkflowInstance;

/**
 * The 'GetWorkflow' command.
 *
 * Retrieve a workflow by its id, if there is no workflow for the id given in the construct, returns false
 *
 * @author  Valentin Corre
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class GetWorkflowInstancesDetailCommand extends GetWorkflowInstancesCommand
{
    /** @var WorkflowInstance $workflowInstance */
    private $workflowInstance;

    /**
     * GetWorkflowCommand constructor.
     *
     * @param Workflow $workflow
     */
    public function __construct(WorkflowInstance $workflowInstance)
    {
//        dump($workflowInstance);
        $this->workflowInstance = $workflowInstance;
    }

    /**
     * @inheritDoc
     */
    public function getGroup(): string
    {
        return 'instance';
    }

    /**
     * @inheritDoc
     */
    public function getAction(): string
    {
        return 'query';
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        $filters = [
            'id' => $this->workflowInstance->getId(),
        ];

        return $filters;
    }

    /**
     * @inheritDoc
     */
    public function parseResponse($responseLine, $responseData)
    {

        if (!(isset($responseData['workflow'])))
            return false;

        $subjobs = $responseData['workflow']['subjobs'];
        $jobInstances = new ArrayCollection([]);
        foreach($subjobs as $subjob) {
            $taskInstances = new ArrayCollection([]);
            foreach($subjob['tasks'] as $tasks) {
                $task = $tasks['@attributes'];

                if (isset($task['execution_time'])) $task['execution_time'] = new \DateTime($task['execution_time']);
                foreach($task as $key => $val)
                    if (ctype_digit($val)) $task[$key] = (int) $task[$key];
                $taskInstances[] = new TaskInstance($task);
            }
            $jobInstances[] = new JobInstance($taskInstances);
        }
        $this->workflowInstance->setJobInstances($jobInstances);

        return $this->workflowInstance;
    }
}

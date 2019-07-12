<?php

namespace Pheanstalk\Command;

use Doctrine\Common\Collections\ArrayCollection;
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
class GetWorkflowInstancesCommand extends AbstractCommand implements \Pheanstalk\ResponseParser
{

    const FILTER_EXECUTING = 'EXECUTING';
    const FILTER_TERMINATED = 'TERMINATED';

    const FILTERS = [
        self::FILTER_EXECUTING,
        self::FILTER_TERMINATED
    ];

    /** @var Workflow $workflow */
    private $workflow;

    /** @var string $status */
    private $status;

    /**
     * GetWorkflowCommand constructor.
     *
     * @param Workflow $workflow
     */
    public function __construct(?Workflow $workflow, $status = self::FILTER_EXECUTING)
    {
        $this->workflow = $workflow;
        $this->status = $status;
    }

    /**
     * @inheritDoc
     */
    public function getGroup(): string
    {
        return ($this->status === self::FILTER_EXECUTING) ? 'status' : 'instances';
    }

    /**
     * @inheritDoc
     */
    public function getAction(): string
    {
        return ($this->status === self::FILTER_EXECUTING) ? 'query' : 'list';
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        $filters = [
            'type' => "workflows",
            'filter_status' => $this->status,
        ];
        if (!empty($this->workflow))
            $filters['filter_workflow_name'] = $this->workflow->getName();

        return $filters;
    }

    /**
     * @inheritDoc
     */
    public function parseResponse($responseLine, $responseData)
    {
        if (!(isset($responseData['workflow'])))
            return new ArrayCollection([]);

        $instances = $responseData['workflow'] ;
        $instances = isset($instances['tags']) ? [$instances['@attributes']] : $instances;
        $workflowInstances = [];
        foreach($instances as $instance) {
            $instance = $instance['@attributes'] ?? $instance;
            if (isset($instance['start_time'])) $instance['start_time'] = new \DateTime($instance['start_time']);
            if (isset($instance['end_time'])) $instance['end_time'] = new \DateTime($instance['end_time']);
            foreach($instance as $key => $val)
                if (ctype_digit($val)) $instance[$key] = (int) $instance[$key];
            $workflowInstances[] = new WorkflowInstance($instance);
        }

        return new ArrayCollection($workflowInstances);
    }
}

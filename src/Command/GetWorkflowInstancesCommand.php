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

    /* (non-phpdoc)
     * @see Command::getCommandLine()
     */
    public function getCommandLine()
    {
        return 'status';
    }

    public function getData()
    {
        return [
            'action' => 'query',
            "attributes" => [
//                'id' => $this->workflow->getId()
                'filter_workflow_name' => $this->workflow->getName(),
//                'filter_status' => "EXECUTING",
                'type' => "workflows"
            ],
            "parameters" => [
//                'filter_workflow_id' => $this->workflow->getId()
            ]
        ];
    }

    public function hasData()
    {
        return true;
    }

    /* (non-phpdoc)
     * @see ResponseParser::parseResponse()
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

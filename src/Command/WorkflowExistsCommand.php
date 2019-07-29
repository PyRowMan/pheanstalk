<?php

namespace Pheanstalk\Command;

use Pheanstalk\Structure\Workflow;

/**
 * The 'WorkflowExists' command.
 *
 * Retrieve a workflow by its name, if there is no workflow named after the arg given in the construct, returns false
 *
 * @author  Valentin Corre
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class WorkflowExistsCommand extends ListWorkflowsCommand
{

    /** @var string $name */
    private $name;

    /**
     * WorkflowExistsCommand constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @inheritDoc
     */
    public function parseResponse($responseLine, $responseData)
    {

        $workflows = parent::parseResponse($responseLine, $responseData);

        $name = $this->name;
        $matchingWorkflows = $workflows->filter(function (Workflow $workflow) use ($name) {
            return $workflow->getName() === $name;
        });

        return !$matchingWorkflows->isEmpty() ?  $matchingWorkflows->first() : false;
    }
}

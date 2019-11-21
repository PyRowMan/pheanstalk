<?php

namespace Pheanstalk\Command;

use Pheanstalk\Parser\RequestOkResponseParser;
use Pheanstalk\Structure\TaskInstance;
use Pheanstalk\Structure\WorkflowInstance;

/**
 * The 'Kill' command.
 *
 * Kills a running workflow instance
 *
 *
 * @author  Valentin Corre
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class KillCommand extends AbstractCommand
{

    /** @var WorkflowInstance $workflowInstance */
    private $workflowInstance;

    /** @var TaskInstance $taskInstance */
    private $taskInstance;

    /**
     * Kills a running task instance
     *
     * @param WorkflowInstance $workflowInstance     The WorkflowInstance
     */
    public function __construct(WorkflowInstance $workflowInstance, TaskInstance $taskInstance)
    {
        $this->workflowInstance = $workflowInstance;
        $this->taskInstance = $taskInstance;
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
        return 'killtask';
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return [
            'id' => $this->workflowInstance->getId(),
            'pid' => $this->taskInstance->getPid(),
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

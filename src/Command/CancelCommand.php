<?php

namespace Pheanstalk\Command;

use Pheanstalk\Parser\RequestOkResponseParser;
use Pheanstalk\Structure\WorkflowInstance;

/**
 * The 'Cancel' command.
 *
 * Cancel a running workflow instance
 *
 *
 * @author  Valentin Corre
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class CancelCommand extends AbstractCommand
{

    /** @var WorkflowInstance $workflowInstance */
    private $workflowInstance;

    /**
     * Cancel a running workflow instance
     *
     * @param WorkflowInstance $workflowInstance     The WorkflowInstance
     */
    public function __construct(WorkflowInstance $workflowInstance)
    {
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
        return 'cancel';
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return [
            'id' => $this->workflowInstance->getId(),
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

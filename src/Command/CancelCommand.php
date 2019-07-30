<?php

namespace Pheanstalk\Command;

use Pheanstalk\Exception;
use Pheanstalk\Structure\Workflow;
use Pheanstalk\Structure\WorkflowInstance;
use Pheanstalk\XmlResponseParser;

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
class CancelCommand extends AbstractCommand implements \Pheanstalk\ResponseParser
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
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseResponse($responseLine, $responseData)
    {
        return $responseLine === 'OK';
    }
}

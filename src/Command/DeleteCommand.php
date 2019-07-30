<?php

namespace Pheanstalk\Command;

use Pheanstalk\Exception;
use Pheanstalk\Response;
use Pheanstalk\Structure\Workflow;

/**
 * The 'delete' command.
 *
 * Permanently deletes an already-reserved job.
 *
 * @author  Paul Annesley
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class DeleteCommand extends AbstractCommand implements \Pheanstalk\ResponseParser
{
    /** @var Workflow $workflow */
    private $workflow;

    /**
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
        return 'delete';
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

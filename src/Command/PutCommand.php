<?php

namespace Pheanstalk\Command;

use Pheanstalk\Structure\Workflow;

/**
 * The 'put' command.
 *
 * Inserts a job into the client's currently used tube.
 *
 * @see UseCommand
 *
 * @author  Valentin Corre
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class PutCommand extends AbstractCommand implements \Pheanstalk\ResponseParser
{

    /** @var Workflow $workflow */
    private $workflow;

    /**
     * Puts a workflow in queue.
     *
     * @param Workflow $workflow     The Workflow
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
        return 'instance';
    }

    /**
     * @inheritDoc
     */
    public function getAction(): string
    {
        return 'launch';
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return [
            'name' => $this->workflow->getName(),
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
        return $responseData['@attributes'];
    }
}

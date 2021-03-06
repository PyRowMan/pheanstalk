<?php

namespace Pheanstalk\Command;

use Pheanstalk\ResponseParser;
use Pheanstalk\Structure\Tube;
use Pheanstalk\Structure\Workflow;

/**
 * The 'CreateTube' command.
 *
 * Inserts a new Tube into the EvQueue.
 *
 *
 * @author  Valentin Corre
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class CreateTubeCommand extends AbstractCommand implements ResponseParser
{

    /** @var Tube $tube */
    private $tube;

    /**
     * Puts a job on the queue.
     *
     * @param Workflow    $workflow     The workflow to create
     */
    public function __construct(Tube $tube)
    {
        $this->tube = $tube;
    }

    /**
     * @inheritDoc
     */
    public function getGroup(): string
    {
        return 'queue';
    }

    /**
     * @inheritDoc
     */
    public function getAction(): string
    {
        return 'create';
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return [
            'name' => $this->tube->getName(),
            "concurrency" => $this->tube->getConcurrency(),
            'scheduler' => $this->tube->getScheduler(),
            'dynamic' => $this->tube->getDynamic(),
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
        $this->tube->setId($responseData['@attributes']['queue-id']);
        return $this->tube;
    }
}

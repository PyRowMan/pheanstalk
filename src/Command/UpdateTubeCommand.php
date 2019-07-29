<?php

namespace Pheanstalk\Command;

use Pheanstalk\Exception;
use Pheanstalk\Response;
use Pheanstalk\Structure\Tube;
use Pheanstalk\Structure\Workflow;
use Pheanstalk\XmlResponseParser;

/**
 * The 'UpdateTube' command.
 *
 * Updates an existing tube
 *
 *
 * @author  Valentin Corre
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class UpdateTubeCommand extends AbstractCommand implements \Pheanstalk\ResponseParser
{

    /** @var Tube $tube */
    private $tube;

    /**
     * Updates an existing tube
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
        return 'edit';
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return [
            'id' => $this->tube->getId(),
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
        return $this->tube;
    }
}

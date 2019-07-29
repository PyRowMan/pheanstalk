<?php

namespace Pheanstalk\Command;

use Pheanstalk\Exception;
use Pheanstalk\Response;
use Pheanstalk\Structure\Workflow;
use Pheanstalk\XmlResponseParser;

/**
 * The 'Create' command.
 *
 * Inserts a new workflow into the client.
 *
 *
 * @author  Valentin Corre
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class CreateCommand extends AbstractCommand implements \Pheanstalk\ResponseParser
{
    /** @var Workflow $workflow */
    private $workflow;

    /**
     * Puts a job on the queue.
     *
     * @param Workflow    $workflow     The workflow to create
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
        return 'create';
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return [
            'name' => $this->workflow->getName(),
            "group" => $this->workflow->getGroup(),
            'content' => base64_encode($this->workflow->getXml()->saveXML()),
            'comment' => $this->workflow->getComment()
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
        $this->workflow->setId($responseData['@attributes']['workflow-id']);
        return $this->workflow;
    }
}

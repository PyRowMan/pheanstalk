<?php

namespace Pheanstalk\Command;

use Pheanstalk\ResponseParser;
use Pheanstalk\Structure\Workflow;

/**
 * The 'update' command.
 *
 * Update an existing job.
 *
 * @author  Valentin Corre
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class UpdateCommand extends AbstractCommand implements ResponseParser
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
        return 'edit';
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return [
            'id' => $this->workflow->getId(),
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
        return $this->workflow;
    }
}

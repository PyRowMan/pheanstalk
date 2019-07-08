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

    /* (non-phpdoc)
     * @see Command::getCommandLine()
     */
    public function getCommandLine()
    {
        return 'workflow';
    }

    public function getData()
    {
        return [
            'action' => 'create',
            'attributes' => [
                'name' => $this->workflow->getName(),
                "group" => $this->workflow->getGroup(),
                'content' => base64_encode($this->workflow->getXml()->saveXML()),
                'comment' => $this->workflow->getComment()
            ]
        ];
    }

    public function hasData()
    {
        return true;
    }

    /* (non-phpdoc)
     * @see ResponseParser::parseResponse()
     */
    public function parseResponse($responseLine, $responseData)
    {
        $this->workflow->setId($responseData['@attributes']['workflow-id']);
        return $this->workflow;
    }
}

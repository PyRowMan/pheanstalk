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
class DeleteCommand
    extends AbstractCommand
    implements \Pheanstalk\ResponseParser
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

    /* (non-phpdoc)
     * @see Command::getCommandLine()
     */
    public function getCommandLine()
    {
        return 'workflow';
    }

    public function hasData()
    {
        return true;
    }

    public function getData()
    {
        return [
            'action' => 'delete',
            'attributes' => [
                'id' => $this->workflow->getId()
            ]
        ];
    }

    /* (non-phpdoc)
     * @see ResponseParser::parseResponse()
     */
    public function parseResponse($responseLine, $responseData)
    {
        return $this->_createResponse($responseLine);
    }
}

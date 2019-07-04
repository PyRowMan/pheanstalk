<?php

namespace Pheanstalk\Command;

use Pheanstalk\Exception;
use Pheanstalk\Structure\Workflow;
use Pheanstalk\XmlResponseParser;

/**
 * The 'put' command.
 *
 * Inserts a job into the client's currently used tube.
 *
 * @see UseCommand
 *
 * @author  Paul Annesley
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class PutCommand
    extends AbstractCommand
    implements \Pheanstalk\ResponseParser
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

    /* (non-phpdoc)
     * @see Command::getCommandLine()
     */
    public function getCommandLine()
    {
        return 'instance';
    }

    public function getData()
    {
        return [
            'action' => 'launch',
            'attributes' => [
                'name' => $this->workflow->getName(),
            ]
        ];
    }

    public function hasData()
    {
        return true;
    }

    public function parseResponse($responseLine, $responseData)
    {
        return $responseData['@attributes'];
    }
}

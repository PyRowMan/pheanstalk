<?php

namespace Pheanstalk\Command;

use Pheanstalk\Exception;
use Pheanstalk\Response;
use Pheanstalk\Structure\Tube;
use Pheanstalk\Structure\Workflow;
use Pheanstalk\XmlResponseParser;

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
class CreateTubeCommand extends AbstractCommand implements \Pheanstalk\ResponseParser
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

    /* (non-phpdoc)
     * @see Command::getCommandLine()
     */
    public function getCommandLine()
    {
        return 'queue';
    }

    public function getData()
    {
        return [
            'action' => 'create',
            'attributes' => [
                'name' => $this->tube->getName(),
                "concurrency" => $this->tube->getConcurrency(),
                'scheduler' => $this->tube->getScheduler(),
                'dynamic' => $this->tube->getDynamic(),
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
        $this->tube->setId($responseData['@attributes']['queue-id']);
        return $this->tube;
    }
}

<?php

namespace Pheanstalk\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Pheanstalk\Exception;
use Pheanstalk\Response;
use Pheanstalk\Structure\Workflow;
use Pheanstalk\XmlResponseParser;

/**
 * The 'ListWorkflows' command.
 *
 * Inserts a new workflow into the client.
 *
 *
 * @author  Valentin Corre
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class ListWorkflowsCommand
    extends AbstractCommand
    implements \Pheanstalk\ResponseParser
{

    /* (non-phpdoc)
     * @see Command::getCommandLine()
     */
    public function getCommandLine()
    {
        return 'workflows';
    }
    /* (non-phpdoc)
     * @see Command::getResponseParser()
     */
//    public function getResponseParser()
//    {
//        return new XmlResponseParser();
//    }

    public function getData()
    {
        return [
            'action' => 'list'
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
        $workflows = [];
        foreach ($responseData['workflow'] as $workflow) {
            $workflow = $workflow['@attributes'] ?? $workflow;
            $object = new Workflow($workflow['name'], $workflow['group'], new ArrayCollection([]), $workflow['comment']);
            $object
                ->setId($workflow['id'])
                ->setBoundToSchedule((int) $workflow['bound-to-schedule'])
                ->setLastcommit((int) $workflow['lastcommit'])
                ->setModified((int) $workflow['modified'])
            ;
            $workflows[] = $object;
        }
        return new ArrayCollection($workflows);
    }
}

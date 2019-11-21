<?php

namespace Pheanstalk\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Pheanstalk\ResponseParser;
use Pheanstalk\Structure\Workflow;

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
class ListWorkflowsCommand extends AbstractCommand implements ResponseParser
{

    /**
     * @inheritDoc
     */
    public function getGroup(): string
    {
        return 'workflows';
    }

    /**
     * @inheritDoc
     */
    public function getAction(): string
    {
        return 'list';
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

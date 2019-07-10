<?php

namespace Pheanstalk\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Pheanstalk\Structure\Workflow;

/**
 * The 'GetWorkflow' command.
 *
 * Retrieve a workflow by its id, if there is no workflow for the id given in the construct, returns false
 *
 * @author  Valentin Corre
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class GetWorkflowInstancesCommand extends AbstractCommand implements \Pheanstalk\ResponseParser
{

    /** @var Workflow $workflow */
    private $workflow;

    /**
     * GetWorkflowCommand constructor.
     *
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
        return 'instances';
    }

    public function getData()
    {
        return [
            'action' => 'list',
            "attributes" => [
//                'id' => $this->workflow->getId()
                'filter_workflow_name' => $this->workflow->getName()
            ],
            "parameters" => [
//                'filter_workflow_id' => $this->workflow->getId()
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

//        foreach ($responseData['workflow'] as $workflow) {

        $workflow = $responseData['workflow'];
        $workflow = $workflow['@attributes'] ?? $workflow;
//        $this->workflow
//            ->setEndTime(new \DateTime($workflow['end_time']))
//            ->setStartTime(new \DateTime($workflow['start_time']))
//            ->setEvqid((int) $workflow['evqid'])
//            ->setQueuedTasks((int) $workflow['queued_tasks'])
//            ->setRunningTasks((int) $workflow['running_tasks'])
//            ->setStatus($workflow['status'])
//            ->setErrors((int) $workflow['errors'])
//        ;

//        dump($workflow);
//        dump($responseData['workflow'], $workflow);
//        $object = new Workflow($workflow['name'], $workflow['group'], new ArrayCollection([]), $workflow['comment']);
//        $object
//            ->setId($workflow['id'])
//            ->setBoundToSchedule((int) $workflow['bound-to-schedule'])
//            ->setLastcommit((int) $workflow['lastcommit'])
//            ->setModified((int) $workflow['modified'])
//        ;
//        $workflows[] = $object;
//        }
//        return new ArrayCollection($workflows);
        dump($this->workflow, $workflow, $responseData);

        return $this->workflow;
    }
}

<?php

namespace Pheanstalk;

use Doctrine\Common\Collections\ArrayCollection;
use Pheanstalk\Command\CreateCommand;
use Pheanstalk\Command\CreateScheduleCommand;
use Pheanstalk\Command\GetWorkflowCommand;
use Pheanstalk\Command\GetWorkflowInstancesCommand;
use Pheanstalk\Command\GetWorkflowInstancesDetailCommand;
use Pheanstalk\Command\ListWorkflowsCommand;
use Pheanstalk\Command\ReleaseCommand;
use Pheanstalk\Command\WorkflowExistsCommand;
use Pheanstalk\Exception\ServerDuplicateEntryException;
use Pheanstalk\Structure\Job;
use Pheanstalk\Structure\Task;
use Pheanstalk\Structure\TimeSchedule;
use Pheanstalk\Structure\Tube;
use Pheanstalk\Structure\Workflow;
use Pheanstalk\Structure\WorkflowInstance;

/**
 * Pheanstalk is a PHP client for the beanstalkd workqueue.
 *
 * The Pheanstalk class is a simple facade for the various underlying components.
 *
 * @see http://github.com/kr/beanstalkd
 * @see http://xph.us/software/beanstalkd/
 *
 * @author  Paul Annesley
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class Pheanstalk implements PheanstalkInterface
{
    const VERSION = '3.2.1';

    private $_connection;
    private $_using = PheanstalkInterface::DEFAULT_TUBE;
    private $_watching = array(PheanstalkInterface::DEFAULT_TUBE => true);

    /** @var $currentClass PheanstalkInterface */
    private $currentClass;

    /**
     * @param string $host
     * @param int    $port
     * @param int    $connectTimeout
     * @param bool   $connectPersistent
     */
    public function __construct($host, $port = PheanstalkInterface::DEFAULT_PORT, $connectTimeout = null, $connectPersistent = false)
    {
        $this->setConnection(new Connection($host, $port, $connectTimeout, $connectPersistent));
    }

    /**
     * {@inheritdoc}
     */
    public function setConnection(Connection $connection)
    {
        $this->_connection = $connection;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * @return PheanstalkInterface
     */
    public function getCurrentClass(): PheanstalkInterface
    {
        return $this->currentClass ?? $this;
    }

    /**
     * @param PheanstalkInterface $currentClass
     *
     * @return Pheanstalk
     */
    public function setCurrentClass(PheanstalkInterface $currentClass): PheanstalkInterface
    {
        $this->currentClass = $currentClass;
        return $this;
    }

    // ----------------------------------------

    /**
     * {@inheritdoc}
     */
    public function delete(Workflow $workflow)
    {
        $this->_dispatch(new Command\DeleteCommand($workflow));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function workflowExists($name)
    {
        $workflow = $this->_dispatch(new Command\WorkflowExistsCommand($name));
        if ($workflow instanceof Workflow)
            return $this->getCurrentClass()->getWorkflow($workflow);
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflow(Workflow $workflow)
    {
        return $this->_dispatch(new Command\GetWorkflowCommand($workflow));
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflowInstances(?Workflow $workflow, string $status = null)
    {
        $status = empty($status) ? GetWorkflowInstancesDetailCommand::FILTERS : [$status];
        $instances = new ArrayCollection([]);
        foreach($status as $stat) {
            $instances[strtolower($stat)] = $this->_dispatch(new Command\GetWorkflowInstancesCommand($workflow, $stat));
            if ($status === GetWorkflowInstancesCommand::FILTER_EXECUTING) {
                foreach($instances as $instance) {
                    $this->getCurrentClass()->getWorkflowInstancesDetails($instance);
                }
            }
        }

        return $instances;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflowInstancesDetails(WorkflowInstance $workflowInstance)
    {
        return $this->_dispatch(new Command\GetWorkflowInstancesDetailCommand($workflowInstance));
    }

    /**
     * {@inheritdoc}
     */
    public function tubeExists($name)
    {
        return $this->_dispatch(new Command\TubeExistsCommand($name));
    }

    /**
     * {@inheritdoc}
     */
    public function listTubes()
    {
        return $this->_dispatch(new Command\ListTubesCommand());
    }

    /**
     * {@inheritdoc}
     */
    public function peek()
    {
        $response = $this->_dispatch(new Command\PeekCommand());

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function put(Workflow $workflow) {
        $response = $this->_dispatch(new Command\PutCommand($workflow));

        return $response['workflow-instance-id'];
    }

    /**
     * {@inheritdoc}
     */
    public function statsJob($job)
    {
        return $this->_dispatch(new Command\StatsJobCommand($job));
    }

    /**
     * {@inheritdoc}
     */
    public function statsTube($tube)
    {
        return $this->_dispatch(new Command\StatsTubeCommand($tube));
    }

    /**
     * {@inheritdoc}
     */
    public function stats()
    {
        return $this->_dispatch(new Command\StatsCommand());
    }

    // ----------------------------------------

    /**
     * Dispatches the specified command to the connection object.
     *
     * If a SocketException occurs, the connection is reset, and the command is
     * re-attempted once.
     *
     * @param Command $command
     *
     * @return Response
     */
    private function _dispatch($command)
    {
        return $this->_connection->dispatchCommand($command);
    }

    /**
     * {@inheritdoc}
     */
    public function create(Workflow $workflow, $force = false): Workflow
    {
        try{
            $tubes = [];
            /** @var Job $job */
            foreach($workflow->getJobs() as $job) {
                /** @var Task $task */
                foreach ($job->getTasks() as $task) {
                    $tubes = array_merge($tubes, [$task->getQueue()]);
                }
            }
            foreach($tubes as $tube) {
                if (!$this->getCurrentClass()->tubeExists($tube)) {
                    $this->getCurrentClass()->createTube(new Tube($tube, 1));
                };
            }
            $workflow = $this->_dispatch(new Command\CreateCommand($workflow));
        } catch(ServerDuplicateEntryException $e) {
            if ($force) {
                $workflows = $this->_dispatch(new Command\ListWorkflowsCommand());
                $workflowToDelete = $workflows->filter(function(Workflow $listedWorkflow) use ($workflow) {
                    return $listedWorkflow->getName() === $workflow->getName()
                        && $listedWorkflow->getGroup() === $workflow->getGroup();
                })->first();
                $this->getCurrentClass()->delete($workflowToDelete);

                return $this->getCurrentClass()->create($workflow);
            }
            throw $e;
        }

        return $workflow;
    }

    /**
     * {@inheritdoc}
     */
    public function createSchedule(Workflow $workflow, TimeSchedule $schedule, $onFailure = CreateScheduleCommand::FAILURE_TYPE_CONTINUE, $active = true, $comment = null)
    {
        $workflowSchedule = $this->_dispatch(
            new Command\CreateScheduleCommand($workflow, $schedule, $onFailure, $active, $comment)
        );
        return $workflowSchedule;
    }

    /**
     * {@inheritdoc}
     */
    public function createTask(string $name, string $group, string $path, $queue = 'default', $useAgent = false, $user = null, $host = null, $comment = null): Workflow
    {
        $task = new Task($path, $queue, $useAgent, $user, $host);
        $job = new Job(new ArrayCollection([$task]));
        $workflow = new Workflow($name, $group, new ArrayCollection([$job]), $comment);

        return $this->getCurrentClass()->create($workflow, true);
    }

    public function createTube(Tube $tube): Tube
    {
        return $this->_dispatch(new Command\CreateTubeCommand($tube));
    }
}

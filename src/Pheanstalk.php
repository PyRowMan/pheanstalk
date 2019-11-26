<?php

namespace Pheanstalk;

use Doctrine\Common\Collections\ArrayCollection;
use Pheanstalk\Command\CreateCommand;
use Pheanstalk\Command\CreateScheduleCommand;
use Pheanstalk\Command\CreateTubeCommand;
use Pheanstalk\Command\GetWorkflowCommand;
use Pheanstalk\Command\GetWorkflowInstancesCommand;
use Pheanstalk\Command\GetWorkflowInstancesDetailCommand;
use Pheanstalk\Command\ListWorkflowsCommand;
use Pheanstalk\Command\ReleaseCommand;
use Pheanstalk\Command\UpdateTubeCommand;
use Pheanstalk\Command\WorkflowExistsCommand;
use Pheanstalk\Exception\ServerDuplicateEntryException;
use Pheanstalk\Structure\Job;
use Pheanstalk\Structure\Schedule;
use Pheanstalk\Structure\Task;
use Pheanstalk\Structure\TaskInstance;
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

    /** @var Connection $connection */
    private $connection;

    /** @var PheanstalkInterface $currentClass */
    private $currentClass;

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param int    $port
     * @param int    $connectTimeout
     * @param bool   $connectPersistent
     */
    public function __construct($host, $user = null, $password = null, $port = PheanstalkInterface::DEFAULT_PORT, $connectTimeout = null, $connectPersistent = false)
    {
        $this->setConnection(new Connection($host, $user, $password, $port, $connectTimeout, $connectPersistent));
    }

    /**
     * {@inheritdoc}
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return $this->connection;
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
    public function deleteSchedule(Schedule $schedule)
    {
        return $this->_dispatch(new Command\DeleteScheduleCommand($schedule));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Workflow $workflow)
    {
        return $this->_dispatch(new Command\DeleteCommand($workflow));
    }

    /**
     * {@inheritdoc}
     */
    public function deleteTube(Tube $tube)
    {
        return $this->_dispatch(new Command\DeleteTubeCommand($tube));
    }

    /**
     * {@inheritdoc}
     */
    public function workflowExists($name)
    {
        $workflow = $this->_dispatch(new Command\WorkflowExistsCommand($name));
        if ($workflow instanceof Workflow) {
            return $this->getCurrentClass()->getWorkflow($workflow);
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchedule(int $scheduleId)
    {
        return $this->_dispatch(new Command\GetScheduleCommand($scheduleId));
    }

    /**
     * {@inheritdoc}
     */
    public function listSchedules()
    {
        return $this->_dispatch(new Command\ListSchedulesCommand());
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
        $paramsStatus = empty($status) ? GetWorkflowInstancesDetailCommand::FILTERS : [$status];
        $instances = new ArrayCollection([]);
        foreach ($paramsStatus as $stat) {
            $instances[strtolower($stat)] = $this->_dispatch(new Command\GetWorkflowInstancesCommand($workflow, $stat));
                /** @var ArrayCollection $workflowCollection */
                $workflowCollection = $instances[strtolower($stat)]->get('workflow_instances');
            if (!empty($workflowCollection)) {
                foreach ($workflowCollection as $instance) {
                    $this->getCurrentClass()->getWorkflowInstancesDetails($instance);
                }
            }
        }
        if (!is_null($status)) {
            return $instances->get(strtolower($status))->get('workflow_instances');
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
    public function put(Workflow $workflow)
    {
        $response = $this->_dispatch(new Command\PutCommand($workflow));

        return $response['workflow-instance-id'];
    }

    /**
     * {@inheritdoc}
     */
    public function statsTube(Tube $tube)
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
     * @throws Exception\ClientException
     * @param Command $command
     *
     * @return mixed
     */
    private function _dispatch($command)
    {
        return $this->connection->dispatchCommand($command);
    }

    /**
     * {@inheritdoc}
     */
    public function create(Workflow $workflow, $force = false): Workflow
    {
        try {
            $this->checkAndCreateTubes($workflow);
            $workflow = $this->_dispatch(new Command\CreateCommand($workflow));
        } catch (ServerDuplicateEntryException $e) {
            if ($force) {
                $workflowToDelete = $this->findWorkflow($workflow);
                $this->getCurrentClass()->delete($workflowToDelete);

                return $this->getCurrentClass()->create($workflow);
            }
            throw $e;
        }

        return $workflow;
    }

    /**
     * @param Workflow $workflow
     *
     * @return Workflow|bool
     * @throws Exception\ClientException
     */
    public function findWorkflow(Workflow $workflow)
    {
        $workflows = $this->_dispatch(new Command\ListWorkflowsCommand());
        return $workflows->filter(function(Workflow $listedWorkflow) use ($workflow) {
            return $listedWorkflow->getName() === $workflow->getName()
                && $listedWorkflow->getGroup() === $workflow->getGroup();
        })->first();
    }

    /**
     * @param Workflow $workflow
     *
     * @throws Exception\ClientException
     */
    public function checkAndCreateTubes(Workflow $workflow)
    {
        $tubes = [];
        /** @var Job $job */
        foreach ($workflow->getJobs() as $job) {
            /** @var Task $task */
            foreach ($job->getTasks() as $task) {
                $tubes = array_merge($tubes, [$task->getQueue()]);
            }
        }
        foreach ($tubes as $tube) {
            if (!$this->getCurrentClass()->tubeExists($tube)) {
                $this->getCurrentClass()->createTube(new Tube($tube, 1));
            };
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update(Workflow $workflow): Workflow
    {
        $workflow = $this->_dispatch(new Command\UpdateCommand($workflow));
        return $workflow;
    }

    /**
     * {@inheritdoc}
     */
    public function updateSchedule(Schedule $schedule): Schedule
    {
        $schedule = $this->_dispatch(new Command\UpdateScheduleCommand($schedule));
        return $schedule;
    }

    /**
     * {@inheritdoc}
     */
    public function createSchedule(Schedule $schedule)
    {
        $workflowSchedule = $this->_dispatch(
            new Command\CreateScheduleCommand($schedule)
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

    /**
     * {@inheritDoc}
     */
    public function createTube(Tube $tube): Tube
    {
        return $this->_dispatch(new Command\CreateTubeCommand($tube));
    }

    /**
     * {@inheritdoc}
     */
    public function updateTube(Tube $tube): Tube
    {
        return $this->_dispatch(new Command\UpdateTubeCommand($tube));
    }

    /**
     * {@inheritdoc}
     */
    public function cancel(WorkflowInstance $workflowInstance)
    {
        return $this->_dispatch(new Command\CancelCommand($workflowInstance));
    }

    /**
     * {@inheritdoc}
     */
    public function kill(WorkflowInstance $workflowInstance, TaskInstance $taskInstance)
    {
        return $this->_dispatch(new Command\KillCommand($workflowInstance, $taskInstance));
    }
}

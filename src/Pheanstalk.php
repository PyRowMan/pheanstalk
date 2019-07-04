<?php

namespace Pheanstalk;

use Doctrine\Common\Collections\ArrayCollection;
use Pheanstalk\Command\CreateCommand;
use Pheanstalk\Command\ReleaseCommand;
use Pheanstalk\Command\WorkflowExistsCommand;
use Pheanstalk\Exception\ServerDuplicateEntryException;
use Pheanstalk\Structure\Job;
use Pheanstalk\Structure\Task;
use Pheanstalk\Structure\Workflow;

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

    // ----------------------------------------

    /**
     * {@inheritdoc}
     */
    public function bury($job, $priority = PheanstalkInterface::DEFAULT_PRIORITY)
    {
        $this->_dispatch(new Command\BuryCommand($job, $priority));
    }

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
    public function ignore($tube)
    {
        if (isset($this->_watching[$tube])) {
            $this->_dispatch(new Command\IgnoreCommand($tube));
            unset($this->_watching[$tube]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function workflowExists($name)
    {
        return $this->_dispatch(new Command\WorkflowExistsCommand($name));
    }

    /**
     * {@inheritdoc}
     */
    public function kickJob($job)
    {
        $this->_dispatch(new Command\KickJobCommand($job));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function listTubes()
    {
        return (array) $this->_dispatch(
            new Command\ListTubesCommand()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function listTubesWatched($askServer = false)
    {
        if ($askServer) {
            $response = (array) $this->_dispatch(
                new Command\ListTubesWatchedCommand()
            );
            $this->_watching = array_fill_keys($response, true);
        }

        return array_keys($this->_watching);
    }

    /**
     * {@inheritdoc}
     */
    public function listTubeUsed($askServer = false)
    {
        if ($askServer) {
            $response = $this->_dispatch(
                new Command\ListTubeUsedCommand()
            );
            $this->_using = $response['tube'];
        }

        return $this->_using;
    }

    /**
     * {@inheritdoc}
     */
    public function pauseTube($tube, $delay)
    {
        $this->_dispatch(new Command\PauseTubeCommand($tube, $delay));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resumeTube($tube)
    {
        // Pause a tube with zero delay will resume the tube
        $this->pauseTube($tube, 0);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function peek($jobId)
    {
        $response = $this->_dispatch(
            new Command\PeekCommand($jobId)
        );

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function peekReady($tube = null)
    {
        $response = $this->_dispatch(
            new Command\PeekCommand(Command\PeekCommand::TYPE_READY)
        );

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function peekDelayed($tube = null)
    {

        $response = $this->_dispatch(
            new Command\PeekCommand(Command\PeekCommand::TYPE_DELAYED)
        );

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function peekBuried($tube = null)
    {

        $response = $this->_dispatch(
            new Command\PeekCommand(Command\PeekCommand::TYPE_BURIED)
        );

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
    public function release(
        $job,
        $priority = PheanstalkInterface::DEFAULT_PRIORITY,
        $delay = PheanstalkInterface::DEFAULT_DELAY
    ) {
        $this->_dispatch(
            new Command\ReleaseCommand($job, $priority, $delay)
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function reserve($timeout = null)
    {
        $response = $this->_dispatch(
            new Command\ReserveCommand($timeout)
        );

        $falseResponses = array(
            Response::RESPONSE_DEADLINE_SOON,
            Response::RESPONSE_TIMED_OUT,
        );

        if (in_array($response->getResponseName(), $falseResponses)) {
            return false;
        } else {
            return new Job($response['id'], $response['jobdata']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reserveFromTube($tube, $timeout = null)
    {
        $this->watchOnly($tube);

        return $this->reserve($timeout);
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

    /**
     * {@inheritdoc}
     */
    public function touch($job)
    {
        $this->_dispatch(new Command\TouchCommand($job));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function useTube($tube)
    {
        if ($this->_using != $tube) {
            $this->_dispatch(new Command\UseCommand($tube));
            $this->_using = $tube;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function watch($tube)
    {
        if (!isset($this->_watching[$tube])) {
            $this->_dispatch(new Command\WatchCommand($tube));
            $this->_watching[$tube] = true;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function watchOnly($tube)
    {
        $this->watch($tube);

        $ignoreTubes = array_diff_key($this->_watching, array($tube => true));
        foreach ($ignoreTubes as $ignoreTube => $true) {
            $this->ignore($ignoreTube);
        }

        return $this;
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
            $workflow = $this->_dispatch(new Command\CreateCommand($workflow));
        } catch(ServerDuplicateEntryException $e) {
            if ($force) {
                $workflows = $this->_dispatch(new Command\ListWorkflowsCommand());
                $workflowToDelete = $workflows->filter(function(Workflow $listedWorkflow) use ($workflow) {
                    return $listedWorkflow->getName() === $workflow->getName()
                        && $listedWorkflow->getGroup() === $workflow->getGroup();
                })->first();
                $this->delete($workflowToDelete);

                return $this->create($workflow);
            }
            throw $e;
        }

        return $workflow;
    }

    /**
     * {@inheritdoc}
     */
    public function createTask(string $name, string $group, string $path, $queue = 'default', $comment = null): Workflow
    {
        $task = new Task($path, $queue);
        $job = new Job(new ArrayCollection([$task]));
        $workflow = new Workflow($name, $group, new ArrayCollection([$job]), $comment);

        return $this->create($workflow, true);
    }
}

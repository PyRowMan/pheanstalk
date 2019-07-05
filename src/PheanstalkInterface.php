<?php

namespace Pheanstalk;

use Doctrine\Common\Collections\ArrayCollection;
use Pheanstalk\Structure\Tube;
use Pheanstalk\Structure\Workflow;

interface PheanstalkInterface
{
    const DEFAULT_PORT = 11300;
    const DEFAULT_DELAY = 0; // no delay
    const DEFAULT_PRIORITY = 1024; // most urgent: 0, least urgent: 4294967295
    const DEFAULT_TTR = 60; // 1 minute
    const DEFAULT_TUBE = 'default';

    /**
     * @param Connection
     *
     * @return $this
     */
    public function setConnection(Connection $connection);

    /**
     * The internal connection object.
     * Not required for general usage.
     *
     * @return Connection
     */
    public function getConnection();

    /**
     * @return PheanstalkInterface
     */
    public function setCurrentClass(PheanstalkInterface $currentClass): PheanstalkInterface;

    public function getCurrentClass(): PheanstalkInterface;

    // ----------------------------------------

    /**
     * Permanently deletes a job.
     *
     * @param Workflow $workflow
     *
     * @return $this
     */
    public function delete(Workflow $workflow);

    /**
     * Retrieve a workflow by its name, if there is no workflow named after
     * the arg given in the construct, returns false
     *
     * @param string $name The name of the workflow searched
     *
     * @return bool|Workflow If exists, the workflow
     */
    public function workflowExists($name);

    /**
     * Retrieve a tube by its name, if there is no tube named after
     * the arg given in the construct, returns false
     *
     * @param string $name The name of the tube searched
     *
     * @return bool|Tube If exists, the workflow
     */
    public function tubeExists($name);

    /**
     * The names of all tubes on the server.
     *
     * @return ArrayCollection[Queue]
     */
    public function listTubes();

    /**
     * Inspect a job in the system, regardless of what tube it is in.
     *
     * @return object Job
     */
    public function peek();

    /**
     * Puts a job on the queue.
     *
     * @param Workflow $workflow     The Workflow
     *
     * @return int The new job ID
     */
    public function put(Workflow $workflow);

    /**
     * Gives statistical information about the specified job if it exists.
     *
     * @param Job|int $job
     *
     * @return object
     */
    public function statsJob($job);

    /**
     * Gives statistical information about the specified tube if it exists.
     *
     * @param string $tube
     *
     * @return object
     */
    public function statsTube($tube);

    /**
     * Gives statistical information about the beanstalkd system as a whole.
     *
     * @return object
     */
    public function stats();

    /**
     * Create a workflow on the queue.
     *
     * @param Workflow  $data     The workflow to create
     * @param null|bool (optional) $force Will erase already existent old workflow if already exists
     *
     * @return Workflow The newly created workflow
     */
    public function create(Workflow $data, $force = false): Workflow;

    /**
     * @param string        $name                   The name of the linked workflow
     * @param string        $group                  The group of the linked workflow
     * @param string        $path                   The command that will be executed by the workflow
     * @param null|string   $queue      (optional)  The queue of the workflow
     * @param null|string   $user       (optional)  The user that will execute the command
     * @param null|string   $host       (optional)  The Ip address where to execute the command
     * @param null|string   $comment    (optional)  The comment of the workflow
     *
     * @return Workflow The newly created workflow
     */
    public function createTask(string $name, string $group, string $path, $queue = 'default', $user = null, $host = null, $comment = null): Workflow;

    /**
     * @param string $name          The name of the tube
     * @param int    $concurrency   The number of workflows that can be executed simultaneously
     * @param string $scheduler     WIP
     *
     * @return Tube
     */
    public function createTube(Tube $tube): Tube;
}

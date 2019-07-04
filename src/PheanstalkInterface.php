<?php

namespace Pheanstalk;

use Doctrine\Common\Collections\ArrayCollection;
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

    // ----------------------------------------

    /**
     * Puts a job into a 'buried' state, revived only by 'kick' command.
     *
     * @param Job $job
     * @param int $priority
     */
    public function bury($job, $priority = self::DEFAULT_PRIORITY);

    /**
     * Permanently deletes a job.
     *
     * @param Workflow $workflow
     *
     * @return $this
     */
    public function delete(Workflow $workflow);

    /**
     * Remove the specified tube from the watchlist.
     *
     * Does not execute an IGNORE command if the specified tube is not in the
     * cached watchlist.
     *
     * @param string $tube
     *
     * @return $this
     */
    public function ignore($tube);

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
     * A variant of kick that operates with a single job. If the given job
     * exists and is in a buried or delayed state, it will be moved to the
     * ready queue of the the same tube where it currently belongs.
     *
     * @param Job $job Job
     *
     * @return $this
     */
    public function kickJob($job);

    /**
     * The names of all tubes on the server.
     *
     * @return ArrayCollection[Queue]
     */
    public function listTubes();

    /**
     * The names of the tubes being watched, to reserve jobs from.
     *
     * Returns the cached watchlist if $askServer is false (the default),
     * or queries the server for the watchlist if $askServer is true.
     *
     * @param bool $askServer
     *
     * @return array
     */
    public function listTubesWatched($askServer = false);

    /**
     * The name of the current tube used for publishing jobs to.
     *
     * Returns the cached value if $askServer is false (the default),
     * or queries the server for the currently used tube if $askServer
     * is true.
     *
     * @param bool $askServer
     *
     * @return string
     */
    public function listTubeUsed($askServer = false);

    /**
     * Temporarily prevent jobs being reserved from the given tube.
     *
     * @param string $tube  The tube to pause
     * @param int    $delay Seconds before jobs may be reserved from this queue.
     *
     * @return $this
     */
    public function pauseTube($tube, $delay);

    /**
     * Resume jobs for a given paused tube.
     *
     * @param string $tube The tube to resume
     *
     * @return $this
     */
    public function resumeTube($tube);

    /**
     * Inspect a job in the system, regardless of what tube it is in.
     *
     * @param int $jobId
     *
     * @return object Job
     */
    public function peek($jobId);

    /**
     * Inspect the next ready job in the specified tube. If no tube is
     * specified, the currently used tube in used.
     *
     * @param string $tube
     *
     * @return object Job
     */
    public function peekReady($tube = null);

    /**
     * Inspect the shortest-remaining-delayed job in the specified tube. If no
     * tube is specified, the currently used tube in used.
     *
     * @param string $tube
     *
     * @return object Job
     */
    public function peekDelayed($tube = null);

    /**
     * Inspect the next job in the list of buried jobs of the specified tube.
     * If no tube is specified, the currently used tube in used.
     *
     * @param string $tube
     *
     * @return object Job
     */
    public function peekBuried($tube = null);

    /**
     * Puts a job on the queue.
     *
     * @param Workflow $workflow     The Workflow
     *
     * @return int The new job ID
     */
    public function put(Workflow $workflow);

    /**
     * Puts a reserved job back into the ready queue.
     *
     * Marks the jobs state as "ready" to be run by any client.
     * It is normally used when the job fails because of a transitory error.
     *
     * @param object $job      Job
     * @param int    $priority From 0 (most urgent) to 0xFFFFFFFF (least urgent)
     * @param int    $delay    Seconds to wait before job becomes ready
     *
     * @return $this
     */
    public function release($job, $priority = self::DEFAULT_PRIORITY, $delay = self::DEFAULT_DELAY);

    /**
     * Reserves/locks a ready job in a watched tube.
     *
     * A non-null timeout uses the 'reserve-with-timeout' instead of 'reserve'.
     *
     * A timeout value of 0 will cause the server to immediately return either a
     * response or TIMED_OUT.  A positive value of timeout will limit the amount of
     * time the client will block on the reserve request until a job becomes
     * available.
     *
     * @param int $timeout
     *
     * @return object Job
     */
    public function reserve($timeout = null);

    /**
     * Reserves/locks a ready job from the specified tube.
     *
     * A non-null timeout uses the 'reserve-with-timeout' instead of 'reserve'.
     *
     * A timeout value of 0 will cause the server to immediately return either a
     * response or TIMED_OUT.  A positive value of timeout will limit the amount of
     * time the client will block on the reserve request until a job becomes
     * available.
     *
     * Using this method is equivalent to calling watch(), ignore() then
     * reserve(), with the added benefit that it will not execute uneccessary
     * WATCH or IGNORE commands if the client is already watching the
     * specified tube.
     *
     * @param string $tube
     * @param int    $timeout
     *
     * @return object Job
     */
    public function reserveFromTube($tube, $timeout = null);

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
     * Allows a worker to request more time to work on a job.
     *
     * This is useful for jobs that potentially take a long time, but you still want
     * the benefits of a TTR pulling a job away from an unresponsive worker.  A worker
     * may periodically tell the server that it's still alive and processing a job
     * (e.g. it may do this on DEADLINE_SOON).
     *
     * @param Job $job
     *
     * @return $this
     */
    public function touch($job);

    /**
     * Change to the specified tube name for publishing jobs to.
     * This method would be called 'use' if it were not a PHP reserved word.
     *
     * Does not execute a USE command if the client is already using the
     * specified tube.
     *
     * @param string $tube
     *
     * @return $this
     */
    public function useTube($tube);

    /**
     * Add the specified tube to the watchlist, to reserve jobs from.
     *
     * Does not execute a WATCH command if the client is already watching the
     * specified tube.
     *
     * @param string $tube
     *
     * @return $this
     */
    public function watch($tube);

    /**
     * Adds the specified tube to the watchlist, to reserve jobs from, and
     * ignores any other tubes remaining on the watchlist.
     *
     * @param string $tube
     *
     * @return $this
     */
    public function watchOnly($tube);

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
     * @param null|string   $comment    (optional)  The comment of the workflow
     *
     * @return Workflow The newly created workflow
     */
    public function createTask(string $name, string $group, string $path, $queue = 'default', $comment = null): Workflow;
}

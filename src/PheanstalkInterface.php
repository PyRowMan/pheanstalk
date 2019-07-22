<?php

namespace Pheanstalk;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Pheanstalk\Command\CreateScheduleCommand;
use Pheanstalk\Command\GetWorkflowInstancesCommand;
use Pheanstalk\Structure\TaskInstance;
use Pheanstalk\Structure\TimeSchedule;
use Pheanstalk\Structure\Tube;
use Pheanstalk\Structure\Workflow;
use Pheanstalk\Structure\WorkflowInstance;

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

    /**
     * @return PheanstalkInterface
     */
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
     * Retrieve a workflow by its id, if there isn't for
     * the id given in the construct, returns false
     *
     * @param Workflow $workflow The workflow searched
     *
     * @return bool|Workflow If exists, the worflow
     */
    public function getWorkflow(Workflow $workflow);

    /**
     * Retrieve instances linked to a workflow
     *
     * @param null|Workflow $workflow The workflow we want the instances
     *
     * @return ArrayCollection
     */
    public function getWorkflowInstances(?Workflow $workflow, string $status = null);

    /**
     * Retrieve details of a workflowInstance
     *
     * @param null|Workflow $workflow The workflow we want the instances
     *
     * @return WorkflowInstance|false
     */
    public function getWorkflowInstancesDetails(WorkflowInstance $workflowInstance);

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
     * Updates a workflow.
     *
     * @param Workflow  $data     The workflow to create
     *
     * @return Workflow The updated workflow
     */
    public function update(Workflow $workflow):Workflow;

    /**
     * @param Workflow      $workflow
     * @param TimeSchedule  $schedule
     * @param null          $onFailure
     * @param null          $active
     * @param null          $comment
     *
     * @return mixed
     */
    public function createSchedule(Workflow $workflow, TimeSchedule $schedule, $onFailure = CreateScheduleCommand::FAILURE_TYPE_CONTINUE, $active = true, $comment = null);

    /**
     * @param string        $name                   The name of the linked workflow
     * @param string        $group                  The group of the linked workflow
     * @param string        $path                   The command that will be executed by the workflow
     * @param null|string   $queue      (optional)  The queue of the workflow
     * @param bool          $useAgent   (optional)  Tell if you want to use the EvQueue Agent (Used to send status of tasks)
     * @param null|string   $user       (optional)  The user that will execute the command
     * @param null|string   $host       (optional)  The Ip address where to execute the command
     * @param null|string   $comment    (optional)  The comment of the workflow
     *
     * @return Workflow The newly created workflow
     */
    public function createTask(string $name, string $group, string $path, $queue = 'default', $useAgent = false, $user = null, $host = null, $comment = null): Workflow;

    /**
     * @param string $name          The name of the tube
     *
     * @return Tube
     */
    public function createTube(Tube $tube): Tube;

    /**
     * @param string $name          The name of the tube
     *
     * @return Tube
     */
    public function updateTube(Tube $tube): Tube;

    /**
     * Cancel a running workflow instance
     *
     * @param WorkflowInstance $workflowInstance
     *
     * @return mixed
     */
    public function cancel(WorkflowInstance $workflowInstance);

    /**
     * Kills a running workflow instance
     *
     * @param WorkflowInstance $workflowInstance
     * @param TaskInstance $taskInstance
     *
     * @return mixed
     */
    public function kill(WorkflowInstance $workflowInstance, TaskInstance $taskInstance);
}

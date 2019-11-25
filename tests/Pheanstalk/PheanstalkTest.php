<?php

namespace Pheanstalk;

use Doctrine\Common\Collections\ArrayCollection;
use Pheanstalk\Command\GetWorkflowInstancesCommand;
use Pheanstalk\Structure\JobInstance;
use Pheanstalk\Structure\Schedule;
use Pheanstalk\Structure\Task;
use Pheanstalk\Structure\Job;
use Pheanstalk\Structure\TimeSchedule;
use Pheanstalk\Structure\Tube;
use Pheanstalk\Structure\Workflow;
use Pheanstalk\Structure\WorkflowInstance;
use PHPUnit\Framework\TestCase;

class PheanstalkTest extends TestCase
{
    const SERVER_HOST = 'localhost';
    const SERVER_PORT = '5000';
    const SERVER_PASSWORD = 'admin';
    const SERVER_USER = 'admin';
    const CONNECT_TIMEOUT = 2;

    /** @var Pheanstalk $pheanstalk */
    protected $pheanstalk;

    protected function setUp()
    {
        parent::setUp();
        // Drain
        $this->pheanstalk = $this->getPheanstalk();
    }

    /**
     * @expectedException \Pheanstalk\Exception\ServerDuplicateEntryException
     */
    public function testCreate()
    {
        $task = new Task('/bin/echo test', 'testTube');
        $job = new Job(new ArrayCollection([$task]));
        $workflow = new Workflow('testWorkflow', 'tests', new ArrayCollection([$job]), 'testComment');
        $createdWorkflow = $this->pheanstalk->create($workflow);
        $this->assertSame($workflow, $createdWorkflow);
        $task->setPath('/bin/echo second test');
        $job->setTasks(new ArrayCollection([$task]));
        $workflow->setJobs(new ArrayCollection([$job]));
        $createdWorkflow = $this->pheanstalk->create($workflow, true);
        $this->assertSame($workflow, $createdWorkflow);
        $this->pheanstalk->create($workflow);
    }

    public function testWorkflowExists()
    {
        $workflow = $this->pheanstalk->workflowExists('testWorkflow');
        $this->assertInstanceOf(Workflow::class, $workflow);
        $this->assertFalse($this->pheanstalk->workflowExists('testNotExistantWorkflow'));
    }

    public function testUpdate()
    {
        $workflow = $this->pheanstalk->workflowExists('testWorkflow');
        $workflow->setComment('testCommentModified');
        $this->assertSame($workflow, $this->pheanstalk->update($workflow));
    }

    public function testEmptyWorkflowInstances()
    {
        $workflow = $this->pheanstalk->workflowExists('testWorkflow');
        $instances = $this->pheanstalk->getWorkflowInstances($workflow);
        foreach(GetWorkflowInstancesCommand::FILTERS as $filter) {
            $this->assertNotNull($FilterInstances = $instances->get(strtolower($filter)));
            $workflowInstances = $FilterInstances->get('workflow_instances');
            $this->assertNull($workflowInstances);
        }
    }

    public function testPut()
    {
        $workflow = $this->pheanstalk->workflowExists('testWorkflow');
        $this->assertNotEmpty($this->pheanstalk->put($workflow));
    }

    public function testGetWorkflowInstances()
    {
        $workflow = $this->pheanstalk->workflowExists('testWorkflow');
        $id = (int) $this->pheanstalk->put($workflow);
        $instances = $this->pheanstalk->getWorkflowInstances($workflow);
        foreach(GetWorkflowInstancesCommand::FILTERS as $filter) {
            $this->assertNotNull($FilterInstances = $instances->get(strtolower($filter)));
            $workflowInstances = $FilterInstances->get('workflow_instances');
            /** @var ArrayCollection $workflowInstances */
            if(!is_null($workflowInstances)) {
                $this->assertFalse($workflowInstances->isEmpty());
            }
        }
    }

    public function testListTubes()
    {
        $tubes = $this->pheanstalk->listTubes();
        $tubes = $tubes->filter(function(Tube $tube) {
           return $tube->getName() === 'testTube';
        });
        $this->assertFalse($tubes->isEmpty());
    }

    public function testPeek()
    {
        $workflow = $this->pheanstalk->createTask('testSleep', 'testGroup', '/bin/sleep 1');
        $this->assertFalse($this->pheanstalk->peek());
        $this->pheanstalk->put($workflow);
        $this->assertNotEmpty($this->pheanstalk->peek());
        $this->pheanstalk->put($workflow);
        $this->pheanstalk->put($workflow);
        $this->pheanstalk->put($workflow);
        $this->pheanstalk->put($workflow);
        $this->assertNotEmpty($this->pheanstalk->peek());
        $this->assertTrue($this->pheanstalk->delete($workflow));
    }

    public function testStatsTube()
    {
        $tube = $this->pheanstalk->tubeExists('testTube');
        $this->assertNotEmpty($this->pheanstalk->statsTube($tube));
    }

    public function testStats()
    {
        $this->assertTrue(isset($this->pheanstalk->stats()['statistics']));
    }

    public function testCreateSchedule()
    {
        $workflow = $this->pheanstalk->workflowExists('testWorkflow');
        $schedule = new Schedule($workflow->getId(), new TimeSchedule());
        $this->pheanstalk->createSchedule($schedule);
        $this->assertNotNull($schedule->getId());
        $recoveredSchedule = $this->pheanstalk->getSchedule($schedule->getId());
        $this->assertSame($schedule->getId(), $recoveredSchedule->getId());
        $this->assertTrue($this->pheanstalk->deleteSchedule($schedule));
    }

    public function testUpdateTube()
    {
        $tube = $this->pheanstalk->tubeExists('testTube');
        $tube->setName('testTubeModified');
        $this->assertSame($tube, $this->pheanstalk->updateTube($tube));
    }

    public function testCancel()
    {
        $workflow = $this->pheanstalk->createTask('testSleep', 'testGroup', '/bin/sleep 3');
        $this->pheanstalk->put($workflow);
        $instances = $this->pheanstalk->getWorkflowInstances($workflow, GetWorkflowInstancesCommand::FILTER_EXECUTING);
        $this->assertFalse($instances->isEmpty());
        $workflowInstance = $instances->first();
        $this->assertTrue($this->pheanstalk->cancel($workflowInstance));
    }

    public function testKill()
    {
        $workflow = $this->pheanstalk->createTask('testSleep', 'testGroup', '/bin/sleep 2');
        $this->pheanstalk->put($workflow);
        $instances = $this->pheanstalk->getWorkflowInstances($workflow, GetWorkflowInstancesCommand::FILTER_EXECUTING);
        $this->assertFalse($instances->isEmpty());
        /** @var WorkflowInstance $workflowInstance */
        $workflowInstance = $instances->first();
        /** @var JobInstance $jobInstance */
        $jobInstance = $workflowInstance->getJobInstances()->first();
        $taskInstance = $jobInstance->getTaskInstances()->first();
        $this->assertTrue($this->pheanstalk->kill($workflowInstance, $taskInstance));
    }

    public function testDelete()
    {
        $workflow = $this->pheanstalk->workflowExists('testWorkflow');
        $this->assertTrue($this->pheanstalk->delete($workflow));
    }


    public function testDeleteTube()
    {
        $tube = $this->pheanstalk->tubeExists('testTubeModified');
        $this->assertTrue($this->pheanstalk->deleteTube($tube));
    }

    public function testCurrentClass()
    {
        $this->pheanstalk->setCurrentClass($this->pheanstalk);
        $this->assertSame($this->pheanstalk, $this->pheanstalk->getCurrentClass());
    }

    private function getPheanstalk(): PheanstalkInterface
    {
        return $this->pheanstalk = $this->pheanstalk ?? new Pheanstalk(
            self::SERVER_HOST,
            self::SERVER_USER,
            self::SERVER_PASSWORD,
            self::SERVER_PORT
        );
    }
}

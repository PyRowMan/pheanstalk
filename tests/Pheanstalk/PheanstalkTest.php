<?php

namespace Pheanstalk;

use Doctrine\Common\Collections\ArrayCollection;
use Pheanstalk\Command\GetWorkflowInstancesCommand;
use Pheanstalk\Structure\Task;
use Pheanstalk\Structure\Job;
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
        $task = new Task('echo test', 'testTube');
        $job = new Job(new ArrayCollection([$task]));
        $workflow = new Workflow('testWorkflow', 'tests', new ArrayCollection([$job]), 'testComment');
        $createdWorkflow = $this->pheanstalk->create($workflow);
        $this->assertSame($workflow, $createdWorkflow);
        $task->setPath('echo second test');
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

    public function testPut()
    {
        $workflow = $this->pheanstalk->workflowExists('testWorkflow');
        $this->assertNotEmpty($this->pheanstalk->put($workflow));
    }

    public function testgetWorkflowInstances()
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

    public function testDelete()
    {
        $workflow = $this->pheanstalk->workflowExists('testWorkflow');
        $this->assertTrue($this->pheanstalk->delete($workflow));
    }

    public function testDeleteTube()
    {
        $tube = $this->pheanstalk->tubeExists('testTube');
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

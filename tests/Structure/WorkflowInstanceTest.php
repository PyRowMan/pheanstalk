<?php

namespace Pheanstalk\Structure;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class WorkflowInstanceTest extends TestCase
{

    public function testGetScheduleId()
    {
        $workflowInstance = new WorkflowInstance(['schedule_id' => 1]);
        $this->assertSame($workflowInstance->getScheduleId(), 1);
    }

    public function testSetScheduleId()
    {
        $workflowInstance = new WorkflowInstance([]);
        $workflowInstance->setScheduleId(1);
        $this->assertSame($workflowInstance->getScheduleId(), 1);
    }

    public function testGetNodeName()
    {
        $workflowInstance = new WorkflowInstance(['node_name' => 'testNodeName']);
        $this->assertSame($workflowInstance->getNodeName(), 'testNodeName');
    }

    public function testSetNodeName()
    {
        $workflowInstance = new WorkflowInstance([]);
        $workflowInstance->setNodeName('testNodeName');
        $this->assertSame($workflowInstance->getNodeName(), 'testNodeName');
    }

    public function testGetComment()
    {
        $workflowInstance = new WorkflowInstance(['comment' => 'testComment']);
        $this->assertSame($workflowInstance->getComment(), 'testComment');
    }

    public function testSetComment()
    {
        $workflowInstance = new WorkflowInstance([]);
        $workflowInstance->setComment('testComment');
        $this->assertSame($workflowInstance->getComment(), 'testComment');
    }

    public function testGetErrors()
    {
        $workflowInstance = new WorkflowInstance(['errors' => 1]);
        $this->assertSame($workflowInstance->getErrors(), 1);
    }

    public function testSetErrors()
    {
        $workflowInstance = new WorkflowInstance([]);
        $workflowInstance->setErrors(1);
        $this->assertSame($workflowInstance->getErrors(), 1);
    }

    public function testGetStatus()
    {
        $workflowInstance = new WorkflowInstance(['status' => 'testStatus']);
        $this->assertSame($workflowInstance->getStatus(), 'testStatus');
    }

    public function testSetStatus()
    {
        $workflowInstance = new WorkflowInstance([]);
        $workflowInstance->setStatus('testStatus');
        $this->assertSame($workflowInstance->getStatus(), 'testStatus');
    }

    public function testGetId()
    {
        $workflowInstance = new WorkflowInstance(['id' => 1]);
        $this->assertSame($workflowInstance->getId(), 1);
    }

    public function testSetId()
    {
        $workflowInstance = new WorkflowInstance([]);
        $workflowInstance->setId(1);
        $this->assertSame($workflowInstance->getId(), 1);
    }

    public function testGetName()
    {
        $workflowInstance = new WorkflowInstance(['name' => 'testName']);
        $this->assertSame($workflowInstance->getName(), 'testName');
    }

    public function testSetName()
    {
        $workflowInstance = new WorkflowInstance([]);
        $workflowInstance->setName('testName');
        $this->assertSame($workflowInstance->getName(), 'testName');
    }

    public function testGetRunningTasks()
    {
        $workflowInstance = new WorkflowInstance(['running_tasks' => 1]);
        $this->assertSame($workflowInstance->getRunningTasks(), 1);
    }

    public function testSetRunningTasks()
    {
        $workflowInstance = new WorkflowInstance([]);
        $workflowInstance->setRunningTasks(1);
        $this->assertSame($workflowInstance->getRunningTasks(), 1);
    }

    public function testGetRetryingTasks()
    {
        $workflowInstance = new WorkflowInstance(['retrying_tasks' => 1]);
        $this->assertSame($workflowInstance->getRetryingTasks(), 1);
    }

    public function testSetRetryingTasks()
    {
        $workflowInstance = new WorkflowInstance([]);
        $workflowInstance->setRetryingTasks(1);
        $this->assertSame($workflowInstance->getRetryingTasks(), 1);
    }

    public function testGetQueuedTasks()
    {
        $workflowInstance = new WorkflowInstance(['queued_tasks' => 1]);
        $this->assertSame($workflowInstance->getQueuedTasks(), 1);
    }

    public function testSetQueuedTasks()
    {
        $workflowInstance = new WorkflowInstance([]);
        $workflowInstance->setQueuedTasks(1);
        $this->assertSame($workflowInstance->getQueuedTasks(), 1);
    }

    public function testGetStartTime()
    {
        $date = new \DateTime();
        $workflowInstance = new WorkflowInstance(['start_time' => $date]);
        $this->assertSame($workflowInstance->getStartTime(), $date);
    }

    public function testSetStartTime()
    {
        $date = new \DateTime();
        $workflowInstance = new WorkflowInstance([]);
        $workflowInstance->setStartTime($date);
        $this->assertSame($workflowInstance->getStartTime(), $date);
    }

    public function testGetEvqid()
    {
        $workflowInstance = new WorkflowInstance(['evqid' => 2]);
        $this->assertSame($workflowInstance->getEvqid(), 2);
    }

    public function testSetEvqid()
    {
        $workflowInstance = new WorkflowInstance([]);
        $workflowInstance->setEvqid(1);
        $this->assertSame($workflowInstance->getEvqid(), 1);
    }

    public function testGetJobInstances()
    {
        $taskInstance = new TaskInstance([]);
        $jobInstance = new JobInstance(new ArrayCollection([$taskInstance]));
        $workflowInstance = new WorkflowInstance(['job_instances' => new ArrayCollection([$jobInstance])]);
        $this->assertTrue($workflowInstance->getJobInstances()->contains($jobInstance));
    }

    public function testSetJobInstances()
    {
        $workflowInstance = new WorkflowInstance([]);
        $taskInstance = new TaskInstance([]);
        $jobInstance = new JobInstance(new ArrayCollection([$taskInstance]));
        $workflowInstance->setJobInstances(new ArrayCollection([$jobInstance]));
        $this->assertTrue($workflowInstance->getJobInstances()->contains($jobInstance));
    }

    public function testAddJobInstance()
    {
        $workflowInstance = new WorkflowInstance([]);
        $taskInstance = new TaskInstance([]);
        $jobInstance = new JobInstance(new ArrayCollection([$taskInstance]));
        $workflowInstance->addJobInstance($jobInstance);
        $this->assertTrue($workflowInstance->getJobInstances()->contains($jobInstance));
    }

    public function testRemoveJobInstance()
    {
        $workflowInstance = new WorkflowInstance([]);
        $taskInstance = new TaskInstance([]);
        $jobInstance = new JobInstance(new ArrayCollection([$taskInstance]));
        $workflowInstance->addJobInstance($jobInstance);
        $this->assertTrue($workflowInstance->getJobInstances()->contains($jobInstance));
        $workflowInstance->removeJobInstance($jobInstance);
        $this->assertFalse($workflowInstance->getJobInstances()->contains($jobInstance));
    }

    public function testGetHost()
    {
        $workflowInstance = new WorkflowInstance(['host' => 'testHost']);
        $this->assertSame($workflowInstance->getHost(), 'testHost');
    }

    public function testSetHost()
    {
        $workflowInstance = new WorkflowInstance([]);
        $workflowInstance->setHost('testHost');
        $this->assertSame($workflowInstance->getHost(), 'testHost');
    }

    public function testGetEndTime()
    {
        $date = new \DateTime();
        $workflowInstance = new WorkflowInstance(['end_time' => $date]);
        $this->assertSame($workflowInstance->getEndTime(), $date);
    }

    public function testSetEndTime()
    {
        $date = new \DateTime();
        $workflowInstance = new WorkflowInstance([]);
        $workflowInstance->setEndTime($date);
        $this->assertSame($workflowInstance->getEndTime(), $date);
    }

    public function testUpdateStatus()
    {
        $workflowInstance = new WorkflowInstance(['running_tasks' => 2, 'queued_tasks' => 0]);
        $this->assertSame($workflowInstance->getStatus(), WorkflowInstance::STATUS_RUNNING);
        $workflowInstance = new WorkflowInstance(['queued_tasks' => 1]);
        $this->assertSame($workflowInstance->getStatus(), WorkflowInstance::STATUS_QUEUED);
        $workflowInstance = new WorkflowInstance(['retrying_tasks' => 1]);
        $this->assertSame($workflowInstance->getStatus(), WorkflowInstance::STATUS_RETRYING);
        $workflowInstance = new WorkflowInstance(['errors' => 1]);
        $this->assertSame($workflowInstance->getStatus(), WorkflowInstance::STATUS_FAILED);
    }
}

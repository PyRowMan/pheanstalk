<?php

namespace Pheanstalk;

use Doctrine\Common\Collections\ArrayCollection;
use Pheanstalk\Structure\Task;
use Pheanstalk\Structure\Job;
use Pheanstalk\Structure\Workflow;
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

    public function testCreate()
    {
        $task = new Task('echo test', 'default');
        $job = new Job(new ArrayCollection([$task]));
        $workflow = new Workflow('testWorkflow', 'tests', new ArrayCollection([$job]), 'testComment');
        $createdWorkflow = $this->pheanstalk->create($workflow);
        $this->assertSame($workflow, $createdWorkflow);
    }

    public function testDelete()
    {
        $workflow = $this->pheanstalk->workflowExists('testWorkflow');
        $this->assertTrue($this->pheanstalk->delete($workflow));
    }

    private function getPheanstalk(): PheanstalkInterface
    {
        return new Pheanstalk(
            self::SERVER_HOST,
            self::SERVER_USER,
            self::SERVER_PASSWORD,
            self::SERVER_PORT
        );
    }
}

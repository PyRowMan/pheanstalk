<?php

namespace Pheanstalk\Structure;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class TubeTest extends TestCase
{
    /** @var Tube $tube */
    protected $tube;

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->tube = new Tube('testTube', 1);
    }

    public function testId()
    {
        $this->tube->setId(1);
        $this->assertSame(1, $this->tube->getId());
    }

    public function testConcurrency()
    {
        $this->tube->setConcurrency(1);
        $this->assertSame(1, $this->tube->getConcurrency());
    }

    public function testDynamic()
    {
        $this->assertSame(Tube::IS_DYNAMIC, $this->tube->getDynamic());
        $this->tube->setDynamic(false);
        $this->assertSame(Tube::IS_NOT_DYNAMIC, $this->tube->getDynamic());
    }

    public function testName()
    {
        $this->tube->setName('1');
        $this->assertSame('1', $this->tube->getName());
    }

    public function testScheduler()
    {
        $this->tube->setScheduler('1');
        $this->assertSame('1', $this->tube->getScheduler());
    }
}

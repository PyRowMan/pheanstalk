<?php

namespace Pheanstalk\Socket;

use PHPUnit\Framework\TestCase;

class WriteHistoryTest extends TestCase
{
    /** @var WriteHistory $object */
    protected $object;

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->object = new WriteHistory(1);
    }

    public function testIsFull()
    {
        $this->assertFalse($this->object->isFull());
    }

    public function testHasWrites()
    {
        $this->assertFalse($this->object->hasWrites());
    }

    public function testIsFullWithNoWrites()
    {
        $this->assertFalse($this->object->isFullWithNoWrites());
    }

    public function testLog()
    {
        $this->assertSame(1, $this->object->log(1));
        $this->assertTrue($this->object->isFull());
        $this->assertSame(1, $this->object->log(1));
    }
}

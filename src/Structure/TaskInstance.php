<?php

namespace Pheanstalk\Structure;

/**
 * A job in a EvQueue server.
 *
 * @author  Valentin Corre
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class TaskInstance extends Task
{
    
    /** @var int $contextId */
    protected $contextId;

    /** @var int $evqid */
    protected $evqid;

    /** @var \DateTime $execution_time */
    protected $execution_time;

    /** @var int $pid */
    protected $pid;

    /** @var int $progression */
    protected $progression;

    /** @var string $status */
    protected $status;

    /** @var int $tid */
    protected $tid;

    public function __construct(array $params)
    {
        $thisObject = new \ReflectionClass($this);
        $properties = $thisObject->getProperties();
        foreach ($properties as $property) {
            $snakeProperty = $this->from_camel_case($property->getName());
            if (isset($params[$snakeProperty]))
                $this->{$property->getName()} = $params[$snakeProperty];
        }
    }

    /**
     * @return int
     */
    public function getContextId(): int
    {
        return $this->contextId;
    }

    /**
     * @param int $contextId
     *
     * @return TaskInstance
     */
    public function setContextId(int $contextId): TaskInstance
    {
        $this->contextId = $contextId;
        return $this;
    }

    /**
     * @return int
     */
    public function getEvqid(): int
    {
        return $this->evqid;
    }

    /**
     * @param int $evqid
     *
     * @return TaskInstance
     */
    public function setEvqid(int $evqid): TaskInstance
    {
        $this->evqid = $evqid;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExecutionTime(): \DateTime
    {
        return $this->execution_time;
    }

    /**
     * @param \DateTime $execution_time
     *
     * @return TaskInstance
     */
    public function setExecutionTime(\DateTime $execution_time): TaskInstance
    {
        $this->execution_time = $execution_time;
        return $this;
    }

    /**
     * @return int
     */
    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * @param int $pid
     *
     * @return TaskInstance
     */
    public function setPid(int $pid): TaskInstance
    {
        $this->pid = $pid;
        return $this;
    }

    /**
     * @return int
     */
    public function getProgression(): int
    {
        return $this->progression;
    }

    /**
     * @param int $progression
     *
     * @return TaskInstance
     */
    public function setProgression(int $progression): TaskInstance
    {
        $this->progression = $progression;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return TaskInstance
     */
    public function setStatus(string $status): TaskInstance
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return int
     */
    public function getTid(): int
    {
        return $this->tid;
    }

    /**
     * @param int $tid
     *
     * @return TaskInstance
     */
    public function setTid(int $tid): TaskInstance
    {
        $this->tid = $tid;
        return $this;
    }
}

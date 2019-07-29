<?php


namespace Pheanstalk\Structure;

use Doctrine\Common\Collections\ArrayCollection;
use Pheanstalk\Exception\ServerBadFormatException;

class WorkflowInstance
{
//@running_tasks - @queued_tasks > 0

    const STATUS_QUEUED = "QUEUED";
    const STATUS_RUNNING = "RUNNING";
    const STATUS_RETRYING = "RETRYING";
    const STATUS_FAILED = "FAILED";

    /** @var string|null $comment */
    private $comment;

    /** @var \DateTime|null $endTime */
    private $endTime;

    /** @var int|null $errors */
    private $errors;

    /** @var string|null $host */
    private $host;

    /** @var int $id */
    private $id;

    /** @var string $name */
    private $name;

    /** @var string|null $nodeName */
    private $nodeName;

    /** @var int|null $scheduleId */
    private $scheduleId;

    /** @var \DateTime $startTime */
    private $startTime;

    /** @var string $status */
    private $status;

    /** @var int|null $evqid */
    private $evqid;

    /** @var int|null $queuedTasks */
    private $queuedTasks;

    /** @var int|null $runningTasks */
    private $runningTasks;

    /** @var int|null $retryingTasks */
    private $retryingTasks;

    /** @var ArrayCollection */
    private $jobInstances;

    /**
     * WorkflowInstance constructor.
     *
     * @param array $params
     *
     * @throws \ReflectionException
     */
    public function __construct(array $params)
    {
        $this->jobInstances = new ArrayCollection([]);
        $thisObject = new \ReflectionClass($this);
        $properties = $thisObject->getProperties();
        foreach ($properties as $property) {
            $snakeProperty = $this->fromCamelCase($property->getName());
            if (isset($params[$snakeProperty])) {
                $this->{$property->getName()} = $params[$snakeProperty];
            }
        }
        $this->updateStatus();
    }

    /**
     * @return null|string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     *
     * @return WorkflowInstance
     */
    public function setComment(string $comment): WorkflowInstance
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return null|\DateTime
     */
    public function getEndTime(): ?\DateTime
    {
        return $this->endTime;
    }

    /**
     * @param \DateTime $endTime
     *
     * @return WorkflowInstance
     */
    public function setEndTime(\DateTime $endTime): WorkflowInstance
    {
        $this->endTime = $endTime;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getErrors(): ?int
    {
        return $this->errors ?? 0;
    }

    /**
     * @param null|int $errors
     *
     * @return WorkflowInstance
     */
    public function setErrors(?int $errors): WorkflowInstance
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @param string $host
     *
     * @return WorkflowInstance
     */
    public function setHost(string $host): WorkflowInstance
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return WorkflowInstance
     */
    public function setId(int $id): WorkflowInstance
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return WorkflowInstance
     */
    public function setName(string $name): WorkflowInstance
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getNodeName(): ?string
    {
        return $this->nodeName;
    }

    /**
     * @param string $nodeName
     *
     * @return WorkflowInstance
     */
    public function setNodeName(string $nodeName): WorkflowInstance
    {
        $this->nodeName = $nodeName;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getScheduleId(): ?int
    {
        return $this->scheduleId;
    }

    /**
     * @param int $scheduleId
     *
     * @return WorkflowInstance
     */
    public function setScheduleId(int $scheduleId): WorkflowInstance
    {
        $this->scheduleId = $scheduleId;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime(): \DateTime
    {
        return $this->startTime;
    }

    /**
     * @param \DateTime $startTime
     *
     * @return WorkflowInstance
     */
    public function setStartTime(\DateTime $startTime): WorkflowInstance
    {
        $this->startTime = $startTime;
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
     *
     */
    public function updateStatus()
    {
        if ($this->getRunningTasks() > 0) {
            $this->setStatus(self::STATUS_RUNNING);
        }
        if ($this->getQueuedTasks() > 0) {
            $this->setStatus(self::STATUS_QUEUED);
        }
        if ($this->getRetryingTasks() > 0) {
            $this->setStatus(self::STATUS_RETRYING);
        }
        if ($this->getErrors() > 0) {
            $this->setStatus(self::STATUS_FAILED);
        }
    }

    /**
     * @param string $status
     *
     * @return WorkflowInstance
     */
    public function setStatus(string $status): WorkflowInstance
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getEvqid(): ?int
    {
        return $this->evqid;
    }

    /**
     * @param int|null $evqid
     *
     * @return WorkflowInstance
     */
    public function setEvqid(?int $evqid): WorkflowInstance
    {
        $this->evqid = $evqid;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getQueuedTasks(): ?int
    {
        return $this->queuedTasks ?? 0;
    }

    /**
     * @param int|null $queuedTasks
     *
     * @return WorkflowInstance
     */
    public function setQueuedTasks(?int $queuedTasks): WorkflowInstance
    {
        $this->queuedTasks = $queuedTasks;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getRunningTasks(): ?int
    {
        return $this->runningTasks ?? 0;
    }

    /**
     * @param int|null $runningTasks
     *
     * @return WorkflowInstance
     */
    public function setRunningTasks(?int $runningTasks): WorkflowInstance
    {
        $this->runningTasks = $runningTasks;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getRetryingTasks(): ?int
    {
        return $this->retryingTasks ?? 0;
    }

    /**
     * @param int|null $retryingTasks
     *
     * @return WorkflowInstance
     */
    public function setRetryingTasks(?int $retryingTasks): WorkflowInstance
    {
        $this->retryingTasks = $retryingTasks;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getJobInstances(): ArrayCollection
    {
        return $this->jobInstances;
    }

    /**
     * @param ArrayCollection $jobInstances
     *
     * @return WorkflowInstance
     */
    public function setJobInstances(ArrayCollection $jobInstances): WorkflowInstance
    {
        $this->jobInstances = $jobInstances->filter(function(JobInstance $jobInstance) {
            return true;
        });
        return $this;
    }

    /**
     * @param JobInstance $jobInstance
     *
     * @return WorkflowInstance
     */
    public function addJobInstance(JobInstance $jobInstance): WorkflowInstance
    {
        $this->jobInstances[] = $jobInstance;
        return $this;
    }

    /**
     * @param JobInstance $jobInstance
     *
     * @return WorkflowInstance
     */
    public function removeJobInstance(JobInstance $jobInstance): WorkflowInstance
    {
        $this->jobInstances->removeElement($jobInstance);
        return $this;
    }

    /**
     * @param $input
     *
     * @return string
     */
    private function fromCamelCase($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }
}

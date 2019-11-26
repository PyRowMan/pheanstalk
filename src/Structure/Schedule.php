<?php


namespace Pheanstalk\Structure;

class Schedule
{
    const FAILURE_TYPE_CONTINUE = "CONTINUE";
    const FAILURE_TYPE_SUSPEND = "SUSPEND";

    /** @var int|null $id */
    protected $id;

    /** @var bool $active */
    protected $active;

    /** @var string|null $comment */
    protected $comment;

    /** @var string|null $host */
    protected $host;

    /** @var string|null $node */
    protected $node;

    /** @var string|null $node */
    protected $onFailure;

    /** @var TimeSchedule $schedule */
    protected $schedule;

    /** @var string|null $user */
    protected $user;

    /** @var int $workflow */
    protected $workflow;

    public function __construct(
        int $workflow,
        TimeSchedule $schedule,
        $onFailure = self::FAILURE_TYPE_CONTINUE,
        $active = true,
        $comment = null,
        $user = null,
        $host = null,
        $node = "any"
    ) {
        $this->workflow = $workflow;
        $this->schedule = $schedule;
        $this->onFailure = $onFailure;
        $this->active = $active;
        $this->comment = $comment;
        $this->user = $user;
        $this->host = $host;
        $this->node = $node;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     *
     * @return Schedule
     */
    public function setId(?int $id): Schedule
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return Schedule
     */
    public function setActive(bool $active): Schedule
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     *
     * @return Schedule
     */
    public function setComment(?string $comment): Schedule
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @param string|null $host
     *
     * @return Schedule
     */
    public function setHost(?string $host): Schedule
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNode(): ?string
    {
        return $this->node;
    }

    /**
     * @param string|null $node
     *
     * @return Schedule
     */
    public function setNode(?string $node): Schedule
    {
        $this->node = $node;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOnFailure(): ?string
    {
        return $this->onFailure;
    }

    /**
     * @param string|null $onFailure
     *
     * @return Schedule
     */
    public function setOnFailure(?string $onFailure): Schedule
    {
        $this->onFailure = $onFailure;
        return $this;
    }

    /**
     * @return TimeSchedule
     */
    public function getSchedule(): TimeSchedule
    {
        return $this->schedule;
    }

    /**
     * @param TimeSchedule $schedule
     *
     * @return Schedule
     */
    public function setSchedule(TimeSchedule $schedule): Schedule
    {
        $this->schedule = $schedule;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUser(): ?string
    {
        return $this->user;
    }

    /**
     * @param string|null $user
     *
     * @return Schedule
     */
    public function setUser(?string $user): Schedule
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return int
     */
    public function getWorkflow(): int
    {
        return $this->workflow;
    }

    /**
     * @param int $workflow
     *
     * @return Schedule
     */
    public function setWorkflow(int $workflow): Schedule
    {
        $this->workflow = $workflow;
        return $this;
    }
}

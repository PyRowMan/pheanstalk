<?php


namespace Pheanstalk\Structure;


class Queue
{

    /** @var int $id */
    private $id;

    /** @var int $concurrency */
    private $concurrency;

    /** @var string $dynamic */
    private $dynamic;

    /** @var string $name */
    private $name;

    /** @var string $scheduler */
    private $scheduler;

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
     * @return Queue
     */
    public function setId(int $id): Queue
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getConcurrency(): int
    {
        return $this->concurrency;
    }

    /**
     * @param int $concurrency
     *
     * @return Queue
     */
    public function setConcurrency(int $concurrency): Queue
    {
        $this->concurrency = $concurrency;
        return $this;
    }

    /**
     * @return string
     */
    public function getDynamic(): string
    {
        return $this->dynamic;
    }

    /**
     * @param string $dynamic
     *
     * @return Queue
     */
    public function setDynamic(string $dynamic): Queue
    {
        $this->dynamic = $dynamic;
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
     * @return Queue
     */
    public function setName(string $name): Queue
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getScheduler(): string
    {
        return $this->scheduler;
    }

    /**
     * @param string $scheduler
     *
     * @return Queue
     */
    public function setScheduler(string $scheduler): Queue
    {
        $this->scheduler = $scheduler;
        return $this;
    }


}
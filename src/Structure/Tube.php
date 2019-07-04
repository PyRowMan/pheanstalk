<?php


namespace Pheanstalk\Structure;


class Tube
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
     * @return Tube
     */
    public function setId(int $id): Tube
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
     * @return Tube
     */
    public function setConcurrency(int $concurrency): Tube
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
     * @return Tube
     */
    public function setDynamic(string $dynamic): Tube
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
     * @return Tube
     */
    public function setName(string $name): Tube
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
     * @return Tube
     */
    public function setScheduler(string $scheduler): Tube
    {
        $this->scheduler = $scheduler;
        return $this;
    }


}
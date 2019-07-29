<?php


namespace Pheanstalk\Structure;

class Tube
{

    const SCHEDULER_TYPE_DEFAULT = 'default';
    const SCHEDULER_TYPE_FIFO = 'fifo';
    const SCHEDULER_TYPE_PRIO = 'prio';

    const IS_DYNAMIC = 'yes';
    const IS_NOT_DYNAMIC = 'no';

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
     * Tube constructor.
     *
     * @param string      $name          The name of the tube
     * @param int         $concurrency   The number of workflows that can be executed simultaneously
     * @param string      $scheduler     WIP
     * @param string|bool $dynamic       Wether the tube is dynamic or not
     */
    public function __construct(string $name, int $concurrency, $scheduler = self::SCHEDULER_TYPE_DEFAULT, $dynamic = true)
    {
        $this->name = $name;
        $this->concurrency = $concurrency;
        $this->scheduler = $scheduler;
        $this->setDynamic($dynamic);
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
        $dynamic = $this->dynamic ?? true;
        return ($dynamic) ? self::IS_DYNAMIC : self::IS_NOT_DYNAMIC;
    }

    /**
     * @param string|bool $dynamic
     *
     * @return Tube
     */
    public function setDynamic($dynamic): Tube
    {
        if (!is_bool($dynamic)) {
            if ($dynamic === self::IS_NOT_DYNAMIC) {
                $dynamic = false;
            } else {
                $dynamic = true;
            }
        }
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
        return $this->scheduler ?? self::SCHEDULER_TYPE_DEFAULT;
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

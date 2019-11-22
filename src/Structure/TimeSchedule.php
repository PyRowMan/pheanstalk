<?php


namespace Pheanstalk\Structure;

class TimeSchedule
{
    const SUM_SUBUNIT_IN_UNIT = 1770;
    const SUM_HOURS = 276;
    const SUM_DAYS = 465;
    const SUM_MONTHS = 66;
    const SUM_WEEKDAYS = 21;

    /** @var array[int] $seconds */
    private $seconds;

    /** @var array[int] $minutes */
    private $minutes;

    /** @var array[int] $hours */
    private $hours;

    /** @var array[int] $hours */
    private $days;

    /** @var array[int] $months */
    private $months;

    /** @var array[int] $weekdays */
    private $weekdays;

    /**
     * TimeSchedule constructor.
     *
     * @param array[int] $seconds
     * @param array[int] $minutes
     * @param array[int] $hours
     * @param array[int] $months
     * @param array[int] $weekdays
     */
    public function __construct(array $seconds = [], array $minutes = [], array $hours = [], array $days = [], array $months = [], array $weekdays = [])
    {
        $this
            ->setSeconds($seconds)
            ->setMinutes($minutes)
            ->setHours($hours)
            ->setDays($days)
            ->setMonths($months)
            ->setWeekdays($weekdays)
        ;
    }

    /**
     * @return array[int]
     */
    public function getSeconds(): array
    {
        return $this->seconds;
    }

    /**
     * @param array[int] $seconds
     *
     * @return TimeSchedule
     */
    public function setSeconds(array $seconds): TimeSchedule
    {
        if (count($seconds) !== count(array_filter($seconds, 'is_numeric')) || array_sum($seconds) > self::SUM_SUBUNIT_IN_UNIT) {
            throw new \Exception('Wrong units of time');
        }
        $this->seconds = $seconds;
        return $this;
    }

    /**
     * @return array[int]
     */
    public function getMinutes(): array
    {
        return $this->minutes;
    }

    /**
     * @param array[int] $minutes
     *
     * @return TimeSchedule
     */
    public function setMinutes(array $minutes): TimeSchedule
    {
        if (count($minutes) !== count(array_filter($minutes, 'is_numeric')) || array_sum($minutes) > self::SUM_SUBUNIT_IN_UNIT) {
            throw new \Exception('Wrong units of time');
        }
        $this->minutes = $minutes;
        return $this;
    }

    /**
     * @return array[int]
     */
    public function getHours(): array
    {
        return $this->hours;
    }

    /**
     * @param array[int] $hours
     *
     * @return TimeSchedule
     */
    public function setHours(array $hours): TimeSchedule
    {
        if (count($hours) !== count(array_filter($hours, 'is_numeric')) || array_sum($hours) > self::SUM_HOURS) {
            throw new \Exception('Wrong units of time');
        }
        $this->hours = $hours;
        return $this;
    }

    /**
     * @return array
     */
    public function getDays(): array
    {
        return $this->days;
    }

    /**
     * @param array $days
     *
     * @return TimeSchedule
     */
    public function setDays(array $days): TimeSchedule
    {
        if (count($days) !== count(array_filter($days, 'is_numeric')) || array_sum($days) > self::SUM_DAYS) {
            throw new \Exception('Wrong units of time');
        }
        $this->days = $days;
        return $this;
    }

    /**
     * @return array[int]
     */
    public function getMonths(): array
    {
        return $this->months;
    }

    /**
     * @param array[int] $months
     *
     * @return TimeSchedule
     */
    public function setMonths(array $months): TimeSchedule
    {
        if (count($months) !== count(array_filter($months, 'is_numeric')) || array_sum($months) > self::SUM_MONTHS) {
            throw new \Exception('Wrong units of time');
        }
        $this->months = $months;
        return $this;
    }

    /**
     * @return array[int]
     */
    public function getWeekdays(): array
    {
        return $this->weekdays;
    }

    /**
     * @param array[int] $weekdays
     *
     * @return TimeSchedule
     */
    public function setWeekdays(array $weekdays): TimeSchedule
    {
        $this->weekdays = $weekdays;
        return $this;
    }

    public function __toString()
    {
        return implode(",", $this->getSeconds()).";".
            implode(",", $this->getMinutes()).";".
            implode(",", $this->getHours()).";".
            implode(",", $this->getDays()).";".
            implode(",", $this->getMonths()).";".
            implode(",", $this->getWeekdays())
        ;
    }

    public function __fromString($time)
    {
        $times = explode(';', $time);
        $arrayOfTimes = [];
        foreach($times as $time) {
            $arrayOfTimes[] = (empty($time)) ? [] : explode(',', $time);
        }
        $this->__construct($arrayOfTimes[0], $arrayOfTimes[1], $arrayOfTimes[2], $arrayOfTimes[3], $arrayOfTimes[4], $arrayOfTimes[5]);
        return $this;
    }
}

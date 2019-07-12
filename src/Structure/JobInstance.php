<?php


namespace Pheanstalk\Structure;

use Doctrine\Common\Collections\ArrayCollection;

class JobInstance
{
    /** @var ArrayCollection[TaskInstance] */
    private $taskInstances;

    /**
     * JobInstance constructor.
     *
     * @param ArrayCollection[TaskInstance] $taskInstances
     */
    public function __construct(ArrayCollection $taskInstances)
    {
        $this->setTaskInstances($taskInstances);
    }

    /**
     * @return ArrayCollection
     */
    public function getTaskInstances(): ArrayCollection
    {
        return $this->taskInstances;
    }

    /**
     * @param ArrayCollection $taskInstances
     *
     * @return JobInstance
     */
    public function setTaskInstances(ArrayCollection $taskInstances): JobInstance
    {
        $this->taskInstances = $taskInstances->filter(function(TaskInstance $taskInstance) {
            return true;
        });
        return $this;
    }

    /**
     * @param TaskInstance $taskInstance
     *
     * @return JobInstance
     */
    public function addTaskInstance(TaskInstance $taskInstance): JobInstance
    {
        $this->taskInstances[] = $taskInstance;
        return $this;
    }

    /**
     * @param TaskInstance $taskInstance
     *
     * @return JobInstance
     */
    public function removeTaskInstance(TaskInstance $taskInstance): JobInstance
    {
        $this->taskInstances->removeElement($taskInstance);
        return $this;
    }
}
<?php


namespace Pheanstalk\Structure;

use Doctrine\Common\Collections\ArrayCollection;

class Job
{
    /** @var ArrayCollection[Task] */
    private $tasks;

    /**
     * Job constructor.
     *
     * @param ArrayCollection[Task] $tasks
     */
    public function __construct(ArrayCollection $tasks)
    {
        $this->setTasks($tasks);
    }

    /**
     * @return ArrayCollection
     */
    public function getTasks(): ArrayCollection
    {
        return $this->tasks;
    }

    /**
     * @param ArrayCollection $tasks
     *
     * @return Job
     */
    public function setTasks(ArrayCollection $tasks): Job
    {
        $this->tasks = $tasks->filter(function(Task $task) {
            return true;
        });
        return $this;
    }

    /**
     * @param Task $task
     *
     * @return Job
     */
    public function addTask(Task $task): Job
    {
        $this->tasks[] = $task;
        return $this;
    }

    /**
     * @param Task $task
     *
     * @return Job
     */
    public function removeTask(Task $task): Job
    {
        $this->tasks->removeElement($task);
        return $this;
    }

    /**
     * @return \DOMDocument
     * @throws \ReflectionException
     */
    public function getXml()
    {
        $dom = new \DOMDocument("1.0", "utf-8");
        $root = $dom->createElement("job");
        $tasks = $dom->createElement("tasks");
        /** @var Task $task */
        foreach($this->getTasks() as $task) {
            $taskItem = $dom->createElement("task");
            $taskNode = $task->getXml()->getElementsByTagName('task')->item(0);
            $tasks->appendChild($dom->importNode($taskNode, true));

        }
        $root->appendChild($tasks);
        $dom->appendChild($root);
        return $dom;
    }
}
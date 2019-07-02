<?php


namespace Pheanstalk\Structure;


use Doctrine\Common\Collections\ArrayCollection;

class Workflow
{
    /** @var $name string */
    private $name;

    /** @var string $group */
    private $group;

    /** @var ArrayCollection[Job] */
    private $jobs;

    /** @var null|string $comment */
    private $comment;

    /**
     * Job constructor.
     *
     * @param string                $name       The name of the workflow
     * @param string                $group      The group of the workflow
     * @param ArrayCollection[Job]  $jobs       The collection of jobs
     * @param null|string           $comment    The comment of the workflow
     */
    public function __construct(string $name, string $group, ArrayCollection $jobs, ?string $comment)
    {
        $this->name = $name;
        $this->group = $group;
        $this->setJobs($jobs);
        $this->comment = $comment;
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
     * @return Workflow
     */
    public function setName(string $name): Workflow
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @param string $group
     *
     * @return Workflow
     */
    public function setGroup(string $group): Workflow
    {
        $this->group = $group;
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
     * @return Workflow
     */
    public function setComment(?string $comment): Workflow
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getJobs(): ArrayCollection
    {
        return $this->jobs;
    }

    /**
     * @param ArrayCollection $jobs
     *
     * @return Workflow
     */
    public function setJobs(ArrayCollection $jobs): Workflow
    {
        $this->jobs = $jobs->filter(function(Job $job) {
            return true;
        });
        return $this;
    }

    /**
     * @param Job $job
     *
     * @return Workflow
     */
    public function addJob(Job $job): Workflow
    {
        $this->jobs[] = $job;
        return $this;
    }

    /**
     * @param Job $job
     *
     * @return Workflow
     */
    public function removeJob(Job $job): Workflow
    {
        $this->jobs->removeElement($job);
        return $this;
    }

    /**
     * @return \DOMDocument
     * @throws \ReflectionException
     */
    public function getXml()
    {
        $dom = new \DOMDocument("1.0", "utf-8");
        $root = $dom->createElement("workflow");
        $subjobs = $dom->createElement("subjobs");
        /** @var Job $job*/
        foreach($this->getJobs() as $job) {
            $jobNode = $job->getXml()->getElementsByTagName('job')->item(0);
            $subjobs->appendChild($dom->importNode($jobNode, true));

        }
        $root->appendChild($subjobs);
        $dom->appendChild($root);
        return $dom;
    }
}
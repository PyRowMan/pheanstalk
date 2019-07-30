<?php

namespace Pheanstalk\Structure;

/**
 * A job in a EvQueue server.
 *
 * @author  Valentin Corre
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class Task
{
    const OUTPUT_TEXT = "TEXT";
    const PARAMETER_MODE_CMD = "CMDLINE";
    const STRING_TRUE = 'yes';
    const STRING_FALSE = 'no';

    /** @var string $outputMethod */
    protected $outputMethod;

    /** @var string $parametersMode */
    protected $parametersMode;

    /** @var string $path */
    protected $path;

    /** @var string string $queue */
    protected $queue;

    /** @var string|null $host */
    protected $host;

    /** @var string|null $user */
    protected $user;

    /** @var bool */
    protected $useAgent;

    /**
     * @param string            $path           The command that should be executed by the server
     * @param string            $queue          The queue used by the Task
     * @param bool              $useAgent       Tell if you want to use the EvQueue Agent (Used to send status of tasks)
     * @param string|null       $user           The user that will execute the command
     * @param string|null       $host           The Ip address where to execute the command
     * @param string            $outputMethod   The output mode
     * @param string            $parametersMode The type of parameters
     */
    public function __construct(
        $path,
        $queue,
        $useAgent = false,
        $user = null,
        $host = null,
        $outputMethod = self::OUTPUT_TEXT,
        $parametersMode = self::PARAMETER_MODE_CMD
    ) {
        $this->path = $path;
        $this->queue = $queue;
        $this->useAgent = $useAgent;
        $this->user = $user;
        $this->host = $host;
        $this->outputMethod = $outputMethod;
        $this->parametersMode = $parametersMode;
    }

    /**
     * @return string
     */
    public function getOutputMethod(): string
    {
        return $this->outputMethod;
    }

    /**
     * @param string $outputMethod
     *
     * @return Task
     */
    public function setOutputMethod(string $outputMethod): Task
    {
        $this->outputMethod = $outputMethod;
        return $this;
    }

    /**
     * @return string
     */
    public function getParametersMode(): string
    {
        return $this->parametersMode;
    }

    /**
     * @param string $parametersMode
     *
     * @return Task
     */
    public function setParametersMode(string $parametersMode): Task
    {
        $this->parametersMode = $parametersMode;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return Task
     */
    public function setPath(string $path): Task
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getQueue(): string
    {
        return $this->queue;
    }

    /**
     * @param string $queue
     *
     * @return Task
     */
    public function setQueue(string $queue): Task
    {
        $this->queue = $queue;
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
     * @return Task
     */
    public function setHost(?string $host): Task
    {
        $this->host = $host;
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
     * @return Task
     */
    public function setUser(?string $user): Task
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @param bool $string
     *
     * @return bool|string
     */
    public function getUseAgent($string = true)
    {
        if ($string) {
            return $this->useAgent ? self::STRING_TRUE : self::STRING_FALSE;
        }
        return $this->useAgent;
    }

    /**
     * @param bool $agent
     *
     * @return Task
     */
    public function setUseAgent(bool $agent): Task
    {
        $this->useAgent = $agent;
        return $this;
    }

    /**
     * @return \DOMDocument
     * @throws \ReflectionException
     */
    public function getXml()
    {
        $reflection = new \ReflectionClass($this);
        $dom = new \DOMDocument("1.0", "utf-8");
        $root = $dom->createElement("task");
        foreach ($reflection->getProperties() as $property) {
            $value = $this->{'get'.ucfirst($property->getName())}();
            $root->setAttribute($this->from_camel_case($property->getName()), $value);
        }
        $dom->appendChild($root);
        return $dom;
    }

    function from_camel_case($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('-', $ret);
    }
}

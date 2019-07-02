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

    private $outputMethod;
    private $parametersMode;
    private $path;
    private $queue;

    /**
     * @param string    $path The command that should be executed by the server
     * @param string    $queue The queue used by the Task
     * @param string    $outputMethod   The output mode
     * @param string    $parametersMode The type of parameters
     */
    public function __construct($path, $queue, $outputMethod = self::OUTPUT_TEXT, $parametersMode = self::PARAMETER_MODE_CMD)
    {
        $this->path = $path;
        $this->queue = $queue;
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
     * @return \DOMDocument
     * @throws \ReflectionException
     */
    public function getXml()
    {
        $reflection = new \ReflectionClass($this);
        $dom = new \DOMDocument("1.0", "utf-8");
        $root = $dom->createElement("task");
        foreach($reflection->getProperties() as $property)
        {
            $value = $this->{'get' . ucfirst($property->getName())}();
            $root->setAttribute($this->from_camel_case($property->getName()), $value);
        }
        $dom->appendChild($root);
        return $dom;
    }

    function from_camel_case($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('-', $ret);
    }
}

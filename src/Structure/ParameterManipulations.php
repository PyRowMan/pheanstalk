<?php


namespace Pheanstalk\Structure;

use Doctrine\Common\Collections\ArrayCollection;

class ParameterManipulations
{
    /**
     * ParameterManipulations constructor.
     *
     * @param array $params
     *
     * @throws \ReflectionException
     */
    public function __construct(array $params)
    {
        $this->fillWithSnakeParams($params);
    }

    /**
     * @param        $input
     * @param string $glue
     *
     * @return string
     */
    protected function fromCamelCase($input, $glue = '_')
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode($glue, $ret);
    }

    public function fillWithSnakeParams(array $params, $glue = '_')
    {
        $thisObject = new \ReflectionClass($this);
        $properties = $thisObject->getProperties();
        foreach ($properties as $property) {
            $snakeProperty = $this->fromCamelCase($property->getName(), $glue);
            if (isset($params[$snakeProperty])) {
                $this->{$property->getName()} = $params[$snakeProperty];
            }
        }
    }
}

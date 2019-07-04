<?php

namespace Pheanstalk\Command;

use Pheanstalk\Structure\Tube;

/**
 * The 'TubeExists' command.
 *
 * Retrieve a tube by its name, if there is no tube named after the arg given in the construct, returns false
 *
 * @author  Valentin Corre
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class TubeExistsCommand extends ListTubesCommand
{

    /** @var string $name */
    private $name;

    /**
     * TubeExistsCommand constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /* (non-phpdoc)
     * @see ResponseParser::parseResponse()
     */
    public function parseResponse($responseLine, $responseData)
    {

        $tubes = parent::parseResponse($responseLine, $responseData);

        $name = $this->name;
        $matchingTubes = $tubes->filter(function(Tube $tube) use ($name) {
            return $tube->getName() === $name;
        });

        return !$matchingTubes->isEmpty() ?  $matchingTubes->first() : false;
    }
}

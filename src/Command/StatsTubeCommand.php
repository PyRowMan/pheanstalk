<?php

namespace Pheanstalk\Command;

use Pheanstalk\XmlResponseParser;
use Pheanstalk\YamlResponseParser;

/**
 * The 'stats-tube' command.
 *
 * Gives statistical information about the specified tube if it exists.
 *
 * @author  Paul Annesley
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class StatsTubeCommand
    extends AbstractCommand
{
    private $_tube;

    /**
     * @param string $tube
     */
    public function __construct($tube)
    {
        $this->_tube = $tube;
    }

    /* (non-phpdoc)
     * @see Command::getCommandLine()
     */
    public function getCommandLine()
    {
        return 'queue';
    }

    /* (non-phpdoc)
     * @see Command::getResponseParser()
     */
    public function getResponseParser()
    {
        return new XmlResponseParser();
    }

    public function getData()
    {
        return [
            'action' => 'get',
            'attributes' => [
                'id' => $this->_tube
            ]
        ];
    }

    public function hasData()
    {
        return true;
    }
}

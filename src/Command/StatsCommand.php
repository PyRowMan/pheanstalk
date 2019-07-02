<?php

namespace Pheanstalk\Command;

use Pheanstalk\XmlResponseParser;
use Pheanstalk\YamlResponseParser;

/**
 * The 'stats' command.
 *
 * Statistical information about the system as a whole.
 *
 * @author  Paul Annesley
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class StatsCommand
    extends AbstractCommand
{
    /* (non-phpdoc)
     * @see Command::getCommandLine()
     */
    public function getCommandLine()
    {
        return 'statistics';
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
            'action' => 'query',
            'attributes' => [
                'type' => 'global'
            ]
        ];
    }

    public function hasData()
    {
        return true;
    }
}

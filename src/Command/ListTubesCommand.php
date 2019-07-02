<?php

namespace Pheanstalk\Command;

use Pheanstalk\XmlResponseParser;
use Pheanstalk\YamlResponseParser;

/**
 * The 'list-tubes' command.
 *
 * List all existing tubes.
 *
 * @author  Paul Annesley
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class ListTubesCommand
    extends AbstractCommand
{
    /* (non-phpdoc)
     * @see Command::getCommandLine()
     */
    public function getCommandLine()
    {
        return 'queuepool';
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
            'action' => 'list'
        ];
    }

    public function hasData()
    {
        return true;
    }
}

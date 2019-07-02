<?php

namespace Pheanstalk\Command;

use Pheanstalk\Exception;
use Pheanstalk\XmlResponseParser;

/**
 * The 'put' command.
 *
 * Inserts a job into the client's currently used tube.
 *
 * @see UseCommand
 *
 * @author  Paul Annesley
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class PutCommand
    extends AbstractCommand
//    implements \Pheanstalk\ResponseParser
{
    private $_data;
    private $_priority;
    private $_delay;
    private $_ttr;

    /**
     * Puts a job on the queue.
     *
     * @param string $data     The job data
     * @param int    $priority From 0 (most urgent) to 0xFFFFFFFF (least urgent)
     * @param int    $delay    Seconds to wait before job becomes ready
     * @param int    $ttr      Time To Run: seconds a job can be reserved for
     */
    public function __construct($data, $priority, $delay, $ttr)
    {
        $this->_data = $data;
        $this->_priority = $priority;
        $this->_delay = $delay;
        $this->_ttr = $ttr;
    }

    /* (non-phpdoc)
     * @see Command::getCommandLine()
     */
    public function getCommandLine()
    {
        return 'task';
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
            'action' => 'create',
            'attributes' => [
//                'id' => uniqid(),
                'name' => $this->_data,
                'binary' => $this->_data,
                'parameters_mode' => 'CMDLINE',
                'output_method' => 'TEXT',
                'comment' => ' '
            ]
        ];
    }

    public function hasData()
    {
        return true;
    }
}

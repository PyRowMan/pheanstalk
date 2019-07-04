<?php

namespace Pheanstalk\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Pheanstalk\Structure\Queue;
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
    implements \Pheanstalk\ResponseParser
{
    /* (non-phpdoc)
     * @see Command::getCommandLine()
     */
    public function getCommandLine()
    {
        return 'queuepool';
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

    public function parseResponse($responseLine, $responseData)
    {
        $responseData = $responseData['queue'];

        $queues = [];
        foreach($responseData as $queue ) {
            $queue = $queue['@attributes'];
            $queueObject = new Queue();
            $queueObject
                ->setId($queue['id'])
                ->setConcurrency($queue['concurrency'])
                ->setDynamic($queue['dynamic'])
                ->setName($queue['name'])
                ->setScheduler($queue['scheduler'])
            ;
            $queues[] = $queueObject;
        }

        return new ArrayCollection($queues);
    }
}

<?php

namespace Pheanstalk\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Pheanstalk\Structure\Queue;
use Pheanstalk\Structure\Tube;
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
class ListTubesCommand extends AbstractCommand implements \Pheanstalk\ResponseParser
{
    /**
     * @inheritDoc
     */
    public function getGroup(): string
    {
        return 'queuepool';
    }

    /**
     * @inheritDoc
     */
    public function getAction(): string
    {
        return 'list';
    }

    /**
     * @inheritDoc
     */
    public function parseResponse($responseLine, $responseData)
    {
        $responseData = $responseData['queue'];
        $queues = [];
        foreach ($responseData as $queue) {
            $queue = $queue['@attributes'] ?? $queue;
            $queueObject = new Tube($queue['name'], $queue['concurrency']);
            $queueObject
                ->setId($queue['id'])
                ->setDynamic($queue['dynamic'])
                ->setScheduler($queue['scheduler'])
            ;
            $queues[] = $queueObject;
        }

        return new ArrayCollection($queues);
    }
}

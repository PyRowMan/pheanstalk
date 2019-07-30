<?php

namespace Pheanstalk\Command;

use Pheanstalk\Structure\Tube;
use Pheanstalk\Structure\Workflow;

/**
 * The 'deleteTube' command.
 *
 * Permanently deletes a tube.
 *
 * @author  Paul Annesley
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class DeleteTubeCommand extends AbstractCommand implements \Pheanstalk\ResponseParser
{
    /** @var Tube $tube */
    private $tube;

    /**
     * @param Tube $tube
     */
    public function __construct(Tube $tube)
    {
        $this->tube = $tube;
    }

    /**
     * @inheritDoc
     */
    public function getGroup(): string
    {
        return 'queue';
    }

    /**
     * @inheritDoc
     */
    public function getAction(): string
    {
        return 'delete';
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return [
            'id' => $this->tube->getId()
        ];
    }

    /**
     * @inheritDoc
     */
    public function getResponseParser()
    {
        return $this;
    }

    /**
     * @inheritDoc
     * @return bool
     */
    public function parseResponse($responseLine, $responseData)
    {
        return $responseLine === 'OK';
    }
}

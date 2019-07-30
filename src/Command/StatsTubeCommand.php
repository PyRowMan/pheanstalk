<?php

namespace Pheanstalk\Command;

use Pheanstalk\Structure\Tube;
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
class StatsTubeCommand extends AbstractCommand
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
        return 'get';
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
}

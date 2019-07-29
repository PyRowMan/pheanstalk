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
class StatsTubeCommand extends AbstractCommand
{
    private $_tube;

    /**
     * @param string $tube
     */
    public function __construct($tube)
    {
        $this->_tube = $tube;
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
    public function getResponseParser()
    {
        return new XmlResponseParser();
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
            'id' => $this->_tube
        ];
    }
}

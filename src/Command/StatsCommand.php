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
class StatsCommand extends AbstractCommand
{
    /**
     * @inheritDoc
     */
    public function getGroup(): string
    {
        return 'statistics';
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
        return 'query';
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return [
            'type' => 'global'
        ];
    }
}

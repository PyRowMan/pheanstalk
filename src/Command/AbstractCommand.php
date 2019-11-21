<?php

namespace Pheanstalk\Command;

use Pheanstalk\Command;
use Pheanstalk\Parser\XmlResponseParser;

/**
 * Common functionality for Command implementations.
 *
 * @author  Paul Annesley
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
abstract class AbstractCommand implements Command
{

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getParameters(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getResponseParser()
    {
        // concrete implementation must either:
        // a) implement ResponseParser
        // b) override this getResponseParser method
        return new XmlResponseParser();
    }
}

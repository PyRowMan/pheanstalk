<?php

namespace Pheanstalk;

/**
 * A command to be sent to the beanstalkd server, and response processing logic.
 *
 * @author  Paul Annesley
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
interface Command
{
    const COMMAND_PUT = 'put';
    const COMMAND_USE = 'use';
    const COMMAND_RESERVE = 'reserve';
    const COMMAND_DELETE = 'delete';
    const COMMAND_RELEASE = 'release';
    const COMMAND_BURY = 'bury';
    const COMMAND_WATCH = 'watch';
    const COMMAND_IGNORE = 'ignore';
    const COMMAND_PEEK = 'peek';
    const COMMAND_KICK = 'kick';
    const COMMAND_STATS_JOB = 'stats-job';
    const COMMAND_STATS = 'stats';
    const COMMAND_LIST_TUBES = 'list-tubes';
    const COMMAND_LIST_TUBE_USED = 'list-tube-used';
    const COMMAND_LIST_TUBES_WATCHED = 'list-tubes-watched';

    /**
     * The group of the command line.
     *
     * @return string
     */
    public function getGroup(): string;

    /**
     * The Action of the command line.
     *
     * @return string
     */
    public function getAction(): string;

    /**
     * The filters of the command line.
     *
     * @return array
     */
    public function getFilters(): array;

    /**
     * the parameters of the command line.
     *
     * @return array
     */
    public function getParameters(): array;

    /**
     * The response parser for the command.
     *
     * @return ResponseParser
     */
    public function getResponseParser();
}

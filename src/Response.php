<?php

namespace Pheanstalk;

/**
 * A response from the beanstalkd server.
 *
 * @author  Paul Annesley
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
interface Response
{
    // global error responses
    const RESPONSE_OUT_OF_MEMORY = 'OUT_OF_MEMORY';
    const RESPONSE_INTERNAL_ERROR = 'INTERNAL_ERROR';
    const RESPONSE_DRAINING = 'DRAINING';
    const RESPONSE_BAD_FORMAT = 'BAD_FORMAT';
    const RESPONSE_UNKNOWN_COMMAND = 'UNKNOWN_COMMAND';
    const RESPONSE_ALREADY_NO_RIGHTS = 'ALREADY_NO_RIGHTS';
    const RESPONSE_INSTANCE_ALREADY_TAGGED = 'INSTANCE_ALREADY_TAGGED';
    const RESPONSE_INSTANCE_IS_RUNNING = 'INSTANCE_IS_RUNNING';
    const RESPONSE_INVALID_BOOLEAN = 'INVALID_BOOLEAN';
    const RESPONSE_INVALID_INTEGER = 'INVALID_INTEGER';
    const RESPONSE_INVALID_MODULE = 'INVALID_MODULE';
    const RESPONSE_INVALID_PARAMETER = 'INVALID_PARAMETER';
    const RESPONSE_INVALID_SCHEDULE = 'INVALID_SCHEDULE';
    const RESPONSE_INVALID_WORKFLOW_PARAMETERS = 'INVALID_WORKFLOW_PARAMETERS';
    const RESPONSE_INVALID_XML = 'INVALID_XML';
    const RESPONSE_MISSING_PARAMETER = 'MISSING_PARAMETER';
    const RESPONSE_UNKNOWN_INSTANCE = 'UNKNOWN_INSTANCE';
    const RESPONSE_UNKNOWN_NOTIFICATION = 'UNKNOWN_NOTIFICATION';
    const RESPONSE_UNKNOWN_NOTIFICATION_TYPE = 'UNKNOWN_NOTIFICATION_TYPE';
    const RESPONSE_UNKNOWN_OBJECT = 'UNKNOWN_OBJECT';
    const RESPONSE_UNKNOWN_QUEUE = 'UNKNOWN_QUEUE';
    const RESPONSE_UNKNOWN_RETRY_SCHEDULE = 'UNKNOWN_RETRY_SCHEDULE';
    const RESPONSE_UNKNOWN_TAG = 'UNKNOWN_TAG';
    const RESPONSE_UNKNOWN_TAG_OR_INSTANCE = 'UNKNOWN_TAG_OR_INSTANCE';
    const RESPONSE_UNKNOWN_TYPE = 'UNKNOWN_TYPE';
    const RESPONSE_UNKNOWN_USER = 'UNKNOWN_USER';
    const RESPONSE_UNKNOWN_WORKFLOW = 'UNKNOWN_WORKFLOW';
    const RESPONSE_UNKNOWN_WORKFLOW_SCHEDULE = 'UNKNOWN_WORKFLOW_SCHEDULE';
    const RESPONSE_WRONG_NODE = 'WRONG_NODE';
    const RESPONSE_WORKFLOW_ALREADY_EXISTS = 'WORKFLOW_ALREADY_EXISTS';
    const RESPONSE_SQL_ERROR = 'SQL_ERROR';
    const RESPONSE_SERVER_ERROR = 'SERVER_ERROR';

    // command responses
    const RESPONSE_INSERTED = 'INSERTED';
    const RESPONSE_BURIED = 'BURIED';
    const RESPONSE_EXPECTED_CRLF = 'EXPECTED_CRLF';
    const RESPONSE_JOB_TOO_BIG = 'JOB_TOO_BIG';
    const RESPONSE_USING = 'USING';
    const RESPONSE_DEADLINE_SOON = 'DEADLINE_SOON';
    const RESPONSE_RESERVED = 'RESERVED';
    const RESPONSE_DELETED = 'DELETED';
    const RESPONSE_NOT_FOUND = 'NOT_FOUND';
    const RESPONSE_RELEASED = 'RELEASED';
    const RESPONSE_WATCHING = 'WATCHING';
    const RESPONSE_NOT_IGNORED = 'NOT_IGNORED';
    const RESPONSE_FOUND = 'FOUND';
    const RESPONSE_KICKED = 'KICKED';
    const RESPONSE_OK = 'OK';
    const RESPONSE_TIMED_OUT = 'TIMED_OUT';
    const RESPONSE_TOUCHED = 'TOUCHED';
    const RESPONSE_PAUSED = 'PAUSED';

    /**
     * The name of the response.
     *
     * @return string
     */
    public function getResponseName();
}

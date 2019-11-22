<?php


namespace Pheanstalk\Structure;


class Schedule
{
    /** @var int|null $id */
    protected $id;

    /** @var bool $active */
    protected $active;

    /** @var string|null $comment */
    protected $comment;

    /** @var string|null $host */
    protected $host;

    /** @var string|null $node */
    protected $node;

    /** @var string|null $node */
    protected $onFailure;

    /** @var TimeSchedule $schedule */
    protected $schedule;

    /** @var string|null $user */
    protected $user;

    /** @var Workflow $workflowId */
    protected $workflowId;
}
<?php

declare(strict_types=1);

namespace HP\Swoole\Helper;

use Swoole\Server\PipeMessage;
use Swoole\Server\Task;
use HP\Swoole\Interfaces\TaskEventInterface;
use HP\Swoole\BusinessWorker;

class TaskEvent implements TaskEventInterface
{
    public $worker;

    public function __construct(BusinessWorker $worker)
    {
        $this->worker = $worker;
    }

    public function onWorkerStart()
    {
    }

    public function onWorkerExit()
    {
    }

    public function onWorkerStop()
    {
    }

    public function onTask(Task $task)
    {
    }

    public function onPipeMessage(PipeMessage $pipeMessage)
    {
    }
}

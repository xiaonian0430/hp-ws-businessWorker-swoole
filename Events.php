<?php
/**
 * 业务逻辑
 * @author: Xiao Nian
 * @contact: xiaonian030@163.com
 * @datetime: 2021-09-14 10:00
 */
use App\Service\Events as ServiceEvents;
declare(strict_types=1);

use Swoole\Server\PipeMessage;
use Swoole\Server\TaskResult;
use HP\Swoole\Api;
use HP\Swoole\Helper\WorkerEvent as HelperWorkerEvent;

class Events extends HelperWorkerEvent
{
    public function onWorkerStart()
    {
    }

    public function onWorkerStop()
    {
    }

    public function onWorkerExit()
    {
    }

    public function onPipeMessage(PipeMessage $pipeMessage)
    {
    }

    public function onFinish(TaskResult $taskResult)
    {
    }

    public function onConnect(string $client, array $session)
    {

    }

    public function onReceive(string $client, array $session, string $data)
    {
        ServiceEvents::getInstance()->onMessage($client, $data);
    }

    public function onClose(string $client, array $session, array $bind)
    {
        ServiceEvents::getInstance()->onClose($client);
    }
}
<?php

declare(strict_types=1);

namespace HP\Swoole\Cmd;

use Swoole\Coroutine\Server\Connection;
use HP\Swoole\Interfaces\CmdInterface;
use HP\Swoole\Gateway;
use HP\Swoole\Protocol;

class GetClientCount implements CmdInterface
{
    public static function getCommandCode(): int
    {
        return 4;
    }

    public static function encode(): string
    {
        return pack('C', self::getCommandCode());
    }

    public static function execute(Gateway $gateway, Connection $conn, string $buffer)
    {
        $count = $gateway->getServer()->stats()['connection_num'];
        $conn->send(Protocol::encode(pack('N', $count)));
    }
}

<?php

declare(strict_types=1);

namespace HP\Swoole\Cmd;

use Swoole\Coroutine\Server\Connection;
use HP\Swoole\Interfaces\CmdInterface;
use HP\Swoole\Gateway;

class Ping implements CmdInterface
{
    public static function getCommandCode(): int
    {
        return 0;
    }

    public static function encode(): string
    {
        return pack('C', self::getCommandCode());
    }

    public static function execute(Gateway $gateway, Connection $conn, string $buffer)
    {
    }
}

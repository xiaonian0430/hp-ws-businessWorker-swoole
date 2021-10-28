<?php

declare(strict_types=1);

namespace HP\Swoole\Cmd;

use Swoole\Coroutine\Server\Connection;
use HP\Swoole\Interfaces\CmdInterface;
use HP\Swoole\Gateway;
use HP\Swoole\Protocol;

class GetClientListByGroup implements CmdInterface
{
    public static function getCommandCode(): int
    {
        return 9;
    }

    public static function encode(string $group): string
    {
        return pack('C', self::getCommandCode()) . $group;
    }

    public static function decode(string $buffer): array
    {
        return [
            'group' => $buffer,
        ];
    }

    public static function execute(Gateway $gateway, Connection $conn, string $buffer)
    {
        $data = self::decode($buffer);
        $fd_list = $gateway->group_list[$data['group']] ?? [];
        $conn->send(Protocol::encode(pack('N*', ...$fd_list)));
    }
}

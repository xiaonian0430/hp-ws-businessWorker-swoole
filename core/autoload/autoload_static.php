<?php
/**
 * @author: Xiao Nian
 * @contact: xiaonian030@163.com
 * @datetime: 2021-09-14 10:00
 */
namespace Composer\Autoload;

class ComposerStaticInit
{
    public static $prefixLengthsPsr4 = array (
        'H' =>
        array (
            'HP\\Swoole\\' => 10,
        ),
        'A' =>
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'HP\\Swoole\\' =>
        array (
            0 => __DIR__ . '/../..' . '/core/framework/swoole-worker',
        ),
        'App\\' =>
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}

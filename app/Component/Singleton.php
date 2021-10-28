<?php
/**
 * @author: Xiao Nian
 * @contact: xiaonian030@163.com
 * @datetime: 2020-12-01 14:00
 */

namespace App\Component;

/**
 * 单例模式
 */
trait Singleton {
    private static $instance;
    static function getInstance(...$args) {
        if(!isset(static::$instance)){
            static::$instance = new static(...$args);
        }
        return static::$instance;
    }
}
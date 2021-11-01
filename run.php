<?php
/**
 * 启动文件
 * @author: Xiao Nian
 * @contact: xiaonian030@163.com
 * @datetime: 2021-09-14 10:00
 */
use HP\Swoole\BusinessWorker;

//初始化
ini_set('display_errors', 'on');
defined('IN_PHAR') or define('IN_PHAR', boolval(\Phar::running(false)));
defined('SERVER_ROOT') or define('SERVER_ROOT', IN_PHAR ? \Phar::running() : realpath(getcwd()));

//创建临时目录
$temp_path=SERVER_ROOT.'/temp';
$log_path=SERVER_ROOT.'/temp/log';
if(!is_dir($log_path)){
    mkdir($log_path, 0777, true);
}

// 检查扩展或环境
if(strpos(strtolower(PHP_OS), 'win') === 0) {
    exit("start.php not support windows.\n");
}

//自动加载文件
$auto_file=SERVER_ROOT . '/vendor/autoload.php';
if (file_exists($auto_file)) {
    require_once $auto_file;
} else {
    exit("Please composer install.\n");
}

//导入配置文件
$mode='produce';
foreach ($argv as $item){
    $item_val=explode('=', $item);
    if(count($item_val)==2 && $item_val[0]=='-mode'){
        $mode=$item_val[1];
    }
}
$config_path=SERVER_ROOT . '/config/'.$mode.'.php';
if (file_exists($config_path)) {
    $conf = require_once $config_path;
}else{
    exit($config_path." is not exist\n");
}
defined('CONFIG') or define('CONFIG', $conf);

$business = new BusinessWorker();

// 设置pid文件
$business->pid_file = $temp_path . '/pid.pid';

$business->set([
    'worker_file'=> SERVER_ROOT.''
]);

// 设置服务端参数 参考:http://wiki.swoole.com/#/server/setting
$business->set([
    'log_level'=> SWOOLE_LOG_ERROR,
    'log_file'=> $log_path.'/log.log',
    'pid_file'=> $temp_path.'/pid.pid',
    'stats_file' => $temp_path . '/stats.log',
    'worker_num'=> CONFIG['HTTP_SERVER']['PROCESS_COUNT'],
    'task_tmpdir'=> $temp_path,
    'max_coroutine'=> 100000,
    'hook_flags' => SWOOLE_HOOK_ALL, // 建议开启
]);

// 设置注册中心连接参数
$business->register_host = CONFIG['REGISTER']['LAN_IP'];
$business->register_port = CONFIG['REGISTER']['LAN_PORT'];

//启动
$business->start();

#!/bin/bash
echo $#
echo $2
echo $1

# 启动
if [ "$1"  = "start" ]
then
    php run.php start -mode=test -d
fi

## 停止服务
if [ "$1"  = "stop" ]
then
    php run.php stop -mode=test
fi

## 热重启
if [ "$1"  = "reload" ]
then
    php run.php reload -mode=test -d
fi

## 强制重启
if [ "$1"  = "restart" ]
then
    php run.php restart -mode=test -d
fi
#!/bin/bash
## 零点执行该脚本
## 日志切割脚本
## Nginx 日志文件所在的目录
LOGS_PATH=/opt/nginx/logs
## 获取昨天的 yyyy-MM-dd
YESTERDAY=$(date -d "yesterday" +%Y%m%d)
## 移动文件
mv ${LOGS_PATH}/access.log ${LOGS_PATH}/access_${YESTERDAY}.log
mv ${LOGS_PATH}/error.log ${LOGS_PATH}/error_${YESTERDAY}.log
## 向 Nginx 主进程发送 USR1 信号。USR1 信号是重新打开日志文件
kill -USR1 $(cat /opt/nginx/run/nginx.pid)

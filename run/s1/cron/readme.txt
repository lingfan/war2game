minute   hour   day   month   dayofweek   command
*/1 * * * * (/usr/local/bin/php /home/eygle/tw2ohter/cron.php)
每分钟执行一次

# record the memory usage of the system every monday
# at 3:30AM in the file /tmp/meminfo
30 3 * * * cat /proc/meminfo >> /tmp/meminfo


# run custom script the first day of every month at 4:10AM
10 4 1 * * /root/scripts/backup.sh
            

minute — 分钟，从 0 到 59 之间的任何整数

hour — 小时，从 0 到 23 之间的任何整数

day — 日期，从 1 到 31 之间的任何整数（如果指定了月份，必须是该月份的有效日期）

month — 月份，从 1 到 12 之间的任何整数（或使用月份的英文简写如 jan、feb 等等）

dayofweek — 星期，从 0 到 7 之间的任何整数，这里的 0 或 7 代表星期日（或使用星期的英文简写如 sun、mon 等等）

command — 要执行的命令（命令可以是 ls /proc >> /tmp/proc 之类的命令，也可以是执行你自行编写的脚本的命令。）


如果需要按秒钟 则需要用C 来写守护进程脚本
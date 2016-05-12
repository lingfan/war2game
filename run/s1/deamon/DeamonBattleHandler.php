#!/usr/bin/env php
<?php
$commonFile = dirname(dirname(__FILE__)) . '/common.php';
include($commonFile);
declare(ticks = 1);
B_Utils::keepOnePid(__FILE__, getmypid());
$starttime = microtime(TRUE);

$bWaitFlag = false; // 是否等待进程结束
//$bWaitFlag = true; // 是否等待进程结束
$intNum = M_Battle_QueueAI::NUM + 1; // 进程总数
$pids = array(); // 进程PID数组
for ($i = 1; $i < $intNum; $i++) {
	$pids[$i] = pcntl_fork(); // 产生子进程，而且从当前行之下开试运行代码，而且不继承父进程的数据信息
	/*if($pids[$i])//父进程
	{
	//echo $pids[$i]."parent"."$i -> " . time(). "\n";
	}
	*/
	if ($pids[$i] == -1) {
		echo "couldn't fork" . "\n";
	} elseif (!$pids[$i]) {
		while (1) {
			$pid = getmypid();
			$now = microtime(true);
			//战斗的ID队列列表
			M_Battle_QueueHandler::run($i);
			$end = microtime(true);
			echo "[{$i}][{$pid}][M_Battle_QueueHandler::run]CostTime:" . sprintf("%f secs.", $end - $now) . " \n";

			M_Battle_QueueAI::run($i);
			$end2 = microtime(true);
			$costTime = sprintf("%f secs.\n", $end2 - $end);
			echo "[{$i}][{$pid}][M_Battle_QueueAI::run]#CostTime:" . sprintf("%f secs.", $end2 - $end) . "\n";

			sleep(3);
		}

		exit(0); //子进程要exit否则会进行递归多进程，父进程不要exit否则终止多进程
	}
	if ($bWaitFlag) {
		pcntl_waitpid($pids[$i], $status, WUNTRACED);
		echo "wait $i -> " . time() . "\n";
	}
}
$elapsed = microtime(TRUE) - $starttime;
print "\n==> total elapsed: " . sprintf("%f secs.\n", $elapsed);


?>

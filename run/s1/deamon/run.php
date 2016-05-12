#!/usr/bin/env php
<?php
$commonFile = dirname(dirname(__FILE__)) . '/common.php';
include($commonFile);
$dir_path = dirname(__FILE__);

$cmd = "ps aux | grep Deamon | grep -v grep | awk '{print $2\",\"$13}'";

$out = trim(shell_exec($cmd));

$outArr = explode("\n", $out);
$enableArr = array(
	'DeamonMarchHandler.php' => '行军守护进程',
	'DeamonBattleHandler.php' => '战斗队列守护进程',
	'DeamonCityVisitQueue.php' => '访问队列',
);


$proc = array();
foreach ($outArr as $val) {
	if (!empty($val)) {
		list($pid, $pname) = explode(',', $val);
		$info = pathinfo($pname);
		$name = $info['filename'];
		$proc[$name] = $pid;
	}
}


if (!empty($argv[1])) {
	switch ($argv[1]) {

		case 'stop':
			foreach ($outArr as $val) {
				if (!empty($val)) {
					list($pid, $pname) = explode(',', $val);
					posix_kill($pid, 9);
					echo "Kill $pname $pid [OK]\n";
				}
			}
			break;
		case 'restart':
		case 'start':
			foreach ($enableArr as $fileName => $desc) {
				if (isset($proc[$fileName])) {
					echo "{$desc} Pid:[{$proc[$fileName]}] {$fileName} [Found]\n";
				} else {
					$cmd = "nohup {$dir_path}/{$fileName} > /dev/null & ";
					$cmdout = shell_exec($cmd);
					echo $cmdout;
					echo "Run {$desc} {$fileName} [OK]\n";
				}
			}
			break;
	}
} else {
	echo "需要参数: (start,restart,stop)\n";
}


$end = microtime(true);
$costTime = $end - $start;
echo "End Cost Time: {$costTime} \n\n";
?>

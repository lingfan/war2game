<?php

/**
 * 日志处理队列
 */
class B_Log_Queue {
	/**
	 * @author huwei
	 * @param string $msg 日志内容
	 * @param string $fileDir 日志目录名
	 * @param string $fileName 日志文件名
	 */
	static public function add($msg, $fileDir, $fileName) {
		$rc = new B_Cache_RC(T_Key::LOG_QUEUE);
		$ret = $rc->rpush(json_encode(array($msg, $fileDir, $fileName)));
		return $ret;
	}

	static public function run() {
		$rc = new B_Cache_RC(T_Key::LOG_QUEUE);
		while ($val = $rc->rPop()) {
			list($msg, $fileDir, $fileName) = json_decode($val);

			if (!file_exists($fileDir)) {
				@mkdir($fileDir, 0777, true);
				@chmod($fileDir, 0777);
			}
			$logFile = $fileDir . $fileName;
			error_log($msg, 3, $logFile);
			usleep(500);
		}
	}


}

?>
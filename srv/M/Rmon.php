<?php

class M_Rmon {
	static public function redis() {
		$fileDir  = LOG_PATH  . '/stats';
		if (!file_exists($fileDir)) {
			@mkdir($fileDir, 0777, true);
			@chmod($fileDir, 0777);
		}

		$config = B_Cache_File::get('redis');
		foreach ($config['hostname'] as $val) {
			list($host, $port) = explode(':', $val);
			$redis = new Redis();
			$redis->connect($host, $port);
			$status = $redis->info();

			$data = array(
				date('YmdHis'),
				$status['connected_clients'],
				$status['blocked_clients'],
				$status['keyspace_hits'], //命中key的次数
				$status['keyspace_misses'], //不命中key的次数
				$status['used_cpu_sys'],
				$status['used_cpu_user'],
				$status['used_cpu_sys_children'],
				$status['used_cpu_user_children'],
				$status['used_memory_peak_human'], //Redis所用内存的高峰值
				$status['total_commands_processed'], //运行以来执行过的命令的总数量
				$status['mem_fragmentation_ratio'], //内存碎片比率
			);

			$logFile = $fileDir . '/' . date('Ymd') . '_' . $port . '.log';
			error_log(implode(',', $data) . "\n", 3, $logFile);
			$redis->close();
		}
	}
}

?>
<?php

class B_Cache_APC {
	/**
	 * 存储数据到内存中
	 * @param string $apcKey 内存key
	 * @param array $data 数据
	 * @param int $ttl 生存周期
	 */
	static public function set($apcKey, $data, $ttl = 0) {
		$serverId = B_Cache_File::server(SERVER_NO);
		return apc_store($serverId . $apcKey, $data, $ttl);
	}

	/**
	 * 从内存中获取数据
	 * @param string $apcKey
	 */
	static public function get($apcKey) {
		$serverId = B_Cache_File::server(SERVER_NO);
		return apc_fetch($serverId . $apcKey);
	}

	/**
	 * 删除内存中数据
	 * @param string $apcKey
	 */
	static public function del($apcKey) {
		$serverId = B_Cache_File::server(SERVER_NO);
		return apc_delete($serverId . $apcKey);
	}

	/**
	 * 是否存在
	 * @param string $apcKey
	 */
	static public function exists($apcKey) {
		$serverId = B_Cache_File::server(SERVER_NO);
		return apc_exists($serverId . $apcKey);
	}

	static public function incr($apcKey) {
		$serverId = B_Cache_File::server(SERVER_NO);
		return apc_inc($serverId . $apcKey);
	}
}

?>
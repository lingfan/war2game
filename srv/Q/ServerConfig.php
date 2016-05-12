<?php

class Q_ServerConfig extends B_DB_Dao {
	protected $_name = 'server_config';
	protected $_connType = 'game';
	protected $_primary = 'name';

	/**
	 * 添加服务器配置
	 * @param string $key
	 * @param string $val
	 */
	public function add($key, $val) {
		$info['name'] = $key;
		$info['value'] = $val;
		$id = $this->insert($info);
		return $id;
	}

	/**
	 * 更新服务器配置
	 * @param string $key
	 * @param string $val
	 * @return bool true/false
	 */
	public function set($key, $val) {
		$info['value'] = $val;
		$ret = $this->updateBy($info, array('name'=>$key));
		return $ret;

	}

	/**
	 * 获取配置
	 * @return array
	 */
	public function all() {
		$list = $this->getAll();
		$rows = array();
		foreach ($list as $val) {
			$rows[$val['name']] = json_decode($val['value'], true);
		}

		return $rows;
	}
}

?>
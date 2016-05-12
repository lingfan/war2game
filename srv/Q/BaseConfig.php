<?php

class Q_BaseConfig extends B_DB_Dao {
	protected $_name = 'base_config';
	protected $_connType = 'base';
	protected $_primary = 'name';

	/**
	 * 添加配置
	 * @author Hejunyun
	 * @param string $key
	 * @param string $val
	 * @param string $type
	 * @param string $desc
	 */
	public function add($key, $val) {
		$info['name'] = $key;
		$info['value'] = $val;
		$id = $this->insert($info);
		return $id;

	}

	/**
	 * 根据配置名字更新配置数据
	 * @author chenhui on 20110616
	 * @param string name 配置的名字
	 * @param array updinfo 要更新的配置名字对应数组
	 * @return bool true/false
	 */
	public function set($key, $val) {
		$ret = $this->updateBy(array('value' => $val), array('name' => $key));
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
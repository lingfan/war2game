<?php

/**
 *  数据库类库
 */
class B_DB {
	static public function instance($name) {
		$daoName = 'Q_' . $name;
		$obj = new $daoName();
		return $obj;
	}
}
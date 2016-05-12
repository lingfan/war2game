<?php

class Q_AdmUser extends B_DB_Dao {
	protected $_name = 'adm_user';
	protected $_connType = 'base';
	protected $_primary = 'id';

	/**
	 * 获取用户信息
	 * @param int $userId 用户ID
	 * @return array
	 */
	public function getInfo($userId) {
		$rows = $this->get($userId);
		return $rows;
	}

	/**
	 * 验证用户登陆
	 * @param array $info (username, password)
	 * @return bool/array
	 */
	public function verifyLogin($username, $password) {
		$row = $this->getBy(array('username' => $username, 'password' => $password));

		return $row;
	}

	/**
	 * 通过分页获取记录数 for admin
	 * @author huwei
	 * @param int $page 当前页
	 * @param int $offset 每页记录数
	 * @return array
	 */
	public function getRowsByPage($page, $offset) {
		$start = ($page - 1) * $offset;
		$rows = $this->getList($start, $offset);
		return $rows;
	}
}

?>
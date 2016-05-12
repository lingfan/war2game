<?php

class C_I {
	public $objPlayer = null;

	public function __construct() {
		$info = M_Auth::getLoginCookie();
		$cityId = !empty($info['city_id']) ? $info['city_id'] : 0;
		$this->objPlayer = new O_Player($cityId);
	}

	public function isLogin() {
		$cityId = $this->objPlayer->getId();
		return $cityId;
	}

	public function hasCity() {
		$ret = false;
		$id = $this->objPlayer->City()->id;
		if (!empty($id)) {
			$ret = $id;
		}
		return $ret;
	}
}
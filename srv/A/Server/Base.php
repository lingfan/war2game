<?php

//基础信息api
class A_Server_Base {
	/**
	 * 模板装备列表
	 */
	static public function EquipList() {
		$list = M_Base::equipAll();
		unset($list['ids']);
		return $list;
	}

	/** 道具列表 */
	static public function PropsList() {
		$baseList = M_Base::propsAll();
		return $baseList;
	}

	/**
	 * 武器列表
	 */
	static public function WeaponList() {
		$baseList = M_Base::weaponAll();
		return $baseList;
	}

}

?>
<?php

/**
 * 行军排队模块
 */
class M_March_Wait {
	private $_posNo = '';

	public function __construct($posNo) {
		$this->_posNo = $posNo;
	}

	/**
	 * 获取坐标对应等待的行军数据
	 *
	 */
	public function get() {
		//由于redis读写分离 刚set的数据不能马上get 统一用写句柄操作
		$rc  = new B_Cache_RC(T_Key::MARCH_WAIT_KEY, $this->_posNo);
		$ids = $rc->jsonget();
		$ret = !empty($ids) ? $ids : array();
		return $ret;
	}

	/**
	 * 添加坐标对应等待的行军数据
	 */
	public function add($marchId, $toHead = false) {
		$list = self::get();
		if (empty($list)) {
			$list = array();
		}
		if ($toHead) {
			array_unshift($list, $marchId);
		} else {
			array_push($list, $marchId);
		}

		$rc  = new B_Cache_RC(T_Key::MARCH_WAIT_KEY, $this->_posNo);
		$ret = $rc->jsonset($list, T_App::ONE_WEEK);
	}

	/**
	 * 删除坐标对应等待的行军数据
	 */
	public function del($marchId) {
		$oldList = self::get();
		$newList = array();
		if (!empty($oldList)) {
			foreach ($oldList as $id) {
				if ($id != $marchId) {
					$newList[] = $id;
				}
			}
		} else {
			$newList = array();
		}
		$rc  = new B_Cache_RC(T_Key::MARCH_WAIT_KEY, $this->_posNo);
		$ret = $rc->jsonset($newList, T_App::ONE_WEEK);
	}

	/**
	 * 获取当前坐标的战斗ID
	 * @author huwei
	 * @return string
	 */
	public function getBattleId() {
		$rc  = new B_Cache_RC(T_Key::BATTLE_ING_KEY, $this->_posNo);
		$ret = $rc->get();
		return $ret ? $ret : 0;
	}

	/**
	 * 更新当前坐标的战斗ID
	 * @author huwei
	 * @param int $battleId
	 * @return bool
	 */
	public function setBattleId($battleId) {
		$rc = new B_Cache_RC(T_Key::BATTLE_ING_KEY, $this->_posNo);
		return $rc->set($battleId, T_App::ONE_HOUR * 0.5);
	}

	/**
	 * 清除当前坐标的战斗ID
	 * @author huwei
	 * @return bool
	 */
	public function delBattleId() {
		$rc = new B_Cache_RC(T_Key::BATTLE_ING_KEY, $this->_posNo);
		return $rc->delete();
	}
}

?>
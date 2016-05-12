<?php

class O_Log {
	/**
	 * @var O_Player
	 */
	private $_objPlayer = null;

	public function __construct(O_Player $objPlayer) {
		$this->_objPlayer = $objPlayer;
	}

	private $_milpay = 0;
	private $_coupon = 0;

	/**
	 * 物品
	 */
	const OP_TYPE_ITEM = 1;
	/**
	 * 装备
	 */
	const OP_TYPE_EQUIP = 2;
	/**
	 * 军官
	 */
	const OP_TYPE_HERO = 3;

	public function income($type, $cost, $action, $data = '') {
		if ($type == T_App::MILPAY) {
			$this->_milpay = $cost;
		} else {
			$this->_coupon = $cost;
		}
		$this->_coin(B_Log_Trade::TYPE_INCOME, $action, $data);
	}

	public function expense($type, $cost, $action, $data = '') {
		if ($type == T_App::MILPAY) {
			$this->_milpay = $cost;
		} else {
			$this->_coupon = $cost;
		}
		$this->_coin(B_Log_Trade::TYPE_EXPENSE, $action, $data);
	}

	public function _coin($type, $action, $data = '') {
		$cityInfo = $this->_objPlayer->getCityBase();
		if ($cityInfo['id'] && $action) {
			//军饷支出流水账
			$logData = array(
				'city_id' => $cityInfo['id'],
				'action' => $action,
				'type' => $type,
				'milpay' => $this->_milpay,
				'left_milpay' => $cityInfo['mil_pay'],
				'coupon' => $this->_coupon,
				'left_coupon' => $cityInfo['coupon'],
				'data' => $data,
				'created_at' => time(),
			);

			B_DB::instance('LogCoin')->insert($logData);
		}
	}

	public function get() {
		return array();
	}

	public function isChange() {
		return false;
	}

	public function getSync() {
		return array();
	}


}
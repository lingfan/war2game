<?php

/**
 * 冷却时间模块
 */
class O_CD implements O_I {
	/** 建筑 */
	const TYPE_BUILD = 1;
	/** 科技 */
	const TYPE_TECH = 2;
	/** 战役 (快速战斗) */
	const TYPE_FB = 3;
	/** 武器 */
	const TYPE_WEAPON = 4;
	/** 解救 */
	const TYPE_RESCUE = 5;
	/** 突击 */
	const TYPE_BOUT = 6;
	/** 爬楼 */
	const TYPE_FLOOR = 7;

	/** CD标记 关闭 **/
	const FLAG_CLOSE = 0;
	/** CD标记 打开 **/
	const FLAG_OPEN = 1;
	/** CD标记 锁定 **/
	const FLAG_LOCKED = 2;

	private $_change = false;

	/**
	 * 同步数据
	 * @var array
	 */
	private $_sync = array();

	/** CD队列类型 array(免费开放数量, 上限时间, 初始最大数量) */
	static $limitType = array(
		self::TYPE_BUILD => array(2, 3600, 7),
		self::TYPE_TECH => array(1, 3600, 1),
		self::TYPE_FB => array(1, 3600, 1),
		self::TYPE_WEAPON => array(1, 3600, 1),
		self::TYPE_RESCUE => array(1, 3600, 1),
		self::TYPE_BOUT => array(1, 3600, 1),
		self::TYPE_FLOOR => array(1, 3600, 1),
	);


	/**
	 * 数据结构内容
	 * [1:[2,0],[1,0],3:[1,0]]
	 * @var array
	 */
	private $_data = array();
	private $_type = 0;
	private $_tmpData = array();
	private $_num = array();
	private $_now = 0;

	public function __construct(O_Player $objPlayer) {
		$extraInfo = $objPlayer->getCityExtra();
		$cdList = array();
		if (!empty($extraInfo['cd_list'])) {
			$cdList = json_decode($extraInfo['cd_list'], true);
		}
		$this->_data = $cdList;
		$this->_now = time();
		$this->_tmpData = array();
	}

	/**
	 * 获取CD队列数量
	 * @param int $vipLimitNum
	 * @param int $freeNum
	 * @return array
	 */
	private function _init($type) {
		$newCD = array();
		if (!empty($type) && !isset($this->_tmpData[$type])) {
			list($freeNum, $lockTime, $maxNum) = self::$limitType[$type];
			//队列最大数量
			for ($i = 1; $i <= $maxNum; $i++) {
				$expireTime = 0;
				$flag = ($i > $freeNum) ? O_CD::FLAG_CLOSE : O_CD::FLAG_OPEN;

				if (isset($this->_data[$type][$i])) {
					list($flag, $expireTime) = $this->_data[$type][$i];
					if ($this->_now > $expireTime) {
						if ($flag == O_CD::FLAG_LOCKED) {
							$flag = O_CD::FLAG_OPEN;
						}
						$expireTime = 0;
					}
				}

				if ($flag != O_CD::FLAG_CLOSE) {
					if (!isset($this->_num[$type])) {
						$this->_num[$type] = 1;
					} else {
						$this->_num[$type] += 1;
					}

				}

				$newCD[$i] = array($flag, $expireTime);
			}

			$this->_tmpData[$type] = $newCD;
		}
	}

	public function getList($type) {
		$this->_init($type);
		return isset($this->_tmpData[$type]) ? $this->_tmpData[$type] : array();
	}

	public function getOpenNum($type) {
		$this->_init($type);
		return isset($this->_num[$type]) ? $this->_num[$type] : 0;
	}

	/**
	 * 更新CD时间
	 * @author huwei
	 * @param int $idx
	 * @param int $flag
	 * @param int $time
	 * @return array
	 */
	public function set($type, $idx, $costTime) {
		$ret = false;
		$this->_init($type);
		if (isset($this->_tmpData[$type][$idx])) {
			list($flag, $expireTime) = $this->_tmpData[$type][$idx];

			$nowExpireTime = ($expireTime > $this->_now ? $expireTime : $this->_now) + $costTime;

			list($freeNum, $lockTime, $maxNum) = self::$limitType[$type];

			if ($nowExpireTime >= ($lockTime + $this->_now)) {
				$flag = self::FLAG_LOCKED;
			}
			$this->_tmpData[$type][$idx] = array($flag, $nowExpireTime);
			$ret = true;

			$this->_sync[$type] = $this->_tmpData[$type];

			$this->_change = true;
		}
		return $ret;
	}

	public function open($type) {
		$ret = 0;
		$this->_init($type);
		if (isset($this->_tmpData[$type])) {
			foreach ($this->_tmpData[$type] as $idx => $val) {
				list($flag, $expireTime) = $val;
				if ($flag == self::FLAG_CLOSE) {
					$this->_tmpData[$type][$idx] = array(self::FLAG_OPEN, $expireTime);
					$this->_change = true;
					$ret = $idx;
					break;
				}
			}
		}
		return $ret;
	}

	/**
	 * 清楚CD时间
	 * @param int $type
	 * @param int $idx
	 * @return boolean
	 */
	public function clean($type, $idx = 0) {
		$ret = false;
		$this->_init($type);
		if (!empty($idx)) {
			if (isset($this->_tmpData[$type][$idx])) {
				$this->_tmpData[$type][$idx] = array(self::FLAG_OPEN, 0);
				$ret = true;
				$this->_sync[$type] = $this->_tmpData[$type];

				$this->_change = true;
			}
		} else {
			foreach ($this->_tmpData[$type] as $x => $v) {
				$this->_tmpData[$type][$x][1] = 0;
				$this->_sync[$type] = $this->_tmpData[$type];

			}
			$this->_change = true;
		}

		return $ret;
	}

	/**
	 * 获取空闲的CD编号
	 * @author huwei
	 * @return int
	 */
	public function getFreeIdx($type) {
		$this->_init($type);
		if (isset($this->_tmpData[$type])) {
			foreach ($this->_tmpData[$type] as $key => $val) {
				list($flag, $expireTime) = $val;
				if ($flag == self::FLAG_OPEN) {
					return $key;
				}
			}
		}
		return 0;
	}

	/**
	 * 获取数据
	 *
	 * @author huwei
	 * @return array
	 */
	public function get() {
		return $this->_tmpData;
	}

	public function isChange() {
		return $this->_change;
	}

	public function getSync() {
		$ret = $this->_sync;
		$this->_sync = array();
		return $ret;
	}

	public function toFront($type) {
		$tmp = $this->getList($type);
		$view = $this->_filter($tmp);

		$ret = array();
		if ($type == self::TYPE_BUILD) {
			$ret = $view;
		} else {
			$ret = isset($view[0]) ? $view[0] : array(0, 0);
		}
		return $ret;
	}

	private function _filter($arr) {
		$ret = array();
		foreach ($arr as $i => $v) {
			list($flag, $expireTime) = $v;
			if ($flag != self::FLAG_CLOSE) {
				$status = ($flag == self::FLAG_OPEN) ? T_App::ADDUP_CAN : T_App::ADDUP_CANT;
				$diff = max($expireTime - $this->_now, 0);
				if ($diff == 0 && $flag == self::FLAG_LOCKED) {
					$status = T_App::ADDUP_CAN;
				}

				$ret[] = array($diff, $status);
			}
		}
		return $ret;
	}
}
<?php

class O_Pack implements O_I {
	/**
	 * array(
	 *    array('道具Id','类型','数量')
	 * )
	 * @var array
	 */
	private $_data = array();
	private $_change = false;
	private $_sync = array();
	private $_now = 0;

	/** @var O_Player */
	private $_objPlayer = null;

	/** 道具ID=>数量 */
	private $_numByProps = array();
	/** 道具ID=>槽ID */
	private $_slotIdByProps = array();
	/** 类型=>槽ID */
	private $_slotIdIdByType = array();
	private $_maxSlotId = 0;

	public function __construct(O_Player $objPlayer) {
		$this->_objPlayer = $objPlayer;
		$extraInfo = $objPlayer->getCityExtra();
		$data = array();
		if (!empty($extraInfo['pack_list'])) {
			$data = json_decode($extraInfo['pack_list'], true);
		}

		if (empty($list)) {
			$this->_data = !empty($data[0]) ? $data[0] : array();
			$this->_maxSlotId = !empty($data[1]) ? $data[1] : 0;
			$this->_change = true;
		}

		$this->_formatData(__METHOD__);
	}

	public function get() {
		return array($this->_data, $this->_maxSlotId);
	}

	public function isChange() {
		return $this->_change;
	}

	public function getSync() {
		$ret = $this->_sync;
		$this->_sync = array();
		return $ret;
	}

	/**
	 * 添加物品列表
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @param int $type 物品类型
	 * @return array
	 */
	public function add($id, $incrNum = 1) {
		$ret = false;
		if ($id > 0 && $incrNum > 0) {
			$baseInfo = M_Props::baseInfo($id);
			if (!empty($baseInfo['id'])) {
				if (!empty($this->_slotIdByProps[$id])) {
					foreach ($this->_slotIdByProps[$id] as $slotId) {
						if (!empty($this->_data[$slotId])) {
							list($propsId, $type, $num) = $this->_data[$slotId];
							$diffNum = max($baseInfo['stack_num'] - $num, 0);
							//echo "incrNum:{$incrNum} -- stack_num:{$baseInfo['stack_num']} - num:{$num} = diffNum:{$diffNum}<hr>";
							if ($diffNum > 0) { //数量叠加未满
								$leftNum = $incrNum - $diffNum;
								if ($leftNum >= 0) { //补满数量
									$this->_data[$slotId][2] += $diffNum;
									$this->_sync[$slotId] = array(intval($propsId), intval($this->_data[$slotId][2]), 0);
									$incrNum = $leftNum; //剩余数量
								} else {
									$this->_data[$slotId][2] += $incrNum;
									$this->_sync[$slotId] = array(intval($propsId), intval($this->_data[$slotId][2]), 0);
									$incrNum = 0;
									break;
								}
							}
						}
					}
				}

				$newNum = array();

				while ($incrNum > $baseInfo['stack_num']) {
					$incrNum -= $baseInfo['stack_num'];
					if ($incrNum > 0) {
						$newNum[] = $baseInfo['stack_num'];
					}
				}

				if ($incrNum > 0) {
					$newNum[] = $incrNum;
				}

				foreach ($newNum as $aNum) {
					if ($aNum > 0) {
						$this->_maxSlotId += 1;
						$this->_data[$this->_maxSlotId] = array(intval($id), intval($baseInfo['type']), $aNum);
						$this->_sync[$this->_maxSlotId] = array(intval($id), intval($aNum), 0);

					}
				}
				$this->_formatData(__METHOD__);

				$this->_change = true;
				$ret = true;
			}
		}

		return $ret;

	}

	public function getPropsBySlotId($slotId) {
		$ret = array();
		if (!empty($this->_data[$slotId])) {
			$ret = $this->_data[$slotId];
		}
		return $ret;
	}

	/**
	 * 扣道具数量
	 * @param int $id
	 * @param number $decrnum
	 * @return number  [true足够|false不足]
	 */
	public function decrNumBySlotId($slotId, $decrNum = 1) {
		$num = -1;
		$tmpSync = array();
		if ($slotId) {
			if (!empty($this->_data[$slotId])) {
				list($propsId, $type, $num) = $this->_data[$slotId];
				$num -= $decrNum;
				if ($num > 0) {
					$this->_data[$slotId] = array(intval($propsId), intval($type), $num);
					$tmpSync[$slotId] = array(intval($propsId), $num, 0);
				} else if ($num == 0) {
					unset($this->_data[$slotId]);
					$tmpSync[$slotId] = M_Sync::DEL;
				}
			}
		}

		$ret = false;
		if ($num >= 0) {
			foreach ($tmpSync as $k => $v) {
				$this->_sync[$k] = $v;
			}
			$this->_formatData(__METHOD__);
			$this->_change = true;
			$ret = true;
		}
		return $ret;
	}

	/**
	 * 扣道具数量
	 * @param int $id
	 * @param number $decrNum
	 * @return number  [true足够|false不足]
	 */
	public function decrNumByPropId($id, $decrNum = 1) {
		$tmpNum = $decrNum;
		foreach ($this->_slotIdByProps[$id] as $slotId) {
			list($propsId, $type, $num) = $this->_data[$slotId];
			$tmpNum -= $num;

			//tmpNum 1 - num 10 = tmpNum -9
			//tmpNum 1 - num 1 = tmpNum 0
			//tmpNum 11 - num 10 = tmpNum 1
			if ($tmpNum <= 0) { //剩余数量大于0 表示扣完数量了
				$this->_data[$slotId] = array(intval($propsId), intval($type), abs($tmpNum));
				$this->_sync[$slotId] = array(intval($propsId), abs($tmpNum), 0);
				break;
			} else if ($tmpNum > 0) {
				unset($this->_data[$slotId]);
				$this->_sync[$slotId] = M_Sync::DEL;
			}
		}

		$ret = false;
		if ($tmpNum <= 0) {
			$this->_formatData(__METHOD__);
			$this->_change = true;
			$ret = true;
		}

		return $ret;
	}

	public function getSlotIdByType($type) {
		return isset($this->_slotIdIdByType[$type]) ? $this->_slotIdIdByType[$type] : array();
	}

	public function getSlotIdByPropsId($id) {
		return isset($this->_slotIdByProps[$id]) ? $this->_slotIdByProps[$id] : 0;
	}

	public function getNumByPropsId($id) {
		return isset($this->_numByProps[$id]) ? array_sum($this->_numByProps[$id]) : 0;
	}

	public function _formatData($logName='') {
		$this->_change = true;
		$this->_numByProps = array();
		$this->_slotIdByProps = array();
		$this->_slotIdIdByType = array();

		//Logger::debug(array(__METHOD__, $logName, $this->_data, $this->_numByProps));

		foreach ($this->_data as $slotId => $itemArr) {
			list($propsId, $type, $num) = $itemArr;
			$this->_slotIdByProps[$propsId][] = $slotId;
			$this->_numByProps[$propsId][] = $num;
			$this->_slotIdIdByType[$type][] = $slotId;
		}
		//Logger::debug(array(__METHOD__, $this->_numByProps));
	}

	/**
	 * 排序
	 */
	public function sort() {
		$typeArr = array(M_Props::TYPE_INNER, M_Props::TYPE_HERO, M_Props::TYPE_TREA, M_Props::TYPE_WAR, M_Props::TYPE_DRAW, M_Props::TYPE_STUFF);

		$propsArr = array();
		foreach ($this->_data as $slotId => $itemData) {
			list($propsId, $t, $num) = $itemData;
			$propsArr[$t][$propsId] += isset($propsArr[$t][$propsId]) ? $num : 0;
		}

		$this->_maxSlotId = 0;
		$tmp = array();
		foreach ($typeArr as $tmpT) {
			if (isset($propsArr[$tmpT])) {
				foreach ($propsArr[$tmpT] as $tmpP => $tmpNum) {
					$baseInfo = M_Props::baseInfo($tmpP);
					$k = intval($tmpNum / $baseInfo['stack_num']); //前k个都是满
					$leftNum = intval($tmpNum % $baseInfo['stack_num']);
					for ($i = 0; $i < $k; $i++) {
						$this->_maxSlotId += 1;
						$tmp[$this->_maxSlotId] = array($tmpP, $tmpT, $baseInfo['stack_num']);
					}

					if ($leftNum > 0) {
						$this->_maxSlotId += 1;
						$tmp[$this->_maxSlotId] = array($tmpP, $tmpT, $leftNum);
					}
				}
			}
		}

		$this->_data = $tmp;
		$this->_formatData(__METHOD__);
		$this->_change = true;
	}

	public function toFront() {
		$ret = array();
		foreach ($this->_data as $slotId => $itemVal) { //物品id, 基础道具id, 数量, 是否绑定,//军官经验道具array(经验空间, 经验值, 军官ID)
			list($propsId, $type, $num) = $itemVal;
			if ($num > 0) {
				$ret[] = array($propsId, $num, 0, $slotId);
			}
		}
		return $ret;
	}

	public function incr($propsId, $num = 1) {
		$ret = $this->add($propsId, $num);
		$baseInfo = M_Props::baseInfo($propsId);
		if ('WEAPON_PIECE' == $baseInfo['effect_txt']) {
			$hasNum = $this->getNumByPropsId($propsId);
			if ($hasNum > 0) {
				$arrEffectVal = explode(',', $baseInfo['effect_val']); //道具值基础array(对应图纸ID,需要数量)
				if (!empty($arrEffectVal[1])) {
					$needNum = $arrEffectVal[1];
				} else {
					Logger::error(array(__METHOD__, $baseInfo));
					$needNum = 1;
				}

				$incrNum = floor($hasNum / $needNum); //生成的图纸数量
				$decrNum = $incrNum * $needNum; //消耗掉的残页数量

				if ($incrNum > 0 && $arrEffectVal[0] > 0) { //生成了新的道具
					$newPropsId = intval($arrEffectVal[0]);
					$bSucc = $this->decrNumByPropId($propsId, $decrNum);
					!$bSucc && Logger::error(array(__METHOD__, 'fail to decr city props', array($propsId, $decrNum)));

					$this->add($newPropsId, $incrNum);
					!$bSucc && Logger::error(array(__METHOD__, 'fail to incr city props', array($newPropsId, $incrNum)));
				}
			}
		}
		return $ret;
	}

	/**
	 * 返回物品数量和包裹状态
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @param int $vipLevel VIP等级
	 * @return array(<br>
	 * 'normal' => array('num'=>数量, 'full'=>true已满/false未满),<br>
	 * 'draw' => array('num'=>数量, 'full'=>true已满/false未满),<br>
	 * 'stuff' => array('num'=>数量, 'full'=>true已满/false未满)<br>);
	 */
	public function hasNum() {
		$vipLevel = $this->_objPlayer->City()->vip_level;

		$vipConf = M_Config::getVal('vip_config');

		$maxNormal = $vipConf['PACK_PROPS'][$vipLevel];
		$maxDraw = $vipConf['PACK_DRAW'][$vipLevel];
		$maxStuff = $vipConf['PACK_MATERIAL'][$vipLevel];

		//道具
		$total = 0;
		$arr = array(M_Props::TYPE_INNER, M_Props::TYPE_HERO, M_Props::TYPE_TREA, M_Props::TYPE_WAR);

		foreach ($arr as $v) {
			$ids = $this->getSlotIdByType($v);
			$total += count($ids);
		}
		$curNum['normal'] = $total;
		$isFull['normal'] = ($curNum['normal'] >= $maxNormal) ? true : false;
		//图纸
		$curNum['draw'] = $this->getSlotIdByType(M_Props::TYPE_DRAW);
		$isFull['draw'] = ($curNum['draw'] >= $maxDraw) ? true : false;
		//材料
		$curNum['stuff'] = $this->getSlotIdByType(M_Props::TYPE_STUFF);
		$isFull['stuff'] = ($curNum['stuff'] >= $maxStuff) ? true : false;

		$ret = array(
			'normal' => array('num' => $curNum['normal'], 'full' => $isFull['normal']),
			'draw' => array('num' => $curNum['draw'], 'full' => $isFull['draw']),
			'stuff' => array('num' => $curNum['stuff'], 'full' => $isFull['stuff']),
		);
		return $ret;
	}
}
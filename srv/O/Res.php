<?php

/**
 * 资源效果
 *
 */
class O_Res implements O_I {

	/**
	 * [gold,food,oil,max_store,last_update_time,gold_grow,oil_grow,food_grow]
	 *
	 * @var array
	 */
	private $_data = array();
	/**
	 * 同步数据
	 * @var array
	 */
	private $_sync = array();
	private $_change = false;
	private $_now = 0;

	/**
	 *
	 * @var O_Player
	 */
	private $_objPlayer = null;

	private $_fieldType = array('gold_grow', 'food_grow', 'oil_grow');

	public function __construct(O_Player $objPlayer) {
		$cityInfo = $objPlayer->getCityBase();
		$extraInfo = $objPlayer->getCityExtra();
		$resList = array();
		if (!empty($extraInfo['res_list'])) {
			$resList = json_decode($extraInfo['res_list'], true);
		}

		$this->_now = time();
		if (empty($resList)) {
			//获取城市初始化数据
			$baseConf = M_Config::getVal();

			$resList = array(
				'gold' => $baseConf['city_base_gold'],
				'food' => $baseConf['city_base_food'],
				'oil' => $baseConf['city_base_oil'],
				'max_store' => $baseConf['city_max_store'],
				'last_update_time' => $this->_now,
				'gold_grow' => 0,
				'oil_grow' => 0,
				'food_grow' => 0,
			);
			$this->_change = true;

		}
		$this->_data = $resList;
		$this->_objPlayer = $objPlayer;
	}

	/**
	 * 更新仓库容量
	 *
	 * @param int $num
	 */
	public function upStore($num) {
		$this->_data['max_store'] = $num;
		$this->_sync['max_store'] = $this->_data['max_store'];
		$this->_change = true;
	}

	public function isFull($type) {
		$ret = false;
		if ($this->data[$type] > $this->_data['max_store']) {
			$ret = true;
		}
		return $ret;
	}

	public function incr($type, $num, $force = false) {
		$ret = -1;
		if (in_array($type, array('gold', 'food', 'oil')) && abs($num) >= 0) {
			if ($num <= 0) { //扣资源时不判断仓库大小
				$force = true;
			}

			$checkMaxStore = $force ? true : $this->_data[$type] < $this->_data['max_store'];

			if ($checkMaxStore) {
				$this->_data[$type] += $num;
				$this->_sync[$type] = floor($this->_data[$type]);
				$this->_change = true;
				$ret = $this->_data[$type];
			}
		}
		return $ret;
	}

	public function getNum($type) {
		return isset($this->_data[$type]) ? $this->_data[$type] : 0;
	}

	public function calc() {
		$diffTime = max($this->_now - $this->_data['last_update_time'], 0);

		if ($diffTime > 0) {
			$this->_upResNum($diffTime);
		}
	}

	public function upGrow($changType = '') {
		$resAdd = $this->calcResBaseAdd();

		if (!empty($resAdd)) {
			$add = array('tech', 'vip', 'props', 'zone', 'union', 'npc_colony');

			foreach ($this->_fieldType as $t) {
				$tmp = array();
				foreach ($add as $v) {
					$tmp[] = isset($resAdd[$v][$t]) ? $resAdd[$v][$t] : 0;
				}

				$this->_data[$t] = $this->_calcAdd($resAdd['base'][$t], $tmp);
			}

			$this->_checkHold();

			$this->_change = true;

			if ($changType) {
				$this->_syncResAdd($resAdd, $changType);
			}
		}
	}

	private function _calcAdd($base, $addArr) {
		$num = floatval($base) * ((100 + array_sum($addArr)) / 100);
		return $num;
	}

	private function _calcNum($maxStore, $resNum, $baseGrow, $diffTime) {
		$incrNum = 0;
		$tmpAdd = $baseGrow / T_App::ONE_HOUR * $diffTime;

		$totalNum = floatval($resNum) + floatval($tmpAdd);
		if ($maxStore > $resNum) {
			$incrNum = $totalNum > $maxStore ? max($maxStore - $resNum, 0) : $tmpAdd;
		}
		$ret = round(floatval($incrNum), 8);
		return $ret;
	}

	private function _upResNum($diffTime = 0) {

		$oldGold = intval($this->_data['gold']);
		$oldFood = intval($this->_data['food']);
		$oldOil = intval($this->_data['oil']);
		$addNum['gold'] = $this->_calcNum($this->_data['max_store'], $this->_data['gold'], $this->_data['gold_grow'], $diffTime);
		$addNum['oil'] = $this->_calcNum($this->_data['max_store'], $this->_data['oil'], $this->_data['oil_grow'], $diffTime);
		$addNum['food'] = $this->_calcNum($this->_data['max_store'], $this->_data['food'], $this->_data['food_grow'], $diffTime);

		//防沉迷
		$this->_data['gold'] += $addNum['gold'];
		$this->_data['oil'] += $addNum['oil'];
		$this->_data['food'] += $addNum['food'];

		$this->_data['last_update_time'] = $this->_now;

		if ($oldGold != intval($this->_data['gold'])) {
			$this->_sync['gold'] = intval($this->_data['gold']);
		}
		if ($oldFood != intval($this->_data['food'])) {
			$this->_sync['food'] = intval($this->_data['food']);
		}
		if ($oldOil != intval($this->_data['oil'])) {
			$this->_sync['oil'] = intval($this->_data['oil']);
		}

		$this->_change = true;
	}

	/**
	 * 占领资源减少
	 *
	 */
	private function _checkHold() {
		if (M_March_Hold::exist($this->_objPlayer->City()->pos_no)) {
			$foodReduce = M_Config::getVal('food_reduce');
			$goldReduce = M_Config::getVal('gold_reduce');
			$oilReduce = M_Config::getVal('oil_reduce');

			$this->_data['gold_grow'] *= $goldReduce / 100;
			$this->_data['food_grow'] *= $foodReduce / 100;
			$this->_data['oil_grow'] *= $oilReduce / 100;
		}
	}

	/**
	 * 获取资源数据
	 * gold,food,oil,max_store,last_update_time,gold_grow,oil_grow,food_grow,
	 * @return array
	 */
	public function get() {
		return $this->_data;
	}

	public function isChange() {
		return $this->_change;
	}

	public function getSync() {
		$ret = $this->_sync;
		$this->_sync = array();
		return $ret;
	}

	private function _syncResAdd($resAdd, $changeType = '') {
		$this->_sync['gold_grow'] = $this->_data['gold_grow'];
		$this->_sync['food_grow'] = $this->_data['food_grow'];
		$this->_sync['oil_grow'] = $this->_data['oil_grow'];

		if ($changeType) {
			$this->_sync['gold_grow_' . $changeType] = $resAdd[$changeType]['gold_grow'];
			$this->_sync['food_grow_' . $changeType] = $resAdd[$changeType]['food_grow'];
			$this->_sync['oil_grow_' . $changeType] = $resAdd[$changeType]['oil_grow'];
		}
	}

	/**
	 * 计算资源产量
	 *
	 */
	public function calcResBaseAdd() {
		//建筑加成
		$resBuildAdd = $this->_objPlayer->Build()->getResAdd();
		//科技加成
		$resTechAdd = $this->_objPlayer->Tech()->getResAdd();
		//道具加成
		$resPropsAdd = $this->_objPlayer->Props()->getResAdd();
		//VIP加成
		$resVipAdd = $this->_objPlayer->Vip()->getResAdd();

		$filed = array(T_App::RES_GOLD => 'gold_grow', T_App::RES_FOOD => 'food_grow', T_App::RES_OIL => 'oil_grow');

		$resBaseBuild = array();
		foreach ($resBuildAdd as $type => $num) {
			$resBaseBuild[$filed[$type]] = $num;
		}
		//资源建筑基础
		$resAdd['base'] = $resBaseBuild;

		$resBaseTech = array();
		foreach ($resTechAdd as $type => $num) {
			$resBaseTech[$filed[$type]] = $num;
		}

		//资源科技加成
		$resAdd['tech'] = $resBaseTech;

		$resBaseProps = array();
		foreach ($resPropsAdd as $type => $num) {
			$resBaseProps[$filed[$type]] = $num;
		}

		//资源道具加成
		$resAdd['props'] = $resBaseProps;

		//据点加成
		list($zone, $posX, $posY) = M_MapWild::calcWildMapPosXYByNo($this->_objPlayer->City()->pos_no);
		//洲[区域]加成
		$resAdd['zone'] = M_City::$zone_res_add[$zone];

		//资源Vip加成
		$resVipProps = array();
		foreach ($resVipAdd as $type => $num) {
			$resVipProps[$filed[$type]] = $num;
		}

		$resAdd['vip'] = $resVipProps;

		$campAdd = M_Campaign::getAddition(M_Campaign::CAMP_TYPE_RES, $this->_objPlayer->City()->union_id);

		//资源联盟加成
		$resAdd['union'] = array('gold_grow' => $campAdd, 'food_grow' => $campAdd, 'oil_grow' => $campAdd);

		//野外属地加成
		$resAdd['npc_colony'] = $this->_calcColonyNpc();

		return $resAdd;
	}

	public function _calcColonyNpc() {
		$npcColonyGold = $npcColonyOil = $npcColonyFood = 0;
		$list = $this->_objPlayer->ColonyNpc()->get();
		$cityId = $this->_objPlayer->City()->id;
		if (!empty($list) && is_array($list)) {
			foreach ($list as $colonyVal) {
				$mapInfo = M_MapWild::getWildMapInfo($colonyVal[1]);
				if (!empty($mapInfo['city_id']) &&
					$mapInfo['city_id'] = $cityId &&
						$mapInfo['type'] == T_Map::WILD_MAP_CELL_NPC
				) {
					$arr = explode(',', $mapInfo['last_fill_army_time']);
					switch ($arr[1]) {
						case M_NPC::RES_NPC_GOLD:
							$npcColonyGold += $arr[2];
							break;
						case M_NPC::RES_NPC_FOOD:
							$npcColonyFood += $arr[2];
							break;
						case M_NPC::RES_NPC_OIL:
							$npcColonyOil += $arr[2];
							break;
					}
				}
			}
			//Logger::debug(arrya(__METHOD__, $cityId, $list, $arr, $npcColonyGold, $npcColonyFood, $npcColonyOil));
		}
		$ret = array('gold_grow' => $npcColonyGold, 'food_grow' => $npcColonyFood, 'oil_grow' => $npcColonyOil);
		return $ret;
	}
}
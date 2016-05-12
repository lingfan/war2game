<?php

class O_ColonyNpc implements O_I {

	/**
	 * @var array//数据格式array(是否开启1/0,地图编号,探索次数,探索过期时间,属地到期时间,更新日期)
	 */
	private $_data = array();
	private $_sync = array();
	private $_change = false;
	public $base = array();
	private $_objPlayer = null;

	public function __construct(O_Player $objPlayer) {
		$this->_objPlayer = $objPlayer;
		$extraInfo = $objPlayer->getCityExtra();
		$colonyNpcList = array();
		if (!empty($extraInfo['colony_npc'])) {
			$colonyNpcList = json_decode($extraInfo['colony_npc'], true);
		}

		if (empty($colonyNpcList)) {
			$this->_init();
		}

		$this->_check();
	}

	private function _init() {
		//属地编号1,2,3
		//数据格式array(是否开启1/0,地图编号,探索次数,探索过期时间,属地到期时间,更新日期)
		$ret = array(
			1 => array(1, 0, 0, 0, 0, 0),
			2 => array(0, 0, 0, 0, 0, 0),
			3 => array(0, 0, 0, 0, 0, 0),
		);
		$this->_data = $ret;
		$this->_change = true;
	}

	private function _check() {
		foreach ($this->_data as $no => $v) {
			if (!empty($v[1]) && $v[5] != date('Ymd')) {
				//如果日期不同 则重新计算次数
				$this->_data[$no][2] = 0;
				$this->_data[$no][5] = date('Ymd');
				$this->_change = true;
			}
		}

	}

	public function getNoByPos($pos) {
		$ret = array();
		foreach ($this->_data as $no => $v) {
			if ($v[1] == $pos) {
				$ret = array($no, $v);
				break;
			}
		}
		return $ret;
	}

	/**
	 * 添加野外城市信息
	 * @author huwei
	 * @param int $cityId
	 * @param int $posNo
	 * @return bool
	 */
	public function add($marchInfo) {

		$ret = false;
		if (!empty($marchInfo)) {
			return $ret;
		}

		$cityId = intval($marchInfo['atk_city_id']);
		$posNo = $marchInfo['def_pos'];
		$marchId = $marchInfo['id'];
		$now = time();
		foreach ($this->_data as $no => $val) {
			if ($val[0] != 1 &&
				empty($val[1])
			) {

				if (empty($posNo)) {
					return false;
				}

				$mapInfo = M_MapWild::getWildMapInfo($posNo);
				if ($mapInfo['npc_id']) {
					//设置行军状态为驻守
					M_March::setMarchHold($marchInfo);
					//Logger::dev("setMarchHold#".json_encode($marchInfo));

					$holdTime = $now + T_App::ONE_HOUR * M_Config::getVal('hold_time_interval');

					//数据格式array(是否开启1/0,地图编号,探索次数,探索过期时间,属地到期时间)
					$this->_data[$no] = array(1, $posNo, $val[2], $val[3], $holdTime, $val[5]);

					//Logger::dev("MAP INFO:{$posNo}#".json_encode($mapInfo));

					$npcInfo = M_NPC::getInfo($mapInfo['npc_id']);

					$expireTime = 0;
					if (in_array($npcInfo['type'], array(M_NPC::RES_NPC_GOLD, M_NPC::RES_NPC_FOOD, M_NPC::RES_NPC_OIL))) { //资源野地1分钟过期
						$expireTime = $now + T_App::ONE_MINUTE;
					} else if (in_array($npcInfo['type'], array(M_NPC::CITY_NPC_FOOT, M_NPC::CITY_NPC_GUN, M_NPC::CITY_NPC_ARMOR, M_NPC::CITY_NPC_AIR))) { //军事属地10分钟过期
						$expireTime = $now + T_App::ONE_MINUTE * 10;
					}

					if ($expireTime > $now) {
						$pieces = array($expireTime, $npcInfo['type'], $npcInfo['res_data']);
						$restoreArmy = implode(',', $pieces);

						$upData = array(
							'city_id' => $cityId,
							'hold_expire_time' => $holdTime,
							'march_id' => $marchId,
							'last_fill_army_time' => $restoreArmy,
						);
						//Logger::dev("Colony setWildMapInfo".json_encode($upData));
						//更新野外地图信息
						$ret = M_MapWild::setWildMapInfo($posNo, $upData);
						//添加到占领队列
						M_March_Hold::set($posNo);
						$this->_buildSyncData($no);

						$ret = true;
					}
				}

				break;
			}
		}
		return $ret;
	}

	public function explore($no) {
		$ret = false;
		if (!empty($this->_data[$no][1])) {
			$now = time();
			$tmpTime = M_Formula::calcExploreTimeByTimes($this->_data[$no][2]);
			//$tmpTime = 0;
			$this->_data[$no][2] += 1;
			$this->_data[$no][3] = $now + $tmpTime;

			$this->_buildSyncData($no);

			$this->_change = true;
			$ret = true;
		}
		return $ret;
	}

	public function open($no) {
		$this->_data[$no][0] = 1;
		$this->_change = true;
		return true;
	}


	public function buildSyncData($no) {

		$ret = array();
		if (isset($this->_data[$no])) {
			$zone = $posx = $poxy = 0;
			$nickname = '';
			$faceId = '';
			$level = 0;

			$posNo = $this->_data[$no][1];
			$mapInfo = M_MapWild::getWildMapInfo($posNo);
			$marchId = $mapInfo['march_id'];
			$npcInfo = M_NPC::getInfo($mapInfo['npc_id']);
			if (!empty($npcInfo['id'])) {
				list($zone, $posx, $poxy) = M_MapWild::calcWildMapPosXYByNo($posNo);
				$nickname = $npcInfo['nickname'];
				$faceId = $npcInfo['face_id'];
				$level = intval($npcInfo['level']);
			}

			$ret = array(
				'IsOpen' => $this->_data[$no][0],
				'FaceId' => $faceId,
				'Name' => $nickname,
				'PosX' => $posx,
				'PosY' => $poxy,
				'PosArea' => $zone,
				'Level' => $level,
				'IsHold' => $marchId > 0 ? intval($marchId) : 0,
				'ExploreTimes' => $this->_data[$no][2],
				'ExploreExprieTime' => $this->_data[$no][3],
				'ExprieTime' => $this->_data[$no][4],
				'IntervalTime' => max($this->_data[$no][3] - time(), 0),
			);

			$this->_sync[$no] = $ret;
		}


		return $ret;
	}

	/**
	 * 删除野外城市信息
	 * @author huwei
	 * @param int $cityId
	 * @param int $posNo
	 * @return bool
	 */
	public function del($posNo) {
		$ret = false;
		list($no,) = $this->getNoByPos($posNo);
		if ($no) {

			$this->_data[$no][1] = 0; //地图编号
			$this->_data[$no][4] = 0; //属地到期时间

			//删除占领队列
			M_March_Hold::del($posNo);

			$this->_objPlayer->Res()->upGrow('npc_colony');

			//更新野外城市信息
			$upData = array('march_id' => 0, 'city_id' => 0, 'hold_expire_time' => 0);
			M_MapWild::setWildMapInfo($posNo, $upData);
			M_MapWild::syncWildMapBlockCache($posNo);

			$this->_sync[$no] = array(
				'IsOpen' => 1,
				'Name' => '',
				'FaceId' => '',
				'PosX' => 0,
				'PosY' => 0,
				'PosArea' => 0,
				'Level' => 0,
				'IsHold' => 0,
				'ExploreTimes' => 0,
				'ExploreExprieTime' => 0,
				'ExprieTime' => 0,
				'IntervalTime' => 0,
			);
		}

		return $ret;
	}

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
}
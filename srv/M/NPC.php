<?php

/**
 * 世界地图NPC各种操作模型层
 * @author chenhui on 20110513
 */
class M_NPC {
	/** 野外NPCID */
	static $WILD_NPC_IDS = array(
		//亚洲
		1 => array(9111, 9112, 9113, 9114, 9115, //补兵
			9121, 9122, 9123, 9124, 9125, //炮兵
			9131, 9132, 9133, 9134, 9135, //装甲
			9141, 9142, 9143, 9144, 9145, //航空
			9151, 9152, 9153, 9154, 9155, //金钱
			9161, 9162, 9163, 9164, 9165, //食物
			9171, 9172, 9173, 9174, 9175, //石油
		),
		//欧洲
		2 => array(9211, 9212, 9213, 9214, 9215, //补兵
			9221, 9222, 9223, 9224, 9225, //炮兵
			9231, 9232, 9233, 9234, 9235, //装甲
			9241, 9242, 9243, 9244, 9245, //航空
			9251, 9252, 9253, 9254, 9255, //金钱
			9261, 9262, 9263, 9264, 9265, //食物
			9271, 9272, 9273, 9274, 9275, //石油
		),
		//非洲
		3 => array(9311, 9312, 9313, 9314, 9315, //补兵
			9321, 9322, 9323, 9324, 9325, //炮兵
			9331, 9332, 9333, 9334, 9335, //装甲
			9341, 9342, 9343, 9344, 9345, //航空
			9351, 9352, 9353, 9354, 9355, //金钱
			9361, 9362, 9363, 9364, 9365, //食物
			9371, 9372, 9373, 9374, 9375, //石油
		),
	);
	/** 城市步兵NPC */
	const CITY_NPC_FOOT = 1;
	/** 城市炮兵NPC */
	const CITY_NPC_GUN = 2;
	/** 城市装甲兵NPC */
	const CITY_NPC_ARMOR = 3;
	/** 城市航空兵NPC */
	const CITY_NPC_AIR = 4;
	/** 道具NPC */
	const CITY_NPC_PROPS = 5;
	/** 图纸NPC */
	const CITY_NPC_DWG = 6;
	/** 副本NPC */
	const FB_NPC = 7;
	/** 据点NPC */
	const CAMP_NPC = 8;
	/** 金钱资源点 */
	const RES_NPC_GOLD = 9;
	/** 粮食资源点 */
	const RES_NPC_FOOD = 10;
	/** 石油资源点 */
	const RES_NPC_OIL = 11;
	/** 临时NPC */
	const TMP_NPC = 12;
	/** 突围NPC */
	const BOUT_NPC = 13;
	/** 多人副本NPC */
	const MULTI_FB = 14;
	/** 固定法西斯 */
	const FASCIST_NPC = 15;

	static $NpcType = array(
		self::CITY_NPC_FOOT  => '步兵学院',
		self::CITY_NPC_GUN   => '炮兵学院',
		self::CITY_NPC_ARMOR => '装甲兵学院',
		self::CITY_NPC_AIR   => '航空兵学院',
		self::CITY_NPC_PROPS => '道具NPC',
		self::CITY_NPC_DWG   => '图纸NPC',
		self::FB_NPC         => '副本NPC',
		self::CAMP_NPC       => '据点NPC',
		self::RES_NPC_GOLD   => '金钱资源NPC',
		self::RES_NPC_FOOD   => '食物资源NPC',
		self::RES_NPC_OIL    => '石油资源NPC',
		self::TMP_NPC        => '临时NPC',
		self::BOUT_NPC       => '突围NPC',
		self::MULTI_FB       => '多人副本NPC',
		self::FASCIST_NPC    => '固定法西斯',
	);


	/**
	 * 根据ID获取NPC基础信息
	 * @author chenhui on 20110513
	 * @param int $id NPC的ID
	 * @return array/false
	 */
	static public function getInfo($npcId) {
		$apcKey = T_Key::BASE_NPC . '_' . $npcId;
		$info   = B_Cache_APC::get($apcKey);
		if (empty($info)) {
			$list = M_Base::npcAll();
			$info = isset($list[$npcId]) ? $list[$npcId] : array();
			APC::set($apcKey, $info);
		}
		return $info;
	}

	/**
	 * 获取NPC英雄信息
	 * @author huwei on 20110627
	 * @param int $heroId NPC英雄ID
	 * @return array
	 */
	static public function getNpcHeroInfo($heroId) {
		if (empty($heroId)) {
			trigger_error(__METHOD__ . ':' . $heroId);
		}

		$apcKey = T_Key::BASE_NPC_HERO . '_' . $heroId;
		$info   = B_Cache_APC::get($apcKey);
		if (empty($info)) {
			$s    = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '-', '_');
			$info = B_DB::instance('BaseNpcHero')->get($heroId);
			if (!empty($info)) {
				M_Skill::getBaseEffectByHero($info);
				M_Equip::getBaseEffectByHero($info);
				$info['nickname']          = str_replace($s, '', $info['nickname']);
				$info['training_lead']     = 0;
				$info['training_command']  = 0;
				$info['training_military'] = 0;
				Logger::base(__METHOD__ . '#' . $heroId);
				APC::set($apcKey, $info);
			} else {
				Logger::error(array(__METHOD__, func_get_args()));
			}
		}

		return $info;
	}

	/**
	 * 插入NPC信息
	 * @author chenhui on 20110513
	 * @param array $info NPC数据 数组
	 * @return int/false
	 */
	static public function insert($info) {
		$ret = false;
		if (is_array($info) && !empty($info)) {
			$ret = B_DB::instance('BaseNpcTroop')->insert($info);
		}
		return $ret;
	}

	/**
	 * 根据NPC ID更新基础NPC信息
	 * @author chenhui on 20110513
	 * @param int id NPC ID
	 * @param array updinfo 要更新的键值对数组
	 * @return bool true/false
	 */
	static public function updateInfo($npcId, $fieldArr, $isUp = false) {
		$ret   = false;
		$npcId = intval($npcId);

		if (!empty($npcId) && is_array($fieldArr) && !empty($fieldArr)) {
			$apcKey = T_Key::BASE_NPC . $npcId;
			if (!empty($fieldArr)) {
				$ret = B_DB::instance('BaseNpcTroop')->update($fieldArr, $npcId);
			}
			APC::del($apcKey);
		}
		return $ret;
	}

	static public function getRandTempNpcRefreshData() {
		$rc          = new B_Cache_RC(T_Key::HAD_REFRESH_TMP_NPC, 'rand_temp_npc');
		$refreshData = $rc->jsonget();
		return $refreshData;
	}

	static public function getFixedTempNpcRefreshData() {
		$rc          = new B_Cache_RC(T_Key::HAD_REFRESH_TMP_NPC, 'fixed_temp_npc');
		$refreshData = $rc->jsonget();
		return $refreshData;
	}

	/**
	 * 获取临时NPC数据
	 * @param bool $sync
	 * @return array
	 */
	static public function getRandTempNpcConf() {
		return M_Config::getVal('wild_refresh_npc');
	}

	/**
	 * 获取固定法西斯数据
	 * @param bool $sync
	 * @return array
	 */
	static public function getFixedTempNpcConf() {
		return M_Config::getVal('wild_fixed_npc');
	}

	/**
	 * 刷新野外地图NPC
	 */
	static public function wildnpc1() {
		//区分NPC所在洲


		$baseAreaList  = M_MapWild::getWildMapAreaList();
		$totalAreaNum  = count($baseAreaList);
		$tmpAreaNoList = array_keys($baseAreaList);

		$now = time();

		$npcList  = B_DB::instance('BaseNpcTroop')->getAllMapNpc();
		$baseConf = M_Config::getVal();
		//基础区块数据

		$posList        = array();
		$npcConf        = $baseConf['config_probe_map'];
		$needSyncAreaNo = array();

		//获取当前战区可能的地形
		$terrain     = T_App::TERRAIN_PLAIN;
		$weather     = T_App::WEATHER_CLEAR;
		$refreshTime = $now + $baseConf['weather_refresh_interval'] * T_App::ONE_HOUR;

		foreach (T_App::$map as $zone => $zname) {
			echo "make wild npc zone#{$zone}\n";
			if (isset($baseConf['map_zone_terrain'][$zone])) {
				//随机一个地形给城市
				$randKey = array_rand($baseConf['map_zone_terrain'][$zone]);
				$terrain = $baseConf['map_zone_terrain'][$zone][$randKey];
			}

			//获取当前战区可能的天气
			if (isset($baseConf['map_zone_weather'][$terrain])) {
				//随机一个地形给城市
				$randKey = array_rand($baseConf['map_zone_weather'][$terrain]);
				$weather = $baseConf['map_zone_weather'][$terrain][$randKey];
			}

			$npcList = self::$WILD_NPC_IDS;
			foreach ($npcList[$zone] as $npcId) {
				$npcInfo = M_NPC::getInfo($npcId);
				if ($npcInfo) {
					//分配数量不能超过3600
					$num = min($npcConf['npc_num'][$npcInfo['level']], $totalAreaNum);

					$bClean = B_DB::instance('WildMap')->clean($zone, $npcId);

					$areaList = (array)array_rand($tmpAreaNoList, $num);
					//echo "zone#{$zone};npcId#{$npcId};Num#{$num};areaList#".json_encode($areaList)."\n";
					$tmp = array();
					foreach ($areaList as $indexNo) {
						$areaNo   = $tmpAreaNoList[$indexNo];
						$posNoArr = isset($baseAreaList[$areaNo]) ? $baseAreaList[$areaNo] : array();

						$tmpStr                  = $areaNo . '_' . $zone;
						$needSyncAreaNo[$tmpStr] = 1; //统一更新区块数据
						shuffle($posNoArr); //随机区块坐标
						//echo json_encode($posNoArr). "\n";
						//echo "areaNo#{$areaNo}=>".json_encode($posNoArr)."\n";
						foreach ($posNoArr as $posXY) {
							list($x, $y) = explode('_', $posXY);
							$posNo   = M_MapWild::calcWildMapPosNoByXY($zone, $x, $y);
							$mapInfo = M_MapWild::getWildMapInfo($posNo);
							//echo $mapInfo['type']."\n";
							if ($mapInfo['type'] == T_Map::WILD_MAP_CELL_SPACE) {
								$newMapInfo = array(
									'pos_no'               => $posNo,
									'type'                 => T_Map::WILD_MAP_CELL_NPC,
									'npc_id'               => $npcId,
									'city_id'              => 0,
									'terrain'              => $terrain,
									'weather'              => $weather,
									'march_id'             => 0,
									'weather_refresh_time' => $refreshTime,
									'hold_expire_time'     => 0,
								);

								$ret = B_DB::instance('WildMap')->insert($newMapInfo);
								$ret && M_MapWild::setWildMapInfo($posNo, $newMapInfo);
								$posList[] = $posNo;
								$tmp[]     = $posNo;
								break;
							}
						}
					}
					echo "posList#" . json_encode($tmp) . "\n================================================\n";
				} else {
					Logger::error(array(__METHOD__, 'npc not exist!', $npcId));
				}
			}
		}

		echo "make new wild map cache\n";
		foreach ($needSyncAreaNo as $areaStr => $tmp) {
			list($areaNo, $zone) = explode('_', $areaStr);
			$ret = M_MapWild::setWildMapAreaCache($zone, $areaNo);
			//Logger::debug(array(__METHOD__, $zone, $areaNo));
			echo $areaStr . '-';
		}

		return $posList;

	}

	/**
	 * 刷新野外地图NPC
	 */
	static public function wildnpc() {
		//区分NPC所在洲




		$now = time();
		$baseConf = M_Config::getVal();
		//基础区块数据

		$posList        = array();
		$npcConf        = $baseConf['config_probe_map'];
		$needSyncAreaNo = array();

		//获取当前战区可能的地形
		$terrain     = T_App::TERRAIN_PLAIN;
		$weather     = T_App::WEATHER_CLEAR;
		$refreshTime = $now + $baseConf['weather_refresh_interval'] * T_App::ONE_HOUR;

		foreach (T_App::$map as $zone => $zname) {

			$initPos = M_Npc::buildPos();
			echo "make wild npc zone#{$zone}\n";
			if (isset($baseConf['map_zone_terrain'][$zone])) {
				//随机一个地形给城市
				$randKey = array_rand($baseConf['map_zone_terrain'][$zone]);
				$terrain = $baseConf['map_zone_terrain'][$zone][$randKey];
			}

			//获取当前战区可能的天气
			if (isset($baseConf['map_zone_weather'][$terrain])) {
				//随机一个地形给城市
				$randKey = array_rand($baseConf['map_zone_weather'][$terrain]);
				$weather = $baseConf['map_zone_weather'][$terrain][$randKey];
			}

			$npcList = self::$WILD_NPC_IDS;
			foreach ($npcList[$zone] as $npcId) {
				$npcInfo = M_NPC::getInfo($npcId);
				if ($npcInfo) {
					//分配数量不能超过3600
					$lv = $npcInfo['level'];
					$num = $npcConf['npc_num'][$lv];

					$posList = array();
					$bClean = B_DB::instance('WildMap')->clean($zone, $npcId);
					for($n=$num;$n>0;$n--) {
						list($x,$y) = array_pop($initPos);
						$posNo   = M_MapWild::calcWildMapPosNoByXY($zone, $x, $y);
						$mapInfo = M_MapWild::getWildMapInfo($posNo);
						//echo $mapInfo['type']."\n";
						if ($mapInfo['type'] == T_Map::WILD_MAP_CELL_SPACE) {
							$newMapInfo = array(
								'pos_no'               => $posNo,
								'type'                 => T_Map::WILD_MAP_CELL_NPC,
								'npc_id'               => $npcId,
								'city_id'              => 0,
								'terrain'              => $terrain,
								'weather'              => $weather,
								'march_id'             => 0,
								'weather_refresh_time' => $refreshTime,
								'hold_expire_time'     => 0,
							);

							$ret = B_DB::instance('WildMap')->insert($newMapInfo);
							$ret && M_MapWild::setWildMapInfo($posNo, $newMapInfo);
							$posList[] = $posNo;
						}
					}

					echo "posList#" . json_encode($posList) . "\n================================================\n";
				} else {
					Logger::error(array(__METHOD__, 'npc not exist!', $npcId));
				}
			}
		}

	}

	static public function makeInitNpcPos($a,$b) {
		$ret = array();
		for($i=1;$i<150;$i++) {
			for($j=1;$j<150;$j++) {
				if($i%$a==0 && $j%$b==0) {
					$ret[] = array($i,$j);
				}
			}
		}

		return $ret;
	}

	static public function buildPos() {
		$makeParam = array(
			1 => array(6,4),//350 378
			//2 => array(11,7),//280 288
			//3 => array(13,9),//210 222
			//4 => array(15,11),//140 145
			//5 => array(17,13),//70 72
		);
		$baseConf = M_Config::getVal();
		//基础区块数据
		$npcConf        = $baseConf['config_probe_map'];
		$tmp = $initPos = array();
		$t = $t1 = 0;
		foreach($makeParam as $k => $v) {

			$ttt = M_NPC::makeInitNpcPos($v[0],$v[1]);
			$n = $npcConf['npc_num'][$k]*7;

			foreach($ttt as $p) {
				$str = $p[0]."_".$p[1];
				$initPos[$str] = $p;
			}

			$n1 = count($ttt);
			echo $k."=>{$n}=>".$n1."<br>";
			$t += $n;
			$t1 += $n1;
		}
		shuffle($initPos);
		return $initPos;
	}

	/**
	 * 刷新随机位置临时NPC
	 */
	static public function refreshRandTempNpc() {
		$conf = M_NPC::getRandTempNpcConf();
		//Logger::debug(array(__METHOD__,$conf));
		$day = date('d');
		$now = time();

		//基础区块数据
		$baseAreaList  = M_MapWild::getWildMapAreaList();
		$tmpAreaNoList = array_keys($baseAreaList);
		//总区块数
		$totalAreaNum = count($baseAreaList);

		//当天以刷新过的NPC数据
		$rc             = new B_Cache_RC(T_Key::HAD_REFRESH_TMP_NPC, 'rand_temp_npc');
		$refreshData    = $rc->jsonget();
		$needSyncAreaNo = array();
		if (empty($refreshData)) {
			$refreshData = array();
		}
		//Logger::debug(array(__METHOD__,$refreshData));

		$tmpRc        = new B_Cache_RC(T_Key::TMP_EXPIRE, 'npc');
		$tmpExpireArr = $tmpRc->jsonget();
		//Logger::debug(array(__METHOD__,$tmpExpireArr));
		foreach ($conf as $troopId => $val) {
			$npcInfo = M_NPC::getInfo($troopId);
			if (empty($npcInfo)) {
				Logger::error(array(__METHOD__, 'npc not exist!', $troopId));
				continue;
			}
			//'10020' => array('2012-04-12', '2013-04-13', '12:00:00', '13:00:00', 100, '1|2|3', 0, array(10=>11002, 20=>1102, 30=>1000, 40=>11002, 50=>1102, 80=>1000)),
			list($startDate, $endDate, $startTime, $endTime, $num, $zoneStr, $needRadio, $awardArr) = $val;
			$sTime   = strtotime($startTime);
			$eTime   = strtotime($endTime);
			$nowDate = date('Ymd');
			$sDate   = date('Ymd', strtotime($startDate));
			$eDate   = date('Ymd', strtotime($endDate));

			$startRadioTime = $sTime - 30 * T_App::ONE_MINUTE;

			$expireTime = isset($tmpExpireArr[$troopId]) && ($tmpExpireArr[$troopId] >= $startRadioTime) ? $tmpExpireArr[$troopId] : $startRadioTime;

			$zoneArr = explode('|', $zoneStr);
			//Logger::debug(array(__METHOD__, $troopId, $expireTime, date('Y-m-d H:i:s', $expireTime), date('Y-m-d H:i:s', $sTime), date('Y-m-d H:i:s', $now)));

			if ($nowDate >= $sDate &&
				$nowDate <= $eDate &&
				$now > $expireTime &&
				$now < $sTime
			) //30分钟内
			{
				$tmpExpireArr[$troopId] = $expireTime + 10 * T_App::ONE_MINUTE;
				//临时NPC将要被刷新
				$tArr  = explode(':', $startTime);
				$z     = $zoneArr[0];
				$title = json_encode(array(T_Lang::TMP_NPC_WOULD_OUT, $npcInfo['nickname'], $tArr[0], $tArr[1], array(T_Lang::$Map[$z])));
				$msg   = implode("\t", array($title, T_Chat::SYS_RADIO_PRIO, T_Chat::SYS_RADIO_STAY_TIME));
				M_Chat::addWorldMessage(uniqid(), $msg, T_Chat::CHAT_SYS_RADIO);
			}

			//Logger::debug(array(__METHOD__,$troopId, !isset($refreshData[$troopId]), $nowDate >= $sDate, $nowDate <= $eDate,   $now > $sTime,  $now < $eTime, date('Y-m-d H:i:s'), date('Y-m-d H:i:s', $sTime), date('Y-m-d H:i:s', $eTime)));

			if (!isset($refreshData[$troopId]) &&
				$nowDate >= $sDate &&
				$nowDate <= $eDate &&
				$now > $sTime &&
				$now < $eTime
			) {
				unset($tmpExpireArr[$troopId]);

				$posList = array();

				foreach ($zoneArr as $zone) //先循环洲
				{
					//获取当前战区可能的地形
					list($terrain, $weather, $weatherRefreshTime) = M_MapWild::getRndTerrainAndWeather($zone);

					$num      = min($num, $totalAreaNum);
					$areaList = (array)array_rand($tmpAreaNoList, $num);

					//echo "zone#{$zone};npcId#{$troopId};Num#{$num};areaList#".json_encode($areaList)."\n";

					$tmp = array();
					foreach ($areaList as $indexNo) {
						//计算区块
						$areaNo   = $tmpAreaNoList[$indexNo];
						$posNoArr = isset($baseAreaList[$areaNo]) ? $baseAreaList[$areaNo] : array();

						$tmpStr                  = $areaNo . '_' . $zone;
						$needSyncAreaNo[$tmpStr] = 1; //统一更新区块数据

						shuffle($posNoArr); //随机区块坐标

						foreach ($posNoArr as $posXY) {
							list($x, $y) = explode('_', $posXY);
							$posNo   = M_MapWild::calcWildMapPosNoByXY($zone, $x, $y);
							$mapInfo = M_MapWild::getWildMapInfo($posNo);
							if ($mapInfo['type'] == T_Map::WILD_MAP_CELL_SPACE) {
								$newMapInfo = array(
									'pos_no'               => $posNo,
									'type'                 => T_Map::WILD_MAP_CELL_NPC,
									'npc_id'               => $troopId,
									'city_id'              => 0,
									'terrain'              => $terrain,
									'weather'              => $weather,
									'march_id'             => 0,
									'weather_refresh_time' => $weatherRefreshTime,
									'hold_expire_time'     => 0,
								);

								$ret       = M_MapWild::setWildMapInfo($posNo, $newMapInfo);
								$posList[] = $posNo;
								break;
							}
						}
					}
				}
				//npc已经出现
				$title = json_encode(array(T_Lang::TMP_NPC_HAD_OUT, $npcInfo['nickname'], array(T_Lang::$Map[$zone])));
				$msg   = implode("\t", array($title, T_Chat::SYS_RADIO_PRIO, T_Chat::SYS_RADIO_STAY_TIME));
				M_Chat::addWorldMessage(uniqid(), $msg, T_Chat::CHAT_SYS_RADIO);

				//设置结束时间
				$refreshData[$troopId] = array('end_time' => $eTime, 'list' => $posList);
			}
		}

		$tmpRc->jsonset($tmpExpireArr);

		//删除过期的npc数据
		foreach ($refreshData as $delTroopId => $val) {
			if ($now > $val['end_time']) {
				foreach ($val['list'] as $posNo) //先循环洲
				{
					//Logger::debug(array(__METHOD__, $posNo));
					M_MapWild::cleanWildMapInfo($posNo);
					list($z, $x, $y) = M_MapWild::calcWildMapPosXYByNo($posNo);
					$areaNo                  = M_MapWild::calcWildMapAreaNoByPos($x, $y);
					$tmpStr                  = "{$areaNo}_{$z}";
					$needSyncAreaNo[$tmpStr] = 1;
				}
				unset($refreshData[$delTroopId]);
			}
		}

		$ret = $rc->jsonset($refreshData, T_App::ONE_WEEK);

		foreach ($needSyncAreaNo as $areaStr => $tmp) {
			list($areaNo, $zone) = explode('_', $areaStr);
			$ret = M_MapWild::setWildMapAreaCache($zone, $areaNo);
			//Logger::debug(array(__METHOD__, $zone, $areaNo));
		}

	}

	/**
	 * 刷新固定位置的临时NPC
	 */
	static public function refreshFixedTempNpc() {
		$conf = M_NPC::getFixedTempNpcConf(); //得到固定法西斯的数据
		//Logger::debug(array(__METHOD__, 'getFixedTempNpcConf', $conf));

		$day            = date('d'); //当日
		$now            = time(); //当时
		$needSyncAreaNo = array();

		if (!empty($conf)) {
			$rc          = new B_Cache_RC(T_Key::HAD_REFRESH_TMP_NPC, 'fixed_temp_npc');
			$refreshData = $rc->jsonget();
			//Logger::debug(array(__METHOD__, 'refreshData', $refreshData));
			$tmpRc        = new B_Cache_RC(T_Key::TMP_EXPIRE, 'fixed_npc');
			$tmpExpireArr = $tmpRc->jsonget();
			foreach ($conf as $troopId => $val) {
				$npcInfo = M_NPC::getInfo($troopId);
				if (empty($npcInfo)) {
					Logger::error(array(__METHOD__, 'npc not exist!', $troopId));
					continue;
				}

				$startRadioTime = strtotime($val['broadcast_start']);
				$endRadioTime   = strtotime($val['broadcast_end']);

				$npcZone = $val['npc_zone'];
				$npcPos  = $val['npc_pos'];

				$npcAwardArr = $val['npc_awardArr'];

				$expireTime = isset($tmpExpireArr[$troopId]) && ($tmpExpireArr[$troopId] >= $startRadioTime) ? $tmpExpireArr[$troopId] : $startRadioTime;
				// 				Logger::debug('============'.date('Y-m-d H:i:s',$expireTime));
				if ($now >= $expireTime &&
					$now <= $endRadioTime &&
					!empty($val['broadcast'])
				) {
					//广播开始结束之间，5分钟发一次，每次持续5秒
					$tmpExpireArr[$troopId] = $now + $val['Interval_broadcast'] * T_App::ONE_MINUTE;
					if (!empty($val['broadcast'])) {
						$title = json_encode(array($val['broadcast'])); //广播的内容
						$msg   = implode("\t", array($title, T_Chat::SYS_RADIO_PRIO, T_Chat::SYS_RADIO_STAY_TIME));
						M_Chat::addWorldMessage(uniqid(), $msg, T_Chat::CHAT_SYS_RADIO);
					}
					if (!empty($val['channel'])) {
						$title1 = json_encode(array($val['channel'])); //广播的内容
						$msg1   = implode("\t", array($title1, T_Chat::SYS_RADIO_PRIO, T_Chat::SYS_RADIO_STAY_TIME));
						M_Chat::addWorldMessage(uniqid(), $msg1, T_Chat::CHAT_SYS);
					}
				}

				$sTime = strtotime($val['npc_start']);
				$eTime = strtotime($val['npc_end']); //npc_pos

				list($x, $y) = explode(',', $npcPos);

				if (empty($refreshData[$troopId]['list']) &&
					$now > $sTime &&
					$now < $eTime
				) {
					$posList = array();
					unset($tmpExpireArr[$troopId]);
					$posNo   = M_MapWild::calcWildMapPosNoByXY($npcZone, $x, $y);
					$mapInfo = M_MapWild::getWildMapInfo($posNo);
					//Logger::debug(array(__METHOD__, 'map info ', $mapInfo));

					if ($mapInfo['type'] == T_Map::WILD_MAP_CELL_SPACE) {

						list($terrain, $weather, $weatherRefreshTime) = M_MapWild::getRndTerrainAndWeather($npcZone);

						$newMapInfo = array(
							'pos_no'               => $posNo,
							'type'                 => T_Map::WILD_MAP_CELL_NPC,
							'npc_id'               => $troopId,
							'city_id'              => 0,
							'terrain'              => $terrain,
							'weather'              => $weather,
							'march_id'             => 0,
							'weather_refresh_time' => $weatherRefreshTime,
							'hold_expire_time'     => 0,
						);

						M_MapWild::setWildMapInfo($posNo, $newMapInfo);

						$areaNo = M_MapWild::calcWildMapAreaNoByPos($x, $y);
						$tmpStr = $areaNo . '_' . $npcZone;

						//Logger::debug(array(__METHOD__, 'setWildMapInfo', $tmpStr));

						$needSyncAreaNo[$tmpStr] = 1;
						$posList[]               = $posNo;
						//npc已经出现
						if (!empty($val['out_broadcast'])) {
							$title = json_encode(array($val['out_broadcast']));
							$msg   = implode("\t", array($title, T_Chat::SYS_RADIO_PRIO, T_Chat::SYS_RADIO_STAY_TIME));
							M_Chat::addWorldMessage(uniqid(), $msg, T_Chat::CHAT_SYS_RADIO);
						}
					}

					//设置结束时间
					$refreshData[$troopId] = array('end_time' => $eTime, 'list' => $posList);
				}

			}

			//Logger::debug(array(__METHOD__, 'refreshData', $refreshData));
			$tmpRc->jsonset($tmpExpireArr);
			//删除过期的npc数据
			if (!empty($refreshData)) {
				foreach ($refreshData as $delTroopId => $val) {
					if ($now > $val['end_time']) {
						//Logger::debug(array(__METHOD__, 'delTroopId', $delTroopId, $val));

						foreach ($val['list'] as $posNo) //先循环洲
						{
							//Logger::debug(array(__METHOD__, $posNo));
							M_MapWild::cleanWildMapInfo($posNo);
							list($z, $x, $y) = M_MapWild::calcWildMapPosXYByNo($posNo);
							$areaNo                  = M_MapWild::calcWildMapAreaNoByPos($x, $y);
							$tmpStr                  = "{$areaNo}_{$z}";
							$needSyncAreaNo[$tmpStr] = 1;
						}
						unset($refreshData[$delTroopId]);
					}
				}
			}

			$ret = $rc->jsonset($refreshData, T_App::ONE_WEEK);
			if (!empty($needSyncAreaNo)) {
				foreach ($needSyncAreaNo as $areaStr => $tmp) {
					list($areaNo, $zone) = explode('_', $areaStr);
					$ret = M_MapWild::setWildMapAreaCache($zone, $areaNo);
					//Logger::debug(array(__METHOD__, 'setWildMapAreaCache', $zone, $areaNo));
				}
			}
		}

	}
}

?>
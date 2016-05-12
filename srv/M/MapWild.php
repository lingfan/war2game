<?php

/**
 * 地图模块
 */
class M_MapWild {
	/** 初始未占领坐标分割 */
	const DIV_NUM = 10;

	/** 城外地图缓存块分割X (每个缓存块5*5个坐标)**/
	const WILD_MAP_SPLIT_AREA_X = 5;
	/** 城外地图缓存块分割Y **/
	const WILD_MAP_SPLIT_AREA_Y = 5;

	/** 城外地图最大坐标X **/
	const WILD_MAP_MAX_POS_X = 150;
	/** 城外地图最大坐标Y **/
	const WILD_MAP_MAX_POS_Y = 150;
	/** 150*150 / 5*5 = 900 */
	const WILD_MAP_MAX_AREA = 900;

	/**
	 * 随机获取未占用的坐标
	 * @author huwei on 20111013
	 * @param int $zone
	 */
	static public function getWildMapNoHoldPos($zone) {
		$pos = false;
		if (isset(T_App::$map[$zone])) {
			$pos = M_MapWild::getNoHoldMapPos($zone);

		}
		return $pos;
	}


	static public function _getRndEmptyPos($zone) {
		$i = 1;
		while ($i < 1000) {
			$x       = rand(5, self::WILD_MAP_MAX_POS_X - 5);
			$y       = rand(5, self::WILD_MAP_MAX_POS_Y - 5);
			$posNo   = M_MapWild::calcWildMapPosNoByXY($zone, $x, $y);
			$mapInfo = M_MapWild::getWildMapInfo($posNo);
			if ($mapInfo['type'] == T_Map::WILD_MAP_CELL_SPACE && empty($mapInfo['city_id'])) {
				$divNo = $posNo % M_MapWild::DIV_NUM;
				$rc    = new B_Cache_RC(T_Key::WILD_MAP_NO_HOLD_POS, $zone . $divNo);
				$rc->sadd($posNo);
				$i++;
			}

		}

	}

	/**
	 * 生成二进制地图数据 存贮到缓存
	 * @author huwei
	 * @param array $data
	 */
	static private function _makeWildMapAreaBinData($data) {
		//	Logger::error(array(__METHOD__, 'make_in', func_get_args()));
		$binStr = '';
		if (!empty($data['type'])) {
			$mapCellType = array(
				T_Map::WILD_MAP_CELL_SPACE  => 0,
				T_Map::WILD_MAP_CELL_CITY   => 101,
				T_Map::WILD_MAP_CELL_SCENIC => 102,
				T_Map::WILD_MAP_CELL_NPC    => array(
					M_NPC::CITY_NPC_FOOT  => 103,
					M_NPC::CITY_NPC_GUN   => 104,
					M_NPC::CITY_NPC_ARMOR => 105,
					M_NPC::CITY_NPC_AIR   => 106,
					M_NPC::CITY_NPC_PROPS => 107,
					M_NPC::CITY_NPC_DWG   => 108,
					M_NPC::RES_NPC_GOLD   => 109,
					M_NPC::RES_NPC_FOOD   => 110,
					M_NPC::RES_NPC_OIL    => 111,
					M_NPC::TMP_NPC        => 112,
					M_NPC::FASCIST_NPC    => 112,
				),
			);
			//	Logger::error(array('1111111111'));
			list($zone, $posX, $posY) = M_MapWild::calcWildMapPosXYByNo($data['pos_no']);
			$binPosX = B_Utils::mapDec2Bin($posX, 2);
			$binPosY = B_Utils::mapDec2Bin($posY, 2);
			//	Logger::error(array('data_type==', $data['type']));
			switch ($data['type']) {
				case T_Map::WILD_MAP_CELL_SPACE:
					break;
				case T_Map::WILD_MAP_CELL_CITY:
					$cityInfo = M_City::getInfo($data['city_id']);
					if (!empty($cityInfo)) {
						//Logger::error(array('22222222222'));
						$occupiedCityId   = $expireTime = $marchstatus = 0;
						$cityName         = $cityInfo['nickname'];
						$wildType         = $mapCellType[T_Map::WILD_MAP_CELL_CITY];
						$tmpCityLv        = $cityInfo['level'];
						$cityId           = $cityInfo['id'];
						$occupiedCityName = '';
						if (M_March_Hold::exist($cityInfo['pos_no'])) {
							//Logger::error(array('33333333333333333'));
							$now              = time();
							$cityColonyInfo   = M_ColonyCity::getInfo($cityId);
							$occupiedCityId   = $cityColonyInfo['atk_city_id'];
							$atkCityInfo      = M_City::getInfo($occupiedCityId);
							$occupiedCityName = $atkCityInfo['nickname'];
							$colonyHoldTime   = isset($cityColonyInfo['hold_time']) ? $cityColonyInfo['hold_time'] : 0;
							$holdTimeInterval = M_Config::getVal('hold_city_time_interval');
							$expireTime       = $now + (T_App::ONE_HOUR * $holdTimeInterval - $colonyHoldTime);
							//$expireTime = $colonyHoldTime;
							if ($cityColonyInfo['atk_march_id'] > 0) {
								$marchstatus = 1;
							}
						}
						//Logger::error(array('44444444444444444'));
						$binPkgNo            = B_Utils::mapDec2Bin($wildType, 1);
						$cityNameLen         = B_Utils::mapDec2Bin(mb_strlen($cityName), 1);
						$newbie              = B_Utils::mapDec2Bin($cityInfo['newbie'], 1);
						$vipLvBin            = B_Utils::mapDec2Bin($cityInfo['vip_level'], 1); //vip等级
						$cityLvBin           = B_Utils::mapDec2Bin($tmpCityLv, 1);
						$cityIdBin           = B_Utils::mapDec2Bin($cityId, 4);
						$unionIdBin          = B_Utils::mapDec2Bin($cityInfo['union_id'], 4);
						$occupiedCityIdBin   = B_Utils::mapDec2Bin($occupiedCityId, 4);
						$occupiedCityNameLen = B_Utils::mapDec2Bin(mb_strlen($occupiedCityName), 1);
						$expireTimeLen       = B_Utils::mapDec2Bin(mb_strlen($expireTime), 1);
						$marchstatus         = B_Utils::mapDec2Bin($marchstatus, 1);
						//Logger::error(array('55555555555555555'));
						$unionName = '';
						if ($cityInfo['union_id'] > 0) { //获取联盟信息
							$unionInfo = M_Union::getInfo($cityInfo['union_id']);
							$unionName = isset($unionInfo['name']) ? $unionInfo['name'] : '';
						}
						$unionNameLen = B_Utils::mapDec2Bin(mb_strlen($unionName), 1);

						//Logger::debug(array(__METHOD__, $cityInfo['pos_no'].'|'.$occupiedCityId.'|'.$occupiedCityName.'|'.$expireTime));

						//坐标X 2|坐标Y 2|CityLv 1|CityId 4|UnionId 4|CityNameLength|CityName|Newbie 1|联盟名称字节长度 1|联盟名称|Vip等级 1|占领城市ID 4|占领城市名长度 1|占领城市名|占领过期时间长度 1|占领过期时间戳|驻军状态
						$binPkgData = $binPosX . $binPosY . $cityLvBin . $cityIdBin . $unionIdBin . $cityNameLen . $cityName . $newbie . $unionNameLen . $unionName . $vipLvBin . $occupiedCityIdBin . $occupiedCityNameLen . $occupiedCityName . $expireTimeLen . $expireTime . $marchstatus;
						$binPkgLen  = B_Utils::mapDec2Bin(mb_strlen($binPkgData), 2);

						$binStr = $binPkgLen . $binPkgNo . $binPkgData;
					}
					//Logger::error(array('66666666666666'));
					break;
				case T_Map::WILD_MAP_CELL_NPC:
					$npcInfo = M_NPC::getInfo($data['npc_id']);
					if (!empty($npcInfo)) {
						$npcId    = $data['npc_id'];
						$cityId   = 0;
						$cityName = '';
						if ($data['city_id'] > 0) {
							$cityId   = $data['city_id'];
							$cityInfo = M_City::getInfo($data['city_id']);
							$cityName = $cityInfo['nickname'];
						}

						$isHoldArmy = 0;
						if ($data['march_id'] > 0) {
							$isHoldArmy = 1;
						}
						$isHold = B_Utils::mapDec2Bin($isHoldArmy, 1);

						$cityId      = B_Utils::mapDec2Bin($cityId, 4);
						$cityNameLen = B_Utils::mapDec2Bin(mb_strlen($cityName), 1);

						$wildType   = $mapCellType[T_Map::WILD_MAP_CELL_NPC][$npcInfo['type']];
						$npcLv      = B_Utils::mapDec2Bin($npcInfo['level'], 1);
						$npcName    = $npcInfo['nickname'];
						$npcNameLen = B_Utils::mapDec2Bin(mb_strlen($npcName), 1);
						$binPkgNo   = B_Utils::mapDec2Bin($wildType, 1);

						$npcIdBin = B_Utils::mapDec2Bin($npcId, 4);
						if ($npcInfo['type'] == M_NPC::TMP_NPC) {
							$tmpRefreshData = M_NPC::getRandTempNpcRefreshData();
							$now            = time();
							if (isset($tmpRefreshData[$npcId]['end_time']) &&
								$now < $tmpRefreshData[$npcId]['end_time']
							) {
								$expireTime    = $tmpRefreshData[$npcId]['end_time'];
								$expireTimeLen = B_Utils::mapDec2Bin(mb_strlen($expireTime), 1);
								//Logger::debug(array(__METHOD__, $data['pos_no'], $data['npc_id'], $npcName, $expireTime, $tmpRefreshData[$npcId], date('Y-m-d H:i:s', $expireTime)));

								$baseTmpNpcFlag = M_Config::getVal('wild_refresh_npc_showflag');
								$showFlag       = isset($baseTmpNpcFlag[$npcId]) ? min($baseTmpNpcFlag[$npcId], 255) : 0;
								$showFlagBin    = B_Utils::mapDec2Bin($showFlag, 1);

								//坐标X|坐标Y|NpcLv|NpcNameLength|NpcName|结束时间长度|结束时间戳|显示形态
								$binPkgData = $binPosX . $binPosY . $npcLv . $npcNameLen . $npcName . $expireTimeLen . $expireTime . $showFlagBin;
								$binPkgLen  = B_Utils::mapDec2Bin(mb_strlen($binPkgData), 2);
								$binStr     = $binPkgLen . $binPkgNo . $binPkgData;
							} else {
								M_MapWild::cleanWildMapInfo($data['pos_no']);
							}
						} else if ($npcInfo['type'] == M_NPC::FASCIST_NPC) {
							$tmpRefreshData = M_NPC::getFixedTempNpcRefreshData();

							$now = time();
							if (isset($tmpRefreshData[$npcId]['end_time']) &&
								$now < $tmpRefreshData[$npcId]['end_time']
							) {
								$expireTime = $tmpRefreshData[$npcId]['end_time'];

								$expireTimeLen = B_Utils::mapDec2Bin(mb_strlen($expireTime), 1);
								//Logger::debug(array(__METHOD__, $data['pos_no'], $data['npc_id'], $npcName, $expireTime, $tmpRefreshData[$npcId], date('Y-m-d H:i:s', $expireTime)));

								$showFlagBin = B_Utils::mapDec2Bin(0, 1);

								//坐标X|坐标Y|NpcLv|NpcNameLength|NpcName|结束时间长度|结束时间戳
								$binPkgData = $binPosX . $binPosY . $npcLv . $npcNameLen . $npcName . $expireTimeLen . $expireTime . $showFlagBin;
								$binPkgLen  = B_Utils::mapDec2Bin(mb_strlen($binPkgData), 2);
								$binStr     = $binPkgLen . $binPkgNo . $binPkgData;

								//Logger::debug(array(__METHOD__, 'make pos cache', $npcId, $npcInfo['level'], $npcName, $expireTime-time(), base64_encode($binStr)));

							} else {
								Logger::debug(array(__METHOD__, 'cleanWildMapInfo', $data['pos_no']));
								M_MapWild::cleanWildMapInfo($data['pos_no']);
							}
						} else {
							$expireTime    = $data['hold_expire_time'];
							$expireTimeLen = B_Utils::mapDec2Bin(mb_strlen($expireTime), 1);

							//坐标X|坐标Y|NpcLv|城市ID|城市名称长度|城市名称|NpcNameLength|NpcName|占领结束时间长度|占领结束时间戳
							$binPkgData = $binPosX . $binPosY . $npcLv . $cityId . $cityNameLen . $cityName . $npcNameLen . $npcName . $expireTimeLen . $expireTime . $isHold;
							$binPkgLen  = B_Utils::mapDec2Bin(mb_strlen($binPkgData), 2);
							$binStr     = $binPkgLen . $binPkgNo . $binPkgData;
						}

					}
					break;
				case T_Map::WILD_MAP_CELL_SCENIC:
					if (!empty($data['scene_id'])) {
						$binPkgNo = B_Utils::mapDec2Bin($data['scene_id'], 4);
						$key      = $data['scene_id'] . $data['scene_start_pos'];
						$lv       = B_Utils::mapDec2Bin(0, 1);
						if (!isset($scenicArr[$key])) {
							$scenicArr[$key] = true;
							list($posX, $posY) = $data['scene_start_pos'];
							$binPosX     = B_Utils::mapDec2Bin($posX, 2);
							$binPosY     = B_Utils::mapDec2Bin($posY, 2);
							$list        = M_MapWild::getScencePosList($data['scene_id'], $data['scene_start_pos']);
							$posListData = '';
							if (is_array($list)) {
								foreach ($list as $val) {
									$posListData .= B_Utils::mapDec2Bin($val['pos_x'], 2) . B_Utils::mapDec2Bin($val['pos_y'], 2);
								}
							}

							$posListLen = mb_strlen($posListData);
							//起点坐标X|起点坐标Y|level|PosListLength|PosList
							$binPkgData = $binPosX . $binPosY . $lv . $posListLen . $posListData;
							$binPkgLen  = B_Utils::mapDec2Bin(mb_strlen($binPkgData), 2);
							$binStr     = $binPkgLen . $binPkgNo . $binPkgData;
						}
					}
					break;
			}
		}
		//Logger::error(array(__METHOD__, 'make_out'));
		return $binStr;
	}

	/**
	 * 获取地貌信息.
	 * @author huwei
	 * @param int $id 地貌ID
	 * @return array
	 */
	static public function getSceneInfo($id) {
		return B_DB::instance('Map')->get($id);
	}

	/**
	 * 获取地貌相关的坐标列表
	 * @author huwei
	 * @param int $scenceId 地貌ID
	 * @param string $startPos 开始坐标
	 * @return array
	 */
	static public function getScencePosList($scenceId, $startPos) {
		$apcKey = T_Key::MAP_SCENCE_LIST . $scenceId . '_' . $startPos;
		$result = B_Cache_APC::get($apcKey);
		if (!$result) {
			$result = B_DB::instance('Map')->getsBy(array('scene_start_pos' => $startPos, 'id' => $scenceId));
			APC::set($apcKey, $result);
		}
		return $result;
	}


	/**
	 * 插入地貌数据
	 * @author chenhui on 20110923
	 * @param array $info 地貌数据 1D
	 * @return bool
	 */
	static public function insertScenicInfo($info) {
		return B_DB::instance('Map')->insert($info);
	}

	/**
	 * 更新地貌数据
	 * @author chenhui on 20110923
	 * @param int $id
	 * @param array $updinfo 地貌数据 1D
	 * @return bool
	 */
	static public function updateScenicInfo($id, $updinfo) {
		return B_DB::instance('Map')->update($updinfo, $id);
	}

	/**
	 * 删除地貌数据
	 * @author chenhui on 20110923
	 * @param int $id 地图景点ID
	 * @return bool
	 */
	static public function deleteScenicInfo($id) {
		return B_DB::instance('Map')->delete($id);
	}

	/**
	 * 根据条件值获取地貌数据
	 * @author chenhui on 20110924
	 * @param array $parms 条件=>值
	 * @return array 地貌数据 2D
	 */
	static public function getScenicInfoByParm($parms) {
		return B_DB::instance('Map')->getsBy($parms);
	}

	/**
	 * 获取野外地图区块中的数据 一般有4个区块数据
	 * @author huwei
	 * @param int $zone 洲
	 * @param int $posX 起点X
	 * @param int $posY 起点Y
	 * @param int $weight 坐标行数
	 * @param int $height 坐标列数
	 * @return string
	 */
	static public function getWildMapBlock($zone, $posX, $posY, $weight, $height) {
		$areaXNum = max(ceil($weight / self::WILD_MAP_SPLIT_AREA_X), 1);
		//$areaXNum = min($areaXNum, 10);//X坐标列最大显示10个区块数据
		$areaYNum = max(ceil($height / self::WILD_MAP_SPLIT_AREA_Y), 1);
		//$areaYNum = min($areaYNum, 5);//Y坐标列最大显示5个区块数据

		$ret      = '';
		$areaList = self::_calcWildMapAreaList($posX, $posY, $areaXNum, $areaYNum);

		$data = array();
		foreach ($areaList as $areaNo) {
			$ret .= self::_getWildMapAreaCacheByAreaNo($areaNo, $zone);
		}

		return $ret;
	}

	/** 初始化野外地图数据 */
	static public function initWildMapData($cityId, $posNo) {
		$ret = false;
		if ($posNo > 0 && !empty($cityId)) {
			$baseConf = M_Config::getVal();
			list($zone, $posX, $posY) = M_MapWild::calcWildMapPosXYByNo($posNo);

			//获取当前战区可能的地形
			$terrain = T_App::TERRAIN_PLAIN;
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
			} else {
				$weather = T_App::WEATHER_CLEAR;
			}
			$refreshTime = time() + $baseConf['weather_refresh_interval'] * T_App::ONE_HOUR;

			$fileds = array(
				'pos_no'               => $posNo,
				'city_id'              => $cityId,
				'terrain'              => $terrain,
				'weather'              => $weather,
				'weather_refresh_time' => $refreshTime,
				'type'                 => T_Map::WILD_MAP_CELL_CITY,
				'march_id'             => 0,
				'npc_id'               => 0,
				'hold_expire_time'     => 0,
			);
			$ret    = B_DB::instance('WildMap')->insert($fileds, true);
			$ret && self::setWildMapInfo($posNo, $fileds);
			$ret && self::syncWildMapBlockCache($posNo);
		}

		return $ret;
	}

	/** 迁城时初始化野外地图数据 */
	static public function initWildMapDataMove($cityId, $posNo, $hold_expire_time = 0) {
		$ret = false;
		if ($posNo > 0 && !empty($cityId)) {
			$expire   = !empty($hold_expire_time) ? intval($hold_expire_time) : 0;
			$baseConf = M_Config::getVal();
			list($zone, $posX, $posY) = M_MapWild::calcWildMapPosXYByNo($posNo);

			//获取当前战区可能的地形
			$terrain = T_App::TERRAIN_PLAIN;
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
			} else {
				$weather = T_App::WEATHER_CLEAR;
			}
			$refreshTime = time() + $baseConf['weather_refresh_interval'] * T_App::ONE_HOUR;

			$fileds = array(
				'pos_no'               => $posNo,
				'city_id'              => $cityId,
				'terrain'              => $terrain,
				'weather'              => $weather,
				'weather_refresh_time' => $refreshTime,
				'type'                 => T_Map::WILD_MAP_CELL_CITY,
				'march_id'             => 0,
				'npc_id'               => 0,
				'hold_expire_time'     => $expire,
			);
			$ret    = B_DB::instance('WildMap')->insert($fileds);
			$ret && self::setWildMapInfo($posNo, $fileds);
			$ret && self::syncWildMapBlockCache($posNo);
		}

		return $ret;
	}

	/**
	 * 更新野外地图信息
	 * @author huwei on 20111010
	 * @param int $zone 洲
	 * @param int $posX 坐标X
	 * @param int $posY 坐标Y
	 * @param array $fieldArr 要更新的字段
	 * @return array
	 */
	static public function setWildMapInfo($posNo, $fieldArr, $upDB = true) {
		$ret = false;
		if ($posNo > 0 && is_array($fieldArr) && !empty($fieldArr)) {
			$info = array();
			foreach ($fieldArr as $key => $val) {
				if (!empty($key) && in_array($key, T_DBField::$wildMapFields)) {
					$info[$key] = $val;
				}
			}

			if (!empty($info)) {
				$rc  = new B_Cache_RC(T_Key::WILD_MAP_INFO, $posNo);
				$ret = $rc->hmset($info, T_App::ONE_DAY);
				if ($ret) {
					$upDB && M_CacheToDB::addQueue(T_Key::WILD_MAP_INFO . ':' . $posNo);
				} else {
					Logger::error(array(__METHOD__, 'Err Update', func_get_args()));
				}
			}
		}
		//Logger::dev(json_encode(array(__METHOD__, func_get_args(), $ret)));
		return $ret ? $info : false;
	}

	/**
	 * 获取野外地图信息
	 * @author huwei on 20111010
	 * @param int $posNo 地图坐标点编号
	 * @param int $zone 洲
	 * @param int $posX 坐标X
	 * @param int $posY 坐标Y
	 * @return array
	 */
	static public function getWildMapInfo($posNo, $isDB = false) {
		$ret   = false;
		$posNo = intval($posNo);
		$now   = time();
		if ($posNo > 0) {
			$rc  = new B_Cache_RC(T_Key::WILD_MAP_INFO, $posNo);
			$ret = $rc->hgetall();
			//@todo ret=false
			//$ret = false;
			if (empty($ret['pos_no']) || $isDB) {
				$data = B_DB::instance('WildMap')->get($posNo);
				if (empty($data)) {
					//Logger::error(array(__METHOD__, $posNo, $data));
					$data = array(
						'pos_no'               => $posNo,
						'type'                 => T_Map::WILD_MAP_CELL_SPACE,
						'city_id'              => 0,
						'march_id'             => 0,
						'npc_id'               => 0,
						'weather'              => 0,
						'march_id'             => 0,
						'weather_refresh_time' => 0,
						'terrain'              => 0,
						'hold_expire_time'     => 0,
						'scene_type'           => 0,
						'last_fill_army_time'  => '',
					);
				}
				$bUp = $rc->hmset($data, T_App::ONE_MINUTE);
				$ret = $data;
			}
		}
		return $ret;
	}

	/**
	 * 地图数据校正
	 * @param array $mapInfo
	 * @return bool
	 */
	static public function fixWildMapHoldInfo($mapInfo) {
		$now = time();
		if (!empty($mapInfo['pos_no']) &&
			T_Map::WILD_MAP_CELL_NPC == $mapInfo['type'] &&
			$mapInfo['city_id'] > 0 &&
			$mapInfo['hold_expire_time'] > 0 &&
			$mapInfo['hold_expire_time'] < ($now + T_App::ONE_MINUTE)
		) {

			$objPlayer = new O_Player($mapInfo['city_id']);
			$delUp     = $objPlayer->ColonyNpc()->del($mapInfo['pos_no']);

			Logger::error(array(__METHOD__, 'Error WildMap Data[NPC]', $mapInfo));

			if ($mapInfo['march_id'] > 0) {
				M_March::setMarchBack($mapInfo['march_id']);
			}

			$fieldArr = array('city_id' => 0, 'hold_expire_time' => 0, 'march_id' => 0);
			M_MapWild::setWildMapInfo($mapInfo['pos_no'], $fieldArr);
			M_MapWild::syncWildMapBlockCache($mapInfo['pos_no']);
			return true;
		} else if (!empty($mapInfo['pos_no']) &&
			T_Map::WILD_MAP_CELL_CITY == $mapInfo['type'] &&
			$mapInfo['city_id'] == 0
		) {
			Logger::error(array(__METHOD__, 'Error WildMap Data[City]', $mapInfo));
			M_MapWild::cleanWildMapInfo($mapInfo['pos_no']);
			M_MapWild::syncWildMapBlockCache($mapInfo['pos_no']);
		}
		return false;
	}

	/**
	 * 删除野外地图信息
	 * @author huwei on 20111010
	 * @param int $posNo 城外坐标编号
	 * @return bool
	 */
	static public function delWildMapInfo($posNo) {
		$ret = self::cleanWildMapInfo($posNo);
		if ($ret) {
			B_DB::instance('WildMap')->delete($posNo);
		}

		return $ret;
	}

	static public function cleanWildMapInfo($posNo) {
		$rc  = new B_Cache_RC(T_Key::WILD_MAP_INFO, $posNo);
		$ret = $rc->delete();
		if (!$ret) {
			Logger::error(array(__METHOD__, 'Del Wildmap Fail', $posNo));
		}
		return $ret;
	}

	/**
	 * 重新生成区块的缓存
	 * @author huwei on 20111010
	 * @param int $areaNo
	 * @return bool
	 */
	static public function syncWildMapBlockCache($posNo) {
		$ret = false;
		if ($posNo > 0) {
			list($zone, $posX, $posY) = M_MapWild::calcWildMapPosXYByNo($posNo);
			$areaNo = self::calcWildMapAreaNoByPos($posX, $posY);
			$ret    = self::setWildMapAreaCache($zone, $areaNo);
		}
		return $ret;
	}

	static public function initWildMapCache($zone) {
		$ret = array();

		$maxAreaX = ceil(self::WILD_MAP_MAX_POS_X / self::WILD_MAP_SPLIT_AREA_X) + 1;
		$maxAreaY = ceil(self::WILD_MAP_MAX_POS_Y / self::WILD_MAP_SPLIT_AREA_Y) + 1;

		for ($a = 1; $a < $maxAreaX; $a++) {
			for ($b = 1; $b < $maxAreaY; $b++) {
				$areaNo       = self::_calcWildMapAreaNoByAreaXY($a, $b);
				$ret[$areaNo] = self::setWildMapAreaCache($zone, $areaNo);
				$s            = $ret[$areaNo] ? 'Y' : 'N';
				echo "NO#{$areaNo}XY#{$a}_{$b}:{$s}\n";
			}
		}
		echo count($ret);
		return $ret;
	}


	/**
	 * 生成区块的缓存通过编号
	 * @author huwei on 20111010
	 * @param int $areaNo
	 * @return string
	 */
	static public function setWildMapAreaCache($zone, $areaNo) {
		list($areaX, $areaY) = self::_calcWildMapAreaXYByAreaNo($areaNo);
		list($startPosX, $startPosY, $endPosX, $endPosY) = self::_getWildMapPosListByAreaXY($areaX, $areaY);
		$dataStr = '';
		$n       = 0;
		for ($x = $startPosX; $x < $endPosX; $x++) {
			for ($y = $startPosY; $y < $endPosY; $y++) {
				$posNo       = M_MapWild::calcWildMapPosNoByXY($zone, $x, $y);
				$binDataList = M_MapWild::getWildMapInfo($posNo);
				//Logger::error(array(__METHOD__, 'memery_error_init', func_get_args()));
				$info = self::_makeWildMapAreaBinData($binDataList);
				//Logger::error(array(__METHOD__, 'memery_error_end'));
				if (!empty($info)) {
					$dataStr .= $info;
				}
			}
		}
		$rc  = new B_Cache_RC(T_Key::WILD_MAP_AREA, $zone . ':' . $areaNo);
		$bUp = $rc->set($dataStr, T_App::ONE_WEEK);
		if (!$bUp) {
			Logger::error(array(__METHOD__, "setWildMapAreaCache Fail", $zone, $areaNo, $dataStr));
		}
		return $dataStr;
	}

	/**
	 * 通过坐标获取 地图缓存区块编号
	 * @author huwei
	 * @param int $posX
	 * @param int $posY
	 * @param int $areaXNum 横多少个区域
	 * @param int $areaYNum 竖多少个区域
	 * @return array (区域1,区域2,区域3,区域4)
	 */
	static private function _calcWildMapAreaList($posX, $posY, $areaXNum = 1, $areaYNum = 1) {
		$startAreaX = $startAreaY = 1;

		list($areaX, $areaY) = self::_calcWildMapAreaXYByPos($posX, $posY);
		$maxAreaX = ceil(self::WILD_MAP_MAX_POS_X / self::WILD_MAP_SPLIT_AREA_X);
		$maxAreaY = ceil(self::WILD_MAP_MAX_POS_Y / self::WILD_MAP_SPLIT_AREA_Y);

		$startAreaX = ($maxAreaX == $areaX) ? $areaX - 1 : $areaX;
		$startAreaY = ($maxAreaY == $areaY) ? $areaY - 1 : $areaY;

		$area = array();
		for ($i = 0; $i < $areaXNum; $i++) {
			for ($j = 0; $j < $areaYNum; $j++) {
				$area[] = self::_calcWildMapAreaNoByAreaXY($startAreaX + $i, $startAreaY + $j);
			}
		}

		return $area;
	}

	/**
	 * 获取野外地图缓存数据 通过 缓存编号
	 * @author huwei
	 * @param int $areaNo
	 * @param int $zone
	 */
	static private function _getWildMapAreaCacheByAreaNo($areaNo, $zone) {
		$rc  = new B_Cache_RC(T_Key::WILD_MAP_AREA, $zone . ':' . $areaNo);
		$ret = $rc->get();
		//@todo ret=false
		$ret = false;
		if ($ret === false) {
			$ret = M_MapWild::setWildMapAreaCache($zone, $areaNo);
		}
		return $ret;
	}

	/**
	 * 计算地图缓存区块的 起始坐标范围
	 * @author huwei on 20111010
	 * @param int $areaX
	 * @param int $areaY
	 * @return array (起点坐标X,起点坐标Y,结束坐标X,结束坐标Y)
	 */
	static private function _getWildMapPosListByAreaXY($areaX, $areaY) {
		$maxAreaX = ceil(self::WILD_MAP_MAX_POS_X / self::WILD_MAP_SPLIT_AREA_X);
		$maxAreaY = ceil(self::WILD_MAP_MAX_POS_Y / self::WILD_MAP_SPLIT_AREA_Y);

		$areaX = max(min($areaX, $maxAreaX), 0);
		$areaY = max(min($areaY, $maxAreaY), 0);

		$startPosX = $areaX * self::WILD_MAP_SPLIT_AREA_X;
		$startPosY = $areaY * self::WILD_MAP_SPLIT_AREA_Y;

		$endPosX = ($areaX + 1) * self::WILD_MAP_SPLIT_AREA_X;
		$endPosY = ($areaY + 1) * self::WILD_MAP_SPLIT_AREA_Y;

		return array($startPosX, $startPosY, $endPosX, $endPosY);
	}

	/**
	 * 计算野外坐标 做在的缓存区块编号
	 * @author huwei on 20111010
	 * @param int $posX
	 * @param int $posY
	 * @return int
	 */
	static public function calcWildMapAreaNoByPos($posX, $posY) {
		list($areaX, $areaY) = self::_calcWildMapAreaXYByPos($posX, $posY);

		return self::_calcWildMapAreaNoByAreaXY($areaX, $areaY);
	}

	/**
	 * 计算野外缓存区块编号通过区块坐标
	 * @author huwei on 20111010
	 * @param int $areaX
	 * @param int $areaY
	 * @return int
	 */
	static private function _calcWildMapAreaNoByAreaXY($areaX, $areaY) {
		return $areaX * 100 + $areaY;
	}

	/**
	 * 计算野外缓存区块坐标通过区块编号
	 * @author huwei on 20111010
	 * @param int $areaX
	 * @param int $areaY
	 * @return int
	 */
	static private function _calcWildMapAreaXYByAreaNo($areaNo) {
		$areaX = floor($areaNo / 100);
		$areaY = floor($areaNo % 100);
		return array($areaX, $areaY);
	}

	/**
	 * 计算野外坐标 做在的缓存区块坐标
	 * @author huwei on 20111010
	 * @param int $posX
	 * @param int $posY
	 * @return array [$areaX, $areaY]
	 */
	static private function _calcWildMapAreaXYByPos($posX, $posY) {
		$posX = max(min($posX, self::WILD_MAP_MAX_POS_X), 1);
		$posY = max(min($posY, self::WILD_MAP_MAX_POS_Y), 1);

		$areaX = floor($posX / self::WILD_MAP_SPLIT_AREA_X);
		$areaY = floor($posY / self::WILD_MAP_SPLIT_AREA_Y);
		return array($areaX, $areaY);
	}

	/**
	 * 计算野外地图编号
	 * @author huwei on 20111010
	 * @param int $posZ
	 * @param int $posX
	 * @param int $posY
	 * @return int
	 */
	static public function calcWildMapPosNoByXY($posZ, $posX, $posY) {
		return $posZ * 1000000 + $posX * 1000 + $posY;
	}

	/**
	 * 计算野外地图坐标
	 * @author huwei on 20111010
	 * @param int $posNo
	 * @return array(z,x,y)
	 */
	static public function calcWildMapPosXYByNo($posNo) {
		$ret = array(0, 0, 0);
		if (!empty($posNo)) {
			if (stristr($posNo, '_')) {
				$ret = explode('_', $posNo);
				array_unshift($ret, 0);
			} else if (strlen($posNo) >= 7) { //副本编号
				$posNo = intval($posNo);
				$z     = floor($posNo / 1000000);
				$tmp   = $posNo % 1000000;
				$x     = floor($tmp / 1000);
				$y     = floor($tmp % 1000);
				$ret   = array($z, $x, $y);
			} else {
				$ret = M_Formula::calcParseFBNo($posNo);
				array_unshift($ret, T_App::MAP_FB);
			}
		}

		return $ret;
	}

	/**
	 * 初始化未占用地图坐标数据
	 * 每个区块25个点 5*5
	 * 总共3600个区块
	 * @param int $zone 洲
	 * @param int $area 区块
	 */
	static public function initNoHoldMapPos($zone, $area) {
		if (!empty($zone) && !empty($area)) {
			list($startPosX, $startPosY, $endPosX, $endPosY) = self::calcNoHoldAreaXYMapAreaNo($area);

			$n    = 0;
			$list = array();
			$rc   = new B_Cache_RC(T_Key::WILD_MAP_NO_HOLD_POS, $zone . ':' . $area);

			for ($x = $startPosX; $x <= $endPosX; $x++) {
				for ($y = $startPosY; $y <= $endPosY; $y++) {
					$pos     = $x . '_' . $y;
					$posNo   = M_MapWild::calcWildMapPosNoByXY($zone, $x, $y);
					$mapInfo = M_MapWild::getWildMapInfo($posNo);
					if (empty($mapInfo['type'])) {
						//无城市信息 无地图数据
						$rc->sadd($pos);
						$list[] = $pos;
					}
				}
			}
			shuffle($list);
			return $list;
		}
		return false;
	}

	/**
	 * 获取未占用的地图坐标数据
	 * 每个区块25个点 5*5
	 * 总共3600个区块
	 * @param int $zone 洲
	 * @return int
	 */
	static public function getNoHoldMapPos($zone) {
		$n   = 0;
		$pos = false;
		$rc  = new B_Cache_RC(T_Key::WILD_MAP_NO_HOLD_AREA, $zone);
		while (!$pos) {
			$area = M_MapWild::getNoHoldAreaNo($zone);
			if ($area) {
				$pos = M_MapWild::getNoHoldPosXY($zone, $area);
				if ($pos) { //查询到坐标 则删除列表中的坐标

					$rc1 = new B_Cache_RC(T_Key::WILD_MAP_NO_HOLD_POS, $zone . ':' . $area);
					$rc1->srem($pos);
				} else { //如果没有坐标 删除区块列表中坐标
					$rc->srem($area);
				}
			} else { //无坐标 中断
				break;
			}

			if ($n > 3) { //超过删除不继续查询
				break;
			}
			$n++;
		}

		return $pos;
	}

	/**
	 * 获取未占用地图编号
	 * @param int $zone
	 * @return int
	 */
	static public function getNoHoldAreaNo($zone) {
		$ret = false;

		$rc = new B_Cache_RC(T_Key::WILD_MAP_NO_HOLD_AREA, $zone);
		if (!$rc->exists()) {
			$list = array();
			for ($area = 1; $area <= self::WILD_MAP_MAX_AREA; $area++) {
				$rc->sadd($area);
				$list[] = $area;
			}
			shuffle($list);
			$ret = $list[0];
		} else {
			$ret = $rc->srandmember();
		}
		return $ret;
	}

	/**
	 * 获取未占用的坐标
	 * @param int $zone
	 * @param int $area
	 */
	static public function getNoHoldPosXY($zone, $area) {
		$rc = new B_Cache_RC(T_Key::WILD_MAP_NO_HOLD_POS, $zone . ':' . $area);
		if (!$rc->exists()) {
			$list = M_MapWild::initNoHoldMapPos($zone, $area);
			$pos  = isset($list[0]) ? $list[0] : '';
		} else {
			$pos = $rc->srandmember();
		}
		return $pos;
	}

	/**
	 * 通过区块编号计算坐标起始范围 (1 - 900)
	 * 150*150 / 5*5 = 900
	 * @param int $area
	 * @return array
	 */
	static public function calcNoHoldAreaXYMapAreaNo($area) {
		$area      = max(1, $area);
		$area      = min($area, self::WILD_MAP_MAX_AREA);
		$splitArea = self::WILD_MAP_MAX_POS_X / self::WILD_MAP_SPLIT_AREA_X;

		$x         = ceil($area / $splitArea);
		$y         = max(ceil($area % $splitArea), 1);
		$startPosX = ($x - 1) * self::WILD_MAP_SPLIT_AREA_X + 1;
		$startPosY = ($y - 1) * self::WILD_MAP_SPLIT_AREA_X + 1;
		$endPosX   = $x * self::WILD_MAP_SPLIT_AREA_X;
		$endPosY   = $y * self::WILD_MAP_SPLIT_AREA_X;

		return array($startPosX, $startPosY, $endPosX, $endPosY);
	}

	static public function initWildNpc($z, $x, $y, $npcId, $npcNum) {
		$ret = B_DB::instance('WildMap')->clean($z, $npcId);

		$res = array();
		//分成几块
		$areaX = ceil(self::WILD_MAP_MAX_POS_X / $x);
		$areaY = ceil(self::WILD_MAP_MAX_POS_Y / $y);

		$areaArr = array();
		for ($i = 0; $i < $areaX; $i++) {
			for ($j = 0; $j < $areaY; $j++) {
				$areaArr[] = array($i, $j);
			}
		}

		foreach ($areaArr as $v) {
			$randArr = array();
			list($areaXNo, $areaYNo) = $v;
			$minX = $x * $areaXNo + 1;
			$maxX = $x * ($areaXNo + 1);
			$minY = $y * $areaYNo + 1;
			$maxY = $y * ($areaYNo + 1);

			for ($j = $minX; $j < $maxX; $j++) //循环当前区块内X坐标点
			{
				for ($k = $minY; $k < $maxY; $k++) //循环当前区块内Y坐标点
				{
					if ($j % 3 == 0 && $k % 4 == 0) {
						$posNo = M_MapWild::calcWildMapPosNoByXY($z, $j, $k);
						$row   = B_DB::instance('WildMap')->get($posNo);
						//Logger::debug($posNo);
						if (!empty($row['pos_no'])) {
							if ($row['type'] == T_Map::WILD_MAP_CELL_SPACE) {
								$randArr[] = $posNo;
							}
						} else {
							$randArr[] = $posNo;
						}
					}

				}
			}
			shuffle($randArr);
			//echo json_encode($randArr)."\n";
			$tmp = array();
			if (!empty($randArr)) {
				$tmp = (array)array_rand($randArr, $npcNum);
			}
			echo count($randArr) . "=>{$npcNum};";

			foreach ($tmp as $v) {
				$posNo = $randArr[$v];
				//Logger::debug($posNo);
				$data  = array(
					'pos_no' => $posNo,
					'type'   => T_Map::WILD_MAP_CELL_NPC,
					'npc_id' => $npcId,
				);
				$res[] = B_DB::instance('WildMap')->insert($data);
			}
		}
		return $res;
	}

	static public function getWildMapAreaList() {
		static $list = null;
		if ($list == null) {
			$apcKey = T_Key::BASE_MAP_AREA;
			$list   = B_Cache_APC::get($apcKey);
			if (empty($list)) {
				$maxAreaX = floor(M_MapWild::WILD_MAP_MAX_POS_X / M_MapWild::WILD_MAP_SPLIT_AREA_X);
				$maxAreaY = floor(M_MapWild::WILD_MAP_MAX_POS_Y / M_MapWild::WILD_MAP_SPLIT_AREA_Y);
				for ($areaX = 0; $areaX < $maxAreaX; $areaX++) {
					for ($areaY = 0; $areaY < $maxAreaY; $areaY++) {
						$no = self::_calcWildMapAreaNoByAreaXY($areaX, $areaY);
						list($startPosX, $startPosY, $endPosX, $endPosY) = self::_getWildMapPosListByAreaXY($areaX, $areaY);
						$posNoArr = array();
						for ($x = $startPosX; $x < $endPosX; $x++) {
							for ($y = $startPosY; $y < $endPosY; $y++) {
								if ($x > 0 && $y > 0) {
									$posNoArr[] = "{$x}_{$y}";
								}

							}
						}
						$list[$no] = $posNoArr;
					}
				}
				APC::set($apcKey, $list);
			}
		}

		return $list;
	}

	static public function getRndTerrainAndWeather($npcZone) {
		$terrain = T_App::TERRAIN_PLAIN;
		$weather = T_App::WEATHER_CLEAR;

		$baseConf = M_Config::getVal();
		if (isset($baseConf['map_zone_terrain'][$npcZone])) {
			//随机一个地形给城市
			$randKey = array_rand($baseConf['map_zone_terrain'][$npcZone]);
			$terrain = $baseConf['map_zone_terrain'][$npcZone][$randKey];
		}

		//获取当前战区可能的天气
		if (isset($baseConf['map_zone_weather'][$terrain])) {
			//随机一个地形给城市
			$randKey = array_rand($baseConf['map_zone_weather'][$terrain]);
			$weather = $baseConf['map_zone_weather'][$terrain][$randKey];
		}

		$refreshTime = time() + $baseConf['weather_refresh_interval'] * T_App::ONE_HOUR;

		return array($terrain, $weather, $refreshTime);
	}

}

?>
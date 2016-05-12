<?php

/**
 * 合服
 * @return array
 */
class M_Merge {
	static $InitPos = array();
	static $InitId = 0;
	static $TableKeys = array();
	static $Log = array();
	static $ServerId = 0;
	static $NewCityId = 100000;
	static $NewUnionId = 100000;
	static $NewHeroId = 100000;
	static $NewEquipId = 100000;
	static $moveUnionData = array();
	static $offset = 100;
	static $Total = 0;
	/** 玩家附加数据表 */
	static $TableArr = array('city_extra', 'city_colony', 'city_props', 'city_res', 'city_horse', 'city_question', 'city_qq_share', 'city_task', 'city_quest', 'city_breakout');

	/** 最后访问过期时间 */
	static $ExpireTime = 0;
	/** 玩家补偿道具ID */
	static $CompensatePropsIdCity = 198;
	/** 军团长补偿道具ID */
	static $CompensatePropsIdUnion = 2002;
	/** 48小时外玩家 给补偿 */
	static $CompensateLimitHour = 48;

	/**
	 * 合服导出数据
	 * @return array
	 */
	static public function exportData($initNum) {
		$serverId         = B_Cache_File::server(SERVER_NO);
		$offset           = 100000;
		self::$ServerId   = $serverId; //服务器ID
		self::$InitId     = $initNum; //初始计数ID
		self::$NewUnionId = $initNum * $offset; //联盟ID初始计数
		self::$NewHeroId  = $initNum * $offset * 10; //军官ID初始计数 不能超过100w
		self::$NewEquipId = $initNum * $offset * 10; //装备ID初始计数 不能超过100w
		self::$NewCityId  = $initNum * $offset; //城市ID初始计数

		self::$InitPos = self::makePos();

		$expireTime = time() - 86400 * 30;

		self::$ExpireTime = $expireTime;

		//查询条件
		$sqlWhere = "FROM city as c, user as u  WHERE ((u.`last_visit_time`> {$expireTime} and c.`mil_medal`>16) OR c.`total_mil_pay`>0  OR c.`mil_pay`>0)  AND u.id=c.user_id";

		$obj = new B_ORM('city', 'id');
		$sql = "SELECT count(c.id) as num {$sqlWhere}";
		$sth = $obj->query($sql);
		$row = $sth->fetch();

		self::$Total = $row['num'];
		$num         = ceil(self::$Total / self::$offset);
		$n           = $row['num'];

		$file        = RUN_PATH . '/' . self::$ServerId . '/merge/city_data_' . self::$ServerId . '_' . date('Ymd');
		$moveDataStr = '';
		for ($i = 1; $i <= $num; $i++) {
			$start    = self::$offset * ($i - 1);
			$limitStr = " LIMIT {$start}, " . self::$offset;

			$sql      = "SELECT c.* {$sqlWhere}";
			$limitSql = $sql . $limitStr;
			$sth      = $obj->query($limitSql);
			$rows     = $sth->fetchAll();
			foreach ($rows as $cityInfo) {
				self::$NewCityId++;
				$obj      = new B_ORM('user', 'id');
				$userInfo = $obj->fetch(array('id' => $cityInfo['user_id']));
				list($moveData, $log) = self::buildCityData($userInfo, $cityInfo, self::$NewCityId);

				//$moveDataStr .= json_encode($moveData)."\n";
				$log['table'] = count($moveData);
				$logStr       = json_encode($log);
				echo "[{$n}] {$cityInfo['id']}=>" . self::$NewCityId . " : (" . $logStr . ")\n";
				$n--;

				self::writeLog(self::$NewCityId . '_' . $userInfo['username_ext'] . '_' . $cityInfo['id']);

			}
			//error_log($moveDataStr, 3, $file);

		}

		echo "总数[" . self::$Total . "]\n";
	}

	/**
	 * 构建玩家数据记录
	 * @return array
	 */
	static public function buildCityData($userInfo, $cityInfo, $newCityId) {
		$oldCityId = $cityInfo['id'];
		$moveData  = self::cityData($oldCityId, $newCityId);

		$newUnionId = self::unionData($cityInfo['union_id'], $oldCityId, $newCityId);

		$pos  = strval($cityInfo['pos_no']);
		$zone = $pos{0};
		list($posX, $posY) = array_pop(self::$InitPos[$zone]);

		$posNo = M_MapWild::calcWildMapPosNoByXY($zone, $posX, $posY); //新坐标

		$cityInfo['pos_no']    = $posNo;
		$cityInfo['id']        = $newCityId;
		$cityInfo['user_id']   = $newCityId;
		$cityInfo['nickname']  = $cityInfo['nickname'] . self::$InitId;
		$cityInfo['signature'] = str_replace(array('\'', '\\'), array('', ''), $cityInfo['signature']);
		$cityInfo['union_id']  = $newUnionId;

		$moveData['city']    = $cityInfo;
		self::$Log['city'][] = self::buildInsertLog('city', $cityInfo);

		$moveData['wild_map'] = self::wildMap($posNo, $newCityId);

		$userInfo['id']      = $newCityId;
		$moveData['user']    = $userInfo;
		self::$Log['user'][] = self::buildInsertLog('user', $userInfo);

		$moveData['city_item'] = self::cityItem($oldCityId, $newCityId, $cityInfo['created_at']);

		$moveData['auction']           = self::cityAuction($oldCityId, $newCityId);
		$moveData['city_hero']         = self::cityHero($oldCityId, $newCityId);
		$moveData['city_equip']        = self::cityEquip($oldCityId, $newCityId);
		$moveData['stats_log_pay']     = self::cityStatsPay($userInfo['username'], $newCityId);
		$moveData['stats_log_expense'] = self::cityStatsExpense($oldCityId, $newCityId);
		$moveData['stats_log_income']  = self::cityStatsIncome($oldCityId, $newCityId);

		$log['hero']     = count($moveData['city_hero']);
		$log['equip']    = count($moveData['city_equip']);
		$log['item']     = count($moveData['city_item']);
		$log['auction']  = count($moveData['auction']);
		$log['union_id'] = $newUnionId;
		$log['pay']      = count($moveData['stats_log_pay']);
		$log['expense']  = count($moveData['stats_log_expense']);
		$log['income']   = count($moveData['stats_log_income']);

		return array($moveData, $log);
	}

	/**
	 * 联盟记录
	 * @return array
	 */
	static public function unionData($oldUnionId, $oldCityId, $newCityId) {
		$newUnionId = 0;
		if ($oldUnionId > 0) {
			if (!isset(self::$moveUnionData[$oldUnionId])) {
				self::$NewUnionId++;
				$obj          = new B_ORM('union', 'id');
				$tmpUnionInfo = $obj->fetch(array('id' => $oldUnionId));

				$tmpUnionInfo['name']             = str_replace(array('\'', '\\', '"'), array('', '', ''), $tmpUnionInfo['name']);
				$tmpUnionInfo['id']               = self::$NewUnionId;
				self::$moveUnionData[$oldUnionId] = $tmpUnionInfo['id'];


				$tmpUnionInfo['name'] = $tmpUnionInfo['name'] . self::$InitId;
				self::$Log['union'][] = self::buildInsertLog('union', $tmpUnionInfo);
			}

			if (!empty(self::$moveUnionData[$oldUnionId])) {
				$newUnionId         = self::$moveUnionData[$oldUnionId];
				$obj                = new B_ORM('union_member', 'id');
				$tmpUnionMemberInfo = $obj->fetch(array('city_id' => $oldCityId, 'flag' => 1));
				if (!empty($tmpUnionMemberInfo['id'])) {
					unset($tmpUnionMemberInfo['id']);
					$tmpUnionMemberInfo['city_id']  = $newCityId;
					$tmpUnionMemberInfo['union_id'] = $newUnionId;
					//self::$moveUnionData['member'][$oldUnionId][] = $tmpUnionMemberInfo;

					if ($tmpUnionMemberInfo['position'] == M_Union::UNION_MEMBER_TOP) {
						self::newPropsId($newCityId, self::$CompensatePropsIdUnion);
					}

					self::$Log['union_member'][] = self::buildInsertLog('union_member', $tmpUnionMemberInfo);

					//$s = json_encode($tmpUnionMemberInfo);
					//$s = '';
					//echo "union: {$newCityId} {$oldUnionId}:".$s."\n";
				}
			}
		}
		return $newUnionId;
	}

	/**
	 * 玩家城市附加记录
	 * @return array
	 */
	static public function cityData($oldCityId, $newCityId) {
		$moveData = array();

		foreach (self::$TableArr as $tableName) {
			$obj  = new B_ORM($tableName, 'city_id');
			$info = $obj->fetch(array('city_id' => $oldCityId));
			if (!empty($info['city_id'])) {
				if ($tableName == 'city_quest') {
					$info['ing_content'] = isset($info['ing_content']) ? $info['ing_content'] : '[]';
					$info['daily_date']  = isset($info['daily_date']) ? $info['daily_date'] : date('Ymd');
					$info['daily_text']  = isset($info['daily_text']) ? $info['daily_text'] : '';
				} else if ($tableName == 'city_colony') {
					$colonyCity = '';
					if (!empty($info['city_id'])) {
						$tmpArr = json_decode($info['colony_city'], true);
						if ($tmpArr) {
							$t = array();
							foreach ($tmpArr as $key => $val) {
								$t[$key] = array($val[0], 0, 0, 0);
							}
							if (!empty($t)) {
								$colonyCity = json_encode($t);
							}
						}
					}

					$info['rescue_date']  = 0;
					$info['atk_city_id']  = 0;
					$info['atk_march_id'] = 0;
					$info['hold_time']    = 0;
					$info['colony_city']  = $colonyCity;
				} else if ($tableName == 'city_extra') {
					$tmpTechList = json_decode($info['tech_list'], true);
					$tmp         = array();
					for ($t = 1; $t <= 20; $t++) {
						$tmp[$t] = $tmpTechList[$t];
					}
					$info['tech_list'] = json_encode($tmp);

					$info['pos_collect'] = json_encode(array());

					$tmpExtraArr = json_decode($info['wild_city'], true);
					if ($tmpExtraArr) {
						$t = array();
						foreach ($tmpExtraArr as $key => $val) {
							$t[$key] = array($val[0], 0, 0, 0, 0, 0);
						}
						if (!empty($t)) {
							$info['wild_city'] = json_encode($t);
						}
					}
				}

				$info['city_id'] = $newCityId;

				self::$Log[$tableName][] = self::buildInsertLog($tableName, $info);

				$moveData[$tableName] = $info;
			}
		}
		return $moveData;
	}

	/**
	 * 玩家军官记录
	 * @return array
	 */
	static public function cityHero($oldCityId, $newCityId) {
		$moveHero     = array();
		$obj          = new B_ORM('city_hero', 'id');
		$cityHeroList = $obj->fetchAll(array('city_id' => $oldCityId));

		if ($cityHeroList) {
			foreach ($cityHeroList as $info) {
				self::$NewHeroId++;
				$info['id']               = self::$NewHeroId;
				$tmpHero                  = self::cleanHero($info, $newCityId);
				self::$Log['city_hero'][] = self::buildInsertLog('city_hero', $tmpHero);
				$moveHero[]               = $tmpHero;
			}
		}
		return $moveHero;
	}

	/**
	 * 玩家装备记录
	 * @return array
	 */
	static public function cityEquip($oldCityId, $newCityId) {
		$moveEquip = array();
		/* city_equip表迁移 */
		$obj           = new B_ORM('city_equip', 'id');
		$cityEquipList = $obj->fetchAll(array('city_id' => $oldCityId));
		if ($cityEquipList) {
			foreach ($cityEquipList as $info) {
				self::$NewEquipId++;
				$info['id']                = self::$NewEquipId;
				$tmpEquip                  = self::cleanEquip($info, $newCityId);
				self::$Log['city_equip'][] = self::buildInsertLog('city_equip', $tmpEquip);
				$moveEquip[]               = $tmpEquip;
			}
		}
		return $moveEquip;
	}

	/**
	 * 玩家拍卖记录
	 * @return array
	 */
	static public function cityAuction($oldCityId, $newCityId) {
		$moveAuction = array();
		/* auction表迁移 */

		$obj1            = new B_ORM('auction', 'id');
		$cityAucListSale = $obj1->fetchAll(array('sale_city_id' => $oldCityId, 'auction_status' => M_Auction::STATUS_FAIL));

		if (!empty($cityAucListSale) && is_array($cityAucListSale)) {
			foreach ($cityAucListSale as $info) {
				$aucId = $info['id'];
				unset($info['id']);
				$info['sale_city_id'] = $newCityId;
				$info['buy_city_id']  = 0;

				$goodsId = $info['goods_id'];
				$tmpInfo = array();
				if ($info['goods_type'] == M_Auction::GOODS_HERO) {
					$obj     = new B_ORM('city_hero', 'id');
					$tmpInfo = $obj->fetch(array('id' => $info['goods_id']));

					$tmpInfo = self::cleanHero($tmpInfo, 0);

					self::$NewHeroId++;
					$tmpInfo['id']            = self::$NewHeroId;
					self::$Log['city_hero'][] = self::buildInsertLog('city_hero', $tmpInfo);
					$goodsId                  = $tmpInfo['id'];
					//var_dump($goodsId);
					//echo "\n";
				} else if ($info['goods_type'] == M_Auction::GOODS_EQUI) {
					$obj     = new B_ORM('city_equip', 'id');
					$tmpInfo = $obj->fetch(array('id' => $info['goods_id']));
					self::$NewEquipId++;
					$tmpInfo['id']             = self::$NewEquipId;
					self::$Log['city_equip'][] = self::buildInsertLog('city_equip', $tmpInfo);
					$goodsId                   = $tmpInfo['id'];
				}

				$info['goods_id'] = $goodsId;
				//var_dump($info);
				//echo "\n";
				self::$Log['auction'][] = self::buildInsertLog('auction', $info);

			}
		}

		$cityAucListBuy = $obj1->fetchAll(array('buy_city_id' => $oldCityId, 'auction_status' => M_Auction::STATUS_SUCC));
		if (!empty($cityAucListBuy) && is_array($cityAucListBuy)) {
			foreach ($cityAucListBuy as $info) {
				$aucId = $info['id'];
				unset($info['id']);
				$info['sale_city_id'] = 0;
				$info['buy_city_id']  = $newCityId;
				$goodsId              = $info['goods_id'];
				$tmpInfo              = array();
				if ($info['goods_type'] == M_Auction::GOODS_HERO) {
					$obj     = new B_ORM('city_hero', 'id');
					$tmpInfo = $obj->fetch(array('id' => $info['goods_id']));
					$tmpInfo = self::cleanHero($tmpInfo, 0);

					self::$NewHeroId++;
					$tmpInfo['id']            = self::$NewHeroId;
					self::$Log['city_hero'][] = self::buildInsertLog('city_hero', $tmpInfo);
					$goodsId                  = $tmpInfo['id'];
				} else if ($info['goods_type'] == M_Auction::GOODS_EQUI) {
					$obj     = new B_ORM('city_equip', 'id');
					$tmpInfo = $obj->fetch(array('id' => $info['goods_id']));
					self::$NewEquipId++;
					$tmpInfo['id']             = self::$NewEquipId;
					self::$Log['city_equip'][] = self::buildInsertLog('city_equip', $tmpInfo);
					$goodsId                   = $tmpInfo['id'];
				}
				$info['goods_id']       = $goodsId;
				self::$Log['auction'][] = self::buildInsertLog('auction', $info);
			}
		}
		return $moveAuction;
	}

	/**
	 * 玩家物品记录
	 * @return array
	 */
	static public function cityItem($oldCityId, $newCityId, $cityCreateTime = 0) {
		$moveItem = array();
		/* city_equip表迁移 */
		$obj          = new B_ORM('city_item', 'id');
		$cityItemList = $obj->fetchAll(array('city_id' => $oldCityId));
		if ($cityItemList) {
			foreach ($cityItemList as $info) {
				unset($info['id']);
				$info['city_id']          = $newCityId;
				self::$Log['city_item'][] = self::buildInsertLog('city_item', $info);
				$moveItem[]               = $info;
			}

			//48小时外玩家 给补偿
			if (self::$CompensatePropsIdCity > 0 &&
				$cityCreateTime < (time() - self::$CompensateLimitHour * T_App::ONE_HOUR)
			) {
				$moveItem[] = self::newPropsId($newCityId, self::$CompensatePropsIdCity);
			}

		}
		return $moveItem;
	}

	/**
	 * 玩家充值记录
	 * @return array
	 */
	static public function cityStatsPay($username, $newCityId) {
		$rows = array();
		$sql  = "SELECT * FROM `stats_log_pay` where `username`='{$username}'";
		$sth  = B_DBStats::getStatsDB()->prepare($sql);
		$ret  = $sth->execute();
		$list = $sth->fetchAll();
		foreach ($list as $val) {
			unset($val['id']);
			$val['city_id']               = $newCityId;
			self::$Log['stats_log_pay'][] = self::buildInsertLog('stats_log_pay', $val);
			$rows[]                       = $val;
		}
		return $rows;
	}

	/**
	 * 玩家收入记录
	 * @return array
	 */
	static public function cityStatsIncome($oldCityId, $newCityId) {
		$rows       = array();
		$expireTime = self::$ExpireTime;
		$sql        = "SELECT * FROM `stats_log_income` where `city_id`='{$oldCityId}' and `create_at` > {$expireTime}";
		$sth        = B_DBStats::getStatsDB()->prepare($sql);
		$ret        = $sth->execute();
		$list       = $sth->fetchAll();
		foreach ($list as $val) {
			unset($val['id']);
			$val['city_id']                  = $newCityId;
			self::$Log['stats_log_income'][] = self::buildInsertLog('stats_log_income', $val);
			$rows[]                          = $val;
		}
		return $rows;
	}

	/**
	 * 玩家消费记录
	 * @return array
	 */
	static public function cityStatsExpense($oldCityId, $newCityId) {
		$rows       = array();
		$expireTime = self::$ExpireTime;
		$sql        = "SELECT * FROM `stats_log_expense` where `city_id`='{$oldCityId}' and `create_at` > {$expireTime}";
		$sth        = B_DBStats::getStatsDB()->prepare($sql);
		$ret        = $sth->execute();
		$list       = $sth->fetchAll();
		foreach ($list as $val) {
			unset($val['id']);
			$val['city_id']                   = $newCityId;
			self::$Log['stats_log_expense'][] = self::buildInsertLog('stats_log_expense', $val);
			$rows[]                           = $val;
		}
		return $rows;

	}

	/**
	 * 军官装备属性
	 * @return array
	 */
	static public function cleanHero($info, $newCityId) {
		$arr = array_flip(T_DBField::$cityHeroFields);
		foreach ($info as $field => $val) {
			if (!isset($arr[$field])) {
				unset($info[$field]);
			}
		}

		if (is_null($info['equip_exp'])) {
			$info['equip_exp'] = 0;
		}

		$info['city_id']       = $newCityId;
		$info['equip_arm']     = 0;
		$info['equip_cap']     = 0;
		$info['equip_uniform'] = 0;
		$info['equip_medal']   = 0;
		$info['equip_shoes']   = 0;
		$info['equip_sit']     = 0;
		$info['flag']          = 0;
		return $info;
	}

	/**
	 * 清除装备属性
	 * @return array
	 */
	static public function cleanEquip($info, $newCityId) {
		$info['city_id'] = $newCityId;
		$info['is_use']  = 0;
		return $info;
	}

	/**
	 * 构建SQL插入语句
	 * @return void
	 */
	static public function buildInsertLog($tableName, $info) {
		if (!isset(self::$TableKeys[$tableName])) {
			self::$TableKeys[$tableName] = "(`" . implode('`, `', array_keys($info)) . "`)";
		}

		return "('" . implode('\',\'', array_values($info)) . "')";
	}

	/**
	 * 写日志到文件
	 * @return void
	 */
	static public function writeLog($str) {
		$dir = RUN_PATH . '/' . self::$ServerId . '/merge/sql/' . self::$ServerId . "_ww2";

		if (!file_exists($dir)) {
			@mkdir($dir, 777, true);
		}
		foreach (self::$Log as $tableName => $vals) {
			if (stristr($tableName, 'stats')) {
				$file = $dir . "_stats.{$tableName}.sql";
			} else if ($tableName == 'union') {
				$file = $dir . ".union.sql";
			} else {

				$file = $dir . "/" . $str . '.sql';
			}

			$tN = count($vals);

			$offset = 1;

			$num   = ceil($tN / $offset);
			$field = self::$TableKeys[$tableName];

			for ($i = 1; $i <= $num; $i++) {
				$start   = $offset * ($i - 1);
				$newVals = array_slice($vals, $start, $offset);
				$sqlVal  = implode(",", $newVals);
				$sqlStr  = "INSERT INTO `{$tableName}` {$field} VALUES {$sqlVal};\n";
				error_log($sqlStr, 3, $file);
			}

		}
		self::$Log = array();
	}

	/**
	 * 按区生成坐标位置
	 * 最大合服数量 10
	 * 150/10*150 = 2250*3个洲 = 6750玩家 单区最大玩家数量
	 * @return array
	 */
	static public function makePos() {
		//最大合服数量 10个区  区分坐标段

		$xArr = array();
		for ($n = 0; $n < 15; $n++) {
			$xArr[] = $n * 10 + self::$InitId;
		}

		$posArr = array();
		foreach ($xArr as $x) {
			for ($y = 1; $y < M_MapWild::WILD_MAP_MAX_POS_Y; $y++) {
				$posArr[] = array($x, $y);
			}
		}

		$ret = array();
		foreach (T_App::$map as $type => $name) {
			shuffle($posArr);
			$ret[$type] = $posArr;

		}
		return $ret;
	}

	/**
	 * 构建野外地图记录
	 * @return array
	 */
	static public function wildMap($posNo, $newCityId) {
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

		$info                    = array(
			'pos_no'               => $posNo,
			'city_id'              => $newCityId,
			'terrain'              => $terrain,
			'weather'              => $weather,
			'weather_refresh_time' => $refreshTime,
			'type'                 => T_Map::WILD_MAP_CELL_CITY,
			'march_id'             => 0,
			'npc_id'               => 0,
			'hold_expire_time'     => 0,
		);
		self::$Log['wild_map'][] = self::buildInsertLog('wild_map', $info);
		return $info;
	}

	/**
	 * 新道具
	 *
	 */
	static public function newPropsId($newCityId, $propsId) {
		$info                     = array();
		$info['city_id']          = $newCityId;
		$info['props_id']         = $propsId;
		$info['type']             = 1;
		$info['num']              = 1;
		$info['locked']           = 0;
		$info['create_at']        = time();
		self::$Log['city_item'][] = self::buildInsertLog('city_item', $info);
		return $info;
	}

	/**
	 * 合服后校验数据
	 *
	 */
	static public function verfiyData() {
		$s = microtime(true);
		self::verfiyCityData();
		echo "verfiyCityData ok\n";
		self::verfiyUnionData();
		echo "verfiyUnionData ok\n";
		$e = microtime(true);
		echo "cost time:" . ($e - $s) . "s\n";
	}


	/**
	 * 校验城市数据
	 */
	static public function verfiyCityData() {

		$obj   = new B_ORM('city', 'id');
		$sql   = "SELECT count(id) as num FROM city";
		$sth   = $obj->query($sql);
		$row   = $sth->fetch();
		$total = isset($row['num']) ? $row['num'] : 0;

		$offset      = 100;
		$num         = ceil($total / $offset);
		$n           = 1;
		$moveDataStr = '';

		$succ = 0;
		for ($i = 1; $i <= $num; $i++) {
			$start = $offset * ($i - 1);
			$sql   = "SELECT * FROM city LIMIT {$start}, " . self::$offset;
			$sth   = $obj->query($sql);
			$rows  = $sth->fetchAll();
			foreach ($rows as $cityInfo) {
				$setArr = array(
					'id'       => $cityInfo['id'],
					'nickname' => mb_substr($cityInfo['nickname'], 0, mb_strlen($cityInfo['nickname']) - 1),
				);

				$ret = $obj->update($setArr);

				if (!$ret) {
					echo "city nickname update {$cityInfo['id']}: {$cityInfo['nickname']} => {$setArr['nickname']} fail\n";
				} else {
					$succ++;
				}
			}
		}
		echo "city nickname update succ:{$succ}\n";

	}

	/**
	 * 校验联盟数据
	 */
	static public function verfiyUnionData() {
		$unionObj  = new B_ORM('union', 'id');
		$unionList = $unionObj->fetchAll();
		$succ      = 0;
		foreach ($unionList as $unionInfo) {
			$unionId = $unionInfo['id'];
			//查找军团长
			$pos  = M_Union::UNION_MEMBER_TOP;
			$obj  = new B_ORM('union_member', 'id');
			$sql  = "SELECT id, city_id FROM `union_member` WHERE union_id ='{$unionId}' and position = '{$pos}'";
			$sth  = $obj->query($sql);
			$rows = $sth->fetch();
			if (!empty($rows['city_id'])) {
				$cityId = $rows['city_id'];
			} else {

				$sql    = "SELECT id, city_id FROM `union_member` WHERE union_id ='{$unionId}' order by point desc";
				$sth    = $obj->query($sql);
				$row    = $sth->fetch();
				$setArr = array(
					'id'       => $row['id'],
					'position' => M_Union::UNION_MEMBER_TOP,
					'union_id' => $unionId,
					'city_id'  => $cityId,
				);

				$obj->update($setArr);

				$cityId = $row['city_id'];
			}

			if ($cityId) {
				$obj      = new B_ORM('city', 'id');
				$cityInfo = $obj->fetch(array('id' => $cityId));
				$setArr   = array(
					'id'               => $unionId,
					'create_nick_name' => $cityInfo['nickname'],
					'create_city_id'   => $cityId,
					'boss'             => $cityInfo['nickname'],
				);
				$unionObj->update($setArr);

				//分开目的  联盟名称会有可能重复  更新失败
				$setArr = array(
					'id'   => $unionId,
					'name' => mb_substr($unionInfo['name'], 0, mb_strlen($unionInfo['name']) - 1),
				);
				$ret    = $unionObj->update($setArr);

				if (!$ret) {
					echo "union name update {$unionId}: {$unionInfo['name']} => {$setArr['name']} fail\n";
				}
				$succ++;
			}

			$rc1 = new B_Cache_RC(T_Key::UNION_INFO, $unionId);
			$rc1->delete();
			$rc2 = new B_Cache_RC(T_Key::UNION_MEMBER_LIST, $unionId);
			$rc2->delete();
		}
		echo "union name update succ:{$succ}\n";
	}
}

?>
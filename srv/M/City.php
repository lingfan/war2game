<?php

/**
 * 城市模型层
 */
class M_City {
	/** 有建筑 但等级不够 */
	const BUILD_NO_LEVEL = 1;
	/** 无所需建筑 */
	const NO_BUILD = 2;
	/** 有科技 但等级不够 */
	const TECH_NO_LEVEL = 3;
	/** 无所需科技 */
	const NO_TECH = 4;
	/** 建筑 科技条件都满足 */
	const BUILD_TECH_OK = 5;

	/** 市场买入交易系数 */
	const MARKET_BUY_RATE = 2;
	/** 市场卖出交易系数 */
	const MARKET_SALE_RATE = 1.2;
	/** 市场交易限额系数 */
	const TRADE_QUOTA_RATE = 1000;
	/** 购买一定量粮食任务的最少粮食值 */
	const TASK_TRADE_FOOD_MIN = 100;

	/** 建筑相关CD时间 */
	const CD_BUILD = 1;
	/** 科技相关CD时间 */
	const CD_TECH = 2;
	/** 武器相关CD时间 */
	const CD_WEAPON = 3;
	/** 副本相关CD时间 */
	const CD_FB = 4;
	/** 解救相关CD时间 */
	const CD_RESCUE = 5;
	/** 突围快速CD时间 */
	const CD_BOUT = 6;
	/** 爬楼快速CD时间 */
	const CD_FLOOR = 7;

	/** CD时间类型数组 */
	static $cdTimeType = array(
		self::CD_BUILD  => '建筑CD时间',
		self::CD_TECH   => '科技CD时间',
		self::CD_WEAPON => '武器CD时间',
		self::CD_FB     => '副本CD时间',
		self::CD_RESCUE => '解救CD时间',
		self::CD_BOUT   => '突围CD时间',
		self::CD_FLOOR  => '爬楼CD时间',
	);
	/** 个性签名最大长度 500英文字母或250汉字 */
	const MAX_SIGN_LENGTH = 500;
	/** 收藏目标名称最大长度 14英文字母或7汉字 */
	const MAX_COLLNAME_LENGTH = 14;
	/** 收藏坐标最大数量 */
	const MAX_COLL_SUM = 20;

	/** 建筑CD单个队列最大累计时间(秒) 4小时 */
	const CD_BUILD_ADDUP_MAX = 14400;
	/** 科技CD单个队列最大累计时间(秒) 4小时 */
	const CD_TECH_ADDUP_MAX = 14400;
	/** 武器CD单个队列最大累计时间(秒) 4小时 */
	const CD_WEAPON_ADDUP_MAX = 14400;
	/** 副本CD单个队列最大累计时间(秒) 30分钟 */
	const CD_FB_ADDUP_MAX = 1800;
	/** 突围CD单个队列最大累计时间(秒) 30分钟 */
	const CD_BOUT_ADDUP_MAX = 1800;
	/** 爬楼CD单个队列最大累计时间(秒) 30分钟 */
	const CD_FLOOR_ADDUP_MAX = 1800;

	/** 初始化城市幸运池点数 */
	const INIT_LUCK = 50;

	/** 迁城冷却时间 24小时 */
	const CD_MOVECITY_HOUR = 24;

	/** 不是新手保护  **/
	const NEWBIE_GUARD_NOT = 1;
	/** 是新手保护 **/
	const NEWBIE_GUARD_YES = 0;

	/** 洲[区域]兵种属性加成[百分比值] */
	static $zone_army_add = array(
		T_App::MAP_ASIA   => array(
			M_Army::ID_FOOT  => array('A' => 10, 'D' => 20, 'L' => 10),
			M_Army::ID_GUN   => array('A' => 10, 'D' => 20, 'L' => 10),
			M_Army::ID_ARMOR => array('A' => 0, 'D' => 10, 'L' => 0),
			M_Army::ID_AIR   => array('A' => 0, 'D' => 10, 'L' => 0),
		),
		T_App::MAP_EUROPE => array(
			M_Army::ID_FOOT  => array('A' => 10, 'D' => 0, 'L' => 0),
			M_Army::ID_GUN   => array('A' => 10, 'D' => 0, 'L' => 0),
			M_Army::ID_ARMOR => array('A' => 20, 'D' => 10, 'L' => 10),
			M_Army::ID_AIR   => array('A' => 15, 'D' => 5, 'L' => 5),
		),
		T_App::MAP_AFRICA => array(
			M_Army::ID_FOOT  => array('A' => 0, 'D' => 0, 'L' => 10),
			M_Army::ID_GUN   => array('A' => 0, 'D' => 0, 'L' => 10),
			M_Army::ID_ARMOR => array('A' => 5, 'D' => 5, 'L' => 15),
			M_Army::ID_AIR   => array('A' => 10, 'D' => 10, 'L' => 20),
		),
	);

	/** 洲[区域]资源产量加成[百分比值] */
	static $zone_res_add = array(
		T_App::MAP_ASIA   => array('gold_grow' => 0, 'food_grow' => 10, 'oil_grow' => 0),
		T_App::MAP_EUROPE => array('gold_grow' => 10, 'food_grow' => 0, 'oil_grow' => 0),
		T_App::MAP_AFRICA => array('gold_grow' => 0, 'food_grow' => 0, 'oil_grow' => 10),
	);


	/**
	 * 根据城市昵称获取城市ID
	 * @author Hejunyun
	 * @param int $nickName 城市昵称
	 * @return int/bool
	 */
	static public function getCityIdByNickName($nickName) {
		$ret = false;
		if (!empty($nickName)) {
			$rc     = new B_Cache_RC(T_Key::CITY_NICKNAME_TO_CITYID, md5($nickName));
			$cityId = $rc->get();
			if (empty($cityId)) {
				$cityId = B_DB::instance('City')->getCityIdCityName($nickName);
			}

			if (intval($cityId) > 0) {
				$rc->set($cityId, T_App::ONE_MINUTE);
				$ret = $cityId;
			}
		}

		return $ret;
	}

	/**
	 * 更新城市呢称获取城市ID的缓存
	 * @author huwei
	 * @param string $oldNickname
	 * @param string $newNickname
	 * @param int $cityId
	 */
	static public function upCityIdByNickName($oldNickname, $newNickname, $cityId) {
		$rc1 = new B_Cache_RC(T_Key::CITY_NICKNAME_TO_CITYID, md5($oldNickname));
		$rc1->set(0, T_App::ONE_DAY);

		$rc2 = new B_Cache_RC(T_Key::CITY_NICKNAME_TO_CITYID, md5($newNickname));
		$rc2->set($cityId, T_App::ONE_DAY);
	}

	/**
	 * 获取城市信息
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @return array 1D
	 */
	static public function getInfo($cityId, $sync = false) {
		$ret = false;
		if (!empty($cityId)) {
			$rc  = new B_Cache_RC(T_Key::CITY_INFO, $cityId);
			$ret = $rc->hmget(T_DBField::$cityInfoFields);
			//$ret = null;	//测试用
			if (empty($ret['id'])) {
				$cityInfo = B_DB::instance('City')->get($cityId);
				if (!empty($cityInfo)) {
					$ret = self::_setCityInfo($cityId, $cityInfo);
				}

			}
		}
		return $ret;
	}


	/**
	 * 检测建筑冷却时间是否结束
	 * @author huwei
	 * @param string $cd_build json格式的字符串[1,2,3]
	 * @param int $now 当前时间戳
	 * @return string
	 */
	static public function calcCDBuild($cd_build, $now) {
		$cdBuildArr = array();
		if (!empty($cd_build)) {
			$cdBuildArr = json_decode($cd_build, true);
			if (!empty($cdBuildArr)) {
				foreach ($cdBuildArr as $key => $val) {
					$val  = is_numeric($val) ? $val . '_' . '1' : $val; //@todo 容错处理
					$arrT = explode('_', $val);
					if ($now >= $arrT[0]) {
						unset($cdBuildArr[$key]);
					}
				}
			}
		}
		return json_encode($cdBuildArr);
	}

	/**
	 * 判断此城市是否满足特定的建筑/科技需求
	 * @author chenhui on 20110407
	 * @param int cityId 城市ID
	 * @param array $arr_need_build //解析建筑需求为数组
	 * @param array $arr_need_tech //解析科技需求为数组
	 * @return int 返回整型值标识相应结果
	 */
	static public function checkBuildTech($cityId, $arr_need_build = array(), $arr_need_tech = array()) {
		$cityextrainfo  = M_Extra::getInfo($cityId); //玩家城市额外信息
		$arr_tech_list  = json_decode($cityextrainfo['tech_list'], true); //已有科技信息数组
		$arr_build_list = json_decode($cityextrainfo['build_list'], true); //已有建筑信息数组
		//建筑条件判断
		if (!empty($arr_need_build) && is_array($arr_need_build)) {
			foreach ($arr_need_build as $needbid => $needblev) {
				if (isset($arr_build_list[$needbid])) {
					$flag1 = false;
					foreach ($arr_build_list[$needbid] as $pos => $lev) {
						if ($needblev <= $lev) {
							$flag1 = true; //可能有可多建建筑，其中只要有一个符合要求即可
						}
					}
					if (!$flag1) {
						return M_City::BUILD_NO_LEVEL; //有建筑 但等级不够
					}
				} else {
					return M_City::NO_BUILD; //无所需建筑
				}
			}
		}

		//科技条件判断
		if (is_array($arr_need_tech) && count($arr_need_tech) > 0) {
			foreach ($arr_need_tech as $needtid => $needtlev) {
				if (isset($arr_tech_list[$needtid])) {
					if ($needtlev > intval($arr_tech_list[$needtid])) {
						return M_City::TECH_NO_LEVEL; //有科技 但等级不够
					}
				} else {
					return M_City::NO_TECH; //无所需科技
				}
			}
		}
		return M_City::BUILD_TECH_OK; //建筑 科技条件都满足
	}

	/**
	 * 添加城市数据
	 * @author huwei
	 * @param int $userId 用户ID
	 * @param string $cityName 城市名称
	 * @param int $posX 位置X
	 * @param int $posY 位置Y
	 * @param int $zone 区域
	 * @param int $gender 性别
	 * @param int $consumer_id 平台ID
	 * @return int
	 */
	static public function create($cityId, $faceId, $cityName, $posNo, $gender) {
		$result = false;
		if (!empty($cityId) &&
			!empty($faceId) &&
			!empty($cityName) &&
			!empty($posNo) &&
			!empty($gender)
		) {
			//获取城市初始化数据
			$baseConf   = M_Config::getVal();
			$clientInfo = M_Client::get($cityId);
			$info       = array(
				'id'                     => $cityId,
				'consumer_id'            => $clientInfo['consumer_id'],
				'server_id'              => $clientInfo['server_id'],
				'face_id'                => $gender . '_' . $faceId,
				'gender'                 => $gender,
				'nickname'               => $cityName,
				'pos_no'                 => $posNo,
				'mil_pay'                => 10000, //@todo delete milpay
				'energy'                 => $baseConf['user_energy_limit'],
				'mil_order'              => $baseConf['user_mil_order_limit'],
				'max_people'             => $baseConf['city_max_people'],
				'equip_strong_luck_pool' => M_City::INIT_LUCK, //初始幸运池幸运值
			);

			$cityId = B_DB::instance('City')->add($info);
			$succ   = false;
			if (!empty($cityId)) {
				$succ = M_MapWild::initWildMapData($cityId, $posNo);
			}

			if ($succ) {
				$result = $cityId;
			} else {
				$msg = array(__METHOD__, T_ErrNo::CITY_INIT_ERR, func_get_args());
				Logger::debug($msg);
			}

		}
		return $result;
	}


	/**
	 * 检测城市名称
	 * @author huwei at 2011/03/31
	 * @param string $name 用户呢称
	 * @return string 正确为空
	 */
	static public function checkCityNickname($name) {
		$errNo = '';
		$len   = B_Utils::len($name);

		$maxNameLength = T_App::MAX_NAME_LENGTH;

		if ($len < T_App::MIN_NAME_LENGTH || $len > $maxNameLength) { //检测长度[越南版30字符]
			$errNo = T_ErrNo::CITY_NAME_LENGTH_ERR;
		} else if (B_Utils::isIllegalName($name) || B_Utils::isBlockName($name)) {
			$errNo = T_ErrNo::CITY_NAME_ILLEGAL;
		} else if (M_City::getCityIdByNickName($name)) {
			$errNo = T_ErrNo::CITY_NAME_EXIST;
		}
		return $errNo;
	}

	/**
	 * 同步最新的某城市军饷或点券数量至前端接口
	 * @author huwei on 20110928
	 * @param int $cityId 城市ID
	 * @param array $consumeArr array(T_App::MILPAY, T_App::COUPON)
	 */
	static public function syncConsume2Front($cityId, $consumeArr) {
		if (!empty($cityId) && !empty($consumeArr)) {
			$cityInfo = self::getInfo($cityId);
			$msRow    = array();
			foreach ($consumeArr as $con) {
				if ($con == T_App::MILPAY) {
					$msRow['milpay'] = $cityInfo['mil_pay'];
				}
				if ($con == T_App::COUPON) {
					$msRow['coupon'] = $cityInfo['coupon'];
				}
			}
			if (!empty($msRow)) {
				M_Sync::addQueue($cityId, M_Sync::KEY_CITY_INFO, $msRow); //同步资源(金钱)数据
			}
		}
	}

	/**
	 * 同步建筑CD时间至前端接口
	 * @author chenhui on 20110815
	 * @param int $cityId 城市ID
	 */
	static public function syncCDBuild2Front($cityId, $cdBuild, $cdBuildNum) {
		if (!empty($cityId) && !empty($cdBuild)) {
			$cdData = M_Formula::calcBuildCDTime($cdBuild, $cdBuildNum);
			$msRow  = array(
				'build' => $cdData
			);
			M_Sync::addQueue($cityId, M_Sync::KEY_CDTIME, $msRow); //同步建筑CD数据
		}
	}

	/**
	 * 同步科技CD时间至前端接口
	 * @author chenhui on 20110815
	 * @param int $cityId 城市ID
	 * @param string $str_new_cd_tech 科技CD字符串
	 * @param int $cd_tech_num 科技队列数
	 */
	static public function syncCDTech2Front($cityId, $str_new_cd_tech, $cd_tech_num) {
		if (!empty($cityId)) {
			$cdData = M_Formula::calcTechCDTime($str_new_cd_tech, $cd_tech_num);
			$msRow  = array(
				'tech' => $cdData
			);
			M_Sync::addQueue($cityId, M_Sync::KEY_CDTIME, $msRow); //同步科技CD数据
		}
	}

	/**
	 * 同步武器CD时间至前端接口
	 * @author chenhui on 20110815
	 * @param int $cityId 城市ID
	 * @param int $cityCDWeaponTime 武器CD到期时间
	 * @param int $fT CD队列可否累加
	 */
	static public function syncCDWeapon2Front($cityId, $cityCDWeaponTime, $fT) {
		if (!empty($cityId)) {
			$cdData = array(M_Formula::calcCDTime($cityCDWeaponTime), $fT);
			$msRow  = array(
				'weapon' => $cdData
			);
			M_Sync::addQueue($cityId, M_Sync::KEY_CDTIME, $msRow); //同步武器CD数据
		}
	}

	/**
	 * 同步副本CD时间至前端接口
	 * @author chenhui on 20120302
	 * @param int $cityId 城市ID
	 * @param int $cityCDFBTime 副本CD到期时间
	 * @param int $fT CD队列可否累加
	 */
	static public function syncCDFB2Front($cityId, $cdTime, $flag) {
		if (!empty($cityId)) {
			$cdData = array(M_Formula::calcCDTime($cdTime), $flag);
			$msRow  = array('fb' => $cdData);
			//Logger::dev("cityID#{$cityId}#".json_encode($msRow));
			M_Sync::addQueue($cityId, M_Sync::KEY_CDTIME, $msRow); //同步副本CD数据
		}
	}

	/**
	 * 同步探索属地CD时间至前端接口
	 * @author chenhui on 20110815
	 * @param int $cityId 城市ID
	 * @param int $cityInfo ['cd_explore']
	 * @param int $fT CD队列可否累加
	 * @todo 探索属地接口还没有，同步接口待添加
	 */
	static public function syncCDExplore2Front($cityId, $cityCDExploreTime, $fT) {
		if (!empty($cityId)) {
			$cdData = array(M_Formula::calcCDTime($cityCDExploreTime), $fT);
			$msRow  = array('explore' => $cdData);
			M_Sync::addQueue($cityId, M_Sync::KEY_CDTIME, $msRow); //同步资源(金钱)数据
		}
	}


	/**
	 * 获取某玩家/城市活力值上限
	 * @author chenhui on 20110708
	 * @param int $vipLevel VIP等级
	 * @return int 上限值
	 */
	static public function getEnergyUpLimit($vipLevel) {
		$ret        = 0;
		$vipLevel   = intval($vipLevel);
		$baseConfig = M_Config::getVal();

		if (!empty($baseConfig) && isset($baseConfig['user_energy_limit'])) {
			$ret     = $baseConfig['user_energy_limit'];
			$vipConf = $baseConfig['vip_config'];
			if (isset($vipConf['INCR_ENERGY_LIMIT'][$vipLevel])) {
				$ret += $vipConf['INCR_ENERGY_LIMIT'][$vipLevel];
			}
		}

		return $ret;
	}

	/**
	 * 获取某玩家/城市军令值上限
	 * @author chenhui on 20110816
	 * @param int $vipLevel VIP等级
	 * @return int 上限值
	 */
	static public function getOrderUpLimit($vipLevel) {
		$ret        = 0;
		$vipLevel   = intval($vipLevel);
		$baseConfig = M_Config::getVal();

		if (!empty($baseConfig) && isset($baseConfig['user_mil_order_limit'])) {
			$ret     = $baseConfig['user_mil_order_limit'];
			$vipConf = $baseConfig['vip_config'];
			if (isset($vipConf['INCR_MILORDER_LIMIT'][$vipLevel])) {
				$ret += $vipConf['INCR_MILORDER_LIMIT'][$vipLevel];
			}
		}

		return $ret;
	}


	/**
	 * 获取消费动作需要扣除的消费数
	 * @author huwei
	 * @param string $action 消费动作
	 * @param int $payType 消费类型(1军饷 或 2点券)
	 * @return int/bool
	 */
	static public function getConsumeVal($action, $payType) {
		$ret = false;
		if (isset(T_Effect::$payAction[$action])) {
			$payConf = M_Config::getVal('pay_action_value');
			if (!empty($payConf[$action][$payType])) {
				$ret = $payConf[$action][$payType];
			}
		}
		return $ret;
	}


	/**
	 * 更新玩家的货币
	 * @author huwei
	 * @param int $cityId
	 * @param array $addNumArr [milpay,coupon]
	 * @param array $leftNumArr [milpay,coupon]
	 * @return bool
	 */
	static private function _upCityCurrency($cityInfo, $addNumArr = array(), &$leftNumArr = array(), $passGateway = true) {
		$ret              = false;
		$addNum['milpay'] = isset($addNumArr['milpay']) ? intval($addNumArr['milpay']) : 0;
		$addNum['coupon'] = isset($addNumArr['coupon']) ? intval($addNumArr['coupon']) : 0;
		$cityId           = intval($cityInfo['id']);
		if ($cityId > 0) {
			$newNum     = -1;
			$leftNumArr = array(
				'milpay' => $cityInfo['mil_pay'],
				'coupon' => $cityInfo['coupon'],
			);

			$tmpArr = $upFieldArr = $upFieldResArr = $syncRow = array();
			//军饷
			if (!empty($addNum['milpay'])) {
				if (($addNum['milpay'] < 0 && $cityInfo['mil_pay'] >= abs($addNum['milpay'])) ||
					($addNum['milpay'] > 0)
				) {
					$tmpArr['milpay'] = max($cityInfo['mil_pay'] + $addNum['milpay'], 0);
				}
			}
			//礼券
			if (!empty($addNum['coupon'])) {
				if (($addNum['coupon'] < 0 && $cityInfo['coupon'] >= abs($addNum['coupon'])) ||
					($addNum['coupon'] > 0)
				) {
					$tmpArr['coupon'] = max($cityInfo['coupon'] + $addNum['coupon'], 0);
				}
			}

			//发生变化
			if (isset($tmpArr['milpay']) && $tmpArr['milpay'] != $cityInfo['mil_pay']) {
				$upFieldArr['mil_pay'] = $tmpArr['milpay'];
				$syncRow['milpay']     = $tmpArr['milpay'];
				$leftNumArr['milpay']  = $tmpArr['milpay'];
			}
			if (isset($tmpArr['coupon']) && $tmpArr['coupon'] != $cityInfo['coupon']) {
				$upFieldArr['coupon'] = $tmpArr['coupon'];
				$syncRow['coupon']    = $tmpArr['coupon'];
				$leftNumArr['coupon'] = $tmpArr['coupon'];
			}

			if (!empty($upFieldArr) || !empty($upFieldResArr)) {
				$ret = self::setCityInfo($cityId, $upFieldArr);
				$ret && M_Sync::addQueue($cityId, M_Sync::KEY_CITY_INFO, $syncRow, $passGateway);
			}
		}

		return $ret;
	}

	/**
	 * 根据城市ID更新城市信息
	 * @author chenhui on 20110517
	 * @param int $cityId 城市ID
	 * @param array $updInfo 要更新的键值对数组
	 * @return bool true/false
	 */
	static public function setCityInfo($cityId, $updInfo) {
		$ret = self::_setCityInfo($cityId, $updInfo);
		M_City::getInfo($cityId, true);
		return $ret;
	}

	/**
	 * 删除城市基本信息key
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @return bool
	 */
	static public function delCityInfo($cityId) {
		$rc  = new B_Cache_RC(T_Key::CITY_INFO, $cityId);
		$ret = $rc->delete();
		return $ret;
	}

	/**
	 * 更新城市基础信息
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @param array $fieldArr 需要更新的数据字段数组 例:array('gold'=>100,'oil'=>100,....)
	 * @return array
	 */
	static private function _setCityInfo($cityId, $fieldArr, $upDB = true) {
		$ret = false;
		if (!empty($cityId) && is_array($fieldArr) && !empty($fieldArr)) {
			$info = array();
			foreach ($fieldArr as $key => $val) {
				if (!empty($key) && in_array($key, T_DBField::$cityInfoFields)) {
					$info[$key] = $val;
				}
			}

			if (!empty($info)) {
				$rc  = new B_Cache_RC(T_Key::CITY_INFO, $cityId);
				$ret = $rc->hmset($info, T_App::ONE_DAY);
				if ($ret) {
					//$cityData = $rc->hgetall();
					//$cityData['sys_sync_time'] = time();
					//B_DB::instance('City')->update($cityData, $cityData['id']);
				//	Logger::debug(array(__METHOD__, $cityData));

					$upDB && M_CacheToDB::addQueue(T_Key::CITY_INFO . ':' . $cityId);
				} else {
					Logger::error(array(__METHOD__, 'Err Update', func_get_args()));
				}
			}
		}

		return $ret ? $info : false;
	}


	/**
	 * 去除新手保护
	 * @author duhuihui on 20120905
	 */
	static public function removeNewProtection() {
		$cityId    = array();
		$cityIdArr = B_DB::instance('City')->getExpiredNew(); //查询数据表，将符合条件的用户坐标查询出来
		$pos_no    = array();
		foreach ($cityIdArr as $value) {
			M_City::setCityInfo($value['id'], array('newbie' => self::NEWBIE_GUARD_NOT)); //更新这些用户缓存
			$cityInfo = M_City::getInfo($value['id']);
			$pos_no[] = $cityInfo['pos_no'];
		}
		foreach ($pos_no as $posNo) {
			M_MapWild::syncWildMapBlockCache($posNo);
		}
		return $pos_no;

	}

	/**
	 * 分区人数
	 * @return array
	 */
	static public function getTotal() {
		$apcKey = T_Key::TOTAL_PLAYER;
		$arr    = B_Cache_APC::get($apcKey);
		if (empty($arr)) {
			$arr = array(
				T_App::MAP_ASIA   => intval(B_DB::instance('WildMap')->total(T_App::MAP_ASIA)),
				T_App::MAP_EUROPE => intval(B_DB::instance('WildMap')->total(T_App::MAP_EUROPE)),
				T_App::MAP_AFRICA => intval(B_DB::instance('WildMap')->total(T_App::MAP_AFRICA)),
			);
			B_Cache_APC::set($apcKey, $arr, T_App::ONE_MINUTE);
		}
		return $arr;

	}
}

?>
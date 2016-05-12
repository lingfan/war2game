<?php

class M_ColonyCity {
	/**
	 * 开放属地条件
	 * @var array(槽ID=>array(Vip等级,军饷数))
	 */
	static $OpenSlotRule = array(
		1 => array(),
		2 => array(5, 500),
		3 => array(8, 1000),
	);


	/**
	 * 获取城市信息
	 * 数据格式array(是否开启1/0,地图编号,税收次数,税收过期时间,更新日期)
	 * @author duhuihui
	 * @param int $cityId
	 * @return array
	 */
	static public function get($cityId) {
		$cityId         = intval($cityId);
		$cityColonyInfo = M_ColonyCity::getInfo($cityId);
		if (!empty($cityColonyInfo['colony_city'])) {
			$tmpArr = json_decode($cityColonyInfo['colony_city'], true);
		} else {
			//属地编号1,2,3
			//数据格式array(是否开启1/0,地图编号,税收过期时间,更新日期)
			if ('tw' == ETC_NO) {
				$tmpArr = array(
					1 => array(1, 0, 0, 0),
					2 => array(1, 0, 0, 0),
					3 => array(1, 0, 0, 0),
				);
			} else {
				$tmpArr = array(
					1 => array(1, 0, 0, 0),
					2 => array(0, 0, 0, 0),
					3 => array(0, 0, 0, 0),
				);
			}
			$upArr = array('colony_city' => json_encode($tmpArr));
			$ret   = M_ColonyCity::setInfo($cityId, $upArr);
		}
		return $tmpArr;
	}

	/**
	 * 添加野外城市信息
	 * @author duhuihui
	 * @param array $marchInfo
	 * @return bool
	 */
	static public function add($marchInfo) {
		$ret = false;
		if (!empty($marchInfo)) {
			$cityId  = intval($marchInfo['atk_city_id']); //攻击方
			$posNo   = $marchInfo['def_pos']; //占领的城市坐标
			$marchId = $marchInfo['id'];
			$now     = time();
			$tmpArr  = M_ColonyCity::get($cityId);
			foreach ($tmpArr as $no => $val) {
				if ($val[0] == 1 && empty($val[1])) //属地要开启并且坐标编号不为空
				{
					$nickname    = '';
					$restoreArmy = '';
					if (!empty($posNo)) { //设置行军状态为驻守
						M_March::setMarchHold($marchInfo);
						$mapInfo = M_MapWild::getWildMapInfo($posNo);

						$cityColonyInfo = M_ColonyCity::getInfo($mapInfo['city_id']); //得到该城市已之前已被占领时间
						$colonyHoldTime = isset($cityColonyInfo['hold_time']) ? $cityColonyInfo['hold_time'] : 0;

						$holdTimeInterval = M_Config::getVal('hold_city_time_interval');
						$rescueInterval   = M_Config::getVal('rescue_cd');

						$holdTime = $now + (T_App::ONE_HOUR * $holdTimeInterval - $colonyHoldTime);
						//Logger::debug(array(__METHOD__,'-----------',date('Y-m-d H:i:s',$holdTime)));
						if (date('Ymd', $holdTime) != date('Ymd')) {
							$holdTime = $now;
						}
						//Logger::debug(array(__METHOD__,'-----------',date('Y-m-d H:i:s',$holdTime)));
						$taxCd         = M_Config::getVal('tax_cd');
						$tmpTime       = $taxCd * T_App::ONE_MINUTE;
						$taxExpireTime = $now + $tmpTime;

						//数据格式array(是否开启1/0,地图编号,税收过期时间,属地到期时间)
						$tmpArr[$no] = array(1, $posNo, $taxExpireTime, date('Ymd'));

						$upArr1 = array(
							'colony_city' => json_encode($tmpArr)
						);
						$bUp1   = M_ColonyCity::setInfo($cityId, $upArr1);

						$objPlayerDef = new O_Player($cityId);
						$objPlayerDef->CD()->set(O_CD::TYPE_RESCUE, 1, $rescueInterval * T_App::ONE_MINUTE);
						$objPlayerDef->save();

						$upArr2 = array(
							'atk_city_id'  => $cityId,
							'atk_march_id' => $marchInfo['id'],
						);
						$bUp2   = M_ColonyCity::setInfo($mapInfo['city_id'], $upArr2);

						//同步解救属地CD时间
						$msRow = array('rescue' => $objPlayerDef->CD()->toFront(O_CD::TYPE_RESCUE));
						M_Sync::addQueue($mapInfo['city_id'], M_Sync::KEY_CDTIME, $msRow);

						list($zone, $posx, $poxy) = M_MapWild::calcWildMapPosXYByNo($posNo);

						$cityInfo = M_City::getInfo($mapInfo['city_id']);
						$level    = $cityInfo['level'];
						$nickname = $cityInfo['nickname'];
						$faceId   = $cityInfo['face_id'];
						$upData   = array(
							'hold_expire_time' => $holdTime, //在线程里已经对此字段不停地修改了
							'march_id'         => $marchId,
						);

						$ret = $bUp1 && $bUp2;
						//更新野外地图信息
						//Logger::debug(array(__METHOD__,'-----------',$bUp1,$bUp2,$ret,$upData));
						$ret && M_MapWild::setWildMapInfo($posNo, $upData);

						//添加到占领队列
						$ret && M_March_Hold::set($posNo);

						M_MapWild::syncWildMapBlockCache($posNo);

						$msRow[$no] = array(
							'IsOpen'        => $val[0],
							'FaceId'        => $faceId,
							'Name'          => $nickname,
							'PosX'          => $posx,
							'PosY'          => $poxy,
							'PosArea'       => $zone,
							'Level'         => intval($level),
							'MarchId'       => $marchId > 0 ? intval($marchId) : 0,
							'MarchType'     => $marchId > 0 ? 1 : 0,
							'TaxExprieTime' => $taxExpireTime,
							'ExprieTime'    => $holdTime,
							'IntervalTime'  => $tmpTime,
						);

						$ret && M_Sync::addQueue($cityId, M_Sync::KEY_CITY_COLONY, $msRow); //同步属地数据

					} else {
						Logger::error(array(__METHOD__, 'wild npc type err', $val));
					}
					break;
				}
			}
		}
		return $ret;
	}

	/**
	 * 更新野外城市信息
	 * @author duhuihui
	 * @param int $cityId
	 * @param int $no
	 * @param array $data
	 * @return bool
	 */
	static public function set($cityId, $no, $data = array(0, 0, 0, 0)) {
		$ret    = false;
		$now    = time();
		$cityId = intval($cityId);
		$msRow  = array();
		$tmpArr = M_ColonyCity::get($cityId);
		if (isset($tmpArr[$no]) && count($data) == 4) {
			$tmpArr[$no] = $data;
			$upArr       = array('colony_city' => json_encode($tmpArr));
			$ret         = M_ColonyCity::setInfo($cityId, $upArr);
			$marchType   = $marchId = $marchId1 = $level = $zone = $posx = $poxy = 0;
			$nickname    = $faceId = '';
			if (!empty($data[1])) {
				$marchList = M_March::getMarchList($cityId, M_War::MARCH_OWN_ATK);
				foreach ($marchList as $key => $marchInfo) {
					if ($marchInfo['def_pos'] == $data[1]) {
						$marchId1 = $key;
					}
				}
				list($zone, $posx, $poxy) = M_MapWild::calcWildMapPosXYByNo($data[1]);
				$mapInfo          = M_MapWild::getWildMapInfo($data[1]);
				$defCityColony    = M_ColonyCity::getInfo($mapInfo['city_id']);
				$colonyHoldTime   = isset($defCityColony['hold_time']) ? $defCityColony['hold_time'] : 0;
				$holdTimeInterval = M_Config::getVal('hold_city_time_interval');
				$holdTime         = $now + (T_App::ONE_HOUR * $holdTimeInterval - $colonyHoldTime);
				$mapInfo          = M_MapWild::getWildMapInfo($data[1]);
				$marchId          = $defCityColony['atk_march_id'];
				$cityInfo         = M_City::getInfo($mapInfo['city_id']);
				$nickname         = $cityInfo['nickname'];
				$level            = $cityInfo['level'];
				$faceId           = $cityInfo['face_id'];
				if ($marchId > 0) {
					$marchType = 1;
				} else {
					$marchType = 0;
					if ($marchId1 > 0) {
						$marchType = 2;
					}
				}
				M_MapWild::syncWildMapBlockCache($data[1]);
			}

			$msRow[$no] = array(
				'IsOpen'        => $data[0],
				'FaceId'        => $faceId,
				'Name'          => $nickname,
				'PosX'          => $posx,
				'PosY'          => $poxy,
				'PosArea'       => $zone,
				'Level'         => intval($level),
				'MarchId'       => $marchId > 0 ? intval($marchId) : 0,
				'MarchType'     => $marchType,
				'TaxExprieTime' => $data[2],
				'ExprieTime'    => !empty($holdTime) ? $holdTime : 0,
				'IntervalTime'  => $data[2] - $now,
			);
			$ret && !empty($msRow) && M_Sync::addQueue($cityId, M_Sync::KEY_CITY_COLONY, $msRow); //同步属地数据
		}
		return $ret;
	}

	/**
	 * 删除野外城市信息
	 * @author duhuihui
	 * @param int $cityId
	 * @param int $posNo
	 * @return bool
	 */
	static public function del($cityId, $posNo) {
		$ret    = false;
		$cityId = intval($cityId);
		$now    = time();
		$tmpArr = M_ColonyCity::get($cityId);
		Logger::dev("#{$cityId}属地信息#" . json_encode($tmpArr));
		foreach ($tmpArr as $no => $val) {
			if ($val[0] == 1 &&
				$val[1] == $posNo
			) {
				//数据格式array(是否开启1/0,地图编号,税收过期时间,属地到期时间,更新日期)
				$tmpArr[$no] = array(1, 0, 0, date('Ymd'));
				$mapInfo     = M_MapWild::getWildMapInfo($posNo);

				$upArr1 = array('colony_city' => json_encode($tmpArr));
				$ret1   = M_ColonyCity::setInfo($cityId, $upArr1);

				$upArr2 = array('atk_city_id' => 0, 'atk_march_id' => 0);
				$ret2   = M_ColonyCity::setInfo($mapInfo['city_id'], $upArr2);

				$ret = $ret1 && $ret2;
				if ($ret) {
					//删除占领队列
					$bUp    = M_March_Hold::del($posNo);
					$upData = array('march_id' => 0);
					if (date('Ymd', $mapInfo['hold_expire_time']) != date('Ymd')) {
						$upData = array('march_id' => 0, 'hold_expire_time' => $now);
					}
					M_MapWild::setWildMapInfo($posNo, $upData);
					//	Logger::debug(array(__METHOD__,'-----------',$upData));
					M_MapWild::syncWildMapBlockCache($posNo);
					$msRow[$no] = array(
						'IsOpen'        => 1,
						'Name'          => '',
						'FaceId'        => '',
						'PosX'          => 0,
						'PosY'          => 0,
						'PosArea'       => 0,
						'Level'         => 0,
						'MarchId'       => 0,
						'MarchType'     => 0,
						'TaxExprieTime' => 0,
						'ExprieTime'    => 0,
						'IntervalTime'  => 0,
					);
					M_Sync::addQueue($cityId, M_Sync::KEY_CITY_COLONY, $msRow); //同步属地数据
				}
				break;
			}
		}

		return $ret;
	}

	/**
	 * 删除野外城市信息
	 * @author duhuihui
	 * @param int $cityId
	 * @param int $posNo
	 * @return bool
	 */
	static public function getNoByPosNo($cityId, $posNo) {
		$ret    = false;
		$cityId = intval($cityId);
		$msRow  = array();
		$tmpArr = M_ColonyCity::get($cityId);
		foreach ($tmpArr as $no => $val) {
			if ($val[1] == $posNo) {
				$ret['no']  = $no;
				$ret['val'] = $val;
				break;
			}
		}
		return $ret;
	}

	/**
	 *增加临时仓库资源
	 * @author duhuihui
	 * @param int $cityId
	 * @return $list
	 */
	static public function getTempWareHouse($cityId) {
		$rc           = new B_Cache_RC(T_Key::CITY_TEMP_WAERHOUSE, $cityId); //存放在被占领城市的临时仓库
		$cityResInfo  = $rc->hgetall(); //得到所有的值
		$list['food'] = isset($cityResInfo['food']) ? $cityResInfo['food'] : 0;
		$list['oil']  = isset($cityResInfo['oil']) ? $cityResInfo['oil'] : 0;
		$list['gold'] = isset($cityResInfo['gold']) ? $cityResInfo['gold'] : 0;
		return $list;
	}

	/**
	 * 增加临时仓库资源
	 * @author duhuihui
	 * @param int $cityId
	 * @param array $arr
	 * @return bool
	 */
	static public function upTempWareHouse($cityId, $arr) //('food'=>,'oil'=>,'gold'=>)
	{
		$ret         = false;
		$cityResInfo = array();
		$cityResInfo = self::getTempWareHouse($cityId);

		if (!empty($arr)) {
			$cityResInfo['food'] += $arr['food'];
			$cityResInfo['oil'] += $arr['oil'];
			$cityResInfo['gold'] += $arr['gold'];
		}
		if (!empty($cityResInfo)) {
			$rc  = new B_Cache_RC(T_Key::CITY_TEMP_WAERHOUSE, $cityId);
			$ret = $rc->hmset($cityResInfo);
		}
		return $ret;
	}

	/**
	 * 更新临时仓库资源
	 * @author duhuihui
	 * @param int $cityId
	 * @param array $arr
	 * @return bool
	 */
	static public function setTempWareHouse($cityId, $arr) //('food'=>,'oil'=>,'gold'=>)
	{
		$ret = false;
		if (empty($arr)) {
			$arr['food'] = 0;
			$arr['oil']  = 0;
			$arr['gold'] = 0;
		}
		if (!empty($arr)) {
			$rc  = new B_Cache_RC(T_Key::CITY_TEMP_WAERHOUSE, $cityId);
			$ret = $rc->hmset($arr);
		}
		return $ret;
	}

	/**
	 * 被占领城市的资源存入临时仓库
	 * @author duhuihui
	 * @param int $cityId
	 * @return bool
	 */
	static public function setWareHouse() {
		$ret  = false;
		$now  = time();
		$list = M_March_Hold::get();
		//echo json_encode($list);
		//Logger::dev('========='.json_encode($list));
		foreach ($list as $posNo) {
			$needDel = true;
			$mapInfo = M_MapWild::getWildMapInfo($posNo);
			$cityId  = $mapInfo['city_id'];

			$cityResInfo = self::getTempWareHouse($cityId);
			$rc          = new B_Cache_RC(T_Key::UNION_INVITE_TIMES, $cityId);
			if (empty($cityResInfo['food']) && empty($cityResInfo['oil']) && empty($cityResInfo['gold'])) //刚开始和在领取过后都清空
			{
				$rc->delete();
			}

			$rc->hincrby($cityId, M_Client::VISIT_LOOP_DELAY_TIME);
			$calTime = $rc->hgetall();
			//Logger::dev('+++++++++++++++'.$calTime[$cityId]);
			$taxCd = M_Config::getVal('tax_cd');
			if ($calTime[$cityId] < $taxCd * T_App::ONE_MINUTE) //没到可以领取的时间就一直存在临时仓库中
			{ //传值过来

				$objPlayer = new O_Player($cityId);
				$result    = $objPlayer->Res()->get();

				//Logger::dev(json_encode($cityId));
				if (!empty($result)) {
					$oldResInfo = $result;
					//计算资源
					$diffTime = M_Client::VISIT_LOOP_DELAY_TIME;
					if ($diffTime > 0) {
						$foodReduce = M_Config::getVal('food_reduce');
						$goldReduce = M_Config::getVal('gold_reduce');
						$oilReduce  = M_Config::getVal('oil_reduce');

						$upResArr['gold'] = M_Formula::calcIncrResAddTemp($result['gold_grow'], $diffTime);
						$upResArr['oil']  = M_Formula::calcIncrResAddTemp($result['oil_grow'], $diffTime);
						$upResArr['food'] = M_Formula::calcIncrResAddTemp($result['food_grow'], $diffTime);
						$upResArr['gold'] = ($upResArr['gold'] * (100 - $goldReduce)) / 100;
						$upResArr['food'] = ($upResArr['food'] * (100 - $foodReduce)) / 100;
						$upResArr['oil']  = ($upResArr['oil'] * (100 - $oilReduce)) / 100;
						$ret              = self::upTempWareHouse($cityId, $upResArr); //存放到
						//Logger::dev(json_encode($upResArr));
						//Logger::dev(json_encode(self::getTempWareHouse($cityId)));


						$upResInfo = array();
						if ($result['gold'] + $upResArr['gold'] > $result['max_store']) {
							$objPlayer->Res()->incr('gold', -$upResArr['gold']);
						}
						if ($result['food'] + $upResArr['food'] > $result['max_store']) {
							$objPlayer->Res()->incr('food', -$upResArr['food']);
						}
						if ($result['oil'] + $upResArr['oil'] > $result['max_store']) {
							$objPlayer->Res()->incr('oil', -$upResArr['oil']);
						}

						$objPlayer->save();
					}
				}

			}
		}
		return $ret;
	}

	/**
	 * 检查城市是否有占领城市
	 * @author huwei
	 * @param int $cityId
	 * @return bool
	 */
	static public function isHadCityColony($cityId) {
		$cityColonyInfo = M_ColonyCity::getInfo($cityId);
		$hasHold        = false;
		if (!empty($cityColonyInfo['colony_city'])) {
			$colonyCity = (array)json_decode($cityColonyInfo['colony_city'], true);

			foreach ($colonyCity as $val) {
				if (!empty($val[1])) //如果有城市属地则不可以使用免战道具
				{
					$hasHold = true;
					break;
				}
			}
		}
		return $hasHold;
	}

	/**
	 * 重置玩家属地信息 校正错误数据
	 * @author huwei
	 * @param array $info
	 * @param string $posNo
	 */
	static public function revise($cityId) {
		$info     = M_ColonyCity::getInfo($cityId);
		$cityInfo = M_City::getInfo($cityId);
		if (!empty($info['colony_city'])) {
			$upArr = array();
			if ($info['atk_city_id'] > 0) {
				//当前玩家的 占领方城市ID(谁占领我的城市信息)
				$holdColnyInfo = M_ColonyCity::get($info['atk_city_id']);
				//Logger::debug(array(__METHOD__, $holdColnyInfo, $cityId));
				if ($holdColnyInfo) {
					$flag = 0;
					//查看占领属地信息里面有没有当前玩家的坐标
					foreach ($holdColnyInfo as $val) {
						if ($val[1] == $cityInfo['pos_no']) {
							$flag++;
						}
					}
					if ($flag == 0) { //没有我的坐标  更新我的占领方为空
						$upArr['atk_city_id'] = 0;
						M_ColonyCity::setInfo($info['city_id'], $upArr);
						Logger::error(array(__METHOD__, "err atk_city_id#{$info['atk_city_id']}", $info['city_id']));
					}
				}
			}

			$list = (array)json_decode($info['colony_city'], true);
			if ($list) {
				foreach ($list as $no => $val) {
					if ($val[1] > 0) {
						$mapInfo      = M_MapWild::getWildMapInfo($val[1]);
						$holdCityInfo = M_ColonyCity::getInfo($mapInfo['city_id']);
						//当前玩家占领的属地 的占领方标记不是自己的
						if ($holdCityInfo['atk_city_id'] != $info['city_id']) {
							$up = array($val[0], 0, 0, 0);
							M_ColonyCity::set($info['city_id'], $no, $up);
							Logger::error(array(__METHOD__, "err atk_city_id#{$holdCityInfo['atk_city_id']}", $no, $info['city_id']));
						}
					}

				}
			}

		}

	}

	/**
	 * 根据城市ID更新城市属地信息
	 * @author chenhui on 20110517
	 * @param int cityid 城市ID
	 * @param array updinfo 要更新的键值对数组
	 * @return bool true/false
	 */
	static public function setInfo($cityId, $updInfo, $upDB = true) {
		$ret = self::_setInfo($cityId, $updInfo, $upDB);
		M_ColonyCity::getInfo($cityId, true);
		return $ret;

	}

	/**
	 * 根据城市ID获取城市额外数据
	 * @author chenhui 20110426
	 * @param int $cityId 城市ID
	 * @return array/bool
	 */
	static public function getInfo($cityId, $sync = false) {
		$ret    = false;
		$cityId = intval($cityId);
		if ($cityId > 0) {
			$rc   = new B_Cache_RC(T_Key::CITY_COLONY_INFO, $cityId);
			$info = $rc->hmget(T_DBField::$cityCityColonyFields);
			if (empty($info['city_id'])) {
				$info = B_DB::instance('CityColony')->getRow($cityId);

				if (!empty($info)) {
					self::_setInfo($cityId, $info, false);
				}
			}

			if (!empty($info['city_id']) && $info['rescue_date'] != date('Ymd')) {
				$info['rescue_date'] = date('Ymd');
				$info['rescue_num']  = 0;
				self::_setInfo($cityId, $info, false);
			}

			$ret = $info;
		}
		return $ret;
	}

	/**
	 * 更新扩展信息
	 * @param int $cityId
	 * @param array $updInfo
	 * @param bool $upDB
	 * @return bool
	 */
	static private function _setInfo($cityId, $updInfo, $upDB = true) {
		$ret    = false;
		$cityId = intval($cityId);
		if ($cityId > 0) {
			$arr = array();
			if (!empty($updInfo)) {
				foreach ($updInfo as $k => $v) {
					in_array($k, T_DBField::$cityCityColonyFields) && $arr[$k] = $v;
				}
				$rc = new B_Cache_RC(T_Key::CITY_COLONY_INFO, $cityId);
				if (!empty($arr)) {
					$ret = $rc->hmset($arr, T_App::ONE_DAY);
				}

				if ($ret) {
					$upDB && M_CacheToDB::addQueue(T_Key::CITY_COLONY_INFO . ':' . $cityId);
				} else {
					Logger::error(array(__METHOD__, 'Err Update', func_get_args()));
				}
			}
		}

		return $ret;
	}
}

?>
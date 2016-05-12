<?php

class M_Hero {


	/**
	 * 根据品质数组随机一个模板军官ID
	 * @author chenhui on 20120111
	 * @param array $arrQual 品质数组
	 * @return int 军官ID
	 */
	static public function getRandVipAwardHeroId($arrQual) {
		$heroId = 0;
		if (!empty($arrQual) && is_array($arrQual)) {
			$arrId = array();
			foreach ($arrQual as $qual) {
				$arrT  = self::_getBaseHeroTplListByQual($qual, true);
				$arrId = array_merge($arrId, $arrT);
			}
			$heroId = $arrId[array_rand($arrId)];
			if ($heroId > 0) {
				self::decrBaseTplHeroNum($heroId);
			}
		}
		return intval($heroId);
	}

	/**
	 * 寻将生成一个传奇英雄
	 * @author huwei
	 * @param int $findNum 寻将次数
	 * @param int $qual 品质 (如果无当前品质的英雄,重新抽)
	 * @return bool/array
	 */
	static public function makeSpecialHero($findNum) {
		static $n = 0;
		$heroConf = M_Config::getVal();
		//通过次数获取当前等级对应的概率
		if (empty($qual)) {
			$key = ceil($findNum / 5);
		} else {
			$key = $qual;
		}

		//获取传奇英雄的概率
		//超过30次 去最大怪率数组
		if ($key > 6) {
			$rateArr = $heroConf['hero_find_rate'][6];
		} else {
			$rateArr = isset($heroConf['hero_find_rate'][$key]) ? $heroConf['hero_find_rate'][$key] : $heroConf['hero_find_rate'][1];
		}
		$heroQual = B_Utils::dice($rateArr);
		//从模板库中抽取一个
		$heroList = self::_getBaseHeroTplListByQual($heroQual);

		$info = false;
		if (!empty($heroList[0])) {
			$info = M_Hero::baseInfo($heroList[0]);
		}
		return $info;
	}


	/**
	 * 获取模板英雄列表
	 * @author huwei on 2011109
	 * @param int $heroId 模板英雄品质
	 * @return array
	 */
	static private function _getBaseHeroTplListByQual($heroQual, $isDraw = false) {
		$arr = array();
		if (!empty($heroQual)) {
			$tmpArr   = array();
			$baseList = M_Base::heroAll();
			foreach ($baseList as $val) {
				if (empty($val['can_find'])) {
					$tmpArr[$val['quality']][] = $val['id'];
				}
			}
			$list = isset($tmpArr[$heroQual]) ? $tmpArr[$heroQual] : array();

			shuffle($list);
			if ($isDraw) {
				$arr = $list;
			} else {
				$arr = array();
				foreach ($list as $val) {
					$hasNum = self::getBaseTplHeroNum($val);
					if ($hasNum > 0) {
						$arr[] = $val;
					}
				}
			}
		}

		return $arr;
	}

	/**
	 * 获取模板英雄数量
	 * @author huwei on 2011214
	 * @param int $heroId 模板英雄ID
	 * @return int
	 */
	static public function getBaseTplHeroNum($heroTplId) {
		return 10000;
		$ret = false;
		if (!empty($heroTplId)) {
			$rc  = new B_Cache_RC(T_Key::BASE_HERO_TPL_NUM);
			$ret = $rc->hget($heroTplId);
		}
		return $ret;
	}

	/**
	 * 更新模板英雄数量
	 * @author huwei on 2011214
	 * @param int $heroId 模板英雄ID
	 * @param int $num
	 * @return int
	 */
	static public function setBaseTplHeroNum($heroTplId, $num) {
		$ret = false;
		if (!empty($heroTplId)) {
			$rc  = new B_Cache_RC(T_Key::BASE_HERO_TPL_NUM);
			$ret = $rc->hset($heroTplId, $num);
		}
		return $ret;
	}

	/**
	 * 删除模板英雄key
	 * @author huwei on 2011214
	 * @param int $heroId 模板英雄ID
	 * @return int
	 */
	static public function delBaseTplHeroNum($heroTplId) {
		$ret = false;
		if (!empty($heroTplId)) {
			$rc  = new B_Cache_RC(T_Key::BASE_HERO_TPL_NUM);
			$ret = $rc->hdel($heroTplId);
		}
		return $ret;
	}

	/**
	 * 增加模板英雄数量
	 * @author huwei on 2011214
	 * @param int $heroId 模板英雄ID
	 * @return int
	 */
	static public function incrBaseTplHeroNum($heroTplId) {
		$ret = false;
		if (!empty($heroTplId)) {
			$rc  = new B_Cache_RC(T_Key::BASE_HERO_TPL_NUM);
			$ret = $rc->hincrby($heroTplId, 1);
		}
		return $ret;

	}

	/**
	 * 减少模板英雄数量
	 * @author huwei on 2011214
	 * @param int $heroId 模板英雄ID
	 * @return int
	 */
	static public function decrBaseTplHeroNum($heroTplId) {
		$ret = false;
		if (!empty($heroTplId)) {
			$rc  = new B_Cache_RC(T_Key::BASE_HERO_TPL_NUM);
			$ret = $rc->hincrby($heroTplId, -1);
		}
		return $ret;
	}


	/**
	 * 获取学院中的英雄
	 * @author huwei on 20111009
	 * @param int $cityId 城市ID
	 * @return array
	 */
	static public function getHeroCollegeList($cityId) {
		$ret = false;
		if (!empty($cityId)) {
			$MCC  = new M_CityCollege();
			$data = $MCC->getData($cityId);

			if (!empty($data['hero_list'])) {
				$ret['list'] = json_decode($data['hero_list'], true);
				$ret['time'] = $data['refresh_time'];
			}
		}
		return $ret;
	}

	/**
	 * 计算军事学院刷新时间
	 * @author huwei on 20111009
	 * @param int $lastRefreshTime 学院上次刷新时间
	 * @param int $payType 付费类型
	 * @return int
	 */
	static public function calcHeroCollegeRefreshTime($lastRefreshTime, $payType = 0) {
		$now          = time();
		$refreshTime  = 0;
		$heroConf     = M_Config::getVal();
		$intervalTime = $heroConf['hero_refresh_interval'] * T_App::ONE_HOUR;

		if (in_array($payType, array(T_App::MILPAY, T_App::COUPON))) {
			$refreshTime = $now + $intervalTime;
		} elseif ($lastRefreshTime < $now) {
			//计算时间差
			$diffTime = !empty($lastRefreshTime) ? $now - $lastRefreshTime : 0;
			if ($diffTime > $intervalTime) {
				//如果时间差大于  刷新时间间隔 (取余时间)
				$diffTime = $diffTime % $intervalTime;
			}
			$refreshTime = $now + $intervalTime - $diffTime;
		}
		return $refreshTime;
	}

	/**
	 * 统计城市拥有的英雄数量
	 * @author huwei
	 * @param int $cityId
	 * @return int
	 */
	static public function totalCityHeroNum($cityId) {
		$list = M_Hero::getCityHeroList($cityId);
		$num  = count($list);
		//$aucHeroNum = M_Auction::getAuctionGoodsNum($cityId, M_Auction::GOODS_HERO);	//在拍卖系统中的军官数量
		//$num += $aucHeroNum;
		return $num;
	}

	/**
	 * 判断某城市军官数量是否满
	 * @author chenhui on 20120315
	 * @param int $cityId
	 * @return bool [false为未满]
	 */
	static public function isHeroNumFull($cityId) {
		$ret        = false;
		$cityInfo   = M_City::getInfo($cityId);
		$cityLv     = $cityInfo['level'];
		$heroNum    = M_Formula::canHasHeroNum($cityLv);
		$hadHeroNum = M_Hero::totalCityHeroNum($cityId);
		if ($hadHeroNum >= $heroNum) {
			$ret = true;
		}
		return $ret;
	}

	/**
	 * 检测英雄名称
	 * @author huwei at 2011/03/31
	 * @param string $name 用户呢称
	 * @return string 正确为空
	 */
	static public function checkName($name) {
		$errNo = '';
		$len   = B_Utils::len($name);
		if ($len < T_App::MIN_NAME_LENGTH || $len > T_App::MAX_NAME_LENGTH) //检测长度
		{
			$errNo = T_ErrNo::HERO_NAME_LENGTH_ERR;
		} else if (B_Utils::isIllegal($name) || B_Utils::isBlockName($name)) //非法字符
		{
			$errNo = T_ErrNo::HERO_NAME_ILLEGAL;
		}
		return $errNo;
	}

	/**
	 * 招募学院中的英雄
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @param int $id 学院英雄ID
	 * @return array
	 */
	static public function collegeHire($cityId, $id) {
		$errNo       = T_ErrNo::HERO_HIRE_ERR;
		$collegeData = M_Hero::getHeroCollegeList($cityId);

		$info = isset($collegeData['list'][$id]) ? $collegeData['list'][$id] : false;

		$ret    = false;
		$heroId = 0;
		if (!empty($info)) {
			$data = $collegeData['list'];
			if ($info['is_hired'] == T_Hero::IS_HIRED_FALSE) {
				$cityInfo   = M_City::getInfo($cityId);
				$cityLv     = $cityInfo['level'];
				$heroNum    = M_Formula::canHasHeroNum($cityLv);
				$hadHeroNum = M_Hero::totalCityHeroNum($cityId);
				if ($heroNum > $hadHeroNum) {
					$row       = array(
						'city_id'        => $cityId,
						'nickname'       => $info['nickname'],
						'gender'         => (int)$info['gender'],
						'quality'        => (int)$info['quality'],
						'level'          => (int)$info['level'],
						'face_id'        => $info['face_id'],
						'exp'            => (int)$info['exp'],
						'is_legend'      => (int)$info['is_legend'],
						'attr_lead'      => (int)$info['attr_lead'],
						'attr_command'   => (int)$info['attr_command'],
						'attr_military'  => (int)$info['attr_military'],
						'attr_energy'    => (int)$info['attr_energy'],
						'attr_mood'      => (int)$info['attr_mood'],
						'stat_point'     => (int)$info['stat_point'],
						'grow_rate'      => $info['grow_rate'],
						'skill_slot_num' => (int)$info['skill_slot_num'],
						'skill_slot'     => $info['skill_slot'],
						'skill_slot_1'   => $info['skill_slot_1'],
						'skill_slot_2'   => $info['skill_slot_2'],
						'army_id'        => $info['army_id'],
						'weapon_id'      => $info['weapon_id'],
						'create_at'      => time(),
					);
					$objPlayer = new O_Player($cityId);
					$isEnough  = false;
					if ($info['quality'] <= 4) {
						$needGold = M_Formula::heroValue($info['attr_lead'] + $info['attr_command'] + $info['attr_military']);

						$leftGold = $objPlayer->Res()->incr('gold', -$needGold);

						if ($leftGold >= 0) {
							$isEnough = true;
						}

					} else {
						//招募传奇军官消耗
						$hireNeed = isset($info['hire_need']) ? json_decode($info['hire_need'], true) : array();
						if (!empty($hireNeed)) {
							$ret = false;
							if (isset($hireNeed['props']) && $hireNeed['props'][0] == 1) {

								$ret = $objPlayer->Pack()->decrNumByPropId($hireNeed['props'][1], 1);
								$ret && $isEnough = true;
							}
							if (!$ret && isset($hireNeed['milpay']) && $hireNeed['milpay'][0] == 1) {
								$ret = $objPlayer->City()->decrCurrency(T_App::MILPAY, $hireNeed['milpay'][1], B_Log_Trade::E_FindHero, $id);
								$ret && $isEnough = true;
							}
							if (!$ret && isset($hireNeed['coupon']) && $hireNeed['coupon'][0] == 1) {
								$ret = $objPlayer->City()->decrCurrency(T_App::COUPON, $hireNeed['coupon'][1], B_Log_Trade::E_FindHero, $id);
								$ret && $isEnough = true;
							}
							if (!$ret && isset($hireNeed['gold']) && $hireNeed['gold'][0] == 1) {
								$objPlayer = new O_Player($cityId);
								$leftGold  = $objPlayer->Res()->incr('gold', -$hireNeed['gold'][1]);
								if ($leftGold >= 0) {
									$isEnough = true;
								}
							}
						}
					}

					if ($isEnough) {
						$heroId = B_DB::instance('CityHero')->insert($row);
						//添加到缓存中
						if (!empty($heroId)) {
							$errNo = '';
							M_Hero::setCityHeroList($cityId, $heroId);

							//设置英雄被招募
							$MCC                   = new M_CityCollege();
							$data[$id]['is_hired'] = 1;
							$ret                   = $MCC->setData($cityId, array('hero_list' => json_encode($data)));

							if ($row['quality'] >= 6) //超级将领系统公告
							{
								$skillName = '';
								$faceId    = 0;
								$desc      = '';
								if (!empty($row['skill_slot'])) {
									$skillInfo = M_Skill::getBaseInfo($row['skill_slot']);
									$skillName = $skillInfo['name'];
									$faceId    = $skillInfo['face_id'];
									$desc      = $skillInfo['desc'];
								}

								$msgArr  = array(
									'Type'         => 1,
									'NickName'     => $cityInfo['nickname'],
									'HeroId'       => $heroId,
									'HeroName'     => $row['nickname'],
									'Gender'       => $row['gender'],
									'FaceId'       => $row['face_id'],
									'Quality'      => $row['quality'],
									'GrowRate'     => $row['grow_rate'], //$growAttr,
									'NextExp'      => M_Formula::getGrowExp($row['level']), //$row['exp_next'],
									'AttrLead'     => $row['attr_lead'] ? $row['attr_lead'] : 0,
									'AttrCommand'  => $row['attr_command'] ? $row['attr_command'] : 0,
									'AttrMilitary' => $row['attr_military'] ? $row['attr_military'] : 0,
									'AttrEnergy'   => $row['attr_energy'] ? $row['attr_energy'] : 0,
									'SkillSlot'    => $skillName,
									'SkillFaceId'  => $row['skill_slot'],
									'SkillDesc'    => $desc,
									'SkillSlotNum' => $row['skill_slot_num'],
									'From'         => Logger::H_ACT_FIND,
								);
								$heroStr = '{' . implode("\t", $msgArr) . '}';
								$title   = json_encode(array(T_Lang::GET_LEGEND_HERO_FIND, $cityInfo['nickname'], $heroStr));
								$msg     = implode("\t", array($title, T_Chat::SYS_RADIO_PRIO, T_Chat::SYS_RADIO_STAY_TIME));
								M_Chat::addWorldMessage(uniqid(), $msg, T_Chat::CHAT_SYS);
							}
						}
					} else {
						$errNo = T_ErrNo::HIRE_NOT_ENOUGH;
					}
				} else {
					$errNo = T_ErrNo::HERO_NUM_FULL_FAIL;
				}
			} else {
				$errNo = T_ErrNo::HERO_EXIST;
			}


		}
		return array('Id' => $heroId, 'Err' => $errNo);
	}

	/**
	 * 修改英雄信息
	 * @author Hejunyun
	 * @param array $data 英雄信息 必须包含ID
	 * @return bool
	 */
	static public function updateInfo($data) {
		$ret = false;
		if (isset($data['id']) && count($data) > 1) {
			$heroId = $data['id'];
			unset($data['id']);
			$fieldArr = $data;
			$ret      = M_Hero::setHeroInfo($heroId, $fieldArr);
		}
		return $ret;
	}

	/**
	 * 获取某军官带兵数量
	 * @author chenhui on 20110630
	 * @param int $heroId 军官ID
	 * @return int
	 */
	static public function getHeroArmyNum($heroId) {
		$heroInfo = self::getHeroInfo($heroId);
		return max($heroInfo['army_num'], 0);
	}

	/**
	 * 复活英雄使用金币
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @param int $cityGold 城市金币
	 * @param int $heroId 英雄ID
	 * @param int $heroLv 英雄等级
	 * @param bool
	 */
	static public function relifeUseGold($cityId, $cityGold, $heroId, $heroLv) {
		$errNo = T_ErrNo::NO_ENOUGH_GOLD;
		//计算复活需要金币
		$num = M_Formula::relifeGlod($heroLv);
		if (floor($cityGold) > intval($num)) {
			//减少金币
			$objPlayer = new O_Player($cityId);
			$objPlayer->Res()->incr('gold', -$num);
			$ret = $objPlayer->save();

			if ($ret && M_Hero::changeHeroFlag($cityId, array($heroId), T_Hero::FLAG_FREE, array('march_id' => 0))) {
				$errNo = '';
			} else {
			}
		}
		return $errNo;
	}


	/**
	 * 杀死英雄
	 * @author huwei
	 * @param int $cityId 城市ID (方便封装同步到前端的数据)
	 * @param int $heroId 英雄ID
	 * @param int $relifeTime 复活时间
	 * @param bool
	 */
	static public function changeHeroFlagKilled($cityId, $heroId, $relifeTime) {
		$ret = false;
		if (!empty($heroId) && !empty($relifeTime)) {
			$now  = time();
			$info = array(
				'flag'        => T_Hero::FLAG_DIE,
				'relife_time' => $now + $relifeTime,
				'march_id'    => 0,
			);

			$ret = M_Hero::setHeroInfo($heroId, $info);

			$msRow = array($heroId => $info);
			$ret && M_Sync::addQueue($cityId, M_Sync::KEY_HERO, $msRow); //同步军官数据到前端
		}
		return $ret;
	}

	/**
	 * 改变英雄状态
	 * @param int $cityId 城市ID (方便封装同步到前端的数据)
	 * @param array $heroIdList 英雄ID列表
	 * @param int $flag 英雄状态
	 * @param bool
	 */
	static public function changeHeroFlag($cityId, $heroIdList, $flag, $extraArr = array()) {
		$err = array();
		if (!empty($cityId) &&
			is_array($heroIdList) &&
			!empty($heroIdList) &&
			isset(T_Hero::$heroFlag[$flag])
		) {
			$msRow = array();
			foreach ($heroIdList as $heroId) {
				$upInfo['flag'] = $flag;
				if (isset($extraArr['march_id'])) {
					$upInfo['march_id'] = $extraArr['march_id'];
				}
				$ret = M_Hero::setHeroInfo($heroId, $upInfo);
				if ($ret) {
					$msRow[$heroId] = $upInfo;
				} else {
					$err[] = $heroId;
				}
			}
			M_Sync::addQueue($cityId, M_Sync::KEY_HERO, $msRow); //同步军官数据到前端
		}

		$ret = (count($err) == 0) ? true : false;
		if (!$ret) {
			Logger::error(array(__METHOD__, 'Err Change HeroId', array($err, func_get_args())));
		}
		return $ret;
	}

	/**
	 * 配兵
	 * @author huwei
	 * @param int $cityId 城市ID (方便封装同步到前端的数据)
	 * @param int $heroId 英雄ID
	 * @param int $armyNum 兵种数量
	 * @param int $armyId 兵种ID
	 * @param int $weaponId 武器ID
	 * @param bool
	 */
	static public function updateFitArmy($cityId, $heroId, $armyNum, $armyId, $weaponId) {
		$info = array(
			'army_num'  => $armyNum,
			'army_id'   => $armyId,
			'weapon_id' => $weaponId,
		);

		$ret = M_Hero::setHeroInfo($heroId, $info, true);

		$msRow = array($heroId => $info);
		$ret && M_Sync::addQueue($cityId, M_Sync::KEY_HERO, $msRow); //同步军官数据到前端

		return $ret;
	}

	/**
	 * 删除我拥有的英雄
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @param array $heroInfo 英雄信息
	 * @return bool
	 */
	static public function delCityHeroInfo($cityId, $heroInfo) {
		$ret = false;

		if (!empty($heroInfo['id']) && $heroInfo['flag'] == T_Hero::FLAG_FREE) {
			$heroId = $heroInfo['id'];
			$ret    = M_Hero::delCityHeroList($cityId, $heroId);
			if ($ret) {
				$bDel = B_DB::instance('CityHero')->delete($heroId);
				M_Hero::delHeroInfo($heroId);

				unset($heroInfo['skill_army_num']);
				unset($heroInfo['on_sale']);
				unset($heroInfo['sys_is_del']);
				unset($heroInfo['march_id']);
				unset($heroInfo['skill_lead']);
				unset($heroInfo['skill_command']);
				unset($heroInfo['skill_military']);
				unset($heroInfo['skill_energy']);
				unset($heroInfo['equip_lead']);
				unset($heroInfo['equip_command']);
				unset($heroInfo['equip_military']);
				unset($heroInfo['weapon_id']);
				unset($heroInfo['flag']);
				unset($heroInfo['army_id']);
				unset($heroInfo['army_num']);
				unset($heroInfo['equip_arm']);
				unset($heroInfo['equip_cap']);
				unset($heroInfo['equip_uniform']);
				unset($heroInfo['equip_medal']);
				unset($heroInfo['equip_shoes']);
				unset($heroInfo['equip_sit']);

				$tmp = array();
				foreach ($heroInfo as $key => $val) {
					if ($key == 'recycle_next') {
						$val = implode("_", $val);
					}
					$tmp[] = "{$key}:{$val}";
				}

				Logger::opHero($cityId, $heroId, Logger::H_ACT_FIRE, implode(';', $tmp));
			}
		}
		return $ret;
	}

	/**
	 * 获取寻将系统中的 英雄信息
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @return array
	 */
	static public function getSeekInfo($cityId) {
		$MSH = new M_CityCollege();
		$ret = $MSH->getData($cityId);
		$now = time();
		//寻访次数
		$find_num = $ret['find_num'];
		//寻将重置时间间隔 2中模式[12/24] (清空)
		$confDiff = M_Config::getVal('hero_find_interval');
		if ($confDiff == 12) {
			$mode = 'mda';
		} else {
			$mode = 'md';
		}

		if (date($mode, $ret['last_find_time']) != date($mode)) {
			$find_num = 0;
		}

		if (!empty($ret['end_time']) && $now < $ret['end_time']) { //如果没到期 则状态为进行中
			$ret['flag']           = T_Hero::FIND_FLAG_PROC;
			$ret['succ_keep_time'] = 0;
		}
		$ret['find_num'] = $find_num;
		return $ret;
	}

	/**
	 * 清空寻将系统中的 英雄信息
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @return bool
	 */
	static public function emptySeekInfo($cityId) {
		$row = array(
			'city_id'        => $cityId,
			'hero_tpl_id'    => 0,
			'hire_time'      => 0,
			'succ_rate'      => 0,
			'is_pay'         => 0,
			'time_props_id'  => 0,
			'rate_props_id'  => 0,
			'start_time'     => 0,
			'end_time'       => 0,
			'succ_keep_time' => 0,
			'flag'           => 0,
		);
		$MCC = new M_CityCollege();
		$ret = $MCC->setData($cityId, $row, true);
		return $ret;
	}

	/**
	 * 获取 英雄模板中的信息
	 * @author huwei
	 * @param int $heroTplId 英雄模板ID
	 * @return array
	 */
	static public function baseInfo($heroTplId) {
		$apcKey = T_Key::BASE_HERO_TPL_INFO . '_' . $heroTplId;
		$info   = B_Cache_APC::get($apcKey);
		if (empty($info)) {
			$heroInfo = B_DB::instance('BaseHeroTpl')->get($heroTplId);
			if (!empty($heroInfo['id'])) {
				$info = $heroInfo;
				M_Skill::getBaseEffectByHero($info);
				M_Equip::getBaseEffectByHero($info);

				APC::set($apcKey, $info);
			}
		}
		return $info;

	}


	/**
	 * 更新一个新的寻将数据
	 * @author huwei
	 * @param int $cityId
	 * @param int $collegeLv
	 * @param int $findNum
	 * @param int $payType
	 * @param int $now
	 * @return array/string
	 */
	static public function updateSeekInfo($cityId, $findNum, $payType, $now, $heroInfo) {
		$errNo = '';
		$ret   = false;
		//生成一个传奇英雄
		if (!empty($heroInfo['id'])) {
			$fields = array(
				'city_id'        => $cityId,
				'hero_tpl_id'    => $heroInfo['id'],
				'hire_time'      => $heroInfo['hire_time'],
				'succ_rate'      => $heroInfo['succ_rate'],
				'time_props_id'  => 0,
				'rate_props_id'  => 0,
				'is_pay'         => empty($payType) ? 0 : $payType,
				'last_find_time' => $now,
				'start_time'     => 0,
				'end_time'       => 0,
				'succ_keep_time' => 0,
				'cd_time'        => 0,
				'flag'           => T_Hero::FIND_FLAG_INIT,
				'find_num'       => max($findNum + 1, 0),
			);

			//跟新寻将系统中
			$MCC    = new M_CityCollege();
			$isSucc = $MCC->setData($cityId, $fields, true);
			if ($isSucc) {
				$ret = $heroInfo;
			}
		} else {
			$msg = array(__METHOD__, T_ErrNo::HERO_TPL_NO_DATA, func_get_args());
			Logger::debug($msg);
		}

		return $ret;
	}

	/**
	 * 把寻将数据更新为开始寻将
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @param int $timePropsId 减少时间道具ID
	 * @param int $ratePropsId 增加成功率道具ID
	 * @return bool
	 */
	static public function startSeekInfo($cityId, $timePropsId, $ratePropsId, $hireTime, $succRate) {
		$now = time();
		//通过概率 计算是否成功
		$heroConf = M_Config::getVal();
		$bSucc    = B_Utils::odds($succRate);
		$keepTime = $bSucc ? ($now + $hireTime + $heroConf['hero_succ_keep_time'] * T_App::ONE_HOUR) : 0;
		$flag     = $bSucc ? T_Hero::FIND_FLAG_SUCC : T_Hero::FIND_FLAG_FAIL;
		$fields   = array(
			'city_id'        => $cityId,
			'time_props_id'  => $timePropsId,
			'rate_props_id'  => $ratePropsId,
			'hire_time'      => $hireTime,
			'succ_rate'      => $succRate,
			'start_time'     => $now,
			'end_time'       => $now + $hireTime,
			'flag'           => $flag, //更新标志
			'succ_keep_time' => $keepTime,
		);

		$MCC = new M_CityCollege();
		$ret = $MCC->setData($cityId, $fields, true);
		return $ret;
	}

	/**
	 * 修改英雄名称
	 * @author huwei
	 * @param array $fields 字段数组
	 * @param int $cityId 城市ID
	 * @param int $payType 付费类型
	 * @return array
	 */
	static public function modifyName($fields) {
		//扣礼券或军饷
		$ret = false;
		if (!empty($fields['city_id']) &&
			!empty($fields['id']) &&
			!empty($fields['nickname'])
		) {
			$fieldArr['nickname'] = trim($fields['nickname']);
			$ret                  = M_Hero::setHeroInfo($fields['id'], $fieldArr);
		}

		return $ret;

	}

	/**
	 * 重置英雄属性点
	 * @author huwei
	 * @param array $fields 属性点相关字段
	 * @return bool
	 */
	static public function resetAttrPoint($fields) {
		$ret = false;
		if (!empty($fields['id']) && !empty($fields['city_id'])) {
			$cityId = intval($fields['city_id']);
			$heroId = intval($fields['id']);

			unset($fields['id']);
			unset($fields['city_id']);
			$fieldArr = $fields;
			$ret      = M_Hero::setHeroInfo($heroId, $fieldArr);
		}
		return $ret;
	}


	/**
	 * 检查属性点是否正确
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @param int $heroId 英雄ID
	 * @param int $addL 增加的统帅点数
	 * @param int $addC 增加的指挥点数
	 * @param int $addM 增加的军事点数
	 * @param bool
	 */
	static public function updateAttrPoint($cityId, $heroId, $addL, $addC, $addM) {
		$errNo = T_ErrNo::HERO_ERR_POINT;

		$heroInfo = M_Hero::getCityHeroInfo($cityId, $heroId);
		//分配点数
		$totalAddPoint = intval($addL) + intval($addC) + intval($addM);

		if (!empty($heroInfo['id'])) {

			//分配点数
			$tmpLPoint = intval($heroInfo['attr_lead']) + intval($addL);
			$tmpCPoint = intval($heroInfo['attr_command']) + intval($addC);
			$tmpMPoint = intval($heroInfo['attr_military']) + intval($addM);

			//未分配点数
			$statPoint = floor($heroInfo['stat_point']);
			//已有点数
			$baseLPoint = floor($heroInfo['attr_lead']);
			$baseCPoint = floor($heroInfo['attr_command']);
			$baseMPoint = floor($heroInfo['attr_military']);
			$basePoint  = $baseLPoint + $baseCPoint + $baseMPoint;
			//总点数
			$totalPoint = $basePoint + $statPoint;
			$maxPoint   = floor($totalPoint * 0.5);
			$isLOK      = $isCOK = $isMOK = false;
			if ($statPoint > 0 && $totalAddPoint <= $statPoint) {
				$isLOK = $tmpLPoint <= $maxPoint;
				$isCOK = $tmpCPoint <= $maxPoint;
				$isMOK = $tmpMPoint <= $maxPoint;
			}

			//Logger::dev("英雄配点#".json_encode(array($isLOK,$isCOK,$isMOK)));

			if ($isLOK && $isCOK && $isMOK) {
				$addedPoint = $totalAddPoint;
				$fields     = array(
					'id'            => $heroId,
					'city_id'       => $cityId,
					'attr_lead'     => $tmpLPoint,
					'attr_command'  => $tmpCPoint,
					'attr_military' => $tmpMPoint,
					'stat_point'    => max($heroInfo['stat_point'] - $totalAddPoint, 0),
				);
				$ret        = M_Hero::setHeroInfo($heroId, $fields);
				unset($fields['id']);
				unset($fields['city_id']);
				M_Sync::addQueue($cityId, M_Sync::KEY_HERO, array($heroId => $fields)); //同步数据!

				if ($ret) {
					$errNo = '';
				} else {
					$msg = __METHOD__ . ':' . T_ErrNo::HERO_UPDATE_POINT_FAIL . ':' . json_encode(func_get_args());
					Logger::debug($msg);
				}
			}
		}
		return $errNo;
	}

	static public function addCityHero($cityId, $heroTplId, $fillArmy = false) {
		$id       = 0;
		$heroInfo = M_Hero::baseInfo($heroTplId);
		if (!empty($heroInfo['nickname'])) {

			$armyNum = 0;
			if ($fillArmy) {
				$maxArmyNum = M_Formula::calcHeroMaxArmyNum($heroInfo['level']); //计算各兵种最大带兵数
				$armyNum    = $maxArmyNum[$heroInfo['army_id']];
			}

			$fields = array(
				'nickname'          => $heroInfo['nickname'],
				'gender'            => $heroInfo['gender'],
				'quality'           => $heroInfo['quality'],
				'level'             => $heroInfo['level'],
				'face_id'           => $heroInfo['face_id'],
				'exp'               => $heroInfo['exp'],
				'is_legend'         => 1,
				'army_id'           => $heroInfo['army_id'],
				'army_num'          => $armyNum,
				'weapon_id'         => T_Hero::$army2weapon[$heroInfo['army_id']],
				'attr_lead'         => $heroInfo['attr_lead'],
				'attr_command'      => $heroInfo['attr_command'],
				'attr_military'     => $heroInfo['attr_military'],
				'training_lead'     => 0,
				'training_command'  => 0,
				'training_military' => 0,
				'attr_energy'       => $heroInfo['attr_energy'],
				'attr_mood'         => $heroInfo['attr_mood'],
				'stat_point'        => $heroInfo['stat_point'],
				'grow_rate'         => $heroInfo['grow_rate'],
				'equip_arm'         => $heroInfo['equip_arm'],
				'equip_cap'         => $heroInfo['equip_cap'],
				'equip_uniform'     => $heroInfo['equip_uniform'],
				'equip_shoes'       => $heroInfo['equip_shoes'],
				'equip_sit'         => $heroInfo['equip_sit'],
				'skill_slot_num'    => $heroInfo['skill_slot_num'],
				'skill_slot'        => $heroInfo['skill_slot'],
				'skill_slot_1'      => $heroInfo['skill_slot_1'],
				'skill_slot_2'      => $heroInfo['skill_slot_2'],
				'create_at'         => time(),
				'city_id'           => $cityId
			);

			$id = B_DB::instance('CityHero')->insert($fields);
			$id && M_Hero::setCityHeroList($cityId, $id);
		}
		return $id;
	}


	/**
	 * 复制模板中的英雄数据到城市英雄表
	 * @author huwei
	 * @param int $cityId
	 * @param int $heroTplId
	 * @param int $flag 0寻将 1VIP抽取 2奖励
	 * @return bool
	 */
	static public function moveTplHeroToCityHero(O_Player $objPlayer, $heroTplId, $actType = B_Logger::H_ACT_FIND) {
		$ret = false;

		$cityInfo = $objPlayer->getCityBase();

		$id = self::addCityHero($cityInfo['id'], $heroTplId);
		if ($id) {

			$heroInfo = M_Hero::getHeroInfo($id);

			$heroArmyNumAdd = M_Hero::heroArmyNumAdd($heroInfo['city_id'], $cityInfo['union_id']);

			$heroInfo['max_army_num'] = M_Formula::calcHeroMaxArmyNum($heroInfo['level'], $heroInfo['skill_army_num'], $heroArmyNumAdd); //计算各兵种最大带兵数
			$heroInfo['exp_next']     = M_Formula::getGrowExp($heroInfo['level']);
			$heroInfo['in_team']      = 0;

			Logger::opHero($cityInfo['id'], $id, $actType, $heroInfo['nickname']);

			M_Sync::addQueue($cityInfo['id'], M_Sync::KEY_HERO, array($id => $heroInfo)); //同步数据!

			if ($heroInfo['quality'] >= T_Hero::HERO_PURPLE_LEGEND) { //超级将领系统公告
				$skillName = '';
				$faceId    = 0;
				$desc      = '';
				if (!empty($fields['skill_slot'])) {
					$skillInfo = M_Skill::getBaseInfo($fields['skill_slot']);
					$skillName = $skillInfo['name'];
					$faceId    = $skillInfo['face_id'];
					$desc      = $skillInfo['desc'];
				}

				$msgArr  = array(
					'Type'         => 1,
					'NickName'     => $cityInfo['nickname'],
					'HeroId'       => (int)$heroInfo['id'],
					'HeroName'     => $heroInfo['nickname'],
					'Gender'       => $heroInfo['gender'],
					'FaceId'       => $heroInfo['face_id'],
					'Quality'      => (int)$heroInfo['quality'],
					'GrowRate'     => $heroInfo['grow_rate'], //$growAttr,
					'NextExp'      => $heroInfo['exp_next'],
					'AttrLead'     => $heroInfo['attr_lead'] ? (int)$heroInfo['attr_lead'] : 0,
					'AttrCommand'  => $heroInfo['attr_command'] ? (int)$heroInfo['attr_command'] : 0,
					'AttrMilitary' => $heroInfo['attr_military'] ? (int)$heroInfo['attr_military'] : 0,
					'AttrEnergy'   => $heroInfo['attr_energy'] ? (int)$heroInfo['attr_energy'] : 0,
					'SkillSlot'    => $skillName,
					'SkillFaceId'  => $heroInfo['skill_slot'],
					'SkillDesc'    => $desc,
					'SkillSlotNum' => (int)$heroInfo['skill_slot_num'],
					'From'         => $actType,
					'Recycle'      => (int)$heroInfo['recycle'],
				);
				$heroStr = '{' . implode("\t", $msgArr) . '}';
				if ($actType == Logger::H_ACT_FIND) {
					$titleLang = T_Lang::GET_LEGEND_HERO_FIND;
				} elseif ($actType == Logger::H_ACT_VIP) {
					$titleLang = T_Lang::GET_LEGEND_HERO_VIP;
				} elseif ($actType == Logger::H_ACT_LOTTERY) {
					$titleLang = T_Lang::GET_LEGEND_HERO_LOTTERY;
				} elseif ($actType == Logger::H_HERO_EXCHANGE) {
					$titleLang = T_Lang::GET_LEGEND_HERO_EXCHANGE;
				} else {
					$titleLang = T_Lang::GET_LEGEND_HERO_AWARD;
				}


				$title = json_encode(array($titleLang, $cityInfo['nickname'], $heroStr));
				$msg   = implode("\t", array($title, T_Chat::SYS_RADIO_PRIO, T_Chat::SYS_RADIO_STAY_TIME));
				//Logger::debug(array(__METHOD__, $titleLang, $actType));
				M_Chat::addWorldMessage(uniqid(), $msg, T_Chat::CHAT_SYS);
			}
			$ret = $id;
		}
		return $ret;
	}

	/**
	 * 清除英雄招募CD时间
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @param int $cdTime 冷却时间
	 * @return bool
	 */
	static public function clearHeroFindCDTime($cityId, $cdTime = 0) {
		$ret = false;
		if (!empty($cdTime) && $cdTime < time()) {
			$fields = array(
				'city_id' => $cityId,
				'cd_time' => '',
			);
			$MCC    = new M_CityCollege();
			$row    = $MCC->setData($cityId, $fields, true);
			!$ret && Logger::debug(array(__METHOD__, T_ErrNo::HERO_CLEAR_CDTIME_FAIL, func_get_args()));
		}
		return $ret;
	}

	/**
	 * 获取军队出征信息
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @param array $heroIdList 英雄ID数组(heroId1,heroId2,heroId3)
	 * @return array (基础速度,基础消耗油,基础消食物)
	 */
	static public function getArmyMarchInfo($objPlayer, $heroIdList) {
		$type = array(
			M_Army::ID_FOOT  => array(M_Tech::ID_FOOT_S, 'FOOT_INCR_SP'),
			M_Army::ID_GUN   => array(M_Tech::ID_GUN_S, 'GUN_INCR_SP'),
			M_Army::ID_ARMOR => array(M_Tech::ID_ARMOR_S, 'ARMOR_INCR_SP'),
			M_Army::ID_AIR   => array(M_Tech::ID_AIR_S, 'AIR_INCR_SP'),
		);

		$ret    = false;
		$cityId = $objPlayer->City()->id;
		if (!empty($cityId) && !empty($heroIdList)) {
			$heroList = M_Hero::getCityHeroList($cityId);
			$err      = $costOil = $costFood = $armySpeed = array();

			//道具行军速度加成
			$propsSpeedAdd = $objPlayer->Props()->getEffectVal('ARMY_INCR_SPEED');;
			$minSpeed = 10000;

			foreach ($heroIdList as $heroId) {
				$heroInfo = M_Hero::getCityHeroInfo($cityId, $heroId);

				//检测是否带兵和是否装备武器
				if (!empty($heroInfo['army_num']) &&
					!empty($heroInfo['army_id']) && //检测是否带兵
					!empty($heroInfo['weapon_id']) && //检测是否装备武器
					$heroInfo['flag'] == T_Hero::FLAG_FREE //英雄状态是否空闲
				) {
					$armyInfo   = M_Army::baseInfo($heroInfo['army_id']);
					$weaponInfo = M_Weapon::baseInfo($heroInfo['weapon_id']);

					$costOil[$heroId]  = $heroInfo['army_num'] * $weaponInfo['march_cost_oil'];
					$costFood[$heroId] = $heroInfo['army_num'] * $weaponInfo['march_cost_food'];

					//武器的基础速度
					$weaponSpeed = $weaponInfo['speed'];

					//获取科技行军速度加成
					$techSpeedAdd = $objPlayer->Tech()->calcArmyTechSpeed($heroInfo['army_id']);

					//计算兵种行军速度
					$armySpeed = $weaponSpeed * (1 + $techSpeedAdd / 100 + $propsSpeedAdd / 100);
					$minSpeed  = min($armySpeed, $minSpeed); //取最小的值
				} else {
					$err[] = $heroId;
				}
			}

			if (count($err) == 0) {
				$ret = array($minSpeed, array_sum($costOil), array_sum($costFood));
			}
			//Logger::debug(array(__METHOD__, $err, $ret));
		}
		return $ret;
	}

	/**
	 * 英雄穿戴装备
	 * @author HeJunyun on 20110602
	 * @param int $heroId 英雄ID
	 * @param int $cityId 城市ID
	 * @param string $equipPos 装备位置
	 * @param int $cityEquipId 玩家城市装备ID
	 * @param string $isWear 是否穿 (ture穿 false卸)
	 * @return int $errNo  错误编号
	 */
	static public function heroSetEquip($heroInfo, $posField, $cityEquipId, $isWear) {
		$cityId = $heroInfo['city_id'];
		$heroId = $heroInfo['id'];
		$errNo  = '';

		if (!$isWear) { //以下为卸装备
			if (!M_Hero::setHeroInfo($heroId, array($posField => 0))) { //英雄对应位置设空
				$errNo = T_ErrNo::HERO_UNEQUIP_FAIL;
			}
		} else { //以下为穿装备
			$fieldArr = array(
				'city_id' => $cityId,
				'is_use'  => $heroId,
			);

			if (!M_Hero::setHeroInfo($heroId, array($posField => $cityEquipId))) {
				$errNo = T_ErrNo::HERO_EQUIP_FAIL;
			} else if (!M_Equip::setInfo($cityEquipId, $fieldArr)) { //修改装备使用状态
				$errNo = T_ErrNo::EQUIP_STATE_FALL;
			}
		}

		if (empty($errNo)) {
			$n = $isWear ? -1 : 1;
			M_Equip::incrCityEquipNum($cityId, $n);

			$oldEquipId = isset($heroInfo[$posField]) ? $heroInfo[$posField] : 0;
			if ($oldEquipId > 0) { //英雄指定部位已有装备
				$errNo = T_ErrNo::EQUIP_STATE_FALL;
				//修改原有装备状态——>0未使用
				$fieldArr = array(
					'city_id' => $cityId,
					'is_use'  => 0,
				);
				if (M_Equip::setInfo($oldEquipId, $fieldArr)) {
					M_Equip::syncRemoveEquip($cityId, $oldEquipId);
					$errNo = '';
				}
			}
		}

		if (!empty($errNo)) {
			Logger::debug(array(__METHOD__, $errNo, func_get_args()));
		}

		return $errNo;
	}

	/**
	 * 获取被攻击时 出战的部队
	 * 如果设置出战部队 则出战的优先,如果没有设置 则随机抽取部队
	 * @author huwei 20110622
	 * @param int $cityId
	 * @param int $type 轰炸 1/攻击2
	 * @return array    英雄id列表
	 */
	static public function getFightHeroList($cityId, $type = T_Hero::FIGHT_ATK) {
		$result   = $fightList = array();
		$heroList = M_Hero::getCityHeroList($cityId);
		$fight    = 0;
		if (!empty($heroList)) {
			foreach ($heroList as $heroId) {
				$heroInfo = M_Hero::getCityHeroInfo($cityId, $heroId);
				if (T_Hero::FLAG_FREE == $heroInfo['flag'] &&
					!empty($heroInfo['weapon_id']) &&
					!empty($heroInfo['army_id']) &&
					!empty($heroInfo['army_num'])
				) {
					//判断是否有设置当前类型的 出战部队
					if (!empty($heroInfo['fight'])) {
						$f = (int)$heroInfo['fight'];
						($f & $type) > 0 && $fight++;
					}

					if ($type == T_Hero::FIGHT_BOMB && M_Army::ID_AIR == $heroInfo['army_id']) {
						//所有的空闲 防空袭部队
						$fightList[] = $heroInfo;
					} else {
						//所有的空闲 防攻击部队
						$fightList[] = $heroInfo;
					}
				}
			}
		}

		if (!empty($fightList)) {
			//如果没有设置出战部队
			($fight == 0) && shuffle($fightList);
		}
		$heroConf = M_Config::getVal();
		$totalNum = count($fightList);
		$addNum   = 0;
		for ($i = 0; $i < $totalNum; $i++) {
			if (isset($fightList[$i])) {
				$info = $fightList[$i];
				//有设置出战部队  并且 部队已设置出战
				if ($fight > 0 && $info['fight'] == $type && $addNum < $heroConf['hero_num_troop']) {
					$addNum++;
					$result[] = $info['id'];
				} else if ($fight == 0 && $addNum < $heroConf['hero_num_troop']) { //没有有设置出战部队
					$addNum++;
					$result[] = $info['id'];
				}
			}
		}

		return $result;
	}


	/**
	 * 构建英雄战场数据(单个英雄 单个兵 单个武器的数值)
	 * @author huwei on 20110701
	 * @param int $heroId 英雄ID
	 * @param int $cityId 城市ID (npc为0)
	 * @return array
	 */
	static public function buildHeroBattleInfo($heroId, $cityData) {
		$data     = false;
		$cityId   = !empty($cityData['cityId']) ? $cityData['cityId'] : 0;
		$heroInfo = !empty($cityId) ? M_Hero::getHeroInfo($heroId) : M_NPC::getNpcHeroInfo($heroId);

		if (!empty($heroInfo)) {
			$armyInfo   = M_Army::baseInfo($heroInfo['army_id']);
			$weaponInfo = M_Weapon::baseInfo($heroInfo['weapon_id']);

			$heroInfo['total_lead']     = $heroInfo['attr_lead'] + $heroInfo['training_lead'];
			$heroInfo['total_command']  = $heroInfo['attr_command'] + $heroInfo['training_command'];
			$heroInfo['total_military'] = $heroInfo['attr_military'] + $heroInfo['training_military'];
			$heroInfo['total_energy']   = $heroInfo['attr_energy'];

			$log['total_lead']     = $heroInfo['attr_lead'];
			$log['total_command']  = $heroInfo['attr_command'];
			$log['total_military'] = $heroInfo['attr_military'];
			//Logger::dev("HeroId#{$heroInfo['id']};Start Total:".json_encode($log));

			$equip = array('equip_arm', 'equip_cap', 'equip_uniform', 'equip_medal', 'equip_shoes', 'equip_sit');
			foreach ($equip as $val) {
				if (!empty($heroInfo[$val])) {
					$equipInfo = M_Equip::getCityEquipById($cityId, $heroInfo[$val]);
					if ($equipInfo['id']) {
						$heroInfo['total_lead'] += $equipInfo['base_lead'];
						$heroInfo['total_command'] += $equipInfo['base_command'];
						$heroInfo['total_military'] += $equipInfo['base_military'];
					}
				}
			}

			$log['total_lead']     = $heroInfo['total_lead'];
			$log['total_command']  = $heroInfo['total_command'];
			$log['total_military'] = $heroInfo['total_military'];
			//Logger::dev("HeroId#{$heroInfo['id']};End Equip Total:".json_encode($log));


			if (!empty($cityId)) {
				//npc英雄无技能
				//技能基础属性
				$heroInfo['total_lead'] += $heroInfo['skill_lead'];
				$heroInfo['total_command'] += $heroInfo['skill_command'];
				$heroInfo['total_military'] += $heroInfo['skill_military'];
				$heroInfo['total_energy'] += $heroInfo['skill_energy'];

				$heroInfo['total_lead'] += $heroInfo['equip_lead'];
				$heroInfo['total_command'] += $heroInfo['equip_command'];
				$heroInfo['total_military'] += $heroInfo['equip_military'];

				$log['total_lead']     = $heroInfo['total_lead'];
				$log['total_command']  = $heroInfo['total_command'];
				$log['total_military'] = $heroInfo['total_military'];
				//Logger::dev("HeroId#{$heroInfo['id']};End Skill Total:".json_encode($log));

				//获取兵种基础等级加成
				$armyList     = $cityData['cityArmy'];
				$cityArmyInfo = isset($armyList[$heroInfo['army_id']]) ? $armyList[$heroInfo['army_id']] : array();
				$armyLv       = $cityArmyInfo['level'];
			} else {
				//npc兵种等级
				$armyLv = $heroInfo['army_lv'];
			}
			$armyInfo['att_land']   = M_Formula::getArmyBaseValByLv($armyInfo['att_land'], $armyLv);
			$armyInfo['att_sky']    = M_Formula::getArmyBaseValByLv($armyInfo['att_sky'], $armyLv);
			$armyInfo['def_land']   = M_Formula::getArmyBaseValByLv($armyInfo['def_land'], $armyLv);
			$armyInfo['def_sky']    = M_Formula::getArmyBaseValByLv($armyInfo['def_sky'], $armyLv);
			$armyInfo['life_value'] = M_Formula::getArmyBaseValByLv($armyInfo['life_value'], $armyLv);

			$base = $armyInfo['cost_gold'] + $armyInfo['cost_food'] + $armyInfo['cost_oil'];

			$data = $heroInfo;

			//计算英雄基础攻击力

			$data['is_legend'] = 1;

			$data['att_land'] = $weaponInfo['att_land'] + $armyInfo['att_land'];
			$data['att_sky']  = $weaponInfo['att_sky'] + $armyInfo['att_sky'];
			$data['def_land'] = $weaponInfo['def_land'] + $armyInfo['def_land'];
			$data['def_sky']  = $weaponInfo['def_sky'] + $armyInfo['def_sky'];
			//计算英雄基础生命力
			$data['life_value'] = $weaponInfo['life_value'] + $armyInfo['life_value'];

			//计算总价值
			$data['total_value'] = M_Formula::calcArmyRecruitCost($base, $armyLv);

			//获取兵种科技加成
			$data['tech_add'] = M_Battle_Calc::getArmyTechAdd($cityData['cityTech'], $heroInfo['army_id']);
			//获取道具加成
			$data['props_add'] = M_Battle_Calc::getBattleAddByCityPorps($cityData['cityUsingProps']);

			//军衔加成
			$data['rank_add'] = $cityData['cityRankAdd'];

			//洲加成
			$data['zone_add'] = M_Battle_Calc::getBattleAddByCityZone($cityData['zone'], $heroInfo['army_id']);

			//VIP加成
			$data['vip_add'] = $cityData['cityVipAdd'];

			//联盟加成
			$data['union_add'] = $cityData['cityUnionAdd'];

			//获取技能加成
			//array(几率,[所有0|空1|地2],加成)
			$skillInfo = M_Skill::getBattleEffectByHero($heroInfo);

			$equipInfo = M_Equip::getBattleEffectByHero($heroInfo);

			$data['view_range']     = $weaponInfo['view_range'];
			$data['move_range']     = $weaponInfo['move_range'];
			$data['shot_range_min'] = $weaponInfo['shot_range_min'];
			$data['shot_range_max'] = $weaponInfo['shot_range_max'];

			if (isset($skillInfo['INCR_RGE'])) { //加视野
				$data['view_range'] += $skillInfo['INCR_RGE'][1];
				//$data['total_energy'] -= $skillInfo['DECR_AN'][6];
			}
			if (isset($skillInfo['INCR_MVE'])) { //加移动力
				$data['move_range'] += $skillInfo['INCR_MVE'][1];
				//$data['total_energy'] -= $skillInfo['DECR_AN'][6];
			}
			if (isset($skillInfo['INCR_SHT'])) { //加攻击范围
				$data['shot_range_min'] += $skillInfo['INCR_SHT'][1];
				$data['shot_range_max'] += $skillInfo['INCR_SHT'][1];
				//$data['total_energy'] -= $skillInfo['DECR_AN'][6];
			}

			if (isset($skillInfo['DECR_RGE'])) { //降低视野
				$data['view_range'] -= $skillInfo['DECR_RGE'][1];
				//$data['total_energy'] -= $skillInfo['DECR_AN'][6];
			}
			if (isset($skillInfo['DECR_MVE'])) { //降低移动力
				$data['move_range'] -= $skillInfo['DECR_MVE'][1];
				//$data['total_energy'] -= $skillInfo['DECR_AN'][6];
			}
			if (isset($skillInfo['DECR_SHT'])) { //降低攻击范围
				$data['shot_range_min'] -= $skillInfo['DECR_SHT'][1];
				$data['shot_range_max'] -= $skillInfo['DECR_SHT'][1];
				//$data['total_energy'] -= $skillInfo['DECR_AN'][6];
			}

			$data['skill_add'] = $skillInfo;
			$data['equip_add'] = $equipInfo;
			//获取武器的相关信息
			$data['add_effect'] = $weaponInfo['add_effect'];
			$data['move_type']  = $weaponInfo['move_type'];
			$data['shot_type']  = $weaponInfo['shot_type'];
			$data['carry']      = $weaponInfo['carry'];
			//剩余兵数
			$data['left_num'] = $heroInfo['army_num'];
			//每回合 记录对被攻击的伤害累计
			$data['left_dmg'] = 0;
			//每回合 记录对被攻击的持续伤害值
			$data['atk_hurt'] = 0;

			unset($armyInfo['cost_gold']);
			unset($armyInfo['cost_food']);
			unset($armyInfo['cost_oil']);
			unset($armyInfo['cost_people']);
			unset($armyInfo['desc_1']);
			unset($armyInfo['desc_2']);
			unset($armyInfo['features']);
			$data['army_info'] = $armyInfo;

			unset($weaponInfo['att_num']);
			unset($weaponInfo['cost_gold']);
			unset($weaponInfo['cost_food']);
			unset($weaponInfo['cost_oil']);
			unset($weaponInfo['cost_time']);
			unset($weaponInfo['march_cost_oil']);
			unset($weaponInfo['march_cost_food']);
			unset($weaponInfo['need_tech']);
			unset($weaponInfo['need_build']);
			unset($weaponInfo['add_effect']);
			unset($weaponInfo['need_army_lv']);
			unset($weaponInfo['detail']);
			unset($weaponInfo['features']);
			$data['weapon_info'] = $weaponInfo;
			$weaponInfo          = null;
			$armyInfo            = null;
			$heroInfo            = null;
			$skillInfo           = null;
		}

		return $data;
	}


	/**
	 * 战斗结束后累加英雄经验并修改其状态
	 * @author HeJunyun on 20110711
	 * @param int $cityId
	 * @param array $heroData array(0=>array('id'=>英雄ID,'exp'=>增长经验,'flag'=>状态),1=>array()...)
	 * @param int $status 战斗结果标记 [1失败 2平局 3胜利]
	 * @return bool
	 */
	static public function setHeroExpAndFlag($cityId, $heroData, $status = '') {
		$ret     = array();
		$sysData = array();

		foreach ($heroData as $key => $val) {
			$heroId   = $val['id'];
			$exp      = $val['exp'];
			$flag     = $val['flag'];
			$army_num = $val['army_num'];

			$heroInfo = M_Hero::getHeroInfo($heroId);

			$level = $heroInfo['level']; //当前等级
			$maxLv = M_Config::getVal('hero_maxlv'); //英雄最大等级

			$leftExp = M_Hero::fillEquipExp($heroInfo['equip_exp'], $exp);
			$heroExp = $heroInfo['exp'] + $leftExp; //英雄战后经验值

			if ($level < $maxLv) {
				$needExp = M_Formula::getGrowExp($level); //升级所需经验

				while ($heroExp >= $needExp) {
					$tmpLv   = $level;
					$level   = $level + 1; //等级提升1
					$heroExp = $heroExp - $needExp; //扣除升级经验
					$needExp = M_Formula::getGrowExp($tmpLv); //升级所需经验
					if ($level >= $maxLv) { //最大100级
						break;
					}
				}
			}


			$tmpArr = array(
				'id'          => $heroId,
				'level'       => $level,
				'exp'         => $heroExp,
				'flag'        => $flag,
				'army_num'    => $army_num,
				'relife_time' => isset($val['relife_time']) ? $val['relife_time'] : 0,
			);

			if (isset(M_Battle_Calc::$resultStatus[$status])) {
				$field          = M_Battle_Calc::$resultStatus[$status];
				$tmpArr[$field] = $heroInfo[$field] + 1;
			}

			if ($level > $heroInfo['level']) {
				$heroInfo['level'] = $level;
				$heroInfo['exp']   = $heroExp;

				$heroAttr = M_Hero::incrHeroAttr($heroInfo); //自动分配英雄属性
				if ($heroAttr) {
					$tmpArr['attr_lead']     = $heroAttr['attr_lead'];
					$tmpArr['attr_command']  = $heroAttr['attr_command'];
					$tmpArr['attr_military'] = $heroAttr['attr_military'];
				}
				//升级后最大带兵数
				$cityInfo       = M_City::getInfo($cityId);
				$heroArmyNumAdd = M_Hero::heroArmyNumAdd($cityId, $cityInfo['union_id']);

				$tmpArr['max_army_num'] = M_Formula::calcHeroMaxArmyNum($level, $heroInfo['skill_army_num'], $heroArmyNumAdd);
				$tmpArr['exp_next']     = M_Formula::getGrowExp($level);
			}

			$result = M_Hero::updateInfo($tmpArr);

			if ($result) {
				$syncData[$heroId] = $tmpArr;
				M_Sync::addQueue($cityId, M_Sync::KEY_HERO, $syncData);
			} else {
				$msg = array(__METHOD__, 'Update Fail', $tmpArr);
				Logger::error($msg);
			}
			$ret[] = $result;
		}

		return $ret;
	}

	/**
	 * 设置防御出战军官
	 * @author Hejunyun
	 * @param int $heroId 军官ID
	 * @param int $fight 出战类型（1被轰炸时出战 2被攻击时出战）
	 * @return int $errNo 错误类型
	 */
	static public function setHeroFight($heroId, $fight) {
		$heroInfo  = M_Hero::getHeroInfo($heroId);
		$heroFight = $heroInfo['fight'] | $fight;
		$data      = array(
			'fight' => $heroFight
		);
		$ret       = M_Hero::setHeroInfo($heroId, $data);

		$syncData = array($heroId => $data);
		$ret && M_Sync::addQueue($heroInfo['city_id'], M_Sync::KEY_HERO, $syncData);
		return $ret;
	}


	/**
	 * 取消军官防御出战
	 * @author Hejunyun
	 * @param int $heroId 军官ID
	 * @param int $fight 出战类型（1被轰炸时 2被攻击时）
	 * @return int $errNo 错误类型
	 */
	static public function cancelHeroFight($heroId, $fight) {
		$heroInfo = M_Hero::getHeroInfo($heroId);
		if (isset($heroInfo['id'])) {
			if ($heroInfo['fight'] == $fight || $heroInfo['fight'] == 3) {
				$heroFight = $heroInfo['fight'] ^ $fight;
				$data      = array(
					'fight' => $heroFight
				);
				$ret       = M_Hero::setHeroInfo($heroId, $data);
				$syncData  = array($heroId => $data);
				$ret && M_Sync::addQueue($heroInfo['city_id'], M_Sync::KEY_HERO, $syncData);
				return $ret;
			} else {
				return true;
			}
		} else {
			return false;
		}

	}


	/**
	 * 检测英雄是否达到出征要求
	 * @author huwei
	 * @param array $heroIdList
	 * @return bool
	 */
	static public function checkHeroStatus($cityId, $heroIdList) {
		$okArr = array();
		if (is_array($heroIdList) && !empty($heroIdList)) {
			foreach ($heroIdList as $heroId) {
				$heroInfo = M_Hero::getCityHeroInfo($cityId, $heroId);
				//检测是否带兵和是否装备武器
				if (!empty($heroInfo['army_num'])
					&& !empty($heroInfo['army_id']) //检测是否带兵
					&& !empty($heroInfo['weapon_id']) //检测是否装备武器
					&& $heroInfo['flag'] == T_Hero::FLAG_FREE
				) { //英雄状态是否空闲
					$okArr[] = $heroId;
				}
			}
		}

		$okSize   = count($okArr);
		$heroSize = count($heroIdList);

		return $okSize > 0 && $okSize == $heroSize;
	}

	/**
	 * 自动补充英雄兵力
	 * @author huwei on 20111129
	 * @param array $heroIds
	 * @return bool
	 */
	static public function fillHeroArmyNumByHeroId($cityId, $heroIds, $force = 0) {
		$ret = false;
		//Logger::debug(array(__METHOD__,"CityId#{$cityId};Hero Ids:".json_encode($heroIds)));
		if (!empty($cityId) && !empty($heroIds) && is_array($heroIds)) {
			$objPlayer = new O_Player($cityId);
			$objArmy   = $objPlayer->Army();
			$objWeapon = $objPlayer->instance('Weapon');

			//Logger::debug($cityArmyInfo);
			$heroArmyNumAdd = M_Hero::heroArmyNumAdd($cityId, $objPlayer->City()->union_id);
			foreach ($heroIds as $heroId) {
				$heroInfo = M_Hero::getCityHeroInfo($cityId, $heroId);

				//Logger::debug(array(__METHOD__, "army_id:{$heroInfo['army_id']},army_id:{$heroInfo['army_id']},flag:{$heroInfo['flag']},fill_flag:{$heroInfo['fill_flag']}"));

				//Logger::dev("HeroId#{$heroId};".json_encode($heroInfo));

				$newWeaponId = $heroInfo['weapon_id'];

				if (!$objWeapon->hasWeapon($heroInfo['weapon_id'])) {
					M_Sync::addQueue($cityId, M_Sync::KEY_WEAPON_TEMP, array($heroInfo['weapon_id'] => M_Sync::DEL));
					$newWeaponId = 0;

					$info  = array(
						'weapon_id' => 0,
					);
					$ret   = M_Hero::setHeroInfo($heroId, $info, true);
					$msRow = array($heroId => $info);
					$ret && M_Sync::addQueue($cityId, M_Sync::KEY_HERO, $msRow); //同步军官数据到前端
				}

				//Logger::debug(array(__METHOD__, $heroInfo['id'], $newWeaponId, $heroInfo['army_id'], $heroInfo['weapon_id'], $heroInfo['flag'],$heroInfo['fill_flag']));

				$needFill = false;
				if ($force) {
					$needFill = true;
				} else if ($heroInfo['fill_flag'] == T_Hero::AUTO_FILL_ARMY) {
					$needFill = true;
				}

				//英雄已配兵种 并 英雄状态为空闲
				if (!empty($heroInfo['army_id']) &&
					!empty($heroInfo['weapon_id']) &&
					$heroInfo['flag'] == T_Hero::FLAG_FREE &&
					$needFill
				) {
					$maxArmyNumArr = M_Formula::calcHeroMaxArmyNum($heroInfo['level'], $heroInfo['skill_army_num'], $heroArmyNumAdd);
					//Logger::debug($maxArmyNumArr);
					$armyId = $heroInfo['army_id'];

					//城市拥有士兵数
					list($tmpNum, $tmpLv, $tmpExp) = $objArmy->getById($armyId);
					//需要分配的兵数
					$heroNeedArmyNum = $maxArmyNumArr[$armyId] - $heroInfo['army_num'];

					//有未分配兵种 并 当前所带兵数 小于可带兵数
					if ($tmpNum > 0 && $heroNeedArmyNum > 0) {
						//英雄可分配士兵数量
						$add            = min($tmpNum, $heroNeedArmyNum);
						$heroAddArmyNum = $heroInfo['army_num'] + $add;

						$objArmy->addNum($armyId, -$add);

						//Logger::debug(array(__METHOD__, "heroAddArmyNum#{$heroAddArmyNum};cityLeftArmyNum#{$cityLeftArmyNum}"));
						//Logger::debug(array(__METHOD__, $cityId, $heroId, $heroAddArmyNum, $heroInfo['army_id'], $newWeaponId));
						$bUp = M_Hero::updateFitArmy($cityId, $heroId, $heroAddArmyNum, $heroInfo['army_id'], $newWeaponId);
					}
				}
			}

			$ret = $objPlayer->save();
			if ($ret) {
				$msRow = $objArmy->get();
				M_Sync::addQueue($cityId, M_Sync::KEY_ARMY, $msRow);
			}
		}
		return $ret;
	}

	/**
	 * 自动补充所有英雄的兵力
	 * @author huwei
	 * @param int $cityId
	 * @return bool
	 */
	static public function fillAllHeroArmyNum($cityId) {
		$ret      = false;
		$heroList = M_Hero::getCityHeroList($cityId);
		$ret      = self::fillHeroArmyNumByHeroId($cityId, $heroList);
		return $ret;
	}


	/**
	 * 获取内存中的城市英雄信息
	 * @author huwei on 2011108
	 * @param int $cityId 城市ID
	 * @param int $heroId 英雄ID
	 * @return array
	 */
	static public function getCityHeroInfo($cityId, $heroId) {
		$ret    = false;
		$heroId = intval($heroId);
		if ($heroId > 0) {
			$heroInfo = M_Hero::getHeroInfo($heroId);
			if ($heroInfo['city_id'] == $cityId) {
				$ret = $heroInfo;
			}
		}

		return $ret;
	}

	/**
	 * 获取内存中的英雄信息
	 * @author huwei on 2011108
	 * @param int $heroId 英雄ID
	 * @return array
	 */
	static public function getHeroInfo($heroId) {
		$ret    = false;
		$heroId = intval($heroId);
		if ($heroId > 0) {
			$rc  = new B_Cache_RC(T_Key::CITY_HERO_INFO, $heroId);
			$ret = $rc->hmget(T_DBField::$cityHeroFields);
			if (empty($ret['id'])) {
				$heroInfo = B_DB::instance('CityHero')->get($heroId);
				if (!empty($heroInfo)) {
					$ret = self::setHeroInfo($heroId, $heroInfo, false);
				}
			}
		}
		$data = false;
		if (!empty($ret['id'])) {
			if ($ret['flag'] == T_Hero::FLAG_DIE && $ret['relife_time'] <= time()) { //复活军官
				$bUp = M_Hero::changeHeroFlag($ret['city_id'], array($heroId), T_Hero::FLAG_FREE, array('march_id' => 0));
				if ($bUp) {
					$ret['flag']        = T_Hero::FLAG_FREE;
					$ret['relife_time'] = 0;
				}
			}

			M_Skill::getBaseEffectByHero($ret);
			M_Equip::getBaseEffectByHero($ret);

			$ret['training_lead']     = !empty($ret['training_lead']) ? $ret['training_lead'] : 0;
			$ret['training_command']  = !empty($ret['training_command']) ? $ret['training_command'] : 0;
			$ret['training_military'] = !empty($ret['training_military']) ? $ret['training_military'] : 0;

			$recycleCfgArr       = M_Hero::getHeroRecycle();
			$nextRecycleLv       = $ret['recycle'] + 1;
			$ret['recycle_next'] = isset($recycleCfgArr[$nextRecycleLv]) ? $recycleCfgArr[$nextRecycleLv] : array();
			$data                = $ret;
		}

		return $data;
	}

	/**
	 * 删除城市英雄信息key
	 * @author huwei on 2011108
	 * @param int $heroId 英雄ID
	 * @return bool
	 */
	static public function delHeroInfo($heroId) {
		$rc  = new B_Cache_RC(T_Key::CITY_HERO_INFO, $heroId);
		$ret = $rc->delete();
		return $ret;
	}

	/**
	 * 更新城市英雄信息
	 * @author huwei on 2011108
	 * @param int $heroId 英雄ID
	 * @param array $fieldArr 需要更新的英雄数据字段数组 例:array('flag'=>1,'army_num'=>100,....)
	 * @param bool $isUp 是否更新到DB
	 * @return array
	 */
	static public function setHeroInfo($heroId, $fieldArr, $upDB = true) {
		$ret = false;
		if (!empty($heroId) && is_array($fieldArr) && !empty($fieldArr)) {
			$info = array();
			foreach ($fieldArr as $key => $val) {
				if (!empty($key) && in_array($key, T_DBField::$cityHeroFields)) {
					$info[$key] = $val;
				}
			}
			if (!empty($info)) {
				$rc  = new B_Cache_RC(T_Key::CITY_HERO_INFO, $heroId);
				$ret = $rc->hmset($info, T_App::ONE_DAY);
				if ($ret) {
					$upDB && M_CacheToDB::addQueue(T_Key::CITY_HERO_INFO . ':' . $heroId);
				} else {
					$msg = array(__METHOD__, 'Update Hero Info Fail', func_get_args());
					Logger::error($msg);
				}
			}
		}

		return $ret ? $info : false;
	}

	/**
	 * 添加城市英雄列表
	 * @author huwei on 20111008
	 * @param int $cityId 城市ID
	 * @param int $heroId 英雄ID
	 * @return bool
	 */
	static public function setCityHeroList($cityId, $heroId) {
		$ret    = false;
		$cityId = intval($cityId);
		$heroId = intval($heroId);
		if (!empty($cityId) && !empty($heroId)) {
			$rc = new B_Cache_RC(T_Key::CITY_HERO_LIST, $cityId);
			if ($rc->sismember($heroId)) {
				$ret = true;
			} else {
				$ret = $rc->sadd($heroId);
				if (!$ret) {
					Logger::error(array(__METHOD__, "Err Add City Hero List", func_get_args()));
				}
			}


		}
		return $ret;
	}

	/**
	 * 获取城市英雄列表
	 * @author huwei on 20111008
	 * @param int $cityId 城市ID
	 * @return array    返回城市英雄id的数组 array(1,2,34)
	 */
	static public function getCityHeroList($cityId) {
		$ret    = array();
		$cityId = intval($cityId);
		if (!empty($cityId)) {
			$rc  = new B_Cache_RC(T_Key::CITY_HERO_LIST, $cityId);
			$ret = $rc->smembers();
			if (empty($ret)) {
				$heroList = B_DB::instance('CityHero')->getsBy(array('city_id' => $cityId));
				foreach ($heroList as $val) {
					$rc->sadd($val['id']);
					$ret[] = $val['id'];
				}
			}

		}
		return $ret;
	}

	/**
	 * 删除城市的英雄
	 * @author huwei on 20111008
	 * @param int $cityId 城市ID
	 * @param int $heroId 英雄ID
	 * @return bool
	 */
	static public function delCityHeroList($cityId, $heroId) {
		$ret    = false;
		$cityId = intval($cityId);
		$heroId = intval($heroId);
		if (!empty($cityId) && !empty($heroId)) {
			$rc = new B_Cache_RC(T_Key::CITY_HERO_LIST, $cityId);
			if ($rc->sismember($heroId)) {
				$ret = $rc->srem($heroId);
				if (!$ret) {
					Logger::error(array(__METHOD__, 'Err del', func_get_args()));
				}
			} else {
				$ret = true;
			}
		}
		return $ret;
	}

	/**
	 * 如果将领丢失   战斗中的英雄
	 * @author huwei
	 * @param int $cityId
	 * @return array
	 */
	static public function getBattleHeroIds($cityId) {
		//如果副本中英雄列表
		$bdList = M_Battle_List::getBattleIdByCity($cityId);
		$mArr   = array();
		if (!empty($bdList)) {
			foreach ($bdList as $bdData) {
				if ($bdData['is_def'] > 0 && !empty($bdData['def_hero_list'])) {
					$hids = json_decode($bdData['def_hero_list'], true);
				} else if (!empty($bdData['atk_hero_list'])) {
					$hids = json_decode($bdData['atk_hero_list'], true);
				}

				if (!empty($hids) && is_array($hids)) {
					$mArr = array_merge($hids, $mArr);
				}
			}
		}

		return $mArr;
	}

	/**
	 * 如果将领丢失   行军中的英雄
	 * @author huwei
	 * @param int $cityId
	 * @return array
	 */
	static public function getMarchHeroIds($cityId) {
		//如果行军中英雄列表
		$list = M_March::getMarchList($cityId, M_War::MARCH_OWN_ATK);

		$mArr = array();
		foreach ($list as $mInfo) {
			$hids = json_decode($mInfo['hero_list'], true);
			if (is_array($hids)) {
				foreach ($hids as $hId) {
					if ($mInfo['flag'] == M_March::MARCH_FLAG_HOLD) {
						$mArr[$hId] = T_Hero::FLAG_HOLD;
					} else {
						$mArr[$hId] = T_Hero::FLAG_MOVE;
					}
				}
			}
		}
		return $mArr;
	}

	/**
	 * 卸掉装备和配兵
	 * @author huwei on 20120302
	 * @param int $cityId 城市ID
	 * @param int $heroId 城市军官ID
	 */
	static public function clearHero($cityInfo, $heroInfo, $cleanCityId = false) {
		$ret = false;
		if ($cityInfo['id'] > 0 && !empty($heroInfo['id'])) {
			$cityId = $cityInfo['id'];
			$heroId = $heroInfo['id'];

			$objPlayer = new O_Player($cityId);
			$objArmy   = $objPlayer->Army();

			//设置装备为空闲状态
			$equipIds = array();
			$heroInfo['equip_arm'] && $equipIds[] = $heroInfo['equip_arm'];
			$heroInfo['equip_cap'] && $equipIds[] = $heroInfo['equip_cap'];
			$heroInfo['equip_uniform'] && $equipIds[] = $heroInfo['equip_uniform'];
			$heroInfo['equip_medal'] && $equipIds[] = $heroInfo['equip_medal'];
			$heroInfo['equip_shoes'] && $equipIds[] = $heroInfo['equip_shoes'];
			$heroInfo['equip_sit'] && $equipIds[] = $heroInfo['equip_sit'];
			$heroInfo['equip_exp'] && $equipIds[] = $heroInfo['equip_exp'];
			if (!empty($equipIds) && is_array($equipIds)) {
				$syncData = array();
				$incrNum  = 0;
				foreach ($equipIds as $equipId) {
					$equiInfo = M_Equip::getInfo($equipId);
					M_Equip::setInfo($equipId, array('city_id' => $cityId, 'is_use' => T_Equip::EQUIP_NOT_USE));
					$equiInfo['is_use']       = T_Equip::EQUIP_NOT_USE;
					$equiInfo['hero_name']    = '';
					$equiInfo['hero_quality'] = '';
					$equiInfo['_0']           = M_Sync::SET;
					$syncData[$equipId]       = $equiInfo;
					$incrNum++;
				}
				$incrNum > 0 && M_Equip::incrCityEquipNum($cityId, $incrNum);

				M_Sync::addQueue($cityId, M_Sync::KEY_EQUIP, $syncData); //同步数据
			}
			//卸下军官装备和配兵,返还预备兵
			if ($heroInfo['army_num'] > 0) {
				$armyId = $heroInfo['army_id'];
				$objArmy->addNum($armyId, $heroInfo['army_num']);

				$msRow[$armyId] = $objArmy->getById($armyId);
				M_Sync::addQueue($cityId, M_Sync::KEY_ARMY, $msRow);
			}


			//设置军官属性
			$newHeroInfo = array(
				'equip_arm'     => 0,
				'equip_cap'     => 0,
				'equip_uniform' => 0,
				'equip_medal'   => 0,
				'equip_shoes'   => 0,
				'equip_sit'     => 0,
				'equip_exp'     => 0,
				'army_num'      => 0,
			);

			if ($cleanCityId) {
				$newHeroInfo['city_id'] = 0;
			}

			$ret = M_Hero::setHeroInfo($heroId, $newHeroInfo);

			if ($ret) {
				if ($cleanCityId) {
					M_Hero::delCityHeroList($cityId, $heroId);
					M_Sync::addQueue($cityId, M_Sync::KEY_HERO, array($heroId => M_Sync::DEL)); //同步数据
				} else {
					//升级后最大带兵数
					$heroArmyNumAdd              = M_Hero::heroArmyNumAdd($cityId, $cityInfo['union_id']);
					$newHeroInfo['max_army_num'] = M_Formula::calcHeroMaxArmyNum($heroInfo['level'], $heroInfo['skill_army_num'], $heroArmyNumAdd);
					M_Sync::addQueue($cityId, M_Sync::KEY_HERO, array($heroId => $newHeroInfo));
				}
			} else {
				Logger::error(array(__METHOD__, 'fail for sale hero', array(func_get_args(), $heroInfo)));
			}
		}
		return $ret;
	}

	/**
	 * 解雇军官
	 * @author chenhui on 20120302
	 * @param int $cityId 城市ID
	 * @param array $heroInfo 城市军官信息
	 */

	static public function fireHero($cityInfo, $heroInfo) {
		$ret = false;
		if (!empty($cityInfo['id']) > 0 && !empty($heroInfo['id'])) {
			$bClear = M_Hero::clearHero($cityInfo, $heroInfo, true);
			if ($bClear) {
				$ret = M_Hero::delCityHeroInfo($cityInfo['id'], $heroInfo);
			}
		}
		return $ret;
	}

	/**
	 * 自动分配英雄属性点
	 * @author Hejunyun
	 * @param int $cityId 城市ID
	 * @param int $heroId 英雄ID
	 */
	static public function incrHeroAttr($heroInfo) {
		$fields   = array();
		$curPoint = $heroInfo['grow_rate'] * $heroInfo['level'];
		$total    = array_sum(array($heroInfo['attr_lead'], $heroInfo['attr_command'], $heroInfo['attr_military']));
		$newL     = floor(($heroInfo['attr_lead'] / $total) * $curPoint);
		$newC     = floor(($heroInfo['attr_command'] / $total) * $curPoint);
		$newM     = $curPoint - $newL - $newC;

		$fields = array(
			'attr_lead'     => $newL,
			'attr_command'  => $newC,
			'attr_military' => $newM,
		);
		return $fields;
	}

	/**
	 * 获取军官培养未确定的临时属性点
	 * @author Hejunyun
	 * @param int $heroId 军官ID
	 * @return array
	 */
	static public function getTmpTraining($heroId) {
		$rc     = new B_Cache_RC(T_Key::HERO_TRAINING_TMP, $heroId);
		$tmpArr = $rc->jsonget();
		if (!$tmpArr) {
			$tmpArr = array(
				'training_lead'     => 0,
				'training_command'  => 0,
				'training_military' => 0
			);
		}
		return $tmpArr;
	}

	/**
	 * 保存军官培养未确定的临时属性点(保留1天)
	 * @author Hejunyun
	 * @param int $heroId 军官ID
	 * @param array $setArr 刷出的属性点
	 * @return bool
	 */
	static public function setTmpTraining($heroId, $setArr) {
		$rc  = new B_Cache_RC(T_Key::HERO_TRAINING_TMP, $heroId);
		$ret = $rc->jsonset($setArr, T_App::ONE_DAY);
		return $ret;
	}

	static public function delTmpTraining($heroId) {
		$rc = new B_Cache_RC(T_Key::HERO_TRAINING_TMP, $heroId);
		return $rc->delete();
	}

	/**
	 * 计算军官培养上限
	 * @author Hejunyun
	 * @param int $level 军官等级
	 * @param int $quality 军官品质
	 * @return int $value
	 */
	static public function getTrainingMaxValue($level, $quality, $recycle = 0) {
		$c = 0;
		if ($recycle > 0) {
			$level = M_Config::getVal('hero_maxlv');
			$c     = $recycle * T_Hero::$recycleAdd;
		}

		$trainLimit = M_Config::getVal('hero_train_limit');
		$rate       = isset($trainLimit[$quality]) ? $trainLimit[$quality] : $trainLimit[T_Hero::HERO_BULE_LEGEND];

		$a     = $level * 3;
		$b     = ceil($level / $rate);
		$value = $a + $b + $c;
		//Logger::debug(array(__METHOD__,$level, $rate, $a ,$b ,$c, $value));
		return $value;
	}


	/**
	 * 获取属性点下降后的值
	 * @author Hejunyun
	 * @param int $attr 原培养属性值
	 * @param int $type 培养类型
	 */
	static public function getDownValue($attr, $type) {
		$attr = intval($attr);
		$type = intval($type);
		if ($type && $attr) {
			$parm1 = T_Hero::$trainingDown[$type][0];
			$parm2 = T_Hero::$trainingDown[$type][1];
			$parm  = rand($parm1, $parm2) / 100;
			$value = floor($attr * (1 - $parm));
			//Logger::debug(array(__METHOD__, 'getDownValue', $parm, $parm1, $parm2, $attr, $value));
			return max($value, 0);
		}
		return 0;
	}

	/** 获取某类型第几次培养 */
	static public function calcTrainNum($num, $type) {
		$confHeroTrain = M_Config::getVal('hero_train');
		if ($type == T_Hero::TRAINING_TYPE_ONE) {
			$tmpNum = min($num + 1, $confHeroTrain['max'][$type]);
		} else {
			$num = min($num, $confHeroTrain['max'][$type]);
			//免费次数
			$freeNum = M_Config::getVal('hero_train_free');
			$tmpNum  = max($num + 1 - $freeNum, 0);
		}
		return $tmpNum;
	}

	/** 获取各类型已培养次数 */
	static public function getTrainingNum($cityId) {
		$now       = date('Ymd');
		$tmpFields = array(T_Hero::TRAINING_TYPE_ONE, T_Hero::TRAINING_TYPE_TWO, T_Hero::TRAINING_TYPE_THREE, 'time');
		$tmpRet    = array(0, 0, 0, $now);

		$rc  = new B_Cache_RC(T_Key::HERO_TRAINING_TIMES, $cityId);
		$ret = $rc->hmget($tmpFields);
		if ($ret) {
			if ($ret['time'] != $now) {
				$ret = array_combine($tmpFields, $tmpRet);
				$rc->hmset($ret, T_App::ONE_DAY);
			}
		} else {
			$ret = array_combine($tmpFields, $tmpRet);
		}
		return $ret;
	}

	/** 设置某类型培养次数 */
	static public function setTrainingNum($cityId, $type) {
		$now       = date('Ymd');
		$tmpFields = array(T_Hero::TRAINING_TYPE_ONE, T_Hero::TRAINING_TYPE_TWO, T_Hero::TRAINING_TYPE_THREE, 'time');
		$tmpRet    = array(0, 0, 0, $now);

		$rc   = new B_Cache_RC(T_Key::HERO_TRAINING_TIMES, $cityId);
		$data = $rc->hmget($tmpFields);

		if (isset($data['time']) && $data['time'] == $now) {
			$ret = $data;
		} else {
			$ret = array_combine($tmpFields, $tmpRet);
		}

		if (isset($ret[$type])) {
			$ret[$type] = $ret[$type] + 1;
		}

		$ret['time'] = $now;
		$result      = $rc->hmset($ret, T_App::ONE_DAY);
		return $ret;
	}

	static public function trainingNeed($type, $num) {
		$need = 0;
		if ($type == 1) {
			$need = $num * 100;
		} elseif ($type == 2) {
			$need = ($num - 1) * 2;
			$need = min($need, 50);
		} elseif ($type == 3) {
			$need = ($num - 1) * 5;
			$need = min($need, 100);
		}
		return $need;
	}

	static public function heroArmyNumAdd($cityId, $unionId = 0) {
		$vipAdd = $unionAdd = 0;
		if (!empty($cityId)) {
			$heroConf = M_Config::getVal();

			$objPlayer = new O_Player($cityId);
			//VIP功能增加军官带兵上限
			$vipAdd = $objPlayer->Vip()->getVal('HERO_INCR_ARMY');
			if ($unionId > 0) {
				$unionInfo = M_Union::getInfo($unionId);
				$unionAdd  = M_Union::getUnionTechAddition($unionInfo, M_Union::TECH_ARMY_NUM);
			}
		}

		return array('vip_add' => $vipAdd, 'union_add' => $unionAdd);
	}

	/**
	 * 填充装备经验
	 * @param int $equipExpId 装备ID
	 * @param int $exp 经验
	 * @return int
	 */
	static public function fillEquipExp($equipExpId, $exp) {
		$leftExp = $exp;
		if (!empty($equipExpId) && $leftExp > 0) {
			$equipInfo = M_Equip::getInfo($equipExpId);
			if (!empty($equipInfo['id'])) {
				$max = $equipInfo['ext_attr_name'];
				$cur = $equipInfo['ext_attr_rate'];
				//Logger::debug(array(__METHOD__, $equipInfo['city_id'], $max, $cur));
				$leftStroe = $max - $cur;

				if ($leftStroe > 0) {
					if ($leftStroe > $exp) {
						$hasExp  = $cur + $exp;
						$leftExp = 0;
					} else {
						$hasExp  = $max;
						$leftExp = $exp - $leftStroe;
					}

					$upArr = array('ext_attr_rate' => intval($hasExp));
					//Logger::debug(array(__METHOD__, $upArr));
					M_Equip::setInfo($equipInfo['id'], $upArr);

					$upArr['_0'] = M_Sync::SET;
					M_Sync::addQueue($equipInfo['city_id'], M_Sync::KEY_EQUIP, array($equipInfo['id'] => $upArr)); //同步数据!
				}
			}
		}
		return $leftExp;
	}

	static public function getHeroRecycle() {
		$ret    = array();
		$cfgStr = M_Config::getVal('hero_recycle');
		if (!empty($cfgStr)) {
			$cfgArr = explode("|", $cfgStr);
			$i      = 1;
			foreach ($cfgArr as $val) {
				$tmp     = explode(",", $val);
				$ret[$i] = $tmp;
				$i++;
			}
		}
		return $ret;
	}

	static public function getHeroRecycleAttr() {
		$ret    = array();
		$cfgStr = M_Config::getVal('hero_recycle_attr');
		if (!empty($cfgStr)) {
			$cfgArr = explode("|", $cfgStr);
			$i      = 1;
			foreach ($cfgArr as $val) {
				$tmp = explode(",", $val);
				$t   = array();
				$n   = 1;
				foreach ($tmp as $v) {
					$t[$n] = $v;
					$n++;
				}
				$ret[$i] = $t;
				$i++;
			}
		}
		return $ret;
	}
}

?>
<?php

/**
 * 装备模型层
 */
class M_Equip {
	/**
	 * 根据城市ID获取装备列表(未装备的)
	 * @author HeJunyun on 20110531
	 * @author huwei modify on 20111014
	 * @param int $cityId 城市ID
	 * @param int $vip
	 * @return array $list 装备列表
	 */
	/** 未绑定状态 */
	const UNBINDING = 0;
	/** 绑定状态 */
	const BINDING = 1;
	/** 可以合成 */
	const FLAG_FUSIONING = 1;
	/** 可以升级 */
	const FLAG_UPGRADE = 2;
	/** 可以合强化*/
	const FLAG_STRENGTHEN = 4;
	static $bindingType = array(
		self::UNBINDING => '未绑定',
		self::BINDING   => '已绑定',
	);

	/**
	 * 获取城市所有装备信息
	 * @param int $cityId
	 */
	static public function getEquipListForAdm($cityId) {
		$list   = false;
		$cityId = intval($cityId);
		if ($cityId > 0) {
			$idArr = self::getCityEquipList($cityId);
			if (!empty($idArr)) {
				foreach ($idArr as $equipId) {
					$info              = M_Equip::getInfo($equipId);
					$list[$info['id']] = $info;
				}
			}
		}
		return $list;
	}

	static public function getEquipTplList() {
		$list = M_Base::equipAll();
		return $list['ids'];
	}

	/**
	 * 根据ID获取装备模板
	 * @author HeJunyun on 20110531
	 * @param int $tplId 模板ID
	 * @return  array 一维数组 装备信息
	 */
	static public function baseInfo($tplId) {
		$apcKey = T_Key::BASE_EQUIP_TPL_INFO . '_' . $tplId;
		$info   = B_Cache_APC::get($apcKey);
		if (empty($info)) {
			$info = B_DB::instance('BaseEquipTpl')->get($tplId);
			APC::set($apcKey, $info);
		}
		return $info;
	}

	/**
	 * 根据ID获取城市装备信息
	 * @author HeJunyun on 20110602
	 * @author huwei modify on 20111014
	 * @param int $cityId
	 * @param int $equipId
	 * @return array 城市装备信息
	 */
	static public function getCityEquipById($cityId, $equipId) {
		$ret     = false;
		$cityId  = intval($cityId);
		$equipId = intval($equipId);
		if ($cityId > 0 && $equipId > 0) {
			$info = M_Equip::getInfo($equipId);
			if (!empty($info['id']) && $info['city_id'] == $cityId) {
				$ret = $info;
			}
		}
		return $ret;
	}

	/**
	 * 给城市添加装备
	 * @author HeJunyun on 20110531
	 * @author huwei modify on 20111014
	 * @param array $fieldArr
	 * @param int $id 新增ID
	 */
	static public function addCityEquip($fieldArr) {
		$ret = false;
		if (!empty($fieldArr) && is_array($fieldArr)) {
			$equipId = B_DB::instance('CityEquip')->insert($fieldArr);
			if ($equipId) {
				M_Equip::incrCityEquipNum($fieldArr['city_id'], 1);
				$listUp                   = self::_setCityEquipList($fieldArr['city_id'], $equipId);
				$syncData                 = M_Equip::getInfo($equipId);
				$syncData['hero_name']    = '';
				$syncData['hero_quality'] = '';
				$syncData['_0']           = M_Sync::ADD;
				M_Sync::addQueue($fieldArr['city_id'], M_Sync::KEY_EQUIP, array($equipId => $syncData)); //同步数据!
				$ret = $equipId;
				Logger::opEquip($fieldArr['city_id'], $equipId, Logger::E_ACT_GET, $syncData['name']);
			}
		}
		return $ret;
	}

	/**
	 * 添加系统装备
	 * @author Hejunyun
	 * @param int $cityId 城市ID
	 * @param int $level 穿戴等级
	 * @param int $pos 位置
	 * @param int $quality 品质
	 */
	static public function makeEquip($cityId, $tplInfo = array(), $binding = M_Equip::UNBINDING) {
		$ret    = false;
		$cityId = intval($cityId);
		if ($cityId && $tplInfo['name']) {
			$info = array(
				'city_id'       => $cityId,
				'name'          => $tplInfo['name'],
				'pos'           => $tplInfo['pos'],
				'face_id'       => $tplInfo['face_id'],
				'type'          => 1,
				'need_level'    => $tplInfo['need_level'],
				'level'         => 0,
				'max_level'     => M_Config::getVal('strong_equip_max_level'),
				'quality'       => $tplInfo['quality'],
				'base_lead'     => $tplInfo['base_lead'],
				'base_command'  => $tplInfo['base_command'],
				'base_military' => $tplInfo['base_military'],
				'ext_attr_name' => $tplInfo['ext_attr_name'],
				'is_use'        => 0,
				'suit_id'       => isset($tplInfo['suit_id']) ? $tplInfo['suit_id'] : 0,
				'flag'          => $tplInfo['flag'],
				'is_locked'     => $binding,
				'create_at'     => time(),
			);

			$ret = M_Equip::addCityEquip($info);
		}
		return $ret;
	}

	/**
	 * 删除城市装备
	 * @author HeJunyun on 20110601
	 * @param int $equipId 城市装备ID
	 * @param int $cityId 城市ID
	 * @return bool
	 */
	static public function delCityEquip($equipId, $cityId) {
		$ret     = false;
		$cityId  = intval($cityId);
		$equipId = intval($equipId);
		if ($equipId > 0 && $cityId > 0) {
			$ret = M_Equip::delCityEquipList($cityId, $equipId);
			if ($ret) {
				$bInfoDel = M_Equip::delInfo($equipId);
				M_Sync::addQueue($cityId, M_Sync::KEY_EQUIP, array($equipId => M_Sync::DEL)); //同步数据!
			} else {
				Logger::error(array(__METHOD__, 'del city equip fail', func_get_args()));
			}
		}
		return $ret;
	}

	/**
	 * 获取装备本身强化成功率
	 * @author Hejunyun
	 * @param int $equipLevel 装备等级
	 * @return int $equipSuccRate 成功百分比
	 */
	static public function getEquipStrongSuccRate($equipLevel) {
		//装备本身成功率   成功率=100-（当前等级*2）≥100%取值100%
		//$equipSuccRate = intval(100 - ($equipLevel * 2));
		//return $equipSuccRate;
		if ($equipLevel < 11) { //0＜X≤10,成功率Y1=100%-(X-1)*2%；X为等级
			$equipSuccRate = intval(100 - (($equipLevel - 1) * 2));
		} elseif ($equipLevel < 21) { //当10＜X≤20,成功率Y=82%-(X-10)*2.5%
			$equipSuccRate = intval(82 - (($equipLevel - 10) * 2.5));
		} elseif ($equipLevel < 31) { //当20＜X≤30,成功率Y=57%-(X-20)*3%；X为等级
			$equipSuccRate = intval(57 - (($equipLevel - 20) * 3));
		} elseif ($equipLevel < 41) { //当30＜X≤40,成功率Y=27%-(X-30)*2%；X为等级
			$equipSuccRate = intval(27 - (($equipLevel - 30) * 2));
		} elseif ($equipLevel < 51) { //当40＜X≤50,成功率Y=7%-(X-40)*0.5%；X为等级
			$equipSuccRate = intval(7 - (($equipLevel - 40) * 0.5));
		}
		return $equipSuccRate;
	}

	/**
	 * 获取城市装备数量(已使用的装备不计算在容量范围内)
	 * @author huwei
	 * @param int $cityId
	 * @param int $vip
	 * @param array
	 */
	static public function getCityEquipNum($cityId, $sync = false) {
		$ret    = 0;
		$cityId = intval($cityId);
		if ($cityId > 0) {
			$rc  = new B_Cache_RC(T_Key::CITY_EQUIP_NUM, $cityId);
			$ret = $rc->get();
			if (empty($ret) || $sync) {
				$num   = 0;
				$idArr = self::getCityEquipList($cityId);
				if (!empty($idArr)) {
					foreach ($idArr as $equipId) {
						$info = M_Equip::getInfo($equipId);
						if (!empty($info['id']) && $info['city_id'] == $cityId) {
							//$list[$info['id']] = $info;
							//已使用的装备不计算在容量范围内
							if ($info['is_use'] == T_Equip::EQUIP_NOT_USE) {
								$num++;
							}
						}
					}
				}
				$rc->set($num, T_App::ONE_WEEK);
				$ret = $num;
			}
			//Logger::debug(array(__METHOD__, $cityId, $ret));
		}

		return $ret;
	}

	/**
	 * 更新城市装备数量
	 * @author huwei
	 * @param int $cityId
	 * @param int $num
	 * @param array
	 */
	static public function setCityEquipNum($cityId, $num) {
		$ret    = false;
		$cityId = intval($cityId);
		if ($cityId > 0) {
			$rc  = new B_Cache_RC(T_Key::CITY_EQUIP_NUM, $cityId);
			$ret = $rc->set($num, T_App::ONE_WEEK);
		}
		return $ret;
	}

	/**
	 * 增加城市装备数量
	 * @author huwei
	 * @param int $cityId
	 * @param int $num
	 * @param array
	 */
	static public function incrCityEquipNum($cityId, $num) {
		$ret    = false;
		$cityId = intval($cityId);
		if ($cityId > 0 && $num != 0) {
			$rc  = new B_Cache_RC(T_Key::CITY_EQUIP_NUM, $cityId);
			$ret = $rc->incrBy($num);
		}
		return $ret;
	}

	/**
	 * 判断某城市装备数量是否满
	 * @author chenhui on 20120315
	 * @param int $cityId
	 * @param int $vipLevel 城市VIP等级
	 * @return bool [false为未满]
	 */
	static public function isEquipNumFull($cityId, $vipLevel = 0) {
		$ret = false;
		if (empty($vipLevel)) {
			$cityInfo = M_City::getInfo($cityId);
			$vipLevel = $cityInfo['vip_level'];
		}

		$vipConf     = M_Vip::getVipConfig();
		$maxEquipNum = !empty($vipLevel) ? $vipConf['PACK_EQUI'][$vipLevel] : $vipConf['PACK_EQUI'][0];
		$curEquipNum = M_Equip::getCityEquipNum($cityId);
		//echo "max= $maxEquipNum | cur= $curEquipNum";
		if ($curEquipNum >= $maxEquipNum) {
			$ret = true;
		}
		return $ret;
	}

	/**
	 * 获取城市装备列表
	 * @author huwei on 20111014
	 * @param int $cityId
	 * @return array
	 */
	static public function getCityEquipList($cityId) {
		$ret    = false;
		$cityId = intval($cityId);
		if ($cityId > 0) {
			$rc  = new B_Cache_RC(T_Key::CITY_EQUIP_LIST, $cityId);
			$ret = $rc->smembers();
			if (empty($ret)) {
				//获取当前城市装备列表
				$list = B_DB::instance('CityEquip')->getsBy(array('city_id' => $cityId));
				foreach ($list as $info) {
					$rc->sAdd($info['id']);
					$ret[] = $info['id'];
				}
				$rc->expire(T_App::ONE_WEEK);

				if (!empty($ret)) {
					sort($ret);
				}
			}
		}
		return $ret;
	}

	/**
	 * 添加城市装备列表
	 * @author huwei on 20111014
	 * @param int $cityId
	 * @param int $equipId
	 * @return array
	 */
	static private function _setCityEquipList($cityId, $equipId) {
		$ret      = false;
		$cityId   = intval($cityId);
		$reportId = intval($equipId);
		if ($cityId > 0 && $equipId > 0) {
			$rc = new B_Cache_RC(T_Key::CITY_EQUIP_LIST, $cityId);
			if (!$rc->exists()) {
				self::getCityEquipList($cityId);
			}

			if ($rc->sismember($equipId)) {
				$ret = true;
			} else {
				$ret = $rc->sadd($equipId);
			}

			$rc->expire(T_App::ONE_DAY);
			if (!$ret) {
				Logger::error(array(__METHOD__, 'add city equip list', func_get_args()));
			}
		}
		return $ret;
	}

	/**
	 * 删除城市装备列表
	 * @author huwei on 20111014
	 * @param int $cityId
	 * @param int $equipId
	 * @return array
	 */
	static public function delCityEquipList($cityId, $equipId) {
		$ret      = false;
		$cityId   = intval($cityId);
		$reportId = intval($equipId);
		if ($cityId > 0 && $equipId > 0) {
			$rc = new B_Cache_RC(T_Key::CITY_EQUIP_LIST, $cityId);
			if ($rc->sismember($equipId)) {
				$ret = $rc->srem($equipId);
				if (!$ret) {
					Logger::error(array(__METHOD__, 'del equip id', func_get_args()));
				}
			} else {
				$ret = true;
			}

			$ret && M_Equip::incrCityEquipNum($cityId, -1);
		}
		return $ret;
	}

	/** 添加城市装备列表 */
	static public function setCityEquipList($cityId, $equipId) {
		return self::_setCityEquipList($cityId, $equipId);
	}


	/**
	 * 获取城市装备信息
	 * @author huwei on 20111014
	 * @param int $equipId
	 * @return array
	 */
	static public function getInfo($equipId) {
		$ret     = false;
		$equipId = intval($equipId);
		if ($equipId > 0) {
			$rc  = new B_Cache_RC(T_Key::CITY_EQUIP_INFO, $equipId);
			$ret = $rc->hgetall();
			if (empty($ret['id'])) {
				$fieldArr = B_DB::instance('CityEquip')->get($equipId);
				$ret      = false;
				if (!empty($fieldArr)) {
					M_Equip::setInfo($equipId, $fieldArr, false);
					$ret = $fieldArr;
				}
			}
			if (!isset($ret['flag'])) {
				$ret['flag'] = 7;
			}
		}
		return $ret;
	}


	/**
	 * 更新城市装备信息
	 * @author huwei on 20111014
	 * @param int $equipId
	 * @param array $fieldArr
	 * @param bool $upDB
	 * @return array
	 */
	static public function setInfo($equipId, $fieldArr, $upDB = true) {
		$ret     = false;
		$equipId = intval($equipId);

		if (!empty($equipId) && is_array($fieldArr) && !empty($fieldArr)) {
			$info = array();
			foreach ($fieldArr as $key => $val) {
				if (in_array($key, T_DBField::$equipFields)) {
					$info[$key] = $val;
				}
			}
			if (!empty($info)) {
				$rc  = new B_Cache_RC(T_Key::CITY_EQUIP_INFO, $equipId);
				$ret = $rc->hmset($info, T_App::ONE_DAY);
				if ($ret) {
					$upDB && M_CacheToDB::addQueue(T_Key::CITY_EQUIP_INFO . ':' . $equipId);
				} else {
					Logger::error(array(__METHOD__, func_get_args()));
				}
			}
		}
		return $ret ? $info : false;
	}

	/**
	 * 删除城市装备信息
	 * @author huwei on 20111014
	 * @param int $equipId
	 * @return array
	 */
	static public function delInfo($equipId) {
		$ret     = false;
		$equipId = intval($equipId);
		if ($equipId > 0) {
			$rc  = new B_Cache_RC(T_Key::CITY_EQUIP_INFO, $equipId);
			$ret = $rc->delete();
			$ret && B_DB::instance('CityEquip')->delete($equipId);
		}
		return $ret;
	}

	/**
	 * 同步卸下装备数据!
	 * @author huwei on 20111025
	 * @param int $cityId
	 * @param int $equipId
	 */
	static public function syncRemoveEquip($cityId, $equipId) {
		//同步卸下装备数据!
		$syncEquipData = array(
			$equipId => array(
				'_0'           => M_Sync::SET,
				'is_use'       => 0,
				'hero_name'    => '',
				'hero_quality' => '',
			),
		);

		M_Sync::addQueue($cityId, M_Sync::KEY_EQUIP, $syncEquipData);
	}

	/**
	 * 根据装备ID获取所属套装ID
	 * @author Hejunyun
	 * @param int $cityId 城市ID
	 * @param int $equipId 装备ID
	 */
	static public function getSuitId($cityId, $equipId) {
		$info = M_Equip::getCityEquipById($cityId, $equipId);
		if (isset($info['id'])) {
			return $info['need_level'] . '_' . $info['quality'];
		} else {
			return false;
		}
	}

	/**
	 * 根据faceId获取系统装备名字
	 * @author Hejunyun
	 * @param int $equipFaceId
	 * @return string $equipName
	 */
	static public function getEquipName($equipFaceId) {
		$nameList  = self::getNameList();
		$equipName = isset($nameList[$equipFaceId]) ? $nameList[$equipFaceId] : false;
		return $equipName;
	}

	static public function getNameList() {
		static $list = array();
		if (empty($list)) {
			$key  = T_Key::BASE_EQUIP_TPL_NAMES;
			$list = B_Cache_APC::get($key);
			if (!$list) {
				$arr = B_DB::instance('BaseEquipTpl')->getNames();
				foreach ($arr as $val) {
					$list[$val['face_id']] = $val['name'];
				}
				Logger::base(__METHOD__);
				APC::set($key, $list);
			}

		}
		return $list;
	}

	/**
	 * 获取英基础雄技能加成
	 * @author duhuihui
	 * @param array $heroInfo
	 * @return void
	 */
	static public function getBaseEffectByHero(&$heroInfo) {

		$equip = array('equip_arm', 'equip_cap', 'equip_uniform', 'equip_medal', 'equip_shoes', 'equip_sit');
		$num   = array();
		foreach ($equip as $val) {
			if (!empty($heroInfo[$val])) {
				$equipInfo = M_Equip::getInfo($heroInfo[$val]);

				if (isset($equipInfo['suit_id']) && $equipInfo['suit_id'] != 0) {
					$num[] = $equipInfo['suit_id'];
				}
			}
		}
		$suitNum = array();

		if (!empty($num)) {
			$suitNum = array_count_values($num);
		}

		$data         = M_Equip::getEffect($suitNum);
		$effect       = $data['base'];
		$battleeffect = $data['battle'];

		$heroInfo['equip_lead']     = 0;
		$heroInfo['equip_command']  = 0;
		$heroInfo['equip_military'] = 0;
		//0几率|1技能值|2触发类型|3使用兵种|4目标兵种|攻击类型|消耗精力|影响回合数
		if (!empty($effect)) {
			foreach ($effect as $k => $val) {
				switch ($k) {
					case 'TZ_ZH':
						$heroInfo['equip_lead'] += M_Formula::calcHeroBaseSkillAdd($heroInfo['attr_lead'], $val[0]);
						break;
					case 'TZ_JS':
						$heroInfo['equip_command'] += M_Formula::calcHeroBaseSkillAdd($heroInfo['attr_command'], $val[0]);
						break;
					case 'TZ_TS':
						$heroInfo['equip_military'] += M_Formula::calcHeroBaseSkillAdd($heroInfo['attr_military'], $val[0]);
						break;
					case 'TZ_ALLATTR':
						$heroInfo['equip_lead'] += M_Formula::calcHeroBaseSkillAdd($heroInfo['attr_lead'], $val[0]);
						$heroInfo['equip_command'] += M_Formula::calcHeroBaseSkillAdd($heroInfo['attr_command'], $val[0]);
						$heroInfo['equip_military'] += M_Formula::calcHeroBaseSkillAdd($heroInfo['attr_military'], $val[0]);
						break;
				}
			}
		}
		return true;
	}

	/**
	 * 获取技能加成
	 * @author duhuihui
	 * @param array $heroInfo
	 * @return void
	 */
	static public function getBattleEffectByHero($heroInfo) {
		$equip = array('equip_arm', 'equip_cap', 'equip_uniform', 'equip_medal', 'equip_shoes', 'equip_sit');
		$num   = array();
		foreach ($equip as $val) {
			if (!empty($heroInfo[$val])) {
				$objPlayer = new O_Player($heroInfo['city_id']);
				$cityInfo  = $objPlayer->getCityBase();

				$equipInfo = M_Equip::getCityEquipById($cityInfo['id'], $heroInfo[$val]);
				if (isset($equipInfo['suit_id']) && $equipInfo['suit_id'] != 0) {
					$num[] = $equipInfo['suit_id'];
				}
			}
		}

		// 		$num = array(34,34,34,34,34,34);
		$suitNum = array();
		if (!empty($num)) {
			$suitNum = array_count_values($num);
		}

		$data = self::getEffect($suitNum);

		$effect = array();
		$armyId = isset($heroInfo['army_id']) ? $heroInfo['army_id'] : '';
		foreach ($data['battle'] as $key => $val) {
			//几率|技能值|触发类型|使用兵种|目标兵种|攻击类型|消耗精力|影响回合数
			if (empty($val[1]) && $val[1] == 0) {
				$effect[$key] = $val; //使用兵种
			} else if (!empty($val[1]) && $val[1] == $armyId) {
				$effect[$key] = $val; //使用兵种
			}

		}
		return $effect;
	}

	static public function getEffect($suitNum) {

		$values        = array();
		$baseEffects   = array();
		$battleEffects = array();
		$equipSuitList = M_Base::equipSuitAll(); //全部套装装备

		if (!empty($suitNum)) {
			foreach ($suitNum as $suitId => $n) {
				$suitEffect = !empty($equipSuitList[$suitId]['effect']) ? json_decode($equipSuitList[$suitId]['effect'], true) : array();
				$value      = array();

				foreach ($suitEffect as $num => $v) {
					if ($n >= $num) {
						$value = $v;
					}
				}
				$values[] = $value;
			}
		}
		if (!empty($values)) {

			foreach ($values as $value) //几个（类型，兵种，值）
			{
				foreach ($value as $effectTxt => $val) {
					//0技能值|1使用兵种|2目标兵种|3攻击类型
					$valArr = explode('|', $val);
					if (isset(T_Effect::$SuitBaseType[$effectTxt])) //基础技能效果
					{
						if (isset($baseEffects[$effectTxt])) {
							//是否可以叠加
							if (in_array($effectTxt, T_Effect::$SuitOverlayType) && $valArr[0]) {
								$baseEffects[$effectTxt][0] += $valArr[0];
							}
						} else {
							$baseEffects[$effectTxt] = $valArr;
						}
					} else if (isset(T_Effect::$SuitBattleType[$effectTxt])) //战斗技能效果
					{
						//战斗技能不可以叠加
						$battleEffects[$effectTxt] = $valArr;
					}
				}

			}
		}
		$effects['base']   = $baseEffects;
		$effects['battle'] = $battleEffects;
		return $effects;
	}


}

?>
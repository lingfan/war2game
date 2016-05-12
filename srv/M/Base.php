<?php

class M_Base {
	static public function tech() {
		$arrdata     = array();
		$baseInfoAll = M_Base::techAll();

		foreach ($baseInfoAll as $key => $baseInfo) {
			$arr1             = array();
			$arr1['TechId']   = $baseInfo['id'];
			$arr1['TechName'] = $baseInfo['name'];
			$arr1['FaceId']   = $baseInfo['id'];
			$arr1['Features'] = $baseInfo['features'];
			$arr1['Area']     = $baseInfo['type'];
			$arr1['MaxLevel'] = $baseInfo['max_level'];
			$arr1['Desc1']    = $baseInfo['desc_1'];
			$arr1['Desc2']    = $baseInfo['desc_2'];
			$arr1['TechAttr'] = array();

			if (!empty($baseInfo['upg'])) {
				foreach ($baseInfo['upg'] as $k1 => $upginfo) {
					$arrEffect          = json_decode($upginfo['effect'], true);
					$arr1['TechAttr'][] = array(
						'TechId'    => $upginfo['tech_id'],
						'TechLevel' => intval($upginfo['level']),
						'CostGold'  => $upginfo['cost_gold'],
						'CostFood'  => $upginfo['cost_food'],
						'CostOil'   => $upginfo['cost_oil'],
						'CostTime'  => $upginfo['cost_time'],
						'NeedBuild' => $upginfo['need_build'],
						'NeedTech'  => $upginfo['need_tech'],
						'Effect'    => array(key($arrEffect), current($arrEffect))
					);
				}
			}
			$arrdata[] = $arr1;
		}
		return $arrdata;
	}

	/**
	 * 获取所有科技基础数据
	 * @author chenhui    on 20110414
	 * @return array 科技基础数据(二维数组)
	 */
	static public function techAll($clean = false) {
		static $list = array();
		if (empty($list)) {
			$apcKey = T_Key::BASE_TECH;
			$clean && B_Cache_APC::del($apcKey);

			$list = B_Cache_APC::get($apcKey);
			if (empty($list)) {
				$list = B_DB::instance('BaseTech')->all();
				APC::set($apcKey, $list);
			}
		}

		return $list;
	}

	static public function task() {

		$baseInfoAll = M_Base::taskAll();
		$arrdata     = array();
		foreach ($baseInfoAll as $key => $baseInfo) {
			$needTimes = 1;
			$isNeedLv  = M_Task::NEED_LV_NO;

			$awardArr = M_Award::allResult($baseInfo['award_id']);

			if (M_Task::TYPE_DAILY == $baseInfo['type']) {
				$needTimes = intval(current(json_decode($baseInfo['need'], true)));
			}

			$arrdata[] = array(
				'TaskId'     => $baseInfo['id'],
				'Title'      => $baseInfo['title'],
				'Type'       => $baseInfo['type'],
				'DescAim'    => $baseInfo['desc_aim'],
				'DescIntro'  => $baseInfo['desc_intro'],
				'DescGuide'  => $baseInfo['desc_guide'],
				'DescFinish' => $baseInfo['desc_finish'],
				'IsNeedLv'   => $isNeedLv,
				'Award'      => M_Award::toText($awardArr),
				'Sort'       => $baseInfo['sort'],
				'NeedTimes'  => $needTimes,
			);

		}
		return $arrdata;
	}

	/**
	 * 获取所有任务基础数据
	 * @author chenhui    on 20110428
	 * @return array 科技基础数据(二维数组)
	 */
	static public function taskAll($clean = false) {
		$arrShieldId = M_Task::getShieldId(); //屏蔽任务ID

		static $info = array();
		if (empty($info)) {
			$apcKey = T_Key::BASE_TASK;
			$clean && B_Cache_APC::del($apcKey);

			$info = B_Cache_APC::get($apcKey);
			if (empty($info)) {
				$ret  = array();
				$info = B_DB::instance('BaseTask')->all();
				foreach ($info as $id => $infoT) {
					if (!in_array($id, $arrShieldId)) {
						$ret[$id] = $infoT;
					}
				}
				$info = $ret;
				APC::set($apcKey, $info);
			}
		}
		return $info;
	}

	static public function weapon() {
		$arrdata     = array();
		$baseInfoAll = M_Base::weaponAll();

		foreach ($baseInfoAll as $key => $baseInfo) {
			$arrdata[] = array(
				'WeaponId'      => $baseInfo['id'],
				'WeaponName'    => $baseInfo['name'],
				'ArmyName'      => $baseInfo['army_name'],
				'Features'      => $baseInfo['features'],
				'Detail'        => $baseInfo['detail'],
				'ArmyId'        => $baseInfo['army_id'],
				'NeedArmyLv'    => $baseInfo['need_army_lv'],
				'MarchType'     => $baseInfo['march_type'],
				'ShowType'      => $baseInfo['show_type'],
				'Sort'          => $baseInfo['sort'],
				'IsSpecial'     => $baseInfo['is_special'],
				'IsNPC'         => $baseInfo['is_npc'],
				'LifeValue'     => $baseInfo['life_value'],
				'AttLand'       => $baseInfo['att_land'],
				'AttSky'        => $baseInfo['att_sky'],
				'AttOcean'      => $baseInfo['att_ocean'],
				'DefLand'       => $baseInfo['def_land'],
				'DefSky'        => $baseInfo['def_sky'],
				'DefOcean'      => $baseInfo['def_ocean'],
				'Speed'         => $baseInfo['speed'],
				'MoveRange'     => $baseInfo['move_range'],
				'MoveType'      => $baseInfo['move_type'],
				'ShotRangeMin'  => $baseInfo['shot_range_min'],
				'ShotRangeMax'  => $baseInfo['shot_range_max'],
				'ShotType'      => $baseInfo['shot_type'],
				'ViewRange'     => $baseInfo['view_range'],
				'Carry'         => $baseInfo['carry'],
				'CostGold'      => $baseInfo['cost_gold'],
				'CostFood'      => $baseInfo['cost_food'],
				'CostOil'       => $baseInfo['cost_oil'],
				'CostTime'      => $baseInfo['cost_time'],
				'MarchCostOil'  => $baseInfo['march_cost_oil'],
				'MarchCostFood' => $baseInfo['march_cost_food'],
				'NeedTech'      => $baseInfo['need_tech'],
				'NeedBuild'     => $baseInfo['need_build']
			);
		}

		return $arrdata;
	}

	/**
	 * 获取所有武器基础信息
	 * @author chenhui on 20110414
	 * @return array 武器基础信息(二维数组)
	 */
	static public function weaponAll($clean = false) {
		static $list = array();
		if (empty($list)) {
			$apcKey = T_Key::BASE_WEAPON;
			$clean && B_Cache_APC::del($apcKey);

			$list = B_Cache_APC::get($apcKey);
			if (empty($list)) {
				$tmpList = B_DB::instance('BaseWeapon')->all();
				$list    = array();
				foreach ($tmpList as $val) {
					$list[$val['id']] = $val;
				}
				APC::set($apcKey, $list);
			}
		}
		return $list;
	}

	static public function army() {
		$arrdata     = array();
		$baseInfoAll = M_Base::armyAll(); //全部兵种基础信息
		$maxLev      = M_Config::getVal('army_max_level');
		foreach ($baseInfoAll as $key => $baseInfo) {
			$needBuild = array();
			for ($lv = 1; $lv <= 10; $lv++) {
				$needBuild[] = array(M_Build::ID_MIL_CAMP => M_Formula::armyUpgBuildLev($baseInfo['id'], $lv));


			}


			$arrdata[] = array(
				'ArmyId'     => $baseInfo['id'],
				'ArmyName'   => $baseInfo['name'],
				'Features'   => $baseInfo['features'],
				'ArmyLife'   => $baseInfo['life_value'],
				'AttLand'    => $baseInfo['att_land'],
				'AttSky'     => $baseInfo['att_sky'],
				'AttOcean'   => $baseInfo['att_ocean'],
				'DefLand'    => $baseInfo['def_land'],
				'DefSky'     => $baseInfo['def_sky'],
				'DefOcean'   => $baseInfo['def_ocean'],
				'CostGold'   => $baseInfo['cost_gold'],
				'CostFood'   => $baseInfo['cost_food'],
				'CostOil'    => $baseInfo['cost_oil'],
				'CostPeople' => $baseInfo['cost_people'],
				'Desc1'      => $baseInfo['desc_1'],
				'Desc2'      => $baseInfo['desc_2'],
				'NeedBuild'  => $needBuild,
				'NeedTech'   => array(),
				'MaxLevel'   => $maxLev,
			);
		}
		return $arrdata;
	}

	/**
	 * 获取兵种的基本信息
	 * @author huwei
	 * @return array
	 */
	static public function armyAll($clean = false) {
		static $list = array();
		if (empty($list)) {
			$apcKey = T_Key::BASE_ARMY;
			$clean && B_Cache_APC::del($apcKey);

			$info = B_Cache_APC::get($apcKey);
			if (empty($info)) {
				$rows = B_DB::instance('BaseArmy')->getAll();
				$info = array();
				foreach ($rows as $key => $val) {
					$info[$val['id']] = $val;
				}

				APC::set($apcKey, $info);
			}
			$list = $info;
		}
		return $list;
	}

	static public function build() {
		$arrdata     = array();
		$baseInfoAll = M_Base::buildAll();

		foreach ($baseInfoAll as $key => $baseInfo) {
			if (M_Build::NOT_BEAUTIFY == $baseInfo['is_beautify'] &&
				M_Build::ID_SPY_COLLEGE != $baseInfo['id']
			) //屏蔽装饰建筑和间谍学校
			{
				$arrArea  = json_decode($baseInfo['area'], true);
				$zoneArea = array();
				foreach ($arrArea as $z => $val) {
					$zoneArea[$z] = M_MapCity::buildDec2Chr($val);
				}
				$arr1               = array();
				$arr1['BuildId']    = $baseInfo['id'];
				$arr1['BuildName']  = $baseInfo['name'];
				$arr1['Features']   = $baseInfo['features'];
				$arr1['Area']       = $zoneArea;
				$arr1['IsMoved']    = $baseInfo['is_moved'];
				$arr1['IsMulti']    = $baseInfo['is_multi'];
				$arr1['IsBeautify'] = $baseInfo['is_beautify'];
				$arr1['MaxLevel']   = $baseInfo['max_level'];
				$arr1['Sort']       = $baseInfo['sort'];
				$arr1['Desc1']      = $baseInfo['desc_1'];
				$arr1['Desc2']      = $baseInfo['desc_2'];
				$arr1['BuildAttr']  = array();

				if (!empty($baseInfo['upg'])) {
					foreach ($baseInfo['upg'] as $k1 => $upginfo) {
						$arr1['BuildAttr'][] = array(
							'BuildId'     => $upginfo['build_id'],
							'BuildLevel'  => $upginfo['level'],
							'CostGold'    => $upginfo['cost_gold'],
							'CostFood'    => $upginfo['cost_food'],
							'CostOil'     => $upginfo['cost_oil'],
							'CostTime'    => $upginfo['cost_time'],
							'NeedBuild'   => B_Utils::kv2vv($upginfo['need_build']),
							'NeedTech'    => B_Utils::kv2vv($upginfo['need_tech']),
							'ResGrowNow'  => $upginfo['res_grow_now'], //资源建筑当前等级基础产量,其它则为0
							'ResGrowNext' => $upginfo['res_grow_next'], //资源建筑下一等级基础产量,其它则为0
						);
					}
				}
				$arrdata[] = $arr1;
			}
		}
		return $arrdata;
	}

	/**
	 * 获取所有建筑基础数据
	 * @author chenhui    on 20110413
	 * @return array 建筑基础数据(二维数组)
	 */
	static public function buildAll($clean = false) {
		static $list = array();
		if (empty($list)) {
			$apcKey = T_Key::BASE_BUILD;
			$clean && B_Cache_APC::del($apcKey);

			$list = B_Cache_APC::get($apcKey);
			if (empty($list)) {
				$list = B_DB::instance('BaseBuild')->all();
				APC::set($apcKey, $list);
			}
		}
		return $list;

	}

	static public function war_map_cell() {
		$list = M_Base::warmapcellAll();
		$data = array();
		foreach ($list as $key => $val) {
			$data['Cell'][] = array(
				'CellId'    => $val['id'],
				'Name'      => $val['name'],
				'FaceId'    => $val['face_id'],
				'Ban'       => $val['ban'],
				'Type'      => $val['type'],
				'Hp'        => $val['life_value'],
				'AttLand'   => $val['att_land'],
				'AttSky'    => $val['att_sky'],
				'AttOcean'  => $val['att_ocean'],
				'DefLand'   => $val['def_land'],
				'DefSky'    => $val['def_sky'],
				'DefOcean'  => $val['def_ocean'],
				'ShotRange' => $val['shot_range'],
				'ViewRange' => $val['view_range'],
				'MoveRange' => $val['move_range'],
			);
		}
		foreach ($list as $key => $val) {
			$data['Secne'][] = array(
				'SecneId' => $val['id'],
				'Name'    => $val['name'],
				'FaceId'  => $val['face_id'],
			);
		}
		return $data;
	}

	/**
	 * 获取所有基础战斗地图标记物信息
	 * @author huwei on 20110617
	 * @return array
	 */
	static public function warmapcellAll($clean = false) {
		static $info = null;
		if (is_null($info)) {
			$apcKey = T_Key::BASE_WAR_MAP_CELL;
			$clean && B_Cache_APC::del($apcKey);

			$result = B_Cache_APC::get($apcKey);
			if (empty($result)) {
				$result = B_DB::instance('BaseWarMapCell')->all();
				$ret    = B_Cache_APC::set($apcKey, $result);
			}
			$info = $result;
		}
		return $info;
	}

	static public function skill() {
		$arrdata = array();
		$list    = M_Base::skillAll();
		foreach ($list as $key => $info) {
			$arrdata[] = array(
				'Id'     => $info['id'],
				'Name'   => $info['name'],
				'FaceId' => $info['id'],
				'Type'   => $info['type'],
				'Level'  => $info['level'],
				'Desc'   => $info['desc'],
			);
		}
		return $arrdata;
	}

	/**
	 * 获取技能的基本信息
	 * @author huwei
	 * @return array
	 */
	static public function skillAll($clean = false) {
		static $list = array();
		if (empty($list)) {
			$apcKey = T_Key::BASE_SKILL;
			$clean && B_Cache_APC::del($apcKey);

			$info = B_Cache_APC::get($apcKey);
			if (empty($info)) {
				$info = B_DB::instance('BaseSkill')->getAll();
				APC::set($apcKey, $info);
			}
			$list = $info;
		}
		return $list;
	}

	static public function info() {
		$baseConf  = M_Config::getVal();
		$unionConf = M_Union::$unionConf;

		$unionUpLevel = array();
		foreach ($baseConf['union_up'] as $lv => $val) {
			list($gold, $mil_pay, $maxNum) = $val;
			$unionUpLevel[$lv] = array(
				'gold'    => $gold,
				'mil_pay' => $mil_pay,
			);
		}
		$data = array(
			'BaseGoldGrow'               => $baseConf['city_gold_grow'], //金币初始增长值(每小时)
			'BaseFoodGrow'               => $baseConf['city_food_grow'], //食物初始增长值(每小时)
			'BaseOilGrow'                => $baseConf['city_oil_grow'], //石油初始增长值(每小时)
			'BaseOilGrow'                => $baseConf['hero_maxlv'], //英雄最大等级
			'HeroHireBaseValue'          => $baseConf['hero_base_value'], //召募价格系数
			'HeroCollegeRefreshInterval' => $baseConf['hero_refresh_interval'], //英雄学院刷新时间(小时)
			//'HeroFindCDTime'				=> $baseConf['hero_find_cd_time'],		//寻将冷却时间(分钟)
			'HeroRelifeGoldFactor'       => $baseConf['hero_relife_gold'], //复活英雄需要金钱系数
			'PosGrowList'                => $baseConf['strong_equip_attr_add_rate'],
			'Strng_a'                    => $baseConf['strong_equip_rate_a'],
			'Strng_b'                    => $baseConf['strong_equip_rate_b'],
			'Strng_s'                    => $baseConf['strong_equip_rate_s'],
			'strongMaxLevel'             => $baseConf['strong_equip_max_level'],
			'EquipUpgradeSuccRate'       => T_Equip::$upEquipSuccRate,
			'EquipUpgradeQualityRate'    => T_Equip::$upEquipQuality,
			'EquipUpgradeLevelRate'      => T_Equip::$upEquipLevel,

			'UnionTopLevel'              => count($baseConf['union_up']),
			'CreateCost'                 => M_Config::getVal('union_create_cost'),
			'UpFaceCost'                 => M_Config::getVal('union_up_face_cost'),
			'CreateNeedMedal'            => M_Config::getVal('union_create_need_medal'),
			'DonationNeedMedal'          => M_Config::getVal('union_donation_need_medal'),
			'InitAcmd'                   => $unionConf['init_acmd'],
			'IncreAcmd'                  => $unionConf['incre_acmd'],
			'UnionCoinPption'            => $unionConf['union_coin_pption'],
			'FaceArr'                    => M_Union::$faceArr,
			'UnionUpLevel'               => $unionUpLevel,
			'UnionDynamicNum'            => M_Union::$UnionDynamicLimit,

		);

		foreach (M_Union::$unionTech as $techId => $val) {
			//科技ID, 联盟等级需求, 科技加成, 科技名称
			$data['UnionTech'][] = array($techId, $val[1], $val[0], '');
		}
		return $data;
	}

	static public function vip() {
		$arrData  = array();
		$vipConf  = M_Vip::getVipConfig();
		$maxLevel = intval($vipConf['MAX_VIP_LEVEL']);
		for ($i = 0; $i <= $maxLevel; $i++) {
			$tmp = array();

			$strLev = isset($vipConf['EQUI_AWARD'][$i]) ? $vipConf['EQUI_AWARD'][$i] : '';
			$data   = explode(',', $strLev);

			if (!empty($data) && is_array($data)) {
				foreach ($data as $equiId) {
					$tplInfo = M_Equip::baseInfo($equiId);
					if (!empty($tplInfo) && is_array($tplInfo)) {
						$arrT              = array(
							'Id'           => $equiId,
							'Name'         => $tplInfo['name'],
							'FaceId'       => $tplInfo['face_id'],
							'Pos'          => $tplInfo['pos'],
							'Type'         => $tplInfo['type'],
							'NeedLevel'    => $tplInfo['need_level'],
							'Level'        => $tplInfo['level'],
							'Quality'      => $tplInfo['quality'],
							'BaseLead'     => $tplInfo['base_lead'],
							'BaseCommand'  => $tplInfo['base_command'],
							'BaseMilitary' => $tplInfo['base_military'],
						);
						$tmp[(int)$equiId] = $arrT;
					}
				}
			}
			$arrData[] = $tmp;
		}

		return $arrData;
	}

	static public function probe() {
		$arrData = array();
		$info    = M_Base::probeAll();
		foreach ($info as $id => $val) {
			$arrData[] = array(
				'Id'    => $val['id'],
				'Title' => $val['title'],
			);
		}
		return $arrData;
	}

	/**
	 * 探索事件基础数据
	 * @author huwei
	 * @return array
	 */
	static public function probeAll($clean = false) {
		static $list = array();
		if (empty($list)) {
			$apcKey = T_Key::BASE_PROBE;
			$clean && B_Cache_APC::del($apcKey);

			$list = B_Cache_APC::get($apcKey);
			if (empty($list)) {
				$list = B_DB::instance('BaseProbe')->all();
				APC::set($apcKey, $list);
			}
		}
		return $list;
	}

	static public function city_map_block() {
		$arrdata['block'] = M_MapCity::getCityMapBlock();
		return $arrdata;
	}

	static public function item_draw() {
		$arrdata     = array();
		$baseInfoAll = M_Base::propsAll();
		if (!empty($baseInfoAll)) {
			foreach ($baseInfoAll as $key => $baseInfo) {
				if ($baseInfo['type'] == M_Props::TYPE_DRAW) {
					$arrPrice  = json_decode($baseInfo['price'], true);
					$showPrice = array();
					isset($arrPrice[T_App::MILPAY]) && $showPrice[] = $arrPrice[T_App::MILPAY];
					isset($arrPrice[T_App::COUPON]) && $showPrice[] = $arrPrice[T_App::COUPON];

					$arrdata[] = array(
						'PropsId'    => $baseInfo['id'],
						'Name'       => $baseInfo['name'],
						'Desc'       => $baseInfo['desc'],
						'Feature'    => $baseInfo['feature'],
						'FaceId'     => $baseInfo['face_id'],
						'Type'       => $baseInfo['type'],
						'Price'      => $showPrice,
						'SysPrice'   => $baseInfo['sys_price'],
						'SysIsSale'  => 1, //是否可出售给系统，默认可以
						'IsShop'     => $baseInfo['is_shop'],
						'IsLocked'   => $baseInfo['is_locked'],
						'IsFall'     => $baseInfo['is_fall'],
						'EffectTxt'  => $baseInfo['effect_txt'],
						'EffectVal'  => $baseInfo['effect_val'], //格式不统一
						'EffectTime' => $baseInfo['effect_time'],
						'Sort'       => $baseInfo['sort'],
						'DirectUse'  => isset(M_Props::$EffectUse[$baseInfo['effect_txt']]) ? 1 : 0, //是否可以直接使用
					);
				}
			}
		}
		return $arrdata;
	}

	/**
	 * 获取所有道具基础信息
	 * @author chenhui on 20110414
	 * @return array 道具基础信息(二维数组)
	 */
	static public function propsAll($clean = false) {
		static $list = array();
		if (empty($list)) {
			$apcKey = T_Key::BASE_PROPS;
			$clean && B_Cache_APC::del($apcKey);

			$list = B_Cache_APC::get($apcKey);
			if (empty($list)) {
				$tmpList = B_DB::instance('BaseProps')->all();
				$list    = array();
				foreach ($tmpList as $val) {
					$list[$val['id']] = $val;
				}
				APC::set($apcKey, $list);
			}
		}
		return $list;
	}

	/**
	 * 物品合成材料
	 */
	static public function item_stuff() {
		$arrdata     = array();
		$baseInfoAll = M_Base::propsAll();
		if (!empty($baseInfoAll)) {
			foreach ($baseInfoAll as $key => $baseInfo) {
				if ($baseInfo['type'] == M_Props::TYPE_STUFF) {
					$arrPrice  = json_decode($baseInfo['price'], true);
					$showPrice = array();
					isset($arrPrice[T_App::MILPAY]) && $showPrice[] = $arrPrice[T_App::MILPAY];
					isset($arrPrice[T_App::COUPON]) && $showPrice[] = $arrPrice[T_App::COUPON];

					$arrdata[] = array(
						'PropsId'    => $baseInfo['id'],
						'Name'       => $baseInfo['name'],
						'Desc'       => $baseInfo['desc'],
						'Feature'    => $baseInfo['feature'],
						'FaceId'     => $baseInfo['face_id'],
						'Type'       => $baseInfo['type'],
						'Price'      => $showPrice,
						'SysPrice'   => $baseInfo['sys_price'],
						'SysIsSale'  => 1, //是否可出售给系统，默认可以
						'IsShop'     => $baseInfo['is_shop'],
						'IsLocked'   => $baseInfo['is_locked'],
						'IsFall'     => $baseInfo['is_fall'],
						'EffectTxt'  => $baseInfo['effect_txt'],
						'EffectVal'  => $baseInfo['effect_val'], //格式不统一
						'EffectTime' => $baseInfo['effect_time'],
						'Sort'       => $baseInfo['sort'],
						'DirectUse'  => isset(M_Props::$EffectUse[$baseInfo['effect_txt']]) ? 1 : 0, //是否可以直接使用
					);
				}
			}
		}
		return $arrdata;
	}

	static public function props() {
		$arrdata     = array();
		$baseInfoAll = M_Base::propsAll();
		if (!empty($baseInfoAll) && is_array($baseInfoAll)) {
			$propsType = array(
				M_Props::TYPE_HERO  => 1,
				M_Props::TYPE_INNER => 1,
				M_Props::TYPE_TREA  => 1,
				M_Props::TYPE_WAR   => 1
			);
			foreach ($baseInfoAll as $key => $baseInfo) {
				//@todo 道具分类
				$ok = isset($propsType[$baseInfo['type']]);
				if (true) {
					$arrPrice  = json_decode($baseInfo['price'], true);
					$showPrice = array();
					isset($arrPrice[T_App::MILPAY]) && $showPrice[] = $arrPrice[T_App::MILPAY];
					isset($arrPrice[T_App::COUPON]) && $showPrice[] = $arrPrice[T_App::COUPON];
					if ('EQUI_INCR_STRONG' == $baseInfo['effect_txt']) {
						$tmpVal   = explode(',', $baseInfo['effect_val']);
						$frontVal = $tmpVal[0];
					} else if ('WEAPON_PIECE' == $baseInfo['effect_txt']) {
						$frontVal = explode(',', $baseInfo['effect_val']); //array(对应图纸ID,合成需要数量)
					} else {
						$frontVal = $baseInfo['effect_val'];
					}

					$arrdata[] = array(
						'PropsId'    => $baseInfo['id'],
						'Name'       => $baseInfo['name'],
						'Desc'       => $baseInfo['desc'],
						'Feature'    => $baseInfo['feature'],
						'FaceId'     => $baseInfo['face_id'],
						'Type'       => $baseInfo['type'],
						'Price'      => $showPrice,
						'SysPrice'   => $baseInfo['sys_price'],
						'SysIsSale'  => 1, //是否可出售给系统，默认可以
						'IsShop'     => $baseInfo['is_shop'],
						'IsLocked'   => $baseInfo['is_locked'],
						'IsFall'     => $baseInfo['is_fall'],
						'EffectTxt'  => $baseInfo['effect_txt'],
						'EffectVal'  => $frontVal, //格式不统一
						'EffectTime' => $baseInfo['effect_time'],
						'Sort'       => $baseInfo['sort'],
						'DirectUse'  => isset(M_Props::$EffectUse[$baseInfo['effect_txt']]) ? 1 : 0, //是否可以直接使用
					);
				}
			}
		}
		return $arrdata;
	}

	static public function genBinFile() {
		$arr = array(
			'tech'            => 'tech',
			'weapon'          => 'weapon',
			'army'            => 'army',
			'build'           => 'build',
			'citymapblock'    => 'city_map_block',
			'warmapcell'      => 'war_map_cell',
			'vipsysequiaward' => 'vip',
			'baseprobe'       => 'probe',
			'task'            => 'task',
			'base'            => 'info',
			'props'           => 'props',
			'build'           => 'build',
			'skill'           => 'skill',
		);
		$ret = array();
		foreach ($arr as $filename => $func) {
			$arrdata = M_Base::$func();
			$data    = gzcompress(json_encode($arrdata), 9);
			$resPath = WWW_PATH . '/bin/' . ETC_NO . '/';
			$resFile = $resPath . $filename . '.bin';
			if (is_writeable($resPath)) {
				$fp = fopen($resFile, 'wb');
				fwrite($fp, $data);
				fclose($fp);
				$ret[] = $resFile;
			} else {
				$ret[] = $resFile . " not to write";
			}
		}
		return $ret;
	}

	/**
	 *获取商城基础信息
	 * @author duhuihui    on 20120907
	 * @return array 商城基础信息(一维数组)
	 */
	static public function mallAll($clean = false) {
		static $list = array();
		if (empty($list)) {
			$apcKey = T_Key::BASE_MALL;
			$clean && B_Cache_APC::del($apcKey);

			$list = B_Cache_APC::get($apcKey);
			if (empty($list)) {
				$list = B_DB::instance('BaseMall')->all();
				APC::set($apcKey, $list);
			}
		}
		return $list;
	}

	/**
	 *获取兑换商城商城基础信息
	 * @author duhuihui    on 20130403
	 * @return array 商城基础信息(一维数组)
	 */
	static public function mallExchangeAll($clean = false) {
		static $list = array();
		if (empty($list)) {
			$apcKey = T_Key::BASE_MALL_EXCHANGE;
			$clean && B_Cache_APC::del($apcKey);

			$list = B_Cache_APC::get($apcKey);
			if (empty($list)) {
				$list = B_DB::instance('BaseMallExchange')->getAll();
				APC::set($apcKey, $list);
			}
		}
		return $list;
	}

	static public function npcAll($clean = false) {
		static $list = array();
		if (empty($list)) {
			$apcKey = T_Key::BASE_NPC;
			$clean && B_Cache_APC::del($apcKey);

			$list = B_Cache_APC::get($apcKey);
			if (empty($list)) {
				$s    = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '-', '_');
				$info = B_DB::instance('BaseNpcTroop')->all();
				foreach ($info as $val) {
					$list[$val['id']]             = $val;
					$list[$val['id']]['nickname'] = str_replace($s, '', $val['nickname']);
				}

				APC::set($apcKey, $list);
			}
		}
		return $list;
	}

	/**
	 * 新手指引基础数据
	 * @author huwei
	 * 任务完成条件:
	 * 升级建筑 => array(build_up, 建筑ID(0任意), 等级)
	 * 建筑迁移 => array(build_move, 建筑ID(0任意), 次数)
	 * 建筑清CD => array(build_cd, 次数)
	 * 科技升级 => array(tech_up,科技ID(0任意),等级)
	 * 科技清CD => array(tech_cd, 次数)
	 * 兵种升级 => array(army_up,兵种ID(0任意),等级)
	 * 兵种招募 => array(army_hire,兵种ID(0任意),数量)
	 * 兵种配兵=> array(army_fit, 兵种ID(0任意), 数量)
	 * 副本次数=> array(fb_times, 副本编号, 次数)
	 * 充值=> array(pay,军饷数)
	 * 道具购买 => array(props_buy, 道具ID(0任意), 道具数量)
	 * 道具使用 => array(props_use, 道具ID(0任意), 次数)
	 * 军官寻找=> array(hero_find, 次数)
	 * 传奇招募=> array(hero_hire_s,次数)
	 * 军官招募=> array(hero_hire, 次数)
	 * 军官培养=> array(hero_train, 次数)
	 * 军团申请=> array(union_apply, 次数)
	 * 装备强化=> array(equip_strong, 品质(0任意), 等级(0任意), 次数)
	 * 装备升级=> array(equip_up, 品质(0任意), 等级(0任意), 次数)
	 * 装备合成=> array(equip_mix, 品质(0任意), 等级(0任意), 次数)
	 * 特殊武器研究=> array(weapon_study_s, 第几个)
	 * 普通武器研究=> array(weapon_study,    武器ID)
	 * 好友邀请=>array(friend_invite,数量)
	 * 攻打玩家=>array(atk_player,1)
	 * 攻打学院=>array(atk_wildnpc,1)
	 */
	static public function questAll($clean = false) {
		static $info = array();
		if (empty($info)) {
			$key = T_Key::BASE_QUEST;
			$clean && B_Cache_APC::del($key);

			$info = B_Cache_APC::get($key);
			if (empty($info)) {
				$list = B_DB::instance('BaseQuest')->all();
				$rel  = array();
				foreach ($list as $qid => $qv) {
					$list[$qid]['cond_pass'] = explode(',', $qv['cond_pass']);
					$rel[$qv['prev_id']][]   = $qid;
				}

				$info['info'] = $list;
				$info['rel']  = $rel;
				APC::set($key, $info);
			}
		}
		return $info;

	}

	static public function questInfo($id) {
		$info = array();
		if ($id) {
			$apcKey = T_Key::BASE_QUEST . '_' . $id;
			$info   = B_Cache_APC::get($apcKey);
			if (empty($info)) {
				$info = B_DB::instance('BaseQuest')->get($id);
				if (!empty($info['id'])) {
					$info['cond_pass'] = explode(',', $info['cond_pass']);
					APC::set($apcKey, $info);
				}
			}
		}

		return $info;
	}


	/**
	 * 获取奖励基础数据
	 * @author huwei 2012-07-01
	 */
	static public function awardAll($clean = false) {
		static $list = array();
		if (empty($list)) {
			$apcKey = T_Key::BASE_AWARD;
			$clean && B_Cache_APC::del($apcKey);

			$list = B_Cache_APC::get($apcKey);
			if (empty($list)) {
				$rows = B_DB::instance('BaseAward')->getAll();
				$list = array();
				foreach ($rows as $key => $val) {
					$list[$val['id']] = $val;
				}
				APC::set($apcKey, $list);
			}
		}
		return $list;
	}

	static public function award($awardId) {
		$info = array();
		if ($awardId) {
			$apcKey = T_Key::BASE_AWARD . '_' . $awardId;
			$info   = B_Cache_APC::get($apcKey);
			if (empty($info)) {
				$info = B_DB::instance('BaseAward')->get($awardId);
				if (!empty($info['id'])) {
					$info['data'] = json_decode($info['data'], true);
					APC::set($apcKey, $info);
				}
			}
		}

		return $info;
	}

	static public function solofbAll($clean = false) {
		static $list = array();
		if (empty($list)) {
			$apcKey = T_Key::WAR_FB_CHAPTER;
			$clean && B_Cache_APC::del($apcKey);

			$list = B_Cache_APC::get($apcKey);
			if (empty($list)) {
				$tmpC = array();
				$tc   = B_DB::instance('BaseWarFbChapter')->getAll();
				foreach ($tc as $valc) {
					$chapterId       = $valc['id'];
					$campaignTmpList = B_DB::instance('BaseWarFB')->getListByChapter($chapterId); //战役列表
					$campaignList    = array();
					if ($campaignTmpList) {
						foreach ($campaignTmpList as $tmpval) {
							$tmpval['checkpoint_data']            = json_decode($tmpval['checkpoint_data'], true);
							$campaignList[$tmpval['campaign_no']] = $tmpval;
						}
					}
					$list[$chapterId]            = $valc;
					$list[$chapterId]['fb_list'] = $campaignList;
				}

				APC::set($apcKey, $list);
			}
		}
		return $list;
	}

	static public function equipAll($clean = false) {
		static $list = array();
		if (empty($list)) {
			$apcKey = T_Key::BASE_EQUIP_TPL_LIST;
			$clean && B_Cache_APC::del($apcKey);

			$list = B_Cache_APC::get($apcKey);
			if (empty($list)) {
				$info = B_DB::instance('BaseEquipTpl')->getAll();
				$ids  = array();
				foreach ($info as $val) {
					$list[$val['id']] = $val;
					$ids[]            = $val['id'];
				}
				$list['ids'] = $ids;
				APC::set($apcKey, $list);
			}
		}
		return $list;
	}

	static public function equipSuitAll($clean = false) {
		static $list = array();
		if (empty($list)) {
			$apcKey = T_Key::BASE_EQUIP_SUIT_LIST;
			$clean && B_Cache_APC::del($apcKey);

			$list = B_Cache_APC::get($apcKey);
			if (empty($list)) {
				$info = B_DB::instance('BaseEquipSuit')->getAll();
				$ids  = array();
				foreach ($info as $val) {
					$list[$val['id']] = $val;
					$ids[]            = $val['id'];
				}
				$list['ids'] = $ids;
				APC::set($apcKey, $list);
			}
		}
		return $list;
	}

	/**
	 * 兑换基础信息
	 * @author huwei on 20121207
	 * @return array
	 */
	static public function exchangeAll($clean = false) {
		static $list = array();
		if (empty($list)) {
			$apcKey = T_Key::BASE_EXCHANGE;
			$clean && B_Cache_APC::del($apcKey);

			$list = B_Cache_APC::get($apcKey);
			if (empty($list)) {
				$data = array();
				$cate = array();
				$info = B_DB::instance('BaseExchange')->getAll();
				foreach ($info as $k => $item) {
					$item['need_props'] = B_Utils::str2arr($item['need_props']);
					$item['cost_val']   = B_Utils::str2arr($item['cost_val']);
					$data[$k]           = $item;
					if ($item['type'] == 1) {
						$cate[$item['type']][$item['sub_type']][] = $item;
					} else if ($item['type'] == 2) {
						list($lv, $suitId) = explode(',', $item['sub_type']);
						$cate[$item['type']][$lv][$suitId][] = $item;
					} else if ($item['type'] == 3) {
						$cate[$item['type']][] = $item;
					}
				}

				$list['cate'] = $cate;
				$list['data'] = $data;
				APC::set($apcKey, $list);
			}
		}
		return $list;
	}

	/**
	 * 得到基础分享数据
	 * @author duhuihui
	 */
	static public function qqshareAll($clean = false) {
		static $info = array();
		$arr = array();
		if (empty($info)) {
			$apcKey = T_Key::BASE_QQ_SHARE;
			$clean && B_Cache_APC::del($apcKey);

			$info = B_Cache_APC::get($apcKey);
			if (empty($info)) {
				$list = B_DB::instance('BaseQqShare')->all();
				foreach (M_QqShare::$type as $val) {
					foreach ($list as $qid => $qv) {
						$condPass = explode(',', $qv['cond_pass']);
						if ($val == $condPass[0]) {
							if (isset($condPass[1]) && isset($condPass[2])) {
								$arr[$val][$condPass[1] . '_' . $condPass[2]] = $qid;
							} else if (isset($condPass[1]) && !isset($condPass[2])) {
								$arr[$val][$condPass[1]] = $qid;
							} else if (!isset($condPass[1]) && !isset($condPass[2])) {
								$arr[$val] = $qid;
							}
						}
					}
				}
				foreach ($list as $qid => $qv) {
					$list[$qid]['cond_pass'] = $condPass;
				}
				$info['list'] = $list;
				$info['type'] = $arr;
				APC::set($apcKey, $info);
			}
		}
		return $info;

	}

	/**
	 * 获取突围基础信息
	 * @author chenhui on 20121020
	 * @return array 2D
	 */
	static public function breakoutAll($clean = false) {
		static $info = array();
		if (empty($info)) {
			$apcKey = T_Key::BASE_BREAKOUT;
			$clean && B_Cache_APC::del($apcKey);

			$info = B_Cache_APC::get($apcKey);
			if (empty($info)) {
				$info = B_DB::instance('BaseBreakout')->all();
				APC::set($apcKey, $info);
			}
		}
		return $info;
	}

	/**
	 * 传奇军官数据
	 * @author huwei on 20110617
	 * @return array
	 */
	static public function heroAll($clean = false) {
		static $list = array();
		if (empty($list)) {
			$apcKey = T_Key::BASE_HERO_TPL_LIST;
			$clean && B_Cache_APC::del($apcKey);

			$list = B_Cache_APC::get($apcKey);
			if (empty($list)) {
				$list    = array();
				$tmpList = B_DB::instance('BaseHeroTpl')->all();
				foreach ($tmpList as $val) {
					$list[$val['id']] = $val;
				}
				$ret = B_Cache_APC::set($apcKey, $list);
			}
		}
		return $list;
	}

	/**
	 * 获取据点基础信息
	 * @author huwei
	 * @return array
	 */
	static public function campaignAll($clean = false) {
		static $info = array();
		if (empty($info)) {
			$apcKey = T_Key::BASE_CAMPAIGN;
			$clean && B_Cache_APC::del($apcKey);

			$result = B_Cache_APC::get($apcKey);
			if (empty($result)) {
				$result = B_DB::instance('BaseCampaign')->all();
				$ret    = B_Cache_APC::set($apcKey, $result);
			}
			$info = $result;
		}
		return $info;
	}

	public function questBaseList($clean = false) {
		static $info = array();
		if (empty($info)) {
			$key = T_Key::BASE_QUEST;
			$clean && B_Cache_APC::del($key);

			$info = B_Cache_APC::get($key);
			if (empty($info)) {
				$list = B_DB::instance('BaseQuest')->all();
				$rel  = array();
				foreach ($list as $qid => $qv) {
					$qv['cond_pass']            = explode(',', $qv['cond_pass']);
					$info[$qv['prev_id']][$qid] = $qv;
				}
				APC::set($key, $info);
			}
		}
		return $info;

	}

	/**
	 * 获取开放属地条件
	 * @return array
	 */
	static public function getColonyNpcConf() {
		$vipConf = M_Vip::getVipConfig();
		$colConf = $vipConf['COLONY_OPEN'];

		$arrNeed = array();
		for ($colId = 1; $colId <= 3; $colId++) {
			foreach ($colConf as $vipLev => $strConf) {
				$arrConf = explode(',', $strConf);
				if (isset($arrConf[$colId - 1])) {
					$arrNeed[$colId] = array($vipLev, $arrConf[$colId - 1]);
					break;
				}
			}
		}
		return $arrNeed;
	}

	/**
	 * 获取开放属地条件
	 * @return array
	 */
	static public function getColonyCityConf() {
		$vipConf = M_Vip::getVipConfig();
		$colConf = $vipConf['CITY_COLONY_OPEN'];

		$arrNeed = array();
		for ($colId = 1; $colId <= 3; $colId++) {
			foreach ($colConf as $vipLev => $strConf) {
				$arrConf = explode(',', $strConf);
				if (isset($arrConf[$colId - 1])) {
					$arrNeed[$colId] = array($vipLev, $arrConf[$colId - 1]);
					break;
				}
			}
		}
		return $arrNeed;
	}

	static public function answer($id) {
		$apcKey = T_Key::BASE_QUESTION . '_' . $id;
		$info   = B_Cache_APC::get($apcKey);
		if (empty($info)) {
			$info = B_DB::instance('BaseQuestion')->get($id);
			APC::set($apcKey, $info);
		}
		return $info;
	}
}

?>
<?php

/**
 * 军官接口
 */
class C_Hero extends C_I {
	/**
	 * @see CSeekHero::AInfo
	 */
	public function ASeekInfo() {
		$obj = new C_SeekHero();
		return $obj->AInfo();
	}

	/**
	 * @see CSeekHero::AFind
	 */
	public function ASeekLegend() {
		$obj = new C_SeekHero();
		return $obj->AFind();
	}


	/**
	 * @see CSeekHero::ATryHireByProps
	 */
	public function ATryHireByProps($probPropsId = 0, $timePropsId = 0) {
		$obj = new C_SeekHero();
		return $obj->ATryHire($probPropsId, $timePropsId);
	}


	/**
	 * @CSeekHero::ASuccHire
	 */
	public function ASeekHire() {
		$obj = new C_SeekHero();
		return $obj->ASuccHire();
	}

	/**
	 * @CSeekHero::ACancel
	 */
	public function ASeekCancel() {
		$obj = new C_SeekHero();
		return $obj->ACancel();

	}

	/**
	 * @see CCollege::AList
	 */
	public function ACollegeInfo($payType = 0) {
		$errNo = '';
		$data  = array();
		return B_Common::result($errNo, $data);
	}


	/**
	 * 修改英雄名字
	 * @author huwei
	 * @param int $heroId 英雄的ID
	 * @param string $name 新英雄名字
	 * @param int pay 付费类型(1军饷 2点券)
	 * @return array[ErrNo,Data]
	 */
	public function AModifyName($heroId, $name, $pay) {

		$errNo     = T_ErrNo::ERR_ACTION;
		$data      = array();
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$heroId    = intval($heroId);

		if ($heroId > 0 && !empty($name) && in_array($pay, array(T_App::MILPAY, T_App::COUPON))) {
			$price = M_City::getConsumeVal('ModifyHeroName', $pay);
			$err   = '';
			if ($cityInfo['mil_pay'] < $price) {
				$err = T_ErrNo::NO_ENOUGH_MILIPAY;
			} else if ($nameErr = M_Hero::checkName($name)) {
				$err = $nameErr;
			}
			//检测英雄名字是否合法

			if (empty($err)) {
				$bCost  = $objPlayer->City()->decrCurrency($pay, $price, B_Log_Trade::E_UpHeroName, $heroId);
				$fields = array(
					'id'       => $heroId,
					'city_id'  => $cityInfo['id'],
					'nickname' => $name,
				);
				$ret    = $bCost && M_Hero::modifyName($fields);

				if ($ret) {

					$errNo  = '';
					$syInfo = array(
						'nickname' => $name
					);
					M_Sync::addQueue($cityInfo['id'], M_Sync::KEY_HERO, array($heroId => $syInfo)); //同步数据!
					$citySyncData = array('milpay' => $cityInfo['mil_pay'] - $price);
					M_Sync::addQueue($cityInfo['id'], M_Sync::KEY_CITY_INFO, $citySyncData); //同步数据!
				}
			} else {
				$errNo = $err;
			}
		}


		return B_Common::result($errNo, $data);
	}

	/**
	 * 解雇城市中的
	 * @author huwei
	 * @param int $heroId 当前城市中的英雄ID
	 * @return array[ErrNo,Data]
	 */
	public function AFire($heroId) {

		$errNo     = T_ErrNo::ERR_ACTION;
		$data      = array();
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$heroId    = intval($heroId);

		if ($heroId > 0) {
			$heroInfo = M_Hero::getCityHeroInfo($cityInfo['id'], $heroId);
			$n        = 0;
			foreach (T_Equip::$posFieldArr as $v) {
				if (!empty($heroInfo[$v])) {
					$n++;
				}
			}

			if (!empty($n) && M_Equip::isEquipNumFull($cityInfo['id'], $cityInfo['vip_level'])) {
				$errNo = T_ErrNo::FIRE_NUM_FULL;
			} else {
				$ret = M_Hero::fireHero($cityInfo, $heroInfo);
				if ($ret) {
					$errNo = '';
				}
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 当前城市英雄列表
	 * @author huwei
	 * @return array[ErrNo,Data]
	 */
	public function AList() {

		$errNo     = T_ErrNo::ERR_ACTION;
		$data      = array();
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		$errNo = '';

		$cityInfo = $objPlayer->getCityBase();
		$hasIds   = $objPlayer->instance('Team')->hasIds();

		$list = M_Hero::getCityHeroList($cityInfo['id']);

		$ml        = new M_March_List($cityInfo['pos_no']);
		$marchList = $ml->get();

		if (!empty($list)) {
			$recycleCfgArr  = M_Hero::getHeroRecycle();
			$heroArmyNumAdd = M_Hero::heroArmyNumAdd($cityInfo['id'], $cityInfo['union_id']);


			foreach ($list as $heroId) {
				$isFree   = false;
				$heroInfo = M_Hero::getCityHeroInfo($cityInfo['id'], $heroId);
				if (!empty($heroInfo['id'])) {
					$nextRecycleLv  = $heroInfo['recycle'] + 1;
					$nextRecycleArr = isset($recycleCfgArr[$nextRecycleLv]) ? $recycleCfgArr[$nextRecycleLv] : array();
					M_Hero::delTmpTraining($heroId);
					$tmpAttr = M_Hero::getTmpTraining($heroId);

					if (empty($heroInfo['march_id']) && in_array($heroInfo['flag'], array(T_Hero::FLAG_MOVE, T_Hero::FLAG_HOLD))) {
						Logger::error(array(__METHOD__, 'err hero flag for empty march_id', $cityInfo['id'], $heroId, $heroInfo['flag']));
						$heroInfo['flag'] = T_Hero::FLAG_FREE;
						M_Hero::setHeroInfo($heroId, array('flag' => T_Hero::FLAG_FREE));
					}

					$armyId = !empty($heroInfo['army_id']) ? $heroInfo['army_id'] : 1;

					$data[] = array(
						'HeroId'              => $heroId,
						'CityId'              => $heroInfo['city_id'],
						'NickName'            => $heroInfo['nickname'],
						'Gender'              => $heroInfo['gender'],
						'Quality'             => $heroInfo['quality'],
						'Level'               => (int)$heroInfo['level'],
						'FaceId'              => $heroInfo['face_id'],
						'IsLegend'            => 1,
						'Exp'                 => $heroInfo['exp'],
						'ExpNext'             => M_Formula::getGrowExp($heroInfo['level']),
						'AttrLead'            => $heroInfo['attr_lead'],
						'AttrCommand'         => $heroInfo['attr_command'],
						'AttrMilitary'        => $heroInfo['attr_military'],
						'AttrEnergy'          => $heroInfo['attr_energy'],
						//确定的培养属性点
						'TrainingLead'        => $heroInfo['training_lead'],
						'TrainingCommand'     => $heroInfo['training_command'],
						'TrainingMilitary'    => $heroInfo['training_military'],
						//未确定的培养属性点
						'TmpTrainingLead'     => !empty($tmpAttr['training_lead']) ? $heroInfo['training_lead'] + $tmpAttr['training_lead'] : 0,
						'TmpTrainingCommand'  => !empty($tmpAttr['training_command']) ? $heroInfo['training_command'] + $tmpAttr['training_command'] : 0,
						'TmpTrainingMilitary' => !empty($tmpAttr['training_military']) ? $heroInfo['training_military'] + $tmpAttr['training_military'] : 0,

						'SkillLead'           => $heroInfo['skill_lead'],
						'SkillCommand'        => $heroInfo['skill_command'],
						'SkillMilitary'       => $heroInfo['skill_military'],
						'SkillEnergy'         => $heroInfo['skill_energy'],
						'EquipLead'           => $heroInfo['equip_lead'],
						'EquipCommand'        => $heroInfo['equip_command'],
						'EquipMilitary'       => $heroInfo['equip_military'],

						'AttrMood'            => $heroInfo['attr_mood'],
						'StatPoint'           => floor($heroInfo['stat_point']),
						'GrowRate'            => $heroInfo['grow_rate'],
						'MaxArmyNum'          => M_Formula::calcHeroMaxArmyNum($heroInfo['level'], $heroInfo['skill_army_num'], $heroArmyNumAdd),
						'EquipArm'            => $heroInfo['equip_arm'],
						'EquipCap'            => $heroInfo['equip_cap'],
						'EquipUniform'        => $heroInfo['equip_uniform'],
						'EquipMedal'          => $heroInfo['equip_medal'],
						'EquipShoes'          => $heroInfo['equip_shoes'],
						'EquipSit'            => $heroInfo['equip_sit'],
						'EquipExp'            => $heroInfo['equip_exp'],
						'SkillSlotNum'        => $heroInfo['skill_slot_num'],
						'SkillSlot'           => $heroInfo['skill_slot'] ? $heroInfo['skill_slot'] : 0,
						'SkillSlot1'          => $heroInfo['skill_slot_1'] ? $heroInfo['skill_slot_1'] : 0,
						'SkillSlot2'          => $heroInfo['skill_slot_2'] ? $heroInfo['skill_slot_2'] : 0,
						'WinNum'              => $heroInfo['win_num'],
						'DrawNum'             => $heroInfo['draw_num'],
						'FailNum'             => $heroInfo['fail_num'],
						'RelifeTime'          => $heroInfo['relife_time'],
						'Fight'               => $heroInfo['fight'],
						'Flag'                => $heroInfo['flag'],
						'ArmyNum'             => $heroInfo['army_num'],
						'ArmyId'              => $armyId,
						'WeaponId'            => !empty($heroInfo['weapon_id']) ? $heroInfo['weapon_id'] : T_Hero::$army2weapon[$armyId],
						'FillFlag'            => $heroInfo['fill_flag'],
						'OnSale'              => $heroInfo['on_sale'],
						'MarchId'             => $heroInfo['march_id'],
						'Recycle'             => (int)$heroInfo['recycle'],
						'RecycleNext'         => $nextRecycleArr,
						'InTeam'              => isset($hasIds[$heroId]) ? $hasIds[$heroId] : 0,
					);
				} else {
					$ret = M_Hero::delCityHeroList($cityInfo['id'], $heroId);
					Logger::error(array(__METHOD__, 'del city hero list', array($cityInfo['id'], $heroId)));
				}
			}
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 获取英雄信息
	 * @author huwei
	 * @param int $heroId 英雄ID
	 * @return array[ErrNo,Data]
	 */
	public function AInfo($heroId) {

		$errNo     = T_ErrNo::ERR_ACTION;
		$data      = array();
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		if (!empty($heroId)) {
			$heroInfo       = M_Hero::getCityHeroInfo($cityInfo['id'], $heroId);
			$heroArmyNumAdd = M_Hero::heroArmyNumAdd($cityInfo['id'], $cityInfo['union_id']);
			if (!empty($heroInfo)) {
				$recycleCfgArr  = M_Hero::getHeroRecycle();
				$nextRecycleLv  = $heroInfo['recycle'] + 1;
				$nextRecycleArr = isset($recycleCfgArr[$nextRecycleLv]) ? $recycleCfgArr[$nextRecycleLv] : array();


				$errNo = '';
				//edit by hejunyun
				if ($heroInfo['flag'] == T_Hero::FLAG_DIE && $heroInfo['relife_time'] <= time()) {
					$res = M_Hero::changeHeroFlag($heroInfo['city_id'], array($heroId), T_Hero::FLAG_FREE, array('march_id' => 0));
					if ($res) {
						$heroInfo['flag']        = T_Hero::FLAG_FREE;
						$heroInfo['relife_time'] = 0;
					}
				}

				$cityInfo = $objPlayer->getCityBase();
				$hasIds   = $objPlayer->instance('Team')->hasIds();

				$data = array(
					'HeroId'        => $heroId,
					'CityId'        => $heroInfo['city_id'],
					'NickName'      => $heroInfo['nickname'],
					'Gender'        => $heroInfo['gender'],
					'Quality'       => $heroInfo['quality'],
					'Level'         => (int)$heroInfo['level'],
					'FaceId'        => $heroInfo['face_id'],
					'IsLegend'      => 1,
					'Exp'           => $heroInfo['exp'],
					'ExpNext'       => M_Formula::getGrowExp($heroInfo['level']),
					'AttrLead'      => $heroInfo['attr_lead'],
					'AttrCommand'   => $heroInfo['attr_command'],
					'AttrMilitary'  => $heroInfo['attr_military'],
					'AttrEnergy'    => $heroInfo['attr_energy'],

					'SkillLead'     => $heroInfo['skill_lead'],
					'SkillCommand'  => $heroInfo['skill_command'],
					'SkillMilitary' => $heroInfo['skill_military'],
					'SkillEnergy'   => $heroInfo['skill_energy'],

					'AttrMood'      => $heroInfo['attr_mood'],
					'StatPoint'     => floor($heroInfo['stat_point']),
					'GrowRate'      => $heroInfo['grow_rate'],
					'MaxArmyNum'    => M_Formula::calcHeroMaxArmyNum($heroInfo['level'], $heroInfo['skill_army_num'], $heroArmyNumAdd),
					'EquipArm'      => $heroInfo['equip_arm'],
					'EquipCap'      => $heroInfo['equip_cap'],
					'EquipUniform'  => $heroInfo['equip_uniform'],
					'EquipMedal'    => $heroInfo['equip_medal'],
					'EquipShoes'    => $heroInfo['equip_shoes'],
					'EquipSit'      => $heroInfo['equip_sit'],
					'EquipExp'      => $heroInfo['equip_exp'],
					'SkillSlotNum'  => $heroInfo['skill_slot_num'],
					'SkillSlot'     => $heroInfo['skill_slot'],
					'SkillSlot1'    => $heroInfo['skill_slot_1'],
					'SkillSlot2'    => $heroInfo['skill_slot_2'],
					'WinNum'        => $heroInfo['win_num'],
					'DrawNum'       => $heroInfo['draw_num'],
					'FailNum'       => $heroInfo['fail_num'],
					'RelifeTime'    => $heroInfo['relife_time'],
					'Fight'         => $heroInfo['fight'],
					'Flag'          => $heroInfo['flag'],
					'ArmyNum'       => $heroInfo['army_num'],
					'ArmyId'        => $heroInfo['army_id'],
					'WeaponId'      => $heroInfo['weapon_id'],
					'FillFlag'      => $heroInfo['fill_flag'],
					'Recycle'       => (int)$heroInfo['recycle'],
					'RecycleNext'   => $nextRecycleArr,
					'InTeam'        => isset($hasIds[$heroId]) ? $hasIds[$heroId] : 0,

				);
			}
		}


		return B_Common::result($errNo, $data);
	}

	/**
	 * 重置英雄属性点
	 * @author huwei
	 * @param int $heroId 英雄ID
	 * @param int $payType 付费类型(1军饷,2礼券)
	 * @return array[ErrNo,Data]
	 */
	public function AResetAttrPoint($heroId = 0, $payType = 1) {

		$errNo     = T_ErrNo::ERR_ACTION;
		$data      = array();
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		$heroId = intval($heroId);
		if ($heroId > 0 && in_array($payType, array(T_App::MILPAY, T_App::COUPON))) {
			$info       = M_Hero::getCityHeroInfo($cityInfo['id'], $heroId);
			$err        = '';
			$costMilpay = M_City::getConsumeVal('ResetHeroAttrPoint', $payType);
			if ($info['flag']) {
				$err = T_ErrNo::HERO_NOT_FREE;
			}
			if (empty($costMilpay)) {
				$err = T_ErrNo::ERR_CONF;
			} else if ($cityInfo['mil_pay'] < $costMilpay) {
				$err = T_ErrNo::NO_ENOUGH_MILIPAY;
			} else if (empty($info['id'])) {
				$err = T_ErrNo::HERO_NO_EXIST;
			} else if ($info['attr_lead'] + $info['attr_command'] + $info['attr_military'] == 0) {
				$err = T_ErrNo::HERO_ERR_POINT;
			}

			$bCost = $objPlayer->City()->decrCurrency($payType, $costMilpay, B_Log_Trade::E_WashHeroAttr, $heroId);

			if (empty($err) && $bCost) {
				$totalPoints = $info['attr_lead'] + $info['attr_command'] + $info['attr_military'];
				$total       = $info['stat_point'] + $totalPoints;

				$minPoint = floor($total * 0.1);
				$fields   = array(
					'id'            => $heroId,
					'city_id'       => $cityInfo['id'],
					'attr_lead'     => $minPoint,
					'attr_command'  => $minPoint,
					'attr_military' => $minPoint,
					'stat_point'    => $total - ($minPoint * 3),
				);

				$ret = M_Hero::resetAttrPoint($fields);

				$errNo = '';
				$data  = array(
					'StatPoint'    => floor($total - ($minPoint * 3)),
					'AttrLead'     => $minPoint,
					'AttrCommand'  => $minPoint,
					'AttrMilitary' => $minPoint
				);

				$cityInfo = M_City::getInfo($cityInfo['id']);
				M_Sync::addQueue($cityInfo['id'], M_Sync::KEY_CITY_INFO, array('milpay' => $cityInfo['mil_pay'], 'coupon' => $cityInfo['coupon'])); //同步数据!
				M_Sync::addQueue($cityInfo['id'], M_Sync::KEY_HERO, array($heroId => $fields)); //同步数据!
			} else {
				$errNo = $err;
			}
		}


		return B_Common::result($errNo, $data);
	}

	/**
	 * 更新英雄属性点
	 * @author huwei
	 * @param int $heroId 英雄ID
	 * @param int $addL 增加的统帅点数
	 * @param int $addC 增加的指挥点数
	 * @param int $addM 增加的军事点数
	 * @return array[ErrNo,Data]
	 */
	public function AIncrAttrPoint($heroId = 0, $addL = 0, $addC = 0, $addM = 0) {

		$errNo     = T_ErrNo::ERR_ACTION;
		$data      = array();
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$heroId    = intval($heroId);
		$total     = intval($addL) + intval($addC) + intval($addM);

		if ($heroId > 0 && $total > 0) {
			$errNo = M_Hero::updateAttrPoint($cityInfo['id'], $heroId, $addL, $addC, $addM);
			if (empty($errNo)) {
				$errNo = '';
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 复活英雄
	 * @author huwei
	 * @param int $heroId 英雄ID
	 * @param int $payType 付费类型(1军饷,2礼券,0正常)
	 * @return array[ErrNo,Data]
	 */
	public function ARelife($heroId = 0, $payType = T_App::MILPAY) {
		$errNo = T_ErrNo::ERR_ACTION;
		$data  = array();


		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$heroId    = intval($heroId);
		if ($heroId > 0) {
			$heroInfo = M_Hero::getCityHeroInfo($cityInfo['id'], $heroId);

			if (!empty($heroInfo) && $heroInfo['flag'] == T_Hero::FLAG_DIE) {
				if ($payType == T_App::MILPAY) {
					$errNo = T_ErrNo::HERO_RELIFE_FAIL;
					$diff  = max($heroInfo['relife_time'] - time(), 0);
					$price = M_Formula::heroRelifeCost($diff);

					if ($diff > 0 && $price > 0 && $cityInfo['mil_pay'] >= $price) {
						$bCost = $objPlayer->City()->decrCurrency($payType, $price, B_Log_Trade::E_ResurrectHero, $heroId);
						$ret   = $bCost && M_Hero::changeHeroFlag($cityInfo['id'], array($heroId), T_Hero::FLAG_FREE, array('march_id' => 0));
						if ($ret) {
							$msyInfo = array(
								'id'   => $heroId,
								'flag' => T_Hero::FLAG_FREE
							);
							$errNo   = '';


							$cityInfo = M_City::getInfo($cityInfo['id']);
							M_Sync::addQueue($cityInfo['id'], M_Sync::KEY_CITY_INFO, array('milpay' => $cityInfo['mil_pay'])); //同步数据!
							M_Sync::addQueue($cityInfo['id'], M_Sync::KEY_HERO, array($heroId => $msyInfo)); //同步数据!
						}
					}
				} else {
					$errNo = T_ErrNo::ERR_PAY;
					//$errNo = M_Hero::relifeUseGold($cityInfo['id'], $cityInfo['gold'], $heroId, $heroInfo['level']);
				}
			}
		}


		return B_Common::result($errNo, $data);

	}

	/**
	 * 武装英雄 配兵
	 * @author huwei
	 * @param int $heroId 英雄ID
	 * @param int $armyNum 配兵数量
	 * @param int $armyId 兵种ID
	 * @param int $weaponId 武器ID
	 * @return array[ErrNo,Data]
	 */
	public function AFitArmy($heroId, $armyNum = 0, $armyId = 0, $weaponId = 0) {

		$ret   = false;
		$errNo = T_ErrNo::ERR_ACTION;
		$data  = array();

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		$armyNum  = intval($armyNum);
		$weaponId = intval($weaponId);

		$heroInfo = M_Hero::getCityHeroInfo($cityInfo['id'], $heroId);

		$weaponId = !empty($weaponId) ? $weaponId : intval($heroInfo['weapon_id']);
		$armyId   = intval($heroInfo['army_id']);

		$heroArmyNumAdd = M_Hero::heroArmyNumAdd($cityInfo['id'], $cityInfo['union_id']);
		$maxArmyNumArr  = M_Formula::calcHeroMaxArmyNum($heroInfo['level'], $heroInfo['skill_army_num'], $heroArmyNumAdd);

		list($armyHasNum, $armyHasLv, $armyHasExp) = $objPlayer->Army()->getById($armyId);

		$offsetNum   = $armyNum - $heroInfo['army_num'];
		$leftArmyNum = $objPlayer->Army()->addNum($armyId, -$offsetNum);

		$weaponInfo = M_Weapon::baseInfo($weaponId);

		if (empty($heroInfo)) {
			return B_Common::result(T_ErrNo::HERO_NO_EXIST);
		} else if ($heroInfo['flag'] != T_Hero::FLAG_FREE) {
			return B_Common::result(T_ErrNo::HERO_NOT_FREE);
		} else if (!empty($armyId) && !isset(M_Army::$type[$armyId])) {
			return B_Common::result(T_ErrNo::ARMY_ID_ILLEGAL);
		} else if ($heroInfo['city_id'] != $cityInfo['id']) { //不属于当前城市
			return B_Common::result(T_ErrNo::ERR_ACTION);
		} else if ($armyNum > $maxArmyNumArr[$armyId]) { //兵种数量超过能拥有的兵数
			return B_Common::result(T_ErrNo::ARMY_NUM_EXCEED);
		} else if ($leftArmyNum < 0) { //预备兵数量不足
			return B_Common::result(T_ErrNo::NO_ENOUGH_ARMY);
		} else if (!$objPlayer->Weapon()->hasWeapon($weaponId)) {
			return B_Common::result(T_ErrNo::WEAPON_NOT_HAVE);
		} else if ($weaponInfo['army_id'] != $armyId) { //武器和兵种不匹配
			return B_Common::result(T_ErrNo::WEAPON_NOT_MATCH);
		} else if ($weaponInfo['need_army_lv'] > $armyHasLv) { //兵种等级不够
			return B_Common::result(T_ErrNo::ARMY_LEVEL_NO_ENOUGH);
		}

		$objPlayer->Quest()->check('army_fit', array('id' => $armyId, 'num' => $armyNum));
		$objPlayer->save();

		$heroInfo['army_num'] = $armyNum;
		$ret                  = M_Hero::updateFitArmy($heroInfo['city_id'], $heroInfo['id'], $armyNum, $armyId, $weaponId);

		$msRow[$armyId] = $objPlayer->Army()->getById($armyId);
		M_Sync::addQueue($cityInfo['id'], M_Sync::KEY_ARMY, $msRow);


		return B_Common::result('', $data);
	}


	/**
	 * 英雄自动补兵功能
	 * @author huwei
	 * @param int $switch (1开0关)
	 * @return array
	 */
	public function AAutoFillSwitch($heroId, $switch = 1) {
		$ret   = false;
		$errNo = T_ErrNo::ERR_ACTION;
		$data  = array();

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		if (!empty($heroId) && in_array($switch, array(1, 0))) {
			$heroInfo = M_Hero::getCityHeroInfo($cityInfo['id'], $heroId);
			$errNo    = '';
			if ($switch != $heroInfo['fill_flag']) {
				$params                    = array('fill_flag' => $switch);
				$ret                       = M_Hero::setHeroInfo($heroInfo['id'], $params);
				$syncList[$heroInfo['id']] = $params;
				$ret && M_Sync::addQueue($cityInfo['id'], M_Sync::KEY_HERO, $syncList); //同步数据!
			} else {
				$ret    = true;
				$switch = (int)$heroInfo['fill_flag'];
			}
			$data['HeroAutoFill'] = $switch;
		}

		if ($ret) {
			$errNo = '';
		}

		return B_Common::result($errNo, $data);
	}

	public function AFitEquip($heroId, $equipId) {

		$data    = array();
		$errNo   = T_ErrNo::ERR_ACTION;
		$heroId  = intval($heroId);
		$equipId = intval($equipId);

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		if ($heroId && $equipId) {
			$cityId    = $cityInfo['id'];
			$heroInfo  = M_Hero::getCityHeroInfo($cityId, $heroId); //英雄信息
			$equipInfo = M_Equip::getCityEquipById($cityId, $equipId); //城市装备信息

			if (empty($equipInfo['id'])) { //装备不存在
				$err = T_ErrNo::EQUIP_NO_EXIST;
			} else if (empty($heroInfo['id'])) { //军官不存在
				$err = T_ErrNo::HERO_NO_EXIST;
			} else if ($heroInfo['flag'] != T_Hero::FLAG_FREE) { //军官非空闲
				$err = T_ErrNo::HERO_NOT_FREE;
			} else if (!empty($equipInfo['is_use']) && $equipInfo['is_use'] != $heroId) { //装备的使用者不是当前军官
				$err = T_ErrNo::HERO_EQUIP_AGIN;
			}

			if (empty($err)) {
				//英雄装备位置对应字段
				$posField = T_Equip::$posFieldArr[$equipInfo['pos']];

				$newEquipId = $equipId;
				$oldEquipId = 0;
				if (!empty($heroInfo[$posField])) {
					$oldEquipId = $heroInfo[$posField];
				}

				if ($newEquipId == $oldEquipId) { //装备相同则卸下操作
					$newEquipId = 0;
				}

				if ($newEquipId == 0 && $oldEquipId > 0 && M_Equip::isEquipNumFull($cityId, $cityInfo['vip_level'])) {
					$errNo = T_ErrNo::EQUI_NUM_FULL;
				} else {
					$errFlag       = array();
					$syncEquipData = array();
					//更新当前军官位置武器
					$bSet = M_Hero::setHeroInfo($heroId, array($posField => $newEquipId));
					if ($bSet) { //同步英雄装备数据!
						M_Sync::addQueue($cityId, M_Sync::KEY_HERO, array($heroId => array($posField => $newEquipId)));
					} else {
						$errFlag[] = array('heroId' => $heroId, 'pos' => $posField, 'new' => $newEquipId);
					}

					$equipNum = 0;
					$bDown    = true;
					if ($oldEquipId > 0) { //卸装备
						$bDown = M_Equip::setInfo($oldEquipId, array('is_use' => 0));
						if ($bDown) {
							$syncEquipData[$oldEquipId] = array(
								'_0'           => M_Sync::SET,
								'is_use'       => 0,
								'hero_name'    => '',
								'hero_quality' => ''
							);
							$equipNum++;
						} else {
							$errFlag[] = array('heroId' => $heroId, 'old' => $oldEquipId, 'is_use' => 0);
						}
					}

					if ($bDown && $newEquipId > 0) { //穿装备
						$bUp = M_Equip::setInfo($newEquipId, array('is_use' => $heroId));
						if ($bUp) {
							$syncEquipData[$newEquipId] = array(
								'_0'           => M_Sync::SET,
								'is_use'       => $heroId,
								'hero_name'    => $heroInfo['nickname'],
								'hero_quality' => $heroInfo['quality'],
							);
							$equipNum--;
						} else {
							$errFlag[] = array('heroId' => $heroId, 'new' => $newEquipId, 'is_use' => $heroId);
						}
					}

					M_Equip::incrCityEquipNum($cityId, $equipNum);

					M_Sync::addQueue($cityId, M_Sync::KEY_EQUIP, $syncEquipData);


					$errNo = '';
					//如果之前英雄位置 有装备 则 为1 否则为0
					$data = array('OLD_POS' => !empty($oldEquipId) ? 1 : 0);

					if (!empty($errFlag)) {
						Logger::error(array(__METHOD__, $errFlag));
					}
				}
			} else {
				$errNo = $err;
			}
		}


		return B_Common::result($errNo, $data);
	}


	/**
	 * @see CSkill::ALearn
	 */
	public function ALearnSkill($heroId, $sLevel, $payType = 1) {
		$obj = new C_Skill();
		return $obj->ALearn($heroId, $sLevel, $payType);
	}

	/**
	 * @see CSkill::AForget
	 */
	public function AForgetSkill($heroId, $slotId, $payType) {
		$obj = new C_Skill();
		return $obj->AForget($heroId, $slotId, $payType);
	}

	/**
	 * 设置军官在防御中是否出战
	 * @author Hejunyun
	 * @param int $heroList 英雄ID,英雄ID,英雄ID
	 * @param int $fight 战争类型[1被轰炸 2被攻击]
	 */
	public function ASetHeroFight($heroList, $fight) {
		/** 操作内存版本*/

		$errNo = T_ErrNo::ERR_ACTION;
		$fight = intval($fight);

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		$heroList = explode(',', $heroList);
		$res      = 0;
		if (is_array($heroList) && count($heroList) < 6 && in_array($fight, array(1, 2))) {
			$mylist = M_Hero::getCityHeroList($cityInfo['id']);
			//先取消未选中的原出战军官
			foreach ($mylist as $val) {
				$ret = M_Hero::cancelHeroFight($val, $fight);
				!$ret && $res = $res + 1;
			}
			//再指派选中的军官出战
			foreach ($heroList as $val) {
				if (in_array($val, $mylist)) {
					$ret = M_Hero::setHeroFight($val, $fight);
					!$ret && $res = $res + 1;
				}
			}

			!$res && $errNo = '';
		}


		$data = array();

		return B_Common::result($errNo, $data);
	}

	/**
	 * 重置所有英雄兵力
	 * @author Hejunyun
	 */
	public function AResetHeroArmy() {

		$errNo     = T_ErrNo::ERR_ACTION;
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$syncList  = array();
		$list      = M_Hero::getCityHeroList($cityInfo['id']);
		$objArmy   = $objPlayer->Army();

		$errNum = 0;
		foreach ($list as $heroId) {
			$heroInfo = M_Hero::getCityHeroInfo($cityInfo['id'], $heroId);

			if (isset($heroInfo['id']) && T_Hero::FLAG_FREE == $heroInfo['flag'] && $heroInfo['army_id'] > 0) { //只操作空闲状态
				$armyId  = $heroInfo['army_id'];
				$armyNum = $heroInfo['army_num'];

				$objPlayer->Army()->addNum($armyId, $armyNum);

				$objPlayer->save();
				$setArr = array(
					'weapon_id' => T_Hero::$army2weapon[$armyId],
					'army_num'  => 0
				);

				$ret = M_Hero::setHeroInfo($heroId, $setArr);
				if ($ret) {
					$syncList[$heroId] = $setArr;
				} else {
					$errNum++;
				}
			}
		}

		if ($errNum == 0) {
			$msRow = $objPlayer->Army()->get();
			M_Sync::addQueue($cityInfo['id'], M_Sync::KEY_ARMY, $msRow);

			M_Sync::addQueue($cityInfo['id'], M_Sync::KEY_HERO, $syncList); //同步数据!

			$objPlayer->save();
			$errNo = '';
		}

		$data = array();
		return B_Common::result($errNo, $data);
	}

	/**
	 * 自动补满所有英雄兵力
	 * @author huwei
	 */
	public function AFillHeroArmy() {
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$cityId    = $cityInfo['id'];
		$heroList  = M_Hero::getCityHeroList($cityId);
		$ret       = M_Hero::fillHeroArmyNumByHeroId($cityId, $heroList, true);

		$errNo = T_ErrNo::NO_ENOUGH_ARMY;
		if ($ret) {
			$errNo = '';
		}
		$data = array();
		return B_Common::result($errNo, $data);
	}

	/**
	 * 军官培养
	 * @author Hejunyun
	 * @param int $heroId 军官ID
	 * @param int $type 类型 1军功普通培养 2军饷中级培养 3军饷高级培养
	 * @return array
	 */
	public function ATraining($heroId, $type = 1) {

		$errNo     = T_ErrNo::ERR_ACTION;
		$data      = array();
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$cityId    = $cityInfo['id'];
		$heroInfo  = M_Hero::getCityHeroInfo($cityId, $heroId);
		if (isset($heroInfo['id'])) {
			$trainNumArr   = M_Hero::getTrainingNum($cityId);
			$num           = !empty($trainNumArr[$type]) ? $trainNumArr[$type] : 0;
			$tmpNum        = M_Hero::calcTrainNum($num, $type);
			$isContinue    = false;
			$confHeroTrain = M_Config::getVal('hero_train');

			if ($type == T_Hero::TRAINING_TYPE_ONE) //军功
			{
				$needWarexp = $confHeroTrain['cost'][$type] * $tmpNum;
				if ($cityInfo['mil_medal'] >= ($needWarexp + T_Hero::TRAINING_MIN_MEDAL)) {
					$setInfo    = array(
						'mil_medal' => $cityInfo['mil_medal'] - $needWarexp
					);
					$isContinue = M_City::setCityInfo($cityId, $setInfo);
					$syInfo     = array(
						'mil_medal' => $setInfo['mil_medal']
					);
					$isContinue && M_Sync::addQueue($cityId, M_Sync::KEY_CITY_INFO, $syInfo);
				} else { //军功不足
					$errNo = T_ErrNo::TRAINING_NOT_ENOUGH;
				}
			} else { //军饷
				$needMilpay = $confHeroTrain['cost'][$type] * $tmpNum;
				if ($needMilpay == 0) {
					$isContinue = true;
				} else if ($cityInfo['mil_pay'] >= $needMilpay) {
					$tmpData    = $heroInfo['nickname'] . ':' . $heroInfo['training_lead'] . ',' . $heroInfo['training_command'] . ',' . $heroInfo['training_military'];
					$isContinue = $objPlayer->City()->decrCurrency(T_App::MILPAY, $needMilpay, B_Log_Trade::E_TrainingHero, $tmpData);
				} else {
					//军饷不足
					$errNo = T_ErrNo::NO_ENOUGH_MILIPAY;
				}
			}

			if ($isContinue) {
				M_Hero::setTrainingNum($cityId, $type);

				$maxTraining = M_Hero::getTrainingMaxValue($heroInfo['level'], $heroInfo['quality'], $heroInfo['recycle']); //单项属性最大上限 根据等级、品质计算
				$tplArr      = array(
					'training_lead'     => !empty($heroInfo['training_lead']) ? $heroInfo['training_lead'] : 0,
					'training_command'  => !empty($heroInfo['training_command']) ? $heroInfo['training_command'] : 0,
					'training_military' => !empty($heroInfo['training_military']) ? $heroInfo['training_military'] : 0
				);
				$setArr      = array();
				$upAttrNum   = B_Utils::dice(T_Hero::$trainingRate[$type]); //增长几个属性
				//Logger::debug(array(__METHOD__, 'up attr num', $upAttrNum));
				if ($upAttrNum > 0) {
					$setKey  = (array)array_rand($tplArr, $upAttrNum); //增长哪几个属性
					$minGrow = T_Hero::$trainingGrow[$type][0]; //最小增长多少点
					$maxGrow = T_Hero::$trainingGrow[$type][1]; //最大增长多少点

					foreach ($setKey as $key) //增加随机到的属性点
					{
						$rndVal       = rand($minGrow, $maxGrow);
						$setArr[$key] = min($maxTraining, $tplArr[$key] + max($rndVal, 0));
						unset($tplArr[$key]);
					}
					//没随到的属性点维持或减少
					if (!empty($tplArr)) {
						foreach ($tplArr as $k => $v) {
							$setArr[$k] = M_Hero::getDownValue($v, $type);
						}
					}
				} else { //全都下降
					foreach ($tplArr as $k => $v) {
						$setArr[$k] = M_Hero::getDownValue($v, $type);
					}
				}

				if (!empty($setArr)) {
					$objPlayer = new O_Player($cityId);
					$objPlayer->Quest()->check('hero_train', array('num' => 1));
					$objPlayer->save();

					$tmpArr = array();
					foreach ($setArr as $kName => $vData) {
						$tmpArr[$kName] = $vData - $heroInfo[$kName];
					}
					//Logger::debug(array(__METHOD__, $tmpArr));
					$ret = M_Hero::setTmpTraining($heroId, $tmpArr);
					if ($ret) {
						$flag          = T_App::SUCC;
						$errNo         = '';
						$data          = $setArr;
						$heroTrainNeed = array();
						foreach ($confHeroTrain['cost'] as $kT => $vT) {
							$num                = !empty($trainNumArr[$kT]) ? $trainNumArr[$kT] : 0;
							$num                = ($kT == $type) ? $num + 1 : $num;
							$nextNum            = M_Hero::calcTrainNum($num, $kT);
							$needNum            = $confHeroTrain['cost'][$kT] * $nextNum;
							$heroTrainNeed[$kT] = array((int)$num, (int)$needNum);
						}

						$data = array(
							'TmpTrainingLead'     => $setArr['training_lead'],
							'TmpTrainingCommand'  => $setArr['training_command'],
							'TmpTrainingMilitary' => $setArr['training_military'],
							'HeroTrainNeed'       => $heroTrainNeed,
						);
						M_QqShare::check($objPlayer, 'hero_train', array('num' => max($setArr['training_lead'], $setArr['training_command'], $setArr['training_military'])));
						$dataStr = $setArr['training_lead'] . '_' . $setArr['training_command'] . '_' . $setArr['training_military'];
						Logger::opHero($cityId, $heroId, Logger::H_ACT_CULTURE, $dataStr);
					}
				}
			}
		} else {
			//军官不存在
			$errNo = T_ErrNo::HERO_NO_EXIST;
		}


		return B_Common::result($errNo, $data);
	}

	/**
	 * 是否覆盖培养属性点
	 * @author Hejunyun
	 * @param int $heroId 军官ID
	 * @param int $isCover 是否覆盖 1是 0否
	 */
	public function AIsCover($heroId, $isCover = 1) {

		$errNo     = T_ErrNo::ERR_ACTION;
		$data      = array();
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$heroInfo  = M_Hero::getCityHeroInfo($cityInfo['id'], $heroId);
		if (isset($heroInfo['id'])) {
			$tmpArr = M_Hero::getTmpTraining($heroId);
			if ($isCover == 1 && count($tmpArr) == 3) {
				$setArr      = array();
				$maxTraining = M_Hero::getTrainingMaxValue($heroInfo['level'], $heroInfo['quality'], $heroInfo['recycle']);
				foreach ($tmpArr as $kName => $vData) {
					$setArr[$kName] = min(max($vData + $heroInfo[$kName], 0), $maxTraining);
				}
				//Logger::debug(array(__METHOD__, $tmpArr, $setArr));
				$ret = M_Hero::setHeroInfo($heroId, $setArr);
				if ($ret) {
					$rc = new B_Cache_RC(T_Key::HERO_TRAINING_TMP, $heroId);
					$rc->delete();

					$syncList = array($heroId => $setArr);
					M_Sync::addQueue($cityInfo['id'], M_Sync::KEY_HERO, $syncList); //同步数据!

					$errNo = '';
				}
			}
		} else {
			//军官不存在
			$errNo = T_ErrNo::HERO_NO_EXIST;
		}


		return B_Common::result($errNo, $data);
	}

	/**
	 * 军官兑换
	 * @author duhuihui
	 * @param int $heroId1 英雄1ID
	 * @param int $equipId2 英雄2ID
	 * @param int $exchangeType 兑换模式
	 */
	public function AHeroExchange($heroId1 = 0, $heroId2 = 0, $exchangeType = 0) {

		$errNo        = T_ErrNo::ERR_ACTION;
		$heroId1      = intval($heroId1);
		$heroId2      = intval($heroId2);
		$exchangeType = intval($exchangeType);

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		$data                  = array();
		$tmpArr[$heroId1]      = 0;
		$tmpArr[$heroId2]      = 0;
		$tmpArr[$exchangeType] = 0;
		$awardList             = array();
		if (count($tmpArr) == 3) {
			$cityId = $cityInfo['id'];
			$info1  = M_Hero::getCityHeroInfo($cityId, $heroId1);
			$info2  = M_Hero::getCityHeroInfo($cityId, $heroId2);

			$heroArmyNumAdd = M_Hero::heroArmyNumAdd($cityInfo['id'], $cityInfo['union_id']);

			$heroExchange = array();
			$heroExchange = M_Config::getVal('hero_exchange');
			$exchangeInfo = array();
			//根据兑换模式获得需要花费的军饷和奖励ID
			if (!empty($heroExchange[$info1['quality']]) && !empty($heroExchange[$info2['quality']]) && !empty($info1['quality']) && !empty($info2['quality']) && $info1['quality'] == $info2['quality']) {
				$exchangeInfo = $heroExchange[$info1['quality']][$exchangeType];
			}
			$tmp1    = array();
			$tmp2    = array();
			$tep1Sum = 0;
			$tep2Sum = 0;
			$tmp1    = array($info1['equip_arm'], $info1['equip_cap'], $info1['equip_uniform'], $info1['equip_medal'], $info1['equip_shoes'], $info1['equip_sit']);
			$tmp2    = array($info2['equip_arm'], $info2['equip_cap'], $info2['equip_uniform'], $info2['equip_medal'], $info2['equip_shoes'], $info2['equip_sit']);
			$tep1Sum = array_sum($tmp1);
			$tep2Sum = array_sum($tmp2);
			$err     = '';


			$awardId = $exchangeInfo['awardId'];

			$awardArr  = M_Award::rateResult($awardId);
			$awardList = M_Award::toText($awardArr);


			$heroTplId = $awardList[0][1];

			if ((!empty($tep1Sum) || !empty($tep2Sum)) && M_Equip::isEquipNumFull($cityInfo['id'], $cityInfo['vip_level'])) {
				$err = T_ErrNo::EQUI_NUM_FULL;
			}
			if ($info1['flag'] != T_Hero::FLAG_FREE) {
				$err = T_ErrNo::HERO_NOT_FREE;
			} else if ($info1['quality'] != $info2['quality']) {
				$err = T_ErrNo::EXCHANGE_HERO_QUALITY_LOW; //英雄不能兑换,必须是同名同等级同品质紫，红，金的传奇军官
			} else if (empty($exchangeInfo)) {
				$err = T_ErrNo::ERR_ACTION;
			} else if ($cityInfo['mil_pay'] < $exchangeInfo['cost']) {
				$err = T_ErrNo::NO_ENOUGH_MILIPAY;
			} else if (empty($heroTplId)) {
				$err = T_ErrNo::ERR_ACTION;
			}

			if (empty($err)) {
				$bCost = $objPlayer->City()->decrCurrency(T_App::MILPAY, $exchangeInfo['cost'], B_Log_Trade::HERO_EXCHANGE, $heroTplId);
				if ($bCost) {
					$ret1 = M_Hero::fireHero($cityInfo, $info1);
					$ret2 = M_Hero::fireHero($cityInfo, $info2);
				}
				if ($ret1 && $ret2) {
					$heroId   = M_Hero::moveTplHeroToCityHero($objPlayer, $heroTplId, Logger::H_HERO_EXCHANGE);
					$heroInfo = M_Hero::getCityHeroInfo($cityId, $heroId); //城市英雄ID
					if (!empty($heroInfo)) {
						$data = array(
							'HeroId'        => $heroId,
							'CityId'        => $heroInfo['city_id'],
							'NickName'      => $heroInfo['nickname'],
							'Gender'        => $heroInfo['gender'],
							'Quality'       => $heroInfo['quality'],
							'Level'         => $heroInfo['level'],
							'FaceId'        => $heroInfo['face_id'],
							'IsLegend'      => 1,
							'Exp'           => $heroInfo['exp'],
							'ExpNext'       => M_Formula::getGrowExp($heroInfo['level']),
							'AttrLead'      => $heroInfo['attr_lead'],
							'AttrCommand'   => $heroInfo['attr_command'],
							'AttrMilitary'  => $heroInfo['attr_military'],
							'AttrEnergy'    => $heroInfo['attr_energy'],

							'SkillLead'     => $heroInfo['skill_lead'],
							'SkillCommand'  => $heroInfo['skill_command'],
							'SkillMilitary' => $heroInfo['skill_military'],
							'SkillEnergy'   => $heroInfo['skill_energy'],

							'AttrMood'      => $heroInfo['attr_mood'],
							'StatPoint'     => floor($heroInfo['stat_point']),
							'GrowRate'      => $heroInfo['grow_rate'],
							'MaxArmyNum'    => M_Formula::calcHeroMaxArmyNum($heroInfo['level'], $heroInfo['skill_army_num'], $heroArmyNumAdd),
							'EquipArm'      => $heroInfo['equip_arm'],
							'EquipCap'      => $heroInfo['equip_cap'],
							'EquipUniform'  => $heroInfo['equip_uniform'],
							'EquipMedal'    => $heroInfo['equip_medal'],
							'EquipShoes'    => $heroInfo['equip_shoes'],
							'EquipSit'      => $heroInfo['equip_sit'],
							'SkillSlotNum'  => $heroInfo['skill_slot_num'],
							'SkillSlot'     => $heroInfo['skill_slot'],
							'SkillSlot1'    => $heroInfo['skill_slot_1'],
							'SkillSlot2'    => $heroInfo['skill_slot_2'],
							'WinNum'        => $heroInfo['win_num'],
							'DrawNum'       => $heroInfo['draw_num'],
							'FailNum'       => $heroInfo['fail_num'],
							'RelifeTime'    => $heroInfo['relife_time'],
							'Fight'         => $heroInfo['fight'],
							'Flag'          => $heroInfo['flag'],
							'ArmyNum'       => $heroInfo['army_num'],
							'ArmyId'        => $heroInfo['army_id'],
							'WeaponId'      => $heroInfo['weapon_id'],
							'FillFlag'      => $heroInfo['fill_flag'],
						);
					}

					$errNo = '';
				}

			} else {
				$errNo = $err;
			}
		}


		return B_Common::result($errNo, $data);
	}

	//军官转生
	public function ARecycle($heroId) {

		$errNo     = T_ErrNo::ERR_ACTION;
		$data      = array();
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$heroId    = intval($heroId);

		if ($heroId > 0) {
			//转生丹道具ID
			//$propsId = M_Props::HERO_RECYCLE_PROPS_ID;
			$propsId  = M_Props::getRecycleId();
			$heroInfo = M_Hero::getCityHeroInfo($cityInfo['id'], $heroId);

			if ($heroInfo['id']) {
				$newRecycleLv   = $heroInfo['recycle'] + 1;
				$cfgArr         = M_Hero::getHeroRecycle();
				$recycleCfgAttr = M_Hero::getHeroRecycleAttr();

				if (isset($cfgArr[$newRecycleLv]) && isset($recycleCfgAttr[$newRecycleLv][$heroInfo['quality']])) {
					$tmp1    = array($heroInfo['equip_arm'], $heroInfo['equip_cap'], $heroInfo['equip_uniform'], $heroInfo['equip_medal'], $heroInfo['equip_shoes'], $heroInfo['equip_sit']);
					$tep1Sum = array_sum($tmp1);

					$err = '';
					list($lv, $rate, $num, $add) = $cfgArr[$newRecycleLv];
					if ($heroInfo['level'] < $lv) {
						$err = T_ErrNo::HERO_RECYCLE_LV;
					} else if (!M_Props::checkCityPropsNum($cityInfo['id'], $propsId, -1, $num)) {
						$err = T_ErrNo::HERO_RECYCLE_PROPS_NUM;
					} else if (!empty($tep1Sum) && M_Equip::isEquipNumFull($cityInfo['id'], $cityInfo['vip_level'])) {
						$err = T_ErrNo::HERO_RECYCLE_EQUIP_FULL;
					}

					if (empty($err)) {
						$bCost = $objPlayer->Pack()->decrNumByPropId($propsId, 1);
						if ($bCost) {

							$errNo = '';
							$succ  = 0;

							$odds = B_Utils::odds($rate);
							if ($odds) {
								$num = intval($recycleCfgAttr[$newRecycleLv][$heroInfo['quality']]);
								//第一：0.1~0.4，A
								//第二：0.5-A~0.5，B
								//第三：1-A-B
								$attr1    = floor($num * rand(10, 40) / 100);
								$attr2    = rand(floor($num * 0.5) - $attr1, floor($num * 0.5));
								$attr3    = $num - $attr1 - $attr2;
								$fieldArr = array(
									'recycle'       => $newRecycleLv,
									'grow_rate'     => $add + $heroInfo['grow_rate'],
									'level'         => 1,
									'exp'           => 0,
									'attr_lead'     => $attr1,
									'attr_command'  => $attr2,
									'attr_military' => $attr3,
									'exp_next'      => M_Formula::getGrowExp(1),
								);

								$ret = M_Hero::setHeroInfo($heroId, $fieldArr);

								$heroInfo['level'] = 1;
								$bClear            = M_Hero::clearHero($cityInfo, $heroInfo);

								$syncData                 = $fieldArr;
								$nextRecycleLv            = $newRecycleLv + 1;
								$syncData['recycle_next'] = isset($cfgArr[$nextRecycleLv]) ? $cfgArr[$nextRecycleLv] : array();
								$succ                     = 1;
							} else {
								$fieldArr = array(
									'level' => $heroInfo['level'] - 1,
								);

								$heroInfo['level'] = $heroInfo['level'] - 1;
								$bClear            = M_Hero::clearHero($cityInfo, $heroInfo);

								$syncData = $fieldArr;
								$ret      = M_Hero::setHeroInfo($heroId, $fieldArr);
							}

							if ($ret && !empty($syncData)) {
								M_Sync::addQueue($cityInfo['id'], M_Sync::KEY_HERO, array($heroId => $syncData)); //同步数据!
							}

							$data['succ'] = $succ;
							$errNo        = '';
						}
					} else {
						$errNo = $err;
					}
				} else {
					$errNo = T_ErrNo::HERO_RECYCLE_CFG_ERR;
				}
			}
		}


		return B_Common::result($errNo, $data);
	}

	/**
	 * 给军官使用经验丹
	 * @param int $heroId
	 * @param int $itemId
	 * @return array
	 */
	public function AUseExpItem($heroId, $equipId) {

		$data      = array();
		$errNo     = T_ErrNo::ERR_ACTION;
		$heroId    = intval($heroId);
		$equipId   = intval($equipId);
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		if ($heroId && $equipId) {
			$cityId    = $cityInfo['id'];
			$heroInfo  = M_Hero::getCityHeroInfo($cityId, $heroId);
			$equipInfo = M_Equip::getInfo($equipId);

			if (!empty($equipInfo['id']) && !empty($heroInfo['id'])) {
				$level = $heroInfo['level']; //当前等级
				$maxLv = M_Config::getVal('hero_maxlv'); //英雄最大等级
				$err   = '';
				if ($heroInfo['flag'] != T_Hero::FLAG_FREE) {
					$err = T_ErrNo::HERO_NOT_FREE;
				} else if (!empty($equipInfo['is_use'])) { //经验物品在军官身上
					$err = T_ErrNo::HERO_EXP_ITEM_IN_HERO;
				} else if (empty($equipInfo['ext_attr_rate'])) { //经验物品的经验为空
					$err = T_ErrNo::HERO_EXP_ITEM_EMPTY_EXP;
				} else if ($level >= $maxLv) {
					$err = T_ErrNo::HERO_EXP_FULL_LEVEL;
				}

				if (empty($err)) {
					$bCost = M_Equip::delCityEquip($equipId, $cityId);
					if ($bCost) {
						Logger::opEquip($cityId, $equipId, Logger::E_ACT_DEL_EXP, $equipInfo['name']);

						$leftExp = $equipInfo['ext_attr_rate'];

						$needExp = M_Formula::getGrowExp($level); //升级所需经验
						$heroExp = $heroInfo['exp'] + $leftExp; //英雄战后经验值

						while ($heroExp >= $needExp) {
							$tmpLv   = $level;
							$level   = $level + 1; //等级提升1
							$heroExp = $heroExp - $needExp; //扣除升级经验
							$needExp = M_Formula::getGrowExp($tmpLv); //升级所需经验
							if ($level >= $maxLv) //最大100级
							{
								break;
							}
						}

						$tmpArr = array(
							'id'    => $heroId,
							'level' => $level,
							'exp'   => $heroExp,
						);

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
							$heroArmyNumAdd = M_Hero::heroArmyNumAdd($cityId, $cityInfo['union_id']);

							$tmpArr['max_army_num'] = M_Formula::calcHeroMaxArmyNum($level, $heroInfo['skill_army_num'], $heroArmyNumAdd);
							$tmpArr['exp_next']     = M_Formula::getGrowExp($level);
						}

						$ret = M_Hero::updateInfo($tmpArr);
						if ($ret) {
							$syncData[$heroId] = $tmpArr;
							M_Sync::addQueue($cityId, M_Sync::KEY_HERO, $syncData);

							$errNo = '';
						}
					}
				} else {
					$errNo = $err;
				}
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 编队列表
	 *
	 * @param $teamId
	 */
	public function ATeamList() {
		$data      = array();
		$errNo     = '';
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$list      = $objPlayer->instance('Team')->get();

		foreach ($list as $no => $val) {
			$data[] = array($no, $val[0], $val[1]);
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 修改编队
	 *
	 * @param $teamId
	 * @param $heroIds
	 */
	public function ATeamSet($teamNo, $heroIdStr) {
		$data      = array();
		$errNo     = T_ErrNo::ERR_ACTION;
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$errHero   = '';
		$heroIds   = array();
		if (!empty($heroIdStr)) {
			$heroIds     = explode(',', $heroIdStr);
			$heroListIds = M_Hero::getCityHeroList($cityInfo['id']);
			$tmpIds      = array_intersect($heroIds, $heroListIds);
			if (count($tmpIds) != count($heroIds)) {
				$errHero = 1;
				//军官属于城市
			}
		}

		$tlObj = $objPlayer->instance('Team');

		$oldIds = $tlObj->getIdsByNo($teamNo);
		$err    = '';
		if (!empty($errHero)) {
			$err = 1003;
		} else if (!empty($heroIds) && $tlObj->exclExist($teamNo, $heroIds)) {
			$err = 1004;
		} else if (!$tlObj->set($teamNo, $heroIds)) {
			$err = 1005;
		}

		$errNo = $err;
		if (empty($err)) {
			$syncData = array();
			foreach ($oldIds as $hId) {
				$syncData[$hId] = array(
					'id'      => $hId,
					'in_team' => 0
				);
			}

			foreach ($heroIds as $hId) {
				$syncData[$hId] = array(
					'id'      => $hId,
					'in_team' => $teamNo
				);
			}

			if ($syncData) {
				M_Sync::addQueue($cityInfo['id'], M_Sync::KEY_HERO, $syncData);
			}

			$errNo = '';
			$ret   = $objPlayer->save();
			$data  = $tlObj->get();
		}


		return B_Common::result($errNo, $data);
	}

}

?>
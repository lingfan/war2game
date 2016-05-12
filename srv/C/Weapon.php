<?php

/**
 * 武器控制器
 */
class C_Weapon extends C_I {
	public function AGetAllSysInfo() {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_ACTION; //失败原因默认
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$data = M_Base::weapon();
		$flag = T_App::SUCC;
		$errNo = '';

		return B_Common::result($errNo, $data);
	}

	/**
	 * 军械所研究常规武器    on 2011/04/07
	 * @author chenhui
	 * @param int weaponid 武器ID
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AResearch($weaponid) {

		$errNo = T_ErrNo::ERR_ACTION;
		$weaponid = intval($weaponid);
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$nowtime = time();
		if ($weaponid > 0) {
			$objRes = $objPlayer->Res();
			$objRes->calc();
			$objBuild = $objPlayer->Build();
			$objTech = $objPlayer->Tech();


			$objWeapon = $objPlayer->instance('Weapon');

			$cityweaponinfo = $objWeapon->get();
			$weaponInfo = M_Weapon::baseInfo($weaponid);

			$needBuild = !empty($weaponInfo['need_build']) ? json_decode($weaponInfo['need_build'], true) : array();
			$needTech = !empty($weaponInfo['need_tech']) ? json_decode($weaponInfo['need_tech'], true) : array();


			if (!empty($weaponInfo) &&
				!empty($cityweaponinfo) &&
				M_Weapon::COMMON == $weaponInfo['is_special']
			) {
				//常规武器

				$err = '';

				$objRes->incr('gold', -$weaponInfo['cost_gold']);
				$objRes->incr('food', -$weaponInfo['cost_food']);
				$objRes->incr('oil', -$weaponInfo['cost_oil']);

				if ($objWeapon->inBaseWeapon($weaponid)) { //已有常规武器ID
					$err = T_ErrNo::WEAPON_EXIST;
				} else if ($objRes->getNum('gold') < 0) { //金钱不足
					$err = T_ErrNo::NO_ENOUGH_GOLD;
				} else if ($objRes->getNum('food') < 0) { //粮食不足
					$err = T_ErrNo::NO_ENOUGH_FOOD;
				} else if ($objRes->getNum('oil') < 0) { //石油不足
					$err = T_ErrNo::NO_ENOUGH_OIL;
				} else if (!$objBuild->limitCond($needBuild)) { //判断此玩家城市是否满足特定的建筑需求
					$err = T_ErrNo::BUILD_NO_PRE_BUILD_COND;
				} else if (!$objTech->limitCond($needTech)) { //判断此玩家城市是否满足特定的科技需求
					$err = T_ErrNo::BUILD_NO_PRE_TECH_COND;
				}

				$errNo = $err;
				if (empty($err)) {
					$objWeapon->addBase($weaponid);

					$objPlayer->Quest()->check('weapon_study', array('val' => $weaponid));

					$ret = $objPlayer->save();
					//更新城市武器信息失败，数据库执行错误
					$errNo = T_ErrNo::ERR_DB_EXECUTE;
					if ($ret) {
						M_Weapon::syncNormalWeapon2Front($cityInfo['id'], $weaponid); //同步武器数据
						//此武器研究成功
						$errNo = '';
					}
				}
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 开启特殊武器槽(可一次开启多个)
	 * @author chenhui on 20110817
	 * @param int $slotId 槽ID(从1开始)
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AOpenSlot($endSlotId = 4) {

		$errNo = T_ErrNo::ERR_PARAM;
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$objWeapon = $objPlayer->instance('Weapon');

		$slotIds = $objWeapon->getOpenSlotIds($endSlotId);

		if (!empty($slotIds)) {
			$cityId = $cityInfo['id'];
			$err = '';
			//共需花费军饷值
			$needMilPay = $objWeapon->getOpenSlotCost($endSlotId);

			$objPlayer->City()->mil_pay -= $needMilPay;
			//添加军饷或礼券判断
			if ($objPlayer->City()->mil_pay < 0) {
				$errNo = T_ErrNo::NO_ENOUGH_MILIPAY;
			} else {
				foreach ($slotIds as $id) {
					$objWeapon->addSpecial($id, 0);
				}

				$ret = $objPlayer->save();


				if ($ret) {
					$syncRow['milpay'] = $objPlayer->City()->mil_pay;
					M_Sync::addQueue($cityId, M_Sync::KEY_CITY_INFO, $syncRow);

					$objPlayer->Log()->expense(T_App::MILPAY, $needMilPay, B_Log_Trade::E_OpenWeaponSlot, implode(',', $slotIds));

					$errNo = '';
					$data[] = implode(',', $slotIds);
				}
			}
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 合成(由图纸变成武器)    on 20110409
	 * @author chenhui
	 * @param int $slotId 槽ID(从1开始)
	 * @param int $propsId 图纸(即道具)ID
	 * @param int $binding 绑定状态 0未 1绑
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AMixture($slotId, $propsId, $binding = M_Props::UNBINDING) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();

		$slotId = intval($slotId);
		$propsId = intval($propsId);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$objWeapon = $objPlayer->instance('Weapon');

		$propsInfo = M_Props::baseInfo($propsId);

		if ($slotId > 0 && $objWeapon->hasSlot($slotId)) {
			$cityId = $cityInfo['id'];
			$weaponId = intval($propsInfo['effect_val']);
			$weaponInfo = M_Weapon::baseInfo($weaponId);
			$objBuild = $objPlayer->Build();
			$objTech = $objPlayer->Tech();

			$needBuild = !empty($weaponInfo['need_build']) ? json_decode($weaponInfo['need_build'], true) : array();
			$needTech = !empty($weaponInfo['need_tech']) ? json_decode($weaponInfo['need_tech'], true) : array();

			$err = '';
			if (empty($weaponInfo)) {
				$err = T_ErrNo::WEAPON_NOT_HAVE;
			} else if (!M_Props::checkCityPropsNum($cityId, $propsId, $binding, 1)) {
				$err = T_ErrNo::PROPS_NOT_ENOUGH;
			} else if ($objWeapon->getSpecialWidBySlot($slotId) != $weaponId &&
				$objWeapon->inSpecialWeapon($weaponId)
			) {
				$err = T_ErrNo::WEAPON_EXIST;
			} else if ('WEAPON_CREATE' != $propsInfo['effect_txt'] || M_Weapon::SPECIAL != $weaponInfo['is_special']) {
				$err = T_ErrNo::WEAPON_NOT_USE;
			} else if (!$objBuild->limitCond($needBuild)) { //判断此玩家城市是否满足特定的建筑需求
				$err = T_ErrNo::BUILD_NO_PRE_BUILD_COND;
			} else if (!$objTech->limitCond($needTech)) { //判断此玩家城市是否满足特定的科技需求
				$err = T_ErrNo::BUILD_NO_PRE_TECH_COND;
			}

			$errNo = $err;
			if (empty($err)) {
				$objWeapon->addSpecial($slotId, $weaponId);
				$objPlayer->Quest()->check('weapon_study_s', array('val' => $slotId));
				$ret = $objPlayer->save();
				if ($ret) {
					$objPlayer->Pack()->decrNumByPropId($propsId, 1);
					M_Sync::addQueue($cityId, M_Sync::KEY_WEAPON_SPECIAL, array($slotId => $weaponId)); //同步特殊武器数据

					$errNo = '';
				} else {
					Logger::debug(array(__METHOD__, func_get_args(), "weaponId#{$weaponId}"));
					$errNo = T_ErrNo::ERR_DB_EXECUTE;
				}

			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 武器租赁
	 *
	 */
	public function ARent($weaponId = 0) {

		$errNo = T_ErrNo::ERR_ACTION;
		$weaponId = intval($weaponId);
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$nowtime = time();
		if ($weaponId > 0) {
			$objWeapon = $objPlayer->instance('Weapon');

			$tempWeaponCfg = M_Config::getVal('temp_weapon');
			$baseWeaponInfo = M_Weapon::baseInfo($weaponId);

			if (isset($tempWeaponCfg[$weaponId]) &&
				!empty($baseWeaponInfo)
			) {
				$err = '';
				list($t, $cost, $lv) = $tempWeaponCfg[$weaponId];
				$cityBuildLv = $objPlayer->Build()->getLevel(M_Build::ID_ARMORY);

				$objPlayer->City()->mil_pay -= $cost;

				if ($cityBuildLv < $lv) {
					$err = T_ErrNo::WEAPON_RENT_LV_LACK;
				} else if ($objPlayer->City()->mil_pay < 0) {
					$err = T_ErrNo::WEAPON_RENT_MILPAY_LACK;
				}

				if (empty($err)) {
					$objWeapon->addTemp($weaponId, $t);
					$ret = $objPlayer->save();

					if ($ret) { //武器租借成功
						$info = $objWeapon->get();
						M_Sync::addQueue($cityInfo['id'], M_Sync::KEY_WEAPON_TEMP, array($weaponId => $info['temp'][$weaponId])); //同步特殊武器数据

						$objPlayer->Log()->expense(T_App::MILPAY, $cost, B_Log_Trade::E_RentWeapon, implode(',', $tempWeaponCfg[$weaponId]));

						$errNo = '';
					} else { //更新城市武器信息失败，数据库执行错误
						$errNo = T_ErrNo::ERR_DB_EXECUTE;
					}

				} else {
					$errNo = $err;
				}
			}
		}

		return B_Common::result($errNo, $data);
	}
}

?>
<?php

/**
 * 兵种接口
 */
class C_Army extends C_I {
	/**
	 * 获取基础兵种信息
	 * @author chenhui    on 20110411
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AGetAllSysInfo() {
		$data = M_Base::army();
		$errNo = '';
		return B_Common::result($errNo, $data);
	}

	/**
	 * 招募兵
	 * @author chenhui    on 20110411
	 * @param int armyid 兵种ID
	 * @param int number 征兵数量
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ARecruit($armyId, $number) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$armyId = intval($armyId);
		$number = intval($number);

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if (isset(M_Army::$type[$armyId]) && $number > 0) {
			$cityId = $cityInfo['id'];
			$objBuild = $objPlayer->Build();
			$objArmy = $objPlayer->Army();
			$objRes = $objPlayer->Res();

			//兵种基础信息
			$baseInfo = $objArmy->base[$armyId];

			list($num, $lv, $exp) = $objArmy->getById($armyId);

			$needBuild = array(M_Build::ID_MIL_CAMP => M_Formula::armyUpgBuildLev($armyId, $lv));

			$new_gold = $number * $objArmy->calcRecruitCost($baseInfo['cost_gold'], $lv); //扣除的金钱
			$new_food = $number * $objArmy->calcRecruitCost($baseInfo['cost_food'], $lv); //扣除的粮食
			$new_oil = $number * $objArmy->calcRecruitCost($baseInfo['cost_oil'], $lv); //扣除的石油

			//占用人口
			$objPlayer->City()->cur_people += $number * $baseInfo['cost_people'];

			$err = '';
			if (!$objBuild->limitCond($needBuild)) {
				$err = T_ErrNo::BUILD_NO_PRE_BUILD_COND;
			} else if ($objPlayer->City()->correctPeople()) {
				$err = T_ErrNo::NO_ENOUGH_PEOPLE; //人口不足
			} else if ($objRes->incr('gold', -$new_gold) < 0) {
				$err = T_ErrNo::NO_ENOUGH_GOLD; //金钱不足
			} else if ($objRes->incr('food', -$new_food) < 0) {
				$err = T_ErrNo::NO_ENOUGH_FOOD; //粮食不足
			} else if ($objRes->incr('oil', -$new_oil) < 0) {
				$err = T_ErrNo::NO_ENOUGH_OIL; //石油不足
			} else if ($objPlayer->City()->cur_people > $objPlayer->City()->max_people) {
				$err = T_ErrNo::NO_ENOUGH_PEOPLE; //人口不足
			}

			$errNo = $err;
			if (empty($err)) {
				$newArmyNum = $objArmy->addNum($armyId, $number);
				Logger::debug(array(__METHOD__, $armyId, $number, $newArmyNum));

				$objPlayer->Quest()->check('army_hire', array('id' => $armyId, 'num' => $newArmyNum));
				$ret = $objPlayer->save();
				if ($ret) {
					//招募兵成功
					$errNo = '';
				} else {
					$errNo = T_ErrNo::ERR_DB_EXECUTE;
				}
			}
		}
		return B_Common::result($errNo, $data);

	}

	/**
	 * 解散兵(返还人口不返还资源)
	 * @author chenhui    on 20110412
	 * @param int armyid 兵种ID
	 * @param int number 解散兵数量
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ADismiss($armyId, $number) {
		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$armyId = intval($armyId);
		$number = intval($number);

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if (isset(M_Army::$type[$armyId]) && $number > 0) {

			$objArmy = $objPlayer->Army();
			$baseInfo = $objArmy->base[$armyId]; //兵种基础信息

			$newArmyNum = $objArmy->addNum($armyId, -$number);
			//新 已占用人口 减少占用人口
			$objPlayer->City()->cur_people -= $number * $baseInfo['cost_people'];

			if ($objPlayer->City()->cur_people < 0) {
				$objPlayer->City()->cur_people = 0;
			}

			Logger::debug(array(__METHOD__, $newArmyNum, $objPlayer->City()->cur_people, $objPlayer->City()->max_people));

			$errNo = T_ErrNo::ARMY_LESS_DISMISS;
			if ($newArmyNum >= 0 && $objPlayer->City()->cur_people >= 0) {
				$ret = $objPlayer->save();
				$errNo = T_ErrNo::ERR_DB_EXECUTE;
				if ($ret) {
					$syncData = array('cur_people' => $objPlayer->City()->cur_people);
					M_Sync::addQueue($objPlayer->City()->id, M_Sync::KEY_CITY_INFO, $syncData);

					$msRow[$armyId] = $objArmy->getById($armyId);
					M_Sync::addQueue($objPlayer->City()->id, M_Sync::KEY_ARMY, $msRow);

					//解散兵成功
					$errNo = '';
				}
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 兵种升级
	 * @author chenhui    on 20110418
	 * @param int armyid 兵种ID
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AUpgrade($armyId) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$armyId = intval($armyId);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if (isset(M_Army::$type[$armyId])) {

			$cityId = $cityInfo['id'];
			$objBuild = $objPlayer->Build();
			$objArmy = $objPlayer->Army();

			$armyMaxLv = M_Config::getVal('army_max_level');
			$newLv = $objArmy->addLv($armyId, 1);

			$needBuild = array(M_Build::ID_MIL_CAMP => M_Formula::armyUpgBuildLev($armyId, $newLv));

			//下一级所需熟练度
			$needExp = $objArmy->calcExp($newLv);

			$leftExp = $objArmy->addExp($armyId, -$needExp);

			if (!$objBuild->limitCond($needBuild)) {
				$err = T_ErrNo::BUILD_NO_PRE_BUILD_COND;
			} else if ($newLv > $armyMaxLv) {
				$err = T_ErrNo::ARMY_MAX_LEVEL_NOW;
			} else if ($leftExp < 0) {
				//升级失败，兵种熟练度未达到升级所需
				$err = T_ErrNo::ARMY_LESS_PROFIC;
			}

			$errNo = $err;
			if (empty($err)) {
				$ret = $objPlayer->save();
				$errNo = T_ErrNo::ERR_DB_EXECUTE;
				if ($ret) {
					$msRow[$armyId] = $objArmy->getById($armyId);
					M_Sync::addQueue($objPlayer->City()->id, M_Sync::KEY_ARMY, $msRow);

					$objPlayer->Quest()->check('army_up', array('id' => $armyId, 'lv' => $newLv));

					$errNo = '';
					$data = array($leftExp);
				}
			}
		}

		return B_Common::result($errNo, $data);
	}

}

?>
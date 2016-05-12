<?php

/**
 * 科技控制器
 */
class C_Tech extends C_I {
	public function AGetAllSysInfo() {
		//操作结果默认为失败0
		$data = M_Base::tech();
		$errNo = '';
		return B_Common::result($errNo, $data);
	}

	/**
	 * 升级科技
	 * @param int tech_id 科技ID
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AUpgrade($techId) {
		$errNo = T_ErrNo::ERR_ACTION;
		$techId = intval($techId);
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if ($techId > 0) {
			$cityId = $cityInfo['id'];
			$objTech = $objPlayer->Tech();
			$objBuild = $objPlayer->Build();
			//科技基本信息
			$baseInfo = M_Tech::baseInfo($techId);

			if (!empty($baseInfo)) {
				//要升级到的等级
				$objTech->$techId += 1;

				$baseInfoUp = M_Tech::getUpgInfoByLevel($techId, $objTech->$techId); //科技升级信息

				$unionInfo = M_Union::getInfo($objPlayer->City()->union_id);
				$unionAdd = M_Union::getUnionTechAddition($unionInfo, M_Union::TECH_CD_BUILD);

				$baseInfoUp['cost_time'] = M_Formula::calcUnionTechDecrEff($unionAdd, $baseInfoUp['cost_time']); //联盟科技减成CD时间


				$limitBuild = $limitTech = array();
				if (!empty($baseInfoUp['need_build'])) {
					foreach ($baseInfoUp['need_build'] as $val) {
						$limitBuild[$val[0]] = $val[1];
					}
				}

				if (!empty($baseInfoUp['need_tech'])) {
					foreach ($baseInfoUp['need_tech'] as $val) {
						$limitTech[$val[0]] = $val[1];
					}
				}

				//处理建筑CD时间
				$objCD = $objPlayer->CD();
				$cdIndex = $objCD->getFreeIdx(O_CD::TYPE_TECH);

				$err = '';
				if (!$cdIndex) { //科技队列已满，CD时间未结束
					$err = T_ErrNo::TECH_CD_TIME;
				} else if ($objTech->$techId > intval($baseInfo['max_level'])) //判断此科技是否已达最高等级
				{
					$err = T_ErrNo::TECH_MAX_LEVEL_NOW; //此科技等级已经是最高等级了
				} else if (empty($baseInfoUp)) {
					$err = T_ErrNo::ERR_PARAM; //参数错误
				} else if ($objPlayer->Res()->incr('gold', -$baseInfoUp['cost_gold']) < 0) {
					$err = T_ErrNo::NO_ENOUGH_GOLD; //金钱不足
				} else if ($objPlayer->Res()->incr('food', -$baseInfoUp['cost_food']) < 0) {
					$err = T_ErrNo::NO_ENOUGH_FOOD; //粮食不足
				} else if ($objPlayer->Res()->incr('oil', -$baseInfoUp['cost_oil']) < 0) {
					$err = T_ErrNo::NO_ENOUGH_OIL; //石油不足
				} else if (!$objBuild->limitCond($limitBuild)) {
					$err = T_ErrNo::BUILD_NO_PRE_BUILD_COND;
				} else if (!$objTech->limitCond($limitTech)) {
					$err = T_ErrNo::BUILD_NO_PRE_TECH_COND;
				}

				$errNo = $err;
				if (empty($err)) {
					$objCD->set(O_CD::TYPE_TECH, $cdIndex, $baseInfoUp['cost_time']);

					$objPlayer->Quest()->check('tech_up', array('id' => $techId, 'lv' => $objTech->$techId));

					M_QqShare::check($objPlayer, 'tech_up', array('level' => $objTech->$techId));

					//同步科技CD时间
					$msRow = array(
						'tech' => $objCD->toFront(O_CD::TYPE_TECH)
					);
					M_Sync::addQueue($cityId, M_Sync::KEY_CDTIME, $msRow);

					if (in_array($techId, array(M_Tech::ID_FOOD, M_Tech::ID_OIL, M_Tech::ID_GOLD))) { //同步资源增长值
						$objPlayer->Res()->upGrow('tech');
					}
					$ret = $objPlayer->save();

					//升级科技失败，数据执行错误
					$errNo = T_ErrNo::TECH_UPGRADE_FAIL;
					if ($ret) { //升级科技成功
						$errNo = '';
					}
				}
			}
		}

		return B_Common::result($errNo, $data);
	}

}

?>
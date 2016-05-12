<?php

/**
 * 建筑接口
 */
class C_Build extends C_I {
	/**
	 * 获取系统定义的建筑基础和升级数据
	 * @author chenhui at 2011/04/02
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AGetAllSysInfo() {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_ACTION; //失败原因默认
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		list($zone, $mapPosX, $mapPosY) = M_MapWild::calcWildMapPosXYByNo($objPlayer->City()->pos_no);
		$baseinfoall = M_Base::buildAll();
		if (!empty($baseinfoall)) {
			foreach ($baseinfoall as $key => $baseinfo) {
				if (M_Build::NOT_BEAUTIFY == $baseinfo['is_beautify'] &&
					M_Build::ID_SPY_COLLEGE != $baseinfo['id']
				) //屏蔽装饰建筑和间谍学校
				{
					$zoneArea = array();
					$arrArea = json_decode($baseinfo['area'], true);
					if (!empty($arrArea)) {
						foreach ($arrArea as $z => $val) {
							$z = intval($z);
							$zoneArea[$z] = M_MapCity::buildDec2Chr($val);
						}
					}

					$arr1 = array();
					$arr1['BuildId'] = $baseinfo['id'];
					$arr1['BuildName'] = $baseinfo['name'];
					$arr1['Features'] = $baseinfo['features'];
					$arr1['Area'] = $arrArea[$zone];
					$arr1['IsMoved'] = $baseinfo['is_moved'];
					$arr1['IsMulti'] = $baseinfo['is_multi'];
					$arr1['IsBeautify'] = $baseinfo['is_beautify'];
					$arr1['MaxLevel'] = $baseinfo['max_level'];
					$arr1['Sort'] = $baseinfo['sort'];
					$arr1['Desc1'] = $baseinfo['desc_1'];
					$arr1['Desc2'] = $baseinfo['desc_2'];
					$arr1['BuildAttr'] = array();

					if (!empty($baseinfo['upg'])) {
						foreach ($baseinfo['upg'] as $k1 => $upginfo) {
							$arr1['BuildAttr'][] = array(
								'BuildId' => $upginfo['build_id'],
								'BuildLevel' => intval($upginfo['level']),
								'CostGold' => $upginfo['cost_gold'],
								'CostFood' => $upginfo['cost_food'],
								'CostOil' => $upginfo['cost_oil'],
								'CostTime' => $upginfo['cost_time'],
								'NeedBuild' => B_Utils::kv2vv($upginfo['need_build']),
								'NeedTech' => B_Utils::kv2vv($upginfo['need_tech']),
								'ResGrowNow' => $upginfo['res_grow_now'], //资源建筑当前等级基础产量,其它则为0
								'ResGrowNext' => $upginfo['res_grow_next'], //资源建筑下一等级基础产量,其它则为0
							);
						}
					}
					$data[] = $arr1;
				}
			}
		}
		$errNo = '';

		return B_Common::result($errNo, $data);
	}


	/**
	 * 升级建筑
	 * @author chenhui at 2011/03/25
	 * @param int bid 建筑ID
	 * @param int posx 建筑位置X坐标
	 * @param int posy 建筑位置Y坐标
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AUpgrade($bid = 0, $posx = 0, $posy = 0) {
		$objPlayer = $this->objPlayer;

		$bid = intval($bid);
		$posx = intval($posx);
		$posy = intval($posy);

		$pos = $posx . '_' . $posy;
		$baseInfo = M_Build::baseInfo($bid); //建筑基本信息
		$cityBuildList = $objPlayer->Build()->get();

		if (!isset($cityBuildList[$bid][$pos]) || empty($baseInfo)) {
			return B_Common::result(T_ErrNo::ERR_PARAM);
		}

		$oldLv = intval($cityBuildList[$bid][$pos]);

		if ($bid == M_Build::ID_TOWN_CENTER) {//城镇中心
			$tmp = T_Build::$CenterUpLimit[$oldLv];
			if ($objPlayer->City()->last_fb_no < M_Formula::calcFBNo($tmp[0],$tmp[1],0)) {
				return B_Common::result(T_ErrNo::BUILD_NO_PRE_BUILD_COND);
			}
		}


		//要升级到的等级
		$newLv = intval($cityBuildList[$bid][$pos]) + 1;
		$baseInfoUp = M_Build::baseUpgInfo($bid, $newLv); //建筑升级信息

		$unionInfo = M_Union::getInfo($objPlayer->City()->union_id);
		$unionAdd = M_Union::getUnionTechAddition($unionInfo, M_Union::TECH_CD_BUILD);

		//联盟科技减成CD时间
		$baseInfoUp['cost_time'] = M_Formula::calcUnionTechDecrEff($unionAdd, $baseInfoUp['cost_time']);

		//处理建筑CD时间
		$cdIndex = $objPlayer->CD()->getFreeIdx(O_CD::TYPE_BUILD);

		$err = '';
		if (!$cdIndex) { //建筑队列已满，CD时间未结束
			$err = T_ErrNo::BUILD_MAX_ROW_NOW;
		} else if ($newLv > intval($baseInfo['max_level'])) { //判断此建筑是否已达最高等级
			$err = T_ErrNo::BUILD_MAX_LEVEL_NOW;
		} else if ($objPlayer->Res()->incr('gold', -$baseInfoUp['cost_gold']) < 0) { //金钱不足
			$err = T_ErrNo::NO_ENOUGH_GOLD;
		} else if ($objPlayer->Res()->incr('food', -$baseInfoUp['cost_food']) < 0) { //粮食不足
			$err = T_ErrNo::NO_ENOUGH_FOOD;
		} else if ($objPlayer->Res()->incr('oil', -$baseInfoUp['cost_oil']) < 0) { //石油不足
			$err = T_ErrNo::NO_ENOUGH_OIL;
		} else if (!$objPlayer->Build()->limitCond($baseInfoUp['need_build'])) {
			$err = T_ErrNo::BUILD_NO_PRE_BUILD_COND;
		} else if (!$objPlayer->Tech()->limitCond($baseInfoUp['need_tech'])) {
			$err = T_ErrNo::BUILD_NO_PRE_TECH_COND;
		}

		if (!empty($err)) {
			return B_Common::result($err);
		}

		$objPlayer->CD()->set(O_CD::TYPE_BUILD, $cdIndex, $baseInfoUp['cost_time']);

		$cityBuildList[$bid][$pos] = $newLv;

		$objPlayer->Build()->set($cityBuildList);

		if ($bid == M_Build::ID_TOWN_CENTER) { //城市中心升级 开放其他建筑
			//M_Build::checkShowBuild($objPlayer, $newLv, 'level');
		}

		//Logger::debug(array($cityId, $bid, $pos, $newLv, 1));
		M_Build::syncBuildinfo2Front($objPlayer->City()->id, $bid, $pos, $newLv, 1);

		$objPlayer->Quest()->check('build_up', array('id' => $bid, 'lv' => $newLv));

		M_QqShare::check($objPlayer,  'build_up', array('id' => $bid, 'level' => $newLv));

		//建筑相关效果的更新
		$objPlayer->Build()->updateEffect($bid, M_Build::BUILD_UPGRADE);

		//同步建筑CD时间
		$msRow = array(
			'build' => $objPlayer->CD()->toFront(O_CD::TYPE_BUILD)
		);
		M_Sync::addQueue($objPlayer->City()->id, M_Sync::KEY_CDTIME, $msRow);

		if (in_array($bid, array(M_Build::ID_FOOD_BASE, M_Build::ID_OIL_BASE, M_Build::ID_GOLD_BASE))) { //同步资源增长值
			$objPlayer->Res()->upGrow('base');
		}

		//城市中心升级成功后续处理
		if (M_Build::ID_TOWN_CENTER == $bid && ($newLv % 10 == 1)) {
			$tmpCityLv = max(1, ceil($newLv / 10));
			if ($tmpCityLv > $objPlayer->City()->level) {
				$objPlayer->City()->level = $tmpCityLv;
				//$upCityInfo['level'] = $new_city_level;
				//M_Sync::addQueue($objPlayer->City()->id, M_Sync::KEY_CITY_INFO, array('level'=>$tmpCityLv));//同步城市等级

				M_Build::incrStatsCityLevelNum($newLv); //统计城市中心升级数据
				M_MapWild::syncWildMapBlockCache($objPlayer->City()->pos_no); //刷新此块地图数据
			}
		}

		$objPlayer->save();
		$errNo = '';
		$data = array($newLv);


		return B_Common::result($errNo, $data);
	}


	/**
	 * 点击添加建筑CD队列(可多个)
	 * @author chenhui on 20110528
	 * @param int $listId CD队列ID(从1开始)
	 * @param int $payType 付费类型(默认军饷)
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AIncrCDNum($idxId = 3, $payType = 1) {

		$idxId = intval($idxId);
		$payType = intval($payType);

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$buildCdData = $objPlayer->CD()->getList(O_CD::TYPE_BUILD);
		$totalNum = count($buildCdData);
		$curNum = $objPlayer->CD()->getOpenNum(O_CD::TYPE_BUILD);

		if (!M_Pay::isPayType($payType) || $idxId > $totalNum || $idxId <= $curNum) {
			return B_Common::result(T_ErrNo::ERR_PARAM);
		}

		if (!M_Vip::isCDListIdOpenPower($cityInfo['vip_level'], $idxId)) {
			return B_Common::result(T_ErrNo::VIP_NOT_LEVEL);
		}

		$cost = 0; //共需花费数值
		$logData = array();
		for ($i = $curNum; $i < $idxId; $i++) {
			$logData[] = $i + 1;
			$cost += M_Formula::calcIncrCDNumNeed($i + 1);
		}

		$logCostMilpay = $logCostCoupon = 0;
		if (T_App::MILPAY == $payType) {
			$objPlayer->City()->mil_pay -= $cost;
			$logCostMilpay = $cost;
		} else {
			$objPlayer->City()->coupon = $cost;
			$logCostCoupon = $cost;
		}

		//添加军饷点券检测操作
		if ($objPlayer->City()->mil_pay < 0) {
			return B_Common::result(T_ErrNo::NO_ENOUGH_MILIPAY);
		} else if ($objPlayer->City()->coupon < 0) {
			return B_Common::result(T_ErrNo::NO_ENOUGH_COUPON);
		}

		$newIdx = $objPlayer->CD()->open(O_CD::TYPE_BUILD);

		$objPlayer->Log()->expense(T_App::MILPAY, $logCostMilpay, B_Log_Trade::E_AddBuildQueue, implode(',', $logData));

		$ret = $objPlayer->save();
		if (!$ret) {
			return B_Common::result(T_ErrNo::ERR_UPDATE);
		}

		//同步建筑CD队列数
		$cityRow = array('cd_build_num' => $idxId);
		M_Sync::addQueue($objPlayer->City()->id, M_Sync::KEY_CITY_INFO, $cityRow);

		$msRow = array('build' => $objPlayer->CD()->toFront(O_CD::TYPE_BUILD));
		M_Sync::addQueue($objPlayer->City()->id, M_Sync::KEY_CDTIME, $msRow); //同步建筑CD队列

		return B_Common::result('');
	}
}
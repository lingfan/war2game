<?php

/**
 * 城市接口
 */
class C_City extends C_I {
	/**
	 * 城市状况
	 * @author 胡威  at 2011/03/28
	 * @return array
	 */
	public function AInfo() {
		$errNo = T_ErrNo::ERR_ACTION;
		$data  = array();

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		if ($cityInfo['id']) {
			list($zone, $posX, $posY) = M_MapWild::calcWildMapPosXYByNo($cityInfo['pos_no']);

			$objPlayer->Liveness()->check(M_Liveness::GET_POINT_ONLINE);

			$objPlayer->City()->checkNewbie();

			$unionName = '';
			$unionFlag = 0;
			if (!empty($cityInfo['union_id'])) {
				//获取联盟信息
				$unionInfo       = M_Union::getInfo($cityInfo['union_id']);
				$unionName       = isset($unionInfo['name']) ? $unionInfo['name'] : '';
				$unionMemberInfo = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);
				$unionFlag       = intval($unionMemberInfo['position']);
			}

			//暂时不可用 可建装饰建筑的ID组成的数组
			//$BeaBuildIdList = M_Build::getBeaBuildIdList($cityInfo['id']);
			$BeaBuildIdList = array();

			$armyList = $objPlayer->Army()->toFront();


			$weaponObj  = $objPlayer->instance('Weapon');
			$weaponList = $weaponObj->toFront(); //武器列表

			$buildList = $objPlayer->Build()->toFront($zone);

			$techList = $objPlayer->Tech()->toFront();

			$propsUse = $objPlayer->Props()->toFront();

			$itemList = $objPlayer->Pack()->toFront();

			$voidData = $objPlayer->Res()->calcResBaseAdd();

			//更新资源计算
			$objPlayer->Res()->calc();

			$findInfo = M_Hero::getSeekInfo($cityInfo['id']); //寻将

			$vipConf  = M_Vip::getVipConfig(); //VIP基础配置
			$vipLevel = $cityInfo['vip_level'];

			$curEquipNum = M_Equip::getCityEquipNum($cityInfo['id'], true);

			$canMaxTimesEnergy   = intval($vipConf['BUY_ENERGY'][$vipLevel]); //活力允许最大次数
			$todayBuyTimesEnergy = M_Vip::getTodayShopItemBuyNum($cityInfo['id'], M_Vip::SHOP_ENERGY, M_Vip::ENERGY_ID); //活力当前次数

			$canMaxTimesOrder   = intval($vipConf['BUY_MILORDER'][$vipLevel]); //军令允许最大次数
			$todayBuyTimesOrder = M_Vip::getTodayShopItemBuyNum($cityInfo['id'], M_Vip::SHOP_MILORDER, M_Vip::MILORDER_ID); //军令当前次数

			//敌情列表
			$enemylist = M_March::getMarchList($cityInfo['id'], M_War::MARCH_OWN_DEF);
			//获取未读消息
			$nuReadMsgList = M_Message::getNureadMsg($cityInfo['id']);
			//获取未读消息
			$nuReadReportList = M_WarReport::getNureadReport($cityInfo['id']);

			list($zone, $posX, $posY) = M_MapWild::calcWildMapPosXYByNo($cityInfo['pos_no']);

			$switch      = M_Config::getSvrCfg('anti_addiction_switch');
			$onlineAddup = 0;
			if ($switch) {
				$tmpAnti = $objPlayer->City()->getVisit();;
				$onlineAddup = !empty($tmpAnti['onlineAddup']) ? intval($tmpAnti['onlineAddup']) : 0;
			}

			$trainNumArr   = M_Hero::getTrainingNum($cityInfo['id']);
			$heroTrainNeed = array();

			$confHeroTrain = M_Config::getVal('hero_train');
			$trainCostRate = isset($confHeroTrain['cost']) ? (array)$confHeroTrain['cost'] : array();
			foreach ($trainCostRate as $kT => $vT) {
				$num                = !empty($trainNumArr[$kT]) ? $trainNumArr[$kT] : 0;
				$tmpNum             = M_Hero::calcTrainNum($num, $kT);
				$costNum            = $trainCostRate[$kT] * $tmpNum;
				$heroTrainNeed[$kT] = array((int)$num, (int)$costNum);
			}

			$CanReceVipPack = 0;
			$arrVipPack     = explode('_', $cityInfo['vip_pack_date']);
			if (date('Ymd') != $arrVipPack[0] || $vipLevel > intval($arrVipPack[1])) {
				$strPackage = $vipConf['VIP_PACKAGE'][$vipLevel];
				if (!empty($strPackage)) {
					$arrPackage = explode('_', $strPackage);
					($vipLevel > 0 && $arrPackage[1] > 0) && $CanReceVipPack = 1; //是否可以领VIP礼包
				}
			}

			$prestigeRank = 0;
			$cityId       = $cityInfo['id'];
			$prestigeRank = M_Ranking::getRenownRankingsByCityId($cityId);


			$showOncePay = empty($cityInfo['first_recharge']) ? 1 : 0;

			$now              = time();
			$baseoncepay      = M_Config::getVal('config_pay_once_award');
			$showOncePayAward = ($now > strtotime($baseoncepay['start']) && $now < strtotime($baseoncepay['end'])) ? 1 : 0;
			$baseaddpay       = M_Config::getVal('config_pay_add_award');
			//var_dump($baseaddpay);
			$showAddPayAward = ($now > strtotime($baseaddpay['start']) && $now < strtotime($baseaddpay['end'])) ? 1 : 0;

			$showOncePayAward = 1;
			$showAddPayAward  = 1;

			$objPlayer->City()->mil_pay = 10000;
			$objPlayer->City()->last_fb_no = 110101;

			$data = array(
				'CityId'           => (int)$cityInfo['id'],
				'UserId'           => (int)$cityInfo['id'],
				'FaceId'           => $cityInfo['face_id'],
				'NickName'         => $cityInfo['nickname'],
				'Gender'           => $cityInfo['gender'],
				'PosX'             => $posX,
				'PosY'             => $posY,
				'PosArea'          => $zone,
				'Level'            => $cityInfo['level'],
				'UnionId'          => (int)$cityInfo['union_id'],
				'UnionName'        => $unionName,
				'UnionFlag'        => $unionFlag,

				'Affiliated'       => $cityInfo['affiliated'], //属地

				'Gold'             => floor($objPlayer->Res()->getNum('gold')),
				'Food'             => floor($objPlayer->Res()->getNum('food')),
				'Oil'              => floor($objPlayer->Res()->getNum('oil')),

				'GoldGrow'         => $voidData['base']['gold_grow'],
				'FoodGrow'         => $voidData['base']['food_grow'],
				'OilGrow'          => $voidData['base']['oil_grow'],

				'GoldGrowBase'     => $voidData['base']['gold_grow'],
				'FoodGrowBase'     => $voidData['base']['food_grow'],
				'OilGrowBase'      => $voidData['base']['oil_grow'],

				//'GoldGrowBuild'		=> $voidData['build']['gold_grow'],
				//'FoodGrowBuild'		=> $voidData['build']['food_grow'],
				//'OilGrowBuild'		=> $voidData['build']['oil_grow'],

				'GoldGrowTech'     => $voidData['tech']['gold_grow'],
				'FoodGrowTech'     => $voidData['tech']['food_grow'],
				'OilGrowTech'      => $voidData['tech']['oil_grow'],

				'GoldGrowProps'    => $voidData['props']['gold_grow'],
				'FoodGrowProps'    => $voidData['props']['food_grow'],
				'OilGrowProps'     => $voidData['props']['oil_grow'],

				'GoldGrowZone'     => M_City::$zone_res_add[$zone]['gold_grow'],
				'FoodGrowZone'     => M_City::$zone_res_add[$zone]['food_grow'],
				'OilGrowZone'      => M_City::$zone_res_add[$zone]['oil_grow'],

				'GoldGrowVip'      => $voidData['vip']['gold_grow'],
				'FoodGrowVip'      => $voidData['vip']['food_grow'],
				'OilGrowVip'       => $voidData['vip']['oil_grow'],

				'GoldGrowUnion'    => $voidData['union']['gold_grow'],
				'FoodGrowUnion'    => $voidData['union']['food_grow'],
				'OilGrowUnion'     => $voidData['union']['oil_grow'],

				'MaxStore'         => (int)$objPlayer->Res()->getNum('max_store'),
				'MarketAmount'     => $objPlayer->City()->getTradeQuota(),

				'CurPeople'        => (int)$cityInfo['cur_people'],
				'MaxPeople'        => (int)$cityInfo['max_people'],

				'HeroAutoFill'     => 0,

				'LastFBNo'         => M_SoloFB::showFBNo(M_SoloFB::calcNextFBNo($cityInfo['last_fb_no'])),

				'Rank'             => (int)$cityInfo['rank'],
				'TotalMilPay'      => (int)$cityInfo['total_mil_pay'],
				'MilPay'           => (int)$cityInfo['mil_pay'],
				'Coupon'           => (int)$cityInfo['coupon'],
				'VipLevel'         => (int)$vipLevel,
				'VipEndtime'       => M_Formula::calcCDTime($cityInfo['vip_endtime']),
				'MilRankDaily'     => (date('Ymd') != $cityInfo['mil_rank_daily'] && $cityInfo['mil_rank'] > 0) ? 1 : 0,
				'MilRankAward'     => $cityInfo['mil_rank_award'],
				'MilRank'          => $cityInfo['mil_rank'],
				'Renown'           => $cityInfo['renown'],
				'MilMedal'         => $cityInfo['mil_medal'],
				'Energy'           => $cityInfo['energy'],
				'MaxEnergy'        => M_City::getEnergyUpLimit($vipLevel),
				'EnergyLeftTimes'  => max(0, $canMaxTimesEnergy - $todayBuyTimesEnergy), //今日剩余活力购买次数
				'EnergyNextNeed'   => M_Formula::calcBuyEnergyCost($todayBuyTimesEnergy + 1), //下次购买活力所需军饷数
				'MilOrder'         => $cityInfo['mil_order'],
				'MaxMilOrder'      => M_City::getOrderUpLimit($vipLevel),
				'OrderLeftTimes'   => max(0, $canMaxTimesOrder - $todayBuyTimesOrder), //今日剩余军令购买次数
				'OrderNextNeed'    => M_Formula::calcBuyMilOrderCost($todayBuyTimesOrder + 1), //下次购买军令所需军饷数
				'IsAvoidWar'       => $objPlayer->Props()->isAvoidWar(),
				'CDMoveCity'       => intval($cityInfo['move_city_cd_time']), //迁城CD时间
				'CanAlterNick'     => $cityInfo['can_alter_nick'],
				'AlterNickTime'    => $cityInfo['alter_nick_time'],
				'LuckPoolVal'      => $cityInfo['equip_strong_luck_pool'],
				'Signature'        => htmlspecialchars_decode($cityInfo['signature']),
				'OnlineAddup'      => $onlineAddup, //在线累计

				'CDWeapon'         => $objPlayer->CD()->toFront(O_CD::TYPE_WEAPON),
				'CDRescue'         => $objPlayer->CD()->toFront(O_CD::TYPE_RESCUE),
				//'CDExplore'			=> array(M_Formula::calcCDTime($arrCDExplore[0]), $eFT),
				'CDFB'             => $objPlayer->CD()->toFront(O_CD::TYPE_FB),
				'CDTech'           => $objPlayer->CD()->toFront(O_CD::TYPE_TECH),
				'CDTechNum'        => $objPlayer->CD()->getOpenNum(O_CD::TYPE_TECH),
				'CDBuild'          => $objPlayer->CD()->toFront(O_CD::TYPE_BUILD),
				'CDBuildNum'       => $objPlayer->CD()->getOpenNum(O_CD::TYPE_BUILD),

				'EndTime'          => M_Formula::calcCDTime($findInfo['end_time']),

				'BuildList'        => $buildList,
				'BeaBuildIdList'   => $BeaBuildIdList,
				'TechList'         => $techList,
				'ArmyList'         => $armyList,
				'WeaponList'       => $weaponList,
				'SpecialList'      => $weaponObj->getBaseSpcialSlot(),
				//'SpecialList'		=> $tmpWeaponList['special_num'],
				'PropsList'        => $itemList, //$propsList,
				'PropsUseList'     => $propsUse,
				//'TaskStatus'		=> M_Task::getCityTaskStatus($cityInfo['id']),
				'Newbie'           => $cityInfo['newbie'],
				'CurEquipNum'      => $curEquipNum,
				//'CurDrawNum'		=> $drawNum,
				//'CurPropsNum'		=> $propsNum,
				'EnemyNum'         => count($enemylist),
				'UnreadMsgNum'     => count($nuReadMsgList),
				'IsMsgFull'        => M_Message::isMessageFull($cityInfo['id']),
				'MaxMsgNum'        => M_Message::MSG_MAX_NUM,
				'UnreadReportNum'  => count($nuReadReportList),
				'SysTime'          => time(),
				'HeroTrainNeed'    => $heroTrainNeed,
				'CanReceVipPack'   => $CanReceVipPack,
				'PrestigeRank'     => $prestigeRank,
				'ShowOncePay'      => $showOncePay,
				'ShowOncePayAward' => $showOncePayAward,
				'ShowAddPayAward'  => $showAddPayAward,
			);

			$heroList        = M_Hero::getCityHeroList($cityInfo['id']);
			$data['HeroNum'] = count($heroList);

			$IsOpen     = 0;
			$dailyAward = M_Config::getVal('active_award'); //得到config的内容
			list($IsOpen, $activeField) = M_Task::getHoldNpcActiveStaus($cityInfo['id'], $dailyAward); //判断是不是学院奖励活动是否开放
			$data['ActiveIsOpen'] = $IsOpen;

			$data['ActivityIcon'] = array();

			$cityBreakoutInfo      = M_BreakOut::getCityBreakOut($cityId);
			$data['BreakoutPoint'] = $cityBreakoutInfo['point'];


			//答题10 突击11 积分12  据点13 爬楼14

			$data['IconList'] = array(
				array(1, 0, array()),
				array(2, 1, array()),
				array(3, 1, array()),
				array(4, 1, array()),
				array(5, 1, array()),
				array(6, 1, array()),
				array(7, 1, array()),
				array(8, 1, array()),
				array(9, 1, array()),
				array(10, $objPlayer->City()->checkIconStatus('event_answer'), array()),
				array(11, $objPlayer->City()->checkIconStatus('event_breakout'), array()),
				array(12, 1, array()),
				array(13, $objPlayer->City()->checkIconStatus('event_campaign'), array()),
				array(14, $objPlayer->City()->checkIconStatus('event_floor'), array()),
			);

			$data['BuildLimit'] = T_Build::$CenterUpLimit;


			$objPlayer->save();
			$errNo = '';
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 创建城市
	 * @author huwei at 2011/03/08
	 * @param string $cityName 城市名称
	 * @param int $faceId 图像
	 * @param int $gender 性别
	 * @param int $posArea 所选战区(默认为1)
	 * @return array
	 */
	public function ACreate($cityName = '', $faceId = '', $gender = '', $posArea = '') {

		$errNo   = T_ErrNo::ERR_ACTION;
		$data    = array();
		$posArea = !isset(T_App::$map[$posArea]) ? array_rand(T_App::$map) : $posArea;
		if (!empty($faceId) &&
			isset(T_App::$genderType[$gender]) &&
			!empty($cityName)
		) {

			$objPlayer = $this->objPlayer;
			$cityId    = $objPlayer->getId();
			$cityInfo  = $objPlayer->getCityBase();


			$nickErr = M_City::checkCityNickname($cityName);

			$err = '';
			if (!empty($cityInfo)) {
				$err = T_ErrNo::CITY_EXIST;
			} else if (!empty($nickErr)) {
				$err = $nickErr;
			}

			if (empty($err)) {
				$posStr = M_MapWild::getWildMapNoHoldPos($posArea);
				if ($posStr) {
					list($posX, $posY) = explode('_', $posStr);
					$posNo = M_MapWild::calcWildMapPosNoByXY($posArea, $posX, $posY);
					$ret   = M_City::create($cityId, $faceId . '_' . $posArea, $cityName, $posNo, $gender);

					//检测城市是否初始化成功
					if ($ret) {
						M_RandName::delRandName($gender, $cityName);
						$rc = new B_Cache_RC(T_Key::CITY_ACTIVE_NUM, date('Ymd'));
						$rc->hincrby($cityId, 1);
						$data['CityId'] = $cityId;

						$errNo = '';

						//基础配置信息
						$buildList = array();

						$baseBuildOpen = M_Config::getVal('build_open');
						if (!empty($baseBuildOpen[$posArea])) {
							foreach ($baseBuildOpen[$posArea] as $val) {
								list($tPos, $tBid, $tLv, $tFBNo) = $val;
								if (empty($tFBNo)) {
									$buildList[$tBid][$tPos] = 1;
								}
							}
						}


						//创建城市成功即送礼包
						$objPlayer->Pack()->incr(T_App::NEWBE_PROPS_ID, 1);

						$objPlayer->City()->mil_pay = 10000;
						$objPlayer->Build()->set($buildList);

						$initHeroId = M_Hero::addCityHero($cityId, 1003, true);
						$heroInfo   = M_Hero::getHeroInfo($initHeroId);

						$objPlayer->City()->cur_people = $heroInfo['army_num'];

						$tlObj = $objPlayer->instance('Team');
						$tlObj->set(1, array($initHeroId));

						$objPlayer->save();

					} else {
						$errNo = T_ErrNo::CITY_INIT_ERR;
					}
				} else {
					$errNo = T_ErrNo::CITY_MAP_FULL_POS;
				}
			} else {
				$errNo = $err;
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 检测城市名称
	 * @author huwei at 2011/03/31
	 * @param string $name 用户呢称
	 * @return array
	 */
	public function ACheckCityNickname($name) {
		$errNo = M_City::checkCityNickname($name);
		if (empty($errNo)) {
			$errNo = '';
		}

		$data = array();
		return B_Common::result($errNo, $data);
	}

	/**
	 * 清除CD时间
	 * @author chenhui on 20110722
	 * @param int $cdType CD类型(1建筑 2科技 3武器 4副本 5解救 6突围, 7爬楼)
	 * @param int $idx 建筑CD索引(从1开始)
	 * @param int $payType 付费类型(默认军饷1,2点券)
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ACleanCD($cdType, $idx = 1, $payType = 1) {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_PARAM; //失败原因默认为参数错误
		$data  = array(); //返回数据默认为空数组

		$cdType  = intval($cdType);
		$payType = intval($payType);
		$idx     = intval($idx); //建筑CD索引从1开始

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		$nowtime = time();
		if (isset(M_City::$cdTimeType[$cdType]) && in_array($payType, array(T_App::MILPAY, T_App::COUPON))) {
			$cityInfo   = $objPlayer->getCityBase();
			$objCD      = $objPlayer->CD();
			$cityId     = intval($cityInfo['id']);
			$needMilPay = 0; //所需军饷数
			$arrUPD     = $arrRESCUE = $arrBout = array(); //要更新的相应CD数据
			$action     = 0;
			$clearTime  = 0;
			switch ($cdType) {
				case M_City::CD_BUILD:
					$arrT = $objPlayer->CD()->toFront(O_CD::TYPE_BUILD);
					if (isset($arrT[$idx - 1][0])) {
						$clearTime  = $arrT[$idx - 1][0];
						$needMilPay = M_Formula::calcCleanBuildCDNeed($clearTime);
						$objPlayer->CD()->clean(O_CD::TYPE_BUILD, $idx);
						$action = B_Log_Trade::E_ClearBuildCD;
					}
					break;
				case M_City::CD_TECH:
					$arrT       = $objPlayer->CD()->toFront(O_CD::TYPE_TECH);
					$clearTime  = $arrT[0];
					$needMilPay = M_Formula::calcCleanTechCDNeed($clearTime);

					$objPlayer->CD()->clean(O_CD::TYPE_TECH);
					$action = B_Log_Trade::E_ClearTechCD;
					break;
				case M_City::CD_WEAPON:
					$arrT       = $objPlayer->CD()->toFront(O_CD::TYPE_WEAPON);
					$clearTime  = $arrT[0];
					$needMilPay = M_Formula::calcCleanWeaponCDNeed($clearTime);
					$objPlayer->CD()->clean(O_CD::TYPE_WEAPON);
					$action = B_Log_Trade::E_ClearWeaponCD;
					break;
				case M_City::CD_FB:
					$arrT       = $objPlayer->CD()->toFront(O_CD::TYPE_FB);
					$clearTime  = $arrT[0];
					$needMilPay = M_Formula::calcCleanQuickCD($clearTime);
					$objPlayer->CD()->clean(O_CD::TYPE_FB);
					$action = B_Log_Trade::E_ClearFBCD;
					break;
				case M_City::CD_RESCUE:
					$cityColonyInfo = M_ColonyCity::getInfo($cityId);
					$rescueNum      = $cityColonyInfo['rescue_num'] + 1;
					$needMilPay     = M_Formula::calcCleanRescueCDNeed($rescueNum);
					$arrRESCUE      = array('rescue_num' => $rescueNum);
					$objPlayer->CD()->clean(O_CD::TYPE_RESCUE);

					$action = B_Log_Trade::E_ClearRescueCD;
					break;
				case M_City::CD_BOUT:
					$arrT       = $objPlayer->CD()->toFront(O_CD::TYPE_BOUT);
					$clearTime  = $arrT[0];
					$needMilPay = M_Formula::calcCleanQuickCD($clearTime);
					$objPlayer->CD()->clean(O_CD::TYPE_BOUT);
					$action = B_Log_Trade::E_ClearBoutCD;
					break;
				case M_City::CD_FLOOR:
					$arrT       = $objPlayer->CD()->toFront(O_CD::TYPE_FLOOR);
					$clearTime  = $arrT[0];
					$needMilPay = M_Formula::calcCleanQuickCD($clearTime);
					$objPlayer->CD()->clean(O_CD::TYPE_FLOOR);
					$action = B_Log_Trade::E_ClearFloorCD;
					break;
				default:
					break;
			}

			$costMilpay = $costCoupon = 0;
			switch ($payType) {
				case T_App::MILPAY:
					$objPlayer->City()->mil_pay -= $needMilPay;
					$costMilpay = $needMilPay;
					break;
				case T_App::COUPON:
					$objPlayer->City()->coupon -= $needMilPay;
					$costCoupon = $needMilPay;
					break;
			}


			$err = '';
			if ($needMilPay <= 0) {
				$err = T_ErrNo::ERR_ACTION;
			}
			if ($objPlayer->City()->mil_pay < 0) {
				$err = T_ErrNo::NO_ENOUGH_MILIPAY;
			} else if ($objPlayer->City()->coupon < 0) {
				$err = T_ErrNo::NO_ENOUGH_COUPON;
			}


			$errNo = $err;
			if (empty($err)) {
				if (M_City::CD_BUILD == $cdType) {
					$objPlayer->Quest()->check('build_cd', array('num' => 1));
					//同步建筑CD时间
					$msRow = array('build' => $objPlayer->CD()->toFront(O_CD::TYPE_BUILD));
					M_Sync::addQueue($cityId, M_Sync::KEY_CDTIME, $msRow);

				} else if (M_City::CD_TECH == $cdType) {
					$objPlayer->Quest()->check('tech_cd', array('num' => 1));
					//同步科技CD时间
					$msRow = array('tech' => $objPlayer->CD()->toFront(O_CD::TYPE_TECH));
					M_Sync::addQueue($cityId, M_Sync::KEY_CDTIME, $msRow);
				} else if (M_City::CD_BOUT == $cdType) { //同步突击CD时间
					$msRow = array('breakout_cd' => $objPlayer->CD()->toFront(O_CD::TYPE_BOUT));
					M_Sync::addQueue($cityId, M_Sync::KEY_CDTIME, $msRow);

				} else if (M_City::CD_WEAPON == $cdType) { //同步武器CD时间
					$msRow = array('weapon' => $objPlayer->CD()->toFront(O_CD::TYPE_WEAPON));
					M_Sync::addQueue($cityId, M_Sync::KEY_CDTIME, $msRow);
				} else if (M_City::CD_FB == $cdType) { //同步副本战斗CD时间
					$msRow = array('fb' => $objPlayer->CD()->toFront(O_CD::TYPE_FB));
					M_Sync::addQueue($cityId, M_Sync::KEY_CDTIME, $msRow);
				} else if (M_City::CD_RESCUE == $cdType) { //解救属地CD时间
					$msRow = array('rescue' => $objPlayer->CD()->toFront(O_CD::TYPE_FLOOR));
					M_Sync::addQueue($cityId, M_Sync::KEY_CDTIME, $msRow);
				} else if (M_City::CD_FLOOR == $cdType) {
					$msRow = array('cd' => $objPlayer->CD()->toFront(O_CD::TYPE_FLOOR));
					M_Sync::addQueue($cityId, M_Sync::KEY_FLOOR, $msRow); //同步
				}

				$ret = $objPlayer->save();

				$objPlayer->Log()->expense(T_App::MILPAY, $costMilpay, $action, $clearTime);

				if (M_City::CD_RESCUE == $cdType) {
					M_ColonyCity::setInfo($cityId, $arrRESCUE); //更新
				}

				if ($ret) {
					$errNo = '';
				}
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 清除所有建筑CD时间
	 * @author chenhui on 20120322
	 * @param int $payType 付费类型(默认军饷1,2点券)
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ACleanAllBuildCD($payType = T_App::MILPAY) {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_PARAM; //失败原因默认为参数错误
		$data  = array(); //返回数据默认为空数组

		$payType = intval($payType);

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		$nowtime = time();
		if (in_array($payType, array(T_App::MILPAY, T_App::COUPON))) {
			$cityId = $cityInfo['id'];

			$arrT      = $objPlayer->CD()->toFront(O_CD::TYPE_BUILD);
			$clearTime = 0;
			foreach ($arrT as $x => $v) {
				$clearTime += $v[0];
			}
			$needMilPay = M_Formula::calcCleanBuildCDNeed($clearTime);
			$costMilpay = $costCoupon = 0;
			switch ($payType) {
				case T_App::MILPAY:
					$objPlayer->City()->mil_pay -= $needMilPay;
					$costMilpay = $needMilPay;
					break;
				case T_App::COUPON:
					$objPlayer->City()->coupon -= $needMilPay;
					$costCoupon = $needMilPay;
					break;
			}


			$err = '';
			if ($needMilPay <= 0) {
				$err = T_ErrNo::ERR_ACTION;
			}
			if ($objPlayer->City()->mil_pay < 0) {
				$err = T_ErrNo::NO_ENOUGH_MILIPAY;
			} else if ($objPlayer->City()->coupon < 0) {
				$err = T_ErrNo::NO_ENOUGH_COUPON;
			}

			$errNo = $err;
			if (empty($err)) {
				$objPlayer->CD()->clean(O_CD::TYPE_BUILD);

				$errNo = '';

				//同步建筑CD时间
				$msRow = array(
					'build' => $objPlayer->CD()->toFront(O_CD::TYPE_BUILD)
				);
				M_Sync::addQueue($cityId, M_Sync::KEY_CDTIME, $msRow);

				$objPlayer->Quest()->check('build_cd', array('num' => 1));

				$objPlayer->save();

				$objPlayer->Log()->expense(T_App::MILPAY, $costMilpay, B_Log_Trade::E_ClearBuildCD, $clearTime);
			}
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 更改玩家名字
	 * @author chenhui on 20111019
	 * @param string $newNickName 新名字
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AModifyNickName($newNickName, $isProps = 0) {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_PARAM; //失败原因默认为参数错误
		$data  = array(); //返回数据默认为空数组

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();


		$newNickName = trim($newNickName);
		if (!empty($newNickName)) {

			$err     = '';
			$cityId  = $cityInfo['id'];
			$propsid = M_Props::MODIFY_CITY_NAME_PROPS_ID;
			if ($isProps > 0 && !M_Props::checkCityPropsNum($cityInfo['id'], $propsid, -1, 1)) {
				$err = T_ErrNo::CITY_MODIFY_NAME_PROPS_ERR;
			} else if (empty($isProps) && $cityInfo['mil_pay'] < T_App::CG_NICKNAME_COST) {
				$err = T_ErrNo::NO_ENOUGH_MILIPAY;
			} else if ($retErr = M_City::checkCityNickname($newNickName)) {
				$err = $retErr;
			}

			if (empty($err)) {
				if ($isProps > 0) {
					$bCost = $objPlayer->Pack()->decrNumByPropId($propsid, 1);
				} else {
					$bCost = $objPlayer->City()->decrCurrency(T_App::MILPAY, T_Chat::CG_NICKNAME_COST, B_Log_Trade::E_UpCityName, $newNickName);
				}

				$ret = $bCost && M_City::setCityInfo($cityId, array('nickname' => $newNickName));
				if ($ret) {

					$errNo = '';

					B_DB::instance('City')->update(array('nickname' => $newNickName), $cityId); //直接改数据库
					M_Sync::addQueue($cityId, M_Sync::KEY_CITY_INFO, array('nickname' => $newNickName)); //同步新名字
					M_City::upCityIdByNickName($cityInfo['nickname'], $newNickName, $cityId);

					if (intval($cityInfo['union_id']) > 0) {
						$unionInfo       = M_Union::getInfo($cityInfo['union_id']);
						$unionMemberInfo = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);
						$arrUpd          = array();
						($cityInfo['id'] == $unionInfo['create_city_id']) && $arrUpd['create_nick_name'] = $newNickName;
						(M_Union::UNION_MEMBER_TOP == intval($unionMemberInfo['position'])) && $arrUpd['boss'] = $newNickName;

						!empty($arrUpd) && M_Union::setInfo($cityInfo['union_id'], $arrUpd, true); //更新军团长[和创始人]名字
					}

					M_MapWild::syncWildMapBlockCache($cityInfo['pos_no']); //刷新此块地图数据
				} else {
					$errNo = T_ErrNo::ERR_UPDATE;
				}
			} else {
				$errNo = $err;
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 更改玩家个性签名
	 * @author chenhui on 20111019
	 * @param string $newSign 新个性签名
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AModifySignature($newSign) {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_PARAM; //失败原因默认为参数错误
		$data  = array(); //返回数据默认为空数组

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		$newSign = trim($newSign);
		if (!empty($newSign)) {

			$cityId = $cityInfo['id'];
			if (!B_Utils::isBlockName($newSign)) {
				$len = B_Utils::len($newSign);
				if ($len <= M_City::MAX_SIGN_LENGTH) {
					$ret = M_City::setCityInfo($cityId, array('signature' => htmlspecialchars($newSign)));
					if ($ret) {

						$errNo = '';
						M_Sync::addQueue($cityId, M_Sync::KEY_CITY_INFO, array('signature' => $newSign)); //同步个性签名
					} else {
						$errNo = T_ErrNo::ERR_DB_EXECUTE;
					}
				} else {
					$errNo = T_ErrNo::NICKNAME_LENGTH_ERR;
				}
			} else {
				$errNo = T_ErrNo::NICKNAME_ILLEGAL;
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 迁城
	 * @author chenhui on 20120327
	 * @param int $propsId 迁城道具ID
	 * @param int $zone 要迁到的洲编号
	 * @param int $posx 要迁到的X坐标
	 * @param int $posy 要迁到的Y坐标
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AMoveCity($propsId, $zone, $posx, $posy) {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_PARAM; //失败原因默认为参数错误
		$data  = array(); //返回数据默认为空数组

		$propsId = intval($propsId);
		$zone    = intval($zone);
		$posx    = intval($posx);
		$posy    = intval($posy);

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		if (isset(T_App::$map[$zone]) &&
			$posx > 0 && $posx < M_MapWild::WILD_MAP_MAX_POS_X &&
			$posy > 0 && $posy < M_MapWild::WILD_MAP_MAX_POS_Y
		) {

			$cityId   = intval($cityInfo['id']);
			$oldPosNo = $cityInfo['pos_no']; //旧的坐标编号

			if ($posx >= M_MapWild::WILD_MAP_MAX_POS_X || $posy >= M_MapWild::WILD_MAP_MAX_POS_Y) {
				$errNo = T_ErrNo::MAP_NOT_MOIVE;
			} else if (isset($cityInfo['pos_no']) && M_March_Hold::exist($cityInfo['pos_no'])) //如果被占领则不能使用免战道具
			{
				$errNo = T_ErrNo::CITY_OCCUPIED_MOVE_ZONE;
			} else if (M_ColonyCity::isHadCityColony($cityId)) {
				$errNo = T_ErrNo::CITY_OCCUPION_MOVE_ZONE;
			} else {
				if (M_Props::checkCityPropsNum($cityId, $propsId, M_Props::UNBINDING, 1)) {
					$propsInfo = M_Props::baseInfo($propsId);
					if ('MOVE_CITY' == $propsInfo['effect_txt']) {
						if (0 == M_Formula::calcCDTime($cityInfo['move_city_cd_time'])) {
							$list = M_March::getMarchList($cityId); //获取行军队列
							if (empty($list)) {
								list($oldZone, $oldPosX, $oldPosY) = M_MapWild::calcWildMapPosXYByNo($oldPosNo); //旧的坐标数据

								$posErr = ''; //坐标错误编号
								switch ($propsInfo['effect_val']) {
									case M_Props::MOVE_JUNIOR:
										if ($oldZone == $zone) {
											$posStr = M_MapWild::getWildMapNoHoldPos($oldZone);
											if (!empty($posStr)) {
												list($posx, $posy) = explode('_', $posStr);
											} else {
												$posErr = T_ErrNo::CITY_MAP_FULL_POS;
											}
										} else {
											$posErr = T_ErrNo::CITY_CANT_MOVE_ZONE;
										}
										break;
									case M_Props::MOVE_MIDDLE:
										$posStr = M_MapWild::getWildMapNoHoldPos($zone);
										if (!empty($posStr)) {
											list($posx, $posy) = explode('_', $posStr);
										} else {
											$posErr = T_ErrNo::CITY_MAP_FULL_POS;
										}
										break;
									case M_Props::MOVE_HIGH:
										break;
									default:
										break;
								}

								if (empty($posErr)) {
									$oldMapInfo = M_MapWild::getWildMapInfo($oldPosNo); //旧地图数据
									$posNo      = M_MapWild::calcWildMapPosNoByXY($zone, $posx, $posy);
									$mapInfo    = M_MapWild::getWildMapInfo($posNo);

									if (T_Map::WILD_MAP_CELL_SPACE == $mapInfo['type']) {
										$bDel = M_MapWild::delWildMapInfo($oldPosNo); //删除旧的地图数据
										if ($bDel) {
											$bUse = $objPlayer->Pack()->decrNumByPropId($propsId, 1);
											if ($bUse) {
												$moveCDTime = time() + T_App::ONE_HOUR * M_City::CD_MOVECITY_HOUR;
												//$moveCDTime = strtotime('+10 second');//测试用
												$arrUp = array(
													'move_city_cd_time' => $moveCDTime,
													'pos_no'            => $posNo
												);
												$bUp   = M_City::setCityInfo($cityId, $arrUp);
												if ($bUp) {
													$flag  = T_App::SUCC;
													$errNo = '';
													$data  = array($moveCDTime);

													$arrCityUp = array(
														'pos_area'     => $zone,
														'pos_x'        => $posx,
														'pos_y'        => $posy,
														'cd_move_city' => $moveCDTime,
													);
													M_Sync::addQueue($cityId, M_Sync::KEY_CITY_INFO, $arrCityUp); //同步城市坐标

													//同步资源增长值
													$objPlayer->Res()->upGrow('zone');

													M_MapWild::syncWildMapBlockCache($oldPosNo); //刷新旧的地图数据

													//M_MapWild::initWildMapData($cityId, $posNo);	//初始化新的地图数据
													M_MapWild::initWildMapDataMove($cityId, $posNo, $oldMapInfo['hold_expire_time']); //初始化新的地图数据

													$content = array(T_Lang::C_MOVE_MSG, array(T_Lang::$Map[$oldZone]), $oldPosX . ',' . $oldPosY, array(T_Lang::$Map[$zone]), $posx . ',' . $posy, array(T_Lang::$Map[$zone]));
													M_Message::sendSysMessage($cityId, json_encode(array(T_Lang::T_MOVE_CITY_TIP)), json_encode($content));
												} else {
													$errNo = T_ErrNo::ERR_UPDATE;
												}
											} else {
												$errNo = T_ErrNo::ERR_DB_EXECUTE;
											}
										} else {
											$errNo = T_ErrNo::WILD_MAP_DEL_FAIL;
										}
									} else {
										$errNo = T_ErrNo::WILD_POS_NOT_SPACE;
									}
								} else {
									$errNo = $posErr;
								}
							} else {
								$errNo = T_ErrNo::CITY_MARCH_ING;
							}
						} else {
							$errNo = T_ErrNo::CITY_CD_MOVE_CITY;
						}
					} else {
						$errNo = T_ErrNo::PROPS_WRONG_USE;
					}
				} else {
					$errNo = T_ErrNo::PROPS_NOT_ENOUGH; //道具数量不足
				}
			}
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 升级军衔
	 * @author chenhui on 20120531
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AUpMilRank() {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_ACTION; //失败原因默认
		$data  = array(); //返回数据默认为空数组

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();


		$cityId            = intval($cityInfo['id']);
		$oldMilRank        = intval($cityInfo['mil_rank']); //当前军衔
		$milRankRenownConf = M_Config::getVal('mil_rank_renown');

		if (isset($milRankRenownConf[$oldMilRank])) {
			$val = $milRankRenownConf[$oldMilRank];

			$curRenown = $cityInfo['renown'] - $val[0];
			if ($curRenown >= 0) {
				$updInfo = array(
					'mil_rank' => $oldMilRank + 1,
				);
				M_City::setCityInfo($cityId, $updInfo);

				if (0 == $oldMilRank) {
					$updInfo['mil_rank_daily'] = 1; //初始军衔等级升到1级时可领奖
				}

				M_Sync::addQueue($cityId, M_Sync::KEY_CITY_INFO, $updInfo);


				$errNo = '';
			} else {
				$errNo = T_ErrNo::NO_ENOUGH_RENOWN;
			}
		} else {
			$errNo = T_ErrNo::USER_MILRANK_CANT_UP;
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 领取军衔专属礼包奖励
	 * @author chenhui on 20110531
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AMilRankOnceAward() {
		//操作结果默认为失败0
		$errNo     = T_ErrNo::ERR_ACTION; //失败原因默认
		$data      = array(); //返回数据默认为空数组
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		//当前已领奖军衔奖励

		$milRankRenownConf = M_Config::getVal('mil_rank_renown');

		$errNo = T_ErrNo::USER_MILRANK_AWARD_CANT;
		if ($objPlayer->City()->mil_rank_award < $objPlayer->City()->mil_rank &&
			isset($milRankRenownConf[$objPlayer->City()->mil_rank_award])
		) {
			$curRankData = $milRankRenownConf[$objPlayer->City()->mil_rank_award];

			$awardArr = M_Award::rateResult($curRankData[1]);
			$bAward   = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_RankOnce);


			$errNo = T_ErrNo::ERR_UPDATE;
			if ($bAward) {
				$objPlayer->City()->mil_rank_award += 1;
				$objPlayer->save();
				$data = array();

				$errNo = '';
			}
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 领取军衔每日奖励
	 * @author chenhui on 20120601
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AMilRankDailyAward() {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_ACTION; //失败原因默认
		$data  = array(); //返回数据默认为空数组

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		$nowDate = date('Ymd');
		//当前已领奖军衔奖励日期 和 当前军衔
		$errNo = T_ErrNo::USER_MILRANK_DAILY_OVER;
		if ($nowDate != $objPlayer->City()->mil_rank_daily && $objPlayer->City()->mil_rank > 0) {
			$milRankRenownConf = M_Config::getVal('mil_rank_renown');
			$tmpRank           = max($objPlayer->City()->mil_rank - 1, 0);
			$errNo             = T_ErrNo::USER_MILRANK_AWARD_OVER;
			if (isset($milRankRenownConf[$tmpRank])) {
				$curRankData = $milRankRenownConf[$tmpRank];

				$awardArr = M_Award::rateResult($curRankData[2]);
				$bAward   = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_RankOnce);

				$errNo = T_ErrNo::ERR_UPDATE;
				if ($bAward) {
					$objPlayer->City()->mil_rank_daily = $nowDate;
					$objPlayer->save();
					$data = array();
					//M_Sync::addQueue($cityId, M_Sync::KEY_CITY_INFO, array('mil_rank_daily'=>0));
					$errNo = '';
				}
			}
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * @see CCollect::AAdd
	 */
	public function ACollect($title, $area, $posx, $posy, $unionName) {
		$obj = new C_Collect();
		return $obj->AAdd($title, $area, $posx, $posy, $unionName);
	}

	/**
	 * @see CCollect::AList
	 */
	public function AGetCollList() {
		$obj = new C_Collect();
		return $obj->AList();
	}

	/**
	 * @see CCollect::ADel
	 */
	public function ADelCollect($posNo) {
		$obj = new C_Collect();
		return $obj->ADel($posNo);
	}

	/**
	 * @see CMarket::ABuy
	 */
	public function AMarketBuy($resType, $num) {
		$obj = new C_Market();
		return $obj->ABuy($resType, $num);
	}

	/**
	 * @see CMarket::ASell
	 */
	public function AMarketSale($resType, $num) {
		$obj = new C_Market();
		return $obj->ASell($resType, $num);
	}


	/**
	 * @see CColony::AList
	 */
	public function AColonyList() {
		$obj = new C_Colony();
		return $obj->ANpcList();
	}

	/**
	 * @see CColony::AOpen
	 */
	public function AColonyOpen($no) {
		$obj = new C_Colony();
		return $obj->ANpcOpen($no);
	}

	/**
	 * @see CColony::ADel
	 */
	public function AColonyDel($zone, $posX, $posY) {
		$obj = new C_Colony();
		return $obj->ANpcDel($zone, $posX, $posY);
	}

	/**
	 * @see CColony::AExplore
	 */
	public function AColonyExplore($zone = 0, $posX = 0, $posY = 0) {
		$obj = new C_Colony();
		return $obj->ANpcExplore($zone, $posX, $posY);

	}

	/**
	 * @see CColony::AList
	 */
	public function ACityColonyList() //城市属地列表
	{
		$obj = new C_Colony();
		return $obj->ACityList();
	}

	/**
	 * @see CColony::AOpen
	 */
	public function ACityColonyOpen($no) //开启城市属地
	{
		$obj = new C_Colony();
		return $obj->ACityOpen($no);
	}

	/**
	 * @see CColony::ADel
	 */
	public function ACityColonyDel($zone, $posX, $posY) //删除城市属地
	{
		$obj = new C_Colony();
		return $obj->ACityDel($zone, $posX, $posY);
	}

	/**
	 * @see CColony::AExplore
	 */
	public function ACityColonyTax($zone = 0, $posX = 0, $posY = 0) //税收
	{
		$obj = new C_Colony();
		return $obj->ACityTax($zone, $posX, $posY);
	}

	/**
	 * @see CCamp::AList
	 */
	public function ACampaignList() {
		$obj = new C_Camp();
		return $obj->AList();
	}

	/**
	 * @see CCamp::AJoin
	 */
	public function ACampaignJoin($campId) {
		$obj = new C_Camp();
		return $obj->AJoin($campId);
	}

	/**
	 * @see CCamp::AOut
	 */
	public function ACampaignOut($campId) {
		$obj = new C_Camp();
		return $obj->AQuit($campId);
	}

	/**
	 * @see CCamp::AExplore
	 */
	public function ACampaignExplore($campId) {
		$obj = new C_Camp();
		return $obj->AExplore($campId);
	}

	/**
	 * @see CCamp::ADrawAward
	 */
	public function ACampaignDrawAward($campId) {
		$obj = new C_Camp();
		return $obj->ADrawAward($campId);
	}

	/**
	 * @see CCamp::AQueue
	 */
	public function ACampaignQueue($campId, $defLineNo) {
		$obj = new C_Camp();
		return $obj->AQueue($campId, $defLineNo);
	}

	/**
	 * @see CCamp::AHold
	 */
	public function ACampaignHold($campId, $defLineNo, $heroIdList, $isAuto = 1, $spPercent = 0) {
		$obj = new C_Camp();
		return $obj->AHold($campId, $defLineNo, $heroIdList, $isAuto, $spPercent);
	}

	/**
	 * @see CCamp::AMove
	 */
	public function ACampaignMove($campId, $begDefLineNo, $endDefLineNo, $marchId) {
		$obj = new C_Camp();
		return $obj->AMove($campId, $begDefLineNo, $endDefLineNo, $marchId);
	}

	/**
	 * @see CCamp::ADetail
	 */
	public function ACampaignDetail($campId, $type = 'bases') {
		$obj = new C_Camp();
		return $obj->ADetail($campId, $type);
	}

	/**
	 * @see CCamp::AMarchList
	 */
	public function ACampaignMarchList($campId) {
		$obj = new C_Camp();
		return $obj->AMarchList($campId);
	}

	/**
	 * 获取随机名字
	 * @author huwei
	 * @param int $gender
	 * @return string
	 */
	public function ARandName($gender = 1) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data  = array();
		if (isset(T_App::$genderType[$gender])) {
			$zone  = M_City::getTotal();
			$val   = M_RandName::getRandName($gender);
			$errNo = '';
			$data  = array(
				'newname' => !empty($val) ? $val : '',
				'zone'    => $zone,
			);
		}

		return B_Common::result($errNo, $data);
	}

}

?>
<?php

/**
 * VIP模块控制器(对外)
 * @author chenhui on 20111113
 */
class C_Vip extends C_I {
	/**
	 * 获取玩家当前VIP等级可购买功能及已有功能
	 * @author chenhui on 20111114
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AGetSysVipFunction() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data  = array();

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$cityId    = intval($cityInfo['id']);
		$vipLevel  = intval($cityInfo['vip_level']);

		if (M_Vip::isShopRes($vipLevel)) {
			$todayBuyTimes    = M_Vip::getTodayShopItemBuyNum($cityId, M_Vip::SHOP_RES, T_App::SHOP_RES_ID); //当前已购次数[数量]
			$data['SHOP_RES'] = array(
				'BuyTimes' => $todayBuyTimes,
				'NextNeed' => M_Formula::calcBuyShopResCost($todayBuyTimes + 1), //本次所需军饷价格
			);
		}

		$arrFunction = M_Vip::getFunctionConf($vipLevel);
		if (!empty($arrFunction) && is_array($arrFunction)) {
			$objVip = $objPlayer->Vip();
			foreach ($arrFunction as $funCode => $effInfo) {
				$nowFunVal     = $objVip->getVal($funCode); //当前功能值
				$todayBuyTimes = floor($nowFunVal / $effInfo[2]); //当前次数

				$data['FUNCTION'][] = array(
					'FunCode'  => $funCode,
					'EffDay'   => $effInfo[0],
					'MaxTimes' => $effInfo[1],
					'PerAdd'   => $effInfo[2],
					'LeftTime' => $objVip->getTime($funCode), //到期时间戳
					'BuyTimes' => max(0, $effInfo[1] - $todayBuyTimes), //有效期内剩余次数
					'NextNeed' => M_Formula::calcBuyFunctionCost($funCode, $todayBuyTimes + 1), //本次所需价格
				);
			}
		}
		$errNo = '';

		return B_Common::result($errNo, $data);
	}

	/**
	 * 购买VIP功能中的资源包[1天累计次数]
	 * @author chenhui on 20120405
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AVipShopResBuy() {

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		$cityId   = intval($cityInfo['id']);
		$vipLevel = $cityInfo['vip_level'];

		if (!M_Vip::isShopRes($vipLevel)) {
			return B_Common::result(T_ErrNo::VIP_NOT_LEVEL);
		}
		$canMaxTimes   = T_App::SYS_VAL_LIMIT_TOP; //允许最大次数
		$todayBuyTimes = M_Vip::getTodayShopItemBuyNum($cityId, M_Vip::SHOP_RES, T_App::SHOP_RES_ID); //当前已购次数
		if ($todayBuyTimes >= $canMaxTimes) {
			return B_Common::result(T_ErrNo::VIP_SHOP_RES_NO_TIMES);
		}
		$total = M_Formula::calcBuyShopResCost($todayBuyTimes + 1); //本次所需军饷价格
		if ($cityInfo['mil_pay'] < $total) {
			return B_Common::result(T_ErrNo::NO_ENOUGH_MILIPAY);
		}
		$bDecr = $objPlayer->City()->decrCurrency(T_App::MILPAY, $total, B_Log_Trade::E_BuyVipProps, $todayBuyTimes + 1);

		$num = 1000000;
		$objPlayer->Res()->incr('gold', $num, true);
		$objPlayer->Res()->incr('food', $num, true);
		$objPlayer->Res()->incr('oil', $num, true);

		$bAdd = $objPlayer->save();

		if ($bAdd) {
			M_Vip::upShopItemNumUserBuy($cityId, $vipLevel, M_Vip::SHOP_RES, T_App::SHOP_RES_ID, 1); //更新购买次数[兼容商品购买数量]

			$data = array(
				'NextNeed' => M_Formula::calcBuyShopResCost($todayBuyTimes + 2), //下次购买所需军饷
			);
			return B_Common::result('', $data);
		}

		return B_Common::result(T_ErrNo::ERR_UPDATE);
	}

	/**
	 * 购买VIP功能中的功能[3天累计次数]
	 * @author chenhui on 20120406
	 * @param string $funCode 功能标签
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AVipFunctionBuy($funCode) {

		$errNo     = T_ErrNo::ERR_PARAM;
		$data      = array();
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$funCode   = trim(strval($funCode));
		if (empty($funCode)) {
			return B_Common::result(T_ErrNo::ERR_PARAM);
		}
		$cityId   = intval($cityInfo['id']);
		$vipLevel = intval($cityInfo['vip_level']);

		$arrFunction = M_Vip::getFunctionConf($vipLevel); //功能配置
		if (!isset($arrFunction[$funCode])) {
			return B_Common::result(T_ErrNo::VIP_NOT_LEVEL);
		}
		$canMaxTimes = $arrFunction[$funCode][1]; //允许最大次数

		$objVip = $objPlayer->Vip();

		$perAdd    = $arrFunction[$funCode][2]; //每次增加的功能值
		$effDay    = $arrFunction[$funCode][0]; //每次有效期天数
		$nowFunVal = $objVip->getVal($funCode); //当前功能值

		$todayBuyTimes = floor($nowFunVal / $perAdd); //当前次数

		if ($todayBuyTimes >= $canMaxTimes) {
			return B_Common::result(T_ErrNo::VIP_FUNCTION_NO_TIMES);
		}
		$total = M_Formula::calcBuyFunctionCost($funCode, $todayBuyTimes + 1); //本次所需军饷价格
		if ($cityInfo['mil_pay'] < $total) {
			return B_Common::result(T_ErrNo::NO_ENOUGH_MILIPAY);
		}
		$bDecr = $objPlayer->City()->decrCurrency(T_App::MILPAY, $total, B_Log_Trade::E_BuyVipFunction, $funCode . ':' . ($todayBuyTimes + 1));
		if ($objVip->isExist($funCode)) {
			$newEndTime = $objVip->getTime($funCode);
		} else {
			$newEndTime = strtotime('+' . $effDay . ' day');
		}
		//Logger::debug(array(__METHOD__, $nowFunVal, $perAdd, $todayBuyTimes,$newEndTime));
		$objVip->setKey($funCode, $nowFunVal + $perAdd, $newEndTime);

		if (in_array($funCode, array('GOLD_INCR_YIELD', 'FOOD_INCR_YIELD', 'OIL_INCR_YIELD'))) {
			//同步资源增长值
			$objPlayer->Res()->upGrow('vip');
		}

		$bUp = $objPlayer->save();
		if ($bUp) {

			$errNo = '';
			$data  = array(
				'LeftTime' => $newEndTime, //到期时间戳
				'NextNeed' => M_Formula::calcBuyFunctionCost($funCode, $todayBuyTimes + 2), //本次所需价格
			);

			if ('HERO_INCR_ARMY' == $funCode) {
				$list     = M_Hero::getCityHeroList($cityId);
				$syncList = array();
				if (!empty($list) && is_array($list)) {
					$heroArmyNumAdd = M_Hero::heroArmyNumAdd($cityId, $cityInfo['union_id']);
					foreach ($list as $heroId) {
						$heroInfo = M_Hero::getCityHeroInfo($cityId, $heroId);
						if (!empty($heroInfo['id'])) {
							$setArr            = array(
								'max_army_num' => M_Formula::calcHeroMaxArmyNum($heroInfo['level'], $heroInfo['skill_army_num'], $heroArmyNumAdd),
							);
							$syncList[$heroId] = $setArr;
						}
					}
					!empty($syncList) && M_Sync::addQueue($cityId, M_Sync::KEY_HERO, $syncList); //同步数据
				}
			}
		} else {
			$errNo = T_ErrNo::ERR_UPDATE;
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 获取玩家当前VIP等级可购买商品
	 * @author chenhui on 20111114
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AGetSysVipShop() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data  = array();

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$vipLevel  = $cityInfo['vip_level'];
		$arrT      = M_Vip::getShopItemInfo($vipLevel);

		if (!empty($arrT) && is_array($arrT)) {
			foreach ($arrT as $type => $arr1) {
				if (!empty($arr1) && is_array($arr1)) {
					foreach ($arr1 as $itemId => $arr2) {
						$price         = '0';
						$itemName      = '';
						$faceId        = '';
						$pos           = '';
						$quality       = '';
						$feature       = '';
						$base_lead     = '';
						$base_command  = '';
						$base_military = '';
						$need_level    = '';
						$level         = '';
						$effecttxt     = '';
						$effectval     = '';
						if (M_Vip::SHOP_RES == $type || M_Vip::SHOP_DRAW == $type) {
							$baseInfo  = M_Props::baseInfo($itemId);
							$arrPriceT = json_decode($baseInfo['price'], true);

							$price     = $arr2[2];
							$itemName  = $baseInfo['name'];
							$faceId    = $baseInfo['face_id'];
							$feature   = $baseInfo['feature'];
							$effecttxt = $baseInfo['effect_txt'];
							$effectval = $baseInfo['effect_val'];
						} else if (M_Vip::SHOP_EQUI == $type) {
							$baseInfo      = M_Equip::baseInfo($itemId);
							$price         = $arr2[2];
							$itemName      = $baseInfo['name'];
							$faceId        = $baseInfo['face_id'];
							$pos           = $baseInfo['pos'];
							$quality       = $baseInfo['quality'];
							$feature       = trim($baseInfo['desc_1']);
							$base_lead     = $baseInfo['base_lead'];
							$base_command  = $baseInfo['base_command'];
							$base_military = $baseInfo['base_military'];
							$need_level    = $baseInfo['need_level'];
							$level         = $baseInfo['level'];
						}
						//VIP标识、VIP商品类型、商品ID、玩家限量、系统限量、系统限量剩余、价格、商品名字、图片ID、装备位置、装备品质、描述、统帅、指挥、军事、需求等级、强化等级
						$data[] = array(
							'Vip'           => 1,
							'Type'          => $type,
							'ItemId'        => $itemId,
							'UserLimit'     => $arr2[0],
							'UserLimitLeft' => M_Vip::getTodayShopItemUserLeftNum($cityInfo['id'], $vipLevel, $type, $itemId),
							'SysLimit'      => $arr2[1],
							'SysLimitLeft'  => M_Vip::getTodayShopItemSysLeftNum($vipLevel, $type, $itemId),
							'Price'         => array($price),
							'Name'          => $itemName,
							'FaceId'        => $faceId,
							'Pos'           => $pos,
							'Quality'       => $quality,
							'Feature'       => $feature,
							'BaseLead'      => $base_lead,
							'BaseCommand'   => $base_command,
							'BaseMilitary'  => $base_military,
							'NeedLevel'     => $need_level,
							'Level'         => $level,
							'EffectTxt'     => $effecttxt, //道具标签
							'EffectVal'     => $effectval, //标签对应的值[武器ID]
						);
					}
				}
			}
		}


		$errNo = '';

		return B_Common::result($errNo, $data);
	}

	/**
	 * 购买VIP商城中的物品
	 * @author chenhui on 20111117
	 * @param int $type 物品类型(1资源包 2武器图纸 3装备)
	 * @param int $itemId 对应ID
	 * @param int $buyNum 购买数量
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AVipShopBuy($type, $itemId, $buyNum) {

		$errNo = T_ErrNo::ERR_PARAM;
		$data  = array();

		$type      = intval($type);
		$itemId    = intval($itemId);
		$buyNum    = intval($buyNum);
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		if (isset(M_Vip::$shop_type[$type]) && $itemId > 0 && $buyNum > 0) {
			$cityId   = intval($cityInfo['id']);
			$vipLevel = $cityInfo['vip_level'];

			$itemInfo = M_Vip::getShopItemInfo($vipLevel, $type, $itemId);


			if (!empty($itemInfo) && is_array($itemInfo) && $itemInfo[2] > 0) {
				$propsNumArr = $objPlayer->Pack()->hasNum();
				$err         = '';
				if ($type == M_Vip::SHOP_EQUI && M_Equip::isEquipNumFull($cityId, $vipLevel)) {
					$err = T_ErrNo::EQUIP_NUM_FULL;
				} else if ($type == M_Vip::SHOP_DRAW && $propsNumArr['draw']['full']) {
					$err = T_ErrNo::DRAW_NUM_FULL;
				}
				if (empty($err)) {
					$needMilPay = $itemInfo[2] * $buyNum; //所需军饷
					if ($cityInfo['mil_pay'] >= $needMilPay) {
						if (M_Vip::isShopItemNumUserOK($cityId, $vipLevel, $type, $itemId, $buyNum)) {
							$sysNumErr = M_Vip::isShopItemSysOK($vipLevel, $type, $itemId, $buyNum);
							if (empty($sysNumErr)) {
								$vipConf     = M_Vip::getVipConfig();
								$maxEquipNum = !empty($vipLevel) ? $vipConf['PACK_EQUI'][$vipLevel] : $vipConf['PACK_EQUI'][0];
								$curEquipNum = M_Equip::getCityEquipNum($cityId);

								$canBuyNum = max(0, $maxEquipNum - $curEquipNum);
								if ((M_Vip::SHOP_EQUI != $type) ||
									(M_Vip::SHOP_EQUI == $type && $buyNum <= $canBuyNum)
								) {
									$bCost = $objPlayer->City()->decrCurrency(T_App::MILPAY, $needMilPay, B_Log_Trade::E_BuyVipProps, $type . ':' . $itemId . ':' . $buyNum);
									$opRet = false;
									switch ($type) {
										case M_Vip::SHOP_RES:
										case M_Vip::SHOP_DRAW:
											$opRet = $bCost && $objPlayer->Pack()->incr($itemId, $buyNum);
											break;
										case M_Vip::SHOP_EQUI:
											$tplInfo = M_Equip::baseInfo($itemId);
											for ($i = 0; $i < $buyNum; $i++) {
												$opRet = $bCost && M_Equip::makeEquip($cityId, $tplInfo); //把模板中的装备添加到城市装备中
											}
											break;
										default:
											break;
									}
									if ($opRet) {
										M_Vip::upShopItemNumUserBuy($cityId, $vipLevel, $type, $itemId, $buyNum);

										$errNo = '';
									} else {
										$errNo = T_ErrNo::ERR_DB_EXECUTE;
									}
								} else {
									$errNo = T_ErrNo::EQUIP_NUM_FULL;
								}
							} else {
								$errNo = $sysNumErr;
							}
						} else {
							$errNo = T_ErrNo::SHOP_USER_OVER_LIMIT;
						}
					} else {
						$errNo = T_ErrNo::NO_ENOUGH_MILIPAY;
					}
				} else {
					$errNo = $err;
				}
			} else {
				$errNo = T_ErrNo::VIP_NOT_LEVEL;
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 获取某玩家可购买减少出征时间列表
	 * @author chenhui on 20111118
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AGetCityVipDecrMarch() {

		$errNo     = T_ErrNo::ERR_ACTION;
		$data      = array();
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		$vipLevel = $cityInfo['vip_level'];
		$vipConf  = M_Vip::getVipConfig();
		if (isset($vipConf['DECR_MARCH_TIME'][$vipLevel])) {

			$errNo       = '';
			$strVipMarch = $vipConf['DECR_MARCH_TIME'][$vipLevel];
			$arrVipMarch = explode(',', $strVipMarch);
			if (!empty($arrVipMarch) && is_array($arrVipMarch)) {
				foreach ($arrVipMarch as $perVal) {
					$perPrice = M_Vip::getDecrMarchTimeCost($vipLevel, $perVal);
					if (!empty($perVal) && !empty($perPrice)) {
						$data[] = array($perVal, $perPrice); //百分比值、价格
					}
				}
			}
		} else {
			$errNo = T_ErrNo::VIP_NOT_LEVEL;
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 获取各VIP等级可奖励装备数据
	 * @author chenhui on 20111119
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AGetSysEquiAward() {
		$errNo     = T_ErrNo::ERR_ACTION;
		$data      = array();
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$errNo     = '';
		$data      = M_Base::vip();
		return B_Common::result($errNo, $data);
	}

	/**
	 * 获取某玩家已领取VIP装备奖励数据
	 * @author chenhui on 20111118
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AGetCityEquiAward() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data  = array();

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$extraInfo = M_Extra::getInfo($cityInfo['id']);
		if (isset($extraInfo['vip_equip_award'])) {

			$errNo              = '';
			$str_vip_equi_award = empty($extraInfo['vip_equip_award']) ? '[]' : $extraInfo['vip_equip_award'];
			$arrEquiAward       = json_decode($str_vip_equi_award, true);
			if (!empty($arrEquiAward) && is_array($arrEquiAward)) {
				foreach ($arrEquiAward as $vipLevel => $equiId) {
					if ($equiId > 0) {
						$tplInfo         = M_Equip::baseInfo($equiId);
						$arrT            = array(
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
						$data[$vipLevel] = $arrT;
					}
				}
			}
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 随机抽取并领取某VIP等级装备奖励
	 * @author chenhui on 20111119
	 * @param int $vipLevel VIP等级
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AReceVipEquiAward($vipLevel) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data  = array();

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		if (!empty($vipLevel)) {
			$vipLevel       = intval($vipLevel);
			$vipConf        = M_Vip::getVipConfig();
			$strEqui        = isset($vipConf['EQUI_AWARD'][$vipLevel]) ? $vipConf['EQUI_AWARD'][$vipLevel] : '';
			$arrLevelEquiId = explode(',', $strEqui);
			if ($cityInfo['vip_level'] >= $vipLevel && !empty($arrLevelEquiId) && is_array($arrLevelEquiId)) {
				$cityId             = intval($cityInfo['id']);
				$extraInfo          = M_Extra::getInfo($cityId);
				$str_vip_equi_award = empty($extraInfo['vip_equip_award']) ? '[]' : $extraInfo['vip_equip_award'];
				$arrEquiAward       = json_decode($str_vip_equi_award, true); //VIP已奖励装备数据
				if (!isset($arrEquiAward[$vipLevel]) || $arrEquiAward[$vipLevel] < 1) {
					if (M_Equip::isEquipNumFull($cityInfo['id'], $cityInfo['vip_level'])) {
						$errNo = T_ErrNo::EQUI_NUM_FULL;
					} else {
						$equiId = $arrLevelEquiId[array_rand($arrLevelEquiId)]; //某VIP等级随机抽取的装备ID

						$tplInfo                 = M_Equip::baseInfo($equiId);
						$eId                     = M_Equip::makeEquip($cityId, $tplInfo); //把模板中的装备添加到城市装备中
						$arrEquiAward[$vipLevel] = $equiId;
						$bUp                     = $eId && M_Extra::setInfo($cityId, array('vip_equip_award' => json_encode($arrEquiAward)));
						if ($bUp) {
							$tplInfo = M_Equip::baseInfo($equiId);
							$data    = array(
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


							$errNo = '';
						} else {
							$errNo = T_ErrNo::ERR_DB_EXECUTE;
						}
					}
				} else {
					$errNo = T_ErrNo::VIP_AWARD_EQUI_HAVE;
				}
			} else {
				$errNo = T_ErrNo::VIP_NOT_LEVEL;
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 获取某玩家已领取VIP军官奖励数据
	 * @author chenhui on 20111118
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AGetCityHeroAward() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data  = array();

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$extraInfo = M_Extra::getInfo($cityInfo['id']);
		if (isset($extraInfo['vip_hero_award'])) {

			$errNo              = '';
			$str_vip_hero_award = empty($extraInfo['vip_hero_award']) ? '[]' : $extraInfo['vip_hero_award'];
			$arrHeroAward       = json_decode($str_vip_hero_award, true);
			if (!empty($arrHeroAward) && is_array($arrHeroAward)) {
				foreach ($arrHeroAward as $vipLevel => $heroId) {
					$val      = M_Hero::baseInfo($heroId);
					$isLegend = 0;
					if (isset($val['quality'])) {
						$isLegend = 1;
					}
					$data[$vipLevel] = array(
						'NickName'     => isset($val['nickname']) ? $val['nickname'] : '',
						'Gender'       => isset($val['gender']) ? $val['gender'] : '',
						'Quality'      => isset($val['quality']) ? $val['quality'] : '',
						'Level'        => isset($val['level']) ? $val['level'] : '',
						'FaceId'       => isset($val['face_id']) ? $val['face_id'] : '',
						'IsLegend'     => $isLegend,
						'Exp'          => isset($val['exp']) ? $val['exp'] : '',
						'AttrLead'     => isset($val['attr_lead']) ? $val['attr_lead'] : '',
						'AttrCommand'  => isset($val['attr_command']) ? $val['attr_command'] : '',
						'AttrMilitary' => isset($val['attr_military']) ? $val['attr_military'] : '',
						'AttrEnergy'   => isset($val['attr_energy']) ? $val['attr_energy'] : '',
						'AttrMood'     => isset($val['attr_mood']) ? $val['attr_mood'] : '',
						'StatPoint'    => isset($val['stat_point']) ? floor($val['stat_point']) : '',
						'GrowRate'     => isset($val['grow_rate']) ? $val['grow_rate'] : '',
						'SkillSlotNum' => isset($val['skill_slot_num']) ? $val['skill_slot_num'] : '',
						'SkillSlot'    => isset($val['skill_slot']) ? $val['skill_slot'] : '',
						'SkillSlot1'   => isset($val['skill_slot_1']) ? $val['skill_slot_1'] : '',
						'SkillSlot2'   => isset($val['skill_slot_2']) ? $val['skill_slot_2'] : '',
						'Desc'         => isset($val['desc']) ? $val['desc'] : '',
						'Detail'       => isset($val['detail']) ? $val['detail'] : '',
					);
				}
			}
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 随机抽取并领取某VIP等级传奇军官奖励
	 * @author chenhui on 20111119
	 * @param int $vipLevel VIP等级
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AReceVipHeroAward($vipLevel) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data  = array();

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$vipLevel  = intval($vipLevel);
		$vipConf   = M_Vip::getVipConfig();
		$strHero   = isset($vipConf['HERO_AWARD'][$vipLevel]) ? $vipConf['HERO_AWARD'][$vipLevel] : '';
		$arrQual   = explode(',', $strHero);

		if ($cityInfo['vip_level'] >= $vipLevel && //vip等级不够
			!empty($arrQual) &&
			is_array($arrQual)
		) {
			$cityId             = intval($cityInfo['id']);
			$extraInfo          = M_Extra::getInfo($cityId);
			$str_vip_hero_award = empty($extraInfo['vip_hero_award']) ? '[]' : $extraInfo['vip_hero_award'];
			$arrHeroAward       = json_decode($str_vip_hero_award, true); //VIP已奖励传奇军官数据
			if (!isset($arrHeroAward[$vipLevel]) || $arrHeroAward[$vipLevel] < 1) //已赠送
			{
				$canHeroNum = M_Formula::canHasHeroNum($cityInfo['level']); //能拥有军官数量上限
				$hadHeroNum = M_Hero::totalCityHeroNum($cityId); //当前拥有军官数量
				if ($canHeroNum > $hadHeroNum) {
					$heroId                  = M_Hero::getRandVipAwardHeroId($arrQual); //某VIP等级随机抽取的传奇军官ID
					$hId                     = M_Hero::moveTplHeroToCityHero($cityId, $heroId, Logger::H_ACT_VIP); //复制模板中的英雄数据到城市英雄表
					$arrHeroAward[$vipLevel] = $heroId;
					$bUp                     = $hId && M_Extra::setInfo($cityId, array('vip_hero_award' => json_encode($arrHeroAward)));
					if ($bUp) {
						$val      = M_Hero::baseInfo($heroId);
						$isLegend = 0;
						if (isset($val['quality'])) {
							$isLegend = 1;
						}
						$data = array(
							'NickName'            => isset($val['nickname']) ? $val['nickname'] : '',
							'Gender'              => isset($val['gender']) ? $val['gender'] : '',
							'Quality'             => isset($val['quality']) ? $val['quality'] : '',
							'Level'               => isset($val['level']) ? $val['level'] : '',
							'FaceId'              => isset($val['face_id']) ? $val['face_id'] : '',
							'IsLegend'            => $isLegend,
							'Exp'                 => isset($val['exp']) ? $val['exp'] : '',
							'AttrLead'            => isset($val['attr_lead']) ? $val['attr_lead'] : '',
							'AttrCommand'         => isset($val['attr_command']) ? $val['attr_command'] : '',
							'AttrMilitary'        => isset($val['attr_military']) ? $val['attr_military'] : '',
							//确定的培养属性点
							'TrainingLead'        => 0,
							'TrainingCommand'     => 0,
							'TrainingMilitary'    => 0,
							//未确定的培养属性点
							'TmpTrainingLead'     => 0,
							'TmpTrainingCommand'  => 0,
							'TmpTrainingMilitary' => 0,

							'AttrEnergy'          => isset($val['attr_energy']) ? $val['attr_energy'] : '',
							'AttrMood'            => isset($val['attr_mood']) ? $val['attr_mood'] : '',
							'StatPoint'           => isset($val['stat_point']) ? floor($val['stat_point']) : '',
							'GrowRate'            => isset($val['grow_rate']) ? $val['grow_rate'] : '',
							'SkillSlotNum'        => isset($val['skill_slot_num']) ? $val['skill_slot_num'] : '',
							'SkillSlot'           => isset($val['skill_slot']) ? $val['skill_slot'] : '',
							'SkillSlot1'          => isset($val['skill_slot_1']) ? $val['skill_slot_1'] : '',
							'SkillSlot2'          => isset($val['skill_slot_2']) ? $val['skill_slot_2'] : '',
							'Desc'                => isset($val['desc']) ? $val['desc'] : '',
							'Detail'              => isset($val['detail']) ? $val['detail'] : '',
						);


						$errNo = '';
					} else {
						$errNo = T_ErrNo::ERR_DB_EXECUTE;
					}
				} else {
					$errNo = T_ErrNo::HERO_NUM_FULL_FAIL;
				}
			} else {
				$errNo = T_ErrNo::VIP_AWARD_HERO_HAVE;
			}
		} else {
			$errNo = T_ErrNo::VIP_NOT_LEVEL;
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * VIP玩家直接购买活力值
	 * @author chenhui on 20111105
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ABuyEnergy() {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_ACTION; //失败原因默认
		$data  = array(); //返回数据默认为空数组

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$energyNum = M_Vip::BUY_ENERGY_NUM;
		if ($energyNum > 0) {
			$cityId   = intval($cityInfo['id']);
			$vipLevel = $cityInfo['vip_level'];

			$vipConf       = M_Vip::getVipConfig();
			$canMaxTimes   = intval($vipConf['BUY_ENERGY'][$vipLevel]); //允许最大次数
			$todayBuyTimes = M_Vip::getTodayShopItemBuyNum($cityId, M_Vip::SHOP_ENERGY, M_Vip::ENERGY_ID); //当前次数
			if ($todayBuyTimes < $canMaxTimes) {
				if ($cityInfo['energy'] + $energyNum <= T_App::ENERGY_TOP_LIMIT) {
					$total = M_Formula::calcBuyEnergyCost($todayBuyTimes + 1); //本次所需军饷价格
					if ($cityInfo['mil_pay'] >= $total) {
						$bDecr      = $objPlayer->City()->decrCurrency(T_App::MILPAY, $total, B_Log_Trade::E_BuyVitality, $energyNum . ':' . ($todayBuyTimes + 1));
						$addItemArr = array('march_num' => $energyNum);

						$bAdd = false;
						if ($bDecr) {
							$objPlayer = new O_Player($cityId);
							//增加活力，含同步
							$bAdd = $objPlayer->City()->addPoint($addItemArr);
							$objPlayer->save();
						}

						if ($bAdd) {
							M_Vip::upShopItemNumUserBuy($cityId, $vipLevel, M_Vip::SHOP_ENERGY, M_Vip::ENERGY_ID, 1); //更新购买次数[兼容商品购买数量]


							$errNo = '';

							$data = array($canMaxTimes - $todayBuyTimes - 1, M_Formula::calcBuyEnergyCost($todayBuyTimes + 2)); //array(今日剩余购买次数,下次购买所需军饷)

							$msRow = array(
								'energy_left_times' => max(0, $canMaxTimes - $todayBuyTimes - 1),
								'energy_next_need'  => M_Formula::calcBuyEnergyCost($todayBuyTimes + 2)
							);
							M_Sync::addQueue($cityId, M_Sync::KEY_CITY_INFO, $msRow); //同步
						} else {
							$errNo = T_ErrNo::ERR_UPDATE;
						}
					} else {
						$errNo = T_ErrNo::NO_ENOUGH_MILIPAY;
					}
				} else {
					$errNo = T_ErrNo::CITY_ENERGY_FULL;
				}
			} else {
				$errNo = T_ErrNo::VIP_BUY_ENERGY_NO_TIMES;
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * VIP玩家直接购买军令值
	 * @author chenhui on 20120213
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ABuyMilOrder() {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_ACTION; //失败原因默认
		$data  = array(); //返回数据默认为空数组

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$orderNum  = M_Vip::BUY_MILORDER_NUM;
		if ($orderNum > 0) {
			$cityId   = intval($cityInfo['id']);
			$vipLevel = $cityInfo['vip_level'];

			$vipConf       = M_Vip::getVipConfig();
			$canMaxTimes   = intval($vipConf['BUY_MILORDER'][$vipLevel]); //允许最大次数
			$todayBuyTimes = M_Vip::getTodayShopItemBuyNum($cityId, M_Vip::SHOP_MILORDER, M_Vip::MILORDER_ID); //当前次数
			if ($todayBuyTimes < $canMaxTimes) {
				if ($cityInfo['mil_order'] + $orderNum <= T_App::MILORDER_TOP_LIMIT) {
					$total = M_Formula::calcBuyMilOrderCost($todayBuyTimes + 1); //本次所需军饷价格
					if ($cityInfo['mil_pay'] >= $total) {
						$bDecr      = $objPlayer->City()->decrCurrency(T_App::MILPAY, $total, B_Log_Trade::E_BuyMilOrder, $orderNum . ':' . ($todayBuyTimes + 1));
						$addItemArr = array('atkfb_num' => $orderNum);

						$bAdd = false;
						if ($bDecr) {
							//增加军令，含同步
							$bAdd = $objPlayer->City()->addPoint($addItemArr);
						}

						if ($bAdd) {
							M_Vip::upShopItemNumUserBuy($cityId, $vipLevel, M_Vip::SHOP_MILORDER, M_Vip::MILORDER_ID, 1); //更新购买次数[兼容商品购买数量]


							$errNo = '';

							$data = array($canMaxTimes - $todayBuyTimes - 1, M_Formula::calcBuyMilOrderCost($todayBuyTimes + 2)); //array(今日剩余购买次数,下次购买所需军饷)

							$msRow = array(
								'order_left_times' => max(0, $canMaxTimes - $todayBuyTimes - 1),
								'order_next_need'  => M_Formula::calcBuyMilOrderCost($todayBuyTimes + 2)
							);
							M_Sync::addQueue($cityId, M_Sync::KEY_CITY_INFO, $msRow); //同步
						} else {
							$errNo = T_ErrNo::ERR_UPDATE;
						}
					} else {
						$errNo = T_ErrNo::NO_ENOUGH_MILIPAY;
					}
				} else {
					$errNo = T_ErrNo::CITY_MILORDER_FULL;
				}
			} else {
				$errNo = T_ErrNo::VIP_BUY_MILORDER_NO_TIMES;
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 每天领取一个VIP宝箱
	 * @author chenhui on 20120826
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AReceVipPackage() {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_ACTION; //失败原因默认
		$data  = array(); //返回数据默认为空数组

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$cityId    = intval($cityInfo['id']);
		$vipLevel  = intval($cityInfo['vip_level']);
		$packInfo  = M_Vip::isVipPackPower($vipLevel);
		if (!empty($packInfo)) {
			$arrVipPack = explode('_', $cityInfo['vip_pack_date']);
			if (date('Ymd') != $arrVipPack[0] || $vipLevel > $arrVipPack[1]) {
				$arrInfo   = explode('_', $packInfo);
				$packId    = $arrInfo[0]; //奖励ID
				$packNum   = $arrInfo[1]; //道具数量
				$propsInfo = M_Props::baseInfo($packId);
				if ('NEWBIE_PACKS' == $propsInfo['effect_txt']) {

					$awardArr = M_Award::rateResult($propsInfo['effect_val']);
					$award    = M_Award::toText($awardArr);

					for ($i = 0; $i < $packNum; $i++) {
						$bAward = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Prop);
					}
					if ($bAward) {
						M_City::setCityInfo($cityId, array('vip_pack_date' => date('Ymd') . '_' . $vipLevel));
						M_Sync::addQueue($cityId, M_Sync::KEY_CITY_INFO, array('vip_pack_date' => 0));
						$data = $award;


						$errNo = '';
					} else {
						$errNo = T_ErrNo::ERR_DB_EXECUTE;
					}
				} else {
					$errNo = T_ErrNo::PROPS_WRONG_USE;
				}
			} else {
				$errNo = T_ErrNo::VIP_PACK_RECEIVED;
			}
		} else {
			$errNo = T_ErrNo::VIP_NOT_LEVEL;
		}

		return B_Common::result($errNo, $data);
	}

}

?>
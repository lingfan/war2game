<?php

/**
 * 道具
 */
class C_Props extends C_I {
	public function AGetAllSysInfo($type = 0) {
		//操作结果默认为失败0
		$errNo     = T_ErrNo::ERR_ACTION; //失败原因默认
		$data      = array();
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		$now = time();
		if ($type == M_Props::TYPE_STUFF) {
			$data = M_Base::item_stuff();
		} else if ($type == M_Props::TYPE_DRAW) {
			$data = M_Base::item_draw();
		} else {
			$data = M_Base::props();
		}

		$errNo = '';

		return B_Common::result($errNo, $data);
	}


	/**
	 * 在道具栏直接使用道具
	 * @author chenhui on 20110524
	 * @param int $propsId 道具ID
	 * @param int $binding 绑定状态
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AUseProps($itemId, $binding = M_Props::UNBINDING) {
		$itemId  = intval($itemId);
		$binding = intval($binding);
		$data    = array();

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$now       = time();
		if (empty($itemId)) {
			return B_Common::result(T_ErrNo::ERR_PARAM);
		}
		$bUse = $objPlayer->Pack()->decrNumBySlotId($itemId, 1);
		if (!$bUse) { //道具数量不足
			return B_Common::result(T_ErrNo::PROPS_NOT_ENOUGH);
		}
		$itemData = $objPlayer->Pack()->getPropsBySlotId($itemId);
		$propsId  = $itemData[0];

		$baseInfo = M_Props::baseInfo($propsId); //道具基础数据
		if (empty($baseInfo)) {
			return B_Common::result(T_ErrNo::PROPS_WRONG_USE);
		}

		$arr_effect = M_Props::$EffectUse; //道具效果对应函数的数组
		$effect_txt = $baseInfo['effect_txt']; //道具效果编码
		$err        = '';
		//判断某城市某状态道具数量是否大于某值
		if (!isset($arr_effect[$effect_txt])) {
			$err = T_ErrNo::PROPS_CANT_DIRECT_USE; //此道具不可以在此直接使用
		} else if ('AVOID_WAR' == $effect_txt && $objPlayer->City()->avoid_war_cd_time > $now) { //不能连续使用免进攻道具
			$err = T_ErrNo::AVOID_WAR_CD_HOLD;
		} else if ('AVOID_HOLD' == $effect_txt) { //不能连续使用免占领道具
			$err = M_Props::canUseNoHoldProps($objPlayer);
		} else if ('HERO_WAR_EXP_INCR' == $effect_txt && !$objPlayer->Props()->canUseHeroExpProps()) { //不能连续使用增加军官经验道具
			$err = T_ErrNo::HERO_EXP_CANT_CONTINUE;
		} else if ('REMOVE_AVOID_WAR' == $effect_txt && !M_Props::canUseRemoveNoWar($cityInfo)) {
			$err = T_ErrNo::CITY_NO_AVOID_WAR;
		} else if ('HERO_CARD' == $effect_txt && M_Hero::isHeroNumFull($cityInfo['id'])) {
			$err = T_ErrNo::HERO_NUM_FULL_FAIL;
		}

		if (!empty($err)) {
			return B_Common::result($err);
		}

		$ret   = $objPlayer->Props()->call($baseInfo);
		if (!$ret) {//道具使用失败，数据库执行错误
			return B_Common::result(T_ErrNo::ERR_DB_EXECUTE);
		}

		$objPlayer->Quest()->check('props_use', array('id' => $propsId, 'num' => 1));

		M_QqShare::check($objPlayer, 'props_use', array());

		$objPlayer->save();

		if ('NEWBIE_PACKS' == $baseInfo['effect_txt']) {
			$data = $ret; //解析礼包类详细数据
		}


		return B_Common::result('', $data);
	}

	/**
	 * 在商城购买一定数量的某道具
	 * @author chenhui on 20110602
	 * @param int $propsId 道具ID
	 * @param int $num 购买数量
	 * @param int $payType 支付类型(1军饷、2点券)
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ABuyProps($propsId, $num, $payType) {
		//默认失败
		$errNo     = T_ErrNo::ERR_PARAM; //默认失败编号:参数错误
		$propsId   = intval($propsId);
		$num       = intval($num);
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		if ($propsId > 0 && $num > 0 && in_array($payType, array(T_App::MILPAY, T_App::COUPON))) {
			$basepropsinfo = M_Props::baseInfo($propsId); //道具基础数据
			$arr_price     = json_decode($basepropsinfo['price'], true); //道具价格数组
			if ($basepropsinfo['is_shop'] &&
				isset($basepropsinfo['price']) &&
				isset($arr_price[$payType]) &&
				isset($cityInfo['id'])
			) {
				$total     = $arr_price[$payType] * $num; //总价格
				$err       = ''; //添加军饷或礼券判断
				$propsInfo = M_Props::baseInfo($propsId);

				$propsNumArr = $objPlayer->Pack()->hasNum();

				if ($propsInfo['type'] == M_Props::TYPE_DRAW && $propsNumArr['draw']['full']) {
					$err = T_ErrNo::DRAW_NUM_FULL;
				} else if (in_array($propsInfo['type'], array(M_Props::TYPE_INNER, M_Props::TYPE_HERO, M_Props::TYPE_TREA, M_Props::TYPE_WAR)) && $propsNumArr['normal']['full']) {
					$err = T_ErrNo::PROPS_NUM_FULL;
				} else if ($propsInfo['type'] == M_Props::TYPE_STUFF && $propsNumArr['stuff']['full']) {
					$err = T_ErrNo::MATERIAL_NUM_FULL;
				}

				if ($payType == T_App::MILPAY && $cityInfo['mil_pay'] < $total) {
					$err = T_ErrNo::NO_ENOUGH_MILIPAY;
				} else if ($payType == T_App::COUPON && $cityInfo['coupon'] < $total) {
					$err = T_ErrNo::NO_ENOUGH_COUPON;
				}

				if (empty($err)) {
					$bCost = $objPlayer->City()->decrCurrency($payType, $total, B_Log_Trade::E_BuyProps, $basepropsinfo['name']);
					$ret   = $bCost && $objPlayer->Pack()->incr($propsId, $num);
					if ($ret) {

						$objPlayer->Quest()->check('props_buy', array('id' => $propsId, 'num' => $num));
						$objPlayer->save();

						M_QqShare::check($objPlayer, 'props_buy', array());
						$errNo = '';
					}
				} else {
					$errNo = $err;
				}
			}
		}

		$data = array();
		return B_Common::result($errNo, $data);
	}

	/**
	 * 获取当前城市正在使用的未过期道具效果数据
	 * @author chenhui on 20110617
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AGetCityUseProps() {
		//默认失败
		$data      = array();
		$objPlayer = $this->objPlayer;
		$tmpData   = $objPlayer->Props()->get();
		if (!empty($tmpData) && is_array($tmpData)) {
			foreach ($tmpData as $key => $val) {
				$data[] = array(
					'EffectTxt' => $val['effect_txt'],
					'EffectVal' => $val['effect_val'],
					'EndTime'   => M_Formula::calcCDTime($val['end_time']),
				);
			}
		}
		$errNo = '';

		$objPlayer->Res()->upGrow('props');
		$objPlayer->save();

		return B_Common::result($errNo, $data);
	}

	/**
	 * 抽取幸运卡片
	 * @param int $propsId
	 */
	public function ALuckCard($propsId) {
		//默认失败
		$errNo     = T_ErrNo::ERR_PARAM; //默认失败编号:参数错误
		$data      = array();
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$cityId    = $cityInfo['id'];
		$propsInfo = M_Props::baseInfo($propsId); //道具信息
		$err       = '';
		$awardId   = $propsInfo['effect_val'];

		$awardArr = M_Award::rateResult($awardId);
		$CurAward = M_Award::toText($awardArr);

		$bDecr = $objPlayer->Pack()->decrNumByPropId($propsId, 1);
		if (!$bDecr) {
			$err = T_ErrNo::ERR_LUCK_CARD;
		} else if ($propsInfo['effect_txt'] != 'LUCK_CARD') {
			$err = T_ErrNo::ERR_LUCK_CARD_PROPS;
		}

		$errNo = $err;
		if (empty($err)) {
			$bSucc = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Task); //奖励结果
			$oth   = array();
			if (!empty($bSucc)) {
				$awars_all = $CurAward; //所有的奖励
				foreach ($awars_all as $key => $val) {
					if ($val[0] == $CurAward[0][0] && //类别
						$val[1] == $CurAward[0][1] && //编号
						$val[3] == $CurAward[0][3]
					) //数量
					{
					} else {
						if ($val[0] == 'props') {
							$propsInfo = M_Props::baseInfo($val[1]);
							$val[4]    = $propsInfo['face_id'];
						} else if ($val[0] == 'equip') {
							$equiTplInfo = M_Equip::baseInfo($val[1]);
							$val[4]      = $equiTplInfo['face_id'];
						} else if ($val[0] == 'hero') {
							$heroTplInfo = M_Hero::baseInfo($val[1]);
							$val[4]      = $heroTplInfo['face_id'];
						} else {
							unset($val[4]);
						}
						$oth[] = $val;
					}
				}
			}

			$objPlayer->save();

			$errNo = '';
			shuffle($oth);
			$data['CurAward'] = $CurAward; //抽取的奖励，前端显示的格式
			$data['OthAward'] = $oth; //翻开的其他14个奖励;
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 将背包中的所有道具出售给系统
	 * @author chenhui on 20120907
	 * @param int $itemId 物品ID
	 * @param int $propsNum 数量
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ASale2Sys($slotId, $propsNum = 1) {
		//默认失败
		$errNo = T_ErrNo::ERR_PARAM; //默认失败编号:参数错误
		$data  = array();

		$objPlayer = $this->objPlayer;
		$slotId    = intval($slotId);
		$propsNum  = intval($propsNum);
		$errNo     = T_ErrNo::PROPS_NOT_ENOUGH;
		$bDecr     = $objPlayer->Pack()->decrNumBySlotId($slotId, $propsNum);
		if ($bDecr) {
			$itemData  = $objPlayer->Pack()->getPropsBySlotId($slotId);
			$propsInfo = M_Props::baseInfo($itemData[0]);
			$incrGold  = intval($propsInfo['sys_price']) * $propsNum;
			$objPlayer->Res()->incr('gold', $incrGold, true);
			$bIncr = $objPlayer->save();
			$errNo = T_ErrNo::ERR_DB_EXECUTE;
			if ($bIncr) {
				$errNo = '';
			}
		}


		return B_Common::result($errNo, $data);
	}

	/**
	 * 出售多个物品给系统
	 * @author huwei
	 * @param array $itemIdsStr
	 * @return array
	 */
	public function ASaleMulti2Sys($itemIdsStr) {
		//默认失败
		$errNo      = T_ErrNo::ERR_PARAM; //默认失败编号:参数错误
		$data       = array();
		$objPlayer  = $this->objPlayer;
		$cityInfo   = $objPlayer->getCityBase();
		$itemIdsArr = explode(',', $itemIdsStr);
		//var_dump($itemIdsArr);
		//echo "<hr>";
		if (!empty($itemIdsArr[0])) {
			$cityId    = $cityInfo['id'];
			$salePirce = array();
			foreach ($itemIdsArr as $itemId) {
				$itemData = $objPlayer->Pack()->getPropsBySlotId($itemId);

				if ($itemData) {
					$propsId = $itemData[0];
					$num     = $itemData[2];
					$objPlayer->Pack()->decrNumBySlotId($itemId, $num);
					$salePirce[$propsId] += isset($salePirce[$propsId]) ? $num : 0;
				}
			}

			if (!empty($salePirce) && !empty($saleIds)) {
				$gold = 0;
				foreach ($salePirce as $propsId => $num) {
					$propsInfo = M_Props::baseInfo($propsId);
					$gold += intval($propsInfo['sys_price']) * $num;

					Logger::opItem($cityId, $propsId, Logger::P_ACT_DECR, $num . "==Props_SaleMulti2Sys");
				}

				if ($gold > 0) {
					$objPlayer->Res()->incr('gold', $gold, true);
					$bIncr = $objPlayer->save();
					$errNo = '';
				}
			}
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 使用道具[雷达]
	 * @author chenhui on 20120912
	 * @param int $propsId 道具ID
	 * @param string $nickName 要查找的角色名
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AUseRadar($propsId, $nickName) {
		//默认失败
		$errNo = T_ErrNo::ERR_PARAM; //默认失败编号:参数错误
		$data  = array();

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$propsId   = intval($propsId);
		$nickName  = trim($nickName);
		$errNo     = T_ErrNo::PROPS_NOT_ENOUGH; //道具数量不足
		$bDecr     = $objPlayer->Pack()->decrNumByPropId($propsId, 1);
		if ($bDecr) {
			$errNo     = T_ErrNo::PROPS_WRONG_USE;
			$propsInfo = M_Props::baseInfo($propsId);
			if ('RADAR_SEEK' == $propsInfo['effect_txt']) {
				$seekCityId = M_City::getCityIdByNickName($nickName);
				$errNo      = T_ErrNo::USER_NO_EXIST;
				if (!empty($seekCityId)) {
					$seekCityInfo = M_City::getInfo($seekCityId);
					$data         = M_MapWild::calcWildMapPosXYByNo($seekCityInfo['pos_no']);
					$unionName    = '';
					if (!empty($seekCityInfo['union_id'])) {
						$unionInfo = M_Union::getInfo($seekCityInfo['union_id']);
						$unionName = isset($unionInfo['name']) ? $unionInfo['name'] : '';
					}
					$data[] = $unionName;

					$objPlayer->save();
					$errNo = '';
				}
			}
		}


		return B_Common::result($errNo, $data);
	}

	/**
	 * 兑换功能
	 * @author huwei at 20121207
	 * @param int $eid 兑换ID
	 */
	public function AExchange($eid, $milpay = 0, $keepPropsId = 0) {
		//默认失败
		$errNo     = T_ErrNo::ERR_PARAM; //默认失败编号:参数错误
		$data      = array();
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$now       = time();
		if ($eid > 0) {
			$baseMilpaySuccRate = B_Utils::str2arr(M_Config::getVal('exchange_milpay_succ_rate'));
			$costMilpay         = $milpaySuccRate = 0;
			if (isset($baseMilpaySuccRate[$milpay])) {
				$milpaySuccRate = $baseMilpaySuccRate[$milpay];
				$costMilpay     = $milpay;
			}

			$cityId           = $cityInfo['id'];
			$tmpBase          = M_Base::exchangeAll();
			$baseExchangeList = $tmpBase['data'];
			if (isset($baseExchangeList[$eid])) {
				$info = $baseExchangeList[$eid]; //道具信息
				$has  = 0;

				$needProps = $info['need_props'];
				foreach ($needProps as $needPropId => $needNum) {
					$hasNum = $objPlayer->Pack()->getNumByPropsId($needPropId);
					if ($hasNum >= $needNum) {
						$has++;
					}
				}

				$hasKeep = false;
				if ($keepPropsId > 0) {
					$pInfo = M_Props::baseInfo($keepPropsId);
					if ($pInfo['effect_txt'] == 'EXCHANGE_KEEP_PROPS') {
						$hasKeep = $objPlayer->Pack()->decrNumByPropId($keepPropsId, 1);
					}
				}

				$err = '';

				if (strtotime($info['start_time']) > $now || strtotime($info['end_time']) < $now) {
					$err = T_ErrNo::ERR_EXCHANGE_EXPIRE;
				} else if ($has != count($needProps)) {
					$err = T_ErrNo::ERR_EXCHANGE_PROPS_NUM;
				} else if (empty($info['new_props'])) {
					$err = T_ErrNo::ERR_EXCHANGE_PROPS_VAL;
				} else if ($costMilpay > 0 && $cityInfo['mil_pay'] < $costMilpay) {
					$err = T_ErrNo::ERR_EXCHANGE_NO_MILPAY;
				} else if ($keepPropsId > 0 && !$hasKeep) {
					$err = T_ErrNo::ERR_EXCHANGE_NO_KEEP_PID;
				}

				if (empty($err)) {
					$rate = min($info['base_succ'] + $milpaySuccRate, 100);
					//打造是否成功
					$bSucc = B_Utils::odds($rate);

					$isDecrStuff = true;
					//打造失败  不扣合成材料
					if ($hasKeep && !$bSucc) {
						$isDecrStuff = false;
					}

					if ($isDecrStuff) {
						$fail = array();
						foreach ($needProps as $pId => $pNum) {
							$bUse = $objPlayer->Pack()->decrNumByPropId($pId, $pNum);
							if (!$bUse) {
								$fail[$pId] = $pNum;
							}
						}

						if (count($fail) > 0) {
							Logger::debug(array(__METHOD__, 'has no props', $fail));
						}
					}


					if ($costMilpay > 0) {
						$bCost = $objPlayer->City()->decrCurrency(T_App::MILPAY, $costMilpay, B_Log_Trade::E_Exchange, $eid);
						if (!$bCost) {
							Logger::error(array(__METHOD__, 'err cost milpay', $cityId, $costMilpay));
						}
					}

					$ret = false;
					if ($bSucc) {
						if ($info['type'] == 1 || $info['type'] == 3) {
							$ret = $objPlayer->Pack()->incr($info['new_props'], 1);
						} else if ($info['type'] == 2) {
							$tplInfo = M_Equip::baseInfo($info['new_props']);
							if ($tplInfo['id']) {
								$ret = M_Equip::makeEquip($cityId, $tplInfo);
							}
						}
					}

					$objPlayer->save();

					$data['Succ']     = $bSucc ? 1 : 0;
					$data['LoseItem'] = $isDecrStuff ? 1 : 0; //材料是否消失
					$errNo            = '';
				} else {
					$errNo = $err;
				}
			}

		}


		return B_Common::result($errNo, $data);
	}

	/**
	 * 整理道具背包
	 * @author duhuihui at 20130315
	 * @param int $type 背包类型
	 */
	public function AArrangeBackpack($type) {
		// 默认失败
		$data      = array();
		$objPlayer = $this->objPlayer;
		$errNo     = T_ErrNo::DRAW_IS_EMPTY;
		if ($objPlayer->Pack()->get()) {
			$ret   = $objPlayer->Pack()->sort();
			$errNo = '';
		}
		return B_Common::result($errNo, $data);
	}
}
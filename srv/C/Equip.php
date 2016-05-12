<?php

/**
 * 装备接口
 */
class C_Equip extends C_I {

	/**
	 * 批量出售装备
	 * @author chenhui on 20121015
	 * @param string $strCityEquipId 逗号隔开城市装备ID字符串
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ASellBatch($strCityEquipId = '') {
		$errNo = T_ErrNo::ERR_PARAM;

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$strCityEquipId = trim($strCityEquipId);

		$cityId = intval($cityInfo['id']);
		$equipIds = explode(',', $strCityEquipId);
		if (empty($equipIds)) {
			return B_Common::result(T_ErrNo::ERR_PARAM);
		}

		$errIds = array();
		foreach ($equipIds as $equipId) {
			$equipId = intval($equipId);
			$equipInfo = M_Equip::getInfo($equipId);

			if (empty($equipInfo['id'])) {
				$errIds[] = $equipId;
			} else if ($equipInfo['city_id'] == $cityId) {
				$errIds[] = $equipId;
			} else if ($equipInfo['is_use'] == T_Equip::EQUIP_IS_USE) {
				$errIds[] = $equipId;
			} else if ($equipInfo['on_sale'] == M_Auction::GOODS_ON_SALE_YES) {
				$errIds[] = $equipId;
			}
		}

		if (count($errIds) > 0) {
			return B_Common::result(T_ErrNo::EQUIP_DEL_FAIL);
		}

		foreach ($equipIds as $equipId) {
			Logger::opEquip($cityId, $equipId, Logger::E_ACT_SELLGOLD, $equipInfo['name']);
			//出售资源返还
			$sellGold = M_Formula::equipSellGold($equipInfo['quality'], $equipInfo['need_level'], $equipInfo['level']);

			$objPlayer->Res()->incr('gold', $sellGold);

			if (!M_Equip::delCityEquip($equipId, $cityInfo['id'])) {
				return B_Common::result(T_ErrNo::EQUIP_DEL_FAIL);
			}
		}

		$objPlayer->save();

		return B_Common::result('');
	}


	/**
	 * 合成装备
	 * @author hejunyun
	 * @param int $equipId1 装备1ID
	 * @param int $equipId2 装备2ID
	 * @param int $equipId3 装备3ID
	 */
	public function AFusioning($equipId1 = 0, $equipId2 = 0, $equipId3 = 0) {
		$errNo = T_ErrNo::ERR_ACTION;
		$equipId1 = intval($equipId1);
		$equipId2 = intval($equipId2);
		$equipId3 = intval($equipId3);

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$tmpArr[$equipId1] = 0;
		$tmpArr[$equipId2] = 0;
		$tmpArr[$equipId3] = 0;

		if (count($tmpArr) != 3) {
			return B_Common::result(T_ErrNo::ERR_PARAM);
		}

		$cityId = $cityInfo['id'];
		$info1 = M_Equip::getInfo($equipId1);
		$info2 = M_Equip::getInfo($equipId2);
		$info3 = M_Equip::getInfo($equipId3);


		if (empty($info1['id']) || $info1['city_id'] != $cityId || ($info1['flag'] & M_Equip::FLAG_FUSIONING) == 0) {
			return B_Common::result(T_ErrNo::SUIT_SYNTHESIS_NO);
		}

		$err = array();
		$arr = array('pos', 'need_level', 'quality', 'city_id', 'is_use', 'suit_id', 'flag', 'on_sale');
		foreach ($arr as $val) {
			if (intval($info1[$val]) != intval($info2[$val]) || intval($info1[$val]) != intval($info3[$val])) {
				$err[] = $val;
			}
		}

		if (count($err) > 0) {
			//装备不能合成,装备合成必须是同名同等级同品质
			return B_Common::result(T_ErrNo::EQUIP_CANNOT_FUSIONING);
		} else if ($info1['quality'] >= T_Equip::EQUIP_GOLD) {
			//装备不能合成,已是最高品质
			return B_Common::result(T_ErrNo::EQUIP_CANNOT_FUSIONING);
		} else if ($info1['is_use'] == T_Equip::EQUIP_IS_USE) {
			return B_Common::result(T_ErrNo::EQUIP_WEAR);
		} else if ($info1['on_sale'] == M_Auction::GOODS_ON_SALE_YES) {
			return B_Common::result(T_ErrNo::EQUIP_ON_SALE);
		}

		$info = $info1;
		$info['quality'] = min(($info1['quality'] + 1), 6); //品质
		$info['level'] = intval(($info1['level'] + $info2['level'] + $info3['level']) / 3 * T_Equip::LEVEL_PARAM); //强化等级取3件装备的平均值再乘以系数
		$equipSuitFace = !empty($info['suit_id']) ? '_' . $info['suit_id'] : ''; //修改图片faceId
		$info['face_id'] = $info['pos'] . '_' . $info['quality'] . '_' . $info['need_level'] . $equipSuitFace;
		$info['name'] = M_Equip::getEquipName($info['face_id']);
		//根据品质和强化等级获取强化属性
		$addNum = 0;
		if ($info['level'] > 0) {
			$equipConfig = M_Config::getVal();
			$attrAddRate = $equipConfig['strong_equip_attr_add_rate'];
			if (!empty($info['suit_id'])) {
				$attrAddRate = $equipConfig['strong_suit_equip_attr_add_rate'];
			}
			$addNum = $attrAddRate[$info['need_level']][$info['quality']];
			$addNum = $addNum * $info['level'];
		}
		//基础属性
		$attr = M_Formula::equipMakeAttrPoint($info['need_level'], $info['quality']);
		$rand = rand(-2, 3);
		$attr = $attr + $rand;
		$attrColumns = T_Equip::$posBaseAttr[$info['pos']]; //最终属性

		if (count($attrColumns) == 1) {
			$info[$attrColumns[0]] = $attr + $addNum;
		} elseif (count($attrColumns) == 2) {
			$attr1 = rand(0, $attr);
			$attr2 = $attr - $attr1;
			$info[$attrColumns[0]] = $attr1 + $addNum / 2;
			$info[$attrColumns[1]] = $attr2 + $addNum / 2;
		}

		$info['create_at'] = time();
		$ret = M_Equip::setInfo($equipId1, $info);
		if (!$ret) {
			return B_Common::result(T_ErrNo::ERR_DB_EXECUTE);
		}

		$objPlayer = new O_Player($cityId);
		$objPlayer->Quest()->check('equip_mix', array('qual' => $info1['quality'], 'lv' => $info1['need_level'], 'num' => 1));
		$objPlayer->save();

		M_QqShare::check($objPlayer, 'equip_mix', array('qual' => 0, 'level' => $info1['need_level']));
		M_Equip::delCityEquip($equipId2, $cityId);
		M_Equip::delCityEquip($equipId3, $cityId);
		$ret['_0'] = M_Sync::SET;
		$sysArr = array(
			$equipId1 => $ret,
		);
		M_Sync::addQueue($cityId, M_Sync::KEY_EQUIP, $sysArr);
		$data = $equipId1;
		if ($info['quality'] > 3) {
			$dataStr = 'Qual:' . $info2['quality'];
			Logger::opEquip($cityId, $equipId2, Logger::E_ACT_FSDIS, $dataStr);
			$dataStr = 'Qual:' . $info3['quality'];
			Logger::opEquip($cityId, $equipId3, Logger::E_ACT_FSDIS, $dataStr);
			$dataStr = 'Qual:' . $info['quality'];
			Logger::opEquip($equipId1, Logger::E_ACT_FSRETAIN, $dataStr);
		}

		return B_Common::result('', $data);
	}


	/**
	 * @see CEquip::AList
	 */
	public function AGetCityEquipList() {

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$cityId = intval($cityInfo['id']);
		$list = M_Equip::getCityEquipList($cityId);

		if (empty($list)) {
			return B_Common::result('');
		}

		foreach ($list as $equipId) {
			$val = M_Equip::getInfo($equipId);
			if (!empty($val['id']) && $val['city_id'] == $cityId) {
				$heroname = $heroquality = '';
				$pos = $val['pos'];
				if (!empty($val['is_use'])) {
					//如果已装备、获取装备军官名称
					$heroInfo = M_Hero::getHeroInfo($val['is_use']);
					if ($heroInfo[T_Equip::$equipPosWithHeroColumn[$pos]] == $val['id']) {
						$heroname = $heroInfo['nickname'];
						$heroquality = $heroInfo['quality'];
					} else { //校验数据
						$fieldArr['is_use'] = 0;
						$heroPosId = $heroInfo[T_Equip::$equipPosWithHeroColumn[$pos]];
						Logger::error(array(__METHOD__, "set is_user=0;Pos#{$pos};CityId#{$val['city_id']};EquipId#{$val['id']};HeroId#{$val['is_use']};HeroEquipId#{$heroPosId}"));
						M_Equip::setInfo($val['id'], $fieldArr);
					}
				}

				$data[] = array(
					'Id' => $val['id'],
					'Name' => $val['name'],
					'Pos' => $val['pos'],
					'FaceId' => $val['face_id'],
					'NeedLevel' => $val['need_level'],
					'Level' => $val['level'],
					'MaxLevel' => $val['max_level'],
					'Quality' => $val['quality'],
					'BaseLead' => $val['base_lead'],
					'BaseCommand' => $val['base_command'],
					'BaseMilitary' => $val['base_military'],
					'IsLocked' => isset($val['is_locked']) ? $val['is_locked'] : 0,
					'ExtAttrName' => $val['ext_attr_name'],
					'ExtAttrRate' => $val['ext_attr_rate'],
					'ExtAttrSkill' => $val['ext_attr_skill'],
					'IsUse' => $val['is_use'],
					'OnSale' => $val['on_sale'],
					'SuitId' => $val['suit_id'],
					'Desc1' => $val['desc_1'],
					'Desc2' => $val['desc_2'],
					'CreateAt' => $val['create_at'],
					'Flag' => isset($val['flag']) ? $val['flag'] : 7,
					'HeroName' => $heroname,
					'HeroQuality' => $heroquality,
					'SysPrice' => M_Formula::equipSellGold($val['quality'], $val['need_level'], $val['level']),
				);
			} else { //校正数据
				Logger::error(array(__METHOD__, "del city equip list#{$equipId}"));
				$ret = M_Equip::delCityEquipList($cityId, $equipId);
			}
		}


		return B_Common::result('', $data);
	}

	/**
	 * 出售装备
	 * @param int $cityEquipId 城市装备ID
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ADelCityEquip($equipId) {
		$equipId = intval($equipId);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$equipInfo = M_Equip::getInfo($equipId);
		if (empty($equipInfo['id'])) {
			return B_Common::result(T_ErrNo::EQUIP_NO_EXIST);
		} else if ($equipInfo['city_id'] == $cityInfo['id']) {
			return B_Common::result(T_ErrNo::EQUIP_NO_EXIST);
		} else if ($equipInfo['is_use'] == T_Equip::EQUIP_IS_USE) {
			return B_Common::result(T_ErrNo::HERO_EQUIP_AGIN);
		} else if ($equipInfo['on_sale'] == M_Auction::GOODS_ON_SALE_YES) {
			return B_Common::result(T_ErrNo::EQUIP_ON_SALE);
		} else if (!M_Equip::delCityEquip($equipId, $cityInfo['id'])) {
			return B_Common::result(T_ErrNo::EQUIP_DEL_FAIL);
		}

		Logger::opEquip($cityInfo['id'], $equipId, Logger::E_ACT_SELLGOLD, $equipInfo['name']);
		//出售资源返还
		$sellGold = M_Formula::equipSellGold($equipInfo['quality'], $equipInfo['need_level'], $equipInfo['level']);

		$objPlayer->Res()->incr('gold', $sellGold);
		$objPlayer->save();

		return B_Common::result('');
	}

	/**
	 * 强化装备
	 *
	 * @param int $cityEquipId 装备ID
	 * @param int $stoneId 强化石道具ID
	 * @param int $luckPropsId 成功率道具ID
	 * @param int $luck 使用幸运池的幸运点
	 */
	public function AStrongEquip($equipId, $stoneId = 0, $luckPropsId = 0, $luck = 0) {
		$errNo = T_ErrNo::ERR_ACTION;
		$isSuccess = 0;
		$equipId = intval($equipId);
		$stoneId = intval($stoneId);
		$luckPropsId = intval($luckPropsId);
		$luck = intval($luck);
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$cityLuck = $cityInfo['equip_strong_luck_pool'];
		//获得当前装备信息
		$equipInfo = M_Equip::getInfo($equipId);
		if (empty($equipInfo['id'])) {
			return B_Common::result(T_ErrNo::EQUIP_NO_EXIST);
		} else if (empty($stoneId)) {
			return B_Common::result(T_ErrNo::ERR_PARAM);
		} else if ($equipInfo['city_id'] == $cityInfo['id']) {
			return B_Common::result(T_ErrNo::EQUIP_NO_EXIST);
		} else if (($equipInfo['flag'] & M_Equip::FLAG_STRENGTHEN) == 0) {
			return B_Common::result(T_ErrNo::SUIT_STRENGTHEN_NO);
		}

		$cityId = $cityInfo['id'];

		//获取城市幸运池

		if ($luck > 0) {
			$cityLuck -= $luck;
		}

		//读取装备配置
		$equipConfig = M_Config::getVal();
		//最大强化等级
		$maxLevel = $equipConfig['strong_equip_max_level'];
		$equipLevel = $equipInfo['level'];
		$strongLevel = $equipLevel + 1;

		$needGold = M_Formula::calcStrongEquipCostGold($strongLevel, $equipInfo['quality']);

		//获取强化石的最大强化等级   (低级2*强化上限/5=20)(中级4*强化上限/5)(高级5*强化上限/5=)
		$maxStrongLevel = M_Props::getMaxStrongGrade($stoneId);

		if ($equipInfo['level'] >= $maxLevel) { //判断等级是否达到顶级
			return B_Common::result(T_ErrNo::EQUIP_ISMAX_LEVEL);
		} else if ($cityLuck < 0) { //幸运池当前幸运点不够
			return B_Common::result(T_ErrNo::LUCK_NOT_ENOUGH);
		} else if ($objPlayer->Res()->incr('gold', -$needGold) < 0) { //判断当前资源是否足够强化 当前城市黄金
			return B_Common::result(T_ErrNo::NO_ENOUGH_GOLD);
		} else if ($maxStrongLevel['min'] > $equipInfo['need_level'] || $maxStrongLevel['max'] < $equipInfo['need_level']) { //判断强化石等级是否够
			return B_Common::result(T_ErrNo::STRONG_STONE_FALL);
		} else if (!$objPlayer->Pack()->decrNumByPropId($stoneId, 1)) { //判断有没有强化石道具
			return B_Common::result(T_ErrNo::PROPS_NOT_ENOUGH);
		} else if ($luckPropsId > 0 && !$objPlayer->Pack()->decrNumByPropId($luckPropsId, 1)) { //判断有没有幸运符道具
			return B_Common::result(T_ErrNo::PROPS_NOT_ENOUGH);
		} else if ($equipInfo['on_sale'] == M_Auction::GOODS_ON_SALE_YES) {
			return B_Common::result(T_ErrNo::EQUIP_ON_SALE);
		}

		//获取幸运符成功率加成
		$propSuccRate = intval(M_Props::getLuckyRate($luckPropsId));
		//装备本身成功率
		$equipSuccRate = M_Equip::getEquipStrongSuccRate($equipLevel);
		//总成功率
		$sumSuccRate = intval($propSuccRate + $equipSuccRate + $luck);
		//依几率计算成功或失败
		$isSuccess = B_Utils::odds($sumSuccRate);

		if ($isSuccess == false) {
			//获取失败应该返回给幸运池的幸运点
			if ($luckPropsId > 0) {
				$backLuck = M_Props::getLuckyPool($luckPropsId);
				$cityLuck = $cityLuck + $backLuck;
				$cityLuck = min($cityLuck, 100);
			}
		}

		$objPlayer->City()->equip_strong_luck_pool = $cityLuck;


		if ($isSuccess) {
			$posNo = $equipInfo['pos']; //装备位置编号
			//加成属性
			$attrArr = T_Equip::$posBaseAttr[$posNo];

			//加成点数
			$equipConfig = M_Config::getVal();
			$attrAddRate = $equipConfig['strong_equip_attr_add_rate'];
			$addNum = $attrAddRate[$equipInfo['need_level']][$equipInfo['quality']];

			if (count($attrArr) == 2) {
				$addNum = $addNum / 2;
			}

			$upArr['level'] = $strongLevel;
			if (isset($attrArr[0])) {
				$upArr[$attrArr[0]] = $equipInfo[$attrArr[0]] + $addNum;
			}
			if (isset($attrArr[1])) {
				$upArr[$attrArr[1]] = $equipInfo[$attrArr[1]] + $addNum;
			}
			$ret = M_Equip::setInfo($equipId, $upArr);

			if (!$ret) { //加点失败
				return B_Common::result(T_ErrNo::EQUIP_ADD_ATTR_FALL);
			}

			//同步装备数据
			$upArr['_0'] = M_Sync::SET;
			$syncData = array($equipId => $upArr);
			M_Sync::addQueue($cityId, M_Sync::KEY_EQUIP, $syncData);
		}


		$objPlayer->Quest()->check('equip_strong', array('qual' => $equipInfo['quality'], 'lv' => $equipInfo['need_level'], 'num' => 1));
		$objPlayer->save();

		$Lv = $isSuccess ? ($equipInfo['level'] + 1) : $equipInfo['level'];
		M_QqShare::check($objPlayer, 'equip_strong', array('level' => $Lv));

		$errNo = '';
		$data = array('isSuccess' => $isSuccess ? 1 : 0);
		if ($equipInfo['quality'] > 3) {
			$newLv = $isSuccess ? ($equipInfo['level'] + 1) : $equipInfo['level'];
			$dataStr = "Lv:{$newLv}";
			Logger::opEquip($cityId, $equipId, Logger::E_ACT_STRENGTHEN, $dataStr);
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 升级装备
	 * @param int $equipId 城市装备ID
	 * @param int $type 升级类型[0,1,2]
	 */
	public function AUpEquipLevel($equipId, $type = 0) {
		$errNo = T_ErrNo::ERR_ACTION;
		$equipId = intval($equipId);
		$type = intval($type);
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$cityId = $cityInfo['id'];
		$info = M_Equip::getInfo($equipId);

		if (empty($info['id']) || $info['city_id'] != $cityId) {
			return B_Common::result(T_ErrNo::EQUIP_NO_EXIST);
		}

		if ($info['flag'] & M_Equip::FLAG_UPGRADE == 0) {
			return B_Common::result(T_ErrNo::SUIT_UPGRADE_NO);
		}

		if ($info['quality'] <= T_Equip::EQUIP_BLUE || $info['need_level'] >= 90) {
			return B_Common::result(T_ErrNo::EQUIP_CANNOT_UPLEVEL);
		}

		if ($info['on_sale'] == M_Auction::GOODS_ON_SALE_YES) {
			return B_Common::result(T_ErrNo::EQUIP_ON_SALE);
		}


		$lv = $info['need_level'];
		$strengLv = $info['level'];

		$bUp = false;
		if ($type > 0) {
			if ($cityInfo['vip_level'] < 5) { //VIP等级不足
				$errNo = T_ErrNo::VIP_NOT_LEVEL;
			} else {
				$costMilpay = M_Formula::calcUpgradeEquipCostMilpay($lv, $strengLv, $info['quality'], $type);

				if ($cityInfo['mil_pay'] >= $costMilpay) {
					$log = json_encode(array('need_level' => $lv, 'level' => $strengLv, 'quality' => $info['quality'], 'up_type' => $type));
					$bUp = $objPlayer->City()->decrCurrency(T_App::MILPAY, $costMilpay, B_Log_Trade::E_UpEquipNeedLevel, $log);
					if (!$bUp) { //扣军饷
						$errNo = T_ErrNo::ERR_PAY;
					}
				} else {
					//军饷不足
					$errNo = T_ErrNo::NO_ENOUGH_MILIPAY;
				}
			}
		} else {
			$costGold = M_Formula::equipUpgradeCostGold($lv, $strengLv);

			$leftGold = $objPlayer->Res()->incr('gold', -$costGold);

			if ($leftGold < 0) {
				//金钱不足
				$errNo = T_ErrNo::NO_ENOUGH_GOLD;
			}
		}

		if ($bUp) {
			if ($info['is_use'] > 0) { //自动卸下装备
				$posField = T_Equip::$posFieldArr[$info['pos']];
				$upArr = array($info['is_use'] => array($posField => 0));
				$bRemove = M_Hero::setHeroInfo($info['is_use'], $upArr);
				if ($bRemove) {
					M_Sync::addQueue($cityId, M_Sync::KEY_HERO, $upArr); //同步英雄装备数据!

					$fieldArr = array(
						'city_id' => $cityId,
						'is_use' => 0,
					);
					$bUp = M_Equip::setInfo($equipId, $fieldArr);
					$bUp && M_Equip::syncRemoveEquip($cityId, $equipId);
				}
			}
			$isSucc = B_Utils::odds(T_Equip::$upEquipSuccRate[$type]); //成功率

			$objPlayer->Quest()->check('equip_up', array('qual' => $info['quality'], 'lv' => $info['need_level'], 'num' => 1));
			$objPlayer->save();

			if ($isSucc) {
				//装备位置编号
				$posNo = $info['pos'];
				$newLv = intval(($info['need_level'] + 10) / 10) * 10; //穿戴等级+10
				$isDemotionQuality = B_Utils::odds(T_Equip::$upEquipQuality[$type]); //装备品质不降级的几率
				$isDemotionLevel = B_Utils::odds(T_Equip::$upEquipLevel[$type]); //装备强化等级不降级的几率
				$newQuality = $info['quality']; //不降品质
				$newStrengLv = $strengLv; //不降强化等级
				if (!$isDemotionQuality) {
					$newQuality = max(1, $info['quality'] - rand(1, 2)); //品质随机降1-2级
				}

				if (!$isDemotionLevel) {
					$newStrengLv = max(0, $strengLv - rand(15, 30)); //强化等级随机降10-20级
				}

				$equipSuitFace = !empty($info['suit_id']) ? '_' . $info['suit_id'] : '';
				$setArr = array(
					'face_id' => $posNo . '_' . $newQuality . '_' . $newLv . $equipSuitFace,
					'need_level' => $newLv,
					'quality' => $newQuality,
					'level' => $newStrengLv,
				);
				$setArr['name'] = M_Equip::getEquipName($setArr['face_id']);

				$attrAddRate = M_Config::getVal('strong_equip_attr_add_rate');
				if (!empty($info['suit_id'])) {
					$attrAddRate = M_Config::getVal('strong_suit_equip_attr_add_rate');
				}

				$newAddNum = $attrAddRate[$newLv][$newQuality] * $newStrengLv; //新强化属性值
				$attrArr = T_Equip::$posBaseAttr[$posNo]; //加成属性

				//新装备基础属性值
				$attr = M_Formula::equipMakeAttrPoint($newLv, $newQuality);
				$attr = $attr + rand(-2, 3);
				//属性赋值
				if (count($attrArr) == 1) {
					$setArr[$attrArr[0]] = $attr + $newAddNum;
				} else if (count($attrArr) == 2) {
					$attr1 = rand(0, $attr);
					$attr2 = $attr - $attr1;
					$newAddNum = $newAddNum / 2;
					$setArr[$attrArr[0]] = $attr1 + $newAddNum;
					$setArr[$attrArr[1]] = $attr2 + $newAddNum;
				}

				$ret = M_Equip::setInfo($equipId, $setArr);
				if ($ret) {
					$ret['_0'] = M_Sync::SET;
					$sysArr = array(
						$equipId => $ret,
					);
					M_Sync::addQueue($cityId, M_Sync::KEY_EQUIP, $sysArr);

					$data = array(
						'IsSucc' => 1
					);
					$errNo = '';
					$objPlayer->save();

				}

				$dataStr = "NeedLv:{$newLv}_Qual:{$newQuality}_Lv:{$newStrengLv}";
				Logger::opEquip($cityId, $equipId, Logger::E_ACT_UPLEVEL, $dataStr);
			} else {
				//升级失败(type=0时有50%几率升级失败,升级失败装备不变扣除金钱)
				$data = array(
					'IsSucc' => 0
				);
				$errNo = '';
			}
		}


		return B_Common::result($errNo, $data);
	}

}

?>
<?php

class C_Mall extends C_I {
	/**
	 * 在商城购买一定数量的某物品
	 * @author duhuihui on 20120907
	 * @param int $mallId 物品ID
	 * @param int $num 购买数量
	 * @param int $payType 支付类型(1军饷、2点券、3金钱, 4积分)
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AMallBuy($mallId, $num, $payType = T_App::MILPAY) //如果商品数量为-1，则没有限制
	{
		//默认失败
		$errNo = T_ErrNo::ERR_PARAM; //默认失败编号:参数错误
		$mallId = intval($mallId);
		$num = intval($num);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if ($mallId > 0 && $num > 0 && isset(M_Mall::$payType[$payType])
		) {
			$basemallinfo = M_Mall::getBaseInfoById($mallId); //商城基础数据
			$arr_price = json_decode($basemallinfo['price'], true); //商城物品价格数组
			if (isset($basemallinfo['price']) &&
				isset($arr_price[$payType])
			) {
				$total = $arr_price[$payType] * $num; //总价格

				$cityBreakout = M_BreakOut::getCityBreakOut($cityInfo['id']);
				$point = 0;
				$err = '';
				$hasNum = M_Mall::getNum($mallId);
				if ($payType == M_Mall::PAY_MILPAY && $cityInfo['mil_pay'] < $total) {
					$err = T_ErrNo::NO_ENOUGH_MILIPAY;
				} else if ($payType == M_Mall::PAY_COUPON && $cityInfo['coupon'] < $total) {
					$err = T_ErrNo::NO_ENOUGH_COUPON;
				} else if ($payType == M_Mall::PAY_GOLD && $objPlayer->Res()->incr('gold', -$total) < 0) {
					$err = T_ErrNo::NO_ENOUGH_GOLD;
				} else if ($payType == M_Mall::PAY_POINT &&
					$cityBreakout['point'] < $total
				) {
					$err = T_ErrNo::NO_ENOUGH_POINT;
				} else if ($basemallinfo['item_type'] == M_Mall::ITEM_HERO &&
					M_Hero::isHeroNumFull($cityInfo['id'])
				) {
					$err = T_ErrNo::HERO_NUM_FULL_FAIL; //军官数量已满
				} else if ($basemallinfo['num'] != -1 &&
					$hasNum < $num
				) {
					$err = T_ErrNo::MALL_ITEM_NO; //已售完
				} else if ($basemallinfo['item_type'] == M_Mall::ITEM_EQUIP &&
					M_Equip::isEquipNumFull($cityInfo['id'], $cityInfo['vip_level'])
				) {
					$err = T_ErrNo::EQUI_NUM_FULL;
				} else if ($basemallinfo['item_type'] == M_Mall::ITEM_PROPS) {
					$propsInfo = M_Props::baseInfo($basemallinfo['item_id']);

					$propsNumArr = $objPlayer->Pack()->hasNum();

					if ($propsInfo['type'] == M_Props::TYPE_DRAW &&
						$propsNumArr['draw']['full']
					) {
						$err = T_ErrNo::DRAW_NUM_FULL;
					} else if (in_array($propsInfo['type'], array(M_Props::TYPE_INNER, M_Props::TYPE_HERO, M_Props::TYPE_TREA, M_Props::TYPE_WAR)) &&
						$propsNumArr['normal']['full']
					) {
						$err = T_ErrNo::PROPS_NUM_FULL;
					} else if ($propsInfo['type'] == M_Props::TYPE_STUFF &&
						$propsNumArr['stuff']['full']
					) {
						$err = T_ErrNo::MATERIAL_NUM_FULL;
					}

				}

				$arrAward = array();
				if (empty($err)) {
					if ($payType == M_Mall::PAY_GOLD) {
						$bCost = true;
					} else if ($payType == M_Mall::PAY_POINT) {
						$newPoint = max(0, $cityBreakout['point'] - $total);
						$bCost = M_BreakOut::updateCityBreakOut($cityInfo['id'], array('point' => $newPoint));

						$bCost && M_Sync::addQueue($cityInfo['id'], M_Sync::KEY_CITY_INFO, array('BreakoutPoint' => $newPoint));

					} else {
						$bCost = $objPlayer->City()->decrCurrency($payType, $total, B_Log_Trade::E_BuyMall, $mallId . ':' . $num);
					}

					//减掉商城商品的数量
					if ($basemallinfo['num'] != -1) //为-1的话无限制
					{
						M_Mall::decrNum($mallId, $num);
					}

					$bIncr = false;
					if ($basemallinfo['item_type'] == M_Mall::ITEM_PROPS && $bCost) {
						$bIncr = $objPlayer->Pack()->incr($basemallinfo['item_id'], $num);
					} else if ($basemallinfo['item_type'] == M_Mall::ITEM_HERO && $bCost) { //增加城市军官数量
						$bIncr = M_Hero::moveTplHeroToCityHero($objPlayer, $basemallinfo['item_id'], Logger::H_MALL_BUY);
					} else if ($basemallinfo['item_type'] == M_Mall::ITEM_EQUIP && $bCost) //增加城市装备数量
					{
						$tplInfo = M_Equip::baseInfo($basemallinfo['item_id']);
						$equipIds = array();
						for ($i = 0; $i < $num; $i++) {
							$equipIds[] = M_Equip::makeEquip($cityInfo['id'], $tplInfo);
						}
						$total = count($equipIds);
						$bIncr = $num;
						if ($total != $num) {
							Logger::error(array(__METHOD__, 'err equip num', $num, $total));
						}
					}

					if ($bIncr) {
						$objPlayer->Quest()->check('props_buy', array('id' => $basemallinfo['item_id'], 'num' => $num));
						$objPlayer->save();

						M_QqShare::check($objPlayer,  'props_buy', array());

						$errNo = '';
					}
				} else {
					$errNo = $err;
				}
			}
		}

		$data = '';
		return B_Common::result($errNo, $data);
	}

	/**
	 * 商城物品信息
	 * @author duhuihui on 20120907
	 * @param
	 * @param
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AMallList() {
		$data = M_Mall::getList();
		$errNo = '';
		return B_Common::result($errNo, $data);
	}
}

?>
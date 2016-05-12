<?php

class C_MallExchange extends C_I {
	/**
	 * 在商城购买一定数量的某物品
	 * @author duhuihui on 20120907
	 * @param int $mallId 物品ID
	 * @param int $num 购买数量
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AMallExchangeBuy($mallId, $num) //如果商品数量为-1，则没有限制
	{
		$mallId = intval($mallId);
		$num = intval($num);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (empty($mallId) || empty($num)) {
			return B_Common::result(T_ErrNo::ERR_PARAM);
		}

		$basePropsList = M_Config::getVal('activeness_item');
		list($itemType, $itemId, $needNum) = $basePropsList[$mallId]; //商城基础数据

		$total = $needNum * $num; //总价格

		if ($objPlayer->Liveness()->incr(-$total) < 0) {
			return B_Common::result(T_ErrNo::NO_ENOUGH_ACTIVENESS);
		} else if ($itemType == M_Mall::ITEM_EQUIP && M_Equip::isEquipNumFull($cityInfo['id'], $cityInfo['vip_level'])) {
			return B_Common::result(T_ErrNo::EQUI_NUM_FULL);
		} else if ($itemType == M_Mall::ITEM_PROPS) {
			$propsInfo = M_Props::baseInfo($itemId);
			$propsNumArr = $objPlayer->Pack()->hasNum();
			if ($propsInfo['type'] == M_Props::TYPE_DRAW && $propsNumArr['draw']['full']) {
				return B_Common::result(T_ErrNo::DRAW_NUM_FULL);
			} else if (in_array($propsInfo['type'], array(M_Props::TYPE_INNER, M_Props::TYPE_HERO, M_Props::TYPE_TREA, M_Props::TYPE_WAR)) && $propsNumArr['normal']['full']) {
				return B_Common::result(T_ErrNo::PROPS_NUM_FULL);
			} else if ($propsInfo['type'] == M_Props::TYPE_STUFF && $propsNumArr['stuff']['full']) {
				return B_Common::result(T_ErrNo::MATERIAL_NUM_FULL);
			}
		}

		if ($itemType == M_Mall::ITEM_PROPS) {
			$objPlayer->Pack()->incr($itemId, $num);
		} else if ($itemType == M_Mall::ITEM_EQUIP) { //增加城市装备数量
			$tplInfo = M_Equip::baseInfo($itemId);
			$equipIds = array();
			for ($i = 0; $i < $num; $i++) {
				$equipIds[] = M_Equip::makeEquip($cityInfo['id'], $tplInfo);
			}
			$total = count($equipIds);
			if ($total != $num) {
				Logger::error(array(__METHOD__, 'err equip num', $num, $total));
			}
		}

		return B_Common::result('');
	}

	/**
	 * 商城物品信息
	 * @param int $cate
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AMallExchangeList($cate) {
		$data = array();
		$errNo = '';
		if ($cate == 3) {
			$objPlayer = $this->objPlayer;

			list($livenessPoint, $date, $livenessList) = $objPlayer->Liveness()->get();

			$data['activenessSum'] = $livenessPoint;
			$data['baseActiveness'] = M_Config::getVal('activeness_list');
			$basePropsList = M_Config::getVal('activeness_item');
			$data['cityActiveness'] = $livenessList;

			if (!empty($basePropsList)) {
				$arrAward = array();
				foreach ($basePropsList as $id => $val) {
					list($type, $itemId, $num) = $val;
					if ($type == M_Mall::ITEM_PROPS) {
						$itemData = array();
					} else if ($type == M_Mall::ITEM_EQUIP) {
						$equiTplInfo = M_Equip::baseInfo($itemId);
						if (!empty($equiTplInfo['name'])) {
							$itemData = array(
								'Name' => $equiTplInfo['name'],
								'Quality' => $equiTplInfo['quality'],
								'Pos' => $equiTplInfo['pos'],
								'FaceId' => $equiTplInfo['face_id'],
								'BaseCommand' => $equiTplInfo['base_command'],
								'BaseMilitary' => $equiTplInfo['base_military'],
								'BaseLead' => $equiTplInfo['base_lead'],
								'Level' => $equiTplInfo['level'],
								'NeedLevel' => $equiTplInfo['need_level'],
								'ExtAttrName' => $equiTplInfo['ext_attr_name'],
								'ExtAttrRate' => $equiTplInfo['ext_attr_rate'],
								'SuitId' => $equiTplInfo['suit_id'],
								'Flag' => $equiTplInfo['flag'],
							);

						} else {
							Logger::error(array(__METHOD__ . ':' . __LINE__, $itemId, $equiTplInfo));
						}
					}

					$now = time();
					$arrAward[] = array(
						'MallId' => $id,
						'Nab' => 9,
						'ItemType' => $type,
						'Price' => array(M_Mall::PAY_ACTIVENESS => $num),
						'IsHot' => 0,
						'itemId' => $itemId,
						'Num' => -1,
						'TtemData' => $itemData,
						'Sort' => $id,
						'UpTime' => $now - 86400,
						'DownTime' => $now + 86400,
					);

				}
				$data['arrAward'] = $arrAward;
			}
		}

		return B_Common::result($errNo, $data);
	}
}

?>
<?php

/**
 * 物品模块
 */
class M_Item {

	/**
	 * 整理城市物品列表
	 * @author duhuihui
	 * @param int $cityId
	 * @param int $type
	 * @return $errNo
	 */
	static public function ArrangeBackpack($cityId, $type) {
		$errNo     = T_ErrNo::ERR_PARAM; //默认失败编号:参数错误
		$flag      = T_App::FAIL; //默认失败
		$itemList  = array();
		$itemIdArr = array();
		$propsList = array();
		$arr       = array();
		$itemIds   = array();
		$syncRow   = array();
		$fields    = array();
		$fields1   = array();
		$objplayer = new O_Player($cityId);
		$itemList  = $objplayer->Pack()->get();
		if ($type == 1) {
			$arr = array(M_Props::TYPE_INNER => 1, M_Props::TYPE_HERO => 1, M_Props::TYPE_TREA => 1, M_Props::TYPE_WAR => 1);
		} else if ($type == 2) {
			$arr = array(M_Props::TYPE_DRAW => 1);
		} else if ($type == 3) {
			$arr = array(M_Props::TYPE_STUFF => 1);
		}
		if (!empty($arr)) {
			if (!empty($itemList)) {

				foreach ($itemList as $key => $itemInfo) {
					if (isset($arr[$itemInfo['type']])) {
						$propsInfo = M_Props::baseInfo($itemInfo['props_id']);
						$stackNum  = $propsInfo['stack_num'];
						if ($itemInfo['num'] != $stackNum) {
							$itemIdArr[] = array(
								'id'       => $key,
								'props_id' => $itemInfo['props_id'],
								'num'      => $itemInfo['num']
							);
						}
					}
				}

				if (!empty($itemIdArr)) {
					foreach ($itemIdArr as $itemId) {
						$propsList[$itemId['props_id']]['num']  = isset($propsList[$itemId['props_id']]['num']) ? $propsList[$itemId['props_id']]['num'] : 0;
						$propsList[$itemId['props_id']]['id'][] = $itemId['id'];
						$propsList[$itemId['props_id']]['num'] += $itemId['num'];

					}
					foreach ($propsList as $propsId => $items) {
						$propsInfo = M_Props::baseInfo($propsId);
						$stackNum  = $propsInfo['stack_num'];
						$count     = count($items['id']);
						if (($count > 1 && !empty($stackNum)) || (!empty($stackNum) && !empty($items['num']) && $items['num'] > $stackNum)) {
							$k = (int)($items['num'] / $stackNum); //前k个都是为0
							$y = $items['num'] % $stackNum;
							if ($k + 1 <= $count) {
								for ($i = 0; $i <= $k; $i++) //更新数据表
								{
									$itemInfo                   = $itemList[$items['id'][$i]];
									$itemList[$items['id'][$i]] = array(
										'city_id'   => $cityId,
										'props_id'  => $itemInfo['props_id'],
										'type'      => $itemInfo['type'],
										'locked'    => $itemInfo['locked'],
										'create_at' => $itemInfo['create_at'],
									);
									if ($i == $k) { //num=$y
										$itemList[$items['id'][$i]]['num'] = $y;
										$syncRow[$items['id'][$i]]         = array($propsId, $y, $itemInfo['locked']);
										$fields                            = array('id' => $items['id'][$i], 'num' => $y);

									} else if ($i < $k) {
										$itemList[$items['id'][$i]]['num'] = $stackNum;
										$syncRow[$items['id'][$i]]         = array($propsId, $stackNum, $itemInfo['locked']);
										$fields                            = array('id' => $items['id'][$i], 'num' => $stackNum);
									}
									if (empty($itemList[$items['id'][$i]]['num'])) {
										unset($itemList[$items['id'][$i]]);
										$syncRow[$items['id'][$i]] = M_Sync::DEL;
										$itemIds[]                 = $items['id'][$i];
									}
								}
								for ($j = $k + 1; $j < $count; $j++) //删除数据表
								{
									unset($itemList[$items['id'][$j]]);
									$syncRow[$items['id'][$j]] = M_Sync::DEL; // 同步
									$itemIds[]                 = $items['id'][$j]; //要删除的id
								}
								if (!empty($itemIds)) {
									M_Item::delItems($cityId, $itemIds);
								}
							} else {
								for ($i = 0; $i < $count; $i++) //更新数据表
								{
									$itemInfo                          = $itemList[$items['id'][$i]];
									$itemList[$items['id'][$i]]        = array(
										'city_id'   => $cityId,
										'num'       => $stackNum,
										'props_id'  => $itemInfo['props_id'],
										'type'      => $itemInfo['type'],
										'locked'    => $itemInfo['locked'],
										'create_at' => $itemInfo['create_at'],
									);
									$itemList[$items['id'][$i]]['num'] = $stackNum;
									$syncRow[$items['id'][$i]]         = array($propsId, $stackNum, $itemInfo['locked']);
									$fields                            = array('id' => $items['id'][$i], 'num' => $stackNum);
								}
								for ($j = $count; $j < $k + 1; $j++) //删除数据表
								{
									$fields1 = array(
										'city_id'   => $cityId,
										'props_id'  => $propsId,
										'type'      => $itemInfo['type'],
										'locked'    => $itemInfo['locked'],
										'create_at' => $itemInfo['create_at'],
									);
									if ($j == $k) {
										if (!empty($y)) {
											$fields1['num'] = $y;

											$itemId = B_DB::instance('CityItem')->insert($fields1);
											if ($itemId) {
												$syncRow[$itemId]  = array(intval($propsId), $y, $itemInfo['locked']);
												$itemList[$itemId] = $fields1;
											}
										}
									} else {
										$fields1['num'] = $stackNum;
										$itemId         = B_DB::instance('CityItem')->insert($fields1);
										if ($itemId) {
											$syncRow[$itemId]  = array(intval($propsId), $stackNum, $itemInfo['locked']);
											$itemList[$itemId] = $fields1;
										}

									}
								}

							}
							$ret = M_Item::setItemList($cityId, $itemList);
							B_DB::instance('CityItem')->update($fields, $fields['id']);
							if (!empty($syncRow)) {
								M_Sync::addQueue($cityId, M_Sync::KEY_ITEM_PROPS, $syncRow);
							}
							$flag  = T_App::SUCC;
							$errNo = '';
						} else {
							$errNo = T_ErrNo::DRAW_NOT_ARRANGE;
						}
					}
				} else {
					$errNo = T_ErrNo::DRAW_IS_EMPTY;
				}
			}
		}
		return array($errNo, $flag);
	}
}

?>
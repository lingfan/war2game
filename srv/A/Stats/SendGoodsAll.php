<?php

/**
 * 周活跃和月活跃用户统计
 */
class A_Stats_SendGoodsAll {
	static public function SendGoodsAll($params = array()) {
		$ret = false;
		$ret1 = false;
		if (!empty($params['id'])) {
			$ret = B_DB::instance('ServerCompensate')->update($params, $params['id']);
		} else {
			$ret = B_DB::instance('ServerCompensate')->insert($params);
		}
		$rc = new B_Cache_RC(T_Key::SERVER_COMPENSATE);
		$ret1 = $rc->delete();
		return $ret && $ret1;
	}

	static public function GetSendGoodsAll($params = array()) {
		$listArr = array();
		$textArr = array(
			'gold' => '金钱',
			'food' => '食物',
			'oil' => '石油',
			'milpay' => '军饷',
			'coupon' => '礼券',
			'total_mil_pay' => '累计充值量',
			'props' => '道具',
			'equip' => '装备',
		);
		$ConditionArr = array(
			'renown' => '威望',
			'vip' => 'VIP等级',
			'level' => '城市等级',
			'warexp' => '功勋',
			'record' => '战绩值',
		);
		$listArr = M_Compensate::getBaseAwardList();
		if (!empty($listArr)) {
			foreach ($listArr as $key => $list) {
				$str = '';
				$str1 = '';
				$awardText = json_decode($list['award_text'], true);
				$awardCondition = json_decode($list['award_condition'], true);
				foreach ($awardText as $k => $val) {
					if ($k == 'gold' || $k == 'food' || $k == 'oil' || $k == 'march_num' || $k == 'milpay' || $k == 'coupon' || $k == 'total_mil_pay') {
						$str .= $textArr[$k] . 'x' . $val . ' ';
					} elseif ($k == 'equip') {
						foreach ($val as $equipID => $equipValue) {
							$equiTplInfo = M_Equip::baseInfo($equipID);
							$str .= $textArr[$k] . ':' . $equiTplInfo['name'] . 'x' . $equipValue . ' ';
						}

					} elseif ($k == 'props') {
						foreach ($val as $propsID => $propsValue) {
							$propsInfo = M_Props::baseInfo($propsID);
							$str .= $textArr[$k] . ':' . $propsInfo['name'] . 'x' . $propsValue;
						}
					}
				}
				foreach ($awardCondition as $k => $val) {
					$str1 .= $ConditionArr[$k] . '：' . $val . ' ';
				}
				$listArr[$key]['award_text'] = $str;
				$listArr[$key]['award_condition'] = $str1;
			}
		}

		return $listArr;
	}

	static public function GetSendGoodsUpdateAll($params = array()) {
		$listArr = array();

		$listArr = M_Compensate::getBaseAwardList();

		return $listArr;
	}

	static public function DeleteSendGoodsAll($params = array()) {
		$ret = false;
		$ret1 = false;
		$ret = B_DB::instance('ServerCompensate')->delete($params['id']);
		$rc = new B_Cache_RC(T_Key::SERVER_COMPENSATE);
		$ret1 = $rc->delete();
		return $ret && $ret1;
	}
}

?>
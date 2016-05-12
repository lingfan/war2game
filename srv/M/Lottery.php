<?php

class M_Lottery {
	/**
	 * 自动生成可以抽取的奖励列表
	 * @author huwei
	 * @return array
	 */
	static public function make() {
		$itemArr    = array();
		$conf       = M_Config::getVal('lottery');
		$packageArr = $conf['Package'];
		$n          = 0;
		$debugLog   = array();
		$tmpList    = array();
		for ($i = 1; $i <= $conf['ItemNum']; $i++) {
			$rateArr = array();
			foreach ($packageArr as $pid => $val) {
				$rateArr[$pid] = $val['rate'];
			}

			$tmpPid = B_Utils::dice($rateArr);

			if (in_array($tmpPid, $conf['OutList'])) {
				$debugLog[$tmpPid] = isset($debugLog[$tmpPid]) ? $debugLog[$tmpPid] + 1 : 1;
				$n++;
			}

			$tmpInfo = $packageArr[$tmpPid];

			//如果限制列表的ID 出现最大数量 排除操作
			if ($n >= $conf['OutRate']) {
				foreach ($conf['OutList'] as $vid) {
					unset($packageArr[$vid]);
				}
			}

			$tmpArr = array();
			foreach ($tmpInfo['data'] as $k => $v) {
				$tmpArr[$k] = $val['rate'];
			}

			$tmpK             = B_Utils::dice($tmpArr);
			$itemInfo         = $tmpInfo['data'][$tmpK];
			$itemInfo['type'] = $tmpInfo['type'];
			unset($itemInfo['rate1']);
			$itemArr[$i] = $itemInfo;

			//$tmpList[$tmpPid] = $itemInfo;
		}
		//Logger::debug(array(__METHOD__, $tmpList, $debugLog, $n, $conf['OutList'], $conf['ItemNum']));
		return $itemArr;
	}

	/**
	 * 更新抽取奖励列表
	 * @author huwei
	 * @param array $data
	 * @param bool $upDB
	 */
	static public function setInfo($data, $upDB = true) {
		$ret    = false;
		$cityId = isset($data['city_id']) ? $data['city_id'] : 0;
		if (!empty($cityId) && is_array($data)) {
			$info = array();
			foreach ($data as $key => $val) {
				if (!empty($key) && in_array($key, T_DBField::$cityLotteryFields)) {
					$info[$key] = $val;
				}
			}

			if (!empty($info)) {
				$rc  = new B_Cache_RC(T_Key::CITY_LOTTERY_INFO, $cityId);
				$ret = $rc->hmset($info, T_App::ONE_DAY);
				if ($ret) {
					$upDB && M_CacheToDB::addQueue(T_Key::CITY_LOTTERY_INFO . ':' . $cityId);
				}
			}

			if (!$ret) {
				Logger::error(array(__METHOD__, 'Set Data Fail', func_get_args()));
			}
		}
		return $ret ? $info : false;
	}

	/**
	 * 获取奖励信息
	 * @author huwei
	 * @param int $cityId
	 * @return array
	 */
	static public function getInfo($cityId) {
		$info = false;
		if (!empty($cityId)) {
			$rc   = new B_Cache_RC(T_Key::CITY_LOTTERY_INFO, $cityId);
			$info = $rc->hgetall();
			if (empty($info['city_id'])) {
				$info = B_DB::instance('CityLottery')->getRow($cityId);
				if (!empty($info['city_id'])) {
					$bSucc = self::setInfo($info, false);
					if (!$bSucc) {
						Logger::error(array(__METHOD__, 'fail set lottery info', func_get_args()));
					}
				}
			}
			if ($info['refresh_date'] != date('Ymd')) {
				$info['refresh_date'] = date('Ymd');
				$info['refresh_num']  = 0;
				self::setInfo($info, true);
			}
		}
		return $info;
	}

	/**
	 * 抽取奖励操作
	 * @author huwei
	 * @param int $awardContent
	 */
	static public function draw($awardContent, $refreshNum) {
		$ret = false;
		if (!empty($awardContent)) {
			$rateArr = array();
			foreach ($awardContent as $k => $v) {
				$rate = 0;
				foreach ($v['rate2'] as $num => $val) {
					if ($refreshNum >= $num) {
						$rate = $val;
					}
				}

				if ($rate > 0) {
					$rateArr[$k] = $rate;
				}
			}
			$tmpK = B_Utils::dice($rateArr);
			$ret  = $tmpK;
		}
		return $ret;
	}

	/**
	 * 解析奖励数据
	 * @author huwei
	 * @param $award array
	 *        ['milpay']=>100,
	 *        ['coupon']=>100,
	 *        ['march_num']=>100,
	 *        ['atkfb_num']=>100,
	 *        ['renown']=>100,
	 *        ['warexp']=>100,
	 *        ['gold']=>100,
	 *        ['food']=>100,
	 *        ['oil']=>100,
	 *        ['equip']=>    array(id1=>num,id2=>num,id3=>num,)
	 *        ['props']=>    array(id1=>num,id2=>num,id3=>num,)
	 *        ['hero']=>    array(id1=>num,id2=>num,id3=>num,)
	 * @return array
	 */
	static public function awardData($award) {
		$arrAward = array();
		if (!empty($award['type'])) {
			if ($award['type'] == 'res') {
				$arrAward[$award['id']] = $award['num'];
			} else if ($award['type'] == 'props') {
				$arrAward['props'] = array($award['id'] => $award['num']);
			} else if ($award['type'] == 'equip') {
				$arrAward['equip'] = array($award['id'] => $award['num']);
			} else if ($award['type'] == 'hero') {
				$arrAward['hero'] = array($award['id'] => $award['num']);
			}
		}

		return $arrAward;
	}

	/**
	 * 解析奖励显示格式
	 * @author huwei
	 * @param array $awardContent
	 * @return array
	 */
	static public function awardText($awardContent) {
		$ret          = false;
		$arrAwardText = array();
		if (!empty($awardContent)) {
			foreach ($awardContent as $k => $v) {
				if ($v['type'] == 'res') {
					switch ($v['id']) {
						case 'gold':
							$lang = array(T_Lang::RES_GOLD_NAME);
							break;
						case 'oil':
							$lang = array(T_Lang::RES_OIL_NAME);
							break;
						case 'food':
							$lang = array(T_Lang::RES_FOOD_NAME);
							break;
					}
					$arrAwardText[] = array('res', $v['id'], $lang, $v['num']);
				}

				if ($v['type'] == 'props') {
					$propsInfo = M_Props::baseInfo($v['id']);
					if (!empty($propsInfo['name'])) {
						$arrAwardText[] = array('props', $v['id'], $propsInfo['name'], $v['num'], $propsInfo['face_id']);
					} else {
						Logger::error(array(__METHOD__, $v));
					}
				}

				if ($v['type'] == 'equip') {
					$equiTplInfo = M_Equip::baseInfo($v['id']);
					if (!empty($equiTplInfo['name'])) {
						$propsName      = array(T_Lang::EQUIP_NAME, $equiTplInfo['name'], array(T_Lang::$EQUIP_QUAL[$equiTplInfo['quality']]), $equiTplInfo['quality']);
						$arrAwardText[] = array('equip', $equiTplInfo['pos'], $propsName, $v['num'], $equiTplInfo['face_id']);
					} else {
						Logger::error(array(__METHOD__, $v));
					}
				}

				if ($v['type'] == 'hero') {
					$heroTplInfo = M_Hero::baseInfo($v['id']);

					if (!empty($heroTplInfo['nickname'])) {
						$heroName       = array(T_Lang::HERO_NAME, $heroTplInfo['nickname'], array(T_Lang::$HERO_QUAL[$heroTplInfo['quality']]), $heroTplInfo['quality']);
						$arrAwardText[] = array('hero', $v['id'], $heroName, $v['num'], $heroTplInfo['face_id']);
					} else {
						Logger::error(array(__METHOD__, $v));
					}
				}
			}
		}
		return $arrAwardText;
	}

	/**
	 * 计算刷新消耗军饷数
	 * @author huwei
	 * @param int $num
	 * return int
	 */
	static public function calcCostPrice($num = 0) {
		$arr       = explode(',', M_Config::getVal('lotter_refresh')); //array(免费次数,军饷增加系数,最大军饷)
		$costPrice = max($num - $arr[0], 0) * $arr[1];
		$costPrice = min($costPrice, $arr[2]);
		return $costPrice;
	}
}

?>
<?php

class C_Lottery extends C_I {
	/**
	 * 奖励信息
	 * @author huwei
	 *    ['gold']=> array(rate, num),
	 *    ['food']=> array(rate, num),
	 *    ['oil']=> array(rate, num),
	 *    ['equip']=>    array(rate, mode, array(id1 => array(rate, num)，id2 => array(rate, num)))
	 *    ['props']=>    array(rate, mode, array(id1 => array(rate, num)，id2 => array(rate, num)))
	 *    ['hero']=>    array(rate, mode, array(id1 => array(rate, num)，id2 => array(rate, num)))
	 */
	public function AInfo() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$info = M_Lottery::getInfo($cityInfo['id']);
		if (!empty($info['city_id'])) {
			$awardContent = json_decode($info['award_content'], true);

			$errNo = '';
			$data = array(
				'RefreshDate' => $info['refresh_date'],
				'RefreshNum' => $info['refresh_num'],
				'AwardNo' => !empty($awardContent) ? $info['award_no'] : 0,
				'AwardContent' => M_Lottery::awardText($awardContent),
				'NextCostPrice' => M_Lottery::calcCostPrice($info['refresh_num'] + 1),
			);
		}


		return B_Common::result($errNo, $data);
	}

	/**
	 * 刷新奖励
	 * @author huwei
	 */
	public function ARefresh() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$info = M_Lottery::getInfo($cityInfo['id']);
		$refreshNum = $info['refresh_num'] + 1;
		$err = '';
		$costPrice = M_Lottery::calcCostPrice($refreshNum);

		$isRefresh = true;
		if ($costPrice > 0) {
			$isRefresh = false;
			if ($cityInfo['mil_pay'] < $costPrice) {
				$err = T_ErrNo::NO_ENOUGH_MILIPAY;
			} else {
				$isRefresh = $objPlayer->City()->decrCurrency(T_App::MILPAY, $costPrice, B_Log_Trade::E_Lottery, $refreshNum);
			}
		}

		if (!empty($err)) {
			$errNo = $err;
		} else if ($isRefresh) {
			$awardContent = M_Lottery::make();

			$data['award_content'] = json_encode($awardContent);
			$data['refresh_num'] = $refreshNum;
			$data['city_id'] = $cityInfo['id'];
			$data['award_no'] = 0;
			$ret = M_Lottery::setInfo($data);
			if ($ret) {

				$errNo = '';
				$data = array(
					'RefreshDate' => $info['refresh_date'],
					'RefreshNum' => $refreshNum,
					'AwardNo' => $data['award_no'],
					'AwardContent' => M_Lottery::awardText($awardContent),
					'NextCostPrice' => M_Lottery::calcCostPrice($refreshNum + 1),
				);
			}
		}


		return B_Common::result($errNo, $data);
	}

	/**
	 * 抽取奖励
	 * @author huwei
	 */
	public function ADraw() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$info = M_Lottery::getInfo($cityInfo['id']);
		if (empty($info['award_no'])) {
			$awardContent = json_decode($info['award_content'], true);
			$awardNo = M_Lottery::draw($awardContent, $info['refresh_num']);

			if ($awardNo) {
				$upData['city_id'] = $cityInfo['id'];
				$upData['award_no'] = $awardNo;
				$ret = M_Lottery::setInfo($upData);
				if ($ret) {

					$errNo = '';
					$data = array(
						'AwardNo' => $awardNo,
					);
				} else {
					$errNo = T_ErrNo::ERR_UPDATE;
				}
			} else {
				$errNo = T_ErrNo::AWARD_DRAW_FAIL;
				Logger::error(array(__METHOD__, 'awardNo', $cityInfo['id'], $awardContent));
			}
		} else {
			$errNo = T_ErrNo::AWARD_HAD;
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 领取奖励
	 * @author huwei
	 */
	public function AGet() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$info = M_Lottery::getInfo($cityInfo['id']);
		$awardContent = json_decode($info['award_content'], true);
		if (!empty($info['award_no']) && isset($awardContent[$info['award_no']])) {
			$awardData = M_Lottery::awardData($awardContent[$info['award_no']]);
			$err = '';
			if (isset($awardData['hero']) &&
				M_Hero::isHeroNumFull($cityInfo['id'])
			) {
				$err = T_ErrNo::HERO_NUM_FULL_FAIL;
			} elseif (isset($awardData['equip']) &&
				M_Equip::isEquipNumFull($cityInfo['id'], $cityInfo['vip_level'])
			) {
				$err = T_ErrNo::EQUI_NUM_FULL;
			} elseif (isset($awardData['props'])) {
				$propsIdArr = array_keys($awardData['props']);
				$propsInfo = M_Props::baseInfo($propsIdArr[0]);
				M_QqShare::check($objPlayer, 'props_award', array());
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
			if (empty($err)) {

				$bAward = $objPlayer->City()->toAward($awardData, B_Log_Trade::I_Lottery);

				if ($bAward) {
					$upData['city_id'] = $cityInfo['id'];
					$upData['award_no'] = 0;
					$upData['award_content'] = '[]';
					$ret = M_Lottery::setInfo($upData);
					if ($ret) {

						$errNo = '';
						$data = array(
							'AwardNo' => 0,
							'AwardContent' => array(),
						);
					}
				} else {
					$errNo = T_ErrNo::AWARD_GET_FAIL;
				}
			} else {
				$errNo = $err;
			}
		} else {
			$errNo = T_ErrNo::AWARD_NOT_EXISTS;
		}

		return B_Common::result($errNo, $data);
	}
}

?>
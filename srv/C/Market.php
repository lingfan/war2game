<?php

class C_Market extends C_I {
	/**
	 * 市场买入资源
	 * @author chenhui on 20110622
	 * @param int $resType 资源类型 1粮 2油
	 * @param int $num 数量
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ABuy($resType, $num) {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_PARAM; //失败原因默认为参数错误
		$data = array(); //返回数据默认为空数组

		$customArr = array(
			T_App::RES_FOOD => T_App::RES_FOOD_NAME,
			T_App::RES_OIL => T_App::RES_OIL_NAME,
		);
		$num = intval($num);
		$objPlayer = $this->objPlayer;
		if (isset($customArr[$resType]) && $num > 0) {
			$resName = $customArr[$resType];

			$objRes = $objPlayer->Res();
			$objRes->incr($resName, $num);

			$cost_gold = round($num * M_City::MARKET_BUY_RATE);

			$leftGold = $objRes->incr('gold', -$cost_gold);


			$err = '';
			if (!$objPlayer->City()->isTradeQuotaOK($num)) { //市场交易额已达上限,不能进行此操作
				$err = T_ErrNo::CITY_TRADE_FULL;
			} else if ($objRes->isFull($resName)) { //城市资源已满仓,不能进行此操作
				$err = T_ErrNo::CITY_RES_FULL;
			} else if ($leftGold < 0) { //金钱不足
				$err = T_ErrNo::NO_ENOUGH_GOLD;
			}

			$errNo = $err;
			if (empty($err)) {
				$ret = $objPlayer->save();
				$errNo = T_ErrNo::ERR_UPDATE;
				if ($ret) { //结果置为成功
					$errNo = '';
				}
			}

		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 市场卖出资源
	 * @author chenhui on 20110622
	 * @param int $resType 资源类型 1粮 2油
	 * @param int $num 数量
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ASell($resType, $num) {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_PARAM; //失败原因默认为参数错误
		$data = array(); //返回数据默认为空数组
		$num = intval($num);

		$customArr = array(
			T_App::RES_FOOD => T_App::RES_FOOD_NAME,
			T_App::RES_OIL => T_App::RES_OIL_NAME,
		);

		$objPlayer = $this->objPlayer;

		if (isset($customArr[$resType]) && $num > 0) {
			$resName = $customArr[$resType];
			$gold = round($num / M_City::MARKET_SALE_RATE);

			$objRes = $objPlayer->Res();
			$left = $objRes->incr($resName, -$num);

			$err = '';
			if ($objRes->isFull('gold')) { //城市资源已满仓,不能进行此操作
				$err = T_ErrNo::CITY_RES_FULL;
			} else if ($left < 0) { //资源不足
				$err = (T_App::RES_FOOD == $resType) ? T_ErrNo::NO_ENOUGH_FOOD : T_ErrNo::NO_ENOUGH_OIL;
			}

			$errNo = $err;
			if (empty($err)) {
				$objRes->incr('gold', $gold);

				$errNo = T_ErrNo::ERR_UPDATE;
				$ret = $objPlayer->save();
				if ($ret) { //结果置为成功
					$errNo = ''; //错误码置为空
				}
			}
		}
		return B_Common::result($errNo, $data);
	}
}
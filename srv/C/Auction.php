<?php

/**
 * 拍卖接口
 */
class C_Auction extends C_I {
	/**
	 * 获取当前正在拍卖状态的拍卖数据
	 * @author chenhui on 20120217
	 * @param int $goodsType 物品类型[军官1 图纸2 装备3]
	 * @param int $secVal 物品类型下的小类型(因大类型不同而不同)
	 * @param string $sortField 排序字段[id,goods_name,goods_type,quality,pos,price_only]
	 * @param int $sortType 排序类型(1升序、2降序)
	 * @param int $page 当前页码
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AGetAucInfoIng($goodsType = 0, $secVal = 0, $sortField = 'goods_type', $sortType = 1, $page = 1) {
		//操作结果默认为失败0
		$errNo   = T_ErrNo::ERR_PARAM; //失败原因默认为参数错误
		$tmpData = array(); //拍卖数据默认为空数组
		$data    = array(1, $tmpData); //返回0=>总页码、1=>拍卖数据

		$objPlayer = $this->objPlayer;

		$goodsType = intval($goodsType);
		$secVal    = intval($secVal);
		$sortType  = intval($sortType);
		$page      = max(intval($page), 1);
		if ((empty($goodsType) || isset(M_Auction::$goods_type[$goodsType])) &&
			(empty($secVal) || isset(T_Word::$EQUIP_POS[$secVal]) || isset(T_Hero::$heroQual[$secVal])) &&
			isset(M_Auction::$sort_type[$sortType])
		) {
			$totalNum  = M_Auction::getAucOlSum($goodsType, $secVal);
			$totalPage = max(1, ceil($totalNum / M_Auction::ING_PAGE_SIZE));
			$page      = min($page, $totalPage);
			$dataTemp  = M_Auction::getAucInfoIng($goodsType, $secVal, $sortField, $sortType, $page);
			if (!empty($dataTemp) && is_array($dataTemp)) {
				$nowTime = time();
				foreach ($dataTemp as $dataT) {
					$aucInfo = M_Auction::getAucInfo($dataT['id']);
					if ($aucInfo['auction_expired'] > $nowTime) {

						$tmpData[] = array(
							'AucId'          => $dataT['id'],
							'SaleCityId'     => $aucInfo['sale_city_id'],
							'GoodsType'      => $aucInfo['goods_type'],
							'GoodsId'        => $aucInfo['goods_id'],
							'GoodsName'      => $aucInfo['goods_name'],
							'Quality'        => $aucInfo['quality'],
							'Pos'            => $aucInfo['pos'],
							'PriceOnly'      => $aucInfo['price_only'],
							'PriceStart'     => $aucInfo['price_start'],
							'PriceNew'       => $aucInfo['price_new'],
							'PriceSucc'      => $aucInfo['price_succ'],
							'BuyCityId'      => $aucInfo['buy_city_id'],
							'AuctionExpired' => M_Formula::calcCDTime($aucInfo['auction_expired']),
							'AuctionStatus'  => $aucInfo['auction_status'],
							'KeepExpired'    => M_Formula::calcCDTime($aucInfo['keep_expired']),
							'GoodsDetail'    => M_Auction::buildFrontData($aucInfo),
						);
					}
				}
			}
			$errNo = '';
			$data  = array($totalPage, $tmpData);
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 根据物品名字关键字模糊搜索相应物品
	 * @author chenhui on 20120228
	 * @param string $goodsName 物品名字关键字
	 * @param int $page 当前页码
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ASearchAucInfo($goodsName = '', $page = 1) {
		$tmpdata = array(); //拍卖数据默认为空数组
		$data    = array(1, $tmpdata); //返回0=>总页码、1=>拍卖数据

		$objPlayer = $this->objPlayer;
		$len       = mb_strlen($goodsName);
		$isIllName = ($len > 30 || $len == 0) ? true : false;

		$goodsName = trim(strval($goodsName));
		$page      = intval($page);
		if ($isIllName) {
			return B_Common::result(T_ErrNo::ERR_PARAM);
		}
		$totalNum  = M_Auction::totalAucListByName($goodsName);
		$totalPage = max(1, ceil($totalNum / M_Auction::ING_PAGE_SIZE));
		$page      = max(1, min($page, $totalPage));
		$dataTemp  = M_Auction::getAucListByName($goodsName, $page);
		if (!empty($dataTemp) && is_array($dataTemp)) {
			$nowTime = time();
			foreach ($dataTemp as $dataT) {
				$aucInfo = M_Auction::getAucInfo($dataT['id']);
				if ($aucInfo['auction_expired'] > $nowTime) {
					$tmpdata[] = array(
						'AucId'          => $dataT['id'],
						'SaleCityId'     => $aucInfo['sale_city_id'],
						'GoodsType'      => $aucInfo['goods_type'],
						'GoodsId'        => $aucInfo['goods_id'],
						'GoodsName'      => $aucInfo['goods_name'],
						'Quality'        => $aucInfo['quality'],
						'Pos'            => $aucInfo['pos'],
						'PriceOnly'      => $aucInfo['price_only'],
						'PriceStart'     => $aucInfo['price_start'],
						'PriceNew'       => $aucInfo['price_new'],
						'PriceSucc'      => $aucInfo['price_succ'],
						'BuyCityId'      => $aucInfo['buy_city_id'],
						'AuctionExpired' => M_Formula::calcCDTime($aucInfo['auction_expired']),
						'AuctionStatus'  => $aucInfo['auction_status'],
						'KeepExpired'    => M_Formula::calcCDTime($aucInfo['keep_expired']),
						'GoodsDetail'    => M_Auction::buildFrontData($aucInfo),
					);
				}
			}
		}

		$errNo = '';
		$data  = array($totalPage, $tmpdata);
		return B_Common::result($errNo, $data);
	}

	/**
	 * 购买管理(自己一口价或竞价购买的物品)
	 * @author chenhui on 20120220
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AGetBuyManage() {
		$obj = new C_Auction();
		return $obj->AMyList(1);
	}

	/**
	 * 出售管理(自己正在拍卖中或拍卖结束的物品)
	 * @author chenhui on 20120220
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AGetSaleManage() {
		$obj = new C_Auction();
		return $obj->AMyList(2);
	}

	/**
	 * 获取自己的拍卖数据
	 * @param int $type [1购买,2出售]
	 * @return array
	 */
	public function AMyList($type = 0) {
		//操作结果默认为失败0
		$data    = array(); //返回数据默认为空数组
		$nowTime = time();

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		if ($cityInfo['mil_medal'] < M_Auction::USE_MIN) {
			return B_Common::result(T_ErrNo::NO_ENOUGH_MILMEDAL);
		}

		$arrAucIds = M_Auction::getCityAucList($objPlayer->City()->id);
		if (!empty($arrAucIds)) {
			foreach ($arrAucIds as $aucId) {
				$aucInfo   = M_Auction::getAucInfo($aucId);
				$bShow     = true;
				$buyCityId = $saleCityId = 0;
				if ($type == 1) {
					$bShow = ($aucInfo['buy_city_id'] == $cityInfo['id']) ? true : false;
				} else if ($type == 2) {
					$bShow = ($aucInfo['sale_city_id'] == $cityInfo['id']) ? true : false;
				}

				if ($aucInfo['keep_expired'] > $nowTime && $bShow) {
					$data[] = array(
						'AucId'          => $aucId,
						'SaleCityId'     => $aucInfo['sale_city_id'],
						'GoodsType'      => $aucInfo['goods_type'],
						'GoodsId'        => $aucInfo['goods_id'],
						'GoodsName'      => $aucInfo['goods_name'],
						'Quality'        => $aucInfo['quality'],
						'Pos'            => $aucInfo['pos'],
						'PriceOnly'      => $aucInfo['price_only'],
						'PriceStart'     => $aucInfo['price_start'],
						'PriceNew'       => $aucInfo['price_new'],
						'PriceSucc'      => $aucInfo['price_succ'],
						'BuyCityId'      => $aucInfo['buy_city_id'],
						'AuctionExpired' => M_Formula::calcCDTime($aucInfo['auction_expired']),
						'AuctionStatus'  => $aucInfo['auction_status'],
						'KeepExpired'    => M_Formula::calcCDTime($aucInfo['keep_expired']),
						'GoodsDetail'    => M_Auction::buildFrontData($aucInfo),
					);
				}
			}
		}
		return B_Common::result('', $data);
	}


	/**
	 * 出售物品
	 * @author chenhui on 20120109
	 * @param int $goodsType 物品类型(军官1 图纸2 装备3)
	 * @param int $goodsId 物品相应ID
	 * @param int $keepTypeTmp 保管类型小时数[24,48]/[1,2,3,4]
	 * @param int $priceStart 竞拍价
	 * @param int $priceOnly 一口价(可不填,有则必大于竞拍价)
	 * @param int $isType $keepTypeTmp是否保管类型[0否/1是]
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ASale($goodsType, $goodsId, $keepTypeTmp, $priceStart, $priceOnly = 0, $isType = 0) {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_PARAM; //失败原因默认为参数错误
		$data  = array(); //返回数据默认为空数组

		$goodsType   = intval($goodsType);
		$goodsId     = intval($goodsId);
		$keepTypeTmp = intval($keepTypeTmp);
		$priceStart  = min(intval($priceStart), M_Auction::PRICE_LIMIT_MAX);
		$priceOnly   = min(intval($priceOnly), M_Auction::PRICE_LIMIT_MAX);
		$isType      = intval($isType);

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();


		if (!M_Auction::isGoodsType($goodsType) || $goodsId <= 0 || $keepTypeTmp <= 0 || $priceStart < M_Auction::PRICE_START_MIN) {
			return B_Common::result(T_ErrNo::ERR_PARAM);
		}

		$checkPrice = (empty($priceOnly) || ($priceOnly > 0 && $priceOnly > $priceStart)) ? true : false;
		if (!$checkPrice) {
			return B_Common::result(T_ErrNo::ERR_PARAM);
		}

		if ($cityInfo['mil_medal'] < M_Auction::USE_MIN) {
			return B_Common::result(T_ErrNo::NO_ENOUGH_MILMEDAL);
		}

		$cityId     = intval($cityInfo['id']); //卖家CityId
		$cityAucSum = M_Auction::getCityAucListSum($cityId); //当前挂单数
		if ($cityAucSum >= M_Auction::MAX_ORDER_SUM) {
			return B_Common::result(T_ErrNo::AUC_LIST_FULL);
		}

		$saleConf = M_Auction::getAucSaleConf();
		$KeepType = array(
			M_Auction::COUPON_ONE => array(T_App::COUPON, $saleConf[0][2]), //点券20
			M_Auction::MILPAY_ONE => array(T_App::MILPAY, $saleConf[0][1]), //军饷10
			M_Auction::COUPON_TWO => array(T_App::COUPON, $saleConf[1][2]), //点券40
			M_Auction::MILPAY_TWO => array(T_App::MILPAY, $saleConf[1][1]), //军饷20
		);

		$qualityErr = ''; //品质错误
		$goodsInfo  = array();
		$goods_name = '';
		$quality    = 0;
		$pos        = 0;

		$err = ''; //保管错误
		switch ($goodsType) {
			case M_Auction::GOODS_HERO:
				$goodsInfo  = M_Hero::getCityHeroInfo($cityId, $goodsId);
				$qualityErr = T_ErrNo::HERO_NOT_FREE;
				if (!empty($goodsInfo)) {
					$tmp1    = array($goodsInfo['equip_arm'], $goodsInfo['equip_cap'], $goodsInfo['equip_uniform'], $goodsInfo['equip_medal'], $goodsInfo['equip_shoes'], $goodsInfo['equip_sit']);
					$tep1Sum = array_sum($tmp1);

					if (!empty($tep1Sum) && M_Equip::isEquipNumFull($cityId)) {
						$qualityErr = T_ErrNo::EQUI_NUM_FULL;
					} else if (T_Hero::FLAG_FREE == $goodsInfo['flag']) {
						$qualityErr = '';
						$goods_name = $goodsInfo['nickname'];
						$quality    = $goodsInfo['quality'];
						$pos        = 1;
						if ($quality < T_Hero::HERO_BULE_LEGEND) {
							$qualityErr = T_ErrNo::AUC_HERO_QUALITY_LOW;
						}
					}
				}
				break;
			case M_Auction::GOODS_DRAW:
				if (M_Props::checkCityPropsNum($cityId, $goodsId, M_Props::UNBINDING, 1)) {
					$goodsInfo = M_Props::baseInfo($goodsId); //图纸信息
					if ('WEAPON_CREATE' == $goodsInfo['effect_txt']) {
						$goods_name = $goodsInfo['name'];
						$quality    = 1;
						$pos        = 1;
					}
				} else {
					$qualityErr = T_ErrNo::PROPS_NOT_ENOUGH;
				}
				break;
			case M_Auction::GOODS_EQUI:
				$goodsInfo = M_Equip::getCityEquipById($cityId, $goodsId); //装备信息
				if (!empty($goodsInfo) && T_Equip::EQUIP_NOT_USE == $goodsInfo['is_use']) {
					if ($goodsInfo['is_locked'] == M_Equip::UNBINDING) {
						$goods_name = $goodsInfo['name'];
						$quality    = $goodsInfo['quality'];
						$pos        = $goodsInfo['pos'];
						if ($quality < T_Equip::EQUIP_PURPLE && $pos != T_Equip::EQUIP_EXP) {
							$qualityErr = T_ErrNo::AUC_EQUI_QUALITY_LOW;
						}
					} else {
						$qualityErr = T_ErrNo::AUC_NOT_BINGING;
					}
				} else {
					$qualityErr = T_ErrNo::EQUIP_ON_SALE;
				}
				break;
			case M_Auction::GOODS_PROPS:
				$goodsInfo = M_Props::baseInfo($goodsId);
				if (!M_Props::checkCityPropsNum($cityId, $goodsId, M_Props::UNBINDING, 1)) {
					$qualityErr = T_ErrNo::PROPS_NOT_ENOUGH;
				}
				break;
			case M_Auction::GOODS_STUFF:
				$goodsInfo = M_Props::baseInfo($goodsId);
				if (!M_Props::checkCityPropsNum($cityId, $goodsId, M_Props::UNBINDING, 1)) {
					$qualityErr = T_ErrNo::PROPS_NOT_ENOUGH;
				}
				break;
			default:
				break;
		}

		if (!empty($qualityErr)) {
			return B_Common::result($qualityErr);
		}
		$keep_type = 0; //程序用保管类型
		$keep_hour = 0; //保管小时数

		if (!empty($isType)) {
			$keep_type = $keepTypeTmp;
			switch ($keepTypeTmp) {
				case M_Auction::COUPON_ONE:
					if ($cityInfo['coupon'] < $saleConf[0][2]) {
						$err = T_ErrNo::NO_ENOUGH_COUPON;
					} else {
						$keep_hour = $saleConf[0][0];
					}
					break;
				case M_Auction::MILPAY_ONE:
					if ($cityInfo['mil_pay'] < $saleConf[0][1]) {
						$err = T_ErrNo::NO_ENOUGH_MILIPAY;
					} else {
						$keep_hour = $saleConf[0][0];
					}
					break;
				case M_Auction::COUPON_TWO:
					if ($cityInfo['coupon'] < $saleConf[1][2]) {
						$err = T_ErrNo::NO_ENOUGH_COUPON;
					} else {
						$keep_hour = $saleConf[1][0];
					}
					break;
				case M_Auction::MILPAY_TWO:
					if ($cityInfo['mil_pay'] < $saleConf[1][1]) {
						$err = T_ErrNo::NO_ENOUGH_MILIPAY;
					} else {
						$keep_hour = $saleConf[1][0];
					}
					break;
				default:
					$err = T_ErrNo::AUC_TYPE_ERR;
					break;
			}
		} else {
			$keep_hour = $keepTypeTmp;
			if ($saleConf[0][0] == $keepTypeTmp) {
				if ($cityInfo['coupon'] >= $saleConf[0][2]) {
					$keep_type = M_Auction::COUPON_ONE;
				} else if ($cityInfo['mil_pay'] >= $saleConf[0][1]) {
					$keep_type = M_Auction::MILPAY_ONE;
				} else {
					$err = T_ErrNo::NO_ENOUGH_MILIPAY;
				}
			} else if ($saleConf[1][0] == $keepTypeTmp) {
				if ($cityInfo['coupon'] >= $saleConf[1][2]) {
					$keep_type = M_Auction::COUPON_TWO;
				} else if ($cityInfo['mil_pay'] >= $saleConf[1][1]) {
					$keep_type = M_Auction::MILPAY_TWO;
				} else {
					$err = T_ErrNo::NO_ENOUGH_MILIPAY;
				}
			}
		}

		$nowTime = time();
		if (!empty($err)) {
			return B_Common::result($err);
		}
		//Logger::debug(array(__METHOD__, $goodsInfo, $goods_name, $quality,  $pos, $keep_type));
		if (empty($goodsInfo) || empty($goods_name) || empty($quality) || empty($pos) || !isset($KeepType[$keep_type])) {
			return B_Common::result(T_ErrNo::AUC_GOODS_DATA_ERR);
		}

		$saleData = array(
			'sale_city_id'    => $cityId,
			'goods_type'      => $goodsType,
			'goods_id'        => $goodsId,
			'goods_name'      => $goods_name,
			'quality'         => $quality,
			'pos'             => $pos,
			'keep_type'       => $keep_type,
			'price_only'      => $priceOnly,
			'price_start'     => $priceStart,
			'price_new'       => $priceStart,
			'price_succ'      => 0,
			'buy_city_id'     => 0,
			'create_at'       => $nowTime,
			'auction_start'   => $nowTime,
			'auction_expired' => $nowTime + T_App::ONE_HOUR * $keep_hour,
			'auction_status'  => M_Auction::STATUS_ING,
			'keep_expired'    => $nowTime + T_App::ONE_DAY * M_Auction::SYS_KEEP_DAY,
			'shift_at'        => 0,
			'shift_type'      => 0,
			'ol_time'          => $nowTime + rand(10, 30) * T_App::ONE_MINUTE,
		);

		$aucId = M_Auction::insert($saleData);
		if (!empty($aucId)) {
			return B_Common::result(T_ErrNo::AUC_INSERT_ERR);
		}

		M_Auction::addCityAucList($saleData['sale_city_id'], $aucId);

		$bUp = false;
		if ($goodsType == M_Auction::GOODS_HERO) {
			//删除玩家英雄列表里面 修改英雄城市ID为0
			$bUp = M_Hero::clearHero($cityInfo, $goodsInfo, true);
		} else if ($goodsType == M_Auction::GOODS_DRAW) {
			$bUp = $objPlayer->Pack()->decrNumByPropId($goodsId, 1);
		} else if ($goodsType == M_Auction::GOODS_EQUI) {
			//删除玩家装备列表里面 修改装备城市ID为0
			$bUp = M_Equip::setInfo($goodsId, array('city_id' => 0));
			if ($bUp) {
				M_Equip::delCityEquipList($cityId, $goodsId);
				$equipSyncData[$goodsId] = M_Sync::DEL;
				M_Sync::addQueue($cityId, M_Sync::KEY_EQUIP, $equipSyncData); //同步数据
			}
		}

		if (!$bUp) {
			return B_Common::result(T_ErrNo::AUC_GOODS_UPDATE_ERR);
		}

		//扣保管费
		$k_t = $KeepType[$keep_type];
		if ($k_t[0] == T_App::MILPAY) {
			$objPlayer->City()->mil_pay -= $k_t[1];
		} else {
			$objPlayer->City()->coupon -= $k_t[1];
		}

		$objPlayer->Log()->expense($k_t[0], $k_t[1], B_Log_Trade::E_AuctionSale, $aucId);

		if ($aucId > 0) {

			if ($goodsType == M_Auction::GOODS_HERO) {
				$dataStr = $priceStart . '_' . $priceOnly;
				Logger::opHero($cityId, $goodsId, Logger::H_ACT_AUCTION, $dataStr);
			} elseif ($goodsType == M_Auction::GOODS_EQUI) {
				$dataStr = $priceStart . '_' . $priceOnly;
				Logger::opEquip($cityId, $goodsId, Logger::E_ACT_AUCTION, $dataStr);
			}
		}

		$errNo = '';
		return B_Common::result($errNo, $data);
	}

	/**
	 * 购买一口价物品
	 * @author chenhui on 20120117
	 * @param int $aucId 交易ID
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ABuyPriceOnly($aucId) {
		//操作结果默认为失败0
		$data = array(); //返回数据默认为空数组

		$aucId = intval($aucId);
		if ($aucId <= 0) {
			return B_Common::result(T_ErrNo::ERR_PARAM);
		}

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();


		$cityId = intval($cityInfo['id']);

		$rc         = new B_Cache_RC(T_Key::AUC_BUY_ONLY_DATA, $aucId);
		$buyingCity = $rc->get();
		if (empty($buyingCity)) {
			$rc->set($cityId, 2); //设置2秒的过期时间
			$buyingCity = $cityId;
		}

		$aucInfo  = M_Auction::getAucInfo($aucId);
		$saleConf = M_Auction::getAucSaleConf();

		$nowTime    = time();
		$errFull    = ''; //判断相应物品是否已满
		$goodsType  = intval($aucInfo['goods_type']);
		$price_only = intval($aucInfo['price_only']);

		if ($cityInfo['mil_medal'] < M_Auction::USE_MIN) {
			return B_Common::result(T_ErrNo::NO_ENOUGH_MILMEDAL);
		} else if (empty($aucInfo['id'])) {
			return B_Common::result(T_ErrNo::AUC_DATA_ERR);
		} else if ($buyingCity != $cityId) {
			return B_Common::result(T_ErrNo::AUC_BEEN_BUG_SB);
		} else if (M_Auction::STATUS_ING != $aucInfo['auction_status']) {
			return B_Common::result(T_ErrNo::AUC_NOT_ING);
		} else if ($aucInfo['auction_expired'] < $nowTime) {
			return B_Common::result(T_ErrNo::AUC_EXPIRED);
		} else if ($price_only < 1) {
			return B_Common::result(T_ErrNo::AUC_NOT_PRICE_ONLY);
		} else if ($cityInfo['mil_pay'] < $price_only) {
			return B_Common::result(T_ErrNo::NO_ENOUGH_MILIPAY);
		} else if ($aucInfo['sale_city_id'] == $cityId) {
			return B_Common::result(T_ErrNo::AUC_IS_SELF);
		}
		//扣除买家军饷
		$bDecr = $objPlayer->City()->decrCurrency(T_App::MILPAY, $price_only, B_Log_Trade::E_AuctionBuy, $aucId);
		if (!$bDecr) {
			return B_Common::result(T_ErrNo::ERR_PAY);
		}
		//设置拍卖行状态为购买成功
		$newInfo = array(
			'price_succ'      => $price_only,
			'buy_city_id'     => $cityId,
			'auction_expired' => $nowTime,
			'auction_status'  => M_Auction::STATUS_SUCC,
			'keep_expired'    => time() + T_App::ONE_DAY * M_Auction::SYS_KEEP_DAY,
		);
		$bUp     = M_Auction::updateAucInfo($aucId, $newInfo, true);
		if (!$bUp) {
			Logger::auction(array(__METHOD__, $aucId, func_get_args(), 'Fail for M_Auction::updateAucInfo ', $newInfo));
			return B_Common::result(T_ErrNo::ERR_UPDATE);
		}

		M_Auction::delCityAucList($aucInfo['sale_city_id'], $aucId);
		M_Auction::addCityAucList($cityId, $aucId);

		//返还卖家保管费
		$keepBackCoupon = 0;
		$keepBackMilpay = 0;
		$keepMoneyType  = 2; //默认点券

		$param = array();
		switch ($aucInfo['keep_type']) {
			case M_Auction::COUPON_ONE:
				$param          = array('coupon' => $saleConf[0][2]);
				$keepBackCoupon = $saleConf[0][2];
				break;
			case M_Auction::MILPAY_ONE:
				$param          = array('milpay' => $saleConf[0][1]);
				$keepBackMilpay = $saleConf[0][1];
				$keepMoneyType  = 1;
				break;
			case M_Auction::COUPON_TWO:
				$param          = array('coupon' => $saleConf[1][2]);
				$keepBackCoupon = $saleConf[1][2];
				break;
			case M_Auction::MILPAY_TWO:
				$param          = array('milpay' => $saleConf[1][1]);
				$keepBackMilpay = $saleConf[1][1];
				$keepMoneyType  = 1;
				break;
			default:
				break;
		}

		$strKeepBack = max($keepBackCoupon, $keepBackMilpay); //返还保管费通知
		$langMoney   = array(T_Lang::$PAY_TYPE[$keepMoneyType]);
		//扣税后给卖家加军饷
		$tax    = max(M_Auction::TAX_MIN, floor($price_only * M_Auction::TAX_RATE));
		$income = max(0, $price_only - $tax);

		$param['milpay'] = isset($param['milpay']) ? $param['milpay'] + $income : $income;

		$objPlayerSale = new O_Player($aucInfo['sale_city_id']);
		$objPlayerSale->City()->mil_pay += $aucInfo['price_new'];

		if (isset($param['milpay'])) {
			$objPlayerSale->Log()->income(T_App::MILPAY, $param['milpay'], B_Log_Trade::I_AucSale);
		} else {
			$objPlayerSale->Log()->income(T_App::COUPON, $param['coupon'], B_Log_Trade::I_AucSale);
		}

		$bIncrSale = $objPlayerSale->save();

		if (!$bIncrSale) {
			Logger::auction(array(__METHOD__, $aucId, func_get_args(), 'Fail for M_City::incrCityCurrency ', $newInfo));
			return B_Common::result(T_ErrNo::ERR_INCOME);
		}

		//删除内存表拍卖中数据

		//拍卖成功通知买家
		$contentBuy = array(T_Lang::C_AUC_BUY_SUCC, $aucInfo['goods_name'], $price_only);
		M_Message::sendSysMessage($cityId, json_encode(array(T_Lang::T_AUC_TIP)), json_encode($contentBuy));

		//拍卖成功通知卖家
		$contentSale = array(T_Lang::C_AUC_SALE_SUCC, $aucInfo['goods_name'], $price_only, $tax, $strKeepBack, $langMoney, $income + $keepBackMilpay, $keepBackCoupon);
		M_Message::sendSysMessage($aucInfo['sale_city_id'], json_encode(array(T_Lang::T_AUC_TIP)), json_encode($contentSale), false);

		//返还军饷给原竞拍失败玩家
		if ($aucInfo['buy_city_id'] > 0 && $aucInfo['price_new'] > 0) {
			M_Auction::delCityAucList($aucInfo['buy_city_id'], $aucId);
			$objPlayerBuy = new O_Player($aucInfo['buy_city_id']);
			$objPlayerBuy->City()->mil_pay += $aucInfo['price_new'];
			$objPlayerBuy->Log()->income(T_App::MILPAY, $aucInfo['price_new'], B_Log_Trade::I_AucBack);
			$bIncr = $objPlayerBuy->save();

			if ($bIncr) {
				$content = array(T_Lang::C_AUC_MILPAY_BACK, $aucInfo['goods_name'], $aucInfo['price_new']);
				M_Message::sendSysMessage($aucInfo['buy_city_id'], json_encode(array(T_Lang::T_AUC_TIP)), json_encode($content));
			}
		}

		if ($aucInfo['goods_type'] == M_Auction::GOODS_HERO) {
			Logger::opHero($aucInfo['sale_city_id'], $aucInfo['goods_id'], Logger::H_ACT_SELLOUT, $price_only);
			Logger::opHero($aucInfo['buy_city_id'], $aucInfo['goods_id'], Logger::H_ACT_BUY, $price_only);
		} elseif ($aucInfo['goods_type'] == M_Auction::GOODS_EQUI) {
			Logger::opEquip($aucInfo['sale_city_id'], $aucInfo['goods_id'], Logger::E_ACT_SELLOUT, $price_only);
			Logger::opEquip($aucInfo['buy_city_id'], $aucInfo['goods_id'], Logger::E_ACT_BUY, $price_only);
		}

		$errNo = '';

		return B_Common::result($errNo, $data);
	}

	/**
	 * 竞价
	 * @author chenhui on 20120118
	 * @param int $aucId 交易ID
	 * @param int $price_new 新的竞拍价格
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ABid($aucId, $price_new) {

		$aucId     = intval($aucId);
		$price_new = intval($price_new);

		if ($aucId <= 0 || $price_new <= 0) {
			return B_Common::result(T_ErrNo::ERR_PARAM);
		}

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		$cityId     = intval($cityInfo['id']);
		$cityAucSum = M_Auction::getCityAucListSum($cityId); //当前挂单数
		if ($cityAucSum >= M_Auction::MAX_ORDER_SUM) {
			return B_Common::result(T_ErrNo::AUC_LIST_FULL);
		}
		$rc = new B_Cache_RC(T_Key::AUC_BID_DATA, $aucId);

		$aucInfo = M_Auction::getAucInfo($aucId);
		$nowTime = time();

		if ($cityInfo['mil_medal'] < M_Auction::USE_MIN) {
			return B_Common::result(T_ErrNo::NO_ENOUGH_MILMEDAL);
		} else if (empty($aucInfo['id'])) {
			return B_Common::result(T_ErrNo::AUC_DATA_ERR);
		} else if ($rc->exists()) {
			return B_Common::result(T_ErrNo::AUC_BEEN_BID_SB);
		} else if (M_Auction::STATUS_ING != $aucInfo['auction_status']) {
			return B_Common::result(T_ErrNo::AUC_NOT_ING);
		} else if ($aucInfo['auction_expired'] < $nowTime) {
			return B_Common::result(T_ErrNo::AUC_EXPIRED);
		} else if ($price_new < $aucInfo['price_new']) {
			$data = array($aucInfo['price_new']);
			return B_Common::result(T_ErrNo::AUC_NEW_PRICE_LESS, $data);
		} else if ($cityInfo['mil_pay'] < $price_new) {
			return B_Common::result(T_ErrNo::NO_ENOUGH_MILIPAY);
		} else if ($price_new >= M_Auction::PRICE_LIMIT_MAX || ($aucInfo['price_only'] > 0 && $price_new >= $aucInfo['price_only'])) {
			return B_Common::result(T_ErrNo::AUC_NEW_PRICE_MORE);
		} else if ($aucInfo['sale_city_id'] == $cityId) {
			return B_Common::result(T_ErrNo::AUC_IS_SELF);
		}

		$bDecr = $objPlayer->City()->decrCurrency(T_App::MILPAY, $price_new, B_Log_Trade::E_AucPriceNew, $aucId);
		if (!$bDecr) {
			return B_Common::result(T_ErrNo::ERR_PAY);
		}

		$auction_expired = $aucInfo['auction_expired'];
		if (($aucInfo['auction_expired'] - $nowTime) < 30 * T_App::ONE_MINUTE) {
			$auction_expired = $nowTime + T_App::ONE_MINUTE * 30;
		}
		$newInfo = array(
			'price_new'       => $price_new,
			'buy_city_id'     => $cityId,
			'auction_expired' => $auction_expired,
			'auction_status'  => M_Auction::STATUS_ING,
		);
		$bUp     = M_Auction::updateAucInfo($aucId, $newInfo, true);
		if (!$bUp) {
			Logger::auction(array(__METHOD__, $aucId, func_get_args(), 'Fail for M_Auction::updateAucInfo ', "aucId#{$aucId}", $newInfo));
			return B_Common::result(T_ErrNo::ERR_UPDATE);
		}
		//竞价成功 添加交易ID到玩家购买列表
		M_Auction::addCityAucList($cityId, $aucInfo['id']);

		$rc->set(1, 1); //设置1秒的过期时间

		$errNo = '';
		$data  = array($price_new);
		//返还军饷给原竞拍失败玩家
		if ($aucInfo['buy_city_id'] > 0 && $aucInfo['price_new'] > 0) {
			//删掉上一个购买者 列表中的 物品ID
			M_Auction::delCityAucList($aucInfo['buy_city_id'], $aucInfo['id']);

			$objPlayerBuy = new O_Player($aucInfo['buy_city_id']);
			$objPlayerBuy->City()->mil_pay += $aucInfo['price_new'];
			$objPlayerBuy->Log()->income(T_App::MILPAY, $aucInfo['price_new'], B_Log_Trade::I_AucBack);
			$bIncr = $objPlayerBuy->save();

			if ($bIncr) {
				$content = array(T_Lang::C_AUC_MILPAY_BACK, $aucInfo['goods_name'], $aucInfo['price_new']);
				M_Message::sendSysMessage($aucInfo['buy_city_id'], json_encode(array(T_Lang::T_AUC_TIP)), json_encode($content));
			} else {
				Logger::auction(array(__METHOD__, $aucId, func_get_args(), 'Fail for M_City::incrCityCurrency', "aucId#{$aucId}", $newInfo));
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 领取购买成功后的物品[买家操作]
	 * @author chenhui on 20120214
	 * @param int $aucId 交易ID
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public
	function AReceiveGoods($aucId) {
		$data = array(); //返回数据默认为空数组

		$aucId = intval($aucId);
		if ($aucId <= 0) {
			return B_Common::result(T_ErrNo::ERR_PARAM);
		}

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		$cityId  = intval($cityInfo['id']);
		$aucInfo = M_Auction::getAucInfo($aucId);
		$nowTime = time();

		$propsNumArr = $objPlayer->Pack()->hasNum();

		if ($cityInfo['mil_medal'] < M_Auction::USE_MIN) {
			return B_Common::result(T_ErrNo::NO_ENOUGH_MILMEDAL);
		} else if (empty($aucInfo['id'])) {
			return B_Common::result(T_ErrNo::AUC_DATA_ERR);
		} else if ($aucInfo['goods_type'] == M_Auction::GOODS_HERO && M_Hero::isHeroNumFull($cityId)) {
			return B_Common::result(T_ErrNo::HERO_NUM_FULL_FAIL);
		} else if ($aucInfo['goods_type'] == M_Auction::GOODS_EQUI && M_Equip::isEquipNumFull($cityId)) {
			return B_Common::result(T_ErrNo::EQUI_NUM_FULL);
		} else if ($aucInfo['goods_type'] == M_Auction::GOODS_DRAW && $propsNumArr['draw']['full']) {
			return B_Common::result(T_ErrNo::DRAW_NUM_FULL);
		} else if ($aucInfo['goods_type'] == M_Auction::GOODS_PROPS && $propsNumArr['normal']['full']) {
			return B_Common::result(T_ErrNo::PROPS_NUM_FULL);
		} else if ($aucInfo['goods_type'] == M_Auction::GOODS_STUFF && $propsNumArr['stuff']['full']) {
			return B_Common::result(T_ErrNo::MATERIAL_NUM_FULL);
		} else if (M_Auction::STATUS_SUCC != $aucInfo['auction_status']) {
			return B_Common::result(T_ErrNo::AUC_NOT_SUCC);
		} else if ($aucInfo['buy_city_id'] != $cityId) {
			return B_Common::result(T_ErrNo::AUC_NOT_SELF);
		} else if ($aucInfo['keep_expired'] < $nowTime) {
			return B_Common::result(T_ErrNo::AUC_KEEP_EXPIRED);
		}

		$bUp = false;
		//领取物品
		//由于redis读写分离 所以 刚写入的数据 无法立刻获取 所以先获取 然后更新
		if ($aucInfo['goods_type'] == M_Auction::GOODS_HERO) {
			$bUp = M_Auction::fetchSelfAuctionHero($objPlayer, $aucInfo['goods_id']);
		} else if ($aucInfo['goods_type'] == M_Auction::GOODS_DRAW) {
			$bUp = M_Auction::fetchSelfAuctionProps($objPlayer, $aucInfo['goods_id']);
		} else if ($aucInfo['goods_type'] == M_Auction::GOODS_EQUI) {
			$bUp = M_Auction::fetchSelfAuctionEquip($objPlayer, $aucInfo['goods_id']);
		}

		$ret = false;
		if ($bUp) {
			$newInfo = array(
				'shift_at'       => $nowTime,
				'shift_type'     => M_Auction::SHIFT_KEEP_ING_BUY,
				'auction_status' => M_Auction::STATUS_DEL,
			);
			$ret     = M_Auction::updateAucInfo($aucId, $newInfo, true);
		}

		if ($ret) {
			M_Auction::delCityAucList($aucInfo['buy_city_id'], $aucId);
			$errNo = '';
		} else {
			Logger::auction(array(__METHOD__, "aucId#{$aucId};goodsId#{$aucInfo['goods_id']};cityId#{$cityId};goodsType#{$aucInfo['goods_type']}"));
			$errNo = T_ErrNo::ERR_UPDATE;
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 撤销交易(返还拍卖中的物品)[卖家操作]
	 * @author chenhui on 20120214
	 * @param int $aucId 交易ID
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public
	function ARevocation($aucId) {
		$data = array(); //返回数据默认为空数组

		$aucId = intval($aucId);
		if ($aucId <= 0) {
			return B_Common::result(T_ErrNo::ERR_PARAM);
		}

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		$cityId      = intval($cityInfo['id']);
		$aucInfo     = M_Auction::getAucInfo($aucId);
		$nowTime     = time();
		$propsNumArr = $objPlayer->Pack()->hasNum();

		if ($cityInfo['mil_medal'] < M_Auction::USE_MIN) {
			return B_Common::result(T_ErrNo::NO_ENOUGH_MILMEDAL);
		} else if (empty($aucInfo['id'])) {
			return B_Common::result(T_ErrNo::AUC_DATA_ERR);
		} else if ($aucInfo['goods_type'] == M_Auction::GOODS_HERO && M_Hero::isHeroNumFull($cityId)) {
			return B_Common::result(T_ErrNo::HERO_NUM_FULL_FAIL);
		} else if ($aucInfo['goods_type'] == M_Auction::GOODS_EQUI && M_Equip::isEquipNumFull($cityId, $cityInfo['vip_level'])) {
			return B_Common::result(T_ErrNo::EQUI_NUM_FULL);
		} else if ($aucInfo['goods_type'] == M_Auction::GOODS_DRAW && $propsNumArr['draw']['full']) {
			return B_Common::result(T_ErrNo::DRAW_NUM_FULL);
		} else if (M_Auction::STATUS_ING != $aucInfo['auction_status'] || $aucInfo['sale_city_id'] != $cityId) {
			return B_Common::result(T_ErrNo::AUC_NOT_ING);
		} else if ($aucInfo['keep_expired'] < $nowTime) {
			return B_Common::result(T_ErrNo::AUC_KEEP_EXPIRED);
		}
		//删除内存表拍卖中数据
		$newInfo = array(
			'shift_at'       => $nowTime,
			'shift_type'     => M_Auction::SHIFT_AUC_ING_SALE,
			'auction_status' => M_Auction::STATUS_DEL,
		);
		$bUp     = M_Auction::updateAucInfo($aucId, $newInfo, true);

		if (!$bUp) {
			return B_Common::result(T_ErrNo::ERR_UPDATE);
		}
		M_Auction::delCityAucList($aucInfo['sale_city_id'], $aucId);
		//返还军饷给原出价竞拍玩家
		if ($aucInfo['buy_city_id'] > 0 && $aucInfo['price_new'] > 0) {
			M_Auction::delCityAucList($aucInfo['buy_city_id'], $aucId);
			$param = array('milpay' => $aucInfo['price_new']);

			$objPlayerBuy = new O_Player($aucInfo['buy_city_id']);
			$objPlayerBuy->City()->mil_pay += $aucInfo['price_new'];
			$objPlayerBuy->Log()->income(T_App::MILPAY, $aucInfo['price_new'], B_Log_Trade::I_AucBack);
			$bIncr = $objPlayerBuy->save();
			if ($bIncr) {
				$content = array(T_Lang::C_AUC_MILPAY_BACK, $aucInfo['goods_name'], $aucInfo['price_new']);
				M_Message::sendSysMessage($aucInfo['buy_city_id'], json_encode(array(T_Lang::T_AUC_TIP)), json_encode($content));
			} else {
				Logger::auction(array(__METHOD__, $aucId, 'Fail for M_City::incrCityCurrency', "old_buy_city_id#{$aucInfo['buy_city_id']};", $param));
			}
		}

		//返还物品
		$bErrLog = false;
		switch ($aucInfo['goods_type']) {
			case M_Auction::GOODS_HERO:
				$bErrLog = M_Auction::fetchSelfAuctionHero($cityId, $aucInfo['goods_id']);
				break;
			case M_Auction::GOODS_DRAW:
				$bErrLog = M_Auction::fetchSelfAuctionProps($cityId, $aucInfo['goods_id']);
				break;
			case M_Auction::GOODS_EQUI:
				$bErrLog = M_Auction::fetchSelfAuctionEquip($cityId, $aucInfo['goods_id']);
				break;
			default:
				break;
		}

		if ($bErrLog) {
			$errNo = '';
		} else {
			Logger::auction(array(__METHOD__, "aucId#{$aucId};goodsId#{$aucInfo['goods_id']};cityId#{$cityId};goodsType#{$aucInfo['goods_type']}"));
			$errNo = T_ErrNo::ERR_UPDATE;
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 取回拍卖结束的物品(未上架、拍卖过期)[未托管过期][卖家操作]
	 * @author chenhui on 20120214
	 * @param int $aucId 交易ID
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public
	function ARetrieve($aucId) {
		//操作结果默认为失败0

		$aucId = intval($aucId);
		if ($aucId <= 0) {
			return B_Common::result(T_ErrNo::ERR_PARAM);
		}
		$data      = array(); //返回数据默认为空数组
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		$cityId      = intval($cityInfo['id']);
		$aucInfo     = M_Auction::getAucInfo($aucId);
		$nowTime     = time();
		$propsNumArr = $objPlayer->Pack()->hasNum();

		if ($cityInfo['mil_medal'] < M_Auction::USE_MIN) {
			return B_Common::result(T_ErrNo::NO_ENOUGH_MILMEDAL);
		} else if (empty($aucInfo['id'])) {
			return B_Common::result(T_ErrNo::AUC_DATA_ERR);
		} else if ($aucInfo['goods_type'] == M_Auction::GOODS_HERO && M_Hero::isHeroNumFull($cityId)) {
			return B_Common::result(T_ErrNo::HERO_NUM_FULL_FAIL);
		} else if ($aucInfo['goods_type'] == M_Auction::GOODS_EQUI && M_Equip::isEquipNumFull($cityId, $cityInfo['vip_level'])) {
			return B_Common::result(T_ErrNo::EQUI_NUM_FULL);
		} else if ($aucInfo['goods_type'] == M_Auction::GOODS_DRAW && $propsNumArr['draw']['full']) {
			return B_Common::result(T_ErrNo::DRAW_NUM_FULL);
		} else if (!in_array($aucInfo['auction_status'], array(M_Auction::STATUS_FAIL)) || $aucInfo['ol_time'] < $nowTime) {
			return B_Common::result(T_ErrNo::AUC_NOT_FAIL);
		} else if ($aucInfo['sale_city_id'] != $cityId) {
			return B_Common::result(T_ErrNo::AUC_NOT_SELF);
		} else if ($aucInfo['keep_expired'] < $nowTime) {
			return B_Common::result(T_ErrNo::AUC_KEEP_EXPIRED);
		}

		$shift_type = ($aucInfo['ol_time'] < $nowTime) ? M_Auction::SHIFT_AUC_OFF_SALE : M_Auction::SHIFT_KEEP_ING_SALE;
		$newInfo    = array(
			'shift_at'       => $nowTime,
			'shift_type'     => $shift_type,
			'auction_status' => M_Auction::STATUS_DEL,
		);
		$bUp        = M_Auction::updateAucInfo($aucId, $newInfo, true);
		if (!$bUp) {
			return B_Common::result(T_ErrNo::ERR_UPDATE);
		}

		M_Auction::delCityAucList($aucInfo['sale_city_id'], $aucId);
		$bErrLog = false;
		//返还物品
		switch ($aucInfo['goods_type']) {
			case M_Auction::GOODS_HERO:
				$bErrLog = M_Auction::fetchSelfAuctionHero($objPlayer, $aucInfo['goods_id']);
				break;
			case M_Auction::GOODS_DRAW:
				$bErrLog = M_Auction::fetchSelfAuctionProps($objPlayer, $aucInfo['goods_id']);
				break;
			case M_Auction::GOODS_EQUI:
				$bErrLog = M_Auction::fetchSelfAuctionEquip($objPlayer, $aucInfo['goods_id']);
				break;
			default:
				break;
		}
		if ($bErrLog) {
			$errNo = '';

		} else {
			Logger::auction(array(__METHOD__, "aucId#{$aucId};goodsId#{$aucInfo['goods_id']};cityId#{$cityId};goodsType#{$aucInfo['goods_type']}"));
			$errNo = T_ErrNo::ERR_UPDATE;
		}
		return B_Common::result($errNo, $data);
	}

}

?>
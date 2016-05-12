<?php

/** 拍卖模块 */
class M_Auction {
	/** 排序类型 */
	static $sort_type = array(
		1 => 'ASC', //升序
		2 => 'DESC', //降序
	);

	/** 拍卖物品 军官(玩家已有军官) */
	const GOODS_HERO = 1;
	/** 拍卖物品 武器图纸(玩家已有道具) */
	const GOODS_DRAW = 2;
	/** 拍卖物品 装备(玩家已有装备) */
	const GOODS_EQUI = 3;
	/** 拍卖物品 道具 */
	const GOODS_PROPS = 4;
	/** 拍卖物品 材料 */
	const GOODS_STUFF = 5;

	/** 拍卖物品类型 */
	static $goods_type = array(
		self::GOODS_HERO  => '军官',
		self::GOODS_DRAW  => '图纸',
		self::GOODS_EQUI  => '装备',
		self::GOODS_PROPS => '道具',
		self::GOODS_STUFF => '材料',
	);

	/** 允许使用拍卖系统最低功勋值[2000] */
	const USE_MIN = 2000;
	/** 每次拍卖交易税率 */
	const TAX_RATE = 0.01;
	/** 每次拍卖交易税最低值 */
	const TAX_MIN = 2;
	/** 每次拍卖竞拍价格最低值 */
	const PRICE_START_MIN = 10;
	/** 每页拍卖中物品条数 */
	const ING_PAGE_SIZE = 9;
	/** 每个玩家允许最大挂单数 */
	const MAX_ORDER_SUM = 10;
	/** 拍卖系统可出现价格最大极限值 */
	const PRICE_LIMIT_MAX = 99999999;

	/** 第一种保管方式小时数 */
	const ONE_KEEP_HOUR = 24;
	/** 第二种保管方式小时数 */
	const TWO_KEEP_HOUR = 48;
	/** 系统托管天数[30] */
	const SYS_KEEP_DAY = 30;

	/** 第一种保管方式需要保管费数值 */
	const ONE_KEEP_COST = 10;
	/** 第二种保管方式需要保管费数值 */
	const TWO_KEEP_COST = 20;

	/** 保管类型 点券10 */
	const COUPON_ONE = 1;
	/** 保管类型 军饷10 */
	const MILPAY_ONE = 2;
	/** 保管类型 点券20 */
	const COUPON_TWO = 3;
	/** 保管类型 军饷20 */
	const MILPAY_TWO = 4;

	/** 保管类型 */
	static $KeepType = array( //暂弃[直接写在CAuction]
		M_Auction::COUPON_ONE => array(T_App::COUPON, M_Auction::ONE_KEEP_COST), //点券20
		M_Auction::MILPAY_ONE => array(T_App::MILPAY, M_Auction::ONE_KEEP_COST), //军饷10
		M_Auction::COUPON_TWO => array(T_App::COUPON, M_Auction::TWO_KEEP_COST), //点券40
		M_Auction::MILPAY_TWO => array(T_App::MILPAY, M_Auction::TWO_KEEP_COST), //军饷20
	);

	/** 拍卖状态 未上架 */
	const STATUS_OFF = 0;
	/** 拍卖状态 拍卖中 */
	const STATUS_ING = 1;
	/** 拍卖状态 拍卖失败托管中 */
	const STATUS_FAIL = 2;
	/** 拍卖状态 拍卖完成托管中 */
	const STATUS_SUCC = 3;
	/** 拍卖状态 删除 */
	const STATUS_DEL = 4;
	/** 拍卖状态类型 */
	static $status_type = array(
		self::STATUS_OFF  => '未上架',
		self::STATUS_ING  => '拍卖中',
		self::STATUS_FAIL => '拍卖失败托管中',
		self::STATUS_SUCC => '拍卖完成托管中',
		self::STATUS_DEL  => '删除',
	);

	/** 军官,装备等物品正在拍卖状态 */
	const GOODS_ON_SALE_YES = 1;
	/** 军官,装备等物品不在拍卖状态 */
	const GOODS_ON_SALE_NO = 0;

	/** 卖家出售后未上架前取回 */
	const SHIFT_AUC_OFF_SALE = 1;
	/** 卖家上架后正在拍卖中取回 */
	const SHIFT_AUC_ING_SALE = 2;
	/** 卖家拍卖过期托管未过期无人购买取回 */
	const SHIFT_KEEP_ING_SALE = 3;
	/** 卖家托管过期系统自动返给卖家 */
	const SHIFT_KEEP_EXP_BACK_SALE = 4;
	/** 卖家托管过期系统自动删除[数量已满] */
	const SHIFT_KEEP_EXP_DEL_SALE = 5;
	/** 买家已购买托管未过期的物品领取 */
	const SHIFT_KEEP_ING_BUY = 6;
	/** 买家托管过期系统自动返给买家 */
	const SHIFT_KEEP_EXP_BACK_BUY = 7;
	/** 买家托管过期系统自动删除[数量已满] */
	const SHIFT_KEEP_EXP_DEL_BUY = 8;
	/** 物品被卖出后最终转移类型 */
	static $shift_type = array(
		self::SHIFT_AUC_OFF_SALE       => '卖家出售后未上架前取回',
		self::SHIFT_AUC_ING_SALE       => '卖家上架后正在拍卖中取回',
		self::SHIFT_KEEP_ING_SALE      => '卖家拍卖过期托管未过期无人购买取回',
		self::SHIFT_KEEP_EXP_BACK_SALE => '卖家托管过期系统自动返给卖家',
		self::SHIFT_KEEP_EXP_DEL_SALE  => '卖家托管过期系统自动删除[数量已满]',
		self::SHIFT_KEEP_ING_BUY       => '买家已购买托管未过期的物品领取',
		self::SHIFT_KEEP_EXP_BACK_BUY  => '买家托管过期系统自动返给买家',
		self::SHIFT_KEEP_EXP_DEL_BUY   => '买家托管过期系统自动删除[数量已满]',
	);

	/**
	 * 获取拍卖出售时间和花费
	 * @author chenhui on 20120818
	 * @return array array(array(24, 10, 20), array(48, 20, 40));
	 */
	static public function getAucSaleConf() {
		$ret = M_Config::getVal('auc_time_cost');
		if (empty($ret)) {
			$ret = array(array(24, 10, 10), array(48, 20, 10)); //小时数,军饷数,点券数
		}
		return $ret;
	}


	static public function isGoodsType($type) {
		return isset(M_Auction::$goods_type[$type]) ? true : false;
	}
	/**
	 * 插入一条拍卖数据
	 * @author chenhui on 20120106
	 * @param array $info 键值对数组
	 * @return int/false
	 */
	static public function insert($info) {
		$aucId = false;
		if (!empty($info) && is_array($info)) {
			$aucId = intval(B_DB::instance('Auction')->insert($info));
			if ($aucId > 0) {
				self::updateAucInfo($aucId, $info);
			}
		}
		return $aucId;
	}

	/**
	 * 新增拍卖ID至玩家拍卖物品列表
	 * @author chenhui on 20120119
	 * @param int $cityId 城市ID
	 * @param int $aucId 拍卖ID
	 */
	static public function addCityAucList($cityId, $aucId) {
		$rc = new B_Cache_RC(T_Key::AUCTION_LIST, $cityId);
		return $rc->sadd($aucId);
	}

	/**
	 * 玩家拍卖物品列表挂单数
	 * @author chenhui on 20120219
	 * @param int $cityId 城市ID
	 * @return int 数量
	 */
	static public function getCityAucListSum($cityId) {
		$rc  = new B_Cache_RC(T_Key::AUCTION_LIST, $cityId);
		$sum = intval($rc->scard());
		return $sum;
	}

	/**
	 * 删除拍卖ID从玩家拍卖物品列表
	 * @author chenhui on 20120119
	 * @param int $cityId 城市ID
	 * @param int $aucId 拍卖ID
	 */
	static public function delCityAucList($cityId, $aucId) {
		$ret = false;
		$rc  = new B_Cache_RC(T_Key::AUCTION_LIST, $cityId);
		if ($rc->sismember($aucId)) {
			$ret = $rc->srem($aucId);
			if (!$ret) {
				Logger::error(array(__METHOD__, 'Err Remove', func_get_args()));
			}
		} else {
			$ret = true;
		}

		return $ret;
	}

	/**
	 * 获取城市拍卖行记录
	 * @param unknown_type $cityId
	 */
	static public function getCityAucList($cityId) {
		$rc = new B_Cache_RC(T_Key::AUCTION_LIST, $cityId);
		$rc->delete();
		$ret = $rc->smembers();
		//$ret = false;
		if (empty($ret)) {
			$saleList = B_DB::instance('Auction')->getSaleList($cityId);
			$buyList  = B_DB::instance('Auction')->getBuyList($cityId);
			$saleList = is_array($saleList) ? $saleList : array();
			$buyList  = is_array($buyList) ? $buyList : array();

			$ret = array_merge($saleList, $buyList);
			if (!empty($ret)) {
				foreach ($ret as $goodsId) {
					$rc->sadd($goodsId);
				}
			}

		}
		return $ret;
	}

	/**
	 * 构建前端数据
	 * @param array $aucInfo
	 * @return array
	 */
	static public function buildFrontData($aucInfo) {
		$arrDetail = array();
		if (M_Auction::GOODS_HERO == $aucInfo['goods_type']) {
			$heroInfo = M_Hero::getHeroInfo($aucInfo['goods_id']);
			if (!empty($heroInfo['id'])) {
				$heroArmyNumAdd = M_Hero::heroArmyNumAdd($heroInfo['city_id'], 0);
				$arrDetail      = array(
					'HeroId'           => $aucInfo['goods_id'],
					'CityId'           => $heroInfo['city_id'],
					'NickName'         => $heroInfo['nickname'],
					'Gender'           => $heroInfo['gender'],
					'Quality'          => $heroInfo['quality'],
					'Level'            => $heroInfo['level'],
					'FaceId'           => $heroInfo['face_id'],
					'IsLegend'         => 1,
					'Exp'              => $heroInfo['exp'],
					'ExpNext'          => M_Formula::getGrowExp($heroInfo['level']),
					'AttrLead'         => $heroInfo['attr_lead'],
					'AttrCommand'      => $heroInfo['attr_command'],
					'AttrMilitary'     => $heroInfo['attr_military'],
					'AttrEnergy'       => $heroInfo['attr_energy'],
					'TrainingLead'     => $heroInfo['training_lead'],
					'TrainingCommand'  => $heroInfo['training_command'],
					'TrainingMilitary' => $heroInfo['training_military'],
					'SkillLead'        => $heroInfo['skill_lead'],
					'SkillCommand'     => $heroInfo['skill_command'],
					'SkillMilitary'    => $heroInfo['skill_military'],
					'SkillEnergy'      => $heroInfo['skill_energy'],
					'AttrMood'         => $heroInfo['attr_mood'],
					'StatPoint'        => floor($heroInfo['stat_point']),
					'GrowRate'         => $heroInfo['grow_rate'],
					'MaxArmyNum'       => M_Formula::calcHeroMaxArmyNum($heroInfo['level'], $heroInfo['skill_army_num'], $heroArmyNumAdd),
					'EquipArm'         => $heroInfo['equip_arm'],
					'EquipCap'         => $heroInfo['equip_cap'],
					'EquipUniform'     => $heroInfo['equip_uniform'],
					'EquipMedal'       => $heroInfo['equip_medal'],
					'EquipShoes'       => $heroInfo['equip_shoes'],
					'EquipSit'         => $heroInfo['equip_sit'],
					'SkillSlotNum'     => $heroInfo['skill_slot_num'],
					'SkillSlot'        => $heroInfo['skill_slot'] ? $heroInfo['skill_slot'] : 0,
					'SkillSlot1'       => $heroInfo['skill_slot_1'] ? $heroInfo['skill_slot_1'] : 0,
					'SkillSlot2'       => $heroInfo['skill_slot_2'] ? $heroInfo['skill_slot_2'] : 0,
					'WinNum'           => $heroInfo['win_num'],
					'DrawNum'          => $heroInfo['draw_num'],
					'FailNum'          => $heroInfo['fail_num'],
					'RelifeTime'       => $heroInfo['relife_time'],
					'Fight'            => $heroInfo['fight'],
					'Flag'             => $heroInfo['flag'],
					'ArmyNum'          => $heroInfo['army_num'],
					'ArmyId'           => $heroInfo['army_id'],
					'WeaponId'         => $heroInfo['weapon_id'],
					'FillFlag'         => $heroInfo['fill_flag'],
					'Recycle'          => $heroInfo['recycle'],
				);
			}
		} else if (M_Auction::GOODS_EQUI == $aucInfo['goods_type']) {
			$equiInfo = M_Equip::getInfo($aucInfo['goods_id']);
			if (!empty($equiInfo)) {
				$arrDetail = array(
					'Id'           => $aucInfo['goods_id'],
					'Name'         => $equiInfo['name'],
					'Pos'          => $equiInfo['pos'],
					'FaceId'       => $equiInfo['face_id'],
					'NeedLevel'    => $equiInfo['need_level'],
					'Level'        => $equiInfo['level'],
					'MaxLevel'     => $equiInfo['max_level'],
					'Quality'      => $equiInfo['quality'],
					'BaseLead'     => $equiInfo['base_lead'],
					'BaseCommand'  => $equiInfo['base_command'],
					'BaseMilitary' => $equiInfo['base_military'],
					'IsLocked'     => $equiInfo['is_locked'],
					'ExtAttrName'  => $equiInfo['ext_attr_name'],
					'ExtAttrRate'  => $equiInfo['ext_attr_rate'],
					'ExtAttrSkill' => $equiInfo['ext_attr_skill'],
					'IsUse'        => $equiInfo['is_use'],
					'SuitId'       => $equiInfo['suit_id'],
					'Desc1'        => $equiInfo['desc_1'],
					'Desc2'        => $equiInfo['desc_2'],
					'CreateAt'     => $equiInfo['create_at'],
					'Flag'         => isset($equiInfo['flag']) ? $equiInfo['flag'] : 7,
				);
			}
		}
		return $arrDetail;
	}

	/**
	 * 获取一条拍卖数据
	 * @author chenhui on 20120117
	 * @param int $aucId 交易ID
	 * @return array/false
	 */
	static public function getAucInfo($aucId) {
		$ret = false;
		if ($aucId > 0) {
			$rc  = new B_Cache_RC(T_Key::AUCTION_DETAIL, $aucId);
			$ret = $rc->hmget(T_DBField::$auctionFields);
			if (empty($ret['id'])) {
				$ret = B_DB::instance('Auction')->get($aucId);
				if (!empty($ret)) {
					$ret = $rc->hmset($ret, T_App::ONE_DAY);
					if (!$ret) {
						Logger::error(array(__METHOD__, $ret, func_get_args()));
					}
				}
			}
		}
		return $ret;
	}

	/**
	 * 更新拍卖行单个交易数据
	 * @author chenhui on 20120109
	 * @param int $aucId 交易ID
	 * @param array $fieldArr 需要更新的交易数据字段数组
	 * @param bool $isUp 是否更新到DB
	 * @return array
	 */
	static public function updateAucInfo($aucId, $fieldArr, $isUp = true) {
		$ret = false;
		if (!empty($aucId) && !empty($fieldArr) && is_array($fieldArr)) {
			$info = array();
			foreach ($fieldArr as $key => $val) {
				if (!empty($key) && in_array($key, T_DBField::$auctionFields)) {
					$info[$key] = $val;
				}
			}

			if (!empty($info)) {
				$rc  = new B_Cache_RC(T_Key::AUCTION_DETAIL, $aucId);
				$ret = $rc->hmset($info, T_App::ONE_DAY);
				if ($ret) {
					$isUp && B_DB::instance('Auction')->update($info, $aucId);
				} else {
					Logger::error(array(__METHOD__, $info, func_get_args()));
				}
			}

		}

		return $ret ? $info : false;
	}

	/**
	 * 获取当前正在拍卖状态的拍卖数据
	 * @author chenhui on 20120217
	 * @param int $goodsType 物品类型[军官1 图纸2 装备3]
	 * @param int $secVal 物品类型下的小类型(因大类型不同而不同)
	 * @param string $sortField 排序字段[id,goods_name,goods_type,quality,pos,price_only]
	 * @param int $sortT 排序类型(1升序、2降序)
	 * @param int $pageNo 当前页码
	 * @return array()/array(array(id)...)
	 */
	static public function getAucInfoIng($goodsType, $secVal, $sortField, $sortT, $pageNo) {
		$ret        = array();
		$sortType   = self::$sort_type[$sortT];
		$rowStart   = ($pageNo - 1) * self::ING_PAGE_SIZE;
		$arrAucInfo = B_DB::instance('Auction')->getAucInfoIng($goodsType, $secVal, $sortField, $sortType, $rowStart);
		if (!empty($arrAucInfo) && is_array($arrAucInfo)) {
			$ret = $arrAucInfo;
		}
		return $ret;
	}

	/**
	 * 根据物品名字模糊查询获取正在拍卖的数据
	 * @author chenhui on 20120228
	 * @param string $goodsName 名字关键字
	 * @param int $pageNo 页码
	 * @return array 拍卖ID数组 2D
	 */
	static public function getAucListByName($goodsName, $pageNo) {
		$ret = array();
		if (!empty($goodsName)) {
			$rowStart   = ($pageNo - 1) * self::ING_PAGE_SIZE;
			$arrAucInfo = B_DB::instance('Auction')->getAucListByName($goodsName, $rowStart);
			if (!empty($arrAucInfo) && is_array($arrAucInfo)) {
				$ret = $arrAucInfo;
			}
		}
		return $ret;
	}

	/**
	 * 根据物品类型获取拍卖总条数
	 * @author chenhui on 20120219
	 * @param int $goodsType 物品类型
	 * @return int 数量
	 */
	static public function getAucOlSum($goodsType, $secVal) {
		$goodsType = (!empty($goodsType) && isset(self::$goods_type[$goodsType])) ? intval($goodsType) : '';
		$num       = B_DB::instance('Auction')->getAucOlSum($goodsType, $secVal);
		return $num;
	}

	/**
	 * 根据物品名字模糊查询获取拍卖总条数
	 * @author chenhui on 20120228
	 * @param string $goodsName
	 * @return int 总条数
	 */
	static public function totalAucListByName($goodsName) {
		$num = B_DB::instance('Auction')->totalAucListByName($goodsName);
		return $num;
	}



	/**
	 * 获取某城市拍卖系统中正在出售和已购买某类型物品的总数量
	 * @author chenhui on 20120315
	 * @param int $cityId 城市ID
	 * @param int $goodsType 物品类型:军官1 图纸2 装备3
	 * @return int 数量
	 */
	static public function getAuctionGoodsNum($cityId, $goodsType) {
		$ret       = 0;
		$goodsType = intval($goodsType);
		$arrId     = self::getCityAucList($cityId);
		if (!empty($arrId) && is_array($arrId) && isset(self::$goods_type[$goodsType])) {
			foreach ($arrId as $aucId) {
				$aucInfo = M_Auction::getAucInfo($aucId);
				($aucInfo['goods_type'] == $goodsType) && $ret++;
			}
		}
		return $ret;
	}


	/** 拍卖模块定时任务 */
	static public function updateAucInfoTimer() {
		$nowtime  = time();
		$saleConf = M_Auction::getAucSaleConf();

		//拍卖到期有人竞价则拍卖成功托管，否则拍卖失败托管
		$arrAucExpInfo = B_DB::instance('Auction')->getAucExpiredInfo();
		if (!empty($arrAucExpInfo) && is_array($arrAucExpInfo)) {
			$arrId = array();
			foreach ($arrAucExpInfo as $aucExpInfo) {
				$buyCityId = intval($aucExpInfo['buy_city_id']);

				$priceSucc = 0; //成交价
				if ($buyCityId > 0) //拍卖成功
				{

					$priceSucc = $aucExpInfo['price_new'];

					//返还卖家保管费
					$keepBackCoupon = 0;
					$keepBackMilpay = 0;
					$keepMoneyType  = 2; //默认点券
					$getPay         = array();
					switch ($aucExpInfo['keep_type']) {
						case self::COUPON_ONE:
							$getPay['coupon'] = $saleConf[0][2];
							$keepBackCoupon   = $saleConf[0][2];
							break;
						case self::MILPAY_ONE:
							$getPay['milpay'] = $saleConf[0][1];
							$keepBackMilpay   = $saleConf[0][1];
							$keepMoneyType    = 1;
							break;
						case self::COUPON_TWO:
							$getPay['coupon'] = $saleConf[1][2];
							$keepBackCoupon   = $saleConf[1][2];
							break;
						case self::MILPAY_TWO:
							$getPay['milpay'] = $saleConf[1][1];
							$keepBackMilpay   = $saleConf[1][1];
							$keepMoneyType    = 1;
							break;
						default:
							break;
					}
					$numBack  = max($keepBackCoupon, $keepBackMilpay);
					$keepBack = array(T_Lang::$PAY_TYPE[$keepMoneyType]); //返还保管费通知

					$stats = self::STATUS_SUCC;
					//扣税后给卖家加军饷
					$tax              = max(self::TAX_MIN, floor($priceSucc * self::TAX_RATE));
					$income           = max(0, $priceSucc - $tax);
					$getPay['milpay'] = isset($getPay['milpay']) ? $getPay['milpay'] + $income : $income;
					//拍卖成功后获得收益

					$objPlayerSale = new O_Player($aucExpInfo['sale_city_id']);
					$objPlayerSale->City()->mil_pay += $getPay;
					$objPlayerSale->Log()->income(T_App::MILPAY, $getPay, B_Log_Trade::I_AucSale);
					$objPlayerSale->save();

					M_Auction::delCityAucList($aucExpInfo['sale_city_id'], $aucExpInfo['id']); //删除卖家[出售管理]中拍卖ID
					M_Auction::addCityAucList($aucExpInfo['buy_city_id'], $aucExpInfo['id']); //添加交易ID到玩家购买列表

					//拍卖成功通知买家
					$contentBuy = array(T_Lang::C_AUC_BUY_SUCC, $aucExpInfo['goods_name'], $priceSucc);
					M_Message::sendSysMessage($buyCityId, json_encode(array(T_Lang::T_AUC_TIP)), json_encode($contentBuy));

					//拍卖成功通知卖家
					$contentSale = array(T_Lang::C_AUC_SALE_SUCC, $aucExpInfo['goods_name'], $priceSucc, $tax, $numBack, $keepBack, $income + $keepBackMilpay, $keepBackCoupon);
					M_Message::sendSysMessage($aucExpInfo['sale_city_id'], json_encode(array(T_Lang::T_AUC_TIP)), json_encode($contentSale));

					//给物品设置新的city_id
					$goodsId = intval($aucExpInfo['goods_id']);

					if ($aucExpInfo['goods_type'] == M_Auction::GOODS_HERO) {
						Logger::opHero($aucExpInfo['sale_city_id'], $goodsId, Logger::H_ACT_SELLOUT, $priceSucc);
						Logger::opHero($buyCityId, $goodsId, Logger::H_ACT_BUY, $priceSucc);
					} elseif ($aucExpInfo['goods_type'] == M_Auction::GOODS_EQUI) {
						Logger::opEquip($aucExpInfo['sale_city_id'], $goodsId, Logger::E_ACT_SELLOUT, $priceSucc);
						Logger::opEquip($buyCityId, $goodsId, Logger::H_ACT_BUY, $priceSucc);
					}
				} else //拍卖失败
				{
					$stats = self::STATUS_FAIL; //拍卖状态

					//计算卖家保管费
					$keepBackCoupon = 0;
					$keepBackMilpay = 0;
					$keepMoneyType  = 2; //默认点券
					switch ($aucExpInfo['keep_type']) {
						case self::COUPON_ONE:
							$keepBackCoupon = $saleConf[0][2];
							break;
						case self::MILPAY_ONE:
							$keepBackMilpay = $saleConf[0][1];
							$keepMoneyType  = 1;
							break;
						case self::COUPON_TWO:
							$keepBackCoupon = $saleConf[1][2];
							break;
						case self::MILPAY_TWO:
							$keepBackMilpay = $saleConf[1][1];
							$keepMoneyType  = 1;
							break;
						default:
							break;
					}
					$numBack  = max($keepBackCoupon, $keepBackMilpay);
					$keepBack = array(T_Lang::$PAY_TYPE[$keepMoneyType]); //保管费


					//拍卖失败通知卖家
					$contentSale = array(T_Lang::C_AUC_SALE_FAIL, $aucExpInfo['goods_name'], $numBack, $keepBack);
					M_Message::sendSysMessage($aucExpInfo['sale_city_id'], json_encode(array(T_Lang::T_AUC_TIP)), json_encode($contentSale));
				}

				//更新缓存和DB
				$keepExpired = time() + T_App::ONE_DAY * self::SYS_KEEP_DAY;
				$updInfo     = array('price_succ' => $priceSucc, 'auction_status' => $stats, 'keep_expired' => $keepExpired);
				self::updateAucInfo($aucExpInfo['id'], $updInfo, true);
				$arrId[] = $aucExpInfo['id'];
			}
		}

		//托管过期则返还物品给卖[买]家后直接删除数据(删缓存、DB设为删除状态)
		$arrKeepExpInfo = B_DB::instance('Auction')->getKeepExpiredInfo();
		if (!empty($arrKeepExpInfo) && is_array($arrKeepExpInfo)) {
			foreach ($arrKeepExpInfo as $aucInfo) {
				$back2CityId = ($aucInfo['auction_status'] == self::STATUS_FAIL) ? intval($aucInfo['sale_city_id']) : intval($aucInfo['buy_city_id']); //返到CityId

				//删掉列表中的 物品ID
				M_Auction::delCityAucList($back2CityId, $aucInfo['id']);
				M_Auction::updateAucInfo($aucInfo['id'], array('shift_at' => $nowtime, 'auction_status' => self::STATUS_DEL), true);
			}
		}
		return true;
	}


	/**
	 * 更新拍卖中的英雄状态(还给玩家英雄)
	 * @param int $cityId
	 * @param int $goodsId
	 * @return bool
	 */
	static public function fetchSelfAuctionHero($cityId, $goodsId) {
		$heroInfo = M_Hero::getHeroInfo($goodsId);

		$params = array('city_id' => $cityId, 'on_sale' => M_Auction::GOODS_ON_SALE_NO, 'flag' => T_Hero::FLAG_FREE);
		$ret    = M_Hero::setHeroInfo($goodsId, $params);
		if ($ret) {
			M_Hero::setCityHeroList($cityId, $goodsId);

			$cityInfo       = M_City::getInfo($cityId);
			$heroArmyNumAdd = M_Hero::heroArmyNumAdd($cityId, $cityInfo['union_id']);
			//Logger::debug(array(__METHOD__, $cityId, $cityInfo['union_id'], $heroArmyNumAdd));
			$recycleCfgArr            = M_Hero::getHeroRecycle();
			$nextRecycleLv            = $heroInfo['recycle'] + 1;
			$nextRecycleArr           = isset($recycleCfgArr[$nextRecycleLv]) ? $recycleCfgArr[$nextRecycleLv] : array();
			$heroInfo['city_id']      = $params['city_id'];
			$heroInfo['on_sale']      = $params['on_sale'];
			$heroInfo['flag']         = $params['flag'];
			$heroInfo['max_army_num'] = M_Formula::calcHeroMaxArmyNum($heroInfo['level'], $heroInfo['skill_army_num'], $heroArmyNumAdd); //计算各兵种最大带兵数
			$heroInfo['exp_next']     = M_Formula::getGrowExp($heroInfo['level']);
			$heroInfo['recycle_next'] = $nextRecycleArr;

			Logger::opHero($cityId, $goodsId, Logger::H_ACT_RECEIVE, $heroInfo['nickname']);

			M_Sync::addQueue($cityId, M_Sync::KEY_HERO, array($goodsId => $heroInfo)); //同步数据
		} else {
			Logger::auction(array(__METHOD__, "cityId#{$cityId};goodsId#{$goodsId}", $params));
		}

		return $ret;
	}

	/**
	 * 更新拍卖中的道具状态(还给玩家道具)
	 * @param int $cityId
	 * @param int $goodsId
	 * @return bool
	 */
	static public function fetchSelfAuctionProps($objPlayer, $goodsId) {
		$cityId = $objPlayer->City()->id;
		$ret = $objPlayer->Pack()->incr($goodsId, 1);
		if (!$ret) {
			Logger::auction(array(__METHOD__, "cityId#{$cityId};goodsId#{$goodsId}"));
		}
		return $ret;
	}

	/**
	 * 更新拍卖中的装备状态(还给玩家装备)
	 * @param int $cityId
	 * @param int $goodsId
	 * @return bool
	 */
	static public function fetchSelfAuctionEquip($cityId, $goodsId) {
		$equiInfo = M_Equip::getInfo($goodsId);

		$params = array('city_id' => $cityId, 'on_sale' => M_Auction::GOODS_ON_SALE_NO, 'is_use' => T_Equip::EQUIP_NOT_USE);
		$ret    = M_Equip::setInfo($goodsId, $params);

		if ($ret) {
			M_Equip::incrCityEquipNum($cityId, 1);
			M_Equip::setCityEquipList($cityId, $goodsId);
			$equiInfo['city_id']      = $params['city_id'];
			$equiInfo['on_sale']      = $params['on_sale'];
			$equiInfo['is_use']       = $params['is_use'];
			$equiInfo['hero_name']    = '';
			$equiInfo['hero_quality'] = '';
			$equiInfo['_0']           = M_Sync::ADD;
			M_Sync::addQueue($cityId, M_Sync::KEY_EQUIP, array($goodsId => $equiInfo)); //同步数据

			Logger::opEquip($cityId, $goodsId, Logger::E_ACT_RECEIVE, $equiInfo['name']);
		} else {
			Logger::auction(array(__METHOD__, "cityId#{$cityId};goodsId#{$goodsId}", $params));
		}
		return $ret;
	}
}

?>
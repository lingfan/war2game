<?php

/**
 * 军饷流水账
 *
 */
class B_Log_Trade {
	/** 消费类型 (修改此处 记得同步 T_Word::$EXPENSE_TYPE) */
	/**  1清除建筑CD时间 */
	const E_ClearBuildCD = 1;
	/**  2清除科技CD时间 */
	const E_ClearTechCD = 2;
	/**  3清除武器CD时间 */
	const E_ClearWeaponCD = 3;
	/**  4军官更名  */
	const E_UpHeroName = 4;
	/**  5军官洗点  */
	const E_WashHeroAttr = 5;
	/**  6寻将  */
	const E_FindHero = 6;
	/**  7开启武器槽  */
	const E_OpenWeaponSlot = 7;
	/**  8军官复活  */
	const E_ResurrectHero = 8;
	/**  9增加建筑队列  */
	const E_AddBuildQueue = 9;
	/**  10遗忘技能  */
	const E_ForgetSkill = 10;
	/**  11购买道具   */
	const E_BuyProps = 11;
	/**  12刷新学院  */
	const E_RefreshHero = 12;
	/**  13 VIP购买活力  */
	const E_BuyVitality = 13;
	/**  14购买VIP资源包  */
	const E_BuyVipProps = 14;
	/**  15购买VIP功能  */
	const E_BuyVipFunction = 15;
	/**  16VIP减少出征时间  */
	const E_ReductionMarchTime = 16;
	/**  17军团升级  */
	const E_UpUnionLevel = 17;
	/**  18学习技能  */
	const E_LearnSkill = 18;
	/**  19拍卖行出售扣保管费  */
	const E_AuctionSale = 19;
	/**  20拍卖行购买一口价物品  */
	const E_AuctionBuy = 20;
	/**  21拍卖行竞价  */
	const E_AucPriceNew = 21;
	/**  22玩家改名  */
	const E_UpCityName = 22;
	/**  23 VIP购买军令  */
	const E_BuyMilOrder = 23;
	/**  24 清除副本CD时间  */
	const E_ClearFBCD = 24;
	/**  25 购买野地权限  */
	const E_BuyColony = 25;
	/**  26 装备升级  */
	const E_UpEquipNeedLevel = 26;
	/**  27 培养军官  */
	const E_TrainingHero = 27;
	/**  28 全服广播  */
	const E_Radio = 28;
	/**  29 尝试招募  */
	const E_SEEKHERO = 29;
	/**  30抽奖  */
	const E_Lottery = 30;
	/**  31购买商品   */
	const E_BuyMall = 31;
	/**  32军官兑换   */
	const HERO_EXCHANGE = 32;
	/**  33清除解救CD时间  */
	const E_ClearRescueCD = 33;
	/**  34购买突围次数  */
	const E_BuyBoutTimes = 34;
	/**  35购买多人副本次数  */
	const E_BuyMultiFBTimes = 35;
	/**  36购买多人副本加成  */
	const E_BuyMultiFBAddition = 36;
	/**  37 清除突围CD时间  */
	const E_ClearBoutCD = 37;
	/**  38 跑马系统投注  */
	const E_BettingHorse = 38;
	/**  39 跑马系统打气  */
	const E_EncourHorse = 39;
	/** 40 兑换 */
	const E_Exchange = 40;
	/** 41 租借武器 */
	const E_RentWeapon = 41;
	/** 42 清除爬楼CD时间  */
	const E_ClearFloorCD = 42;

	/** 收入类型  */
	/** 1 充值 [充值订单]  */
	const I_Pay = 1;
	/** 2 赠送 [谁从后台赠送 ]  */
	const I_Give = 2;
	/** 3 道具 [使用什么道具]  */
	const I_Prop = 3;
	/** 4 掉落 [攻打什么NPC掉落]  */
	const I_Drop = 4;
	/** 5 任务 [奖励]  */
	const I_Task = 5;
	/** 6 拍卖 [竞价失败返还]  */
	const I_AucBack = 6;
	/** 7 拍卖 [售出物品收益]  */
	const I_AucSale = 7;
	/** 8 拍卖 [保管费返还]  */
	const I_AucKeep = 8;
	/** 9 探索  */
	const I_Probe = 9;
	/** 10 军衔专属奖励  */
	const I_RankOnce = 10;
	/** 11 军衔每日奖励  */
	const I_RankDaily = 11;
	/** 12 抽奖获取  */
	const I_Lottery = 12;
	/** 13 跑马赢取奖励  */
	const I_HorseBack = 13;
	/** 14 跑马第一名奖励  */
	const I_HorseFirst = 14;
	/** 15 爬楼  */
	const I_FLOOR = 15;

	/** 收入 */
	const TYPE_INCOME = 1;
	/** 支出 */
	const TYPE_EXPENSE = 2;

	/**
	 * 添加日志
	 * @author huwei
	 * @param int $type 日志类型
	 * @param array $data 键值对
	 * @return bool
	 */
	static public function add($type, $data) {
		$now = time();
		$ret = false;
		if (!empty($type) && !empty($data)) {
			$params = array(
				'city_id' => $data['city_id'],
				'consumer_id' => $data['consumer_id'],
				'pay_action' => $data['pay_action'],
				'milpay' => $data['milpay'],
				'left_milpay' => $data['left_milpay'],
				'coupon' => $data['coupon'],
				'left_coupon' => $data['left_coupon'],
				'server_id' => $data['server_id'],
				'data' => $data['data'],
				'create_at' => $now,
			);

			if (B_Log_Trade::TYPE_INCOME == $type) {
				if ($data['pay_action'] == B_Log_Trade::I_Pay) {
					$incomeData = $params;
					$incomeData['order_no'] = isset($data['order_no']) ? $data['order_no'] : date('YmdHis'); //订单编号
					$incomeData['rmb'] = isset($data['rmb']) ? $data['rmb'] : ($data['milpay'] / 10); //新增RMB字段
					$incomeData['username'] = isset($data['username']) ? $data['username'] : '';
					$incomeData['username_ext'] = $data['username_ext'];
					$incomeData['total_milpay'] = $data['total_milpay'];

					$ret = B_DB::instance('StatsLogPay')->insert($incomeData);
				} else {
					$incomeData = $params;
					$ret = B_DB::instance('StatsLogIncome')->insert($incomeData);
				}
			} else if (B_Log_Trade::TYPE_EXPENSE == $type) {
				//统计记录消费动作
				$expenseData = $params;
				$expenseData['num'] = $data['num'];
				$ret = B_DB::instance('StatsLogExpense')->insert($expenseData);
			}
			if (!$ret) {
				Logger::error(array(__METHOD__, func_get_args()));
			}
			return $ret;
		}

	}


	static public function set(O_Player $objPlayer, $action, $costMilpay, $costCoupon, $num, $data = '') {
		if ($objPlayer->City()->id && $action) {
			//军饷支出流水账
			$logData = array(
				'city_id' => $objPlayer->City()->id,
				'server_id' => str_replace('s', '', SERVER_NO),
				'consumer_id' => $objPlayer->City()->consumer_id,
				'pay_action' => $action,
				'milpay' => $costMilpay,
				'left_milpay' => $objPlayer->City()->mil_pay,
				'coupon' => $costCoupon,
				'left_coupon' => $objPlayer->City()->coupon,
				'num' => $num,
				'data' => $data,
				'gold' => 0,
			);
			B_Log_Trade::add(B_Log_Trade::TYPE_EXPENSE, $logData);
		}

	}

}

?>
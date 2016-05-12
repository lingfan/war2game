<?php

/**
 * 同步信息队列
 */
class M_Sync {
	static $ownCityId = 0;
	/** 删除 */
	const DEL = 0;
	/** 添加 */
	const ADD = 1;
	/** 更新 */
	const SET = 2;

	/** 变量数据 array(变量类型=>数量,)  */
	//const KEY_VAR 			= 1;
	/** 建筑 array(建筑ID=>array(位置,等级)...)*/
	const KEY_BUILD = 2;
	/** 科技 array(科技ID=>等级)*/
	const KEY_TECH = 3;
	/** 兵种 array(兵种ID=>array(数量,等级,经验)) */
	const KEY_ARMY = 4;
	/** 道具  array(道具ID=>array(0=>未绑定数量,1=>绑定数量))*/
	const KEY_ITEM_PROPS = 5;
	/** 道具效果 array(效果编号=>array(效果值,剩余时间)) */
	const KEY_PROPS_EFFECT = 6;
	/** 特殊武器 array(槽ID=>武器ID) */
	const KEY_WEAPON_SPECIAL = 7;
	/** 英雄 array(英雄ID=>array(属性=>数值))*/
	const KEY_HERO = 8;
	/** CD时间 array(时间类型=>array(array(剩余时间, 1可累加 0不可)...))*/
	const KEY_CDTIME = 9;
	/** 装备  array(装备ID=>array(属性))*/
	const KEY_EQUIP = 10;
	/** 城市数据 array(字段=>数量,) */
	const KEY_CITY_INFO = 11;
	/** 常规武器 array(武器ID=>1) */
	const KEY_WEAPON_NORMAL = 12;
	/** 战斗相关数据 */
	const KEY_WAR = 13;
	/** 资源相关数据 */
	const KEY_RES = 14;
	/** 任务相关数据 array(任务ID=>array(状态,单项进行次数)) */
	const KEY_TASK = 15;
	/** 行军结束 */
	const KEY_MARCH_END = 17;
	/** 行军数据同步  array('行军ID'=>数据)*/
	const KEY_MARCH_DATA = 19;
	/** 消息相关数据 */
	const KEY_MESSAGE = 20;
	/** 属地数据 */
	const KEY_COLONY = 21;
	/** 新手任务数据 */
	const KEY_QUEST = 22;
	/** 活动完成数据 */
	const KEY_ACTIVE = 23;
	/** 城市突围数据 */
	const KEY_BOUT = 24;
	/** 属地数据 */
	const KEY_CITY_COLONY = 25;
	/** 城市被占领数据 */
	const KEY_CITY_OCCUPIED = 26;
	/** 组队数据 */
	const KEY_TEAM = 27;
	/** 分享数据 */
	const KEY_QQ_SHARE = 28;

	/** 材料 */
	const KEY_ITEM_STUFF = 29;
	/** 图纸 */
	const KEY_ITEM_DRAW = 30;
	/** 临时武器 array(武器ID=>过期) */
	const KEY_WEAPON_TEMP = 31;
	/** 爬楼数据 */
	const KEY_FLOOR = 32;


	/** 城市数据类型 */
	static $typeCityInfo = array(
		'gold_grow'              => 'GoldGrow', //金钱总产量(每小时)
		'food_grow'              => 'FoodGrow', //粮食总产量
		'oil_grow'               => 'OilGrow', //石油总产量
		'gold_grow_base'         => 'GoldGrowBase', //金钱基础产量
		'food_grow_base'         => 'FoodGrowBase', //粮食基础产量
		'oil_grow_base'          => 'OilGrowBase', //石油基础产量
		//'gold_grow_build'	=> 'GoldGrowBuild',	//金钱建筑对产量加成值%
		//'food_grow_build'	=> 'FoodGrowBuild',	//粮食建筑对产量加成值%
		//'oil_grow_build'	=> 'OilGrowBuild',	//石油建筑对产量加成值%
		'gold_grow_tech'         => 'GoldGrowTech', //金钱科技对产量加成值%
		'food_grow_tech'         => 'FoodGrowTech', //粮食科技对产量加成值%
		'oil_grow_tech'          => 'OilGrowTech', //石油科技对产量加成值%
		'gold_grow_props'        => 'GoldGrowProps', //金钱道具对产量加成值%
		'food_grow_props'        => 'FoodGrowProps', //粮食道具对产量加成值%
		'oil_grow_props'         => 'OilGrowProps', //石油道具对产量加成值%
		'gold_grow_zone'         => 'GoldGrowZone', //金钱洲区域对产量加成值%
		'food_grow_zone'         => 'FoodGrowZone', //粮食洲区域对产量加成值%
		'oil_grow_zone'          => 'OilGrowZone', //石油洲区域对产量加成值%
		'gold_grow_vip'          => 'GoldGrowVip', //金钱VIP功能对产量加成值%
		'food_grow_vip'          => 'FoodGrowVip', //粮食VIP功能对产量加成值%
		'oil_grow_vip'           => 'OilGrowVip', //石油VIP功能对产量加成值%

		'pos_area'               => 'PosArea', //城外地图洲坐标
		'pos_x'                  => 'PosX', //城外地图X坐标
		'pos_y'                  => 'PosY', //城外地图Y坐标
		'cd_move_city'           => 'CDMoveCity', //迁城CD时间
		'mil_pay'                => 'MilPay', //军饷
		'milpay'                 => 'MilPay', //军饷
		'coupon'                 => 'Coupon', //点券
		'total_mil_pay'          => 'TotalMilPay', //累计充值军饷
		'vip_level'              => 'VipLevel', //VIP等级
		'vip_endtime'            => 'VipEndtime', //VIP到期时间
		'cur_people'             => 'CurPeople', //城市当前占用人口
		'max_people'             => 'MaxPeople', //城市最大人口
		'max_store'              => 'MaxStore', //仓库容量
		'energy'                 => 'Energy', //活力
		'energy_left_times'      => 'EnergyLeftTimes', //今日剩余活力购买次数
		'energy_next_need'       => 'EnergyNextNeed', //下次购买活力所需军饷数
		'mil_order'              => 'MilOrder', //军令
		'order_left_times'       => 'OrderLeftTimes', //今日剩余军令购买次数
		'order_next_need'        => 'OrderNextNeed', //下次购买军令所需军饷数
		'mil_rank_award'         => 'MilRankAward', //军衔领奖编号
		'mil_rank_daily'         => 'MilRankDaily',
		'mil_rank'               => 'MilRank', //军衔编号
		'renown'                 => 'Renown', //威望
		'mil_medal'              => 'MilMedal', //功勋

		'last_fb_no'             => 'LastFBNo', //最新副本编号
		'equip_strong_luck_pool' => 'LuckPoolVal', //幸运池数据
		'market_amount'          => 'MarketAmount', //市场交易限额
		'cd_build_num'           => 'CDBuildNum', //建筑CD队列数
		'cd_tech_num'            => 'CDTechNum', //科技CD队列数
		'nickname'               => 'NickName', //玩家名字
		'signature'              => 'Signature', //个性签名
		'hero_auto_fill'         => 'HeroAutoFill', //自动补兵[废弃]
		'level'                  => 'Level', //城市等级[1-5]
		'union_id'               => 'UnionId', //联盟ID
		'UnionName'              => 'UnionName', //联盟名字
		'OnlineAddup'            => 'OnlineAddup', //防沉迷在线累计
		'newbie'                 => 'Newbie', //新手模式
		//'ShowNewbeCard'		=> 'ShowNewbeCard',	//是否显示新手卡
		'UnreadMsgNum'           => 'UnreadMsgNum', //未读消息数量
		'IsMsgFull'              => 'IsMsgFull', //消息数量是否已满 0/1
		'EnemyNum'               => 'EnemyNum', //敌情消息数量
		'UnreadReportNum'        => 'UnreadReportNum', //战报未读数量
		'QQInviteFriendNum'      => 'QQInviteFriendNum', //QQ邀请朋友次数
		'vip_pack_date'          => 'CanReceVipPack', //是否可以领取VIP宝箱(1可以,0不可以)
		'BreakoutPoint'          => 'BreakoutPoint', //突围积分
		'Activeness'             => 'Activeness', //活跃度
		'ShowOncePay'            => 'ShowOncePay',
	);

	static $typeHeroInfo = array(
		'id'                => 'HeroId',
		'city_id'           => 'CityId',
		'nickname'          => 'NickName',
		'gender'            => 'Gender',
		'quality'           => 'Quality',
		'level'             => 'Level',
		'face_id'           => 'FaceId',
		'exp'               => 'Exp',
		'exp_next'          => 'ExpNext',
		'recycle'           => 'Recycle',
		'is_legend'         => 'IsLegend',
		'attr_lead'         => 'AttrLead',
		'attr_command'      => 'AttrCommand',
		'attr_military'     => 'AttrMilitary',

		'training_lead'     => 'TrainingLead',
		'training_command'  => 'TrainingCommand',
		'training_military' => 'TrainingMilitary',

		'attr_energy'       => 'AttrEnergy',
		'skill_lead'        => 'SkillLead',
		'skill_command'     => 'SkillCommand',
		'skill_military'    => 'SkillMilitary',
		'skill_energy'      => 'SkillEnergy',
		'equip_lead'        => 'EquipLead',
		'equip_command'     => 'EquipCommand',
		'equip_military'    => 'EquipMilitary',
		'attr_mood'         => 'AttrMood',
		'stat_point'        => 'StatPoint',
		'grow_rate'         => 'GrowRate',
		'equip_arm'         => 'EquipArm',
		'equip_cap'         => 'EquipCap',
		'equip_uniform'     => 'EquipUniform',
		'equip_medal'       => 'EquipMedal',
		'equip_shoes'       => 'EquipShoes',
		'equip_sit'         => 'EquipSit',
		'equip_exp'         => 'EquipExp',
		'skill_slot_num'    => 'SkillSlotNum',
		'skill_slot'        => 'SkillSlot',
		'skill_slot_1'      => 'SkillSlot1',
		'skill_slot_2'      => 'SkillSlot2',
		'win_num'           => 'WinNum',
		'draw_num'          => 'DrawNum',
		'fail_num'          => 'FailNum',
		'relife_time'       => 'RelifeTime',
		'fight'             => 'Fight',
		'flag'              => 'Flag',
		'weapon_id'         => 'WeaponId',
		'army_id'           => 'ArmyId',
		'army_num'          => 'ArmyNum',
		'max_army_num'      => 'MaxArmyNum',
		'fill_flag'         => 'FillFlag',
		'on_sale'           => 'OnSale',
		'recycle_next'      => 'RecycleNext',
		'in_team'           => 'InTeam',
	);

	static $typeEquipInfo = array(
		'_0'             => '_0',
		'id'             => 'Id',
		'equip_id'       => 'EquipId',
		'name'           => 'Name',
		'pos'            => 'Pos',
		'city_id'        => 'CityId',
		'face_id'        => 'FaceId',
		'need_level'     => 'NeedLevel',
		'level'          => 'Level',
		'max_level'      => 'MaxLevel',
		'quality'        => 'Quality',
		'base_lead'      => 'BaseLead',
		'base_command'   => 'BaseCommand',
		'base_military'  => 'BaseMilitary',
		'is_locked'      => 'IsLocked',
		'ext_attr_name'  => 'ExtAttrName',
		'ext_attr_rate'  => 'ExtAttrRate',
		'ext_attr_skill' => 'ExtAttrSkill',
		'is_use'         => 'IsUse',
		'suit_id'        => 'SuitId',
		'create_at'      => 'CreateAt',
		'hero_name'      => 'HeroName',
		'hero_quality'   => 'HeroQuality',
		'on_sale'        => 'OnSale',
		'flag'           => 'Flag',

	);

	static $typeMsgInfo = array(
		'id'        => 'MsgId',
		'title'     => 'Title',
		'content'   => 'Content',
		'nickname'  => 'SendName',
		'nickname'  => 'OwnName',
		'flag'      => 'Flag',
		'status'    => 'Status',
		'type'      => 'Type',
		'create_at' => 'CreateAt'

	);

	/** 城市资源类型 */
	static $typeRes = array(
		'food'      => 'Food', //粮食
		'oil'       => 'Oil', //石油
		'gold'      => 'Gold', //金钱
		'max_store' => 'MaxStore',
	);


	/** 效果类型 */
	static $typePropsEffect = array();

	/** cd时间类型 */
	static $typeCdTime = array(
		'build'        => 'CDBuild', //建筑
		'tech'         => 'CDTech', //科技
		'weapon'       => 'CDWeapon', //武器
		'explore'      => 'CDExplore', //探索属地
		'hero_refresh' => 'HeroRefreshTime', //军校刷新
		'fb'           => 'CDFB', //单人副本
	);

	/** 城市突围数据 */
	static $typeBoutInfo = array(
		'battle_id_now'   => 'BattleIdNow', //正在进行的战斗ID(无突围战斗则为0)
		'free_times_left' => 'FreeTimesLeft', //免费剩余次数
		'buy_times_left'  => 'BuyTimesLeft', //购买所剩次数
		'next_buy_cost'   => 'NextBuyCost', //下次购买所需军饷
		'breakout_cd'     => 'CDBout', //突围快速CD时间 array
		'story'           => 'Story', //城市通关数据
	);

	/** 城市突围数据 */
	static $typeFloor = array(
		'BattleId'   => 'BattleId', //正在进行的战斗ID
		'CurFloorNo' => 'CurFloorNo',
		'Times'      => 'Times',
		'NextCost'   => 'NextCost', //下次购买所需军饷
		'cd'         => 'CD', //冷却时间
	);

	static $tmpList = array();

	static public function addGateQueue($cityId, $keyType, $newData) {
		$oldData = isset(self::$tmpList[$cityId]) ? self::$tmpList[$cityId] : array();

		self::$tmpList[$cityId] = self::_filterQueue($oldData, $keyType, $newData);

		return self::$tmpList[$cityId];
	}

	static private function _filterQueue($oldData, $keyType, $newData) {

		$tmpNewData = array();
		if (!empty($oldData)) {
			if ($keyType == M_Sync::KEY_MARCH_END) {
				foreach ($newData as $k => $v) {
					if (isset($oldData[M_Sync::KEY_MARCH_DATA][$k])) {
						unset($oldData[M_Sync::KEY_MARCH_DATA][$k]);
					}
				}
			}

			foreach ($oldData as $key => $val) {
				//如果存在这个类型的数据 合并数组里面的值
				if ($key == $keyType) {
					foreach ($newData as $k => $v) {
						if (is_array($v)) {
							foreach ($v as $nk => $nv) {
								$val[$k][$nk] = $nv;
							}
						} else {
							$val[$k] = $newData[$k];
						}
					}

					$tmpNewData[strval($key)] = $val;
				} else {
					$tmpNewData[strval($key)] = $val;
				}

			}
		}

		if (!isset($oldData[$keyType])) {
			$tmpNewData[strval($keyType)] = $newData;
		}

		return $tmpNewData;
	}

	/**
	 * 添加信息到同步队列
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @param int $keyType 同步数据类型
	 * @param array $newData 同步数据
	 * @param bool $passGateway 通过gateway
	 * @return bool
	 */
	static public function addQueue($cityId, $keyType, $newData, $passGateway = true) {
		$ret = false;
		if (!empty($cityId) &&
			!empty($keyType) &&
			!empty($newData)
		) {

			if ($cityId == self::$ownCityId) {
				return self::addGateQueue($cityId, $keyType, $newData);
			}

			/*
			 if ($keyType == self::KEY_MARCH_DATA ||
					 $keyType == self::KEY_MARCH_END ||
					 $keyType == self::KEY_HERO)
			 {
			$passGateway = false;
			}

			if (defined('IN_GATEWAY') && $passGateway)
			{
			return self::addGateQueue($cityId, $keyType, $newData);
			}
			*/

			$rc      = new B_Cache_RC(T_Key::CITY_SYNC_QUEUE, $cityId);
			$tmpData = $rc->get();

			$oldData    = strlen($tmpData) > 2 ? json_decode($tmpData, true) : array();
			$tmpNewData = self::_filterQueue($oldData, $keyType, $newData);
			if ($tmpNewData) {
				$ret = $rc->set(json_encode($tmpNewData), T_App::ONE_MINUTE);
			}

		}
		return $ret;
	}

	/**
	 * 获取后删除同步队列信息
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @return array
	 */
	static public function getQueue($cityId) {
		$info    = array();
		$rc      = new B_Cache_RC(T_Key::CITY_SYNC_QUEUE, $cityId);
		$infoStr = $rc->getset(false, T_App::ONE_MINUTE);
		if (strlen($infoStr) > 2) {
			$info = (array)json_decode($infoStr, true);
		}

		//if (defined('IN_GATEWAY') && !empty(self::$tmpList[$cityId]))
		if (!empty(self::$tmpList[$cityId])) {
			$tmpData = self::$tmpList[$cityId];

			foreach ($tmpData as $key => $val) {
				$info[$key] = $val;
			}

			unset(self::$tmpList[$cityId]);
		}


		if (!empty($info)) {
			self::_filter($info);
		}

		return $info;
	}

	/**
	 * 更新同步数据返回格式
	 * @author huwei on 20120820
	 * @param array $info
	 * @return array
	 */
	static private function _filter(&$info) {
		$filter = array(
			self::KEY_HERO      => self::$typeHeroInfo,
			self::KEY_EQUIP     => self::$typeEquipInfo,
			self::KEY_CITY_INFO => self::$typeCityInfo,
			self::KEY_RES       => self::$typeRes,
			self::KEY_CDTIME    => self::$typeCdTime,
			self::KEY_BOUT      => self::$typeBoutInfo,
			self::KEY_FLOOR     => self::$typeFloor,
		);

		foreach ($info as $syncKey => $syncVal) {
			if (isset($filter[$syncKey])) {
				$filed  = $filter[$syncKey];
				$newVal = array();
				foreach ($syncVal as $key => $val) {
					if (is_int($key) && is_array($val)) {
						$tmpVal = array();
						foreach ($val as $k => $v) {
							if (isset($filed[$k])) {
								$tmpVal[$filed[$k]] = $v;
							}
						}
						$newVal[$key] = $tmpVal;
					} else {
						if (isset($filed[$key])) {
							$key = $filed[$key];
						}
						$newVal[$key] = $val;
					}
				}
				if (!empty($newVal)) {
					$info[$syncKey] = $newVal;
				} else {
					unset($info[$syncKey]);
				}
			}
		}
	}

}

?>
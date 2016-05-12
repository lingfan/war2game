<?php

/**
 * 语言常量
 */
class T_Lang {
	const RES_FOOD_NAME = '{LANG_RES_FOOD}'; //金钱
	const RES_OIL_NAME = '{LANG_RES_OIL}'; //石油
	const RES_GOLD_NAME = '{LANG_RES_GOLD}'; //粮食

	const MILPAY = '{LANG_MILPAY}'; //军饷
	const COUPON = '{LANG_COUPON}'; //点券
	const RENOWN = '{LANG_RENOWN}'; //威望
	const WAREXP = '{LANG_WAREXP}'; //功勋

	const MARCH_NUM = '{LANG_MARCH_NUM}'; //活力
	const ATKFB_NUM = '{LANG_ATKFB_NUM}'; //军令
	const ACTIVENESS = '{LANG_ACTIVENESS}'; //活跃度积分制

	static $RES = array(
		T_App::RES_FOOD_NAME => self::RES_FOOD_NAME, //金钱
		T_App::RES_OIL_NAME => self::RES_OIL_NAME, //石油
		T_App::RES_GOLD_NAME => self::RES_GOLD_NAME, //粮食
	);

	static $EQUIP_QUAL = array(
		T_Equip::EQUIP_WHITE => '{LANG_EQUIP_QUAL_WHITE}', //'白'
		T_Equip::EQUIP_GREEEN => '{LANG_EQUIP_QUAL_GREEEN}', //'绿'
		T_Equip::EQUIP_BLUE => '{LANG_EQUIP_QUAL_BLUE}', //'蓝'
		T_Equip::EQUIP_PURPLE => '{LANG_EQUIP_QUAL_PURPLE}', //'紫'
		T_Equip::EQUIP_RED => '{LANG_EQUIP_QUAL_RED}', //'红'
		T_Equip::EQUIP_GOLD => '{LANG_EQUIP_QUAL_GOLD}', //'金'
	);
	/** 白1, 绿2, 蓝3, 紫4, 蓝(传)5, 紫(传)6, 红(传)7, 金(传)8*/
	static $HERO_QUAL = array(
		T_Hero::HERO_BULE_LEGEND => '{LANG_HERO_QUAL_BULE_LEGEND}',
		T_Hero::HERO_PURPLE_LEGEND => '{LANG_HERO_QUAL_PURPLE_LEGEND}',
		T_Hero::HERO_RED => '{LANG_HERO_QUAL_RED}',
		T_Hero::HERO_GOLD => '{LANG_HERO_QUAL_GOLD}',
	);

	/** 消费类型 */
	static $PAY_TYPE = array(
		T_App::MILPAY => self::MILPAY,
		T_App::COUPON => self::COUPON,
	);

	static $Map = array(
		T_App::MAP_ASIA => 'ASIA',
		T_App::MAP_EUROPE => 'EUROPE',
		T_App::MAP_AFRICA => 'AFRICA',
	);

	const EQUIP_NAME = '{LANG_EQUIP_NAME}'; //装备名
	const HERO_NAME = '{LANG_HERO_NAME}'; //英雄名

	const T_SYS_TIP = '{LANG_T_SYS_TIP}'; //系统提示
	const T_AUC_TIP = '{LANG_T_AUC_TIP}'; //拍卖
	const T_WILD_NPC_TIP = '{LANG_T_WILD_NPC_TIP}'; //属地
	const T_WILD_CITY_TIP = '{LANG_T_WILD_CITY_TIP}'; //城市属地
	const T_MOVE_CITY_TIP = '{LANG_T_MOVE_CITY_TIP}'; //迁城


	/** 您已成功加入军团 {0} */
	const C_JOIN_UNION_SUCC = '{LANG_C_JOIN_UNION_SUCC}';
	/** 玩家{0}同意招募，成功加入您的军团 */
	const C_APPLY_JOIN_UNION_SUCC = '{LANG_C_APPLY_JOIN_UNION_SUCC}';
	/** {0} 军团 拒绝了您的加入申请！ **/
	const C_JOIN_UNION_FALL = '{LANG_C_JOIN_UNION_FALL}';
	/** 您被踢出军团！ */
	const C_UNION_DEL_MEMBER = '{LANG_C_UNION_DEL_MEMBER}';
	/** {0} {1}（{2}）战斗等待人数过多！ */
	const C_BATTLE_WAIT_QUEUE = '{LANG_C_BATTLE_WAIT_QUEUE}';
	/** {0} 据点排队人数过多，您无法介入战斗，部队已经被遣返！*/
	const C_CAMPAIGN_WAIT_QUEUE = '{LANG_C_CAMPAIGN_WAIT_QUEUE}';
	/** 您竞价购买 {0} 失败，全额返还 {1} 军饷。*/
	const C_AUC_MILPAY_BACK = '{LANG_C_AUC_MILPAY_BACK}';
	/** 您购买 {0}成功，花费 {1} 军饷，请尽快至拍卖->购买管理页面领取。*/
	const C_AUC_BUY_SUCC = '{LANG_C_AUC_BUY_SUCC}';
	/** 您出售 {0} 成功，出售价格 {1} 军饷，扣除交易税 {2} 军饷，退还保管费 {3} {4}，收入 {5} 军饷，{6} 点券。*/
	const C_AUC_SALE_SUCC = '{LANG_C_AUC_SALE_SUCC}';

	/** 您军官已满，购买 {0} 失败，退还 {1} 军饷。*/
	const C_AUC_BUY_FAIL_FULL_HERO = '{LANG_C_AUC_BUY_FAIL_FULL_HERO}';
	/** 您出售 {0} 失败，退还保管费 {1} {2}。*/
	const C_AUC_SALE_FAIL_FULL_HERO = '{LANG_C_AUC_SALE_FAIL_FULL_HERO}';


	/** 您拍卖的 {0} 已结束，保管费 {1} {2} 已扣除，请至拍卖->出售管理页面领取未售出的物品。*/
	const C_AUC_SALE_FAIL = '{LANG_C_AUC_SALE_FAIL}';
	/** 您的 {0} 属地 {1}（{2}），于{3}年{4}月{5}日{6}时{7}分被玩家{8}占领，你失去该属地。*/
	const C_WILD_NPC_LOSE = '{LANG_C_WILD_NPC_LOSE}';
	/** 您的 {0} 属地 {1}（{2}），于{3}年{4}月{5}日{6}时{7}分被玩家{8}攻击，你成功防御该属地。*/
	const C_WILD_NPC_DEF_SUCC = '{LANG_C_WILD_NPC_DEF_SUCC}';
	/** 您占领 {0} {1}（{2}）的军事行动成功。*/
	const C_WILD_NPC_HOLD_SUCC = '{LANG_C_WILD_NPC_HOLD_SUCC}';
	/** 您占领 {0} {1}（{2}）的军事行动失败。*/
	const C_WILD_NPC_HOLD_FAIL = '{LANG_C_WILD_NPC_HOLD_FAIL}';


	/** 您的 {0} 城市属地 {1}（{2}），于{3}年{4}月{5}日{6}时{7}分被玩家{8}占领，你失去该城市属地。*/
	const C_WILD_CITY_LOSE = '{LANG_C_WILD_CITY_LOSE}';
	/** 您占领 {0} {1}（{2}）的军事行动成功。*/
	const C_WILD_CITY_OCCUPIED_SUCC = '{LANG_C_WILD_CITY_OCCUPIED_SUCC}';
	/** 您占领 {0} {1}（{2}）的军事行动失败。*/
	const C_WILD_CITY_OCCUPIED_FAIL = '{LANG_C_WILD_CITY_OCCUPIED_FAIL}';
	/** 您于{0}年{1}月{2}日{3}时{4}分被玩家{5} {6}({7})占领，成为该玩家的属地*/
	const C_WILD_CITY_OCCUPIED = '{LANG_C_WILD_CITY_OCCUPIED}';
	/**  {0} {1}（{2}）已经成为您的属地，您无法对自己的属地进行占领！*/
	const C_WILD_CITY_NOT_OCCUPIED_SELF = '{LANG_C_WILD_CITY_NOT_OCCUPIED_SELF}';
	/**  {0} {1}（{2}）已经成为您同盟的属地，您无法对同盟的属地进行占领！*/
	const C_WILD_CITY_NOT_OCCUPIED_UNION = '{LANG_C_WILD_CITY_NOT_OCCUPIED_UNION}';
	/**  {0} {1}（{2}）已经成为您的属地，您无法对自己的属地进行掠夺！*/
	const C_WILD_CITY_NOT_ATK_SELF = '{LANG_C_WILD_CITY_NOT_ATK_SELF}';
	/**  {0} {1}（{2}）已经成为您同盟的属地，您无法对同盟的属地进行掠夺！*/
	const C_WILD_CITY_NOT_ATK_UNION = '{LANG_C_WILD_CITY_NOT_ATK_UNION}';
	/**  {0} {1}（{2}）已经被占领，您无法对占领的属地进行掠夺！*/
	const C_WILD_CITY_NOT_ATK = '{LANG_C_WILD_CITY_NOT_ATK}';
	/** 您的 {0} 城市属地 {1}（{2}），于{3}年{4}月{5}日{6}时{7}分被玩家{8}解救，你失去该城市属地。*/
	const C_WILD_CITY_RESCUE_LOSE = '{LANG_C_WILD_CITY_RESCUE_LOSE}';
	/** 您解救 {0} {1}（{2}）的军事行动成功。*/
	const C_WILD_CITY_RESCUE_SUCC = '{LANG_C_WILD_CITY_RESCUE_SUCC}';
	/** 您解救 {0} {1}（{2}）的军事行动失败。*/
	const C_WILD_CITY_RESCUE_FAIL = '{LANG_C_WILD_CITY_RESCUE_FAIL}';
	/** 您被同盟{0} {1}（{2}）解救成功。*/
	const C_WILD_CITY_RESCUED_SUCC = '{LANG_C_WILD_CITY_RESCUED_SUCC}';
	/** 您被同盟{0} {1}（{2}）解救失败。*/
	const C_WILD_CITY_RESCUED_FAIL = '{LANG_C_WILD_CITY_RESCUED_FAIL}';
	/** 您的城市属地上限已满，占领 {0} {1}（{2}）的军事行动失败。*/
	const C_WILD_CITY_OCCUPIED_FULL = '{LANG_C_WILD_CITY_OCCUPIED_FULL}';
	/** 您派兵驻守占领城市{0} {1}（{2}）的军事行动成功。*/
	const C_WILD_CITY_HOLD_SUCC = '{LANG_C_WILD_CITY_HOLD_SUCC}';
	/** 您派兵驻守占领城市 {0} {1}（{2}）的军事行动失败。*/
	const C_WILD_CITY_HOLD_FAIL = '{LANG_C_WILD_CITY_HOLD_FAIL}';
	/**  {0} {1}（{2}）已经被占领超过{3}个小时，您无法对该属地进行占领！*/
	const C_WILD_CITY_NOT_OCCUPIED_OUT = '{LANG_C_WILD_CITY_NOT_OCCUPIED_OUT}';
	/**  {0} {1}（{2}）的占领者已经成为您同盟，您无法进行解救！*/
	const C_WILD_CITY_NOT_RESCUE_UNION = '{LANG_C_WILD_CITY_NOT_RESCUE_UNION}';
	/** 您被{0} {1}（{2}）城市击败，损失驻军：{3} {4} {5} {6}*/
	const C_WILD_CITY_ATK_FALL = '{LANG_C_WILD_CITY_ATK_FALL}';
	/** 您成功击败{0} {1}（{2}）城市，对方驻军损失：{3} {4} {5} {6}*/
	const C_WILD_CITY_ATK_SUCC = '{LANG_C_WILD_CITY_ATK_SUCC}';
	/** 步兵{0}*/
	const C_ARMY_1 = '{LANG_C_ARMY_1}';
	/** 炮兵{0}*/
	const C_ARMY_2 = '{LANG_C_ARMY_2}';
	/** 装甲兵{0}*/
	const C_ARMY_3 = '{LANG_C_ARMY_3}';
	/** 航空兵{0}*/
	const C_ARMY_4 = '{LANG_C_ARMY_4}';

	/** 您的属地上限已满，占领 {0} {1}（{2}）的军事行动失败。*/
	const C_WILD_NPC_HOLD_FULL = '{LANG_C_WILD_NPC_HOLD_FULL}';
	/** 恭喜您，您成功将城市从({0}：{1})迁移到({2}：{3})，您将获得全新的{4}风格体验。*/
	const C_MOVE_MSG = '{LANG_C_MOVE_MSG}';
	/** {0} 据点战已经结束，部队已经被遣返！*/
	const C_CAMP_WAR_END = '{LANG_C_CAMP_WAR_END}';
	/** {0} 据点驻守部队已满，无法进行驻守，部队已经被遣返！*/
	const C_CAMP_HOLD_FULL = '{LANG_C_CAMP_HOLD_FULL}';
	/** "您收到系统奖励 {0}, 请查收*/
	const C_AWARD_MESSAGE = '{LANG_C_AWARD_MESSAGE}';
	/** {0}x{1}*/
	const C_AWARD_MOD_NUM = '{LANG_C_AWARD_MOD_NUM}';
	/** {0}x{1}*/
	const C_AWARD_MOD_PROPS = '{LANG_C_AWARD_MOD_PROPS}';
	/** {0} ({1})x{2}*/
	const C_AWARD_MOD_HERO = '{LANG_C_AWARD_MOD_HERO}';
	/** {0} ({1})x{2}*/
	const C_AWARD_MOD_EQUIP = '{LANG_C_AWARD_MOD_EQUIP}';
	/** 班 */
	const C_TROOPS_LEVEL_1 = '{LANG_C_TROOPS_LEVEL_1}';
	/** 排 */
	const C_TROOPS_LEVEL_2 = '{LANG_C_TROOPS_LEVEL_2}';
	/** 连 */
	const C_TROOPS_LEVEL_3 = '{LANG_C_TROOPS_LEVEL_3}';
	/** 营 */
	const C_TROOPS_LEVEL_4 = '{LANG_C_TROOPS_LEVEL_4}';
	/** 团 */
	const C_TROOPS_LEVEL_5 = '{LANG_C_TROOPS_LEVEL_5}';
	/** 旅 */
	const C_TROOPS_LEVEL_6 = '{LANG_C_TROOPS_LEVEL_6}';
	/** 师 */
	const C_TROOPS_LEVEL_7 = '{LANG_C_TROOPS_LEVEL_7}';
	/** 军 */
	const C_TROOPS_LEVEL_8 = '{LANG_C_TROOPS_LEVEL_8}';

	/** 小队 */
	const C_TROOPS_AIR_LEVEL_1 = '{LANG_C_TROOPS_AIR_LEVEL_1}';
	/** 中队 */
	const C_TROOPS_AIR_LEVEL_2 = '{LANG_C_TROOPS_AIR_LEVEL_2}';
	/** 大队 */
	const C_TROOPS_AIR_LEVEL_3 = '{LANG_C_TROOPS_AIR_LEVEL_3}';
	/** 团 */
	const C_TROOPS_AIR_LEVEL_4 = '{LANG_C_TROOPS_AIR_LEVEL_4}';
	/** 师 */
	const C_TROOPS_AIR_LEVEL_5 = '{LANG_C_TROOPS_AIR_LEVEL_5}';
	/** 军 */
	const C_TROOPS_AIR_LEVEL_6 = '{LANG_C_TROOPS_AIR_LEVEL_6}';

	const UNKNOW = 'UNKNOW';

	/** XX军团通过艰苦战斗终于成功攻占“粮食基地”据点。 */
	const CAMP_UNION_WIN_RADIO = '{LANG_CAMP_UNION_WIN_RADIO}';
	/** 攻打“{0}”的战斗将于{1}时{2}分后开启，请报名军团做好战斗准备！*/
	const CAMP_START_RADIO = '{LANG_CAMP_START_RADIO}';
	/** 野外npc刷新 */
	const WILD_NPC_START_REFRESH = '{LANG_WILD_NPC_START_REFRESH}';
	/** XX玩家非常勇猛的击溃了XX部队 */
	const WILD_NPC_REFRESH_KILLED = '{LANG_WILD_NPC_REFRESH_KILLED}';

	/** X号马在比赛中,表现神勇,勇夺冠军 */
	const HORSE_NO_WIN = '{HORSE_NO_WIN}';

	/** 寻将系统 */
	const GET_LEGEND_HERO_FIND = '{LANG_GET_LEGEND_HERO_FIND}';
	/** VIP抽奖 */
	const GET_LEGEND_HERO_VIP = '{LANG_GET_LEGEND_HERO_VIP}';
	/** 奖励 */
	const GET_LEGEND_HERO_AWARD = '{LANG_GET_LEGEND_HERO_AWARD}';
	/** 抽奖机 */
	const GET_LEGEND_HERO_LOTTERY = '{LANG_GET_LEGEND_HERO_LOTTERY}';
	/** 交换获得 */
	const GET_LEGEND_HERO_EXCHANGE = '{LANG_GET_LEGEND_HERO_EXCHANGE}';
	/** 攻击目标不存在 */
	const AIM_NOT_EXIST = '{LANG_AIM_NOT_EXIST}';
	/** 临时NPC将要被刷新 */
	const TMP_NPC_WOULD_OUT = '{LANG_TMP_NPC_WOULD_OUT}';
	/** 临时NPC将要被刷新 */
	const TMP_NPC_HAD_OUT = '{LANG_TMP_NPC_HAD_OUT}';
	/** 军团招募- */
	const TMP_UNION_HIRE = '{LANG_TMP_UNION_HIRE}';
	/** 您租用的 XX 已经到期，请重新租赁以便您能获得该武器使用权！ */
	const WEAPON_EXPRITE = '{LANG_WEAPON_EXPRITE}';

}

?>
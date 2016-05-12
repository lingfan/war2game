<?php

/**
 * 缓存key定义
 */
class T_Key {
	/** 配置文件数据key + name*/
	const CONFIG_FILE = 101;
	/** 运营商配置文件数据*/
	const CONSUMER_LIST = 102;
	/** 日志队列 */
	const LOG_QUEUE = 103;

	/** 配置信息列表key */
	const BASE_CONFIG_LIST = 104;
	/** 黑名单数据key */
	const BASE_BLOCK_LIST = 105;
	/** 英雄名字数据key */
	const BASE_HERO_NAME_LIST = 106;

	/** 基础建筑信息key */
	const BASE_BUILD = 107;
	/** 升级建筑信息key */
	const UPG_BUILD = 108;
	/** 基础科技信息key */
	const BASE_TECH = 109;
	/** 升级科技信息key */
	const UPG_TECH = 110;
	/** 基础兵种信息key */
	const BASE_ARMY = 111;
	/** 基础武器信息key */
	const BASE_WEAPON = 112;
	/** 基础任务信息key */
	const BASE_TASK = 113;
	/** 基础技能key */
	const BASE_SKILL = 114;
	/** 基础道具key */
	const BASE_PROPS = 115;


	/** 战斗副本章节总数 缓存key */
	const WAR_TOTAL_CHAPTER = 116;

	/** 战场地图标记物key*/
	const BASE_WAR_MAP_CELL = 117;

	/** NPC英雄key + npc_hero_id*/
	const BASE_NPC_HERO = 118;

	/** 基础NPC数据key + ID*/
	const BASE_NPC = 119;
	/** 基础奖励数据key + ID*/
	const BASE_AWARD = 120;


	/** 战场地图数据key+ 战斗地图ID*/
	const BASE_WAR_MAP_DATA = 121;

	/** 装备基表key */
	const BASE_EQUIP_TPL_LIST = 122;
	/** 装备模板key+ tplId */
	const BASE_EQUIP_TPL_INFO = 123;
	/** 装备模板faceId => name */
	const BASE_EQUIP_TPL_NAMES = 124;

	/** 英雄模板列表 + 品质 */
	const BASE_HERO_TPL_LIST = 125;
	/** 英雄模板key + 模板英雄ID*/
	const BASE_HERO_TPL_INFO = 126;
	/** 英雄数量key + 模板英雄ID*/
	const BASE_HERO_TPL_NUM = 127;


	/** 正在行军队列 */
	const WAR_MARCH_QUEUE_LIST = 128;
	/** 行军Key+ 行军ID */
	const CITY_WAR_MARCH_INFO = 129;
	/** 行军列表 KEY+ 坐标  */
	const CITY_WAR_MARCH_LIST = 130;


	/** 最后访问时间*/
	const CITY_VISIT = 131;
	/** 用户信息key + userId*/
	const USER_INFO = 132;
	/** 用户城市IDkey + userId*/
	const USERID_TO_CITYID = 133;
	/** 城市信息key+ cityId */
	const CITY_INFO = 134;
	/** 城市用户昵称key+ md5(昵称) */
	const CITY_NICKNAME_TO_CITYID = 135;
	/** 城市资源key+ cityId */
	const CITY_RES = 136;
	/** 城市额外信息key+ cityId */
	const CITY_EXTRA_INFO = 137;
	/** 城市拥有兵种key+ cityId */
	const CITY_ARMY = 138;
	/** 城市拥有武器key+ cityId */
	const CITY_WEAPON = 139;
	/** 城市任务key+ cityId */
	const CITY_TASK = 140;
	/** 城市学院key + cityId */
	const CITY_COLLEGE = 141;
	/** 城市访问队列 + cityId */
	const CITY_VISIT_QUEUE = 142;
	/** 城市访问队列 + cityId */
	const CITY_VISIT_DIFF = 143;

	/** 城市拥有道具key+ cityId */
	const CITY_PROPS = 144;

	/** 城市使用道具key+ cityId */
	const CITY_PROPS_USE = 145;

	/** 城市最新发送邮件key+ cityId */
	const CITY_MSG = 146;
	/** 城市未读邮件id key+ cityId */
	const CITY_MSG_UNREAD = 147;
	/** 城市最新任务领奖key+ cityId */
	const CITY_TASK_FIN = 148;
	/** 城市数据同步 + 城市ID */
	const CITY_SYNC_QUEUE = 149;
	/** 某玩家战胜另一玩家次数key + atkCityId_defCityId */
	const CITY_ATK_CITY = 150;

	/** 城市正在战场中的数据 + 城市ID */
	const CITY_BATTLE_DATA = 151;

	/** 城市英雄列表key+ cityId (只存储ID列表)*/
	const CITY_HERO_LIST = 152;
	/** 城市英雄信息key + heroId */
	const CITY_HERO_INFO = 153;


	/** 战斗报告列表key + 城市ID (只存储ID列表)*/
	const CITY_REPORT_LIST = 154;
	/** 战斗报告key + 战报ID */
	const CITY_REPORT_INFO = 155;
	/** 未读战斗报告key + cityId */
	const CITY_REPORT_UNREAD = 156;

	/** 城市装备列表key+ cityId (只存储ID列表)*/
	const CITY_EQUIP_LIST = 157;
	/** 城市装备信息key+ 装备ID */
	const CITY_EQUIP_INFO = 158;

	/** 城市消息列表key+ cityId (只存储ID列表)*/
	const CITY_MESSAGE_LIST = 159;
	/** 城市消息信息key+ 信息ID */
	const CITY_MESSAGE_INFO = 160;


	/** 防沉迷系统的KEY + cityId */
	const ANTI_ADDICTION = 161;

	/** 军官培养临时属性点 key+heroId */
	const HERO_TRAINING_TMP = 162;
	/** 军官培养次数 key+CityId */
	const HERO_TRAINING_TIMES = 163;

	/** 城市抽奖信息 + cityId */
	const CITY_LOTTERY_INFO = 164;
	/** 城市任务指引信息 + cityId */
	const CITY_QUEST_INFO = 165;


	/** 拍卖物品数据key + 交易ID */
	const AUCTION_DETAIL = 166;
	/** 玩家出售拍卖物品列表key + 城市ID */
	const AUCTION_LIST = 167;

	/** 地图区域片段  + 战区 + 编号*/
	const MAP_AREA_SEGMENT = 168;
	/** 地貌坐标列表  + 地貌ID + 起始坐标*/
	const MAP_SCENCE_LIST = 169;

	/** 战场计策 + 计策ID*/
	const WAR_PLOY = 170;


	/** 战斗报告列表key + 城市ID */
	const REPORT_LIST = 171;
	/** 战斗报告key + 战报ID */
	const REPORT = 172;
	/** 据点信息key + 据点ID */
	const CAMPAIGN_INFO = 173;
	/** 据点巡逻次数key + 据点ID + 城市ID*/
	const CAMPAIGN_TIMES = 174;
	/** 缓存世界频道 + unix时间戳 */
	const CHAT_WORLD = 175;
	/** 联盟频道 + 联盟ID */
	const CHAT_UNION = 176;
	/** 战场频道 + 战场ID */
	const CHAT_WAR = 177;
	/** 队伍频道 + 队伍ID */
	const CHAT_TEAM = 178;
	/** 私聊频道 + 玩家nickname*/
	const CHAT_OWNER = 179;
	/** 系统频道  */
	const CHAT_SYS = 180;

	/** 玩家最后发送聊天信息时间key+city_id */
	const CHAT_LAST_SEND_TIME = 181;
	/** 玩家已接收的系统消息标记 KEY+userId */
	const CHAT_USER_SYS = 182;
	/** 玩家接收的消息缓存 */
	const CITY_CHAT = 183;
	/** 系统广播列表 */
	const RADIO_LIST = 184;
	/** 临时广播列表 */
	const RADIO_TMP_LIST = 185;

	/** VIP商城系统限购物品剩余数量 + 今天Ymd */
	const VIP_SHOP_SYS_LEFT = 186;
	/** 日常任务完成次数 + 今天Ymd + CityId */
	const TASK_DAILY_TIMES = 187;

	/** 野外地图缓存区块  */
	const WILD_MAP_AREA = 188;
	/** 野外地图信息  */
	const WILD_MAP_INFO = 189;
	/** 野外地图未使用坐标列表 */
	const WILD_MAP_NO_HOLD_POS = 190;
	/** 野外地图未使用坐标区块 */
	const WILD_MAP_NO_HOLD_AREA = 191;

	/** 野外地图未使用坐标增长序号 */
	const WILD_MAP_NO_HOLD_NUM = 192;

	const RANKINGS_SYNC_TIME = 193;
	/** 威望排行缓存key */
	const RANKINGS_RENOWN = 194;
	/** 军功排行缓存key */
	const RANKINGS_MILMEDAL = 195;
	/** 联盟排行缓存key + page*/
	const RANKINGS_UNION = 196;
	/** 英雄(默认等级)排行缓存key */
	const RANKINGS_HERO_LEVEL = 197;
	/** 英雄(指挥)排行缓存key */
	const RANKINGS_HERO_COMMAND = 198;
	/** 英雄(军事)排行缓存key */
	const RANKINGS_HERO_MILITARY = 199;
	/** 英雄(统帅)排行缓存key */
	const RANKINGS_HERO_LEAD = 200;
	/** 英雄(胜利)排行缓存key */
	const RANKINGS_HERO_WIN = 201;
	/** 联盟ID列表缓存 */
	const UNION_LIST = 202;

	/** 联盟信息缓存 key + 联盟ID */
	const UNION_INFO = 203;
	/** 联盟成员列表缓存key+联盟ID */
	const UNION_MEMBER_LIST = 204;
	/** 联盟成员信息表缓存key+城市ID */
	const UNION_MEMBER_INFO = 205;
	/** 玩家申请的联盟列表 */
	const UNION_USER_APP_LIST = 206;
	/** 联盟待审核的玩家列表 */
	const UNION_APP_USER_LIST = 207;
	/** 联盟奖励 */
	const UNION_AWARD_KEY = 208;


	/** 据点联盟加成 */
	const CAMP_UNION_EFFECT = 209;
	/** 据点结束 */
	const CAMP_END = 210;

	/**    战斗数据key + 战斗ID */
	const BATTLE_DATA = 211;
	/** 战斗记录查看点 +战斗ID+城市ID */
	const BATTLE_RECORD_INFO = 212;
	/** 战斗记录查看点 +战斗ID+城市ID */
	const BATTLE_RECORD_NUM = 213;
	/** 用户是否在线 key+用户ID */
	const ONLINE_USER = 214;
	/** 在线用户列表 key */
	const ONLINE_USER_LIST = 215;
	/** 统计在线用户列表 key */
	const STATS_ONLINE_USER_LIST = 216;
	/** 在线用户统计数据 key+日期 */
	const STATS_ONLINE_USER_NUM = 217;

	/** 玩家城市中心升级统计数据 key+日期 */
	const STATS_USER_CITY_UPLEVEL = 218;

	/**    战斗队列key */
	const BATTLE_HANDLE_LIST_KEY = 219;
	/**    玩家战斗AI随机key队列 */
	const BATTLE_AI_RND_KEY = 220;

	/**    行军占领队列key */
	const MARCH_HOLD_LIST_KEY = 221;
	/**    战斗进行中KEY + 地图坐标 */
	const BATTLE_ING_KEY = 222;
	/**    玩家调用接口次数 */
	const USER_CALL_TIMES = 223;

	/** 玩家登陆排队队列 */
	const LOGIN_QUEUE_KEY = 224;

	/** 玩家登陆排队信息 */
	const LOGIN_QUEUE_INFO_KEY = 225;


	/** 验证礼包卡是否重复提交key+cityId */
	const CHECK_CODE = 226;

	/** 行军队列key */
	const MARCH_QUEUE_KEY = 227;

	const QQ_KEEP_LIVE = 228;
	/** 每天玩家邀请数KEY + userId + 20120101 */
	const QQ_INVITE_NUM = 229;

	const CACHE_TO_DB_QUEUE = 230;

	const ONLINE_NUM = 231;

	/** 基础事件key */
	const BASE_PROBE = 234;
	/** 基础据点key */
	const BASE_CAMPAIGN = 235;

	/** 战斗副本章节缓存key + 副本章节ID */
	const WAR_FB_CHAPTER = 236;
	/** 新手卡是否使用 */
	const NEWBE_CARD = 237;
	/** 行军排队*/
	const MARCH_WAIT_KEY = 238;
	/** 最后聊天内容*/
	const CHAT_TMP_KEY = 239;
	/** 每日登陆*/
	const LOGIN_DAILY_TMP_KEY = 240;
	/** 幸运卡片抽取*/
	const LOGIN_LUCK_CARD_KEY = 241;
	/** SOHA订单信息+用户ID*/
	const SOHA_ORDER_INFO = 242;
	/** 地图区块信息*/
	const BASE_MAP_AREA = 244;
	/** 野外刷新过的NPC*/
	const HAD_REFRESH_TMP_NPC = 245;
	/** 临时NPC*/
	const BASE_TMP_NPC_CONF = 246;
	/** 活跃用户统计*/
	const CITY_ACTIVE_NUM = 247;
	/** 临时过期数据*/
	const TMP_EXPIRE = 248;
	/** 用户在线信息*/
	const SSID_USER_INFO = 249;
	/** 商城物品key */
	const BASE_MALL = 250;
	/** 招募军团列表key */
	const UNION_HIRE = 251;
	/** 拒绝军团列表key */
	const UNION_REFUSE = 252;
	/** 招募次数 */
	const UNION_INVITE_TIMES = 253;
	/** 合服用户数据 */
	const MERGE_USER_LIST = 254; //
	/** 战役排行 */
	const FB_PASS_RANK = 255;
	/** 商城物品key */
	const MALL_NUM = 256;
	/** 套装基表key */
	const BASE_EQUIP_SUIT_LIST = 257;
	/** 城市物品列表 */
	const CITY_ITEM_LIST = 258;
	/** 城市物品信息*/
	const CITY_ITEM_INFO = 259;
	/** 城市装备数量*/
	const CITY_EQUIP_NUM = 260;
	/** 城市突围数据key+ cityId*/
	const CITY_BREAKOUT = 261;
	/** 基础突围数据*/
	const BASE_BREAKOUT = 262;
	/** 城市属地信息key+ cityId */
	const CITY_COLONY_INFO = 263;
	/** 临时仓库key+ cityId */
	const CITY_TEMP_WAERHOUSE = 264;
	/** 多人副本队伍列表 */
	const TEAM_MULTI_FB_LIST = 265;
	/** 多人副本队伍信息 */
	const TEAM_MULTI_FB_INFO = 266;
	/** 基础组队副本*/
	const BASE_MULTI_FB = 267;
	/** 城市多人副本队伍信息  */
	const CITY_TEAM_MULTI_FB = 268;
	/** 临时多人副本队伍列表 */
	const TEAM_MULTI_FB_LIST_TMP = 269;

	/** 军团公告中删除的公告城市ID */
	const CITY_ID_DEL_UNION = 270; //
	/** 存入仓库的时间+cityId */
	const CITY_TAX_TIME = 271;
	/** 城市多人副本信息  */
	const CITY_MULTI_FB_INFO = 272;
	/** 战绩值排行缓存key */
	const RANKINGS_RECORD = 273;
	/** 当日占领的城市+cityId */
	const OCCUPIED_LIST = 274;
	/** 城市越野数据key+cityId */
	const CITY_HORSE = 275;
	/** 越野系统统一数据 */
	const SYS_HORSE = 276;
	/** 城市被攻击失败次数 */
	const CITY_ATK_TIMES = 277;

	/** 基础兑换key */
	const BASE_EXCHANGE = 278;
	/** 学院活动占领次数 */
	const CITY_OCCOUPIED_TIMES = 279;

	/** 拍卖购买一口价物品key + 交易ID */
	const AUC_BUY_ONLY_DATA = 280;
	/** 拍卖竞价物品key + 交易ID */
	const AUC_BID_DATA = 281;
	/** 配置信息列表key */
	const SERVER_CONFIG = 282;
	/** 全服奖励补偿列表 */
	const SERVER_COMPENSATE = 283;
	/** 全服奖励补偿列表+$cityId */
	const CITY_COMPENSATE = 284;
	/** 新手指引基础数据 */
	const BASE_QUEST = 285;
	/** 分享奖励基础数据 */
	const BASE_QQ_SHARE = 286;
	/** 城市分享情况+$cityId*/
	const CITY_QQ_SHARE = 287;
	/** 每日分享成功次数 */
	const SUCCESS_SHARE_TIMES = 288;
	/** 辅助中心列表*/
	const SERVER_NEWS = 289;
	/** 守护进程定时 */
	const CRON_EXPIRE_TIME = 290;
	/** 玩家申请加入军团的冷却时间 */
	const CD_APPLY_UNION = 291;
	/** 固定法西斯*/
	const BASE_FASCIST_NPC_CONF = 292;
	/** 刷新过得固定法西斯*/
	const HAD_REFRESH_FASCIST_NPC = 293;
	/** 玩家活跃度+$cityId */
	const CITY_ACTIVENESS = 294;
	/** 积分兑换商城物品key */
	const BASE_MALL_EXCHANGE = 295;
	/** 商城物品key */
	const MALL_EXCHANGE_NUM = 296;

	/** 随机名字key */
	const RAND_NAME = 298;
	/** 玩家总数key */
	const TOTAL_PLAYER = 299;

	const CITY_QUESTION = 300;
	const BASE_QUESTION = 301;

	const CITY_FLOOR = 302;
	/** 随机名字组 */
	const RAND_NAME_IDX = 303;
	/** 随机临时 */
	const RAND_TMP_IDX = 304;

	const QQ_FRIEND_INVITE = 305;
	const QQ_FRIEND_LIVE = 306;
}

?>
<?php

class T_DBField {
	static $userFields = array(
		'id', 'consumer_id', 'username', 'nickname', 'username_ext', 'server_id', 'nickname', 'status',
		'is_adult', 'last_visit_ip', 'last_visit_time', 'online_time', 'create_at',
		'sys_sync_time', 'new_guide_step', 'login_times', 'ban_login_time'
	);

	/** 城市信息字段 */
	static $cityInfoFields = array(
		'id', 'consumer_id', 'server_id', 'username', 'nickname', 'gender', 'face_id', 'pos_no',
		'cur_people', 'max_people', 'affiliated', 'hero_refresh_time', 'level',
		'can_move_build', 'can_alter_hero', 'last_checkpoint', 'cd_tech', 'cd_tech_num', 'cd_build',
		'cd_build_num', 'cd_weapon', 'cd_explore', 'cd_fb', 'union_id', 'market_amount', 'rank',
		'total_mil_pay', 'mil_pay', 'coupon', 'vip_level', 'vip_endtime', 'mil_rank_daily', 'mil_rank_award', 'mil_rank',
		'renown', 'mil_medal', 'energy', 'mil_order', 'energy_update', 'avoid_war_cd_time',
		'move_city_cd_time', 'can_alter_nick', 'alter_nick_time', 'equip_strong_luck_pool', 'last_fb_no', 'signature',
		'ban_talking', 'newbie', 'first_recharge', 'vip_pack_date', 'created_at', 'fb_battle_id', 'is_adult', 'online_time',
		'new_guide_step','last_visit_time'
	);

	/** 城市资源信息字段 */
	static $cityCompensateFields = array(
		'city_id', 'award_data'
	);

	/** 城市额外信息字段 */
	static $cityExtraFields = array(
		'city_id', 'build_list', 'tech_list', 'beautify_list', 'medal_list', 'pos_collect',
		'army_list', 'vipshop_buylist', 'vip_effect', 'vip_equip_award',
		'vip_hero_award', 'wild_city', 'team_list', 'weapon_list', 'res_list', 'props_use', 'quest_list', 'cd_list',
		'liveness_list', 'floor_list', 'seek_hero', 'compensate_list', 'pack_list', 'find_hero','answer_list'
	);
	/** 城市额外信息字段 */
	static $cityCityColonyFields = array(
		'city_id', 'colony_city', 'atk_city_id', 'atk_march_id', 'hold_time', 'rescue_date', 'rescue_num'
	);
	/** 拍卖字段列表 */
	static $auctionFields = array(
		'id', 'sale_city_id', 'goods_type', 'goods_id', 'goods_name', 'quality',
		'pos', 'keep_type', 'price_only', 'price_start', 'price_new',
		'price_succ', 'buy_city_id', 'create_at', 'auction_start', 'auction_expired',
		'auction_status', 'keep_expired', 'shift_at', 'shift_type','ol_time','goods_info'
	);


	/** 据点信息字段 */
	static $campaignFields = array(
		'id', 'owner_union_id', 'join_union_ids', 'had_award',
		'no_11', 'no_12', 'no_13', 'no_14', 'no_15', 'no_16',
		'no_21', 'no_22', 'no_23',
		'no_31', 'no_32',
		'no_41',
	);

	/** 城市道具信息 字段 */
	static $cityPropsFields = array(
		'city_id', 'props', 'create_at', 'in_use' // 'drawing',
	);
	/** 城市物品信息 字段 */
	static $cityItemFields = array(
		'id', 'city_id', 'props_id', 'type', 'num', 'locked',
	);
	/** 城市道具信息 字段 */
	static $mallFields = array(
		'id', 'category', 'item_type', 'item_id', 'price', 'num', 'up_time', 'down_time', 'status', 'sort', 'del',
	);

	/** 战报字段 */
	static $warReportFields = array(
		'id', 'type', 'battle_type', 'atk_city_id', 'atk_info', 'def_city_id', 'def_info',
		'atk_time', 'content', 'reward', 'is_succ', 'flag_see', 'flag_del', 'replay_address',
		'replay_address_md5', 'create_at'
	);

	/** 城市任务字段 */
	static $cityTaskFields = array(
		'city_id', 'tasks_ok', 'tasks_end', 'tasks_daily_ok', 'tasks_daily_end',
		'daily_date', 'drama_end', 'create_at', 'sys_sync_time', 'active_filed', 'once',
		'yellow_vip_level', 'yellow_year_vip', 'yellow_vip_one',
		'blue_vip_level', 'blue_year_vip', 'blue_vip_one',
		'section_pay_once', 'section_pay_add', 'calender_award',
		'friend_live', 'friend_invite'
	);

	/** 城市突围字段 */
	static $cityBreakOutFields = array(
		'city_id', 'breakout_date', 'free_times_left', 'buy_times_left', 'buy_times', 'breakout_pass', 'breakout_data',
		'breakout_cd', 'point', 'battle_id', 'create_at', 'sys_sync_time'
	);

	/** 城市越野字段 */
	static $cityHorseFields = array(
		'city_id', 'horse_date', 'cycle_no', 'encour_times', 'horse1', 'horse2', 'horse3',
		'horse4', 'horse5', 'horse6', 'horse7', 'horse_all', 'milpay_total', 'create_at'
	);

	/** 城市公共越野字段 */
	static $sysHorseFields = array(
		'id', 'horse_date', 'cycle_no', 'stage', 'stage_endtime', 'stage_iscalc', 'stage_run_no', 'run_per_time', 'first_city_id',
		'first_award', 'horse1', 'horse2', 'horse3', 'horse4', 'horse5', 'horse6', 'horse7', 'join_log', 'award_log', 'award_data'
	);

	/** 消息字段 */
	static $messageFields = array(
		'id', 'title', 'content', 'sender', 'owner', 'flag', 'status', 'type', 'create_at'
	);

	static $cityHeroFields = array(
		'id', 'city_id', 'nickname', 'gender', 'quality', 'level', 'face_id', 'recycle',
		'exp', 'is_legend', 'attr_lead', 'attr_command', 'attr_military',
		'attr_energy', 'training_lead', 'training_command', 'training_military', 'attr_mood', 'stat_point', 'grow_rate',
		'equip_arm', 'equip_cap', 'equip_uniform', 'equip_medal', 'equip_shoes', 'equip_sit', 'equip_exp',
		'skill_slot_num', 'skill_slot', 'skill_slot_1', 'skill_slot_2',
		'win_num', 'draw_num', 'fail_num', 'relife_time', 'fight', 'flag', 'weapon_id',
		'army_id', 'army_num', 'create_at', 'fill_flag', 'sys_is_del', 'on_sale', 'march_id'
	);

	static $tplHeroFields = array(
		'id', 'nickname', 'gender', 'quality', 'level', 'face_id', 'exp', 'is_legend',
		'attr_lead', 'attr_command', 'attr_military', 'attr_energy', 'attr_mood',
		'stat_point', 'grow_rate', 'equip_arm', 'equip_cap', 'equip_uniform',
		'equip_shoes', 'equip_medal', 'equip_sit', 'skill_slot_num', 'skill_slot',
		'skill_slot_1', 'skill_slot_2', 'desc', 'detail', 'num', 'start_time',
		'end_time', 'succ_rate', 'hire_time', 'create_at');

	static $equipFields = array(
		'id', 'name', 'pos', 'face_id', 'type', 'city_id', 'need_level',
		'level', 'max_level', 'quality', 'base_lead', 'base_command',
		'base_military', 'is_locked', 'gold', 'ext_attr_name',
		'ext_attr_rate', 'ext_attr_skill', 'is_use', 'suit_id', 'desc_1',
		'desc_2', 'create_at', 'sys_is_del', 'on_sale', 'flag'
	);

	static $marchFields = array(
		'id', 'atk_city_id', 'atk_nickname', 'def_city_id', 'def_nickname', 'action_type',
		'hero_list', 'atk_pos', 'def_pos', 'arrived_time', 'wait_end_time', 'award', 'auto_fight',
		'flag', 'battle_id', 'atk_ext', 'def_ext', 'create_at', 'update_at', 'start_pos_ext'
	);

	static $unionFields = array(
		'id', 'face_id', 'name', 'coin', 'level', 'notice', 'rank', 'boss', 'create_nick_name',
		'create_city_id', 'total_person', 'total_renown', 'station_no', 'station_data', 'tech_data',
		'rel_friend', 'rel_enemy', 'create_at'
	);

	static $wildMapFields = array(
		'pos_no', 'type', 'city_id', 'npc_id', 'weather', 'march_id',
		'weather_refresh_time', 'terrain', 'hold_expire_time', 'scene_type', 'last_fill_army_time'
	);

	static $cityLotteryFields = array(
		'city_id', 'refresh_date', 'refresh_num', 'award_content', 'award_no', 'sys_sync_time',
	);

	static $cityQqShare = array(
		'city_id', 'complete_txt', 'award'
	);
	static $teamMultiFields = array(
		'id', 'type', 'multi_fb_id', 'city_pos', 'pos_1', 'pos_2', 'pos_3', 'pos_4', 'pos_5', 'sys_sync_time',
		'create_at', 'hold_def_line', 'npc_def_line', 'union_id', 'start_time'
	);

	static $cityMultiFields = array(
		'city_id', 'daily_date', 'daily_free_times', 'daily_buy_times', 'left_buy_times',
		'team_id', 'sys_sync_time',
	);
}

?>
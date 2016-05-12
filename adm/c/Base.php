<?php

class C_Base {
	/** 奖励 */
	static $AwardHeader = array(
		'id' => 'ID',
		'name' => '奖励名称',
		'type' => '奖励类型',
		'num' => '掉落数量',
		'val' => '奖励数据',
		'desc' => '描述',
	);

	/** 军官模板 */
	static $HeroTplHeader = array(
		'id' => 'ID',
		'nickname' => '军官名称',
		'gender' => '性别(1男2女)',
		'quality' => '品质',
		'level' => '等级',
		'face_id' => '头像',
		'attr_lead' => '防御',
		'attr_command' => '攻击',
		'attr_military' => '生命',
		'attr_energy' => '精力',
		'grow_rate' => '成长值',
		'army_id' => '兵种',
		'skill_slot_num' => '技能槽数量',
		'skill_slot' => '天赋技能ID',
		'skill_slot_1' => '技能1ID',
		'skill_slot_2' => '技能2ID',
		'desc' => '军官描述',
		'del' => '是否删除[1删除,0未删除]',

	);

	/** 据点 */
	static $campHeaderArr = array(
		'id' => 'ID',
		'title' => '据点名称',
		'open_week' => '开放周',
		'open_start_time' => '开放开始时间',
		'open_end_time' => '开放结束时间',
		'no_11' => '11NPC部队|战场地图',
		'no_12' => '12NPC部队|战场地图',
		'no_13' => '13NPC部队|战场地图',
		'no_14' => '14NPC部队|战场地图',
		'no_15' => '15NPC部队|战场地图',
		'no_16' => '16NPC部队|战场地图',
		'no_21' => '21NPC部队|战场地图',
		'no_22' => '22NPC部队|战场地图',
		'no_23' => '23NPC部队|战场地图',
		'no_31' => '31NPC部队|战场地图',
		'no_32' => '32NPC部队|战场地图',
		'no_41' => '41NPC部队|战场地图',
		'probe_event_data' => '巡逻事件概率',
		'probe_times' => '可探索次数',
		'award_id' => '团长奖励',
		'effect' => '据点效果(加成,联盟资金)',
		'is_open' => '是否开放',

	);

	/** 新手指引 */
	static $questHeaderArr = array(
		'id' => 'id',
		'prev_id' => '前置ID',
		'name' => '任务名称',
		'type' => '类型',
		'desc' => '描述',
		'guide' => '操作提示',
		'cond_pass' => '任务完成条件',
		'award_id' => '奖励ID',
		'level' => '等级',
		'cond_req' => '任务需求',
		'event' => '前端事件',
		'del' => '是否删除[1删除,0未删除]',
	);
	/** QQ分享*/
	static $qqShareHeaderArr = array(
		'id' => 'id',
		'name' => '任务名称',
		'type' => '类型',
		'desc' => '描述',
		'cond_pass' => '任务完成条件',
		'award_id' => '奖励ID',
		'title' => '分享的标题',
		'img' => '显示的应用图片URL',
		'summary' => '故事摘要',
		'msg' => '分享内容',
		'del' => '是否删除[1删除,0未删除]'
	);
	/** 事件 */
	static $ProbeHeader = array(
		'id' => 'ID',
		'title' => '事件',
		'type' => '类型',
		'award_id' => '奖励ID'
	);

	/** 道具 */
	static $PropsListHeader = array(
		'id' => 'ID',
		'name' => '道具名称',
		'desc' => '道具描述',
		'feature' => '道具功能',
		'face_id' => '道具图标地址',
		'type' => '道具类型[内政1,军官2,宝物3,战斗4,图纸5,材料6]',
		'price' => '道具购买价格[1军饷,2点卷]',
		'sys_price' => '道具出售价格[金钱]',
		'is_hot' => '是否热卖',
		'is_shop' => '是否可出现在商城',
		'is_locked' => '是否可绑定',
		'is_fall' => '是否可掉落',
		'is_vip_use' => '是否可用于VIP',
		'is_multi' => '是否可以批量使用',
		'max_times' => '最大使用次数',
		'interval' => '使用间隔',
		'effect_txt' => '道具效果标签',
		'effect_val' => '效果值',
		'effect_time' => '持续时间',
		'ext_class' => '用来控制逻辑流程调用的方法名',
		'sort' => '道具排序序号',
		'stack_num' => '叠加数',
	);

	/** 装备 */
	static $EquipListHeader = array(
		'id' => 'ID',
		'name' => '装备名称',
		'face_id' => '装备图片',
		'pos' => '装备位置(军帽1 军装2 武器3 军裤4 军鞋5 座驾6 经验法宝7)',
		'type' => '装备类型(1系统装备 2套装装备 3特殊装备)',
		'level' => '当前装备等级',
		'need_level' => '需要等级',
		'quality' => '装备品质(白1 绿2 蓝3 紫4 红5 金6)',
		'suit_id' => '所属套装',
		'base_lead' => '防御',
		'base_command' => '攻击',
		'base_military' => '生命',
		'is_locked' => '是否绑定(1是0否)',
		'is_vip_use' => '是否可用于VIP赠送(1是0否)',
		'desc_1' => '装备描述',
		'gold' => '出售价格',
		'flag' => '是否可以合成,升级,强化(1合成2升级4强化7合成升级强化)',
		'ext_attr_name' => '特殊属性 最大经验'

	);

	/** 装备套装 */
	static $EquipSuitHeader = array(
		'id' => 'ID',
		'name' => '套装装备名称',
		'effect' => '套装装备效果(几套(2,3,4,5,6):效果类型(1[指挥加成],2[军事加成],3[统帅加成],4[全属性值加成],5[所有兵种暴击加成],6[增加攻击力],7[增加防御力],8[生命加成],9[伤害加成],10[减少伤害]),效果值|使用兵种(0[所有兵种],1[步兵],2[炮兵],3[装甲兵],4[航空兵],5[没有兵种])|目标兵种|攻击类型(0[所有],SKY[对空],LAND[对地]);)',
		'desc' => '套装装备描述',
	);
	/** 技能 */
	static $SkillHeader = array(
		'id' => 'ID',
		'name' => '技能名称',
		'face_id' => '图标',
		'type' => '类型 [1普通,2特殊]',
		'level' => '等级',
		'is_repeat' => '是否重复学习[0不允许重复,1允许重复]',
		'sort' => '排序',
		'effect' => '效果类型(1[增加统帅(统帅%)],2[增加指挥(指挥%)],3[增加军事(军事%)],4[增加精力(精力%)],5[增加带兵数(兵数%)],6[减少带兵数(兵数%)],7[增加视野(视野)],
			8[增加移动力(移动力)],9[增加射程(射程)],10[降低视野(视野)],11[降低移动力(移动力)],12[降低射程(射程)],13[增加攻击(攻击%)],
			14[增加防御(防御%)],15[增加生命(生命%)],16[统帅增加攻击(统帅,攻击%)],17[指挥增加攻击(指挥,攻击%)],18[军事增加暴击几率(军事,暴击几率%)],
			19[兵数越少攻击越高(兵力%,攻击%;兵力%,攻击%)],20[敌方兵力大于自己,攻击越高(兵力%,攻击%;兵力%,攻击%)],
			21[距离提高攻击(距离,攻击%;距离,攻击%)],22[无法找到目标(范围)],23[无法移动(无)], 24[攻击持续伤害(伤害%)],25[攻击附加伤害(伤害%)],
			26[无法查看带兵数(无)],27[无法看到自己(无)],28[恢复士兵数(伤害值%)],29[增加闪避几率(闪避%)],30[增加暴击几率(暴击%)],31[增加伤害(伤害值%)],32[减少伤害(伤害值%)],33[无法攻击(无)],34[统帅增加防御(统帅,防御%)],35[军事增加生命(军事,生命%)])
			:几率|技能值|触发类型(ATK DEF ATK&DEF)|使用兵种(0[所有兵种],1[步兵],2[炮兵],3[装甲兵],4[航空兵])|目标兵种(0[所有兵种],1[步兵],2[炮兵],3[装甲兵],4[航空兵])|5攻击类型(0[所有],SKY[对空],LAND[对地])|6消耗精力|7影响回合数_',
		'desc' => '技能描述',
		'level_type' => '区分同一类型技能等级',
	);
	/** 武器 */
	static $WeaponListHeader = array(
		'id' => 'ID',
		'name' => '武器名称',
		'features' => '武器描述',
		'detail' => '武器详细介绍',
		'army_name' => '对应部队名称',
		'army_id' => '可装备兵种ID ',
		'need_army_lv' => '需要兵种等级',
		'march_type' => '出征系 [普通0,侦察1,轰炸2,间谍3]',
		'show_type' => '战场展示[1单个,2群体]',
		'sort' => '武器默认排序',
		'is_special' => '是否特殊武器 1是 0否',
		'is_npc' => '是否NPC武器 1是 0否',
		'life_value' => '生命值',
		'att_land' => '对地攻击力',
		'att_sky' => '对空攻击力',
		'att_ocean' => '对海攻击力',
		'def_land' => '对地防御力',
		'def_sky' => '对空防御力',
		'def_ocean' => '对海防御力',
		'add_effect' => '对其它某些武器的加成效果array(id=>+30,id2=>-10)',
		'speed' => '速度',
		'move_range' => '移动范围',
		'move_type' => '移动类型:1步行类,2车辆类,3飞行类,4航海类',
		'shot_range_min' => '射程最小值',
		'shot_range_max' => '射程最大值',
		'shot_type' => '射程类型:1直线型,2弧线型',
		'view_range' => '视野范围',
		'carry' => '掠夺量(运载量)',
		'att_num' => '攻击次数',
		'cost_gold' => '研发金钱消耗',
		'cost_food' => '研发粮食消耗',
		'cost_oil' => '研发石油消耗',
		'cost_time' => '研发冷却时间',
		'march_cost_oil' => '出征石油消耗',
		'march_cost_food' => '出征食物消耗',
		'need_tech' => '科技前提条件 (科技ID_1:等级_1)',
		'need_build' => '建筑前提条件 (建筑ID_1:等级_1)',

	);

	/** 商城 */
	static $MallListHeader = array(
		'id' => 'ID',
		'category' => '商城栏目[1内政道具,2军官道具,3宝物道具,4战斗道具,5图纸,6点券商城,7材料]',
		'item_type' => '物品类型[1道具,2军官,3装备]',
		'item_id' => '物品ID',
		'price' => '物品价格[1军饷,2点卷,3金钱,4突围积分,5活跃度积分]',
		'num' => '物品数量',
		'up_time' => '上架时间',
		'down_time' => '下架时间',
		'status' => '物品状态[1热卖2非热卖]',
		'sort' => '物品排列序号',
		'del' => '是否删除[1删除,0未删除]',
	);
	/** 积分对换商城 */
	static $MallExchangeListHeader = array(
		'id' => 'ID',
		'cate' => '1普通商城2VIP商城3积分商城',
		'nab' => '标签[1内政道具,2军官道具,3宝物道具,4战斗道具,5图纸,6点券商城,7材料,8突围积分,9积分商城]',
		'item_type' => '物品类型[1道具,2军官,3装备]',
		'item_id' => '物品ID',
		'price' => '物品价格[1军饷,2点卷,3金钱,4突围积分,5活跃度积分]',
		'num' => '物品数量',
		'up_time' => '上架时间',
		'down_time' => '下架时间',
		'status' => '物品状态[1热卖2非热卖]',
		'sort' => '物品排列序号',
		'del' => '是否删除[1删除,0未删除]',
	);
	/** 突围 */
	static $BoutListHeader = array(
		'id' => 'ID',
		'is_open' => '是否开启',
		'open_week' => '开启周',
		'open_start_time' => '开启开始时间',
		'open_end_time' => '开启结束时间',
		'next_boutid' => '下一个突围ID',
		'data' => 'npc部队ID,地图ID,宝箱奖励ID,积分|...',
		'del' => '是否删除[1删除,0未删除]',
	);

	static $MultiListHeader = array(
		'id' => 'ID',
		'name' => '名称',
		'type' => '类型(1,2,3)',
		'join_rule' => '加入条件(单人副本进度,城市等级(1-5),军官数量(1-5),威望,最少参与人数(1-5))',
		'award_no' => '奖励ID',
		'win_rule' => '胜利条件',
		'def_line' => '防线(11:部队ID2_地图编号,部队ID1_地图编号|12:部队ID_地图编号|21:部队ID_地图编号)',
		'time_limit' => '战功规则(小于分钟:获得战功, 30:100,45:80,60:50,120:20)',
		'award_list' => '战功奖励(战功1:奖励1,战功2:奖励2)',
		'award_desc' => '奖励描述(描述1|图片Id1&描述2|图片Id2)',
		'fb_desc' => '副本描述',
		'del' => '是否删除[1删除,0未删除]',
	);

	static $ExchangeHeader = array(
		'id' => 'ID',
		'name' => '名称',
		'type' => '类型(1道具,2装备)',
		'sub_type' => '子类型[道具(1,2,3,...)] 或 [装备(等级10,套装ID|等级20,套装ID|..)]',
		'need_props' => '需要道具(道具ID1,数量1|道具ID2,数量2)',
		'new_props' => '新道具ID|装备ID',
		'base_succ' => '基础成功率',
		'cost_val' => '消耗(milpay,100|coupon,10|gold,1000|oil,1000|food,100)',
		'start_time' => '开始时间',
		'end_time' => '结束时间',
		'desc' => '描述',
		'sort' => '排序',
		'del' => '是否删除[1删除,0未删除]',
	);

	static $RankRecordHeader = array(
		'RANK' => '排行',
		'Record' => '战绩',
		'CityID' => '城市ID',
		'NickName' => '昵称',
		'Renown' => '威望',
		'MilMedal' => '军功',
		'MilRank' => '军衔',
	);

	static $QuestionHeader = array(
		'id' => 'Id',
		'title' => '问题',
		'answer' => '回答(回答1|回答2|...)',
		'result' => '结果(序号|序号|...)'
	);

	static public function AInit() {
		if (!M_Adm::isLogin()) {
			B_Common::redirect('?r=Index/Login');
		}
	}

	static public function AIndex() {
		B_View::render('index');
	}

	/*-----建筑管理模块--开始-----------------------------------------------------*/
	//建筑基础数据 列表
	static public function ABuildBaseList() {
		$args = array(
			'page' => FILTER_SANITIZE_NUMBER_INT,
		);
		$formVals = filter_var_array($_REQUEST, $args);
		$offset = 100;
		$curPage = max(1, $formVals['page']);
		$start = ($curPage - 1) * $offset;
		$pageData['list'] = B_DB::instance('BaseBuild')->getList($start, $offset);
		$totalNum = B_DB::instance('BaseBuild')->count();
		$pageData['page'] = B_Page::make($curPage, $totalNum, $offset);

		B_View::setVal('pageData', $pageData);
		B_View::render('Base/BuildBaseList');
	}

	//建筑数据 删除
	static public function ADoBuildBaseDel() {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if ($id > 0) {

		}
		header("location:?r=Base/BuildBaseList&page=1");
	}

	//建筑基础数据 新增/修改
	static public function ABuildBaseAdd() {
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		$info = array();
		if (!empty($id)) {
			$info = M_Build::baseInfo($id);
		}
		$pageData['info'] = $info;
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/BuildBaseAdd');
	}

	//保存 建筑基础数据 新增/修改
	static public function ADoBuildBaseAdd() {
		if (isset($_REQUEST['name']) && isset($_REQUEST['is_moved']) && isset($_REQUEST['is_multi'])
			&& isset($_REQUEST['is_beautify']) && isset($_REQUEST['max_level'])
		) {
			$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
			$name = $_REQUEST['name'];
			$features = isset($_REQUEST['features']) ? $_REQUEST['features'] : '';
			$is_moved = $_REQUEST['is_moved'];
			$is_multi = $_REQUEST['is_multi'];
			$is_beautify = $_REQUEST['is_beautify'];
			$max_level = $_REQUEST['max_level'];
			$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 1;
			$desc_1 = isset($_REQUEST['desc_1']) ? $_REQUEST['desc_1'] : '';
			$info = array(
				'name' => $name,
				'features' => $features,
				'is_moved' => $is_moved,
				'is_multi' => $is_multi,
				'is_beautify' => $is_beautify,
				'max_level' => $max_level,
				'sort' => $sort,
				'desc_1' => $desc_1,
			);
			$ret1 = false;
			$ret2 = false;
			if ($id > 0) {
				$ret1 = B_DB::instance('BaseBuild')->update($info, $id);
			} else {
				$ret2 = B_DB::instance('BaseBuild')->insert($info);
			}
			($ret1 || $ret2) && M_Build::delBuildBaseCache(); //删缓存
		}
		header("location:?r=Base/BuildBaseList&page=1");
	}

	//建筑升级数据 列表
	static public function ABuildUpgList() {
		$id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 1;
		$args = array(
			'page' => FILTER_SANITIZE_NUMBER_INT,
		);
		$formVals = filter_var_array($_REQUEST, $args);

		$totalNum = B_DB::instance('BaseBuildAttr')->count(array('build_id'=>$id));
		$buildBase = M_Base::buildAll();
		$techBase = M_Base::techAll();

		$pageData['list'] = B_DB::instance('BaseBuildAttr')->getList(0, $totalNum, array('build_id' => $id));

		$pageData['page'] = B_Page::make(1, $totalNum, $totalNum);
		$pageData['id'] = $id;
		$pageData['buildBase'] = $buildBase;
		$pageData['techBase'] = $techBase;

		B_View::setVal('pageData', $pageData);
		B_View::render('Base/BuildUpgList');
	}

	//建筑升级数据 删除
	static public function ADoBuildUpgDel() {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$level = isset($_REQUEST['level']) ? intval($_REQUEST['level']) : 0;
		if ($id > 0 && $level > 0) {
			$ret1 = B_DB::instance('BaseBuild')->deleteBy(array('build_id' => $id, 'level' => $level)); //删除升级数据
			$ret1 && M_Build::delBuildUpgCache(); //删缓存
		}
		header("location:?r=Base/BuildUpgList&id={$id}&page=1");
	}

	//建筑升级数据 新增/修改
	static public function ABuildUpgAdd() {
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		$level = isset($_REQUEST['level']) ? $_REQUEST['level'] : 0;
		$info = array();
		if ($id > 0 && $level > 0) {
			$info = M_Build::baseUpgInfo($id, $level);
		}
		$buildBase = M_Base::buildAll();
		$techBase = M_Base::techAll();

		$pageData['info'] = $info;
		$pageData['id'] = $id;
		$pageData['buildBase'] = $buildBase;
		$pageData['techBase'] = $techBase;
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/BuildUpgAdd');
	}

	//保存 建筑升级数据 新增/修改
	static public function ADoBuildUpgAdd() {
		$id = 1;
		if (!empty($_REQUEST['id']) && !empty($_REQUEST['level']) && !empty($_POST)) {
			$record_id = isset($_REQUEST['record_id']) ? $_REQUEST['record_id'] : 0;
			$id = $_REQUEST['id'];
			$level = $_REQUEST['level'];
			$cost_gold = isset($_REQUEST['cost_gold']) ? $_REQUEST['cost_gold'] : 0;
			$cost_food = isset($_REQUEST['cost_food']) ? $_REQUEST['cost_food'] : 0;
			$cost_oil = isset($_REQUEST['cost_oil']) ? $_REQUEST['cost_oil'] : 0;
			$cost_time = isset($_REQUEST['cost_time']) ? $_REQUEST['cost_time'] : 0;
			$need_build_id = isset($_REQUEST['need_build_id']) ? $_REQUEST['need_build_id'] : 0;
			$need_build_level = isset($_REQUEST['need_build_level']) ? $_REQUEST['need_build_level'] : 0;
			$need_tech_id = isset($_REQUEST['need_tech_id']) ? $_REQUEST['need_tech_id'] : 0;
			$need_tech_level = isset($_REQUEST['need_tech_level']) ? $_REQUEST['need_tech_level'] : 0;
			$effect_code = isset($_REQUEST['effect_code']) ? $_REQUEST['effect_code'] : 0;
			$effect_val = isset($_REQUEST['effect_val']) ? $_REQUEST['effect_val'] : 0;

			$need_build = (!empty($need_build_id) && !empty($need_build_level)) ? json_encode(array($need_build_id => $need_build_level)) : '[]';
			$need_tech = (!empty($need_tech_id) && !empty($need_tech_level)) ? json_encode(array($need_tech_id => $need_tech_level)) : '[]';
			$effect = (!empty($effect_code) && !empty($effect_val)) ? json_encode(array($effect_code => $effect_val)) : '[]';
			$info = array(
				'cost_gold' => $cost_gold,
				'cost_food' => $cost_food,
				'cost_oil' => $cost_oil,
				'cost_time' => $cost_time,
				'need_build' => $need_build,
				'need_tech' => $need_tech,
				'effect' => $effect,
			);
			$ret1 = false;
			$ret2 = false;
			if ($record_id > 0) {
				$ret1 = B_DB::instance('BaseBuildAttr')->updateBy($info, array('build_id' => $id, 'level' => $level));
			} else {
				$info['build_id'] = $id;
				$info['level'] = $level;
				$ret2 = B_DB::instance('BaseBuildAttr')->insert($info);
			}
			($ret1 || $ret2) && M_Build::delBuildUpgCache(); //删缓存
		}
		header("location:?r=Base/BuildUpgList&page=1&id={$id}");
	}
	/*-----建筑管理模块--结束-----------------------------------------------------*/

	/*-----科技管理模块--开始-----------------------------------------------------*/
	//科技基础数据 列表
	static public function ATechBaseList() {
		$args = array(
			'page' => FILTER_SANITIZE_NUMBER_INT,
		);
		$formVals = filter_var_array($_REQUEST, $args);
		$offset = 100;
		$curPage = max(1, $formVals['page']);
		$start = ($curPage - 1) * $offset;
		$pageData['list'] = B_DB::instance('BaseTech')->getList($start, $offset);
		$totalNum = B_DB::instance('BaseTech')->count();
		$pageData['page'] = B_Page::make($curPage, $totalNum, $offset);

		B_View::setVal('pageData', $pageData);
		B_View::render('Base/TechBaseList');
	}

	//科技数据 删除
	static public function ADoTechBaseDel() {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if ($id > 0) {

		}
		header("location:?r=Base/TechBaseList&page=1");
	}

	//科技基础数据 新增/修改
	static public function ATechBaseAdd() {
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		$info = array();
		if (!empty($id)) {
			$info = M_Tech::baseInfo($id);
		}
		$pageData['info'] = $info;
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/TechBaseAdd');
	}

	//保存 科技基础数据 新增/修改
	static public function ADoTechBaseAdd() {
		if (isset($_REQUEST['name']) && isset($_REQUEST['features']) && isset($_REQUEST['max_level'])) {
			$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
			$name = $_REQUEST['name'];
			$features = isset($_REQUEST['features']) ? $_REQUEST['features'] : '';
			$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 1;
			$max_level = $_REQUEST['max_level'];
			$desc_1 = isset($_REQUEST['desc_1']) ? $_REQUEST['desc_1'] : '';
			$info = array(
				'name' => $name,
				'features' => $features,
				'type' => $type,
				'max_level' => $max_level,
				'desc_1' => $desc_1,
			);
			$ret1 = false;
			$ret2 = false;
			if ($id > 0) {
				$ret1 = B_DB::instance('BaseTech')->update($info, $id);
			} else {
				$ret2 = B_DB::instance('BaseTech')->insert($info);
			}
			($ret1 || $ret2) && M_Tech::delTechBaseCache(); //删缓存
		}
		header("location:?r=Base/TechBaseList&page=1");
	}

	//科技升级数据 列表
	static public function ATechUpgList() {
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 1;
		$args = array(
			'page' => FILTER_SANITIZE_NUMBER_INT,
		);
		$formVals = filter_var_array($_REQUEST, $args);
		$curPage = max(1, $formVals['page']);
		$param = array('tech_id' => $id);
		$totalNum = B_DB::instance('BaseTechAttr')->count($param);
		$offset = $totalNum;
		$buildBase = M_Base::buildAll();
		$techBase = M_Base::techAll();
		$start = ($curPage - 1) * $offset;
		$pageData['list'] = B_DB::instance('BaseTechAttr')->getList($start, $offset, $param);
		$pageData['page'] = B_Page::make($curPage, $totalNum, $offset);
		$pageData['id'] = $id;
		$pageData['buildBase'] = $buildBase;
		$pageData['techBase'] = $techBase;

		B_View::setVal('pageData', $pageData);
		B_View::render('Base/TechUpgList');
	}

	//科技升级数据 删除
	static public function ADoTechUpgDel() {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$level = isset($_REQUEST['level']) ? intval($_REQUEST['level']) : 0;
		if ($id > 0 && $level > 0) {
			$ret1 = B_DB::instance('BaseTechAttr')->deleteBy(array('tech_id' => $id, 'level' => $level)); //删除升级数据
			$ret1 && M_Tech::delTechUpgCache(); //删缓存
		}
		header("location:?r=Base/TechUpgList&id={$id}&page=1");
	}

	//科技升级数据 新增/修改
	static public function ATechUpgAdd() {
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		$level = isset($_REQUEST['level']) ? $_REQUEST['level'] : 0;
		$info = array();
		if ($id > 0 && $level > 0) {
			$info = M_Tech::getUpgInfoByLevel($id, $level);
		}
		$buildBase = M_Base::buildAll();
		$techBase = M_Base::techAll();

		$pageData['info'] = $info;
		$pageData['id'] = $id;
		$pageData['buildBase'] = $buildBase;
		$pageData['techBase'] = $techBase;
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/TechUpgAdd');
	}

	//保存 科技升级数据 新增/修改
	static public function ADoTechUpgAdd() {
		$id = 0;
		if (!empty($_REQUEST['id']) && !empty($_REQUEST['level'])) {
			$record_id = isset($_REQUEST['record_id']) ? $_REQUEST['record_id'] : 0;
			$id = $_REQUEST['id'];
			$level = $_REQUEST['level'];
			$cost_gold = isset($_REQUEST['cost_gold']) ? $_REQUEST['cost_gold'] : 0;
			$cost_food = isset($_REQUEST['cost_food']) ? $_REQUEST['cost_food'] : 0;
			$cost_oil = isset($_REQUEST['cost_oil']) ? $_REQUEST['cost_oil'] : 0;
			$cost_time = isset($_REQUEST['cost_time']) ? $_REQUEST['cost_time'] : 0;
			$need_build_id = isset($_REQUEST['need_build_id']) ? $_REQUEST['need_build_id'] : 0;
			$need_build_level = isset($_REQUEST['need_build_level']) ? $_REQUEST['need_build_level'] : 0;
			$need_tech_id = isset($_REQUEST['need_tech_id']) ? $_REQUEST['need_tech_id'] : 0;
			$need_tech_level = isset($_REQUEST['need_tech_level']) ? $_REQUEST['need_tech_level'] : 0;
			$effect_code = isset($_REQUEST['effect_code']) ? $_REQUEST['effect_code'] : 0;
			$effect_val = isset($_REQUEST['effect_val']) ? $_REQUEST['effect_val'] : 0;

			$need_build = (!empty($need_build_id) && !empty($need_build_level)) ? json_encode(array($need_build_id => $need_build_level)) : '[]';
			$need_tech = (!empty($need_tech_id) && !empty($need_tech_level)) ? json_encode(array($need_tech_id => $need_tech_level)) : '[]';
			$effect = (!empty($effect_code) && !empty($effect_val)) ? json_encode(array($effect_code => $effect_val)) : '[]';
			$info = array(
				'cost_gold' => $cost_gold,
				'cost_food' => $cost_food,
				'cost_oil' => $cost_oil,
				'cost_time' => $cost_time,
				'need_build' => $need_build,
				'need_tech' => $need_tech,
				'effect' => $effect,
			);
			$ret1 = false;
			$ret2 = false;
			if ($id > 0 && $level > 0) {
				if ($record_id > 0) {
					$ret1 = B_DB::instance('BaseTechAttr')->updateBy($info, array('tech_id' => id, 'level' => $level));
				} else {
					$info['tech_id'] = $id;
					$info['level'] = $level;
					$ret2 = B_DB::instance('BaseTechAttr')->insert($info);
				}
				($ret1 || $ret2) && M_Tech::delTechUpgCache(); //删缓存
			}
		}
		header("location:?r=Base/TechUpgList&page=1&id={$id}");
	}
	/*-----科技管理模块--结束-----------------------------------------------------*/

	/*-----兵种管理模块--开始-----------------------------------------------------*/
	//兵种数据 列表
	static public function AArmyList() {
		$list = M_Base::armyAll();
		$totalNum = count($list);
		$offset = $totalNum;
		$pageData['list'] = $list;

		B_View::setVal('pageData', $pageData);
		B_View::render('Base/ArmyList');
	}

	//兵种基础数据 新增/修改
	static public function AArmyAdd() {
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		$info = array();
		if (!empty($id)) {
			$list = M_Base::armyAll();
			$info = $list[$id];
		}
		$pageData['info'] = $info;
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/ArmyAdd');
	}
	/*-----兵种管理模块--结束-----------------------------------------------------*/

	/*-----武器管理模块--开始-----------------------------------------------------*/
	//武器数据 列表
	static public function AWeaponList() {
		$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
		$curPage = max(1, $page);
		$is_npc = isset($_REQUEST['is_npc']) ? $_REQUEST['is_npc'] : M_Weapon::NOTNPC;

		if (isset($_GET['db'])) {
			$pageData['list'] = B_DB::instance('BaseWeapon')->getNpcList();
		} else {
			$list = M_Base::weaponAll();
			$totalNum = count($list);
			$tmpList = array();
			foreach ($list as $id => $val) {
				if ($val['is_npc'] == $is_npc) {
					$tmpList[$id] = $val;
				}
			}
			$pageData['list'] = $tmpList;
		}

		$pageData['page'] = array();
		$pageData['is_npc'] = $is_npc;

		B_View::setVal('pageData', $pageData);
		B_View::render('Base/WeaponList');
	}

	//武器数据 删除
	static public function ADoWeaponDel() {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if ($id > 0) {

			$ret1 = B_DB::instance('BaseWeapon')->delete($id); //删除基础数据
			$ret1 && B_Cache_APC::del(T_Key::BASE_WEAPON); //删缓存

		}
		header("location:?r=Base/WeaponList&page=1");
	}

	//武器基础数据 新增/修改
	static public function AWeaponAdd() {
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		$info = array();
		if (!empty($id)) {
			$info = M_Weapon::baseInfo($id);
		}
		$buildBase = M_Base::buildAll();
		$techBase = M_Base::techAll();

		$pageData['info'] = $info;
		$pageData['id'] = $id;
		$pageData['buildBase'] = $buildBase;
		$pageData['techBase'] = $techBase;

		B_View::setVal('pageData', $pageData);
		B_View::render('Base/WeaponAdd');
	}

	//保存 武器数据 新增/修改
	static public function ADoWeaponAdd() {
		$is_npc = M_Weapon::NOTNPC;
		if (!empty($_POST)) {
			$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
			$name = !empty($_REQUEST['name']) ? $_REQUEST['name'] : '默认武器名字';
			$features = !empty($_REQUEST['features']) ? $_REQUEST['features'] : '默认描述';
			$detail = !empty($_REQUEST['detail']) ? $_REQUEST['detail'] : '';
			$army_name = !empty($_REQUEST['army_name']) ? $_REQUEST['army_name'] : '默认部队名字';
			$army_id = isset($_REQUEST['army_id']) ? $_REQUEST['army_id'] : M_Army::ID_FOOT;
			$need_army_lv = isset($_REQUEST['need_army_lv']) ? $_REQUEST['need_army_lv'] : 0;
			$march_type = isset($_REQUEST['march_type']) ? $_REQUEST['march_type'] : M_War::MARCH_NOMAL;
			$show_type = isset($_REQUEST['show_type']) ? $_REQUEST['show_type'] : M_Weapon::WAR_SHOW_ONE;
			$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 0;
			$is_special = isset($_REQUEST['is_special']) ? $_REQUEST['is_special'] : M_Weapon::COMMON;
			$is_npc = isset($_REQUEST['is_npc']) ? $_REQUEST['is_npc'] : M_Weapon::NOTNPC;
			$life_value = isset($_REQUEST['life_value']) ? $_REQUEST['life_value'] : 0;
			$att_land = isset($_REQUEST['att_land']) ? $_REQUEST['att_land'] : 0;
			$att_sky = isset($_REQUEST['att_sky']) ? $_REQUEST['att_sky'] : 0;
			$att_ocean = isset($_REQUEST['att_ocean']) ? $_REQUEST['att_ocean'] : 0;
			$def_land = isset($_REQUEST['def_land']) ? $_REQUEST['def_land'] : 0;
			$def_sky = isset($_REQUEST['def_sky']) ? $_REQUEST['def_sky'] : 0;
			$def_ocean = isset($_REQUEST['def_ocean']) ? $_REQUEST['def_ocean'] : 0;
			$speed = isset($_REQUEST['speed']) ? $_REQUEST['speed'] : 0;
			$move_range = isset($_REQUEST['move_range']) ? $_REQUEST['move_range'] : 1;
			$move_type = isset($_REQUEST['move_type']) ? $_REQUEST['move_type'] : 1;
			$shot_range_min = isset($_REQUEST['shot_range_min']) ? $_REQUEST['shot_range_min'] : 1;
			$shot_range_max = isset($_REQUEST['shot_range_max']) ? $_REQUEST['shot_range_max'] : 1;
			$shot_type = isset($_REQUEST['shot_type']) ? $_REQUEST['shot_type'] : 1;
			$view_range = isset($_REQUEST['view_range']) ? $_REQUEST['view_range'] : 0;
			$carry = isset($_REQUEST['carry']) ? $_REQUEST['carry'] : 0;
			$att_num = isset($_REQUEST['att_num']) ? $_REQUEST['att_num'] : 0;
			$cost_gold = isset($_REQUEST['cost_gold']) ? $_REQUEST['cost_gold'] : 0;
			$cost_food = isset($_REQUEST['cost_food']) ? $_REQUEST['cost_food'] : 0;
			$cost_oil = isset($_REQUEST['cost_oil']) ? $_REQUEST['cost_oil'] : 0;
			$cost_time = isset($_REQUEST['cost_time']) ? $_REQUEST['cost_time'] : 0;
			$march_cost_oil = isset($_REQUEST['march_cost_oil']) ? $_REQUEST['march_cost_oil'] : 0;
			$march_cost_food = isset($_REQUEST['march_cost_food']) ? $_REQUEST['march_cost_food'] : 0;

			$need_build_id = isset($_REQUEST['need_build_id']) ? $_REQUEST['need_build_id'] : 0;
			$need_build_level = isset($_REQUEST['need_build_level']) ? $_REQUEST['need_build_level'] : 0;
			$need_tech_id = isset($_REQUEST['need_tech_id']) ? $_REQUEST['need_tech_id'] : 0;
			$need_tech_level = isset($_REQUEST['need_tech_level']) ? $_REQUEST['need_tech_level'] : 0;

			$need_build = (!empty($need_build_id) && !empty($need_build_level)) ? json_encode(array($need_build_id => $need_build_level)) : '[]';
			$need_tech = (!empty($need_tech_id) && !empty($need_tech_level)) ? json_encode(array($need_tech_id => $need_tech_level)) : '[]';

			$info = array(
				'name' => $name,
				'features' => $features,
				'detail' => $detail,
				'army_name' => $army_name,
				'army_id' => $army_id,
				'need_army_lv' => $need_army_lv,
				'march_type' => $march_type,
				'show_type' => $show_type,
				'sort' => $sort,
				'is_special' => $is_special,
				'is_npc' => $is_npc,
				'life_value' => $life_value,
				'att_land' => $att_land,
				'att_sky' => $att_sky,
				'att_ocean' => $att_ocean,
				'def_land' => $def_land,
				'def_sky' => $def_sky,
				'def_ocean' => $def_ocean,
				'speed' => $speed,
				'move_range' => $move_range,
				'move_type' => $move_type,
				'shot_range_min' => $shot_range_min,
				'shot_range_max' => $shot_range_max,
				'shot_type' => $shot_type,
				'view_range' => $view_range,
				'carry' => $carry,
				'att_num' => $att_num,
				'cost_gold' => $cost_gold,
				'cost_food' => $cost_food,
				'cost_oil' => $cost_oil,
				'cost_time' => $cost_time,
				'march_cost_oil' => $march_cost_oil,
				'march_cost_food' => $march_cost_food,
				'need_build' => $need_build,
				'need_tech' => $need_tech,
			);
			$ret1 = false;
			$ret2 = false;
			if ($id > 0) {
				$ret1 = B_DB::instance('BaseWeapon')->update($info, $id);
			} else {
				$ret2 = B_DB::instance('BaseWeapon')->insert($info);
			}
			APC::del(T_Key::BASE_WEAPON); //删缓存
		}

		header("location:?r=Base/WeaponList&page=1&is_npc={$is_npc}");
	}
	/*-----武器管理模块--结束-----------------------------------------------------*/

	/*-----道具管理模块--开始-----------------------------------------------------*/
	//道具数据 列表
	static public function APropsList() {
		$args = array(
			'page' => FILTER_SANITIZE_NUMBER_INT,
		);
		$formVals = filter_var_array($_REQUEST, $args);
		$length = 50;
		$curPage = max(1, $formVals['page']);

		if (isset($_GET['db'])) {
			$start = ($curPage - 1) * $length;
			$pageData['list'] = B_DB::instance('BaseProps')->getRowsByPage($start, $length);
			$totalNum = B_DB::instance('BaseProps')->count();
		} else {
			$list = M_Base::propsAll();
			$offset = ($curPage - 1) * $length;
			$pageData['list'] = array_slice($list, $offset, $length);
			$totalNum = count($list);
		}

		$pageData['page'] = B_Page::make($curPage, $totalNum, $length, 20);

		B_View::setVal('pageData', $pageData);
		B_View::render('Base/PropsList');
	}

	//道具数据 删除
	static public function ADoPropsDel() {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if ($id > 0) {
			$ret1 = B_DB::instance('BaseProps')->delete($id); //删除基础数据
			$ret1 && M_Props::delPropsCache(); //删缓存
		}
		header("location:?r=Base/PropsList&page=1");
	}

	//道具基础数据 新增/修改
	static public function APropsAdd() {
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		$info = array();

		if (!empty($id)) {
			$info = B_DB::instance('BaseProps')->get($id);
		}

		/** 模板装备列表 */
		$equipList = M_Equip::getEquipTplList();
		$tmpEquipArr = array();
		if (!empty($equipList)) {
			foreach ($equipList as $equipId) {
				$tmpEquipArr[$equipId] = M_Equip::baseInfo($equipId);
			}
		}

		/** 全部军官模板 */
		$pageData['baseHero'] = B_DB::instance('BaseHeroTpl')->all();

		$pageData['beautify'] = M_Build::getBeautifyIdName(); //装饰建筑数据
		$pageData['weapon'] = M_Weapon::getSpecialIdName(); //特殊武器数据
		$pageData['props'] = M_Props::getPropsIdName(); //道具(除礼包)
		$pageData['equipList'] = $tmpEquipArr;
		$pageData['info'] = $info;
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/PropsAdd');
	}

	//保存 道具数据 新增/修改
	static public function ADoPropsAdd() {
		if (isset($_REQUEST['name']) && isset($_REQUEST['type']) && isset($_REQUEST['effect_txt'])) {
			$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
			$name = $_REQUEST['name'];
			$desc = isset($_REQUEST['desc']) ? $_REQUEST['desc'] : '';
			$feature = isset($_REQUEST['feature']) ? $_REQUEST['feature'] : '';
			$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 0;

			$price1 = isset($_REQUEST['price1']) ? $_REQUEST['price1'] : 0;
			$price2 = isset($_REQUEST['price2']) ? $_REQUEST['price2'] : 0;

			$is_hot = isset($_REQUEST['is_hot']) ? $_REQUEST['is_hot'] : 0;
			$is_shop = isset($_REQUEST['is_shop']) ? $_REQUEST['is_shop'] : 1;
			$is_fall = isset($_REQUEST['is_fall']) ? $_REQUEST['is_fall'] : 1;
			$is_vip_use = isset($_REQUEST['is_vip_use']) ? $_REQUEST['is_vip_use'] : 0;

			$effect_txt = isset($_REQUEST['effect_txt']) ? $_REQUEST['effect_txt'] : '';
			$effect_val = isset($_REQUEST['effect_val']) ? $_REQUEST['effect_val'] : 0;
			$effect_time = isset($_REQUEST['effect_time']) ? $_REQUEST['effect_time'] : 0;
			$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 0;
			$sys_price = isset($_REQUEST['sys_price']) ? $_REQUEST['sys_price'] : 0;

			$arrPrice = array();
			$price1 > 0 && $arrPrice[T_App::MILPAY] = $price1;
			$price2 > 0 && $arrPrice[T_App::COUPON] = $price2;
			$price = json_encode($arrPrice);
			$info = array(
				'name' => $name,
				'desc' => $desc,
				'feature' => $feature,
				'type' => $type,
				'price' => $price,
				'sys_price' => $sys_price,
				'is_hot' => $is_hot,
				'is_shop' => $is_shop,
				'is_fall' => $is_fall,
				'is_vip_use' => $is_vip_use,
				'effect_txt' => $effect_txt,
				'effect_val' => $effect_val,
				'effect_time' => $effect_time,
				'sort' => $sort,
			);
			$ret1 = false;
			$ret2 = false;
			if ($id > 0) {
				$ret1 = B_DB::instance('BaseProps')->set($id, $info);
			} else {
				$ret2 = B_DB::instance('BaseProps')->insert($info);
			}
			($ret1 || $ret2) && M_Props::delPropsCache(); //删缓存
		}
		header("location:?r=Base/PropsList&page=1");
	}
	/*-----道具管理模块--结束-----------------------------------------------------*/

	/*-----任务管理模块--开始-----------------------------------------------------*/
	//任务数据 列表
	static public function ATaskList() {
		$args = array(
			'page' => FILTER_SANITIZE_NUMBER_INT,
		);
		$formVals = filter_var_array($_REQUEST, $args);
		$offset = 20;
		$curPage = max(1, $formVals['page']);
		$start = ($curPage - 1) * $offset;
		$pageData['list'] = B_DB::instance('BaseTask')->getList($start, $offset);
		$totalNum = B_DB::instance('BaseTask')->count();
		$pageData['page'] = B_Page::make($curPage, $totalNum, $offset);

		B_View::setVal('pageData', $pageData);
		B_View::render('Base/TaskList');
	}

	//任务数据 删除
	static public function ADoTaskDel() {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if ($id > 0) {
			$ret1 = B_DB::instance('BaseTask')->delete($id); //删除基础数据
			$ret1 && M_Task::delTaskCache(); //删缓存
		}
		header("location:?r=Base/TaskList&page=1");
	}

	/** 更新任务缓存 */
	static public function ATaskCacheUp() {
		$ret = M_Base::taskAll(true);

		echo "<script>";
		echo $ret ? "alert('更新成功');" : "alert('更新失败');";
		echo "</script>";
	}

	//任务基础数据 新增/修改
	static public function ATaskAdd() {
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		$info = array();
		if (!empty($id)) {
			$list = B_DB::instance('BaseTask')->all();
			$info = isset($list[$id]) ? $list[$id] : array();;
		}

		/** 模板装备列表 */

		$equipList = B_DB::instance('BaseEquipTpl')->all();
		foreach ($equipList as $key => $val) {
			$pageData['equipList'][$val['id']] = $val;
		}
		/** 全部军官模板 */
		$pageData['baseHero'] = B_DB::instance('BaseHeroTpl')->all();

		//$pageData['beautify'] = M_Build::getBeautifyIdName();	//装饰建筑数据
		//$pageData['weapon'] = M_Weapon::getSpecialIdName();	//特殊武器数据
		$pageData['props'] = M_Props::getPropsIdName(); //道具(除礼包)

		$pageData['info'] = $info;
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/TaskAdd');
	}

	//保存 任务数据 新增/修改
	static public function ADoTaskAdd() {
		if (isset($_REQUEST['title']) && isset($_REQUEST['type'])) {
			$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
			$title = $_REQUEST['title'];
			$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 1;
			$desc_aim = isset($_REQUEST['desc_aim']) ? $_REQUEST['desc_aim'] : '';
			$desc_intro = isset($_REQUEST['desc_intro']) ? $_REQUEST['desc_intro'] : '';
			$desc_guide = isset($_REQUEST['desc_guide']) ? $_REQUEST['desc_guide'] : '';
			$desc_finish = isset($_REQUEST['desc_finish']) ? $_REQUEST['desc_finish'] : '';
			$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 0;
			$award_id = isset($_REQUEST['award_id']) ? $_REQUEST['award_id'] : 0;

			$info = array(
				'title' => $title,
				'type' => $type,
				'desc_aim' => $desc_aim,
				'desc_intro' => $desc_intro,
				'desc_guide' => $desc_guide,
				'desc_finish' => $desc_finish,
				'award' => '[]',
				'sort' => $sort,
				'award_id' => $award_id,
			);

			if ($id > 0) {
				$ret = B_DB::instance('BaseTask')->update($info,$id);

			} else {
				$info['need'] = '[]';
				$info['create_at'] = time();
				$ret = B_DB::instance('BaseTask')->insert($info);
			}
			APC::del(T_Key::BASE_TASK); //删缓存
		}
		header("location:?r=Base/TaskList&page=1");
	}
	/*-----任务管理模块--结束-----------------------------------------------------*/

	/**
	 * 模板装备列表
	 * @author HeJunyun
	 */
	static public function AEquipList() {
		$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
		$pageData['order'] = $order;
		if ($order == 'quality') {
			$order = $order . ' DESC, need_level DESC,';
		}
		$args = array(
			'page' => FILTER_SANITIZE_NUMBER_INT,
			'equip_id' => FILTER_SANITIZE_NUMBER_INT,
			'quality' => FILTER_SANITIZE_NUMBER_INT,
			'suit_id' => FILTER_SANITIZE_NUMBER_INT,
		);
		$formVals = filter_var_array($_REQUEST, $args);
		$offset = 50;
		$curPage = max(1, $formVals['page']);
		$pageData['parms'] = array();
		if ($formVals['equip_id'] > 0) {
			$pageData['parms']['equip_id'] = $formVals['equip_id'];
		}
		if ($formVals['quality'] > 0) {
			$pageData['parms']['quality'] = $formVals['quality'];
		}
		if ($formVals['suit_id'] > 0) {
			$pageData['parms']['suit_id'] = $formVals['suit_id'];
		}
		$pageData['suit'] = B_DB::instance('BaseEquipSuit')->getAll();
		$start = ($curPage - 1) * $offset;
		$pageData['list'] = B_DB::instance('BaseEquipTpl')->getList($start, $offset, $pageData['parms'], $order);
		foreach ($pageData['list'] as $key => $val) {
			switch ($pageData['list'][$key]['quality']) {
				case 1:
					$pageData['list'][$key]['color'] = 'white';
					break;
				case 2:
					$pageData['list'][$key]['color'] = 'green';
					break;
				case 3:
					$pageData['list'][$key]['color'] = 'blue';
					break;
				case 4:
					$pageData['list'][$key]['color'] = 'purple';
					break;
				case 5:
					$pageData['list'][$key]['color'] = 'red';
					break;
				case 6:
					$pageData['list'][$key]['color'] = 'orange';
					break;
			}
			$pageData['list'][$key]['quality'] = T_Word::$EQUIP_QUAL[$pageData['list'][$key]['quality']];

			/**
			 * $quality = $pageData['list'][$key]['quality'];
			 * $qualityType = array(1=>'white',2=>'green',3=>'blue',4=>'purple',5=>'red',6=>'red',7=>'orange');
			 * $pageData['list'][$key]['color'] = $qualityType[$quality];
			 * $pageData['list'][$key]['quality'] = T_Word::$EQUIP_QUAL[$quality];
			 **/

		}
		$totalNum = B_DB::instance('BaseEquipTpl')->count($pageData['parms']);
		$pageData['page'] = B_Page::make($curPage, $totalNum, $offset, 10);

		B_View::setVal('pageData', $pageData);
		B_View::render('Base/EquipList');
	}

	/**
	 * 模板装备设置所属套装
	 * @author HeJunyun
	 */
	static public function ASetTplSuit() {
		$args = array(
			'id' => FILTER_SANITIZE_NUMBER_INT,
			'suit_id' => FILTER_SANITIZE_NUMBER_INT,
		);
		$formVals = filter_var_array($_REQUEST, $args);
		if ($formVals['id'] > 0) {
			$result = B_DB::instance('BaseEquipTpl')->update($formVals, $formVals['id']);
			if ($result) {
				$flag = 1;
				$msg = '修改成功！';
			} else {
				$flag = 0;
				$msg = '修改失败！';
			}
		}
		$json = array(
			'flag' => $flag,
			'msg' => $msg
		);
		echo json_encode($json);
	}

	/**
	 * 添加基表装备页面
	 * @author HeJunyun
	 */
	static public function AEquipView() {
		$pageData = array();
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		if ($id > 0) {
			$info = B_DB::instance('BaseEquipTpl')->get($id);
			$pageData['info'] = $info;
		}

		B_View::setVal('pageData', $pageData);
		B_View::render('Base/EquipView');
	}

	/**
	 * 添加模板装备执行...
	 * @author HeJunyun
	 */
	static public function AEquipEdit() {
		$act = isset($_POST['act']) ? $_POST['act'] : '';
		$args = array(
			'name' => FILTER_SANITIZE_STRING,
			'pos' => FILTER_SANITIZE_NUMBER_INT,
			'type' => FILTER_SANITIZE_NUMBER_INT,
			'need_level' => FILTER_SANITIZE_NUMBER_INT,
			'quality' => FILTER_SANITIZE_NUMBER_INT,
			'base_lead' => FILTER_SANITIZE_NUMBER_INT,
			'base_command' => FILTER_SANITIZE_NUMBER_INT,
			'base_military' => FILTER_SANITIZE_NUMBER_INT,
			'is_locked' => FILTER_SANITIZE_NUMBER_INT,
			'is_vip_use' => FILTER_SANITIZE_NUMBER_INT,
			'suit_id' => FILTER_SANITIZE_NUMBER_INT,
			'gold' => FILTER_SANITIZE_NUMBER_INT,
			'desc_1' => FILTER_SANITIZE_STRING,
			'desc_2' => FILTER_SANITIZE_STRING,
			'flag' => FILTER_SANITIZE_NUMBER_INT,
		);
		$tplData = filter_var_array($_REQUEST, $args);

		/*foreach ($tplData as $key => $val)
		 {
		if (!$val)
		{
		$msg = '请填写完整';
		}
		}*/
		print_r($tplData);
		if (!isset($msg)) {
			if ($act == 'add') {

				$tplData['level'] = 0;
				$tplData['max_level'] = M_Config::getVal('strong_equip_max_level');
				//$tplData['base_energy'] = 0; //暂留
				$tplData['ext_attr_name'] = '';
				$tplData['ext_attr_rate'] = '';
				$tplData['ext_attr_skill'] = '';
				$res = B_DB::instance('BaseEquipTpl')->insert($tplData);
				if ($res) {
					$flag = 1;
					$msg = '添加成功！';
				}
			} elseif ($act == 'edit') {
				$id = intval($_POST['id']);
				if ($id > 0) {
					$res = B_DB::instance('BaseEquipTpl')->update($tplData, $id);
					if ($res) {
						$flag = 1;
						$msg = '修改成功！';
					} else {
						$msg = '修改失败！';
					}
				} else {
					$msg = '参数错误';
				}
			} else {
				$msg = '非法操作';
			}
		}
		$flag = isset($flag) ? $flag : 0;
		$msg = isset($msg) ? $msg : '操作错误';

		$json['flag'] = $flag;
		$json['msg'] = $msg;
		echo json_encode($json);
	}

	/**
	 * 添加模板套装装备执行...
	 * @author HeJunyun
	 */
	static public function AEquipSuitDel() {
		$flag = 0;
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if ($id > 0) {
			if (B_DB::instance('BaseEquipSuit')->delete($id)) {
				$flag = 1;
				$msg = '删除成功';
			} else {
				$msg = '删除失败';
			}
		} else {
			$msg = '参数错误';
		}
		$json['flag'] = $flag;
		$json['msg'] = $msg;
		echo json_encode($json);
	}

	/**
	 * 删除模板装备
	 * @author HeJunyun
	 */
	static public function AEquipTplDel() {
		$flag = 0;
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if ($id > 0) {
			if (B_DB::instance('BaseEquipTpl')->delete($id)) {
				$flag = 1;
				$msg = '删除成功';
			} else {
				$msg = '删除失败';
			}
		} else {
			$msg = '参数错误';
		}
		$json['flag'] = $flag;
		$json['msg'] = $msg;
		echo json_encode($json);
	}

	/**
	 * 套装列表
	 * @author HeJunyun
	 */
	static public function AEquipSuit() {
		$args = array(
			'page' => FILTER_SANITIZE_NUMBER_INT,
		);
		$formVals = filter_var_array($_REQUEST, $args);
		$offset = 1000;
		$curPage = 1;

		$baseList = B_DB::instance('BaseEquipTpl')->getAll();
		$start = ($curPage - 1) * $offset;
		$pageData['list'] = B_DB::instance('BaseEquipSuit')->getList($start, $offset);
		$totalNum = B_DB::instance('BaseEquipSuit')->count();
		$pageData['page'] = B_Page::make($curPage, $totalNum, $offset, 10);
		foreach ($pageData['list'] as $key => $val) {
			$pageData['list'][$key]['effect'] = json_decode($val['effect'], true);
		}

		B_View::setVal('pageData', $pageData);
		B_View::render('Base/EquipSuit');
	}

	/**
	 * 添加套装模板页面
	 * @author HeJunyun
	 */
	static public function AEquipSuitAddView() {
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		$pageData = array();
		if ($id > 0) {
			$pageData['info'] = B_DB::instance('BaseEquipSuit')->get($id);

			if ($pageData['info']) {
				$pageData['info']['effect'] = json_decode($pageData['info']['effect'], true);
			}
		}

		B_View::setVal('pageData', $pageData);
		B_View::render('Base/EquipSuitAddTpl');
	}

	/**
	 * 添加套装执行
	 * @author HeJunyun
	 */
	static public function AEquipSuitAdd() {

		$data = $_REQUEST;
		if ($data['name'] == '') {
			$msg = '名称不能为空';
		} else {
			if (!empty($data['effect2'])) {

				$effect['2'] = $data['effect2'];
			}
			if (!empty($data['effect3'])) {
				$effect['3'] = $data['effect3'];
			}
			if (!empty($data['effect4'])) {
				$effect['4'] = $data['effect4'];
			}
			if (!empty($data['effect5'])) {
				$effect['5'] = $data['effect5'];
			}
			if (!empty($data['effect6'])) {
				$effect['6'] = $data['effect6'];
			}


			$effect = json_encode($effect, true);

			$info = array(
				'name' => $data['name'],
				'effect' => $effect,
				'desc' => $data['desc'],
			);

			if ($data['id'] > 0) {
				$result = B_DB::instance('BaseEquipSuit')->update($info, $data['id']);
				$msg = $result ? '修改成功' : '修改失败';
				$flag = $msg == '修改成功' ? 1 : 0;

			} else {
				$result = B_DB::instance('BaseEquipSuit')->insert($info);
				$msg = $result ? '添加成功' : '添加失败';
				$flag = $msg == '添加成功' ? 1 : 0;
			}

		}
		$json['flag'] = $flag;
		$json['msg'] = $msg;
		echo json_encode($json);
	}

	/**
	 * 军官列表
	 * @author HeJunyun
	 */
	static public function AHeroList() {
		$order = '';
		$pageData['order'] = '';

		$args = array(
			'page' => FILTER_SANITIZE_NUMBER_INT,
			'nickname' => FILTER_SANITIZE_STRING,
			'quality' => FILTER_SANITIZE_NUMBER_INT,
		);
		$formVals = filter_var_array($_REQUEST, $args);
		$length = 50;
		$curPage = max(1, $formVals['page']);

		$pageData['parms'] = array();
		if ($formVals['nickname'] != '') {
			$pageData['parms']['nickname'] = $formVals['nickname'];
		}
		if ($formVals['quality'] > 0) {
			$pageData['parms']['quality'] = $formVals['quality'];
		}

		$pageData['color'] = array(
			'1' => 'white',
			'2' => 'green',
			'3' => 'blue',
			'4' => 'purple',
			'5' => 'blue',
			'6' => 'purple',
			'7' => 'red',
			'8' => 'orange',
			'9' => 'ffa03b', //201307.01
			'10' => 'ee5e10', //201307.01
		);

		if (isset($_GET['db'])) {
			$start = ($curPage - 1) * $length;
			$order = array('quality' => 'ASC', 'id' => 'ASC');
			$pageData['list'] = B_DB::instance('BaseHeroTpl')->getList($start, $length, $pageData['parms'], $order);
			$totalNum = B_DB::instance('BaseHeroTpl')->count($pageData['parms']);
		} else {
			$list = M_Base::heroAll();
			$offset = ($curPage - 1) * $length;
			$pageData['list'] = array_slice($list, $offset, $length);
			$totalNum = count($list);
		}

		$pageData['page'] = B_Page::make($curPage, $totalNum, $length, 20);

		B_View::setVal('pageData', $pageData);
		B_View::render('Base/HeroList');
	}

	static public function AHeroTplImport() {
		if (!empty($_POST)) {
			$headerArr = array_keys(self::$HeroTplHeader);
			if (!empty($_FILES['herocsvfile']['tmp_name'])) {
				require_once ADM_PATH . '/lib/PHPExcel.php';
				$file = $_FILES["herocsvfile"]['tmp_name'];

				$objReader = new PHPExcel_Reader_Excel5();
				$objReader->setReadDataOnly(true);
				$objPHPExcel = $objReader->load($file);

				$currentSheet = $objPHPExcel->getSheet(0);
				/**取得最大的列号*/
				$allColumn = $currentSheet->getHighestColumn();
				/**取得一共有多少行*/
				$allRow = $currentSheet->getHighestRow();
				/**从第二行开始输出，因为excel表中第一行为列名*/
				$arr = array();
				for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
					/**从第A列开始输出*/
					$tmp = array();
					$n = 0;
					for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
						$address = $currentColumn . $currentRow;
						$key = $headerArr[$n];
						$v = $currentSheet->getCell($address)->getValue();
						if ($key == 'start_time' || $key == 'end_time') {
							$v = !empty($v) ? strtotime($v) : 0;
						}
						$tmp[$key] = trim($v);
						$n++;
					}

					if (empty($tmp['id'])) {
						echo 'ID不能为空';
						var_dump($tmp);
						exit;
					}

					if (!isset(T_App::$genderType[$tmp['gender']])) {
						echo '不识别的性别';
						var_dump($tmp);
						exit;
					}

					//if (!$tmp['quality'] || $tmp['quality'] > 8)		//07.01
					if (!$tmp['quality'] || $tmp['quality'] > 10) {
						echo '不识别的品质';
						var_dump($tmp);
						exit;
					}

					if (empty($tmp['nickname'])) {
						echo '昵称不能为空';
						exit;
					}
					if (B_DB::instance('BaseHeroTpl')->get($tmp['id']) && $tmp['del'] != 1) {
						unset($tmp['del']);
						$ret = B_DB::instance('BaseHeroTpl')->update($tmp, $tmp['id']);
						$tip[$tmp['id']] = $ret ? '更新成功' : '更新失败';
					} else if (B_DB::instance('BaseHeroTpl')->get($tmp['id']) && isset($tmp['del']) && $tmp['del'] == 1) {
						$ret = B_DB::instance('BaseHeroTpl')->delete($tmp['id']);
						$tip[$tmp['id']] = $ret ? '删除成功' : '删除失败';
					} else if (!(B_DB::instance('BaseHeroTpl')->get($tmp['id'])) && isset($tmp['del']) && $tmp['del'] == 1) {
						$tip[$tmp['id']] = '删掉的记录不进行插入';
					} else if (!(B_DB::instance('BaseHeroTpl')->get($tmp['id']))) {
						unset($tmp['del']);
						$tmp['create_at'] = time();
						$tmp['hire_need'] = '';
						$ret = B_DB::instance('BaseHeroTpl')->insert($tmp);
						$tip[$tmp['id']] = $ret ? '插入成功' : '插入失败';
					}
					//$arr[] = $tmp;
				}

				$pageData['tip'] = $tip;
			}

		}
		$pageData['act'] = 'HeroTplImport';
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/HeroTplImport');
	}

	static public function AHeroListExport() {
		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		$tmp = range('A', 'Z');
		$range = $tmp;
		foreach ($tmp as $val) {
			$range[] = 'A' . $val;
		}
		foreach ($tmp as $val) {
			$range[] = 'B' . $val;
		}

		require_once ADM_PATH . '/lib/PHPExcel.php';

		$rows = B_DB::instance('BaseHeroTpl')->getsBy(array(), array('id' => 'ASC'));

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		$header = self::$HeroTplHeader;
		$obj = $objPHPExcel->setActiveSheetIndex(0);

		$i = 0;
		foreach ($header as $k => $val) {
			$prefix = $range[$i];
			$obj->setCellValue($prefix . '1', $val);
			$i++;
		}

		$no = 2;
		foreach ($rows as $vals) {

			$vData = $vals;
			$vData['start_time'] = !empty($vals['start_time']) ? date('Y-m-d H:i:s', $vals['start_time']) : '';
			$vData['end_time'] = !empty($vals['end_time']) ? date('Y-m-d H:i:s', $vals['end_time']) : '';
			$vData['del'] = 0;
			$tmp = array();
			$i = 0;
			foreach ($header as $k => $line) {
				$prefix = $range[$i];
				$obj->setCellValue($prefix . $no, $vData[$k]);
				$i++;
			}
			$no++;
		}

		// Redirect output to a client’s web browser (Excel5)
		$filename = 'hero_tpl_info_' . date('YmdHis') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;

	}

	static public function ADelHeroCache() {
		$ret = M_Base::heroAll(true);
		echo "<script>";
		echo $ret ? "alert('操作成功!');" : "alert('操作失败!');";
		echo "</script>";
	}

	static public function AHeroAddTpl() {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$pageData = array();
		$pageData['skill_list'] = B_DB::instance('BaseSkill')->getAll();
		if ($id > 0) {
			$info = M_Hero::baseInfo($id);
			if ($info) {
				$pageData['info'] = $info;
			}
		}
		$pageData['props_list'] = B_DB::instance('BaseProps')->all();

		B_View::setVal('pageData', $pageData);
		B_View::render('Base/HeroAddTpl');
	}

	static public function AHeroAdd() {
		//$flag = 0;
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		$act = $_REQUEST['act'];
		$args = array(
			'nickname' => FILTER_SANITIZE_STRING,
			'gender' => FILTER_SANITIZE_NUMBER_INT,
			'quality' => FILTER_SANITIZE_NUMBER_INT,
			'face_id' => FILTER_SANITIZE_STRING,
			'level' => FILTER_SANITIZE_NUMBER_INT,
			'is_vip_use' => FILTER_SANITIZE_NUMBER_INT,
			'attr_lead' => FILTER_SANITIZE_NUMBER_INT,
			'attr_command' => FILTER_SANITIZE_NUMBER_INT,
			'attr_military' => FILTER_SANITIZE_NUMBER_INT,
			'attr_energy' => FILTER_SANITIZE_NUMBER_INT,
			'grow_rate' => FILTER_FLAG_ALLOW_FRACTION,

			'skill_slot_num' => FILTER_SANITIZE_NUMBER_INT,
			'skill_slot' => FILTER_SANITIZE_NUMBER_INT,
			'skill_slot_1' => FILTER_SANITIZE_NUMBER_INT,
			'skill_slot_2' => FILTER_SANITIZE_NUMBER_INT,

			'desc' => FILTER_SANITIZE_STRING,
			'detail' => FILTER_SANITIZE_STRING,

			'num' => FILTER_SANITIZE_NUMBER_INT,
			'hire_time' => FILTER_SANITIZE_NUMBER_INT,

			'start_time' => FILTER_SANITIZE_STRING,
			'end_time' => FILTER_SANITIZE_STRING,
			'succ_rate' => FILTER_FLAG_ALLOW_FRACTION,

			'props' => FILTER_SANITIZE_NUMBER_INT,
			'props_id' => FILTER_SANITIZE_NUMBER_INT,
			'props_num' => FILTER_SANITIZE_NUMBER_INT,
			'milpay' => FILTER_SANITIZE_NUMBER_INT,
			'milpay_num' => FILTER_SANITIZE_NUMBER_INT,
			'coupon' => FILTER_SANITIZE_NUMBER_INT,
			'coupon_num' => FILTER_SANITIZE_NUMBER_INT,
			'gold' => FILTER_SANITIZE_NUMBER_INT,
			'gold_num' => FILTER_SANITIZE_NUMBER_INT,
		);
		$data = filter_var_array($_REQUEST, $args);
		$tmpNeed = array();
		if (isset($data['props']) && $data['props'] && $data['props_id'] && $data['props_num']) {
			$tmpNeed['props'] = array($data['props'], $data['props_id'], $data['props_num']);
		}
		if (isset($data['milpay']) && $data['milpay'] && $data['milpay_num']) {
			$tmpNeed['milpay'] = array($data['milpay'], $data['milpay_num']);
		}
		if (isset($data['coupon']) && $data['coupon'] && $data['coupon_num']) {
			$tmpNeed['coupon'] = array($data['coupon'], $data['coupon_num']);

		}
		if (isset($data['gold']) && $data['gold'] && $data['gold_num']) {
			$tmpNeed['gold'] = array($data['gold'], $data['gold_num']);

		}
		unset($data['props']);
		unset($data['props_id']);
		unset($data['props_num']);
		unset($data['milpay']);
		unset($data['milpay_num']);
		unset($data['coupon']);
		unset($data['coupon_num']);
		unset($data['gold']);
		unset($data['gold_num']);
		$data['hire_need'] = json_encode($tmpNeed);
		$data['start_time'] = strtotime($data['start_time']);
		$data['end_time'] = strtotime($data['end_time']);
		if ($act == 'add') {
			$data['create_at'] = time();
			$res = B_DB::instance('BaseHeroTpl')->insert($data);
			if ($res) {
				//$msg = '添加成功';
				M_Hero::setBaseTplHeroNum($res, $data['num']);
				//$flag = 1;
				echo "<script>alert('添加成功');</script>";
			} else {
				//$msg = '添加失败';
				echo "<script>alert('添加失败');</script>";
			}
		} elseif ($act == 'edit') {
			$res = B_DB::instance('BaseHeroTpl')->update($data, $id);
			if ($res) {
				//$msg = '修改成功';
				M_Hero::setBaseTplHeroNum($id, $data['num']);
				echo "<script>alert('修改成功');</script>";
				//$flag = 1;
			} else {
				echo "<script>alert('修改失败');</script>";
				//$msg = '修改失败';
			}
		} else {
			echo "<script>alert('错误操作');</script>";
			//$msg = '错误操作';
		}

	}

	static public function AHeroDel() {
		$flag = 0;
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

		if ($id > 0) {
			$res = B_DB::instance('BaseHeroTpl')->delete($id);
			if ($res) {
				$msg = '删除成功';
				M_Hero::delBaseTplHeroNum($id);
				$flag = 1;
			} else {
				$msg = '删除失败';
			}
		}
		$json['flag'] = $flag;
		$json['msg'] = $msg;
		echo json_encode($json);
	}

	/**
	 * 技能列表
	 */
	static public function ASkillList() {
		$args = array(
			'page' => FILTER_SANITIZE_NUMBER_INT,
		);
		$formVals = filter_var_array($_REQUEST, $args);
		$length = 50;
		$curPage = max(1, $formVals['page']);


		if (isset($_GET['db'])) {
			$start = ($curPage - 1) * $length;
			$pageData['list'] = B_DB::instance('BaseSkill')->getList($start, $length, array(), array('sort' => 'ASC'));
			$totalNum = B_DB::instance('BaseSkill')->count();
		} else {
			$list = M_Base::skillAll();
			$offset = ($curPage - 1) * $length;
			$pageData['list'] = array_slice($list, $offset, $length);
			$totalNum = count($list);
		}

		$pageData['page'] = B_Page::make($curPage, $totalNum, $length, 10);

		$normalSkill = T_Effect::$SkillBaseType;
		$specialSkill = T_Effect::$SkillBattleType;
		$pageData['allSkill'] = array_merge($normalSkill, $specialSkill);
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/SkillList');
	}

	/**
	 * 技能详细
	 */
	static public function ASkillView() {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if ($id > 0) {
			$info = B_DB::instance('BaseSkill')->get($id);
			$pageData['info'] = $info;
		}
		$normalSkill = T_Effect::$SkillBaseType;
		$specialSkill = T_Effect::$SkillBattleType;
		$pageData['allSkill'] = array_merge($normalSkill, $specialSkill);
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/SkillView');
	}

	static public function ASkillEdit() {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$data['name'] = trim($_REQUEST['name']);
		$data['face_id'] = intval($_REQUEST['face_id'], 0);
		$data['type'] = intval($_REQUEST['type'], 0);
		$data['level'] = intval($_REQUEST['level'], 0);
		$data['desc'] = trim($_REQUEST['desc']);
		$data['level_type'] = trim($_REQUEST['level_type']);
		$data['is_repeat'] = intval($_REQUEST['is_repeat'], 0);
		$data['sort'] = intval($_REQUEST['sort'], 0);

		$effect = array();
		if (!empty($_REQUEST['effect'])) {
			foreach ($_REQUEST['effect'] as $eKey => $val) {
				$effect[$eKey] = $val;
			}
		}
		$data['effect'] = json_encode($effect);
		$data['sort'] = intval($_REQUEST['sort'], 0);

		if ($id > 0) {
			$data['id'] = $id;
			$res = B_DB::instance('BaseSkill')->update($data,$id);
		} else {
			$data['create_at'] = time();
			$res = B_DB::instance('BaseSkill')->insert($data);
		}
		if ($res) {
			M_Skill::cleanBaseSkill();
			$flag = 1;
			$msg = '保存成功！';
		} else {
			$flag = 0;
			$msg = '保存失败！';
		}
		$msg = array('flag' => $flag, 'err' => $msg);
		echo json_encode($msg);
	}


	static public function ASkillDel() {
		$flag = 0;
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

		if ($id > 0) {
			$res = B_DB::instance('BaseSkill')->delete($id);
			if ($res) {
				M_Skill::cleanBaseSkill();
				$msg = '删除成功';
				$flag = 1;
			} else {
				$msg = '删除失败';
			}
		}
		$json['flag'] = $flag;
		$json['msg'] = $msg;
		echo json_encode($json);
	}

	static public function AConsumerList() {
		M_Consumer::clean();
		$pageData['list'] = M_Consumer::getList();
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/ConsumerList');
	}

	public function AUnionList() {
		$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
		$ret = M_Union::getList($page, 20);
		foreach ($ret['list'] as $id) {
			$info = M_Union::getInfo($id);
			$pageData['list'][] = $info;
		}
		$pageData['total'] = $ret['total'];
		$pageData['page'] = B_Page::make($page, $ret['total'], 20, 10);
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/UnionList');
	}

	public function AUnionTotal() {
		$ret = false;
		$total_person = 0;
		$total_renown = 0;
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		if ($id > 0) {
			$uInfo = M_Union::getInfo($id);
			if (isset($uInfo['id'])) {
				$list = M_Union::getUnionMemberList($id);
				if (!empty($list)) {
					foreach ($list as $info) {
						isset($info['id']) && $total_person = $total_person + 1;
						$info['renown'] && $total_renown = $total_renown + $info['renown'];
					}
				}
			}
		}
		if ($total_person) {
			$setArr = array(
				'total_person' => $total_person,
				'total_renown' => $total_renown
			);
			$ret = M_Union::setInfo($id, $setArr);
		}
		echo $ret ? "<script>alert('操作成功!');</script>" : "<script>alert('操作失败!');</script>";
	}

	static public function AProbeList() {
		$args = array(
			'page' => FILTER_SANITIZE_NUMBER_INT,
		);
		$formVals = filter_var_array($_REQUEST, $args);
		$length = 20;
		$curPage = max(1, $formVals['page']);


		if (isset($_GET['db'])) {
			$start = ($curPage - 1) * $length;
			$pageData['list'] = B_DB::instance('BaseProbe')->getList($start, $length);
			$totalNum = B_DB::instance('BaseProbe')->count();
		} else {
			$list = M_Base::probeAll();
			$offset = ($curPage - 1) * $length;
			$pageData['list'] = array_slice($list, $offset, $length);
			$totalNum = count($list);
		}

		$pageData['page'] = B_Page::make($curPage, $totalNum, $length, 10);

		B_View::setVal('pageData', $pageData);
		B_View::render('Base/ProbeList');
	}

	static public function AProbeExport() {
		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		$tmp = range('A', 'Z');
		$range = $tmp;
		foreach ($tmp as $val) {
			$range[] = 'A' . $val;
		}
		foreach ($tmp as $val) {
			$range[] = 'B' . $val;
		}

		require_once ADM_PATH . '/lib/PHPExcel.php';

		$rows = B_DB::instance('BaseProbe')->getsBy(array(), array('id' => 'ASC'));


		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		$header = self::$ProbeHeader;
		$obj = $objPHPExcel->setActiveSheetIndex(0);

		$i = 0;
		foreach ($header as $k => $val) {
			$prefix = $range[$i];
			$obj->setCellValue($prefix . '1', $val);
			$i++;
		}

		$no = 2;
		foreach ($rows as $vals) {
			$vData = $vals;
			$vData['start_time'] = !empty($vals['start_time']) ? date('Y-m-d H:i:s', $vals['start_time']) : '';
			$vData['end_time'] = !empty($vals['end_time']) ? date('Y-m-d H:i:s', $vals['end_time']) : '';
			$tmp = array();
			$i = 0;
			foreach ($header as $k => $line) {
				$prefix = $range[$i];
				$obj->setCellValue($prefix . $no, $vData[$k]);
				$i++;
			}
			$no++;
		}


		// Redirect output to a client’s web browser (Excel5)
		$filename = 'base_probe_' . date('YmdHis') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	static public function AProbeImport() {
		if (!empty($_POST)) {
			$headerArr = array_keys(self::$ProbeHeader);
			if (!empty($_FILES['probecsvfile']['tmp_name'])) {

				require_once ADM_PATH . '/lib/PHPExcel.php';
				$file = $_FILES["probecsvfile"]['tmp_name'];

				$objReader = new PHPExcel_Reader_Excel5();
				$objReader->setReadDataOnly(true);
				$objPHPExcel = $objReader->load($file);

				$currentSheet = $objPHPExcel->getSheet(0);
				/**取得最大的列号*/
				$allColumn = $currentSheet->getHighestColumn();
				/**取得一共有多少行*/
				$allRow = $currentSheet->getHighestRow();
				/**从第二行开始输出，因为excel表中第一行为列名*/
				$arr = array();
				for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
					/**从第A列开始输出*/
					$tmp = array();
					$n = 0;
					for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
						$address = $currentColumn . $currentRow;
						$key = $headerArr[$n];
						$v = $currentSheet->getCell($address)->getValue();
						$tmp[$key] = trim($v);
						$n++;
					}

					if (empty($tmp['id'])) {
						echo 'ID不能为空';
						var_dump($tmp);
						exit;
					}

					if (!$tmp['title']) {
						echo '事件内容不能为空';
						var_dump($tmp);
						exit;
					}

					if (!$tmp['type']) {
						$tmp['type'] = 1;
					}

					if (B_DB::instance('BaseProbe')->get($tmp['id'])) {
						$ret = B_DB::instance('BaseProbe')->update($tmp, $tmp['id']);
						$tip[$tmp['id']] = $ret ? '更新成功' : '更新失败';
					} else {
						$tmp['create_at'] = time();
						$ret = B_DB::instance('BaseProbe')->insert($tmp);
						$tip[$tmp['id']] = $ret ? '插入成功' : '插入失败';
					}
					//$arr[] = $tmp;
				}

				$pageData['tip'] = $tip;

			}

		}
		$pageData['act'] = 'ProbeImport';
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/ProbeImport');
	}

	static public function AProbeCacheUp() {
		$ret = M_Base::probeAll(true);

		if ($ret) {
			echo "<script>alert('更新缓存成功');</script>";
			exit;
		}
	}

	static public function AEquipCacheUp() {
		$ret = M_Base::equipAll(true);
		echo "<script>";
		echo $ret ? "alert('更新成功');" : "alert('更新失败');";
		echo "</script>";
	}

	static public function AEquipSuitCacheUp() {
		$ret = M_Base::equipSuitAll(true);
		if ($ret) {
			echo "<script>alert('更新缓存成功');</script>";
		} else {
			echo "<script>alert('更新缓存失败');</script>";
		}
	}

	static public function AProbeView() {
		$pageData = array();
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if ($id > 0) {
			$info = B_DB::instance('BaseProbe')->get($id);
			$info['award'] = !empty($info['award']) ? json_decode($info['award'], true) : array();
			$pageData['info'] = $info;
		}
		$pageData['props_list'] = B_DB::instance('BaseProps')->all();
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/ProbeView');
	}

	static public function AProbeEdit() {
		$ret = false;
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$data['title'] = isset($_REQUEST['title']) ? trim($_REQUEST['title']) : '';
		$data['type'] = isset($_REQUEST['type']) ? intval($_REQUEST['type']) : 1;
		$data['award_id'] = isset($_REQUEST['award_id']) ? intval($_REQUEST['award_id']) : 0;
		if ($data['title'] && $data['award_id']) {
			if ($id > 0) {
				$ret = B_DB::instance('BaseProbe')->update($data, $id);
			} else {
				$data['create_at'] = time();
				$ret = B_DB::instance('BaseProbe')->insert($data);
			}
		}
		echo "<script>";
		echo $ret ? "alert('操作成功!');" : "alert('操作失败!');";
		echo $id ? "window.location='?r=Base/ProbeView&id=" . $id . "'" : "window.location='?r=Base/ProbeView'";
		echo "</script>";
	}

	static public function AProbeDel() {
		$ret = false;
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if ($id > 0) {
			$ret = B_DB::instance('BaseProbe')->delete($id);
		}
		echo "<script>";
		echo $ret ? "alert('操作成功!');" : "alert('操作失败!');";
		echo "window.location='?r=Base/ProbeList'";
		echo "</script>";
	}

	/** 删除道具缓存 */
	static public function ADelPropsCache() {
		$ret = M_Props::delPropsCache(); //APC::del(T_Key::BASE_PROPS);	//删除缓存

		echo "<script>";
		echo $ret ? "alert('操作成功!');" : "alert('操作失败!');";
		echo "</script>";
	}


	static public function ACampaignList() {
		$args = array(
			'page' => FILTER_SANITIZE_NUMBER_INT,
			'type' => FILTER_SANITIZE_NUMBER_INT,
		);

		$formVals = filter_var_array($_REQUEST, $args);
		$offset = 10;
		$curPage = max(1, $formVals['page']);
		$pageData['parms'] = array();
		$rows = array();

		$rows = B_DB::instance('BaseCampaign')->getsBy(array(), array('id' => 'ASC'));

		$pageData['list'] = $rows;

		$totalNum = B_DB::instance('BaseCampaign')->count($pageData['parms']);
		$pageData['page'] = B_Page::make($curPage, $totalNum, $offset, 10);
		$pageData['tip'] = array();

		$campInfo = array();
		foreach ($rows as $val) {
			$info = M_Campaign::getInfo($val['id']);
			$oui = $info['owner_union_id'];
			$rc = new B_Cache_RC(T_Key::CAMP_UNION_EFFECT, $oui);
			$campInfo[$oui] = $rc->jsonget();
		}

		$pageData['campInfo'] = $campInfo;

		B_View::setVal('pageData', $pageData);
		B_View::render('Base/CampaignList');
	}

	static public function ACampaignCacheUp() {
		//$ret = M_Campaign::getBaseInfoAll(true);
		$ret = M_Base::campaignAll(true);

		$tmpRc = new B_Cache_RC(T_Key::TMP_EXPIRE, 'camp');
		$tmpRc->delete();

		if ($ret) {
			echo "<script>";
			echo "alert('更新成功');";
			echo "</script>";
		} else {
			echo "<script>";
			echo "alert('更新失败');";
			echo "</script>";
		}
	}

	static public function AWeaponCacheUp() {
		$ret = M_Base::weaponAll(true);
		if ($ret) {
			echo "<script>";
			echo "alert('更新成功');";
			echo "</script>";
		} else {
			echo "<script>";
			echo "alert('更新失败');";
			echo "</script>";
		}
	}

	static public function ACampaignExport() {

		$header = self::$campHeaderArr;

		$equipName = T_Word::$EQUIP_NAME;
		$equipPos = T_Word::$EQUIP_POS;
		$equipQuality = T_Word::$EQUIP_QUAL;

		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		$tmp = range('A', 'Z');
		$range = $tmp;
		foreach ($tmp as $val) {
			$range[] = 'A' . $val;
		}
		foreach ($tmp as $val) {
			$range[] = 'B' . $val;
		}

		require_once ADM_PATH . '/lib/PHPExcel.php';

		$rows = B_DB::instance('BaseCampaign')->all(true);

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		$obj = $objPHPExcel->setActiveSheetIndex(0);

		$i = 0;
		foreach ($header as $k => $val) {
			$prefix = $range[$i];
			$obj->setCellValue($prefix . '1', $val);
			$i++;
		}

		$no = 2;
		$weekConst = array('日' => 1, '一' => 2, '二' => 4, '三' => 8, '四' => 16, '五' => 32, '六' => 64);

		foreach ($rows as $line) {
			$data = $line;
			$probe = json_decode($line['probe_event_data'], true);
			$arr = array();
			foreach ($probe as $k => $v) {
				$arr[] = $k . '_' . $v;
			}
			$data['probe_event_data'] = implode(';', $arr);

			$week_arr = array();
			foreach ($weekConst as $k => $v) {
				if (($line['open_week'] & $v) > 0) {
					$week_arr[] = $k;
				}
			}

			$data['open_week'] = implode('|', $week_arr);
			$i = 0;
			foreach ($header as $k => $v) {
				$prefix = $range[$i];
				$obj->setCellValue($prefix . $no, $data[$k]);
				$i++;
			}
			$no++;
		}

		// Redirect output to a client’s web browser (Excel5)
		$filename = 'base_camp_' . date('YmdHis') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	static public function ACampaignImport() {
		$tip = array();
		if (!empty($_POST)) {
			$headerArr = array_keys(self::$campHeaderArr);
			if (!empty($_FILES['csvfile']['tmp_name'])) {
				require_once ADM_PATH . '/lib/PHPExcel.php';
				$file = $_FILES["csvfile"]['tmp_name'];

				$objReader = new PHPExcel_Reader_Excel5();
				$objReader->setReadDataOnly(true);
				$objPHPExcel = $objReader->load($file);

				$currentSheet = $objPHPExcel->getSheet(0);
				/**取得最大的列号*/
				$allColumn = $currentSheet->getHighestColumn();
				/**取得一共有多少行*/
				$allRow = $currentSheet->getHighestRow();
				/**从第二行开始输出，因为excel表中第一行为列名*/
				$arr = array();
				$weekConst = array('日' => 1, '一' => 2, '二' => 4, '三' => 8, '四' => 16, '五' => 32, '六' => 64);
				for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
					/**从第A列开始输出*/
					$tmp = array();
					$n = 0;
					for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
						$address = $currentColumn . $currentRow;
						$key = $headerArr[$n];
						$v = $currentSheet->getCell($address)->getValue();

						if ($key == 'open_week') {
							$weekArr = explode("|", $v);
							$v = 0;

							foreach ($weekArr as $val) {
								$v += $weekConst[$val];
							}
						} else if ($key == 'probe_event_data') {

							$arr = array();
							$prop = explode(';', $v);
							foreach ($prop as $val) {
								list($k, $v1) = explode('_', $val);
								$arr[$k] = $v1;
							}
							$v = json_encode($arr);
						}

						$tmp[$key] = trim($v);
						$n++;
					}

					if (!empty($tmp['title']) && !empty($tmp['id'])) {
						if (B_DB::instance('BaseCampaign')->get($tmp['id'])) {
							$ret = B_DB::instance('BaseCampaign')->update($tmp, $tmp['id']);
							$tip[$tmp['id']] = $ret ? '更新成功' : '更新失败';
						} else {
							$ret = B_DB::instance('BaseCampaign')->insert($tmp);
							$tip[$tmp['id']] = $ret ? '插入成功' : '插入失败';
						}
					} else {
						$tip[$tmp['id']] = "错误数据";
					}
					//$arr[] = $tmp;
				}

				$pageData['tip'] = $tip;
			}
		}
		$pageData['tip'] = $tip;
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/CampaignImport');

	}

	static public function AAwardList() {
		$args = array(
			'page' => FILTER_SANITIZE_NUMBER_INT,
			'type' => FILTER_SANITIZE_NUMBER_INT,
		);

		$formVals = filter_var_array($_REQUEST, $args);
		$length = 20;
		$curPage = max(1, $formVals['page']);
		$pageData['parms'] = isset($_REQUEST['parms']) ? $_REQUEST['parms'] : array();

		/** 道具列表 */
		$props_list = M_Base::propsAll();
		foreach ($props_list as $key => $val) {
			$pageData['props_list'][$val['id']] = $val;
		}
		/** 模板装备列表 */
		$equipList = M_Base::equipAll();
		foreach ($equipList as $key => $val) {
			$pageData['equipList'][$val['id']] = $val;
		}

		$list = M_Base::awardAll();

		if (isset($_GET['db'])) {
			$start = ($curPage - 1) * $length;
			$pageData['list'] = B_DB::instance('BaseAward')->getList($length, $length, $pageData['parms']);
			$totalNum = B_DB::instance('BaseAward')->count($pageData['parms']);
		} else {
			$offset = ($curPage - 1) * $length;
			$pageData['list'] = array_slice($list, $offset, $length);
			$totalNum = count($list);
		}

		$pageData['page'] = B_Page::make($curPage, $totalNum, $length, 20);

		B_View::setVal('pageData', $pageData);
		B_View::render('Base/AwardList');
	}

	/** 更新奖励缓存 */
	static public function AAwardCacheUp() {
		$ret = M_Base::awardAll(true);
		echo "<script>";
		echo $ret ? "alert('更新成功');" : "alert('更新失败');";
		echo "</script>";
	}

	/**
	 * 奖励详细
	 */
	static public function AAwardView() {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$pageData = array();
		if ($id > 0) {
			$info = B_DB::instance('BaseAward')->get($id);
			if ($info) {
				$pageData['info'] = $info;
			}
		}
		/** NPC军官列表 */
		$hero_list = B_DB::instance('BaseNpcHero')->getRowsByPage(1, 999999);
		foreach ($hero_list as $key => $val) {
			$pageData['hero_list'][$val['id']] = $val;
		}
		/** 道具列表 */
		$props_list = B_DB::instance('BaseProps')->all();
		foreach ($props_list as $key => $val) {
			$pageData['props_list'][$val['id']] = $val;
		}
		/** 模板装备列表 */
		$equipList = B_DB::instance('BaseEquipTpl')->getAll();
		foreach ($equipList as $key => $val) {
			$pageData['equipList'][$val['id']] = $val;
		}


		B_View::setVal('pageData', $pageData);
		B_View::render('Base/AwardView');
	}

	/**
	 * 奖励添加/编辑操作
	 */
	static public function AAwardEdit() {
		$id = intval($_REQUEST['id']); //ID
		$data['name'] = trim($_REQUEST['name']); //NPC昵称
		$data['type'] = intval($_REQUEST['type']); //NPC类型
		$data['award_desc'] = trim($_REQUEST['award_desc']); //描述

		/** 战斗奖励数据  - 资源 */
		$award_res_type = isset($_REQUEST['jl_res_type']) ? $_REQUEST['jl_res_type'] : '';
		$award_res = array();
		if (isset($award_res_type[0])) {
			foreach ($award_res_type as $val) {
				$numKey = 'jl_' . $val . '_num';
				$jvKey = 'jl_' . $val . '_jv';
				$award_res[$val] = $_REQUEST[$numKey] . ',' . $_REQUEST[$jvKey];
			}
		}
		/** 战斗奖励数据  - 道具 */
		$award_props_id = isset($_REQUEST['jl_props_id']) ? $_REQUEST['jl_props_id'] : '';
		$award_props_num = isset($_REQUEST['jl_props_num']) ? $_REQUEST['jl_props_num'] : '';
		$award_props_jv = isset($_REQUEST['jl_props_jv']) ? $_REQUEST['jl_props_jv'] : '';
		$award_props = array();
		if (count($award_props_id) == count($award_props_num) && count($award_props_id) == count($award_props_jv)) {
			for ($i = 0; $i < count($award_props_id); $i++) {
				if ($award_props_id[$i] > 0 && $award_props_num[$i] > 0 && $award_props_jv[$i] > 0)
					$award_props[$award_props_id[$i]] = $award_props_num[$i] . ',' . $award_props_jv[$i];
			}
		}
		/** 战斗奖励数据 - 装备 */
		$award_equip_id = isset($_REQUEST['jl_equip_id']) ? $_REQUEST['jl_equip_id'] : '';
		$award_equip_num = isset($_REQUEST['jl_equip_num']) ? $_REQUEST['jl_equip_num'] : '';
		$award_equip_jv = isset($_REQUEST['jl_equip_jv']) ? $_REQUEST['jl_equip_jv'] : '';
		$award_equip = array();
		if ($award_equip_id && count($award_equip_id) == count($award_equip_num) && count($award_equip_id) == count($award_equip_jv)) {
			for ($i = 0; $i < count($award_equip_id); $i++) {
				if ($award_equip_id[$i] > 0 && $award_equip_num[$i] > 0 && $award_equip_jv[$i] > 0)
					$award_equip[$award_equip_id[$i]] = $award_equip_num[$i] . ',' . $award_equip_jv[$i];
			}
		}
		/*<><><><><><><战斗奖励数据，新装备系统使用<><><><><><><><><><><><> */
		$sys_equip_level = isset($_REQUEST['sys_equip_level']) ? $_REQUEST['sys_equip_level'] : '';
		$sys_equip_pos = isset($_REQUEST['sys_equip_pos']) ? $_REQUEST['sys_equip_pos'] : '';
		$sys_equip_quality = isset($_REQUEST['sys_equip_quality']) ? $_REQUEST['sys_equip_quality'] : '';
		$sys_equip_num = isset($_REQUEST['sys_equip_num']) ? $_REQUEST['sys_equip_num'] : '';
		$sys_equip_jl = isset($_REQUEST['sys_equip_jl']) ? $_REQUEST['sys_equip_jl'] : '';
		$award_sys_equip = array();
		if ($sys_equip_level && count($sys_equip_level) == count($sys_equip_pos) && count($sys_equip_level) == count($sys_equip_quality)) {
			for ($i = 0; $i < count($sys_equip_level); $i++) {
				if ($sys_equip_level[$i] && $sys_equip_pos[$i] && $sys_equip_quality[$i] && $sys_equip_num[$i] && $sys_equip_jl[$i]) {
					$tmpArr = array(
						$sys_equip_level[$i], $sys_equip_pos[$i], $sys_equip_quality[$i], $sys_equip_num[$i], $sys_equip_jl[$i]
					);
					$award_sys_equip[] = implode(',', $tmpArr);
				}
			}
		}

		/*<><><><><><><><><><><><><><><><><><><><><><> */
		/** 战斗奖励军饷 */
		$award_mil_pay_num = intval($_REQUEST['jl_mil_pay_num']);
		$award_mil_pay_jv = intval($_REQUEST['jl_mil_pay_jv']);
		$award_mil_pay = array();
		if ($award_mil_pay_num && $award_mil_pay_jv) {
			$award_mil_pay = array($award_mil_pay_num, $award_mil_pay_jv);
		}

		/** 战斗奖励点券 */
		$award_coupon_num = intval($_REQUEST['jl_coupon_num']);
		$award_coupon_jv = intval($_REQUEST['jl_coupon_jv']);
		$award_coupon = array();
		if ($award_coupon_num && $award_coupon_jv) {
			$award_coupon = array($award_coupon_num, $award_coupon_jv);
		}


		/** 战斗奖励数据  - 合并 */
		$award = array();
		if (count($award_res) > 0) {
			$award['res'] = $award_res;
		}
		if (count($award_props) > 0) {
			$award['props'] = $award_props;
		}
		if (count($award_equip) > 0) //模板装备
		{
			$award['equip'] = $award_equip;
		}
		if ($award_mil_pay) {
			$award['mil_pay'] = $award_mil_pay;
		}
		if ($award_coupon) {
			$award['coupon'] = $award_coupon;
		}
		if ($award_sys_equip) //系统装备
		{
			$award['sys_equip'] = $award_sys_equip;
		}
		$data['award_text'] = $award ? json_encode($award) : '';

		//////////////////////////////////////////////////////////////////////////////////
		if ($data['name'] == '') {
			echo "<script>alert('请填写奖励名称！');</script>";
			exit;
		}

		if ($id > 0) {
			//修改
			$result = B_DB::instance('BaseAward')->update($data, $id);
			if ($result) {

				echo "<script>alert('修改成功！');</script>";
			} else {
				echo "<script>alert('修改失败！');</script>";
			}
		} else {
			$data['create_at'] = time(); //添加时间
			$result = B_DB::instance('BaseAward')->insert($data);
			if ($result) {
				echo "<script>alert('添加成功！');</script>";
			} else {
				echo "<script>alert('添加失败！');</script>";
			}
		}
	}


	static public function AAwardListExport() {

		$header = self::$AwardHeader;

		$equipName = T_Word::$EQUIP_NAME;
		$equipPos = T_Word::$EQUIP_POS;
		$equipQuality = T_Word::$EQUIP_QUAL;

		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		$tmp = range('A', 'Z');
		$range = $tmp;
		foreach ($tmp as $val) {
			$range[] = 'A' . $val;
		}
		foreach ($tmp as $val) {
			$range[] = 'B' . $val;
		}

		require_once ADM_PATH . '/lib/PHPExcel.php';

		$rows = B_DB::instance('BaseAward')->getsBy(array(), array('id' => 'ASC'));

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		$obj = $objPHPExcel->setActiveSheetIndex(0);

		$i = 0;
		foreach ($header as $k => $val) {
			$prefix = $range[$i];
			$obj->setCellValue($prefix . '1', $val);
			$i++;
		}

		$no = 2;
		

		foreach ($rows as $line) {
			$data = $line;
			$i = 0;
			foreach ($header as $k => $v) {
				$prefix = $range[$i];
				$obj->setCellValue($prefix . $no, $data[$k]);
				$i++;
			}
			$no++;
		}

		// Redirect output to a client’s web browser (Excel5)
		$filename = 'base_award_' . date('YmdHis') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;

	}

	/*
	 * 金钱(概率_gold_数量)
	 * 食物(概率_food_数量)
	 * 食物(概率_oil_数量)
	 * 军饷(概率_milpay_数量)
	 * 礼券(概率_coupon_数量)
	 * 军令(概率_energy_数量)
	 * 军功(概率_exploit_数量)
	 * 威望(概率_renown_数量)
	 * 道具(概率_props_数量_ID)
	 * 装备(概率_equip_数量_ID)
	 * 军官(概率_hero_数量_ID)
	 */
	static public function AAwardListImport() {
		$tip = '';
		if (!empty($_POST) && isset($_FILES["csvfile"])) {

			
			$file = $_FILES["csvfile"]['tmp_name'];

			$headerArr = array_keys(self::$AwardHeader);
			require_once ADM_PATH . '/lib/PHPExcel.php';
			$objReader = new PHPExcel_Reader_Excel5();
			$objReader->setReadDataOnly(true);
			$objPHPExcel = $objReader->load($file);

			$currentSheet = $objPHPExcel->getSheet(0);
			/**取得最大的列号*/
			$allColumn = $currentSheet->getHighestColumn();
			/**取得一共有多少行*/
			$allRow = $currentSheet->getHighestRow();
			/**从第二行开始输出，因为excel表中第一行为列名*/
			$arr = array();

			for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
				/**从第A列开始输出*/
				$tmp = array();
				$n = 0;
				for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
					$address = $currentColumn . $currentRow;
					$key = $headerArr[$n];
					$v = $currentSheet->getCell($address)->getValue();
					if (!empty($key)) {
						$tmp[$key] = trim($v);
					}
					$n++;
				}

				$info = $tmp;
				
				if (!empty($info['id']) && !empty($info['val'])) {
					
					$ret = array();
					$info['val'] = trim($info['val'], '|');
					$tmpVal = explode("|", $info['val']);
					foreach ($tmpVal as $itemVal) {
						if (!empty($itemVal)) {
							$arr = explode("_", $itemVal);
							list($rate, $type, $num) = $arr;
							$id = isset($arr[3])?$arr[3]:0;
							if ($arr[0] == -1) {
								$ret['fix'][] = array($type, intval($num), $id);
							} else {
								$ret['rnd'][] = array(intval($rate), $type, intval($num), $id);
							}
						}

					}
					
					$info['data'] = json_encode($ret);


					if (B_DB::instance('BaseAward')->get($info['id'])) {
						$ret = B_DB::instance('BaseAward')->update($info, $info['id']);
						$tip[$info['id']] = $ret ? '更新成功' : '更新失败';
					} else {
						$ret = B_DB::instance('BaseAward')->insert($info);
						$tip[$info['id']] = $ret ? '插入成功' : '插入失败';
					}

				}
			}

			$pageData['tip'] = $tip;

		}

		$pageData['tip'] = $tip;
		$pageData['act'] = 'AwardImport';
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/AwardImport');
	}

	static public function APropsListExport() //道具基础信息的导出
	{
		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		$tmp = range('A', 'Z');
		$range = $tmp;
		foreach ($tmp as $val) {
			$range[] = 'A' . $val;
		}
		foreach ($tmp as $val) {
			$range[] = 'B' . $val;
		}
		require_once ADM_PATH . '/lib/PHPExcel.php';

		$rows = B_DB::instance('BaseProps')->getsBy(array(), array('id' => 'ASC'));

		$objPHPExcel = new PHPExcel();
		$header = self::$PropsListHeader;
		$obj = $objPHPExcel->setActiveSheetIndex(0);
		$i = 0;
		foreach ($header as $k => $val) {
			$prefix = $range[$i];
			$obj->setCellValue($prefix . '1', $val);
			$i++;
		}
		$no = 2;
		foreach ($rows as $vals) {

			//if ($vals['effect_txt'] == 'WEAPON_CREATE') {
			//	$newId = T_Weapon::$id2id[$vals['effect_val']];
			//	 B_DB::instance('BaseProps')->update(array('effect_val'=>$newId), $vals['id']);
			//}



			$vData = $vals;
			if (isset($vals['price'])) {
				$price = json_decode($vals['price'], true);
				if (!isset($price[1])) {
					$price[1] = 0;
				}
				if (!isset($price[2])) {
					$price[2] = 0;
				}
				$vData['price'] = '1:' . $price[1] . ',' . '2:' . $price[2];
			}
			$vData['create_at'] = !empty($vals['create_at']) ? date('Y-m-d H:i:s', $vals['create_at']) : '';
			$i = 0;
			foreach ($header as $k => $line) {
				$prefix = $range[$i];
				$obj->setCellValue($prefix . $no, $vData[$k]);
				$i++;
			}
			$no++;
		}
		$filename = 'props_list' . date('YmdHis') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	/** 道具导入 */
	static public function APropsListImport() {
		if (!empty($_POST)) {
			$arrIdName = M_Props::getPropsIdName(); //获取道具ID=>名字的数组

			$headerArr = array_keys(self::$PropsListHeader);
			$tmp = range('A', 'Z');
			$range = $tmp;
			foreach ($tmp as $val) {
				$range[] = 'A' . $val;
			}
			foreach ($tmp as $val) {
				$range[] = 'B' . $val;
			}
			if (!empty($_FILES['propscsvfile']['tmp_name'])) {
				require_once ADM_PATH . '/lib/PHPExcel.php';
				$file = $_FILES["propscsvfile"]['tmp_name'];
				$objReader = new PHPExcel_Reader_Excel5();
				$objReader->setReadDataOnly(true);
				$objPHPExcel = $objReader->load($file);

				$currentSheet = $objPHPExcel->getSheet(0);
				/**取得最大的列号*/
				$allColumn = $currentSheet->getHighestColumn();
				/**取得一共有多少行*/
				$allRow = $currentSheet->getHighestRow();
				/**从第二行开始输出，因为excel表中第一行为列名*/
				$arr = array();
				//$allColumn = count(self::$FBheader);
				for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
					/**从第A列开始输出*/
					$tmp = array();
					$n = 0;
					for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
						$address = $currentColumn . $currentRow;
						$key = $headerArr[$n];

						$v = $currentSheet->getCell($address)->getValue();
						if (!empty($key)) {
							if ($key == 'price') {
								$array_price = explode(',', $v);
								foreach ($array_price as $value) {
									$price = explode(':', $value);
									if ($price[0] == "1") {
										if ($price[1] != 0) {
											$a[1] = $price[1];
										}
									}
									if ($price[0] == "2") {
										if ($price[1] != 0) {
											$a[2] = $price[1];
										}
									}
								}
								$val = json_encode($a);
								unset($a[1]);
								unset($a[2]);
							} else {
								$val = $v;
							}

							$tmp[$key] = trim($val);
						}
						$n++;
					}

					if (!empty($tmp['id'])) {

						if (!empty($err)) {
							print_r($err);
							exit;
						}

						if (!empty($tmp['name']) && !empty($tmp['id'])) {
							if (in_array($tmp['name'], $arrIdName) && $tmp['id'] != array_search($tmp['name'], $arrIdName)) {
								$tip[$tmp['id']] = '道具重名';
							} else {
								if (B_DB::instance('BaseProps')->get($tmp['id'])) {
									$ret = B_DB::instance('BaseProps')->update($tmp, $tmp['id']);
									$tip[$tmp['id']] = $ret ? '更新成功' : '更新失败';
									M_Props::cleanBaseInfo($tmp['id']);
								} else {
									$ret = B_DB::instance('BaseProps')->insert($tmp);
									$tip[$tmp['id']] = $ret ? '插入成功' : '插入失败';
								}


							}
						} else {
							$tip[$tmp['id']] = "错误数据";
						}
					}
				}

				$pageData['tip'] = $tip;
			}
		}
		$pageData['act'] = 'PropsListImport';
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/PropsImport');
	}

	static public function AEquipListExport() //道具基础信息的导出
	{
		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		$tmp = range('A', 'Z');
		$range = $tmp;
		foreach ($tmp as $val) {
			$range[] = 'A' . $val;
		}
		foreach ($tmp as $val) {
			$range[] = 'B' . $val;
		}
		require_once ADM_PATH . '/lib/PHPExcel.php';

		$rows = B_DB::instance('BaseEquipTpl')->getsBy(array(), array('id' => 'ASC'));

		$objPHPExcel = new PHPExcel();
		$header = self::$EquipListHeader;
		$obj = $objPHPExcel->setActiveSheetIndex(0);
		$i = 0;
		foreach ($header as $k => $val) {
			$prefix = $range[$i];
			$obj->setCellValue($prefix . '1', $val);
			$i++;
		}
		$no = 2;
		foreach ($rows as $vals) {

			$vData = $vals;
			// 			if(isset($vals['price']))
			// 			{
			// 				$price=json_decode($vals['price'],true);
			// 				if(!isset($price[1]))
			// 				{
			// 					$price[1]=0;
			// 				}
			// 				if(!isset($price[2]))
			// 				{
			// 					$price[2]=0;
			// 				}
			// 				$vData['price'] = '1:'.$price[1].','.'2:'.$price[2];
			// 			}
			// 			$vData['create_at'] = !empty($vals['create_at']) ? date('Y-m-d H:i:s', $vals['create_at']) :'';
			$i = 0;
			foreach ($header as $k => $line) {
				$prefix = $range[$i];
				$obj->setCellValue($prefix . $no, $vData[$k]);
				$i++;
			}
			$no++;
		}
		$filename = 'equip_list' . date('YmdHis') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	static public function AEquipListImport() {
		if (!empty($_POST)) {
			$headerArr = array_keys(self::$EquipListHeader);
			$tmp = range('A', 'Z');
			$range = $tmp;
			foreach ($tmp as $val) {
				$range[] = 'A' . $val;
			}
			foreach ($tmp as $val) {
				$range[] = 'B' . $val;
			}
			if (!empty($_FILES['equipcsvfile']['tmp_name'])) {
				require_once ADM_PATH . '/lib/PHPExcel.php';
				$file = $_FILES["equipcsvfile"]['tmp_name'];
				$objReader = new PHPExcel_Reader_Excel5();
				$objReader->setReadDataOnly(true);
				$objPHPExcel = $objReader->load($file);

				$currentSheet = $objPHPExcel->getSheet(0);
				/**取得最大的列号*/
				$allColumn = $currentSheet->getHighestColumn();
				/**取得一共有多少行*/
				$allRow = $currentSheet->getHighestRow();
				/**从第二行开始输出，因为excel表中第一行为列名*/
				$arr = array();
				//$allColumn = count(self::$FBheader);
				for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
					/**从第A列开始输出*/
					$tmp = array();
					$n = 0;
					for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
						$address = $currentColumn . $currentRow;
						$key = $headerArr[$n];
						$v = $currentSheet->getCell($address)->getValue();
						if (!empty($key)) {
							$val = $v;
							$tmp[$key] = trim($val);
						}
						$n++;
					}
					if (empty($tmp['id'])) {
						echo 'ID不能为空';
						exit;
					}

					if (!empty($err)) {
						print_r($err);
						exit;
					}

					if (!empty($tmp['name']) && !empty($tmp['id'])) {

						if (B_DB::instance('BaseEquipTpl')->get($tmp['id'])) {
							$ret = B_DB::instance('BaseEquipTpl')->update($tmp['id'], $tmp);
							$tip[$tmp['id']] = $ret ? '更新成功' : '更新失败';
						} else {
							$ret = B_DB::instance('BaseEquipTpl')->insert($tmp);
							$tip[$tmp['id']] = $ret ? '插入成功' : '插入失败';
						}

					} else {
						$tip[$tmp['id']] = "错误数据";
					}

				}

				$pageData['tip'] = $tip;
			}
		}
		$pageData['act'] = 'EquipListImport';
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/EquipImport');
	}

	static public function AWeaponListExport() //武器基础信息的导出
	{
		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		$tmp = range('A', 'Z');
		$range = $tmp;
		foreach ($tmp as $val) {
			$range[] = 'A' . $val;
		}
		foreach ($tmp as $val) {
			$range[] = 'B' . $val;
		}
		require_once ADM_PATH . '/lib/PHPExcel.php';

		$rows = B_DB::instance('BaseWeapon')->getsBy(array(), array('id' => 'ASC'));

		$objPHPExcel = new PHPExcel();
		$header = self::$WeaponListHeader;
		$obj = $objPHPExcel->setActiveSheetIndex(0);
		$i = 0;
		foreach ($header as $k => $val) {
			$prefix = $range[$i];
			$obj->setCellValue($prefix . '1', $val);
			$i++;
		}
		$no = 2;
		foreach ($rows as $vals) {

			$oldId= $vals['id'];

			//$newId = T_Weapon::$id2id[$oldId];
			//$path = '/opt/res/swf/roles/'.$vals['army_id'].'/';
			//copy($path.$oldId.'.swf', $path.$newId.'.swf');

			//$tmp =  T_Weapon::$id2id;
			//$tmp = array_flip($tmp);

			//$tmp1 = $tmp[$oldId];
			//$imgPath = '/opt/res/imgs/weapon/';
			//copy($imgPath.$tmp1.'.jpg', $imgPath.$oldId.'.jpg');

			$vData = $vals;
			if (isset($vals['need_build'])) {
				$need_build = json_decode($vals['need_build'], true);
				if (!empty($need_build)) {
					foreach ($need_build as $key => $value) {
						$vData['need_build'] = $key . ':' . $value . ',';
					}
					$vData['need_build'] = substr($vData['need_build'], 0, strlen($vData['need_build']) - 1);
				} else {
					$vData['need_build'] = '';
				}

			}
			if (isset($vals['need_tech'])) {
				$need_build = json_decode($vals['need_tech'], true);
				if (!empty($need_build)) {
					foreach ($need_build as $key => $value) {
						$vData['need_tech'] = $key . ':' . $value . ',';
					}
					$vData['need_tech'] = substr($vData['need_tech'], 0, strlen($vData['need_tech']) - 1);
				} else {
					$vData['need_tech'] = '';
				}
			}
			$i = 0;
			foreach ($header as $k => $line) {
				$prefix = $range[$i];
				$obj->setCellValue($prefix . $no, $vData[$k]);
				$i++;
			}
			$no++;
		}
		$filename = 'weapon_list' . date('YmdHis') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	static public function AWeaponListImport() //武器基础信息的导入
	{
		if (!empty($_POST)) {
			$headerArr = array_keys(self::$WeaponListHeader);
			$tmp = range('A', 'Z');
			$range = $tmp;
			foreach ($tmp as $val) {
				$range[] = 'A' . $val;
			}
			foreach ($tmp as $val) {
				$range[] = 'B' . $val;
			}
			if (!empty($_FILES['Weaponcsvfile']['tmp_name'])) {
				require_once ADM_PATH . '/lib/PHPExcel.php';
				$file = $_FILES["Weaponcsvfile"]['tmp_name'];
				$objReader = new PHPExcel_Reader_Excel5();
				$objReader->setReadDataOnly(true);
				$objPHPExcel = $objReader->load($file);

				$currentSheet = $objPHPExcel->getSheet(0);
				/**取得最大的列号*/
				$allColumn = $currentSheet->getHighestColumn();
				/**取得一共有多少行*/
				$allRow = $currentSheet->getHighestRow();
				/**从第二行开始输出，因为excel表中第一行为列名*/
				$allColumn = count(self::$WeaponListHeader);
				$arr = array();
				//$allColumn = count(self::$FBheader);
				for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
					/**从第A列开始输出*/
					$tmp = array();
					$n = 0;
					for ($currentColumn = 0; $currentColumn <= $allColumn; $currentColumn++) {
						$currentColumnT = $range[$currentColumn];
						$address = $currentColumnT . $currentRow;
						$key = isset($headerArr[$n]) ? $headerArr[$n] : '';
						$v = $currentSheet->getCell($address)->getValue();
						if (!empty($key)) {
							if ($key == 'need_build') {
								if ($v) {
									$array_price = explode(',', $v);
									$a = array();
									foreach ($array_price as $value) {
										$need_build = explode(':', $value);
										$a[$need_build[0]] = $need_build[1];

									}
									$val = json_encode($a);
								} else {
									$val = '[]';
								}
							} elseif ($key == 'need_tech') {
								if ($v) {
									$array_price = explode(',', $v);
									$b = array();
									foreach ($array_price as $value) {
										$need_tech = explode(':', $value);
										$b[$need_tech[0]] = $need_tech[1];

									}
									$val = json_encode($b);
								} else {
									$val = '[]';
								}
							} else {
								$val = $v;
							}

							$tmp[$key] = trim($val);

						}
						$n++;
					}
					if (empty($tmp['id'])) {
						echo 'ID不能为空';
						exit;
					}
					if (!empty($err)) {
						print_r($err);
						exit;
					}
					if (!empty($tmp['name']) && !empty($tmp['id'])) {

						if (B_DB::instance('BaseWeapon')->get($tmp['id'])) {
							$ret = B_DB::instance('BaseWeapon')->update($tmp, $tmp['id']);
							$tip[$tmp['id']] = $ret ? '更新成功' : '更新失败';
						} else {
							$ret = B_DB::instance('BaseWeapon')->insert($tmp);
							$tip[$tmp['id']] = $ret ? '插入成功' : '插入失败';
						}

					} else {
						$tip[$tmp['id']] = "错误数据";
					}

				}

				$pageData['tip'] = $tip;
			}
		}
		$pageData['act'] = 'WeaponListImport';
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/WeaponImport');
	}

	/**
	 * 商城物品列表
	 * *
	 */
	static public function AMallList() {
		$args = array(
			'page' => FILTER_SANITIZE_NUMBER_INT,
		);
		$formVals = filter_var_array($_REQUEST, $args);
		$length = 50;
		$curPage = max(1, $formVals['page']);

		if (isset($_GET['db'])) {
			$start = ($curPage - 1) * $length;
			$pageData['list'] = B_DB::instance('BaseMall')->getList($start, $length);
			$totalNum = B_DB::instance('BaseMall')->count();
		} else {
			$list = M_Base::mallAll();
			$offset = ($curPage - 1) * $length;
			$pageData['list'] = array_slice($list, $offset, $length);
			$totalNum = count($list);
		}

		$rc = new B_Cache_RC(T_Key::MALL_NUM);
		$data = $rc->hgetall();
		foreach ($pageData['list'] as $key => $infoId) {
			if (!empty($data)) {
				foreach ($data as $key1 => $value) {
					if ($infoId['id'] == $key1) {
						$pageData['list'][$key]['num'] = $data[$key1];
					}
				}
			} else {
				$infoNum = $infoId['num'];
				$rc->hincrby($infoId['id'], $infoNum);
				$pageData['list'][$key]['num'] = $infoNum;
			}

		}
		foreach ($pageData['list'] as $key => $value) {
			if ($value['item_type'] == M_Mall::ITEM_PROPS) {
				$propsInfo = M_Props::baseInfo($value['item_id']);
				$pageData['list'][$key]['name'] = $propsInfo['name'];

			} else if ($value['item_type'] == M_Mall::ITEM_HERO) {
				$heroTplInfo = M_Hero::baseInfo($value['item_id']);
				$pageData['list'][$key]['name'] = $heroTplInfo['nickname'];
			} else if ($value['item_type'] == M_Mall::ITEM_EQUIP) {
				$equiTplInfo = M_Equip::baseInfo($value['item_id']);
				$pageData['list'][$key]['name'] = $equiTplInfo['name'];
			}
		}

		$pageData['page'] = B_Page::make($curPage, $totalNum, $length);

		B_View::setVal('pageData', $pageData);
		B_View::render('Base/MallList');

	}

	/**
	 * 商城列表导出
	 */
	static public function AMallListExport() {
		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		$tmp = range('A', 'Z');
		$range = $tmp;
		foreach ($tmp as $val) {
			$range[] = 'A' . $val;
		}
		foreach ($tmp as $val) {
			$range[] = 'B' . $val;
		}
		require_once ADM_PATH . '/lib/PHPExcel.php';

		$rows = B_DB::instance('BaseMall')->getsBy(array(), array('id' => 'ASC'));

		$objPHPExcel = new PHPExcel();
		$header = self::$MallListHeader;
		$obj = $objPHPExcel->setActiveSheetIndex(0);
		$i = 0;
		foreach ($header as $k => $val) {
			$prefix = $range[$i];
			$obj->setCellValue($prefix . '1', $val);
			$i++;
		}
		$no = 2;
		foreach ($rows as $vals) {

			$vData = $vals;
			if (isset($vals['price'])) {
				$str = '';
				$price = json_decode($vals['price'], true);
				foreach (M_Mall::$payType as $k => $v) {
					if (!isset($price[$k])) {
						$v1 = 0;
					} else {
						$v1 = $price[$k];
					}
					$str[] = "{$k}:{$v1}";
				}

				$vData['price'] = implode(",", $str);
			}
			if (isset($vals['up_time'])) {
				$vData['up_time'] = date('Y-m-d H:i:s', $vals['up_time']);
			}
			if (isset($vals['down_time'])) {
				$vData['down_time'] = date('Y-m-d H:i:s', $vals['down_time']);
			}
			$vData['create_at'] = !empty($vals['create_at']) ? date('Y-m-d H:i:s', $vals['create_at']) : '';
			$i = 0;

			foreach ($header as $k => $line) {
				$prefix = $range[$i];
				$obj->setCellValue($prefix . $no, $vData[$k]);
				$i++;
			}
			$objPHPExcel->getActiveSheet()->getStyle('G' . $no)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT); //设置单元格的时间格式样式
			$objPHPExcel->getActiveSheet()->getStyle('H' . $no)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT); //设置单元格的时间格式样式
			$no++;

		}
		$filename = 'mall_list' . date('YmdHis') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	/**
	 * 商城列表导入
	 */
	static public function AMallListImport() {
		if (!empty($_POST)) {
			$headerArr = array_keys(self::$MallListHeader);
			$tmp = range('A', 'Z');
			$range = $tmp;
			foreach ($tmp as $val) {
				$range[] = 'A' . $val;
			}
			foreach ($tmp as $val) {
				$range[] = 'B' . $val;
			}
			if (!empty($_FILES['mallcsvfile']['tmp_name'])) {
				require_once ADM_PATH . '/lib/PHPExcel.php';
				$file = $_FILES["mallcsvfile"]['tmp_name'];
				$objReader = new PHPExcel_Reader_Excel5();
				$objReader->setReadDataOnly(true);
				$objPHPExcel = $objReader->load($file);

				$currentSheet = $objPHPExcel->getSheet(0);
				/**取得最大的列号*/
				$allColumn = $currentSheet->getHighestColumn();
				/**取得一共有多少行*/
				$allRow = $currentSheet->getHighestRow();
				/**从第二行开始输出，因为excel表中第一行为列名*/
				$arr = array();
				//$allColumn = count(self::$FBheader);
				for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
					/**从第A列开始输出*/
					$tmp = array();
					$n = 0;
					for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
						$address = $currentColumn . $currentRow;
						$key = $headerArr[$n];
						$v = $currentSheet->getCell($address)->getValue();
						if (!empty($key)) {
							if ($key == 'price') {
								$array_price = explode(',', $v);
								foreach ($array_price as $value) {
									$price = explode(':', $value);
									$a[$price[0]] = $price[1];
								}
								$val = json_encode($a);
								unset($a[1]);
								unset($a[2]);
								unset($a[3]);
							} elseif ($key == 'up_time' || $key == 'down_time') {
								$val = strtotime($v);
							} else {
								$val = $v;
							}

							$tmp[$key] = trim($val);

						}
						$n++;
					}
					if (empty($tmp['id'])) {
						echo 'ID不能为空';
						exit;
					}

					if (!empty($err)) {
						print_r($err);
						exit;
					}

					if (!empty($tmp['item_id']) && !empty($tmp['id'])) {

						if (B_DB::instance('BaseMall')->get($tmp['id']) && empty($tmp['del'])) {

							if ($tmp['del'] == 1) {
								$ret = B_DB::instance('BaseMall')->delete($tmp['id']);
								$tip[$tmp['id']] = $ret ? '删除成功' : '删除失败';
							} else {
								unset($tmp['del']);
								$ret = B_DB::instance('BaseMall')->update($tmp, $tmp['id']);
								$tip[$tmp['id']] = $ret ? '更新成功' : '更新失败';
							}

						} else {
							if ($tmp['del'] == 1) {
								$ret = B_DB::instance('BaseMall')->delete($tmp['id']);
								$tip[$tmp['id']] = $ret ? '删除成功' : '删除失败';
							} else {
								unset($tmp['del']);
								$tmp['create_at'] = time();
								$ret = B_DB::instance('BaseMall')->insert($tmp);
								$tip[$tmp['id']] = $ret ? '插入成功' : '插入失败';
							}

						}

					} else {
						$tip[$tmp['id']] = "错误数据";
					}

				}

				$pageData['tip'] = $tip;
			}
		}
		$pageData['act'] = 'MallListImport';
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/MallImport');
	}

	static public function ADelMallCache() {
		$ret = M_Base::mallAll(true);
		echo "<script>";
		echo $ret ? "alert('更新成功');" : "alert('更新失败');";
		echo "</script>";
	}

	static public function AEquipSuitExport() //道具基础信息的导出
	{
		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		$tmp = range('A', 'Z');
		$range = $tmp;
		foreach ($tmp as $val) {
			$range[] = 'A' . $val;
		}
		foreach ($tmp as $val) {
			$range[] = 'B' . $val;
		}
		require_once ADM_PATH . '/lib/PHPExcel.php';

		$rows = B_DB::instance('BaseEquipSuit')->getsBy(array(), array('id' => 'ASC'));

		$objPHPExcel = new PHPExcel();
		$header = self::$EquipSuitHeader;
		$obj = $objPHPExcel->setActiveSheetIndex(0);
		$i = 0;
		foreach ($header as $k => $val) {
			$prefix = $range[$i];
			$obj->setCellValue($prefix . '1', $val);
			$i++;
		}
		$no = 2;
		foreach ($rows as $vals) {

			$vData = $vals;
			if (isset($vals['effect'])) {
				$effect = json_decode($vals['effect'], true);
				$vData['effect'] = '';

				foreach ($effect as $key => $value) {
					$vData['effect'] .= $key . ':';
					foreach ($value as $k => $v) {
						switch ($k) {
							case 'TZ_ZH':
								$k1 = 1;
								break;
							case 'TZ_JS':
								$k1 = 2;
								break;
							case 'TZ_TS':
								$k1 = 3;
								break;
							case 'TZ_ALLATTR':
								$k1 = 4;
								break;
							case 'TZ_CRIT':
								$k1 = 5;
								break;
							case 'TZ_AL_ATK':
								$k1 = 6;
								break;
							case 'TZ_AL_DEF':
								$k1 = 7;
								break;
							case 'TZ_AL_LIFE':
								$k1 = 8;
								break;
							case 'TZ_AL_ADD_HURT':
								$k1 = 9;
								break;
							case 'TZ_AL_DEF_HURT':
								$k1 = 10;
								break;
						}
						$vData['effect'] .= $k1 . ',' . $v . ';';

					}
					$vData['effect'] = substr($vData['effect'], 0, strlen($vData['effect']) - 1);
					$vData['effect'] .= '_';

				}

				$vData['effect'] = substr($vData['effect'], 0, strlen($vData['effect']) - 1);


			}
			$i = 0;
			foreach ($header as $k => $line) {
				$prefix = $range[$i];
				$obj->setCellValue($prefix . $no, $vData[$k]);
				$i++;
			}
			$no++;
		}
		$filename = 'equip_suit' . date('YmdHis') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	static public function AEquipSuitImport() {
		if (!empty($_POST)) {
			$headerArr = array_keys(self::$EquipSuitHeader);
			$tmp = range('A', 'Z');
			$range = $tmp;
			foreach ($tmp as $val) {
				$range[] = 'A' . $val;
			}
			foreach ($tmp as $val) {
				$range[] = 'B' . $val;
			}
			if (!empty($_FILES['equipsuitcsvfile']['tmp_name'])) {
				require_once ADM_PATH . '/lib/PHPExcel.php';
				$file = $_FILES["equipsuitcsvfile"]['tmp_name'];
				$objReader = new PHPExcel_Reader_Excel5();
				$objReader->setReadDataOnly(true);
				$objPHPExcel = $objReader->load($file);

				$currentSheet = $objPHPExcel->getSheet(0);
				/**取得最大的列号*/
				$allColumn = $currentSheet->getHighestColumn();
				/**取得一共有多少行*/
				$allRow = $currentSheet->getHighestRow();
				/**从第二行开始输出，因为excel表中第一行为列名*/
				$arr = array();
				//$allColumn = count(self::$FBheader);
				for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
					/**从第A列开始输出*/
					$tmp = array();
					$n = 0;
					for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
						$address = $currentColumn . $currentRow;
						$key = $headerArr[$n];
						$v = $currentSheet->getCell($address)->getValue();
						if (!empty($key)) {
							if ($key == 'effect') {
								$array_effect = explode('_', $v);
								foreach ($array_effect as $value) {
									$effect = explode(':', $value);
									if (isset($effect[0]) && $effect[0] == "2") {
										$temp1 = array();
										$temp1 = explode(';', $effect[1]);
										if (!empty($temp1)) {
											foreach ($temp1 as $val) {
												$temp2 = array();
												$temp2 = explode(',', $val);
												if (!empty($temp2)) {
													$a[2][$temp2[0]] = $temp2[1];
												}
											}
										}

									}
									if (isset($effect[0]) && $effect[0] == "3") {
										$temp1 = array();
										$temp1 = explode(';', $effect[1]);
										if (!empty($temp1)) {
											foreach ($temp1 as $val) {
												$temp2 = array();
												$temp2 = explode(',', $val);
												if (!empty($temp2)) {
													$a[3][$temp2[0]] = $temp2[1];
												}
											}
										}

									}
									if (isset($effect[0]) && $effect[0] == "4") {
										$temp1 = array();
										$temp1 = explode(';', $effect[1]);
										if (!empty($temp1)) {
											foreach ($temp1 as $val) {
												$temp2 = array();
												$temp2 = explode(',', $val);
												if (!empty($temp2)) {
													$a[4][$temp2[0]] = $temp2[1];
												}
											}
										}

									}
									if (isset($effect[0]) && $effect[0] == "5") {
										$temp1 = array();
										$temp1 = explode(';', $effect[1]);
										if (!empty($temp1)) {
											foreach ($temp1 as $val) {
												$temp2 = array();
												$temp2 = explode(',', $val);
												if (!empty($temp2)) {
													$a[5][$temp2[0]] = $temp2[1];
												}
											}
										}

									}
									if (isset($effect[0]) && $effect[0] == "6") {
										$temp1 = array();
										$temp1 = explode(';', $effect[1]);
										if (!empty($temp1)) {
											foreach ($temp1 as $val) {
												$temp2 = array();
												$temp2 = explode(',', $val);
												if (!empty($temp2)) {
													$a[6][$temp2[0]] = $temp2[1];
												}
											}
										}

									}


								}
								foreach ($a as $k => $values) {
									foreach ($values as $key1 => $value) {
										switch ($key1) {
											case 1:
												$key2 = 'TZ_ZH';
												unset($a[$k][$key1]);
												$a[$k][$key2] = $value;
												break;
											case 2:
												$key2 = 'TZ_JS';
												unset($a[$k][$key1]);
												$a[$k][$key2] = $value;
												break;
											case 3:
												$key2 = 'TZ_TS';
												unset($a[$k][$key1]);
												$a[$k][$key2] = $value;
												break;
											case 4:
												$key2 = 'TZ_ALLATTR';
												unset($a[$k][$key1]);
												$a[$k][$key2] = $value;
												break;
											case 5:
												$key2 = 'TZ_CRIT';
												unset($a[$k][$key1]);
												$a[$k][$key2] = $value;
												break;
											case 6:
												$key2 = 'TZ_AL_ATK';
												unset($a[$k][$key1]);
												$a[$k][$key2] = $value;
												break;
											case 7:
												$key2 = 'TZ_AL_DEF';
												unset($a[$k][$key1]);
												$a[$k][$key2] = $value;
												break;
											case 8:
												$key2 = 'TZ_AL_LIFE';
												unset($a[$k][$key1]);
												$a[$k][$key2] = $value;
												break;
											case 9:
												$key2 = 'TZ_AL_ADD_HURT';
												unset($a[$k][$key1]);
												$a[$k][$key2] = $value;
												break;
											case 10:
												$key2 = 'TZ_AL_DEF_HURT';
												unset($a[$k][$key1]);
												$a[$k][$key2] = $value;
												break;
										}

									}
								}
								$val = json_encode($a);
								unset($a[2]);
								unset($a[3]);
								unset($a[4]);
								unset($a[5]);
								unset($a[6]);
							} else {
								$val = $v;
							}
							$tmp[$key] = trim($val);
						}
						$n++;
					}
					if (empty($tmp['id'])) {
						echo 'ID不能为空';
						exit;
					}

					if (!empty($err)) {
						print_r($err);
						exit;
					}

					if (!empty($tmp['name']) && !empty($tmp['id'])) {
						if (B_DB::instance('BaseEquipSuit')->get($tmp['id'])) {
							$ret = B_DB::instance('BaseEquipSuit')->update($tmp, $tmp['id']);
							$tip[$tmp['id']] = $ret ? '更新成功' : '更新失败';
						} else {
							$ret = B_DB::instance('BaseEquipSuit')->insert($tmp);
							$tip[$tmp['id']] = $ret ? '插入成功' : '插入失败';
						}

					} else {
						$tip[$tmp['id']] = "错误数据";
					}

				}

				$pageData['tip'] = $tip;
			}
		}
		$pageData['act'] = 'EquipSuitImport';
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/EquipSuitImport');
	}

	//突围列表
	static public function ABoutList() {
		$args = array(
			'page' => FILTER_SANITIZE_NUMBER_INT,
		);
		$formVals = filter_var_array($_REQUEST, $args);
		$length = 20;
		$curPage = max(1, $formVals['page']);

		if (isset($_GET['db'])) {
			$start = ($curPage - 1) * $length;
			$pageData['list'] = B_DB::instance('BaseBreakout')->getList($start, $length);
			$totalNum = B_DB::instance('BaseBreakout')->count();
		} else {
			$list = M_Base::breakoutAll();
			$offset = ($curPage - 1) * $length;
			$pageData['list'] = array_slice($list, $offset, $length);
			$totalNum = count($list);
		}

		$pageData['page'] = B_Page::make($curPage, $totalNum, $length, 20);

		B_View::setVal('pageData', $pageData);
		B_View::render('Base/BoutList');
	}

	//删除某个突围数据
	static public function ABoutDel() {
		$ret = false;
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if ($id > 0) {
			$ret = B_DB::instance('BaseBreakout')->delete($id);
		}
		echo "<script>";
		echo $ret ? "alert('操作成功!');" : "alert('操作失败!');";
		echo "window.location='?r=Base/BoutList'";
		echo "</script>";
	}

	//突围导入数据
	static public function ABoutImport() {
		$tip = array();
		if (!empty($_POST)) {
			$baseList = B_DB::instance('BaseBreakout')->all();
			$headerArr = array_keys(self::$BoutListHeader);
			if (!empty($_FILES['boutcsvfile']['tmp_name'])) {
				require_once ADM_PATH . '/lib/PHPExcel.php';
				$file = $_FILES["boutcsvfile"]['tmp_name'];

				$objReader = new PHPExcel_Reader_Excel5();
				$objReader->setReadDataOnly(true);
				$objPHPExcel = $objReader->load($file);

				$currentSheet = $objPHPExcel->getSheet(0);
				/**取得最大的列号*/
				$allColumn = $currentSheet->getHighestColumn();
				/**取得一共有多少行*/
				$allRow = $currentSheet->getHighestRow();
				/**从第二行开始输出，因为excel表中第一行为列名*/
				$arr = array();
				for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
					/**从第A列开始输出*/
					$tmp = array();
					$n = 0;
					for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
						$address = $currentColumn . $currentRow;
						$key = $headerArr[$n];
						$v = $currentSheet->getCell($address)->getValue();
						$tmp[$key] = trim($v);
						$n++;
					}

					if (!empty($tmp['id'])) {
						$del = isset($tmp['del']) ? $tmp['del'] : 0;
						unset($tmp['del']);
						if (!empty($baseList[$tmp['id']])) {
							if ($del) {
								$ret = B_DB::instance('BaseBreakout')->delete($tmp['id']);
								$tip[$tmp['id']] = $ret ? '删除成功' : '删除失败';
							} else {
								//var_dump($tmp);
								//echo "<hr>";
								$ret = B_DB::instance('BaseBreakout')->update($tmp, $tmp['id']);
								$tip[$tmp['id']] = $ret ? '更新成功' : '更新失败';
							}
						} else {
							$ret = B_DB::instance('BaseBreakout')->insert($tmp);
							$tip[$tmp['id']] = $ret ? '插入成功' : '插入失败';
						}
					} else {
						$tip[$tmp['id']] = '错误数据'; //var_export($tmp)
					}
					//$arr[] = $tmp;
				}

				$pageData['tip'] = $tip;
			}
		}
		$pageData['tip'] = $tip;
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/BoutImport');
	}

	//突围导出数据
	static public function ABoutExport() {
		$header = self::$BoutListHeader;

		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		$tmp = range('A', 'Z');
		$range = $tmp;
		foreach ($tmp as $val) {
			$range[] = 'A' . $val;
		}
		foreach ($tmp as $val) {
			$range[] = 'B' . $val;
		}

		require_once ADM_PATH . '/lib/PHPExcel.php';

		$rows = B_DB::instance('BaseBreakout')->all();
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		$obj = $objPHPExcel->setActiveSheetIndex(0);
		$i = 0;
		foreach ($header as $k => $val) {
			$prefix = $range[$i];
			$obj->setCellValue($prefix . '1', $val);
			$i++;
		}

		$no = 2;
		foreach ($rows as $line) {
			$data = $line;

			$i = 0;
			foreach ($header as $k => $v) {
				$prefix = $range[$i];
				$obj->setCellValue($prefix . $no, $data[$k]);
				$i++;
			}
			$no++;
		}

		// Redirect output to a client’s web browser (Excel5)
		$filename = 'base_breakout_' . date('YmdHis') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	//突围清除缓存数据
	static public function ABoutCacheUp() {
		$ret = M_Base::breakoutAll(true);
		echo "<script>";
		echo $ret ? "alert('更新成功');" : "alert('更新失败');";
		echo "</script>";
	}

	static public function AQuestList() {
		if (isset($_GET['db'])) {
			$list = B_DB::instance('BaseQuest')->all();
		} else {
			$tmp = M_Base::questAll();
			$arr = array();
			foreach ($tmp['info'] as $key => $val) {
				$val['cond_pass'] = implode(",", $val['cond_pass']);
				$arr[$key] = $val;
			}
			$list = $arr;
		}
		$pageData['list'] = $list;
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/QuestList');
	}

	static public function AQuestExport() {
		$header = self::$questHeaderArr;

		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		$tmp = range('A', 'Z');
		$range = $tmp;
		foreach ($tmp as $val) {
			$range[] = 'A' . $val;
		}
		foreach ($tmp as $val) {
			$range[] = 'B' . $val;
		}

		require_once ADM_PATH . '/lib/PHPExcel.php';

		$rows = B_DB::instance('BaseQuest')->all();

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		$obj = $objPHPExcel->setActiveSheetIndex(0);

		$i = 0;
		foreach ($header as $k => $val) {
			$prefix = $range[$i];
			$obj->setCellValue($prefix . '1', $val);
			$i++;
		}

		$no = 2;
		foreach ($rows as $line) {
			$data = $line;

			$i = 0;
			foreach ($header as $k => $v) {
				$prefix = $range[$i];
				$obj->setCellValue($prefix . $no, $data[$k]);
				$i++;
			}
			$no++;
		}

		// Redirect output to a client’s web browser (Excel5)
		$filename = 'base_quest_' . date('YmdHis') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	static public function AQuestImport() {
		$tip = array();
		if (!empty($_POST)) {
			$baseList = B_DB::instance('BaseQuest')->all();
			$headerArr = array_keys(self::$questHeaderArr);
			if (!empty($_FILES['csvfile']['tmp_name'])) {
				require_once ADM_PATH . '/lib/PHPExcel.php';
				$file = $_FILES["csvfile"]['tmp_name'];

				$objReader = new PHPExcel_Reader_Excel5();
				$objReader->setReadDataOnly(true);
				$objPHPExcel = $objReader->load($file);

				$currentSheet = $objPHPExcel->getSheet(0);
				/**取得最大的列号*/
				$allColumn = $currentSheet->getHighestColumn();
				/**取得一共有多少行*/
				$allRow = $currentSheet->getHighestRow();
				/**从第二行开始输出，因为excel表中第一行为列名*/
				$arr = array();
				for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
					/**从第A列开始输出*/
					$tmp = array();
					$n = 0;
					for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
						$address = $currentColumn . $currentRow;
						$key = $headerArr[$n];
						$v = $currentSheet->getCell($address)->getValue();
						$tmp[$key] = trim($v);
						$n++;
					}

					if (!empty($tmp['name']) && !empty($tmp['id'])) {
						$del = $tmp['del'];
						unset($tmp['del']);

						if (!empty($baseList[$tmp['id']])) {
							if ($del) {
								$ret = B_DB::instance('BaseQuest')->delete($tmp['id']);
								$tip[$tmp['id']] = $ret ? '删除成功' : '删除失败';
							} else {
								$ret = B_DB::instance('BaseQuest')->update($tmp,$tmp['id']);
								$tip[$tmp['id']] = $ret ? '更新成功' : '更新失败';

							}
						} else {
							$ret = B_DB::instance('BaseQuest')->insert($tmp);
							$tip[$tmp['id']] = $ret ? '插入成功' : '插入失败';
						}
					} else {
						$tip[$tmp['id']] = "错误数据";
					}
					//$arr[] = $tmp;
				}

				$pageData['tip'] = $tip;
			}
		}

		M_Base::questAll(true);
		$pageData['tip'] = $tip;
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/QuestImport');
	}

	static public function AQuestCacheUp() {
		$ret = M_Base::questAll(true);
		echo "<script>";
		echo $ret ? "alert('更新成功');" : "alert('更新失败');";
		echo "</script>";
	}

	static public function AMultiFBList() {
		$pageData = array();
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/MultiFBList');
	}

	static public function AMultiFBClean() {
		$ret = M_MultiFB::clean();
		echo "<script>";
		echo $ret ? "alert('更新成功');" : "alert('更新失败');";
		echo "</script>";
	}

	static public function AMultiFBExport() {
		$header = self::$MultiListHeader;

		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		$tmp = range('A', 'Z');
		$range = $tmp;
		foreach ($tmp as $val) {
			$range[] = 'A' . $val;
		}
		foreach ($tmp as $val) {
			$range[] = 'B' . $val;
		}

		require_once ADM_PATH . '/lib/PHPExcel.php';

		$rows = B_DB::instance('BaseMultiFB')->all();

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		$obj = $objPHPExcel->setActiveSheetIndex(0);

		$i = 0;
		foreach ($header as $k => $val) {
			$prefix = $range[$i];
			$obj->setCellValue($prefix . '1', $val);
			$i++;
		}

		$no = 2;
		foreach ($rows as $line) {
			$data = $line;
			$i = 0;
			foreach ($header as $k => $v) {
				$prefix = $range[$i];
				$obj->setCellValue($prefix . $no, $data[$k]);
				$i++;
			}
			$no++;
		}

		// Redirect output to a client’s web browser (Excel5)
		$filename = 'base_multi_fb_' . date('YmdHis') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	static public function AMultiFBImport() {
		$tip = array();
		if (!empty($_POST)) {
			$headerArr = array_keys(self::$MultiListHeader);
			if (!empty($_FILES['csvfile']['tmp_name'])) {
				require_once ADM_PATH . '/lib/PHPExcel.php';
				$file = $_FILES["csvfile"]['tmp_name'];

				$objReader = new PHPExcel_Reader_Excel5();
				$objReader->setReadDataOnly(true);
				$objPHPExcel = $objReader->load($file);

				$currentSheet = $objPHPExcel->getSheet(0);
				/**取得最大的列号*/
				$allColumn = $currentSheet->getHighestColumn();
				/**取得一共有多少行*/
				$allRow = $currentSheet->getHighestRow();
				/**从第二行开始输出，因为excel表中第一行为列名*/
				$arr = array();
				for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
					/**从第A列开始输出*/
					$tmp = array();
					$n = 0;
					for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
						$address = $currentColumn . $currentRow;
						$key = $headerArr[$n];
						$v = $currentSheet->getCell($address)->getValue();

						$tmp[$key] = trim($v);
						$n++;
					}

					$tmp['del'] = isset($tmp['del']) ? $tmp['del'] : 0;

					if (!empty($tmp['id'])) {
						$isDel = false;
						if (isset($tmp['del']) && $tmp['del'] == 1) {
							$isDel = true;
							unset($tmp['del']);
						}


						if (B_DB::instance('BaseMultiFB')->get($tmp['id'])) {
							if ($isDel) {
								$ret = B_DB::instance('BaseMultiFB')->delete($tmp['id']);
								$tip[$tmp['id']] = $ret ? '删除成功' : '删除失败';
							} else {
								$ret = B_DB::instance('BaseMultiFB')->update($tmp, $tmp['id']);
								$tip[$tmp['id']] = $ret ? '更新成功' : '更新失败';
							}
						} else {
							$ret = B_DB::instance('BaseMultiFB')->insert($tmp);
							$tip[$tmp['id']] = $ret ? '插入成功' : '插入失败';
						}
					} else {
						$tip[$tmp['id']] = "错误数据";
					}

				}

				$pageData['tip'] = $tip;
			}
		}
		$pageData['tip'] = $tip;
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/MultiFBImport');

	}


	static public function AExchangeList() {
		$pageData = array();
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/ExchangeList');
	}

	static public function AExchangeClean() {
		$ret = M_Base::exchangeAll(true);
		echo "<script>";
		echo $ret ? "alert('更新成功');" : "alert('更新失败');";
		echo "</script>";
	}

	static public function AExchangeExport() {
		$header = self::$ExchangeHeader;

		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		$tmp = range('A', 'Z');
		$range = $tmp;
		foreach ($tmp as $val) {
			$range[] = 'A' . $val;
		}
		foreach ($tmp as $val) {
			$range[] = 'B' . $val;
		}

		require_once ADM_PATH . '/lib/PHPExcel.php';

		$rows = B_DB::instance('BaseExchange')->getAll();
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		$obj = $objPHPExcel->setActiveSheetIndex(0);

		$i = 0;
		foreach ($header as $k => $val) {
			$prefix = $range[$i];
			$obj->setCellValue($prefix . '1', $val);
			$i++;
		}

		$no = 2;
		foreach ($rows as $line) {
			$data = $line;
			$i = 0;
			foreach ($header as $k => $v) {
				$prefix = $range[$i];
				$obj->setCellValue($prefix . $no, $data[$k]);
				$i++;
			}
			$no++;
		}

		// Redirect output to a client’s web browser (Excel5)
		$filename = 'base_exchange_' . date('YmdHis') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	static public function AExchangeImport() {
		$tip = array();
		if (!empty($_POST)) {
			$headerArr = array_keys(self::$ExchangeHeader);
			if (!empty($_FILES['csvfile']['tmp_name'])) {
				$baselist = B_DB::instance('BaseExchange')->getAll();
				require_once ADM_PATH . '/lib/PHPExcel.php';
				$file = $_FILES["csvfile"]['tmp_name'];

				$objReader = new PHPExcel_Reader_Excel5();
				$objReader->setReadDataOnly(true);
				$objPHPExcel = $objReader->load($file);

				$currentSheet = $objPHPExcel->getSheet(0);
				/**取得最大的列号*/
				$allColumn = $currentSheet->getHighestColumn();
				/**取得一共有多少行*/
				$allRow = $currentSheet->getHighestRow();
				/**从第二行开始输出，因为excel表中第一行为列名*/
				$arr = array();
				for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
					/**从第A列开始输出*/
					$tmp = array();
					$n = 0;
					for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
						$address = $currentColumn . $currentRow;
						$key = $headerArr[$n];
						$v = $currentSheet->getCell($address)->getValue();

						$tmp[$key] = trim($v);
						$n++;
					}

					$tmp['del'] = isset($tmp['del']) ? $tmp['del'] : 0;

					if (!empty($tmp['id'])) {
						$isDel = 0;
						if (isset($tmp['del'])) {
							$isDel = $tmp['del'] == 1 ? 1 : 0;
							unset($tmp['del']);
						}


						if (isset($baselist[$tmp['id']])) {
							if ($isDel) {
								$ret = B_DB::instance('BaseExchange')->delete($tmp['id']);
								$tip[$tmp['id']] = $ret ? '删除成功' : '删除失败';
							} else {
								$ret = B_DB::instance('BaseExchange')->update($tmp, $tmp['id']);
								$tip[$tmp['id']] = $ret ? '更新成功' : '更新失败';
							}
						} else {
							$ret = B_DB::instance('BaseExchange')->insert($tmp);
							$tip[$tmp['id']] = $ret ? '插入成功' : '插入失败';
						}
					} else {
						$tip[$tmp['id']] = "错误数据";
					}
				}
				$pageData['tip'] = $tip;
			}
		}
		$pageData['tip'] = $tip;
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/ExchangeImport');

	}

	static public function ARankRecordExport() {
		$header = self::$RankRecordHeader;

		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		$tmp = range('A', 'Z');
		$range = $tmp;
		foreach ($tmp as $val) {
			$range[] = 'A' . $val;
		}
		foreach ($tmp as $val) {
			$range[] = 'B' . $val;
		}

		require_once ADM_PATH . '/lib/PHPExcel.php';

		$rc = new B_Cache_RC(T_Key::RANKINGS_RECORD);
		$list = $rc->zcard();
		$sumRow = min(M_Ranking::CITY_TOTAL_LIMIT, $list);

		$info = $rc->zrevrange(0, $sumRow, true);
		$data1 = array();
		if (!empty($info)) {
			foreach ($info as $key => $value) {
				$cityInfo = M_City::getInfo($key);
				$data1[$key] = array(
					'CityID' => $key,
					'NickName' => $cityInfo['nickname'],
					'Renown' => $cityInfo['renown'],
					'MilMedal' => $cityInfo['mil_medal'],
					'MilRank' => $cityInfo['mil_rank'],
					'Record' => $value
				);
			}
		}
		$i = 1;
		$resData = array();
		if (!empty($data1)) {
			foreach ($data1 as $key => $val) {
				if ($val['UnionId'] > 0) {
					$union = M_Union::getInfo($val['UnionId']);
					unset($val['UnionId']);
					$val['Union'] = $union['name'];
				} else {
					unset($val['UnionId']);
					$val['Union'] = '';
				}
				$resData[$i - 1] = $val;
				$resData[$i - 1]['RANK'] = $i;
				$i++;
			}
		}

		$rows = $resData;
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		$obj = $objPHPExcel->setActiveSheetIndex(0);

		$i = 0;
		foreach ($header as $k => $val) {
			$prefix = $range[$i];
			$obj->setCellValue($prefix . '1', $val);
			$i++;
		}

		$no = 2;
		foreach ($rows as $line) {
			$data = $line;
			$i = 0;
			foreach ($header as $k => $v) {
				$prefix = $range[$i];
				$obj->setCellValue($prefix . $no, $data[$k]);
				$i++;
			}
			$no++;
		}

		// Redirect output to a client’s web browser (Excel5)
		$filename = 'rand_record_' . date('YmdHis') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	static public function ASkillExport() //道具基础信息的导出
	{
		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		$tmp = range('A', 'Z');
		$range = $tmp;
		foreach ($tmp as $val) {
			$range[] = 'A' . $val;
		}
		foreach ($tmp as $val) {
			$range[] = 'B' . $val;
		}
		require_once ADM_PATH . '/lib/PHPExcel.php';

		$rows = B_DB::instance('BaseSkill')->getsBy(array(), array('id' => 'ASC'));

		$objPHPExcel = new PHPExcel();
		$header = self::$SkillHeader;
		$obj = $objPHPExcel->setActiveSheetIndex(0);
		$i = 0;
		foreach ($header as $k => $val) {
			$prefix = $range[$i];
			$obj->setCellValue($prefix . '1', $val);
			$i++;
		}
		$no = 2;
		foreach ($rows as $vals) {

			$vData = $vals;
			if (isset($vals['effect'])) {
				$effect = json_decode($vals['effect'], true);
				$vData['effect'] = '';


				foreach ($effect as $key => $value) {
					switch ($key) {
						case 'INCR_LEA':
							$k1 = 1;
							break;
						case 'INCR_COM':
							$k1 = 2;
							break;
						case 'INCR_MIL':
							$k1 = 3;
							break;
						case 'INCR_VIM':
							$k1 = 4;
							break;
						case 'INCR_AN':
							$k1 = 5;
							break;
						case 'DECR_AN':
							$k1 = 6;
							break;
						case 'INCR_RGE':
							$k1 = 7;
							break;
						case 'INCR_MVE':
							$k1 = 8;
							break;
						case 'INCR_SHT':
							$k1 = 9; //INCR_RGE
							break;
						case 'DECR_RGE':
							$k1 = 10; //INCR_RGE
							break;
						case 'DECR_MVE':
							$k1 = 11;
							break;
						case 'DECR_SHT':
							$k1 = 12;
							break;
						case 'INCR_ATK':
							$k1 = 13;
							break;
						case 'INCR_DEF':
							$k1 = 14;
							break;
						case 'INCR_LIF':
							$k1 = 15;
							break;
						case 'LEA_INCR_ATK':
							$k1 = 16;
							break;
						case 'COM_INCR_ATK':
							$k1 = 17;
							break;
						case 'MIL_INCR_CRIT':
							$k1 = 18;
							break;
						case 'DECR_ARMY_INCR_ATK':
							$k1 = 19;
							break;
						case 'GT_ARMY_INCR_ATK':
							$k1 = 20;
							break;
						case 'RANGE_INCR_ATK':
							$k1 = 21;
							break;
						case 'UNAIM':
							$k1 = 22;
							break;
						case 'UNMOVE':
							$k1 = 23;
							break;
						case 'ATK_HURT':
							$k1 = 24;
							break;
						case 'ATK_HARM':
							$k1 = 25;
							break;
						case 'UNVIEW_AN':
							$k1 = 26;
							break;
						case 'UNVIEW_SELF':
							$k1 = 27;
							break;
						case 'RESTOR_AN':
							$k1 = 28;
							break;
						case 'INCR_MISS':
							$k1 = 29;
							break;
						case 'INCR_CRIT':
							$k1 = 30;
							break;
						case 'ADD_HURT':
							$k1 = 31;
							break;
						case 'DEL_HURT':
							$k1 = 32;
							break;
						case 'UNATK':
							$k1 = 33;
							break;
						case 'LEA_INCR_DEF':
							$k1 = 34;
							break;
						case 'MIL_INCR_LIF':
							$k1 = 35;
							break;
					}
					$vData['effect'] .= $k1 . ':' . $value . '_';
				}
				$vData['effect'] = substr($vData['effect'], 0, strlen($vData['effect']) - 1);

			}


			$i = 0;
			foreach ($header as $k => $line) {
				$prefix = $range[$i];
				$obj->setCellValue($prefix . $no, $vData[$k]);
				$i++;
			}
			$no++;
		}
		$filename = 'base_skill' . date('YmdHis') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	static public function ASkillImport() {
		if (!empty($_POST)) {
			$headerArr = array_keys(self::$SkillHeader);
			$tmp = range('A', 'Z');
			$range = $tmp;
			foreach ($tmp as $val) {
				$range[] = 'A' . $val;
			}
			foreach ($tmp as $val) {
				$range[] = 'B' . $val;
			}
			if (!empty($_FILES['skillcsvfile']['tmp_name'])) {
				require_once ADM_PATH . '/lib/PHPExcel.php';
				$file = $_FILES["skillcsvfile"]['tmp_name'];
				$objReader = new PHPExcel_Reader_Excel5();
				$objReader->setReadDataOnly(true);
				$objPHPExcel = $objReader->load($file);

				$currentSheet = $objPHPExcel->getSheet(0);
				/**取得最大的列号*/
				$allColumn = $currentSheet->getHighestColumn();
				/**取得一共有多少行*/
				$allRow = $currentSheet->getHighestRow();
				/**从第二行开始输出，因为excel表中第一行为列名*/
				$arr = array();
				//$allColumn = count(self::$FBheader);
				for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
					/**从第A列开始输出*/
					$tmp = array();
					$n = 0;
					for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
						$address = $currentColumn . $currentRow;
						$key = $headerArr[$n];
						$v = $currentSheet->getCell($address)->getValue();
						if (!empty($key)) {
							if ($key == 'effect') {
								$array_effect = explode('_', $v); //1:'||||'_2:'||||';'||||'
								foreach ($array_effect as $valu) {
									$effect = explode(':', $valu);
									$a[$effect[0]] = $effect[1];
								}
								foreach ($a as $key1 => $value) {
									switch ($key1) {
										case 1:
											$key2 = 'INCR_LEA';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 2:
											$key2 = 'INCR_COM';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 3:
											$key2 = 'INCR_MIL';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 4:
											$key2 = 'INCR_VIM';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 5:
											$key2 = 'INCR_AN';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 6:
											$key2 = 'DECR_AN';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 7:
											$key2 = 'INCR_RGE';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 8:
											$key2 = 'INCR_MVE';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 9:
											$key2 = 'INCR_SHT';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 10:
											$key2 = 'DECR_RGE';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 11:
											$key2 = 'DECR_MVE';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 12:
											$key2 = 'DECR_SHT';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 13:
											$key2 = 'INCR_ATK';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 14:
											$key2 = 'INCR_DEF';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 15:
											$key2 = 'INCR_LIF';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 16:
											$key2 = 'LEA_INCR_ATK';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 17:
											$key2 = 'COM_INCR_ATK';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 18:
											$key2 = 'MIL_INCR_CRIT';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 19:
											$key2 = 'DECR_ARMY_INCR_ATK';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 20:
											$key2 = 'GT_ARMY_INCR_ATK';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 21:
											$key2 = 'RANGE_INCR_ATK';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 22:
											$key2 = 'UNAIM';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 23:
											$key2 = 'UNMOVE';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 24:
											$key2 = 'ATK_HURT';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 25:
											$key2 = 'ATK_HARM';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 26:
											$key2 = 'UNVIEW_AN';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 27:
											$key2 = 'UNVIEW_SELF';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 28:
											$key2 = 'RESTOR_AN';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 29:
											$key2 = 'INCR_MISS';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 30:
											$key2 = 'INCR_CRIT';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 31:
											$key2 = 'ADD_HURT';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 32:
											$key2 = 'DEL_HURT';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 33:
											$key2 = 'UNATK';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 34:
											$key2 = 'LEA_INCR_DEF';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
										case 35:
											$key2 = 'MIL_INCR_LIF';
											unset($a[$key1]);
											$a[$key2] = $value;
											break;
									}
								}
								$val = json_encode($a);
								$a = array();
							} else {
								$val = $v;
							}
							$tmp[$key] = trim($val);
						}
						$n++;
					}
					if (empty($tmp['id'])) {
						echo 'ID不能为空';
						exit;
					}

					if (!empty($err)) {
						print_r($err);
						exit;
					}

					if (!empty($tmp['name']) && !empty($tmp['id'])) {
						if (B_DB::instance('BaseSkill')->get($tmp['id'])) {
							$ret = B_DB::instance('BaseSkill')->update($tmp,$tmp['id']);
							$tip[$tmp['id']] = $ret ? '更新成功' : '更新失败';
						} else {
							$ret = B_DB::instance('BaseSkill')->insert($tmp);
							$tip[$tmp['id']] = $ret ? '插入成功' : '插入失败';
						}

					} else {
						$tip[$tmp['id']] = "错误数据";
					}

				}

				$pageData['tip'] = $tip;
			}
		}
		$pageData['act'] = 'SkillImport';
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/SkillImport');
	}

	static public function ASkillCacheUp() {
		$ret = M_Base::skillAll(true);
		echo "<script>";
		echo $ret ? "alert('更新成功');" : "alert('更新失败');";
		echo "</script>";
	}

	static public function AQqShareCacheUp() {
		$ret = M_Base::qqshareAll(true);
		echo "<script>";
		echo $ret ? "alert('更新成功');" : "alert('更新失败');";
		echo "</script>";
	}

	static public function AQqShareList() {
		if (isset($_GET['db'])) {
			$list = B_DB::instance('BaseQqShare')->all();
		} else {
			$list = M_Base::qqshareAll();
		}
		$pageData['list'] = B_DB::instance('BaseQqShare')->all();
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/QqShareList');
	}

	static public function AQqShareExport() {
		$header = self::$qqShareHeaderArr;

		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		$tmp = range('A', 'Z');
		$range = $tmp;
		foreach ($tmp as $val) {
			$range[] = 'A' . $val;
		}
		foreach ($tmp as $val) {
			$range[] = 'B' . $val;
		}

		require_once ADM_PATH . '/lib/PHPExcel.php';

		$rows = B_DB::instance('BaseQqShare')->all();

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		$obj = $objPHPExcel->setActiveSheetIndex(0);

		$i = 0;
		foreach ($header as $k => $val) {
			$prefix = $range[$i];
			$obj->setCellValue($prefix . '1', $val);
			$i++;
		}

		$no = 2;
		foreach ($rows as $line) {
			$data = $line;

			$i = 0;
			foreach ($header as $k => $v) {
				$prefix = $range[$i];
				$obj->setCellValue($prefix . $no, $data[$k]);
				$i++;
			}
			$no++;
		}

		// Redirect output to a client’s web browser (Excel5)
		$filename = 'base_qq_share_' . date('YmdHis') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	static public function AQqShareImport() {
		$tip = array();
		if (!empty($_POST)) {
			$baseList = B_DB::instance('BaseQqShare')->all();
			$headerArr = array_keys(self::$qqShareHeaderArr);
			if (!empty($_FILES['csvfile']['tmp_name'])) {
				require_once ADM_PATH . '/lib/PHPExcel.php';
				$file = $_FILES["csvfile"]['tmp_name'];

				$objReader = new PHPExcel_Reader_Excel5();
				$objReader->setReadDataOnly(true);
				$objPHPExcel = $objReader->load($file);

				$currentSheet = $objPHPExcel->getSheet(0);
				/**取得最大的列号*/
				$allColumn = $currentSheet->getHighestColumn();
				/**取得一共有多少行*/
				$allRow = $currentSheet->getHighestRow();
				/**从第二行开始输出，因为excel表中第一行为列名*/
				$arr = array();
				for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
					/**从第A列开始输出*/
					$tmp = array();
					$n = 0;
					for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
						$address = $currentColumn . $currentRow;
						$key = $headerArr[$n];
						$v = $currentSheet->getCell($address)->getValue();


						$tmp[$key] = trim($v);
						$n++;
					}
					if (!empty($tmp['name']) && !empty($tmp['id'])) {
						$del = $tmp['del'];
						unset($tmp['del']);
						if (!empty($baseList[$tmp['id']])) {
							if ($del) {
								$ret = B_DB::instance('BaseQqShare')->delete($tmp['id']);
								$tip[$tmp['id']] = $ret ? '删除成功' : '删除失败';
							} else {
								$ret = B_DB::instance('BaseQqShare')->update($tmp, $tmp['id']);
								$tip[$tmp['id']] = $ret ? '更新成功' : '更新失败';
							}
						} else {
							$ret = B_DB::instance('BaseQqShare')->insert($tmp);
							$tip[$tmp['id']] = $ret ? '插入成功' : '插入失败';
						}
					} else {
						$tip[$tmp['id']] = "错误数据";
					}
					//$arr[] = $tmp;
				}

				$pageData['tip'] = $tip;
			}
		}
		$pageData['tip'] = $tip;
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/QqShareImport');
	}


	static public function AQuestionExport() {
		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		$tmp = range('A', 'Z');
		$range = $tmp;
		foreach ($tmp as $val) {
			$range[] = 'A' . $val;
		}
		foreach ($tmp as $val) {
			$range[] = 'B' . $val;
		}

		require_once ADM_PATH . '/lib/PHPExcel.php';

		$rows = B_DB::instance('BaseQuestion')->getsBy(array(), array('id' => 'ASC'));

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		$header = self::$QuestionHeader;
		$obj = $objPHPExcel->setActiveSheetIndex(0);
		$i = 0;
		foreach ($header as $k => $val) {
			$prefix = $range[$i];
			$obj->setCellValue($prefix . '1', $val);
			$i++;
		}

		$no = 2;
		foreach ($rows as $vals) {
			$vData = $vals;
			$tmp = array();
			$i = 0;
			foreach ($header as $k => $line) {
				$prefix = $range[$i];
				$obj->setCellValue($prefix . $no, $vData[$k]);
				$i++;
			}
			$no++;
		}


		// Redirect output to a client’s web browser (Excel5)
		$filename = 'base_question_' . date('YmdHis') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	static public function AQuestionImport() {
		if (!empty($_POST)) {
			$headerArr = array_keys(self::$QuestionHeader);
			if (!empty($_FILES['csvfile']['tmp_name'])) {

				require_once ADM_PATH . '/lib/PHPExcel.php';
				$file = $_FILES["csvfile"]['tmp_name'];

				$objReader = new PHPExcel_Reader_Excel5();
				$objReader->setReadDataOnly(true);
				$objPHPExcel = $objReader->load($file);

				$currentSheet = $objPHPExcel->getSheet(0);
				/**取得最大的列号*/
				$allColumn = $currentSheet->getHighestColumn();
				/**取得一共有多少行*/
				$allRow = $currentSheet->getHighestRow();
				/**从第二行开始输出，因为excel表中第一行为列名*/
				$arr = array();
				for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
					/**从第A列开始输出*/
					$tmp = array();
					$n = 0;
					for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
						$address = $currentColumn . $currentRow;
						$key = $headerArr[$n];
						$v = $currentSheet->getCell($address)->getValue();
						$tmp[$key] = trim($v);
						$n++;
					}

					if (empty($tmp['id'])) {
						echo 'ID不能为空';
						var_dump($tmp);
						exit;
					}

					if (B_DB::instance('BaseQuestion')->get($tmp['id'])) {
						$ret = B_DB::instance('BaseQuestion')->update($tmp, $tmp['id']);
						$tip[$tmp['id']] = $ret ? '更新成功' : '更新失败';
					} else {
						$ret = B_DB::instance('BaseQuestion')->insert($tmp);
						$tip[$tmp['id']] = $ret ? '插入成功' : '插入失败';
					}
					//$arr[] = $tmp;
				}

				$pageData['tip'] = $tip;

			}

		}
		$pageData['act'] = 'ProbeImport';
		B_View::setVal('pageData', $pageData);
		B_View::render('Base/QuestionImport');
	}

}

?>
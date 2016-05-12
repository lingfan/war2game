<?php
return array(
	'System' => array(
		'name' => '系统管理',
		'sub' => array(
			'ConfigServer' => '服务器配置',
			'ConfigPayAward' => '充值奖励',
			'ConfigInviteFriend' => '好友邀请',
			'ConfigOnline' => '在线奖励配置',
			'ConfigActive' => '系统活动配置',
			'ConfigTmpNpc' => '随机坐标临时NPC配置',
			'ConfigFascist' => '固定坐标临时NPC配置',

			'ConfigQQ' => 'QQ平台配置',
			'ConfigBase' => '基础配置',
			'ConfigHero' => '英雄配置',
			'ConfigEquip' => '装备配置',
			'ConfigWeapon' => '武器配置',
			'ConfigUnion' => '军团配置',
			'ConfigHorse' => '跑马配置',
			'ConfigVipNew' => 'VIP配置',
			'ConfigDrama' => '剧情任务奖励配置',
			'DefaultProbeMap' => '野外NPC学院配置',
			'ConfigQuestion' => '答题配置',
			'ConfigFloor' => '爬楼配置',
			'ConfigBuild' => '建筑配置',
			'ConfigLottery' => '抽奖配置',
			'ConfigLiveness' => '积分兑换配置',
			'ConfigEvent' => '活动图标配置',
		),
	),


	'Base' => array(
		'name' => '基础数据',
		'sub' => array(
			'ConsumerList' => '运营商列表',
			'BuildBaseList' => '建筑管理',
			'TechBaseList' => '科技管理',
			'ArmyList' => '兵种管理',
			'WeaponList' => '武器管理',
			'PropsList' => '道具管理',
			'TaskList' => '任务管理',
			'HeroList' => '英雄列表',
			'EquipList' => '装备列表',
			'SkillList' => '技能列表',
			'ProbeList' => '事件列表',
			'CampaignList' => '据点管理',
			'BoutList' => '突围管理',
			'AwardList' => '奖励管理',
			'MallList' => '商城管理',
			'QuestList' => '指引管理',
			'MultiFBList' => '多人副本',
			'ExchangeList' => '兑换管理',
			'QqShareList' => 'QQ分享管理',
			'QuestionImport' => '答题管理',
		),
	),

	'War' => array(
		'name' => '副本管理',
		'sub' => array(
			'WarFbCateList' => '战斗副本',
			'NpcList' => 'NPC部队列表',
			'NpcHeroList' => 'NPC英雄列表',
		),
	),

	'Map' => array(
		'name' => '地图编辑器',
		'sub' => array(
			'BuildEditor' => '城内建筑编辑器',
			'CityInMapEdiotr' => '城内地图编辑',
			'WarMapEditor' => '战场地图编辑器',
			'WarMapCellList' => '战场地图标记列表',
			'WarMapSecneList' => '战场装饰物列表',
			'WorldMapSecneList' => '城外装饰物列表',
			'WarMapImport' => '战场地图导入',
		),
	),

	'Manger' => array(
		'name' => '管理员管理',
		'sub' => array(
			'List' => '管理员列表',
			'UserGroup' => '权限组列表',
			'GMUser' => 'GM列表',
			'DebugIp' => '调试IP列表',
			'DelPayRow' => '清除充值记录',
			'RestoreMerge' => '合服重新迁移',
		),
	),

	'Server' => array(
		'name' => '服务器信息',
		'sub' => array(
			'Apc' => 'Apc信息',
			'Php' => 'Php信息',
			'Cron' => '定时任务脚本信息',
			'Deamon' => '守护进程脚本信息',
			'Mysql' => '数据库信息',
			'Sys' => '系统进程信息',
			'UploadCode' => '更新代码',
			'ReqNum' => '接口请求统计',
		),
	),

	'Cache' => array(
		'name' => '缓存信息',
		'sub' => array(
			'Index' => '信息概况',
			'FileBin' => 'Bin配置文件',
		),
	),
);
?>
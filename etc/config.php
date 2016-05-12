<?php
/** 数据库配置 **/
$config['gamedb'] = array(
	'hostname' => 'localhost',
	'database' => 's1_ww2',
	'username' => 's1_user',
	'password' => '',
	'port' => '3306',
);

/** 统计数据库配置 **/
$config['statsdb'] = array(
	'hostname' => 'localhost',
	'database' => 's1_ww2_stats',
	'username' => 's1_user',
	'password' => '',
	'port' => '3306',
);

/** 统计数据库配置 **/
$config['basedb'] = array(
	'hostname' => 'localhost',
	'database' => 's1_ww2_base',
	'username' => 'base_user',
	'password' => '',
	'port' => '3306',
);

/** 备份数据库配置 **/
$config['backup'] = array(
	'hostname' => '127.0.0.1',
	'username' => 'root',
	'password' => '',
	'port' => '3328',
);

/** redis配置 **/
$config['redis'] = array(
	'hostname' => array('127.0.0.1:6379'),
	'db' => 1,
);


return $config;
?>
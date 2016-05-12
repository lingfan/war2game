#!/usr/bin/env php
<?php
//初始化地图
$commonFile = dirname(dirname(__FILE__)) . '/common.php';
include($commonFile);

$tables = B_DB::instance('AdmUser')->fetchAll('SHOW TABLES');

$dbh = B_DB_Pdo::$dbh;

$filterTableArr = array('server_config', 'wild_map');
$tableArr = array();
foreach ($tables as $table) {
	$t = array_values($table);
	if (!in_array($t[0], $filterTableArr)) {
		$tableArr[] = $t[0];
	}
}

foreach ($tableArr as $val) {
	$ret[$val] = $dbh->exec("TRUNCATE TABLE `{$val}`");
}

$ret['CityHeroIncr'] = $dbh->exec('ALTER TABLE `city` AUTO_INCREMENT=100000');
$ret['CityHeroIncr'] = $dbh->exec('ALTER TABLE `city_hero` AUTO_INCREMENT=100000');
$ret['CityEquipIncr'] = $dbh->exec('ALTER TABLE `city_equip` AUTO_INCREMENT=100000');
$ret['UserIncr'] = $dbh->exec('ALTER TABLE `user` AUTO_INCREMENT=100000');
$ret['MarchIncr'] = $dbh->exec('ALTER TABLE `war_march` AUTO_INCREMENT=100000');

$ret['WildMapDelCity'] = $dbh->exec('DELETE FROM `wild_map` WHERE `type` = 2');
$ret['WildMapReset'] = $dbh->exec('UPDATE `wild_map` SET `city_id`=0,`hold_expire_time`=0, `last_fill_army_time`=0 WHERE `type` = 3');

$ret['stats_log_pay'] = B_DBStats::getStatsDB()->exec("TRUNCATE TABLE `stats_log_pay`");
$ret['stats_log_income'] = B_DBStats::getStatsDB()->exec("TRUNCATE TABLE `stats_log_income`");
$ret['stats_log_expense'] = B_DBStats::getStatsDB()->exec("TRUNCATE TABLE `stats_log_expense`");

$config = B_Cache_File::get('redis');
foreach ($config['hostname'] as $n => $v) {
	$rc = B_Cache_RC::conn($config, $n);
	$ret['CleanRedis'] = $rc->flushDB();
}


echo "\n";
print_r($ret);

$end = microtime(true);
echo $end - $start . "\n\n";
?>

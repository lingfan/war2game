<?php

/**
 * 定时任务模块
 */
class M_Cron {
	/**
	 * 复活英雄时间到期
	 * @author huwei
	 */
	static public function heroRelife() {

	}

	/**
	 * 尝试寻将时间到期
	 * @author huwei
	 */
	static public function findProc() {

	}

	/**
	 * 属地占领时间到期
	 */
	static public function expireColony() {

	}

	/**
	 * 更新威望排行缓存
	 * @author HeJunyun
	 */
	static public function syncRankingsByRenown() {
		$data    = B_DB::instance('User')->getUserRankingsByRenown();
		$i       = 1;
		$resData = array();

		foreach ($data as $key => $val) {
			if ($val['UnionId'] > 0) {
				$union = M_Union::getInfo($val['UnionId']);
				unset($val['UnionId']);
				$val['Union'] = $union['name'];
			} else {
				unset($val['UnionId']);
				$val['Union'] = '';
			}
			$resData[$val['ID']]         = $val;
			$resData[$val['ID']]['RANK'] = $i;
			$i++;
		}

		$rc = new B_Cache_RC(T_Key::RANKINGS_RENOWN);
		$rc->jsonset($resData);
		return $resData;
	}

	/**
	 * 更新军功排行缓存
	 * @author HeJunyun
	 */
	static public function syncRankingsByMilMedal() {
		$data    = B_DB::instance('User')->getUserRankingsByMilMedal();
		$i       = 1;
		$resData = array();
		foreach ($data as $key => $val) {
			if ($val['UnionId'] > 0) {
				$union = M_Union::getInfo($val['UnionId']);
				unset($val['UnionId']);
				$val['Union'] = $union['name'];
			} else {
				unset($val['UnionId']);
				$val['Union'] = '';
			}
			$resData[$val['ID']]         = $val;
			$resData[$val['ID']]['RANK'] = $i;
			$i++;
		}

		$rc = new B_Cache_RC(T_Key::RANKINGS_MILMEDAL);
		$rc->jsonset($resData);
		return $resData;
	}

	/**
	 * 更新军官排行缓存（默认等级）
	 * @author HeJunyun
	 */
	static public function syncRankingsByHero() {
		$data = B_DB::instance('CityHero')->getHeroRankings();
		foreach ($data as $key => $val) {
			$cityInfo             = M_City::getInfo($val['CityId']);
			$data[$key]['CityId'] = $cityInfo['nickname'];
		}
		$rc = new B_Cache_RC(T_Key::RANKINGS_HERO_LEVEL);
		$rc->jsonset($data);
		return $data;
	}

	/**
	 * 更新军官排行缓存（统帅）
	 * @author HeJunyun
	 */
	static public function syncRankingsByHeroLead() {
		$data = B_DB::instance('CityHero')->getHeroRankingsByLead();
		foreach ($data as $key => $val) {
			$cityInfo             = M_City::getInfo($val['CityId']);
			$data[$key]['CityId'] = $cityInfo['nickname'];
		}
		$rc = new B_Cache_RC(T_Key::RANKINGS_HERO_LEAD);
		$rc->jsonset($data);
		return $data;
	}

	/**
	 * 更新军官排行缓存（指挥）
	 * @author HeJunyun
	 */
	static public function syncRankingsByHeroCommand() {
		$data = B_DB::instance('CityHero')->getHeroRankingsByCommand();
		foreach ($data as $key => $val) {
			$cityInfo             = M_City::getInfo($val['CityId']);
			$data[$key]['CityId'] = $cityInfo['nickname'];
		}
		$rc = new B_Cache_RC(T_Key::RANKINGS_HERO_COMMAND);
		$rc->jsonset($data);
		return $data;
	}

	/**
	 * 更新军官排行缓存（军事）
	 * @author HeJunyun
	 */
	static public function syncRankingsByHeroMilitary() {
		$data = B_DB::instance('CityHero')->getHeroRankingsByMilitary();
		foreach ($data as $key => $val) {
			$cityInfo             = M_City::getInfo($val['CityId']);
			$data[$key]['CityId'] = $cityInfo['nickname'];
		}
		$rc = new B_Cache_RC(T_Key::RANKINGS_HERO_MILITARY);
		$rc->jsonset($data);
		return $data;
	}

	/**
	 * 更新军官排行缓存（胜利）
	 * @author HeJunyun
	 */
	static public function syncRankingsByHeroWin() {
		$data = B_DB::instance('CityHero')->getHeroRankingsByWin();
		foreach ($data as $key => $val) {
			$cityInfo             = M_City::getInfo($val['CityId']);
			$data[$key]['CityId'] = $cityInfo['nickname'];
		}
		$rc = new B_Cache_RC(T_Key::RANKINGS_HERO_WIN);
		$rc->jsonset($data);
		return $data;
	}


	/**
	 * 同步城市中心升级日志到数据库
	 * @author Hejunyun
	 */
	static public function syncCityLevel() {
		$day  = date("Ymd", strtotime("-1 day"));
		$rc   = new B_Cache_RC(T_Key::STATS_USER_CITY_UPLEVEL, $day);
		$data = $rc->hgetall();
		if ($data) {
			$setArr = array(
				'add_day'    => $day,
				'build_data' => json_encode($data)
			);
			$ret    = B_DBStats::insert('stats_log_city_build', $setArr);
			$ret && $rc->delete();
			//return $ret;
		}
		return true;
	}

	static public function syncUnionDB() {
		$ret = M_Union::syncUnionListRank();
		return $ret;
	}


	/**
	 * 删除文件
	 * @param  $dirName
	 */
	static public function removeDir($dirName) {
		if (!is_dir($dirName)) //判断指定目录是存在
		{
			@unlink($dirName);
			return false;
		}
		$handle = @opendir($dirName); //打开目录
		while (($file = @readdir($handle)) !== false) {
			if ($file != '.' && $file != '..') //列出目录中的所有文件并去掉 . 和 ..
			{
				$dir = $dirName . '/' . $file;
				is_dir($dir) ? removeDir($dir) : @unlink($dir); //删除指定目录中的文件
			}
		}
		closedir($handle); //关闭由opendir()打开的目录
		return rmdir($dirName); //rmdir()删除空目录
	}

	/**
	 * 数据库备份
	 * @author huwei
	 * @param int $serverid
	 * @param array $dbConfig array('username', 'password', 'port', 'ip')
	 */
	static public function dbbackup($serverid) {
		if ($serverid) {
			//设置拍卖行数据过期
			$nowtime = time();
			//导出数据库
			$db_name        = "{$serverid}_ww2";
			$db_name_stats  = "{$serverid}_ww2_stats";
			$db_table_stats = "stats_log_pay";

			$tables = B_DB::instance('AdmUser')->fetchAll('SHOW TABLES');

			$filterTableArr = array('wild_map', 'war_report', 'war_march', 'message');
			$tableArr       = array();
			foreach ($tables as $table) {
				if (!in_array($table[0], $filterTableArr)) {
					$tableArr[] = $table[0];
				}
			}
			$db_table = implode(" ", $tableArr);

			$dbConfig = B_Cache_File::get('backup');
			$date     = date('Ymd');
			if (!empty($dbConfig['username'])) {
				$db_user  = $dbConfig['username'];
				$password = $dbConfig['password'];
				$db_port  = $dbConfig['port'];
				$db_ip    = $dbConfig['hostname'];

				$bakfile    = "{$backup_dir}/{$newdbname}_{$db_name}.sql";
				$bakfilepay = "{$backup_dir}/{$newdbname}_{$db_name_stats}.sql";

				$newdbname  = "{$serverid}_{$date}";
				$backup_dir = "/data/mysql";
				$MYSQLDUMP  = "/opt/mysql/bin/mysqldump -u{$db_user} -p{$password}";
				$MYSQL      = "/opt/mysql/bin/mysql -u{$db_user} -p{$password} -P{$db_port} -h{$db_ip} ";
				mkdir($backup_dir, 0777, true);
				shell_exec("{$MYSQLDUMP} {$db_name} {$db_table}  > '{$bakfile}' ");
				shell_exec("{$MYSQLDUMP} {$db_name_stats} {$db_table_stats} > '{$bakfilepay}' ");

				shell_exec("{$MYSQL} -e 'drop database {$newdbname}' ");
				shell_exec("{$MYSQL} -e 'create database {$newdbname}' ");

				shell_exec("{$MYSQL} {$newdbname} < '{$bakfile}' ");
				shell_exec("{$MYSQL} {$newdbname} < '{$bakfilepay}' ");
			} else {
				$dbConf             = B_Cache_File::get('gamedb');
				$server             = B_Cache_File::get('server');
				$serverId           = B_Cache_File::server(SERVER_NO);
				$dbConf['database'] = 's' . $serverId . '_' . $dbConf['database'];
				$dbConf['username'] = 's' . $serverId . '_' . $dbConf['username'];

				$backup_dir = "/opt/ww2/backup/";
				$MYSQLDUMP  = "/opt/mysql/bin/mysqldump -u{$dbConf['username']} -p{$dbConf['password']}";
				mkdir($backup_dir, 0777, true);
				$newdbname = "{$serverid}_{$date}";
				shell_exec("{$MYSQLDUMP} {$db_name} {$db_table} | gzip -9 > '{$backup_dir}/{$newdbname}_{$db_name}.gz' ");
				shell_exec("{$MYSQLDUMP} {$db_name_stats} {$db_table_stats} | gzip -9 > '{$backup_dir}/{$newdbname}_{$db_name_stats}.gz' ");
			}
		}
	}

	static public function cleanReportLog() {
		//清理三个月前的战报日志
		$t        = mktime(0, 0, 0, date('m') - 3);
		$serverId = B_Cache_File::server(SERVER_NO);
		$dir      = RPT_PATH . '/' . $serverId . '/' . date('Ym', $t);
		if (file_exists($dir)) {
			$ret = self::_rrmdir($dir);
			if ($ret) {
				Logger::cron('report', 'rmdir_' . $dir);
			}
		}
		$num = B_DB::instance('WarReport')->cleanExpireData($t);
		Logger::cron('report', 'rmdb_' . $num);
	}

	static private function _rrmdir($dir) {
		foreach (glob($dir . '/*') as $file) {
			if (is_dir($file))
				self::_rrmdir($file);
			else
				unlink($file);
		}
		return rmdir($dir);
	}
}

?>
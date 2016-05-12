<?php

/**
 *  数据库类库
 */
class B_DBStats {
	/**
	 * 获取统计的数据库写链接
	 */
	static public function getStatsDB() {
		$db = B_Cache_File::get('statsdb');
		$dbh = self::_connect($db);
		return $dbh;
	}

	/**
	 * 连接数据库
	 */
	private static function _connect($dbConf) {
		try {
			$option = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'");
			$dbh = new PDO("mysql:host={$dbConf['hostname']};port={$dbConf['port']};dbname={$dbConf['database']}", $dbConf['username'], $dbConf['password'], $option);
			$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			$msg = 'DB Connection failed: ' . $e->getMessage();
			trigger_error($msg);
			Logger::halt('Err_STATSDB');
		}
		return $dbh;
	}

	/**
	 * 更新数据通过主键
	 * @param string $table 表名
	 * @param array $fields 更新的字段数组(必须包含主键ID)
	 * @return bool
	 */
	static public function updateByPk($table, $fields) {
		if (!is_array($fields) || !isset($fields['id']) || count($fields) < 1) {
			return false;
		}
		$id = $fields['id'];
		unset($fields['id']);
		$set = array();
		foreach ($fields as $key => $val) {
			$set[] = "`{$key}`=:{$key}";
		}
		$setCond = implode(',', $set);
		$sql = "UPDATE `{$table}` SET {$setCond} WHERE id = :id";
		$sth = self::getStatsDB()->prepare($sql);
		foreach ($fields as $key_sc => $val_sc) {
			$sth->bindValue(':' . $key_sc, $val_sc);
		}
		$sth->bindValue(':id', $id);
		$ret = $sth->execute();
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		return true;
	}


	/**
	 * 更新操作
	 * @author huwei
	 * @param string $table 表名
	 * @param array $setArr 要更新的键值对数组
	 * @param array $whereArr 要更新的条件对数组
	 * @param string $limit 限制更新的数量
	 * @return bool true/false
	 */
	static public function update($table, $setArr, $whereArr, $limit = ' LIMIT 1 ') {

		if (empty($table) || !is_array($setArr) || count($setArr) <= 0 || !is_array($whereArr) || count($whereArr) <= 0) {
			return false;
		}

		$sc = array();
		foreach ($setArr as $keys => $vals) {
			$sc[] = "`{$keys}`=:{$keys}";
		}
		$setCond = implode(',', $sc);

		$wc = array();
		foreach ($whereArr as $keyw => $valw) {
			$wc[] = "`{$keyw}`=:{$keyw}";
		}
		$whereCond = implode(' AND ', $wc);

		$sql = "UPDATE `$table` SET $setCond WHERE $whereCond $limit ";

		$sth = self::getStatsDB()->prepare($sql);
		foreach ($setArr as $key_sc => $val_sc) {
			$sth->bindValue(':' . $key_sc, $val_sc);
		}

		foreach ($whereArr as $key_wc => $val_wc) {
			$sth->bindValue(':' . $key_wc, $val_wc);
		}

		$ret = $sth->execute();
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		return true;
	}

	/**
	 * 插入操作
	 * @author huwei
	 * @param string $table 表名
	 * @param array $set 要插入的键值对数组
	 * @return bool/int
	 */
	static public function insert($table, $setArr) {
		$condition = array();

		foreach ($setArr as $key => $val) {
			$condition[] = "`{$key}`=:{$key}";
		}

		$setStr = implode(',', $condition);
		$sql = "INSERT INTO `{$table}` SET {$setStr} ";
		$dbh = B_DBStats::getStatsDB();
		$sth = $dbh->prepare($sql);
		foreach ($setArr as $k => $v) {
			$sth->bindValue(':' . $k, $v);
		}

		$ret = $sth->execute();
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		$id = $dbh->lastInsertId();
		return $id;
	}

	/**
	 * 通过条件查找信息
	 * @author huwei
	 * @param string $table 表名
	 * @param int $whereArr 条件数组
	 * @param string fields 字段列表默认全部*
	 * @return array/bool
	 */
	static public function getRow($table, $whereArr, $fields = '*') {
		$condition = array();

		foreach ($whereArr as $key => $val) {
			$condition[] = "{$key}=:{$key}";
		}
		$whereStr = implode(' AND ', $condition);

		$fields = empty($fields) ? ' * ' : $fields;

		$sql = "SELECT {$fields} FROM {$table} WHERE {$whereStr} ";

		$sth = self::getStatsDB()->prepare($sql);

		foreach ($whereArr as $k => $v) {
			$sth->bindValue(':' . $k, $v);
		}

		$ret = $sth->execute();

		$row = $sth->fetch(PDO::FETCH_ASSOC);
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		return $row;
	}

	static public function totalOnline($table, $start, $end, $fields = '*') {
		$sql = "SELECT {$fields} FROM {$table} WHERE day >= :start AND day <= :end  group by day ;";
		$sth = self::getStatsDB()->prepare($sql);
		$sth->bindValue(':start', $start);
		$sth->bindValue(':end', $end);
		$ret = $sth->execute();
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		$rows = $sth->fetchAll();
		return $rows;
	}

	/**
	 * 通过主键查找信息
	 * @author huwei
	 * @param string $table 表名
	 * @param int $id 主键值
	 * @return array/bool
	 */
	static public function findByPk($table, $id) {
		$sql = "SELECT * FROM {$table} WHERE id = :id LIMIT 1 ";
		$sth = self::getStatsDB()->prepare($sql);
		$sth->bindValue(':id', $id);
		$ret = $sth->execute();
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		return $row;
	}


	/**
	 * 删除记录通过主键
	 * @author huwei
	 * @param string $table 表名
	 * @param array $whereArr 条件数组
	 * @return array/bool
	 */
	static public function deleteByPk($table, $id) {
		$sql = "DELETE FROM {$table} WHERE id=:id LIMIT 1";
		$sth = self::getStatsDB()->prepare($sql);
		$sth->bindValue(':id', $id);
		$ret = $sth->execute();
		$row = $sth->rowCount();
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		return $row > 0 ? true : false;
	}

	/**
	 * 删除记录
	 * @author huwei
	 * @param string $table 表名
	 * @param array $whereArr 条件数组
	 * @return array/bool
	 */
	static public function delete($table, $whereArr) {
		$condition = array();

		foreach ($whereArr as $key => $val) {
			$condition[] = "{$key}=:{$key}";
		}
		$whereStr = implode(' AND ', $condition);
		$sql = "DELETE FROM {$table} WHERE {$whereStr}";
		$sth = self::getStatsDB()->prepare($sql);
		foreach ($whereArr as $k => $v) {
			$sth->bindValue(':' . $k, $v);
		}
		$ret = $sth->execute();
		$row = $sth->rowCount();
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		return $row > 0 ? true : false;
	}

	/**
	 * 获取分页记录
	 * @author HeJunyun
	 * @param string $table 表名
	 * @param int $curPage 当前页
	 * @param int $offset 页大小
	 * @param array $parms 参数数组
	 * @return array $rows  for admin
	 */
	static public function getPageData($table, $curPage, $offset, $parms = '') {
		$whereArr = array();
		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				$whereArr[] = "{$key}=:{$key}";
			}
		}
		$where = !empty($whereArr) ? ' WHERE ' . implode(' AND ', $whereArr) : '';
		$start = ($curPage - 1) * $offset;
		$sql = "SELECT * FROM {$table} {$where} ORDER BY id  LIMIT {$start}, {$offset}";
		$sth = self::getStatsDB()->prepare($sql);
		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				$sth->bindValue(':' . $key, $val);
			}
		}
		$ret = $sth->execute();
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		$rows = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}

	/**
	 * @author HeJunyun
	 * @param string $table 表名
	 * @param int $curPage 当前页
	 * @param int $offset 页大小
	 * @param array $parms 参数数组
	 * @param string $sidx 排序字段
	 * @param string $sord 排序类型（desc/asc）
	 * @return array $rows  for api
	 */
	static public function apiPageData($table, $selected = '*', $curPage, $offset, $parms = '', $sidx = 'id', $sord = 'DESC') {
		if (empty($table)) {
			return false;
		}
		$selected = $selected ? $selected : '*';
		$sidx = $sidx ? $sidx : 'id';
		$sord = $sord ? $sord : 'DESC';
		$whereArr = array();
		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				if ($key == 'create_start') {
					$whereArr[] = "`create_at`>=:{$key}";
				} elseif ($key == 'create_end') {
					$whereArr[] = "`create_at`<=:{$key}";
				} else {
					$whereArr[] = "`{$key}`=:{$key}";
				}

			}
		}
		$where = !empty($whereArr) ? ' WHERE ' . implode(' AND ', $whereArr) : '';
		$start = ($curPage - 1) * $offset;
		$sql = "SELECT {$selected} FROM `{$table}` {$where} ORDER BY `{$sidx}` {$sord}  LIMIT {$start}, {$offset}";
		$sth = self::getStatsDB()->prepare($sql);
		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				$sth->bindValue(':' . $key, $val);
			}
		}
		$ret = $sth->execute();
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		$rows = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}


	/**
	 * 获取行数
	 * @author HeJunyun
	 * @param string $table 表名
	 * @param array $parms 参数数组
	 */
	static public function totalRows($table, $parms = '') {
		$whereArr = array();
		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				if ($key == 'create_start') {
					$whereArr[] = "`create_at`>=:{$key}";
				} elseif ($key == 'create_end') {
					$whereArr[] = "`create_at`<=:{$key}";
				} else {
					$whereArr[] = "`{$key}`=:{$key}";
				}
			}
		}
		$where = !empty($whereArr) ? ' WHERE ' . implode(' AND ', $whereArr) : '';
		$sql = "SELECT count(id) as num FROM `{$table}` {$where}";
		$sth = self::getStatsDB()->prepare($sql);
		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				$sth->bindValue(':' . $key, $val);
			}
		}
		$ret = $sth->execute();
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		return $row['num'];
	}

	/**
	 * 获取某表一个某列的总数和
	 * @author Hejunyun
	 * @param string $table
	 * @param string $columns
	 * @param array $parms
	 */
	static public function getColumnsSum($table, $columns, $parms) {
		$whereArr = array();
		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				if ($key == 'create_start') {
					$whereArr[] = "`create_at`>=:{$key}";
				} elseif ($key == 'create_end') {
					$whereArr[] = "`create_at`<=:{$key}";
				} else {
					if (is_array($val) && !empty($val)) {
						$val1 = implode(',', $val);
						$val2 = '(' . $val1 . ')';
						$whereArr[] = "`{$key}` in $val2";
					} else {
						$whereArr[] = "`{$key}`=:{$key}";
					}
				}
			}
		}
		$where = !empty($whereArr) ? ' WHERE ' . implode(' AND ', $whereArr) : '';
		$sql = "SELECT SUM(`{$columns}`) as num FROM `{$table}` {$where}";
		$sth = self::getStatsDB()->prepare($sql);

		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				if (is_array($val) && !empty($val)) {

				} else {
					$sth->bindValue(':' . $key, $val);
				}
			}
		}
		$ret = $sth->execute();
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		return $row['num'];
	}

	/**
	 * 根据条件获取流失用户列表
	 * @author duhuihui on 20120827
	 * @param array $parms 其它参数
	 * @return false/array
	 */
	static public function getOutflowUserInfo($parms = '') {
		$whereArr = array();
		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				if ($key == 'num') {
					$whereArr[] = "DATEDIFF(" . date('Ymd') . ",from_unixtime(`last_visit_time`) )>{$val}";
				} elseif ($key == 'create_start') {
					if ($val) {
						$whereArr[] = "`create_at`>='" . strtotime($val) . "'";
					}
				} elseif ($key == 'create_end') {
					if ($val) {
						$whereArr[] = "`create_at`<='" . strtotime($val) . "'";
					}
				}

			}
		}
		$where = !empty($whereArr) ? ' WHERE ' . implode(' AND ', $whereArr) : '';
		$sql = "SELECT count(*) as num FROM `user` {$where} ORDER BY `last_visit_time` DESC ";

		$rows = $this->fetchAll($sql);
		return $rows;
	}

	/**
	 * 根据条件获取周活跃和月活跃用户列表
	 * @author duhuihui on 20120827
	 * @param array $parms 其它参数
	 * @return false/array
	 */
	static public function getActiveUserInfo($parms = '') {
		$whereArr = array();
		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {

				if ($key == 'num') {
					$whereArr[] = "DATEDIFF(:day,`day` )<={$val}";
				} elseif ($key == 'create_start') {
					if ($val) {
						$whereArr[] = "`day`>=:{$key}";
					}
				} elseif ($key == 'create_end') {
					if ($val) {
						$whereArr[] = "`day`<=:{$key} ";
					}
				}

			}
		}
		$where = !empty($whereArr) ? ' WHERE ' . implode(' AND ', $whereArr) : '';
		$sql = "SELECT * FROM `stats_active_num` {$where} group by day ";
		$sth = self::getStatsDB()->prepare($sql);
		$sth->bindValue(':day', date('Ymd'));
		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				if ($key == 'create_start' || $key == 'create_end') {
					if ($val) {
						$sth->bindValue(':' . $key, date('Ymd', strtotime($val)));
					}
				}
			}
		}
		$ret = $sth->execute();
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		$rows = $sth->fetchAll();
		return $rows;
	}

	/**
	 * 根据条件获取周活跃和月活跃用户列表
	 * @author duhuihui on 20120827
	 * @param array $parms 其它参数
	 * @return false/array
	 */
	static public function getOnlineCityId($day = '') {
		$sql = "SELECT * FROM `stats_active_num` where day = :day  group by day limit 1";
		$sth = self::getStatsDB()->prepare($sql);
		$sth->bindValue(':day', $day);
		$ret = $sth->execute();
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		$rows = $sth->fetchAll();
		return $rows;
	}
}
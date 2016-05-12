<?php

//战斗报告模块
class M_WarReport {

	/**
	 * 获取城市战斗报告列表
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @return array $data 战报ID列表 array(1,2,3,5)
	 */
	static public function getWarReportList($cityId, $type) {
		$ret    = array();
		$cityId = intval($cityId);
		if ($cityId > 0 && !empty($type)) {
			$rc  = new B_Cache_RC(T_Key::CITY_REPORT_LIST, $cityId . '_' . $type);
			$ret = $rc->smembers();
			if (empty($ret)) {
				//获取我方作为主动方的战斗报告
				$data1 = B_DB::instance('WarReport')->getRows('atk_city_id', $cityId, $type);
				//获取我方作为被动方的战斗报告
				$data2 = B_DB::instance('WarReport')->getRows('def_city_id', $cityId, $type);

				$data = array_merge($data1, $data2);
				foreach ($data as $info) {
					$rc->sAdd($info['id']);
					$ret[] = $info['id'];
				}
				$rc->expire(T_App::ONE_DAY);
			}

			if (!empty($ret)) {
				sort($ret);
			}
		}
		return $ret;
	}

	/**
	 * 添加城市战斗报告列表
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @param int $reportId 战报ID
	 * @param int $type 战报类型
	 * @return bool
	 */
	static public function setWarReportList($cityId, $reportId, $type) {
		$ret      = false;
		$reportId = intval($reportId);
		$cityId   = intval($cityId);
		if ($reportId > 0 && $cityId > 0 && $type > 0) {
			$rc  = new B_Cache_RC(T_Key::CITY_REPORT_LIST, $cityId . '_' . $type);
			$ret = $rc->sAdd($reportId, T_App::ONE_DAY);
		}
		return $ret;
	}

	/**
	 * 删除城市战斗报告列表
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @param int $reportId 战报ID
	 * @param int $type 战报类型
	 * @return bool
	 */
	static public function delWarReportList($cityId, $reportId, $type) {
		$ret      = false;
		$reportId = intval($reportId);
		$cityId   = intval($cityId);
		if ($reportId > 0 && $cityId > 0 && $type > 0) {
			$rc = new B_Cache_RC(T_Key::CITY_REPORT_LIST, $cityId . '_' . $type);
			if ($rc->sismember($reportId)) {
				$ret = $rc->srem($reportId);
			} else {
				$ret = true;
			}

		}
		return $ret;
	}

	/**
	 * 获取战斗报告信息
	 * @author huwei
	 * @param int $reportId 战报ID
	 * @return array $data
	 */
	static public function getWarReportInfo($reportId) {
		$ret      = false;
		$reportId = intval($reportId);
		if ($reportId > 0) {
			$rc  = new B_Cache_RC(T_Key::CITY_REPORT_INFO, $reportId);
			$ret = $rc->hgetall();
			if (empty($ret['id'])) {
				$ret = B_DB::instance('WarReport')->get($reportId);
				if (!empty($ret)) {
					self::setWarReportInfo($reportId, $ret);
				}
			}
		}
		return $ret;
	}

	/**
	 * 更新战斗报告信息
	 * @author huwei
	 * @param int $reportId 战报ID
	 * @param array $fieldArr 更新字段
	 * @param bool $isUp 是否更新数据库
	 * @return array/bool
	 */
	static public function setWarReportInfo($reportId, $fieldArr, $isUp = false) {
		$ret = false;
		if (!empty($reportId) && is_array($fieldArr) && !empty($fieldArr)) {
			$info = array();
			foreach ($fieldArr as $key => $val) {
				if (in_array($key, T_DBField::$warReportFields)) {
					$info[$key] = $val;
				}
			}
			if (!empty($info)) {
				$rc  = new B_Cache_RC(T_Key::CITY_REPORT_INFO, $reportId);
				$ret = $rc->hmset($info, T_App::ONE_DAY);
				if ($ret && $isUp) {
					$fields = $fieldArr;
					B_DB::instance('WarReport')->update($fields, $reportId);
				}
			}
		}
		return $ret ? $info : false;
	}


	/**
	 * 删除战斗报告信息
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @param int $reportId 战报ID
	 * @return array/bool
	 */
	static public function delWarReportInfo($reportId) {
		$ret      = false;
		$reportId = intval($reportId);
		if ($reportId > 0) {
			$rc  = new B_Cache_RC(T_Key::CITY_REPORT_INFO, $reportId);
			$ret = $rc->delete();

			if ($ret) {
				$bDel = B_DB::instance('WarReport')->delete(array('id' => $reportId));
			}
		}
		return $ret;
	}

	/**
	 * 删除所有战斗报告信息
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @param int $type 类型
	 * @return array/bool
	 */
	static public function delAllWarReport($cityId, $type) {
		$ret    = false;
		$cityId = intval($cityId);
		if ($cityId > 0 && !empty($type)) {
			$rc = new B_Cache_RC(T_Key::CITY_REPORT_LIST, $cityId . '_' . $type);
			if ($rc->exists()) {
				$ids = $rc->smembers();
				$ret = M_WarReport::delReport($cityId, $ids);

			}
		}

		return $ret;
	}


	/**
	 * 添加战斗报告
	 * @author HeJunyun 20110623
	 * @author huwei modify on 20120113
	 * @param array $initData
	 * @param array $upData
	 * @return int/bool $res
	 */
	static public function addWarReport($initData, $upData) {
		$ret = false;
		if (!empty($upData) && !empty($initData)) {
			list($type, $atkCityId, $defCityId) = $initData;
			$reportId = M_WarReport::updateWarReport($upData);
			if ($reportId > 0) {
				M_WarReport::setWarReportList($atkCityId, $reportId, $type);
				M_WarReport::addNureadReport($atkCityId, $reportId);
				if (!empty($defCityId)) {
					M_WarReport::setWarReportList($defCityId, $reportId, $type);
					M_WarReport::addNureadReport($defCityId, $reportId);
				}
				$ret = $reportId;
			}

		}
		return $ret;
	}

	/**
	 * 初始化战报ID==战斗ID
	 * @author huwei
	 * @param array $initData
	 * @return int
	 */
	static public function initWarReport($initData) {
		$ret = false;
		if (count($initData) == 6) {
			list($type, $atkCityId, $defCityId, $atkInfo, $defInfo, $battleType) = $initData;
			$now = time();
			$row = array(
				'type'               => $type,
				'battle_type'        => $battleType,
				'atk_city_id'        => $atkCityId,
				'def_city_id'        => $defCityId,
				'atk_time'           => $now,
				'flag_see'           => M_War::SEE_NO_ONE,
				'flag_del'           => M_War::DEL_NO_ONE,

				'is_succ'            => T_App::SUCC,
				'replay_address'     => '',
				'replay_address_md5' => '',
				'create_at'          => $now,
				'atk_info'           => json_encode($atkInfo),
				'def_info'           => json_encode($defInfo),
				'content'            => json_encode(array()),
				'reward'             => json_encode(array()),
			);
			$ret = B_DB::instance('WarReport')->insert($row);
			if (!$ret) {
				Logger::error(array(__METHOD__, 'Add War Report Fail', func_get_args()));
			}
		}
		return $ret;
	}

	/**
	 * 更新战报数据
	 * @author huwei
	 * @param array $data 字段[id,content,reward,is_succ,replay_address,create_at]
	 * @return int
	 */
	static public function updateWarReport($data) {
		$row = array(
			'id'             => $data['id'],
			'content'        => json_encode($data['content']),
			'reward'         => json_encode($data['reward']),
			'replay_address' => $data['replay_address'],
			'is_succ'        => $data['is_succ'],
			'create_at'      => time(),
		);
		$ret = B_DB::instance('WarReport')->update($row, $row['id']);
		if (!$ret) {
			$msg = array(__METHOD__, 'Update War Report Fail', func_get_args());
			Logger::error($msg);
		}
		return $ret ? $data['id'] : false;
	}

	/**
	 * 获取战斗报告
	 * @author HeJunyun
	 * @param int $cityId
	 * @return array $data
	 */
	static public function getReportList($cityId, $type, $page) {
		$list   = array();
		$cityId = intval($cityId);
		if ($cityId > 0) {
			//获取城市战斗报告列表
			$reportIdArr = M_WarReport::getWarReportList($cityId, $type);

			$total  = count($reportIdArr);
			$offset = 10;
			if ($total > M_War::MAX_WAR_REPORT_NUM + 1) {
				$listArr = array_splice($reportIdArr, 0, M_War::MAX_WAR_REPORT_NUM);
				$total   = M_War::MAX_WAR_REPORT_NUM;
			} else {
				$listArr = $reportIdArr;
			}

			$totalPage = ceil($total / $offset);
			$page      = min($totalPage, $page);
			$tmp       = $total % $offset;
			$start     = ($totalPage - $page) * $offset;
			if ($tmp != 0) {
				$start = $start - $offset + $tmp;
				if ($start < 0) {
					$start  = 0;
					$offset = $tmp;
				}
			}

			if (!empty($listArr)) {
				$listArr = array_splice($listArr, $start, $offset);
				$listArr = array_reverse($listArr);
				foreach ($listArr as $reportId) {
					$info = M_WarReport::getWarReportInfo($reportId);
					if (!empty($info['id'])) {
						if ($info['type'] == $type) {
							$list[] = $info;
						}
					} else {
						Logger::debug(array(__METHOD__, 'delWarReportList', $cityId, $reportId, $type));
						M_WarReport::delWarReportList($cityId, $reportId, $type);
					}
				}
			}
		}
		$ret['list']  = $list;
		$ret['total'] = $total;

		return $ret;
	}

	/**
	 * 查看战报内容
	 * @author HeJunyun
	 * @param int $reportId 战报ID
	 * @return array $data
	 */
	static public function getReport($reportId, $cityId) {

		$ret      = false;
		$reportId = intval($reportId);
		$cityId   = intval($cityId);
		if ($cityId > 0 && $reportId > 0) {
			$data = M_WarReport::getWarReportInfo($reportId);

			if (!empty($data['id']) && in_array($cityId, array($data['atk_city_id'], $data['def_city_id']))) {
				$fieldArr = array();
				$ownerId  = false;
				if ($data['atk_city_id'] == $cityId) {
					$ownerId              = $data['atk_city_id'];
					$fieldArr['flag_see'] = $data['flag_see'] | M_War::SEE_ATK; //改为1或3
				} else if ($data['def_city_id'] == $cityId) {
					$ownerId              = $data['def_city_id'];
					$fieldArr['flag_see'] = $data['flag_see'] | M_War::SEE_DEF; //改为2或3
				}

				if (!empty($fieldArr) && $data['flag_see'] != $fieldArr['flag_see']) {
					//flag变化 才更新
					$bUp = M_WarReport::setWarReportInfo($reportId, $fieldArr, true);
					$ownerId && M_WarReport::delNureadReport($ownerId, $reportId);
					$bUp && $data['flag_see'] = $fieldArr['flag_see'];
				}
				$ret = $data;
			}
		}
		return $ret;
	}


	/**
	 * 删除战斗报告
	 * @author HeJunyun
	 * @param int $cityId 城市ID
	 * @param array $ids example:array(1,2,3,4,5...)
	 * @return array $errID 删除操作失败的战报ID
	 */
	static public function delReport($cityId, $ids) {
		$errID  = array();
		$cityId = intval($cityId);
		if ($cityId > 0 && is_array($ids)) {
			foreach ($ids as $reportId) {
				$bUp = false;
				$row = M_WarReport::getWarReportInfo($reportId);
				if (!empty($row['id'])) {
					$fieldArr = array();

					if (!empty($row['def_city_id'])) {
						//如果防守方为城市
						if ($row['atk_city_id'] == $cityId) {
							$fieldArr['flag_del'] = $row['flag_del'] | M_War::DEL_ATK;
						} elseif ($row['def_city_id'] == $cityId) {
							$fieldArr['flag_del'] = $row['flag_del'] | M_War::DEL_DEF;
						}
						if (!empty($fieldArr)) {
							$bUp = M_WarReport::delWarReportList($cityId, $reportId, $row['type']);
							M_WarReport::delNureadReport($cityId, $reportId);
							if ($fieldArr['flag_del'] == M_War::DEL_ALL) {
								$bUp && M_WarReport::delWarReportInfo($reportId);
							} else {
								$bUp && M_WarReport::setWarReportInfo($reportId, $fieldArr, true);
							}
						}
					} else {
						//如果防守方为副本
						$fieldArr['flag_del'] = M_War::DEL_ALL;
						$bUp                  = M_WarReport::delWarReportList($cityId, $reportId, $row['type']);
						$bUp && M_WarReport::delWarReportInfo($reportId);
						M_WarReport::delNureadReport($cityId, $reportId);
					}
				}
				if (!$bUp) {
					array_push($errID, $reportId);
				}
			}
		}
		return $errID;
	}

	/**
	 * 同步未读战报
	 * @author huwei on 20120215
	 * @param int $cityId
	 * @return bool
	 */
	static public function syncNureadReport($cityId) {
		$ret = false;
		if (!empty($cityId)) {
			$msRow = array(
				'UnreadReportNum' => M_WarReport::getNureadReportNum($cityId),
			);

			$ret = M_Sync::addQueue($cityId, M_Sync::KEY_CITY_INFO, $msRow); //同步未读消息条数
		}
		return $ret;
	}

	/**
	 * 添加未读战报ID
	 * @author huwei on 20120215
	 * @param int $cityId
	 * @param int $msgId
	 * @return bool
	 */
	static public function addNureadReport($cityId, $msgId) {
		$ret = false;
		if (!empty($cityId) && !empty($msgId)) {
			$rc  = new B_Cache_RC(T_Key::CITY_REPORT_UNREAD, $cityId);
			$ret = $rc->sAdd($msgId, T_App::ONE_DAY);
			M_WarReport::syncNureadReport($cityId);
		}

		return $ret;
	}

	/**
	 * 删除未读战报ID
	 * @author huwei on 20120215
	 * @param int $cityId
	 * @param int $msgId
	 * @return bool
	 */
	static public function delNureadReport($cityId, $msgId) {
		$ret = false;
		if (!empty($cityId) && !empty($msgId)) {
			$rc = new B_Cache_RC(T_Key::CITY_REPORT_UNREAD, $cityId);
			if ($rc->sismember($msgId)) {
				$ret = $rc->srem($msgId);
				M_WarReport::syncNureadReport($cityId);
			} else {
				$ret = true;
			}

		}
		return $ret;
	}

	/**
	 * 获取未读战报数量
	 * @author huwei on 20120215
	 * @param int $cityId
	 * @return int
	 */
	static public function getNureadReportNum($cityId) {
		$ret = 0;
		if (!empty($cityId)) {
			$rc  = new B_Cache_RC(T_Key::CITY_REPORT_UNREAD, $cityId);
			$ret = $rc->scard();
		}
		return $ret;
	}

	/**
	 * 修正未读战报数量
	 * 次方法主要用于修复数据 在在初始化信息时调用
	 * 平常调用用 getNureadReportNum方法
	 * @author huwei on 20120215
	 * @param int $cityId
	 * @return void
	 */
	static public function getNureadReport($cityId) {
		$ret = array();
		if (!empty($cityId)) {
			$rc  = new B_Cache_RC(T_Key::CITY_REPORT_UNREAD, $cityId);
			$ids = $rc->sMembers();

			foreach ($ids as $id) {
				$del = true;
				$row = M_WarReport::getWarReportInfo($id);
				if (!empty($row['id'])) {
					$flag = false;
					if ($row['atk_city_id'] == $cityId) {
						$flag = M_War::SEE_ATK;
					} else if ($row['def_city_id'] == $cityId) {
						$flag = M_War::SEE_DEF;
					}

					if ($flag && ($row['flag_see'] & $flag) == 0) {
						$ret[]  = $id;
						$del    = false;
						$type   = $row['type'];
						$rc1    = new B_Cache_RC(T_Key::CITY_REPORT_LIST, $cityId . '_' . $type);
						$bExist = $rc1->sismember($id);
						if (!$bExist) {
							$del = true;
						}
					}
				}

				if ($del) {
					$rc->sRem($id);
				}
			}
		}
		return $ret;
	}
}
<?php

class M_Build {
	static private $_List = array();

	/** 城镇中心 建筑ID */
	const ID_TOWN_CENTER = 1;
	/** 粮食基地 建筑ID */
	const ID_FOOD_BASE = 2;
	/** 石油基地 建筑ID */
	const ID_OIL_BASE = 3;
	/** 银行 建筑ID */
	const ID_GOLD_BASE = 4;
	/** 仓库 建筑ID */
	const ID_STORAGE = 5;
	/** 住宅 建筑ID */
	const ID_HOUSE = 6;
	/** 军校  建筑ID*/
	const ID_HERO_COLLEGE = 7;
	/** 兵营 建筑ID */
	const ID_MIL_CAMP = 8;
	/** 军械所 建筑ID */
	const ID_ARMORY = 9;
	/** 市场 建筑ID */
	const ID_MARKET = 10;
	/** 科研中心 建筑ID */
	const ID_TECH_CENTER = 11;
	/** 雷达 建筑ID */
	const ID_RADAR = 12;
	/** 间谍学校 建筑ID */
	const ID_SPY_COLLEGE = 13;

	/** 是装饰建筑 */
	const IS_BEAUTIFY = 1;

	/** 不是装饰建筑 */
	const NOT_BEAUTIFY = 0;

	/** 仓库容量公式参数 */
	const STORAGE_ARGS = 1000;
	/** 城市无仓库 默认容量 */
	const DEFAULT_STORE = 20000;

	/** 新建建筑 */
	const BUILD_NEW = 1;
	/** 升级建筑 */
	const BUILD_UPGRADE = 2;


	/** 城镇中心升级至某等级需求功勋 */
	static $townExtraNeed = array(
		11 => 10,
		21 => 100,
		31 => 300,
		41 => 800,
	);


	/**
	 * 根据建筑ID获取建筑基础数据
	 * @author chenhui    on 20110413
	 * @param int build_id 建筑ID
	 * @return array 建筑基础数据(一维数组)
	 */
	static public function baseInfo($buildId) {
		$apcKey = T_Key::BASE_BUILD . '_' . $buildId;
		$info   = B_Cache_APC::get($apcKey);
		if (empty($info)) {
			$info = B_DB::instance('BaseBuild')->getOne($buildId);
			APC::set($apcKey, $info);
		}
		return $info;
	}

	/**
	 * 根据建筑ID和等级获取建筑升级数据
	 * @author chenhui    on 20110413
	 * @param int build_id 建筑ID
	 * @param int level 建筑等级
	 * @return array 建筑升级数据(一维数组)
	 */
	static public function baseUpgInfo($buildId, $level) {
		$listData = M_Base::buildAll();
		return empty($listData[$buildId]['upg'][$level]) ? array() : $listData[$buildId]['upg'][$level];
	}


	/**
	 * 获取某城市全部可建 装饰建筑的ID
	 * @author chenhui    on 20110520
	 * @param int cityId
	 * @return array 可建 装饰建筑的ID组成的数组
	 */
	static public function getBeaBuildIdList($cityId) {
		$cityextrainfo = M_Extra::getInfo($cityId);
		$ret           = array();
		if (!empty($cityextrainfo['beautify_list'])) {
			$ret = json_decode($cityextrainfo['beautify_list'], true);
		}
		return $ret;
	}


	/**
	 * 判断某建筑ID是否可多建建筑
	 * @author chenhui    on 20110415
	 * @param int build_id 建筑ID
	 * @return bool true/false
	 */
	static public function isMultiBuild($build_id) {
		$baseinfo = M_Build::baseInfo($build_id);
		$ret      = false;
		if (!empty($baseinfo['is_multi'])) {
			$ret = true;
		}
		return $ret;
	}

	/**
	 * 判断某城市中心是否满足额外升级需求(功勋值)
	 * @author chenhui on 20110819
	 * @param int $cityId 城市ID
	 * @param int $new_level 要升级到的等级
	 * @return bool
	 */
	static public function isTCUpgExtraNeedOK($cityId, $new_level) {
		$ret = true;
		if (array_key_exists($new_level, M_Build::$townExtraNeed)) {
			$cityInfo = M_City::getInfo($cityId);
			if ($cityInfo['mil_medal'] < M_Build::$townExtraNeed[$new_level]) {
				$ret = false;
			}
		}
		return $ret;
	}

	/**
	 * 判断能否拆除某住宅
	 * @author chenhui on 20120421
	 * @param int $bid 住宅ID
	 * @param int $basePeople 无住宅基础人口
	 * @param int $oldLv 原等级
	 * @param int $newLv 新等级
	 * @param int $maxPeople 城市最大人口
	 * @param int $curPeople 当前占用人口
	 */
	static public function isHouseCanDegrade($bid, $basePeople, $oldLv, $newLv, $maxPeople, $curPeople) {
		$ret = true;
		if (M_Build::ID_HOUSE == $bid) {
			$freePeople   = max(0, $maxPeople - $curPeople);
			$oldMaxPeople = M_Formula::calcHouseCapaCity($basePeople, $oldLv);
			$newMaxPeople = M_Formula::calcHouseCapaCity($basePeople, $newLv);
			$descPeople   = max(0, $oldMaxPeople - $newMaxPeople);
			($descPeople > $freePeople) && $ret = false;
		}
		return $ret;
	}

	/**
	 * 处理建筑CD时间(允许CD队列累计时间4小时)
	 * @author chenhui on 20110421
	 * @param int $cd_build_num 当前最大允许建筑CD队列数
	 * @param string $str_cd_build 当前建筑CD时间json字符串
	 * @param int $cost_time 新建筑任务CD时间
	 * @return array(0/1,msg/new_str_cd_build)
	 */
	static public function cdBuild($cd_build_num, $str_cd_build, $cost_time) {
		$flag    = T_App::FAIL;
		$content = T_ErrNo::BUILD_MAX_ROW_NOW;

		$nowtime = time();
		$arrTmp  = json_decode(M_City::calcCDBuild($str_cd_build, $nowtime), true);
		if (count($arrTmp) <= $cd_build_num) {
			$arr_cd_build = json_decode($str_cd_build, true);
			for ($i = 0; $i < $cd_build_num; $i++) {
				if (isset($arr_cd_build[$i])) {
					//$arr_cd_build[$i] = is_numeric($arr_cd_build[$i]) ? $arr_cd_build[$i].'_'.'1' : $arr_cd_build[$i];//@todo 容错处理
					$arrT    = explode('_', $arr_cd_build[$i]);
					$arrT[1] = ($arrT[0] <= $nowtime) ? T_App::ADDUP_CAN : $arrT[1];
					if (T_App::ADDUP_CAN == $arrT[1]) {
						if ($arrT[0] < $nowtime + M_City::CD_BUILD_ADDUP_MAX) {
							$endT             = max($nowtime, $arrT[0]) + $cost_time;
							$fT               = ($endT < $nowtime + M_City::CD_BUILD_ADDUP_MAX) ? T_App::ADDUP_CAN : T_App::ADDUP_CANT;
							$arr_cd_build[$i] = implode('_', array($endT, $fT));
							$flag             = T_App::SUCC;
							break;
						}
					}
				} else {
					$fT               = ($cost_time < M_City::CD_BUILD_ADDUP_MAX) ? T_App::ADDUP_CAN : T_App::ADDUP_CANT;
					$arr_cd_build[$i] = implode('_', array($nowtime + $cost_time, $fT));
					$flag             = T_App::SUCC;
					break;
				}

			}

			while (count($arr_cd_build) < $cd_build_num) {
				$arr_cd_build[] = implode('_', array($nowtime, T_App::ADDUP_CAN));
			}

			if (T_App::SUCC == $flag) {
				$content = json_encode($arr_cd_build);
			}
		}

		return array($flag, $content);
	}

	/**
	 * 同步建筑数据至前端接口
	 * @author chenhui on 20110815
	 * @param int $cityId 城市ID
	 */
	static public function syncBuildinfo2Front($cityId, $buildId, $pos, $lv, $status) {
		if (!empty($cityId) && !empty($buildId)) {
			$msRow = array(
				$buildId => array($pos, $lv, $status)
			);
			M_Sync::addQueue($cityId, M_Sync::KEY_BUILD, $msRow);
		}
	}

	/**
	 * 计算空袭命中建筑
	 * @author chenhui on 20110704
	 * @return string 建筑代号
	 */
	static public function calcBombHit() {
		$randnum = mt_rand(1, 100); //生成随机数
		$build   = 'oil'; //默认 石油基地
		if ($randnum > 92) {
			$build = 'storage'; //仓库
		} else if ($randnum > 80) {
			$build = 'house'; //民居
		} else if ($randnum > 60) {
			$build = 'radar'; //雷达
		} else if ($randnum > 40) {
			$build = 'gold'; //银行中心
		} else if ($randnum > 20) {
			$build = 'food'; //粮食基地
		}
		return $build;
	}







	/**********建筑模块管理后台所需接口*******************/
	/** 删除建筑 基础 数据 缓存 */
	static public function delBuildBaseCache() {
		APC::del(T_Key::BASE_BUILD); //删除缓存，用完注释
	}

	/** 删除建筑 升级 数据 缓存 */
	static public function delBuildUpgCache() {
		APC::del(T_Key::UPG_BUILD . '*'); //删除缓存，用完注释
	}

	/**
	 * 获取装饰建筑ID=>名字的数组
	 * @author chenhui on 20110820
	 * @return array ID=>name
	 */
	static public function getBeautifyIdName() {
		$arrBaseInfo = M_Base::buildAll();
		$arrIdName   = array();
		if (!empty($arrBaseInfo) && is_array($arrBaseInfo)) {
			foreach ($arrBaseInfo as $buildId => $baseInfo) {
				if (M_Build::IS_BEAUTIFY == $baseInfo['is_beautify']) {
					$arrIdName[$buildId] = $baseInfo['name'];
				}
			}
		}
		return $arrIdName;
	}


	/*********************统计相关*************************/
	/**
	 * 累加当天城市中心升级次数
	 * @author Hejunyun
	 * @param int $level 城市中心等级
	 */
	static public function incrStatsCityLevelNum($level) {
		$level = intval($level);
		$level = min(50, $level);
		$level = max(0, $level);
		$rc    = new B_Cache_RC(T_Key::STATS_USER_CITY_UPLEVEL, date('Ymd'));
		$rc->hincrby("b{$level}", 1);
	}

	/**
	 * 减少当天城市中心升级次数
	 * @author Hejunyun
	 * @param int $level 城市中心等级
	 */
	static public function decrStatsCityLevelNum($level) {
		$level = intval($level);
		$level = min(50, $level);
		$level = max(0, $level);

		$rc = new B_Cache_RC(T_Key::STATS_USER_CITY_UPLEVEL, date('Ymd'));
		$rc->hincrby("b{$level}", -1);
	}

	static public function getCityLevelData($day) {
		$ret  = array();
		$data = B_DB::instance('StatsLogCityBuild')->getStatsBuildLog($day);
		if (!empty($data)) {
			foreach ($data as $key => $val) {
				$buildRow = json_decode($val['build_data'], true);
				foreach ($buildRow as $level => $num) {
					$level = substr($level, 1);
					if (!isset($ret[$level])) {
						$ret[$level] = $num;
					} else {
						$ret[$level] = $ret[$level] + $num;
					}
				}
			}
		}

		//print_r($ret);
		return $ret;
	}


	static public function checkShowBuild($objPlayer, $no, $type = 'fb') {
		$objBuild = $objPlayer->Build();

		$cityBuildList = $objBuild->get();
		$tmpList       = $newBuild = array();
		foreach ($cityBuildList as $tmpbid => $binfo) {
			foreach ($binfo as $bpos => $blev) {
				$tmpList[$bpos] = $tmpbid; //建筑位置=>建筑ID
			}
		}

		list($zone, $mapPosX, $mapPosY) = M_MapWild::calcWildMapPosXYByNo($objPlayer->City()->pos_no);

		$baseBuildOpen = M_Config::getVal('build_open');
		if (!empty($baseBuildOpen[$zone])) {
			foreach ($baseBuildOpen[$zone] as $val) {
				list($tPos, $tBid, $tLv, $tFBNo) = $val;
				Logger::debug(array(__METHOD__, $val, $no));
				$isok = false;
				if ($type == 'fb' && $tFBNo <= $no) {
					$isok = true;
				} else if ($type == 'level' && $tLv <= $no) {
					$isok = true;
				}

				if ($isok && !isset($tmpList[$tPos])) {
					$cityBuildList[$tBid][$tPos] = 1;
					$newBuild[$tPos]             = $tBid;
				}
			}
		}

		if (!empty($newBuild)) {
			$objPlayer->Build()->set($cityBuildList);

			foreach ($newBuild as $newPos => $newBId) { //建筑相关效果的更新
				Logger::debug(array(__METHOD__, $objPlayer->City()->id, $newBId, $newPos));
				M_Build::syncBuildinfo2Front($objPlayer->City()->id, $newBId, $newPos, 1, 1);
				$objPlayer->Build()->updateEffect($newBId, M_Build::BUILD_NEW);
			}
		}
	}

}

?>
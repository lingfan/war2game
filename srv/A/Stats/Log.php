<?php

/**
 * 充值统计相关
 */
class A_Stats_Log {
	/**
	 * 充值日志
	 */
	static public function PayLog($formVals) {
		$curPage = !empty($formVals['page']) ? $formVals['page'] : 1;
		$offset = !empty($formVals['rows']) ? $formVals['rows'] : 20;
		$sidx = !empty($formVals['sidx']) ? $formVals['sidx'] : 'id';
		$sord = !empty($formVals['sord']) ? $formVals['sord'] : 'DESC';
		$parms = !empty($formVals['filter']) ? $formVals['filter'] : array();
		if (isset($parms['username'])) {
			$nickname = $parms['username'];
			if ($nickname) {
				$cityId = M_City::getCityIdByNickName($nickname);
				$parms['city_id'] = $cityId;
			}
			unset($parms['username']);
		}
		$curPage = max(1, $curPage);
		$ret = M_Pay::getPayLog($curPage, $offset, $parms, $sidx, $sord);

		return $ret;
	}

	/**
	 * 收入日志
	 */
	static public function IncomeLog($formVals) {
		$curPage = !empty($formVals['page']) ? $formVals['page'] : 1;
		$offset = !empty($formVals['rows']) ? $formVals['rows'] : 20;
		$sidx = !empty($formVals['sidx']) ? $formVals['sidx'] : 'id';
		$sord = !empty($formVals['sord']) ? $formVals['sord'] : 'DESC';
		$parms = !empty($formVals['filter']) ? $formVals['filter'] : array();
		if (isset($parms['username'])) {
			$nickname = $parms['username'];
			if ($nickname) {
				$cityId = M_City::getCityIdByNickName($nickname);
				$parms['city_id'] = $cityId;
			}
			unset($parms['username']);
		}
		$curPage = max(1, $curPage);
		$ret = M_Pay::getIncomeLog($curPage, $offset, $parms, $sidx, $sord);

		return $ret;
	}

	/**
	 * 消费明细
	 */
	static public function ConsumpLog($formVals) {
		$curPage = !empty($formVals['page']) ? $formVals['page'] : 1;
		$offset = !empty($formVals['rows']) ? $formVals['rows'] : 20;
		$sidx = 'id'; //!empty($formVals['sidx']) ? $formVals['sidx'] : 'id';
		$sord = !empty($formVals['sord']) ? $formVals['sord'] : 'DESC';
		$parms = !empty($formVals['filter']) ? $formVals['filter'] : array();
		if (isset($parms['username'])) {
			$nickname = $parms['username'];
			if ($nickname) {
				$cityId = M_City::getCityIdByNickName($nickname);
				$parms['city_id'] = $cityId;
			}
			unset($parms['username']);
		}
		$curPage = max(1, $curPage);

		$ret = M_Pay::getExpenseLog($curPage, $offset, $parms, $sidx, $sord);
		return $ret;
	}

	static public function CityLevelLog($data) {
		$ret = false;
		$day = isset($data['day']) ? $data['day'] : '';
		$day = intval($day);
		if ($day) {
			$ret = M_Build::getCityLevelData($day);
		}
		return $ret;
	}

	static public function LastFbLog() {
		$ret = array();
		$chapterNo = 1;
		while ($chapterInfo = M_SoloFB::getInfo($chapterNo)) {
			$campaignNo = 1;
			while (isset($chapterInfo['fb_list'][$campaignNo])) {
				$pointNo = 1;
				while (isset($chapterInfo['fb_list'][$campaignNo]['checkpoint_data'][$pointNo])) {
					$fbNo = M_Formula::calcFBNo($chapterNo, $campaignNo, $pointNo);
					$num = B_DB::instance('City')->getPastFbPerson($fbNo);
					$ret[$chapterNo][$campaignNo][$pointNo] = $num;
					$pointNo++;
				}
				$campaignNo++;
			}
			$chapterNo++;
		}
		return $ret;
	}

	/**
	 * 获取时间段内的注册量
	 * @param array $data
	 */
	static public function CountPlayer($data) {
		if (isset($data['start']) && isset($data['end'])) {
			$consumer_id = isset($data['consumer_id']) ? $data['consumer_id'] : 0;
			$num = M_User::countUser(intval($data['start']), intval($data['end']), $consumer_id);
			return $num;
		}
		return false;
	}

	/**
	 * 查看装备日志信息
	 * @param unknown_type $parms
	 */
	static public function LogEquip($parms) {
		$ret = false;
		$curPage = isset($parms['page']) ? $parms['page'] : 1;
		$offset = isset($parms['rows']) ? $parms['rows'] : 20;
		$cityId = 0;
		if (!empty($parms['filter']['nickname'])) {
			$cityId = M_City::getCityIdByNickName($parms['filter']['nickname']);
			if ($cityId > 0) {
				//查看指定玩家日志前先同步文件里的日志到数据库
				$pack = $cityId % 1000;
				$path = LOG_PATH . '/info/equip/' . $pack . '/' . $cityId . '.log';
				M_Cron::doFile($path, 'equip');
			}
			$parms['filter']['city_id'] = $cityId;
			unset($parms['filter']['nickname']);
		}

		$list = B_DBStats::apiPageData('stats_log_equip', '*', $curPage, $offset, $parms['filter']);
		foreach ($list as $k => $v) {
			$cityInfo = M_City::getInfo($v['city_id']);
			$list[$k]['nickname'] = $cityInfo['nickname'];
		}

		$ret['list'] = $list;
		$ret['total'] = B_DBStats::totalRows('stats_log_equip', $parms['filter']);
		return $ret;
	}

	/**
	 * 查看英雄日志信息
	 * @param unknown_type $parms
	 */
	static public function LogHero($parms) {
		$ret = false;
		$curPage = isset($parms['page']) ? $parms['page'] : 1;
		$offset = isset($parms['rows']) ? $parms['rows'] : 20;
		$cityId = 0;
		if (!empty($parms['filter']['nickname'])) {
			$cityId = M_City::getCityIdByNickName($parms['filter']['nickname']);
			if ($cityId > 0) {
				//查看指定玩家日志前先同步文件里的日志到数据库
				$pack = $cityId % 1000;
				$path = LOG_PATH . '/info/hero/' . $pack . '/' . $cityId . '.log';
				M_Cron::doFile($path, 'hero');
			}
			$parms['filter']['city_id'] = $cityId;
			unset($parms['filter']['nickname']);
		}

		$list = B_DBStats::apiPageData('stats_log_hero', '*', $curPage, $offset, $parms['filter'], 'create_at');
		foreach ($list as $k => $v) {
			$cityInfo = M_City::getInfo($v['city_id']);
			$list[$k]['nickname'] = $cityInfo['nickname'];
		}

		$ret['list'] = $list;
		$ret['total'] = B_DBStats::totalRows('stats_log_hero', $parms['filter']);

		return $ret;
	}

	/**
	 * 查看道具日志信息
	 * @param array $parms
	 */
	static public function LogProps($parms) {
		$ret = false;
		$curPage = isset($parms['page']) ? $parms['page'] : 1;
		$offset = isset($parms['rows']) ? $parms['rows'] : 20;
		$cityId = 0;
		if (!empty($parms['filter']['nickname'])) {
			$cityId = M_City::getCityIdByNickName($parms['filter']['nickname']);
			if ($cityId > 0) {
				//查看指定玩家日志前先同步文件里的日志到数据库
				$pack = $cityId % 1000;
				$path = LOG_PATH . '/info/props/' . $pack . '/' . $cityId . '.log';
				M_Cron::doFile($path, 'props');
			}
			$parms['filter']['city_id'] = $cityId;
			unset($parms['filter']['nickname']);
		}

		$names = M_Props::getPropsIdName();
		if (!empty($parms['filter']['props_name'])) //道具名称
		{
			$propsIds = array_flip($names);
			$parms['filter']['props_id'] = $propsIds[$parms['filter']['props_name']];
			unset($parms['filter']['props_name']);
		}

		$list = B_DBStats::apiPageData('stats_log_props', '*', $curPage, $offset, $parms['filter']);
		foreach ($list as $k => $v) {
			$list[$k]['props_id'] = $names[$v['props_id']];
			$cityInfo = M_City::getInfo($v['city_id']);
			$list[$k]['nickname'] = $cityInfo['nickname'];
		}
		$ret['list'] = $list;
		$ret['total'] = B_DBStats::totalRows('stats_log_props', $parms['filter']);
		return $ret;
	}

	/** 玩家拥有某道具数量排行 @author chenhui on 20130118 */
	static public function PropsRank($parms) {
		$ret = array();

		$propsId = !empty($parms['props_id']) ? $parms['props_id'] : 1;
		$nickname = !empty($parms['username']) ? $parms['username'] : '';
		$cityId = 0;
		if (!empty($nickname)) {
			$cityIdT = M_City::getCityIdByNickName($nickname);
			$cityId = !empty($cityIdT) ? $cityIdT : T_App::SYS_VAL_LIMIT_TOP;
		}

		$list = B_DB::instance('CityItem')->getPropsRank($propsId, $cityId);

		$ret['list'] = $list;
		$ret['idname'] = M_Props::getPropsIdName();
		return $ret;
	}

	/** 某时间段内玩家城市等级排行 @author chenhui on 20130118 */
	static public function CityRank($parms) {
		$ret = array();
		if (!empty($parms) && is_array($parms)) {
			$start = !empty($parms['create_start']) ? strtotime($parms['create_start']) : 0;
			$end = !empty($parms['create_end']) ? strtotime($parms['create_end']) : 0;
			$level = !empty($parms['city_level']) ? $parms['city_level'] : 1;
			$ret = B_DB::instance('City')->getCityRank($start, $end, $level);
		}

		return $ret;
	}

	/** 拍卖行使用统计 */
	static public function AucUse($parms) {
		$ret = false;
		$curPage = isset($parms['page']) ? $parms['page'] : 1;
		$offset = isset($parms['rows']) ? $parms['rows'] : 20;
		$nickName = isset($parms['filter']['nickname']) ? trim($parms['filter']['nickname']) : '';
		$cityId = intval(M_City::getCityIdByNickName($nickName));
		$title = isset($parms['filter']['title']) ? intval($parms['filter']['title']) : 0;

		unset($parms['filter']['nickname']);
		unset($parms['filter']['title']);
		$list = B_DB::instance('Auction')->getAllAucInfo($curPage, $offset, $cityId, $title, $parms['filter']);
		foreach ($list as $k => $v) {
			$saleInfo = M_City::getInfo($v['sale_city_id']);
			$list[$k]['sale_nickname'] = $saleInfo['nickname'];
			$buyInfo = M_City::getInfo($v['buy_city_id']);
			$list[$k]['buy_nickname'] = $buyInfo['nickname'];
			$list[$k]['goods_type_name'] = M_Auction::$goods_type[$v['goods_type']];
		}

		$ret['list'] = $list;
		$ret['total'] = B_DB::instance('Auction')->getAllAucInfoSum($cityId, $title, $parms['filter']);
		return $ret;
	}

	/** 跑马使用统计 */
	static public function HorseLog($parms) {
		$ret = false;
		$curPage = isset($parms['page']) ? $parms['page'] : 1;
		$offset = isset($parms['rows']) ? $parms['rows'] : 50;

		$list = B_DB::instance('Horse')->getRows($curPage, $offset, $parms['filter']);
		$ret['list'] = $list;
		$ret['total'] = B_DB::instance('Horse')->total($parms['filter']);
		return $ret;
	}

	/** 跑马每月统计 */
	static public function HorseMonth($parms) {
		$ret = false;

		$list = B_DB::instance('Horse')->getMonthSysHorse($parms['date']);
		$ret = $list;
		//$ret['total'] = 1;
		return $ret;
	}

	/** 军衔人数统计 */
	static public function MilRankNum($parms) {
		$milRankRenownConf = M_Config::getVal('mil_rank_renown');

		$ret = array();
		$list = B_DB::instance('City')->getMilRankNum();
		$ret['list'] = $list;
		$ret['total'] = count($milRankRenownConf);

		return $ret;
	}

}

?>
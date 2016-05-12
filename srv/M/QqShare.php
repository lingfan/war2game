<?php

class M_QqShare {
	const OPEN_QQ_SHARE_CONSUMER_ID = 8;
	/**
	 * 分享基础数据
	 * @author duhuihui
	 */
	static $type = array('build_up', 'tech_up', 'equip_strong', 'equip_mix', 'hero_skill', 'union_getaward',
		'props_use', 'props_buy', 'props_award', 'union_contribution', 'hero_train', 'fb_atk', 'break_out',
		'atk_wildnpc', 'occupied_city'
	);

	/**
	 * 通过id获得分享基础数据
	 * @author duhuihui
	 */
	static public function getBaseInfoById($Id) {
		$listData = M_Base::qqshareAll();
		return isset($listData['list'][$Id]) ? $listData['list'][$Id] : array();
	}

	/**
	 * 通过类型获得分享基础数据
	 * @author duhuihui
	 */
	static public function getBaseInfoByType($type) {
		$listData = M_Base::qqshareAll();
		return isset($listData['type'][$type]) ? $listData['type'][$type] : array();
	}

	/**
	 * array(完成条件, ID(0任意), 等级)
	 * @param array $questVal
	 * @param string $questCond 建筑ID=>array(分享id 分享id ),建筑ID=>array(分享id 分享id )
	 */
	static public function call($questCond, $questVal, $ingContent, $params = array()) {
		$ret = false;
		//完成条件
		if (method_exists('M_Task_QqShare', $questCond)) {
			$shareId         = M_Task_QqShare::$questCond($questVal, $ingContent, $params);
			$ret['val']      = $questVal;
			$ret['share_id'] = $shareId;
		}

		return $ret;
	}

	/**
	 * 检测任务条件
	 * @param int $cityId
	 * @param int $consumerId
	 * @param string $questCond
	 * @param array $params
	 */
	static public function check(O_Player $objPlayer, $questCond = 'build_up', $params = array()) {
		$ret      = false;
		$cityInfo = $objPlayer->getCityBase();
		$cityId   = $cityInfo['id'];
		$qqInfo   = M_Qq::getQQLive($cityId);
		$rc       = new B_Cache_RC(T_Key::SUCCESS_SHARE_TIMES, date('Ymd') . '_' . $cityId . '_' . $qqInfo['pf']);
		$num      = $rc->get();
		$maxTimes = M_Config::getVal('qq_share_success_num');
		if ($num < $maxTimes) {
			$info       = M_QqShare::getInfo($cityId);
			$ingContent = (array)json_decode($info['complete_txt'], true);
			$newContent = $ingContent;
			/**
			 * build_up=>array(建筑ID=>等级,建筑ID=>等级,)
			 **/
			$params['city_id'] = $cityId;
			$syncTmp           = array();
			$arr               = array();
			$arr               = isset($newContent[$questCond]) ? $newContent[$questCond] : array();
			$newVal            = M_QqShare::call($questCond, $arr, $ingContent, $params);
			if ($newVal) {
				$newContent[$questCond] = $newVal['val'];
				$syncTmp['id']          = $newVal['share_id'];

				$newIngContent = json_encode($newContent);
				if (isset($syncTmp['id']) && $syncTmp['id'] > 0 && $newIngContent != $info['complete_txt']) {
					$syncData = array();
					$baseList = M_QqShare::getBaseInfoById($newVal['share_id']);

					$awardArr             = M_Award::rateResult($baseList['award_id']);
					$awardText            = M_Award::toText($awardArr);
					$syncTmp['awardText'] = $awardText;
					$syncData             = $syncTmp;
					M_Sync::addQueue($cityId, M_Sync::KEY_QQ_SHARE, $syncData);
					$data = array('city_id' => $cityId, 'complete_txt' => $newIngContent);
					$ret  = M_QqShare::setInfo($data);
				}
			}
		}

		return $ret;
	}

	/**
	 * 获得城市达到的分享条件
	 * @author duhuihui
	 */
	static public function getInfo($cityId) {
		$info = false;
		if (!empty($cityId)) {
			$rc   = new B_Cache_RC(T_Key::CITY_QQ_SHARE, $cityId);
			$info = $rc->hgetall();
			if (empty($info['city_id'])) {
				$info = B_DB::instance('CityQqShare')->getRow($cityId);
				if (!empty($info['city_id'])) {
					$bSucc = self::setInfo($info, false);
					if (!$bSucc) {
						Logger::error(array(__METHOD__, 'fail set city_qq_share info', func_get_args()));
					}
				}
			}
		}
		return $info;
	}

	/**
	 * 更新城市达到的分享条件
	 * @author duhuihui
	 */
	static public function setInfo($data, $upDB = true) {
		$ret    = false;
		$cityId = isset($data['city_id']) ? $data['city_id'] : 0;
		if (!empty($cityId) && is_array($data)) {
			$info = array();
			foreach ($data as $key => $val) {
				if (!empty($key) && in_array($key, T_DBField::$cityQqShare)) {
					$info[$key] = $val;
				}
			}

			if (!empty($info)) {
				$rc  = new B_Cache_RC(T_Key::CITY_QQ_SHARE, $cityId);
				$ret = $rc->hmset($info, T_App::ONE_DAY);
				if ($ret) {
					B_DB::instance('CityQqShare')->update($info, $cityId);
				}
			}

			if (!$ret) {
				Logger::error(array(__METHOD__, 'Set Data Fail', func_get_args()));
			}
		}
		return $ret ? $info : false;
	}

}

?>
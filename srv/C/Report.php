<?php

/**
 * 战报接口
 */
class C_Report extends C_I {
	/**
	 * 战斗报告列表
	 * @author HeJunyun
	 * @param int $type 类型[进攻1,侦察2,空袭4]
	 * @param int $page 页码
	 */
	public function AList($type = 1, $page = 1) {

		$errNo = T_ErrNo::ERR_ACTION;
		$type = intval($type);
		$page = intval($page);
		$page = min($page, 20);
		$page = max($page, 1);
		$data = array();
		$list = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if (isset(M_March::$marchAction[$type])) {
			$retData = M_WarReport::getReportList($cityInfo['id'], $type, $page);

			if (isset($retData['list'])) {
				foreach ($retData['list'] as $key => $val) {
					if ($val['def_city_id'] == $cityInfo['id']) {
						$val['is_succ'] = ($val['is_succ'] == 1) ? 0 : 1;
					}
					$atkInfo = json_decode($val['atk_info'], true);
					$defInfo = json_decode($val['def_info'], true);
					list($atkPosZ, $atkPosX, $atkPosY) = M_MapWild::calcWildMapPosXYByNo($atkInfo[2]);

					$battleType = isset($val['battle_type']) ? $val['battle_type'] : 0;
					if ($battleType == M_War::BATTLE_TYPE_FB) {
						list($defPosZ, $defPosX, $defPosY) = M_Formula::calcParseFBNo($defInfo[2]);
					} else if ($battleType == M_War::BATTLE_TYPE_BOUT) {
						$defPosZ = 0;
						list($defPosX, $defPosY) = explode('_', $defInfo[2]); //突围ID,关编号从1开始
					} else {
						list($defPosZ, $defPosX, $defPosY) = M_MapWild::calcWildMapPosXYByNo($defInfo[2]);
					}

					$list[] = array(
						'ID' => $val['id'],
						'Type' => $val['type'],
						'BattleType' => $battleType,
						'AttInfo' => array($atkInfo[0], $atkInfo[1], $atkPosZ, $atkPosX, $atkPosY, $atkInfo[3]),
						'DefInfo' => array($defInfo[0], $defInfo[1], $defPosZ, $defPosX, $defPosY, $defInfo[3]),
						'AttCityId' => $val['atk_city_id'],
						'DefCityId' => $val['def_city_id'],
						'AttTime' => $val['atk_time'],
						'FlagSee' => $val['flag_see'],
						'IsSucc' => $val['is_succ'],
					);
				}
			}

			$data['List'] = $list;
			$data['Total'] = $retData['total'];
			$data['IsFull'] = ($retData['total'] == M_War::MAX_WAR_REPORT_NUM) ? 1 : 0;
			$errNo = '';
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 查看战报内容
	 * @author Hejunyun
	 * @param int $reportId 战报ID
	 */
	public function AInfo($reportId = 0) {

		$errNo = T_ErrNo::ERR_ACTION;
		$id = intval($reportId);
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if ($id) {
			$info = M_WarReport::getReport($id, $cityInfo['id']);
			if ($info['battle_type'] == M_War::BATTLE_TYPE_FB) {
				M_QqShare::check($objPlayer,  'fb_atk', array('id' => $cityInfo['last_fb_no']));
			} else if ($info['battle_type'] == M_War::BATTLE_TYPE_NPC) {
				M_QqShare::check($objPlayer,  'atk_wildnpc', array('type' => 0, 'level' => 0));
			} else if ($info['battle_type'] == M_War::BATTLE_TYPE_OCCUPIED_CITY) {
				M_QqShare::check($objPlayer,  'occupied_city', array('level' => 0));
			}
			$data = M_War::parseReportInfo($info, $cityInfo['id']);
			if (!empty($data)) {
				$errNo = '';
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 删除战报
	 * @author HeJunyun
	 * @param string $ids 战报ID列 example:'1,2,3,4,5...'
	 */
	public function ADel($ids = '') {

		$errNo = T_ErrNo::ERR_ACTION;
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$ids = explode(",", $ids);
		$data = array();
		if (is_array($ids)) {
			if (isset($ids[0])) {
				$data = M_WarReport::delReport($cityInfo['id'], $ids);
			}

			//如果错误的ID数组 不等于 传进来的id数组  则表示有数据删除成 前端会刷新接口
			if (count($data) != count($ids)) {

				$errNo = '';
			} else {
				$errNo = T_ErrNo::REPORT_DEL_ERR;
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 删除类型所有战报
	 * @author HeJunyun
	 * @param int $type 战报类型[进攻1,侦察2,空袭4]
	 */
	public function ADelAll($type = 0) {
		$errNo = T_ErrNo::ERR_ACTION;
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$data = array();
		if (isset(M_March::$marchAction[$type])) {

			$errNo = '';
			$data = M_WarReport::delAllWarReport($cityInfo['id'], $type);
		}

		return B_Common::result($errNo, $data);
	}
}

?>
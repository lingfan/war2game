<?php

/**
 * 得到用户昵称
 */
class A_Stats_GetNickname {
	/*
	 * 通过军团
	*/
	static public function GetNicknameByUnion($params = array()) //$formVals['unionname']
	{
		$nickName = '';
		$nickNameArr = array();
		$unionName = !empty($params['unionname']) ? explode("\r\n", $params['unionname']) : '';

		if (!empty($unionName)) {
			foreach ($unionName as $name) {
				if (!empty($name)) {
					$unionInfo = M_Union::getUnionByName($name);
					if (!empty($unionInfo)) {
						$cityIdArr = M_Union::getUnionMemberIds($unionInfo['id']); //获取联盟城市ID
						if (!empty($cityIdArr)) {
							foreach ($cityIdArr as $cityId) {
								$cityInfo = M_City::getInfo($cityId); //获取城市信息
								$nickNameArr[] = $cityInfo['nickname'];
							}
						}
					}
				}
			}
			$nickName = implode("\n", $nickNameArr);
		}

		return $nickName;

	}

	static public function GetNicknameByAll($params = array()) //$formVals['allname']
	{
		$nickName = '';
		$nickNameArr = array();
		$cityId1 = intval($params['city_id1']);
		$cityId2 = intval($params['city_id2']);
		$num = $cityId2 - $cityId1;
		if (!empty($cityId1) && !empty($cityId2) && $num >= 0) {
			for ($i = 0; $i <= $num; $i++) {
				$cityId = $cityId1 + $i;
				$cityInfo = M_City::getInfo($cityId); //获取城市信息
				if (!empty($cityInfo)) {
					$nickNameArr[] = $cityInfo['nickname'];
				}
			}
			$nickName = implode("\n", $nickNameArr);
		}
		return $nickName;

	}
}

?>
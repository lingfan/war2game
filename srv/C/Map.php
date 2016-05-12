<?php

/**
 * 地图接口
 */
class C_Map extends C_I {
	/**
	 * 获取地图区域信息
	 * @author huwei
	 * @param int $zone 洲
	 * @param int $posX X坐标
	 * @param int $posY Y坐标
	 * @param int $weight X行数
	 * @param int $height Y列数
	 */
	public function AArea($zone = '', $posX = '', $posY = '', $weight = 1, $height = 1) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = '';
		$posX = intval($posX);
		$posY = intval($posY);
		$zone = intval($zone);

		if (isset(T_App::$map[$zone]) && $posX >= 0 && $posY >= 0) {
			$weight = min($weight, 40);
			$height = min($height, 20);

			$errNo = '';
			$data = M_MapWild::getWildMapBlock($zone, $posX, $posY, $weight, $height);
			$data = !empty($data) ? base64_encode($data) : '';
		}

		return B_Common::result($errNo, $data);

	}

	/**
	 *
	 * 获取地图城市信息
	 * @author huwei
	 * @param int $cityId 城市ID
	 */
	public function AInfo($cityId) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$cityId = intval($cityId);
		if (!empty($cityId)) {
			$objPlayer = new O_Player($cityId);
			$cityInfo = $objPlayer->getCityBase();
			if (!empty($cityInfo['id'])) {
				list($zone, $posX, $posY) = M_MapWild::calcWildMapPosXYByNo($cityInfo['pos_no']);

				$unionName = '';
				if (!empty($cityInfo['union_id'])) {
					//获取联盟信息
					$unionInfo = M_Union::getInfo($cityInfo['union_id']);
					$unionName = isset($unionInfo['name']) ? $unionInfo['name'] : '';
				}


				$data = array(
					'CityId' => (int)$cityInfo['id'],
					'FaceId' => $cityInfo['face_id'],
					'NickName' => $cityInfo['nickname'],
					'Gender' => $cityInfo['gender'],
					'PosX' => $posX,
					'PosY' => $posY,
					'PosArea' => $zone,
					'Level' => $cityInfo['level'],
					'UnionId' => (int)$cityInfo['union_id'],
					'UnionName' => $unionName,
				);
				$errNo = '';
			} else {
				$errNo = T_ErrNo::CITY_NO_EXIST;
			}
		}


		return B_Common::result($errNo, $data);
	}

}

?>
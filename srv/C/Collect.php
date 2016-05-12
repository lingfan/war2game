<?php

/**
 * 收藏接口
 */
class C_Collect extends C_I {
	/**
	 * 收藏地图坐标
	 * @author chenhui on 20111021
	 * @param string $title 收藏坐标的名称
	 * @param int $area 洲编号
	 * @param int $posx X坐标
	 * @param int $posy Y坐标
	 * @param string $unionName 联盟名字
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AAdd($title, $area, $posx, $posy, $unionName) {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_PARAM; //失败原因默认为参数错误
		$data = array(); //返回数据默认为空数组

		$objPlayer = $this->objPlayer;

		$title = trim($title);
		$area = intval($area);
		$posx = intval($posx);
		$posy = intval($posy);
		$unionName = trim($unionName);
		if (!empty($title) && !empty($area) && isset(T_App::$map[$area]) && $posx > 0 && $posy > 0 && $posx < M_MapWild::WILD_MAP_MAX_POS_X && $posy < M_MapWild::WILD_MAP_MAX_POS_Y) {
			$len = B_Utils::len($title);

			$cityExtraInfo = $objPlayer->getCityExtra();
			$collectList = isset($cityExtraInfo['pos_collect']) ? json_decode($cityExtraInfo['pos_collect'], true) : array();

			$err = '';
			if ($len > M_City::MAX_COLLNAME_LENGTH) {
				$err = T_ErrNo::COLL_NAME_LEN_ERR;
			} else if (B_Utils::isBlockName($title)) {
				$err = T_ErrNo::COLLECT_NAME_ERR;
			} else if (count($collectList) > M_City::MAX_COLL_SUM) {
				$err = T_ErrNo::COLL_SUM_MAX;
			}

			$errNo = $err;
			if (empty($err)) {
				$mapPosNo = M_MapWild::calcWildMapPosNoByXY($area, $posx, $posy); //地图坐标编号
				$arrCollect[$mapPosNo] = array($title, $unionName);
				$objPlayer->changeExtra['pos_collect'] = json_encode($arrCollect);
				$ret = $objPlayer->save();
				$errNo = T_ErrNo::ERR_UPDATE;
				if ($ret) {
					$errNo = '';
				}
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 获取所有收藏的地图坐标数据
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AList() {
		//操作结果默认为失败0
		$data = array(); //返回数据默认为空数组

		$objPlayer = $this->objPlayer;

		$extraInfo = $objPlayer->getCityExtra();
		$arrColl = json_decode($extraInfo['pos_collect'], true);
		if (!empty($arrColl) && is_array($arrColl)) {
			//krsort($arrColl);	//倒序
			foreach ($arrColl as $mapPosNo => $arrDesc) {
				$arrPos = M_MapWild::calcWildMapPosXYByNo($mapPosNo);
				$data[] = array($mapPosNo, $arrDesc[0], $arrPos[0], $arrPos[1], $arrPos[2], $arrDesc[1]);
			}
		}

		$errNo = '';

		return B_Common::result($errNo, $data);
	}

	/**
	 * 删除收藏的地图坐标
	 * @param int $posNo 收藏的索引
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ADel($posNo) {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_PARAM; //失败原因默认为参数错误
		$data = array(); //返回数据默认为空数组
		$posNo = intval($posNo);

		$objPlayer = $this->objPlayer;

		if ($posNo > 0) {
			$extraInfo = $objPlayer->getCityExtra();
			$arrColl = json_decode($extraInfo['pos_collect'], true);

			$errNo = T_ErrNo::COLL_POS_NOT_EXIST;
			if (!empty($arrColl)) {
				$errNo = T_ErrNo::ERR_UPDATE;
				if (isset($arrColl[$posNo])) {
					unset($arrColl[$posNo]);
					//$arrColl = array_values($arrColl);	//重排索引
					$objPlayer->changeExtra['pos_collect'] = json_encode($arrColl);
					$ret = $objPlayer->save();
					if ($ret) {
						$errNo = '';
					}
				}
			}
		}

		return B_Common::result($errNo, $data);
	}
}

?>
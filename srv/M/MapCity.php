<?php

class M_MapCity {
	/**
	 * 是否坐标位置被占用
	 * @param int $areaId 洲编号
	 * @param int $cityId 城市ID
	 * @param int $buildId 建筑ID
	 * @param int $posX 建筑新X位置
	 * @param int $posY 建筑新Y位置
	 * @param int $oldPosX 建筑老X位置(移动)
	 * @param int $oldPosY 建筑老Y位置(移动)
	 * @return bool (已占用true 未占用false)
	 */
	static public function isRepeatCityInArea($areaId, $level, $cityId, $buildId, $posX, $posY, $oldPosX = '', $oldPosY = '') {
		//城市建筑坐标图
		$cityArea = self::_makeCityArea($areaId, $level, $cityId);

		if (!empty($oldPosX) && !empty($oldPosY)) {
			//如果老坐标存在 清除老坐标位置的坐标数据
			$cityArea = self::_moveBuildArea($areaId, $cityArea, $buildId, $oldPosX, $oldPosY);
		}

		$buildInfo = M_Build::baseInfo($buildId);
		$arrArea   = json_decode($buildInfo['area'], true);
		$decStr    = $arrArea[$areaId];
		$buildArea = ($posX % 2) ? self::_oddBuildArea($decStr) : self::_eveBuildArea($decStr);
		$offest    = 8;

		for ($i = 0; $i < count($buildArea); $i++) {
			$start     = ($posY + $i) * 50 + $posX;
			$newBinStr = self::_decNum2BinStr($buildArea[$i]); //新的占用坐标位置
			$oldBinStr = substr($cityArea, $start, $offest); //老的占用坐标位置
			$binNum    = bindec($newBinStr) & bindec($oldBinStr); //用与运算 计算是否占用坐标

			if ($binNum > 0) {
				return true; ////返回占用
			}
		}
		return false; //默认没有占用
	}

	/**
	 * 解析十进制 to ascii数据
	 * @author huwei
	 * @param string $decStr 建筑地图坐标数据(1,0,0,28,16,0,22,28)
	 * @return string  ascii字符串
	 */
	static public function buildDec2Chr($decStr) {
		return $decStr;
	}

	/**
	 * 解析ascii数据 to 十进制字符串
	 * @author huwei
	 * @param string $chrStr ascii数据
	 * @return string  $decStr  十进制字符串(1,0,0,28,16,0,22,28)
	 */
	static public function buildChr2Dec($chrStr) {
		$arr    = str_split($chrStr, 1);
		$ordArr = array();
		foreach ($arr as $val) {
			$ordArr[] = ord($val); //解析acsii码数据
		}
		$decStr = implode(',', $ordArr);
		return $decStr;
	}

	/**
	 * 偶数位置建筑坐标数据
	 * @author huwei
	 * @param string $decStr 建筑地图坐标数据(1,0,0,28,16,0,22,28)
	 * @return array [1,0,0,28,16,0,22,28]
	 */
	static private function _eveBuildArea($decStr) {
		$areaArr = explode(',', $decStr);
		return $areaArr;
	}

	/**
	 * 奇数位置建筑坐标数据
	 * @author huwei
	 * @param string $decStr 建筑地图坐标数据(1,0,0,28,16,0,22,28)
	 * @return array  [2,2,0,1,16,0,20,1]
	 */
	static private function _oddBuildArea($decStr) {
		$areaArr = explode(',', $decStr);

		$areaArr[] = 0; //奇数位补一个0  比正常的建筑面积多一行

		foreach ($areaArr as $val) {
			$jStr     = self::_decNum2BinStr($val);
			$posArr[] = str_split($jStr);
		}


		$area = array();
		foreach ($posArr as $xk => $xv) {
			$tmp = '';
			foreach ($xv as $yk => $yv) {
				$odd = $yk % 2;
				if ($odd) {
					//初始第一排记录
					$tmp .= ($xk == 0) ? 0 : $posArr[$xk - 1][$yk];
				} else {
					$tmp .= $posArr[$xk][$yk];
				}
			}
			$area[$xk] = (string)bindec($tmp);
		}
		return $area;
	}

	/**
	 * 十进制 转换成8位的二进制字符串
	 * @author huwei
	 * @param int $decNum
	 */
	static private function _decNum2BinStr($decNum) {
		$baseConf = M_Config::getVal();
		return str_pad(decbin($decNum), $baseConf['build_area_y'], '0', STR_PAD_LEFT);
	}

	/**
	 * 更新城内已占用坐标的ASCII字符串数据
	 * @author chenhui on 20110617
	 * @param int $areaId 洲编号
	 * @param int $level 城市等级(1-5)
	 * @param string $str_as
	 * @return bool
	 */
	static public function updateCityMapBlock($areaId, $level, $str_as) {
		$ret    = false;
		$str_as = trim($str_as);
		if (is_numeric($areaId) && is_numeric($level) && isset(T_App::$map[$areaId]) && !empty($str_as) && is_string($str_as)) {
			$confName                  = 'city_map_block_data';
			$blockVal                  = M_MapCity::getCityMapBlock();
			$blockVal[$areaId][$level] = $str_as;

			$upData = array($confName => $blockVal);
			$ret    = M_Config::setVal($upData);
		}
		return $ret;
	}

	/**
	 * 获取各洲城内地图已占用坐标数据
	 * @author chenhui on 20110620
	 * @return array 数组数据
	 */
	static public function getCityMapBlock() {
		$blockVal = M_Config::getVal('city_map_block_data');
		return !empty($blockVal) ? $blockVal : array();
	}

	/**
	 * 获取城内已占用坐标的ASCII字符串数据
	 * @author chenhui on 20110617
	 * @param int $areaId 洲编号
	 * @return string ASCII字符串
	 */
	static public function getCityMapBlockById($areaId, $level = 1) {
		$level    = 1; //默认取一级的
		$blockVal = M_MapCity::getCityMapBlock();
		$str_as   = isset($blockVal[$areaId][$level]) ? $blockVal[$areaId][$level] : '';
		return $str_as;
	}

	/**
	 * 初始化城内地图数据(包含被占用块的2500位数据)
	 * @author chenhui on 20110617
	 * @param int $areaId 洲编号
	 * @return string 地图字符串
	 */
	static private function _initCityArea($areaId, $level) {
		$str_as = M_MapCity::getCityMapBlockById($areaId, $level);
		$str_as = base64_decode($str_as);

		$baseConf = M_Config::getVal();
		$x        = $baseConf['city_in_area_x'];
		$y        = $baseConf['city_in_area_y'];

		$strArea = '';
		if (empty($str_as)) {
			$strArea = str_repeat('0', $x * $y);
		} else {
			$arr_as        = str_split($str_as, 2);
			$arr_block_pos = array();
			foreach ($arr_as as $k => $as) {
				$pos                 = ord($as[0]) . '_' . ord($as[1]);
				$arr_block_pos[$pos] = 1;
			}

			for ($i = 0; $i < $x; $i++) {
				for ($j = 0; $j < $y; $j++) {
					$ij = $i . '_' . $j;
					$strArea .= isset($arr_block_pos[$ij]) ? '1' : '0';
				}
			}
		}
		return $strArea;
	}

	/**
	 * 生成一个城内地图
	 * @author huwei
	 * @param int $areaId 洲编号
	 * @param int $cityId 城市ID
	 * @return string 101010010110.... 总长度2500
	 */
	static private function _makeCityArea($areaId, $level, $cityId) {
		//初始城内地图数据
		$strArea = self::_initCityArea($areaId, $level);

		$objPlayer = new O_Player($cityId);
		$buildList = $objPlayer->Build()->get();

		//数据结构  建筑ID=>array(X_Y=>等级)
		if (!empty($buildList) && is_array($buildList)) {
			foreach ($buildList as $key => $val) {
				foreach ($val as $k => $v) {
					$pos     = explode('_', $k); //分解坐标
					$strArea = self::_replaceCityArea($areaId, $strArea, $key, $pos[0], $pos[1]);
				}
			}
		}
		return $strArea;
	}

	/**
	 * 替换城内地图数据
	 * @author huwei
	 * @param string $cityArea 老城内地图数据
	 * @param int $x 建筑位置X
	 * @param int $y 建筑位置y
	 * @param int $buildId 建筑ID
	 * @return string 新城内地图数据
	 */
	static private function _replaceCityArea($areaId, $cityArea, $buildId, $posX, $posY) {
		//用于测试数据
		//$str = '255,0,0,250,0,0,0,250';
		//$chrStr = self::addBuildArea($str);

		$buildInfo = M_Build::baseInfo($buildId);
		//$decStr = '255,0,0,250,0,0,0,250';
		$arrArea = json_decode($buildInfo['area'], true);
		$decStr  = $arrArea[$areaId];
		$offest  = 8; //建筑由多少列坐标构成
		//x坐标是否奇数
		$buildArea = ($posX % 2) ? self::_oddBuildArea($decStr) : self::_eveBuildArea($decStr);
		//计算坐标组有几行, 偶数x坐标拥有8行 奇数x坐标拥有9行
		for ($i = 0; $i < count($buildArea); $i++) {
			$start      = ($posY + $i) * 50 + $posX;
			$newBinStr  = self::_decNum2BinStr($buildArea[$i]); //新的占用坐标位置
			$oldBinStr  = substr($cityArea, $start, $offest); //老的占用坐标位置
			$replaceDec = bindec($newBinStr) | bindec($oldBinStr); //用或运算 来保持老的占用坐标
			$replaceStr = self::_decNum2BinStr($replaceDec);
			$cityArea   = substr_replace($cityArea, $replaceStr, $start, $offest);
		}
		return $cityArea;
	}

	/**
	 * 移动建筑物
	 * @author huwei
	 * @param string $cityArea 老城内地图数据
	 * @param int $buildId 建筑ID
	 * @param int $oldPosX 建筑位置X
	 * @param int $oldPosY 建筑位置X
	 * @return string
	 */
	static private function _moveBuildArea($areaId, $cityArea, $buildId, $oldPosX, $oldPosY) {
		//$decStr = '255,0,0,250,0,0,0,250';
		$buildInfo = M_Build::baseInfo($buildId);
		$arrArea   = json_decode($buildInfo['area'], true);
		$decStr    = $arrArea[$areaId];
		$offest    = 8; //建筑由多少列坐标构成
		//x坐标是否奇数
		$buildArea = ($oldPosX % 2) ? self::_oddBuildArea($decStr) : self::_eveBuildArea($decStr);
		//计算坐标组有几行, 偶数x坐标拥有8行 奇数x坐标拥有9行
		for ($i = 0; $i < count($buildArea); $i++) {
			$start      = ($oldPosY + $i) * 50 + $oldPosX;
			$newBinStr  = self::_decNum2BinStr($buildArea[$i]); //新的占用坐标位置
			$oldBinStr  = substr($cityArea, $start, $offest); //老的占用坐标位置
			$replaceDec = bindec($newBinStr) ^ bindec($oldBinStr); //用异或运算 来清除当前建筑占用坐标
			$replaceStr = self::_decNum2BinStr($replaceDec);
			$cityArea   = substr_replace($cityArea, $replaceStr, $start, $offest);
		}
		return $cityArea;
	}

}

?>
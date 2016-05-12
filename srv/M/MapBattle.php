<?php

class M_MapBattle {
	/** 战斗地图攻击方出生点ID */
	const ATT_CELL_BORN = 1;
	/** 战斗地图防守方出生点ID */
	const DEF_CELL_BORN = 2;

	/** 战斗地图障碍属性  禁止通过 */
	static $warMapCellBanCrossType = array(
		M_Weapon::MOVE_FOOT => 1,
		M_Weapon::MOVE_CAR  => 2,
		M_Weapon::MOVE_FLY  => 4,
		M_Weapon::MOVE_SEA  => 8,
	);

	/** 战斗地图障碍属性   禁止停留*/
	static $warMapCellBanHoldType = array(
		M_Weapon::MOVE_FOOT => 16,
		M_Weapon::MOVE_CAR  => 32,
		M_Weapon::MOVE_FLY  => 64,
		M_Weapon::MOVE_SEA  => 128,
	);


	/** 防御物(有攻击,可破坏) */
	const WAR_MAP_CELL_ATTR_ATK = 1;
	/** 障碍物(无攻击,可破坏) */
	const WAR_MAP_CELL_ATTR_DMG = 2;
	/** 装饰 (无攻击,无破坏)*/
	const WAR_MAP_CELL_ATTR_FLAG = 4;
	/** 出生点 */
	const WAR_MAP_CELL_ATTR_BORN = 8;

	/** 战斗地图元素属性 */
	static $warMapCellAttr = array(
		self::WAR_MAP_CELL_ATTR_ATK  => '防御物',
		self::WAR_MAP_CELL_ATTR_DMG  => '障碍物',
		self::WAR_MAP_CELL_ATTR_FLAG => '装饰',
		self::WAR_MAP_CELL_ATTR_BORN => '出生点',
	);


	static public function cleanBaseWarMapCell() {
		APC::del(T_Key::BASE_WAR_MAP_CELL);
	}

	/**
	 * 获取基础战斗地图标记物信息
	 * @author huwei on 20110617
	 * @access public
	 * @param int $id
	 * @return array
	 */
	static public function getBaseWarMapCellInfo($id) {
		$list = M_Base::warmapcellAll();
		return isset($list[$id]) ? $list[$id] : '';
	}


	/**
	 * 把基础战斗地图标记物信息转换成二进制
	 * @author huwei on 20110617
	 * @param array $arr
	 * @return string
	 */
	static public function chrCellData($arr = '') {
		$tmp = $pkg = array();
		if (!empty($arr)) {
			foreach ($arr as $key => $val) {
				$xyArr = explode('_', $key);
				if (isset($xyArr[0]) && isset($xyArr[1])) {
					$xyStr          = B_Utils::mapDec2Bin($xyArr[0], 2) . B_Utils::mapDec2Bin($xyArr[1], 2);
					$tmp[$val[0]][] = $xyStr;
				}

			}

			foreach ($tmp as $k => $v) {
				$str    = implode('', $v);
				$xyLen  = mb_strlen($str);
				$pkgLen = B_Utils::mapDec2Bin($xyLen, 2);
				$xyKey  = B_Utils::mapDec2Bin($k, 1);
				$pkg[]  = $xyKey . $pkgLen . $str;
			}
		}
		return implode('', $pkg);
	}

	/**
	 * 转换战斗地图元素信息
	 * @author huwei on 20110617
	 * @param array $val 战斗元素数组
	 * @return string
	 */
	static public function _buildWarMapCellBanField($val) {
		$ret = '';
		if (!empty($val)) {
			$banStr = '';
			$banArr = json_decode($val, true);
			foreach ($banArr as $v) {
				$banStr .= chr($v);
			}
			$banLen = strlen($banStr);
			$ret    = $banLen . $banStr;
		}
		return $ret;
	}

	/**
	 * 获取战斗地图信息
	 * @author huwei
	 * @param int $mapNo 战斗地图编号
	 * @return array
	 */
	static public function getWarMapInfo($mapNo) {
		$apcKey = T_Key::BASE_WAR_MAP_DATA . '_' . $mapNo;
		$result = B_Cache_APC::get($apcKey);
		if (!$result) {
			$result = B_DB::instance('BaseWarMapData')->get($mapNo);
			//永久存储
			if (!empty($result)) {
				B_Cache_APC::set($apcKey, $result);
			}
		}
		return $result;
	}

	/**
	 * 更新战斗地图信息
	 * @author huwei
	 * @param array $fieldArr
	 * @return bool
	 */
	static public function updateWarMapInfo($fieldArr) {

		$ret = B_DB::instance('BaseWarMapData')->update($fieldArr);
		if ($ret) {
			$result = B_DB::instance('BaseWarMapData')->getInfo($fieldArr['id']);

			$apcKey = T_Key::BASE_WAR_MAP_DATA . '_' . $fieldArr['id'];
			$bSucc  = B_Cache_APC::del($apcKey);

		}
		return $ret;
	}


	/**
	 * 洲地形对应的战斗地图编号
	 * @author huwei on 20110602
	 * @param int $zone 洲编号
	 * @param int $terrian 地形编号
	 * @return int 地图编号
	 */
	static public function getMapNoByZone($zone, $terrain) {
		$ret  = false;
		$info = M_Config::getVal('war_map_zone');
		$key  = $zone . '_' . $terrain;
		if (isset($info[$key])) {
			$result = explode(',', $info[$key]);
			$noKey  = array_rand($result, 1);
			$ret    = !empty($result[$noKey]) ? $result[$noKey] : false;
		} else {
			$msg = array(__METHOD__, 'WAR MAP NOT EXIST', func_get_args());
			Logger::error($msg);
		}
		return $ret;
	}
}

?>
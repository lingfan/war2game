<?php

/**
 * 后台控制器
 */
class C_Admin {
	/**
	 * 获取战斗地图元素
	 * @author huwei on 20110616
	 */
	static public function AGetAllWarMapCell() {
		$baseCfg = M_Config::getVal();
		$resUrl = M_Config::getSvrCfg('server_res_url');
		$bgArr = $baseCfg['map_war_bg'];
		$bg = array();
		foreach ($bgArr as $key => $val) {
			$bg[] = array(
				'id' => $key,
				'name' => $val[0],
				'face_id' => $resUrl . 'imgs/war_map_bg/' . $val[1] . '.jpg'
			);
		}
		$out['bg_list'] = $bg;
		$cellInfo = DB_BaseWarMapCell::all();
		$cellArr = $secneArr = array();
		foreach ($cellInfo as $key => $val) {
			$cellArr[] = array(
				'id' => $val['id'],
				'name' => $val['name'],
				'face_id' => ROOT_URL . 'adm/images/war_map_cell/' . $val['id'] . '.jpg'
			);
		}

		$out['cell_info'] = $cellArr;


		$confFile = $resUrl . 'conf/war_map_secne.xml';
		$arr = B_Common::parseXml($confFile);

		if (isset($arr['row']) && !empty($arr['row'])) {
			foreach ($arr['row'] as $val) {
				$secneArr[] = array(
					//'id'	=> $val['id'],
					'id' => $val['face_id'],
					'name' => $val['name'],
					'face_id' => $resUrl . 'imgs/war_map_secne/' . $val['face_id'] . '.png',
				);
			}
		}

		$out['secne_info'] = $secneArr;
		B_Common::outXml($out);
	}

	static public function AGetMapSecne($type) {
		$baseCfg = M_Config::getVal();
		$resUrl = M_Config::getSvrCfg('server_res_url');
		if ($type == 'world') {
			$name = 'world_map_secne';
		} else {
			$name = 'war_map_secne';
		}
		$confFile = $resUrl . 'conf/' . $name . '.xml';

		$arr = B_Common::parseXml($confFile);
		$secneArr = array();
		if (isset($arr['row']) && !empty($arr['row'])) {
			foreach ($arr['row'] as $val) {
				$secneArr[] = array(
					'id' => $val['id'],
					'name' => $val['name'],
					'face_id' => $resUrl . 'imgs/' . $name . '/' . $val['face_id'] . '.png',
					'cell_id' => $val['cell_id'],
				);
			}
		}
		$out = $secneArr;
		B_Common::outXml($out);
	}

	/**
	 * 添加战斗地图
	 * @author huwei on 20110616
	 */
	static public function AUpdateWarMap() {
		$ret = false;
		$id = '';

		if (!empty($_POST['obj'])) {
			$formVals = json_decode($_POST['obj'], true);

			$info = array(
				'name' => $formVals['mapName'],
				'area_x' => $formVals['mapLie'],
				'area_y' => $formVals['mapHang'],
				'bg_no' => $formVals['mapId'],
				'cell_data' => json_encode($formVals['cellData']),
				'secne_data' => json_encode($formVals['secneData']),
				'create_at' => time(),
			);

			if (empty($formVals['id'])) {
				$ret = B_DB::instance('BaseWarMapData')->insert($info);
				$id = $ret;
			} else {
				$info['id'] = $formVals['id'];
				$ret = M_MapBattle::updateWarMapInfo($info);
				//$ret = B_DB::instance('BaseWarMapData')->update($info,$info['id']);
				$id = $info['id'];
			}
		}
		$out = array('succ' => $ret ? 1 : 0, 'info' => $id);
		B_Common::outXml($out);
	}

	/**
	 * 获取战斗地图
	 * @author huwei on 20110616
	 */
	static public function AGetOneWarMap() {
		$args = array(
			'id' => FILTER_SANITIZE_NUMBER_INT,
		);
		$formVals = filter_var_array($_REQUEST, $args);

		$info = B_DB::instance('BaseWarMapData')->get($formVals['id']);
		$row = false;


		//$str = 's:569:"{"secneData":{"430434":["1",430,434],"644259":["1",644,259],"87078":["1",870,78],"603193":["1",603,193],"921374":["1",921,374],"2942178.2":["1",294,2178.2],"2123.852598.2":["1",2123.85,2598.2],"2983.851941.2":["1",2983.85,1941.2],"2946.852237.2":["1",2946.85,2237.2],"6032236.2":["1",603,2236.2],"2912304.2":["1",291,2304.2]},"mapId":"1","mapHang":41,"mapName":"aaa","mapLie":46,"id":0,"cellData":{"336":["1",3,36],"106":["1",10,6],"338":["1",3,38],"3332":["1",33,32],"3237":["1",32,37],"2343":["1",23,43],"74":["1",7,4],"91":["1",9,1],"637":["1",6,37],"63":["1",6,3]}}"';
		//$data = json_decode(unserialize($str),true);


		if (isset($info['id'])) {
			$row = array(
				'name' => $info['name'],
				'area_x' => $info['area_x'],
				'area_y' => $info['area_y'],
				'bg_no' => $info['bg_no'],
				'cell_data' => $info['cell_data'],
				'secne_data' => $info['secne_data'],
				'id' => $info['id'],
			);
		}

		$out = array('succ' => $row ? 1 : 0, 'info' => $row);
		B_Common::outXml($out);
	}

	/**
	 * 删除战斗地图
	 * @author huwei on 20110616
	 */
	static public function ADelWarMap() {
		$args = array(
			'id' => FILTER_SANITIZE_NUMBER_INT,
		);
		$formVals = filter_var_array($_REQUEST, $args);

		$ret = B_DB::instance('BaseWarMapData')->delete($formVals['id']);

		$out = array('succ' => $ret ? 1 : 0, 'info' => '');
		B_Common::outXml($out);
	}

	/**
	 * 获取战斗列表
	 * @author huwei on 20110616
	 */
	static public function AGetListWarMap() {
		$list = B_DB::instance('BaseWarMapData')->getAll();
		$row = false;

		foreach ($list as $val) {
			$row[] = array(
				'id' => $val['id'],
				'name' => $val['name'],
			);
		}
		$out = array('succ' => $row ? 1 : 0, 'info' => $row);
		B_Common::outXml($out);
	}

	/**
	 * 更新城内已占用坐标的ASCII字符串数据
	 * @author chenhui on 20110617
	 */
	static public function AUpdateCityMapBlock() {
		$ret = false;
		if (!empty($_REQUEST['id']) && !empty($_REQUEST['level']) && !empty($_REQUEST['block'])) {
			$ret = M_MapCity::updateCityMapBlock($_REQUEST['id'], $_REQUEST['level'], $_REQUEST['block']);
		}
		$out = array('succ' => $ret ? 1 : 0);
		B_Common::outXml($out);
	}

	/**
	 * 获取城内已占用坐标的ASCII字符串数据
	 * @author chenhui on 20110617
	 */
	static public function AGetCityMapBlock() {
		$str = '';
		if (!empty($_REQUEST['id']) && !empty($_REQUEST['level'])) {
			$level = 1; //$_REQUEST['level']
			$str = M_MapCity::getCityMapBlockById($_REQUEST['id'], $level);
		}

		$out = array('succ' => 1, 'info' => $str);
		B_Common::outXml($out);
	}

	/**
	 * 地图编辑器:地貌数据操作
	 * @author chenhui on 20110923
	 * @param int $zone 洲编号
	 * @param array $arrScenicData 地貌数据 2D
	 */
	static public function opScenicData($zone, $arrScenicData) {
		$ret = false;
		$zone = intval($zone);
		if (array_key_exists($zone, T_App::$map) && !empty($arrScenicData) && is_array($arrScenicData)) {
			foreach ($arrScenicData as $scenicData) {
				if (is_array($scenicData)) {
					if ('C' == strtoupper($scenicData[1])) //插入地貌数据
					{
						$ps = explode('_', $scenicData[3]);
						$pe = explode('_', $scenicData[4]);
						$info = array(
							'id' => $scenicData[0],
							'scenic_id' => $scenicData[2],
							'zone' => $zone,
							'pos_sx' => $ps[0],
							'pos_sy' => $ps[1],
							'pos_ex' => $pe[0],
							'pos_ey' => $pe[1],
							'zoom' => $scenicData[5],
						);
						$ret = $ret && M_MapWild::insertScenicInfo($info);
					} else if ('U' == strtoupper($scenicData[1])) //更新地貌数据
					{
						$ps = explode('_', $scenicData[3]);
						$pe = explode('_', $scenicData[4]);
						$updinfo = array(
							'scenic_id' => $scenicData[2],
							'zone' => $zone,
							'pos_sx' => $ps[0],
							'pos_sy' => $ps[1],
							'pos_ex' => $pe[0],
							'pos_ey' => $pe[1],
							'zoom' => $scenicData[5],
						);
						$ret = $ret && M_MapWild::updateScenicInfo($scenicData[0], $updinfo);
					} else if ('D' == strtoupper($scenicData[1])) //删除地貌数据
					{
						$ret = $ret && M_MapWild::deleteScenicInfo($scenicData[0]);
					}
				}
			}
		}
		$out = array('succ' => $ret ? 1 : 0);
		B_Common::outXml($out);
	}

	/**
	 * 地图编辑器:标记点操作(只有更新)
	 * @author chenhui on 20110924
	 * @param int $zone 洲编号
	 * @param array $arrFlagData 标记点新数据 2D
	 * @return array array('succ'=>0/1)
	 */
	static public function opFlagData($zone, $arrFlagData) {
		$ret = false;
		$zone = intval($zone);
		if (array_key_exists($zone, T_App::$map) && !empty($arrFlagData) && is_array($arrFlagData)) {
			foreach ($arrFlagData as $flagData) {
				if (is_array($flagData) &&
					array_key_exists($flagData[1], T_Map::$WildMapCellType) &&
					('0' == $flagData[2] || array_key_exists($flagData[2], T_App::$scenicType))
				) {
					$pos = explode('_', $flagData[0]);
					$mapInfo = array(
						'pos_area' => $zone,
						'pos_x' => $pos[0],
						'pos_y' => $pos[1],
						'pos_no' => M_MapWild::calcWildMapPosNoByXY($zone, $pos[0], $pos[1]),
						'type' => $flagData[1],
						'scene_type' => $flagData[2],
					);
					$ret = $ret && M_MapWild::updateMapzoneInfo($mapInfo);
				}
			}
		}
		$out = array('succ' => $ret ? 1 : 0);
		B_Common::outXml($out);
	}


}

?>
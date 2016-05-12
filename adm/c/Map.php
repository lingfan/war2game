<?php

class C_Map {
	static $WarMapHeader = array(
		'id' => 'ID',
		'name' => '地图名称',
		'area_x' => '区域X',
		'area_y' => '区域Y',
		'cell_data' => '坐标类型',
		'secne_data' => '背景数据',
		'bg_no' => '背景编号',
		'create_at' => '时间',
		'sort' => '排序',
		'del' => '是否删除[1删除,0未删除]',
	);

	static public function AInit() {
		if (!M_Adm::isLogin()) {
			B_Common::redirect('?r=Index/Login');
		}
	}

	static public function AIndex() {
		B_View::render('index');
	}

	static public function AWarMapEditor() {
		B_View::render('Map/WarMapEditor');
	}

	static public function ACityInMapEdiotr() {
		B_View::render('Map/CityInMapEdiotr');
	}

	static public function ABuildEditor() {

		$list = B_DB::instance('BaseBuild')->all();

		$info = '';
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 1;
		$zhou = isset($_REQUEST['zhou']) ? $_REQUEST['zhou'] : 1;

		foreach ($list as $val) {
			if ($id == $val['id']) {
				$info = $val;
			}
		}

		$pageData['list'] = $list;
		$pageData['info'] = $info;
		$pageData['zhou'] = $zhou;

		B_View::setVal('pageData', $pageData);
		B_View::render('Map/BuildEditor');
	}

	static public function AWarMapCellList() {
		$args = array(
			'page' => FILTER_SANITIZE_NUMBER_INT,
		);
		$formVals = filter_var_array($_REQUEST, $args);
		$offset = 100;
		$curPage = max(1, $formVals['page']);
		$start = ($curPage - 1) * $offset;
		$pageData['list'] = B_DB::instance('BaseWarMapCell')->getList($start, $offset);
		$totalNum = B_DB::instance('BaseWarMapCell')->count();
		$pageData['page'] = B_Page::make($curPage, $totalNum, $offset);

		B_View::setVal('pageData', $pageData);
		B_View::render('Map/CellList');
	}

	static public function AWarMapCellView() {
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		if ($id > 0) {
			$pageData['info'] = B_DB::instance('BaseWarMapCell')->get($id);
			B_View::setVal('pageData', $pageData);
		}
		B_View::render('Map/CellView');
	}

	static public function AWarMapCellEdit() {
		$id = intval($_REQUEST['id']);

		$data['name'] = trim($_REQUEST['name']);
		$data['face_id'] = intval($_REQUEST['face_id']);
		$ban = $_REQUEST['ban'];

		$data['ban'] = 0;
		if (is_array($ban) && isset($ban[0])) {
			$data['ban'] = array_sum($ban);
		}
		$data['type'] = intval($_REQUEST['type']);
		$data['life_value'] = intval($_REQUEST['life_value']);
		$data['att_land'] = intval($_REQUEST['att_land']);
		$data['att_sky'] = intval($_REQUEST['att_sky']);
		$data['att_ocean'] = intval($_REQUEST['att_ocean']);
		$data['def_land'] = intval($_REQUEST['def_land']);
		$data['def_sky'] = intval($_REQUEST['def_sky']);
		$data['def_ocean'] = intval($_REQUEST['def_ocean']);
		$data['shot_range'] = intval($_REQUEST['shot_range']);
		$data['view_range'] = intval($_REQUEST['view_range']);
		$data['move_range'] = intval($_REQUEST['move_range']);
		$data['sort'] = intval($_REQUEST['sort']);
		if ($data['name'] == '') {
			echo "<script>alert('名称不能为空！');</script>";
			exit;
		}
		M_MapBattle::cleanBaseWarMapCell();
		if ($id > 0) {
			//编辑
			$data['id'] = $id;
			$result = B_DB::instance('BaseWarMapCell')->update($data, $data['id']);
			if ($result) {
				echo "<script>alert('修改成功！');</script>";
			} else {
				echo "<script>alert('修改失败！');</script>";
			}
		} else {
			$data['create_at'] = time();
			$result = B_DB::instance('BaseWarMapCell')->insert($data);
			if ($result) {
				echo "<script>alert('添加成功！');</script>";
			} else {
				echo "<script>alert('添加失败！');</script>";
			}
		}

	}

	static public function AWarMapCellDel() {
		$flag = 0;
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if ($id > 0) {
			if ($id > 2) {
				$res = B_DB::instance('BaseWarMapCell')->delete($id);
				if ($res) {
					$msg = '删除成功';
					$flag = 1;
				} else {
					$msg = '删除失败';
				}
			} else {
				$msg = '该标记物不能删除';
			}
		}
		M_MapBattle::cleanBaseWarMapCell();
		$json['flag'] = $flag;
		$json['msg'] = $msg;
		echo json_encode($json);
	}

	static public function AWarMapSecneList() {

		$baseCfg = M_Config::getVal();
		$resUrl = M_Config::getSvrCfg('server_res_url');
		$confFile = $resUrl . 'conf/war_map_secne.xml';
		$arr = B_Common::parseXml($confFile);

		if (isset($arr['row']) && !empty($arr['row'])) {
			foreach ($arr['row'] as $val) {
				$secneArr[] = array(
					'id' => $val['id'],
					'name' => $val['name'],
					'face_id' => $resUrl . 'imgs/war_map_secne/' . $val['face_id'] . '.png',
				);
			}
		}

		$pageData['list'] = $secneArr;
		$pageData['type'] = 'war';

		B_View::setVal('pageData', $pageData);
		B_View::render('Map/SecneList');
	}

	static public function AWorldMapSecneList() {
		$baseCfg = M_Config::getVal();
		$resUrl = M_Config::getSvrCfg('server_res_url');
		$confFile = $resUrl . 'conf/world_map_secne.xml';
		$arr = B_Common::parseXml($confFile);

		if (isset($arr['row']) && !empty($arr['row'])) {
			foreach ($arr['row'] as $val) {
				$secneArr[] = array(
					'id' => $val['id'],
					'name' => $val['name'],
					'face_id' => $resUrl . 'imgs/world_map_secne/' . $val['face_id'] . '.png',
				);
			}
		}

		$pageData['list'] = $secneArr;
		$pageData['type'] = 'world';
		B_View::setVal('pageData', $pageData);
		B_View::render('Map/SecneList');
	}

	static public function AWarMapImport() {
		$tip = '';
		if (!empty($_POST)) {
			$headerArr = array_keys(self::$WarMapHeader);

			$tmp = range('A', 'Z');
			$range = $tmp;
			foreach ($tmp as $val) {
				$range[] = 'A' . $val;
			}
			foreach ($tmp as $val) {
				$range[] = 'B' . $val;
			}

			if (!empty($_FILES['csvfile']['tmp_name'])) {
				require_once ADM_PATH . '/lib/PHPExcel.php';
				$file = $_FILES["csvfile"]['tmp_name'];

				$objReader = new PHPExcel_Reader_Excel5();
				$objReader->setReadDataOnly(true);
				$objPHPExcel = $objReader->load($file);

				$currentSheet = $objPHPExcel->getSheet(0);
				/**取得最大的列号*/
				$allColumn = $currentSheet->getHighestColumn();
				/**取得一共有多少行*/
				$allRow = $currentSheet->getHighestRow();
				/**从第二行开始输出，因为excel表中第一行为列名*/
				$arr = array();
				$allColumn = count(self::$WarMapHeader);

				for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
					/**从第A列开始输出*/
					$tmp = array();
					$n = 0;

					for ($currentColumn = 0; $currentColumn < $allColumn; $currentColumn++) {
						$currentColumnT = $range[$currentColumn];
						$address = $currentColumnT . $currentRow;
						$key = $headerArr[$n];
						$v = $currentSheet->getCell($address)->getValue();

						$tmp[$key] = $v;
						$n++;
					}

					$info = $tmp;
					$isDel = false;
					if (isset($info['del'])) {
						if ($info['del'] == 1) {
							$isDel = true;
						}
					}
					unset($info['del']);
					if ($info && isset($info['id']) && $info['id'] > 0) {
						if (B_DB::instance('BaseWarMapData')->get($info['id'])) {
							if ($isDel) {
								$ret = B_DB::instance('BaseWarMapData')->delete($info['id']);
								$tip[$info['id']] = $ret ? '删除成功' : '删除失败';
							} else {
								$ret = B_DB::instance('BaseWarMapData')->update($info, $info['id']);
								$tip[$info['id']] = $ret ? '更新成功' : '更新失败';
							}

						} else {
							$info['create_at'] = time();
							$ret = B_DB::instance('BaseWarMapData')->insert($info);
							$tip[$info['id']] = $ret ? '插入成功' : '插入失败';
						}
					}

				}
			}

		}
		$offset = 100;
		$num = B_DB::instance('BaseWarMapData')->count();
		$pageData['page'] = ceil($num / $offset);

		$pageData['tip'] = $tip;
		B_View::setVal('pageData', $pageData);
		B_View::render('Map/WarMapImport');

	}

	static public function AWarMapExport() {

		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		$tmp = range('A', 'Z');
		$range = $tmp;
		foreach ($tmp as $val) {
			$range[] = 'A' . $val;
		}
		foreach ($tmp as $val) {
			$range[] = 'B' . $val;
		}
		$offset = 100;
		$p = isset($_GET['p']) ? $_GET['p'] : 1;

		require_once ADM_PATH . '/lib/PHPExcel.php';

		$start = ($p - 1) * $offset;
		$rows = B_DB::instance('BaseWarMapData')->getList($start, $offset);

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		$header = self::$WarMapHeader;
		$obj = $objPHPExcel->setActiveSheetIndex(0);

		$i = 0;
		foreach ($header as $k => $val) {
			$prefix = $range[$i];
			$obj->setCellValue($prefix . '1', $val);
			$i++;
		}

		$no = 2;
		foreach ($rows as $vals) {
			$vData = $vals;

			$i = 0;
			foreach ($header as $k => $line) {
				$prefix = $range[$i];
				$obj->setCellValue($prefix . $no, $vData[$k]);
				$i++;
			}
			$objPHPExcel->getActiveSheet()->getStyle('F' . $no)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT); //设置单元格的时间格式样式
			$no++;
		}


		// Redirect output to a client’s web browser (Excel5)
		$filename = 'base_war_map_' . date('YmdHis') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
}

?>
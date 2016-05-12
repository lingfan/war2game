<?php

class C_War {
	static $FBheader = array(
		'id' => 'ID',
		'name' => '战役名称',
		'desc' => '描述',
		'checkpoint_data1' => '关卡数据1',
		'checkpoint_text1' => '关卡对话1',
		'checkpoint_desc1' => '关卡描述1',
		'checkpoint_data2' => '关卡2',
		'checkpoint_text2' => '关卡对话2',
		'checkpoint_desc2' => '关卡描述2',
		'checkpoint_data3' => '关卡3',
		'checkpoint_text3' => '关卡对话3',
		'checkpoint_desc3' => '关卡描述3',
		'checkpoint_data4' => '关卡4',
		'checkpoint_text4' => '关卡对话4',
		'checkpoint_desc4' => '关卡描述4',
		'checkpoint_data5' => '关卡5',
		'checkpoint_text5' => '关卡对话5',
		'checkpoint_desc5' => '关卡描述5',
		'checkpoint_data6' => '关卡6',
		'checkpoint_text6' => '关卡对话6',
		'checkpoint_desc6' => '关卡描述6',
		'checkpoint_data7' => '关卡7',
		'checkpoint_text7' => '关卡对话7',
		'checkpoint_desc7' => '关卡描述7',
		'checkpoint_data8' => '关卡8',
		'checkpoint_text8' => '关卡对话8',
		'checkpoint_desc8' => '关卡描述8',
		'checkpoint_data9' => '关卡9',
		'checkpoint_text9' => '关卡对话9',
		'checkpoint_desc9' => '关卡描述9',
		'checkpoint_data10' => '关卡10',
		'checkpoint_text10' => '关卡对话10',
		'checkpoint_desc10' => '关卡描述10',
		'checkpoint_data11' => '关卡11',
		'checkpoint_text11' => '关卡对话11',
		'checkpoint_desc11' => '关卡描述11',
		'checkpoint_data12' => '关卡12',
		'checkpoint_text12' => '关卡对话12',
		'checkpoint_desc12' => '关卡描述12',
	);
	static $NpcHeroHeader = array(
		'id' => 'ID',
		'type' => '类型',
		'nickname' => '军官名称',
		'level' => '部队等级',
		'face_id' => '部队图像',
		'gender' => '性别',
		'quality' => '品质',
		'is_legend' => '是否传奇',
		'attr_lead' => '防御',
		'attr_command' => '攻击',
		'attr_military' => '生命',
		'attr_energy' => '精力',
		'attr_mood' => '情绪',
		'equip_arm' => '武器',
		'equip_cap' => '军帽',
		'equip_uniform' => '军服',
		'equip_medal' => '勋章',
		'equip_shoes' => '军鞋',
		'equip_sit' => '座驾',
		//'skill_slot_num' 	=>'技能槽数量',
		//'skill_slot' 		=>'天赋技能槽',
		//'skill_slot_1' 	=>'技能槽1',
		//'skill_slot_2' 	=>'技能槽2',
		'army_id' => '军官配备兵种',
		'army_lv' => '兵种等级',
		'army_num' => '兵种数量',
		'weapon_id' => '武器',
		'del' => '是否删除[1删除,0未删除]',
	);

	static $NpcHeader = array(
		'id' => 'ID',
		'nickname' => '部队名称',
		'face_id' => '部队图像',
		'level' => '部队等级',
		'type' => '部队类型',
		'army_data' => '部队军官列表',
		'probe_cost_data' => '探索消耗数据',
		'probe_event_data' => '探索事件',
		'res_data' => '资源属地数据',
		'award_id' => '奖励ID',
		'exp_num' => '经验值',
		'hero_num' => '军官数量',
		'feature' => '部队描述',
		'del' => '是否删除[1删除,0未删除]',
	);


	static public function AInit() {
		if (!M_Adm::isLogin()) {
			B_Common::redirect('?r=Index/Login');
		}
	}

	static public function AIndex() {
		B_Common::redirect('?r=War/WarFbCateList');
	}

	static public function AMapFBEdit() {

	}


	/**
	 * 副本战役列表编号修改
	 */
	static public function AWarFbListEdit() {
		$ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : array();
		$campaign_no = isset($_REQUEST['campaign_no']) ? $_REQUEST['campaign_no'] : array();
		$tempArr = array_unique($campaign_no);

		if (count($ids) == count($campaign_no) && count($ids) == count($tempArr)) {
			foreach ($campaign_no as $val) {
				if ($val < 1 || $val > count($campaign_no)) {
					echo "<script>alert('战役编号从1开始，且不能有间断！');</script>";
					exit;
				}
			}
		} else {
			echo "<script>alert('请填写非重复的战役编号！');</script>";
			exit;
		}
		$resNum = 0;
		for ($i = 0; $i < count($ids); $i++) {
			$fieldArr = array(
				'id' => $ids[$i],
				'campaign_no' => $campaign_no[$i]
			);
			$res = B_DB::instance('BaseWarFB')->update($fieldArr, $fieldArr['id']);
			if ($res) {
				$resNum = $resNum + 1;
			}
			unset($fieldArr);
		}

		if ($resNum == count($ids)) {
			echo "<script>alert('保存成功！');</script>";
			exit;
		} else {
			echo "<script>alert('保存失败！');</script>";
			exit;
		}
	}

	/**
	 * 战斗副本详细
	 */
	static public function AWarFbView() {
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		$cate = isset($_REQUEST['cate']) ? $_REQUEST['cate'] : 0;
		$pageData['cate'] = $cate;
		$pageData['maps'] = B_DB::instance('BaseWarMapData')->getAll();
		$npcs = B_DB::instance('BaseNpcTroop')->all();
		M_SoloFB::totalChapter(true);
		foreach ($npcs as $val) {
			if ($val['type'] == M_NPC::FB_NPC) {
				$pageData['npcs'][] = $val;
			}
		}
		$pageData['cates'] = B_DB::instance('BaseWarFbChapter')->getAll();
		if ($id > 0) {
			$pageData['info'] = B_DB::instance('BaseWarFB')->get($id);
		}
		B_View::setVal('pageData', $pageData);
		B_View::render('War/FbView');
	}

	/**
	 * 战斗副本编辑/添加操作
	 */
	static public function AWarFbEdit() {
		$id = intval($_REQUEST['id']);

		$data['name'] = trim($_REQUEST['name']);
		$data['level'] = intval($_REQUEST['level']);
		$data['chapter_no'] = intval($_REQUEST['type']);
		$data['campaign_no'] = intval($_REQUEST['campaign_no']);
		$data['desc'] = trim($_REQUEST['desc']);
		$data['award'] = trim($_REQUEST['award']);
		//$data['checkpoint_num'] = intval($_REQUEST['checkpoint_num']);
		$gnames = $_REQUEST['gname'];
		$dixing = $_REQUEST['dixing'];
		$tianqi = $_REQUEST['tianqi'];
		$ditu = $_REQUEST['ditu'];
		$npc = $_REQUEST['npc'];
		$donghua = $_REQUEST['donghua'];
		$duihua = $_REQUEST['duihua'];
		$gq_desc = $_REQUEST['gq_desc'];

		//$aa = explode('\n', $duihua[0]);
		//$duihua[0] = explode("\r\n", $duihua[0]);
		//var_dump($aa);
		//exit;


		if ($data['name'] == '') {
			echo "<script>alert('名称不能为空！');</script>";
			exit;
		}
		$rows = array();
		for ($i = 0; $i < count($gnames); $i++) {
			if ($gnames[$i] == '' || $dixing[$i] < 1 || $tianqi[$i] < 1 || $ditu[$i] < 1 || $npc[$i] < 1) {
				echo "<script>alert('数据错误！');</script>";
				exit;
			}
			//$duihua[$i] = explode("\r\n", $duihua[$i]);
			$duihua[$i] = explode("\n", $duihua[$i]);
			if (isset($duihua[$i][0])) {
				foreach ($duihua[$i] as $key => $val) {
					if (!$val) {
						unset($duihua[$i][$key]);
					}
				}
			}
			$rows[$i + 1] = array($gnames[$i], $dixing[$i], $tianqi[$i], $ditu[$i], $npc[$i], $donghua[$i], $duihua[$i], $gq_desc[$i]);
		}

		$data['checkpoint_data'] = json_encode($rows);
		if ($id > 0) {
			//编辑
			$result = B_DB::instance('BaseWarFB')->update($data, $id);
		} else {
			$result = B_DB::instance('BaseWarFB')->insert($data);
		}

		echo $result ? "<script>alert('保存成功！');</script>" : "<script>alert('保存失败！');</script>";
	}

	static public function AFBCleanCache() {
		APC::del(T_Key::WAR_FB_CHAPTER);
		echo "<script>";
		echo "alert('更新成功');";
		echo "</script>";
	}

	/**
	 * 战斗副本删除操作
	 */
	static public function AWarFbDel() {
		$flag = 0;
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if ($id > 0) {
			$res = B_DB::instance('BaseWarFB')->delete($id);
			if ($res) {
				$msg = '删除成功';
				$flag = 1;
			} else {
				$msg = '删除失败';
			}
		}
		$json['flag'] = $flag;
		$json['msg'] = $msg;
		echo json_encode($json);
	}

	/**
	 * 副本章节列表
	 */
	static public function AWarFbCateList() {
		$pageData['list'] = B_DB::instance('BaseWarFbChapter')->getAll();

		B_View::setVal('pageData', $pageData);
		B_View::render('War/FbCateList');
	}

	/**
	 * 副本章节详细
	 */
	static public function AWarFbCateView() {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if ($id > 0) {
			$pageData['info'] = B_DB::instance('BaseWarFbChapter')->get($id);
			if ($pageData['info']) {
				$cates = B_DB::instance('BaseWarFB')->getAll();
				foreach ($cates as $key => $val) {
					$pageData['cates'][$val['id']] = $val;
				}
				$pageData['list'] = B_DB::instance('BaseWarFB')->getListByChapter($pageData['info']['id']);

				B_View::setVal('pageData', $pageData);
			}
		}

		B_View::render('War/FbCateView');
	}

	/**
	 * 副本地图
	 */
	static public function AWarFbMapView() {
		$chapterNo = isset($_REQUEST['chapter']) ? intval($_REQUEST['chapter']) : 0;
		$campaignNo = isset($_REQUEST['campaign']) ? intval($_REQUEST['campaign']) : 0;
		$chapter = B_DB::instance('BaseWarFbChapter')->get($chapterNo);
		$campaign = B_DB::instance('BaseWarFB')->getInfoByCC($chapterNo, $campaignNo);
		//print_r($campaign);exit;

		$pageData['chapter'] = $chapter; //章节信息
		$pageData['campaign'] = $campaign; //战役信息

		B_View::setVal('pageData', $pageData);
		B_View::render('War/FbMapView');
	}

	static public function AGetGuanqiaId() {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$map_id = isset($_REQUEST['map_id']) ? trim($_REQUEST['map_id']) : '';
		$campaign = B_DB::instance('BaseWarFB')->get($id);
		$mapShow = json_decode($campaign['map_show'], true);
		echo isset($mapShow[$map_id]) ? $mapShow[$map_id] : '';
	}

	/**
	 * 副本地图编辑
	 */
	static public function AWarFbMapEdit() {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0; //副本（战役）编号
		$mapMark = isset($_REQUEST['map_mark']) ? trim($_REQUEST['map_mark']) : ''; //副本地图标记物ID
		$guanqia = isset($_REQUEST['guanqia']) ? intval($_REQUEST['guanqia']) : 0; //关卡ID

		$campaign = B_DB::instance('BaseWarFB')->get($id);
		$mapShow = json_decode($campaign['map_show'], true);
		$mapShow[$mapMark] = $guanqia;

		$fieldArr = array(
			'map_show' => json_encode($mapShow)
		);
		$res = B_DB::instance('BaseWarFB')->update($fieldArr, $id);
		echo $res ? "<script>alert('保存成功！');</script>" : "<script>alert('保存失败！');</script>";
	}


	/**
	 * 副本章节添加/编辑操作
	 */
	static public function AWarFbCateEdit() {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$data['name'] = $_REQUEST['name'];
		$data['desc'] = $_REQUEST['desc'];

		if ($id > 0) {
			$res = B_DB::instance('BaseWarFbChapter')->update($data, $id);
		} else {
			$res = B_DB::instance('BaseWarFbChapter')->insert($data);
		}

		echo $res ? "<script>alert('保存成功！');</script>" : "<script>alert('保存失败！');</script>";
	}

	/**
	 * 副本章节删除操作
	 */
	static public function AWarFbCateDel() {
		$flag = 0;
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if ($id > 0) {
			$res = B_DB::instance('BaseWarFbChapter')->delete($id);
			if ($res) {
				$msg = '删除成功';
				$flag = 1;
			} else {
				$msg = '删除失败';
			}
		}
		$json['flag'] = $flag;
		$json['msg'] = $msg;
		echo json_encode($json);
	}

	/**
	 * NPC英雄列表
	 */
	static public function ANpcHeroList() {
		$args = array(
			'page' => FILTER_SANITIZE_NUMBER_INT,
			'nickname' => FILTER_SANITIZE_STRING,
			'quality' => FILTER_SANITIZE_NUMBER_INT,
		);
		$formVals = filter_var_array($_REQUEST, $args);
		$offset = 20;
		$curPage = max(1, $formVals['page']);
		$pageData['parms'] = array();
		if ($formVals['nickname'] != '') {
			$pageData['parms']['nickname'] = $formVals['nickname'];
		}
		if ($formVals['quality'] > 0) {
			$pageData['parms']['quality'] = $formVals['quality'];
		}


		$start = ($curPage - 1) * $offset;
		$pageData['list'] = B_DB::instance('BaseNpcHero')->getList($start, $offset, $pageData['parms']);
		$totalNum = B_DB::instance('BaseNpcHero')->count($pageData['parms']);
		$pageData['page'] = B_Page::make($curPage, $totalNum, $offset, 10);
		/** 装备 */
		$equipList = B_DB::instance('BaseEquipTpl')->getAll();

		$baseEquipTpl = array();
		foreach ($equipList as $key => $val) {
			$pageData['equipList'][$val['id']] = $val;
			//$pageData['equipList'][$val['id']]['name'] = $baseEquipTpl[$pageData['equipList'][$val['id']]['equip_id']]['name'];
		}
		/** 技能 */
		$skill_list = B_DB::instance('BaseSkill')->getAll();
		foreach ($skill_list as $key => $val) {
			$pageData['skill_list'][$val['id']] = $val;
		}

		/** 兵种武器 */
		$weaponList = B_DB::instance('BaseWeapon')->all();
		foreach ($weaponList as $key => $val) {
			$pageData['weaponList'][$val['id']] = $val;
		}


		/** 英雄字体颜色定义 */
		$pageData['color'] = array(
			'1' => 'white',
			'2' => 'green',
			'3' => 'blue',
			'4' => 'purple',
			'5' => 'blue',
			'6' => 'purple',
			'7' => 'red',
			'8' => 'orange',
		);
		$pageData['total'] = $totalNum;
		B_View::setVal('pageData', $pageData);
		B_View::render('War/NpcHeroList');
	}


	static public function ANpcHeroListImport() {
		if (!empty($_POST)) {
			$headerArr = array_keys(self::$NpcHeroHeader);

			$tmp = range('A', 'Z');
			$range = $tmp;
			foreach ($tmp as $val) {
				$range[] = 'A' . $val;
			}
			foreach ($tmp as $val) {
				$range[] = 'B' . $val;
			}

			if (!empty($_FILES['npcherocsvfile']['tmp_name'])) {
				require_once ADM_PATH . '/lib/PHPExcel.php';
				$file = $_FILES["npcherocsvfile"]['tmp_name'];

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
				$allColumn = count(self::$FBheader);

				for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
					/**从第A列开始输出*/
					$tmp = array();
					$n = 0;

					for ($currentColumn = 0; $currentColumn < $allColumn; $currentColumn++) {
						$currentColumnT = $range[$currentColumn];
						$address = $currentColumnT . $currentRow;
						$key = isset($headerArr[$n]) ? $headerArr[$n] : '';
						$v = $currentSheet->getCell($address)->getValue();


						if (!empty($key)) {
							if ($key == 'army_lv') {
								$val = !empty($v) ? intval($v) : 0;
							} else if ($key == 'gender') {
								if (!isset(T_App::$genderType[$v])) {
									$err[] = "无性别#{$v}";
									$val = 0;
								} else {
									$val = $v;
								}

							} else if ($key == 'quality') {
								if (!isset(T_Hero::$heroQual[$v])) {
									$err[] = "无品质ID#{$v}";
									$val = 0;
								} else {
									$val = $v;
								}
								/*
								 $arr = array_flip(T_Hero::$heroQual);
								if (!isset($arr[$v]))
								{
								$err[] = "无品质ID#{$v}";
								$val = 0;
								}
								else
								{
								$val = $arr[$v];
								}
								*/
							} else if ($key == 'is_legend') {
								//$val = $v == '传奇' ? 1 : 0;
								$val = $v;
							} else if ($key == 'army_id') {
								if (!isset(M_Army::$type[$v])) {
									$err[] = "无兵种ID#{$v}";
									$val = 0;
								} else {
									$val = $v;
								}
								/*
								 $arr = array_flip(M_Army::$type);
								if (!isset($arr[$v]))
								{
								$err[] = "无兵种ID#{$v}";
								$val = 0;
								}
								else
								{
								$val = $arr[$v];
								}
								*/
							} else if ($key == 'weapon_id') {
								$weaponInfo = B_DB::instance('BaseWeapon')->get($v);
								if (empty($weaponInfo)) {
									$err[] = "无武器ID#{$v}";
									$val = 0;
								} else {
									$val = $weaponInfo['id'];
								}

								/*
								 $weaponInfo = B_DB::instance('BaseWeapon')->getInfoByName($v);
								if (empty($weaponInfo))
								{
								$err[] = "无武器ID#{$v}";
								$val = 0;
								}
								else
								{
								$val = $weaponInfo['id'];
								}
								*/

							} else {
								$val = $v;
							}

							$tmp[$key] = $val;

						}
						$n++;
					}

					if (empty($tmp['id'])) {
						echo 'ID不能为空';
						exit;
					}

					if (!empty($err)) {
						print_r($err);
						exit;
					}

					$isDel = false;
					if (isset($tmp['del'])) {
						if ($tmp['del'] == 1) {
							$isDel = true;
						}
					}
					unset($tmp['del']);
					if (!empty($tmp['nickname']) && !empty($tmp['id'])) {
						if (empty($tmp['army_num'])) {
							$armyInfo = M_Army::baseInfo($tmp['army_id']);
							$tmp['army_num'] = floor($tmp['level'] * (60 * 1 / $armyInfo['cost_people'])); //计算各兵种最大带兵数
						}

						if (B_DB::instance('BaseNpcHero')->get($tmp['id'])) {
							if ($isDel) {
								$ret = B_DB::instance('BaseNpcHero')->delete($tmp['id']);
								$tip[$tmp['id']] = $ret ? '删除成功' : '删除失败';
							} else {
								$ret = B_DB::instance('BaseNpcHero')->update($tmp, $tmp['id']);
								$tip[$tmp['id']] = $ret ? '更新成功' : '更新失败';
							}
						} else {
							$ret = B_DB::instance('BaseNpcHero')->insert($tmp);
							$tip[$tmp['id']] = $ret ? '插入成功' : '插入失败';
						}

						$apcKey = T_Key::BASE_NPC_HERO . $tmp['id'];
						APC::del($apcKey); //删除内存缓存
					} else {
						$tip[$tmp['id']] = "错误数据";
					}

				}

				$pageData['tip'] = $tip;
			}
		}
		$pageData['act'] = 'NpcHeroListImport';
		B_View::setVal('pageData', $pageData);
		B_View::render('War/NpcImport');
	}


	static public function ANpcHeroListExport() {
		require_once ADM_PATH . '/lib/PHPExcel.php';
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

		$where = $limit = '';
		if (isset($_GET['t'])) {
			$type = $_GET['t'];
			$where = " WHERE `type`='{$type}' ";
		}

		if (isset($_GET['p'])) {
			$p = $_GET['p'];
			$offset = isset($_GET['offset']) ? $_GET['offset'] : 1000;
			$start = ($p - 1) * $offset;
			$limit = " LIMIT {$start}, {$offset}";
		}

		$sql = "SELECT * FROM base_npc_hero {$where} ORDER BY id {$limit}";
		$rows = B_DB::instance('BaseNpcHero')->fetchAll($sql);


		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		$header = self::$NpcHeroHeader;
		$obj = $objPHPExcel->setActiveSheetIndex(0);

		$i = 0;
		unset($header['del']);
		foreach ($header as $k => $val) {
			$prefix = $range[$i];
			$obj->setCellValue($prefix . '1', $val);
			$i++;
		}

		$no = 2;
		foreach ($rows as $vals) {

			$vData = $vals;
			$vData['quality'] = $vals['quality'];
			$vData['gender'] = $vals['gender'];
			$vData['is_legend'] = $vals['is_legend'];
			$vData['create_at'] = !empty($vals['create_at']) ? date('Y-m-d H:i:s', $vals['create_at']) : '';
			//$vData['army_id'] = isset(M_Army::$type[$vals['army_id']])?M_Army::$type[$vals['army_id']]:'';
			$vData['army_id'] = $vals['army_id'];

			$vData['army_lv'] = !empty($vals['army_lv']) ? intval($vals['army_lv']) : '0';
			$vData['weapon_id'] = $vals['weapon_id'];


			//$newId = T_Weapon::$id2id[$vData['weapon_id']];
			//B_DB::instance('BaseNpcHero')->update(array('weapon_id'=>$newId), $vals['id']);

			/**
			 * $weaponName = '';
			 * if (!empty($vals['weapon_id']))
			 * {
			 * $weaponInfo = B_DB::instance('BaseWeapon')->get($vals['weapon_id']);
			 * $weaponName = $weaponInfo['name'];
			 * }
			 * $vData['weapon_id'] = $weaponName;
			 **/


			$i = 0;
			foreach ($header as $k => $line) {
				$prefix = $range[$i];
				$obj->setCellValue($prefix . $no, $vData[$k]);
				$i++;
			}
			$no++;
		}


		// Redirect output to a client’s web browser (Excel5)
		$filename = 'base_npc_hero_' . date('YmdHis') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');


	}

	/**
	 * NPC英雄详细
	 */
	static public function ANpcHeroView() {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$pageData = array();
		if ($id > 0) {
			$info = B_DB::instance('BaseNpcHero')->get($id);
			if ($info) {
				$pageData['info'] = $info;
			}
		}
		/** 技能 */
		$pageData['skill_list'] = B_DB::instance('BaseSkill')->getAll();
		/** 装备 */
		$equipList = B_DB::instance('BaseEquipTpl')->getAll();
		foreach ($equipList as $key => $val) {

			$pageData['equipList'][$equipList[$key]['pos']][] = $equipList[$key];
		}
		/** 兵种武器 */
		$pageData['weaponList'] = B_DB::instance('BaseWeapon')->getNpcList();

		B_View::setVal('pageData', $pageData);
		B_View::render('War/NpcHeroView');
	}

	/**
	 * NPC英雄添加/编辑操作
	 */
	static public function ANpcHeroEdit() {
		$flag = 0;
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		$act = $_REQUEST['act'];
		$args = array(
			'nickname' => FILTER_SANITIZE_STRING,
			'gender' => FILTER_SANITIZE_NUMBER_INT,
			'quality' => FILTER_SANITIZE_NUMBER_INT,
			'face_id' => FILTER_SANITIZE_NUMBER_INT,
			'level' => FILTER_SANITIZE_NUMBER_INT,
			'attr_lead' => FILTER_SANITIZE_NUMBER_INT,
			'attr_command' => FILTER_SANITIZE_NUMBER_INT,
			'attr_military' => FILTER_SANITIZE_NUMBER_INT,
			'attr_energy' => FILTER_SANITIZE_NUMBER_INT,
			'attr_mood' => FILTER_SANITIZE_NUMBER_INT,

			'equip_arm' => FILTER_SANITIZE_NUMBER_INT,
			'equip_cap' => FILTER_SANITIZE_NUMBER_INT,
			'equip_uniform' => FILTER_SANITIZE_NUMBER_INT,
			'equip_medal' => FILTER_SANITIZE_NUMBER_INT,
			'equip_shoes' => FILTER_SANITIZE_NUMBER_INT,
			'equip_sit' => FILTER_SANITIZE_NUMBER_INT,

			'skill_slot_num' => FILTER_SANITIZE_NUMBER_INT,
			'skill_slot' => FILTER_SANITIZE_NUMBER_INT,
			'skill_slot_1' => FILTER_SANITIZE_NUMBER_INT,
			'skill_slot_2' => FILTER_SANITIZE_NUMBER_INT,

			'army_id' => FILTER_SANITIZE_NUMBER_INT,
			'army_lv' => FILTER_SANITIZE_NUMBER_INT,
			'army_num' => FILTER_SANITIZE_NUMBER_INT,
			'weapon_id' => FILTER_SANITIZE_NUMBER_INT,
		);
		$data = filter_var_array($_REQUEST, $args);
		if ($act == 'add') {
			$data['create_at'] = time();
			$res = B_DB::instance('BaseNpcTroop')->insert($data);
			if ($res) {
				$msg = '添加成功';
				$flag = 1;
			} else {
				$msg = '添加失败';
			}
		} elseif ($act == 'edit') {
			$res = B_DB::instance('BaseNpcTroop')->update($data, $id);
			if ($res) {
				$msg = '修改成功';
				$flag = 1;

			} else {
				$msg = '修改失败';
			}
		} else {
			$msg = '错误操作';
		}

		$json['flag'] = $flag;
		$json['msg'] = $msg;
		echo json_encode($json);
	}

	/**
	 * NPC英雄删除操作
	 */
	static public function ANpcHeroDel() {
		$flag = 0;
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if ($id > 0) {
			$res = B_DB::instance('BaseNpcTroop')->delete($id);
			if ($res) {
				$msg = '删除成功';
				$flag = 1;
			} else {
				$msg = '删除失败';
			}
		}
		$json['flag'] = $flag;
		$json['msg'] = $msg;
		echo json_encode($json);
	}

	static public function ANpcListImport() {
		$tip = '';
		if (!empty($_POST)) {
			$headerArr = array_keys(self::$NpcHeader);

			$tmp = range('A', 'Z');
			$range = $tmp;
			foreach ($tmp as $val) {
				$range[] = 'A' . $val;
			}
			foreach ($tmp as $val) {
				$range[] = 'B' . $val;
			}

			if (!empty($_FILES['npccsvfile']['tmp_name'])) {
				require_once ADM_PATH . '/lib/PHPExcel.php';
				$file = $_FILES["npccsvfile"]['tmp_name'];

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
				$allColumn = count(self::$NpcHeader);

				for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
					/**从第A列开始输出*/
					$tmp = array();
					$n = 0;

					for ($currentColumn = 0; $currentColumn < $allColumn; $currentColumn++) {
						$currentColumnT = $range[$currentColumn];
						$address = $currentColumnT . $currentRow;
						$key = $headerArr[$n];
						$v = $currentSheet->getCell($address)->getValue();

						if ($key == 'type') {
							//$arr = array_flip(M_NPC::$NpcType);
							$arr = M_NPC::$NpcType;
							//if(in_array($v, $arr))
							if (isset($arr[$v])) {
								//$v = $arr[$v];
								//$v = $v;
							} else {
								$tip[] = '类型错误' . '----' . $v;
								break;
							}
						} else if ($key == 'army_data') {
							$heroList = explode(',', $v);
							$tmpArr = array();
							foreach ($heroList as $id) {
								$tmpArr[] = (int)$id;
							}
							$unique_arr = array_unique($tmpArr);
							if (count($tmpArr) != count($unique_arr)) {
								echo "<script>alert('ID为" . $currentSheet->getCell($range[0] . $currentRow)->getValue() . "的部队军队列表" . $v . "出现了重复军官ID');</script>";
								exit;
							}
							$v = json_encode($tmpArr);
						}
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
						$info['award_id'] = isset($info['award_id']) ? $info['award_id'] : 0;
						if (B_DB::instance('BaseNpcTroop')->get($info['id'])) {
							if ($isDel) {
								$ret = B_DB::instance('BaseNpcTroop')->delete($info['id']);
								$tip[$info['id']] = $ret ? '删除成功' : '删除失败';
							} else {
								$ret = B_DB::instance('BaseNpcTroop')->update($info, $info['id']);
								$tip[$info['id']] = $ret ? '更新成功' : '更新失败';
							}

						} else {
							$info['create_at'] = time();
							$ret = B_DB::instance('BaseNpcTroop')->insert($info);
							$tip[$info['id']] = $ret ? '插入成功' : '插入失败';
						}

						$apcKey = T_Key::BASE_NPC . $info['id'];
						APC::del($apcKey); //删除APC缓存
					}

				}
			}

		}
		$pageData['tip'] = $tip;
		$pageData['act'] = 'NpcListImport';
		B_View::setVal('pageData', $pageData);
		B_View::render('War/NpcImport');

	}

	static public function ANpcListExport() {

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
		$p = isset($_GET['p']) ? $_GET['p'] : 1;

		$t = isset($_GET['t']) ? $_GET['t'] : 0;
		require_once ADM_PATH . '/lib/PHPExcel.php';
		if (!empty($t)) {
			$sql = "SELECT * FROM base_npc_troop where `type` IN ({$t}) order by id asc ";
		} else {
			$sql = "SELECT * FROM base_npc_troop order by id asc";
		}

		$rows = B_DB::instance('BaseNpcTroop')->fetchAll($sql);

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		$header = self::$NpcHeader;
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
			$armyData = json_decode($vals['army_data'], true);
			$heroId = array();
			foreach ($armyData as $val) {
				$heroInfo = B_DB::instance('BaseNpcHero')->get($val);
				$heroId[] = $heroInfo['id'];
			}
			$heroId = implode(',', $heroId);
			$vData['army_data'] = $heroId;
			//$vData['type'] = M_NPC::$NpcType[$vals['type']];
			$vData['type'] = $vData['type'];

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
		$filename = 'base_npc_' . date('YmdHis') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	/**
	 * NPC部队列表
	 */
	static public function ANpcList() {
		$args = array(
			'page' => FILTER_SANITIZE_NUMBER_INT,
			'nickname' => FILTER_SANITIZE_STRING,
			'type' => FILTER_SANITIZE_NUMBER_INT,
		);

		$formVals = filter_var_array($_REQUEST, $args);
		$offset = 10;
		$curPage = max(1, $formVals['page']);
		$pageData['parms'] = array();
		if ($formVals['nickname'] != '') {
			$pageData['parms']['nickname'] = $formVals['nickname'];
		}
		if (empty($formVals['type'])) {
			$pageData['parms']['type'] = 7;
		} else {
			$pageData['parms']['type'] = $formVals['type'];
		}

		$start = ($curPage - 1) * $offset;
		$pageData['list'] = B_DB::instance('BaseNpcTroop')->getList($start, $offset, $pageData['parms']);
		$totalNum = B_DB::instance('BaseNpcTroop')->count($pageData['parms']);

		$pageData['page'] = B_Page::make($curPage, $totalNum, $offset, 10);
		$pageData['total'] = $totalNum;
		B_View::setVal('pageData', $pageData);
		B_View::render('War/NpcList');
	}

	static public function ADelNpcCache() {
		APC::del(T_Key::BASE_NPC);
		echo "<script>";
		echo "alert('更新成功');";
		echo "</script>";
	}

	static public function ADelNpcHeroCache() {
		APC::del(T_Key::BASE_NPC_HERO); //废弃
		echo "<script>";
		echo "alert('更新成功');";
		echo "</script>";

	}

	/**
	 * NPC部队列表
	 */
	static public function AWildNpcList() {
		$args = array(
			'page' => FILTER_SANITIZE_NUMBER_INT,
			'nickname' => FILTER_SANITIZE_STRING,
			'type' => FILTER_SANITIZE_NUMBER_INT,
		);

		$formVals = filter_var_array($_REQUEST, $args);
		$offset = 10000;
		$curPage = max(1, $formVals['page']);
		$pageData['parms'] = array();

		/** NPC英雄列表 */
		$hero_list = B_DB::instance('BaseNpcHero')->all();
		foreach ($hero_list as $key => $val) {
			$pageData['hero_list'][$val['id']] = $val;
		}
		/** 道具列表 */
		$props_list = B_DB::instance('BaseProps')->all();
		foreach ($props_list as $key => $val) {
			$pageData['props_list'][$val['id']] = $val;
		}
		/** 模板装备列表 */
		$equipList = B_DB::instance('BaseEquipTpl')->getAll();
		foreach ($equipList as $key => $val) {
			$pageData['equipList'][$val['id']] = $val;
		}
		$pageData['parms']['type'] = 1;

		$start = ($curPage - 1) * $offset;
		$arr1 = B_DB::instance('BaseNpcTroop')->getList($start, $offset, $pageData['parms']);
		$pageData['parms']['type'] = 2;
		$arr2 = B_DB::instance('BaseNpcTroop')->getList($start, $offset, $pageData['parms']);
		$pageData['parms']['type'] = 3;
		$arr3 = B_DB::instance('BaseNpcTroop')->getList($start, $offset, $pageData['parms']);
		$pageData['parms']['type'] = 4;
		$arr4 = B_DB::instance('BaseNpcTroop')->getList($start, $offset, $pageData['parms']);
		$pageData['parms']['type'] = 9;
		$arr9 = B_DB::instance('BaseNpcTroop')->getList($start, $offset, $pageData['parms']);
		$pageData['parms']['type'] = 10;
		$arr10 = B_DB::instance('BaseNpcTroop')->getList($start, $offset, $pageData['parms']);
		$pageData['parms']['type'] = 11;
		$arr11 = B_DB::instance('BaseNpcTroop')->getList($start, $offset, $pageData['parms']);
		$pageData['parms']['type'] = 12;
		$arr12 = B_DB::instance('BaseNpcTroop')->getList($start, $offset, $pageData['parms']);
		$pageData['list'] = array_merge($arr1, $arr2, $arr3, $arr4, $arr9, $arr10, $arr11, $arr12);
		$totalNum = B_DB::instance('BaseNpcTroop')->totalRows($pageData['parms']);
		$pageData['page'] = B_Page::make($curPage, $totalNum, $offset, 10);

		B_View::setVal('pageData', $pageData);
		B_View::render('War/NpcList');
	}

	/**
	 * NPC部队详细
	 */
	static public function ANpcView() {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$pageData = array();
		if ($id > 0) {
			$info = B_DB::instance('BaseNpcTroop')->get($id);
			if ($info) {
				$pageData['info'] = $info;
			}
		}
		/** NPC英雄列表 */
		$hero_list = B_DB::instance('BaseNpcHero')->all();
		foreach ($hero_list as $key => $val) {
			$pageData['hero_list'][$val['id']] = $val;
		}
		/** 道具列表 */
		$props_list = B_DB::instance('BaseProps')->all();
		foreach ($props_list as $key => $val) {
			$pageData['props_list'][$val['id']] = $val;
		}
		/** 模板装备列表 */
		$equipList = B_DB::instance('BaseEquipTpl')->getAll();
		foreach ($equipList as $key => $val) {
			$pageData['equipList'][$val['id']] = $val;
		}

		$probeList = B_DB::instance('BaseProbe')->all();
		foreach ($probeList as $key => $val) {
			$pageData['probeList'][$val['id']] = $val;
		}

		B_View::setVal('pageData', $pageData);
		B_View::render('War/NpcView');
	}

	/**
	 * NPC部队添加/编辑操作
	 */
	static public function ANpcEdit() {
		$id = intval($_REQUEST['id']); //ID
		$data['nickname'] = trim($_REQUEST['nickname']); //NPC昵称
		$data['face_id'] = intval($_REQUEST['face_id']); //NPC图标
		$data['level'] = intval($_REQUEST['level']); //NPC等级
		$data['type'] = intval($_REQUEST['type']); //NPC类型
		$data['award_remark'] = trim($_REQUEST['award_remark']); //描述
		$data['feature'] = trim($_REQUEST['feature']); //描述
		$data['res_data'] = trim($_REQUEST['res_data']); //描述
		$data['award_id'] = intval($_REQUEST['award_id']); //奖励ID绑定

		/** 探索消耗 */
		$ts_res_type = isset($_REQUEST['ts_res_type']) ? $_REQUEST['ts_res_type'] : '';
		$cost_data = array();
		if (isset($ts_res_type[0])) {
			foreach ($ts_res_type as $val) {
				$requestKey = 'ts_' . $val . '_num';
				$cost_data[$val] = $_REQUEST[$requestKey];
			}
			$data['probe_cost_data'] = json_encode($cost_data);
		}
		/** NPC部队NPC英雄列表 */
		$heros = $_REQUEST['heros'];
		$data['army_data'] = json_encode($heros);
		/** NPC资源数据 */
		$res_data_type = $_REQUEST['res_data_type'];
		$res_data_num = $_REQUEST['res_data_num'];
		if ($res_data_num > 0) {
			$res_data = array($res_data_type => $res_data_num);
			$data['res_data'] = json_encode($res_data);
		}
		/** 探索事件绑定 */
		$probe_event_data = array();
		$probeIds = $_REQUEST['probeId'];
		$probePros = $_REQUEST['probePro'];
		if ($probeIds && $probePros) {
			for ($i = 0; $i < count($probeIds); $i++) {
				$probe_event_data[$probeIds[$i]] = $probePros[$i];
			}
			$data['probe_event_data'] = json_encode($probe_event_data);
		}


		//////////////////////////////////////////////////////////////////////////////////
		if ($data['nickname'] == '') {
			echo "<script>alert('请填写NPC名称！');</script>";
			exit;
		}
		if ($data['level'] < 1) {
			echo "<script>alert('请指定NPC等级！');</script>";
			exit;
		}

		if ($id > 0) {
			//修改
			$result = B_DB::instance('BaseNpcTroop')->update($data, $id);
			if ($result) {
				echo "<script>alert('修改成功！');</script>";
			} else {
				echo "<script>alert('修改失败！');</script>";
			}
		} else {
			$data['create_at'] = time(); //添加时间
			$result = B_DB::instance('BaseNpcTroop')->insert($data);
			if ($result) {
				echo "<script>alert('添加成功！');</script>";
			} else {
				echo "<script>alert('添加失败！');</script>";
			}
		}
	}

	/**
	 * NPC部队删除操作
	 */
	static public function ANpcDel() {
		$flag = 0;
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if ($id > 0) {
			$res = B_DB::instance('BaseNpcTroop')->delete($id);
			if ($res) {
				$msg = '删除成功';
				$flag = 1;
			} else {
				$msg = '删除失败';
			}
		}
		$json['flag'] = $flag;
		$json['msg'] = $msg;
		echo json_encode($json);
	}

	static public function AProbeMap() {
		$pageData = array();
		$act = isset($_POST['act']) ? $_POST['act'] : '';

		if ($act == 'save') {
			$z = intval($_POST['z']);
			$x = intval($_POST['x']);
			$y = intval($_POST['y']);
			$npcId = intval($_POST['npcId']);
			$npcNum = intval($_POST['num']);

			if (!empty($z) && !empty($x) && !empty($y) && !empty($npcId) && !empty($npcNum)) {
				$res = M_MapWild::initWildNpc($z, $x, $y, $npcId, $npcNum);
				$ret = in_array(false, $res) ? 0 : 1;
				echo json_encode(array('succ' => $ret, 'num' => count($res)));
			} else {
				echo json_encode(array('succ' => 0));
			}

			exit;
		}

		$pageData['npcList'] = B_DB::instance('BaseNpcTroop')->getAllMapNpc();

		B_View::setVal('pageData', $pageData);
		B_View::render('War/ProbeMap');
	}

	static public function AWarFbExport() {

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
		$id = isset($_GET['id']) ? $_GET['id'] : 0;
		require_once ADM_PATH . '/lib/PHPExcel.php';


		$rows = B_DB::instance('BaseWarFB')->getsBy(array('chapter_no'=>$id),array('campaign_no'=>'ASC'));

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		$header = self::$FBheader;
		$obj = $objPHPExcel->setActiveSheetIndex(0);

		$i = 0;
		foreach ($header as $k => $val) {
			$prefix = $range[$i];
			$obj->setCellValue($prefix . '1', $val);
			$i++;
		}

		$no = 2;
		$n = 1;
		foreach ($rows as $vals) {
			$checkpoint_data = json_decode($vals['checkpoint_data'], true);

			for ($i = 1; $i <= 12; $i++) {
				$tmpData = $tmpStr = $tmpDesc = '';
				if (!empty($checkpoint_data[$i])) {
					$val = $checkpoint_data[$i];
					//$val[4] = 7000000 + M_Formula::calcFBNo($id, $n, $i);
					$tmpStr = implode('|', $val[6]);
					$tmpDesc = $val[7];
					$val[5] = !empty($val[5]) ? $val[5] : 0;
					unset($val[6]);
					unset($val[7]);
					$tmpData = implode(',', $val);

				}
				$vals['checkpoint_data' . $i] = $tmpData;
				$vals['checkpoint_text' . $i] = $tmpStr;
				$vals['checkpoint_desc' . $i] = $tmpDesc;
			}
			$vData = $vals;
			$i = 0;
			foreach ($header as $k => $line) {
				$prefix = $range[$i];
				$obj->setCellValue($prefix . $no, $vData[$k]);
				$i++;
			}
			$no++;

			$n++;

		}


		// Redirect output to a client’s web browser (Excel5)
		$filename = 'base_war_fb_' . date('YmdHis') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $filename);
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	static public function AWarFbImport() {
		if (!empty($_POST)) {
			$tmp = range('A', 'Z');
			$range = $tmp;
			foreach ($tmp as $val) {
				$range[] = 'A' . $val;
			}
			foreach ($tmp as $val) {
				$range[] = 'B' . $val;
			}

			$headerArr = array_keys(self::$FBheader);
			if (!empty($_FILES['npcherocsvfile']['tmp_name'])) {
				require_once ADM_PATH . '/lib/PHPExcel.php';
				$file = $_FILES["npcherocsvfile"]['tmp_name'];

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
				$allColumn = count(self::$FBheader);

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

					$newTmp = array();
					for ($i = 1; $i <= 12; $i++) {
						$tmpData = $tmp['checkpoint_data' . $i];
						$tmpText = $tmp['checkpoint_text' . $i];
						$tmpDesc = $tmp['checkpoint_desc' . $i];

						unset($tmp['checkpoint_data' . $i]);
						unset($tmp['checkpoint_text' . $i]);
						unset($tmp['checkpoint_desc' . $i]);

						$ttt = array();
						if (!empty($tmpData)) {
							$ttt = explode(',', $tmpData);
							$ttt[] = explode('|', $tmpText);
							$ttt[] = $tmpDesc;
						}
						if (!empty($ttt)) {
							$newTmp[$i] = $ttt;
						}

					}

					$tmp['checkpoint_data'] = json_encode($newTmp);

					if (!empty($tmp['id'])) {
						$tmp['chapter_no'] = floor($tmp['id'] / 100);
						$tmp['campaign_no'] = floor($tmp['id'] % 100);

						if (B_DB::instance('BaseWarFB')->get($tmp['id'])) {
							$ret = B_DB::instance('BaseWarFB')->update($tmp, $tmp['id']);
							$tip[$tmp['id']] = $ret ? '更新成功' : '更新失败';
						} else {
							$ret = B_DB::instance('BaseWarFB')->insert($tmp);
							$tip[$tmp['id']] = $ret ? '插入成功' : '插入失败';
						}


					}


					//$arr[] = $tmp;
				}

				$pageData['tip'] = $tip;
			}

		}
		$pageData['act'] = 'WarFbImport';
		B_View::setVal('pageData', $pageData);
		B_View::render('War/WarFbImport');
	}

}

?>
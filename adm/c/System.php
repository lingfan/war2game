<?php

class C_System {
	static public function AInit() {
		if (!M_Adm::isLogin()) {
			B_Common::redirect('?r=Index/Login');
		}
	}

	static public function AIndex() {
		B_View::render('index');
	}

	static public function AConfigServer() {
		$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
		if ($act == 'edit' && !empty($_POST)) {
			//M_Config::getSvrCfg('log_api');
			$data['city_card_pwd'] = trim($_REQUEST['city_card_pwd']);
			$data['server_name'] = $_REQUEST['server_name'];
			$data['server_title'] = $_REQUEST['server_title'];
			$data['server_api_key'] = $_REQUEST['server_api_key'];
			$data['max_online_people'] = $_REQUEST['max_online_people'];
			$data['server_res_url'] = $_REQUEST['server_res_url'];
			$data['anti_addiction_switch'] = $_REQUEST['anti_addiction_switch'];
			$data['maintenance'] = $_REQUEST['maintenance'];
			$data['qqserverip'] = $_REQUEST['qqserverip'];
			$data['log_api'] = $_REQUEST['log_api'];

			$flag = M_Config::setSvrCfg($data);

			echo $flag ? "<script>alert('保存成功');</script>" : "<script>alert('保存失败');</script>";
			exit;
		}


		B_View::render('System/Config_Server');
	}


	static public function AConfigTmpNpc() {
		$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
		if ($act == 'showflag' && !empty($_POST)) {
			$tmp = explode('|', $_POST['wild_refresh_npc_showflag']);
			$arr = array();
			foreach ($tmp as $val) {
				list($npcId, $showflag) = explode(',', $val);
				$arr[$npcId] = max(min($showflag, 255), 0);
			}

			$upData = array(
				'wild_refresh_npc_showflag' => $arr,
			);
			$ret = M_Config::setVal($upData);

			echo $ret ? "<script>alert('保存成功');</script>" : "<script>alert('保存失败');</script>";

			exit;
		} else if ($act == 'edit' && !empty($_POST)) {
			$arr = array();
			foreach ($_POST['wild_refresh_npc'][0] as $k => $id) {
				if (!empty($id)) {
					$tmp = array();
					for ($i = 1; $i < 9; $i++) {
						if (!empty($_POST['wild_refresh_npc'][$i][$k])) {
							if ($i == 8) {
								$ttt = array();
								$tarr = explode(';', $_POST['wild_refresh_npc'][$i][$k]);
								foreach ($tarr as $tval) {
									if (!empty($tval)) {
										list($tk, $tv) = explode(":", $tval);
										if (!empty($tk) && !empty($tv)) {
											$ttt[(int)$tk] = (int)$tv;
										}
									}
								}
								$tmp[] = $ttt;
							} else if ($i == 5) {
								$tmp[] = min($_POST['wild_refresh_npc'][$i][$k], 300);
							} else {
								$tmp[] = $_POST['wild_refresh_npc'][$i][$k];
							}
						}

					}
					if (count($tmp) == 8) {
						$arr[$id] = $tmp;
					}
				}
			}

			$ret = false;
			if (!empty($arr)) {
				$upData = array(
					'wild_refresh_npc' => $arr,
				);
				$ret = M_Config::setVal($upData);

				M_NPC::getRandTempNpcConf();

				$tmpRc = new B_Cache_RC(T_Key::TMP_EXPIRE, 'npc');
				$tmpRc->delete();

				$hadRc = new B_Cache_RC(T_Key::HAD_REFRESH_TMP_NPC, 'rand_temp_npc');
				$refreshData = $hadRc->jsonget();
				foreach ($refreshData as $npcId => $val) {
					if (!empty($val['list'])) {
						foreach ($val['list'] as $pos) {
							M_MapWild::cleanWildMapInfo($pos);
							M_MapWild::syncWildMapBlockCache($pos);
						}
					}
				}
				$ret = $hadRc->delete();
			}

			echo $ret ? "<script>alert('保存成功');</script>" : "<script>alert('保存失败');</script>";

			exit;
		}
		$pageData['baseCfg'] = M_Config::getVal();
		B_View::setVal('pageData', $pageData);
		B_View::render('System/Config_TmpNpc');

	}


	static public function AConfigOnline() {
		$baseCfg = M_Config::getVal();
		$act = isset($_POST['act']) ? $_POST['act'] : '';
		if ($act == 'edit') {

			$tArr = $_POST['config_online_award'];
			$list = explode("\n", $tArr);
			$setArr = array();
			$i = 1;
			foreach($list as $val) {
				$arr = explode(',',trim($val));
				$setArr[$i] = $arr;
				$i++;
			}


			//日历

			$arr = array();
			$milList = explode("\n", $_POST['calender_award']);
			$i = 1;
			foreach ($milList as $val) {
				if (!empty($val)) {
					list($day, $awardId) = explode(',', trim($val));
					$arr[$i] = array(intval($day), intval($awardId));
					$i++;
				}
			}

			if (!empty($setArr)) {
				$upData = array(
					'config_online_award' => $setArr,
					'calender_award' => $arr,
				);
				$ret = M_Config::setVal($upData);

				if ($ret) {
					echo "<script>";
					echo "alert('保存成功');";
					echo "</script>";
				} else {
					echo "<script>";
					echo "alert('保存失败');";
					echo "</script>";
				}
				exit;
			}
		}


		$pageData['baseCfg'] = $baseCfg;
		B_View::setVal('pageData', $pageData);
		B_View::render('System/Config_Online');
	}


	/**第一次登陆系统每日奖励**/
	static public function AConfigPay() {
		$baseCfg = M_Config::getVal();
		$act = isset($_POST['act']) ? $_POST['act'] : '';
		if ($act == 'edit') //判断保存
		{
			$start_time = $_POST['start_time']; //起始时间
			$end_time = $_POST['end_time']; //截止时间
			$setArr = array();
			$setArr[1] = $start_time;
			$setArr[2] = $end_time;
			$strPayAward = trim(!empty($_REQUEST['pay_award']) ? $_REQUEST['pay_award'] : '');
			$arrPayAward = explode("\r\n", $strPayAward);
			reset($arrPayAward);
			$i = 3;
			foreach ($arrPayAward as $key => $val) {
				$setArr[$i] = explode('_', $val);
				$i++;
			}

			if (!empty($setArr)) {
				$upData = array(
					'config_pay_award' => $setArr,
				);
				$ret = M_Config::setVal($upData);

				if ($ret) {
					echo "<script>";
					echo "alert('保存成功');";
					echo "</script>";
				} else {
					echo "<script>";
					echo "alert('保存失败');";
					echo "</script>";
				}
				exit;
			}
		}

		$data = $baseCfg['config_pay_award'];
		$pageData['list'] = $data ? $data : array();
		B_View::setVal('pageData', $pageData);
		B_View::render('System/Config_Pay');
	}


	static public function AConfigFascist() {
		B_View::render('System/Config_Fascist');

	}

	static public function AFascistAdd() {
		$baseCfg = M_Config::getVal();

		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
		$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
		if (!empty($id) && !empty($act)) //编辑
		{

			$wild_fixed_npc = $baseCfg['wild_fixed_npc'];
			$list = $wild_fixed_npc[$id];
			$pageData['list'] = $list;
			$pageData['list']['npc_id'] = $id;
		} else if (empty($id) && !empty($act)) //添加
		{
			$arr = array();
			$arr = $baseCfg['wild_fixed_npc'];
			$npcId = $_POST['npc_id'];
			$arr[$npcId]['broadcast_start'] = $_POST['broadcast_start'];
			$arr[$npcId]['broadcast_end'] = $_POST['broadcast_end'];
			$arr[$npcId]['Interval_broadcast'] = $_POST['Interval_broadcast'];
			$arr[$npcId]['broadcast'] = $_POST['broadcast'];
			$arr[$npcId]['channel'] = $_POST['channel'];
			$arr[$npcId]['npc_zone'] = $_POST['npc_zone'];
			$arr[$npcId]['npc_start'] = $_POST['npc_start'];
			$arr[$npcId]['npc_end'] = $_POST['npc_end'];
			$arr[$npcId]['out_broadcast'] = $_POST['out_broadcast'];
			$ttt = array();
			$tarr = explode(';', $_POST['npc_awardArr']);
			foreach ($tarr as $tval) {
				if (!empty($tval)) {
					list($tk, $tv) = explode(":", $tval);
					if (!empty($tk) && !empty($tv)) {
						$ttt[(int)$tk] = (int)$tv;
					}
				}
			}
			$arr[$npcId]['npc_awardArr'] = $ttt;
			$arr[$npcId]['npc_pos'] = $_POST['npc_pos1'] . ',' . $_POST['npc_pos2'];
			$ret = false;
			if (!empty($arr)) {
				$upData = array(
					'wild_fixed_npc' => $arr,
				);
				$ret = M_Config::setVal($upData);
			}

			$hadRc = new B_Cache_RC(T_Key::HAD_REFRESH_TMP_NPC, 'fixed_temp_npc');
			$refreshData = $hadRc->jsonget();
			foreach ($refreshData as $npcId => $val) {
				if (!empty($val['list'])) {
					foreach ($val['list'] as $pos) {
						M_MapWild::cleanWildMapInfo($pos);
						M_MapWild::syncWildMapBlockCache($pos);
					}
				}
			}
			$ret = $hadRc->delete();

			$msg = $ret ? '操作成功' : '操作失败';
			$flag = $msg == '操作成功' ? 1 : 0;

			$json['flag'] = $flag;
			$json['msg'] = $msg;
			echo json_encode($json);
			// 			header("location:?r=System/ConfigFascist");

			exit;
		} else if (empty($act)) {
			$pageData['id'] = $id;
		}
		B_View::setVal('pageData', $pageData);
		B_View::render('System/Config_FascistAdd');
	}

	static public function AFascistDelete() {
		$ret = false;
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
		if (!empty($id)) //删除
		{
			$baseCfg = M_Config::getVal();
			$wild_fixed_npc = $baseCfg['wild_fixed_npc'];
			unset($wild_fixed_npc[$id]);

			$upData = array(
				'wild_fixed_npc' => $wild_fixed_npc,
			);
			$ret = M_Config::setVal($upData);

		}
		echo "<script>";
		echo $ret ? "alert('操作成功!');" : "alert('操作失败!');";
		echo "window.location='?r=System/ConfigFascist'";
		echo "</script>";
		//	B_View::render('System/Config_Fascist');

	}

	static public function AFascistAddCacheUp() {
		$ret = false;
		$ret1 = false;
		$hadRc = new B_Cache_RC(T_Key::HAD_REFRESH_TMP_NPC, 'fixed_temp_npc');
		$refreshData = $hadRc->jsonget();
		foreach ($refreshData as $npcId => $val) {
			if (!empty($val['list'])) {
				foreach ($val['list'] as $pos) {
					M_MapWild::cleanWildMapInfo($pos);
					M_MapWild::syncWildMapBlockCache($pos);
				}
			}
		}

		$ret = $hadRc->delete();
		echo "<script>";
		echo $ret ? "alert('操作成功!');" : "alert('操作失败!');";
		echo "window.location='?r=System/ConfigFascist'";
		echo "</script>";
		//	B_View::render('System/Config_Fascist');
	}


	static public function AConfigEvent() {
		$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';

		$baseCfg = M_Config::getVal();

		if ($act == 'edit' && !empty($_POST)) {
			$data = array();
			$data['event_floor'] = $_POST['event_floor'];
			$data['event_breakout'] = $_POST['event_breakout'];
			$data['event_answer'] = $_POST['event_answer'];
			$data['event_campaign'] = $_POST['event_campaign'];
			$data['event_challenge'] = $_POST['event_challenge'];

			$flag = M_Config::setVal($data);

			echo $flag ? "<script>alert('保存成功');</script>" : "<script>alert('保存失败');</script>";
			exit;

		}
		$pageData = $baseCfg;
		B_View::setVal('pageData', $pageData);
		B_View::render('System/Config_Event');
	}

	//此模块测试使用 暂时不要使用
	static public function AConfigActive() {
		$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';

		if ($act == 'edit' && !empty($_POST)) {
			$data = array();
			$data['active_list'] = $_REQUEST['active_list'];

			$flag = M_Config::setVal($data);

			echo $flag ? "<script>alert('保存成功');</script>" : "<script>alert('保存失败');</script>";
			exit;

		}
		$pageData = array();
		B_View::setVal('pageData', $pageData);
		B_View::render('System/Config_Active');
	}

	static public function AConfig() {
		//$conf = array('hero','base','equip','weapon','army');
		$args = array(
			'type' => FILTER_SANITIZE_STRING,
		);
		$formVals = filter_var_array($_REQUEST, $args);
		$type = !empty($formVals['type']) ? ucfirst($formVals['type']) : 'Base';

		B_View::render('System/Config_' . $type);
	}


	static public function AConfigBase() {
		$baseCfg = M_Config::getVal();
		$pageData = array('baselist' => $baseCfg);
		$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
		if ($act == 'edit' && !empty($_POST)) {
			$data['city_base_gold'] = intval($_REQUEST['city_base_gold']);
			$data['city_base_food'] = intval($_REQUEST['city_base_food']);
			$data['city_base_oil'] = intval($_REQUEST['city_base_oil']);
			$data['city_gold_grow'] = intval($_REQUEST['city_gold_grow']);
			$data['city_food_grow'] = intval($_REQUEST['city_food_grow']);
			$data['city_oil_grow'] = intval($_REQUEST['city_oil_grow']);
			$data['city_max_store'] = intval($_REQUEST['city_max_store']);
			$data['city_max_people'] = intval($_REQUEST['city_max_people']);
			$data['base_cd_build_num'] = intval($_REQUEST['base_cd_build_num']);
			$data['final_cd_build_num'] = intval($_REQUEST['final_cd_build_num']);
			$data['build_list_cost'] = explode(',', $_REQUEST['build_list_cost']);
			$data['city_max_store_num'] = explode(',', $_REQUEST['city_max_store_num']);
			$data['city_max_house_num'] = explode(',', $_REQUEST['city_max_house_num']);
			$data['final_cd_tech_num'] = intval($_REQUEST['final_cd_tech_num']);
			$data['tech_list_cost'] = explode(',', $_REQUEST['tech_list_cost']);
			$data['user_energy_limit'] = intval($_REQUEST['user_energy_limit']);
			$data['user_mil_order_limit'] = intval($_REQUEST['user_mil_order_limit']);
			$data['user_energy_incr'] = intval($_REQUEST['user_energy_incr']);
			$data['user_mil_order_incr'] = intval($_REQUEST['user_mil_order_incr']);
			$data['city_in_area_x'] = intval($_REQUEST['city_in_area_x']);
			$data['city_in_area_y'] = intval($_REQUEST['city_in_area_y']);
			$data['build_area_x'] = intval($_REQUEST['build_area_x']);
			$data['build_area_y'] = intval($_REQUEST['build_area_y']);

			$data['war_area_x'] = intval($_REQUEST['war_area_x']);
			$data['war_area_y'] = intval($_REQUEST['war_area_y']);
			$data['weapon_max_special'] = intval($_REQUEST['weapon_max_special']);
			$data['weapon_slot_cost'] = explode(',', $_REQUEST['weapon_slot_cost']);
			$data['army_max_level'] = intval($_REQUEST['army_max_level']);
			$data['map_zone_terrain'] = $_REQUEST['map_zone_terrain'];
			$data['weather_refresh_interval'] = intval($_REQUEST['weather_refresh_interval']);
			$data['hold_time_interval'] = intval($_REQUEST['hold_time_interval']);
			$data['hold_city_time_interval'] = intval($_REQUEST['hold_city_time_interval']);
			$data['rescue_cd'] = intval($_REQUEST['rescue_cd']);
			$data['rescue_cd_times'] = $_REQUEST['rescue_cd_times'];
			$data['tax_cd'] = intval($_REQUEST['tax_cd']);
			$data['oil_reduce'] = intval($_REQUEST['oil_reduce']);
			$data['food_reduce'] = intval($_REQUEST['food_reduce']);
			$data['gold_reduce'] = intval($_REQUEST['gold_reduce']);
			$data['map_zone_weather'] = $_REQUEST['map_zone_weather'];
			$data['props_strong_level'] = $_REQUEST['props_strong_level'];
			$data['pay_action_value'] = $_REQUEST['pay_action_value'];
			$data['war_map_zone'] = $_REQUEST['war_map_zone'];
			$data['lotter_refresh'] = $_REQUEST['lotter_refresh'];
			$data['city_newbie_mil_medal'] = intval($_REQUEST['city_newbie_mil_medal']);
			$data['milpay_exchange'] = intval($_REQUEST['milpay_exchange']);
			$data['bout_times_cost'] = explode(',', $_REQUEST['bout_times_cost']);
			$data['march_camp_max_num'] = $_REQUEST['march_camp_max_num'];

			$data['multi_fb_buy_cost'] = explode(',', $_REQUEST['multi_fb_buy_cost']);
			$data['exchange_milpay_succ_rate'] = $_REQUEST['exchange_milpay_succ_rate'];



			$tmp = array();
			foreach ($_REQUEST['multi_fb_addition_cost'] as $additionType => $val) {
				$arr = explode("|", $val);
				$arrt = array();
				$i = 1;
				foreach ($arr as $t) {
					$arrt[$i] = explode("_", $t);
					$i++;
				}
				$tmp[$additionType] = $arrt;
			}

			$data['multi_fb_addition_cost'] = $tmp;


			$arr = array();
			$milList = explode("\n", $_REQUEST['mil_rank_renown']);
			foreach ($milList as $val) {
				if (!empty($val)) {
					$arr[] = explode('|', trim($val));
				}
			}
			$data['mil_rank_renown'] = $arr;


			$auc_time_cost_1 = explode(',', $_REQUEST['auc_time_cost_1']);
			$auc_time_cost_2 = explode(',', $_REQUEST['auc_time_cost_2']);
			$data['auc_time_cost'] = array($auc_time_cost_1, $auc_time_cost_2);

			$setArr[1] = $_REQUEST['start_time'] ? $_REQUEST['start_time'] : 0;
			$setArr[2] = $_REQUEST['end_time'];
			$setArr[3] = $_REQUEST['daily_login_award'];
			$data['daily_login_award'] = $setArr;

			$start_time = $_POST['start']; //起始时间
			$end_time = $_POST['end']; //截止时间
			$pros = $_POST['pros']; //奖励ID
			$pros2 = $_POST['pros2']; //奖励ID
			$pros3 = $_POST['pros3']; //奖励ID
			$pros4 = $_POST['pros4']; //奖励ID
			$pros5 = $_POST['pros5']; //奖励ID
			$setArr1 = array();
			$setArr1['start'] = $start_time;
			$setArr1['end'] = $end_time;
			$setArr1['list'] = $pros;
			$setArr1['list2'] = $pros2;
			$setArr1['list3'] = $pros3;
			$setArr1['list4'] = $pros4;
			$setArr1['list5'] = $pros5;

			$data['active_award'] = $setArr1;


			$record_start_time = $_POST['record_start']; //起始时间
			$record_end_time = $_POST['record_end']; //截止时间
			$record = $_POST['record']; //奖励ID
			$setArr2 = array();
			$setArr2['start'] = $record_start_time;
			$setArr2['end'] = $record_end_time;
			$setArr2['list'] = $record;
			$data['record_active'] = $setArr2;


			$setArr3 = array();
			$record1 = $_POST['city_level_1']; //奖励ID
			$setArr3['city_level_1'] = $record1;
			$record2 = $_POST['city_level_2']; //奖励ID
			$setArr3['city_level_2'] = $record2;
			$record3 = $_POST['city_level_3']; //奖励ID
			$setArr3['city_level_3'] = $record3;
			$record4 = $_POST['city_level_4']; //奖励ID
			$setArr3['city_level_4'] = $record4;
			$record5 = $_POST['city_level_5']; //奖励ID
			$setArr3['city_level_5'] = $record5;
			$data['atk_punishment'] = $setArr3;

			//图鉴
			$help_detail = explode("\n", $_POST['help_detail']);
			$data['help_detail'] = array();
			foreach ($help_detail as $val) {
				$arr = explode(",", trim($val));
				$cate = intval(array_shift($arr));
				$type = intval(array_shift($arr));
				$data['help_detail'][$cate][$type] = $arr;
			}




			$loss = $_POST['loss']; //奖励ID
			$setArr4 = array();
			foreach (M_War::$warLoss as $val) {
				$setArr4[$val]['before'] = $loss[$val . 'before'];
				$setArr4[$val]['after'] = $loss[$val . 'after'];
				$setArr4[$val]['diff'] = $loss[$val . 'diff'];
			}
			$data['atk_loss_open'] = $setArr4;

			$flag = M_Config::setVal($data);

			echo $flag ? "<script>alert('保存成功');</script>" : "<script>alert('保存失败');</script>";
			exit;

		}

		$pageData = array('baselist' => $baseCfg);

		$data = $baseCfg['daily_login_award'];
		$pageData['list'] = $data ? $data : array();
		B_View::setVal('pageData', $pageData);
		$active_data = $baseCfg['active_award'];
		$record_active = $baseCfg['record_active'];
		$atk_punishment = $baseCfg['atk_punishment'];
		$activeness_list = $baseCfg['activeness_list'];
		$atkLossOpen = $baseCfg['atk_loss_open'];
		$pageData['active_list'] = $active_data ? $active_data : array();
		$pageData['record_list'] = $record_active ? $record_active : array();
		$pageData['atk_list'] = $atk_punishment ? $atk_punishment : array();
		$pageData['activeness_list'] = $activeness_list ? $activeness_list : array();
		$pageData['atk_loss_open'] = $atkLossOpen ? $atkLossOpen : array();
		B_View::setVal('pageData', $pageData);

		B_View::render('System/Config_Base');
	}

	static public function AConfigLiveness() {

		$baseCfg = M_Config::getVal();
		$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
		if ($act == 'edit' && !empty($_POST)) {

			$data['activeness_list'] = $_REQUEST['activeness'];
			$data['activeness_list']['start'] = strtotime($data['activeness_list']['start']);
			$data['activeness_list']['end'] = strtotime($data['activeness_list']['end']);


			$data['quick_map_no'] = explode(',', $_REQUEST['quick_map_no']);
			foreach (M_Liveness::$category as $key => $val) {
				if (substr_count($data['activeness_list'][$key][0], ',') >= 1) {
					$arr = array();
					$activenessList = explode(',', $_REQUEST['activeness'][$key][0]);
					foreach ($activenessList as $v) {
						$str = explode(':', $v);
						$arr[$str[0]] = $str[1];
						$data['activeness_list'][$key][0] = $arr;
					}
				}
			}


			$arr = array();
			$milList = explode("\n", $_REQUEST['activeness_item']);
			$i=1;
			foreach ($milList as $val) {
				if (!empty($val)) {
					$arr[$i] = explode(',', trim($val));
					$i++;
				}
			}
			$data['activeness_item'] = $arr;

			$flag = M_Config::setVal($data);

			echo $flag ? "<script>alert('保存成功');</script>" : "<script>alert('保存失败');</script>";
			exit;

		}

		$pageData = array('baselist' => $baseCfg);
		B_View::setVal('pageData', $pageData);

		B_View::render('System/Config_Liveness');
	}

	static public function ADefaultProbeMap() {
		$baseCfg = M_Config::getVal();
		$pageData = array('baselist' => $baseCfg);
		$act = isset($_POST['act']) ? $_POST['act'] : '';
		$ret = false;
		if ($act == 'edit') {
			$npcNum = isset($_POST['npcNum']) ? $_POST['npcNum'] : 0;
			if ($npcNum) {
				$data = array(
					'npc_num' => $npcNum
				);

				$upData = array('config_probe_map' => $data);
				$ret = M_Config::setVal($upData);
			}


			echo "<script>";
			echo $ret ? "alert('保存成功');" : "alert('保存失败');";
			echo "</script>";
			exit;
		}

		$data = $baseCfg['config_probe_map'];
		$pageData['info'] = $data ? $data : array();
		B_View::setVal('pageData', $pageData);
		B_View::render('System/Config_ProbeMap');
	}

	/** 军团配置 */
	static public function AConfigUnion() {
		$baseCfg = M_Config::getVal();
		$pageData = array('baselist' => $baseCfg);
		$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
		if ($act == 'edit' && !empty($_POST)) {
			$data['union_create_cost'] = $_REQUEST['union_create_cost'];
			$data['union_up_face_cost'] = $_REQUEST['union_up_face_cost'];
			$data['union_create_need_medal'] = $_REQUEST['union_create_need_medal'];
			$data['union_donation_need_medal'] = $_REQUEST['union_donation_need_medal'];
			$data['cd_apply_union'] = $_REQUEST['cd_apply_union'];
			$tmp = array();
			if (!empty($_REQUEST['union_up'])) {
				$arr = explode("|", $_REQUEST['union_up']);
				$arrt = array();
				$i = 1;
				foreach ($arr as $t) {
					if (!empty($t)) {
						$arrt[$i] = explode("_", $t);
						$i++;
					}

				}
				$tmp = $arrt;
			}

			$data['union_up'] = $tmp;

			$tmp = array();
			foreach ($_REQUEST['union_tech'] as $k => $val) {
				$arr = explode("|", $val);
				$arrt = array();
				$i = 1;
				foreach ($arr as $t) {
					$arrt[$i] = explode("_", $t);
					$i++;
				}
				$tmp[$k] = $arrt;
			}

			$data['union_tech'] = $tmp;


			$flag = M_Config::setVal($data);

			echo $flag ? "<script>alert('保存成功');</script>" : "<script>alert('保存失败');</script>";
			exit;

		}

		B_View::setVal('pageData', $pageData);
		B_View::render('System/Config_Union');
	}

	static public function AHorseCleanCache() {
		$baseCfg = M_Config::getVal();
		$pageData = array('baselist' => $baseCfg);
		$cycleStageArr = M_Horse::getCycleStageTime();
		$runNo = $cycleStageArr['curRunNo'];
		$nowDate = $cycleStageArr['nowDate'];
		$maxCycleNo = $cycleStageArr['maxCycleNo'];
		$ret = false;
		for ($i = 1; $i <= $maxCycleNo; $i++) {
			$rc = new B_Cache_RC(T_Key::SYS_HORSE, $nowDate . $i);
			$b = $rc->delete();
			if ($b) {
				$ret[] = $i;
			}
		}

		$str = '\n' . implode('\n', $ret);
		echo "<script>";
		echo $ret ? "alert(\"更新成功{$str}\");" : "alert('更新失败');";
		echo "</script>";
		exit;
	}

	/** 越野跑马配置 */
	static public function AConfigHorse() {
		$baseCfg = M_Config::getVal();
		$pageData = array('baselist' => $baseCfg);
		$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
		if ($act == 'edit' && !empty($_POST)) {
			$horseConf = $status = $encourage = array();
			$horseConf[0] = explode(',', $_REQUEST['costmin']); //四个阶段结束所需分钟,比赛时每段分钟
			$horseConf[1] = explode(',', $_REQUEST['payrate']); //固定赔率,固定赔率,赔率E最小范围,赔率E最大范围

			$statusTmp = trim($_REQUEST['status']);
			$strStatus = str_replace("\r\n", '|', $statusTmp);
			$arrStatus = explode('|', $strStatus);
			foreach ($arrStatus as $k => $noDesc) {
				list($no, $desc) = explode(',', $noDesc);
				$status[$no] = $desc;
			}
			$horseConf[2] = $status; //随机状态

			$horseConf[3][0] = explode(',', $_REQUEST['eventid1']); //1减速事件ID,2匀速事件ID,3加速事件ID
			$horseConf[3][1] = explode(',', $_REQUEST['eventid2']);
			$horseConf[3][2] = explode(',', $_REQUEST['eventid3']);
			$horseConf[4][0] = explode(',', $_REQUEST['randevent1']); //总事件数,加速随机下限,上限,加速比减速多
			$horseConf[4][1] = explode(',', $_REQUEST['randevent2']);
			$horseConf[4][2] = explode(',', $_REQUEST['randevent3']);
			$horseConf[4][3] = explode(',', $_REQUEST['randevent4']);
			$horseConf[4][4] = explode(',', $_REQUEST['randevent5']);
			$horseConf[4][5] = explode(',', $_REQUEST['randevent6']);
			$horseConf[4][6] = explode(',', $_REQUEST['randevent7']);
			$horseConf[5] = explode(',', $_REQUEST['betting']); //投注最小值,最大值
			$horseConf[6] = intval($_REQUEST['encourmax']); //最多打气次数

			$encourageTmp = trim($_REQUEST['encourage']);
			$strEncourage = str_replace("\r\n", '|', $encourageTmp);
			$arrEncourage = explode('|', $strEncourage);
			foreach ($arrEncourage as $k => $noCost) {
				list($no, $cost) = explode(',', $noCost);
				$encourage[$no] = $cost;
			}
			$horseConf[7] = $encourage; //array(打气ID=>所需军饷,...)

			$horseConf[8] = intval($_REQUEST['firstaward']); //第一名奖励ID
			$horseConf[9][0] = intval($_REQUEST['interval']);
			$horseConf[9][1] = intval($_REQUEST['playtimes']);
			$horseConf[10] = intval($_REQUEST['horseswitch']); //系统开关
			$horseConf[11] = explode(",", $_REQUEST['horseweapon']); //基础武器ID列表

			$upData = array(
				'horse' => $horseConf,
			);
			$flag = M_Config::setVal($upData);


			echo $flag ? "<script>alert('保存成功');</script>" : "<script>alert('保存失败');</script>";
			exit;

		}
		B_View::setVal('pageData', $pageData);
		B_View::render('System/Config_Horse');
	}

	static public function AConfigQQ() {
		$baseCfg = M_Config::getVal();
		$pageData = array('baselist' => $baseCfg);
		$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
		if ($act == 'edit' && !empty($_POST)) {
			$data['appid'] = $_REQUEST['appid'];
			$data['appkey'] = $_REQUEST['appkey'];
			$data['yellow_vip_one'] = $_REQUEST['yellow_vip_one'];
			$data['yellow_year_vip'] = $_REQUEST['yellow_year_vip'];
			$data['yellow_vip_level'] = json_encode($_REQUEST['yellow_vip_level']);
			$data['qq_invite_friend_award'] = $_REQUEST['qq_invite_friend_award'];
			$data['qq_invite_friend_num'] = $_REQUEST['qq_invite_friend_num'];
			$data['qq_share_success_num'] = $_REQUEST['qq_share_success_num'];

			$flag = M_Config::setVal($data);

			echo $flag ? "<script>alert('保存成功');</script>" : "<script>alert('保存失败');</script>";
			exit;

		}
		B_View::setVal('pageData', $pageData);

		B_View::render('System/Config_QQ');
	}


	static public function AConfigHero() {
		$baseCfg = M_Config::getVal();
		$pageData = array('baselist' => $baseCfg);
		$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
		if ($act == 'edit' && !empty($_POST)) {
			$data['hero_maxlv'] = $_REQUEST['hero_maxlv'];
			$data['hero_base_value'] = $_REQUEST['hero_base_value'];
			$data['hero_find_interval'] = $_REQUEST['hero_find_interval'];

			$data['hero_succ_keep_time'] = $_REQUEST['hero_succ_keep_time'];
			$data['hero_refresh_interval'] = $_REQUEST['hero_refresh_interval'];
			$data['hero_refresh_num'] = $_REQUEST['hero_refresh_num'];
			$data['hero_relife_gold'] = $_REQUEST['hero_relife_gold'];
			$data['hero_num_troop'] = $_REQUEST['hero_num_troop'];
			$data['hero_num_city_max'] = intval($_REQUEST['hero_num_city_max']);
			$data['hero_max_rate_num'] = $_REQUEST['hero_max_rate_num'];
			$data['hero_rate'] = $_REQUEST['hero_rate'];
			$data['hero_find_rate'] = $_REQUEST['hero_find_rate'];
			$data['hero_learn_skill'] = $_REQUEST['hero_learn_skill'];
			$data['hero_learn_skill_rate'] = $_REQUEST['hero_learn_skill_rate'];
			$data['refresh_rate'] = $_REQUEST['refresh_rate'];
			$data['hero_train'] = $_REQUEST['hero_train'];
			$data['hero_train_limit'] = $_REQUEST['hero_train_limit'];
			$data['hero_train_free'] = $_REQUEST['hero_train_free'];
			$data['hero_recycle'] = $_REQUEST['hero_recycle'];
			$data['hero_recycle_attr'] = $_REQUEST['hero_recycle_attr'];
			$data['hero_refresh_cost'] = explode(',', $_REQUEST['hero_refresh_cost']);
			$seekHero = explode('|', $_REQUEST['seek_hero']);

			foreach ($_REQUEST['hero_seek_rate'] as $k => $v) {
				$data['hero_seek_rate'][$k] = explode(',', $v);
			}

			foreach ($_REQUEST['hero_seek_cost'] as $k => $v) {
				$data['hero_seek_cost'][$k] = explode(',', $v);
			}

			$data['hero_rate_time'] = array();
			foreach ($_REQUEST['hero_rate_time'] as $k => $v) {
				$tmp = explode("\n", $v);
				foreach ($tmp as $v) {
					$data['hero_rate_time'][$k][] = explode('_', trim($v));
				}
			}


			$randomCost = $_REQUEST['random_cost']; //花费军饷

			$awardId = $_REQUEST['award_id']; //奖励ID
			$heroExchangeAward = array();
			foreach ($awardId as $key => $value) {
				foreach ($value as $keyAward => $Award) {
					$heroExchangeAward[$key][$keyAward]['awardId'] = $awardId[$key][$keyAward];
				}
			}
			foreach ($randomCost as $key => $value) {
				foreach ($value as $keyRandom => $Random) {
					$heroExchangeAward[$key][$keyRandom]['cost'] = $randomCost[$key][$keyRandom];
				}
			}
			$data['hero_exchange'] = $heroExchangeAward;

			$expArr = explode(',', $_REQUEST['hero_exp']);
			$lv = 1;
			for ($i = 0; $i < count($expArr); $i++) {
				$data['hero_exp'][$lv] = $expArr[$i];
				$lv++;
			}

			$hero_attr_mood_key = $_REQUEST['hero_attr_mood_key'];
			$hero_attr_mood_val = $_REQUEST['hero_attr_mood_val'];
			for ($i = 0; $i < count($hero_attr_mood_key); $i++) {
				$data['hero_attr_mood'][$hero_attr_mood_key[$i]] = $hero_attr_mood_val[$i];
			}

			$hero_attr_energy_key = $_REQUEST['hero_attr_energy_key'];
			$hero_attr_energy_val = $_REQUEST['hero_attr_energy_val'];
			for ($i = 0; $i < count($hero_attr_energy_key); $i++) {
				$data['hero_attr_energy'][$hero_attr_energy_key[$i]] = $hero_attr_energy_val[$i];
			}


			$flag = M_Config::setVal($data);

			echo $flag ? "<script>alert('保存成功');</script>" : "<script>alert('保存失败');</script>";
			exit;
		}
		$active_data = $baseCfg['hero_exchange'];

		$pageData['heroExchange'] = $active_data ? $active_data : array();
		B_View::setVal('pageData', $pageData);
		B_View::render('System/Config_Hero');
	}

	static public function AConfigEquip() {
		$baseCfg = M_Config::getVal();

		$pageData = array('baselist' => $baseCfg);
		$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';

		if ($act == 'edit' && !empty($_POST)) {
			$data['strong_equip_max_level'] = $_REQUEST['strong_equip_max_level'];
			$data['strong_equip_base_gold'] = $_REQUEST['strong_equip_base_gold'];
			$data['strong_equip_gold_rate'] = $_REQUEST['strong_equip_gold_rate'];
			$data['strong_equip_rate_a'] = $_REQUEST['strong_equip_rate_a'];
			$data['strong_equip_rate_b'] = $_REQUEST['strong_equip_rate_b'];
			$data['strong_equip_rate_s'] = $_REQUEST['strong_equip_rate_s'];
			$data['strong_equip_attr_add_rate'] = $_REQUEST['strong_equip_attr_add_rate'];
			$data['strong_suit_equip_attr_add_rate'] = $_REQUEST['strong_suit_equip_attr_add_rate'];
			// 			$data['is_synthesis'] = $_REQUEST['is_synthesis'];
			// 			$data['is_upgrades'] = $_REQUEST['is_upgrades'];

			//var_dump($data);
			$flag = M_Config::setVal($data);

			echo $flag ? "<script>alert('保存成功');</script>" : "<script>alert('保存失败');</script>";
			exit;
		}
		B_View::setVal('pageData', $pageData);
		B_View::render('System/Config_Equip', $pageData);
	}

	/** VIP新版配置 */
	static public function AConfigVipNew() {
		$baseCfg = M_Config::getVal();
		$pageData = array('baselist' => $baseCfg);
		$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
		if ($act == 'edit' && !empty($_POST)) {
			$vip_config = $baseCfg['vip_config'];
			$vip_config['MAX_VIP_LEVEL'] = intval($_REQUEST['MAX_VIP_LEVEL']);
			$vip_config['MIL_PAY_CONF'] = explode(',', $_REQUEST['MIL_PAY_CONF']);
			$vip_config['INCR_ENERGY_LIMIT'] = explode(',', $_REQUEST['INCR_ENERGY_LIMIT']);
			$vip_config['BUY_ENERGY'] = explode(',', $_REQUEST['BUY_ENERGY']);
			$vip_config['INCR_MILORDER_LIMIT'] = explode(',', $_REQUEST['INCR_MILORDER_LIMIT']);
			$vip_config['BUY_MILORDER'] = explode(',', $_REQUEST['BUY_MILORDER']);
			$vip_config['BUILD_CD_LISTID'] = explode(',', $_REQUEST['BUILD_CD_LISTID']);
			$vip_config['TECH_CD_LISTID'] = explode(',', $_REQUEST['TECH_CD_LISTID']);
			$vip_config['INCR_AWARD_RATE'] = explode(',', $_REQUEST['INCR_AWARD_RATE']);
			$vip_config['SHOP_RES'] = explode(',', $_REQUEST['SHOP_RES']);
			$vip_config['PACK_EQUI'] = explode(',', $_REQUEST['PACK_EQUI']);
			$vip_config['PACK_DRAW'] = explode(',', $_REQUEST['PACK_DRAW']);
			$vip_config['PACK_PROPS'] = explode(',', $_REQUEST['PACK_PROPS']);
			$vip_config['PACK_MATERIAL'] = explode(',', $_REQUEST['PACK_MATERIAL']);
			$vip_config['SPECIAL_SLOTID'] = $_REQUEST['SPECIAL_SLOTID'];
			$vip_config['DECR_MARCH_TIME'] = $_REQUEST['DECR_MARCH_TIME'];
			$vip_config['HERO_AWARD'] = $_REQUEST['HERO_AWARD'];
			$vip_config['EQUI_AWARD'] = $_REQUEST['EQUI_AWARD'];
			$vip_config['FOOD_INCR_YIELD'] = $_REQUEST['FOOD_INCR_YIELD'];
			$vip_config['OIL_INCR_YIELD'] = $_REQUEST['OIL_INCR_YIELD'];
			$vip_config['GOLD_INCR_YIELD'] = $_REQUEST['GOLD_INCR_YIELD'];
			$vip_config['ARMY_INCR_ATT'] = $_REQUEST['ARMY_INCR_ATT'];
			$vip_config['ARMY_INCR_DEF'] = $_REQUEST['ARMY_INCR_DEF'];
			$vip_config['HERO_INCR_ARMY'] = $_REQUEST['HERO_INCR_ARMY'];
			$vip_config['ARMY_RELIFE'] = $_REQUEST['ARMY_RELIFE'];
			$vip_config['COLONY_OPEN'] = $_REQUEST['COLONY_OPEN'];
			$vip_config['CITY_COLONY_OPEN'] = $_REQUEST['CITY_COLONY_OPEN'];
			$vip_config['VIP_PACKAGE'] = $_REQUEST['VIP_PACKAGE'];

			$arrVipShopTmp = $_REQUEST['VIP_SHOP'];
			foreach ($arrVipShopTmp as $vipLev => $strShop) {
				$arrVipShop[$vipLev] = str_replace("\r\n", '|', $strShop);
			}
			$vip_config['VIP_SHOP'] = $arrVipShop;

			/*
			 echo '<pre>';
			print_r($vip_config);
			echo '</pre>';
			exit;
			*/

			$upData = array(
				'vip_config' => $vip_config,
			);
			$flag = M_Config::setVal($upData);

			echo $flag ? "<script>alert('保存成功');</script>" : "<script>alert('保存失败');</script>";
			exit;
		}

		$pageData = $baseCfg['vip_config'];
		B_View::setVal('pageData', $pageData);
		B_View::render('System/Config_VipNew');
	}

	static public function AConfigWeapon() {
		$baseCfg = M_Config::getVal();
		$pageData = array('baselist' => $baseCfg);
		$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
		if ($act == 'edit' && !empty($_POST)) {
			$tmp = array();
			$arr = explode("\n", $_REQUEST['temp_weapon']);
			foreach ($arr as $val) { //武器ID=>array(租借时间(小时),军饷,军械所等级)

				list($id, $t, $coin, $lv) = explode(",", trim($val));
				if (!empty($id) && !empty($t) && !empty($coin) && !empty($lv)) {
					$newId = intval($id);
					$tmp[$newId] = array($t, intval($coin), intval($lv));
				}

			}

			$data['temp_weapon'] = $tmp;
			$flag = M_Config::setVal($data);

			echo $flag ? "<script>alert('保存成功');</script>" : "<script>alert('保存失败');</script>";
			exit;

		}
		B_View::setVal('pageData', $pageData);
		B_View::render('System/Config_Weapon', $pageData);
	}

	/** 剧情任务奖励配置 */
	static public function AConfigDrama() {
		$baseCfg = M_Config::getVal();
		$pageData = array('baselist' => $baseCfg);
		$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
		$dramaNum = 15; //默认10章

		if ($act == 'edit') {
			$arrDrama = $_REQUEST['drama_award'];
			if (!empty($arrDrama) && is_array($arrDrama)) {
				foreach ($arrDrama as $chap => $drama) {
					$arrDrama[$chap] = explode(',', $drama);
				}

				$upData = array(
					'drama_award' => $arrDrama,
				);
				$ret = M_Config::setVal($upData);
			}
			//echo "<script>";
			//echo $ret ? "alert('保存成功');" : "alert('保存失败');";
			//echo "</script>";
		}

		$data = $baseCfg['drama_award'];
		$pageData['info'] = $data ? $data : array();
		$pageData['dramaNum'] = $dramaNum;
		B_View::setVal('pageData', $pageData);
		B_View::render('System/Config_Drama');
	}

	static public function AConfigQuestion() {
		$baseCfg = M_Config::getVal();
		$pageData = array('baselist' => $baseCfg);
		$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';

		if ($act == 'edit') {

			$data['question_open'] = explode(',', $_REQUEST['question_open']);
			$data['question_point'] = $_REQUEST['question_point'];
			$data['question_time'] = $_REQUEST['question_time'];
			$data['question_num'] = $_REQUEST['question_num'];
			$data['question_cost'] = explode(',', $_REQUEST['question_cost']);

			$arr = array();
			$milList = explode("\n", $_REQUEST['question_props']);
			foreach ($milList as $val) {
				if (!empty($val)) {
					list($pid, $point) = explode(',', trim($val));
					$arr[$pid] = $point;
				}
			}
			$data['question_props'] = $arr;

			$flag = M_Config::setVal($data);

			echo $flag ? "<script>alert('保存成功');</script>" : "<script>alert('保存失败');</script>";
			exit;
		}


		B_View::setVal('pageData', $pageData);
		B_View::render('System/Config_Question');
	}

	static public function AConfigFloor() {
		$baseCfg = M_Config::getVal();
		$pageData = array('baselist' => $baseCfg);
		$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';

		if ($act == 'edit') {
			$data['floor_open'] = explode(',', $_REQUEST['floor_open']);
			$data['floor_cost'] = explode(',', $_REQUEST['floor_cost']);
			$data['floor_rate'] = intval($_REQUEST['floor_rate']);

			$arr = array();
			foreach ($_REQUEST['floor_data'] as $key => $tval) {
				$list = explode("\n", $tval);
				$tmp = array();
				$i = 1;
				foreach ($list as $val) {
					if (!empty($val)) {
						list($npcId, $mapNo, $awardId) = explode(',', trim($val));
						$tmp[$i] = array($npcId, $mapNo, $awardId);
						$i++;
					}

				}

				$arr[$key] = $tmp;
			}

			$data['floor_data'] = $arr;


			$flag = M_Config::setVal($data);

			echo $flag ? "<script>alert('保存成功');</script>" : "<script>alert('保存失败');</script>";
			exit;
		}


		B_View::setVal('pageData', $pageData);
		B_View::render('System/Config_Floor');
	}

	/** 更新排行缓存 */
	static public function ARankCacheUp() {
		$rc = new B_Cache_RC(T_Key::RANKINGS_RECORD);
		$ret = $rc->delete();
		echo "<script>";
		echo $ret ? "alert('更新成功');" : "alert('更新失败');";
		echo "</script>";
	}

	/** 清除玩家的积分值 */
	static public function AActivenessCacheUp() {
		$ret = false;
		$list = B_DB::instance('CityActiveness')->getAll();
		foreach ($list as $info) {
			$rc = new B_Cache_RC(T_Key::CITY_ACTIVENESS, $info['city_id']);
			$upDB = $rc->delete();

			$info['activeness_sum'] = 0;
			$info['activeness_arr'] = '';
			$ret = $upDB && B_DB::instance('CityActiveness')->update($info,$info['city_id']);
		}
		echo "<script>";
		echo $ret ? "alert('更新成功');" : "alert('更新失败');";
		echo "</script>";
	}

	static public function AConfigBuild() {
		$baseCfg = M_Config::getVal();
		$pageData = array('baselist' => $baseCfg);

		$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
		if ($act == 'edit' && !empty($_POST['build_open'])) {
			$tmp = array();
			foreach ($_REQUEST['build_open'] as $zone => $val) {
				$arr = explode("\n", $val);
				foreach ($arr as $v) {
					$tmp[$zone][] = explode(",", trim($v));
				}
			}
			$data['build_open'] = $tmp;
			$flag = M_Config::setVal($data);

			echo $flag ? "<script>alert('保存成功');</script>" : "<script>alert('保存失败');</script>";
			exit;

		}
		B_View::setVal('pageData', $pageData);

		B_View::render('System/Config_Build');
	}


	static public function AConfigLottery() {
		$baseCfg = M_Config::getVal();
		if (!isset($baseCfg['lottery'])) {
			$data['lottery'] = B_Cache_File::load('lottery');
			$flag = M_Config::setVal($data);
			$baseCfg['lottery'] = $data['lottery'];
		}

		$pageData = array('baselist' => $baseCfg);
		$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';

		if ($act == 'edit' && !empty($_POST['OutRate'])) {
			$tmp['OutList'] = explode(',', $_REQUEST['OutList']); //出现包裹ID列表
			$tmp['OutRate'] = intval($_REQUEST['OutRate']); //出现频率
			$tmp['ItemNum'] = 18;
			$Package = array();
			$tmpPackage = $_REQUEST['Package'];

			foreach ($tmpPackage as $pid => $val) {
				if (!empty($val['rate'])) {
					$tmpData = array();
					$arr = explode("\n", $val['data']);
					foreach ($arr as $tmpVal) {
						$tmpArr = explode(",", trim($tmpVal));
						if (!empty($tmpArr[0])) {
							$tt = array(
								'id' => trim($tmpArr[0]),
								'num' => intval($tmpArr[1]),
								'rate1' => intval($tmpArr[2]),
							);
							$tmp2Arr = explode('|', trim($tmpArr[3]));
							foreach ($tmp2Arr as $rv) {
								if (!empty($rv)) {
									list($t, $r) = explode("_", $rv);
									$tt['rate2'][$t] = intval($r);
								}
							}
							$tmpData[] = $tt;
						}

					}
					$val['data'] = $tmpData;


					$Package[$pid] = $val;
				}
			}

			$tmp['Package'] = $Package;

			$data['lottery'] = $tmp;
			$flag = M_Config::setVal($data);

			echo $flag ? "<script>alert('保存成功');</script>" : "<script>alert('保存失败');</script>";
			exit;

		}
		B_View::setVal('pageData', $pageData);

		B_View::render('System/Config_Lottery');
	}

	static public function AConfigPayAward() {
		$baseCfg = M_Config::getVal();
		$act = isset($_POST['act']) ? $_POST['act'] : '';
		if ($act == 'edit') //判断保存
		{
			$arradd = $arronce = array();

			$arronce['start'] = $_POST['pay_once_award']['start_time'];
			$arronce['end'] = $_POST['pay_once_award']['end_time'];
			$tmponce = explode("\n", $_POST['pay_once_award']['data']);
			$i = 1;
			foreach ($tmponce as $val) {
				list($s, $e, $id) = explode("_", trim($val));
				if (!empty($s) && !empty($e) && !empty($id)) {
					$arronce['data'][$i] = array($s, $e, intval($id));
					$i++;
				}
			}

			$arradd['start'] = $_POST['pay_add_award']['start_time'];
			$arradd['end'] = $_POST['pay_add_award']['end_time'];
			$tmpadd = explode("\n", $_POST['pay_add_award']['data']);
			$i = 1;
			foreach ($tmpadd as $val) {
				list($num, $id) = explode("_", trim($val));
				if (!empty($num) && !empty($id)) {
					$arradd['data'][$i] = array(intval($num), intval($id));
					$i++;
				}
			}

			$upData['config_pay_add_award'] = $arradd;
			$upData['config_pay_once_award'] = $arronce;


			$upData['first_recharge_id'] = $_POST['first_recharge_id'];

			if (!empty($upData)) {
				$ret = M_Config::setVal($upData);

				if ($ret) {
					echo "<script>";
					echo "alert('保存成功');";
					echo "</script>";
				} else {
					echo "<script>";
					echo "alert('保存失败');";
					echo "</script>";
				}
				exit;
			}
		}

		$pageData = $baseCfg;
		B_View::setVal('pageData', $pageData);
		B_View::render('System/Config_PayAward');
	}

	static public function AConfigInviteFriend() {
		$baseCfg = M_Config::getVal();
		$act = isset($_POST['act']) ? $_POST['act'] : '';
		if ($act == 'edit') //判断保存
		{
			$arrInvite = $arrLive = array();

			$arrLive['start'] = $_POST['friend_live_award']['start_time'];
			$arrLive['end'] = $_POST['friend_live_award']['end_time'];
			$tmp = explode("\n", $_POST['friend_live_award']['data']);
			$i = 1;
			foreach ($tmp as $val) {
				list($s, $e, $id) = explode("_", trim($val));
				if (!empty($s) && !empty($e) && !empty($id)) {
					$arrLive['data'][$i] = array($s, $e, intval($id));
					$i++;
				}
			}

			$arrInvite['start'] = $_POST['friend_invite_award']['start_time'];
			$arrInvite['end'] = $_POST['friend_invite_award']['end_time'];
			$tmp = explode("\n", $_POST['friend_invite_award']['data']);
			$i = 1;
			foreach ($tmp as $val) {
				list($num, $id) = explode("_", trim($val));
				if (!empty($num) && !empty($id)) {
					$arrInvite['data'][$i] = array(intval($num), intval($id));
					$i++;
				}
			}

			$upData['friend_live_award'] = $arrLive;
			$upData['friend_invite_award'] = $arrInvite;


			if (!empty($upData)) {
				$ret = M_Config::setVal($upData);

				if ($ret) {
					echo "<script>";
					echo "alert('保存成功');";
					echo "</script>";
				} else {
					echo "<script>";
					echo "alert('保存失败');";
					echo "</script>";
				}
				exit;
			}
		}

		$pageData = $baseCfg;
		B_View::setVal('pageData', $pageData);
		B_View::render('System/Config_InviteFriend');
	}
}

?>
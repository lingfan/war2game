<?php

/**
 * 基础定义接口
 */
class C_Base extends C_I {
	/**
	 * 获取城内已占用坐标的ASCII字符串数据
	 * @author chenhui on 20110705
	 * @param int $areaId 洲ID
	 * @param int $level 城市等级
	 * @return array
	 */
	public function AGetCityMapBlock($areaId, $level = 1) {
		$errNo = '';
		$data['block'] = '';
		$level = 1;
		if (isset(T_App::$map[$areaId])) {
			$data = M_Base::city_map_block();
			$data['block'] = isset($data['block'][$areaId][$level]) ? $data['block'][$areaId][$level] : '';
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 装备相关配置
	 * @author Hejunyun
	 */
	public function AEquip() {
		$data = M_Base::info();
		$suitList = M_Base::equipSuitAll();
		$data['suit_list'] = array();
		$data['suit_list'] = $suitList;
		if (!empty($data['suit_list'])) {
			foreach ($data['suit_list'] as $key => $value) {
				$data['suit_list'][$key]['effect'] = !empty($data['suit_list'][$key]['effect']) ? json_decode($data['suit_list'][$key]['effect'], true) : array();
				if (!empty($data['suit_list'][$key]['effect'])) {
					foreach ($data['suit_list'][$key]['effect'] as $key1 => $value1) {
						foreach ($value1 as $key2 => $value2) {
							$data['suit_list'][$key]['effect'][$key1][$key2] = explode('|', $value2);
							if (isset($data['suit_list'][$key]['effect'][$key1][$key2][3]) && $data['suit_list'][$key]['effect'][$key1][$key2][3] == 'LAND') {
								$data['suit_list'][$key]['effect'][$key1][$key2][3] = 1;
							} else if (isset($data['suit_list'][$key]['effect'][$key1][$key2][3]) && $data['suit_list'][$key]['effect'][$key1][$key2][3] == 'SKY') {
								$data['suit_list'][$key]['effect'][$key1][$key2][3] = 2;
							}
						}
					}
				}
			}
		}

		$errNo = '';
		return B_Common::result($errNo, $data);
	}

	public function AUnion() {
		$data = M_Base::info();
		$errNo = '';
		return B_Common::result($errNo, $data);
	}


	public function ASkill() {
		$data = M_Base::skill();
		$errNo = '';
		return B_Common::result($errNo, $data);
	}

	public function ABuild() {
		$data = M_Base::build();
		$errNo = '';
		return B_Common::result($errNo, $data);
	}

	public function ATech() {
		$data = M_Base::tech();
		$errNo = '';
		return B_Common::result($errNo, $data);
	}

	public function AWeapon() {
		$data = M_Base::weapon();
		$errNo = '';
		return B_Common::result($errNo, $data);
	}

	public function ATempWeapon() {
		$tempWeapon = M_Config::getVal('temp_weapon');
		$data = array();
		foreach ($tempWeapon as $id => $val) {
			array_unshift($val, $id);
			$data[] = $val;
		}
		$errNo = '';
		return B_Common::result($errNo, $data);
	}


	public function AProps() {
		$data = M_Base::props();
		$errNo = '';
		return B_Common::result($errNo, $data);
	}

	/** VIP抽取装备奖励 */
	public function AVip() {
		$data = M_Base::vip();
		$errNo = '';
		return B_Common::result($errNo, $data);
	}

	/** 获取所有VIP基础配置数据 */
	public function AVipConf() {
		$vipConf = M_Vip::getVipConfig();
		$errNo = '';

		$marchTime = array(); //减少出征时间
		$arrMarch = $vipConf['DECR_MARCH_TIME'];
		foreach ($arrMarch as $vipLev => $strVipMarch) {
			$data = array();
			$arrVipMarch = explode(',', $strVipMarch);
			if (!empty($arrVipMarch) && is_array($arrVipMarch)) {
				foreach ($arrVipMarch as $perVal) {
					if ($perVal > 0) {
						$perPrice = M_Vip::getDecrMarchTimeCost($vipLev, $perVal);
						$data[] = array($perVal, $perPrice); //百分比值、价格
					}
				}
			}
			$marchTime[$vipLev] = $data;
		}

		$package = array(); //VIP礼包
		$arrPack = $vipConf['VIP_PACKAGE'];
		foreach ($arrPack as $vipLev => $strPackage) {
			if (!empty($strPackage)) {
				$arrPackage = explode('_', $strPackage);
				$package[$vipLev] = $arrPackage[1];
			} else {
				$package[$vipLev] = 0;
			}
		}

		$arr_special_slotId = array(); //有权限开启特殊武器ID数组
		$arr_slotId = $vipConf['SPECIAL_SLOTID'];
		foreach ($arr_slotId as $key => $str_slotId) {
			$arr_special_slotId[$key] = explode(',', $str_slotId);
		}

		$vipConf['DECR_MARCH_TIME'] = $marchTime; //减少出征时间
		$vipConf['VIP_PACKAGE'] = $package; //VIP礼包
		$vipConf['SPECIAL_SLOTID'] = $arr_special_slotId; //各VIP等级有权限开启特殊武器ID数组
		$vipConf['SLOT_COST'] = M_Config::getVal('weapon_slot_cost'); //各槽ID所需花费

		$milRankRenownConf = M_Config::getVal('mil_rank_renown');
		$milRankRenownData = array();
		foreach ($milRankRenownConf as $key => $tmpVal) {
			if (!empty($tmpVal[0])) {
				$key = $key + 1;
				$milRankRenownData[$key]['exp'] = $tmpVal[0];

				$awardArr = M_Award::rateResult($tmpVal[1]);
				$milRankRenownData[$key]['once'] = M_Award::toText($awardArr);

				$awardArr = M_Award::rateResult($tmpVal[2]);
				$milRankRenownData[$key]['daily'] = M_Award::toText($awardArr);

				$addArr = explode('_', $tmpVal[3]);
				$milRankRenownData[$key]['atk'] = $addArr[0];
				$milRankRenownData[$key]['def'] = $addArr[1];
				$milRankRenownData[$key]['hp'] = $addArr[2];
				$milRankRenownData[$key]['crit'] = $addArr[3];
			}
		}
		$vipConf['MIL_RANK_RENOWN'] = $milRankRenownData; //威望对于的军衔数据

		$data = $vipConf;
		return B_Common::result($errNo, $data);
	}


	public function ATask() {
		$data = M_Base::task();
		$errNo = '';

		return B_Common::result($errNo, $data);
	}

	public function AWarMapCell() {
		$data = M_Base::war_map_cell();
		$errNo = '';

		return B_Common::result($errNo, $data);
	}

	/** VIP特殊武器槽配置 */
	public function ASpecialConf() {
		$obj = new C_Base();
		$vipConf = $obj->AVipConf(); //基础配置
		$arr_special_slotId = $vipConf['data']['Data']['SPECIAL_SLOTID'];
		$weapon_slot_cost = M_Config::getVal('weapon_slot_cost');
		$data = array(
			'SPECIAL_SLOTID' => $arr_special_slotId,
			'SLOT_COST' => $weapon_slot_cost,
		);

		$errNo = '';

		return B_Common::result($errNo, $data);
	}

	/** 英雄配置 */
	public function AHero() {
		$heroExchange = M_Config::getVal('hero_exchange');
		$data = array(
			'hero_exchange' => $heroExchange,
		);
		$errNo = '';

		return B_Common::result($errNo, $data);
	}


	public function AMultiFB() {
		$data = array();
		$list = M_MultiFB::getBaseList();
		foreach ($list as $val) {
			list($passFBNo, $cityLevel, $heroNum, $renown, $maxPlayerNum) = explode(',', $val['join_rule']);

			$tmp = array();
			foreach ($val['def_line'] as $po => $tVal) {
				$npcInfo = M_NPC::getInfo($tVal[0]);
				$tmp[$po] = array($npcInfo['nickname'], $tVal[2]);
			}

			$data[] = array(
				'Id' => $val['id'],
				'FaceId' => $val['id'],
				'Type' => $val['type'],
				'Name' => $val['name'],
				'Desc' => $val['fb_desc'],
				'JoinFBNo' => $passFBNo,
				'JoinCityLv' => $cityLevel,
				'JoinHeroNum' => $heroNum,
				'JoinRenown' => $renown,
				'JoinPlayerNum' => $maxPlayerNum,
				'AwardDesc' => $val['award_desc'],
				'NpcDefLine' => $tmp,
			);
		}
		$errNo = '';

		return B_Common::result($errNo, $data);
	}

	public function AExchangeEquip($lv = 0, $suitId = 0) {
		$data = $tmpData = array();
		$baseList = M_Base::exchangeAll();
		if ($lv > 0 && $suitId > 0) {
			$baseEquip = M_Base::equipAll();
			$list = isset($baseList['cate'][2][$lv][$suitId]) ? $baseList['cate'][2][$lv][$suitId] : array();
			foreach ($list as $item) {
				if (isset($baseEquip[$item['new_props']])) {
					$val = $baseEquip[$item['new_props']];
					$equipInfo = array(
						'Id' => $val['id'],
						'Name' => $val['name'],
						'Pos' => $val['pos'],
						'FaceId' => $val['face_id'],
						'NeedLevel' => $val['need_level'],
						'Level' => $val['level'],
						'MaxLevel' => $val['max_level'],
						'Quality' => $val['quality'],
						'BaseLead' => $val['base_lead'],
						'BaseCommand' => $val['base_command'],
						'BaseMilitary' => $val['base_military'],
						'IsLocked' => isset($val['is_locked']) ? $val['is_locked'] : 0,
						'ExtAttrName' => $val['ext_attr_name'],
						'ExtAttrRate' => $val['ext_attr_rate'],
						'ExtAttrSkill' => $val['ext_attr_skill'],
						'IsUse' => 0,
						'OnSale' => 0,
						'SuitId' => $val['suit_id'],
						'Desc1' => $val['desc_1'],
						'Desc2' => $val['desc_2'],
						'CreateAt' => 0,
						'Flag' => isset($val['flag']) ? $val['flag'] : 7,
						'HeroName' => 0,
						'HeroQuality' => 0,
					);

					$tmp = array();
					foreach ($item['need_props'] as $k => $v) {
						$tmp[] = "{$k},{$v}";
					}

					$tmpData[] = array(
						'Id' => $item['id'],
						'Name' => $item['name'],
						'Desc' => $item['desc'],
						'NeedProps' => implode("|", $tmp),
						'NewItem' => $equipInfo,
						'BaseSuccRate' => $item['base_succ'],
						'CostVal' => $item['cost_val'],
						'Sort' => $item['sort'],
						'StartTime' => strtotime($item['start_time']),
						'EndTime' => strtotime($item['end_time']),
					);
				}
			}
			$data['List'] = $tmpData;
		} else {
			$list = isset($baseList['cate'][2]) ? $baseList['cate'][2] : array();
			foreach ($list as $lv => $val) {
				$tmp = array();
				$need_props = array();
				foreach ($val as $suitId => $itemList) {
					foreach ($itemList as $tmpItem) {
						$tmpNeed = array();
						foreach ($tmpItem['need_props'] as $k => $v) {
							$tmpNeed[] = "{$k},{$v}";
						}
						$need_props[] = array($suitId, implode("|", $tmpNeed));
					}

					$tmp[] = $suitId;
				}
				$tmpData[] = array('lv' => $lv, 'suit_ids' => $tmp, 'need' => $need_props);
			}
			$data['List'] = $tmpData;

			$tmp = array();
			$succRate = B_Utils::str2arr(M_Config::getVal('exchange_milpay_succ_rate'));
			foreach ($succRate as $k => $v) {
				$tmp[] = array('cost' => $k, 'sucPer' => $v);
			}

			$data['SuccRate'] = $tmp;
		}
		$errNo = '';

		return B_Common::result($errNo, $data);
	}

	public function AExchangeActivity() {
		$tmpData = array();
		$baseList = M_Base::exchangeAll();
		$list = isset($baseList['cate'][3]) ? $baseList['cate'][3] : array();
		foreach ($list as $item) {
			$tmp = array();
			foreach ($item['need_props'] as $k => $v) {
				$tmp[] = "{$k},{$v}";
			}
			$tmpData[] = array(
				'Id' => $item['id'],
				'Name' => $item['name'],
				'Desc' => $item['desc'],
				'NeedProps' => implode("|", $tmp),
				'NewItem' => $item['new_props'],
				'BaseSuccRate' => $item['base_succ'],
				'CostVal' => $item['cost_val'],
				'Sort' => $item['sort'],
				'StartTime' => strtotime($item['start_time']),
				'EndTime' => strtotime($item['end_time']),
			);
		}
		$data['List'] = $tmpData;
		$errNo = '';
		return B_Common::result($errNo, $data);
	}

	public function AExchangeProps($subType = 0) {
		$data = $tmpData = array();
		$baseList = M_Base::exchangeAll();
		if ($subType > 0) {
			$list = isset($baseList['cate'][1][$subType]) ? $baseList['cate'][1][$subType] : array();
			foreach ($list as $item) {
				$tmp = array();
				foreach ($item['need_props'] as $k => $v) {
					$tmp[] = "{$k},{$v}";
				}
				$tmpData[] = array(
					'Id' => $item['id'],
					'Name' => $item['name'],
					'Desc' => $item['desc'],
					'NeedProps' => implode("|", $tmp),
					'NewItem' => $item['new_props'],
					'BaseSuccRate' => $item['base_succ'],
					'CostVal' => $item['cost_val'],
					'Sort' => $item['sort'],
					'StartTime' => strtotime($item['start_time']),
					'EndTime' => strtotime($item['end_time']),
				);
			}
			$data['List'] = $tmpData;
		} else {
			$list = isset($baseList['cate'][1]) ? $baseList['cate'][1] : array();
			$tmpData = array_keys($list);
			$data['List'] = $tmpData;

			$tmp = array();
			$succRate = B_Utils::str2arr(M_Config::getVal('exchange_milpay_succ_rate'));
			foreach ($succRate as $k => $v) {
				$tmp[] = array('cost' => $k, 'sucPer' => $v);
			}

			$data['SuccRate'] = $tmp;
		}
		$errNo = '';
		return B_Common::result($errNo, $data);
	}

	public function AExchange() {
		$data = array();
		$baseList = M_Base::exchangeAll();
		$item = isset($baseList['data'][1]) ? $baseList['data'][1] : array();
		if ($item) {
			$tmp = array();
			foreach ($item['need_props'] as $k => $v) {
				$tmp[] = "{$k},{$v}";
			}

			$data[] = array(
				'Id' => $item['id'],
				'Name' => $item['name'],
				'Desc' => $item['desc'],
				'NeedProps' => implode("|", $tmp),
				'NewItem' => $item['new_props'],
				'BaseSuccRate' => $item['base_succ'],
				'CostVal' => $item['cost_val'],
				'Sort' => $item['sort'],
				'StartTime' => strtotime($item['start_time']),
				'EndTime' => strtotime($item['end_time']),
			);
		}
		$errNo = '';

		return B_Common::result($errNo, $data);
	}

	public function AAssistanceList() {
		$data = array();
		$rc = new B_Cache_RC(T_Key::SERVER_NEWS);
		$infoArr = $rc->jsonget();
		if (empty($infoArr)) {
			$infoArr = B_DB::instance('ServerNews')->all();
			$rc->jsonset($infoArr);
		}
		$data = array_values($infoArr);
		$errNo = '';

		return B_Common::result($errNo, $data);
	}

	public function AProbe() {
		$data = array();
		$info = M_Base::probeAll();
		foreach ($info as $id => $val) {
			$data[] = array(
				'Id' => $val['id'],
				'Title' => $val['title'],
			);
		}
		$errNo = '';

		return B_Common::result($errNo, $data);

	}

	/**
	 * 图鉴功能 (图纸1,军官2,技能3)
	 * @param int $cate
	 * @param int $type
	 * @param int $page
	 * @param int $offset
	 */
	public function ADetail($cate = 0, $type = 1, $page = 1, $offset = 10) {
		$data = array();
		$errNo = '';
		$base = M_Config::getVal('help_detail');
		$offset = min(50, $offset);
		if (isset($base[$cate][$type])) {
			$ids = $base[$cate][$type];
			//list($totalPage, $page, $idArr) = M_Formula::arrPage($ids, $page, $offset);
			$rows = array();
			if ($cate == 1) {
				//$rows = $ids;

				foreach ($ids as $id) {
					$baseinfo = M_Weapon::baseInfo($id);
					$rows[] = array(
						'WeaponId' => $baseinfo['id'],
						'WeaponName' => $baseinfo['name'],
						'ArmyName' => $baseinfo['army_name'],
						'Features' => $baseinfo['features'],
						'Detail' => $baseinfo['detail'],
						'ArmyId' => $baseinfo['army_id'],
						'NeedArmyLv' => $baseinfo['need_army_lv'],
						'MarchType' => $baseinfo['march_type'],
						'ShowType' => $baseinfo['show_type'],
						'Sort' => $baseinfo['sort'],
						'IsSpecial' => $baseinfo['is_special'],
						'IsNPC' => $baseinfo['is_npc'],
						'LifeValue' => $baseinfo['life_value'],
						'AttLand' => $baseinfo['att_land'],
						'AttSky' => $baseinfo['att_sky'],
						'AttOcean' => $baseinfo['att_ocean'],
						'DefLand' => $baseinfo['def_land'],
						'DefSky' => $baseinfo['def_sky'],
						'DefOcean' => $baseinfo['def_ocean'],
						'Speed' => $baseinfo['speed'],
						'MoveRange' => $baseinfo['move_range'],
						'MoveType' => $baseinfo['move_type'],
						'ShotRangeMin' => $baseinfo['shot_range_min'],
						'ShotRangeMax' => $baseinfo['shot_range_max'],
						'ShotType' => $baseinfo['shot_type'],
						'ViewRange' => $baseinfo['view_range'],
						'Carry' => $baseinfo['carry'],
						'CostGold' => $baseinfo['cost_gold'],
						'CostFood' => $baseinfo['cost_food'],
						'CostOil' => $baseinfo['cost_oil'],
						'CostTime' => $baseinfo['cost_time'],
						'MarchCostOil' => $baseinfo['march_cost_oil'],
						'MarchCostFood' => $baseinfo['march_cost_food'],
						'NeedTech' => $baseinfo['need_tech'],
						'NeedBuild' => $baseinfo['need_build']
					);
				}

			} else if ($cate == 2) {
				foreach ($ids as $id) {
					$heroInfo = M_Hero::baseInfo($id);
					if (!empty($heroInfo['id'])) {
						$skillInfo = M_Skill::getBaseInfo($heroInfo['skill_slot']);
						$rows[] = array(
							'HeroId' => $id,
							'NickName' => $heroInfo['nickname'],
							'Gender' => $heroInfo['gender'],
							'Quality' => $heroInfo['quality'],
							'Level' => (int)$heroInfo['level'],
							'FaceId' => $heroInfo['face_id'],
							'IsLegend' => 1,
							'AttrLead' => $heroInfo['attr_lead'],
							'AttrCommand' => $heroInfo['attr_command'],
							'AttrMilitary' => $heroInfo['attr_military'],
							'AttrEnergy' => $heroInfo['attr_energy'],
							'GrowRate' => $heroInfo['grow_rate'],
							'SkillSlot' => array('FaceId' => $skillInfo['id'], 'Name' => $skillInfo['name'], 'Desc' => $skillInfo['desc']),
							'Desc' => $heroInfo['desc'],
						);
					}

				}

			} else if ($cate == 3) {
				//$rows = $ids;

				foreach ($ids as $id) {
					$info = M_Skill::getBaseInfo($id);
					$rows[] = array(
						'Id' => $info['id'],
						'Name' => $info['name'],
						'FaceId' => $info['id'],
						'Type' => $info['type'],
						'Level' => $info['level'],
						'Desc' => $info['desc'],

					);
				}

			}
			//$data = array($totalPage, $page, $rows);
			$data = $rows;
		}

		return B_Common::result($errNo, $data);

	}
}

?>
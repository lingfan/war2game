<?php

/**
 * 联盟控制器
 */
class C_Union extends C_I {
	/**
	 * 创建联盟
	 * @author Hejunyun
	 * @param int $faceId 旗帜ID
	 * @param string $name 联盟名称
	 * @param string $notice 联盟公告
	 */
	public function ACreate($faceId, $name, $notice) {
		$data = array();
		$errNo = T_ErrNo::ERR_ACTION;
		$nameLen = B_Utils::len($name);
		$noticeLen = B_Utils::len($notice);
		$notice = B_Utils::isBlockName($notice, true);

		if (!in_array($faceId, M_Union::$faceArr)) {
			$errNo = T_ErrNo::ERR_PARAM; //非法旗帜
		} elseif ($nameLen < 4 || $nameLen > 14) {
			$errNo = T_ErrNo::ERR_LONG_NAME; //名称长度不合法
		} elseif (B_Utils::isBlockName($name)) {
			$errNo = T_ErrNo::ERR_NAME; //非法名称
		} elseif ($noticeLen > 200) {
			$errNo = T_ErrNo::ERR_LONG_NOTICE; //公告长度不合法
		} else {
			if (isset($cityInfo['id'])) {
				$cityId = $cityInfo['id'];
				//判断是否已加入联盟
				if ($cityInfo['union_id'] > 0) {
					$errNo = T_ErrNo::HAS_JOINED_UNION; //已加入联盟
				} else {
					$resInfo = $this->objPlayer->Res()->get(); //获取资源信息
					if ($resInfo['gold'] < M_Config::getVal('union_create_cost')) {
						$errNo = T_ErrNo::NO_ENOUGH_GOLD; //资源不够
					} elseif ($cityInfo['mil_medal'] < M_Config::getVal('union_create_need_medal')) {
						$errNo = T_ErrNo::NO_ENOUGH_MILMEDAL; //军功不够
					} elseif (M_Union::getUnionByName($name)) {
						$errNo = T_ErrNo::UNION_NAME_EXIST; //联盟名称已存在
					} else {
						$data = array(
							'face_id' => $faceId,
							'name' => $name,
							'notice' => $notice,
							'level' => 1,
							'boss' => $cityInfo['nickname'],
							'create_nick_name' => $cityInfo['nickname'],
							'create_city_id' => $cityId,
							'total_renown' => $cityInfo['renown'],
							'create_at' => time()
						);
						if (M_Union::addUnion($data)) {
							$errNo = '';
							M_MapWild::syncWildMapBlockCache($cityInfo['pos_no']);
						} else {
							$errNo = T_ErrNo::ERR_CREATE_UNION; //创建联盟失败
						}
					}
				}
			}
		}


		return B_Common::result($errNo, $data);
	}

	/** 所有军团列表 */
	public function AList($page = 1, $pageSize = 10) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$page = intval($page);
		$page = max(1, $page);
		$pageSize = intval($pageSize); //页大小
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (isset($cityInfo['id'])) {
			$applyList = M_Union::getUserAppList($cityInfo['id']);
			$applyList = $applyList ? $applyList : array();
			$ret = M_Union::getList($page, $pageSize);
			$data['page'] = $ret['page'];
			$data['sumPage'] = $ret['sumPage'];
			foreach ($ret['list'] as $id) {
				$info = M_Union::getInfo($id);
				$data['list'][] = array(
					'ID' => $info['id'],
					'FaceId' => $info['face_id'],
					'Name' => $info['name'],
					'Level' => $info['level'],
					'Rank' => $info['rank'],
					'Boss' => $info['boss'],
					'CreateNickName' => $info['create_nick_name'],
					'TotalPerson' => $info['total_person'],
					'TotalRenown' => $info['total_renown'],
					'Notice' => $info['notice'],
					'CreateAt' => $info['create_at'],
					'IsApply' => in_array($info['id'], $applyList) ? 1 : 0,
				);
			}


			$errNo = '';

		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 我的联盟
	 * @author Hejunyun
	 */
	public function AMyUnion() {

		$errNo = T_ErrNo::ERR_ACTION;
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$data = $techList = array();
		if (isset($cityInfo['id']) && $cityInfo['union_id'] > 0) {
			$info = M_Union::getInfo($cityInfo['union_id']);
			if ($info) {
				$memberInfo = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);
				if ($memberInfo) {
					$myPos = $memberInfo['position'];
					$isAward = M_Union::isAward($cityInfo['id']);

					if (!$info['tech_data']) {
						//初始化军团科技
						$ret = M_Union::initUnionTech($info['id']);
						$ret && $tmpArr = json_decode($ret, true);
					} else {
						$tmpArr = json_decode($info['tech_data'], true);
					}

					foreach (M_Union::$unionTechName as $techId => $name) {
						$lv = isset($tmpArr[$techId]) ? $tmpArr[$techId] : 0;
						$nextNeedCoin = M_Formula::upgradeUnionTechNeedCoin($techId, $lv + 1);
						$nextNeedLevel = M_Formula::upgradeUnionTechNeedLevel($techId, $lv + 1);
						$techList[] = array($techId, $lv, $nextNeedCoin, $nextNeedLevel);
					}


					$hireTimes = array();
					$hireTimesNum = 0;
					$SurplusTimes = 0;
					$hireCityInfo = M_City::getInfo($cityInfo['id']);
					$ownUnionId = $hireCityInfo['union_id'];
					$hireTimes = M_Union::getInviteTimesList($ownUnionId);
					$hireTimesNum = isset($hireTimes[$cityInfo['union_id']]) ? $hireTimes[$cityInfo['union_id']] : 0;
					$SurplusTimes = M_Union::UNION_HIRE_TIMES - $hireTimesNum;
					if ($SurplusTimes < 0) {
						$SurplusTimes = 0;
					}

					$baseUnionUp = M_Config::getVal('union_up');
					list($costGold, $costMilpay, $maxPerson) = $baseUnionUp[$info['level']];

					$data = array(
						'ID' => $info['id'],
						'FaceId' => $info['face_id'],
						'Name' => $info['name'],
						'Coin' => $info['coin'],
						'Level' => $info['level'],
						'Notice' => $info['notice'],
						'Rank' => $info['rank'],
						'Boss' => $info['boss'],
						'CreateNickName' => $info['create_nick_name'],
						'TotalPerson' => $info['total_person'],
						'MaxPerson' => $maxPerson,
						'TotalRenown' => $info['total_renown'],
						'StationNo' => $info['station_no'],
						'StationData' => json_decode($info['station_data'], true),
						'TechData' => $techList,
						//'RelFriend' => json_decode($info['rel_friend'], true),
						//'RelEnemy' => json_decode($info['rel_enemy'], true),
						'CreateAt' => $info['create_at'],
						'MyPos' => $myPos,
						'MyPoint' => $memberInfo['point'],
						'IsAward' => $isAward ? 1 : 0,
						'HireTimes' => $SurplusTimes,
					);

					$errNo = '';
				} else {
					M_City::setCityInfo($cityInfo['id'], array('union_id' => 0));
					Logger::error(array(__METHOD__, 'err union member'), $cityInfo['union_id']);
					$errNo = T_ErrNo::UNION_NOT_EXIST;
				}
			}
		}


		return B_Common::result($errNo, $data);
	}

	/**
	 * 玩家申请的联盟
	 */
	public function AApplyList() {

		$errNo = T_ErrNo::ERR_ACTION;
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$data = array();
		if (isset($cityInfo['id'])) {
			$idList = M_Union::getUserAppList($cityInfo['id']);
			if (!empty($idList)) {
				foreach ($idList as $unionId) {
					$unionInfo = M_Union::getInfo($unionId);
					if (!empty($unionInfo)) {
						$data[] = array(
							'ID' => $unionInfo['id'],
							'Name' => $unionInfo['name'],
							'FaceId' => $unionInfo['face_id'],
							'Level' => $unionInfo['level'],
							'Boss' => $unionInfo['boss'],
							'Rank' => $unionInfo['rank'],
							'TotalPerson' => $unionInfo['total_person'],
							'TotalRenown' => $unionInfo['total_renown'],
						);
					}
				}
			}

			$errNo = '';
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 申请加入联盟
	 * @author Hejunyun
	 * @param int $unionId 联盟ID
	 */
	public function AJoin($unionId) {

		$errNo = T_ErrNo::ERR_ACTION;
		$unionId = intval($unionId);
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$now = time();
		if (isset($cityInfo['id']) && $unionId) {
			$UnionCd = M_Union::getUnionCd($cityInfo['id']);
			$arrUnionT = explode('_', $UnionCd);
			$data['cd_union'] = !empty($arrUnionT[0]) ? $arrUnionT[0] : 0;
			if (empty($arrUnionT[0]) || $arrUnionT[0] < $now) {
				if ($cityInfo['union_id'] > 0) {
					$errNo = T_ErrNo::HAS_JOINED_UNION; //已加入联盟
				} else {
					$unionInfo = M_Union::getInfo($unionId); //联盟信息
					if (!isset($unionInfo['id'])) {
						$errNo = T_ErrNo::UNION_NOT_EXIST; //该联盟不存在
					} else {
						$baseUnionUp = M_Config::getVal('union_up');
						list($costGold, $costMilpay, $maxPerson) = $baseUnionUp[$unionInfo['level']];

						$accommodSum = $maxPerson;
						if ($unionInfo['total_person'] < $accommodSum) {
							//申请加入联盟
							$res = M_Union::userAppUnion($cityInfo['id'], $unionId);
							if ($res) {
								$objPlayer->Quest()->check('union_apply', array('num' => 1));
								$objPlayer->save();

								$errNo = '';
							}
						} else {
							$errNo = T_ErrNo::FULL_OF_UNION; //联盟人数已满
						}
					}
				}
			} else {
				$errNo = T_ErrNo::UNION_NOT_CD; //很遗憾！军团冷却中。”
			}
		}


		return B_Common::result($errNo, $data);
	}

	/**
	 * 取消申请
	 * @author Hejunyun
	 * @param int $unionId
	 */
	public function ACancelApply($unionId) {

		$errNo = T_ErrNo::ERR_ACTION;

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (isset($cityInfo['id'])) {
			if ($cityInfo['union_id'] > 0) {
				M_Union::delUnionAppKey($cityInfo['id']);
				$errNo = T_ErrNo::HAS_JOINED_UNION; //已加入联盟
			} else {
				$res = M_Union::userUnAppUnion($cityInfo['id'], $unionId);
				if ($res) {
					$errNo = '';
				}
			}
		}

		$data = array();
		return B_Common::result($errNo, $data);
	}


	/**
	 * 退出联盟
	 * @author Hejunyun
	 */
	public function ALeave() {

		$errNo = T_ErrNo::ERR_ACTION;
		$now = time();
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (isset($cityInfo['id']) && $cityInfo['union_id'] > 0) {
			$UnionCd = M_Union::getUnionCd($cityInfo['id']);
			$arrUnionT = explode('_', $UnionCd);
			$data['cd_union'] = !empty($arrUnionT[0]) ? $arrUnionT[0] : 0;
			if (empty($arrUnionT[0]) || $arrUnionT[0] < $now) {
				$ownMemberInfo = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);
				if ($ownMemberInfo && $ownMemberInfo['position'] == 0) {
					$res = M_Union::delUnionMember($ownMemberInfo);
					if ($res) {
						$ApplyUnionCd = M_Config::getVal('cd_apply_union');
						$unionCd = ($now + $ApplyUnionCd * T_App::ONE_HOUR) . '_' . T_App::ADDUP_CAN;
						M_Union::setUnionCd($cityInfo['id'], $unionCd);
						M_MapWild::syncWildMapBlockCache($cityInfo['pos_no']);

						$errNo = '';
					}
				} elseif ($ownMemberInfo['position'] > 0) {
					$errNo = T_ErrNo::UNION_MOVE_POWER;
				} else {
					$errNo = T_ErrNo::NOT_IN_UNION;
				}
			} else {
				$errNo = T_ErrNo::UNION_NOT_CD; //很遗憾！军团冷却中。”
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 联盟成员
	 * @author Hejunyun
	 */
	public function AMember($page = 1) {

		$errNo = T_ErrNo::ERR_ACTION;
		$page = intval($page);
		$page = max($page, 1);
		$pageSize = M_Union::$unionConf['page_offset'];
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (isset($cityInfo['id'])) {
			if ($cityInfo['union_id'] > 0) {
				$list = M_Union::getUnionMemberList($cityInfo['union_id']);

				if (is_array($list)) {
					$bossArr = array(); //军团长
					$boss2Arr = array(); //副军团长
					$tmp3Arr = array(); //普通成员
					$resArr = array(); //合并排序后的结果
					foreach ($list as $key => $val) {
						$cityInfo = M_City::getInfo($val['city_id']);
						$IsRescue = M_March_Hold::exist($cityInfo['pos_no']);
						$IsRescue = !empty($IsRescue) ? $IsRescue : 0;
						list($zone, $posx, $poxy) = M_MapWild::calcWildMapPosXYByNo($cityInfo['pos_no']);
						$tmp = array(
							'CityId' => $val['city_id'],
							'NickName' => $val['nickname'],
							'Position' => $val['position'],
							'Renown' => $val['renown'],
							'MilMedal' => $val['mil_medal'],
							'Point' => $val['point'],
							'CityId' => $val['city_id'],
							'CreateAt' => $val['create_at'],
							'IsRescue' => $IsRescue,
							'PosNo' => array($zone, $posx, $poxy),
							'MilRank' => $cityInfo['mil_rank'],
							'Online' => M_Client::isOnline((int)$cityInfo['id']) ? 1 : 0,
						);
						if ($val['position'] == 2) {
							$bossArr = $tmp;
						} elseif ($val['position'] == 1) {
							$boss2Arr[] = $tmp;
						} else {
							$tmp3Arr[] = $tmp;
						}
					}
					$resArr[] = $bossArr;
					if (count($boss2Arr) > 0) {
						foreach ($boss2Arr as $key => $val) {
							$boss2Renown[$key] = $val['Renown'];
							$boss2MilMedal[$key] = $val['MilMedal'];
							$boss2Point[$key] = $val['Point'];
						}
						array_multisort($boss2Renown, SORT_DESC, $boss2MilMedal, SORT_DESC, $boss2Arr); //按威望排序 @todo速度若慢则加缓存
						foreach ($boss2Arr as $key => $val) {
							$resArr[] = $val;
						}
					}
					if (count($tmp3Arr) > 0) {
						foreach ($tmp3Arr as $key => $val) {
							$tmp3Renown[$key] = $val['Renown'];
							$tmp3MilMedal[$key] = $val['MilMedal'];
							$tmp3Point[$key] = $val['Point'];
						}
						array_multisort($tmp3Renown, SORT_DESC, $tmp3MilMedal, SORT_DESC, $tmp3Arr); //按威望排序
						foreach ($tmp3Arr as $key => $val) {
							$resArr[] = $val;
						}
					}
					$start = ($page - 1) * $pageSize;
					$total = count($resArr);
					$data['list'] = array_slice($resArr, $start, $pageSize, false);
					$data['sumPage'] = $total % $pageSize == 0 ? $total / $pageSize : intval($total / $pageSize) + 1;
					$data['page'] = $page;
				}
			} else {
				$errNo = T_ErrNo::NOT_IN_UNION;
			}
		}

		if (!empty($data)) {

			$errNo = '';
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 待审核成员
	 * @author Hejunyun
	 */
	public function ANotAuditMember($page = 1) {

		$errNo = T_ErrNo::ERR_ACTION;
		$page = intval($page);
		$page = max($page, 1);
		$pageSize = M_Union::$unionConf['page_offset'];
		$data = $tmp = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (isset($cityInfo['id']) && $cityInfo['union_id'] > 0) {
			$ownMemberInfo = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);
			//获取当前用户职位
			if ($ownMemberInfo && $ownMemberInfo['position'] > 0) {
				//$list = M_Union::getAuditMemberList($ownMemberInfo['union_id']);
				$list = M_Union::getUnionAppList($ownMemberInfo['union_id']);
				if (is_array($list)) {
					$start = ($page - 1) * $pageSize;
					$total = count($list);
					if ($list) {
						$list = array_slice($list, $start, $pageSize, false);
						foreach ($list as $id) {
							$tmpCityInfo = M_City::getInfo($id);
							if (isset($tmpCityInfo['id'])) {
								list($zone, $posX, $posY) = M_MapWild::calcWildMapPosXYByNo($tmpCityInfo['pos_no']);
								$tmp[] = array(
									'CityId' => $tmpCityInfo['id'],
									'NickName' => $tmpCityInfo['nickname'],
									'PosArea' => $zone,
									'PosXy' => $posX . '_' . $posY,
									'Renown' => $tmpCityInfo['renown'],
									'MilMedal' => $tmpCityInfo['mil_medal'],
									'MilRank' => $tmpCityInfo['mil_rank']
								);
							}
						}
					}
					$data['list'] = $tmp;
					$data['page'] = $page;
					$data['sumPage'] = $total % $pageSize == 0 ? $total / $pageSize : intval($total / $pageSize) + 1;

					$errNo = '';
				}
			} else {
				$errNo = T_ErrNo::UNION_NO_POWER; //职位不够，无法查看
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 审核成员通过
	 * @author Hejunyun
	 * @param int $applyCityId 申请入盟的玩家城市ID
	 */
	public function ADoAuditMember($applyCityId) {

		$errNo = T_ErrNo::ERR_ACTION;
		$applyCityId = intval($applyCityId);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$now = time();
		if (isset($cityInfo['id']) && $applyCityId && $cityInfo['union_id'] > 0) {
			$ownUnionId = $cityInfo['union_id'];
			$ownMemberInfo = M_Union::getMemberInfo($ownUnionId, $cityInfo['id']);
			//$othMemberInfo = M_Union::getMemberInfo($ownUnionId, $applyCityId);

			if (!empty($ownMemberInfo)) {
				//操作人职位判断
				if ($ownMemberInfo['position'] > M_Union::UNION_MEMBER_ORDINARY) {
					$applyCityInfo = M_City::getInfo($applyCityId);
					if (!$applyCityInfo['union_id']) {
						$ownUnionInfo = M_Union::getInfo($ownUnionId); //联盟信息
						if (isset($ownUnionInfo['id'])) {
							$sum = $ownUnionInfo['total_person']; //联盟总人数
							$baseUnionUp = M_Config::getVal('union_up');
							list($costGold, $costMilpay, $maxPerson) = $baseUnionUp[$ownUnionInfo['level']];

							$accommodSum = $maxPerson; //联盟能容纳的人数
							if ($sum < $accommodSum) {

								$res = M_Union::joinUnion($applyCityId, $ownUnionId, $ownUnionInfo['name']);
								if ($res) {
									$ApplyUnionCd = M_Config::getVal('cd_apply_union');
									$unionCd = ($now + $ApplyUnionCd * T_App::ONE_HOUR) . '_' . T_App::ADDUP_CAN;
									M_Union::setUnionCd($applyCityId, $unionCd);
									$setArr['total_person'] = $sum + 1;
									if ($applyCityInfo['renown']) {
										$setArr['total_renown'] = $ownUnionInfo['total_renown'] + $applyCityInfo['renown'];
									}
									M_Union::setInfo($ownUnionId, $setArr);

									//邮件提示
									$content = array(T_Lang::C_JOIN_UNION_SUCC, $ownUnionInfo['name']);
									M_Message::sendSysMessage($applyCityInfo['id'], json_encode(array(T_Lang::T_SYS_TIP)), json_encode($content), false);


									$errNo = '';
									M_MapWild::syncWildMapBlockCache($applyCityInfo['pos_no']);
								} else {
									$errNo = T_ErrNo::ERR_DB_EXECUTE;
								}
							} else {
								$errNo = T_ErrNo::FULL_OF_UNION; //联盟人数已满
							}
						} else {
							$errNo = T_ErrNo::UNION_NOT_EXIST; //该联盟不存在
						}
					} else {
						$errNo = T_ErrNo::HAS_JOINED_UNION; //已加入联盟
					}
				} else {
					$errNo = T_ErrNo::UNION_NO_POWER; //职位不够，无法查看
				}
			} else {
				$errNo = T_ErrNo::NOT_IN_UNION; //未加入联盟
			}
		}

		$data = array();
		return B_Common::result($errNo, $data);
	}

	/**
	 * 审核拒绝玩家通过
	 * @author Hejunyun
	 * @param int $applyCityId 申请入盟的玩家城市ID
	 */
	public function ABanAuditMember($applyCityId) {

		$errNo = T_ErrNo::ERR_ACTION;
		$applyCityId = intval($applyCityId);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (isset($cityInfo['id']) && $applyCityId && $cityInfo['union_id'] > 0) {
			//获得联盟成员表内信息
			$ownUnionId = $cityInfo['union_id'];
			$ownMemberInfo = M_Union::getMemberInfo($ownUnionId, $cityInfo['id']);

			//操作人职位判断
			if (!empty($ownMemberInfo) && $ownMemberInfo['position'] > 0) {
				$res = M_Union::userUnAppUnion($applyCityId, $ownUnionId);
				if ($res) {
					//邮件提示
					$unionInfo = M_Union::getInfo($ownUnionId); //联盟信息
					$content = array(T_Lang::C_JOIN_UNION_FALL, $unionInfo['name']);
					M_Message::sendSysMessage($applyCityId, json_encode(array(T_Lang::T_SYS_TIP)), json_encode($content), false);


					$errNo = '';
				}
			} else {
				$errNo = T_ErrNo::UNION_NO_POWER; //职位不够，无法查看
			}

		}

		$data = array();
		return B_Common::result($errNo, $data);
	}

	/**
	 * 辞去职位
	 * @author Hejunyun
	 */
	public function AUnionResign() {

		$errNo = T_ErrNo::ERR_ACTION;

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (isset($cityInfo['id']) && $cityInfo['union_id'] > 0) {
			$memberRow = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);
			if ($memberRow) {
				if ($memberRow['position'] == M_Union::UNION_MEMBER_SECOND) {
					//职位设置为普通成员
					$setArr = array(
						'position' => M_Union::UNION_MEMBER_ORDINARY
					);
					$res = M_Union::setMemberInfo($cityInfo['id'], $cityInfo['union_id'], $setArr);
					if ($res) {

						$errNo = '';
					} else {
						$errNo = T_ErrNo::ERR_DB_EXECUTE;
					}
				} else {
					$errNo = T_ErrNo::UNION_NO_POWER; //无职位或是军团长
				}
			}
		}

		$data = array();
		return B_Common::result($errNo, $data);
	}

	/**
	 * 职位操作
	 * @author Hejunyun
	 * @param int $cityId 城市ID
	 * @param int $pos 职位
	 */
	public function ASetPos($cityId, $pos) {

		$errNo = T_ErrNo::ERR_ACTION;
		$cityId = intval($cityId);
		$pos = intval($pos);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if ($cityInfo['union_id'] > 0 && $cityId && in_array($pos, array(0, 1, 2))) {
			//获取自己的职位信息
			$ownMemberInfo = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);
			//获取被操作人的职位信息
			$othMemberInfo = M_Union::getMemberInfo($cityInfo['union_id'], $cityId);
			if ($ownMemberInfo && $othMemberInfo) {
				if ($ownMemberInfo['position'] > $othMemberInfo['position'] &&
					$ownMemberInfo['position'] > $pos
				) {
					$res = M_Union::setMemberInfo($cityId, $cityInfo['union_id'], array('position' => $pos));
					if ($res) {

						$errNo = '';
					} else {
						$errNo = T_ErrNo::ERR_DB_EXECUTE;
					}
				} else {
					$errNo = T_ErrNo::UNION_NO_POWER; //权限不足，只能操作职位比自己低的
				}
			} else {
				$errNo = T_ErrNo::ERR_ACTION; //不在同一个联盟
			}
		}

		$data = array();
		return B_Common::result($errNo, $data);
	}

	/**
	 * 联盟转让
	 * @author Hejunyun
	 * @param int $cityId 接受联盟玩家城市ID
	 */
	public function ATransferUnion($cityId = 0) {

		$errNo = T_ErrNo::ERR_ACTION;
		$cityId = intval($cityId);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$otherCityInfo = M_City::getInfo($cityId);
		if (isset($cityInfo['id']) && $cityInfo['union_id'] > 0 && isset($otherCityInfo['id'])) {
			//获取自己的职位信息
			$ownMemberInfo = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);
			//获取被操作人的职位信息
			$othMemberInfo = M_Union::getMemberInfo($cityInfo['union_id'], $cityId);
			if ($ownMemberInfo && $othMemberInfo) {
				if ($ownMemberInfo['position'] == M_Union::UNION_MEMBER_TOP) {

					$res = M_Union::setMemberInfo($cityInfo['id'], $cityInfo['union_id'], array('position' => 0));
					if ($res) {
						$res1 = M_Union::setMemberInfo($cityId, $cityInfo['union_id'], array('position' => 2));
						if ($res1) {
							$res2 = M_Union::setInfo($ownMemberInfo['union_id'], array('boss' => $otherCityInfo['nickname']));
							if ($res2) {

								$errNo = '';
							}
						}
					}
				} else {
					$errNo = T_ErrNo::UNION_NO_POWER; //权限不足
				}
			} else {
				$errNo = T_ErrNo::ERR_ACTION; //不在同一个联盟
			}
		}

		$data = array();
		return B_Common::result($errNo, $data);
	}

	/**
	 * 联盟贡献
	 * @author Hejunyun
	 * @param int $addGold 贡献数量（金币）
	 */
	public function AAddUnionGold($addGold = 0) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$addGold = intval($addGold);

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if ($addGold) {
			$err = '';
			$myRes = $objPlayer->Res()->get();
			//获取自己的职位信息
			if ($myRes['gold'] < $addGold * M_Union::$unionConf['union_coin_pption']) {
				$err = T_ErrNo::NO_ENOUGH_GOLD; //资源不足
			} else if (empty($cityInfo['union_id']) ||
				!$myMember = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id'])
			) {
				$err = T_ErrNo::NOT_IN_UNION; //未加入联盟
			} else if ($cityInfo['mil_medal'] < M_Config::getVal('union_donation_need_medal')) {
				$err = T_ErrNo::NO_ENOUGH_MILMEDAL; //军功不足，不能贡献
			}


			if (empty($err)) {
				$unionInfo = M_Union::getInfo($myMember['union_id']);
				$goldNum = $addGold * M_Union::$unionConf['union_coin_pption'];

				$objPlayer->Res()->incr('gold', -$goldNum);
				$bUp = true;

				$num = $unionInfo['coin'] + $addGold;
				if ($bUp) {
					$bUp1 = M_Union::setInfo($myMember['union_id'], array('coin' => $num));
					$bUp2 = $bUp1 && M_Union::setMemberInfo($cityInfo['id'], $myMember['union_id'], array('point' => $myMember['point'] + $addGold));
					if ($bUp2) {

						$errNo = '';
						M_QqShare::check($objPlayer, 'union_contribution', array());
						//$data = array('coin' => $num);//$myMember['point'] + $addGold
						$data = array(
							'coin' => $num,
							'point' => $myMember['point'] + $addGold
						);
					}
				}
			} else {
				$errNo = $err;
			}

		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 踢出成员
	 * @author Hejunyun
	 * @param int $cityId 城市ID
	 */
	public function ADelMember($cityId) {

		$errNo = T_ErrNo::ERR_ACTION;
		$cityId = intval($cityId);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$now = time();
		$data = array();
		if ($cityId) {
			$ownUnionId = $cityInfo['union_id'];
			//获取自己的职位信息
			$ownMemberInfo = M_Union::getMemberInfo($ownUnionId, $cityInfo['id']);
			//获取被操作人的职位信息
			$othMemberInfo = M_Union::getMemberInfo($ownUnionId, $cityId);
			$UnionCd = M_Union::getUnionCd($cityId);
			$arrUnionT = explode('_', $UnionCd);
			$data['cd_union'] = !empty($arrUnionT[0]) ? $arrUnionT[0] : 0;
			if (empty($arrUnionT[0]) || $arrUnionT[0] < $now) {
				if ($ownMemberInfo && $othMemberInfo) {
					if ($ownMemberInfo['position'] > $othMemberInfo['position']) {
						$res = M_Union::delUnionMember($othMemberInfo);
						if ($res) {
							//发送提醒邮件
							$ApplyUnionCd = M_Config::getVal('cd_apply_union');
							$unionCd = ($now + $ApplyUnionCd * T_App::ONE_HOUR) . '_' . T_App::ADDUP_CAN;
							M_Union::setUnionCd($cityId, $unionCd);
							$content = array(T_Lang::C_UNION_DEL_MEMBER);
							M_Message::sendSysMessage($othMemberInfo['city_id'], json_encode(array(T_Lang::T_SYS_TIP)), json_encode($content), false);
							$othCityInfo = M_City::getInfo($othMemberInfo['city_id']);
							M_MapWild::syncWildMapBlockCache($othCityInfo['pos_no']);

							$errNo = '';
						} else {
							$errNo = T_ErrNo::ERR_DB_EXECUTE;
						}
					} else {
						$errNo = T_ErrNo::UNION_NO_POWER; //权限不足
					}
				} else {
					$errNo = T_ErrNo::ERR_ACTION; //不在同一个联盟
				}
			} else {
				$errNo = T_ErrNo::UNION_NOT_CD; //很遗憾！军团冷却中。”
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 根据联盟名称搜索联盟
	 * @author Hejunyun
	 * @param string $name
	 */
	public function AGetUnionByName($name) {

		$errNo = T_ErrNo::ERR_ACTION;
		$row = array();
		$data = array();
		$name = trim($name);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if ($name) {
			$row = M_Union::getUnionByName($name);
			if ($row == false) {
				$errNo = T_ErrNo::UNION_NOT_EXIST;
			} else {
				$data = $row['rank'];

				$errNo = '';
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 外交联盟列表
	 * @author Hejunyun
	 * @param int $type 关系 1友好  2敌对
	 */
	public function AGetRelationsList($type) {

		$errNo = T_ErrNo::ERR_ACTION;
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$data = array();
		if (isset($cityInfo['union_id']) && $cityInfo['union_id'] > 0) {
			$info = M_Union::getInfo($cityInfo['union_id']);

			//type:1友好联盟列表  否则 敌对联盟列表
			$list = $type == 1 ? json_decode($info['rel_friend'], true) : $list = json_decode($info['rel_enemy'], true);

			if (!empty($list)) {
				foreach ($list as $uid) {
					$tmpInfo = M_Union::getInfo($uid);
					if (!empty($tmpInfo)) {
						$data[] = array(
							'ID' => $tmpInfo['id'],
							'Name' => $tmpInfo['name'],
							'Boss' => $tmpInfo['boss'],
							'Total' => $tmpInfo['total_person']
						);
					}
				}
			}

			$errNo = '';

		} else {
			$errNo = T_ErrNo::NOT_IN_UNION; //未加入联盟
		}

		return B_Common::result($errNo, $data);
	}


	/**
	 * 添加外交联盟
	 * @author Hejunyun
	 * @param int $type 关系 1友好  2敌对
	 * @param string $name 联盟名称
	 */
	public function ASetRelation($type, $name) {

		$errNo = T_ErrNo::ERR_ACTION;
		$ret = false;
		$name = trim($name);
		$type = intval($type);

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (!empty($name) && $cityInfo['union_id'] > 0) {
			$myMember = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);
			if ($myMember) {
				if ($myMember['position'] > M_Union::UNION_MEMBER_ORDINARY) {
					$union = M_Union::getUnionByName($name);
					if ($union && isset($union['id'])) {
						$myUnion = M_Union::getInfo($myMember['union_id']);
						$friendUnionList = $myUnion['rel_friend'] ? json_decode($myUnion['rel_friend'], true) : array();
						$enemyUnionList = $myUnion['rel_enemy'] ? json_decode($myUnion['rel_enemy'], true) : array();
						if (in_array($union['id'], $friendUnionList) || in_array($union['id'], $enemyUnionList)) {
							$errNo = T_ErrNo::UNION_EXIST_REL; //该盟已经在关系表中
						} else {
							if ($type == 1) //友好
							{
								//$ret = M_Union::addRelFriend($myMember['union_id'], $union['id']);
								$friendUnionList[] = $union['id'];
								$tmp = array(
									'rel_friend' => json_encode($friendUnionList)
								);
							} else //敌对
							{
								//$ret = M_Union::addRelEnemy($myMember['union_id'], $union['id']);
								$enemyUnionList[] = $union['id'];
								$tmp = array(
									'rel_enemy' => json_encode($enemyUnionList)
								);
							}
							$ret = M_Union::setInfo($myUnion['id'], $tmp);
							if ($ret) {

								$errNo = '';
							} else {
								$errNo = T_ErrNo::ERR_DB_EXECUTE; //执行出错
							}
						}
					} else {
						$errNo = T_ErrNo::UNION_NOT_EXIST; //该联盟不存在
					}

				} else {
					$errNo = T_ErrNo::UNION_NO_POWER; //权限不足
				}
			}
		}


		$data = array();
		return B_Common::result($errNo, $data);
	}

	/**
	 * 解除关系
	 * @author Hejunyun
	 * @param int $type 关系类型 1友好 2敌对
	 * @param int $unionId 联盟ID
	 */
	public function ARelieveRelation($type, $unionId) {

		$errNo = T_ErrNo::ERR_ACTION;
		$ret = false;
		$type = intval($type);
		$unionId = intval($unionId);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if ($unionId > 0 && $cityInfo['union_id'] > 0) {
			$ownMemberInfo = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);

			if ($ownMemberInfo && $ownMemberInfo['position'] > M_Union::UNION_MEMBER_ORDINARY) {
				$union = M_Union::getInfo($unionId);
				if ($union && isset($union['id'])) {
					$ownUnionInfo = M_Union::getInfo($cityInfo['union_id']);
					$friendUnionList = $ownUnionInfo['rel_friend'] ? json_decode($ownUnionInfo['rel_friend'], true) : array();
					$enemyUnionList = $ownUnionInfo['rel_enemy'] ? json_decode($ownUnionInfo['rel_enemy'], true) : array();

					if ($type == 1) //解除友好
					{
						if (in_array($union['id'], $friendUnionList)) {
							foreach ($friendUnionList as $key => $uid) {
								if ($union['id'] == $uid) {
									unset($friendUnionList[$key]);
								}
							}
							$tmp = array(
								'rel_friend' => json_encode($friendUnionList)
							);
						} else {
							$errNo = T_ErrNo::UNION_NOT_EXIST_REL; //该盟不在关系表中
						}
					} else //解除敌对
					{
						if (in_array($union['id'], $enemyUnionList)) {
							foreach ($enemyUnionList as $key => $uid) {
								if ($union['id'] == $uid) {
									unset($enemyUnionList[$key]);
								}
							}
							$tmp = array(
								'rel_enemy' => json_encode($enemyUnionList)
							);
						} else {
							$errNo = T_ErrNo::UNION_NOT_EXIST_REL; //该盟不在关系表中
						}
					}
					$ret = M_Union::setInfo($cityInfo['union_id'], $tmp);
					if ($ret) {

						$errNo = '';
					}
				} else {
					$errNo = T_ErrNo::UNION_NOT_EXIST; //该联盟不存在
				}
			} else {
				$errNo = T_ErrNo::UNION_NO_POWER; //权限不足
			}
		}

		$data = array();
		return B_Common::result($errNo, $data);
	}


	/**
	 * 提升联盟等级
	 * @author Hejunyun
	 */
	public function AUpgradeUnion() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if ($cityInfo['union_id'] > 0) {
			$myMember = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);
			if ($myMember && $myMember['position'] > M_Union::UNION_MEMBER_ORDINARY) {
				$myUnion = M_Union::getInfo($myMember['union_id']);
				$nowLevel = $myUnion['level'];
				$baseUnionUp = M_Config::getVal('union_up');
				if ($nowLevel < count($baseUnionUp)) {
					$level = $nowLevel + 1;
					list($costGold, $costMilpay, $maxPerson) = $baseUnionUp[$level];
					if ($myUnion['coin'] < $costGold ||
						$cityInfo['mil_pay'] < $costMilpay
					) {
						$errNo = T_ErrNo::UNION_GOLD_NO_ENOUGH; //联盟资金不够
					} else {
						if ($costMilpay > 0) {
							$bCost = $objPlayer->City()->decrCurrency(T_App::MILPAY, $costMilpay, B_Log_Trade::E_UpUnionLevel, $level);
						} else {
							$bCost = true;
						}

						$res = $bCost && M_Union::unionUpgrade($myMember['union_id'], $costGold);
						if ($res) {

							$errNo = '';
							$data = array(
								'coin' => $myUnion['coin'] - $costGold,
								'maxPerson' => $maxPerson,
							);
						} else {
							$errNo = T_ErrNo::ERR_DB_EXECUTE; //执行出错
						}
					}
				} else {
					$errNo = T_ErrNo::UNION_IS_TOP_LEVEL;
				}
			} else {
				$errNo = T_ErrNo::UNION_NO_POWER; //非军团长，权限不够
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 修改联盟公告
	 * @param string $notice
	 */
	public function ASetNotice($notice) {

		$errNo = T_ErrNo::ERR_ACTION;
		$notice = B_Utils::isBlockName($notice, true);
		$length = B_Utils::len($notice);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if ($cityInfo['union_id'] > 0) {
			if ($length <= 200) {
				$myMember = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);
				if ($myMember['position'] > M_Union::UNION_MEMBER_ORDINARY) {
					$ret = M_Union::setInfo($cityInfo['union_id'], array('notice' => $notice));
					if ($ret) {

						$errNo = '';
					}
				} else {
					$errNo = T_ErrNo::UNION_NO_POWER; //非军团长，权限不够
				}

			} else {
				$errNo = T_ErrNo::ERR_LONG_NOTICE; //公告长度不合法
			}
		}

		$data = array();
		return B_Common::result($errNo, $data);
	}

	/**
	 * 修改联盟旗帜
	 * @author Hejunyun
	 * @param int $faceId 旗帜ID
	 */
	public function ASetFace($faceId) {

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$errNo = T_ErrNo::ERR_ACTION;
		$faceId = intval($faceId);
		if (!in_array($faceId, M_Union::$faceArr)) {
			$errNo = T_ErrNo::ERR_PARAM; //非法旗帜
		} else {
			if ($cityInfo['union_id'] > 0) {
				$resInfo = $objPlayer->Res()->get(); //获取资源信息
				if ($resInfo['gold'] < M_Union::$unionConf['up_face_cost']) {
					$errNo = T_ErrNo::NO_ENOUGH_GOLD; //资源不够
				} else {
					$myMember = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);
					if ($myMember['position'] == M_Union::UNION_MEMBER_TOP) {
						$ret = M_Union::setInfo($cityInfo['union_id'], array('face_id' => $faceId));
						if ($ret) {
							$goldNum = M_Config::getVal('union_up_face_cost');

							$objPlayer->Res()->incr('gold', -$goldNum, true);

							$errNo = '';
						}
					} else {
						$errNo = T_ErrNo::UNION_NO_POWER; //非军团长，权限不够
					}
				}
			}
		}

		$data = array();
		return B_Common::result($errNo, $data);
	}

	/**
	 * 领取联盟每日奖励
	 * @author Hejunyun
	 */
	public function AReceiveAward() {

		$errNo = T_ErrNo::ERR_ACTION;
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if ($cityInfo['union_id'] > 0) {
			$unionInfo = M_Union::getInfo($cityInfo['union_id']);
			$memberInfo = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);
			if (!empty($unionInfo) && !empty($memberInfo)) {
				$unionLevel = $unionInfo['level'];
				$point = $memberInfo['point'];
				if ($point > 0) {
					$isBool = M_Union::isAward($cityInfo['id']);
					if ($isBool) {
						$award = M_Union::getUnionAward($unionLevel, $point);
						if ($award && M_Union::setAwardDate($cityInfo['id'])) {
							if ($award['gold'] > 0) {
								$objPlayer->Res()->incr('gold', $award['gold'], true);

								$ret[] = true;
							}
							if ($award['coupon'] > 0) {
								$objPlayer->City()->coupon += $award['coupon'];
								$objPlayer->Log()->income(T_App::COUPON, $award['coupon'], B_Log_Trade::I_Task);

								$ret[] = true;
							}
						}

						if (!in_array(false, $ret)) {
							M_QqShare::check($objPlayer, 'union_getaward', array());
							$objPlayer->save();
							$errNo = '';
						}
					} else {
						$errNo = T_ErrNo::UNION_AWARD_REPEAT; //今日已领过
					}
				}
			}
		}
		$data = array();
		return B_Common::result($errNo, $data);
	}

	/**
	 * 联盟每日奖励内容
	 */
	public function AGetUnionAward() {

		$errNo = T_ErrNo::ERR_ACTION;
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$data = array();
		if ($cityInfo['union_id'] > 0) {
			$unionInfo = M_Union::getInfo($cityInfo['union_id']);
			$memberInfo = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);
			if (!empty($unionInfo) && !empty($memberInfo)) {
				$unionLevel = $unionInfo['level'];
				$point = $memberInfo['point'];
				$data = M_Union::getUnionAward($unionLevel, $point);
				$errNo = '';

			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 科技升级
	 * @author Hejunyun
	 * @param int $techId 科技ID
	 */
	public function AUpTech($techId) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$techId = intval($techId);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if ($techId > 0 && $cityInfo['union_id'] > 0) {
			$baseUnionTech = M_Config::getVal('union_tech');
			$unionInfo = M_Union::getInfo($cityInfo['union_id']);
			$memberInfo = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);
			if (!empty($unionInfo['id']) &&
				!empty($memberInfo['id']) &&
				isset($baseUnionTech[$techId])
			) {
				if ($memberInfo['position'] > M_Union::UNION_MEMBER_ORDINARY) {
					$techData = json_decode($unionInfo['tech_data'], true);
					$techLevel = isset($techData[$techId]) ? $techData[$techId] : 0;
					$nextLevel = $techLevel + 1;
					list($needUnionLevel, $add, $coin) = $baseUnionTech[$techId][$nextLevel];

					if ($nextLevel <= count($baseUnionTech[$techId])) {
						if ($unionInfo['level'] >= $needUnionLevel) //需要军团等级
						{
							//需要消耗资金
							$cost = M_Formula::upgradeUnionTechNeedCoin($techId, $nextLevel);
							if ($unionInfo['coin'] >= $cost) {
								$techData[$techId] = $nextLevel;
								$setArr = array(
									'coin' => $unionInfo['coin'] - $cost,
									'tech_data' => json_encode($techData)
								);
								$ret = M_Union::setInfo($cityInfo['union_id'], $setArr);
								if ($ret) {
									$nextNextLevel = $nextLevel + 1;
									$needUnionLevel = $add = $coin = 0;
									if (isset($baseUnionTech[$techId][$nextNextLevel])) {
										list($needUnionLevel, $add, $coin) = $baseUnionTech[$techId][$nextNextLevel];
									}


									$data = array(
										'CurLevel' => $nextLevel,
										'NextNeedCoin' => M_Formula::upgradeUnionTechNeedCoin($techId, $nextNextLevel),
										'Coin' => $setArr['coin'],
										'NextNeedLevel' => $needUnionLevel,
									);
									$errNo = '';
								}
							} else {
								$errNo = T_ErrNo::UNION_GOLD_NO_ENOUGH; //资金不足
							}

						} else {
							$errNo = T_ErrNo::UNION_LEVEL_NO_ENOUGH; //军团等级不够
						}
					} else {
						$errNo = T_ErrNo::UNION_TECH_ISTOP; //科技等级已到上限

					}
				} else {
					$errNo = T_ErrNo::UNION_NO_POWER; //职位不够
				}
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 校正联盟总威望
	 */
	public function AUpTotalRenown() {

		$errNo = '';
		$unionList = M_Union::getList(1, 9999);
		foreach ($unionList['list'] as $k => $unionId) {
			echo $unionId;
			$total = 0;
			$memberList = M_Union::getUnionMemberList($unionId);
			foreach ($memberList as $val) {
				$total = $total + $val['renown'];
			}
			M_Union::setInfo($unionId, array('total_renown' => $total));
			$data[$unionId] = $total;
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 军团招募的列表
	 */
	public function AHireList() {

		$errNo = T_ErrNo::ERR_ACTION;
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$data = array();
		$idList = array();
		$idList = M_Union::getUserInviteList($cityInfo['id']);
		if (!empty($idList)) {
			$hireUnionId = $idList;
			foreach ($hireUnionId as $unionId) {
				$unionInfo = M_Union::getInfo($unionId);
				if (!empty($unionInfo)) {
					$data[] = array(
						'ID' => $unionId,
						'Name' => $unionInfo['name'],
						'FaceId' => $unionInfo['face_id'],
						'Level' => $unionInfo['level'],
						'Boss' => $unionInfo['boss'],
						'Rank' => $unionInfo['rank'],
						'TotalPerson' => $unionInfo['total_person'],
						'TotalRenown' => $unionInfo['total_renown'],
					);
				}
			}
		}

		$errNo = '';

		return B_Common::result($errNo, $data);
	}

	/**
	 * 玩家同意军团的招募
	 * @param int $memberCity
	 */
	public function AAgreeHire($unionId) {
		$ret = false;
		$data = array();

		$errNo = T_ErrNo::ERR_ACTION;
		$now = time();
		$unionId = intval($unionId);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if ($unionId > 0) {
			$unionInfo = M_Union::getInfo($unionId); //联盟ID
			$ownCityId = $unionInfo['create_city_id'];
			$ownUnionInfo = M_Union::getInfo($unionId);
			if (!empty($ownUnionInfo)) {
				//操作人职位判断
				$inviteCityInfo = M_City::getInfo($cityInfo['id']); //玩家信息

				if (!$inviteCityInfo['union_id']) {
					//联盟信息
					if (isset($ownUnionInfo['id'])) {
						$sum = $ownUnionInfo['total_person']; //联盟总人数
						$baseUnionUp = M_Config::getVal('union_up');
						list($costGold, $costMilpay, $maxPerson) = $baseUnionUp[$ownUnionInfo['level']];

						$accommodSum = $maxPerson; //联盟能容纳的人数
						if ($sum < $accommodSum) {

							$res = M_Union::joinUnion($cityInfo['id'], $unionId, $ownUnionInfo['name']);
							if ($res) {
								$ApplyUnionCd = M_Config::getVal('cd_apply_union');
								$unionCd = ($now + $ApplyUnionCd * T_App::ONE_HOUR) . '_' . T_App::ADDUP_CAN;
								M_Union::setUnionCd($cityInfo['id'], $unionCd);
								$setArr['total_person'] = $sum + 1;
								if ($inviteCityInfo['renown']) {
									$setArr['total_renown'] = $ownUnionInfo['total_renown'] + $inviteCityInfo['renown'];
								}
								M_Union::setInfo($unionId, $setArr);

								//邮件提示
								$content = array(T_Lang::C_APPLY_JOIN_UNION_SUCC, $cityInfo['nickname']);
								M_Message::sendSysMessage($ownCityId, json_encode(array(T_Lang::T_SYS_TIP)), json_encode($content), false);


								$errNo = '';
								$rc = new B_Cache_RC(T_Key::UNION_HIRE, 'c' . $cityInfo['id']);
								$rc->delete();
								M_MapWild::syncWildMapBlockCache($inviteCityInfo['pos_no']);
							} else {
								$errNo = T_ErrNo::ERR_DB_EXECUTE;
							}
						} else {
							$errNo = T_ErrNo::FULL_OF_UNION; //联盟人数已满
						}
					} else {
						$errNo = T_ErrNo::UNION_NOT_EXIST; //该联盟不存在
					}
				} else {
					$errNo = T_ErrNo::HAS_JOINED_UNION; //已加入联盟
				}
			} else {
				$errNo = T_ErrNo::NOT_IN_UNION; //未加入联盟
			}
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 玩家拒绝军团的招募
	 * @param int $memberCity
	 */
	public function ARefuseHire($unionId) {
		$ret = false;
		$data = array();

		$errNo = T_ErrNo::ERR_ACTION;
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$rc = new B_Cache_RC(T_Key::UNION_HIRE, 'c' . $cityInfo['id']);
		if ($rc->sismember($unionId)) {
			$rc->srem($unionId);

			$errNo = '';
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 团长或副团长发送招募消息给玩家
	 * @param string $ownname
	 * @param string $content
	 */
	public function AHireMessage($inviteNickname, $content) {
		$ret = false;
		$data = array();

		$errNo = T_ErrNo::ERR_ACTION;
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$now = time();
		if (!empty($inviteNickname)) {
			$content = !empty($content) ? $content : '';
			$hireCityInfo = M_City::getInfo($cityInfo['id']);
			$ownUnionId = $hireCityInfo['union_id']; //联盟ID

			$ownMemberInfo = M_Union::getInfo($ownUnionId, $cityInfo['id']); //联盟信息
			$title = json_encode(array(T_Lang::TMP_UNION_HIRE, $ownMemberInfo['name']));
			$content = B_Utils::isBlockName($content, true);
			$inviteCityId = M_City::getCityIdByNickName($inviteNickname);

			$ownUnionMember = M_Union::getMemberInfo($ownUnionId, $cityInfo['id']);

			$inviteCityInfo = M_City::getInfo($inviteCityId);
			$times = array();
			$times = M_Union::getInviteTimesList($ownUnionId);
			$arrLimit = M_Message::getMailNumLimit(); //限制
			$err = '';
			$UnionCd = M_Union::getUnionCd($inviteCityId);
			$arrUnionT = explode('_', $UnionCd);
			$data['cd_union'] = !empty($arrUnionT[0]) ? $arrUnionT[0] : 0;
			if (empty($inviteCityId)) //接收玩家城市ID
			{
				$err = T_ErrNo::USER_NO_EXIST;
			} else if ($inviteCityId == $cityInfo['id']) {
				$err = T_ErrNo::USER_MSG_SELF;
			} else if (mb_strlen($content) > $arrLimit[1]) {
				$err = T_ErrNo::MSG_OVER_LIMIT;
			} else if ($ownUnionMember['position'] < M_Union::UNION_MEMBER_SECOND) {
				$err = T_ErrNo::ERR_HIRE_USER;
			} else if (!empty($inviteCityInfo['union_id'])) {
				$err = T_ErrNo::ERR_UNION_HAD;
			} else if (isset($times[$ownUnionId]) && $times[$ownUnionId] > M_Union::UNION_HIRE_TIMES) {
				$err = T_ErrNo::ERR_UNION_TIMES;
			} else if (!empty($arrUnionT[0]) && $arrUnionT[0] > $now) {
				$err = T_ErrNo::UNION_NOT_CD;
			}
			if (empty($err)) {
				$id = M_Message::sendSysMessage($inviteCityId, $title, json_encode($content), false);
				$data['MsgId'] = array('MsgId' => $id);
				$ret = $id;
				if ($ret) {

					$errNo = '';
					$rc = new B_Cache_RC(T_Key::UNION_HIRE, 'c' . $inviteCityId);
					if (!($rc->sismember($ownUnionId))) {
						$rc->sadd($ownUnionId);
					}
					$rc = new B_Cache_RC(T_Key::UNION_INVITE_TIMES, date('Ymd') . $ownUnionId);
					$rc->hincrby($ownUnionId, 1);
					$hireTimes = array();
					$hireTimesNum = 0;
					$SurplusTimes = 0;
					$hireTimes = M_Union::getInviteTimesList($ownUnionId);
					$hireTimesNum = isset($hireTimes[$cityInfo['union_id']]) ? $hireTimes[$cityInfo['union_id']] : 0;
					$SurplusTimes = M_Union::UNION_HIRE_TIMES - $hireTimesNum;
					if ($SurplusTimes < 0) {
						$SurplusTimes = 0;
					}
					$data['HireTimes'] = $SurplusTimes;
				} else {
					$errNo = T_ErrNo::ERR_DB_EXECUTE;
				}
			} else {
				$errNo = $err;
			}
		}
		return B_Common::result($errNo, $data); //发送成功
	}

	/** 军团动态 */
	public function AUnionDynamic() {
		$data = array();

		$errNo = T_ErrNo::ERR_ACTION;
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (!empty($cityInfo['union_id'])) {
			$unionId = $cityInfo['union_id'];
			$list = M_Union::getUnionMemberList($unionId);
			$rc = new B_Cache_RC(T_Key::CITY_ID_DEL_UNION, $unionId . $cityInfo['id'] . date('Ymd'));
			$delCityIds = $rc->smembers();
			$data = array();

			foreach ($list as $unionMember) {
				$memberCityInfo = M_City::getInfo($unionMember['city_id']); //军团的成员列表
				if (M_March_Hold::exist($memberCityInfo['pos_no'])) //判断是不是被占领
				{
					$defCityColonyInfo = M_ColonyCity::getInfo($unionMember['city_id']); //联盟被占领的信息
					if (!empty($defCityColonyInfo)) {
						$unionCityInfo = M_City::getInfo($unionMember['city_id']);
						$atkCityInfo = M_City::getInfo($defCityColonyInfo['atk_city_id']); //占领方的行军列表
						$mapInfo = M_MapWild::getWildMapInfo($unionCityInfo['pos_no']);

						if (!in_array($unionMember['city_id'], $delCityIds)) {
							$data[] = array(
								'CreateAt' => $mapInfo['hold_expire_time'],
								'cityId' => $unionMember['city_id'],
								'atkPosNo' => M_MapWild::calcWildMapPosXYByNo($atkCityInfo['pos_no']),
								'atkNickName' => $atkCityInfo['nickname'],
								'unionNickName' => $unionCityInfo['nickname'],
								'type' => 1,
							);
						}
					}
				}
			}
			$errNo = '';

		}
		return B_Common::result($errNo, $data); //发送成功
	}

	/**
	 * 删除军团公告
	 * @author duhuihui
	 * @param int $cityId 城市ID
	 * @param array $ids example:array(1,2,3,4,5...)
	 * @return array $errID 删除操作失败的战报ID
	 */
	public function AdelUnionReport($ids) {
		$ret = false;
		$data = array();

		$errNo = T_ErrNo::ERR_ACTION;
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (is_array($ids)) {
			foreach ($ids as $cityId) {
				$rc = new B_Cache_RC(T_Key::CITY_ID_DEL_UNION, $cityInfo['union_id'] . $cityInfo['id'] . date('Ymd'));
				$rc->sadd($cityId);
				$errNo = '';

			}
		}
		return B_Common::result($errNo, $data); //发送成功
	}

	/**
	 * 更改军团名字
	 * @author huwei
	 * @param string $newNickName 新名字
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AModifyNickName($newNickName) {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_PARAM; //失败原因默认为参数错误
		$data = array(); //返回数据默认为空数组

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$newNickName = trim($newNickName);
		if (!empty($cityInfo['union_id']) && !empty($newNickName)) {
			//$propsid 		= M_Props::MODIFY_UNION_NAME_PROPS_ID;
			$propsid = M_Props::getModifyUnionNameId();
			$cityId = $cityInfo['id'];
			$unionId = B_DB::instance('Alliance')->getIdByName($newNickName);
			//$unionInfo 		= M_Union::getInfo($cityInfo['union_id']);
			$unionMemberInfo = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);

			$nameLen = B_Utils::len($newNickName);

			if ($nameLen < 4 || $nameLen > 14) {
				$err = T_ErrNo::ERR_LONG_NAME; //名称长度不合法
			} elseif (B_Utils::isBlockName($newNickName)) {
				$err = T_ErrNo::ERR_NAME; //非法名称
			} else if (!M_Props::checkCityPropsNum($cityId, $propsid, -1, 1)) {
				$err = T_ErrNo::UNION_PROPS_ERR;
			} else if (!empty($unionId)) {
				$err = T_ErrNo::UNION_NAME_EXIST;
			} else if ($unionMemberInfo['position'] < M_Union::UNION_MEMBER_SECOND) {
				$err = T_ErrNo::UNION_POS_ERR;
			}

			if (empty($err)) {
				$bCost = $objPlayer->Pack()->decrNumByPropId($propsid, 1);

				$ret = $bCost && M_Union::setInfo($cityInfo['union_id'], array('name' => $newNickName));

				if ($ret) {
					B_DB::instance('Alliance')->update(array('name' => $newNickName), $cityInfo['union_id']); //直接更新数据库

					$list = M_Union::getUnionMemberList($cityInfo['union_id']);
					if (!empty($list) && is_array($list)) {
						foreach ($list as $val) {
							if (M_Client::isOnline($val['city_id'])) {
								M_Sync::addQueue($val['city_id'], M_Sync::KEY_CITY_INFO, array('UnionName' => $newNickName), true); //同步新名字
							}

							M_MapWild::syncWildMapBlockCache($val['pos_no']); //刷新此块地图数据
						}
					}


					$errNo = '';
				} else {
					$errNo = T_ErrNo::ERR_UPDATE;
				}
			} else {
				$errNo = $err;
			}
		}

		return B_Common::result($errNo, $data);
	}
}

?>
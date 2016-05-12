<?php

class M_Union {
	/** 联盟旗帜图片编号 */
	static $faceArr = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
	/** 军团公告限制数量 */
	static $UnionDynamicLimit = 30;
	/** 联盟配置 */
	static $unionConf = array(
		'init_acmd'         => 50, // 联盟初始容纳人数
		'incre_acmd'        => 5, //联盟每升一级新增容纳人数
		'union_award'       => 200000, //联盟奖励基数
		'union_coin_pption' => 10000, //联盟资金与金钱比例
		'page_offset'       => 7, //联盟成员前端每页人数
	);

	/** 每天发送邮件限制次数 */
	const UNION_HIRE_TIMES = 100;

	/** 军团职务 军团长编号 */
	const UNION_MEMBER_TOP = 2;
	/** 军团职务 副军团长编号 */
	const UNION_MEMBER_SECOND = 1;
	/** 军团职务 普通成员 */
	const UNION_MEMBER_ORDINARY = 0;
	/** 军团职务 */
	static $unionMemberPosition = array(
		self::UNION_MEMBER_TOP      => '军团长',
		self::UNION_MEMBER_SECOND   => '副军团长',
		self::UNION_MEMBER_ORDINARY => '普通成员'
	);
	static $memberFlag = array(
		'yes' => 1, //已通过
		'no'  => 0 //待审核
	);

	/** 联盟科技 建筑加成  */
	const TECH_CD_BUILD = 1;
	/** 联盟科技 科技加成 */
	const TECH_CD_TECH = 2;
	/** 联盟科技 带兵数加成 */
	const TECH_ARMY_NUM = 3;
	/** 联盟科技 暴击加成 */
	const TECH_CRIT = 4;
	/** 联盟科技 步兵加成 */
	const TECH_FOOT = 5;
	/** 联盟科技 装甲兵加成 */
	const TECH_ARMOR = 6;
	/** 联盟科技 空军加成 */
	const TECH_AIR = 7;
	/** 联盟科技 炮兵加成 */
	const TECH_GUN = 8;

	/** 联盟科技列表 */
	static $unionTechName = array(
		self::TECH_CD_BUILD => '工业技术',
		self::TECH_CD_TECH  => '科技技术',
		self::TECH_ARMY_NUM => '紧急征召',
		self::TECH_CRIT     => '致命一击',
		self::TECH_FOOT     => '强化步兵',
		self::TECH_ARMOR    => '强化装甲',
		self::TECH_AIR      => '强化空军',
		self::TECH_GUN      => '强化炮兵'
	);

	/** 联盟科技(加成百分比, 科技所需等级, 升级所用系数) */
	static $unionTech = array(
		self::TECH_CD_BUILD => array(2, 1, 100),
		self::TECH_CD_TECH  => array(2, 3, 100),
		self::TECH_ARMY_NUM => array(3, 10, 200),
		self::TECH_CRIT     => array(1, 9, 200),
		self::TECH_FOOT     => array(3, 5, 150),
		self::TECH_ARMOR    => array(3, 6, 150),
		self::TECH_AIR      => array(3, 7, 150),
		self::TECH_GUN      => array(3, 8, 150)
	);

	/**
	 * 根据联盟ID获取联盟信息
	 * @author HeJunyun
	 * @param int $id 用户ID
	 * @return array/bool
	 */
	static public function getInfo($unionId, $isLoad = false) {
		$info = false;
		if (!empty($unionId)) {
			$rc   = new B_Cache_RC(T_Key::UNION_INFO, $unionId);
			$info = $rc->hgetall();
			if ($isLoad || empty($info['id'])) {
				$info = B_DB::instance('Alliance')->get($unionId);
				if (isset($info['id'])) {
					$unionList   = M_Union::getUnionMemberList($unionId);
					$totalRenown = $totalPeople = 0;
					foreach ($unionList as $k => $v) {
						$totalRenown += $v['renown'];
						$totalPeople++;
					}
					$info['total_person'] = $totalPeople;
					$info['total_renown'] = $totalRenown;

					$bUp = B_DB::instance('Alliance')->update($info, $info['id']);
					$ret = $rc->hmSet($info);

				}
			}
		}

		return $info;
	}

	/**
	 * 修改联盟信息
	 * @author HeJunyun
	 * @param int $id 联盟ID
	 * @param array $data 更新信息
	 * @param bool $isDB 是否更新数据库
	 */
	static public function setInfo($unionId, $upInfo, $upDB = true) {
		$ret = false;
		if (!empty($unionId) &&
			is_array($upInfo) &&
			!empty($upInfo)
		) {
			$info = array();
			foreach ($upInfo as $key => $val) {
				if (!empty($key) && in_array($key, T_DBField::$unionFields)) {
					$info[$key] = $val;
				}
			}

			if (!empty($info)) {
				$rc  = new B_Cache_RC(T_Key::UNION_INFO, $unionId);
				$ret = $rc->hmset($info);
				if ($ret) {
					$upDB && M_CacheToDB::addQueue(T_Key::UNION_INFO . ':' . $unionId);
				} else {
					$msg = array(__METHOD__, 'Update Union Info Fail', func_get_args());
					Logger::error($msg);
				}
			}
		}

		return $ret ? $info : false;
	}

	static public function addUnionMemberIds($unionId, $cityId) {
		$rc  = new B_Cache_RC(T_Key::UNION_MEMBER_LIST, $unionId);
		$ret = $rc->sadd($cityId);
		if (!$ret) {
			$msg = array(__METHOD__, 'add Union Member CityId Fail', func_get_args());
			Logger::error($msg);
		}
		return $ret;
	}

	static public function delUnionMemberIds($unionId, $cityId) {
		$rc = new B_Cache_RC(T_Key::UNION_MEMBER_LIST, $unionId);
		if ($rc->sismember($cityId)) {
			$ret = $rc->srem($cityId);
			if (!$ret) {
				$log = array($rc->smembers(), func_get_args());
				$msg = array(__METHOD__, 'del Union Member CityId Fail', $log);
				Logger::error($msg);
			}
		} else {
			$ret = true;
		}

		return $ret;
	}

	static public function getUnionMemberIds($unionId) {
		$rc         = new B_Cache_RC(T_Key::UNION_MEMBER_LIST, $unionId);
		$memberList = $rc->smembers();
		if (empty($memberList)) {
			$list = B_DB::instance('AllianceMember')->getsBy(array('union_id' => $unionId));
			foreach ($list as $val) {
				$rc->sadd($val['city_id']);
				$memberList[] = $val['city_id'];
			}
		}
		return $memberList;
	}

	/**
	 * 获取联盟成员列表
	 * @author HeJunyun
	 * @param int $unionId 联盟ID
	 * @return array $rows
	 */
	static public function getUnionMemberList($unionId) {
		$rows       = array();
		$memberList = M_Union::getUnionMemberIds($unionId);
		if (!empty($memberList)) {
			foreach ($memberList as $cityId) {
				$memberInfo = M_Union::getMemberInfo($unionId, $cityId);
				if (!empty($memberInfo['city_id'])) {
					$cityInfo                = M_City::getInfo($cityId);
					$memberInfo['nickname']  = $cityInfo['nickname'];
					$memberInfo['renown']    = $cityInfo['renown'];
					$memberInfo['mil_medal'] = $cityInfo['mil_medal'];
					$memberInfo['user_id']   = $cityInfo['user_id'];
					$memberInfo['pos_no']    = $cityInfo['pos_no'];
					$rows[]                  = $memberInfo;
				} else {
					self::delMemberInfo($unionId, $cityId);
					Logger::error(array(__METHOD__, 'get member info fail', array($unionId, $memberInfo)));
				}
			}
		}
		return $rows;
	}

	/**
	 * 获取成员表信息
	 * @author HeJunyun
	 * @param int $memberId
	 */
	static public function getMemberInfo($unionId, $cityId) {
		$rc   = new B_Cache_RC(T_Key::UNION_MEMBER_INFO, $unionId . '_' . $cityId);
		$info = $rc->hgetall();
		if (empty($info['city_id'])) {
			$info = B_DB::instance('AllianceMember')->getBy(array('union_id' => $unionId, 'city_id' => $cityId));
			if (!empty($info['city_id'])) {
				$rc->hmset($info, T_App::ONE_WEEK);
			}
		}
		return $info;
	}

	/**
	 * 修改联盟成员表信息
	 * @author Hejunyun
	 * @param int $cityId
	 * @param array $data
	 * @param bool $isDB
	 * @return bool
	 */
	static public function setMemberInfo($cityId, $unionId, $data, $upDB = true) {
		$ret  = false;
		$info = M_Union::getMemberInfo($unionId, $cityId);
		if (!empty($info['city_id'])) {
			$rc  = new B_Cache_RC(T_Key::UNION_MEMBER_INFO, $unionId . '_' . $cityId);
			$ret = $rc->hmset($data, T_App::ONE_DAY);
			if ($ret) //同步信息至数据库
			{
				$upDB && M_CacheToDB::addQueue(T_Key::UNION_MEMBER_INFO . ':' . $unionId . '_' . $cityId);
			} else {
				Logger::error(array(__METHOD__, 'Err Member Info', func_get_args()));
			}
		}
		return $ret;
	}

	static public function delMemberInfo($unionId, $cityId) {
		$ret     = false;
		$unionId = intval($unionId);
		$cityId  = intval($cityId);
		if ($unionId && $cityId) {
			$rc  = new B_Cache_RC(T_Key::UNION_MEMBER_INFO, $unionId . '_' . $cityId);
			$ret = $rc->delete();
			if (!$ret) {
				$msg = array(__METHOD__, 'Delete Union Member Info Fail', func_get_args());
				Logger::error($msg);
			} else {
				B_DB::instance('AllianceMember')->deleteBy(array('union_id' => $unionId, 'city_id' => $cityId));
			}
		}
		return $ret;
	}

	/**
	 * 获取联盟列表
	 * @author Hejunyun
	 * @param int $page 第几页
	 */
	static public function getList($page, $pageSize = 9) {
		$now = time();
		$ret = array();

		$start = ($page - 1) * $pageSize;
		$end   = $page * $pageSize - 1;

		$syncTime = M_Ranking::getSyncTime(M_Ranking::RANKINGS_UNION);
		$needSync = false;
		$rc       = new B_Cache_RC(T_Key::UNION_LIST);
		if (!$rc->exists()) {
			$needSync = true;
		} else if (empty($syncTime)) {
			$needSync = true;
		} else if (!empty($syncTime) && ($now - $syncTime) > T_App::ONE_MINUTE * 5) //联盟排行每分钟更新一次
		{
			$needSync = true;
		}

		if ($needSync) {
			M_Union::syncUnionListRank();
			M_Ranking::setSyncTime(M_Ranking::RANKINGS_UNION, $now);
		}

		$list = $rc->zrange($start, $end);
		$sum  = $rc->zcard();

		$ret['page']    = $page;
		$ret['list']    = $list;
		$ret['sumPage'] = ceil($sum / $pageSize);
		$ret['total']   = $sum;
		return $ret;
	}

	/**
	 * 更新联盟排名缓存
	 * @author Hejunyun
	 */
	static public function syncUnionListRank() {
		$data = array();

		$rc = new B_Cache_RC(T_Key::UNION_LIST);
		$rc->delete();

		$idList = B_DB::instance('Alliance')->idList();
		$i      = 1;
		foreach ($idList as $val) {
			$setArr = array('rank' => $i);
			M_Union::setInfo($val['id'], $setArr);
			$rc->zadd($i, $val['id']);
			$data[] = $val['id'];
			$i++;
		}
		return $data;
	}

	/**
	 * 添加成员信息
	 * @author Hejunyun
	 * @param array $memberData
	 * @return int $memberId
	 */
	static public function addMember($memberData) {
		$memberId = B_DB::instance('AllianceMember')->insert($memberData);
		return $memberId;
	}


	/**
	 * 添加联盟
	 * @author Hejunyun on 20110628
	 * @param array $data
	 */
	static public function addUnion($data) {
		$ret = false;

		//添加联盟
		$unionId = B_DB::instance('Alliance')->insert($data);
		if ($unionId > 0) {
			//修改城市表联盟ID字段
			if (M_City::setCityInfo($data['create_city_id'], array('union_id' => $unionId))) {
				//删除玩家申请的联盟列表缓存
				M_Union::delUnionAppKey($data['create_city_id']);

				//添加联盟成员info table:union_member
				$memberData = array(
					'union_id'  => $unionId,
					'city_id'   => $data['create_city_id'],
					'position'  => self::UNION_MEMBER_TOP, //默认军团长
					'flag'      => 1, //状态：0申请，1通过
					'point'     => 0,
					'create_at' => time()
				);
				$memberId   = M_Union::addMember($memberData);
				if ($memberId) {
					$rc  = new B_Cache_RC(T_Key::UNION_LIST);
					$num = $rc->zcard();
					$num = $num + 1;
					$rc->zadd($num, $unionId);
					//添加联盟成员List缓存      例：联盟ID=>array(cityId1, cityId2)
					M_Union::addUnionMemberIds($unionId, $data['create_city_id']);
					//创建成功扣黄金
					$goldNum = M_Config::getVal('union_create_cost');

					$objPlayer = new O_Player($data['create_city_id']);
					$objPlayer->Res()->incr('gold', -$goldNum);

					$objPlayer->save();
					//同步城市表联盟ID
					$msArr = array(
						'union_id'  => $unionId,
						'UnionName' => $data['name'],
					);
					M_Sync::addQueue($data['create_city_id'], M_Sync::KEY_CITY_INFO, $msArr, false); //同步数据!
					$ret = $unionId;
				}
			}
		}
		return $ret;
	}

	/**
	 * 加入联盟
	 * @author Hejunyun on 201106
	 * @param int $cityId 城市ID
	 * @param int $unionId 联盟ID
	 * @param int $unionId 联盟名字
	 */
	static public function joinUnion($cityId, $unionId, $unionName = '') {
		$ret     = false;
		$cityId  = intval($cityId);
		$unionId = intval($unionId);
		if ($cityId && $unionId) {
			if (M_City::setCityInfo($cityId, array('union_id' => $unionId))) {
				//同步城市表联盟ID
				$msArr = array(
					'union_id'  => $unionId,
					'UnionName' => $unionName,
				);
				M_Sync::addQueue($cityId, M_Sync::KEY_CITY_INFO, $msArr, false); //同步数据!
				$memberData = array(
					'union_id'  => $unionId,
					'city_id'   => $cityId,
					'position'  => self::UNION_MEMBER_ORDINARY,
					'flag'      => 1, //状态：0申请，1通过
					'point'     => 0,
					'create_at' => time()
				);
				$memberId   = M_Union::addMember($memberData);
				if ($memberId) {
					$ret = $memberId;
					M_Union::addUnionMemberIds($unionId, $cityId);
					//删除玩家申请的联盟列表缓存
					M_Union::delUnionAppKey($cityId);
				}
			}
		}
		return $ret;
	}


	/**
	 * 删除联盟成员
	 * @author Hejunyun
	 * @param array $unionMemberInfo
	 * @return bool
	 */
	static public function delUnionMember($unionMemberInfo) {
		$res = false;
		if (isset($unionMemberInfo['id'])) {
			$unionId = $unionMemberInfo['union_id'];
			$cityId  = $unionMemberInfo['city_id'];
			$res     = M_City::setCityInfo($cityId, array('union_id' => 0));
			if ($res) {
				//同步城市表联盟ID
				$msArr = array(
					'union_id'  => 0,
					'UnionName' => '',
				);
				//联盟总人数减少
				$unionInfo              = M_Union::getInfo($unionId);
				$total_person           = max($unionInfo['total_person'] - 1, 0);
				$setArr['total_person'] = $total_person;
				$cityInfo               = M_City::getInfo($cityId);
				if ($cityInfo['renown']) {
					$setArr['total_renown'] = $unionInfo['total_renown'] - $cityInfo['renown'];
				}
				M_Union::setInfo($unionId, $setArr);
				M_Sync::addQueue($cityId, M_Sync::KEY_CITY_INFO, $msArr, false); //同步数据!
				M_Union::delMemberInfo($unionId, $cityId);
				M_Union::delUnionMemberIds($unionId, $cityId);
			}
		}
		return $res;
	}

	/**
	 * 取消申请
	 * @author Hejunyun
	 * @param array $memberInfo
	 */
	static public function cancelApply($cityId, $unionId) {
		$res = false;
		if ($cityId > 0 && $unionId > 0) {
			$res = M_Union::delMemberInfo($unionId, $cityId);
			if ($res) {
				M_Union::delApplyList($cityId, $unionId);
				M_Union::delUnionMemberIds($unionId, $cityId);
			}
		}
		return $res;
	}

	/**
	 * 贡献金币
	 * @author Hejunyun
	 * @param int $cityId 城市ID
	 * @param int $unionId 联盟ID
	 * @param int $addGold 贡献金额
	 * @return bool
	 */
	static public function donationGold($cityId, $unionId, $addGold) {
		$ret        = false;
		$memberInfo = M_Union::getMemberInfo($unionId, $cityId);
		$unionInfo  = M_Union::getInfo($unionId);
		if ($memberInfo && $unionInfo) {
			$objPlayer = new O_Player($cityId);
			$objPlayer->Res()->incr('gold', -$goldNum);

			$goldNum = addGold * M_Union::$unionConf['union_coin_pption'];
			if (M_Union::setInfo($unionId, array('coin' => $unionInfo['coin'] + $addGold)) &&
				M_Union::setMemberInfo($cityId, $unionId, array('point' => $memberInfo['point'] + $addGold))
			) {

				$objPlayer->save();
				$ret = true;
			}
		}
		return $ret;
	}

	/**
	 * 添加友好关系联盟
	 * @author Hejunyun
	 * @param int $unionId 联盟ID
	 * @param int $friendUnion 添加到关系表的联盟
	 * @return bool
	 */
	static public function addRelFriend($unionId, $friendUnionName) {
		$res       = false;
		$unionInfo = M_Union::getInfo($unionId);
		if (isset($unionInfo['id'])) {
			if ($unionInfo['rel_friend']) {
				$relFriend = json_decode($unionInfo['rel_friend'], true);
			} else {
				$relFriend = array();
			}
			if (!in_array($friendUnionName, $relFriend)) {
				array_push($relFriend, $friendUnionName);
			}
			$relFriend = json_encode($relFriend);
			$data      = array(
				'rel_friend' => $relFriend
			);
			$res       = M_Union::setInfo($unionId, $data);
		}
		return $res;
	}

	/**
	 * 添加敌对关系联盟
	 * @author Hejunyun
	 * @param int $unionId 联盟ID
	 * @param int $enemyUnion 添加到关系表的联盟
	 * @return bool
	 */
	static public function addRelEnemy($unionId, $name) {
		$res       = false;
		$unionInfo = M_Union::getInfo($unionId);
		if (isset($unionInfo['id'])) {
			if ($unionInfo['rel_enemy']) {
				$relEnemy = json_decode($unionInfo['rel_enemy'], true);
			} else {
				$relEnemy = array();
			}
			if (!in_array($name, $relEnemy)) {
				array_push($relEnemy, $name);
			}
			$relFriend = json_encode($relEnemy);
			$data      = array(
				'rel_enemy' => $relEnemy
			);

			$res = M_Union::setInfo($unionId, $data);
		}
		return $res;
	}

	/**
	 * 根据联盟名称查询联盟信息
	 * @author Hejunyun
	 * @param string $name 联盟名称
	 * @return array
	 */
	static public function getUnionByName($name) {
		$id   = B_DB::instance('Alliance')->getIdByName($name);
		$info = M_Union::getInfo($id);
		return $info;
	}

	/**
	 * 等级提升
	 * @author Hejunyun
	 * @param int $unionId 联盟ID
	 * @param int $costGold 升级成本
	 */
	static public function unionUpgrade($unionId, $costGold) {
		$ret       = false;
		$unionInfo = M_Union::getInfo($unionId);
		if ($unionInfo['coin'] >= $costGold) {
			$data = array(
				'coin'  => $unionInfo['coin'] - $costGold,
				'level' => $unionInfo['level'] + 1
			);
			$ret  = M_Union::setInfo($unionId, $data);
		}
		return $ret;
	}

	/**
	 * 获取联盟奖励
	 * @author Hejunyun
	 * @param int $unionLevel 联盟等级
	 * @param int $unionPoint 贡献度
	 */
	static public function getUnionAward($unionLevel, $unionPoint) {
		$award  = array();
		$gold   = 0;
		$coupon = 0;
		if ($unionPoint) {
			$unionPoint = $unionPoint * M_Union::$unionConf['union_coin_pption'];
			$v          = 1;
			$unionPoint > 1000000 && $v = 2;
			$unionPoint > 2000000 && $v = 5;
			$unionPoint > 5000000 && $v = 10;
			$unionPoint > 10000000 && $v = 20;
			$unionPoint > 20000000 && $v = 30;
			$unionPoint > 30000000 && $v = 50;
			$unionPoint > 50000000 && $v = 70;
			$unionPoint > 70000000 && $v = 100;
			$unionPoint > 100000000 && $v = 200;
			$gold = M_Union::$unionConf['union_award'] * $unionLevel / 100 * $v;

			if ($unionLevel > 5) {
				$coupon = 1;
				$unionPoint > 1000000 && $coupon = 2;
				$unionPoint > 2000000 && $coupon = 3;
				$unionPoint > 5000000 && $coupon = 4;
				$unionPoint > 10000000 && $coupon = 5;
				$unionPoint > 20000000 && $coupon = 6;
				$unionPoint > 30000000 && $coupon = 7;
				$unionPoint > 50000000 && $coupon = 8;
				$unionPoint > 70000000 && $coupon = 9;
				$unionPoint > 100000000 && $coupon = 10;

			}
		}
		$award['gold']   = $gold;
		$award['coupon'] = $coupon;
		return $award;
	}

	/**
	 * 更新联盟总威望
	 * @author Hejunyun
	 * @param int $id 联盟ID
	 * @param int $totalRenown 总威望
	 */
	static public function setUnionTotalRenown($id, $totalRenown) {
		$arr    = array(
			'total_renown' => $totalRenown
		);
		$result = B_DB::instance('Alliance')->update($arr, $id);
		return $result;
	}

	/**
	 * 联盟总威望增加
	 * @author Hejunyun
	 * @param int $id 联盟ID
	 * @param int $renown 增长威望
	 * @return bool
	 */
	static public function addUnionRenown($id, $renown) {
		$res       = false;
		$renown    = intval($renown);
		$unionInfo = M_Union::getInfo($id);
		if ($renown > 0 && $unionInfo) {
			$data = array(
				'total_renown' => $unionInfo['total_renown'] + $renown
			);
			$res  = M_Union::setInfo($id, $data);
		}
		return $res;
	}

	/**
	 * 联盟总威望减少
	 * @author Hejunyun
	 * @param array $unionMemberInfo
	 * @return bool
	 */
	static public function decrUnionRenown($unionMemberInfo) {
		$res         = false;
		$ownCityInfo = M_City::getInfo($unionMemberInfo['city_id']);
		if ($ownCityInfo['renown'] > 0 && $unionMemberInfo) {
			$data = array(
				'total_renown' => max($unionMemberInfo['total_renown'] - $ownCityInfo['renown'], 0)
			);
			$res  = M_Union::setInfo($unionMemberInfo['union_id'], $data);
		}
		return $res;
	}

	/**
	 * 初始化联盟科技
	 */
	static public function initUnionTech($id) {
		$ret = false;
		$id  = intval($id);
		if ($id > 0) {
			foreach (M_Union::$unionTech as $techId => $val) {
				$techData[$techId] = 0;
			}
			$techData = json_encode($techData);
			$ret      = M_Union::setInfo($id, array('tech_data' => $techData));
		}
		return $ret ? $techData : $ret;
	}

	/**
	 * 获取联盟科技加成值
	 * @author Hejunyun
	 * @param int $unionId 联盟ID
	 * @param int $techId 科技ID
	 * @return int $val 加成值
	 */
	static public function getUnionTechAddition($unionInfo, $techId) {
		$val           = 0;
		$baseUnionTech = M_Config::getVal('union_tech');
		if (!empty($baseUnionTech[$techId]) &&
			!empty($unionInfo['tech_data']) &&
			$techId > 0
		) {
			$techData  = json_decode($unionInfo['tech_data'], true);
			$techLevel = isset($techData[$techId]) ? $techData[$techId] : 0;
			if (isset($baseUnionTech[$techId][$techLevel])) {
				list($unionLv, $addVal, $costCoin) = $baseUnionTech[$techId][$techLevel];
				$val = $addVal;
			}
		}
		return $val;
	}


	/**
	 * 玩家申请加入某联盟
	 * @author Hejunyun
	 * @param int $cityId
	 * @param int $unionId
	 */
	static public function userAppUnion($cityId, $unionId) {
		$cityId  = intval($cityId);
		$unionId = intval($unionId);
		if ($cityId && $unionId) {
			$rc1 = new B_Cache_RC(T_Key::UNION_USER_APP_LIST, $cityId);
			$rc2 = new B_Cache_RC(T_Key::UNION_APP_USER_LIST, $unionId);
			if ($rc1->sismember($unionId)) {
				$res1 = true;
			} else {
				$res1 = $rc1->sadd($unionId);
				if (!$res1) {
					Logger::error(array(__METHOD__, 'UNION_USER_APP_LIST Fail', func_get_args()));
				}
			}

			if ($rc2->sismember($cityId)) {
				$res2 = true;
			} else {
				$res2 = $rc2->sadd($cityId);
				if (!$res2) {
					Logger::error(array(__METHOD__, 'UNION_APP_USER_LIST Fail', func_get_args()));
				}
			}
			if ($res1 && $res2) {
				return true;
			}
		}

		return false;
	}

	/**
	 * 获取玩家申请的联盟列表
	 * @author Hejunyun
	 * @param int $cityId
	 * @return array(1,2,3...)
	 */
	static public function getUserAppList($cityId) {
		$list   = array();
		$cityId = intval($cityId);
		if ($cityId) {
			$rc   = new B_Cache_RC(T_Key::UNION_USER_APP_LIST, $cityId);
			$list = $rc->smembers();
		}
		return $list;
	}

	/**
	 * 获取玩家被招募的联盟列表
	 * @author duhuihui
	 * @param int $cityId
	 * @return array(1=>1,2=>1,3...)
	 */
	static public function getUserInviteList($cityId) {
		$list   = array();
		$cityId = intval($cityId);
		if ($cityId) {
			$rc   = new B_Cache_RC(T_Key::UNION_HIRE, 'c' . $cityId);
			$list = $rc->smembers();
		}
		return $list;
	}

	/**
	 * 判断联盟招募是否超过一定次数
	 * @author duhuihui
	 * @param int $cityId
	 * @return array(1=>1,2=>1,3...)
	 */
	static public function getInviteTimesList($unionId) {
		$list    = array();
		$unionId = intval($unionId);
		if ($unionId) {
			$rc   = new B_Cache_RC(T_Key::UNION_INVITE_TIMES, date('Ymd') . $unionId);
			$list = $rc->hgetall();
		}
		return $list;
	}

	/**
	 * 获取联盟的申请玩家列表
	 * @author Hejunyun
	 * @param int $unionId
	 * @return array(1,2,3...)
	 */
	static public function getUnionAppList($unionId) {
		$list    = array();
		$unionId = intval($unionId);
		if ($unionId) {
			$rc   = new B_Cache_RC(T_Key::UNION_APP_USER_LIST, $unionId);
			$list = $rc->smembers();
			$list = $list ? $list : array();
		}
		return $list;
	}

	/**
	 * 玩家取消某联盟的申请
	 * @author Hejunyun
	 * @param int $cityId
	 * @param int $unionId
	 */
	static public function userUnAppUnion($cityId, $unionId) {
		$ret     = false;
		$cityId  = intval($cityId);
		$unionId = intval($unionId);
		if ($cityId && $unionId) {
			$rc1 = new B_Cache_RC(T_Key::UNION_USER_APP_LIST, $cityId);
			$rc2 = new B_Cache_RC(T_Key::UNION_APP_USER_LIST, $unionId);

			if ($rc1->sismember($unionId)) {
				$res1 = $rc1->srem($unionId);
				if (!$res1) {
					Logger::error(array(__METHOD__, 'Remove UNION_USER_APP_LIST Fail', func_get_args()));
				}
			} else {
				$res1 = true;
			}

			if ($rc2->sismember($cityId)) {
				$res2 = $rc2->srem($cityId);
				if (!$res2) {
					Logger::error(array(__METHOD__, 'Remove UNION_APP_USER_LIST Fail', func_get_args()));
				}
			} else {
				$res2 = true;
			}

			if ($res1 && $res2) {
				$ret = true;
			}
		}

		return $ret;
	}

	/**
	 * 删除玩家相关的所有联盟申请缓存
	 * @author Hejunyun
	 * @param int $cityId
	 */
	static public function delUnionAppKey($cityId) {
		//$ret = false;
		$cityId = intval($cityId);
		if ($cityId) {
			$unionList = self::getUserAppList($cityId);
			if ($unionList) {
				foreach ($unionList as $unionId) {
					self::userUnAppUnion($cityId, $unionId);
				}
			}
		}
		/*
		 if (!$ret)
		 {
		$msg = array(__METHOD__, 'Del User Apply Union Key Fail', func_get_args());
		Logger::error($msg);
		}
		*/
		//return $ret;
	}

	/**
	 * 当天是否领取过联盟奖励
	 * @param int $cityId
	 * @return $ret bool
	 */
	static public function isAward($cityId) {
		$ret = false;
		$rc  = new B_Cache_RC(T_Key::UNION_AWARD_KEY, $cityId);
		if ($rc->exists()) {
			$today = strtotime(date('Ymd'));
			$date  = $rc->get();
			$ret   = $date < $today ? true : false;
		} else {
			$ret = true;
		}
		return $ret;
	}

	/**
	 * 更新领取联盟奖励时间
	 * @param int $cityId
	 * @return $ret bool
	 */
	static public function setAwardDate($cityId) {
		$ret   = false;
		$rc    = new B_Cache_RC(T_Key::UNION_AWARD_KEY, $cityId);
		$today = strtotime(date('Ymd'));
		$ret   = $rc->set($today, T_App::ONE_WEEK);
		return $ret;
	}

	/**
	 * 得到加入军团的冷却时间
	 * @param int $cityId
	 * @return $ret bool
	 */
	static public function getUnionCd($cityId) {
		$unionCd = '0_1';
		$rc      = new B_Cache_RC(T_Key::CD_APPLY_UNION, $cityId);
		if ($rc->exists()) {
			$unionCd = $rc->get();
		}
		return $unionCd;
	}

	/**
	 * 更新加入军团的冷却时间
	 * @param int $cityId
	 * @return $ret bool
	 */
	static public function setUnionCd($cityId, $unionCd) {
		$ret = false;
		$rc  = new B_Cache_RC(T_Key::CD_APPLY_UNION, $cityId);
		$ret = $rc->set($unionCd);

		return $ret;
	}

}

?>
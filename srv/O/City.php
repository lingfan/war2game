<?php

class O_City implements O_I {
	private $_cityId = 0;
	private $_info = array();
	private $_sync = array();
	private $_change = false;

	private $_now = 0;
	/**
	 * @var O_Player
	 */
	private $_objPlayer = null;


	public function __construct(O_Player $objPlayer) {
		$this->_now  = time();
		$this->_info = $objPlayer->getCityBase();
		if (!empty($this->_info['id'])) {
			$this->_cityId    = $this->_info['id'];
			$this->_objPlayer = $objPlayer;
		}

	}


	public function __set($name, $value) {
		if (isset($this->_info[$name])) {
			$this->_info[$name] = $value;
			$this->_sync[$name] = $value;
			$this->_change      = true;
		}
	}

	public function __get($name) {
		return isset($this->_info[$name]) ? $this->_info[$name] : false;
	}

	/**
	 * 获取城市数据
	 * @return array
	 */
	public function get() {
		return $this->_info;
	}


	public function getSync() {
		$ret         = $this->_sync;
		$this->_sync = array();
		return $ret;
	}

	public function isChange() {
		return $this->_change;
	}

	/**
	 * 进入游戏超过3天自动出新手保护期
	 *
	 */
	public function checkNewbie() {
		if (empty($this->_info['newbie']) && ($this->_now - $this->_info['created_at']) > T_App::NEWBE_PROTECT_TIME) {
			$this->newbie          = 1;
			$this->_sync['newbie'] = $this->newbie;
			$bSync                 = M_MapWild::syncWildMapBlockCache($this->_info['pos_no']);
		}
	}

	/**
	 * 自动恢复活力值和军令值
	 */
	public function relifeEnergyOrder() {
		$oldOrder     = $this->mil_order;
		$orderUpLimit = M_City::getOrderUpLimit($this->vip_level); //玩家军令上限
		$diffTime     = intval($this->_now - $this->energy_update); //距离上一次自动恢复间隔秒数

		if ($diffTime > T_App::ONE_HOUR && $this->mil_order < $orderUpLimit) {
			//间隔了多少小时
			$hours = floor($diffTime / T_App::ONE_HOUR);
			//剩余多少秒
			$leftTime = $diffTime % T_App::ONE_HOUR;
			if ($this->mil_order < $orderUpLimit) {
				$incrNum         = M_Config::getVal('user_mil_order_incr');
				$this->mil_order = min($oldOrder + $incrNum * $hours, $orderUpLimit); //新军令值
			}

			$this->energy_update = $this->_now - $leftTime;
		}
	}

	/**
	 *
	 * 添加城市元素
	 * @author huwei
	 * @param int $cityId
	 * @param array $addItemArr [march_num活力, atkfb_num军令, renown威望, warexp功勋]
	 */
	public function addPoint($addItemArr) {
		$ret = false;
		if (isset($addItemArr['march_num'])) {
			$this->energy += intval($addItemArr['march_num']);
		}

		if (isset($addItemArr['atkfb_num'])) {
			$this->mil_order += intval($addItemArr['atkfb_num']);
		}

		if (isset($addItemArr['renown'])) {
			$this->renown += intval($addItemArr['renown']);
			if ($this->union_id) {
				$bUnion = M_Union::addUnionRenown($this->union_id, intval($addItemArr['renown']));
			}
		}

		if (isset($addItemArr['warexp'])) {
			$this->mil_medal += intval($addItemArr['warexp']);
			if ($this->newbie == 0 && $this->mil_medal >= M_Config::getVal('city_newbie_mil_medal')) {
				$this->newbie = 1;
				$bMap         = M_MapWild::syncWildMapBlockCache($this->pos_no);
			}
		}
	}


	/**
	 * 修正住宅建筑的最大人口数
	 * @author huwei
	 * @return int 最大人口数
	 */
	public function correctMaxPeople() {
		$initPeople = M_Config::getVal('city_max_people'); //初始最大人口数

		$buildList = $this->_objPlayer->Build()->get();
		$capacity  = $initPeople; //无民房默认容量
		if (isset($buildList[M_Build::ID_HOUSE])) {
			$capacity     = 0;
			$arrBuildInfo = $buildList[M_Build::ID_HOUSE];
			foreach ($arrBuildInfo as $pos => $level) {
				$capacity += M_Formula::calcHouseCapaCity($initPeople, $level);
			}
		}
		return $capacity;
	}

	/**
	 * 判断某城市市场交易额更新结果
	 * @param int $num 新的交易额
	 * @return bool true交易额合法并成交/false 交易额非法或交易失败
	 */
	public function isTradeQuotaOK($num) {
		$ret        = false;
		$tradeQuota = $this->getTradeQuota();
		if ($tradeQuota >= $num) {
			$arrMarket  = json_decode($this->market_amount, true);
			$todayStamp = mktime(0, 0, 0);
			if (isset($arrMarket[$todayStamp])) {
				$num += $arrMarket[$todayStamp];
			}
			$upData              = array($todayStamp => $num);
			$this->market_amount = json_encode($upData);
			$ret                 = true;
		}
		return $ret;
	}

	/**
	 * 校正城市已占用人口
	 * @return int 超出人口数
	 */
	public function correctPeople() {
		$overNum = 0;
		if (!empty($this->id)) {
			$maxPeople      = $this->max_people;
			$usedArmyPeople = $this->correctArmyPeople(); //被占用人口
			$usedHeroPeople = $this->correctHeroPeople(); //被占用人口
			$usedPeople     = $usedArmyPeople + $usedHeroPeople;

			if ($usedPeople > $maxPeople) {
				//校正人口数量,如果兵数大于人口数则 让玩家拥有多余的兵
				//没人口 不可以再招兵
				$overNum          = $usedPeople - $maxPeople;
				$this->cur_people = $maxPeople;

				Logger::error(array(__METHOD__, "CityPeople>max:CityId#{$this->id};CurPeople#{$this->cur_people};usedPeople#{$usedPeople};usedArmyPeople#{$usedArmyPeople};usedHeroPeople#{$usedHeroPeople};maxPeople#{$maxPeople}"));
			}

			if ($usedPeople != $this->cur_people) {
				$syncData = array('cur_people' => $this->cur_people);
				M_Sync::addQueue($this->id, M_Sync::KEY_CITY_INFO, $syncData);
			}

		}
		return $overNum;
	}

	/**
	 * 英雄所带兵种的人口数
	 * @return int 所带兵种人口数
	 */
	public function correctHeroPeople() {
		$heroList        = M_Hero::getCityHeroList($this->id);
		$arrBaseArmyInfo = M_Base::armyAll();
		$usedPeople      = 0;
		foreach ($heroList as $heroId) {
			$heroInfo = M_Hero::getCityHeroInfo($this->id, $heroId);
			$armyId   = $heroInfo['army_id'];
			if ($armyId > 0) {
				$usedPeople += intval($arrBaseArmyInfo[$armyId]['cost_people'] * intval($heroInfo['army_num']));
			}

		}
		return $usedPeople;
	}

	/**
	 * 预备兵种的人口数
	 * @author huwei
	 * @return int 预备兵种人口数
	 */
	public function correctArmyPeople() {
		$arrArmyList     = $this->_objPlayer->Army()->get();
		$arrBaseArmyInfo = M_Base::armyAll();
		$usedPeople      = 0;
		foreach ($arrArmyList as $armyId => $armyInfo) {
			if (isset(M_Army::$type[$armyId])) {
				$usedPeople += intval($arrBaseArmyInfo[$armyId]['cost_people'] * intval($armyInfo[0]));
			}
		}
		return $usedPeople;
	}

	/**
	 * 获取当前市场交易限额值
	 * @return int 限额值
	 */
	public function getTradeQuota() {
		$ret = 0;

		$cityBuildList = $this->_objPlayer->Build()->get();
		if (isset($cityBuildList[M_Build::ID_MARKET])) {
			$ret        = M_Formula::calcMarketTradeMax(M_City::TRADE_QUOTA_RATE, current($cityBuildList[M_Build::ID_MARKET])); //初始每天交易限额
			$arrMarket  = json_decode($this->market_amount, true);
			$todayStamp = mktime(0, 0, 0);
			if (isset($arrMarket[$todayStamp])) {
				$ret = max($ret - $arrMarket[$todayStamp], 0);
			}
		}
		return $ret;
	}

	/**
	 * 释放 阵亡 兵种 占用 人口
	 * @param int $diedPeople
	 * @return bool
	 */
	public function diedPeopleToFreePeople($diedPeople) {
		$this->cur_people -= $diedPeople;

		if ($this->cur_people < 0) {
			Logger::error(array(__METHOD__, "CityPeople<0:CityId#{$this->id};CurPeople#{$this->cur_people};diedPeople#{$diedPeople};max_people#{$this->max_people}", func_get_args()));
			$this->cur_people = 0;
		}

		$this->correctPeople();
	}

	public function decrCurrency($type, $num, $action = '', $data = '') {
		$ret = false;
		if ($type == T_App::MILPAY && $this->mil_pay >= $num) {
			$this->mil_pay -= $num;
			$ret = true;
		} else if ($type == T_App::COUPON && $this->coupon >= $num) {
			$this->coupon -= $num;
			$ret = true;
		}

		if ($ret) {
			$this->_objPlayer->Log()->expense($type, $num, $action, $data);
		}
		return $ret;
	}

	/**
	 * 判断某玩家是否为未成年人
	 * @return bool true成年/false未成年
	 */
	public function isAdult() {
		$ret    = true; //默认成年
		$switch = M_Config::getSvrCfg('anti_addiction_switch');
		if ($switch && $this->is_adult == M_AntiAddiction::ADULT_NO) {
			$ret = false;
		}
		return $ret;
	}

	/**
	 * @param array $awardResult array(模式, 奖励内容)
	 * @param string $from
	 * @return void
	 */
	public function toAward($awardResult, $from) {
		foreach ($awardResult as $val) {
			list($type, $num, $id) = $val;
			$num = intval($num);

			switch ($type) {
				case 'gold':
					$this->_objPlayer->Res()->incr('gold', $num);
					break;
				case 'food':
					$this->_objPlayer->Res()->incr('food', $num);
					break;
				case 'oil':
					$this->_objPlayer->Res()->incr('oil', $num);
					break;
				case 'milpay':
					$this->milpay += $num;
					break;
				case 'coupon':
					$this->coupon += $num;
					break;
				case 'renown':
					$this->renown += $num;
					break;
				case 'eploit':
					$this->eploit += $num;
					break;
				case 'energy':
					$this->energy += $num;
					break;
				case 'props':
					$failProps = array();
					$ret       = $this->_objPlayer->Pack()->incr($id, $num);
					if (!$ret) {
						array_push($failProps, $id);
					}
					break;
				case 'equip':
					$failEquip = array();
					$tplInfo   = M_Equip::baseInfo($id);
					for ($i = 0; $i < $num; $i++) {
						$ret = M_Equip::makeEquip($this->id, $tplInfo);
						if (!$ret) {
							array_push($failEquip, $id);
						}
					}
					break;
				case 'hero':
					$failHero = array();
					for ($i = 0; $i < $num; $i++) {
						$ret = M_Hero::moveTplHeroToCityHero($this->_objPlayer, $id, 0);
						if (!$ret) {
							array_push($failHero, $id);
						}
					}
					break;
			}
		}
	}

	/**
	 */
	public function getVisit() {
		$rc  = new B_Cache_RC(T_Key::CITY_VISIT, $this->id);
		$ret = $rc->hgetall();
		return $ret;
	}

	/**
	 * @param array $fields
	 */
	public function setVisit($fieldArr) {
		$rc  = new B_Cache_RC(T_Key::CITY_VISIT, $this->id);
		$ret = $rc->hmset($fieldArr, T_App::ONE_DAY);
		return $ret;
	}

	/**
	 * 更新最后登录游戏时间戳
	 * @return bool
	 */
	public function upLastLogin() {
		$ret = false;
		if (!$this->isAdult()) {
			$nowTime      = time();
			$arr          = $this->getVisit();
			$lastVisit    = !empty($arr['lastVisit']) ? intval($arr['lastVisit']) : 0;
			$offlineAddup = !empty($arr['offlineAddup']) ? intval($arr['offlineAddup']) : 0;

			//更新下线累计
			if ($nowTime > $lastVisit) {
				$offDiffTime = $nowTime - $lastVisit; //本次下线时长
				$oldOffTime  = $offlineAddup; //原有下线累计时长
				$newOffTime  = $oldOffTime + $offDiffTime; //最新下线累计时长
				$endOffTime  = (M_AntiAddiction::OFFLINE_CLEAN_ZERO > $newOffTime) ? $newOffTime : 0; //最终用于更新的下线累计时长

				$up['offlineAddup'] = $endOffTime; //更新下线时间累计时长
				if (0 == $endOffTime) {
					$up['onlineAddup'] = $endOffTime;
				}

			}
			$up['lastLogin'] = $nowTime;

			$ret = $this->setVisit($up);

		}
		return $ret;
	}

	/**
	 * 是否在线(10秒内有访问过接口)
	 * @author huwei on 20111102
	 * @param int $cityId
	 * @return bool
	 */
	public function isOnline() {
		$now       = time();
		$ret       = false;
		$arr       = $this->getVisit();
		$lastVisit = !empty($arr['lastVisit']) ? intval($arr['lastVisit']) : 0;
		if ($lastVisit > 0 && $now - $lastVisit < 30) {
			$ret = true;
		}
		return $ret;
	}

	/**
	 * 更新最后访问游戏接口时间戳
	 * @author chenhui on 20111227
	 * @param int $cityId 城市ID
	 * @return bool
	 */
	public function upLastVisit() {
		$this->_upAddict();
		$this->_upAwardTime();
		//更新用户在线时长到缓存
		$this->online_time += M_Client::VISIT_LOOP_DELAY_TIME;
	}

	private function _upAddict() {
		$ret = false;
		if (!$this->isAdult()) {
			$nowTime = time();

			//更新上线累计
			$arr          = $this->getVisit();
			$oldLastVisit = !empty($arr['lastVisit']) ? intval($arr['lastVisit']) : 0;
			$oldLastLogin = !empty($arr['lastLogin']) ? intval($arr['lastLogin']) : 0;

			if ($oldLastVisit > $oldLastLogin) {
				$onDiffTime        = max($nowTime - $oldLastVisit, 0);
				$oldOnTime         = !empty($arr['onlineAddup']) ? intval($arr['onlineAddup']) : 0; //原有上线累计时长
				$newOnTime         = $oldOnTime + $onDiffTime;
				$up['onlineAddup'] = $newOnTime;
			}

			$up['lastVisit'] = $nowTime;
			$ret             = $this->setVisit($up);

		}
		return $ret;
	}

	/**
	 * 更新最后在线时间
	 * @author huwei on 20111102
	 * @return void
	 */
	private function _upAwardTime() {
		$ret = false;

		$data = $this->getVisit();

		$baseCfg = M_Config::getVal();
		$conf    = $baseCfg['config_online_award'];

		if ($data['award_date'] != date('Ymd')) {
			//初始数据
			$data['award_time'] = 0;
			$data['award_lv']   = 1;
			$data['award_date'] = date('Ymd');
		}

		$info['award_date'] = $data['award_date'];
		$info['award_time'] = $data['award_time'];

		$lv               = !empty($data['award_lv']) ? $data['award_lv'] : 1;
		$info['award_lv'] = $lv;

		if (!empty($conf[$lv]) &&
			$conf[$lv][0] != $info['award_time']
		) {
			$info['award_time'] = min($conf[$lv][0], $info['award_time'] + M_Client::VISIT_LOOP_DELAY_TIME);
			$ret                = $this->setVisit($info);
		}
		return $ret;
	}

	/**
	 * 获取某玩家防沉迷系统加成系数
	 * @author chenhui on 20111229
	 * @param int $cityId 城市ID
	 * @return float 系数
	 */
	public function getAntiAddicRate() {
		$ret = M_AntiAddiction::INCOME_BEGIN_RATE;
		if (!$this->isAdult()) {
			$arr       = $this->getVisit();
			$onlineAdd = !empty($arr['onlineAddup']) ? intval($arr['onlineAddup']) : 0;
			if ($onlineAdd >= M_AntiAddiction::ONLINE_INCOME_ZERO) {
				$ret = M_AntiAddiction::INCOME_END_RATE;
			} else if ($onlineAdd >= M_AntiAddiction::ONLINE_INCOME_HALF) {
				$ret = M_AntiAddiction::INCOME_HALF_RATE;
			}
		}
		return $ret;
	}

	/**
	 * 过滤奖励
	 * @author huwei
	 * @param int $cityId
	 * @param array $rewardArr
	 * 金钱(gold_数量)
	 * 食物(food_数量)
	 * 石油(oil_数量)
	 * 军饷(milpay_数量)
	 * 礼券(coupon_数量)
	 * 军令(energy_数量)
	 * 军功(exploit_数量)
	 * 威望(renown_数量)
	 * 道具(props_数量_ID)
	 * 装备(equip_数量_ID)
	 * 军官(hero_数量_ID)
	 */
	public function filterAward(&$rewardArr) {
		if ($this->isAdult() && !empty($rewardArr)) {
			$rate = $this->getAntiAddicRate(); //攻击方防沉迷系数
			foreach ($rewardArr as $k => $v) {
				if (M_AntiAddiction::INCOME_END_RATE == $rate) {
					unset($rewardArr[$k]);
				} else if (in_array($v[1], array('gold', 'food', 'oil'))) {
					$rewardArr[$k] = floor($v * $rate);
				}
			}
		}
	}

	/**
	 * 获取在线奖励时间
	 * @author huwei on 20111102
	 * @return array
	 */
	public function getAwardTime() {
		$info = $this->getVisit();
		if (empty($info['award_date']) || $info['award_date'] != date('Ymd')) {
			$info['award_date'] = date('Ymd');
			$info['award_lv']   = 1;
			$info['award_time'] = 0;
			$this->setVisit($info);
		}
		return $info;
	}

	public function checkIconStatus($val) {
		$baseArr = M_Config::getVal($val);
		$ret     = false;
		if ($baseArr['fbno'] >= $this->last_fb_no && $baseArr['stime'] >= $this->_now && $baseArr['etime'] <= $this->_now) {
			$ret = true;
		}
		return $ret;
	}
}
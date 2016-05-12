<?php
function convert($size) {
	$unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
	return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}

class C_Test extends C_I {

	public function AMap1() {

		$data =  M_MapWild::getWildMapInfo(2105008);
	}

	public function ARes() {

		$this->objPlayer->Res()->upGrow();
		$this->objPlayer->Res()->calc();
		$this->objPlayer->save();
	}
	public function AItem() {
		$objPlayer = $this->objPlayer;

		$ret = $objPlayer->Pack()->add(10001, 1);


		var_dump($ret);
		$objPlayer->save();

		$ret = $objPlayer->Pack()->toFront();
		var_dump($ret);
	}

	public function AVV2() {
		$objPlayer = $this->objPlayer;

		$objRes = $objPlayer->Res();
		$objRes->upStore(1000000);
		var_dump($objRes->get());
		echo "<hr>";
		var_dump($objRes->calc());
		echo "<hr>";
		var_dump($objRes->get());

		$objRes->upGrow();
		echo "<hr>";
		var_dump($objRes->get());

		$objPlayer->save();
	}


	public function AS1() {
		$skillIds = array(6);
		$data = M_Skill::getEffect($skillIds);
		$armyId = 2;
		$effect = array();
		foreach ($data['battle'] as $key => $val) {
			//几率|技能值|触发类型|使用兵种|目标兵种|攻击类型|消耗精力|影响回合数
			if (empty($val[3])) {
				$effect[$key] = $val;
			} else if (!empty($val[3]) && $val[3] == $armyId) {
				$effect[$key] = $val;
			}
		}

		//var_dump($effect);


		$skillData = $effect;
		$curEffectType = 'att_land';
		$opType = 'ATK';

		$label = 'GT_ARMY_INCR_ATK';
		$num = 0;
		if (isset($skillData[$label]) && ($skillData[$label][2] == $opType || $skillData[$label][2] == 'ATK&DEF')) //增加防御力
		{
			$data = $skillData[$label];


			//所有类型有效
			if (B_Utils::odds($data[0])) {
				$tmpType = ($data[5] == 'SKY') ? 'def_sky' : 'def_land';

				if (empty($data[5]) || empty($curEffectType) || $tmpType == $curEffectType) {
					if (empty($data[4]) || empty($armyId) || ($data[4] == $armyId)) //所有兵种有效
					{
						$num = $data[1];


					}
				}
			}
		}

		$def_army_num = 100;
		$atk_army_num = 20;
		var_dump($num);

		var_dump($def_army_num, $atk_army_num);
		echo "<hr>";

		$val = $num;
		if (!empty($num)) {
			$valArr = explode(';', $val);
			$tmpAdd = 0;
			$tmpFac = ceil($def_army_num / $atk_army_num);

			foreach ($valArr as $tval) {
				list($fac, $atkVal) = explode(',', $tval);

				var_dump($tmpFac, $fac / 100);
				echo "<hr>";
				if ($tmpFac > ($fac / 100)) {
					$tmpAdd = $atkVal;
				}
			}
			var_dump($tmpAdd);
		}
		exit;

	}

	public function AV33() {
		$json = '{"award_no":"0","refresh_date":"20130808","city_id":"100771","refresh_num":"7","award_content":"{\"1\":{\"id\":\"238\",\"num\":1,\"rate2\":{\"1\":0,\"5\":0,\"10\":0,\"15\":0,\"20\":0,\"25\":1,\"30\":3},\"type\":\"props\"},\"2\":{\"id\":\"510\",\"num\":1,\"rate2\":{\"1\":0,\"5\":0,\"10\":0,\"15\":0,\"20\":0,\"25\":0,\"30\":0},\"type\":\"props\"},\"3\":{\"id\":\"216\",\"num\":1,\"rate2\":{\"1\":0,\"5\":0,\"10\":0,\"15\":0,\"20\":10,\"25\":20,\"30\":20},\"type\":\"props\"},\"4\":{\"id\":\"222\",\"num\":1,\"rate2\":{\"1\":0,\"5\":0,\"10\":0,\"15\":0,\"20\":10,\"25\":20,\"30\":20},\"type\":\"props\"},\"5\":{\"id\":\"189\",\"num\":1,\"rate2\":{\"1\":0,\"5\":0,\"10\":10,\"15\":15,\"20\":30,\"25\":40,\"30\":40},\"type\":\"props\"},\"6\":{\"id\":\"231\",\"num\":1,\"rate2\":{\"1\":0,\"5\":0,\"10\":10,\"15\":15,\"20\":30,\"25\":40,\"30\":40},\"type\":\"props\"},\"7\":{\"id\":\"231\",\"num\":1,\"rate2\":{\"1\":0,\"5\":0,\"10\":10,\"15\":15,\"20\":30,\"25\":40,\"30\":40},\"type\":\"props\"},\"8\":{\"id\":\"222\",\"num\":1,\"rate2\":{\"1\":0,\"5\":0,\"10\":0,\"15\":0,\"20\":10,\"25\":20,\"30\":20},\"type\":\"props\"},\"9\":{\"id\":\"193\",\"num\":1,\"rate2\":{\"1\":0,\"5\":0,\"10\":0,\"15\":0,\"20\":10,\"25\":20,\"30\":20},\"type\":\"props\"},\"10\":{\"id\":\"584\",\"num\":1,\"rate2\":{\"1\":0,\"5\":0,\"10\":0,\"15\":0,\"20\":0,\"25\":0,\"30\":0},\"type\":\"props\"},\"11\":{\"id\":\"526\",\"num\":1,\"rate2\":{\"1\":0,\"5\":0,\"10\":0,\"15\":0,\"20\":0,\"25\":0,\"30\":0},\"type\":\"props\"},\"12\":{\"id\":\"510\",\"num\":1,\"rate2\":{\"1\":0,\"5\":0,\"10\":0,\"15\":0,\"20\":0,\"25\":0,\"30\":0},\"type\":\"props\"},\"13\":{\"id\":\"241\",\"num\":1,\"rate2\":{\"1\":0,\"5\":0,\"10\":100,\"15\":80,\"20\":70,\"25\":30,\"30\":0},\"type\":\"props\"},\"14\":{\"id\":\"238\",\"num\":1,\"rate2\":{\"1\":0,\"5\":0,\"10\":0,\"15\":0,\"20\":0,\"25\":1,\"30\":3},\"type\":\"props\"},\"15\":{\"id\":\"193\",\"num\":1,\"rate2\":{\"1\":0,\"5\":0,\"10\":0,\"15\":0,\"20\":10,\"25\":20,\"30\":20},\"type\":\"props\"},\"16\":{\"id\":\"218\",\"num\":1,\"rate2\":{\"1\":0,\"5\":0,\"10\":10,\"15\":15,\"20\":30,\"25\":40,\"30\":40},\"type\":\"props\"},\"17\":{\"id\":\"189\",\"num\":1,\"rate2\":{\"1\":0,\"5\":0,\"10\":10,\"15\":15,\"20\":30,\"25\":40,\"30\":40},\"type\":\"props\"},\"18\":{\"id\":\"232\",\"num\":1,\"rate2\":{\"1\":0,\"5\":0,\"10\":10,\"15\":15,\"20\":30,\"25\":40,\"30\":40},\"type\":\"props\"}}"}';
		$b = json_decode($json, true);
		$c = json_decode($b['award_content'], true);
		$awardNo = M_Lottery::draw($c, $info['refresh_num']);
		var_dump($awardNo);
		exit;

		$userId = mt_rand(1, 1000000);
		$sid = mt_rand(11000, 90000);

		$b = M_Auth::makessid($userId, $sid);
		var_dump($b);
		$s1 = microtime(true);
		$c = M_Auth::parsessid($b);
		var_dump($c);
		$s2 = microtime(true);
		$t1 = ($s1 - $s2);
		echo sprintf('%.6f', $t1) . "<hr>";


		$s1 = microtime(true);
		$rc1 = new B_Cache_RC(T_Key::SSID_USER_INFO, '896590531e797b93');
		$userId = $rc1->get();
		$s2 = microtime(true);
		$t2 = ($s1 - $s2);
		echo sprintf('%.6f', $t2) . "<hr>";

		echo sprintf('%.6f', $t2 - $t1) . "<hr>";

		exit;
	}

	public function AB() {
		$cityId = isset($_GET['id']) ? $_GET['id'] : 0;
		$rc = new B_Cache_RC(T_Key::AUC_BUY_ONLY_DATA, 1);
		$buyingCity = $rc->get();
		if (empty($buyingCity)) {
			$rc->set($cityId, 2); //设置2秒的过期时间
			$buyingCity = $cityId;
		}
		$a = ($buyingCity == $cityId) ? 1 : 0;
		//Logger::debug(array(__METHOD__, microtime(true),$a, $cityId,$_GET));
		echo $a;
		exit;


		$a = mt_rand(100, 9000000);
		var_dump($a);
		$b = B_Utils::bin2hex($a);
		var_dump($b);
		$c = B_Utils::hex2bin($b);
		var_dump($c);
		exit;
		$sql = "select count(*) as num from wild_map where city_id > 0";

		$obj = new B_ORM('wild_map', 'pos_no');
		$sth = $obj->query($sql);
		$tt = $sth->fetch();
		$total = $tt['num'];
		if (!$total) {
			exit("get total fail! \n");
		}

		$offset = 1000;
		$start = 0;
		$num = ceil($total / $offset);

		for ($i = 1; $i <= $num; $i++) {
			$start = $offset * ($i - 1);
			$sql = "SELECT `pos_no`,city_id FROM `wild_map` where city_id > 0 LIMIT {$start},{$offset}";
			$sth = $obj->query($sql);
			$rows = $sth->fetchAll();
			foreach ($rows as $v) {
				$obj1 = new B_ORM('city', 'id');
				$row = $obj1->fetch(array('pos_no' => $v['pos_no']));
				if ($row['id'] == $v['city_id']) {
				} else {
					var_dump($row['id'], $v['city_id'], $v['pos_no']);
					echo "<hr>";
					$posNo = $v['pos_no'];
					M_MapWild::delWildMapInfo($posNo);
					M_MapWild::syncWildMapBlockCache($posNo);
				}
			}
		}
		exit;


		$posNo = '2013064';
		$info = M_MapWild::getWildMapInfo($posNo, true);
		if ($info['city_id'] != $cityInfo['id']) {
			M_MapWild::delWildMapInfo($posNo);
			M_MapWild::syncWildMapBlockCache($posNo);

			M_MapWild::initWildMapData($cityInfo['id'], $posNo);
			M_MapWild::syncWildMapBlockCache($posNo);
		} else if (empty($info['pos_no'])) {
			M_MapWild::initWildMapData($cityInfo['id'], $posNo);
			M_MapWild::syncWildMapBlockCache($posNo);
		}


	}

	public function Atdb() {
		$obj1 = new B_ORM('base_army', 'id', B_ORM::DB_TYPE_BASE);
		$obj2 = new B_ORM('city', 'id', B_ORM::DB_TYPE_GAME);


		$list = $obj1->findAll();
		$list = $obj1->findAll();
		var_dump($list);
		$list = $obj2->findOne(array('id' => 1));
		$list = $obj2->findOne(array('id' => 1));
		$list = $obj2->findOne(array('id' => 1));
		$list = $obj2->findOne(array('id' => 1));
		var_dump($list);
		echo "<hr>";
		$list = $obj2->findOne(array('id' => 1));
		$list = $obj1->findAll();
		$list = $obj2->findOne(array('id' => 1));
		var_dump($list);
		$list = $obj1->findAll();

		$list = $obj1->findAll();

		$list = $obj1->findAll();
		var_dump($list);


	}

	public function AUnnn() {
		M_Union::setMemberInfo(103846, 1, array('position' => 1));
		$cityInfo = M_City::getInfo(106355);
		M_Union::setInfo(1, array('boss' => $cityInfo['nickname']));
		M_Union::setMemberInfo(106355, 1, array('position' => 2));
	}


	public function APay2Leave() {
		$sql = "SELECT *,sum(rmb) as total FROM stats_log_pay group by username order by create_at asc";
		$sth = StatsDB::getStatsDB()->prepare($sql);
		$ret = $sth->execute();
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		$rows = $sth->fetchAll(PDO::FETCH_ASSOC);
		$arr = array();
		echo "呢称,用户名,最后访问时间,第一次充值时间,充值总额<br>";

		foreach ($rows as $val) {
			$expire = $val['create_at'] + 30 * AppConst::ONE_DAY;
			$userInfo = M_User::getInfoByUsername($val['username']);
			$cityId = M_City::getCityIdByUserId($userInfo['id']);
			$cityInfo = M_City::getInfo($cityId);
			$tmp = array(
				'nickname' => $cityInfo['nickname'],
				'username' => $val['username'],
				'last_visit_time' => date('Y-m-d H:i:s', $userInfo['last_visit_time']),
				'frist_pay_time' => date('Y-m-d H:i:s', $val['create_at']),
				'total_rmb' => $val['rmb'],
			);
			echo "{$tmp['nickname']},{$tmp['username']},{$tmp['last_visit_time']},{$tmp['frist_pay_time']},{$tmp['total_rmb']}<br>";
		}
		exit;

		//var_dump($arr);
	}


	public function ATApc() {
		M_Hero::setHeroInfo(102924, array('city_id' => '1244'));
		M_Hero::setCityHeroList('1244', '102924');
		M_Hero::delCityHeroList('1296', '102924');
		exit;

		for ($i = 0; $i < 1000; $i++) {
			$info = B_Cache_APC::get(T_Key::BASE_TECH);
		}

	}

	public function AHero() {
		$sql = "SELECT * FROM base_hero_tpl ORDER BY id";
		$sth = B_DB::getBaseDB()->prepare($sql);
		$ret = $sth->execute();
		$rows = $sth->fetchAll();
		var_export($rows);
	}

	public function ANpcExp() {
		$sql = "SELECT * FROM base_npc order by id asc";
		$sth = B_DB::getBaseDB()->prepare($sql);
		$ret = $sth->execute();
		$rows = $sth->fetchAll();
		foreach ($rows as $val) {
			$heroList = json_decode($val['army_data'], true);
			$expNum = 0;
			foreach ($heroList as $heroId) {
				$heroInfo = M_NPC::getNpcHeroInfo($heroId);
				$armyInfo = M_Army::getBaseInfoById($heroInfo['army_id']);
				$base = $armyInfo['cost_gold'] + $armyInfo['cost_food'] + $armyInfo['cost_oil'];
				$armyLv = $heroInfo['army_lv'];
				$tmpNum = M_Formula::calcArmyRecruitCost($base, $armyLv);
				echo "=={$armyLv}==={$base}======{$tmpNum}======{$heroInfo['army_num']}===={$heroId}<br>";

				$expNum += ($tmpNum * $heroInfo['army_num']);
			}
			$heroNum = count($heroList);
			echo "============={$val['id']}=============={$expNum}<br>";
			$info = array(
				'exp_num' => ceil($expNum / 2000),
				//'hero_num'=> min($heroNum, 5),
			);

			var_dump($info);
			$ret = B_DB::instance('BaseNpcTroop')->updateInfo($val['id'], $info);
			echo "<hr>";

		}

	}

	public function Atpos() {
		$pos = M_MapWild::getNoHoldMapPos(1);
		var_dump($pos);

	}



	public function Agetxml() {
		$xml = $_GET['a'];
		//reader
		$xml = simplexml_load_file("/opt/ww2/etc/xml/{$xml}.xml", 'SimpleXMLElement', LIBXML_NOCDATA);
		//echo "<pre>";
		//print_r($xml);
		//echo "</pre>";
		//echo "<hr>";
		$arr = array();
		foreach ($xml->row as $obj) {
			$tmpArr = (array)$obj->attributes();
			$attrArr = $tmpArr['@attributes'];

			/**
			 * foreach($obj->level as $levelObj)
			 * {
			 * $tmpArr = (array) $levelObj->attributes();
			 * $levelArr = $tmpArr['@attributes'];
			 * $level = $levelArr['id'];
			 * $attrArr['level'][$level] = $levelArr;
			 * }
			 **/
			$arr[$attrArr['id']] = $attrArr;
			//echo "<pre>";
			///print_r($attrArr);
			//echo "</pre>";
		}
		//exit;
		echo "<pre>";
		print_r($arr);
		echo "</pre>";
		exit;
	}


	public function AtoHeroXml() {
		$baseinfoall = B_DB::instance('BaseHeroTpl')->all();
		// set up the document
		$xml = new XmlWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement('data');

		foreach ($baseinfoall as $k => $val) {
			$arr = json_decode($val['hire_need'], true);
			unset($val['create_at']);
			$new = array();
			foreach ($arr as $kkk => $vvv) {
				$v1 = implode("_", $vvv);
				$new[] = "{$kkk}:{$v1}";
			}
			$val['hire_need'] = implode(";", $new);
			$detail = $val['detail'];
			$desc = $val['desc'];
			unset($val['detail']);
			unset($val['desc']);

			$baseinfoall[$k] = $val;
			$baseinfoall[$k]['detail'] = $detail;
			$baseinfoall[$k]['desc'] = $desc;
		}


		foreach ($baseinfoall as $val) {
			$xml->startElement('row');

			foreach ($val as $k => $v) {
				if (in_array($k, array('detail', 'desc'))) {
					$xml->startElement($k);
					$xml->writeCData($v);
					$xml->endElement();


					//$xml->writeElement($k, $v);
				} else {
					$xml->writeAttribute($k, $v);
				}
			}
			$xml->endElement();
		}
		header('Content-type: application/xml');
		$xml->endElement();
		$content = $xml->outputMemory(true);
		echo $content;
		exit;

	}


	public function AHu() {
		echo date('Y-m-d H:i:s', '1342261067');
		exit;

		//$vipConf = M_Vip::getVIPEffectConfig();
		//$arrQual = isset($vipConf['hero_award'][$vipLevel]) ? $vipConf['hero_award'][$vipLevel] : '';
		//var_dump($vipConf['hero_award']);
		exit;
		$cityId = 1;
		$ret1 = M_City::correctMaxPeople($cityId);
		var_dump($ret1);
		$ret2 = M_City::correctHeroPeople($cityId);
		var_dump($ret2);

		$ret3 = M_City::correctArmyPeople($cityId);
		var_dump($ret3);
		echo($ret1 - $ret2 - $ret3);
		echo "<hr>";
		$usedPeople = $ret2 + $ret3;
		var_dump($usedPeople);
		//$ret = M_City::setCityInfo($cityId, array('cur_people' => $usedPeople));
		//var_dump($ret);
		exit;


		$m1 = memory_get_peak_usage(true);
		$info = B_DB::instance('BaseNpcTroop')->getAll();
		$m2 = memory_get_peak_usage(true);
		echo convert($m2 - m1);
		echo "<hr>";
		$info1 = B_DB::instance('BaseNpcHero')->all();
		$m3 = memory_get_peak_usage(true);
		echo convert($m3 - $m2);
	}


	public function AHHH($curWeek) {
		$campOpenWeek = array(
			0 => 1,
			1 => 2,
			2 => 4,
			3 => 8,
			4 => 16,
			5 => 32,
			6 => 64,
		);

		$week = 96;

		$n = M_Formula::calcCampOpenNextWeek($week, $curWeek);
		var_dump($n);

	}

	public function AEN() {
		$arr = B_DB::instance('BaseEquipName')->init();
		var_dump($arr);

	}

	public function AGZ() {

		$url = 'http://res.mswar2.com/conf/data/vipsysequiaward.bin';
		var_dump($url);
		$a = file_get_contents($url);
		$b = gzuncompress($a);
		$c = json_decode($b, true);
		var_dump($c);
	}


	public function AWq() {

		$mc_key = AppD_Key::UPG_TECH; //自定义Memcached中存储科技升级数据的键名
		$ret = B_Cache_APC::get($mc_key);
		return $ret;
		//$ret = M_Auth::checkWaitQueue(3, 1);
		//var_dump($ret);
		//echo "<br><br><br><br><br><br><br>";

		$ret = M_Auth::updateWaitQueue();
		var_dump($ret);
		echo "<br><br><br><br><br><br><br>";
		$ret = M_Auth::getWaitQueue();
		var_dump($ret);
		return 1;
	}

	public function AV($a = 1, $b = '') {
		if ($a == 2) {

			print gzcompress($A, 9);
		} else if ($a == 3) {
			print $A;
		} else if ($a == 4) {
			$data = file_get_contents($b);
			$tmp = gzuncompress($data);
			echo "<pre>";
			print_r(json_decode($tmp, true));
			echo "</pre>";

		}


	}

	public function ADB() {
		$d = new DB_AdmUserGroup();
		$d->name = 'Dorm 1';
		$d->save();
	}


	public function ATC($unionPoint = 0, $type = 0) {
		for ($i = 0; $i < 10000; $i++) {
			Logger::debug($i);
		}
		return $i;
		for ($i = 900; $i < 1000; $i++) {
			$a = uniqid('1|');
			B_Crypt::setKey('xinshouka');
			$aaa = B_Crypt::encode($a);
			echo "<hr>-------------{$a}-----------------------<hr>";
			echo "<hr>=================={$aaa}=======================<hr>";
			echo B_Crypt::decode($aaa);
		}
		exit;


		if ($type == 1) {

			//$unionPoint = ceil($unionPoint/1000000);
			for ($i = 0; $i < 100000; $i++) {
				$v = ($type == 1) ? 1 : 2;
				/**
				 * switch ($unionPoint)
				 * {
				 * case $unionPoint > 1:
				 * $v = 2;
				 * break;
				 * case $unionPoint > 2:
				 * $v = 5;
				 * break;
				 * case $unionPoint > 5:
				 * $v = 10;
				 * break;
				 * case $unionPoint > 10:
				 * $v = 20;
				 * break;
				 * case $unionPoint > 20:
				 * $v = 30;
				 * break;
				 * case $unionPoint > 30:
				 * $v = 50;
				 * break;
				 * case $unionPoint > 50:
				 * $v = 70;
				 * break;
				 * case $unionPoint > 70:
				 * $v = 100;
				 * break;
				 * case $unionPoint > 100:
				 * $v = 200;
				 * break;
				 * default:
				 * $v = 1;
				 * break;
				 * }
				 **/

			}
		} else if ($type == 2) {

			//$unionPoint = ceil($unionPoint/1000000);
			for ($i = 0; $i < 100000; $i++) {
				if ($type == 1) {
					$v = 1;

				} else {
					$v = 2;
				}
				/**
				 * $v = 1;
				 * $unionPoint > 1 && $v = 2;
				 * $unionPoint > 2 && $v = 5;
				 * $unionPoint > 5 && $v = 10;
				 * $unionPoint > 10 && $v = 20;
				 * $unionPoint > 20 && $v = 30;
				 * $unionPoint > 30 && $v = 50;
				 * $unionPoint > 50 && $v = 70;
				 * $unionPoint > 70 && $v = 100;
				 * $unionPoint > 100 && $v = 200;
				 **/

			}
		} else if ($type == 3) {
			$unionPoint = ceil($unionPoint / 1000000);
			if ($unionPoint > 100) {

				$v = 200;
			} else if ($unionPoint > 70) {
				$v = 100;
			} else if ($unionPoint > 50) {
				$v = 70;
			} else if ($unionPoint > 30) {
				$v = 50;
			} else if ($unionPoint > 20) {
				$v = 30;
			} else if ($unionPoint > 10) {
				$v = 20;
			} else if ($unionPoint > 5) {
				$v = 10;
			} else if ($unionPoint > 2) {
				$v = 5;
			} else if ($unionPoint > 1) {
				$v = 2;
			} else {
				$v = 1;
			}
		}


		return $v;


		exit;

		$str = '1111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';
		/**
		 * B_Cache_APC::set('a',$str);
		 * $ret = array();
		 * for($i=0;$i<100000;$i++)
		 * {
		 * $c = B_Cache_APC::get('a');
		 * //var_dump($c);
		 * $ret[] = $c;
		 * }
		 *
		 * echo count($ret);
		 **/

		$rc = RC::instance();
		$rc->set('a', $str);
		$ret = array();
		for ($i = 0; $i < 100000; $i++) {
			$c = $rc->get('a');
			//var_dump($c);
			$ret[] = $c;
		}
		echo count($ret);

	}


	public function ACBD() {

		$DATA = '{"atkHero":{"100535":{"id":"100535","city_id":"1","nickname":"\u6797\u5f6a ","gender":"1","quality":"8","level":"34","face_id":"1_1_15","exp":"2450","is_legend":1,"attr_lead":132,"attr_command":245,"attr_military":171,"attr_energy":30,"attr_mood":"0","stat_point":"0","grow_rate":"10.00","equip_arm":"0","equip_cap":"0","equip_uniform":"0","equip_medal":"0","equip_shoes":"0","equip_sit":"0","skill_slot_num":"1","skill_slot":"15","skill_slot_1":"0","skill_slot_2":"0","win_num":"3","draw_num":"0","fail_num":"8","relife_time":"0","fight":"0","flag":"0","weapon_id":"25","army_id":"3","army_num":"680","create_at":"1325153321","fill_flag":"1","sys_is_del":false,"on_sale":false,"skill_lead":12,"skill_command":22,"skill_military":16,"skill_energy":0,"skill_army_num":0,"att_land":132,"att_sky":14,"def_land":79,"def_sky":79,"life_value":264,"total_value":1780,"tech_add":{"A":0,"D":0,"L":0},"props_add":{"A":0,"D":0,"L":0,"HeroExpAdd":0,"ArmyExpAdd":0,"ArmyRelifeAdd":0},"vip_add":{"A":0,"D":15,"L":0},"union_add":{"4":0,"2":0,"1":0,"3":0,"INCR_CRIT":10},"view_range":"4","move_range":"4","shot_range_min":"1","shot_range_max":"4","skill_add":[],"add_effect":"[]","move_type":"2","shot_type":"1","carry":"175","left_num":"680","left_dmg":0,"army_info":{"id":"3","name":"\u88c5\u7532\u5175","life_value":150,"att_land":75,"att_sky":8,"att_ocean":"0","def_land":45,"def_sky":45,"def_ocean":"0"},"weapon_info":{"id":"25","name":"\u53cd\u5766\u514b\u70ae","army_name":"\u81ea\u884c\u53cd\u5766\u514b\u70ae\u90e8\u961f","army_id":"3","march_type":"0","show_type":"1","sort":"8","is_special":"0","is_npc":"0","life_value":"114","att_land":"57","att_sky":"6","att_ocean":"0","def_land":"34","def_sky":"34","def_ocean":"0","speed":"160","move_range":"4","move_type":"2","shot_range_min":"1","shot_range_max":"4","shot_type":"1","view_range":"4","carry":"175"}},"100543":{"id":"100543","city_id":"1","nickname":"\u6797\u5f6a ","gender":"1","quality":"8","level":"34","face_id":"1_1_15","exp":"2226","is_legend":1,"attr_lead":128,"attr_command":222,"attr_military":198,"attr_energy":30,"attr_mood":"0","stat_point":"0","grow_rate":"10.00","equip_arm":"0","equip_cap":"0","equip_uniform":"0","equip_medal":"0","equip_shoes":"0","equip_sit":"0","skill_slot_num":"1","skill_slot":"15","skill_slot_1":"0","skill_slot_2":"0","win_num":"3","draw_num":"0","fail_num":"7","relife_time":"0","fight":"0","flag":"0","weapon_id":"25","army_id":"3","army_num":"680","create_at":"1325154863","fill_flag":"1","sys_is_del":false,"on_sale":false,"skill_lead":12,"skill_command":20,"skill_military":18,"skill_energy":0,"skill_army_num":0,"att_land":132,"att_sky":14,"def_land":79,"def_sky":79,"life_value":264,"total_value":1780,"tech_add":{"A":0,"D":0,"L":0},"props_add":{"A":0,"D":0,"L":0,"HeroExpAdd":0,"ArmyExpAdd":0,"ArmyRelifeAdd":0},"vip_add":{"A":0,"D":15,"L":0},"union_add":{"4":0,"2":0,"1":0,"3":0,"INCR_CRIT":10},"view_range":"4","move_range":"4","shot_range_min":"1","shot_range_max":"4","skill_add":[],"add_effect":"[]","move_type":"2","shot_type":"1","carry":"175","left_num":"680","left_dmg":0,"army_info":{"id":"3","name":"\u88c5\u7532\u5175","life_value":150,"att_land":75,"att_sky":8,"att_ocean":"0","def_land":45,"def_sky":45,"def_ocean":"0"},"weapon_info":{"id":"25","name":"\u53cd\u5766\u514b\u70ae","army_name":"\u81ea\u884c\u53cd\u5766\u514b\u70ae\u90e8\u961f","army_id":"3","march_type":"0","show_type":"1","sort":"8","is_special":"0","is_npc":"0","life_value":"114","att_land":"57","att_sky":"6","att_ocean":"0","def_land":"34","def_sky":"34","def_ocean":"0","speed":"160","move_range":"4","move_type":"2","shot_range_min":"1","shot_range_max":"4","shot_type":"1","view_range":"4","carry":"175"}},"100357":{"id":"100357","city_id":"1","nickname":"\u6797\u5f6a ","gender":"1","quality":"8","level":"33","face_id":"1_1_15","exp":"2040","is_legend":1,"attr_lead":199,"attr_command":246,"attr_military":188,"attr_energy":30,"attr_mood":"0","stat_point":"0","grow_rate":"10.00","equip_arm":"2337","equip_cap":"0","equip_uniform":"2262","equip_medal":"2264","equip_shoes":"0","equip_sit":"2516","skill_slot_num":"1","skill_slot":"2","skill_slot_1":"1","skill_slot_2":"0","win_num":"3","draw_num":"0","fail_num":"8","relife_time":"0","fight":"0","flag":"0","weapon_id":"25","army_id":"3","army_num":"660","create_at":"1323846188","fill_flag":"1","sys_is_del":false,"on_sale":false,"skill_lead":0,"skill_command":0,"skill_military":0,"skill_energy":0,"skill_army_num":0,"att_land":132,"att_sky":14,"def_land":79,"def_sky":79,"life_value":264,"total_value":1780,"tech_add":{"A":0,"D":0,"L":0},"props_add":{"A":0,"D":0,"L":0,"HeroExpAdd":0,"ArmyExpAdd":0,"ArmyRelifeAdd":0},"vip_add":{"A":0,"D":15,"L":0},"union_add":{"4":0,"2":0,"1":0,"3":0,"INCR_CRIT":10},"view_range":"4","move_range":"4","shot_range_min":"1","shot_range_max":"4","skill_add":[],"add_effect":"[]","move_type":"2","shot_type":"1","carry":"175","left_num":"660","left_dmg":0,"army_info":{"id":"3","name":"\u88c5\u7532\u5175","life_value":150,"att_land":75,"att_sky":8,"att_ocean":"0","def_land":45,"def_sky":45,"def_ocean":"0"},"weapon_info":{"id":"25","name":"\u53cd\u5766\u514b\u70ae","army_name":"\u81ea\u884c\u53cd\u5766\u514b\u70ae\u90e8\u961f","army_id":"3","march_type":"0","show_type":"1","sort":"8","is_special":"0","is_npc":"0","life_value":"114","att_land":"57","att_sky":"6","att_ocean":"0","def_land":"34","def_sky":"34","def_ocean":"0","speed":"160","move_range":"4","move_type":"2","shot_range_min":"1","shot_range_max":"4","shot_type":"1","view_range":"4","carry":"175"}},"100359":{"id":"100359","city_id":"1","nickname":"\u6797\u5f6a ","gender":"1","quality":"8","level":"33","face_id":"1_1_15","exp":"2223","is_legend":1,"attr_lead":111,"attr_command":212,"attr_military":169,"attr_energy":30,"attr_mood":"0","stat_point":"0","grow_rate":"10.00","equip_arm":"0","equip_cap":"0","equip_uniform":"0","equip_medal":"0","equip_shoes":"0","equip_sit":"0","skill_slot_num":"1","skill_slot":"2","skill_slot_1":"20","skill_slot_2":"0","win_num":"5","draw_num":"0","fail_num":"10","relife_time":"0","fight":"0","flag":"0","weapon_id":"25","army_id":"3","army_num":"660","create_at":"1323849707","fill_flag":"1","sys_is_del":false,"on_sale":false,"skill_lead":0,"skill_command":0,"skill_military":0,"skill_energy":0,"skill_army_num":0,"att_land":132,"att_sky":14,"def_land":79,"def_sky":79,"life_value":264,"total_value":1780,"tech_add":{"A":0,"D":0,"L":0},"props_add":{"A":0,"D":0,"L":0,"HeroExpAdd":0,"ArmyExpAdd":0,"ArmyRelifeAdd":0},"vip_add":{"A":0,"D":15,"L":0},"union_add":{"4":0,"2":0,"1":0,"3":0,"INCR_CRIT":10},"view_range":"4","move_range":"4","shot_range_min":"1","shot_range_max":"4","skill_add":[],"add_effect":"[]","move_type":"2","shot_type":"1","carry":"175","left_num":"660","left_dmg":0,"army_info":{"id":"3","name":"\u88c5\u7532\u5175","life_value":150,"att_land":75,"att_sky":8,"att_ocean":"0","def_land":45,"def_sky":45,"def_ocean":"0"},"weapon_info":{"id":"25","name":"\u53cd\u5766\u514b\u70ae","army_name":"\u81ea\u884c\u53cd\u5766\u514b\u70ae\u90e8\u961f","army_id":"3","march_type":"0","show_type":"1","sort":"8","is_special":"0","is_npc":"0","life_value":"114","att_land":"57","att_sky":"6","att_ocean":"0","def_land":"34","def_sky":"34","def_ocean":"0","speed":"160","move_range":"4","move_type":"2","shot_range_min":"1","shot_range_max":"4","shot_type":"1","view_range":"4","carry":"175"}},"100545":{"id":"100545","city_id":"1","nickname":"\u6797\u5f6a ","gender":"1","quality":"8","level":"33","face_id":"1_1_15","exp":"1824","is_legend":1,"attr_lead":110,"attr_command":321,"attr_military":213,"attr_energy":30,"attr_mood":"0","stat_point":"0","grow_rate":"10.00","equip_arm":"2297","equip_cap":"0","equip_uniform":"0","equip_medal":"0","equip_shoes":"0","equip_sit":"2529","skill_slot_num":"1","skill_slot":"15","skill_slot_1":"6","skill_slot_2":"0","win_num":"2","draw_num":"0","fail_num":"7","relife_time":"0","fight":"0","flag":"0","weapon_id":"25","army_id":"3","army_num":"660","create_at":"1325155091","fill_flag":"1","sys_is_del":false,"on_sale":false,"skill_lead":10,"skill_command":20,"skill_military":19,"skill_energy":0,"skill_army_num":0,"att_land":132,"att_sky":14,"def_land":79,"def_sky":79,"life_value":264,"total_value":1780,"tech_add":{"A":0,"D":0,"L":0},"props_add":{"A":0,"D":0,"L":0,"HeroExpAdd":0,"ArmyExpAdd":0,"ArmyRelifeAdd":0},"vip_add":{"A":0,"D":15,"L":0},"union_add":{"4":0,"2":0,"1":0,"3":0,"INCR_CRIT":10},"view_range":"4","move_range":"4","shot_range_min":"1","shot_range_max":"4","skill_add":[],"add_effect":"[]","move_type":"2","shot_type":"1","carry":"175","left_num":"660","left_dmg":0,"army_info":{"id":"3","name":"\u88c5\u7532\u5175","life_value":150,"att_land":75,"att_sky":8,"att_ocean":"0","def_land":45,"def_sky":45,"def_ocean":"0"},"weapon_info":{"id":"25","name":"\u53cd\u5766\u514b\u70ae","army_name":"\u81ea\u884c\u53cd\u5766\u514b\u70ae\u90e8\u961f","army_id":"3","march_type":"0","show_type":"1","sort":"8","is_special":"0","is_npc":"0","life_value":"114","att_land":"57","att_sky":"6","att_ocean":"0","def_land":"34","def_sky":"34","def_ocean":"0","speed":"160","move_range":"4","move_type":"2","shot_range_min":"1","shot_range_max":"4","shot_type":"1","view_range":"4","carry":"175"}}},"atkData":["1",0,["2","154","149"],1,5,"\u53d1\u6492\u65e6\u6cd5","3","2"],"defHero":{"825":{"id":"825","nickname":"\u76ae\u8036\u7f574","gender":"1","quality":"3","level":"32","face_id":"","is_legend":0,"attr_lead":"72","attr_command":"70","attr_military":"43","attr_energy":"30","attr_mood":"1","equip_arm":"90","equip_cap":"22","equip_uniform":"56","equip_medal":"124","equip_shoes":"158","equip_sit":"192","skill_slot_num":"0","skill_slot":"","skill_slot_1":"","skill_slot_2":"","army_id":"1","army_lv":"1","army_num":"1942","weapon_id":"50","create_at":"1320227346","skill_lead":0,"skill_command":0,"skill_military":0,"skill_energy":0,"skill_army_num":0,"att_land":26,"att_sky":7,"def_land":20,"def_sky":20,"life_value":59,"total_value":193,"tech_add":{"A":0,"D":0,"L":0},"props_add":{"A":0,"D":0,"L":0,"HeroExpAdd":0,"ArmyExpAdd":0,"ArmyRelifeAdd":0},"vip_add":{"A":0,"D":0,"L":0},"union_add":{"4":0,"2":0,"1":0,"3":0,"INCR_CRIT":0},"view_range":"2","move_range":"2","shot_range_min":"1","shot_range_max":"2","skill_add":[],"add_effect":"[]","move_type":"1","shot_type":"1","carry":"10","left_num":"1942","left_dmg":0,"army_info":{"id":"1","name":"\u6b65\u5175","life_value":22,"att_land":9,"att_sky":2,"att_ocean":"0","def_land":7,"def_sky":7,"def_ocean":"0"},"weapon_info":{"id":"50","name":"\u5e03\u96f7\u8fbe\u91cd\u673a\u67aa","army_name":"\u91cd\u673a\u67aa\u90e8\u961f","army_id":"1","march_type":"0","show_type":"1","sort":"200","is_special":"0","is_npc":"1","life_value":"37","att_land":"17","att_sky":"5","att_ocean":"0","def_land":"13","def_sky":"13","def_ocean":"0","speed":"90","move_range":"2","move_type":"1","shot_range_min":"1","shot_range_max":"2","shot_type":"1","view_range":"2","carry":"10"}},"826":{"id":"826","nickname":"\u6606\u62585","gender":"1","quality":"2","level":"32","face_id":"","is_legend":0,"attr_lead":"55","attr_command":"48","attr_military":"58","attr_energy":"30","attr_mood":"1","equip_arm":"81","equip_cap":"13","equip_uniform":"47","equip_medal":"115","equip_shoes":"149","equip_sit":"183","skill_slot_num":"0","skill_slot":"","skill_slot_1":"","skill_slot_2":"","army_id":"3","army_lv":"1","army_num":"257","weapon_id":"51","create_at":"1320227350","skill_lead":0,"skill_command":0,"skill_military":0,"skill_energy":0,"skill_army_num":0,"att_land":135,"att_sky":14,"def_land":81,"def_sky":81,"life_value":269,"total_value":1216,"tech_add":{"A":0,"D":0,"L":0},"props_add":{"A":0,"D":0,"L":0,"HeroExpAdd":0,"ArmyExpAdd":0,"ArmyRelifeAdd":0},"vip_add":{"A":0,"D":0,"L":0},"union_add":{"4":0,"2":0,"1":0,"3":0,"INCR_CRIT":0},"view_range":"4","move_range":"3","shot_range_min":"1","shot_range_max":"3","skill_add":[],"add_effect":"[]","move_type":"2","shot_type":"1","carry":"150","left_num":"257","left_dmg":0,"army_info":{"id":"3","name":"\u88c5\u7532\u5175","life_value":110,"att_land":55,"att_sky":6,"att_ocean":"0","def_land":33,"def_sky":33,"def_ocean":"0"},"weapon_info":{"id":"51","name":"CV35\u8f7b\u5766\u514b","army_name":"\u8f7b\u578b\u5766\u514b\u90e8\u961f","army_id":"3","march_type":"0","show_type":"1","sort":"200","is_special":"0","is_npc":"1","life_value":"159","att_land":"80","att_sky":"8","att_ocean":"0","def_land":"48","def_sky":"48","def_ocean":"0","speed":"180","move_range":"3","move_type":"2","shot_range_min":"1","shot_range_max":"3","shot_type":"1","view_range":"4","carry":"150"}},"827":{"id":"827","nickname":"\u594e\u91cc\u8bfa5","gender":"1","quality":"2","level":"32","face_id":"","is_legend":0,"attr_lead":"81","attr_command":"61","attr_military":"19","attr_energy":"30","attr_mood":"1","equip_arm":"81","equip_cap":"13","equip_uniform":"47","equip_medal":"115","equip_shoes":"149","equip_sit":"183","skill_slot_num":"0","skill_slot":"","skill_slot_1":"","skill_slot_2":"","army_id":"2","army_lv":"1","army_num":"202","weapon_id":"52","create_at":"1320227354","skill_lead":0,"skill_command":0,"skill_military":0,"skill_energy":0,"skill_army_num":0,"att_land":162,"att_sky":18,"def_land":41,"def_sky":9,"life_value":215,"total_value":1557,"tech_add":{"A":0,"D":0,"L":0},"props_add":{"A":0,"D":0,"L":0,"HeroExpAdd":0,"ArmyExpAdd":0,"ArmyRelifeAdd":0},"vip_add":{"A":0,"D":0,"L":0},"union_add":{"4":0,"2":0,"1":0,"3":0,"INCR_CRIT":0},"view_range":"3","move_range":"2","shot_range_min":"3","shot_range_max":"5","skill_add":[],"add_effect":"[]","move_type":"1","shot_type":"1","carry":"140","left_num":"202","left_dmg":0,"army_info":{"id":"2","name":"\u70ae\u5175","life_value":88,"att_land":66,"att_sky":7,"att_ocean":"0","def_land":17,"def_sky":3,"def_ocean":"0"},"weapon_info":{"id":"52","name":"\u5e03\u96f7\u897f\u4e9a\u8feb\u51fb\u70ae","army_name":"\u8feb\u51fb\u70ae\u90e8\u961f","army_id":"2","march_type":"0","show_type":"1","sort":"200","is_special":"0","is_npc":"1","life_value":"127","att_land":"96","att_sky":"11","att_ocean":"0","def_land":"24","def_sky":"6","def_ocean":"0","speed":"140","move_range":"2","move_type":"1","shot_range_min":"3","shot_range_max":"5","shot_type":"1","view_range":"3","carry":"140"}},"828":{"id":"828","nickname":"\u62c9\u6cd5\u57c3\u6d1b5","gender":"1","quality":"2","level":"32","face_id":"","is_legend":0,"attr_lead":"42","attr_command":"79","attr_military":"40","attr_energy":"30","attr_mood":"1","equip_arm":"81","equip_cap":"13","equip_uniform":"47","equip_medal":"115","equip_shoes":"149","equip_sit":"183","skill_slot_num":"0","skill_slot":"","skill_slot_1":"","skill_slot_2":"","army_id":"4","army_lv":"1","army_num":"147","weapon_id":"54","create_at":"1320227357","skill_lead":0,"skill_command":0,"skill_military":0,"skill_energy":0,"skill_army_num":0,"att_land":40,"att_sky":95,"def_land":41,"def_sky":41,"life_value":135,"total_value":2123,"tech_add":{"A":0,"D":0,"L":0},"props_add":{"A":0,"D":0,"L":0,"HeroExpAdd":0,"ArmyExpAdd":0,"ArmyRelifeAdd":0},"vip_add":{"A":0,"D":0,"L":0},"union_add":{"4":0,"2":0,"1":0,"3":0,"INCR_CRIT":0},"view_range":"5","move_range":"5","shot_range_min":"1","shot_range_max":"4","skill_add":[],"add_effect":"[]","move_type":"3","shot_type":"1","carry":"200","left_num":"147","left_dmg":0,"army_info":{"id":"4","name":"\u822a\u7a7a\u5175","life_value":55,"att_land":10,"att_sky":39,"att_ocean":"0","def_land":17,"def_sky":17,"def_ocean":"0"},"weapon_info":{"id":"54","name":"RO-37\u6218\u6597\u673a","army_name":"\u6218\u6597\u673a\u90e8\u961f","army_id":"4","march_type":"0","show_type":"1","sort":"200","is_special":"0","is_npc":"1","life_value":"80","att_land":"30","att_sky":"56","att_ocean":"0","def_land":"24","def_sky":"24","def_ocean":"0","speed":"440","move_range":"5","move_type":"3","shot_range_min":"1","shot_range_max":"4","shot_type":"1","view_range":"5","carry":"200"}},"829":{"id":"829","nickname":"\u91cc\u7eb3\u5c14\u591a5","gender":"1","quality":"1","level":"32","face_id":"","is_legend":0,"attr_lead":"62","attr_command":"38","attr_military":"23","attr_energy":"30","attr_mood":"1","equip_arm":"71","equip_cap":"3","equip_uniform":"37","equip_medal":"105","equip_shoes":"139","equip_sit":"173","skill_slot_num":"0","skill_slot":"","skill_slot_1":"","skill_slot_2":"","army_id":"2","army_lv":"1","army_num":"202","weapon_id":"53","create_at":"1320227361","skill_lead":0,"skill_command":0,"skill_military":0,"skill_energy":0,"skill_army_num":0,"att_land":75,"att_sky":125,"def_land":22,"def_sky":24,"life_value":200,"total_value":1557,"tech_add":{"A":0,"D":0,"L":0},"props_add":{"A":0,"D":0,"L":0,"HeroExpAdd":0,"ArmyExpAdd":0,"ArmyRelifeAdd":0},"vip_add":{"A":0,"D":0,"L":0},"union_add":{"4":0,"2":0,"1":0,"3":0,"INCR_CRIT":0},"view_range":"3","move_range":"2","shot_range_min":"3","shot_range_max":"6","skill_add":[],"add_effect":"[]","move_type":"2","shot_type":"1","carry":"140","left_num":"202","left_dmg":0,"army_info":{"id":"2","name":"\u70ae\u5175","life_value":88,"att_land":66,"att_sky":7,"att_ocean":"0","def_land":17,"def_sky":3,"def_ocean":"0"},"weapon_info":{"id":"53","name":"\u5361\u8bfa\u9ad8\u5c04\u70ae","army_name":"\u9ad8\u5c04\u70ae\u90e8\u961f","army_id":"2","march_type":"0","show_type":"1","sort":"200","is_special":"0","is_npc":"1","life_value":"112","att_land":"9","att_sky":"118","att_ocean":"0","def_land":"5","def_sky":"21","def_ocean":"0","speed":"140","move_range":"2","move_type":"2","shot_range_min":"3","shot_range_max":"6","shot_type":"1","view_range":"3","carry":"140"}}},"defData":[0,"170",["2","8","4"],1,0,"\u76ae\u8036\u7f57\u90e84","",0],"mapData":{"mapNo":"402","mapSize":["42","10"],"mapCell":"{\"0_1\":[3],\"1_0\":[3],\"1_5\":[1,\"100545\"],\"2_0\":[3],\"2_1\":[3],\"2_4\":[1,\"100543\"],\"3_0\":[3],\"3_1\":[5],\"3_3\":[5],\"4_0\":[3],\"4_1\":[3],\"4_5\":[1,\"100357\"],\"4_7\":[5],\"5_0\":[3],\"5_1\":[3],\"5_2\":[1,\"100359\"],\"6_0\":[3],\"6_1\":[3],\"6_2\":[3],\"6_4\":[1,\"100535\"],\"7_0\":[3],\"7_1\":[3],\"8_5\":[5],\"9_0\":[5],\"10_9\":[5],\"11_1\":[3],\"11_7\":[16],\"12_1\":[5],\"12_2\":[3],\"13_1\":[3],\"15_3\":[5],\"18_5\":[5],\"19_0\":[3],\"19_8\":[16],\"20_0\":[3],\"20_1\":[5],\"21_0\":[3],\"22_0\":[3],\"24_1\":[3],\"24_2\":[16],\"25_0\":[3],\"26_1\":[3],\"28_6\":[5],\"28_8\":[5],\"29_5\":[16],\"29_6\":[5],\"30_1\":[16],\"30_3\":[16],\"30_7\":[16],\"31_0\":[5],\"31_2\":[5],\"31_6\":[16],\"32_2\":[16],\"32_5\":[16],\"32_6\":[16],\"33_1\":[5],\"33_2\":[16],\"33_7\":[16],\"34_2\":[16],\"34_8\":[16],\"35_1\":[5],\"35_2\":[16],\"35_6\":[2,\"828\"],\"35_7\":[16],\"36_4\":[2,\"825\"],\"36_8\":[5],\"37_1\":[16],\"38_1\":[5],\"39_4\":[2,\"827\"],\"39_6\":[2,\"826\"],\"40_3\":[16],\"40_5\":[2,\"829\"],\"41_2\":[5]}","mapSecne":"[[\"new2_road12\",8,447,1],[\"new2_road12\",198,446,1],[\"new2_road12\",387,447,1],[\"new2_road12\",581,448,1],[\"new2_road12\",764,448,1],[\"new2_road12\",951,447,1],[\"new2_road12\",1139,446,1],[\"new2_road12\",1328,444,1],[\"new2_road12\",1518,443,1],[\"new2_road12\",1707,442,1],[\"new2_road12\",1905,440,1],[\"new2_road12\",2095,442,1],[\"new2_road12\",2175,443,1],[\"new2_house1\",1947,271,1],[\"new2_house1\",1842,277,1],[\"new2_house1\",1883,210,1],[\"new2_house1\",1767,216,1],[\"new2_house1\",2049,164,1],[\"new2_house1\",2222,333,1],[\"new2_house1\",1663,337,1],[\"new2_house1\",1673,112,1],[\"new2_house5\",1763,547,1],[\"new2_house5\",1777,652,1],[\"new2_house5\",1710,711,1],[\"new2_house5\",1599,604,1],[\"new2_house2\",1861,851,1],[\"new2_house5\",1648,767,1],[\"new1_coco6\",1856,97,1],[\"new1_coco6\",1929,104,1],[\"new1_coco6\",2122,46,1],[\"new1_coco6\",2288,209,1],[\"new1_coco6\",1705,220,1],[\"new1_coco1\",1733,15,1],[\"new1_coco6\",1540,593,1],[\"new1_coco6\",1637,664,1],[\"new1_coco1\",1562,821,1],[\"new1_coco1\",2017,841,1],[\"new2_tree1\",1016,549,1],[\"new2_tree1\",844,383,1],[\"new2_tree1\",459,546,1],[\"new2_tree1\",176,376,1],[\"new2_hill3\",-12,4,1],[\"new2_hill3\",197,8,1],[\"new2_hill1\",267,148,1],[\"new2_hill1\",614.5,139,1],[\"new2_hill3\",1014,-31,1],[\"new2_tree9\",181,154,1],[\"new2_tree9\",1110,93,1],[\"new2_ground1\",753,-4,1],[\"new2_ground1\",508,-21,1],[\"new2_ground1\",633,-4,1],[\"new2_ground1\",857,-8,1],[\"new2_ground1\",493,54,1],[\"new2_tree9\",482,24,1],[\"new2_tree9\",664,70,1],[\"new2_ground6\",1954,535,1],[\"new2_ground4\",1451,358,1],[\"new2_ground3\",1212,537,1],[\"new2_ground9\",8,733,1],[\"new2_ground8\",63,846,1],[\"new2_ground9\",450,689,1],[\"new2_ground8\",319,973,1],[\"new2_ground8\",485,815,1],[\"new2_ground8\",592,917,1],[\"new2_ground8\",810,736,1],[\"new2_ground8\",824,852,1],[\"new2_ground9\",1142,947,1],[\"new2_ground9\",1091,736,1],[\"new2_ground8\",790,1015,1],[\"new2_ground9\",992,911,1],[\"new2_ground9\",3,999,1],[\"new2_ground9\",316,807,1],[\"new2_b_car1\",608,836,1],[\"new2_ground8\",1148,819,1],[\"new2_ground8\",980,1034,1],[\"new2_b_car2\",1055,952,1],[\"new2_ground9\",711,721,1],[\"new2_tree5\",212,756,1],[\"new2_tree3\",567,967,1],[\"new2_ground4\",1062,257,1],[\"new2_house1\",1332,236,1],[\"new2_hill11\",1351,86,1]]","mapBgNo":"29","mapName":"\u65b0\u9ed1\u72ee\u7684\u6124\u6012\u4e0a02","defHeroPos":{"825":"36_4","826":"39_6","827":"39_4","828":"35_6","829":"40_5"},"atkHeroPos":{"100535":"6_4","100543":"2_4","100357":"4_5","100359":"5_2","100545":"1_5"}},"weather":1,"battleType":5,"army_data":[{"id":"3","name":"\u88c5\u7532\u5175","life_value":110,"att_land":55,"att_sky":6,"att_ocean":"0","def_land":33,"def_sky":33,"def_ocean":"0"},{"id":"1","name":"\u6b65\u5175","life_value":22,"att_land":9,"att_sky":2,"att_ocean":"0","def_land":7,"def_sky":7,"def_ocean":"0"},{"id":"2","name":"\u70ae\u5175","life_value":88,"att_land":66,"att_sky":7,"att_ocean":"0","def_land":17,"def_sky":3,"def_ocean":"0"},{"id":"4","name":"\u822a\u7a7a\u5175","life_value":55,"att_land":10,"att_sky":39,"att_ocean":"0","def_land":17,"def_sky":17,"def_ocean":"0"}],"weapon_data":[{"id":"25","name":"\u53cd\u5766\u514b\u70ae","army_name":"\u81ea\u884c\u53cd\u5766\u514b\u70ae\u90e8\u961f","army_id":"3","march_type":"0","show_type":"1","sort":"8","is_special":"0","is_npc":"0","life_value":"114","att_land":"57","att_sky":"6","att_ocean":"0","def_land":"34","def_sky":"34","def_ocean":"0","speed":"160","move_range":"4","move_type":"2","shot_range_min":"1","shot_range_max":"4","shot_type":"1","view_range":"4","carry":"175"},{"id":"50","name":"\u5e03\u96f7\u8fbe\u91cd\u673a\u67aa","army_name":"\u91cd\u673a\u67aa\u90e8\u961f","army_id":"1","march_type":"0","show_type":"1","sort":"200","is_special":"0","is_npc":"1","life_value":"37","att_land":"17","att_sky":"5","att_ocean":"0","def_land":"13","def_sky":"13","def_ocean":"0","speed":"90","move_range":"2","move_type":"1","shot_range_min":"1","shot_range_max":"2","shot_type":"1","view_range":"2","carry":"10"},{"id":"51","name":"CV35\u8f7b\u5766\u514b","army_name":"\u8f7b\u578b\u5766\u514b\u90e8\u961f","army_id":"3","march_type":"0","show_type":"1","sort":"200","is_special":"0","is_npc":"1","life_value":"159","att_land":"80","att_sky":"8","att_ocean":"0","def_land":"48","def_sky":"48","def_ocean":"0","speed":"180","move_range":"3","move_type":"2","shot_range_min":"1","shot_range_max":"3","shot_type":"1","view_range":"4","carry":"150"},{"id":"52","name":"\u5e03\u96f7\u897f\u4e9a\u8feb\u51fb\u70ae","army_name":"\u8feb\u51fb\u70ae\u90e8\u961f","army_id":"2","march_type":"0","show_type":"1","sort":"200","is_special":"0","is_npc":"1","life_value":"127","att_land":"96","att_sky":"11","att_ocean":"0","def_land":"24","def_sky":"6","def_ocean":"0","speed":"140","move_range":"2","move_type":"1","shot_range_min":"3","shot_range_max":"5","shot_type":"1","view_range":"3","carry":"140"},{"id":"54","name":"RO-37\u6218\u6597\u673a","army_name":"\u6218\u6597\u673a\u90e8\u961f","army_id":"4","march_type":"0","show_type":"1","sort":"200","is_special":"0","is_npc":"1","life_value":"80","att_land":"30","att_sky":"56","att_ocean":"0","def_land":"24","def_sky":"24","def_ocean":"0","speed":"440","move_range":"5","move_type":"3","shot_range_min":"1","shot_range_max":"4","shot_type":"1","view_range":"5","carry":"200"},{"id":"53","name":"\u5361\u8bfa\u9ad8\u5c04\u70ae","army_name":"\u9ad8\u5c04\u70ae\u90e8\u961f","army_id":"2","march_type":"0","show_type":"1","sort":"200","is_special":"0","is_npc":"1","life_value":"112","att_land":"9","att_sky":"118","att_ocean":"0","def_land":"5","def_sky":"21","def_ocean":"0","speed":"140","move_range":"2","move_type":"2","shot_range_min":"3","shot_range_max":"6","shot_type":"1","view_range":"3","carry":"140"}]}';
		$arr = array();

		for ($i = 1; $i < 100; $i++) {

			$bData = json_decode($DATA, true);
			$battleId = M_War::insertWarBattle($bData, 0);
			$arr[$battleId] = $i;

		}
		//var_dump($arr);

		$w = new War_BattleQueue(M_War::BATTLE_TYPE_FB);
		$a1 = $w->get();
		var_dump($a1);
		var_dump(count($a1));


	}


	public function AScout() {
		$marchInfo = array(
			'id' => '1200',
			'def_city_id' => '1',
			'def_nickname' => '发撒旦法',
			'att_city_id' => '2',
			'att_nickname' => '卓依婷',
			'action_type' => '2',
			'hero_list' => '["100061"]',
			'def_pos' => '["2","154","149"]',
			'att_pos' => '["2","267","75"]',
			'arrived_time' => '1319591583',
			'award' => '',
			'auto_fight' => '1',
			'flag' => '0',
			'battle_id' => '0',
			'create_at' => '1319591523',
			'update_at' => '',
		);
		$a = M_March_Action::scout($marchInfo);
		var_dump($a);
	}


	public function AMR($zone, $terrain) {

		$upInfo = array('last_fb_no' => 1111111112222);
		//同步到前端
		M_Sync::addQueue($atkCityId, M_Sync::KEY_CITY_INFO, $upInfo);
		$upInfo = array('cur_people' => 2222222222222);
		//同步到前端
		M_Sync::addQueue($atkCityId, M_Sync::KEY_CITY_INFO, $upInfo);
		$upInfo = array('energy' => 2332);
		//同步到前端
		M_Sync::addQueue($atkCityId, M_Sync::KEY_CITY_INFO, $upInfo);

		exit;
		$mapData = M_War::getMapNoByZone(3, 4);
		var_dump($mapData);
		exit;


	}

	public function AC1() {
		$exp = '{"value":12,"atkHero":{"110":{"id":110,"exp":30,"flag":0}},"defHero":{"66":{"id":66,"exp":5,"flag":0},"67":{"id":67,"exp":3,"flag":0},"68":{"id":68,"exp":2,"flag":0},"69":{"id":69,"exp":8,"flag":0}},"atkArmy":{"1":12},"defArmy":{"1":1,"3":3}}';
		$expArr = json_decode($exp, true);
		var_dump($expArr);
		$ret = M_Hero::setHeroExpAndFlag('1058', $expArr['atkHero']);
		var_dump($ret);

		exit;
		$RET = M_War::delCityBattleData(1058, 52);
		var_dump($RET);

	}

	static public function ABQ1($id) {
		$ret = War_BattleHandle::initData($id);
		var_dump($ret);
	}

	public function Adel($id) {
		$ret = B_DB::instance('WarBattle')->delete($id);
		var_dump($ret);
	}


	public function ARedis() {
		$rc = RC::instance();
		$start = microtime(true);

		$arr = RC::keys('CITY_INFO_' . '*');
		var_dump($arr);
		foreach ($arr as $val) {
			var_dump($val);
			RC::delete($val);
		}

		exit;

		$arr = RC::keys('CITY_INFO_*');
		var_dump($arr);
		foreach ($arr as $val) {
			var_dump($val);
			RC::delete($val);
		}
		$arr = RC::keys(AppD_Key::CITY_EQUIP_INFO . '*');
		var_dump($arr);
		foreach ($arr as $val) {
			var_dump($val);
			RC::delete($val);
		}

		exit;
		for ($i = 1; $i <= 3000; $i++) {
			$rc->sAdd('Test', 'Test' . $i);
		}
		$end = microtime(true);
		$diff = $end - $start;
		var_dump($diff);

		$list = $rc->sMembers('Test');
		foreach ($list as $val) {
			echo $val . "\n";
		}
		$end1 = microtime(true);
		$diff1 = $end1 - $end;
		var_dump($diff1);


	}

	public function ARedis1() {
		// create the Redis 'mystore' instance
		$redis = Redis::instance('mystore');

		// create some test data
		for ($i = 1; $i <= 10; $i++) {
			$c = $redis->publish('room:chatty', "Hello there!" . $i);
			var_dump($c);
		}

		//$a = $redis->subscribe('test1');
		//var_dump($a);

	}


	public function AFB() {
		$fbData = M_War::getFBInfo(1, 1, 1);
		list($pName, $pTerrain, $pWether, $warMapNo, $npcId, $cgNo, $dialog) = $fbData;
		$npcRow = M_NPC::getInfo($npcId);
		var_dump($npcRow);
	}

	public function AProc() {
		$arr = array();

		echo ($arr === false) ? 1 : 0;


		exit;
		$cityId = 1;
		$ret = M_War::hasFBInCityBattleData($cityId);
		var_dump($ret);
		return $ret;

		for ($i = 200; $i < 300; $i++) {
			War_Battle::initData($i);
			$ret = War_Battle::addQueue($i);
			var_dump($ret);
		}
		return true;

		for ($i = 1; $i < 5000; $i++) {
			if ($i < time()) {
				for ($k = 0; $k < 10; $k++) {
					$a = time();
				}
				echo $a;
			}
		}
	}


	public function AMsg() {
		$MSGKey = "123456";
		$seg = msg_get_queue($MSGKey);
		$a = 0;
		for ($i = 0; $i < 10000; $i++) {
			$ret = msg_send($seg, 1, rand(1, 100000));
			$ret && $a++;
		}
		var_dump($a);
	}


	public function ACalcRate() {
		$wp = new War_Path(array(60, 12), array());
		$data = $wp->getRangeRateList();
		var_export($data);


		//print_r($data);
	}

	public function AUpExp() {
		$data = '{"value":1000,"atk":{"92":{"id":92,"exp":110,"flag":1},"2":{"id":2,"exp":220,"flag":1}},"def":{"19":{"id":19,"exp":142,"flag":3},"20":{"id":20,"exp":201,"flag":3},"17":{"id":17,"exp":106,"flag":3},"18":{"id":18,"exp":142,"flag":0},"21":{"id":21,"exp":112,"flag":3}}}';
		$arr = json_decode($data, true);
		$atkCityId = 1;
		$ret = M_Hero::setHeroExpAndFlag($atkCityId, $arr['atk']);
		var_dump($ret);

	}

	public function AD($sPos = '', $ePos = '') {
		$x = M_Formula::aiCalcDistance($sPos, $ePos);
		var_dump($x);
		/**
		 * $BD = War_Battle::getBattleData(152);
		 * $wp = new War_Path($BD['MapSize'], $BD['MapCell']);
		 * $atkPos = '19_8';
		 * $atkMoveNum = 5;
		 * $atkMoveType = 1;
		 * $atkRange = array(3, 7);
		 * $defPos = '15_8';
		 * $wp->getMoveMaxMove($atkPos, $atkMoveNum, $atkMoveType, $atkRange, $defPos);
		 *
		 * exit;
		 **/


	}

	public function ACM($moveType, $cellId) {
		$info = M_MapWild::checkBanHold($moveType, $cellId);
		var_dump($info);
		exit;
	}

	public function AN($name) {


		$AA = Config::pay($name);
		var_dump($AA);
		exit;


	}


	public function AM($bid, $heroId, $ePos) {
		define('MAP_COLS', 30);
		define('MAP_ROWS', 30);

		define('BLOCK', 0);
		define('ROAD', 1);
		$sTime = microtime(true);
		$sMem = memory_get_usage(); // 36640

		$BD = War_Battle::getBattleData($bid);
		$fp = new War_Path($BD['MapSize'], $BD['MapCell']);
		$blockArr = $BD['MapCell'];
		//获取当前操作方
		$curOp = 1;
		list($heroPos, $heroOpFlag) = $BD[$curOp]['HeroPosData'][$heroId];

		$heroInfo = $BD[$curOp]['HeroDataList'][$heroId];
		$sPos = $heroPos;

		$r = explode('_', $ePos);

		//$rangeList = $fp->getAtkRange($sPos, $r);

		$posList = $fp->getMoveRange($heroInfo['move_type'], $heroPos, $heroInfo['move_range']);


		$moveList = $fp->getMovePath($ePos, $posList);

		$eMem = memory_get_usage();
		$roadArr = $posList;

		$eTime = microtime(true);

		//var_dump($moveList);


		$diffTime = $eTime - $sTime;
		$diffMem = $eMem - $sMem;
		//echo convert($diffMem / 1024)."<br>";

		list($sx, $sy) = explode('_', $sPos);


		$block = '';
		$road = '';
		if (is_array($posList)) {
			foreach ($posList as $k => $v) {
				$val = explode('_', $k);
				$road .= "<item>{$val[0]},{$val[1]}</item>";
			}
		}


		if (is_array($roadArr)) {
			foreach ($roadArr as $key => $v) {

				$val = explode('_', $key);
				$block .= "<item>{$val[0]},{$val[1]}," . $v[0] . ":" . $v[1] . "</item>";
			}
		}

		if (is_array($blockArr)) {
			foreach ($blockArr as $key => $v) {
				$val = explode('_', $key);
				//$block .= "<item>{$val[0]},{$val[1]},{$v[0]}</item>";
			}
		}

		if (is_array($moveList)) {
			foreach ($moveList as $key) {
				$val = explode('_', $key);
				$road .= "<item>{$val[0]},{$val[1]},{$v[0]}</item>";
			}
		}


		$ex = rand(4, 9);
		$ey = rand(1, 4);

		$maxX = MAP_COLS;
		$maxY = MAP_ROWS;
		//exit;
		$txt = '';
		//header('Content-Type:text/xml');
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		echo '<data>';
		echo "<base x='{$maxX}' y='{$maxY}'></base><start x='$sx' y='$sy'></start><end x='$ex' y='$ey'></end>";
		echo "<block>$block</block>";
		echo "<road>$road</road>";
		echo "<txt><![CDATA[$txt]]></txt>";
		echo '</data>';
		exit;

	}


	public function APath() {
		$ret = War_AI::run('152', War_Battle::CUR_OP_DEF, 18);
		var_dump($ret);

	}

	public function ABQ($battleId) {
		War_Battle::addQueue($battleId);
	}


	public function ABattleAtk() {
		//11,11
		$x = 11;
		$y = 10;
		$maxX = 20;
		$maxY = 20;

		if ($_GET['type'] == 1) {
			$info = War_Battle::getMoveRange($x, $y, $_GET['offset'], $maxX, $maxY);
		} else {
			$info = War_Battle::getAtkRange($x, $y, $_GET['offset'], $_GET['num'], $maxX, $maxY);
		}

		$road = '';
		foreach ($info as $val) {
			$road .= "<item>$val[0],$val[1]</item>";
		}

		$ex = 1;
		$ey = 1;

		$block = '';
		header('Content-Type:text/xml');
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		echo '<data>';
		echo "<base x='{$maxX}' y='{$maxY}'></base><start x='$x' y='$y'></start><end x='$ex' y='$ey'></end>";
		echo "<block>$block</block>";
		echo "<road>$road</road>";
		echo "<txt><![CDATA[]]></txt>";
		echo '</data>';
		exit;
		/**
		 * $arr = json_decode($pos);
		 * $arr1 = json_decode($pos1);
		 * var_dump(count($arr)) ;
		 * var_dump(count($arr1)) ;
		 * //print_r($arr);
		 * $atkCityId = 70;
		 * $atkHeroId = 11;
		 * $defCityId = 70;
		 * $defHeroId = 12;
		 * $terrianId = 1;
		 * $moodId= 1;
		 * $num = War_Battle::calcDmgAtk($atkCityId, $atkHeroId, $defCityId, $defHeroId, $terrianId, $moodId);
		 * var_dump($num);
		 * $num = War_Battle::calcDmgDef($atkCityId, $atkHeroId, $defCityId, $defHeroId, $terrianId, $moodId);
		 * var_dump($num);
		 **/
	}


	public function AWarMap() {
		$attHeroList = array(1, 2, 3, 4, 5);
		$defHeroList = array(11, 21, 31, 41, 51);
		$warMapNo = 21;
		$ret = M_War::buildWarMapData($attHeroList, $defHeroList, $warMapNo);
		return $ret;

	}


	public function ADice() {
		$rateArr = array('a' => 10, 'b' => 10, 'c' => 30, 'd' => 50);

		$count = array('a' => 0, 'b' => 0, 'c' => 0, 'd' => 0);
		for ($i = 0; $i < 100; $i++) {
			$a = M_Formula::dice($rateArr);
			$count[$a]++;
		}
		return $count;
	}


	public function ABlock() {
		//echo $_SERVER['REQUEST_TIME'];
		echo microtime();

		$str_as = chr(0) . chr(1) . chr(3) . chr(4) . chr(15) . chr(21) . chr(33) . chr(16) . chr(40) . chr(49);

		$baseConf = M_Config::getVal();
		$x = $baseConf['city_in_area_x'];
		$y = $baseConf['city_in_area_y'];

		$strArea = '';
		if (empty($str_as)) {
			$strArea = str_repeat('0', $x * $y);
		} else {
			$arr_as = str_split($str_as, 2);
			$arr_block_pos = array();
			foreach ($arr_as as $k => $as) {
				$arr_block_pos[] = ord($as[0]) . '_' . ord($as[1]);
			}

			for ($i = 0; $i < $x; $i++) {
				for ($j = 0; $j < $y; $j++) {
					$strArea .= in_array($i . '_' . $j, $arr_block_pos) ? '1' : '0';
				}
			}
		}

		echo '<br />' . microtime();

		return $strArea;
	}

	public function ASxxx() {
		$name = B_Utils::getRandNickName();
		return $name;
	}


	public function AWarSecne() {
		$ret = M_War::getMapNoByTerrain(1, 1);
		return $ret;
	}

	public function AZip() {
		$zip = new ZipArchive();
		$filename = "/tmp/test.zip";

		if ($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {
			exit("cannot open <$filename>\n");
		}

		$zip->addFromString("test.txt", "#1 This is a test string added as testfilephp.txt.\n");
		//$zip->addFromString("testfilephp2.txt" . time(), "#2 This is a test string added as testfilephp2.txt.\n");
		//$zip->addFile(ROOT_PATH . "/test/1.txt","/2.txt");
		echo "numfiles: " . $zip->numFiles . "\n";
		echo "status:" . $zip->status . "\n";
		$zip->close();

		//$zip = new zipfile();
		//$filename = "1.jpg";
		//$fsize = @filesize($filename);
		//$fh = fopen($filename, 'rb', false);
		//$data = fread($fh, $fsize);
		//$zip->addFile($data,$filename);
		//$zipcontents = $zip->file();

		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=\"TheZip.zip\"");
		//header("Content-length: " . strlen($zipcontents) . "\n\n");

		// output data
		//echo $zipcontents;
	}

	public function AMap() {
		M_MapWild::initMap(T_App::MAP_AFRICA);
	}

	public function ATime() {
		$a = time();
		$b = time() + 1;
		echo date('Y-m-d H:i:s', $a) . "<\br>";
		echo date('Y-m-d H:i:s', $b) . "<\br>";
	}

	public function AFind() {
		$mapList = array();
		for ($i = 0; $i < 10; $i++) {
			for ($j = 0; $j < 10; $j++) {
				$areaNo = M_Formula::calcAreaNo($i, $j, 100);
				//$mapList[$areaNo] = in_array($areaNo, array(404,705))?1:0;

			}
		}
		//var_dump($mapList);
		$startX = $startY = 1;
		$endX = $endY = 8;
		$pf = new PathFind($mapList, $startX, $startY, $endX, $endY);
		$arr = $pf->getPath();;
		foreach ($arr as $val) {
			echo "<item>{$val['x']},{$val['y']}</item>\n";
		}
	}


	/*
	 public function ABaseConfig()
	 {
	M_Config::moveToDB();
	}*/

	public function AHejunyun() {
		foreach (T_Word::$EQUIP_QUAL as $quality => $yanse) {
			foreach (T_Equip::$equipLevel as $level) {
				foreach (T_Word::$EQUIP_QUAL as $pos => $name) {
					//(等级/10)*7+12+(品质-1)*5]
					$attr = intval($level / 10 * 27 + 12 + ($quality - 1) * 5);
					$rand = 0;
					//$rand = rand(-2, 3);
					$attr = $attr + $rand;

					$attrColumns = T_Equip::$posBaseAttr[$pos]; //各位置对应的属性字段

					echo "【" . $yanse . "】";
					echo T_Word::$EQUIP_NAME[$level] . $name;
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "需要等级:" . $level;
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "属性：" . $attr;
					echo "<br>";
					//echo $yanse, '==', T_Word::$EQUIP_NAME[$level], $name, '===', $level,'=====',$attr,"<br>";
				}
			}
		}
	}

	/** 装备生成
	 * public function ACreateEquip()
	 * {
	 * //M_Equip::createEquip($a);
	 * $arr = T_Word::$EQUIP_QUAL;
	 * foreach ($arr as $key => $val)
	 * {
	 * M_Equip::createEquip($key);
	 * }
	 * }
	 */

	public function AXiaohe() { //$table, $selected = '*', $curPage, $offset, $parms = '', $sidx = 'id', $sord = 'DESC'
		$list = B_DB::apiPageData('union', 'id, create_city_id', 1, 999, '', 'id', 'DESC');
		foreach ($list as $key => $val) {
			$whereArr = array(
				'city_id' => $val['create_city_id'],
			);
			$where2Arr = array(
				'union_id' => $val['id'],
				'position' => 2,
			);
			if (!DB::getRow('union_member', $whereArr) && !DB::getRow('union_member', $where2Arr)) {
				$setArr = array(
					'union_id' => $val['id'],
					'city_id' => $val['create_city_id'],
					'position' => 2,
					'flag' => 1,
					'point' => 0,
					'award_time' => 0,
					'create_at' => time()
				);
				DB::insert('union_member', $setArr);
			}

		}

	}

	public function ATestPayLog() {
		$curPage = isset($formVals['page']) ? $formVals['page'] : 1;
		$offset = isset($formVals['rows']) ? $formVals['rows'] : 20;
		$sidx = isset($formVals['sidx']) ? $formVals['sidx'] : 'id';
		$sord = isset($formVals['sord']) ? $formVals['sord'] : 'DESC';
		$parms = isset($formVals['filter']) ? $formVals['filter'] : array();
		$ret = M_Pay::getIncomeLog($curPage, $offset, $parms, $sidx, $sord);
		var_dump($ret);
	}

	public function AMakeEquip($cityId = 10, $level = 1, $pos = 1, $quality = 1) {
		$ret = M_Equip::makeEquip($cityId, $level, $pos, $quality);
		var_dump($ret);
	}

	public function ACreateTpl() {
		$lvs = array(1, 10, 20, 30, 40, 50, 60, 70, 80, 90);
		for ($pos = 1; $pos < 7; $pos++) {
			for ($quality = 1; $quality < 7; $quality++) {
				foreach ($lvs as $level) {
					$info = array(
						'name' => '',
						'pos' => $pos,
						'face_id' => $pos . '_' . $quality . '_' . $level,
						'type' => 1,
						'need_level' => $level,
						'level' => 0,
						'max_level' => 50,
						'quality' => $quality,
						'base_lead' => 0,
						'base_command' => 0,
						'base_military' => 0,
						'is_locked' => 0,
						'is_vip_use' => 1,
						'gold' => 0,
					);
					//生成装备名字
					$info['name'] = M_Equip::getSysEquipName($level, $pos);
					//生成属性
					//$attr = intval($level/10 * 7 +12 + ($quality -1) *5);
					$attr = M_Equip::getEquipAttr($level, $quality); //intval($level/10 * 27 +12 + ($quality -1) *5);

					//确定属性分配
					$attrColumns = T_Equip::$posBaseAttr[$pos];
					if (count($attrColumns) == 1) {
						$info[$attrColumns[0]] = $attr;
					} elseif (count($attrColumns) == 2) {
						$attr1 = intval($attr / 2);
						$attr2 = $attr - $attr1;
						$info[$attrColumns[0]] = $attr1;
						$info[$attrColumns[1]] = $attr2;
					}
					echo $pos, '|', $quality, '|', $level, '|', $info['name'], '|', $info[$attrColumns[0]], '|', $info[$attrColumns[1]];
					if (!empty($info['name'])) {
						$ret = B_DB::instance('BaseEquipTpl')->insert($info);
						var_dump($ret);
					}
					echo "<br>";

				}
			}

		}
	}

	public function AGetAward() {
		$award = '{"res":{"gold":"800,30","food":"800,100","oil":"800,30"},"props":{"1":"1,1","4":"1,1","7":"1,1","39":"1,1"},"equip":{"37":"1,20","71":"1,20"},"coupon":[6,1],"sys_equip":["1,1,1,1,100"]}';
		$ret = M_War::parseWarAward($award);
		var_dump($ret);
	}

	public function ATt() {
		$arr = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
		$a = 0;
		$b = 0;
		$rand = rand(0, 9);
		for ($i = 0; $i < 100; $i++) {
			$k = array_rand($arr);
			if ($arr[$k] == 5) {
				$a = $a + 1;
			}
		}
		echo $a;
		echo "<hr>";
		$arr = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20);
		for ($i = 0; $i < 200; $i++) {
			$k = array_rand($arr);
			if ($arr[$k] == 5) {
				$b = $b + 1;
			}
		}
		echo $b;
	}

	/** 删除全部军官/装备缓存数据 */
	public function ADelHeroEquipAll() {
		$cityId = 7;

		$rc1 = new B_Cache_RC(T_Key::CITY_HERO_LIST, $cityId); //所有军官ID
		$arrHeroId = $rc1->smembers();
		foreach ($arrHeroId as $heroId) {
			$rc11 = new B_Cache_RC(T_Key::CITY_HERO_INFO, $heroId);
			$rc11->delete();
		}
		$rc1->delete();

		$rc2 = new B_Cache_RC(T_Key::CITY_EQUIP_LIST, $cityId); //所有装备ID
		$arrEquiId = $rc2->smembers();
		foreach ($arrEquiId as $equipId) {
			$rc21 = new B_Cache_RC(T_Key::CITY_EQUIP_INFO, $equipId);
			$rc21->delete();
		}
		$rc2->delete();

		$rc3 = new B_Cache_RC(T_Key::CITY_ITEM_LIST, $cityId); //所有物品ID
		$rc3->delete();
	}

	/** 删除军官/装备缓存数据 */
	public function ADelHeroEquip() {

	}

	public function ADelAuc() {
		$rc = new B_Cache_RC(T_Key::BASE_WEAPON);
		$ret = $rc->delete();
		$apcKey = T_Key::BASE_WEAPON;
		//$ret = B_Cache_APC::del($apcKey);
		var_dump($ret);
		echo '<hr />';
		$info = B_Cache_APC::get($apcKey);
		//$info = $rc->get();
		var_dump($info);
		echo '<hr />';

	}

	/** 合服拍卖行找回物品 旧数据库名字、新旧 城市ID*/
	public function AAuc() {
	}


	public function AChen12() {
		$code = 'AAArBAHWYzZiMjc2OTQwYmI5';

		$pwd = M_Config::getSvrCfg('city_card_pwd');
		$str = base64_decode($code);
		$arr = unpack('NIdx/Ctype/CpropsNo/C*', $str);
		$idx = $arr['Idx'];
		$type = $arr['type'];
		$propsNo = $arr['propsNo'];
		var_dump($arr);
		echo '<hr />';
		unset($arr['Idx'], $arr['type'], $arr['propsNo']);
		$v = array_values($arr);
		array_unshift($v, "c*");
		$hash = call_user_func_array("pack", $v);
		echo "hash=={$hash}";
		echo "<hr>";
		$verify = substr(md5(pack("NC", $idx, $type) . $pwd . pack('C', $propsNo)), 0, 12);
		echo "verify=={$verify}";
		if ($hash == $verify) {
			echo "<br />SUCC<br />";
		} else {
			echo "<br />FAIL<br />";
		}
	}

	public function AChen11() {
		//删除战斗缓存(卡战斗)
		//查战斗ID march/outlist	[BattleId] => 372582  [即行军ID]
		$cityId = 107325;
		M_Battle_List::delBattleIdByCity($cityId, 198442);
	}

	/** 清除野外NPC不存在数据 */
	public function AChen10() {
		$arrPosno = array(2066239, 1088209); //洲XY
		foreach ($arrPosno as $posno) {
			//$bDel = M_MapWild::delWildMapInfo($posno); //删除旧的地图数据(直接删DB，慎用)
			M_MapWild::syncWildMapBlockCache($posno); //刷新此块地图数据
		}
	}



	/** 清除旧地图坐标 */
	public function ADelWildMapCache() {
		$oldZone = 2; //填入要删除的洲XY
		$oldPosX = 63;
		$oldPosY = 187;
		$oldPosNo = M_MapWild::calcWildMapPosNoByXY($oldZone, $oldPosX, $oldPosY);

		M_MapWild::delWildMapInfo($oldPosNo); //删除旧的地图数据
		M_MapWild::syncWildMapBlockCache($oldPosNo); //刷新旧的地图数据
		$rc = RC::instance();
		$divNo = $oldPosNo % M_MapWild::DIV_NUM;
		$mcListKey = AppD_Key::WILD_MAP_NO_HOLD_POS . $oldZone . $divNo;
		$rc->sadd($mcListKey, $oldPosX . '_' . $oldPosY); //释放原占用坐标
	}

	/** 清除日常任务及完成次数 */
	public function ACleanTask() {
		$cityId = 107;
		$taskId = 36;
		$mcKey = AppD_Key::TASK_DAILY_TIMES . date('Ymd') . $cityId; //redis的key
		$hKey = strval($taskId); //redis的hashkey
		$rcW = RC::instanceW();
		$rcW->hSet($mcKey, $hKey, 0);
	}


	public function ATestLogin($username = '') {
		$list = M_Auth::getAllOnline();
		$num = count($list); //在线人数
		$key = 'QUEUE';
		$lastNumKey = 'QUEUE_NUM';
		$rc = RC::instance();
		$ext = $rc->exists($lastNumKey);

		if ($num < 500 && !$ext) {
			//直接进入游戏
		} else //排队
		{
			if ($ext) //有排队队列
			{
				$lastNum = $rc->get($lastNumKey);
				$newNum = $lastNum + 1;
			} else {
				$newNum = 1;
			}
			//玩家序号=>用户名,状态(1可登陆,0排队中),更新时间
			//$key = $key . $newNum; //序号
			$val = array(
				$newNum, $username, 0, time()
			);
			$str = implode(',', $val);
			$cookieStr = B_Crypt::encode($str);
			setcookie('Q', $cookieStr, 3600, '/', $domain);
			$rc->set($lastNumKey, $newNum);
			$rc->zAdd($key, $newNum, $str);
			//进入排队界面
		}
	}


	public function AUpQueue() {
		$key = 'QUEUE';
		$rc = RC::instance();
		$sum = $rc->zSize($key);
		if ($sum > 0) {
			$list = M_Auth::getAllOnline();
			$online = count($list); //在线人数
			while ($online < 500) {
				$ret = $rc->zRange($key, 0, 0);
				$str = $ret[0];
				$rc->zDelete($key, $str);

				$tmpArr = explode(',', $str);
				$num = $tmpArr[0];
				$tmpArr[2] = 1;
				$str = implode(',', $tmpArr);
				$rc->zAdd($key, $num, $str);
				$online = $online + 1;
			}
		}
	}

	public function AzAdd() {
		$key = 'TEST';
		$rc = RC::instance();
		$lastNumKey = 'QUEUE_NUM';
		$lastNum = $rc->get($lastNumKey);
		//$rc->zAdd($key, 1, 'AAAAAAAAAAA');
		//$rc->zAdd($key, 2, 'BBBBBBBBBBB');
		//$rc->zAdd($key, 3, 'CCCCCCCCCCC');
		//$rc->zAdd($key, 4, 'DDDDDDDDDDD');
		//$rc->zAdd($key, 5, 'EEEEEEEEEEE');
		//$rc->zAdd($key, 6, 'FFFFFFFFFFF');
		//$rc->zDelete($key, 'AAAAAAAAAAA');
		//$ret = $rc->zRange($key, 0, 0);
		$ret = $rc->zRangeByScore($key, 4, 10, array('limit' => array(0, 1)));
		$ret = $rc->zRangeByScore($key, 4, 4);
		var_dump($ret);
	}

	public function ASend() {
		$formVals['nickname'] = '何苦';
		$formVals['gold'] = 1000000;
		$ret = Api_Stats::SendGoods($formVals);
		echo "<hr>";
		var_dump($ret);
	}

	public function ABot() {
		$cityId = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : '';
		$info = M_City::getInfo($cityId);

		$info['city_id'] = $cityId;
		$expire = time() + T_App::ONE_DAY; //过期时间1天
		$ip = B_Utils::getIp();
		$sessId = uniqid('war2_');
		$data = array(
			'user_id' => $info['user_id'],
			'sess_id' => $sessId,
			'city_id' => $info['city_id'],
			'ip_addr' => $ip,
			'expire' => $expire,
			'verify' => sha1($info['user_id'] . '|' . $sessId . '|' . $info['city_id'] . '|' . $ip . '|' . $expire . '|' . M_Auth::COOKIE_KEY),
		);


		$cookieStr = B_Crypt::encode(json_encode($data));
		M_Auth::addOnline($info['user_id'], $sessId, $ip);
		$apiurl = "http://www.mswar2.com/dev.php?r=city/info";
		$r = new HttpRequest($apiurl, HttpRequest::METH_GET);

		$r->setOptions(array('cookies' => array('A' => $cookieStr)));
		$out = $r->send()->getBody();
		var_dump($out);
	}

	public function APayTest($userName) {
		if (!empty($userName)) {
			//[用户名|时间|IP|订单编号|人民币|军饷比例|礼券比例|服务器ID|运营商名称|校验码]
			$userName = 'zhuoyiting';
			$time = date('YmdHis');
			$ip = '127.0.0.1';
			$orderNo = 333333;
			$rmb = 1000;
			$milpay = 10;
			$coupon = 10;
			$serverId = '10001';
			$consName = 'devtest';
			$consumerKey = '123456';

			$keyArr = array($userName, $time, $ip, $orderNo, $rmb, $milpay, $coupon, $serverId, $consName, $consumerKey);
			$key = implode('|', $keyArr);
			$key = md5($key);
			$urlArr = array($userName, $time, $ip, $orderNo, $rmb, $milpay, $coupon, $serverId, $consName, $key);

			$url = implode('|', $urlArr);
			$url = urlencode(base64_encode($url));

			//B_Common::redirect('?r=Member/Pay&verify=' . $url);
			B_Common::redirect('pay/' . $url);
		}

	}

	/**
	 * 模拟连接QQ接口登录
	 */
	public function AQQLogin() {
		$appid = 100265152;
		$appkey = '408eb8a7ca6b47c8a7a9c5c04bc5f4f5';
		$openid = 'E2F709DBCF94078F240BCC9974EAEDC8';
		$openkey = 'EADEBA876BCE731BF74019DB57E38A74';
		$url = "?appid={$appid}&appkey={$appkey}&openid={$openid}&openkey={$openkey}";
		B_Common::redirect('qqlogin/' . $url);
	}

	/**
	 * 内网模拟验证防沉迷
	 * @author chenhui on 20120110
	 */
	public function AAdult() {
		$username = isset($_REQUEST['username']) ? trim($_REQUEST['username']) : '';
		$isadult = isset($_REQUEST['isadult']) ? trim($_REQUEST['isadult']) : '';
		if ($username) {
			$now = date('YmdHis');
			$serverName = 'war2';
			$consumerName = 'consumer1';
			$consumerKey = '123456';
			$keyArr = array($username, $now, $serverName, $consumerName, $consumerKey);
			//$str = $username . '|'.$now.'|'.$serverName.'|'.$consumerName.'|' . $consumerKey;
			$key = implode('|', $keyArr);
			$key = md5($key);
			unset($keyArr);
			$urlArr = array($username, $now, $serverName, $consumerName, $key, $isadult);
			$url = implode('|', $urlArr);
			$url = urlencode(base64_encode($url));
			//B_Common::redirect('?r=Member/CheckAdult&verify=' . $url);
			B_Common::redirect('adult/' . $url);
		}

		B_View::render('adult');
	}

	/**
	 * 生成战斗ID
	 *
	 */
	public function makeBattleId() {
		$now = time();
		$battleId = $now;
		$rc = RC::instance();
		$key = AppD_Key::BATTLE_ID_INCR;
		if ($rc->exists($key)) {
			$battleId = $rc->incr($key);
		} else {
			$bid = $now . '00';
			$bid = substr($bid, -10);
			$battleId = $rc->set($key, $bid);
		}
		return $battleId;
	}

	public function AMakeBaseCacheFile($url = '') {
		if (empty($url)) {
			M_Base::genBinFile();
		} else {
			echo $url;
			$data = file_get_contents($url);
			$tmp = gzuncompress($data);
			echo "<pre>";
			print_r(json_decode($tmp, true));
			echo "</pre>";

		}


	}

	public function AUu() {
		$a = M_War::buildCampWarBattleData(1, '2154149', '9022011', array("100146"));
		var_dump($a);

	}

	/**
	 * 生成系统套装模板
	 * hejunyun
	 */
	public function ACreateEquipSuit() {

		/** 套装加成属性 */
		$equipSuitAdd = array(
			self::TZ_ZH => '指挥',
			self::TZ_JS => '军事',
			self::TZ_TS => '统帅',
		);

		for ($i = 4; $i < 7; $i++) {
			foreach (T_Equip::$equipLevel as $key => $level) {
				$id = $level . '_' . $i;
				$name = T_Word::$EQUIP_QUAL[$i] . '色' . T_Word::$EQUIP_NAME[$level] . '套装';
				$effect = array(
					2 => array(T_Equip::TZ_TS => $suitAddNum[$i] + $key * $suitAddNum[$i]),
					4 => array(T_Equip::TZ_JS => $suitAddNum[$i] + $key * $suitAddNum[$i]),
					6 => array(T_Equip::TZ_ZH => $suitAddNum[$i] + $key * $suitAddNum[$i])
				);

				$effect = json_encode($effect);
				$desc = '2件加成:' . $equipSuitAdd[T_Equip::TZ_TS] . '+' . ($suitAddNum[$i] + $key * $suitAddNum[$i]) . ',';
				$desc .= '4件加成:' . $equipSuitAdd[T_Equip::TZ_JS] . '+' . ($suitAddNum[$i] + $key * $suitAddNum[$i]) . ',';
				$desc .= '6件加成:' . $equipSuitAdd[T_Equip::TZ_ZH] . '+' . ($suitAddNum[$i] + $key * $suitAddNum[$i]);

				$info = array(
					'id' => $id,
					'name' => $name,
					'effect' => $effect,
					'desc' => $desc
				);
				$id = B_DB::instance('BaseEquipSuit')->insert($info);
				echo $id, "<br>";
			}
		}
	}

	public function AChangeTaskAward() {
		$list = B_DB::instance('BaseTask')->all();
		foreach ($list as $info) {
			$data = array();
			$award = array();
			$tmpAward = json_decode($info['award'], true);
			if (isset($tmpAward['res']['gold']) && $tmpAward['res']['gold']) {
				$award['gold'] = array(100, $tmpAward['res']['gold']);
			}
			if (isset($tmpAward['res']['food']) && $tmpAward['res']['food']) {
				$award['food'] = array(100, $tmpAward['res']['food']);
			}
			if (isset($tmpAward['res']['oil']) && $tmpAward['res']['oil']) {
				$award['oil'] = array(100, $tmpAward['res']['oil']);
			}
			if (isset($tmpAward['money']['coupon']) && $tmpAward['money']['coupon']) {
				$award['coupon'] = array(100, $tmpAward['money']['coupon']);
			}
			if (isset($tmpAward['money']['milpay']) && $tmpAward['money']['milpay']) {
				$award['milpay'] = array(100, $tmpAward['money']['milpay']);
			}
			if (isset($tmpAward['item']['renown']) && $tmpAward['item']['renown']) {
				$award['renown'] = array(100, $tmpAward['item']['renown']);
			}
			if (isset($tmpAward['item']['mil_medal']) && $tmpAward['item']['mil_medal']) {
				$award['warexp'] = array(100, $tmpAward['item']['mil_medal']);
			}
			if (isset($tmpAward['energy']) && $tmpAward['energy']) {
				$award['march_num'] = array(100, $tmpAward['energy']);
			}
			if (isset($tmpAward['mil_order']) && $tmpAward['mil_order']) {
				$award['atkfb_num'] = array(100, $tmpAward['mil_order']);
			}

			if (isset($tmpAward['props']) && $tmpAward['props']) {
				$tmpProps = array();
				foreach ($tmpAward['props'] as $id => $num) {
					$tmpProps[$id] = array(100, $num);
				}
				$award['props'] = array(100, M_Award::MODE_ALL, $tmpProps);
			}
			if (isset($tmpAward['equip']) && $tmpAward['equip']) {
				$tmpEquip = array();
				foreach ($tmpAward['equip'] as $id) {
					$tmpEquip[$id] = array(100, 1);
				}
				$award['equip'] = array(100, M_Award::MODE_ALL, $tmpEquip);
			}

			$data = array(
				'id' => 10000 + $info['id'],
				'name' => '新手任务' . $info['id'],
				'type' => M_Award::TYPE_TASK,
				'mode' => M_Award::MODE_ALL,
				'award_text' => json_encode($award),
				'award_desc' => '',
				'create_at' => time(),
			);
			$ret = B_DB::instance('BaseAward')->insert($data);
			echo $ret, "<br>";
		}
	}

	public function AChangeNpcAward() {
		$list = B_DB::instance('BaseNpcTroop')->getAll();
		foreach ($list as $info) {
			$data = array();
			$award = array();
			$tmpAward = json_decode($info['war_award'], true);
			if (isset($tmpAward['res']['gold']) && $tmpAward['res']['gold']) {
				$tmpAward['res']['gold'] = explode(',', $tmpAward['res']['gold']);
				$award['gold'] = array($tmpAward['res']['gold'][1], $tmpAward['res']['gold'][0]);
			}
			if (isset($tmpAward['res']['food']) && $tmpAward['res']['food']) {
				$tmpAward['res']['food'] = explode(',', $tmpAward['res']['food']);
				$award['food'] = array($tmpAward['res']['food'][1], $tmpAward['res']['food'][0]);
			}
			if (isset($tmpAward['res']['oil']) && $tmpAward['res']['oil']) {
				$tmpAward['res']['oil'] = explode(',', $tmpAward['res']['oil']);
				$award['oil'] = array($tmpAward['res']['oil'][1], $tmpAward['res']['oil'][0]);
			}
			if (isset($tmpAward['coupon']) && $tmpAward['coupon']) {
				$award['coupon'] = array($tmpAward['coupon'][1], $tmpAward['coupon'][0]);
			}
			if (isset($tmpAward['mil_pay']) && $tmpAward['mil_pay']) {
				$award['milpay'] = array($tmpAward['mil_pay'][1], $tmpAward['mil_pay'][0]);
			}


			if (isset($tmpAward['props']) && $tmpAward['props']) {
				$tmpProps = array();
				foreach ($tmpAward['props'] as $id => $val) {
					$val = explode(',', $val);
					$tmpProps[$id] = array($val[1], $val[0]);
				}
				$award['props'] = array(100, M_Award::MODE_ALL, $tmpProps);
			}
			if (isset($tmpAward['sys_equip']) && $tmpAward['sys_equip']) {
				$tmpEquip = array();
				foreach ($tmpAward['sys_equip'] as $val) {
					$val = explode(',', $val);
					$EquipId = B_DB::instance('BaseEquipTpl')->getId($val[0], $val[1], $val[2]);
					$tmpEquip[$EquipId] = array($val[4], $val[3]);
				}
				$award['equip'] = array(100, M_Award::MODE_ALL, $tmpEquip);
			}

			if ($award) {
				$data = array(
					'id' => 20000 + $info['id'],
					'name' => $info['nickname'],
					'type' => M_Award::TYPE_DROP,
					'mode' => M_Award::MODE_ALL,
					'award_text' => json_encode($award),
					'award_desc' => $info['award_remark'],
					'create_at' => time(),
				);
				$ret = B_DB::instance('BaseAward')->insert($data);
				echo $ret, "<br>";
			}

		}

	}


	public function AChangeProbeAward() {
		$list = B_DB::instance('BaseProbe')->getall();
		foreach ($list as $info) {
			$data = array();
			$award = array();
			$tmpAward = json_decode($info['award'], true);
			if (isset($tmpAward['res']['gold']) && $tmpAward['res']['gold']) {
				$tmpAward['res']['gold'] = explode(',', $tmpAward['res']['gold']);
				$award['gold'] = array($tmpAward['res']['gold'][1], $tmpAward['res']['gold'][0]);
			}
			if (isset($tmpAward['res']['food']) && $tmpAward['res']['food']) {
				$tmpAward['res']['food'] = explode(',', $tmpAward['res']['food']);
				$award['food'] = array($tmpAward['res']['food'][1], $tmpAward['res']['food'][0]);
			}
			if (isset($tmpAward['res']['oil']) && $tmpAward['res']['oil']) {
				$tmpAward['res']['oil'] = explode(',', $tmpAward['res']['oil']);
				$award['oil'] = array($tmpAward['res']['oil'][1], $tmpAward['res']['oil'][0]);
			}

			if (isset($tmpAward['coupon']) && $tmpAward['coupon']) {
				$award['coupon'] = array($tmpAward['coupon'][1], $tmpAward['coupon'][0]);
			}

			if (isset($tmpAward['mil_medal']) && $tmpAward['mil_medal']) {
				$award['warexp'] = array($tmpAward['mil_medal'][1], $tmpAward['mil_medal'][0]);
			}

			if (isset($tmpAward['props']) && $tmpAward['props']) {
				$tmpProps = array();
				foreach ($tmpAward['props'] as $id => $val) {
					$val = explode(',', $val);
					$tmpProps[$id] = array($val[1], $val[0]);
				}
				$award['props'] = array(100, M_Award::MODE_ONLYONE, $tmpProps);
			}

			if ($award) {
				$data = array(
					'id' => 50000 + $info['id'],
					'name' => '探索奖励' . $info['id'],
					'type' => M_Award::TYPE_PROBE,
					'mode' => M_Award::MODE_ONLYONE,
					'award_text' => json_encode($award),
					'award_desc' => '',
					'create_at' => time(),
				);
				$ret = B_DB::instance('BaseAward')->insert($data);
				echo $ret, "<br>";
			}
		}
	}

	public function AChangePropsAward() {
		$list = B_DB::instance('BaseProps')->getAll();
		foreach ($list as $info) {
			$data = array();
			$award = array();
			if ($info['effect_txt'] == 'NEWBIE_PACKS') {
				$tmpAward = json_decode($info['effect_val'], true);
				if (isset($tmpAward['res']['gold']) && $tmpAward['res']['gold']) {
					$award['gold'] = array(100, $tmpAward['res']['gold']);
				}
				if (isset($tmpAward['res']['food']) && $tmpAward['res']['food']) {
					$award['food'] = array(100, $tmpAward['res']['food']);
				}
				if (isset($tmpAward['res']['oil']) && $tmpAward['res']['oil']) {
					$award['oil'] = array(100, $tmpAward['res']['oil']);
				}

				if (isset($tmpAward['props']) && $tmpAward['props']) {
					$tmpProps = array();
					foreach ($tmpAward['props'] as $id => $num) {
						$tmpProps[$id] = array(100, $num);
					}
					$award['props'] = array(100, M_Award::MODE_ALL, $tmpProps);
				}

				if (isset($tmpAward['equip']) && $tmpAward['equip']) {
					$tmpEquip = array();
					foreach ($tmpAward['equip'] as $id) {
						$tmpEquip[$id] = array(100, 1);
					}
					$award['equip'] = array(100, M_Award::MODE_ALL, $tmpEquip);
				}

				if (isset($tmpAward['money']['coupon']) && $tmpAward['money']['coupon']) {
					$award['coupon'] = array(100, $tmpAward['money']['coupon']);
				}

				if (isset($tmpAward['mil_order']) && $tmpAward['mil_order']) {
					$award['atkfb_num'] = array(100, $tmpAward['mil_order']);
				}

				if (isset($tmpAward['energy']) && $tmpAward['energy']) {
					$award['march_num'] = array(100, $tmpAward['energy']);
				}

				if ($award) {
					$data = array(
						'id' => 60000 + $info['id'],
						'name' => $info['name'],
						'type' => M_Award::TYPE_PROPS,
						'mode' => M_Award::MODE_ALL,
						'award_text' => json_encode($award),
						'award_desc' => $info['desc'],
						'create_at' => time(),
					);
					$ret = B_DB::instance('BaseAward')->insert($data);
					echo $ret, "<br>";
				}
			}
		}
	}


	public function ADelChache() {
		$cityId = 338;
		$mcKey = AppD_Key::CITY_EXTRA_INFO . $cityId;
		RC::delete($mcKey);
	}


	public function ASetHeroCity($heroId, $cityId) {
		$heroInfo = M_Hero::getHeroInfo($heroId);
		$ocityId = $heroInfo['city_id'];
		M_Hero::setHeroInfo($heroId, array('city_id' => $cityId));
		M_Hero::delCityHeroList($ocityId, $heroId);
		M_Hero::setCityHeroList($cityId, $heroId);
	}

	public function AUpHero($heroId, $exp) {
		$heorInfo = M_Hero::getHeroInfo($heroId);
		$tmp = array(
			'exp' => $heorInfo['exp'] + $exp
		);
		M_Hero::setHeroInfo($heroId, $tmp);
	}

	public function AUpArmy($cityId, $armyId, $exp) {
		M_Army::addArmyExp($cityId, array($armyId => $exp));
	}

	public function ADelMapCity() {
		$mcKey = AppD_Key::WILD_MAP_INFO . $posNo;
		RC::delete($mcKey);
		$ret = M_MapWild::syncWildMapBlockCache(2052240);
		var_dump($ret);
	}


	public function ADeleteOccupied($z, $x, $y) {
		//清除城市无敌的情况
		$ret = false;
		$posNo = M_MapWild::calcWildMapPosNoByXY($z, $x, $y);
		$wildInfo = M_MapWild::getWildMapInfo($posNo);

		if ($wildInfo['hold_expire_time'] == 0 && $wildInfo['type'] == 2) {
			$cityId = $wildInfo['city_id'];
			$cityColonyInfo = M_ColonyCity::getInfo($cityId);
			$holdTimeInterval = M_Config::getVal('hold_city_time_interval');
			if (!empty($cityColonyInfo['hold_time']) && $cityColonyInfo['hold_time'] >= T_App::ONE_HOUR * $holdTimeInterval) {
				$ret = M_ColonyCity::setInfo($cityId, array('hold_time' => 0));
			}
		}
		print_r($ret);
	}
}

?>
<?php

/**
 * 程序逻辑日志
 */
class B_Logger {
	const CRON_TYPE_SYNC = 'sync_';
	const CRON_TYPE_DATA = 'data_';
	const CRON_TYPE_ERR = 'err_';


	//日志类型道具
	const LOG_TYPE_PROPS = 'props';
	//日志类型装备
	const LOG_TYPE_EQUIP = 'equip';
	//日志类型英雄
	const LOG_TYPE_HERO = 'hero';
	//日志类型预备兵
	const LOG_TYPE_ARMY = 'army';


	//道具类型获取
	const P_ACT_INCR = 1;
	//道具类型删除
	const P_ACT_DECR = 2;

	//道具类型
	static $PropsType = array(
		self::P_ACT_INCR => 1, //获取
		self::P_ACT_DECR => 2, //删除
	);

	//预备兵类型招募
	const A_ACT_INCR = 1;
	//预备兵类型解散
	const A_ACT_DECR = 2;

	//预备兵类型
	static $ArmyType = array(
		self::A_ACT_INCR => 1, //招募
		self::A_ACT_DECR => 2, //解散
	);


	/** 寻将 */
	const H_ACT_FIND = 1;
	/** 招募 */
	const H_ACT_RECRUIT = 2;
	/** VIP抽取 */
	const H_ACT_VIP = 3;
	/** 学习技能 */
	const H_ACT_LEARN = 4;
	/** 遗忘技能 */
	const H_ACT_FORGET = 5;
	/** 拍卖 */
	const H_ACT_AUCTION = 6;
	/** 卖出 */
	const H_ACT_SELLOUT = 7;
	/** 买入 */
	const H_ACT_BUY = 8;
	/** 拍卖行领取 */
	const H_ACT_RECEIVE = 9;
	/** 解雇 */
	const H_ACT_FIRE = 10;
	/** 培养 */
	const H_ACT_CULTURE = 11;
	/** 抽奖 */
	const H_ACT_LOTTERY = 12;
	/** 奖励 */
	const H_ACT_AWARD = 13;
	/** 军官卡 */
	const H_ACT_CARD = 14;
	/** 军官兑换 */
	const H_HERO_EXCHANGE = 15;
	/** 商城购买 */
	const H_MALL_BUY = 16;

	/** 军官日志 获取类型 */
	static $HeroActType = array(
		self::H_ACT_FIND => '寻将获得',
		self::H_ACT_RECRUIT => '学院招募',
		self::H_ACT_VIP => 'VIP抽取',
		self::H_ACT_LEARN => '学习技能',
		self::H_ACT_FORGET => '遗忘技能',
		self::H_ACT_AUCTION => '拍卖',
		self::H_ACT_SELLOUT => '卖出',
		self::H_ACT_BUY => '买入',
		self::H_ACT_RECEIVE => '拍卖行领取',
		self::H_ACT_FIRE => '解雇',
		self::H_ACT_CULTURE => '培养',
		self::H_ACT_LOTTERY => '抽奖',
		self::H_ACT_AWARD => '奖励',
		self::H_ACT_CARD => '军官卡',
		self::H_HERO_EXCHANGE => '军官兑换 ',
		self::H_MALL_BUY => '商城购买 ',
	);

	/** 获取 */
	const E_ACT_GET = 1;
	/** 金钱出售 */
	const E_ACT_SELLGOLD = 2;
	/** 合成保留 */
	const E_ACT_FSRETAIN = 3;
	/** 合成消失 */
	const E_ACT_FSDIS = 4;
	/** 升级 */
	const E_ACT_UPLEVEL = 5;
	/** 强化 */
	const E_ACT_STRENGTHEN = 6;
	/** 拍卖 */
	const E_ACT_AUCTION = 7;
	/** 拍卖行卖出 */
	const E_ACT_SELLOUT = 8;
	/** 拍卖行买入 */
	const E_ACT_BUY = 9;
	/** 拍卖行领取 */
	const E_ACT_RECEIVE = 10;
	/** 使用经验 */
	const E_ACT_DEL_EXP = 11;

	/** 军官日志 操作类型 */
	static $EquipActType = array(
		self::E_ACT_GET => '获取',
		self::E_ACT_SELLGOLD => '金钱出售',
		self::E_ACT_FSRETAIN => '合成保留',
		self::E_ACT_FSDIS => '合成消失',
		self::E_ACT_UPLEVEL => '升级',
		self::E_ACT_STRENGTHEN => '强化',
		self::E_ACT_AUCTION => '拍卖',
		self::E_ACT_SELLOUT => '拍卖行卖出',
		self::E_ACT_BUY => '拍卖行买入',
		self::E_ACT_RECEIVE => '拍卖行领取',
		self::E_ACT_DEL_EXP => '使用经验',
	);

	static public function error($msg, $debug = false) {
		return self::write($msg, 'error');
	}

	static public function debug($msg) {
		return self::write($msg, 'debug');
	}


	static public function logDel($id, $type) {
		$fileDir = LOG_PATH . '/info/del/' . $type . '/';
		$fileName = date('Ymd') . '.log';
		$msg = "$id\n";
		self::_add($msg, $fileDir, $fileName);
	}

	//性能执行时间
	static public function perform($msg) {
		return self::write($msg, 'perform');
	}

	/**
	 * 拍卖日志
	 * @param array $msg
	 */
	static public function auction($msg) {
		return self::write($msg, 'auction');
	}

	static public function qq($msg) {
		$parms = json_encode($msg);
		return self::write($msg, 'qq');
	}

	static public function warn($msg) {
		return self::write($msg, 'warn');
	}

	static public function base($msg) {
		return true;
		$cli = defined('CLI_MODE') ? 'cli' : '';
		return self::write("{$cli}#{$msg} Reload!", 'base');
	}

	static public function dev($msg) {
		if (B_Utils::isDev()) {
			self::write($msg, 'dev');
		}
	}

	static public function write($msg, $type, $json = true) {
		if ($json) {
			$msg = json_encode($msg);
		}
		$fileDir = LOG_PATH . '/svr/' . date('Ym') . '/';
		$fileName = date('d') . '_' . $type . '.log';
		$now = date('Y-m-d H:i:s');
		$msg = "[{$now}] {$msg} \n";
		self::_add($msg, $fileDir, $fileName);
	}


	static public function dump($msg, $file) {
		error_log($msg, 3, $file);
	}

	/**
	 * 战斗日志
	 * @author huwei
	 * @param int $bId 战斗ID
	 * @param string $msg 数据描叙
	 * @param int $num 回合数
	 */
	static public function battle($msg, $bId, $num = 0) {
		if (B_Utils::isDev() && !empty($bId) && !empty($msg)) {
			$bId = strval($bId);
			$n = strlen($bId) - 1;
			$fileDir = LOG_PATH . '/battle/' . date('Ym') . '/' . date('d') . '/' . $bId[$n] . '/';
			$now = date('Y-m-d H:i:s');
			$msg = "[{$now}] [$num] [{$msg}]\n";
			$fileName = $bId . '.log';
			self::_add($msg, $fileDir, $fileName);
		}
	}

	/**
	 * 数据库操作日志
	 * @author huwei
	 * @param string $funcName 操作函数名
	 * @param string $errMsg 错误信息
	 * @param arr $params 参数
	 */
	static public function db($funcName, $errMsg, $parms) {
		$parms = json_encode($parms);
		$errMsg = json_encode($errMsg);

		$fileDir = LOG_PATH . '/db/';
		$now = date('Y-m-d H:i:s');
		$msg = "[{$now}] [{$funcName}] [{$errMsg}] [{$parms}] \n";
		$fileName = date('Ymd') . '.log';
		self::_add($msg, $fileDir, $fileName);
	}

	/**
	 * 聊天日志类
	 * @author huwei
	 * @param string 发送人
	 * @param int $type 消息类型
	 * @param array $msg 消息内容
	 */
	static public function chat($who, $type, $msg) {
		$fileDir = LOG_PATH . '/chat/' . $type . '/' . date('Ym') . '/';
		//按小时归档
		$now = date('Y-m-d H:i:s');
		//$data = serialize($msgArr);
		$msg = "[{$now}] [{$who}] [{$type}] [{$msg}]\n";
		$fileName = date('dH') . '.log';
		self::_add($msg, $fileDir, $fileName);
	}

	/**
	 * 任务日志类
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @param int $type 聊天频道
	 * @param array $resArr 资源数组
	 */
	static public function cron($action, $data = '') {
		$fileDir = LOG_PATH . '/cron/' . date('Ym') . '/';
		$now = date('Y-m-d H:i:s');
		$msg = "[{$now}] [{$data}]\n";
		$fileName = date('d') . '_' . $action . '.log';
		self::_add($msg, $fileDir, $fileName);
	}

	/**
	 * 充值日志
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @param int $type 聊天频道
	 * @param array $resArr 资源数组
	 */
	static public function pay($data = '', $type = '') {
		$fileDir = LOG_PATH . '/pay/';
		$msg = "{$data}\n";
		$fileName = date('Ym') . '_' . $type . '.log';
		if ($type == 'tmp') {
			$fileName = 'tmp.log';
		}

		self::_add($msg, $fileDir, $fileName);
	}

	static public function halt($msg) {
		die($msg);
	}

	/**
	 * @author huwei
	 * @param string $msg 日志内容
	 * @param string $fileDir 日志目录名
	 * @param string $fileName 日志文件名
	 */
	static private function _add($msg, $fileDir, $fileName) {
		if (!is_dir($fileDir)) {
			@mkdir($fileDir, 0777, true);
			@chmod($fileDir, 0777);
		}
		$logFile = $fileDir . $fileName;
		return error_log($msg, 3, $logFile);
	}

	static public function opItem($cityId, $id, $action, $data = '') {
		self::_op($cityId, O_Log::OP_TYPE_ITEM, $action, $id, $data);
	}

	static public function opEquip($cityId, $id, $action, $data = '') {
		self::_op($cityId, O_Log::OP_TYPE_EQUIP, $action, $id, $data);
	}

	static public function opHero($cityId, $id, $action, $data = '') {
		self::_op($cityId, O_Log::OP_TYPE_HERO, $action, $id, $data);
	}

	static private function _op($cityId, $type, $action, $itemId, $data = '') {
		if ($cityId && $type && $action && $itemId) {
			//军饷支出流水账
			$logData = array(
				'city_id' => $cityId,
				'action' => $action,
				'type' => $type,
				'data' => $data,
				'item_id' => $itemId,
				'created_at' => time(),
			);
			B_DB::instance('LogOp')->insert($logData);
		}

	}
}

?>
<?php

/**
 *
 * 工具类
 *
 */
class B_Utils {
	/**
	 * 黑名单
	 * @param string $name 字符串
	 * @param bool $replace 是否要替换
	 * @author 胡威
	 * @return bool(true非法, false正确) #if $replace==true 返回替换后的字符串
	 */
	static public function isBlockName($name, $replace = false) {
		$ret = false;
		$name = trim($name);
		if (!empty($name)) {
			$key = T_Key::BASE_BLOCK_LIST;
			$data = B_Cache_APC::get($key);
			if (empty($data)) {
				$blockFile = ETC_PATH . '/' . ETC_NO . '/blocklist.txt';
				$lines = file($blockFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
				$tmp = array();
				foreach ($lines as $l) {
					$tmp[md5($l)] = $l;
				}
				sort($tmp);
				$total = count($tmp);
				$offset = 3000;
				$num = ceil($total / $offset);
				for ($i = 0; $i < $num; $i++) {
					$arr = array_splice($tmp, 0, $offset);
					$data[$i] = implode('|', $arr);
				}

				APC::set($key, $data);
			}

			if ($replace == true) {
				$replacement = '**';
				$ret = $name;
				foreach ($data as $txt) {
					$pattern = "/" . $txt . "/i";
					$ret = preg_replace($pattern, $replacement, $ret);
				}
			} else {
				foreach ($data as $txt) {
					$pattern = "/" . $txt . "/i";
					$ret = preg_match($pattern, $name) ? true : false;
					if ($ret) {
						break;
					}
				}
			}
		}
		return $ret;
	}

	/**
	 * 获取ip
	 * @author 胡威
	 * @return string
	 */
	static public function getIp() {
		$onlineip = '';
		$cip = getenv('HTTP_CLIENT_IP');
		$xip = getenv('HTTP_X_FORWARDED_FOR');
		$rip = getenv('REMOTE_ADDR');
		$srip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		if ($cip && strcasecmp($cip, 'unknown')) {
			$onlineip = $cip;
		} elseif ($xip && strcasecmp($xip, 'unknown')) {
			$onlineip = $xip;
		} elseif ($rip && strcasecmp($rip, 'unknown')) {
			$onlineip = $rip;
		} elseif ($srip && strcasecmp($srip, 'unknown')) {
			$onlineip = $srip;
		}
		preg_match("/[\d\.]{7,15}/", $onlineip, $match);
		$onlineip = isset($match[0]) ? $match[0] : 'unknown';
		return $onlineip;
	}

	/**
	 * 获取当前URL
	 * @author 胡威
	 * @return string
	 */
	static public function getCurrentUrl() {
		$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
		$currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$parts = parse_url($currentUrl);

		// drop known fb params
		$query = '';
		if (!empty($parts['query'])) {
			$params = array();
			parse_str($parts['query'], $params);

			if (!empty($params)) {
				$query = '?' . http_build_query($params, null, '&');
			}
		}

		// use port if non default
		$port = isset($parts['port']) && (($protocol === 'http://' && $parts['port'] !== 80) || ($protocol === 'https://' && $parts['port'] !== 443)) ? ':' . $parts['port'] : '';

		// rebuild
		return $protocol . $parts['host'] . $port . $parts['path'] . $query;
	}

	/**
	 * 解析包含json格式的内容为array
	 * @author chenhui on 20110402
	 * @param str /array json 包含json格式的内容(字符串/一维、二维数组)
	 * @param int type jsoninfo的类型 -1=字符串 1=一维数组 2=二维数组
	 * @param array keys 要格式化的字段array
	 * @return array 解析为array的内容
	 */
	static public function json2array($jsoninfo, $type, $keys = array('list')) {
		if (empty($jsoninfo) || empty($type) || empty($keys)) {
			return array();
		}
		$type = intval($type);
		if ($type == -1) //json字符串
		{
			$jsoninfo = is_string($jsoninfo) ? json_decode($jsoninfo, true) : array();
		} else if ($type == 1) //一维数组
		{
			foreach ($keys as $key) {
				if (!array_key_exists($key, $jsoninfo)) {
					continue;
				}
				$jsoninfo[$key] = is_string($jsoninfo[$key]) ? json_decode($jsoninfo[$key], true) : array();
			}
		} else if ($type == 2) //二维数组
		{
			foreach ($jsoninfo as $k => $arrv) {
				if (!is_array($arrv)) {
					continue;
				}
				foreach ($keys as $key) {
					if (!array_key_exists($key, $arrv)) {
						continue;
					}
					$arrv[$key] = is_string($arrv[$key]) ? json_decode($arrv[$key], true) : array();
				}
				$jsoninfo[$k] = $arrv;
			}
		}

		return $jsoninfo;
	}

	/**
	 * 解析包含整数格式时间的内容为可读格式
	 * @author chenhui on 20110509
	 * @param int /array intinfo 包含int格式的内容(int/一维、二维数组)
	 * @param int type intinfo的类型 -1=整数 1=一维数组 2=二维数组
	 * @param array keys 要格式化的字段array
	 * @return string/array 解析后的内容
	 */
	static public function dateint2str($intinfo, $type, $keys = array('create_at')) {
		if (empty($intinfo) || empty($type) || empty($keys)) {
			return array();
		}
		$type = intval($type);
		$datefmt = 'Y-m-d H:i:s'; //时间格式化表达式,类型 2011-01-24 17:15:29
		if ($type == -1) //整数
		{
			$intinfo = is_numeric($intinfo) ? date($datefmt, $intinfo) : date($datefmt, 0);
		} else if ($type == 1) //一维数组
		{
			foreach ($keys as $key) {
				if (!array_key_exists($key, $intinfo)) {
					continue;
				}
				$intinfo[$key] = is_numeric($intinfo[$key]) ? date($datefmt, $intinfo[$key]) : date($datefmt, 0);
			}
		} else if ($type == 2) //二维数组
		{
			foreach ($intinfo as $k => $arrv) {
				if (!is_array($arrv)) {
					continue;
				}
				foreach ($keys as $key) {
					if (!array_key_exists($key, $arrv)) {
						continue;
					}
					$arrv[$key] = is_numeric($arrv[$key]) ? date($datefmt, $arrv[$key]) : date($datefmt, 0);
				}
				$intinfo[$k] = $arrv;
			}
		}

		return $intinfo;
	}

	/**
	 * 将数组的键值作为新数组的两个值返回
	 * @author chenhui on 20110425
	 * @param array arrkv 原始数组
	 * @return array 符合需求的新数组
	 */
	static public function kv2vv($arrkv) {
		$arrvv = array();
		if (!empty($arrkv) && is_array($arrkv)) {
			foreach ($arrkv as $key => $val) {
				$arrvv[] = array(intval($key), intval($val));
			}
		}
		return $arrvv;
	}

	/**
	 * 计算几率
	 * @author huwei
	 * @param int $rate 几率数
	 * @param int $max 范围
	 * @return bool
	 */
	static public function odds($rate, $max = 100) {
		$ret = false;
		$rnd = mt_rand(1, $max);

		if ($rnd <= $rate) {
			$ret = true;
		}

		return $ret;
	}

	/**
	 * 掷骰子概率计算
	 * @author huwei on 20110615
	 * @param array $arr array('a'=>20,'b'=>30,'c'=>50)
	 * @return bool/string 返回数组的KEY
	 */
	static public function dice($rateArr) {
		$kn = false;
		$max = 1;
		$start = 0;
		$new = array();
		foreach ($rateArr as $k => $v) {
			$end = $start + $v;
			$new[$k] = array($start + 1, $end);
			$start = $end;
			$max = $end;
		}

		$rnd = mt_rand(1, $max);

		foreach ($new as $kn => $vn) {
			if ($rnd >= $vn[0] && $rnd <= $vn[1]) {
				return $kn;
			}
		}
		return $kn;
	}

	/**
	 * 计算字符长度
	 * @author huwei
	 * @param string $str
	 * @return int
	 */
	static public function len($str) {
		//utf8格式字符串 3个字节 转换 gb2312算2个字节
		//英文和数字 算1个字节
		return (strlen($str) + mb_strlen($str, 'UTF8')) / 2;
	}

	/**
	 * 是否非法
	 * @author huwei
	 * @param string $str
	 * @return int
	 */
	static public function isIllegal($str) {
		$result = true;
		$pattern = '/[\n\r\t\s]/u';
		if (!preg_match($pattern, $str)) {
			$result = false;
		}
		return $result;
	}

	/**
	 * 地图元素二进制包
	 * @param int $val 数据
	 * @param int $len 分配字节长度
	 */
	static public function mapDec2Bin($val, $len) {
		$formatArr = array(
			1 => 'C', //1个字节
			2 => 'n', //2个字节
			4 => 'N', //4个字节
		);
		$format = isset($formatArr[$len]) ? $formatArr[$len] : $formatArr[1];
		return pack($format, $val);
	}

	/**
	 * 获取加密后的值
	 * @author chenhui on 20110902
	 * @param string $oldVal
	 * @return string 加密后的值
	 */
	static public function getEncrypt($oldVal) {
		return dechex(crc32(($oldVal)));
	}

	/**
	 * 使进程保持唯一进程
	 * @author huwei
	 * @param string $pidName
	 * @param int $curPid
	 * @return bool
	 */
	static public function keepOnePid($pidName, $curPid = '') {
		$cmd = "ps -ef | grep \"$pidName\" | grep -v grep | awk '{print $2}'";
		$outStr = shell_exec($cmd);
		$pids = explode("\n", trim($outStr));
		foreach ($pids as $pid) {
			//$killCmd = "kill $pid";
			!empty($pid) && $pid != $curPid && posix_kill($pid, 9);
		}
		return true;
	}

	/**
	 * 获取名字
	 * @author huwei
	 * @param int $gender 性别
	 * @param int $race 种族
	 * @return string
	 */
	static public function getRandHeroName($gender, $race) {
		$key = T_Key::BASE_HERO_NAME_LIST . $gender . $race;
		$nameData = B_Cache_APC::get($key);
		if (empty($nameData) &&
			in_array($gender, array(T_App::GENDER_MALE, T_App::GENDER_FEMALE)) &&
			in_array($race, array(T_App::RACE_WHITE, T_App::RACE_YELLOW))
		) {
			$nameFile = ETC_PATH . '/' . ETC_NO . '/heroname_' . $gender . '_' . $race . '.txt';
			$lines = file($nameFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			foreach ($lines as $line) {
				$nameData[] = trim($line);
			}
			APC::set($key, $nameData);
		}
		$randKey = array_rand($nameData, 1);
		return isset($nameData[$randKey]) ? trim($nameData[$randKey]) : '无';
	}

	/**
	 * 获取随机字符串
	 * @author Hejunyun
	 * @param string $prefix 前缀
	 * @param int $length 长度
	 */
	static public function getRandString($prefix = '', $length = 18) {
		$prefix = preg_replace('/\s/', '', $prefix); //前缀 去掉空格
		$length = intval($length); //返回的字符长度
		$preLen = strlen($prefix);
		$length = $length - $preLen;
		$length = min($length, 32 - $preLen); //最大32位
		$length = max($length, 6 - $preLen); //最小6位
		$str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"; //输出字符集
		$len = strlen($str) - 1;

		$s = $prefix;
		for ($i = 0; $i < $length; $i++) {
			$s .= $str[rand(0, $len)];
		}
		return $s;
	}

	static public function formatTime($uptime) {
		$day = floor(($uptime / 86400) * 1.0);
		$calc1 = $day * 86400;
		$calc2 = $uptime - $calc1;
		$hour = floor(($calc2 / 3600) * 1.0);

		$calc3 = $hour * 3600;
		$calc4 = $calc2 - $calc3;
		$min = floor(($calc4 / 60) * 1.0);

		$calc5 = $min * 60;
		$sec = floor(($calc4 - $calc5) * 1.0);

		$ret = '';
		if (!empty($day)) {
			$ret .= "{$day}天";
		}
		if (!empty($hour)) {
			$ret .= "{$hour}小时";
		}
		if (!empty($min)) {
			$ret .= "{$min}分";
		}
		if (!empty($sec)) {
			$ret .= "{$sec}秒";
		}
		return $ret;
	}

	/**
	 * 获取自定义的系统时间戳 类似time()
	 * @author huwei
	 * @return int
	 */
	static public function getCustomTimestamp() {
		$start = 1325376000; //strtotime('2012-01-01 00:00:00')
		return time() - $start;
	}

	/**
	 * 检测提交的字符串是不是含有SQL注入字符
	 * @author chenhui on 20120321
	 * @param string $sql_str 提交的字符串
	 * @return 1/0/false
	 */
	static public function injectCheck($sql_str) {
		$ret = preg_match('/select|insert|and|or|update|delete|drop|database|\'|\"|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile/i', $sql_str); //匹配
		return intval($ret);
	}

	/**
	 * 检测用户名称是否含有 空格 换行符 回车 制表符 长度在[3,7]
	 * @author huwei
	 * @param string $name 用户名称
	 * @return bool true失败/false正常
	 */
	static public function isIllegalName($name) {
		$pattern = '/[\n\r\t\s\|\,\_\.\&\<\>\:\^\*\#\$\%\@\!\~\=\+\-\(\)\{\}\[\]\'\"]/u';
		return preg_match($pattern, $name);
	}

	/**
	 * 允许ip过滤函数
	 * @param array $accessIp 允许接入的IP列表array(192.168.0.*, 11.12.13.202)
	 * @param string $clientIp 用户IP 192.168.0.1
	 * @return bool 1禁止 0通过
	 */
	static public function isAccessIp($accessIp, $clientIp) {
		$ipArr = explode('.', $clientIp);
		$forbid = true;
		foreach ($accessIp as $val) {
			$tmpArr = explode('.', $val);
			$n = 0;
			foreach ($ipArr as $k => $v) {
				if (isset($tmpArr[$k]) &&
					$tmpArr[$k] != '*' && $v != $tmpArr[$k]
				) {
					$n++;
				}
			}
			if ($n == 0) {
				$forbid = false;
				break;
			}
		}
		return $forbid;
	}

	/**
	 * 禁止ip过滤函数
	 * @param array $forbidIp 禁止接入的IP列表array(192.168.0.*, 11.12.13.202)
	 * @param string $clientIp 用户IP 192.168.0.1
	 * @return bool 1禁止 0通过
	 */
	static public function isForbidIp($forbidIp, $clientIp) {
		$ipArr = explode('.', $clientIp);
		$forbid = false;
		foreach ($forbidIp as $val) {
			$tmpArr = explode('.', $val);
			$n = 0;
			foreach ($ipArr as $k => $v) {
				if ($tmpArr[$k] == '*') {
					$n++;
				} else if ($v == $tmpArr[$k]) {
					$n++;
				}
			}
			if ($n == 4) {
				$forbid = true;
				break;
			}
		}
		return $forbid;
	}

	/**
	 * 是否开发模式
	 * @return bool
	 */
	static public function isDev() {
		if (defined('DEV_ENV') && DEV_ENV == true) {
			return true;
		}
		return false;
	}

	/**
	 * 获取当前host
	 * @return string
	 */
	static public function getHost() {
		return isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
	}

	static public function awardText($id, $br = false) {
		$val = M_Base::award($id);
		$propsList = M_Base::propsAll();
		$equipList = M_Base::equipAll();
		$heroList = M_Base::heroAll();
		$str = '';
		$arr = explode('|', $val['val']);
		foreach ($arr as $v) {
			$tmp = explode('_', $v);
			switch ($tmp[1]) {
				case 'gold':
					$name = '金钱';
					break;
				case 'food':
					$name = '食物';
					break;
				case 'oil':
					$name = '石油';
					break;
				case 'milpay':
					$name = '军饷';
					break;
				case 'coupon':
					$name = '礼券';
					break;
				case 'renown':
					$name = '威望';
					break;
				case 'eploit':
					$name = '军功';
					break;
				case 'energy':
					$name = '军令';
					break;

				case 'props':
					$name = $propsList[$tmp[3]]['name'];
					break;

				case 'equip':
					$name = $equipList[$tmp[3]]['name'];
					break;

				case 'hero':
					$name = $heroList[$tmp[3]]['name'];
					break;

			}
			$str .= "{$name}x{$tmp[2]}({$tmp[0]}%);&nbsp;";
			$str .= $br ? "<br>" : "&nbsp;";
		}
		return $str;
	}

	static public function admAwardText($id) {
		$textArr = array(
			'gold' => '金钱',
			'food' => '食物',
			'oil' => '石油',
			'milpay' => '军饷',
			'coupon' => '礼券',
			'renown' => '威望',
			'warexp' => '功勋',
			'march_num' => '活力',
			'atkfb_num' => '军令',
			'props' => '道具',
			'equip' => '装备',
			'hero' => '英雄',
			'props_weapon' => '图纸',
		);

		$str = '<br>';
		if (!empty($id)) {
			$awardArr = M_Award::allResult($id);
			$text = M_Award::toText($awardArr);

			foreach ($text as $val) {
				if ($val[0] == 'res' || $val[0] == 'money' || $val[0] == 'item') {
					$str .= $textArr[$val[1]] . 'x' . $val[3] . ' 概率:' . $val[4] . '%' . '<br>';
				} elseif ($val[0] == 'equip') {
					$str .= $textArr[$val[0]] . ':' . $val[2][1] . 'x' . $val[3] . ' 概率:' . $val[4] . '%' . '<br>';
				} elseif ($val[0] == 'hero') {
					$str .= $textArr[$val[0]] . ':' . $val[2][1] . 'x' . $val[3] . ' 概率:' . $val[4] . '%' . '<br>';
				} else {
					$str .= $textArr[$val[0]] . ':' . $val[2] . 'x' . $val[3] . ' 概率:' . $val[4] . '%' . '<br>';
				}
			}
		}
		return $str;
	}

	/**
	 * 内存大小转换
	 * @param int $size 字节
	 * @return string
	 */
	static public function convert($size) {
		$unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
		return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
	}

	/**
	 * 字符串 转换 数组
	 * @author huwei
	 * @param string 字符串milpay,100|coupon,10|80,1000
	 * @return array 数组('milpay'=>100,'coupon'=>10,'80'=>1000)
	 */
	static public function str2arr($str) {
		$tmp = array();
		if ($str) {
			$arr = explode('|', trim($str));
			foreach ($arr as $tmpVal) {
				if (!empty($tmpVal)) {
					list($n, $v) = explode(',', $tmpVal);
					$tmp[$n] = $v;
				}
			}
		}
		return $tmp;
	}

	static public function base64url_encode($arr) {
		$data = gzcompress(json_encode($arr));
		$base64 = base64_encode($data);
		$base64url = strtr($base64, '+/=', '-_,');
		return $base64url;
	}

	static public function base64url_decode($plainText) {
		$base64url = strtr($plainText, '-_,', '+/=');
		$base64 = base64_decode($base64url);
		$ret = json_decode(gzuncompress($base64), true);
		return $ret;
	}

	static public function makessid($uid, $sid) {
		$rnd = mt_rand(1, 9);
		$expire = time() + 10;

		$enUid = dechex($uid + self::UID_OFFSET);
		$enSid = dechex($sid + self::UID_OFFSET);
		$enExpire = dechex($expire);

		$sgin = substr(sha1($expire . $sid . $uid . $rnd . M_Auth::COOKIE_KEY), 0, 6);
		$ssid = $rnd . $sgin . $enExpire . $enSid . $enUid;
		return $ssid;
	}

	static public function parsessid($ssid) {
		$ret = array();
		$rnd = substr($ssid, 0, 1);
		$sgin = substr($ssid, 1, 6);
		$expire = hexdec(substr($ssid, 7, 8));
		$sid = hexdec(substr($ssid, 15, 5)) - self::UID_OFFSET;
		$uid = hexdec(substr($ssid, 20)) - self::UID_OFFSET;
		$verify_sign = substr(sha1($expire . $sid . $uid . $rnd . M_Auth::COOKIE_KEY), 0, 6);
		if ($verify_sign == $sgin) {
			$ret = array($uid, $sid, $expire);
		}
		return $ret;
	}
}

?>
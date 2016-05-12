<?php

class C_Cache {
	static public function AInit() {
		if (!M_Adm::isLogin()) {
			B_Common::redirect('?r=Index/Login');
		}
	}

	static public function AIndex() {
		$config = B_Cache_File::get('redis');
		foreach ($config['hostname'] as $n => $v) {
			$rc = B_Cache_RC::conn($config, $n);
			if (!$rc) {
				$msg = 'Redis Connection failed: ' . json_encode($config);
				Logger::write($msg, 'redis');
				Logger::halt('Err_RC');
			}
			$pageData['info'][$n] = $rc->info();
		}

		B_View::render('Cache/Index', $pageData);
	}


	static public function ACleanKey() {
		$key = isset($_POST['key']) ? trim($_POST['key']) : '';
		$suffix = isset($_POST['val']) ? trim($_POST['val']) : '';

		$content = array();
		if (!empty($key)) {
			$rc = new B_Cache_RC($key, $suffix);
			$ret = $rc->delete();
			$keyName = $rc->get_key();
			$content[] = $ret ? "{$keyName}[成功]<br>" : "{$keyName}[失败]<br>";
		} else {
			$content[] = $key;
		}
		$pageData['content'] = implode("", $content);

		B_View::render('Cache/Tip', $pageData);
	}

	static public function ADelKey() {
		$ret = false;
		if (!empty($_GET['key'])) {

		}
		echo $ret ? '成功 ' : '失败';
	}

	static public function AFileBin() {
		$pageData = array();
		$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
		if ($act == 'gen') {
			$ret = M_Base::genBinFile();

			$str = implode('\n', $ret);

			echo "<script>";
			echo "alert('{$str}')";
			echo "</script>";
			exit;
		}

		B_View::render('Cache/File', $pageData);
	}

	static public function ACleanApc() {
		$ret = apc_clear_cache('user');
		apc_clear_cache();
		echo "<script>";
		echo $ret ? "alert('操作成功!');" : "alert('操作失败!');";
		echo "</script>";

	}
}

function format_html($str) {
	return htmlentities($str, ENT_COMPAT, 'UTF-8');
}


function format_ago($time, $ago = false) {
	$minute = 60;
	$hour = $minute * 60;
	$day = $hour * 24;

	$when = $time;

	if ($when >= 0)
		$suffix = '以前';
	else {
		$when = -$when;
		$suffix = 'in the future';
	}

	if ($when > $day) {
		$when = round($when / $day);
		$what = '天';
	} else if ($when > $hour) {
		$when = round($when / $hour);
		$what = '时';
	} else if ($when > $minute) {
		$when = round($when / $minute);
		$what = '分';
	} else {
		$what = '秒';
	}

	if ($when != 1) $what = '大于' . $when . $what;

	if ($ago) {
		return " $what $suffix";
	} else {
		return " $what";
	}
}


function format_size($size) {
	$sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

	if ($size == 0) {
		return '0 B';
	} else {
		return round($size / pow(1024, ($i = floor(log($size, 1024)))), 1) . ' ' . $sizes[$i];
	}
}


function str_rand($length) {
	$r = '';

	for (; $length > 0; --$length) {
		$r .= chr(rand(32, 126)); // 32 - 126 is the printable ascii range
	}

	return $r;
}


?>
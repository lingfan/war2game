<?php

/**
 * 服务器控制器
 */
class C_Server {
	static public function AInit() {
		if (!M_Adm::isLogin()) {
			B_Common::redirect('?r=Index/Login');
		}
	}

	/**
	 * 服务器运行情况
	 * @author huwei on 20110616
	 */
	static public function AIndex() {
		B_View::render('Server/Index');
	}

	/**
	 * apc 信息
	 * @author huwei on 20110616
	 */
	static public function AApc() {
		B_View::render('Server/Apc');
	}

	/**
	 * php 信息
	 * @author huwei on 20110616
	 */
	static public function APhp() {
		B_View::render('Server/Php');
	}

	/**
	 * 查看定时任务脚本
	 * @author huwei on 20110616
	 */
	static public function ACron() {
		$cmd = "crontab -l";
		$outStr = shell_exec($cmd);

		$outArr = explode("\n", trim($outStr));
		$list = array();
		foreach ($outArr as $val) {
			if (!strstr($val, '#')) {
				$arr = explode(' ', $val);
				$list[] = $arr;
			}
		}

		$pageData['list'] = $list;
		B_View::setVal('pageData', $pageData);
		B_View::render('Server/Cron');
	}

	/**
	 * 守护进程脚本
	 * @author huwei on 20110616
	 */
	static public function ADeamon() {
		$cmd = "ps aux | grep \"Deamon\" | grep -v grep | awk '{print $1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$12}'";
		$outStr = shell_exec($cmd);
		$list = array();
		$outArr = explode("\n", trim($outStr));
		foreach ($outArr as $val) {
			$arr = explode(' ', $val);
			$list[$arr[1]] = $arr;
		}
		$pageData['list'] = $list;
		B_View::setVal('pageData', $pageData);
		B_View::render('Server/Deamon');
	}

	static public function AResetDeamon() {
		$serverId = B_Cache_File::server(SERVER_NO);
		$file = RUN_PATH . '/' . $serverId . '/deamon/run.php';
		shell_exec("{$file} restart");
		header("location:?r=Server/Deamon");
	}

	static public function AMysql() {
		$pageData['list'] = B_DB::instance('AdmUser')->fetchAll('SHOW VARIABLES');

		$pageData['status'] = B_DB::instance('AdmUser')->fetchAll('SHOW global status');

		$pdo = B_DB_Pdo::$dbh;
		$liststatus = array(
			'是否关闭自动提交功能' => $pdo->getAttribute(PDO::ATTR_AUTOCOMMIT),
			'当家PDO错误处理的模式' => $pdo->getAttribute(PDO::ATTR_ERRMODE),
			'表字段字符的大小写转换' => $pdo->getAttribute(PDO::ATTR_CASE),
			'与连接状态相关的特有信息' => $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS),
			'空字符串转换为SQL的NULL' => $pdo->getAttribute(PDO::ATTR_ORACLE_NULLS),
			'应用程序提前获取数据大小' => $pdo->getAttribute(PDO::ATTR_PERSISTENT),
			'与数据库特有的服务器信息' => $pdo->getAttribute(PDO::ATTR_SERVER_INFO),
			'数据库服务器版本号信息' => $pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
			'数据库客户端版本号信息' => $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION),
		);

		$pageData['statusinfo'] = $liststatus;

		B_View::setVal('pageData', $pageData);
		B_View::render('Server/Mysql');
	}

	static public function ASys() {
		$cmd = <<<EOF
ps -e -o pid,pcpu,pmem,comm,lstart,etime | grep -E '(mysql|nginx|php|redis)' |awk '{print $1,"|",$2,"|",$3,"|",$4}'
EOF;
		$outStr = shell_exec($cmd);
		$list = array();
		$outArr = explode("\n", trim($outStr));

		foreach ($outArr as $val) {
			$arr = explode('|', $val);
			$list[$arr[0]] = $arr;
		}
		$pageData['list'] = $list;
		B_View::setVal('pageData', $pageData);
		B_View::render('Server/Sys');
	}

	static public function ACleanOnline() {
		$rc1 = new B_Cache_RC(T_Key::ONLINE_USER_LIST);
		$list = $rc1->smembers();
		foreach ($list as $userId) {
			$rc = new B_Cache_RC(T_Key::ONLINE_USER, $userId);
			$rc->delete();
		}
		$ret = $rc1->delete();


		echo "<script>";
		echo $ret ? "alert('更新成功');" : "alert('更新失败');";
		echo "</script>";

	}

	static public function AUploadCode() {
		if (!empty($_FILES['zipfile']['tmp_name'])) {
			$file = $_FILES["zipfile"]['tmp_name'];
			$zip = new ZipArchive;
			$res = $zip->open($file);

			echo "numFiles: " . $zip->numFiles . "<br>";
			echo "status: " . $zip->status . "<br>";
			echo "statusSys: " . $zip->statusSys . "<br>";
			echo "filename: " . $zip->filename . "<br>";
			echo "comment: " . $zip->comment . "<br>";

			if ($res === TRUE) {
				$zip->extractTo(ROOT_PATH, array('data', 'srv', 'adm'));
				$zip->close();
				echo 'ok';
			} else {
				echo 'failed';
			}
			exit;

		}
		B_View::render('Server/UploadCode');
	}

	static public function APhar() {
		$no = 'v1';
		// 要打包是需要在php.ini设置phar.readonly = off的，默认是on
		$str = date('Ymd');
		$tmpfile = '/tmp/' . $no . '.phar';
		$tmpgzfile = $tmpfile . '.gz';
		unlink($tmpgzfile);
		$phar = new Phar($tmpfile, 0, 'v1.phar');
		$phar->buildFromDirectory('/opt/ww2/srv/v1');
		$phar->compress(Phar::GZ);
		$data = file_get_contents($tmpgzfile);

		$filename = 'ww2.' . $no;
		header("Content-Type: application/force-download");
		header("Content-Disposition: attachment; filename=" . $filename);
		echo $data;
		exit;
	}

	static public function AReqNum() {
		$day = empty($_REQUEST['day']) ? date('Ymd') : $_REQUEST['day'];

		$info = array();
		$startH = strtotime($day);
		$minNum = 24;
		$arr = array();
		$header = "<td>{$day}</td>";
		for ($i = 0; $i < $minNum; $i++) {
			$tmpH = date('YmdH', $startH + $i * 3600);
			$val = M_Stats::getReqNo($tmpH);
			foreach ($val as $req => $num) {
				$arr[$req][$i] = $num;
			}

			$header .= "<td>{$i}</td>";
		}

		$str = "<tr>{$header}</tr>";
		ksort($arr);
		foreach ($arr as $req => $val) {
			$show = substr($req, 0, 20);
			$td = "<td title='{$req}'>{$show}</td>";
			for ($i = 0; $i < $minNum; $i++) {
				$num = isset($val[$i]) ? $val[$i] : 0;
				$td .= "<td>{$num}</td>";
			}
			$str .= "<tr>{$td}</tr>";
		}

		$pageData['day'] = $day;
		$pageData['str'] = $str;
		B_View::setVal('pageData', $pageData);
		B_View::render('Server/ReqNum');

	}

}
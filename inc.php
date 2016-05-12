<?php
/** 版本 */
define('ETC_NO', 'cn');
/** 是否开发环境 [1开发 ,0产品] */
define('DEV_ENV', 1);

//代码根目录
define('ROOT_PATH', dirname(__FILE__));
define('CORE_PATH', ROOT_PATH . '/srv/');

define('WWW_PATH', ROOT_PATH . '/www');
define('RUN_PATH', ROOT_PATH . '/run');
define('ETC_PATH', ROOT_PATH . '/etc');
define('ADM_PATH', ROOT_PATH . '/adm');

define('LIB_PATH', ROOT_PATH . '/lib');
define('LOG_PATH', ROOT_PATH . '/log');
define('RPT_PATH', ROOT_PATH . '/rpt');
define('BIN_PATH', ROOT_PATH . '/bin');
define('BAK_PATH', ROOT_PATH . '/bak');

define('ROOT_URL', "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']));
define('SERVER_NO', basename(ROOT_PATH));

if (defined('IN_ADM')) {
	define('LIB_V_PATH', ADM_PATH . '/v');
} else {
	define('LIB_V_PATH', CORE_PATH . '/v');
}

$timezone = array(
	'cn' => 'Asia/Shanghai',
	'en' => 'America/New_York',
);
$defaultTimezone = isset($timezone[ETC_NO]) ? $timezone[ETC_NO] : $timezone['cn'];
date_default_timezone_set($defaultTimezone);

spl_autoload_register('loaderHandler');
set_error_handler('errHandler');


final class APC extends B_Cache_APC {
}

final class Logger extends B_Logger {
}

function loaderHandler($class) {
	$dir = CORE_PATH;
	if (stristr($class, '_')) {
		$classArr = explode('_', $class);
		$class = implode('/', $classArr);
	}

	$classFile = $dir . '/' . $class . '.php';

	if (is_readable($classFile)) {
		require_once $classFile;
	} else {
		//error_log(__METHOD__.":FAIL:{$classFile}\n", 3, '/tmp/loader_'.date('Ymd').'.log');
	}
}

function errHandler($level, $string, $file, $line) {
	$msg = new B_Exception($level, $string, $file, $line);
	Logger::write($msg, 'php', false);
}

?>
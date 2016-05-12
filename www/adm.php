<?php
define('IN_ADM', 1);

include('common.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
$clientIp = B_Utils::getIp();

$basecfg = M_Config::getVal();

$debugIp = isset($basecfg['debug_ip']) ? $basecfg['debug_ip'] : '';
$accessIp = explode("\r\n", $debugIp);

$forbid = B_Utils::isAccessIp($accessIp, $clientIp);
$forbid = false;
if ($forbid) {
	header("HTTP/1.0 404 Error#{$clientIp}");
	Logger::debug("Ip #{$clientIp} Access Adm Fail");
	exit;
}
$args = array(
	'r' => FILTER_SANITIZE_STRING,
);

$formVals = filter_var_array($_REQUEST, $args);

$c = $a = 'Index';

if (!empty($formVals['r'])) {
	$arr = explode('/', $formVals['r']);
	$c = isset($arr[0]) ? trim($arr[0]) : '';
	$a = isset($arr[1]) ? trim($arr[1]) : '';
}

$c = !empty($c) ? ucfirst($c) : 'Index';
$a = !empty($a) ? ucfirst($a) : 'Index';

if (isset($_REQUEST['r'])) {
	unset($_REQUEST['r']);
}

$params = array();
foreach ($_REQUEST as $val) {
	$params[] = filter_var($val, FILTER_SANITIZE_STRING);
}


require_once ADM_PATH . "/c/{$c}.php";
$c = 'C_' . $c;
$a = 'A' . $a;
if (!method_exists($c, $a)) {
	Logger::debug("404 Not Found [{$c}::{$a}]");
	Logger::halt('Err06');
} else {
	if (method_exists($c, 'AInit')) {
		call_user_func_array(array($c, 'AInit'), array());
	}
	$data = call_user_func_array(array($c, $a), $params);
	$ret = B_Common::outData($c, $a, $data);
	return $ret;
}
?>
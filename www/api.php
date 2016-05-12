<?php
define('IN_API', 1);
$sTime = microtime(1);
include('common.php');
$clientIp = B_Utils::getIp();
$accessIp = array('127.0.0.1', '192.168.0.*', '115.238.138.162');
$forbid = B_Utils::isAccessIp($accessIp, $clientIp);
$forbid = false;
if ($forbid) {
	header("HTTP/1.0 404 Error#{$clientIp}");
	Logger::debug("Ip #{$clientIp} Access API Fail");
	exit;
}
$args = array(
	'm' => FILTER_SANITIZE_STRING,
	'a' => FILTER_SANITIZE_STRING,
	'auth' => FILTER_SANITIZE_STRING,
);

$formVals = filter_var_array($_REQUEST, $args);
$params = array();
$ret = array('err' => -1, 'data' => "err args:[{$formVals['c']}|{$formVals['a']}]");
if (!empty($formVals['m']) && !empty($formVals['a'])) {
	$file = CORE_PATH . '/Api/' . str_replace('_', '/', $formVals['m']) . '.php';
	if (file_exists($file)) {
		$hash = md5($formVals['m'] . '|' . $formVals['a'] . '|' . M_Config::getSvrCfg('server_api_key'));
		if ($hash == $formVals['auth']) {
			require_once($file);
			$params = !empty($_REQUEST['params']) ? json_decode($_REQUEST['params'], true) : array();
			$func = array('A_' . $formVals['m'], $formVals['a']);
			// Logger::debug(array(__METHOD__,$formVals['params'] ));
			$data = call_user_func_array($func, array($params));

			$ret = array('err' => 0, 'data' => $data);
		} else {
			$ret = array('err' => -3, 'data' => "err auth:{$hash}");
		}
	} else {
		$ret = array('err' => -2, 'data' => "err file:{$file}");
	}
}


$eTime = microtime(1);
$diff = $sTime - $eTime;

$ret['debug'] = array('cost_time' => sprintf('%.3f', $diff), 'params' => $formVals);
echo json_encode($ret);
exit;

?>
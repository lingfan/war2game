<?php
define('IN_DEV', 1);
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('common.php');
include('./tool/auth.php');
header("Content-type: text/html; charset=utf-8");
$sTime = microtime(1);
$prof = isset($_GET['prof']) ? true : false;
if ($prof) {
	//xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY)
}

$r = filter_input(INPUT_GET,'r',FILTER_SANITIZE_STRING);
$arr = explode('/', $r);
$c = isset($arr[0]) ? trim($arr[0]) : '';
$a = isset($arr[1]) ? trim($arr[1]) : '';

$args = array($c, $a);

$gets = filter_input_array(INPUT_GET);
if (isset($args['r'])) {
	unset($args['r']);
}

foreach ($gets as $val) {
	$args[] = filter_var($val, FILTER_SANITIZE_STRING);
}
$ret = B_Controller::call($args);

$ret = $ret ? $ret : 'false';
echo "<pre>";
print_r($ret);
echo "</pre>";
$eTime = microtime(1);

$diff = sprintf('%.3f', ($eTime - $sTime));
echo "Cost Time: <b>" . $diff . "</b><br>";
$mem = memory_get_peak_usage(true);
echo "Peak mem. usage: <b>" . round($mem / 1024 / 10124, 2) . "</b> MB<br>";
if ($prof) {
	$data = xhprof_disable();

	$XHPROF_ROOT = dirname(__FILE__);
	include_once $XHPROF_ROOT . "/xhprof/xhprof_lib/utils/xhprof_lib.php";
	include_once $XHPROF_ROOT . "/xhprof/xhprof_lib/utils/xhprof_runs.php";

	$xhprof_runs = new XHProfRuns_Default();

	// Save the run under a namespace "xhprof".
	$run_id = $xhprof_runs->save_run($data, "xhprof");
	echo "<a href='http://" . $_SERVER['HTTP_HOST'] . "/xhprof/xhprof_html/index.php?run=$run_id&source=xhprof' target=' _blank'>view</a>";
}
?>
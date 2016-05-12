<?php
if ('application/x-amf' != getenv('CONTENT_TYPE')) {
	header('HTTP/1.0 404 Error');
	exit;
}

define('IN_GATEWAY', 1);
include('common.php');
$sTime = microtime(1);
//Include things that need to be global, for integrating with other frameworks
$amfphp['startTime'] = $sTime;

//Include framework
require_once LIB_PATH . "/Amfphp/amf/app/Gateway.php";
$gateway = new Gateway();
//$sysLibPath = CORE_PATH.'/';
//$gateway->setClassPath($sysLibPath);
$gateway->setCharsetHandler("none", "utf8", "utf8");
$gateway->setErrorHandling(1);

//Disable profiling, remote tracing, and service browser
$gateway->disableDebug();
// Keep the Flash/Flex IDE player from connecting to the gateway. Used for security to stop remote connections.
$gateway->disableStandalonePlayer();

//Explicitly disable the native extension if it is installed
//$gateway->disableNativeExtension();

//Enable gzip compression of output if zlib is available,
//beyond a certain byte size threshold
$gateway->enableGzipCompression(25 * 1024);

//Service now
$gateway->service();
$eTime = microtime(1);

$tmpDiff = sprintf('%.3f', ($eTime - $sTime));
$body = $gateway->getBody();
$reqNo = $body->_value[0] . $body->_value[1];
M_Stats::setReqNo($reqNo, $tmpDiff);

if ($tmpDiff > T_App::METHOD_EXEC_TIME) {
	Logger::perform("Time#" . $tmpDiff . " => " . implode('|', $body->_value));
}
?>
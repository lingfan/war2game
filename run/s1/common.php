<?php
$start = microtime(true);
define('CLI_MODE', true);
/** 服务器编号 */
define('SERVER_NO', basename(dirname(__FILE__)));
$incFile = dirname(dirname(dirname(__FILE__))) . '/inc.php';
include($incFile);

?>
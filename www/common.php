<?php
define('RPATH', dirname(dirname(__FILE__)));
$incFile = RPATH.'/inc.php';

include($incFile);

if(DEV_ENV)
{
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

?>
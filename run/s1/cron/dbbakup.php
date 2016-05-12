#!/usr/bin/env php
<?php
$commonFile = dirname(dirname(__FILE__)) . '/common.php';
include($commonFile);

$start = microtime(true);

M_Cron::dbbackup(SERVER_NO);
$end1 = microtime(true);
$diff = sprintf('%.3f', $end1 - $start);
Logger::cron("Day#M_Cron::dbbackup({$diff})", array(SERVER_NO));

?>
#!/usr/bin/env php
<?php
$commonFile = dirname(dirname(__FILE__)) . '/common.php';
include($commonFile);

$list = M_NPC::wildnpc();
//echo json_encode($list);
echo 'make wild npc end!';
?>
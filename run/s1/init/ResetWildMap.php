#!/usr/bin/env php
<?php
$commonFile = dirname(dirname(__FILE__)) . '/common.php';
include($commonFile);

$list = array();
$baseAreaList = M_MapWild::getWildMapAreaList();
$keys = array_keys($baseAreaList);


foreach (T_App::$map as $zone => $zname) {
	$pid = pcntl_fork();
	//这里最好不要有其他的语句
	if ($pid == -1) {
		die('could not fork');
	} else if ($pid) {
		$p[$pid] = 'ok';
		echo "we are the parent $pid\n";
		//pcntl_wait($status); //Protect against Zombie children
		//echo $status."\n";
	} else {
		$id = posix_getpid();
		//posix_kill($id, SIGHUP);
		//posix_kill($id, SIGINT);
		echo "I am the child $id\n";

		echo "start clean zone#{$zone}\n";
		$no = 1;
		foreach ($baseAreaList as $areaNo => $posArr) {
			$bDel = array();
			foreach ($posArr as $posXY) {
				list($x, $y) = explode('_', $posXY);
				$posNo = M_MapWild::calcWildMapPosNoByXY($zone, $x, $y);
				$mapInfo = M_MapWild::getWildMapInfo($posNo, true);
				if ($mapInfo['type'] == T_Map::WILD_MAP_CELL_NPC) {
					M_MapWild::delWildMapInfo($posNo);
					$bDel[] = $posNo;
				}
			}

			$rc = new B_Cache_RC(T_Key::WILD_MAP_AREA, $zone . ':' . $areaNo);
			$ret = $rc->delete();

			//$ret = M_MapWild::setWildMapAreaCache($zone, $areaNo);
			echo "{$zone}=>{$no}#{$areaNo}#" . json_encode($ret) . "==" . json_encode($bDel) . "\n";
			$no++;
		}
		echo "end clean zone#{$zone}\n";

		exit;
	}


}

?>
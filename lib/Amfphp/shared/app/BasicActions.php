<?php
/**
 * This file contains actions which are used by all gateways
 */
include_once(AMFPHP_BASE . 'shared/util/Authenticate.php');
include_once(AMFPHP_BASE . 'shared/util/NetDebug.php');
include_once(AMFPHP_BASE . 'shared/util/Headers.php');
include_once(AMFPHP_BASE . 'shared/util/CharsetHandler.php');
/**
 * Class loader action loads the class from which we will get the remote method
 */
function classLoaderAction(&$amfbody) {
	return true;
}

function adapterMap(&$results) {
	if (is_array($results)) {
		array_walk($results, 'adapterMap');
	} elseif (is_object($results)) {
		$className = strtolower(get_class($results));
		if (array_key_exists($className, $GLOBALS['amfphp']['adapterMappings'])) {
			$type = $GLOBALS['amfphp']['adapterMappings'][$className];
			$results = mapRecordSet($results, $type);
		} else {
			$vars = get_object_vars($results);

			array_walk($vars, 'adapterMap');

			foreach ($vars as $key => $value) {
				$results->$key = $value;
			}
		}
	} elseif (is_resource($results)) {
		$type = get_resource_type($results);
		$str = explode(' ', $type);
		if (in_array($str[1], array("result", 'resultset', "recordset", "statement"))) {
			$results = mapRecordSet($results, $str[0]);
		} else {
			$results = false;
		}
	}
	return $results;
}

function mapRecordSet($result, $type) {
	$classname = $type . "Adapter"; // full class name
	$includeFile = include_once(AMFPHP_BASE . "shared/adapters/" . $classname . ".php"); // try to load the recordset library from the sql folder
	if (!$includeFile) {
		trigger_error("The recordset filter class " . $classname . " was not found");
	}
	$recordSet = new $classname($result); // returns formatted recordset

	return array("columns" => $recordSet->columns, "rows" => $recordSet->rows);
}

?>
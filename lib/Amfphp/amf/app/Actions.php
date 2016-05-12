<?php
/**
 * Actions modify the AMF message PER BODY
 * This allows batching of calls
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright (c) 2003 amfphp.org
 * @package flashservices
 * @subpackage filters
 * @version $Id: Filters.php,v 1.6 2005/04/02   18:37:51 pmineault Exp $
 */

/**
 * ExecutionAction executes the required methods
 */
function executionAction(&$amfbody) {
	$args = $amfbody->getValue();
	$time = microtime_float();


	$results = B_Controller::call($args);

	global $amfphp;
	$amfphp['callTime'] += microtime_float() - $time;

	if ($results !== '__amfphp_error') {
		$amfbody->setResults($results);
		$amfbody->responseURI = $amfbody->responseIndex . "/onResult";
	}
	return false;
}

?>
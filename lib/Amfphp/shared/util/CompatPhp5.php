<?php
/**
 * Add a few 4.3.0 functions to old versions of PHP
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright (c) 2003 amfphp.org
 * @package flashservices
 * @subpackage io
 * @version $Id$
 */
function patched_array_search($needle, $haystack, $strict = FALSE) //We only need strict actually
{
	return array_search($needle, $haystack, $strict);
}

function microtime_float() {
	return microtime(true);
}

?>
<?php

/**
 * The Executive class is responsible for executing the remote service method and returning it's value.
 *
 * Currently the executive class is a complicated chain of filtering events testing for various cases and
 * handling them.  Future versions of this class will probably be broken up into many helper classes which will
 * use a delegation or chaining pattern to make adding new exceptions or handlers more modular.  This will
 * become even more important if developers need to make their own custom header handlers.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright (c) 2003 amfphp.org
 * @package flashservices
 * @subpackage app
 * @author Musicman original design
 * @author Justin Watkins Gateway architecture, class structure, datatype io additions
 * @author John Cowen Datatype io additions, class structure
 * @author Klaasjan Tukker Modifications, check routines
 * @version $Id: php5Executive.php,v 1.3 2005/07/05 07:40:50 pmineault Exp $
 */
class Executive {
	/**
	 * The built instance of the service class
	 *
	 * @access private
	 * @var object
	 */
	var $_classConstruct;

	/**
	 * The method name to execute
	 *
	 * @access private
	 * @var string
	 */
	var $_methodname;

	/**
	 * The arguments to pass to the executed method
	 *
	 * @access private
	 * @var mixed
	 */
	var $_arguments;

	function Executive() {
	}


}

?>

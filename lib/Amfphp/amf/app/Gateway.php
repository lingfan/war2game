<?php
/**
 * The Gateway class is the main facade for the AMFPHP remoting service.
 *
 * The developer will instantiate a new gateway instance and will interface with
 * the gateway instance to control how the gateway processes request, securing the
 * gateway with instance names and turning on additional functionality for the gateway
 * instance.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright (c) 2003 amfphp.org
 * @package flashservices
 * @subpackage app
 * @author Musicman  original design
 * @author Justin Watkins  Gateway architecture, class structure, datatype io additions
 * @author John Cowen  Datatype io additions, class structure,
 * @author Klaasjan Tukker Modifications, check routines, and register-framework
 * @version $Id: Gateway.php,v 1.45 2005/07/22 10:58:09 pmineault Exp $
 */

/**
 * AMFPHP_BASE is the location of the flashservices folder in the files system.
 * It is used as the absolute path to load all other required system classes.
 */
define("AMFPHP_BASE", ROOT_PATH . '/lib/Amfphp/');

/**
 * required classes for the application
 */
require_once(AMFPHP_BASE . "shared/app/Constants.php");
require_once(AMFPHP_BASE . "shared/app/Globals.php");
require_once(AMFPHP_BASE . "shared/util/CompatPhp5.php");
require_once(AMFPHP_BASE . "shared/util/CharsetHandler.php");
require_once(AMFPHP_BASE . "shared/util/NetDebug.php");
require_once(AMFPHP_BASE . "shared/util/Headers.php");
require_once(AMFPHP_BASE . "shared/exception/MessageException.php");
require_once(AMFPHP_BASE . "shared/app/BasicActions.php");
require_once(AMFPHP_BASE . "amf/util/AMFObject.php");
require_once(AMFPHP_BASE . "amf/util/WrapperClasses.php");
require_once(AMFPHP_BASE . "amf/app/Filters.php");
require_once(AMFPHP_BASE . "amf/app/Actions.php");

class Gateway {
	var $_looseMode = false;
	var $_charsetMethod = "none";
	var $_charsetPhp = "";
	var $_charsetSql = "";
	var $exec;
	var $filters;
	var $actions;
	var $outgoingMessagesFolder = NULL;
	var $incomingMessagesFolder = NULL;
	var $useSslFirstMethod = true;
	var $_enableGzipCompression = false;
	var $_body;

	/**
	 * The Gateway constructor method.
	 *
	 * The constructor method initializes the executive object so any configurations
	 * can immediately propogate to the instance.
	 */
	function Gateway() {
		//Include right executive for php version
		//Set gloriously nice error handling
		include_once(AMFPHP_BASE . "shared/app/php5Executive.php");
		include_once(AMFPHP_BASE . "shared/exception/php5Exception.php");

		$this->exec = new Executive();
		$this->filters = array();
		$this->actions = array();
		$this->registerFilterChain();
		$this->registerActionChain();
	}

	/**
	 * Create the chain of filters
	 * Subclass gateway and overwrite to create a custom gateway
	 */
	function registerFilterChain() {
		//filters
		$this->filters['deserial'] = 'deserializationFilter';
		//$this->filters['auth'] = 'authenticationFilter';
		$this->filters['batch'] = 'batchProcessFilter';
		//$this->filters['debug'] = 'debugFilter';
		$this->filters['serialize'] = 'serializationFilter';
	}

	/**
	 * Create the chain of actions
	 * Subclass gateway and overwrite to create a custom gateway
	 */
	function registerActionChain() {
		$this->actions['exec'] = 'executionAction';
	}

	/**
	 * The service method runs the gateway application.  It turns the gateway 'on'.  You
	 * have to call the service method as the last line of the gateway script after all of the
	 * gateway configuration properties have been set.
	 *
	 * Right now the service method also includes a very primitive debugging mode that
	 * just dumps the raw amf input and output to files.  This may change in later versions.
	 * The debugging implementation is NOT thread safe so be aware of file corruptions that
	 * may occur in concurrent environments.
	 */

	function service() {

		//Set the parameters for the charset handler
		CharsetHandler::setMethod($this->_charsetMethod);
		CharsetHandler::setPhpCharset($this->_charsetPhp);
		CharsetHandler::setSqlCharset($this->_charsetSql);

		//Attempt to call charset handler to catch any uninstalled extensions
		$ch = new CharsetHandler('flashtophp');
		$ch->transliterate('?');

		$ch2 = new CharsetHandler('sqltophp');
		$ch2->transliterate('?');

		$GLOBALS['amfphp']['actions'] = $this->actions;

		if (!isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
			$GLOBALS['HTTP_RAW_POST_DATA'] = file_get_contents('php://input');
		}

		if (isset($GLOBALS["HTTP_RAW_POST_DATA"]) && $GLOBALS["HTTP_RAW_POST_DATA"] != "") {
			//Start NetDebug
			NetDebug::initialize();

			error_reporting($GLOBALS['amfphp']['errorLevel']);

			//Enable loose mode if requested
			if ($this->_looseMode) {
				ob_start();
			}

			$amf = new AMFObject($GLOBALS["HTTP_RAW_POST_DATA"]); // create the amf object


			foreach ($this->filters as $key => $filter) {
				$filter($amf); //   invoke the first filter in the chain
			}

			$this->_body = $amf->getBodyAt(0);

			$output = $amf->outputStream; // grab the output stream

			//Clear the current output buffer if requested
			if ($this->_looseMode) {
				ob_end_clean();
			}

			//Send content length header
			//Thanks to Alec Horley for pointing out the necessity
			//of this for FlashComm support
			header(AMFPHP_CONTENT_TYPE); // define the proper header

			if (Headers::getHeader('serviceBrowser') == true) {
				//Add the total time header
				$toAddPos = strpos($output, "\301\260\0\0\1\0\0\0");
				$time = (int)((microtime(1) - $GLOBALS['amfphp']['startTime']) * 1000);
				$b = pack("d", $time); // pack the bytes
				if (AMFPHP_BIG_ENDIAN) { // if we are a big-endian processor
					$r = strrev($b);
				} else { // add the bytes to the output
					$r = $b;
				}
				$output = substr($output, 0, $toAddPos) . $r . substr($output, $toAddPos + 8);
			}

			//Send expire header, apparently helps for SSL
			//Thanks to Gary Rogers for that
			//And also to Lucas Filippi from openAMF list
			//And to Robert Reinhardt who appears to be the first who
			//documented the bug
			//Finally to Gary who appears to have find a solution which works even more reliably
			$dateStr = date("D, j M Y ") . date("H:i:s", strtotime("-2 days"));
			header("Expires: $dateStr GMT");
			header("Pragma: no-store");
			header("Cache-Control: no-store");

			//else don't send any special headers at all

			if ($this->outgoingMessagesFolder != NULL) {
				$mt = microtime();
				$pieces = explode(' ', $mt);
				file_put_contents($this->outgoingMessagesFolder .
					'out.' . $pieces[1] . '.' . substr($pieces[0], 2) . ".amf", $output);
			}

			$doCompress = false;
			$outputCompression = @ini_get("zlib.output_compression");
			if (!$outputCompression) {
				$httpAcceptEncoding = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';
				if (strlen($output) > $this->_gzipCompressionThreshold &&
					extension_loaded("zlib") &&
					strpos($httpAcceptEncoding, 'gzip') !== FALSE &&
					$this->_enableGzipCompression
				) {
					$doCompress = true;
					ob_start();
					ob_start('ob_gzhandler');
				} else {
					header("Content-length: " . strlen($output));
				}
			}

			print($output); // flush the binary data

			if ($doCompress) {
				ob_end_flush();
				header("Content-length: " . ob_get_length());
				ob_end_flush();
			}
		} else {
			echo '1';
			exit;
			echo("<p>amfphp and this gateway are installed correctly. You may now connect " .
				"to this gateway from Flash.</p>");

			if (function_exists("amf_decode")) {
				echo("<p>AMF C Extension is loaded " .
					($GLOBALS['amfphp']['native'] ? "and enabled." : "but disabled") .
					"</p>");
			}
			echo "<p>Note: If you're reading an " .
				"old tutorial, it will tell you that you should see a download " .
				"window instead of this message. This confused people so this is " .
				"the new behaviour starting from amfphp 1.2.</p><p>" .
				"<a href='http://www.amfphp.org/docs'>View the amfphp documentation</p>" .
				"<p><a href='browser'>Load the service browser</a></p>";
			echo "<pre>";
		}
	}

	/**
	 * Setter for error handling
	 *
	 * @param the error handling level
	 */
	function setErrorHandling($level) {
		$GLOBALS['amfphp']['errorLevel'] = $level;
	}

	/**
	 * Sets the base path for loading service methods.
	 *
	 * Call this method to define the directory to look for service classes in.
	 * Relative or full paths are acceptable
	 *
	 * @param string $path The path the the service class directory
	 */
	function setClassPath($value) {
		$path = realpath($value . '/') . '/';
		$GLOBALS['amfphp']['classPath'] = $path;
	}

	/**
	 * Sets the base path for loading service methods.
	 *
	 * Call this method to define the directory to look for service classes in.
	 * Relative or full paths are acceptable
	 *
	 * @param string $path The path the the service class directory
	 */
	function setClassMappingsPath($value) {
		$path = realpath($value . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		$GLOBALS['amfphp']['customMappingsPath'] = $path;
	}

	/**
	 * Sets the loose mode. This will enable outbut buffering
	 * And flushing and set error_reporting to 0. The point is if set to true, a few
	 * of the usual NetConnection.BadVersion error should disappear
	 * Like if you try to echo directly from your function, if you are issued a
	 * warning and such. Errors should still be logged to the error log though.
	 *
	 * @example In gateway.php, before $gateway->service(), use $gateway->setLooseMode(true)
	 * @param bool $mode Enable or disable loose mode
	 */
	function setLooseMode($paramLoose = true) {
		$this->_looseMode = $paramLoose;
	}

	function enableGzipCompression($threshold = 30100) {
		$this->_enableGzipCompression = true;
		$this->_gzipCompressionThreshold = $threshold;
	}

	/**
	 * Sets the charset handler.
	 * The charset handler handles reencoding from and to a specific charset
	 * for PHP and SQL resources.
	 *
	 * @param $method The method used for reencoding, either "none", "iconv" or "runtime"
	 * @param $php The internal encoding that is assumed for PHP (typically ISO-8859-1)
	 * @param $sql The internal encoding that is assumed for SQL resources
	 */
	function setCharsetHandler($method = "none", $php, $sql) {
		$this->_charsetMethod = $method;
		$this->_charsetPhp = $php;
		$this->_charsetSql = $sql;
	}

	/**
	 * disableStandalonePlayer will exit the script (die) if the standalone
	 * player is sees in the User-Agent signature
	 *
	 * @param bool $bool Wheather to disable the Standalone player. Ie desktop player.
	 */
	function disableStandalonePlayer($value = true) {
		if ($value && $_SERVER['HTTP_USER_AGENT'] == "Shockwave Flash") {
			trigger_error("Standalone Flash player disabled. Update gateway.php to allow these connections", E_USER_ERROR);
			die();
		}
	}

	/**
	 * disableTrace will ignore any calls to NetDebug::trace
	 *
	 * @param bool $bool Whether to disable tracing
	 */
	function disableDebug($value = true) {
		$GLOBALS['amfphp']['disableDebug'] = $value;
	}

	/**
	 * Disable native extension will disable the native C extension
	 */
	function disableNativeExtension() {
		$GLOBALS['amfphp']['native'] = false;
	}


	/**
	 * Log incoming messages to the specified folder
	 */
	function logIncomingMessages($folder = NULL) {
		$this->incomingMessagesFolder = realpath($folder) . '/';
	}

	/**
	 * Log outgoing messages to the specified folder
	 */
	function logOutgoingMessages($folder = NULL) {
		$this->outgoingMessagesFolder = realpath($folder) . '/';
	}

	/**
	 * Dumps data to a file
	 *
	 * @param string $filepath The location of the dump file
	 * @param string $data The data to insert into the dump file
	 */
	function _saveRawDataToFile($filepath, $data) {
		if (!$handle = fopen($filepath, 'w')) {
			exit;
		}
		if (!fwrite($handle, $data)) {
			exit;
		}
		fclose($handle);
	}

	/**
	 * Appends data to a file
	 *
	 * @param string $filepath The location of the dump file
	 * @param string $data The data to append to the dump file
	 */
	function _appendRawDataToFile($filepath, $data) {
		$handle = fopen($filepath, 'a');
		fwrite($handle, $data);
		fclose($handle);
	}

	function getBody() {
		return $this->_body;
	}
}

?>
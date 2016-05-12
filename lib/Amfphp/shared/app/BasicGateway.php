<?php
require_once(AMFPHP_BASE . "shared/app/BasicActions.php");
require_once(AMFPHP_BASE . "shared/app/Constants.php");
require_once(AMFPHP_BASE . "shared/app/Globals.php");
require_once(AMFPHP_BASE . "shared/exception/MessageException.php");
include_once(AMFPHP_BASE . "shared/util/CompatPhp5.php");

/**
 * This basic gateway is a base class for all simple RPC types (that is, RPC which doesn't
 * allow batching)
 */
class BasicGateway {
	function BasicGateway() {
		//Set gloriously nice error handling
		include_once(AMFPHP_BASE . "shared/app/php5Executive.php");
		include_once(AMFPHP_BASE . "shared/exception/php5Exception.php");
		$this->registerActionChain();
	}

	/**
	 * Sets the base path for loading service methods.
	 *
	 * Call this method to define the directory to look for service classes in.
	 * Relative or full paths are acceptable
	 *
	 * @param string $path The path the the service class directory
	 */
	function setBaseClassPath($value) {
		$path = realpath($value . '/') . '/';
		$GLOBALS['amfphp']['classPath'] = $path;
	}

	function service() {
		//Process the arguments
		$body = $this->createBody();
		foreach ($this->actions as $key => $action) {
			$result = $action($body); //   invoke the first filter in the chain
			if ($result === false) {
				//Go straight to serialization actions
				$serAction = 'serializationAction';
				$serAction($body);
				break;
			}
		}

		echo $body->getResults();
	}

	/**
	 * Add a class mapping for adapters
	 */
	function addAdapterMapping($key, $value) {
		$GLOBALS['amfphp']['adapterMappings'][$key] = $value;
	}

	/**
	 * This function should overriden by the gateways
	 */
	function createBody() {

	}

	/**
	 * Create the chain of actions
	 * Subclass gateway and overwrite to create a custom gateway
	 */
	function registerActionChain() {

	}
}

?>
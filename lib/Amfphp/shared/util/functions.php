<?php
function __setUri() {
	if (__env('HTTP_X_REWRITE_URL')) {
		$uri = __env('HTTP_X_REWRITE_URL');
	} elseif (__env('REQUEST_URI')) {
		$uri = __env('REQUEST_URI');
	} else {
		if (__env('argv')) {
			$uri = __env('argv');

			if (defined('SERVER_IIS')) {
				$uri = BASE_URL . $uri[0];
			} else {
				$uri = __env('PHP_SELF') . '/' . $uri[0];
			}
		} else {
			$uri = __env('PHP_SELF') . '/' . __env('QUERY_STRING');
		}
	}
	return $uri;
}

function __env($key) {
	if (isset($_SERVER[$key])) {
		return $_SERVER[$key];
	} elseif (isset($_ENV[$key])) {
		return $_ENV[$key];
	} elseif (getenv($key) !== false) {
		return getenv($key);
	}

	if ($key == 'DOCUMENT_ROOT') {
		$offset = 0;
		if (!strpos(__env('SCRIPT_NAME'), '.php')) {
			$offset = 4;
		}
		return substr(__env('SCRIPT_FILENAME'), 0, strlen(__env('SCRIPT_FILENAME')) - (strlen(__env('SCRIPT_NAME')) + $offset));
	}
	if ($key == 'PHP_SELF') {
		return r(__env('DOCUMENT_ROOT'), '', __env('SCRIPT_FILENAME'));
	}
	return null;
}

?>
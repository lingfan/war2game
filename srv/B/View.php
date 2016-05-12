<?php

/**
 *
 * 视图类
 * @author 胡威
 *
 */
class B_View {
	static $pageData = array();

	/**
	 *
	 * 呈现布局模板
	 * @param string $file
	 */
	static public function render($file, $pageData = array()) {
		$content = self::load($file, $pageData);
		include(LIB_V_PATH . '/layout.php');
		exit;
	}

	/**
	 *
	 * 呈现局部模板
	 * @param string $file
	 */
	static public function renderPartail($file, $pageData = array()) {
		echo self::load($file);
		exit;
	}

	/**
	 *
	 * 传递视图模板变量
	 * @param string $name
	 * @param string /array $value
	 */
	static public function setVal($name, $value) {
		self::$pageData[$name] = $value;
	}

	/**
	 *
	 * 获取视图模板变量
	 * @param string $name
	 */
	static public function getVal($name) {
		return isset(self::$pageData[$name]) ? self::$pageData[$name] : '';
	}

	/**
	 *
	 * 加载局部模板
	 * @param string $file
	 */
	static public function load($file, $pageData = array()) {
		$viewFile = LIB_V_PATH . '/' . $file . '.php';
		if (is_readable($viewFile)) {
			ob_start();
			if (!empty($pageData) && is_array($pageData)) {
				extract($pageData);
			}
			include($viewFile);

			$content = ob_get_contents();
			ob_end_clean();

			return $content;
		} else {
			die("{$viewFile} 模板文件不存在");
		}

	}

	static public function getData($file) {
		$viewDataFile = LIB_V_PATH . '/' . $file . '.php';
		$viewData = require_once $viewDataFile;
		return $viewData;
	}

	static public function layout() {

	}
}
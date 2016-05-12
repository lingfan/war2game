<?php

class C_Index extends C_I {
	/**
	 * 首页
	 * @author huwei
	 */
	public function AIndex() {
		M_Auth::render($_GET['ssid'],1,1);
		exit;
	}

	public function AViewreport() {
		$args = array(
			'vk' => FILTER_SANITIZE_STRING,
			'cid' => FILTER_SANITIZE_STRING,
		);
		$formVals = filter_var_array($_REQUEST, $args);
		if (!empty($formVals['vk'])) {
			$pageData['server_res_url'] = M_Config::getSvrCfg('server_res_url');
			$pageData['server_title'] = M_Config::getSvrCfg('server_title');
			$domain = B_Utils::getHost();
			$pageData['domain'] = $domain;
			$pageData['sid'] = 1;
			$pageData['resXmlFile'] = str_replace('.', '_', $domain) . '.xml';
			$pageData['ver'] = ETC_NO;
			$pageData['val'] = $formVals['vk'];
			B_View::render('report', $pageData);
			exit;
		} else {
			headers_sent() OR header('HTTP/1.0 404 Page Not Found!!!');
			exit;
		}
		return true;
	}
}

?>
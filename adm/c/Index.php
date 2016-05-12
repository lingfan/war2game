<?php

class C_Index {
	static public function AIndex() {
		$arr = array();
		B_View::render('index', $arr);
	}

	static public function ALogin() {
		$pageData = array();
		B_View::render('Index/Login', $pageData);
	}

	static public function ACheckLogin() {
		$pageData = array();

		$username = isset($_REQUEST['username']) ? trim($_REQUEST['username']) : '';
		$password = isset($_REQUEST['password']) ? trim($_REQUEST['password']) : '';
		if (!empty($username) && !empty($password)) {
			$ret = B_DB::instance('AdmUser')->verifyLogin($username, md5($password));
			if ($ret) {
				M_Adm::setLoginInfo($ret);
				B_Common::redirect('?r=Index/Index');
			}
		}
		B_Common::redirect('?r=Index/Login');
	}

	static public function ALogout() {
		M_Adm::delLoginInfo();
		B_Common::redirect('?r=Index/Login');
	}
}

?>
<?php

class C_User {
	static public function AInit() {
		if (!M_Adm::isLogin()) {
			B_Common::redirect('?r=Index/Login');
		}
	}

	static public function AIndex() {
		B_Common::redirect('?r=User/List');
	}

	public function AList() {
		$args = array(
			'page' => FILTER_SANITIZE_NUMBER_INT,
		);
		$formVals = filter_var_array($_REQUEST, $args);
		$offset = 25;
		$curPage = max(1, $formVals['page']);

		$pageData['list'] = B_DB::instance('City')->getPage($curPage, $offset);
		$totalNum = B_DB::instance('City')->total();
		$pageData['page'] = B_Page::make($curPage, $totalNum, $offset, 20);

		B_View::setVal('pageData', $pageData);
		B_View::render('User/List');
	}

	public function AAdd() {
		$args = array(
			'id' => FILTER_SANITIZE_NUMBER_INT,
		);
		$formVals = filter_var_array($_REQUEST, $args);

		$pageData['info'] = B_DB::instance('User')->get($formVals['id']);

		B_View::setVal('pageData', $pageData);
		B_View::render('User/Add');
	}

	public function ADel() {

		B_Common::redirect('?r=user/list');
	}
}

?>
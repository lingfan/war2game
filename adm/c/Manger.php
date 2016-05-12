<?php

/**
 * 管理员控制器
 */
class C_Manger {
	static public function AInit() {
		if (!M_Adm::isLogin()) {
			B_Common::redirect('?r=Index/Login');
		}
	}

	static public function AIndex() {
		B_Common::redirect('?r=Manger/List');
	}


	static public function AList() {
		$args = array(
			'page' => FILTER_SANITIZE_NUMBER_INT,
		);
		$formVals = filter_var_array($_REQUEST, $args);
		$offset = 25;
		$curPage = max(1, $formVals['page']);
		$pageData['list'] = B_DB::instance('AdmUser')->getRowsByPage($curPage, $offset);
		$totalNum = B_DB::instance('AdmUser')->count();
		$pageData['page'] = B_Page::make($curPage, $totalNum, $offset, 10);

		B_View::setVal('pageData', $pageData);
		B_View::render('Manger/List');
	}

	static public function ADel() {
		$ret = false;
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		$id = intval($id);
		if ($id > 0) {
			if ($id == 1) {
				echo "<script>alert('该用户不能被删除!');</script>";
				exit;
			}
			$ret = B_DB::instance('AdmUser')->delete($id);
		}
		echo $ret ? "<script>alert('操作成功!');window.top.location='?r=Manger/List';</script>" : "<script>alert('操作失败!');</script>";
	}

	static public function AUpdate() {
		$ret = false;
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		if ($id > 0) {
			$username = $_REQUEST['username'];
			$oldpwd = trim($_REQUEST['oldpwd']);
			$password2 = trim($_REQUEST['password2']);
			$password = trim($_REQUEST['password']);

			//verifyLogin
			if (!empty($password)) {
				if ($password == $password2) {
					if (B_DB::instance('AdmUser')->verifyLogin($username, md5($oldpwd))) {
						$ret = B_DB::instance('AdmUser')->update(array('password' => md5($password)), $id);
					} else {
						echo "<script>alert('原始密码错误!');</script>";
						exit;
					}
				} else {
					echo "<script>alert('两次密码不一致!');</script>";
					exit;
				}
			} else {
				echo "<script>alert('密码不能为空!');</script>";
				exit;
			}
		} else {
			$username = trim($_REQUEST['username']);
			$nickname = trim($_REQUEST['nickname']);
			$password = trim($_REQUEST['password']);
			$group_id = $_REQUEST['group_id'];
			if (!$username || !$nickname || !$password || !$group_id) {
				echo "<script>alert('信息不完整!');</script>";
				exit;
			} else {
				$username = B_DB::instance('AdmUser')->getBy(array('username' => $username));

				if (isset($row['id'])) {
					echo "<script>alert('用户名已被使用!');</script>";
					exit;
				} else {
					$info = array(
						'nickname' => $nickname,
						'username' => $username,
						'password' => md5($password),
						'group_id' => $group_id,
						'create_at' => time()
					);
					$ret = B_DB::instance('AdmUser')->insert($info);
				}
			}
		}

		echo $ret ? "<script>alert('操作成功!');</script>" : "<script>alert('操作失败!');</script>";
	}


	static public function AGMUser() {
		if (!empty($_POST['gm_user'])) {
			$data = array('gm_user' => $_POST['gm_user']);

			$ret = M_Config::setVal($data);

			echo $ret ? "<script>alert('保存成功');</script>" : "<script>alert('保存失败');</script>";
			exit;
		}
		B_View::render('Manger/GMUser');
	}

	static public function ADebugIp() {
		if (!empty($_POST['debug_ip'])) {

			$data = array('debug_ip' => $_POST['debug_ip']);
			$ret = M_Config::setVal($data);

			echo $ret ? "<script>alert('保存成功');</script>" : "<script>alert('保存失败');</script>";
			exit;
		}
		B_View::render('Manger/DebugIp');
	}

}
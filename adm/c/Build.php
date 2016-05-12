<?php

class C_Build {
	static public function AInit() {
		if (!M_Adm::isLogin()) {
			B_Common::redirect('?r=Index/Login');
		}
	}

	static public function AIndex() {
		B_View::render('index');
	}

	static public function ASetArea() {
		$flag = 0;
		$id = intval($_REQUEST['id']);
		$areaVal = trim($_REQUEST['area']);
		$zhou = intval($_REQUEST['zhou']);
		if ($id > 0 && $zhou > 0 && $areaVal) {
			$info = B_DB::instance('BaseBuild')->getOne($id);
			$oldArea = json_decode($info['area'], true);
			$oldArea[$zhou] = $areaVal;
			$newArea = json_encode($oldArea);
			//占地区域:(208,112,32,0,0,0,0,0)
			$res = B_DB::instance('BaseBuild')->update(array('area' => $newArea), $id);
			$flag = $res ? 1 : 0;
		}
		$json['flag'] = $flag;
		$json['data'] = array(
			'id' => $id,
			'area' => $area
		);
		M_Build::delBuildBaseCache();
		echo json_encode($json);

	}
}

?>
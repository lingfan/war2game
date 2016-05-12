<?php

class A_Server_Consumer {
	static public function Info($params) {
		$cid = isset($params['consumer_id']) ? $params['consumer_id'] : 0;
		$info = M_Consumer::getById($cid);
		if (!empty($info)) {
			$cid = $info['id'];
		}
		if (empty($cid)) {
			Logger::error("Error Consumer Name#" . json_encode($params));
		}
		return $cid;
	}

	static public function getConsumerList() {
		M_Consumer::clean();
		$info = M_Consumer::getList();
		return $info;
	}

	static public function delConsumer($params) {
		$ret = false;
		if (!empty($params['cid'])) {
			$ret = B_DB::instance('AdmConsumer')->delete($params['cid']);
			Logger::debug(array("Del Consumer", array($params, $ret)));
			if ($ret) {
				M_Consumer::clean();
			}
		}
		return $ret;
	}

	static public function addConsumer($params) {
		$ret = false;
		if (!empty($params['id']) &&
			!empty($params['name']) &&
			!empty($params['key'])
		) {
			$params['create_at'] = time();
			$ret = B_DB::instance('AdmConsumer')->insert($params);
			Logger::debug(array("Add Consumer", $params));
			if ($ret) {
				M_Consumer::clean();
			}
		}
		return $ret;

	}
}

?>
<?php

class C_Question extends C_I {
	public function AInfo() {

		$now = time();
		$objPlayer = $this->objPlayer;

		$info = $objPlayer->Answer()->info();
		$questionInfo = M_Base::answer($info['curId']);

		$openArr = M_Config::getVal('question_open');
		$open = 0;
		if ($now >= strtotime($openArr[0]) && $now <= strtotime($openArr[1])) {
			$open = 1;
		}

		$errNo = '';
		$cost = M_Config::getVal('question_cost');
		$data = array(
			'MaxNum' => M_Config::getVal('question_num'),
			'Point' => intval($info['point']),
			'Times' => max($cost[0] - $info['times'], 0),
			'NextCost' => M_Formula::stepCost($cost, $info['times'] + 1),
			'QuestionNo' => $info['curNum'],
			'Title' => isset($questionInfo['title']) ? $questionInfo['title'] : '',
			'Answer' => isset($questionInfo['answer']) ? $questionInfo['answer'] : '',
			'EndTime' => $info['endTime'],
			'Open' => $open,
		);

		$objPlayer->save();

		return B_Common::result($errNo, $data);
	}

	public function AStart() {
		$objPlayer = $this->objPlayer;
		$info = $objPlayer->Answer()->info();

		if (!empty($info['endTime'])) {
			return B_Common::result(T_ErrNo::QUESTION_HAD_START);
		} else if (empty($info['curId'])) {
			return B_Common::result(T_ErrNo::QUESTION_ANSWER_EMPTY);
		}

		$endTime = $objPlayer->Answer()->start();

		$data = array(
			'EndTime' => $endTime,
		);

		$objPlayer->save();

		return B_Common::result('', $data);
	}

	public function AAnswer($str) {
		$objPlayer = $this->objPlayer;
		$info = $objPlayer->Answer()->info();
		$questionInfo = M_Base::answer($info['curId']);

		if (empty($info['curId'])) {
			return B_Common::result(T_ErrNo::QUESTION_ANSWER_EMPTY);
		} else if (empty($info['cur_endtime'])) {
			return B_Common::result(T_ErrNo::QUESTION_NOT_START);
		} else if (empty($questionInfo['id'])) {
			return B_Common::result(T_ErrNo::QUESTION_INFO_EMPTY);
		}

		//下一个问题
		$flag = $objPlayer->Answer()->next($str);
		$newInfo = $objPlayer->Answer()->info();
		$nextInfo = M_Base::answer($newInfo['curId']);
		$data = array(
			'Flag' => $flag,
			'Result' => explode(",", $questionInfo['result']),
			'Point' => $newInfo['point'],
			'QuestionNo' => $newInfo['curNum'],
			'Title' => isset($nextInfo['title']) ? $nextInfo['title'] : '',
			'Answer' => isset($nextInfo['answer']) ? $nextInfo['answer'] : '',
			'EndTime' => $newInfo['endTime'],
		);
		$objPlayer->save();

		return B_Common::result('', $data);
	}

	public function ANew() {

		$objPlayer = $this->objPlayer;


		$questionId = $objPlayer->Answer()->resetId();
		$questionInfo = M_Base::answer($questionId);
		if (empty($questionInfo['id'])) {
			return B_Common::result(T_ErrNo::QUESTION_INFO_EMPTY);
		}

		$info = $objPlayer->Answer()->info();

		$curCost = M_Formula::stepCost(M_Config::getVal('question_cost'), $info['times']);
		$objPlayer->City()->mil_pay -= $curCost;
		if ($objPlayer->City()->mil_pay < 0) {
			return B_Common::result(T_ErrNo::NO_ENOUGH_MILIPAY);
		}

		$cost = M_Config::getVal('question_cost');
		$data = array(
			'Times' => max($cost[0] - $info['times'], 0),
			'NextCost' => M_Formula::stepCost($cost, $info['times'] + 1),
			'QuestionNo' => $info['curNum'],
			'Title' => isset($questionInfo['title']) ? $questionInfo['title'] : '',
			'Answer' => isset($questionInfo['answer']) ? $questionInfo['answer'] : '',
			'EndTime' => $info['endTime'],
		);
		$objPlayer->save();


		return B_Common::result('', $data);
	}

	public function AExchange($id, $num = 1) {
		$objPlayer = $this->objPlayer;
		$id = intval($id);
		$num = intval($num);
		if (empty($id) || empty($num)) {
			return B_Common::result(T_ErrNo::ERR_PARAM);
		}

		$baseInfo = M_Config::getVal('question_props');
		$costPoint = 0;
		if (isset($baseInfo[$id])) {
			$costPoint = $baseInfo[$id] * $num;
		}

		$leftPoint = $objPlayer->Answer()->decrPoint($costPoint);
		if ($leftPoint < 0) {
			return B_Common::result(T_ErrNo::NO_ENOUGH_QUESTION_POINT);
		}

		$objPlayer->Pack()->incr($id, $num);

		$data = array(
			'Point' => $leftPoint,
		);

		$objPlayer->save();

		return B_Common::result('', $data);

	}

	public function AShop() {
		$list = M_Config::getVal('question_props');
		$errNo = '';
		$data = array();
		foreach ($list as $id => $point) {
			$data[] = array($id, $point);
		}
		return B_Common::result($errNo, $data);
	}
}

?>
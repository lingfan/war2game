<?php

class C_Quest extends C_I {
	/**
	 * 新手引导基础信息
	 * @author huwei 20120801
	 */
	public function ABase() {
		return B_Common::result('', array());
	}

	/**
	 * 玩家新手引导信息
	 * @author huwei 20120801
	 */
	public function AList() {
		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$objQuest = $objPlayer->Quest();
		$list = $objQuest->get();
		$qList = array();
		foreach ($list as $qId => $qVal) {
			$qInfo = M_Base::questInfo($qId);
			if (!empty($qInfo['id'])) {
				//array(完成条件, 已完成数量, 是否完成)
				list($ruleArr, $tmpVal, $isOk) = $qVal;

				$qList[] = array(
					'Id' => $qId,
					'Name' => $qInfo['name'],
					'Desc' => $qInfo['desc'],
					'DescArgs' => array($tmpVal),
					'IsOk' => $isOk,
					'Event' => $qInfo['event'],
				);
			} else {
				Logger::error(array(__METHOD__, 'err quest id', $qId));
			}
		}

		$errNo = '';
		$data['QuestList'] = $qList;


		return B_Common::result($errNo, $data);
	}

	/**
	 * 玩家完成引导操作
	 * @author huwei 20120801
	 */
	public function AFinsh($questId = 0) {
		return array();
		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$objQuest = $objPlayer->Quest();
		$list = $objQuest->get();
		$base = $objQuest->getBaseTree();
		if (!isset($list[$questId])) {
			$errNo = T_ErrNo::QUEST_NOT_EXIST;
		} else if ($list[$questId][2] != 1) {
			$errNo = T_ErrNo::QUEST_NOT_COMPLETE;
		} else {
			$bUp = $objQuest->finish($questId);
			$qInfo = $base['info'][$questId];
			if ($bUp) {
				//获取奖励
				$awardArr = M_Award::rateResult($qInfo['award_id']);
				$award = M_Award::toText($awardArr);
				$bAward = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Task); //奖励结果

				$objPlayer->save();
				$errNo = '';
				$qList = array();

				$data['Award'] = $award;
				$data['QuestList'] = $qList;
			}
		}

		return B_Common::result($errNo, $data);
	}
}

?>
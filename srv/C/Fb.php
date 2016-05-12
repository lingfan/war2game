<?php

/**
 * 副本接口
 */
class C_Fb extends C_I {
	/**
	 * 获取副本战役数据
	 * @author HeJunyun
	 * @param int $chapterNo 章节编号
	 * @param int $campaignNo 战役编号
	 */
	public function ACampaignInfo($chapterNo = 0, $campaignNo = 0) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$chapterNo = intval($chapterNo);
		$campaignNo = intval($campaignNo);

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if ($chapterNo && $campaignNo) {
			if (intval($chapterNo) && intval($campaignNo)) {
				$info = M_SoloFB::getDetail($chapterNo, $campaignNo);
				if ($info) {
					$mapShow = array();
					for($i=1;$i<=12;$i++) {
						$mapShow['_Guanka_'.$i] = $i;
					}
					
					$data = array(
						'ID' => $info['id'],
						'Name' => $info['name'],
						'Level' => $info['level'],
						'Desc' => $info['desc'],
						'CheckpointData' => $info['checkpoint_data'],
						'MapShow' => $mapShow,
						'Award' => $info['award'],
					);
					$flag = T_App::SUCC;
					$errNo = '';
				}
			}
		}

		return B_Common::result($errNo, $data);

	}

	/**
	 * 根据章节ID获取章节信息
	 * @author HeJunyun
	 * @param int $chapterNo 章节编号
	 */
	public function AChapterInfo($chapterNo = 0) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$chapterId = intval($chapterNo);

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if ($chapterId) {
			$chapter = M_SoloFB::getInfo($chapterId);
			if ($chapter) {
				$tmpArr = array();
				$totalChapter = M_SoloFB::totalChapter(); //章节总数
				foreach ($chapter['fb_list'] as $key => $val) {
					$tmpArr[] = array(
						'No' => $key,
						'Name' => $val['name'],
						'Desc' => $val['desc'],
						'Level' => $val['level'],
						'CheckpointNum' => count($val['checkpoint_data']),
						'Award' => $val['award'],
					);
				}

				$data = array(
					'ID' => $chapter['id'],
					'Name' => $chapter['name'],
					'Desc' => $chapter['desc'],
					'TotalChapterNum' => $totalChapter,
					'FbList' => $tmpArr,
				);

				$flag = T_App::SUCC;
				$errNo = '';
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 出征副本
	 * @author huwei
	 * @param string $fbStr (章节编号,战役编号,关卡编号)
	 * @param string $heroIdList 英雄列表 (id,id,id)
	 * @param int $isAutoFight 是否自动操作
	 * @return array
	 */
	public function AAtk($fbStr = '', $heroIdList = '', $isAutoFight = M_War::FIGHT_TYPE_AUTO) {
		$errNo = T_ErrNo::ERR_ACTION;

		$data = array();

		$heroConf = M_Config::getVal();
		$fbArr = !empty($fbStr) ? explode(',', $fbStr) : array();
		$attHeroIdArr = !empty($heroIdList) ? explode(',', $heroIdList) : array();
		$heroNum = count($attHeroIdArr);

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (in_array($isAutoFight, array(M_War::FIGHT_TYPE_HAND, M_War::FIGHT_TYPE_AUTO, M_War::FIGHT_TYPE_QUICK)) &&
			count($fbArr) == 3 && //检查坐标是否有(章节编号,战役编号,关卡编号)
			!empty($fbArr[0]) &&
			!empty($fbArr[1]) &&
			!empty($fbArr[2]) &&
			$heroNum > 0 &&
			$heroNum < $heroConf['hero_num_troop'] + 1
		) //检查英雄数量是否正确
		{
			list($chapterNo, $campaignNo, $pointNo) = $fbArr;
			//将要攻击的副本编号
			$atkFBNo = M_Formula::calcFBNo($chapterNo, $campaignNo, $pointNo);
			//检测最后副本关卡是否通过
			$lastPassFbNo = !empty($cityInfo['last_fb_no']) ? $cityInfo['last_fb_no'] : M_Formula::calcFBNo(1, 1, 0);

			$nextFBNo = M_SoloFB::calcNextFBNo($lastPassFbNo);

			//扣除军令
			$objPlayer->City()->mil_order -= T_App::MARCH_FB_COST_MILORDER;
			$fbOK = false;
			if (!empty($atkFBNo)) {
				if ($atkFBNo > $lastPassFbNo && $atkFBNo == $nextFBNo) {
					$fbOK = true;
				} else if ($atkFBNo <= $lastPassFbNo) {
					$fbOK = true;
				}
			}

			$err = '';
			$objCD = $objPlayer->CD();
			$cdIdx = $objCD->getFreeIdx(O_CD::TYPE_FB);
			if (!$fbOK) {
				$err = T_ErrNo::MARCH_NO_FB_POINT;
			} else if (!M_Hero::checkHeroStatus($cityInfo['id'], $attHeroIdArr)) //检测英雄是否空闲 或 不存在 此英雄
			{
				$err = T_ErrNo::HERO_EXIST_FIGHT;
			} else if (!empty($cityInfo['fb_battle_id'])) //是否在副本战斗中
			{
				$err = T_ErrNo::MARCH_FB_WAR_EXIST;
			} else if ($objPlayer->City()->mil_order < 0) //军令是否足够
			{
				$err = T_ErrNo::NO_ENOUGH_MILORDER;
			} else if ($isAutoFight == M_War::FIGHT_TYPE_QUICK && !$cdIdx) {
				$err = T_ErrNo::FB_CD_TIME_LOCK;
			}

			if (empty($err)) {
				//触发战斗
				//构建战斗数据
				$bData = M_War::buildFBWarBattleData($cityInfo['id'], $cityInfo['pos_no'], $atkFBNo, $attHeroIdArr, $isAutoFight);
				//插入战斗队列
				$battleId = M_War::insertWarBattle($bData, $isAutoFight);
				if ($battleId) {

					if ($isAutoFight == M_War::FIGHT_TYPE_QUICK) {
						$objCD->set(O_CD::TYPE_FB, $cdIdx, T_Battle::QUICK_TIME);
						$cdFB = $objCD->toFront(O_CD::TYPE_FB);
						M_City::syncCDFB2Front($cityInfo['id'], $cdFB[0], $cdFB[1]); //同步武器CD时间
					} else { //改变英雄状态为战斗中
						$objPlayer->City()->fb_battle_id = $battleId;
						M_Hero::changeHeroFlag($cityInfo['id'], $attHeroIdArr, T_Hero::FLAG_WAR);
					}

					$objPlayer->save();

					$errNo = '';
					$data = array('BattleId' => $battleId);

				} else {
					$errNo = T_ErrNo::BATTLE_DATA_ERR;
				}
			} else {
				$errNo = $err;
			}
		}

		return B_Common::result($errNo, $data);

	}

	/**
	 * @see战役排行
	 */
	public function AFbRanking($fbStr) {
		$fbArr = !empty($fbStr) ? explode(',', $fbStr) : array();

		$errNo = T_ErrNo::ERR_ACTION;
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$data = array();

		if (!empty($fbArr[0]) && !empty($fbArr[1]) && !empty($fbArr[2])
		) //检查英雄数量是否正确
		{
			$fbNo = M_Formula::calcFBNo($fbArr[0], $fbArr[1], $fbArr[2]);
			$list = M_Ranking::getFBPass($fbNo);

			$data = !empty($list) ? $list : array();
			$flag = T_App::SUCC;
			$errNo = '';
		}

		return B_Common::result($errNo, $data);
	}
}

?>
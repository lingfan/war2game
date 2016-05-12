<?php

class M_SoloFB {
	/**
	 * 获取副本章节信息
	 * @author Hejunyun
	 * @param int $chapterId 章节ID
	 * @return array
	 */
	static public function getInfo($chapterId) {
		static $info = array();
		if (empty($info[$chapterId])) {
			$apcKey  = T_Key::WAR_FB_CHAPTER . '_' . $chapterId;
			$tmpInfo = B_Cache_APC::get($apcKey);
			if (empty($tmpInfo)) {
				$list    = M_Base::solofbAll();
				$tmpInfo = isset($list[$chapterId]) ? $list[$chapterId] : array();
				APC::set($apcKey, $tmpInfo);
			}
			$info[$chapterId] = $tmpInfo;
		}

		return isset($info[$chapterId]) ? $info[$chapterId] : array();
	}

	/**
	 * 获取章节总数
	 * @author Hejunyun
	 * @return int/bool
	 */
	static public function totalChapter($reload = false) {
		$list = M_Base::solofbAll($reload);
		$num  = count($list);

		return $num;
	}

	static public function showFBNo($fbNo) {
		list($chapterNo, $campaignNo, $pointNo) = M_Formula::calcParseFBNo($fbNo);
		$total = M_SoloFB::totalChapter();
		if ($chapterNo > $total) {
			$chapterInfo = M_SoloFB::getInfo($total);
			$campaignNo  = count($chapterInfo['fb_list']);
			$pointNo     = count($chapterInfo['fb_list'][$campaignNo]['checkpoint_data']);
			$fbNo        = M_Formula::calcFBNo($total, $campaignNo, $pointNo);
		}
		return $fbNo;
	}

	/**
	 * 计算下个副本编号
	 * @param int $chapterNo
	 * @param int $campaignNo
	 * @param int $pointNo
	 */
	static public function calcNextFBNo($fbNo) {
		list($chapterNo, $campaignNo, $pointNo) = M_Formula::calcParseFBNo($fbNo);
		$nextFBNo       = false;
		$nextChapterNo  = max($chapterNo + 1, 1);
		$nextCampaignNo = max($campaignNo + 1, 1);
		$nextPointNo    = max($pointNo + 1, 1);

		$chapterInfo = M_SoloFB::getInfo($chapterNo);
		if (isset($chapterInfo['fb_list'][$campaignNo]['checkpoint_data'][$pointNo + 1])) {
			$nextChapterNo  = $chapterNo;
			$nextCampaignNo = $campaignNo;
			$nextPointNo    = $pointNo + 1;
		} elseif (isset($chapterInfo['fb_list'][$campaignNo + 1]['checkpoint_data'][1])) {
			$nextChapterNo  = $chapterNo;
			$nextCampaignNo = $campaignNo + 1;
			$nextPointNo    = 1;
		} else {
			$nextChapterNo  = $chapterNo + 1;
			$nextCampaignNo = 1;
			$nextPointNo    = 1;
		}
		$nextFBNo = M_Formula::calcFBNo($nextChapterNo, $nextCampaignNo, $nextPointNo);
		return $nextFBNo;
	}

	/**
	 * 获取副本战斗场景
	 * @author huwei on 20010602
	 * @param int $chapterNo 章节编号
	 * @param int $campaignNo 战役编号
	 * @param int $pointNo 关卡编号
	 * @return array [副本编号,副本名称,限制等级,关卡数据...]
	 */
	static public function getDetail($chapterNo, $campaignNo, $pointNo = 0) {
		$list = M_SoloFB::getInfo($chapterNo);
		$data = array();
		if (isset($list['fb_list'][$campaignNo])) {
			if ($pointNo > 0) {
				$data = $list['fb_list'][$campaignNo]['checkpoint_data'][$pointNo];

				if (!empty($list['fb_list'][$campaignNo]['checkpoint_data'][$pointNo])) {
					$data    = $list['fb_list'][$campaignNo]['checkpoint_data'][$pointNo];
					$npcId   = $data[4];
					$npcInfo = M_NPC::getInfo($npcId);

					$awardArr = M_Award::allResult($npcInfo['award_id']);
					$data[]   = M_Award::toText($awardArr);
				}

			} else {
				$data       = $list['fb_list'][$campaignNo];
				$weaponList = M_Base::weaponAll();
				foreach ($data['checkpoint_data'] as $key => $val) {
					$npcId                           = $data['checkpoint_data'][$key][4];
					$npcInfo                         = M_NPC::getInfo($npcId);
					$awardArr                        = M_Award::allResult($npcInfo['award_id']);
					$awardInfo                       = M_Base::award($npcInfo['award_id']);
					$face_id                         = $npcInfo['face_id'];
					$data['checkpoint_data'][$key][] = $awardInfo['desc'];
					$data['checkpoint_data'][$key][] = $face_id;
					$data['checkpoint_data'][$key][] = M_Award::toText($awardArr);
					$npcHeroList                     = json_decode($npcInfo['army_data'], true);

					$tmp = array();
					foreach ($npcHeroList as $npcHeroId) {
						$tmpNpcHeroInfo = M_NPC::getNpcHeroInfo($npcHeroId);
						$weaponInfo     = $weaponList[$tmpNpcHeroInfo['weapon_id']];
						$tmp[]          = array(
							'nickname'    => $tmpNpcHeroInfo['nickname'],
							'face_id'     => $tmpNpcHeroInfo['face_id'],
							'army_id'     => $tmpNpcHeroInfo['army_id'],
							'weapon_id'   => $tmpNpcHeroInfo['weapon_id'],
							'army_num'    => $tmpNpcHeroInfo['army_num'],
							'level'       => $tmpNpcHeroInfo['level'],
							'weapon_name' => $weaponInfo['name'],
						);
					}
					$data['checkpoint_data'][$key][] = $tmp;

				}
			}
		}
		return $data;
	}

}

?>
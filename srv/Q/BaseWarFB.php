<?php

class Q_BaseWarFB extends B_DB_Dao {
	protected $_name = 'base_war_fb';
	protected $_connType = 'base';
	protected $_primary = 'id';

	/**
	 * 根据章节编号获取章节下所有战役
	 * @author Hejunyun
	 * @param int $chapter_no 章节编号
	 * @return array $rows
	 */
	public function getListByChapter($chapter_no) {
		$chapter_no = intval($chapter_no);
		$rows = $this->getsBy(array('chapter_no' => $chapter_no));
		return $rows;

	}

	/**
	 * 获取副本数据
	 * @author huwei
	 * @param int $chapterNo 章节编号
	 * @param int $campaignNo 战役编号
	 * @return int
	 */
	public function getInfoByCC($chapterNo, $campaignNo) {
		$row = $this->getsBy(array('campaign_no' => $campaignNo, 'chapter_no' => $chapterNo));
		return $row;

	}

}

?>
<?php

class Q_CityHero extends B_DB_Dao {
	protected $_name = 'city_hero';
	protected $_connType = 'game';
	protected $_primary = 'id';

	/**
	 * 军官排行(等级)
	 * @author HeJunyun
	 */
	public function getHeroRankings() {
		$limit = '';
		if (M_Ranking::CITY_TOTAL_LIMIT != 0) {
			$limit = ' LIMIT ' . M_Ranking::CITY_TOTAL_LIMIT;
		}

		$sql = "
		SELECT id AS ID, city_id AS CityId, nickname AS NickName, `level` AS `Level`, attr_lead AS AttrLead, attr_command AS AttrCommand, attr_military AS AttrMilitary, win_num AS WinNum, fail_num AS FailNum
		FROM city_hero where city_id > 0
		ORDER BY `level` DESC, win_num DESC {$limit}";

		if ('tw' == ETC_NO) {
			$visitTime = max(time() - T_App::ONE_DAY * M_Ranking::DAY_NO_LOGIN, 0);
			$sql = "
			SELECT h.id AS ID, h.city_id AS CityId, h.nickname AS NickName, h.`level` AS `Level`, h.attr_lead AS AttrLead, h.attr_command AS AttrCommand, h.attr_military AS AttrMilitary, h.win_num AS WinNum, h.fail_num AS FailNum
			FROM `city` c LEFT JOIN `user` u ON c.user_id = u.id LEFT JOIN city_hero h ON h.city_id = c.id WHERE h.city_id > 0 AND u.last_visit_time > {$visitTime}
			ORDER BY h.`level` DESC, h.win_num DESC {$limit} ";
		}

		$rows = $this->fetchAll($sql);
		return $rows;
	}

	/**
	 * 军官排行(统帅)
	 * @author HeJunyun
	 */
	public function getHeroRankingsByLead() {
		$limit = '';
		if (M_Ranking::CITY_TOTAL_LIMIT != 0) {
			$limit = ' LIMIT ' . M_Ranking::CITY_TOTAL_LIMIT;
		}

		$sql = "
		SELECT id AS ID, city_id AS CityId, nickname AS NickName, `level` AS `Level`, attr_lead AS AttrLead, attr_command AS AttrCommand, attr_military AS AttrMilitary, win_num AS WinNum, fail_num AS FailNum
		FROM city_hero
		ORDER BY attr_lead DESC, `level` DESC, win_num DESC {$limit}";

		$rows = $this->fetchAll($sql);
		return $rows;
	}

	/**
	 * 军官排行(指挥)
	 * @author HeJunyun
	 */
	public function getHeroRankingsByCommand() {
		$limit = '';
		if (M_Ranking::CITY_TOTAL_LIMIT != 0) {
			$limit = ' LIMIT ' . M_Ranking::CITY_TOTAL_LIMIT;
		}

		$sql = "
		SELECT id AS ID, city_id AS CityId, nickname AS NickName, `level` AS `Level`, attr_lead AS AttrLead, attr_command AS AttrCommand, attr_military AS AttrMilitary, win_num AS WinNum, fail_num AS FailNum
		FROM city_hero
		ORDER BY attr_command DESC, `level` DESC, win_num DESC {$limit}";

		$rows = $this->fetchAll($sql);
		return $rows;
	}

	/**
	 * 军官排行(军事)
	 * @author HeJunyun
	 */
	public function getHeroRankingsByMilitary() {
		$limit = '';
		if (M_Ranking::CITY_TOTAL_LIMIT != 0) {
			$limit = ' LIMIT ' . M_Ranking::CITY_TOTAL_LIMIT;
		}

		$sql = "
		SELECT id AS ID, city_id AS CityId, nickname AS NickName, `level` AS `Level`, attr_lead AS AttrLead, attr_command AS AttrCommand, attr_military AS AttrMilitary, win_num AS WinNum, fail_num AS FailNum
		FROM city_hero
		ORDER BY attr_military DESC, `level` DESC, win_num DESC {$limit}";

		$rows = $this->fetchAll($sql);
		return $rows;
	}

	/**
	 * 军官排行(胜利)
	 * @author HeJunyun
	 */
	public function getHeroRankingsByWin() {
		$limit = '';
		if (M_Ranking::CITY_TOTAL_LIMIT != 0) {
			$limit = ' LIMIT ' . M_Ranking::CITY_TOTAL_LIMIT;
		}

		$sql = "
		SELECT id AS ID, city_id AS CityId, nickname AS NickName, `level` AS `Level`, attr_lead AS AttrLead, attr_command AS AttrCommand, attr_military AS AttrMilitary, win_num AS WinNum, fail_num AS FailNum
		FROM city_hero
		ORDER BY win_num DESC, `level` DESC {$limit}";

		$rows = $this->fetchAll($sql);
		return $rows;
	}

}
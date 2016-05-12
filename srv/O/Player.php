<?php

class O_Player {
	protected $_cityId = 0;
	protected $cityBase = array();
	protected $cityExtra = array();
	protected $obj = array();

	public $changeBase = array();
	public $changeExtra = array();
	protected $sync = array();


	static $ExtraField = array(
		'Team' => 'team_list',
		'Quest' => 'quest_list',
		'Quest' => 'quest_list',
		'Weapon' => 'weapon_list',
		'Army' => 'army_list',
		'Build' => 'build_list',
		'Tech' => 'tech_list',
		'Props' => 'props_use',
		'Res' => 'res_list',
		'Vip' => 'vip_effect',
		'CD' => 'cd_list',
		'Liveness' => 'liveness_list',
		'Floor' => 'floor_list',
		'SeekHero' => 'seek_hero',
		'Pack' => 'pack_list',
		'FindHero' => 'find_hero',
		'ColonyNpc' => 'colony_npc',
		'Answer' => 'answer_list',
	);

	public function __construct($cityId) {
		$this->_cityId = $cityId;
	}

	public function getId() {
		return $this->_cityId;
	}

	public function instance($name) {
		if (!isset($this->obj[$name])) {
			$objName = 'O_' . $name;
			$this->obj[$name] = new $objName($this);
		}
		return $this->obj[$name];
	}

	public function getCityBase() {
		if (empty($this->cityBase)) {
			$info = M_City::getInfo($this->_cityId);
			if (!empty($info['id'])) {
				$info['vip_level'] = 8;
				$this->cityBase = $info;
			}

		}
		return $this->cityBase;
	}

	public function getCityExtra() {
		if (empty($this->cityExtra)) {
			$info = M_Extra::getInfo($this->_cityId);
			if (!empty($info['city_id'])) {
				$this->cityExtra = $info;
			}
		}
		return $this->cityExtra;
	}

	/**
	 * 保存数据
	 * @return bool
	 */
	public function save() {
		$this->buildChangeData();

		$ret = false;
		if (!empty($this->changeExtra)) {
			//Logger::debug(array(__METHOD__, $this->changeExtra));
			$ret = M_Extra::setInfo($this->_cityId, $this->changeExtra);
			$this->changeExtra = array();
		}

		if (!empty($this->changeBase)) {
			//Logger::debug(array(__METHOD__, $this->changeBase));
			$ret = M_City::setCityInfo($this->_cityId, $this->changeBase);
			$this->changeBase = array();
		}

		$this->toSync();

		return $ret;
	}


	public function toSync() {
		foreach ($this->obj as $name => $mod) {
			if ($mod->isChange()) {
				$sync = $mod->getSync();
				if ($name == 'Res') {
					M_Sync::addQueue($this->_cityId, M_Sync::KEY_RES, $sync);
				} else if ($name == 'Tech') {
					M_Sync::addQueue($this->_cityId, M_Sync::KEY_TECH, $sync);
				} else if ($name == 'Liveness') {
					if (!empty($sync['num'])) {
						$this->sync['Activeness'] = $sync['num'];
					}
				} else if ($name == 'Quest') {
					M_Sync::addQueue($this->_cityId, M_Sync::KEY_QUEST, $sync);
				} else if ($name == 'City') {
					if (isset($sync['last_fb_no'])) {
						$nextFBNo = M_SoloFB::calcNextFBNo($sync['last_fb_no']);
						$sync['last_fb_no'] = $nextFBNo;
					}
					Logger::debug(array(__METHOD__, $this->_cityId, $sync));
					M_Sync::addQueue($this->_cityId, M_Sync::KEY_CITY_INFO, $sync);
				} else if ($name == 'Pack') {
					M_Sync::addQueue($this->_cityId, M_Sync::KEY_ITEM_PROPS, $sync);
				} else if ($name == 'Props') {
					M_Sync::addQueue($this->_cityId, M_Sync::KEY_PROPS_EFFECT, $sync);
				} else if ($name == 'ColonyNpc') {
					M_Sync::addQueue($this->_cityId, M_Sync::KEY_COLONY, $sync);
				}
			}

		}
	}

	public function buildChangeData() {
		foreach ($this->obj as $name => $mod) {
			//Logger::debug(array(__METHOD__, $name));
			if (isset(self::$ExtraField[$name]) && $mod->isChange()) {
				$field = self::$ExtraField[$name];
				$this->changeExtra[$field] = json_encode($mod->get());
			}
		}

		//Logger::debug(array(__METHOD__, $this->changeExtra));

		if (isset($this->obj['City']) && $this->obj['City']->isChange()) {
			$this->changeBase = $this->obj['City']->get();
		}
	}


	/**
	 * @return O_City
	 */
	public function City() {
		return $this->instance('City');
	}

	/**
	 * @return O_Team
	 */
	public function Team() {
		return $this->instance('Team');
	}

	/**
	 * @return O_Quest
	 */
	public function Quest() {
		return $this->instance('Quest');
	}

	/**
	 * @return O_Weapon
	 */
	public function Weapon() {
		return $this->instance('Weapon');
	}

	/**
	 * @return O_Army
	 */
	public function Army() {
		return $this->instance('Army');
	}

	/**
	 * @return O_Build
	 */
	public function Build() {
		return $this->instance('Build');
	}

	/**
	 * @return O_Tech
	 */
	public function Tech() {
		return $this->instance('Tech');
	}

	/**
	 * @return O_Props
	 */
	public function Props() {
		return $this->instance('Props');
	}


	/**
	 * @return O_Res
	 */
	public function Res() {
		return $this->instance('Res');
	}

	/**
	 * @return O_Vip
	 */
	public function Vip() {
		return $this->instance('Vip');
	}

	/**
	 * @return O_CD
	 */
	public function CD() {
		return $this->instance('CD');
	}

	/**
	 * @return O_Liveness
	 */
	public function Liveness() {
		return $this->instance('Liveness');
	}

	/**
	 * @return O_Floor
	 */
	public function Floor() {
		return $this->instance('Floor');
	}

	/**
	 * @return O_SeekHero
	 */
	public function SeekHero() {
		return $this->instance('SeekHero');
	}

	/**
	 * @return O_Pack
	 */
	public function Pack() {
		return $this->instance('Pack');
	}

	/**
	 * @return O_FindHero
	 */
	public function FindHero() {
		return $this->instance('FindHero');
	}

	/**
	 * @return O_ColonyNpc
	 */
	public function ColonyNpc() {
		return $this->instance('ColonyNpc');
	}

	/**
	 * @return O_Log
	 */
	public function Log() {
		return $this->instance('Log');
	}

	/**
	 * @return O_Answer
	 */
	public function Answer() {
		return $this->instance('Answer');
	}
}
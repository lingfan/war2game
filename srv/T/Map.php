<?php

class T_Map {
	/** 空地 */
	const WILD_MAP_CELL_SPACE = 0;
	/** 地貌 */
	const WILD_MAP_CELL_SCENIC = 1;
	/** 玩家城市 */
	const WILD_MAP_CELL_CITY = 2;
	/** NPC城市 */
	const WILD_MAP_CELL_NPC = 3;

	/** 据点 */
	const WILD_MAP_CELL_CAMP = 9;

	static $WildMapCellType = array(
		self::WILD_MAP_CELL_SPACE => '空地',
		self::WILD_MAP_CELL_SCENIC => '地貌',
		self::WILD_MAP_CELL_CITY => '玩家城市',
		self::WILD_MAP_CELL_NPC => 'NPC城市',
		self::WILD_MAP_CELL_CAMP => '据点',
	);

}

?>
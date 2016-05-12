<?php
//运行战斗进程
include('common.php');
require 'PHPUnit/Framework/TestCase.php';
define('PHPUNIT_CITYID', 1);

class vvTest extends PHPUnit_Framework_TestCase {
	protected static $__name = '';
	protected static $_userId = '1';
	protected static $_cityId = '1';
	protected static $userInfo = array();
	protected static $cityInfo = array();
	protected static $collectId = '';

	protected function setUp() {
		$nameArr = range('a', 'z');
		$name = '';
		for ($i = 0; $i < 5; $i++) {
			$name .= $nameArr[rand(1, 25)];
		}
		$name .= rand(100, 999);
		self::$__name = $name;
	}

	public function testCityInfo() {
		$result = CCity::AInfo();
		$this->assertEquals(T_App::SUCC, $result['flag']);
	}

	public function testCheckCityNickname() {
		$result = CCity::ACheckCityNickname('fadsfasdf');
		$this->assertEquals(T_App::SUCC, $result['flag']);
	}

	public function testMarketBuy() {
		$result = CCity::AMarketBuy(T_App::RES_FOOD, 1);
		$this->assertEquals(T_App::SUCC, $result['flag']);
		$result = CCity::AMarketBuy(T_App::RES_OIL, 1);
		$this->assertEquals(T_App::SUCC, $result['flag']);
		$result = CCity::AMarketBuy(T_App::RES_GOLD, 1);
		$this->assertEquals(T_App::SUCC, $result['flag']);
	}

	public function testMarketSale() {
		$result = CCity::AMarketSale(T_App::RES_FOOD, 1);
		$this->assertEquals(T_App::SUCC, $result['flag']);
		$result = CCity::AMarketSale(T_App::RES_OIL, 1);
		$this->assertEquals(T_App::SUCC, $result['flag']);
		$result = CCity::AMarketSale(T_App::RES_GOLD, 1);
		$this->assertEquals(T_App::SUCC, $result['flag']);
	}


	public function testACollect() {
		$result = CCity::ACollect('test', 1, 100, 100, 'zvzfewaf5');
	}

	public function testGetCollList() {
		$result = CCity::AGetCollList();
		foreach ($result['data']['Data'] as $k => $v) {
			self::$collectId = $k;
		}
		$this->assertEquals('', $result['flag']);

	}

	public function testDelCollect() {
		$result = CCity::ADelCollect(self::$collectId);
		$this->assertEquals(T_App::SUCC, $result['flag']);
	}


	public function tearDown() {

	}

}


?>

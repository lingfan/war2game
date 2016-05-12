<?php

class M_RandName {
	const OFFSET = 500;

	public function makeRandIdx($gender) {
		$rc    = new B_Cache_RC(T_Key::RAND_NAME_IDX, $gender);
		$split = 50000 / self::OFFSET;
		for ($i = 1; $i < $split; $i++) {
			$rc->sadd($i);
		}
		return $rc->scard();
	}

	static public function makeRandName($gender, $idx = 0) {
		$rc     = new B_Cache_RC(T_Key::RAND_NAME, $gender . $idx);
		$handle = @fopen(ETC_PATH . '/' . ETC_NO . "/randname_{$gender}.txt", "r");
		if ($handle) {
			$n      = 0;
			$offset = self::OFFSET;
			$start  = $idx * $offset;
			$end    = $start + $offset;
			while (($buffer = fgets($handle, 128)) !== false) {
				if ($n > $start) {
					$name = trim($buffer);
					$ret  = $rc->sadd($name);
				}

				if ($n > $end) {
					break;
				}
				$n++;
			}

			if (!feof($handle)) {
				//echo "Error: unexpected fgets() fail\n";
			}
			fclose($handle);
		}

		return $rc->scard();
	}

	static public function getRandName($gender) {
		$name = '';
		/**
		 * $rc1 = new B_Cache_RC(T_Key::RAND_NAME_IDX, $gender);
		 * $idx = $rc1->srandmember();
		 * if (empty($idx))
		 * {
		 * self::makeRandIdx($gender);
		 * $rc1 = new B_Cache_RC(T_Key::RAND_NAME_IDX, $gender);
		 * $idx = $rc1->srandmember();
		 * }
		 **/
		$idx = rand(1, 50000 / self::OFFSET);

		$rc   = new B_Cache_RC(T_Key::RAND_NAME, $gender . $idx);
		$name = $rc->srandmember();
		if (empty($name)) {
			self::makeRandName($gender, $idx);
			$rc   = new B_Cache_RC(T_Key::RAND_NAME, $gender . $idx);
			$name = $rc->srandmember();
		}

		self::setIdx($name, $idx);
		return $name;
	}

	/**
	 * 删除随机中的名字
	 * @author huwei
	 * @param int $gender
	 * @param string $newName
	 * @return boolean
	 */
	static public function delRandName($gender, $newName) {
		$ret = false;
		$idx = self::getIdx($newName);
		$rc  = new B_Cache_RC(T_Key::RAND_NAME, $gender . $idx);
		if ($rc->sismember($newName)) {
			$ret = $rc->srem($newName);
		}
		return $ret;
	}

	static public function setIdx($name, $idx) {
		$rc = new B_Cache_RC(T_Key::RAND_TMP_IDX, md5($name));
		return $rc->set($idx, 600);
	}

	static public function getIdx($name) {
		$rc = new B_Cache_RC(T_Key::RAND_TMP_IDX, md5($name));
		return $rc->get();
	}
}

?>
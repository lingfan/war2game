<?php

class B_Cache_RC extends Redis {
	protected static $instances = null;
	private $_key = '';
	private $_rc = null;
	private $_suffix = '';

	public function __construct($val, $keySuffix = '') {
		if (!empty($val)) {
			$this->_key = $val . ':' . $keySuffix;
			$config = B_Cache_File::get('redis');
			$n = abs(crc32($val)) % count($config['hostname']);

			$this->_rc = self::conn($config, $n);
		} else {
			Logger::error(array(__METHOD__, 'err KeyConst', func_get_args()));
		}
	}

	static public function conn($config, $n) {
		if (!isset(self::$instances[$n])) {
			$redis = new Redis;
			$ret = $redis->pconnect($config['hostname'][$n], 1);
			if (!$ret) {
				trigger_error('Redis Connection failed:' . $config['hostname']);
				Logger::halt('Err_RC');
			}
			$no = isset($config['db']) ? $config['db'] : 0;
			$redis->select($no);
			self::$instances[$n] = $redis;
		}
		return self::$instances[$n];
	}

	/**
	 * Set the string value in argument as value of the key.
	 * @param string $val Value
	 * @param int $ttl Timeout
	 */
	public function set($val, $ttl = 0) {
		return $this->_rc->set($this->_key, $val, $ttl);
	}

	public function incrby($val) {
		return $this->_rc->incrBy($this->_key, $val);
	}

	public function decrby($val) {
		return $this->_rc->decrBy($this->_key, $val);
	}

	/**
	 * Get the value related to the specified key
	 */
	public function get() {
		return $this->_rc->get($this->_key);
	}

	/**
	 * Set the string value in argument as value of the key, with a time to live.
	 * @param int $ttl Timeout
	 * @param string $val Value
	 */
	public function setex($ttl, $val) {
		return $this->_rc->setex($this->_key, $ttl, $val);
	}

	/**
	 * Fills in a whole hash. Non-string values are converted to string,
	 * using the standard (string) cast.
	 * NULL values are stored as empty strings.
	 * @param array $params
	 * @param int $ttl Timeout
	 */
	public function hmset($params, $ttl = false) {
		$ret = $this->_rc->hmset($this->_key, $params);
		if ($ttl !== false) {
			$this->expire($ttl);
		}
		return $ret;
	}

	/**
	 * Returns the contents of a set.
	 */
	public function smembers() {
		return $this->_rc->sMembers($this->_key);
	}

	/**
	 * Checks if value is a member of the set stored at the key key.
	 * @param string $val
	 */
	public function sismember($val) {
		return $this->_rc->sIsMember($this->_key, $val);
	}

	public function hget($filed) {
		return $this->_rc->hget($this->_key, $filed);
	}

	public function hset($filed, $val) {
		return $this->_rc->hset($this->_key, $filed, $val);
	}

	/**
	 * @param array $filed
	 */
	public function hmget($filed) {
		return $this->_rc->hmget($this->_key, $filed);
	}

	public function hgetall() {
		return $this->_rc->hgetall($this->_key);
	}

	public function hdel($val) {
		return $this->_rc->hdel($this->_key, $val);

	}

	public function srem($val) {
		return $this->_rc->sRem($this->_key, $val);
	}

	public function sadd($val, $ttl = false) {
		$ret = $this->_rc->sAdd($this->_key, $val);
		if ($ttl !== false) {
			$this->expire($ttl);
		}
		return $ret;
	}

	public function expire($ttl = 0) {
		$ret = $this->_rc->expire($this->_key, $ttl);
	}

	public function spop($val) {
		return $this->_rc->sPop($this->_key, $val);
	}

	public function srandmember() {
		return $this->_rc->sRandMember($this->_key);

	}

	public function exists() {
		return $this->_rc->exists($this->_key);
	}

	public function delete() {
		$ret = true;
		if ($this->exists()) {
			$ret = $this->_rc->delete($this->_key);
		}
		return $ret;
	}

	public function hincrby($field, $val) {
		return $this->_rc->hIncrBy($this->_key, $field, $val);
	}

	public function jsonget() {
		$ret = false;
		$val = $this->_rc->get($this->_key);
		if (!empty($val)) {
			$ret = json_decode($val, true);
		}
		return $ret;
	}

	public function jsonset($val, $ttl = 0) {
		$data = json_encode($val);
		$ret = $this->_rc->set($this->_key, $data, $ttl);
		return $ret;
	}

	public function rpush($val) {
		return $this->_rc->rPush($this->_key, $val);
	}

	public function rpop() {
		return $this->_rc->rPop($this->_key);
	}

	public function lpop() {
		return $this->_rc->lPop($this->_key);
	}

	public function llen() {
		return $this->_rc->lSize($this->_key);
	}

	public function lrange($start, $end) {
		return $this->_rc->lRange($this->_key, $start, $end);

	}

	/**
	 * Returns the cardinality of the set identified by key.
	 */
	public function scard() {
		return $this->_rc->sCard($this->_key);
	}

	/**
	 * 返回原来key中的值，并将value写入key
	 * @example
	$redis->set('42');
	 * $exValue = $redis->getSet('lol');  // return '42', replaces x by 'lol'
	 * $newValue = $redis->get()      // return 'lol'
	 */
	public function getset($val, $ttl = false) {
		$ret = $this->_rc->getSet($this->_key, $val);

		if ($ttl !== false) {
			$this->expire($ttl);
		}
		return $ret;

	}

	/**
	 * 返回名称为key的zset（元素已按score从小到大排序）中的index从start到end的所有元素
	 * @example
	$redis->zadd(0, 'val0');
	 * $redis->zadd(2, 'val2');
	 * $redis->zadd(10, 'val10');
	 * $redis->zrange(0, -1); //array('val0', 'val2', 'val10')
	 * // with scores
	 * $redis->zrange(0, -1, true); //array('val0' => 0, 'val2' => 2, 'val10' => 10)
	 *
	 */
	public function zrange($start, $end, $flag = false) {
		return $this->_rc->zRange($this->_key, $start, $end, $flag);
	}

	/**
	 * 返回名称为key的值的所有元素的个数
	 * @example
	$redis->zAdd(0, 'val0');
	 * $redis->zAdd(2, 'val2');
	 * $redis->zAdd(10, 'val10');
	 * $redis->zSize(); //3
	 */
	public function zcard() {
		return $this->_rc->zCard($this->_key);
	}

	/**
	 * 删除名称为key的zset中的元素member
	 * @example
	$redis->zadd(0, 'val0');
	 * $redis->zadd(2, 'val2');
	 * $redis->zadd(10, 'val10');
	 * $redis->zrem('val2');
	 * $redis->zrange(0, -1); //array('val0', 'val10')
	 */
	public function zrem() {
		return $this->_rc->zRem($this->_key);
	}

	/**
	 * 向名称为key的zset中添加元素member，score用于排序。如果该元素已经存在，则根据score更新该元素的顺序。
	 * @example
	$redis->zadd(1, 'val1');
	 * $redis->zadd(0, 'val0');
	 * $redis->zadd(5, 'val5');
	 * $redis->zrange(0, -1); // array(val0, val1, val5)
	 */
	public function zadd($no, $val) {
		return $this->_rc->zAdd($this->_key, $no, $val);
	}

	/**
	 * 返回名称为key的值中元素member的score
	 * @example
	$redis->zadd(2.5, 'val2');
	 * $redis->zscore('val2'); //2.5
	 */
	public function zscore($val) {
		return $this->_rc->zScore($this->_key, $val);
	}

	/**
	 * 返回名称为key的值（元素已按score从小到大排序）中member 元素的rank（即index，从0开始），若没有member 元素，返回“null”。
	 * @example
	$redis->zAdd(1, 'one');
	 * $redis->zAdd(2, 'two');
	 * $redis->zRank('one'); //0
	 * $redis->zRank('two'); //1
	 */
	public function zrank($val) {
		return $this->_rc->zRank($this->_key, $val);
	}

	/**
	 * zRevRank 是从大到小排序
	 * @example
	$redis->zAdd(1, 'one');
	 * $redis->zAdd(2, 'two');
	 * $redis->zRevRank('one'); //1
	 * $redis->zRevRank('two'); //0
	 */
	public function zrevrank($val) {
		return $this->_rc->zRevRank($this->_key, $val);
	}

	/**
	 * 如果在名称为key的值中已经存在元素member，则该元素的score增加increment；否则向集合中添加该元素，其score的值为increment
	 * @example
	$redis->delete();
	 * //key or member1 didn't exist, so member1's score is to 0 before the increment
	 * $redis->zincrby(2.5, 'member1'); // and now has the value 2.5
	 * $redis->zincrby(1, 'member1'); //3.5
	 */
	public function zincrby($num, $val) {
		return $this->_rc->zIncrBy($this->_key, $num, $val);
	}

	public function zrevrange($start, $end, $flag = false) {
		return $this->_rc->zRevRange($this->_key, $start, $end, $flag);
	}

	/**
	 * 移除有序集key中，指定排名(rank)区间内的所有成员。区间分别以下标参数start和stop指出，包含start和stop在内。
	 * @example
	$redis->zadd(1, 'one');
	 * $redis->zadd(2, 'two');
	 * $redis->zadd(3, 'three');
	 * $redis->zremrangebyrank(0, 1); //2
	 * $redis->zrange(0, -1, array('withscores' => TRUE)); //array('three' => 3)
	 */
	public function zremrangebyrank($start, $end) {
		return $this->_rc->zRemRangeByRank($this->_key, $start, $end);
	}

	/**
	 * 返回名称为key值中score >= star且score <= end的所有元素的个数
	 * @example
	$redis->zadd(0, 'val0');
	 * $redis->zadd(2, 'val2');
	 * $redis->zadd(10, 'val10');
	 * $redis->zcount(0, 3); //2, corresponding to array('val0', 'val2')
	 */
	public function zcount($start, $end) {
		return $this->_rc->zCount($this->_key, $start, $end);
	}

	/**
	 * 查找多个KEY
	 */
	public function keys($suffix = '*') {
		return $this->_rc->keys($this->_key . $suffix);
	}

	/**
	 * 当前KEY状态
	 */
	public function mix_status() {
		$ret['type'] = $this->_rc->type($this->_key);
		$ret['exists'] = $this->_rc->exists($this->_key);
		$ret['ttl'] = $this->_rc->ttl($this->_key);
		$ret['encoding'] = $this->_rc->object('encoding', $this->_key);
		return $ret;
	}

	/**
	 * 获取当前KEY名
	 */
	public function get_key() {
		return $this->_key;
	}

	/**
	 * 关闭连接
	 */
	public function close() {
		return $this->_rc->close();
	}

}

?>
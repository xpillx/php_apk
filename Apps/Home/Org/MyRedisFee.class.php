<?php
class MyRedisFee
{
    private static $instance = null;
	private $redis = null;
	public $prefix = null;
	
	/**
	 * 	@param $conf   array
	 * $conf['prefix']  键名前缀   必须传入
	 * $conf['host']   主机地址   默认：127.0.0.1
	 * $conf['port']   端口          默认：6379
	 * $conf['timeout'] 超时      默认：1
	 * $conf['pwd']   密码         默认：null ，传入密码就自动进行密码验证，不传入就不验证
	 */
	private function __construct( $conf = array() )
	{
		if(!isset($conf['prefix']))
            die("redis未设置键名前缀，请配置：prefix");

		$host = $conf['host'] ? $conf['host'] : '127.0.0.1' ;
		$port = $conf['port'] ? $conf['port'] : 6379;
		$timeout = $conf['timeout'] ? $conf['timeout'] : 1;
		
		$this->redis = new Redis();
		$this->redis->connect($host,$port, $timeout );
//		$this->prefix = $prefix  ;
		$this->prefix = $conf['prefix']  ;
		if($conf['pwd'])
			$this->redis->auth($conf['pwd']);
		
	}
    static function getInstance()
    {
        if( self::$instance === null )
        {
            self::$instance = new MyRedisFee( C('REDIS_FEE') );
        }
        return self::$instance;
    }

	function getRedis()
	{
		return $this->redis;
	}


    /**
     * @param $pattern
     * @return array
     */
    public function keys($pattern)
    {
        return $this->redis->keys($pattern);
    }

    /**
     * Set the string value in argument as value of the key.
     * @param string $key
     * @param string $value
     * @param int $lifetime seconds.
     * @return bool: true when success.
     */
    public function set($key, $value, $lifetime = 0)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        if ($lifetime == 0) {
            return $this->redis->set($key, $value);
        }
        return $this->redis->setex($key, $lifetime, $value);
    }

    /**
     * Adds a value to the hash stored at key. If this value is already in the hash, FALSE is returned.
     *
     * @param string $key
     * @param string $subKey
     * @param string $value
     * @param int $lifetime
     * @return long 1 if value didn't exist and was added successfully, 0 if the value was already present and was replaced, FALSE if there was an error.
     */
    public function hSet($key, $subKey, $value, $lifetime = 0)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $rs = $this->redis->hSet($key, $subKey, $value);
        $lifetime > 0 && $this->redis->setTimeout($key, $lifetime);
        return $rs;
    }

    /**
     * Gets a value from the hash stored at key. If the hash table doesn't exist, or the key doesn't exist, FALSE is returned.
     * @param string $key
     * @param string $subKey
     * @return string
     */
    public function hGet($key, $subKey)
    {
        $data = $this->redis->hGet($key, $subKey);
        $data_temp = json_decode($data, true);
        if ($data_temp) {
            $data = $data_temp;
        }
        return $data;
    }

    /**
     * Removes a value from the hash stored at key. If the hash table doesn't exist, or the key doesn't exist, FALSE is returned.
     * @param string $key
     * @param string $subKey
     * @return bool
     */
    public function hDel($key, $subKey)
    {
        return $this->redis->hDel($key, $subKey);
    }

    public function hDelAll($key)
    {
        return $this->redis->del($key);
    }

    /**
     * Returns the whole hash, as an array of strings indexed by strings.
     * @param string $key
     * @return array
     */
    public function hGetAll($key)
    {
        return $this->redis->hGetAll($key);
    }

    /**
     * Adds the string value to the head (left) of the list. Creates the list if the key didn't exist. If the key exists and is not a list, FALSE is returned.
     * @param string $key
     * @param string $value
     * @return long The new length of the list in case of success, FALSE in case of Failure.
     */
    public function lPush($key, $value)
    {
        return $this->redis->lPush($key, $value);
    }

    /**
     * Return and remove the first element of the list.
     * @param string $key
     * @return string STRING if command executed successfully BOOL FALSE in case of failure (empty list)
     */
    public function lPop($key)
    {
        return $this->redis->lPop($key);
    }

    /**
     * Adds the string value to the tail (right) of the list. Creates the list if the key didn't exist. If the key exists and is not a list, FALSE is returned.
     * @param string $key
     * @param string $value
     * @return long The new length of the list in case of success, FALSE in case of Failure.
     */
    public function rPush($key, $value)
    {
        return $this->redis->rPush($key, $value);
    }

    /**
     * Returns and removes the last element of the list.
     * @param string $key
     * @return string if command executed successfully BOOL FALSE in case of failure (empty list).
     */
    public function rPop($key)
    {
        return $this->redis->rPop($key);
    }

    /**
     * Returns the specified elements of the list stored at the specified key in the range [start, end]. start and stop are interpretated as indices: 0 the first element, 1 the second ... -1 the last element, -2 the penultimate ...
     * @param string $start
     * @param string $end
     * @return array  containing the values in specified range.
     */
    public function lRange($key, $start, $end)
    {
        // lRange('key', 0, -1) get the all elements.
        return $this->redis->lRange($key, $start, $end);
    }

    /**
     * Get the value related to the specified key.
     * @param string $key
     * @return string|bool: If key didn't exist, FALSE is returned.
     *        Otherwise, the value related to this key is returned.
     */
    public function get($key)
    {
        $data = $this->redis->get($key);
        $data_temp = json_decode($data, true);
        if ($data_temp) {
            $data = $data_temp;
        }
        return $data;
    }

    /**
     * Remove specified key.
     * @param string $key
     * @return int 1 success.
     */

    public function delete($key)
    {
        return $this->redis->delete($key);
    }

    /**
     * @param $key
     * @return array
     */

    public function hKeys($key)
    {
        return $this->redis->hKeys($key);
    }

    /**
     * Verify if the specified key exists.
     * @param string $key
     * @return bool: If the key exists, return TRUE, otherwise return FALSE.
     */
    public function exists($key)
    {
        return $this->redis->exists($key);
    }

    /**
     * Increment the number stored at key by one. If the second argument is filled, it will be used as the integer value of the increment.
     * @param string $key
     * @param integer $value value that will be added to key, default is one.
     * @return int: The new value.
     */
    public function incr($key, $value = 1)
    {
        return $this->redis->incr($key);
    }

    /**
     * Decrement the number stored at key by one. If the second argument is filled, it will be used as the integer value of the decrement.
     * @param string $key
     * @param integer $value value that will be substracted to key, default is one.
     * They just know think
     * @return int: The new value.
     */
    public function decr($key, $value = 1)
    {
        return $this->redis->decr($key);
    }

    public function hIncrBy($key, $subkey, $value)
    {
        return $this->redis->hIncrBy($key, $subkey, $value);
    }

    /**
     * Remove specified keys.
     * @param array $keys
     * @return long: Number of keys deleted.
     */
    public function delMulti(array $keys)
    {
        return $this->redis->delete($keys);
    }

    /**
     * Get the values of all the specified keys. If one or more keys dont exist, the array will contain FALSE at the position of the key.
     * @param array $keys
     * @return array:Array containing the values related to keys in argument.
     */
    public function getMulti(array $keys)
    {
        return $this->redis->getMulti($keys);
    }

    /**
     * Sets an expiration date (a timeout) on an item.
     * @param string $key
     * @param int $expire_time
     * @return bool: TRUE in case of success, FALSE in case of failure.
     */
    public function setExpireTime($key, $expire_time)
    {
        return $this->redis->setTimeout($key, $expire_time);
    }

    //将一个或多个 member 元素及其 score 值加入到有序集 key 当中。
    public function zadd($key, $score, $member)
    {
        return $this->redis->zadd($key, $score, $member);
    }

    //返回有序集 key 中，指定区间内的成员。其中成员的位置按 score 值递增(从小到大)来排序。
    public function zrange($key, $start, $end, $type = "WITHSCORES")
    {
        return $this->redis->zrange($key, $start, $end, $type);
    }

    //返回有序集 key 中成员 member 的排名。其中有序集成员按 score 值递增(从小到大)顺序排列。
    public function zrank($key, $member)
    {
        return $this->redis->zrank($key, $member);
    }

    //移除一个成员
    public function zrem($key, $member)
    {
        return $this->redis->zrem($key, $member);
    }

    //返回有序集 key 中，指定区间内的成员。其中成员的位置按 score 值递增(从大到小)来排序。
    public function zrevrange($key, $start, $end, $type = "WITHSCORES")
    {
        return $this->redis->zrevrange($key, $start, $end, $type);
    }

    //返回有序集 key 中成员 member 的排名。其中有序集成员按 score 值递增(从大到小)顺序排列。
    public function zrevrank($key, $member)
    {
        return $this->redis->zrevrank($key, $member);
    }

    //返回有序集 key 中，成员 member 的 score 值。
    public function zscore($key, $member)
    {
        return $this->redis->zscore($key, $member);
    }

    //返回有序集 key 中，成员 member 的 score 值。
    public function zincrby($key, $increment, $member)
    {
        return $this->redis->zincrby($key, $increment, $member);
    }

    //返回有序集 key 中的成员数。
    public function zcard($key)
    {
        return $this->redis->zcard($key);
    }

    //返回有序集 key 中， score 值在 min 和 max 之间(默认包括 score 值等于 min 或 max )的成员的数量。
    public function zcount($key, $min, $max)
    {
        return $this->redis->zcount($key, $min, $max);
    }


    //返回集合 key 中的所有成员。不存在的 key 被视为空集合。
    public function smembers($key)
    {
        return $this->redis->smembers($key);
    }

    //判断 member 元素是否集合 key 的成员。
    public function sismember($key, $member)
    {
        return $this->redis->sismember($key, $member);
    }

    //将一个或多个 member 元素加入到集合 key 当中，已经存在于集合的 member 元素将被忽略。
    public function sadd($key, $member)
    {
        return $this->redis->sadd($key, $member);
    }

    //返回集合 key 的基数(集合中元素的数量)。
    public function scard($key)
    {
        return $this->redis->scard($key);
    }

    //移除集合 key 中的一个或多个 member 元素，不存在的 member 元素会被忽略
    public function srem($key, $member)
    {
        return $this->redis->srem($key, $member);
    }

    //移除并返回集合中的一个随机元素。
    public function spop($key)
    {
        return $this->redis->spop($key);
    }

    /**
     * @param $key
     * @param $index
     * @param $value
     * @return BOOL
     */
    public function lSet($key, $index, $value)
    {
        return $this->redis->lSet($key, $index, $value);
    }
	
}
?>

<?php
/**
 *
 * 封装Cache的操作
 *
 * Use PHP 5.3.0 or newer
 *
 * @package        Library
 * @author        Smart Lee <ismtlee@gmail.com>
 * @copyright    Copyright (c) 2008 - 2012, Joymeng, Inc.
 * @since        Version 2.0
 * @filesource
 */
// ------------------------------------------------------------------------
/**
 *
 * JoyRedisWeb
 *
 * 封装Cache的操作
 *
 * @package Library
 * @author Smart Lee <ismtlee@gmail.com>
 * @version $Revision 2.0 2012-11-19 上午9:10:16
 * @see https://github.com/nicolasff/phpredis
 */
class JoyRedisWeb
{
    // lifetime definition.
    const FOREVER = 0;
    const DAY = 86400;
    const HALF_DAY = 43200;
    const SHORT = 180;
    const NORMAL = 480;
    const LONG = 600;

    // result code definition.
    const RES_NOTFOUND = 13;
    const RES_SUCCESS = 0;
    private $host = array("192.168.8.30");
    private $port = '6379';
    private $time = 2.5;

    private static $instance;
    private $cache;

    /**
     * @see https://github.com/nicolasff/phpredis
     * @return JoyRedisWeb
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new JoyRedisWeb();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->cache = new Redis();
        $this->cache->connect($this->host[array_rand($this->host)], $this->port, 10);
    }

    /**
     * Set the string value in argument as value of the key.
     * @param string $key
     * @param mixed $value
     * @param int $lifetime seconds.
     * @return bool: true when success.
     */
    public function set($key, $value, $lifetime = 0)
    {
        if (is_array($value)) {
            $value = json_encode($value,256);
        }
        if ($lifetime == 0) {
            return $this->cache->set($key, $value);
        }
        return $this->cache->setex($key, $lifetime, $value);
    }

    /**
     * Adds a value to the hash stored at key. If this value is already in the hash, FALSE is returned.
     *
     * @param $key
     * @param $subKey
     * @param $value
     * @param int $lifetime
     * @return int  1 if value didn't exist and was added successfully, 0 if the value was already present and was replaced, FALSE if there was an error.
     */
    public function hSet($key, $subKey, $value, $lifetime = 0)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $rs = $this->cache->hSet($key, $subKey, $value);
        $lifetime > 0 && $this->cache->setTimeout($key, $lifetime);
        return $rs;
    }

    /**
     * Gets a value from the hash stored at key. If the hash table doesn't exist, or the key doesn't exist, FALSE is returned.
     * @param string $key
     * @param string $subKey
     * @return string|array
     */
    public function hGet($key, $subKey)
    {
        $data = $this->cache->hGet($key, $subKey);
        $data_temp = json_decode($data, true);
        if (is_array($data_temp)) {
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
        return $this->cache->hDel($key, $subKey);
    }

    /**
     * Returns the whole hash, as an array of strings indexed by strings.
     * @param string $key
     * @return array
     */
    public function hGetAll($key)
    {
        return $this->cache->hGetAll($key);
    }

    /**
     * Adds the string value to the head (left) of the list. Creates the list if the key didn't exist. If the key exists and is not a list, FALSE is returned.
     * @param string $key
     * @param string $value
     * @return long The new length of the list in case of success, FALSE in case of Failure.
     */
    public function lPush($key, $value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        return $this->cache->lPush($key, $value);
    }

    public function lLen($key)
    {
        return $this->cache->lLen($key);
    }

    /**
     * Return and remove the first element of the list.
     * @param string $key
     * @return string STRING if command executed successfully BOOL FALSE in case of failure (empty list)
     */
    public function lPop($key)
    {
        return $this->cache->lPop($key);
    }

    /**
     * Adds the string value to the tail (right) of the list. Creates the list if the key didn't exist. If the key exists and is not a list, FALSE is returned.
     * @param string $key
     * @param string $value
     * @return long The new length of the list in case of success, FALSE in case of Failure.
     */
    public function rPush($key, $value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        return $this->cache->rPush($key, $value);
    }

    /**
     * Returns and removes the last element of the list.
     * @param string $key
     * @return string if command executed successfully BOOL FALSE in case of failure (empty list).
     */
    public function rPop($key)
    {
        return $this->cache->rPop($key);
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
        return $this->cache->lRange($key, $start, $end);
    }

    /**
     * Get the value related to the specified key.
     * @param string $key
     * @return array|bool: If key didn't exist, FALSE is returned.
     *        Otherwise, the value related to this key is returned.
     */
    public function get($key)
    {
        $data = $this->cache->get($key);
        $data_temp = json_decode($data, true);
        if (is_array($data_temp)) {
            $data = $data_temp;
        }
        if ($data == "[]") {
            return null;
        }
        return $data;
    }

    /**
     * @param $pattern
     * @return array
     */
    public function keys($pattern)
    {
        return $this->cache->keys($pattern);
    }

    /**
     * Remove specified key.
     * @param string $key
     * @return int: 1 success.
     */
    public function delete($key)
    {
        return $this->cache->delete($key);
    }

    /**
     *
     * @
     * @
     */
    public function hKeys($key)
    {
        return $this->cache->hKeys($key);
    }

    /**
     * Verify if the specified key exists.
     * @param string $key
     * @return bool: If the key exists, return TRUE, otherwise return FALSE.
     */
    public function exists($key)
    {
        return $this->cache->exists($key);
    }

    /**
     * Increment the number stored at key by one. If the second argument is filled, it will be used as the integer value of the increment.
     * @param string $key
     * @param string $value value that will be added to key, default is one.
     * @return int: The new value.
     */
    public function incr($key, $value = 1)
    {
        return $this->cache->incr($key);
    }

    /**
     * Decrement the number stored at key by one. If the second argument is filled, it will be used as the integer value of the decrement.
     * @param string $key
     * @param string $value value that will be substracted to key, default is one.
     * @return int: The new value.
     */
    public function decr($key, $value = 1)
    {
        return $this->cache->decr($key);
    }

    /**
     * Remove specified keys.
     * @param array $keys
     * @return long: Number of keys deleted.
     */
    public function delMulti(array $keys)
    {
        return $this->cache->delete($keys);
    }

    /**
     * Get the values of all the specified keys. If one or more keys dont exist, the array will contain FALSE at the position of the key.
     * @param array $keys
     * @return array:Array containing the values related to keys in argument.
     */
    public function getMulti(array $keys)
    {
        return $this->cache->mget($keys);
    }

    public function info()
    {
        return $this->cache->info();
    }

    /**
     * Sets an expiration date (a timeout) on an item.
     * @param string $key
     * @param int $expire_time
     * @return bool: TRUE in case of success, FALSE in case of failure.
     */
    public function setExpireTime($key, $expire_time)
    {
        return $this->cache->setTimeout($key, $expire_time);
    }

    public function getLastTime($key)
    {
        return $this->cache->ttl($key);
    }

    //将一个或多个 member 元素及其 score 值加入到有序集 key 当中。
    public function zadd($key, $score, $member)
    {
        return $this->cache->zadd($key, $score, $member);
    }

    //返回有序集 key 中，指定区间内的成员。其中成员的位置按 score 值递增(从小到大)来排序。
    public function zrange($key, $start, $end, $type = "WITHSCORES")
    {
        return $this->cache->zrange($key, $start, $end, $type);
    }

    //返回有序集 key 中成员 member 的排名。其中有序集成员按 score 值递增(从小到大)顺序排列。
    public function zrank($key, $member)
    {
        return $this->cache->zrank($key, $member);
    }

    //移除一个成员
    public function zrem($key, $member)
    {
        return $this->cache->zrem($key, $member);
    }


    /**
     * @param $key
     * @param $increment
     * @param $member
     * @return float
     */
    public function zincrby($key, $increment, $member)
    {
        return $this->cache->zincrby($key, $increment, $member);
    }


    /**
     * 返回有序集 key 中，指定区间内的成员。其中成员的位置按 score 值递增(从大到小)来排序。
     * @param $key
     * @param $start
     * @param $end
     * @param string $type
     * @return array
     */
    public function zrevrange($key, $start, $end, $type = "WITHSCORES")
    {
        return $this->cache->zrevrange($key, $start, $end, $type);
    }

    //返回有序集 key 中成员 member 的排名。其中有序集成员按 score 值递增(从大到小)顺序排列。
    public function zrevrank($key, $member)
    {
        return $this->cache->zrevrank($key, $member);
    }

    //返回有序集 key 中，成员 member 的 score 值。
    public function zscore($key, $member)
    {
        return $this->cache->zscore($key, $member);
    }

    //返回有序集 key 中的成员数。
    public function zcard($key)
    {
        return $this->cache->zcard($key);
    }

    //返回有序集 key 中， score 值在 min 和 max 之间(默认包括 score 值等于 min 或 max )的成员的数量。
    public function zcount($key, $min, $max)
    {
        return $this->cache->zcount($key, $min, $max);
    }

    //返回有序集 key 中，所有 score 值介于 min 和 max 之间(包括等于 min 或 max )的成员。有序集成员按 score 值递增(从小到大)次序排列。
    public function zrangebyscore($key, $min = "-inf", $max = "+inf", $array = array('withscores' => TRUE, 'limit' => array(0, 20)))
    {
        return $this->cache->zrangebyscore($key, $min, $max, $array);
    }

    //返回有序集 key 中，所有 score 值介于 min 和 max 之间(包括等于 min 或 max )的成员。有序集成员按 score 值递增(从小到大)次序排列。
    public function zrevrangebyscore($key, $max = "+inf", $min = "-inf", $array = array('withscores' => TRUE, 'limit' => array(0, 20)))
    {
        return $this->cache->zrevrangebyscore($key, $max, $min, $array);
    }


    //时间复杂度中的N表示Sorted-Set中成员的数量，M则表示被删除的成员数量。删除分数在min和max之间的所有成员，即满足表达式min <= score <= max的所有成员。对于min和max参数，可以采用开区间的方式表示，具体规则参照ZCOUNT。 。
    public function zremrangebyscore($key, $min, $max)
    {
        return $this->cache->zremrangebyscore($key, $min, $max);
    }


    //时间复杂度中的N表示Sorted-Set中成员的数量，M则表示被删除的成员数量。删除排名在min和max之间的所有成员，即满足表达式min <= score <= max的所有成员。对于min和max参数，可以采用开区间的方式表示，具体规则参照ZCOUNT。 。
    public function zremrangebyrank($key, $min, $max)
    {
        return $this->cache->zremrangebyrank($key, $min, $max);
    }


    /**
     * Adds a values to the set value stored at key.
     * If this value is already in the set, FALSE is returned.
     *
     * @param   string  $key        Required key
     * @param   string  $value      Required value
     * @return  int     The number of elements added to the set
     * @link    http://redis.io/commands/sadd
     * @example
     * <pre>
     * $redis->sAdd('k', 'v1');                // int(1)
     * $redis->sAdd('k', 'v1', 'v2', 'v3');    // int(2)
     * </pre>
     *
     */
    public function sAdd( $key, $value ) {

        return $this->cache->sAdd($key, $value);
    }

    /**
     * Adds a values to the set value stored at key.
     *
     * @param   string  $key        Required key
     * @param   array   $values      Required values
     * @return  boolean The number of elements added to the set
     * @link    http://redis.io/commands/sadd
     * @link    https://github.com/phpredis/phpredis/commit/3491b188e0022f75b938738f7542603c7aae9077
     * @since   phpredis 2.2.8
     * @example
     * <pre>
     * $redis->sAddArray('k', array('v1'));                // boolean
     * $redis->sAddArray('k', array('v1', 'v2', 'v3'));    // boolean
     * </pre>
     */
    public function sAddArray( $key, array $values) {
        return $this->cache->sAddArray($key,$values);
    }


    /**
     * Removes the specified members from the set value stored at key.
     *
     * @param   string  $key
     * @param   string  $member1
     * @param   string  $member2
     * @param   string  $memberN
     * @return  int     The number of elements removed from the set.
     * @link    http://redis.io/commands/srem
     * @example
     * <pre>
     * var_dump( $redis->sAdd('k', 'v1', 'v2', 'v3') );    // int(3)
     * var_dump( $redis->sRem('k', 'v2', 'v3') );          // int(2)
     * var_dump( $redis->sMembers('k') );
     * //// Output:
     * // array(1) {
     * //   [0]=> string(2) "v1"
     * // }
     * </pre>
     */
    public function sRem( $key, $member1, $member2 = null, $memberN = null ) {
        return $this->cache->sRem($key, $member1, $member2, $memberN );
    }

    public function sMove( $srcKey, $dstKey, $member ) {}


    /**
     * Checks if value is a member of the set stored at the key key.
     *
     * @param   string  $key
     * @param   string  $value
     * @return  bool    TRUE if value is a member of the set at key key, FALSE otherwise.
     * @link    http://redis.io/commands/sismember
     * @example
     * <pre>
     * $redis->sAdd('key1' , 'set1');
     * $redis->sAdd('key1' , 'set2');
     * $redis->sAdd('key1' , 'set3'); // 'key1' => {'set1', 'set2', 'set3'}
     *
     * $redis->sIsMember('key1', 'set1'); // TRUE
     * $redis->sIsMember('key1', 'setX'); // FALSE
     * </pre>
     */
    public function sIsMember( $key, $value ) {
        return $this->cache->sIsMember($key, $value);
    }

    /**
     * Returns the contents of a set.
     *
     * @param   string  $key
     * @return  array   An array of elements, the contents of the set.
     * @link    http://redis.io/commands/smembers
     * @example
     * <pre>
     * $redis->delete('s');
     * $redis->sAdd('s', 'a');
     * $redis->sAdd('s', 'b');
     * $redis->sAdd('s', 'a');
     * $redis->sAdd('s', 'c');
     * var_dump($redis->sMembers('s'));
     *
     * //array(3) {
     * //  [0]=>
     * //  string(1) "c"
     * //  [1]=>
     * //  string(1) "a"
     * //  [2]=>
     * //  string(1) "b"
     * //}
     * // The order is random and corresponds to redis' own internal representation of the set structure.
     * </pre>
     */
    public function sMembers( $key ) {
        return $this->cache->sMembers($key);
    }

}

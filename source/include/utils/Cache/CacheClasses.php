<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
 
/* crmv@105600 */ 

// Classi cache divise per persistenza


/**
 * Base class for all the caches, just call the storage backend
 */
abstract class BaseCache {

	public $supportExpiration;
	protected $storage;
	
	protected static $instances = array();
	
	// the constructor is abstract, so you are forced to define a new one!
	abstract public function __construct();

	public function has($key) {
		return $this->storage->has($key);
	}
	
	public function get($key) {
		return $this->storage->get($key);
	}
	
	public function set($key, $value, $duration=null, $options=null) {
		return $this->storage->set($key, $value);
	}
	
	// crmv@106294
	public function clearMatching($regexp) {
		return $this->storage->clearMatching($regexp);
	}
	// crmv@106294e
	
	public function clear($key) {
		return $this->storage->clear($key);
	}
	
	public function clearAll() {
		return $this->storage->clearAll();
	}
	
	// this is necessary in order not to depend on any sdk class, since the sdk uses this cache!
	public static function getInstance() {
		$classname = get_called_class();
		if (!array_key_exists($classname, self::$instances)) {
			$args = func_get_args();
			self::$instances[$classname] = new $classname($args[0], $args[1], $args[2], $args[3]);
		}
		return self::$instances[$classname];
	}
	
}


/**
 * Provide a basic wrapper to handle expiration of cache entries
 */
abstract class BaseExpirationClass extends BaseCache {
	
	public $supportExpiration = true;
	public $defaultDuration = 21600;	// seconds, set to 0 to disable, default to 6 hours
	
	/**
	 * Get an entry from the cache, checking for expiration
	 */
	public function get($key) {
		$v = $this->storage->get($key);
		if ($this->checkExpiration($v)) {
			return $v['val'];
		} else {
			$this->clear($key);
			return null;
		}
	}
	
	/**
	 * Set an entry in the cache, with an optional duration set
	 */
	public function set($key, $value, $duration=null, $options=null) {
		$v = array('val' => $value);
		if ($duration > 0) {
			$v['expire'] = time() + intval($duration);
		} elseif ($this->defaultDuration > 0) {
			$v['expire'] = time() + $this->defaultDuration;
		}
		return $this->storage->set($key, $v);
	}
	
	/**
	 * Return true if the values is valid, or false if expired
	 */
	protected function checkExpiration($v) {
		$expire = intval($v['expire']);
		if ($expire > 0 && $expire < time()) return false;
		return true;
	}
}


/**
 * Request Cache class
 * Caches data for the length of the request only
 * Especially useful to hold small data that otherwise would be read from db
 * Does not support expiration
 */
class RCache extends BaseCache {

	public $supportExpiration = false;
	
	public function __construct() {
		$this->storage = new CacheStorageVar();
	}
	
}


/**
 * Session Cache class
 * Caches data for the length of the current session
 */
class SCache extends BaseExpirationClass {
	
	public $defaultDuration = 21600;	// seconds, set to 0 to disable
	
	public function __construct() {
		$this->storage = new CacheStorageSession();
	}
	
}

/**
 * User Cache class
 * Caches data for the current user only. Persists between logins/logout, it's trasversal to all user's sessions
 */
class UCache extends BaseExpirationClass {
	
	public $defaultDuration = 7200;	// 2 hours
	
	public function __construct($userid = null) {
		global $current_user;
		if (!$userid) $userid = $current_user->id;
		
		$this->storage = new CacheStorageFile('cache/sys/ucache_'.$userid.".json");
	}
}

/**
 * Global Cache class
 * Caches data for all users/sessions. Must be manually invalidated or wait for the expiration time
 * TODO: implement this class!!
 */
/*class GCache extends BaseCache {
	public $supportExpiration = true;
}*/


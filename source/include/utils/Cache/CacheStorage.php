<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
 
/* crmv@105600 crmv@106294 */


/**
 * Base class which represent a storage engine for cache
 */
abstract class CacheStorage {
	abstract public function has($key);
	abstract public function get($key);
	abstract public function set($key, $value);
	abstract public function setMulti($values); // crmv@115378
	abstract public function clear($key);
	abstract public function clearAll();
	abstract public function clearMatching($regexp);
}


/**
 * Var Cache storage
 * Store everything inside a global variable
 */
class CacheStorageVar extends CacheStorage {
	
	protected $data = array();
	
	public function has($key) {
		return isset($this->data[$key]);
	}
	
	public function get($key) {
		if (isset($this->data[$key])) {
			return $this->data[$key];
		}
		return null;
	}
	
	public function set($key, $value) {
		$this->data[$key] = $value;
	}
	
	// crmv@115378
	public function getAll() {
		return $this->data;
	}
	
	public function setMulti($values) {
		$this->data = array_merge($this->data, $values);
	}
	// crmv@115378e
	
	public function clear($key) {
		unset($this->data[$key]);
	}
	
	public function clearAll() {
		$this->data = array();
	}
	
	public function clearMatching($regexp) {
		$keys = array_keys($this->data);
		foreach ($keys as $k) {
			if (preg_match($regexp, $k)) {
				$this->clear($k);
			}
		}
	}
}


/**
 * Session Cache storage
 * Store everything inside the current session
 */
class CacheStorageSession extends CacheStorage {
	
	public function has($key) {
		return isset($_SESSION['scache'][$key]);
	}
	
	public function get($key) {
		if (isset($_SESSION['scache'][$key])) {
			return $_SESSION['scache'][$key];
		}
		return null;
	}
	
	public function set($key, $value) {
		$_SESSION['scache'][$key] = $value;
	}
	
	// crmv@115378
	public function getAll() {
		if (is_array($_SESSION['scache'])) {
			return $_SESSION['scache'];
		}
		return null;
	}
	
	public function setMulti($values) {
		if (!is_array($_SESSION['scache'])) $_SESSION['scache'] = array();
		$_SESSION['scache'] = array_merge($_SESSION['scache'], $values);
	}
	// crmv@115378e
	
	public function clear($key) {
		unset($_SESSION['scache'][$key]);
	}
	
	public function clearAll() {
		$_SESSION['scache'] = array();
	}
	
	public function clearMatching($regexp) {
		if (is_array($_SESSION['scache'])) {
			$keys = array_keys($_SESSION['scache']);
			foreach ($keys as $k) {
				if (preg_match($regexp, $k)) {
					$this->clear($k);
				}
			}
		}
	}
	
}


/**
 * File Cache storage
 * Store everything inside a file
 * Warning, might be slow with many variables, since everything is in the same file,
 * might be better to store each var in a separate file!
 */
class CacheStorageFile extends CacheStorage {

	public $type = "json";	// one of "json", "php", "serialize" (from the fastest to the slowest)
							// decides the way variables are encoded inside the file
							
	protected $filename;
	
	public function __construct($filename, $type = null) {
		if ($type) $this->type = $type;
		$this->filename = $filename;
	}
	
	public function has($key) {
		$cache = $this->getData();
		return isset($cache[$key]);
	}
	
	public function get($key) {
		$cache = $this->getData();
		if (isset($cache[$key])) {
			return $cache[$key];
		}
		return null;
	}
	
	public function set($key, $value) {
		$cache = $this->getData() ?: array();
		$cache[$key] = $value;
		$this->setData($cache);
	}
	
	// crmv@115378
	public function getAll() {
		$cache = $this->getData();
		if (is_array($cache)) return $cache;
		return null;
	}
	
	public function setMulti($values) {
		$cache = $this->getData() ?: array();
		$cache = array_merge($cache, $values);
		$this->setData($cache);
	}
	// crmv@115378e
	
	public function clear($key) {
		$cache = $this->getData();
		unset($cache[$key]);
		$this->setData($cache);
	}
	
	public function clearAll() {
		if (is_file($this->filename)) {
			unlink($this->filename);
		}
	}
	
	public function clearMatching($regexp) {
		$cache = $this->getData() ?: array();
		$keys = array_keys($cache);
		foreach ($keys as $k) {
			if (preg_match($regexp, $k)) {
				unset($cache[$k]);
			}
		}
		$this->setData($cache);
	}
	
	// crmv@115378
	public function isFileEmpty() {
		return !(is_readable($this->filename) && is_file($this->filename) && filesize($this->filename) > 0);
	}
	// crmv@115378e
	
	
	protected function getData() {
		$cache = array();
		if (is_readable($this->filename) && is_file($this->filename)) {
			if ($this->type == 'php') {
				// the file should contain a $cache variable
				@include($this->filename);
			} else{
				// otherwise just read the contents
				$content = @file_get_contents($this->filename);
				if ($content) {
					if ($this->type == "json") {
						$cache = json_decode($content, true);
					} elseif ($this->type == "serialize") {
						$cache = unserialize($content);
					}
				}
			}
		}
		return $cache;
	}
	
	protected function setData($cache) {
		if ($this->type == 'php') {
			$content = "<?php\n\$cache = ".var_export($cache, true).";\n";
		} elseif ($this->type == 'json') {
			$content = json_encode($cache);
		} elseif ($this->type == "serialize") {
			$content = serialize($cache);
		}
		file_put_contents($this->filename, $content);
	}
	
}


/**
 * Apc Cache storage
 * Store cache inside apc. Beware, can be read by any other php process on the same host
 * @experimental
 */
class CacheStorageApc extends CacheStorage {

	public function __construct() {
		global $root_directory, $application_unique_key;
		// calculate a prefix for the var, so different VTE won't collide
		$this->prefix = md5($root_directory.'#'.$application_unique_key);
	}
	
	public static function isSupported() {
		return function_exists('apc_store');
	}
	
	public function has($key) {
		return apc_exists($this->key2apc($key));
	}
	
	public function get($key) {
		return apc_fetch($this->key2apc($key));
	}
	
	public function set($key, $value, $duration = null) {
		apc_store($this->key2apc($key), $value, intval($duration));
	}
	
	// crmv@115378
	public function setMulti($values, $duration = null) {
		$values = array_combine(array_map(array($this, 'key2apc'), array_keys($values)), array_values($values));
		apc_store($values, null, intval($duration));
	}
	// crmv@115378e
	
	public function clear($key) {
		apc_delete($this->key2apc($key));
	}
	
	public function clearAll() {
		apc_clear_cache("user");
	}
	
	public function clearMatching($regexp) {
		$iter = new APCIterator('user', $regexp, APC_ITER_VALUE);
		apc_delete($iter);
	}
	
	protected function key2apc($key) {
		return $this->prefix."_".$key;
	}
	
	protected function apc2key($akey) {
		$l = strlen($this->prefix);
		return substr($akey, $l+1);
	}
}


/**
 * Memcached Cache storage
 * Store cache inside memcache. Beware, can be read by any other process accessing memcache
 * @experimental
 */
class CacheStorageMemcached extends CacheStorage {

	protected $mc;

	public function __construct($servers, $options = null) {
		global $root_directory, $application_unique_key;
		// calculate the persistend id, so different VTE won't collide
		$pid = md5($root_directory.'#'.$application_unique_key);
		$this->mc = new Memcached($pid);
		// TODO: add servers
	}
	
	public static function isSupported() {
		return class_exists('Memcached');
	}
	
	public function has($key) {
		$v = $this->mc->get($key);
		return ($v != Memcached::RES_NOTFOUND);
	}
	
	public function get($key) {
		$v = $this->mc->get($key);
		if ($v != Memcached::RES_NOTFOUND) {
			return $v;
		}
		return null;
	}
	
	public function set($key, $value, $duration = null) {
		$this->mc->set($key, $value, intval($duration));
	}
	
	// crmv@115378
	public function setMulti($values, $duration = null) {
		$this->mc->setMulti($values, intval($duration));
	}
	// crmv@115378e
	
	public function clear($key) {
		$this->mc->delete($key);
	}
	
	public function clearAll() {
		$this->mc->flush();
	}
	
	public function clearMatching($regexp) {
		$keys = $this->mc->getAllKeys();
		foreach ($keys as $k) {
			if (preg_match($regexp, $k)) {
				$this->clear($k);
			}
		}
	}

}
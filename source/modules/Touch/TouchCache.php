<?php
/*
 * Handler for caching stuff within a touch request
 * Very similar to the class Cache
 */

/* crmv@56798 crmv@93148 */

class TouchCache extends SDKExtendableUniqueClass {

	protected $enabled = true;				// false to deactivate the cache
	protected $cacheType = 'session';		// only session is supported at the moment
											// the Touch session is not shared with the web session

	public function getType() {
		return $this->cacheType;
	}
	
	public function isEnabled() {
		return $this->enabled;
	}
	
	public function disable($clear = false) {
		if (!$this->enabled) return true;
		
		if ($clear) $this->clear();
		$this->enabled = false;
		return true;
	}
	
	public function enable() {
		$this->enabled = true;
		return true;
	}
	
	/* return FALSE if not available in cache, otherwise the stored value.
	 * Therefore, you cannot store the value FALSE in the cache
	 */
	public function get($key) {
		if (!$this->enabled) return false;
	
		$key = $this->transformKey($key);
		
		if ($this->cacheType == 'session') {
			if (isset($_SESSION['cache'][$key])) {
				$expiration = $_SESSION['cache_expiration'][$key];
				if (!empty($expiration) && time() > $expiration) {
					$cs = $this->checkSession();
					unset($_SESSION['cache'][$key]);
					unset($_SESSION['cache_expiration'][$key]);
					if ($cs) $this->closeSession();
					return false;
				}
				return $_SESSION['cache'][$key];
			}
		} else {
			throw new Exception("The cache type is not supported");
		}
		return false;
	}
	
	public function set($key, $value, $life = null) {
		if (!$this->enabled) return false;
		
		$key = $this->transformKey($key);
		
		if ($this->cacheType == 'session') {
			$cs = $this->checkSession();
			$_SESSION['cache'][$key] = $value;
 			if (!empty($life) && is_int($life)) {
 				$expiration = time() + $life;
	 			$_SESSION['cache_expiration'][$key] = $expiration;
 			}
 			if ($cs) $this->closeSession();
		} else {
			throw new Exception("The cache type is not supported");
		}
		return true;
	}
	
	public function delete($key) {
		if (!$this->enabled) return false;
		
		$key = $this->transformKey($key);
		
		if ($this->cacheType == 'session') {
			$cs = $this->checkSession();
			unset($_SESSION['cache'][$key]);
	 		unset($_SESSION['cache_expiration'][$key]);
	 		if ($cs) $this->closeSession();
		} else {
			throw new Exception("The cache type is not supported");
		}
		return true;
	}
	
	// delete keys matching the regexp
	public function deleteMatching($regexp) {
		if (!$this->enabled) return false;
		
		if ($this->cacheType == 'session') {
			$cs = $this->checkSession();
			if (is_array($_SESSION['cache'])) {
				foreach ($_SESSION['cache'] as $key => $val) {
					if (preg_match($regexp, $key)) {
						unset($_SESSION['cache'][$key]);
						unset($_SESSION['cache_expiration'][$key]);
					}
				}
			}
			if ($cs) $this->closeSession();
		} else {
			throw new Exception("The cache type is not supported");
		}
		return true;
	}
	
	public function clear() {
		if ($this->cacheType == 'session') {
			$cs = $this->checkSession();
			unset($_SESSION['cache']);
			unset($_SESSION['cache_expiration']);
			if ($cs) $this->closeSession();
		} else {
			throw new Exception("The cache type is not supported");
		}
		return true;
	}
	
	protected function checkSession() {
		global $touchInst;
		return $touchInst->reopenWSSession();
	}
	
	protected function closeSession() {
		global $touchInst;
		return $touchInst->closeWSSession();
	}
	
	// in case the key should be changed (according to the user, or something...)
	protected function transformKey($key) {
		return $key;
	}

}
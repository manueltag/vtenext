<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

/* crmv@47905bis crmv@105600 */

require_once('include/utils/Cache/CacheStorage.php');
require_once('include/utils/Cache/CacheClasses.php');

class Cache {
	
	private $enabled;	// language do not respect this value and use always the cache
	private $type;		// file, session
	private $rootFolder = 'cache/sys/';
	private $name = '';
	private $extension = '.json';	// json is faster than php
	
	protected $scache;
	protected $fstorage;
	
	static function getInstance($name, $extension=null, $rootFolder=null) {
		return new Cache($name, $extension, $rootFolder);
	}
	
	function __construct($name, $extension=null, $rootFolder=null) {
		$this->name = $name;
		if (!empty($extension)) $this->extension = $extension;
		if (!empty($rootFolder)) $this->rootFolder = $rootFolder;

		// crmv@115378
		$this->enabled = PerformancePrefs::getBoolean('CACHE');
		$this->type = PerformancePrefs::get('CACHE_TYPE');
		// crmv@115378e
		
		if (in_array($name,array('vteCacheHV','mIiTtC','numberUsersMorphsuit','cacheResources'))) $this->type = 'file'; //crmv@61417 crmv@94125
		if (basename($_SERVER['PHP_SELF']) == 'install.php') $this->enabled = false;
		
		if ($this->enabled) {
			if ($this->type == 'session') {
				$this->scache = SCache::getInstance();
			} elseif ($this->type == 'file') {
				$this->fstorage = new CacheStorageFile($this->getFile(), 'json');
			}
		}
	}
	
	function getType() {
 		return $this->type;
 	}
 	
	function getFile() {
 		return $this->rootFolder.$this->name.$this->extension;
 	}
	
	function get() {
		if (!$this->enabled) return false;
		
		$r = false;
		if ($this->type == 'file') {
			$r = $this->fstorage->get('data');
			if ($r === null) return false;
		} elseif ($this->type == 'session') {
			$r = $this->scache->get($this->name);
			if ($r === null) return false;
		}
		return $r;
 	}
 	
 	function set($value, $life=null) {
 		if (!$this->enabled) return false;
 		
 		if ($this->type == 'file') {
	 		$this->fstorage->set('data', $value, $life);
 		} elseif ($this->type == 'session') {
			$this->scache->set($this->name, $value, $life);
 		}
 	}
 	
 	function clear($all_users=true) {
 		if (!$this->enabled) return false;

 		if ($this->type == 'file') {
	 		$cacheFile = $this->getFile();
	 		if (file_exists($cacheFile)) {
	 			$this->fstorage->clearAll();
	 		} elseif (is_dir($this->rootFolder.$this->name)) {	// ex. vte_languages, sdk_js_lang
	 			$files = glob($this->rootFolder.$this->name.'/*');
	 			foreach ($files as $file) {
	 				if (is_file($file)) unlink($file);
	 			}
	 		}
 		} elseif ($this->type == 'session') {
			$this->scache->clear($this->name);
			
			// crmv@106294
			if ($this->name == 'vte_languages') {
				$this->scache->clearMatching('#^SDK/vte_languages#');
			}
			// crmv@106294e
 			
 			global $adb, $table_prefix, $current_user;
 			if (Vtiger_Utils::CheckTable($table_prefix.'_reload_session')) {
	 			if ($all_users) {
	 				if ($adb->isMysql()) {
						$query = "insert ignore into {$table_prefix}_reload_session select id, \"{$this->name}\" as \"session_var\" from {$table_prefix}_users";
		 				if (!empty($current_user->id)) {
							$query .= " where id <> {$current_user->id}";
						}
	 				} else {
	 					//TODO
	 				}
					$adb->query($query);
	 			} elseif (!empty($current_user->id)) {
	 				$adb->pquery("delete from {$table_prefix}_reload_session where userid = ? and session_var = ?",array($current_user->id,$this->name));
	 			}
 			}
 		}
 	}
}


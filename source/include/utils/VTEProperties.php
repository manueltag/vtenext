<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@94084 crmv@115378 */

require_once('include/BaseClasses.php');
require_once('include/utils/Cache/CacheStorage.php');


/**
 * This class handles a basic key-value store with various configuration options for VTE.
 * For better performances, it uses a duble cache (file and request)
 */
class VTEProperties extends VTEUniqueClass {
	
	public $table_name_prop = '';
	
	public $use_cache = true;
	public $cache_file = 'cache/sys/vteprops.json';
	public $cache_format = 'json';
	
	protected $rcache;
	protected $fcache;
	
	protected $rcache_initialized = false;
	protected $fcache_initialized = false;
	
	// overrides valid only during request
	protected $req_overrides = array();
	
	// values to save during install
	protected $default_values = array(
	
		'smtp_editable' => 1,
		
		/* old performance.config values */
		
		// Enable log4php debugging only if requried 
		'performance.log4php_debug' => 0,
		
		// Should the caller information be captured in SQL Logging?
		// It adds little overhead for performance but will be useful to debug
		'performance.sql_log_include_caller' => 0,
		
		// crmv@47905 write timing into table tbl_s_logtime
		// log sql timing 
		'performance.sql_log_timing' => 0,
		
		// log app timing
		'performance.app_log_timing' => 0,
		
		// include backtrace while timing
		'performance.backtrace_log_timing' => 0,
		// crmv@47905e
		
		// If database default charset is UTF-8, set this to true 
		// This avoids executing the SET NAMES SQL for each query!
		'performance.db_default_charset_utf8' => 1,
		
		// Compute record change indication for each record shown on listview
		'performance.listview_record_change_indicator' => 0,
		
		// Turn-off default sorting in ListView, could eat up time as data grows
		'performance.listview_default_sorting' => 1,
		
		// Control DetailView Record Navigation
		'performance.detailview_record_navigation' => 0,
		
		// To control the Email Notifications being sent to the Owner
		// By default it is set to true, if it is set to false, then notifications will not be sent
		'performance.notify_owner_emails' => 1,
		
		// reduce number of ajax requests on home page, reduce this value if home page widget dont show value
		'performance.home_page_widget_group_size' => 12,
		
		//take backup legacy style, whenever an admin user logs out.
		'performance.logout_backup' => 1,
		
		// Show the record count in the related lists
		'performance.related_list_count' => 1, //crmv@25809
		
		// Limit above which the massedits will be done in background
		'performance.listview_mass_check_with_workflow' => 100, //crmv@27096
		
		// Check the notifications every this number of seconds
		'performance.notification_interval_time' => 240000, //crmv@35676
		
		// crmv@47905bis
		// Enable global VTE Cache
		'performance.cache' => 1,
		
		// Type of cache ('session' or 'file')
		'performance.cache_type' => 'session',
		// crmv@47905bise
		
		'performance.add_relation_in_full_page' => 1, //crmv@54245
		
		// True if the crmentity table is partitioned by setype
		'performance.crmentity_partitioned' => 0, //crmv@64325
		
		// If true, when popup is opened, the first module is automatically selected
		'performance.popup_autoselect_module' => 0, //crmv@65506
		
		// set to false to disable almost all temporary tables, good for db replication
		// if you change this from true to false, remember to recalculate the privileges
		'performance.use_temp_tables' => 0, // crmv@63349
		
		// set to true to enable logging of Javascript errors. For the log to be written, 
		// you also have to activate the LOG4PHP_DEBUG
		'performance.js_debug' => 0, // crmv@92034
		
		// crmv@96019
		// set to 'enable' to enable imap actions when pressing update button
		// set to 'disable' to reload messages list and waiting for cron update
		// set to 'fast_sync' to enable imap actions with interval INTERVAL_IMAP_FAST_SYNC
		'performance.messages_update_icon_perform_imap_actions' => 'enable',
		'performance.interval_imap_fast_sync' => '1 days',
		// crmv@96019e
		
		//crmv@94125
		// if true, resources (js, css, images) will be versioned
		'performance.version_resources' => 1,
		// how to create the versioned file ('link': symbolic link or 'copy')
		'performance.version_resources_method' => 'link',
		// if true, versioned files will be checked automatically for changes
		// note: may degrade performances due to many stat calls
		'performance.version_resources_autorefresh' => 0,
		//crmv@94125e
		
		// set to 0 to disable ajax editing from detail view
		'performance.detailview_ajax_edit' => 1,
		
		//crmv@118551
		'layout.default_detail_view' => '',	// (empty string) / summary
		'layout.enable_switch_detail_view' => 0,
		'layout.old_style' => 0,
		'layout.tb_relations_order' => 'num_of_records', // num_of_records / layout_editor
		'layout.enable_always_mandatory_css' => 0,	// use css class dvtCellInfoM also in edit view and other views
		//crmv@118551e
	);
	
	public function __construct() {
		global $table_prefix;
		
		$this->table_name_prop = $table_prefix.'_vteprop';
		
		// initializes the caches
		if ($this->use_cache) {
			$this->rcache = new CacheStorageVar();
			$this->fcache = new CacheStorageFile($this->cache_file, $this->cache_format);
			$this->fcache_initialized = !$this->fcache->isFileEmpty();
		}
	}
	
	/**
	 * Initialize default values (used during install)
	 */
	public function initDefaultProperties() {
		// set properties if not already set
		foreach ($this->default_values as $prop=>$value) {
			$oldVal = $this->getProperty($prop);
			if ($oldVal === null) $this->setProperty($prop, $value);
		}
	}
	
	/**
	 * Rename a property, taking care to update all the involved caches
	 */
	public function renameProperty($oldKey, $newKey) {
		global $adb;
		
		// database
		$r = $adb->pquery("UPDATE {$this->table_name_prop} SET property = ? WHERE property = ?", array($newKey, $oldKey));
		
		// req overrides
		if (isset($this->req_overrides[$oldKey])) {
			$this->req_overrides[$newKey] = $this->req_overrides[$oldKey];
			unset($this->req_overrides[$oldKey]);
		}
		
		if ($this->use_cache) {
			if ($this->rcache_initialized) {
				$oldval = $this->rcache->get($oldKey);
				if ($oldval !== null) {
					$this->rcache->set($newKey, $oldval);
					$this->rcache->clear($oldKey);
				}
			}
			if ($this->fcache_initialized) {
				$oldval = $this->fcache->get($oldKey);
				if ($oldval !== null) {
					$this->fcache->set($newKey, $oldval);
					$this->fcache->clear($oldKey);
				}
			}
		}
	}
	
	/**
	 * Alias for getProperty
	 */
	public function get($property, $noCache = false, $noOverride = false) {
		return $this->getProperty($property, $noCache, $noOverride);
	}
	
	/**
	 * Get all properties
	 */
	public function getAll($noCache = false, $noOverride = false) {
		// if skipping the override, skip the cache also (the cache stores only the overridden values)
		if ($noOverride) $noCache = true;
		
		if ($noCache || !$this->use_cache) {
			return $this->getAllPropertiesFromDB($noOverride);
		}
		
		if (!$this->rcache_initialized && !$this->fcache_initialized) {
			// no caches are valid, rebuild all
			$this->rebuildCache();
		} elseif (!$this->rcache_initialized) {
			// only file cache is valid, rebuild request cache
			$this->rebuildRCache();
		}
		
		$values = $this->rcache->getAll();
		return $values;		
	}
	
	/**
	 * Return a stored value
	 */
	public function getProperty($property, $noCache = false, $noOverride = false) {
		
		// if skipping the override, skip the cache also (the cache stores only the overridden values)
		if ($noOverride) $noCache = true;
		
		if ($noCache || !$this->use_cache) {
			return $this->getPropertyFromDB($property, $noOverride);
		}
		
		if (!$this->rcache_initialized && !$this->fcache_initialized) {
			// no caches are valid, rebuild all
			$this->rebuildCache();
		} elseif (!$this->rcache_initialized) {
			// only file cache is valid, rebuild request cache
			$this->rebuildRCache();
		}
		
		$value = $this->rcache->get($property);
		if ($value !== null) return $value;
		
		return null;
	}
	
	protected function getPropertyFromDB($property, $noOverride = false) {
		global $adb;
		
		// check if db connection is valid
		if (!$adb || !$adb->database || !$adb->database->IsConnected()) {
			return $this->getInstallValue($property);
		}

		$r = $adb->pquery("SELECT value, override_value FROM {$this->table_name_prop} WHERE property = ?", array($property));
		if ($r && $adb->num_rows($r) > 0) {
			$value = $adb->query_result_no_html($r, 0, 'value');
			$ovalue = $adb->query_result_no_html($r, 0, 'override_value');
			if (!$noOverride && $ovalue !== '' && !is_null($ovalue)) {
				$value = $ovalue;
			}
			return $value;
		}
		
		return null;
	}
	
	protected function getAllPropertiesFromDB($noOverride = false) {
		global $adb;
		
		// check if db connection is valid
		if (!$adb || !$adb->database || !$adb->database->IsConnected()) {
			return $this->getAllInstallValues();
		}
		
		$values = array();
		$r = $adb->query("SELECT property, value, override_value FROM {$this->table_name_prop}");
		if ($r) {
			while ($row = $adb->fetchByAssoc($r, -1, false)) {
				$values[$row['property']] = ($noOverride ? $row['value'] : ($row['override_value'] ?: $row['value']));
			}
		}
		return $values;
	}
	
	protected function getInstallValue($property) {
		if (isset($this->default_values[$property])) {
			return $property;
		}
		return null;
	}
	
	protected function getAllInstallValues() {
		return $this->default_values;
	}
	
	/**
	 * Alias for setProperty
	 */
	public function set($property, $value) {
		return $this->setProperty($property, $value);
	}
	
	public function setProperty($property, $value) {
		global $adb;
		
		$r = $adb->pquery("SELECT property FROM {$this->table_name_prop} WHERE property = ?", array($property));
		if ($r) {
			// transform values
			if ($value === false) {
				$value = '0';
			} elseif ($value === true) {
				$value = '1';
			}
			if ($adb->num_rows($r) > 0) {
				// update
				$r = $adb->pquery("UPDATE {$this->table_name_prop} SET value = ? WHERE property = ?", array($value, $property));
			} else {
				// insert
				$r = $adb->pquery("INSERT INTO {$this->table_name_prop} (property, value) VALUES (?,?)", array($property, $value));
			}
			if ($this->use_cache) {
				$this->rcache->set($property, $value);
				$this->fcache->set($property, $value);
			}
		}
	}
	
	/**
	 * Retrieve the overriden value, or null if not overridden
	 */
	public function getOverride($property) {
		global $adb;
		
		// if it was a request override, we have it here
		if (isset($this->req_overrides[$property])) {
			return $this->req_overrides[$property];
		}

		// otherwise check the db (in the file cache i don't know if it was the original value or not)
		$r = $adb->pquery("SELECT override_value FROM {$this->table_name_prop} WHERE property = ?", array($property));
		if ($r && $adb->num_rows($r) > 0) {
			$ovalue = $adb->query_result_no_html($r, 0, 'override_value');
			if ($ovalue !== '' && !is_null($ovalue)) {
				return $ovalue;
			}
		}
		
		return null;
	}
	
	/**
	 * Set an override for a property. The persistence is either "request" or "db"
	 */
	public function setOverride($property, $value, $persistence = 'db') {
		global $adb;
		
		if ($persistence == 'request') {
			$this->req_overrides[$property] = $value;
			if ($this->use_cache) {
				$this->rcache->set($property, $value);
			}
			return;
		}
		
		$r = $adb->pquery("SELECT value FROM {$this->table_name_prop} WHERE property = ?", array($property));
		if ($r) {
			if ($adb->num_rows($r) > 0) {
				// update
				$r = $adb->pquery("UPDATE {$this->table_name_prop} SET override_value = ? WHERE property = ?", array($value, $property));
			} else {
				// insert
				$r = $adb->pquery("INSERT INTO {$this->table_name_prop} (property, value, override_value) VALUES (?,?,?)", array($property, $value, $value));
			}
			if ($this->use_cache) {
				$this->rcache->set($property, $value);
				$this->fcache->set($property, $value);
			}
		}
	}
	
	/**
	 * Remove the override
	 */
	public function unsetOverride($property, $persistence = 'db') {
		global $adb;
		
		// get original value
		$value = $this->getPropertyFromDB($property, true);

		// remove it from req
		unset($this->req_overrides[$property]);
		
		if ($persistence == 'request') {
			if ($this->use_cache) {
				$this->rcache->set($property, $value);
			}
			return;
		}
		
		// unset it from the db
		$r = $adb->pquery("UPDATE {$this->table_name_prop} SET override_value = NULL WHERE property = ?", array($property));
		
		// restore caches
		if ($this->use_cache) {
			$this->rcache->set($property, $value);
			$this->fcache->set($property, $value);
		}
	}
	
	/**
	 * Remove all overrides
	 */
	public function unsetAllOverrides($persistence = 'db') {
		global $adb;
		
		// remove it from req
		$this->req_overrides = array();
		
		if ($persistence == 'request') {
			if ($this->use_cache) {
				$this->clearRCache();
			}
			return;
		}
		
		// unset it from the db
		$r = $adb->query("UPDATE {$this->table_name_prop} SET override_value = NULL");
		
		// clear caches
		if ($this->use_cache) {
			$this->clearCache();
		}
	}
	
	/**
	 * Remove a property
	 */
	public function deleteProperty($property) {
		global $adb;
		$r = $adb->pquery("DELETE FROM {$this->table_name_prop} WHERE property = ?", array($property));
		unset($this->req_overrides[$property]);
		if ($this->use_cache) {
			$this->rcache->clear($property);
			$this->fcache->clear($property);
		}
	}
	
	public function clearCache() {
		$this->clearRCache();
		$this->clearFCache();
	}
	
	protected function clearRCache() {
		$this->rcache->clearAll();
		$this->rcache_initialized = false;
	}
	
	protected function clearFCache() {
		$this->fcache->clearAll();
		$this->fcache_initialized = false;
	}
	
	public function rebuildCache() {
		$this->rebuildFCache();
		$this->rebuildRCache();
	}
	
	protected function rebuildRCache() {
		
		$this->rcache->clearAll();
		$this->rcache_initialized = false;

		// get the values from the file cache
		if ($this->fcache_initialized) {
			$cache = $this->fcache->getAll();
		} else {
			$cache = $this->getAllPropertiesFromDB();
		}
		// apply request overrides
		if (is_array($this->req_overrides)) {
			$cache = array_merge($cache, $this->req_overrides);
		}
		$this->rcache->setMulti($cache);
		$this->rcache_initialized = true;

	}
	
	protected function rebuildFCache($cache = null) {

		$this->fcache->clearAll();
		$this->fcache_initialized = false;
		
		if (!$cache) {
			$cache = $this->getAllPropertiesFromDB();
		}
		
		$this->fcache->setMulti($cache);
		$this->fcache_initialized = true;

	}

}

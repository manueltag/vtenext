<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once('vtlib/Vtiger/ModuleBasic.php');

/**
 * Provides API to work with vtiger CRM Modules
 * @package vtlib
 */
class Vtiger_Module extends Vtiger_ModuleBasic {

	/**
	 * Get unique id for related list
	 * @access private
	 */
	function __getRelatedListUniqueId() {
		global $adb,$table_prefix;
		return $adb->getUniqueID($table_prefix.'_relatedlists');
	}

	/**
	 * Get related list sequence to use
	 * @access private
	 */
	function __getNextRelatedListSequence() {
		global $adb,$table_prefix;
		$max_sequence = 0;
		$result = $adb->pquery("SELECT max(sequence) as maxsequence FROM ".$table_prefix."_relatedlists WHERE tabid=?", Array($this->id));
		if($adb->num_rows($result)) $max_sequence = $adb->query_result($result, 0, 'maxsequence');
		return ++$max_sequence;
	}

	/**
	 * Set related list information between other module
	 * @param Vtiger_Module Instance of target module with which relation should be setup
	 * @param String Label to display in related list (default is target module name)
	 * @param Array List of action button to show ('ADD', 'SELECT')
	 * @param String Callback function name of this module to use as handler
	 *
	 * @internal Creates table vtiger_crmentityrel if it does not exists
	 */
	function setRelatedList($moduleInstance, $label='', $actions=false, $function_name='get_related_list') {
		global $adb,$table_prefix;

		if(empty($moduleInstance)) return;

		Vtiger_Utils::CreateTable(
			$table_prefix.'_crmentityrel',
			'crmid I(19) NOTNULL, 
			module C(100) NOTNULL, 
			relcrmid I(19) NOTNULL, 
			relmodule C(100) NOTNULL',
			true
		);

		$relation_id = $this->__getRelatedListUniqueId();
		$sequence = $this->__getNextRelatedListSequence();
		$presence = 0; // 0 - Enabled, 1 - Disabled

		if(empty($label)) $label = $moduleInstance->name;

		// Allow ADD action of other module records (default)
		if($actions === false) $actions = Array('ADD');

		$useactions_text = $actions;
		if(is_array($actions)) $useactions_text = implode(',', $actions);
		$useactions_text = strtoupper($useactions_text);

		// Add column to vtiger_relatedlists to save extended actions
		Vtiger_Utils::AlterTable($table_prefix.'_relatedlists', 'actions C(50)');

		$adb->pquery("INSERT INTO ".$table_prefix."_relatedlists(relation_id,tabid,related_tabid,name,sequence,label,presence,actions) VALUES(?,?,?,?,?,?,?,?)",
			Array($relation_id,$this->id,$moduleInstance->id,$function_name,$sequence,$label,$presence,$useactions_text));

		self::log("Setting relation with $moduleInstance->name [$useactions_text] ... DONE");
	}

	/**
	 * Unset related list information that exists with other module
	 * @param Vtiger_Module Instance of target module with which relation should be setup
	 * @param String Label to display in related list (default is target module name)
	 * @param String Callback function name of this module to use as handler
	 */
	function unsetRelatedList($moduleInstance, $label='', $function_name='get_related_list') {
		global $adb,$table_prefix;

		if(empty($moduleInstance)) return;

		if(empty($label)) $label = $moduleInstance->name;

		$adb->pquery("DELETE FROM ".$table_prefix."_relatedlists WHERE tabid=? AND related_tabid=? AND name=? AND label=?", 
			Array($this->id, $moduleInstance->id, $function_name, $label));

		self::log("Unsetting relation with $moduleInstance->name ... DONE");		
	}

	/**
	 * Add custom link for a module page
	 * @param String Type can be like 'DETAILVIEW', 'LISTVIEW' etc.. 
 	 * @param String Label to use for display
	 * @param String HREF value to use for generated link 
	 * @param String Path to the image file (relative or absolute)
	 * @param Integer Sequence of appearance
	 *
	 * NOTE: $url can have variables like $MODULE (module for which link is associated), 
	 * $RECORD (record on which link is dispalyed)
	 */
	//crmv@37303
	function addLink($type, $label, $url, $iconpath='', $sequence=0, $condition='', $size=1) {
		Vtiger_Link::addLink($this->id, $type, $label, $url, $iconpath, $sequence, $condition, $size);
	}
	//crmv@37303e

	/**
	 * Delete custom link of a module
	 * @param String Type can be like 'DETAILVIEW', 'LISTVIEW' etc.. 
 	 * @param String Display label to lookup
	 * @param String URL value to lookup
	 */
	function deleteLink($type, $label, $url=false) {
		Vtiger_Link::deleteLink($this->id, $type, $label, $url);
	}

	/**
	 * Get all the custom links related to this module.
	 */
	function getLinks() {
		return Vtiger_Link::getAll($this->id);
	}

	/**
	 * Initialize webservice setup for this module instance.
	 */
	function initWebservice() {
		Vtiger_Webservice::initialize($this);
	}

	// crmv@105600
	/**
	 * Get instance by id or name
	 * @param mixed id or name of the module
	 */
	static function getInstance($value) {
		global $adb,$table_prefix;
		
		$cache = RCache::getInstance();
		$key = "vtmodule_instances_".$value;
		
		$instance = $cache->get($key);
		if ($instance) return $instance;
		
		$query = $instance = false;
		if(Vtiger_Utils::isNumber($value)) {
			$query = "SELECT * FROM ".$table_prefix."_tab WHERE tabid=?";
		} else {
			$query = "SELECT * FROM ".$table_prefix."_tab WHERE name=?";
		}
		$result = $adb->pquery($query, Array($value));
		if($adb->num_rows($result)) {
			$class = get_called_class() ?: get_class();
			$instance = new $class();
			$instance->initialize($adb->fetch_array($result));
		}
		$cache->set($key, $instance);

		return $instance;
	}
	// crmv@105600e

	/**
	 * Get instance of the module class.
	 * @param String Module name
	 */
	static function getClassInstance($modulename) {
		require_once('data/CRMEntity.php');
		return CRMEntity::getInstance($modulename);
	}

	/**
	 * Fire the event for the module (if vtlib_handler is defined)
	 */
	static function fireEvent($modulename, $event_type, $version='') {	//crmv@fix
		$instance = self::getClassInstance((string)$modulename);
		$instance->version = $version;	//crmv@fix
		if($instance) {
			if(method_exists($instance, 'vtlib_handler')) {
				self::log("Invoking vtlib_handler for $event_type ...START");
				$instance->vtlib_handler((string)$modulename, (string)$event_type);
				self::log("Invoking vtlib_handler for $event_type ...DONE");
			}
		}
	}
	
	//crmv@27711
	function hide($flags) {
		global $adb;
		//crmv@33465 fix mancanza vte_hide_tab
		if (!$adb->table_exist('vte_hide_tab')){
			self::log("Hide table DON'T EXIST! ... FAILED");
			return;
		}
		//crmv@33465e	
		$result = $adb->pquery('select * from vte_hide_tab where tabid = ?',array($this->id));
		if (!$result || $adb->num_rows($result) == 0) {
			$adb->pquery('insert into vte_hide_tab (tabid) values (?)',array($this->id));
		}
		if (!empty($flags)) {
			foreach ($flags as $k => $v) {
				$adb->pquery("update vte_hide_tab set $k = ? where tabid = ?",array($v,$this->id));
			}
			self::log("Hide $moduleInstance->name [".implode(', ',array_keys($flags))."] ... DONE");
		} else {
			self::log("Hide $moduleInstance->name ... FAILED");
		}
	}
	//crmv@27711e
}

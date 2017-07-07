<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/

/* crmv@104975 */
 
require_once('vtlib/Vtiger/Utils.php');


/**
 * Provides API to work with vtiger CRM Module Blocks
 * @package vtlib
 */
class Vtiger_Block {
	/** ID of this block instance */
	var $id;
	/** Label for this block instance */
	var $label;

	var $sequence;
	var $showtitle = 0;
	var $visible = 0;
	var $increateview = 0;
	var $ineditview = 0;
	var $indetailview = 0;
	
	var $module;
	
	public $panel;

	/**
	 * Constructor
	 */
	function __construct() {
	}

	/**
	 * Get unquie id for this instance
	 * @access private
	 */
	function __getUniqueId() {
		global $adb,$table_prefix;

		/** Sequence table was added from 5.1.0 */
		$maxblockid = $adb->getUniqueID($table_prefix.'_blocks');
		return $maxblockid;
	}

	/**
	 * Get next sequence value to use for this block instance
	 * @access private
	 */
	function __getNextSequence() {
		global $adb,$table_prefix;
		$result = $adb->pquery("SELECT MAX(sequence) as max_sequence from ".$table_prefix."_blocks where tabid = ?", Array($this->module->id));
		$maxseq = 0;
		if($adb->num_rows($result)) {
			$maxseq = $adb->query_result($result, 0, 'max_sequence');
		}
		return ++$maxseq;
	}

	/**
	 * Initialize this block instance
	 * @param Array Map of column name and value
	 * @param Vtiger_Module Instance of module to which this block is associated
	 * @access private
	 */
	function initialize($valuemap, $moduleInstance=false) {
		$this->id = $valuemap[blockid];
		$this->label= $valuemap[blocklabel];
		$this->module=$moduleInstance? $moduleInstance: Vtiger_Module::getInstance($valuemap[tabid]);
		
		if ($valuemap['panelid']) {
			$this->panel = Vtiger_Panel::getInstance($valuemap['panelid']);
		}
	}

	/**
	 * Create vtiger CRM block
	 * @access private
	 */
	function __create($moduleInstance) {
		global $adb,$table_prefix, $metaLogs; // crmv@49398

		$this->module = $moduleInstance;

		$this->id = $this->__getUniqueId();
		if(!$this->sequence) $this->sequence = $this->__getNextSequence();
		
		$panelId = 0;
		if (!$this->panel) {
			$this->panel = Vtiger_Panel::getFirstForModule($moduleInstance);
			// crmv@105426
			if ($this->panel) {
				$panelId = $this->panel->id;
			} else {
				// create a default one
				$this->panel = Vtiger_Panel::createDefaultPanel($moduleInstance);
				if ($this->panel) {
					$panelId = $this->panel->id;
				}
			}
			// crmv@105426e
		} else {
			$panelId = $this->panel->id;
		}

		$adb->pquery("INSERT INTO ".$table_prefix."_blocks(blockid,tabid,panelid,blocklabel,sequence,show_title,visible,create_view,edit_view,detail_view)
			VALUES(?,?,?,?,?,?,?,?,?,?)", Array($this->id, $this->module->id, $panelId, $this->label,$this->sequence,
			$this->showtitle, $this->visible,$this->increateview, $this->ineditview, $this->indetailview));

		if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_ADDBLOCK, $this->id); // crmv@49398
		self::log("Creating Block $this->label ... DONE");
		self::log("Module language entry for $this->label ... CHECK");
	}

	/**
	 * Update vtiger CRM block
	 * @access private
	 * @internal TODO
	 */
	function __update() {
		self::log("Updating Block $this->label ... DONE");
	}

	/**
	 * Delete this instance
	 * @access private
	 */
	function __delete() {
		global $adb,$table_prefix, $metaLogs; // crmv@49398
		self::log("Deleting Block $this->label ... ", false);
		$adb->pquery("DELETE FROM ".$table_prefix."_blocks WHERE blockid=?", Array($this->id));
		if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_DELBLOCK, $this->id, array('module'=>$this->module->name)); // crmv@49398
		self::log("DONE");
	}

	/**
	 * Save this block instance
	 * @param Vtiger_Module Instance of the module to which this block is associated
	 */
	function save($moduleInstance=false) {
		if($this->id) $this->__update();
		else $this->__create($moduleInstance);
		return $this->id;
	}

	/**
	 * Delete block instance
	 * @param Boolean True to delete associated fields, False to avoid it
	 */
	function delete($recursive=true) {
		if($recursive) {
			$fields = Vtiger_Field::getAllForBlock($this);
			foreach($fields as $fieldInstance) $fieldInstance->delete($recursive);
		}
		$this->__delete();
	}

	/**
	 * Add field to this block
	 * @param Vtiger_Field Instance of field to add to this block.
	 * @return Reference to this block instance
	 */
	function addField($fieldInstance) {
		$fieldInstance->save($this);
		return $this;
	}

	/**
	 * Helper function to log messages
	 * @param String Message to log
	 * @param Boolean true appends linebreak, false to avoid it
	 * @access private
	 */
	static function log($message, $delim=true) {
		Vtiger_Utils::Log($message, $delim);
	}

	/**
	 * Get instance of block
	 * @param mixed block id or block label
	 * @param Vtiger_Module Instance of the module if block label is passed
	 */
	static function getInstance($value, $moduleInstance=false) {
		global $adb,$table_prefix;
		$instance = false;

		$query = false;
		$queryParams = false;
		if(Vtiger_Utils::isNumber($value)) {
			$query = "SELECT * FROM ".$table_prefix."_blocks WHERE blockid=?";
			$queryParams = Array($value);
		} else {
			$query = "SELECT * FROM ".$table_prefix."_blocks WHERE blocklabel=? AND tabid=?";
			$queryParams = Array($value, $moduleInstance->id);
		}
		$result = $adb->pquery($query, $queryParams);
		if($adb->num_rows($result)) {
			$class = get_called_class() ?: get_class();
			$instance = new $class();
			$instance->initialize($adb->fetch_array($result), $moduleInstance);
		}
		return $instance;
	}

	/**
	 * Get all block instances associated with the module
	 * @param Vtiger_Module Instance of the module
	 */
	static function getAllForModule($moduleInstance) {
		global $adb, $table_prefix;
		$instances = false;

		$query = "SELECT * FROM ".$table_prefix."_blocks WHERE tabid=?";
		$queryParams = Array($moduleInstance->id);

		$result = $adb->pquery($query, $queryParams);
		for($index = 0; $index < $adb->num_rows($result); ++$index) {
			$class = get_called_class() ?: get_class();
			$instance = new $class();
			$instance->initialize($adb->fetch_array($result), $moduleInstance);
			$instances[] = $instance;
		}
		return $instances;
	}
	
	static function getAllForTab($tabInstance) {
		global $adb, $table_prefix;
		$instances = false;

		$query = "SELECT * FROM ".$table_prefix."_blocks WHERE panelid=?";
		$queryParams = Array($tabInstance->id);

		$class = get_called_class() ?: get_class();
		$result = $adb->pquery($query, $queryParams);
		for($index = 0; $index < $adb->num_rows($result); ++$index) {
			$instance = new $class();
			$instance->initialize($adb->fetch_array($result), $tabInstance->module);
			$instances[] = $instance;
		}
		return $instances;
	}

	/**
	 * Delete all blocks associated with module
	 * @param Vtiger_Module Instnace of module to use
	 * @param Boolean true to delete associated fields, false otherwise
	 * @access private
	 */
	static function deleteForModule($moduleInstance, $recursive=true) {
		global $adb,$table_prefix, $metaLogs; // crmv@49398
		if($recursive) Vtiger_Field::deleteForModule($moduleInstance);
		$adb->pquery("DELETE FROM ".$table_prefix."_blocks WHERE tabid=?", Array($moduleInstance->id));
		if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_EDITMODFIELDS , $moduleInstance->id, array('module'=>$moduleInstance->name)); // crmv@49398
		self::log("Deleting blocks for module ... DONE");
	}

	//crmv@18954
	function moveHereFields($fieldnames){
		global $adb,$table_prefix, $metaLogs; // crmv@49398

		$tabid=$this->module->id;

		foreach($fieldnames as $fieldname){
			$query="UPDATE ".$table_prefix."_field SET block=? WHERE fieldname=? AND tabid=?";
			$params=array($this->id,$fieldname,$tabid);
			$adb->pquery($query,$params);
			self::log("Move $fieldname for $this->label ... DONE");
		}
		if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_EDITMODFIELDS , $tabid, array('module'=>$this->module->name)); // crmv@49398
	}
	//crmv@18954e
}

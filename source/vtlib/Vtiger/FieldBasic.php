<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

/**
 * Provides basic API to work with vtiger CRM Fields
 * @package vtlib
 */
class Vtiger_FieldBasic {
	/** ID of this field instance */
	var $id;
	var $name;
	var $label = false;
	var $table = false;
	var $column = false;
	var $columntype = false;
	var $helpinfo = '';
	var $masseditable = 1; // Default: Enable massedit for field

	var $uitype = 1;
	var $typeofdata = 'V~O';
	var	$displaytype   = 1;

	var $generatedtype = 1;
	var	$readonly      = 1;
	var	$presence      = 2;
	var	$selected      = 0;
	var	$maximumlength = 100;
	var	$sequence      = false;
	var	$quickcreate   = 1;
	var	$quicksequence = false;
	var	$info_type     = 'BAS';

	var $block;

	/**
	 * Constructor
	 */
	function __construct() {
	}

	/**
	 * Initialize this instance
	 * @param Array
	 * @param Vtiger_Module Instance of module to which this field belongs
	 * @param Vtiger_Block Instance of block to which this field belongs
	 * @access private
	 */
	function initialize($valuemap, $moduleInstance=false, $blockInstance=false) {
		$this->id = $valuemap['fieldid'];
		$this->name = $valuemap['fieldname'];
		$this->label= $valuemap['fieldlabel'];
		$this->column = $valuemap['columnname'];
		$this->table  = $valuemap['tablename'];
		$this->uitype = $valuemap['uitype'];
		$this->typeofdata = $valuemap['typeofdata'];
		$this->helpinfo = $valuemap['helpinfo'];
		$this->masseditable = $valuemap['masseditable'];
		$this->block= $blockInstance? $blockInstance : Vtiger_Block::getInstance($valuemap['block'], $moduleInstance);
	}

	/** Cache (Record) the schema changes to improve performance */
	static $__cacheSchemaChanges = Array();

	/**
	 * Initialize vtiger schema changes.
	 * @access private
	 */
	function __handleVtigerCoreSchemaChanges() {
		// Add helpinfo column to the vtiger_field table
		global $table_prefix;
		if(empty(self::$__cacheSchemaChanges[$table_prefix.'_field.helpinfo'])) {
			Vtiger_Utils::AlterTable($table_prefix.'_field', 'helpinfo  X');
			self::$__cacheSchemaChanges[$table_prefix.'_field.helpinfo'] = true;
		}
	}

	/**
	 * Get unique id for this instance
	 * @access private
	 */
	function __getUniqueId() {
		global $adb,$table_prefix;
		return $adb->getUniqueID($table_prefix.'_field');
	}

	/**
	 * Get next sequence id to use within a block for this instance
	 * @access private
	 */
	function __getNextSequence() {
		global $adb,$table_prefix;
		$result = $adb->pquery("SELECT MAX(sequence) AS max_seq FROM ".$table_prefix."_field WHERE tabid=? AND block=?",
			Array($this->getModuleId(), $this->getBlockId()));
		$maxseq = 0;
		if($result && $adb->num_rows($result)) {
			$maxseq = $adb->query_result($result, 0, 'max_seq');
			$maxseq += 1;
		}
		return $maxseq;
	}

	/**
	 * Get next quick create sequence id for this instance
	 * @access private
	 */
	function __getNextQuickCreateSequence() {
		global $adb,$table_prefix;
		$result = $adb->pquery("SELECT MAX(quickcreatesequence) AS max_quickcreateseq FROM ".$table_prefix."_field WHERE tabid=?",
			Array($this->getModuleId()));
		$max_quickcreateseq = 0;
		if($result && $adb->num_rows($result)) {
			$max_quickcreateseq = $adb->query_result($result, 0, 'max_quickcreateseq');
			$max_quickcreateseq += 1;
		}
		return $max_quickcreateseq;
	}

	/**
	 * Create this field instance
	 * @param Vtiger_Block Instance of the block to use
	 * @access private
	 */
	function __create($blockInstance) {
		$this->__handleVtigerCoreSchemaChanges();

		global $adb,$table_prefix, $metaLogs; // crmv@49398

		$this->block = $blockInstance;

		$moduleInstance = $this->getModuleInstance();

		$this->id = $this->__getUniqueId();

		if(!$this->sequence) {
			$this->sequence = $this->__getNextSequence();
		}
		//crmv@fix quickcreate
		if ($this->quickcreate === ''){
			$this->quickcreate = 1;
		}
		//crmv@fix quickcreate end
		if($this->quickcreate != 1) { // If enabled for display
			if(!$this->quicksequence) {
				$this->quicksequence = $this->__getNextQuickCreateSequence();
			}
		} else {
			$this->quicksequence = 0;
		}

		// Initialize other variables which are not done
		if(!$this->table) $this->table = $moduleInstance->basetable;
		if(!$this->column) {
			$this->column = strtolower($this->name);
			if(!$this->columntype) $this->columntype = 'C(100)';
		}
		//crmv@27654
		if($this->table == $table_prefix.'_crmentity' && in_array($this->column,array('smcreatorid','smownerid','modifiedby','description','createdtime','modifiedtime','viewedtime'))) {
			$this->columntype = null;	//skip alter table
		}
		//crmv@27654e
		if(!$this->label) $this->label = $this->name;
		//crmv@fix quicksequence
		if ($this->quicksequence == '')
			$this->quicksequence = null;
		//crmv@fix quicksequence end
		//crmv@30456
		if(strpos($this->table, 'TABLEPREFIX') !== false){
			$this->table=str_replace('TABLEPREFIX', $table_prefix, $this->table);
		}
		//crmv@30456e
		$params = Array($this->getModuleId(), $this->id, $this->column, $this->table, $this->generatedtype,
				$this->uitype, $this->name, $this->label, $this->readonly, $this->presence, $this->selected,
				$this->maximumlength, $this->sequence, $this->getBlockId(), $this->displaytype, $this->typeofdata,
				$this->quickcreate, $this->quicksequence, $this->info_type, $this->helpinfo);
		$sql ="INSERT INTO ".$table_prefix."_field (tabid, fieldid, columnname, tablename, generatedtype,
			uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence,
			block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, helpinfo)
			VALUES (".generateQuestionMarks($params).")";
		$adb->pquery($sql,$params);
		// Set the field status for mass-edit (if set)
		$adb->pquery('UPDATE '.$table_prefix.'_field SET masseditable=? WHERE fieldid=?', Array($this->masseditable, $this->id));

		Vtiger_Profile::initForField($this);

		if(!empty($this->columntype)) {
			Vtiger_Utils::AlterTable($this->table, $this->column." ".$this->columntype);
		}

		if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_ADDFIELD, $this->id); //crmv@49398
		self::log("Creating Field $this->name ... DONE");
		self::log("Module language mapping for $this->label ... CHECK");
	}

	/**
	 * Update this field instance
	 * @access private
	 * @internal TODO
	 */
	function __update() {
		self::log("Updating Field $this->name ... DONE");
	}

	/**
	 * Delete this field instance
	 * @access private
	 */
	function __delete() {
		global $adb,$table_prefix, $metaLogs; // crmv@49398

		Vtiger_Profile::deleteForField($this);

		$adb->pquery("DELETE FROM ".$table_prefix."_field WHERE fieldid=?", Array($this->id));
		if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_DELFIELD, $this->id); //crmv@49398
		self::log("Deleting Field $this->name ... DONE");
	}

	/**
	 * Get block id to which this field instance is associated
	 */
	function getBlockId() {
		return $this->block->id;
	}

	/**
	 * Get module id to which this field instance is associated
	 */
	function getModuleId() {
		return $this->block->module->id;
	}

	/**
	 * Get module name to which this field instance is associated
	 */
	function getModuleName() {
		return $this->block->module->name;
	}

	/**
	 * Get module instance to which this field instance is associated
	 */
	function getModuleInstance(){
		return $this->block->module;
	}

	/**
	 * Save this field instance
	 * @param Vtiger_Block Instance of block to which this field should be added.
	 */
	function save($blockInstance=false) {
		if($this->id) $this->__update();
		else $this->__create($blockInstance);
		return $this->id;
	}

	/**
	 * Delete this field instance
	 */
	function delete() {
		$this->__delete();
	}

	/**
	 * Set Help Information for this instance.
	 * @param String Help text (content)
	 */
	function setHelpInfo($helptext) {
		// Make sure to initialize the core tables first
		$this->__handleVtigerCoreSchemaChanges();

		global $adb,$table_prefix;
		$adb->pquery('UPDATE '.$table_prefix.'_field SET helpinfo=? WHERE fieldid=?', Array($helptext, $this->id));
		self::log("Updated help information of $this->name ... DONE");
	}

	/**
	 * Set Masseditable information for this instance.
	 * @param Integer Masseditable value
	 */
	function setMassEditable($value) {
		global $adb,$table_prefix;
		$adb->pquery("UPDATE ".$table_prefix."_field SET masseditable=? WHERE fieldid=?", Array($value, $this->id));
		self::log("Updated masseditable information of $this->name ... DONE");
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
}

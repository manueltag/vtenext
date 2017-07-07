<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

/* crmv@104975 */
 
require_once('vtlib/Vtiger/Utils.php');

/**
 * Provides API to work with vtiger CRM Module Blocks
 * @package vtlib
 */
class Vtiger_Panel {

	/** ID of this panel instance */
	var $id;
	
	/** Label for this panel instance */
	var $label;

	var $sequence;

	var $visible = 0;

	var $module;

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
		$maxblockid = $adb->getUniqueID($table_prefix.'_panels');
		return $maxblockid;
	}

	/**
	 * Get next sequence value to use for this block instance
	 * @access private
	 */
	function __getNextSequence() {
		global $adb,$table_prefix;
		$result = $adb->pquery("SELECT MAX(sequence) as max_sequence from ".$table_prefix."_panels where tabid = ?", Array($this->module->id));
		$maxseq = 0;
		if($adb->num_rows($result)) {
			$maxseq = $adb->query_result_no_html($result, 0, 'max_sequence');
		}
		return ++$maxseq;
	}

	/**
	 * Initialize this panel instance
	 * @param Array Map of column name and value
	 * @param Vtiger_Module Instance of module to which this panel is associated
	 * @access private
	 */
	function initialize($valuemap, $moduleInstance=false) {
		$this->id = $valuemap['panelid'];
		$this->label= $valuemap['panellabel'];
		if (isset($valuemap['visible'])) {
			$this->visible = intval($valuemap['visible']);
		}
		$this->module = $moduleInstance ? $moduleInstance : Vtiger_Module::getInstance($valuemap['tabid']);
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

		$adb->pquery(
			"INSERT INTO ".$table_prefix."_panels (panelid,tabid,panellabel,sequence,visible)
			VALUES(?,?,?,?,?)",
			array($this->id, $this->module->id, $this->label,$this->sequence,$this->visible)
		);

		//if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_ADDBLOCK, $this->id); // crmv@49398
		self::log("Creating Panel $this->label ... DONE");
	}

	/**
	 * Update vtiger CRM tab
	 * @access private
	 * @internal
	 */
	function __update() {
		global $adb,$table_prefix;
		self::log("Updating Panel $this->label ...", false);
		$adb->pquery(
			"UPDATE {$table_prefix}_panels SET panellabel = ?, visible = ? WHERE panelid = ?",
			array($this->label, $this->visible, $this->id)
		);
		self::log("DONE");
	}

	/**
	 * Delete this instance
	 * @access private
	 */
	function __delete() {
		global $adb,$table_prefix, $metaLogs; // crmv@49398
		self::log("Deleting Panel $this->label ... ", false);
		$adb->pquery("DELETE FROM ".$table_prefix."_panels WHERE panelid = ?", Array($this->id));
		//if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_DELBLOCK, $this->id, array('module'=>$this->module->name)); // crmv@49398
		self::log("DONE");
	}

	/**
	 * Save this panel instance
	 * @param Vtiger_Module Instance of the module to which this panel is associated
	 */
	function save($moduleInstance=false) {
		if ($this->id) $this->__update();
		else $this->__create($moduleInstance);
		return $this->id;
	}

	/**
	 * Delete panel instance
	 */
	function delete($moveBlocksTo = false) {
		global $adb,$table_prefix;
		// move the blocks
		if ($moveBlocksTo) {
			if (is_numeric($moveBlocksTo)) {
				$moveid = $moveBlocksTo;
			} else {
				$moveid = $moveBlocksTo->id;
			}
			$adb->pquery("UPDATE {$table_prefix}_blocks SET panelid = ? WHERE panelid = ?", array($moveid, $this->id));
		}
		$this->__delete();
	}

	/**
	 * Add block to this tab
	 * @param Vtiger_Block Instance of block to add to this tab.
	 * @return Reference to this panel instance
	 */
	function addBlock($blockInstance) {
		$blockInstance->panel = $this;
		$blockInstance->save($this->module);
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
	 * Get instance of tab
	 * @param mixed block id or block label
	 * @param Vtiger_Module Instance of the module if block label is passed
	 */
	static function getInstance($value, $moduleInstance=false) {
		global $adb,$table_prefix;
		$instance = false;

		$query = false;
		$queryParams = false;
		if(Vtiger_Utils::isNumber($value)) {
			$query = "SELECT * FROM ".$table_prefix."_panels WHERE panelid = ?";
			$queryParams = Array($value);
		} else {
			$query = "SELECT * FROM ".$table_prefix."_panels WHERE panellabel = ? AND tabid = ?";
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
	 * Get all tabs instances associated with the module
	 * @param Vtiger_Module Instance of the module
	 */
	static function getAllForModule($moduleInstance) {
		global $adb, $table_prefix;
		$instances = false;

		$query = "SELECT * FROM ".$table_prefix."_panels WHERE tabid = ? ORDER BY sequence ASC";
		$queryParams = Array($moduleInstance->id);

		$class = get_called_class() ?: get_class();
		$result = $adb->pquery($query, $queryParams);
		for($index = 0; $index < $adb->num_rows($result); ++$index) {
			$instance = new $class();
			$instance->initialize($adb->fetch_array($result), $moduleInstance);
			$instances[] = $instance;
		}
		return $instances;
	}
	
	static function getFirstForModule($moduleInstance) {
		global $adb, $table_prefix;
		$instance = false;

		$query = "SELECT * FROM ".$table_prefix."_panels WHERE tabid = ? ORDER BY sequence ASC";
		$queryParams = Array($moduleInstance->id);

		$result = $adb->limitpQuery($query, 0, 1, $queryParams);
		if ($result && $adb->num_rows($result) > 0) {
			$class = get_called_class() ?: get_class();
			$instance = new $class();
			$instance->initialize($adb->fetch_array($result), $moduleInstance);
		}
		
		return $instance;
	}

	// crmv@105426
	static function createDefaultPanel($moduleInstance) {
		$panelInstance = new Vtiger_Panel();
		$panelInstance->label = 'LBL_TAB_MAIN';
		$moduleInstance->addPanel($panelInstance);
		return $panelInstance;
	}
	// crmv@105426e
	
	/**
	 * Delete all tabs associated with module
	 * @param Vtiger_Module Instnace of module to use
	 * @param Boolean true to delete associated fields, false otherwise
	 * @access private
	 */
	/*static function deleteForModule($moduleInstance, $recursive=true) {
		global $adb,$table_prefix, $metaLogs; // crmv@49398
		if($recursive) Vtiger_Field::deleteForModule($moduleInstance);
		$adb->pquery("DELETE FROM ".$table_prefix."_blocks WHERE tabid=?", Array($moduleInstance->id));
		if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_EDITMODFIELDS , $moduleInstance->id, array('module'=>$moduleInstance->name)); // crmv@49398
		self::log("Deleting blocks for module ... DONE");
	}*/

	function moveHereBlocks($blocknames){
		global $adb,$table_prefix, $metaLogs; // crmv@49398

		$tabid = $this->module->id;
		if (!is_array($blocknames)) $blocknames = array($blocknames);

		foreach($blocknames as $blockname){
			if (is_numeric($blockname)) {
				$query="UPDATE ".$table_prefix."_blocks SET panelid = ? WHERE blockid = ? AND tabid = ?";
			} else {
				$query="UPDATE ".$table_prefix."_blocks SET panelid = ? WHERE blocklabel = ? AND tabid = ?";
			}
			$params=array($this->id,$blockname,$tabid);
			$adb->pquery($query,$params);
			self::log("Move $blockname for $this->label ... DONE");
		}
		//if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_EDITMODFIELDS , $tabid, array('module'=>$this->module->name)); // crmv@49398
	}
	
	function getRelatedLists() {
		global $adb,$table_prefix;
		$list = array();
		
		// check if exists
		$res = $adb->pquery(
			"SELECT pr.relation_id, pr.sequence, rt.name as module
			FROM {$table_prefix}_panel2rlist pr
			INNER JOIN {$table_prefix}_relatedlists rl ON rl.relation_id = pr.relation_id
			INNER JOIN {$table_prefix}_tab rt ON rt.tabid = rl.related_tabid
			WHERE pr.panelid = ? 
			ORDER BY pr.sequence ASC", 
			array($this->id)
		);
		if ($res && $adb->num_rows($res) > 0) {
			while ($row = $adb->fetchByAssoc($res, -1, false)) {
				$list[] = array(
					'id' => $row['relation_id'],
					'module' => $row['module'],
					'sequence' => $row['sequence'],
					'label' => getTranslatedString($row['module'], $row['module']),
				);
			}
		}
		
		return $list;
	}
	
	function addRelatedList($relid) {
		global $adb,$table_prefix;
		
		// check if exists
		$res = $adb->pquery("SELECT panelid FROM {$table_prefix}_panel2rlist WHERE panelid = ? AND relation_id = ?", array($this->id, $relid));
		if ($res && $adb->num_rows($res) == 0) {
			$rseq = $adb->pquery("SELECT MAX(sequence) as mseq FROM {$table_prefix}_panel2rlist WHERE panelid = ?", array($this->id));
			if ($rseq && $adb->num_rows($rseq) > 0) {
				$seq = (int)$adb->query_result_no_html($rseq, 0, 'mseq') + 1;
			} else {
				$seq = 1;
			}
			$adb->pquery("INSERT INTO {$table_prefix}_panel2rlist (panelid, relation_id, sequence) VALUES (?,?,?)", array($this->id, $relid, $seq));
		}
	}
	
	function addRelatedLists($relids) {
		foreach ($relids as $relid) {
			$this->addRelatedList($relid);
		}
	}
	
	function removeRelatedList($relid) {
		global $adb,$table_prefix;
		
		$adb->pquery("DELETE FROM {$table_prefix}_panel2rlist WHERE panelid = ? AND relation_id = ?", array($this->id, $relid));
	}
	
	function setRelatedLists($relids) {
		global $adb,$table_prefix;
		
		// delete all
		$adb->pquery("DELETE FROM {$table_prefix}_panel2rlist WHERE panelid = ?", array($this->id));
		$this->addRelatedLists($relids);
	}

}

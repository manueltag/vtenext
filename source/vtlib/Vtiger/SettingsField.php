<?php
require_once('vtlib/Vtiger/SettingsBlock.php');
/**
 * Provides basic API to work with vtiger CRM Settings Fields
 * @package vtlib
 */
class Vtiger_SettingsField {
	/** ID of this field instance */
	var $id;
	var $name;
	var $iconpath;
	var $description;
	var $linkto;
	var	$sequence = false;
	var	$active = 0;
	var $block;

	/**
	 * Constructor
	 */
	function __construct() {
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
	
	static function getInstance($value, $blockInstance=false) {
		global $adb, $table_prefix;
		$instance = false;

		$query = false;
		$queryParams = false;
		if(Vtiger_Utils::isNumber($value)) {
			$query = "SELECT * FROM ".$table_prefix."_settings_field WHERE fieldid=?";
			$queryParams = Array($value);
		} else {
			$query = "SELECT * FROM ".$table_prefix."_settings_field WHERE name=? AND blockid=?";
			$queryParams = Array($value, $blockInstance->id);
		}
		$result = $adb->pquery($query, $queryParams);
		if($adb->num_rows($result)) {
			$class = get_called_class() ?: get_class();
			$instance = new $class();
			$instance->initialize($adb->fetch_array($result), $blockInstance);
		}
		return $instance;
	}
	
	/**
	 * Initialize this instance
	 * @param Array 
	 * @param Vtiger_SettingsBlock Instance of block to which this field belongs
	 * @access private
	 */
	function initialize($valuemap, $blockInstance=false) {
		$this->id = $valuemap['fieldid'];
		$this->name = $valuemap['name'];
		$this->iconpath = $valuemap['iconpath'];
		$this->description = $valuemap['description'];
		$this->linkto = $valuemap['linkto'];
		$this->sequence = $valuemap['sequence'];
		$this->active = $valuemap['active'];
		$this->block= $blockInstance? $blockInstance : Vtiger_SettingsBlock::getInstance($valuemap['blockid']);
	}
	
	/**
	 * Get unique id for this instance
	 * @access private
	 */
	function __getUniqueId() {
		global $adb, $table_prefix;
		return $adb->getUniqueID($table_prefix.'_settings_field');
	}
	
	/**
	 * Get next sequence id to use within a block for this instance
	 * @access private
	 */
	function __getNextSequence() {
		global $adb, $table_prefix;
		$result = $adb->pquery("SELECT MAX(sequence) AS max_seq FROM ".$table_prefix."_settings_field WHERE blockid=?",
			Array($this->getBlockId()));
		$maxseq = 0;
		if($result && $adb->num_rows($result)) {
			$maxseq = $adb->query_result($result, 0, 'max_seq');
			$maxseq += 1;
		}
		return $maxseq;
	}
	
	/**
	 * Get block id to which this field instance is associated
	 */
	function getBlockId() {
		return $this->block->id;
	}
	
	/**
	 * Save this field instance
	 * @param Vtiger_SettingsBlock Instance of block to which this field should be added.
	 */
	function save($blockInstance=false) {
		if($this->id) $this->__update();
		else $this->__create($blockInstance);
		return $this->id;
	}
	
	function __create($blockInstance) {
		global $adb, $table_prefix;

		$this->block = $blockInstance;
		$this->id = $this->__getUniqueId();
		if(!$this->sequence) {
			$this->sequence = $this->__getNextSequence();
		}
		
		$sql ="INSERT INTO ".$table_prefix."_settings_field (fieldid, blockid, name, iconpath, description, linkto, sequence,active) 
				VALUES (?,?,?,?,?,?,?,?)";
		$params = Array($this->id, $this->getBlockId(), $this->name, $this->iconpath,
				$this->description, $this->linkto, $this->sequence,$this->active);
		
		$adb->pquery($sql,$params);	
		
		self::log("Creating Field $this->name ... DONE");
	}
	
	/**
	 * Update this field instance
	 * @access private
	 * @internal TODO
	 */
	function __update() {
		self::log("Updating Field $this->name ... TODO");
	}
	
	/**
	 * Delete this field instance
	 */
	function delete() {
		global $adb, $table_prefix;

		$adb->pquery("DELETE FROM ".$table_prefix."_settings_field WHERE fieldid=?", Array($this->id));
		self::log("Deleteing Field $this->name ... DONE");
	}
}

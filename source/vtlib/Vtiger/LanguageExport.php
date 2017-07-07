<?php
/************************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ************************************************************************************/
include_once('vtlib/Vtiger/Package.php');

/**
 * Provides API to package vtiger CRM language files.
 * @package vtlib
 */
class Vtiger_LanguageExport extends Vtiger_Package {

	const TABLENAME = '_language';

	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * Generate unique id for insertion
	 * @access private
	 */
	function __getUniqueId() {
		global $adb,$table_prefix;
		return $adb->getUniqueID($table_prefix.self::TABLENAME);
	}

	/**
	 * Initialize Language Schema
	 * @access private
	 */
	static function __initSchema() {
		global $table_prefix;
		$hastable = Vtiger_Utils::CheckTable($table_prefix.self::TABLENAME);
		if(!$hastable) {
			Vtiger_Utils::CreateTable(
				$table_refix.self::TABLENAME,
				'id I(11) NOTNULL PRIMARY,
				name C(50), 
				prefix C(10), 
				label C(30), 
				lastupdated T, 
				sequence I(11), 
				isdefault I(1), 
				active INT(1)',
				true
			);
			global $languages, $adb;
			foreach($languages as $langkey=>$langlabel) {
				$uniqueid = self::__getUniqueId();
				$adb->pquery('INSERT INTO '.$table_prefix.self::TABLENAME.'(id,name,prefix,label,lastupdated,active) VALUES(?,?,?,?,?,?)',
					Array($uniqueid, $langlabel,$langkey,$langlabel,date('Y-m-d H:i:s',time()), 1));
			}
		}
	}

	/**
	 * Register language pack information.
	 */
	static function register($prefix, $label, $name='', $isdefault=false, $isactive=true, $overrideCore=false) {
		self::__initSchema();
		global $table_prefix;
		$prefix = trim($prefix);
		// We will not allow registering core language unless forced
		if(strtolower($prefix) == 'en_us' && $overrideCore == false) return;

		$useisdefault = ($isdefault)? 1 : 0;
		$useisactive  = ($isactive)?  1 : 0;

		global $adb;
		$checkres = $adb->pquery('SELECT * FROM '.$table_prefix.self::TABLENAME.' WHERE prefix=?', Array($prefix));
		$datetime = date('Y-m-d H:i:s');
		if($adb->num_rows($checkres)) {
			$id = $adb->query_result($checkres, 0, 'id');
			$adb->pquery('UPDATE '.$table_prefix.self::TABLENAME.' set label=?, name=?, lastupdated=?, isdefault=?, active=? WHERE id=?',
				Array($label, $name, $datetime, $useisdefault, $useisactive, $id));
		} else {
			$uniqueid = self::__getUniqueId();
			$adb->pquery('INSERT INTO '.$table_prefix.self::TABLENAME.' (id,name,prefix,label,lastupdated,isdefault,active) VALUES(?,?,?,?,?,?,?)',
				Array($uniqueid, $name, $prefix, $label, $datetime, $useisdefault, $useisactive));
		}
		self::log("Registering Language $label [$prefix] ... DONE");		
	}

	/**
	 * De-Register language pack information
	 * @param String Language prefix like (de_de) etc
	 */
	static function deregister($prefix) {
		global $table_prefix;
		$prefix = trim($prefix);
		// We will not allow deregistering core language
		if(strtolower($prefix) == 'en_us') return;

		self::__initSchema();

		global $adb;
		$checkres = $adb->pquery('DELETE FROM '.$table_prefix.self::TABLENAME.' WHERE prefix=?', Array($prefix));
		self::log("Deregistering Language $prefix ... DONE");
	}

	/**
	 * Get all the language information
	 * @param Boolean true to include in-active languages also, false (default)
	 */
	static function getAll($includeInActive=false) {
		global $adb,$table_prefix;
		$hastable = Vtiger_Utils::CheckTable($table_prefix.self::TABLENAME);

		$languageinfo = Array();

		if($hastable) {
			if($includeInActive) $result = $adb->query('SELECT * FROM '.$table_prefix.self::TABLENAME);
			else $result = $adb->query('SELECT * FROM '.$table_prefix.self::TABLENAME . ' WHERE active=1');

			for($index = 0; $index < $adb->num_rows($result); ++$index) {
				$resultrow = $adb->fetch_array($result);
				$prefix = $resultrow['prefix'];
				$label  = $resultrow['label'];
				$languageinfo[$prefix] = $label;
			}
		} else {
			global $languages;
			foreach((Array)$languages as $prefix=>$label) { //crmv@36557
				$languageinfo[$prefix] = $label;
			}
		}
		return $languageinfo;
	}
}

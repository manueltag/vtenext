<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

//TODO: verificare che non basti soltanto l'inclusione di include_webservices.php al posto di tutte le seguenti
require_once('include/Webservices/Utils.php');
require_once("include/Webservices/VtigerCRMObject.php");
require_once("include/Webservices/VtigerCRMObjectMeta.php");
require_once("include/Webservices/DataTransform.php");
require_once("include/Webservices/WebServiceError.php");
require_once('include/Webservices/ModuleTypes.php');
require_once('include/Webservices/Retrieve.php');
require_once('include/Webservices/Update.php');
require_once('include/Webservices/WebserviceField.php');
require_once('include/Webservices/EntityMeta.php');
require_once('include/Webservices/VtigerWebserviceObject.php');
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');

class VTWorkflowEntity{
	function __construct($user, $id){
		$this->moduleName = null;
		$this->id = $id;
		$this->user = $user;
		//crmv@44747
		try {
			$data = vtws_retrieve($id, $user);
		} catch (Exception $e) {
			return false;
		}
		//crmv@44747e
		foreach($data as $key => $value){
			if(is_string($value)){
				$data[$key] = html_entity_decode($value, ENT_QUOTES, 'utf-8');
			}
		}
		$this->data = $data;
	}
	/**
	 * Get the data from the entity object as an array.
	 *
	 * @return An array representation of the module data.
	 */
	function getData(){
		return $this->data;
	}
	
	/**
	 * Get the entity id.
	 *
	 * @return The entity id.
	 */
	function getId(){
		return $this->data['id'];
	}
	
	/**
	 * Get the name of the module represented by the entity data object.
	 *
	 * @return The module name.
	 */
	function getModuleName(){
		if($this->moduleName==null){
			global $adb, $table_prefix;
			$wsId = $this->data['id'];
			if (empty($wsId)) $wsId = $this->id;	//crmv@105312
			$parts = explode('x', $wsId);
			$result = $adb->pquery('select name from '.$table_prefix.'_ws_entity where id=?', 
						 array($parts[0]));
			$rowData = $adb->raw_query_result_rowdata($result, 0);
			$this->moduleName = $rowData['name'];
		}
		return $this->moduleName;
	}
	
	function get($fieldName){
		return $this->data[$fieldName];
	}
	
	function set($fieldName, $value){

		$this->data[$fieldName] = $value;
	}

	function save(){
		vtws_update($this->data,$this->user);
	}
	
}

class VTEntityCache {

	// enable the cache to be cleared when a field changes
	public static $resetCache = array(); // crmv@79058

	function __construct($user){
		$this->user = $user;
		$this->cache = array();
	}

	public static function setResetCache($id) {
		self::$resetCache[$id] = true;
	}
	
	function forId($id){
		// impersonate admin
		global $iAmAProcess;
		if ($iAmAProcess) {
			global $current_user;
			$tmp_this_user = $this->user;
			$tmp_current_user = $current_user;
			$user = CRMEntity::getInstance('Users');
			$user->retrieveCurrentUserInfoFromFile(1);
			$current_user = $this->user = $user;
			$PMUtils = ProcessMakerUtils::getInstance();
			$PMUtils->setDefaultDataFormat();
		}
		
		// crmv@79058
		if (self::$resetCache[$id]){
			$this->cache[$id] = null;
			unset(self::$resetCache[$id]);
		}
		// crmv@79058e
		if($this->cache[$id]==null){
			$data = new VTWorkflowEntity($this->user, $id);
			if ($data !== false) $this->cache[$id] = $data;	//crmv@44747
		}
		
		// restore admin
		if ($iAmAProcess) {
			$current_user = $tmp_current_user;
			$this->user = $tmp_this_user;
			$PMUtils->restoreDataFormat();
		}
		
		return $this->cache[$id];
	}
}
?>
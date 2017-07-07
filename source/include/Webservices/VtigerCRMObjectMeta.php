<?php
/*************************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 *************************************************************************************/

class VtigerCRMObjectMeta extends EntityMeta {

	private $tabId;

	private $meta;
	private $assign;
	private $hasAccess;
	private $hasReadAccess;
	private $hasWriteAccess;
	private $hasDeleteAccess;
	private $assignUsers;

	function VtigerCRMObjectMeta($webserviceObject,$user){
		global $table_prefix;
		parent::__construct($webserviceObject,$user);

		$this->columnTableMapping = null;
		$this->fieldColumnMapping = null;
		$this->userAccessibleColumns = null;
		$this->mandatoryFields = null;
		$this->emailFields = null;
		$this->referenceFieldDetails = null;
		$this->ownerFields = null;
		$this->moduleFields = array();
		$this->hasAccess = false;
		$this->hasReadAccess = false;
		$this->hasWriteAccess = false;
		$this->hasDeleteAccess = false;
		$instance = vtws_getModuleInstance($this->webserviceObject);
		$this->idColumn = $instance->tab_name_index[$instance->table_name];
		$this->baseTable = $instance->table_name;
		$this->tableList = $instance->tab_name;
		$this->tableIndexList = $instance->tab_name_index;
		if(in_array($table_prefix.'_crmentity',$instance->tab_name)){
			$this->defaultTableList = array($table_prefix.'_crmentity');
		}else{
			$this->defaultTableList = array();
		}
		$this->tabId = null;
	}

	public function getTabId(){
		if($this->tabId == null){
			//crmv@23687
			//$this->tabId = getTabid($this->objectName);
			$moduleInstance = Vtiger_Module::getInstance($this->objectName);
			$this->tabId = $moduleInstance->id;
			//crmv@23687e
		}
		return $this->tabId;
	}

	/**
	 * returns tabid that can be consumed for database lookup purpose generally, events and
	 * calendar are treated as the same module
	 * @return Integer
	 */
	public function getEffectiveTabId() {
		return getTabid($this->getTabName());
	}

	public function getTabName(){
		if($this->objectName == 'Events'){
			return 'Calendar';
		}
		return $this->objectName;
	}

	private function computeAccess(){

		global $adb,$table_prefix;

		$active = vtlib_isModuleActive($this->getTabName());
		if($active == false){
			$this->hasAccess = false;
			$this->hasReadAccess = false;
			$this->hasWriteAccess = false;
			$this->hasDeleteAccess = false;
			return;
		}

		// crmv@39110
		$userid = $this->user->id;
		require('user_privileges/requireUserPrivileges.php');
		// crmv@39110e
		if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0){
			$this->hasAccess = true;
			$this->hasReadAccess = true;
			$this->hasWriteAccess = true;
			$this->hasDeleteAccess = true;
		}else{

			//TODO get oer sort out the preference among profile2tab and profile2globalpermissions.
			//TODO check whether create/edit seperate controls required for web sevices?
			$profileList = getCurrentUserProfileList();

			$sql = "select * from ".$table_prefix."_profile2globalperm where profileid in (".generateQuestionMarks($profileList).")";
			$result = $adb->pquery($sql,array($profileList));

			$noofrows = $adb->num_rows($result);
			//globalactionid=1 is view all action.
			//globalactionid=2 is edit all action.
			for($i=0; $i<$noofrows; $i++){
				$permission = $adb->query_result($result,$i,"globalactionpermission");
				$globalactionid = $adb->query_result($result,$i,"globalactionid");
				if($permission != 1 || $permission != "1"){
					$this->hasAccess = true;
					if($globalactionid == 2 || $globalactionid == "2"){
						$this->hasWriteAccess = true;
						$this->hasDeleteAccess = true;
					}else{
						$this->hasReadAccess = true;
					}
				}
			}

			$sql = 'select * from '.$table_prefix.'_profile2tab where profileid in ('.generateQuestionMarks($profileList).') and tabid = ?';
			$result = $adb->pquery($sql,array($profileList,$this->getTabId()));
			$standardDefined = false;
			$permission = $adb->query_result($result,0,"permissions"); // crmv@33018
			if($permission == 1 || $permission == "1"){
				$this->hasAccess = false;
				return;
			}else{
				$this->hasAccess = true;
			}

			//operation=2 is delete operation.
			//operation=0 or 1 is create/edit operation. precise 0 create and 1 edit.
			//operation=3 index or popup. //ignored for websevices.
			//operation=4 is view operation.
			$sql = "select * from ".$table_prefix."_profile2standardperm where profileid in (".generateQuestionMarks($profileList).") and tabid=?";
			$result = $adb->pquery($sql,array($profileList,$this->getTabId()));

			$noofrows = $adb->num_rows($result);
			for($i=0; $i<$noofrows; $i++){
				$standardDefined = true;
				$permission = $adb->query_result($result,$i,"permissions");
				$operation = $adb->query_result($result,$i,"Operation");
				if(!$operation){
					$operation = $adb->query_result($result,$i,"operation");
				}

				if($permission != 1 || $permission != "1"){
					$this->hasAccess = true;
					if($operation == 0 || $operation == "0"){
						$this->hasWriteAccess = true;
					}else if($operation == 1 || $operation == "1"){
						$this->hasWriteAccess = true;
					}else if($operation == 2 || $operation == "2"){
						$this->hasDeleteAccess = true;
					}else if($operation == 4 || $operation == "4"){
						$this->hasReadAccess = true;
					}
				}
			}
			if(!$standardDefined){
				$this->hasReadAccess = true;
				$this->hasWriteAccess = true;
				$this->hasDeleteAccess = true;
			}

		}
	}

	function hasAccess(){
		if(!$this->meta){
			$this->retrieveMeta();
		}
		return $this->hasAccess;
	}

	function hasWriteAccess(){
		if(!$this->meta){
			$this->retrieveMeta();
		}
		return $this->hasWriteAccess;
	}

	function hasReadAccess(){
		if(!$this->meta){
			$this->retrieveMeta();
		}
		return $this->hasReadAccess;
	}

	function hasDeleteAccess(){
		if(!$this->meta){
			$this->retrieveMeta();
		}
		return $this->hasDeleteAccess;
	}

	function hasPermission($operation,$webserviceId){

		$idComponents = vtws_getIdComponents($webserviceId);
		$id=$idComponents[1];

		$permitted = isPermitted($this->getTabName(),$operation,$id);
		if(strcmp($permitted,"yes")===0){
			return true;
		}
		return false;
	}

	function hasAssignPrivilege($webserviceId){
		global $adb;

		// administrator's have assign privilege
		if(is_admin($this->user)) return true;

		$idComponents = vtws_getIdComponents($webserviceId);
		$userId=$idComponents[1];
		$ownerTypeId = $idComponents[0];

		if($userId == null || $userId =='' || $ownerTypeId == null || $ownerTypeId ==''){
			return false;
		}
		$webserviceObject = VtigerWebserviceObject::fromId($adb,$ownerTypeId);
		if(strcasecmp($webserviceObject->getEntityName(),"Users")===0){
			if($userId == $this->user->id){
				return true;
			}
			if(!$this->assign){
				$this->retrieveUserHierarchy();
			}
			if(in_array($userId,array_keys($this->assignUsers))){
				return true;
			}else{
				return false;
			}
		}elseif(strcasecmp($webserviceObject->getEntityName(),"Groups") === 0){
			$tabId = $this->getTabId();
			$groups = vtws_getUserAccessibleGroups($tabId, $this->user);
			foreach ($groups as $group) {
				if($group['id'] == $userId){
					return true;
				}
			}
			return false;
		}

	}

	function getUserAccessibleColumns(){

		if(!$this->meta){
			$this->retrieveMeta();
		}
		return parent::getUserAccessibleColumns();
	}

	public function getModuleFields() {
		if(!$this->meta){
			$this->retrieveMeta();
		}
		return parent::getModuleFields();
	}

	function getColumnTableMapping(){
		if(!$this->meta){
			$this->retrieveMeta();
		}
		return parent::getColumnTableMapping();
	}

	function getFieldColumnMapping(){

		if(!$this->meta){
			$this->retrieveMeta();
		}
		if($this->fieldColumnMapping === null){
			$this->fieldColumnMapping =  array();
			foreach ($this->moduleFields as $fieldName=>$webserviceField) {
				if(strcasecmp($webserviceField->getFieldDataType(),'file') !== 0){
					$this->fieldColumnMapping[$fieldName] = $webserviceField->getColumnName();
				}
			}
			$this->fieldColumnMapping['id'] = $this->idColumn;
		}
		return $this->fieldColumnMapping;
	}

	function getMandatoryFields(){
		if(!$this->meta){
			$this->retrieveMeta();
		}
		return parent::getMandatoryFields();
	}

	function getReferenceFieldDetails(){
		if(!$this->meta){
			$this->retrieveMeta();
		}
		return parent::getReferenceFieldDetails();
	}

	function getOwnerFields(){
		if(!$this->meta){
			$this->retrieveMeta();
		}
		return parent::getOwnerFields();
	}

	function getEntityName(){
		return $this->objectName;
	}

	function getEntityId(){
		return $this->objectId;
	}

	function getEmailFields(){
		if(!$this->meta){
			$this->retrieveMeta();
		}
		return parent::getEmailFields();
	}

	function getFieldIdFromFieldName($fieldName){
		if(!$this->meta){
			$this->retrieveMeta();
		}

		if(isset($this->moduleFields[$fieldName])){
			$webserviceField = $this->moduleFields[$fieldName];
			return $webserviceField->getFieldId();
		}
		return null;
	}

	function retrieveMeta(){

		require_once('modules/CustomView/CustomView.php');
		$current_user = vtws_preserveGlobal('current_user',$this->user);
		$theme = vtws_preserveGlobal('theme',VTWS_PreserveGlobal::getGlobal('theme'));	//crmv@24541
		$default_language = VTWS_PreserveGlobal::getGlobal('default_language');
		$current_language = vtws_preserveGlobal('current_language',$_SESSION['authenticated_user_language']);	//crmv@30166

		$this->computeAccess();

		$cv = new CustomView();
		$module_info = $cv->getCustomViewModuleInfo($this->getTabName());
		$blockArray = array();
		foreach($cv->module_list[$this->getTabName()] as $label=>$blockList){
			$blockArray = array_merge($blockArray,explode(',',$blockList));
		}
		$this->retrieveMetaForBlock($blockArray);

		$this->meta = true;
		VTWS_PreserveGlobal::flush();
	}

	private function retrieveUserHierarchy(){

		$heirarchyUsers = get_user_array(false,"ACTIVE",$this->user->id);
		$groupUsers = vtws_getUsersInTheSameGroup($this->user->id);
		$this->assignUsers = $heirarchyUsers+$groupUsers;
		$this->assign = true;
	}

	private function retrieveMetaForBlock($block){

		global $adb,$table_prefix,$iAmAProcess;	//crmv@105685

		$tabid = $this->getTabId();
		if (empty($block)) $block = array(0); // crmv@30967
		// crmv@39110
		$userid = $this->user->id;
		require('user_privileges/requireUserPrivileges.php');
		// crmv@39110e
		/* crmv@53053 : tolti 33136,34559,34559  */
		if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] ==0 || $iAmAProcess === true){	//crmv@105685
			$sql = "select * from ".$table_prefix."_field where tabid = ? and block in (".generateQuestionMarks($block).") and displaytype in (1,2,3,4) and ".$table_prefix."_field.presence in (0,2) and readonly != 100"; // crmv@33097 crmv@55746
			$params = array($tabid, $block);
		}else{
			$profileList = getCurrentUserProfileList();
			//crmv@sdk-18508	crmv@33097
			//crmv@39110 (used subquery, otherwise there might be duplicates for multiple profiles, unpredictable order with multiple profiles )
			if (count($profileList) > 0) {
				//crmv@60969
				$sql = "SELECT ".$table_prefix."_field.*, p2ftemp.profile_sequence
						FROM ".$table_prefix."_field
						INNER JOIN ".$table_prefix."_def_org_field ON ".$table_prefix."_def_org_field.fieldid = ".$table_prefix."_field.fieldid
						INNER JOIN (select distinct fieldid, sequence as profile_sequence FROM {$table_prefix}_profile2field p2f inner join {$table_prefix}_profile2tab p2t on p2t.tabid = p2f.tabid and p2f.profileid = p2t.profileid and p2t.permissions = 0 WHERE p2f.profileid in (".generateQuestionMarks($profileList).") and p2f.tabid = ? and p2f.visible = 0) p2ftemp ON p2ftemp.fieldid = {$table_prefix}_field.fieldid
						WHERE ".$table_prefix."_field.tabid = ?
						AND {$table_prefix}_field.readonly != 100 AND ".$table_prefix."_def_org_field.visible = 0 and ".$table_prefix."_field.block in (".generateQuestionMarks($block).") and ".$table_prefix."_field.displaytype in (1,2,3,4) and ".$table_prefix."_field.presence in (0,2)
						ORDER BY p2ftemp.profile_sequence ASC";
				$params = array($profileList, $tabid, $tabid, $block);
				//crmv@60969e
			} else {
				$sql = "SELECT ".$table_prefix."_field.*
						FROM ".$table_prefix."_field
						INNER JOIN ".$table_prefix."_def_org_field ON ".$table_prefix."_def_org_field.fieldid = ".$table_prefix."_field.fieldid
						WHERE ".$table_prefix."_field.tabid = ?
						AND {$table_prefix}_field.readonly != 100 AND ".$table_prefix."_def_org_field.visible = 0 and ".$table_prefix."_field.block in (".generateQuestionMarks($block).") and ".$table_prefix."_field.displaytype in (1,2,3,4) and ".$table_prefix."_field.presence in (0,2)
 						AND EXISTS(SELECT * FROM ".$table_prefix."_profile2field WHERE ".$table_prefix."_profile2field.fieldid = ".$table_prefix."_field.fieldid AND ".$table_prefix."_profile2field.visible = 0) ";
				$params = array($tabid, $block);
			}
			//crmv@sdk-18508e crmv@33097e crmv@39110e
		}
		$result = $adb->pquery($sql,$params);
		$noofrows = $adb->num_rows($result);
		$referenceArray = array();
		$knownFieldArray = array();
		for($i=0; $i<$noofrows; $i++){
			$fieldname = $adb->query_result($result,$i,"fieldname");
			if(strcasecmp($fieldname,'imagename')===0){
				continue;
			}
			$webserviceField = WebserviceField::fromQueryResult($adb,$result,$i);
			$this->moduleFields[$webserviceField->getFieldName()] = $webserviceField;
		}
	}

	function getObjectEntityName($webserviceId){
		global $adb,$table_prefix;

		$idComponents = vtws_getIdComponents($webserviceId);
		$id=$idComponents[1];

		$seType = null;
		if($this->objectName == 'Users'){
			$sql = "select user_name from ".$table_prefix."_users where id=? and deleted=0";
			$result = $adb->pquery($sql , array($id));
			if($result != null && isset($result)){
				if($adb->num_rows($result)>0){
					$seType = 'Users';
				}
			}
		}else{
			$sql = "select * from ".$table_prefix."_crmentity where crmid=? and deleted=0";
			$result = $adb->pquery($sql , array($id));
			if($result != null && isset($result)){
				if($adb->num_rows($result)>0){
					$seType = $adb->query_result($result,0,"setype");
					if($seType == "Calendar"){
						$seType = vtws_getCalendarEntityType($id);
					}
				}
			}
		}

		return $seType;
	}

	function exists($recordId){
		global $adb,$table_prefix;

		$exists = false;
		$sql = '';
		if($this->objectName == 'Users'){
			$sql = "select * from ".$table_prefix."_users where id=? and deleted=0 and status='Active'";
		}else{
			$sql = "select * from ".$table_prefix."_crmentity where crmid=? and deleted=0 and setype='".
				$this->getTabName()."'";
		}
		$result = $adb->pquery($sql , array($recordId));
		if($result != null && isset($result)){
			if($adb->num_rows($result)>0){
				$exists = true;
			}
		}
		return $exists;
	}

	public function getNameFields(){
		global $adb,$table_prefix;

		$query = "select fieldname,tablename,entityidfield from ".$table_prefix."_entityname where tabid = ?";
		$result = $adb->pquery($query, array($this->getEffectiveTabId()));
		$fieldNames = '';
		if($result){
			$rowCount = $adb->num_rows($result);
			if($rowCount > 0){
				$fieldNames = $adb->query_result($result,0,'fieldname');
			}
		}
		return $fieldNames;
	}

	public function getName($webserviceId){

		$idComponents = vtws_getIdComponents($webserviceId);
		$id=$idComponents[1];

		$nameList = getEntityName($this->getTabName(),array($id));
		return $nameList[$id];
	}

	public function getEntityAccessControlQuery(){
		$accessControlQuery = '';
		$instance = vtws_getModuleInstance($this->webserviceObject);
		if($this->getTabName() != 'Users') {
			$accessControlQuery = $instance->getNonAdminAccessControlQuery($this->getTabName(),
					$this->user);
		}
		return $accessControlQuery;
	}

	public function getEntitylistQueryNonAdminChange($query){
		$instance = vtws_getModuleInstance($this->webserviceObject);
		$module = $this->getTabName();
		if($module != 'Users') {
			$query = $instance->listQueryNonAdminChange($query,$module,$scope);
		}
		return $query;
	}

	public function getJoinClause($tableName) {
		$instance = vtws_getModuleInstance($this->webserviceObject);
		return $instance->getJoinClause($tableName);
	}

	public function isModuleEntity() {
		return true;
	}
}
?>
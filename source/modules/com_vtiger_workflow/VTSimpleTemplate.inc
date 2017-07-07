<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
class VTSimpleTemplate{

	function __construct($templateString){
		$this->template = $templateString;
	}

	function render($entityCache, $entityId){
		$this->cache = $entityCache;
		$this->parent = $this->cache->forId($entityId);
		return $this->parseTemplate();
	}

	private function matchHandler($match){
		preg_match('/\((\w+) : \(([_\w]+)\) (\w+)\)/', $match[1], $matches);
		if(count($matches)==0){
			$fieldname = $match[1];
			$module = $this->parent->getModuleName(); // crmv@112144 - main module
			$data = $this->parent->getData();
			//crmv@92272
			if ($fieldname == 'crmid') {
				$result = $this->parent->getId();
			//crmv@92272e
			}elseif($this->useValue($data, $fieldname)){
				$result = $this->prepareValue($data[$fieldname], $fieldname, $module, $data); // crmv@112144
			}else{
				$result ='';
			}
		}else{
			list($full, $referenceField, $referenceModule, $fieldname) = $matches;
			if($referenceModule === '__VtigerMeta__'){
				$result = $this->getMetaValue($fieldname);
			}else{
				$referenceId = $this->parent->get($referenceField);
				if($referenceId==null){
					$result="";
				}else{
					$entity = $this->cache->forId($referenceId);
					if($referenceModule==="Users" && $entity->getModuleName()=="Groups"){
						list($groupEntityId, $groupId) = vtws_getIdComponents($referenceId);

						require_once('include/utils/GetGroupUsers.php');
						$ggu = new GetGroupUsers();
						$ggu->getAllUsersInGroup($groupId,true);	//crmv@46552

						$users = $ggu->group_users;
						$parts = Array();
						foreach($users as $userId){
							$refId = vtws_getWebserviceEntityId("Users", $userId);
							$entity = $this->cache->forId($refId);
							$data = $entity->getData();
							if($this->useValue($data, $fieldname)){
								$parts[] = $data[$fieldname];
							}
						}
						$result = implode(",", $parts);
					}
					//crmv@24350
					elseif($entity->getModuleName()===$referenceModule){
					//crmv@24350e
						$data = $entity->getData();
						if($this->useValue($data, $fieldname)){
							$result = $this->prepareValue($data[$fieldname], $fieldname, $referenceModule, $data); // crmv@112144
						}else{
							$result = '';
						}
					}else{
						$result = '';
					}
				}
			}
		}
		return $result;

	}
	
	// crmv@112144
	protected function prepareValue($value, $fieldname, $module, $data) {
		$field_uitype = $this->getUITypeByName($module,$fieldname);
		if (in_array($field_uitype, array(15, 16, 300))) {
			return getTranslatedString($value, $module);
		} else {
			return $value;
		}
	}
	
	protected function getUITypeByName($module, $fieldname) {
		global $adb, $table_prefix;
		$result = $adb->pquery("select uitype from {$table_prefix}_field where tabid=? and fieldname=?", array(getTabid($module), $fieldname));
		$uitype = $adb->query_result_no_html($result,0,"uitype");
		return $uitype;
	}
	// crmv@112144e
	
	protected function useValue($data, $fieldname) {
		return !empty($data[$fieldname]);
	}

	function parseTemplate(){
		return preg_replace_callback('/\\$(\w+|\((\w+) : \(([_\w]+)\) (\w+)\))/', array($this,"matchHandler"), $this->template); // crmv@73654
	}
	//crmv@36510
	function getMetaValue($fieldname){
		require_once 'config.inc.php';
		global $site_URL, $PORTAL_URL, $current_user;
		switch($fieldname){
			case 'date'					:	return getNewDisplayDate();
			case 'time'					:	return date('h:i:s');
			case 'dbtimezone'			:	return DateTimeField::getDBTimeZone();
			case 'crmdetailviewurl'		:	$wsId = $this->parent->getId();
											$parts = explode('x', $wsId);
											$recordId = $parts[1];
											$moduleName = $this->parent->getModuleName();
											return $site_URL.'/index.php?action=DetailView&module='.$moduleName.'&record='.$recordId;
			case 'portaldetailviewurl'	: 	$wsId = $this->parent->getId();
											$parts = explode('x', $wsId);
											$recordId = $parts[1];
											$moduleName = $this->parent->getModuleName();
											$recorIdName='id';
											if($moduleName == 'HelpDesk') $recorIdName = 'ticketid';
											if($moduleName == 'Faq') $recorIdName = 'faqid';
											if($moduleName == 'Products') $recorIdName = 'productid';
											return $PORTAL_URL.'/index.php?module='.$moduleName.'&action=index&'.$recorIdName.'='.$recordId.'&fun=detail';
			case 'siteurl'				: return $site_URL;
			case 'portalurl'			: return $PORTAL_URL;
			default: '';
		}
	}
	//crmv@36510 e
}
//crmv@15309
class VTSimpleTemplate2{

	function __construct($templateString,$newsletterid,$templateid=''){	//crmv@22700
		$this->template = $templateString;
		$this->newsletterid = $newsletterid;	//crmv@22700
		$this->id = $templateid;	//crmv@22700
	}

	//crmv@43581	crmv@22700	crmv@24644
	function render($entityCache, $entityId){
		$this->cache = $entityCache;
		$this->parent = $this->cache->forId($entityId);
    	$id = explode('x',$entityId);
    	//crmv@64475
		static $field_definition_cache = array();
		$entityName = getSalesEntityType($id[1]);
		if (!isset($field_definition_cache[$entityName])) {
			$this->field_definition = $field_definition_cache[$entityName] = getEmailTemplateFields(false,$entityName);
		} else {
			$this->field_definition = $field_definition_cache[$entityName];
		}
		//crmv@64475e
		$this->replacements = array(); // crmv@38592
		return $this->parseTemplate();
	}

	private function matchHandler($match){
		$real_value = $match[0];	//crmv@22700
		$match = $match[1];
	    $fields = Array();
        $data = explode('|',$match);
        $module=$data[0];
        $related=$data[1];
        //crmv@31358
        if (strpos($related,'#') !== false){
        	list($related,$related_field) = explode('#',$related);
        }
        //crmv@31358 e
        $fieldname=$data[2];
        if ($module=='custom'){
			$result = $this->getMetaValue($fieldname);

        }else{
        	if (!$related){
        		if ($module == 'Newsletter') {
        			if (strpos($fieldname,'tracklink#') !== false) {
        				$result = $this->getTrackLink(str_replace('tracklink#','',$fieldname));
        				$data[$fieldname] = $result;
        			} elseif (strpos($fieldname,'target_') !== false) {
        				$result = $this->getNewsletterValue($fieldname);
        				$data[$fieldname] = $result;
        			} else {	//campo del modulo Newsletter
        				$wsid = construct_ws_id($this->newsletterid,'Newsletter');
        				$ent = $this->cache->forId($wsid);
        				$data = $ent->getData();
        			}
        		} elseif ($module == 'Users'){
        			global $current_user;
        			$wsid = construct_ws_id($_SESSION['authenticated_user_id'],$module); //crmv@28425
        			$ent = $this->cache->forId($wsid);
        			$data = $ent->getData();
        		} else {
	        		$data = $this->parent->getData();
        		}
				if($this->useValue($data, $fieldname)){
					//crmv@59215 crmv@59737 crmv@71678
					// added parsing numbers TODO picklists
					// added parsing uitype 19
					$field_uitype = $this->getUITypeByName($module,$fieldname);
					$currencyFields = array('hdnGrandTotal', 'hdnSubTotal', 'hdnS_H_Amount', 'hdnDiscountAmount', 'txtAdjustment');
					if ($field_uitype == 71 || in_array($fieldname, $currencyFields)) {
						global $current_user;
						if ($current_user->id == '') $current_user->id = 1;
						$result = formatUserNumber($data[$fieldname]);
					} elseif ($field_uitype == 19) {
						$result = nl2br($data[$fieldname]);
					// crmv@109388
					} elseif ($field_uitype == 33) {
						$list = explode(' |##| ', $data[$fieldname]);
						foreach ($list as &$listval) {
							$listval = getTranslatedString($listval, $module);
						}
						$result = implode(', ', $list);
					// crmv@109388e
					} else {
						$result = $data[$fieldname];
					}
					//crmv@59215e crmv@59737e crmv@71678e
				}else{
					$result ='';
				}
	        } else{
        		//crmv@31358
        		if ($related_field != ''){
                	if (is_array($this->field_definition[$module][related][$related_field][$related])){
	        			$found = false;
	        			foreach ($this->field_definition[$module][related][$related_field][$related] as $f){
	        				if ($f[name] == $fieldname){
	        					$found = $this->field_definition[$module][master][$f[id_reference]][name];
	        					if ($found)
	        						break;
	        				}
	        			}
	        			if ($found){
	        				$referenceId = $this->parent->get($found);
	        				if($referenceId==null){
								$result="";
							}else{
								$entity = $this->cache->forId($referenceId);
								if($related==="Users" && $entity->getModuleName()=="Groups"){
									list($groupEntityId, $groupId) = vtws_getIdComponents($referenceId);

									require_once('include/utils/GetGroupUsers.php');
									$ggu = new GetGroupUsers();
									$ggu->getAllUsersInGroup($groupId,true);	//crmv@46552

									$users = $ggu->group_users;
									$parts = Array();
									foreach($users as $userId){
										$refId = vtws_getWebserviceEntityId("Users", $userId);
										$entity = $this->cache->forId($refId);
										$data = $entity->getData();
										if($this->useValue($data, $fieldname)){
											$parts[] = $data[$fieldname];
										}
									}
									$result = implode(",", $parts);
								//crmv@24350
								}elseif($entity->getModuleName()===$related){
								//crmv@24350e
									$data = $entity->getData();
									if($this->useValue($data, $fieldname)){
										$result = $data[$fieldname];
									}else{
										$result = '';
									}
								}else{
									$result = '';
								}
							}
	        			}
	        			else{
	        				$result = '';
	        			}
	        		}
	        		else{
	        			$result = '';
	        		}
        		}
        		else{
        			$found_ = false;
        			foreach ($this->field_definition[$module][related] as $field_=>$module_arr_){
        				foreach ($module_arr_ as $module_=>$arrs_){
        					if ($related == $module_){
        						$found_ = $field_;
        						break;
        					}
        				}
        				if ($found_){
        					break;
        				}
        			}
	        		if (is_array($this->field_definition[$module][related][$found_][$related])){
	        			$found = false;
	        			foreach ($this->field_definition[$module][related][$found_][$related] as $f){
	        				if ($f[name] == $fieldname){
	        					$found = $this->field_definition[$module][master][$f[id_reference]][name];
	        					if ($found)
	        						break;
	        				}
	        			}
	        			if ($found){
	        				$referenceId = $this->parent->get($found);
	        				if($referenceId==null){
								$result="";
							}else{
								$entity = $this->cache->forId($referenceId);
								if($related==="Users" && $entity->getModuleName()=="Groups"){
									list($groupEntityId, $groupId) = vtws_getIdComponents($referenceId);

									require_once('include/utils/GetGroupUsers.php');
									$ggu = new GetGroupUsers();
									$ggu->getAllUsersInGroup($groupId,true);	//crmv@46552

									$users = $ggu->group_users;
									$parts = Array();
									foreach($users as $userId){
										$refId = vtws_getWebserviceEntityId("Users", $userId);
										$entity = $this->cache->forId($refId);
										$data = $entity->getData();
										if($this->useValue($data, $fieldname)){
											$parts[] = $data[$fieldname];
										}
									}
									$result = implode(",", $parts);
								//crmv@24350
								}elseif($entity->getModuleName()===$related){
								//crmv@24350e
									$data = $entity->getData();
									if($this->useValue($data, $fieldname)){
										$result = $data[$fieldname];
									}else{
										$result = '';
									}
								}else{
									$result = '';
								}
							}
	        			}
	        			else{
	        				$result = '';
	        			}
	        		}
	        		else{
	        			$result = '';
	        		}
        		}
        		//crmv@31358
        	}
        }
        $this->replacements[$match] = $result; // crmv@38592
        return $result;
	}
	
	// crmv@71678
	protected function getUITypeByName($module, $fieldname) {
		global $adb, $table_prefix;
		$result = $adb->pquery("select uitype from {$table_prefix}_field where tabid=? and fieldname=?", array(getTabid($module), $fieldname));
		$uitype = $adb->query_result_no_html($result,0,"uitype");
		return $uitype;
	}
	// crmv@71678e

	protected function useValue($data, $fieldname) {
		return !empty($data[$fieldname]);
	}

	function parseTemplate(){
		return preg_replace_callback('/\$(.*?)\$/', array($this,"matchHandler"), $this->template);
	}

	function getMetaValue($fieldname){
		switch($fieldname){
			case 'date': return getNewDisplayDate();
			case 'time': return date('h-i-s');
			default: '';
		}
	}

	// crmv@38592
	function getReplacements() {
		return $this->replacements;
	}
	// crmv@38592e

	//crmv@22700
	function getNewsletterValue($fieldname) {
		$moduleName = $this->parent->getModuleName();
		if (!in_array($moduleName,array('Accounts','Contacts','Leads'))) {
			return '';
		}
		$infoFields = getNewsletterTemplateEmailInfoFields($fieldname);
		$realfield = $infoFields['realfield'][$moduleName];
		$data = $this->parent->getData();
		return $data[$realfield];
	}
	function getTrackLink($fieldname) {
		//crmv@2285m
		if(isModuleInstalled('Fairs')) {
			switch($fieldname){
				case 'unsubscription_data_processing':
					$focus = CRMEntity::getInstance('Newsletter');
					return "<a href='$focus->url_unsubscription_data_processing_file'>".getTranslatedString('LBL_HERE')."</a>";
				case 'unsubscription_fair_comunications':
					$focus = CRMEntity::getInstance('Newsletter');
					return "<a href='$focus->url_unsubscription_fair_comunications_file'>".getTranslatedString('LBL_HERE')."</a>";
				case 'unsubscription_third_party_comunications':
					$focus = CRMEntity::getInstance('Newsletter');
					return "<a href='$focus->url_unsubscription_third_party_comunications_file'>".getTranslatedString('LBL_HERE')."</a>";
				case 'unsubscription_fair':
					$focus = CRMEntity::getInstance('Newsletter');
					return "<a href='$focus->url_unsubscription_fair_file'>".getTranslatedString('LBL_HERE')."</a>";
			}
		} else {
		//crmv@2285me
			switch($fieldname){
				case 'unsubscription':
					$focus = CRMEntity::getInstance('Newsletter');
					return "<a href='$focus->url_unsubscription_file'>".getTranslatedString('LBL_HERE')."</a>";
			}
		//crmv@2285m
		}
		//crmv@2285me
	}
	//crmv@22700e
}
//crmv@15309 end
?>
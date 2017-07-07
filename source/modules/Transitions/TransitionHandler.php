<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

class TransitionHandler extends VTEventHandler {

	function handleEvent($eventName, $entityData) {
		global $log, $adb, $table_prefix;
		$moduleName = $entityData->getModuleName();
		//crmv@31357
		$trans_obj = CRMEntity::getInstance('Transitions');
		$trans_obj->Initialize($moduleName);
		//crmv@31357e
		if($eventName == 'vtiger.entity.beforesave') {			
			if ($trans_obj->ismanaged_global()){
				$objId = $entityData->getId();
				$oldStatus = '';
				if(!empty($objId)) {
					$sql = "select columnname,tablename from ".$table_prefix."_field where tabid = ? and fieldname = ?";
					$params = Array(getTabId($moduleName),$trans_obj->status_field);
					$res = $adb->pquery($sql,$params);
					if ($res && $adb->num_rows($res)>0){
						$columnname = $adb->query_result($res,0,'columnname');
						$tablename = $adb->query_result($res,0,'tablename');
						$entity_obj = CRMEntity::getInstance($moduleName);
						$primary_key = $entity_obj->tab_name_index[$tablename];
						$query = "select $columnname from $tablename where $primary_key = ?";
						$res2=$adb->pquery($query,Array($objId));
						if ($res2 && $adb->num_rows($res2)>0){
							$oldStatus = $adb->query_result_no_html($res2,0,$columnname); //crmv@31357
						}
					}
				}
				$entityData->oldStatus = $oldStatus;
			}
		}
		if($eventName == 'vtiger.entity.aftersave') {
			if ($trans_obj->ismanaged_global()){
				$objId = $entityData->getId();		
				$objData = $entityData->getData();
				if ($objData[$trans_obj->status_field] != $entityData->oldStatus){	//crmv@16600
					$trans_obj->insertIntoHistoryTable($entityData->oldStatus,$objData[$trans_obj->status_field],$objId,$_REQUEST[motivation]);	
				}
			}
		}
	}
}

?>

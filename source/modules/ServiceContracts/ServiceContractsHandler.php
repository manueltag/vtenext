<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
 
class ServiceContractsHandler extends VTEventHandler {

	function handleEvent($eventName, $entityData) {
		global $log, $adb, $table_prefix;
		global $current_user;
		if($eventName == 'vtiger.entity.beforesave') {			
			$moduleName = $entityData->getModuleName();
			if ($moduleName == 'HelpDesk') {
				$ticketId = $entityData->getId();
				$oldStatus = '';
				if(!empty($ticketId)) {
					$tktResult = $adb->pquery('SELECT status FROM '.$table_prefix.'_troubletickets WHERE ticketid = ?', array($ticketId));
					if($adb->num_rows($tktResult) > 0) {
						$oldStatus = $adb->query_result($tktResult,0,'status');
					}
				}
				$entityData->oldStatus = $oldStatus;
			}
			if ($moduleName == 'ServiceContracts') {				
				$contractId = $entityData->getId();
				$oldTrackingUnit = '';
				if(!empty($contractId)) {
					$contractResult = $adb->pquery('SELECT tracking_unit FROM '.$table_prefix.'_servicecontracts WHERE servicecontractsid = ?', array($contractId));
					if($adb->num_rows($contractResult) > 0) {
						$oldTrackingUnit = $adb->query_result($contractResult,0,'tracking_unit');
					}
				}
				$entityData->oldTrackingUnit = $oldTrackingUnit;
			}
		}

		if($eventName == 'vtiger.entity.aftersave') {
			
			$moduleName = $entityData->getModuleName();
			
			// Update Used Units for the Service Contract, everytime the status of a ticket related to the Service Contract changes
			//crmv@19400
//			if ($moduleName == 'HelpDesk' && $_REQUEST['return_module'] != 'ServiceContracts') {
			if ($moduleName == 'HelpDesk') {
			//crmv@19400e
				$ticketId = $entityData->getId();
				$data = $entityData->getData();
				//crmv@19400
				/*
				if($data['ticketstatus'] != $entityData->oldStatus) {
					if(strtolower($data['ticketstatus']) == 'closed' || strtolower($entityData->oldStatus) == 'closed') {
						if (strtolower($entityData->oldStatus) == 'closed') {
							$op = '-';
						} else {
							$op = '+';
						}
						$contract_tktresult = $adb->pquery("SELECT crmid FROM vtiger_crmentityrel WHERE module = 'ServiceContracts'" .
								" AND relmodule = 'HelpDesk' AND relcrmid = ?", array($ticketId));
						$noOfContracts = $adb->num_rows($contract_tktresult);
						if($noOfContracts > 0) {
							for($i=0;$i<$noOfContracts;$i++) {
								$contract_id = $adb->query_result($contract_tktresult,$i,'crmid');			
								$scFocus = CRMEntity::getInstance('ServiceContracts');
								$scFocus->id = $contract_id;
								$scFocus->retrieve_entity_info_no_html($contract_id,'ServiceContracts');
								
								$prevUsedUnits = $scFocus->column_fields['used_units'];
								if(empty($prevUsedUnits)) $prevUsedUnits = 0;
								
								$usedUnits = $scFocus->computeUsedUnits($data);
								if ($op == '-') {
									$totalUnits = $prevUsedUnits - $usedUnits;
								} else {
									$totalUnits = $prevUsedUnits + $usedUnits;									
								}
								$scFocus->updateUsedUnits($totalUnits);
								$scFocus->calculateProgress();							
							}
						}
					}
				}
				*/
				if(strtolower($data['ticketstatus']) == 'closed') {
					//crmv@63349
					$ticketId = intval($ticketId);

					// removed join with user table, shouldn't be done
					/*$tmodreltables = TmpUserModRelTables::getInstance();
					$tabname = $entityData->focus->setupTemporaryRelTable('HelpDesk','ServiceContracts',$ticketId);
					
					if ($tabname == $tmodreltables->tmpTable) {
						$joinCondition = "AND ".$tmodreltables->getJoinCondition('HelpDesk', 'ServiceContracts', $current_user->id, $ticketId, null, null, 't');
					} else {
						$joinCondition = "";
					}
					
					$sql = "select relcrmid from {$tabname} t
					inner join {$table_prefix}_crmentity c on c.crmid = t.relcrmid
					where c.deleted = 0 $joinCondition";
					//crmv@63349e
					*/
					
					// crmv@96226
					$sql = 
						"SELECT {$table_prefix}_crmentityrel.crmid as relcrmid FROM {$table_prefix}_crmentityrel
						INNER JOIN {$table_prefix}_crmentity ON {$table_prefix}_crmentity.crmid = {$table_prefix}_crmentityrel.relcrmid
						INNER JOIN {$table_prefix}_troubletickets ON {$table_prefix}_troubletickets.ticketid = {$table_prefix}_crmentity.crmid
						WHERE module = 'ServiceContracts' AND {$table_prefix}_crmentityrel.relcrmid = ?
							AND relmodule = 'HelpDesk' AND deleted = 0
						UNION ALL
						SELECT {$table_prefix}_crmentityrel.relcrmid FROM {$table_prefix}_crmentityrel
						INNER JOIN {$table_prefix}_crmentity ON {$table_prefix}_crmentity.crmid = {$table_prefix}_crmentityrel.crmid
						INNER JOIN {$table_prefix}_troubletickets ON {$table_prefix}_troubletickets.ticketid = {$table_prefix}_crmentity.crmid
						WHERE relmodule = 'ServiceContracts' AND {$table_prefix}_crmentityrel.crmid = ?
							AND module = 'HelpDesk' AND deleted = 0";
		
					$contract_tktresult = $adb->pquery($sql,Array($ticketId,$ticketId));
					if ($contract_tktresult) {
						$scFocus = CRMEntity::getInstance('ServiceContracts');
						while($row = $adb->fetchByAssoc($contract_tktresult,-1,false)){
							$scFocus->updateServiceContractState($row['relcrmid']);
						}
					}
					// crmv@96226e
				}
				//crmv@47905 e
				//crmv@19400e
			}
			
			// Update the Planned Duration, Actual Duration, End Date and Progress based on other field values.			
			if ($moduleName == 'ServiceContracts') {				
				$contractId = $entityData->getId();	
				$data = $entityData->getData();							
				$scFocus = CRMEntity::getInstance('ServiceContracts');
				if($data['tracking_unit'] != $entityData->oldTrackingUnit) { // Need to recompute used_units based when tracking_unit changes.
					$scFocus->updateServiceContractState($contractId);
				} else {
					$scFocus->id = $contractId;
					$scFocus->retrieve_entity_info($contractId,'ServiceContracts');
					$scFocus->calculateProgress();
					$scFocus->updateResidualUnits();	//crmv@19400
				}
			}
		}
	}
}

?>

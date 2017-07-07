<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@92272 */

class PMActionDelete extends SDKExtendableClass {
	
	function edit(&$smarty,$id,$elementid,$retrieve,$action_type,$action_id='') {
		$PMUtils = ProcessMakerUtils::getInstance();
		$record_involved = '';
		if ($action_id != '') {
			$vte_metadata = Zend_Json::decode($retrieve['vte_metadata']);
			if (!empty($vte_metadata[$elementid])) {
				$metadata_action = $vte_metadata[$elementid]['actions'][$action_id];
				$record_involved = $metadata_action['record_involved'];
			}
			$smarty->assign('METADATA', $metadata_action);
		}
		$records_pick = $PMUtils->getRecordsInvolvedOptions($id, $record_involved);
		$smarty->assign("RECORDS_INVOLVED", $records_pick);
	}
	
	function execute($engine,$actionid) {
		$action = $engine->vte_metadata['actions'][$actionid];
		list($metaid,$module) = explode(':',$action['record_involved']);
		$record = $engine->getCrmid($metaid);
		if ($record !== false) {
			$engine->log("Action Delete","$actionid {$action['action_type']} {$action['action_title']}");
			$focus = CRMEntity::getInstance($module);
			$focus->trash($module, $record);
			//crmv@112539
			$engine->logElement($engine->elementid, array(
				'action_type'=>'Delete',
				'action_title'=>$action['action_title'],
				'metaid'=>$metaid,
				'crmid'=>$record,
				'module'=>$module,
			));
			//crmv@112539e
		}
	}
}
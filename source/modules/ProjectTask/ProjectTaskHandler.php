<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@104562 */
class ProjectTaskHandler extends VTEventHandler {
	function handleEvent($eventName, $data) {
		if (!($data->focus instanceof ProjectTask)) {
			return;
		}
		if($eventName == 'vtiger.entity.beforesave') {
			$id = $data->getId();
			$module = $data->getModuleName();
			$focus = $data->getData();
			if ($focus['auto_working_days'] == 'on' || $focus['auto_working_days'] == 1) {
				$crmv_utils = CRMVUtils::getInstance();
				$working_days = $crmv_utils->number_of_working_days(getValidDBInsertDateValue($focus['startdate']), getValidDBInsertDateValue($focus['enddate']));
				$data->set('working_days', $working_days);
			}
		}
	}
}
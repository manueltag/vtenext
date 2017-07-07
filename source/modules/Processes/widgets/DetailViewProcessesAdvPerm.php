<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@100731 */
require_once('Smarty_setup.php');
require_once('modules/Settings/ProcessMaker/ProcessMakerUtils.php');

class Processes_DetailViewProcessesAdvPerm {
	
	private $_name;
	private $title;
	protected $context = false;

	function __construct() {
		$this->_name = 'DetailViewProcessesAdvPerm';
		$this->title = getTranslatedString('LBL_PM_ADVANCED_PERMISSIONS_WIDGET','Settings');
	}

	function name() {
		return $this->_name;
	}

	function title() {
		return $this->title;
	}
	
	function getFromContext($key, $purify=false) {
		if ($this->context) {
			$value = $this->context[$key];
			if ($purify && !empty($value)) {
				$value = vtlib_purify($value);
			}
			return $value;
		}
		return false;
	}
	
	function process($context = false) {
		global $theme, $app_strings;
		$this->context = $context;
		$sourceRecordId = $this->getFromContext('ID', true);
		$smarty = new vtigerCRM_Smarty;

		$PMUtils = ProcessMakerUtils::getInstance();
		$resources = $PMUtils->getAdvancedPermissionsResources($sourceRecordId);
		$smarty->assign('RESOURCES', $resources);

		$smarty->display('modules/Processes/widgets/DetailViewProcessesAdvPerm.tpl');
	}
}
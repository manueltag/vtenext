<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is:  CRMVILLAGE.BIZ VTECRM
* The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
* Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
* All Rights Reserved.
***************************************************************************************/
/* crmv@43864 */
require_once('include/BaseClasses.php');

class Popup extends SDKExtendableUniqueClass {

	public $sortLinkModules = true;		// if true, sorts modules alfabetically
	public $excludedLinkMods = array('Calendar', 'Messages', 'Fax', 'Sms', 'ModComments');

	/*
	 * Populate the $_REQUEST to simulate an EditView called with parameters
	 */
	public function populateRequestForEdit($from_module, $from_crmid, $for_module) {

		if ($for_module == 'Calendar') {
			return $this->populateRequestForEditCalendar($from_module, $from_crmid);
		} else {
			// get all the 1-n relations with the other module and populate the fields
			$RM = RelationManager::getInstance();

			$relations = $RM->getRelations($from_module, ModuleRelation::$TYPE_1TON, $for_module);
			foreach ($relations as $r) {
				if (!empty($r->fieldname)) {
					$_REQUEST[$r->fieldname] = $from_crmid;
				}
			}

			//crmv@41906
			$focus = CRMEntity::getInstance($from_module);
			$focus->retrieve_entity_info_no_html($from_crmid, $from_module);

			$entity_fields = getEntityFieldNames($for_module);
			if (is_array($entity_fields['fieldname'])) {
				$entity_fieldname = $entity_fields['fieldname'][0];
			} else {
				$entity_fieldname = $entity_fields['fieldname'];
			}
			if (!empty($entity_fieldname)) {
				$_REQUEST[$entity_fieldname] = $focus->column_fields['subject'];
			}
			($for_module == 'Messages') ? $description = $focus->stripHTML($focus->column_fields['description']) : $description = strip_tags($focus->column_fields['description']);	//crmv@59097
			($for_module == 'Documents') ? $_REQUEST['notecontent'] = $description : $_REQUEST['description'] = $description;
			//crmv@41906e
		}
	}

	public function populateRequestForEditCalendar($from_module, $from_crmid) {

		if ($from_crmid <= 0) return;

		$focus = CRMEntity::getInstance($from_module);
		$focus->retrieve_entity_info($from_crmid, $from_module);

		if ($from_module == 'Messages') {

			// now populate some values
			$_REQUEST['subject'] = $focus->column_fields['subject'];
			$_REQUEST['description'] = $focus->stripHTML($focus->column_fields['description']);	//crmv@58485	crmv@59097

			// contacts (directly linked or from email addresses)
			$RM = RelationManager::getInstance();
			$relConts = $RM->getRelatedIds($from_module, $from_crmid, array('Contacts'));
			$email = $focus->column_fields['mfrom'];
			$otherConts = $focus->getEntitiesFromEmail($email, false, false, array('Contacts'), true);
			if ($otherConts['crmid'] > 0) $relConts[] = $otherConts['crmid'];

			if (count($relConts) > 0) {
				$_REQUEST['contact_id'] = $relConts[0];
			}
		} else {
			// populate the related module
			$calendarMods = getCalendarRelatedToModules();
			if (in_array($from_module, $calendarMods)) {
				$_REQUEST['parent_id'] = $from_crmid;
			}
			if ($from_module == 'Contacts') {
				$_REQUEST['contact_id'] = $from_crmid;
				// crmv@72718
				if(!empty($from_crmid)) {
					$_REQUEST['account_id'] = getSingleFieldValue($focus->table_name,'accountid','contactid',$from_crmid);
				}
				// crmv@72718e
			}
		}
	}

	/*
	 * Get the list of mdules available for the Link
	 * If the module's class define the named methid, use that instead
	 */
	public function getLinkModules($from_module, $from_crmid = '', $mode = '') {

		// check override
		$modInstance = CRMEntity::getInstance($from_module);
		if (method_exists($modInstance, 'getPopupLinkModules')) {
			$r = $modInstance->getPopupLinkModules($from_module, $from_crmid, $mode);
			if ($r !== false) return $r;
		}

		// get related lists
		$mods = array();
		if ($from_module == 'Calendar') {
			//$relatedto = getCalendarRelatedToModules();
			$relatedto[] = 'Contacts';
			foreach ($relatedto as $relmod) {
				if (isPermitted($relmod, 'EditView')) {
					$mods[] = $relmod;
				}
			}
		} elseif ($from_module == 'ModComments') {
			$rm = RelationManager::getInstance();
			$mods = $rm->getRelatedModules($from_module);
		} else {
			$rellists = getRelatedLists($from_module, null);
			// crmv@49398
			if (is_array($rellists)) {
				foreach ($rellists as $relinfo) {
					$relmod = $relinfo['related_tabname'];
					$actions = array_filter(array_map('trim', explode(',',strtolower(trim($relinfo['actions'])))));
					if (in_array('select', $actions)) {
						$mods[] = $relmod;
					}
				}
			}
			// crmv@49398e
		}

		$mods = array_diff($mods, $this->excludedLinkMods);

		// sort by module label
		if ($this->sortLinkModules) {
			usort($mods, create_function('$m1, $m2', 'return strcasecmp(getTranslatedString($m1, $m1), getTranslatedString($m2, $m2));'));
		}

		return $mods;
	}

	/*
	 * Get a list of modules available for creation
	 */
	public function getCreateModules($from_module, $from_crmid = '', $mode = '') {
		$modInstance = CRMEntity::getInstance($from_module);
		if (method_exists($modInstance, 'getPopupCreateModules')) {
			$r = $modInstance->getPopupCreateModules($from_module, $from_crmid, $mode);
			if ($r !== false) return $r; // allow standard processing if function returns false
		}
		// get related lists
		$mods = array();
		if ($from_module == 'Calendar') {
			//$relatedto = getCalendarRelatedToModules();
			$relatedto[] = 'Contacts';
			foreach ($relatedto as $relmod) {
				if (isPermitted($relmod, 'EditView')) {
					$mods[] = $relmod;
				}
			}
		} elseif ($from_module == 'ModComments') {
			$rm = RelationManager::getInstance();
			$mods = $rm->getRelatedModules($from_module);
		} else {
			$rellists = getRelatedLists($from_module, null);
			// crmv@49398
			if (is_array($rellists)) {
				foreach ($rellists as $relinfo) {
					$relmod = $relinfo['related_tabname'];
					$actions = array_filter(array_map('trim', explode(',',strtolower(trim($relinfo['actions'])))));
					if (in_array('add', $actions) && isPermitted($relmod, 'EditView')) {
						$mods[] = $relmod;
					}
				}
			}
			// crmv@49398e
		}

		$mods = array_diff($mods, $this->excludedLinkMods);

		// sort by module label
		if ($this->sortLinkModules) {
			usort($mods, create_function('$m1, $m2', 'return strcasecmp(getTranslatedString($m1, $m1), getTranslatedString($m2, $m2));'));
		}

		return $mods;
	}

	public function getAllModules($from_module, $from_crmid = '', $mode = '') {
		$link = $this->getLinkModules($from_module, $from_crmid = '', $mode = '');
		$create = $this->getCreateModules($from_module, $from_crmid = '', $mode = '');

		$all = array_unique(array_merge($link, $create));
		if ($this->sortLinkModules) {
			usort($all, create_function('$m1, $m2', 'return strcasecmp(getTranslatedString($m1, $m1), getTranslatedString($m2, $m2));'));
		}

		return $all;
	}

	function vtlib_handler($modulename, $event_type) {
	}

	//crmv@47104
	function addOtherParams(&$params) {
		if (isInventoryModule($_REQUEST['module']) && isProductModule($_REQUEST['return_module']) && !empty($_REQUEST['from_crmid'])) {
			$params['parent_id'] = $params['product_id'] = $_REQUEST['from_crmid'];
		}
	}
	//crmv@47104e
}
?>
<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

// crmv@120738

require_once('include/BaseClasses.php');

class SettingsUtils extends SDKExtendableUniqueClass {

	public function getSettingsBlocks() {
		global $adb, $table_prefix;
		
		$blocksQ = "SELECT * FROM {$table_prefix}_settings_blocks ORDER BY sequence";
		$blocksR = $adb->query($blocksQ);
		$blocks = array();
		
		if ($blocksR && $adb->num_rows($blocksR)) {
			while ($row = $adb->fetchByAssoc($blocksR, -1, false)) {
				$blockid = $row['blockid'];
				
				$image = explode('.', $row['image']);
				$row['image_type'] = $image[1] ? 'image' : 'icon';
				
				$blocks[$blockid] = $row;
			}
		}
		
		return $blocks;
	}
		
	public function getSettingsFields() {
		global $adb, $table_prefix;
		
		$fieldsQ = "SELECT * FROM {$table_prefix}_settings_field WHERE blockid <> ? AND active = 0 ORDER BY blockid, sequence";
		$fieldsR = $adb->pquery($fieldsQ, array($this->getSettingsBlockId('LBL_MODULE_MANAGER')));
		$fields = array();
		
		if ($fieldsR && $adb->num_rows($fieldsR)) {
			while ($row = $adb->fetchByAssoc($fieldsR, -1, false)) {
				$blockid = $row['blockid'];
				$iconpath = $row['iconpath'];
				$description = $row['description'];
				$linkto = $row['linkto'];
				$action = getPropertiesFromURL($linkto, 'action');
				$module = getPropertiesFromURL($linkto, 'module');
				$name = $row['name'];
				$formodule = getPropertiesFromURL($linkto, 'formodule');
				
				$fields[$blockid][] = array('icon' => $iconpath, 'description' => $description, 'link' => $linkto, 'name' => $name, 'action' => $action, 'module' => $module, 'formodule' => $formodule);
			}
		}
		
		// add blanks for 4-column layout
		foreach ($fields as $blockid => &$field) {
			if (count($field) > 0 && count($field) < 4) {
				for ($i = count($field); $i < 4; $i++) {
					$field[$i] = array();
				}
			}
		}
		
		return $fields;
	}

	public function getSettingsBlockId($label) {
		global $adb, $table_prefix;
		
		$blockid = 0;
		
		$blockQ = "SELECT blockid FROM {$table_prefix}_settings_blocks WHERE label = ?";
		$blockR = $adb->pquery($blockQ, array($label));
		
		if ($blockR && $adb->num_rows($blockR)) {
			$blockid = intval($adb->query_result($blockR, 0, 'blockid'));
		}
		
		return $blockid;
	}

	public function isModuleSettingPermitted($module) {
		if (file_exists("modules/$module/Settings.php") && isPermitted('Settings', 'index', '') == 'yes') {
			return 'yes';
		}
		return 'no';
	}
	
}

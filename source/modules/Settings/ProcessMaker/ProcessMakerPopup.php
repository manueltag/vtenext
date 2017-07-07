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
require_once('modules/Popup/Popup.php');

class ProcessMakerPopup extends Popup {
	
	function getModules() {
		global $adb, $table_prefix;
		$mods = array();
		$result = $adb->pquery("select name from {$table_prefix}_tab where isentitytype = 1 and presence in (0,2) and name not in (".generateQuestionMarks($this->excludedLinkMods).")",array($this->excludedLinkMods));
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$mods[] = $row['name'];
			}
		}
		// sort by module label
		if ($this->sortLinkModules) {
			usort($mods, create_function('$m1, $m2', 'return strcasecmp(getTranslatedString($m1, $m1), getTranslatedString($m2, $m2));'));
		}
		return $mods;
	}
}
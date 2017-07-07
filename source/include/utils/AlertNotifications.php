<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@98484 */
class AlertNotifications extends SDKExtendableUniqueClass {
	function log($str) {
		echo $str."<br>\n";
	}
	function getLabel($id) {
		global $adb, $table_prefix;
		$result = $adb->pquery("select label from {$table_prefix}_alertnot where id = ?", array($id));
		if ($result && $adb->num_rows($result) > 0) {
			return getTranslatedString($adb->query_result($result,0,'label'));
		}
		return false;
	}
 	function save($label, $alert_arr_translations) {
		global $adb, $table_prefix;
		$result = $adb->pquery("select id from {$table_prefix}_alertnot where label = ?", array($label));
		if ($adb->num_rows($result) > 0) {
			$this->log("Alert $label already exists with id ".$adb->query_result($result,0,'id'));
		} else {
			$adb->pquery("insert into {$table_prefix}_alertnot(id,label) values(?,?)", array($adb->getUniqueID("{$table_prefix}_alertnot"),$label));
			if (!empty($alert_arr_translations)) {
				SDK::setLanguageEntries('APP_STRINGS', $label, $alert_arr_translations);
			}
		}
	}
	function isSeen($id,$userid) {
		global $adb, $table_prefix, $current_user;
		if (empty($userid)) $userid = $current_user->id;
		$result = $adb->pquery("select seen from {$table_prefix}_alertnot_seen where id = ? and userid = ?", array($id,$userid));
		if ($result && $adb->num_rows($result) > 0) {
			return ($adb->query_result($result,0,'seen') == 1);
		} else {
			return false;
		}
	}
	function setSeen($id,$userid) {
		global $adb, $table_prefix, $current_user;
		if (empty($userid)) $userid = $current_user->id;
		$result = $adb->pquery("select id from {$table_prefix}_alertnot_seen where id = ? and userid = ?", array($id,$userid));
		if ($result && $adb->num_rows($result) > 0) {
			$adb->pquery("update {$table_prefix}_alertnot_seen set seen = ?, seen_date = ? where id = ? and userid = ?", array(1,date('Y-m-d H:i:s'),$id,$userid));
		} else {
			$adb->pquery("insert into {$table_prefix}_alertnot_seen(id,userid,seen,seen_date) values(?,?,?,?)", array($id,$userid,1,date('Y-m-d H:i:s')));
		}
	}
}
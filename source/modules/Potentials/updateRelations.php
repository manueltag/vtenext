<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
 //crmv@44187

// do the standard stuff
require('modules/VteCore/updateRelations.php');

// crmv@49622 fix the delete
if($mode == 'delete') {

	$idlist = vtlib_purify($_REQUEST['idlist']);
	$dest_mod = vtlib_purify($_REQUEST['destination_module']);

	// Split the string of ids
	$ids = array_filter(explode (";",$idlist));
	if (!empty($ids)) {

		if ($dest_mod == 'Contacts') {
			foreach ($ids as $contactid) {
				$adb->pquery("delete from {$table_prefix}_contpotentialrel where potentialid = ? and contactid = ?", array($forCRMRecord, $contactid));
			}
		} else {
			foreach ($ids as $accountid) {
				$adb->pquery("delete from {$table_prefix}_accpotentialrel where potentialid = ? and accountid = ?", array($forCRMRecord, $accountid));
			}
		}
	}
}
// crmv@49622e

// and now add extra informations for the related
if ($forCRMRecord > 0 && $_REQUEST['extra_relation_info'] == '1') {

	if ($dest_mod == 'Contacts') {
		$main_contact = ($_REQUEST['main_contact'] == 'on' ? 1 : 0);
		$contact_role = vtlib_purify($_REQUEST['contact_role']);

		if (is_array($ids)) {
			foreach ($ids as $contactid) {
				$adb->pquery("update {$table_prefix}_contpotentialrel set main_contact = ?, contact_role = ? where potentialid = ? and contactid = ?", array($main_contact, $contact_role, $forCRMRecord, $contactid));
			}
		}
	} elseif ($dest_mod == 'Accounts') {
		$main_account = ($_REQUEST['main_account'] == 'on' ? 1 : 0); // crmv@53923
		$partner_role = vtlib_purify($_REQUEST['partner_role']);

		if (is_array($ids)) {
			foreach ($ids as $accountid) {
				$adb->pquery("update {$table_prefix}_accpotentialrel set main_account = ?, partner_role = ? where potentialid = ? and accountid = ?", array($main_account, $partner_role, $forCRMRecord, $accountid)); // crmv@53923
			}
		}

	}

}

?>
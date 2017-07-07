<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

if ($_REQUEST['mode'] == 'SimpleView') {
	exit;
} elseif ($_REQUEST['mode'] == 'DetailViewMyNotesWidget') {
	$parent = vtlib_purify($_REQUEST['parent']);
	if (!empty($parent)) {
		$rel_notes = $focus->getRelNotes($parent,1);
		if (!empty($rel_notes[0])) {
			$rel_note = $rel_notes[0];
		}
		echo Zend_Json::encode(array('success'=>'true','record'=>$rel_note,'parent'=>$parent));
	}
	exit;
}
?>
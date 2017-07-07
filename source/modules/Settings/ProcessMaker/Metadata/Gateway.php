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

global $adb, $table_prefix, $current_language, $app_strings;

$show_required2go_check = false;
$groups = $PMUtils->getGatewayConditions($id,$elementid,$vte_metadata_arr,$show_required2go_check);
if (empty($groups)) unset($buttons['save']);
$smarty->assign("CONDITION_GROUPS", $groups);
$smarty->assign("SHOW_REQUIRED_CHECK", $show_required2go_check);

$outgoing = $PMUtils->getOutgoing($id,$elementid);
$outgoings = array(''=>getTranslatedString('LBL_NONE'));
foreach($outgoing as $out) {
	$type = $out['shape']['type'];
	$text = $out['shape']['text'];
	$text_conn = $out['connection']['text'];
	$title = $type;
	if (!empty($text)) $title .= ': '.$text;
	if (!empty($text_conn)) $title .= " ($text_conn)";
	
	$outgoings[$out['shape']['id']] = $title;
}
$smarty->assign("OUTGOINGS", $outgoings);
?>
<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@97566 */

$outgoing = $PMUtils->getOutgoing($id,$elementid);
if (!empty($outgoing)) {
	$type = $outgoing[0]['shape']['type'];
	$text = $outgoing[0]['shape']['text'];
	$text_conn = $outgoing[0]['connection']['text'];
	$next_title = $type;
	if (!empty($text)) $next_title .= ': '.$text;
	if (!empty($text_conn)) $next_title .= " ($text_conn)";
}

$timerOptions = array(
	array(0,32,'days',getTranslatedString('LBL_DAYS'),$vte_metadata_arr['days']),
	array(0,24,'hours',getTranslatedString('LBL_HOURS'),$vte_metadata_arr['hours']),
	array(0,60,'min',getTranslatedString('LBL_MINUTES'),$vte_metadata_arr['min'])
);
$smarty->assign("TIMEROPTIONS", $timerOptions);

$smarty->assign("START_LABEL", getTranslatedString('LBL_PM_WAIT','Settings'));
$smarty->assign("END_LABEL", getTranslatedString('LBL_PM_TO_GO_TO_NEXT_STEP','Settings'));
$smarty->assign("NEXT_ELEMENT", $next_title);
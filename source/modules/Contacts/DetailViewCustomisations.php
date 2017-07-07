<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

//Display the error message
if($record != '' && $_SESSION['image_type_error'] != '') {
	echo '<font color="red">'.$_SESSION['image_type_error'].'</font>';
	unset($_SESSION['image_type_error']);
}
$sql = $adb->pquery('select accountid from '.$table_prefix.'_contactdetails where contactid=?', array($focus->id));
$accountid = $adb->query_result($sql,0,'accountid');
if ($accountid == 0) {
	$accountid = '';
}
$smarty->assign("accountid",$accountid);

/* crmv@55961 */
$focusNewsletter = CRMEntity::getInstance('Newsletter');
$email = $focus->column_fields[$focusNewsletter->email_fields[$currentModule]['fieldname']];
$newsletter_unsub_status = $focusNewsletter->receivingNewsletter($email);
$smarty->assign('RECEIVINGNEWSLETTER',$newsletter_unsub_status);
?>
<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

$smarty->assign("TICKETID",$record);

//Added code for Error display in sending mail to assigned to user when ticket is created or updated.
if($_REQUEST['mail_error'] != '')
{
    require_once("modules/Emails/mail.php");
	$ticket_owner = getUserName($focus->column_fields['assigned_user_id']);
    $error_msg = strip_tags(parseEmailErrorString($_REQUEST['mail_error']));
	$error_msg = $app_strings['LBL_MAIL_NOT_SENT_TO_USER']. ' ' . $ticket_owner. '. ' .$app_strings['LBL_PLS_CHECK_EMAIL_N_SERVER'];
	echo $mod_strings['LBL_MAIL_SEND_STATUS'].' <b><font class="warning">'.$error_msg.'</font></b>';
}

//Added button for Convert the ticket to FAQ
if (vtlib_isModuleActive('Faq') && isPermitted("Faq","EditView",'') == 'yes') {
	$smarty->assign("CONVERTASFAQ","permitted");
}

//Added to display the ticket comments information
$smarty->assign("COMMENT_BLOCK",$focus->getCommentInformation($record));
?>
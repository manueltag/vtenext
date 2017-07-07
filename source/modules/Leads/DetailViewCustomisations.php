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

if(isPermitted("Leads","EditView",$_REQUEST['record']) == 'yes' && isPermitted("Leads","ConvertLead") =='yes' && (isPermitted("Accounts","EditView") =='yes' || isPermitted("Contacts","EditView") == 'yes') && (vtlib_isModuleActive('Contacts') || vtlib_isModuleActive('Accounts')))
{
	$smarty->assign("CONVERTLEAD","permitted");
}

/* crmv@55961 */
$focusNewsletter = CRMEntity::getInstance('Newsletter');
$email = $focus->column_fields[$focusNewsletter->email_fields[$currentModule]['fieldname']];
$newsletter_unsub_status = $focusNewsletter->receivingNewsletter($email);
$smarty->assign('RECEIVINGNEWSLETTER',$newsletter_unsub_status);
?>
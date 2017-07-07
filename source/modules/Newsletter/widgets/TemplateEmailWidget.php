<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@80155 */

global $adb, $table_prefix, $theme, $currentModule, $current_user,$default_charset; //crmv@119012

$focus = CRMEntity::getInstance($currentModule);
$focus->id = $_REQUEST['record'];
$focus->retrieve_entity_info($_REQUEST['record'], $currentModule);

$templatename = '';
if (!in_array($focus->column_fields['templateemailid'],array('',0))) {
	$edit_perm = true;
	$result = $adb->pquery("select * from {$table_prefix}_emailtemplates where templateid=?", array($focus->column_fields['templateemailid']));
	$templatename = $adb->query_result($result,0,'templatename');
	$templatename = htmlspecialchars($templatename,ENT_QUOTES,$default_charset); //crmv@119012
	
	$res = $adb->query("select * from {$table_prefix}_field where fieldname = 'bu_mc'");
	if ($res && $adb->num_rows($res) > 0) {
		$saved_bu_mc = explode(' |##| ', $adb->query_result($result,0,'bu_mc'));
		$bu_mc = explode(' |##| ', $current_user->column_fields['bu_mc']);
		$edit_perm = false;
		if (!empty($bu_mc)) {
			foreach($bu_mc as $b) {
				if (in_array($b, $saved_bu_mc)) {
					$edit_perm = true;
					break;
				}
			}
		}
	}
} else {
	$edit_perm = false;
}
?>
<input id="templateemail_name" name="templateemail_name" readonly="" type="text" style="border:1px solid #bababa;" value="<?php echo $templatename; ?>">
<img align="absmiddle" style="cursor:hand;cursor:pointer" onclick="openPopup('index.php?module=Newsletter&action=NewsletterAjax&file=widgets/TemplateEmailList&record=<?php echo $_REQUEST['record']; ?>','TemplateEmailList','top=100,left=200,height=400,width=500,resizable=yes,scrollbars=yes,menubar=no,addressbar=no,status=yes','auto')" language="javascript" title="<?php echo getTranslatedString('LBL_SELECT'); ?>" alt="<?php echo getTranslatedString('LBL_SELECT'); ?>" src="<?php echo vtiger_imageurl('select.gif',$theme);?>">
<img align="absmiddle" style="cursor:hand;cursor:pointer" onclick="openPopup('index.php?module=Newsletter&action=NewsletterAjax&file=widgets/TemplateEmailEdit&record=<?php echo $_REQUEST['record']; ?>','TemplateEmailList','top=100,left=200,height=400,width=500,resizable=yes,scrollbars=yes,menubar=no,addressbar=no,status=yes','auto')" language="javascript" title="<?php echo getTranslatedString('LBL_CREATE'); ?>" alt="<?php echo getTranslatedString('LBL_CREATE'); ?>" src="modules/Newsletter/src/add_template.png" height="20">
<?php if ($edit_perm) { ?>
<img align="absmiddle" style="cursor:hand;cursor:pointer" onclick="openPopup('index.php?module=Newsletter&action=NewsletterAjax&file=widgets/TemplateEmailEdit&record=<?php echo $_REQUEST['record']; ?>&mode=edit','TemplateEmailList','top=100,left=200,height=400,width=500,resizable=yes,scrollbars=yes,menubar=no,addressbar=no,status=yes','auto')" language="javascript" title="<?php echo getTranslatedString('LBL_EDIT'); ?>" alt="<?php echo getTranslatedString('LBL_EDIT'); ?>" src="<?php echo vtiger_imageurl('small_edit.png',$theme);?>" height="20">
<?php } ?>
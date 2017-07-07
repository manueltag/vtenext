<?php
/*********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ********************************************************************************/
/* crmv@80155 */

require_once('include/utils/utils.php');
require('user_privileges/requireUserPrivileges.php'); // crmv@39110

global $adb, $table_prefix, $current_user, $theme;

$small_page_title = getTranslatedString('LBL_EMAIL_TEMPLATES');
include('themes/SmallHeader.php');
?>

<form action="index.php" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="module" value="Users">
	<div style="padding:10px;">
	<table style="background-color: rgb(204, 204, 204);" class="small" border="0" cellpadding="5" cellspacing="1" width="100%">
	<tr>
		<td width="5%" class="lvtCol"><b><?php echo getTranslatedString('LBL_PREVIEW'); ?></b></td>
		<td width="35%" class="lvtCol"><b><?php echo trim($mod_strings['LBL_TEMPLATE_NAME'],':'); ?></b></td>
		<td width="60%" class="lvtCol"><b><?php echo $mod_strings['LBL_DESCRIPTION']; ?></b></td>
	</tr>
<?php
$res = $adb->query("select * from ".$table_prefix."_field where fieldname = 'bu_mc'");
$bu_mc_enabled = ($res && $adb->num_rows($res) > 0);

$sql = "select * from ".$table_prefix."_emailtemplates";
if ($bu_mc_enabled) {
	$bu_mc = explode(' |##| ', $current_user->column_fields['bu_mc']);
	if (!empty($bu_mc)) {
		$cond = array();
		foreach($bu_mc as $b) {
			$cond[] = "bu_mc like '%$b%'"; 
		}
		$sql .= " where ".implode(' or ',$cond); 
	}
}
$sql .= " order by templateid desc";
$result = $adb->query($sql);
if ($result && $adb->num_rows($result) > 0) {
	while($temprow = $adb->fetch_array($result)) {
		$templatename = $temprow["templatename"];
		$folderName = $temprow['foldername'];
		if($is_admin || (!$is_admin && $folderName != 'Personal'))
		{
			echo "<tr class='lvtColData' onmouseover=\"this.className='lvtColDataHover'\" onmouseout=\"this.className='lvtColData'\" bgcolor='white'>";
			echo "<td><a href='javascript:previewTemplate(".$temprow['templateid'].")'><img src='modules/Newsletter/src/preview_mail.png'></a></td>";
			echo "<td><a href='javascript:submittemplate(".$temprow['templateid'].");'>".$temprow["templatename"]."</a></td>";
			printf("<td>%s</td>",$temprow["description"]);
		}
	}
} else {
	echo '<tr><td colspan="3" style="background-color:#ffffff;height:340px" align="center">
		<div style="border: 1px solid rgb(246, 249, 252); background-color: rgb(255, 255, 255); width: 45%; position: relative;">
		<table border="0" cellpadding="5" cellspacing="0" width="98%">
		<tr>
		<td rowspan="2" width="25%"><img src="'.vtiger_imageurl('denied.gif',$theme).'"></td>
		<td nowrap="nowrap" width="75%"><span class="genHeaderSmall">
		'.getTranslatedString('LBL_NO_M').' '.getTranslatedString('LBL_RECORDS').' '.getTranslatedString('LBL_FOUND').' !
		</tr>
		</table>
		</div>
	</td></tr>';
}
?>
</table></div>

<?php
include('themes/SmallFooter.php');
?>

<script>
function submittemplate(templateid)
{
	window.document.location.href = 'index.php?module=Users&action=UsersAjax&file=TemplateMerge&templateid='+templateid;
}
function previewTemplate(templateid)
{
	window.document.location.href = 'index.php?module=Users&action=UsersAjax&file=EmailTemplatePreview&templateid='+templateid;
}
// crmv@22038
jQuery(window).load(function() {
    loadedPopup();
});
// crmv@22038e
</script>
</html>
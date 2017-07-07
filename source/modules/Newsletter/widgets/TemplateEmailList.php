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

global $adb, $table_prefix, $current_user,$default_charset; //crmv@119012

$small_page_title = getTranslatedString('LBL_EMAIL_TEMPLATES','Users');
include('themes/SmallHeader.php');
?>

<script type="text/javascript" src="modules/Newsletter/Newsletter.js"></script> 
<div style="padding:10px;">
	<table style="background-color: rgb(204, 204, 204);" class="small" border="0" cellpadding="5" cellspacing="1" width="100%">
	<tr>
		<td width="5%" class="lvtCol"><b><?php echo getTranslatedString('LBL_PREVIEW'); ?></b></td>
		<td width="35%" class="lvtCol"><b><?php echo trim(getTranslatedString('LBL_TEMPLATE_NAME','Users'),':'); ?></b></td>
		<td width="60%" class="lvtCol"><b><?php echo getTranslatedString('LBL_DESCRIPTION','Users'); ?></b></td>
	</tr>
<?php
$res = $adb->query("select * from ".$table_prefix."_field where fieldname = 'bu_mc'");
$bu_mc_enabled = ($res && $adb->num_rows($res) > 0);

$sql = "select * from ".$table_prefix."_emailtemplates where templatetype = 'Newsletter'";
if ($bu_mc_enabled) {
	$bu_mc = explode(' |##| ', $current_user->column_fields['bu_mc']);
	if (!empty($bu_mc)) {
		$cond = array();
		foreach($bu_mc as $b) {
			$cond[] = "bu_mc like '%$b%'"; 
		}
		$sql .= " and (".implode(' or ',$cond).")"; 
	}
}
$sql .= " order by templateid desc";

$result = $adb->pquery($sql, array());
$temprow = $adb->fetch_array($result);
do
{
	$templatename = $temprow["templatename"];
	$folderName = $temprow['foldername'];
	if($is_admin || (!$is_admin && $folderName != 'Personal'))
	{
		//crmv@119012
		$templatename = popup_from_html($temprow["templatename"]);
		$templatename = htmlspecialchars($templatename,ENT_QUOTES,$default_charset);
		echo "<tr class='lvtColData' onmouseover=\"this.className='lvtColDataHover'\" onmouseout=\"this.className='lvtColData'\" bgcolor='white'>";
		echo "<td><a href='javascript:previewTemplate(".$_REQUEST['record'].",".$temprow['templateid'].",\"".$templatename."\")'><img src='modules/Newsletter/src/preview_mail.png'></a></td>";
		echo "<td><a href='javascript:submittemplate(".$_REQUEST['record'].",".$temprow['templateid'].",\"".$templatename."\");'>".$temprow["templatename"]."</a></td>";
		//crmv@119012e
		printf("<td>%s</td>",$temprow["description"]);
	}
} while($temprow = $adb->fetch_array($result));
?>
</table></div>

<?php
include('themes/SmallFooter.php');
?>

<script>
// crmv@22038
jQuery(window).load(function() {
    loadedPopup();
});
// crmv@22038e
</script>
</html>
<?php
require_once('include/utils/utils.php');
global $app_strings,$current_user,$theme,$adb,$table_prefix;

$image_path = 'themes/'.$theme.'/images/';
$language = $_SESSION['authenticated_user_language'];
$mod_strings = return_module_language($language, "Documents");
$pdf_strings = return_module_language($language, "PDFMaker");

// crmv@30967
$sql="select foldername,folderid from ".$table_prefix."_crmentityfolder where tabid = ? order by foldername";
$res=$adb->pquery($sql,array(getTabId('Documents')));
// crmv@30967e

$options="";
for($i=0;$i<$adb->num_rows($res);$i++)
{
	$fid = $adb->query_result($res,$i,"folderid");
	$fldr_name = $adb->query_result($res,$i,"foldername");
	$options .= '<option value="'.$fid.'">'.$fldr_name.'</option>';
}

echo '
<form name="PDFDocForm" method="post" action="index.php" onSubmit="return validatePDFDocForm();">
<input type="hidden" name="module" value="PDFMaker" />
<input type="hidden" name="action" value="SavePDFDoc" />
<input type="hidden" name="pmodule" value="'.$_REQUEST["return_module"].'" />
<input type="hidden" name="pid" value="'.$_REQUEST["return_id"].'" />
<input type="hidden" name="template_ids" value="" />
<input type="hidden" name="language" value="" />
<div class="closebutton" onclick="jQuery(\'#PDFDocDiv\').hide();"></div>
<table border=0 cellspacing=0 cellpadding=5 width=100%>
<tr>
	<td width="100%" align="left" class="small level3Bg" id="PDFDocDivHandle" style="padding:5px; cursor:move;">'.$pdf_strings["LBL_SAVEASDOC"].'</td>
</tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=100% align=center>
    <tr><td class="small">
        <table border=0 cellspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
            <tr><td colspan="2" class="detailedViewHeader" style="padding-top:5px;padding-bottom:5px;"><b>'.$app_strings["Documents"].'</b></td></tr>
            <tr>
                <td class="dvtCellLabel" width="20%" align="right"><font color="red">*</font>'.$mod_strings["Title"].'</td>
                <td width="80%" align="left">
                	<div class="dvtCellInfo"><input name="notes_title" type="text" class="detailedViewTextBox"></div>
                </td>
            </tr>
            <tr>
                <td class="dvtCellLabel" width="20%" align="right">'.$mod_strings["Folder Name"].'</td>
                <td width="80%" align="left">
                	<div class="dvtCellInfo">
						<select name="folderid" class="detailedViewTextBox">'.$options.'</select>
					</div>
                </td>
            </tr>
            <tr>
                <td class="dvtCellLabel" width="20%" align="right">'.$mod_strings["Note"].'</td>
                <td width="80%" align="left">
                	<div class="dvtCellInfo">
                		<textarea name="notecontent" class="detailedViewTextBox"></textarea>
                	</div>
                </td>
            </tr>
        </table>
    </td></tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
<tr><td align=center class="small">
	<input type="submit" value="'.$app_strings["LBL_SAVE_BUTTON_LABEL"].'" class="crmbutton small create"/>&nbsp;&nbsp;
	<input type="button" name="'.$app_strings["LBL_CANCEL_BUTTON_LABEL"].'" value="'.$app_strings["LBL_CANCEL_BUTTON_LABEL"].'" class="crmbutton small cancel" onclick="fninvsh(\'PDFDocDiv\');" />
</td></tr>
</table>
</form>';
exit;
?>
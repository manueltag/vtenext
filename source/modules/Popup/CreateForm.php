<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@42752 crmv@43050 crmv@43448 crmv@43864 */

require_once('Smarty_setup.php');
require_once('modules/Popup/Popup.php');

global $adb, $table_prefix;
global $mod_strings, $app_strings, $theme;
global $currentModule, $current_user;

$from_module = vtlib_purify($_REQUEST['from_module']);
$from_crmid = intval($_REQUEST['from_crmid']);

$mod = str_replace('.', '', vtlib_purify($_REQUEST['mod']));
$linktomessage = ($_REQUEST['linktomessage'] == 1);

$email = trim($_REQUEST['original_email']);
$name = trim($_REQUEST['original_name']);

$callback_create = vtlib_purify($_REQUEST['callback_create']);

if (isPermitted($mod, 'EditView') != 'yes') die('<p>Not authorized</p>');

// related ids
try {
	$relatedIds = Zend_Json::decode($_REQUEST['relevant_ids']);
	if (empty($relatedIds)) $relatedIds = array();
} catch (Exception $e) {
	$relatedIds = array();
}

$popup = Popup::getInstance();
$focus = CRMEntity::getInstance($from_module);
$focus->id = $from_crmid; // crmv@81136

$popup->populateRequestForEdit($from_module, $from_crmid, $mod);
if (method_exists($focus, 'getPopupQCreateValues')) {
	// TODO: sposta questo in Popup.php
	$presetFields = $focus->getPopupQCreateValues($mod, $relatedIds, $email, $name);
}

// put values into request
if (is_array($presetFields))
foreach ($presetFields as $k=>$v) {
	$_REQUEST[$k] = $v;
}

unset($_REQUEST['record']);
$_REQUEST['module'] = $currentModule = $module = $mod;
$_REQUEST['return_module'] = $from_module;
$_REQUEST['action'] = 'EditView';
$_REQUEST['hide_button_list'] = 1;

$label_back = ($_REQUEST['popup_mode'] == 'onlycreate' ? getTranslatedString('LBL_CANCEL_BUTTON_LABEL') : getTranslatedString('LBL_BACK'));
($_REQUEST['show_create_note'] == 'yes') ? $notes = sprintf(getTranslatedString('LBL_POPUP_RECORDS_NOT_SELECTABLE'),getTranslatedString($currentModule,$currentModule)) : $notes = '';	//crmv@46678

$popup->addOtherParams($_REQUEST);	//crmv@47104
?>
<table border="0" cellspacing="0" cellpadding="0" width=100%"><tr>
	<?php if (!empty($notes)) { ?>
	<td nowrap>
		<span class="helpmessagebox" style="font-style: italic;"><?php echo $notes; ?></span>
	</td>
	<?php } ?>
	<td width="100%" align="right">
		<input class="crmbutton small save" onclick="<?php echo "{$callback_create}('$mod')"; ?>" type="button" title="<?php echo getTranslatedString('LBL_SAVE_BUTTON_TITLE'); ?>" value="<?php echo getTranslatedString('LBL_SAVE_BUTTON_LABEL'); ?>">
		<input class="crmbutton small cancel" onclick="LPOP.create_cancel()" type="button" title="<?php echo $label_back ?>" value="<?php echo $label_back ?>">
	</td>
</tr></table>
<?php
//crmv@sdk-18501
include_once('vtlib/Vtiger/Link.php');
$hdrcustomlink_params = Array('MODULE'=>$currentModule);
$COMMONHDRLINKS = Vtiger_Link::getAllByType(getTabid($currentModule), Array('HEADERSCRIPT'), $hdrcustomlink_params);
foreach ($COMMONHDRLINKS['HEADERSCRIPT'] as $HEADERSCRIPT) {
	echo  '<script type="text/javascript" src="'.$HEADERSCRIPT->linkurl.'"></script>';
}
//crmv@sdk-18501e

$sdk_custom_file = 'EditView';
if (isModuleInstalled('SDK')) {
    $tmp_sdk_custom_file = SDK::getFile($currentModule,$sdk_custom_file);
    if (!empty($tmp_sdk_custom_file)) {
    	$sdk_custom_file = $tmp_sdk_custom_file;
    }
}
require("modules/$currentModule/$sdk_custom_file.php");
?>
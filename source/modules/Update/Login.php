<?php
global $app_strings;
global $currentModule, $current_user,$current_language;
require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('include/utils/utils.php');

if($current_user->is_admin != 'on')
{
	die("<br><br><center>".$app_strings['LBL_PERMISSION']." <a href='javascript:window.history.back()'>".$app_strings['LBL_GO_BACK'].".</a></center>");
}

$smarty = new vtigerCRM_Smarty;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty->assign("IMAGE_PATH",$image_path);
$mod_strings = return_module_language($current_language, $currentModule);
$settings_strings = return_module_language($current_language,'Settings');
$smarty->assign("SMOD", $settings_strings);
$smarty->assign("MOD", return_module_language($current_language,'Update'));
$smarty->assign("APP", $app_strings);

//NB: ripristinando $connection = false; si riattiva la scheramta di Login
//$connection = false;
$connection = true;

if ($_REQUEST['change_login'] != 'true' && ($_REQUEST['server'] != '' && $_REQUEST['server_username'] != '' && $_REQUEST['server_password'] != '')) {
	
	$smarty->assign("MAILSERVER",$_REQUEST['server']);
	$smarty->assign("USERNAME",$_REQUEST['server_username']);
	$smarty->assign("PASSWORD",$_REQUEST['server_password']);
	
	// TODO: eseguire il controllo di connessione SVN, intanto ritorno true
	$connection = true;
	
	if (!$connection) {
		$error_msg = 'Errore di Connessione. Verificare i dati.';
		$smarty->assign("ERROR_MSG",'<b><font class="warning">'.$error_msg.'</font></b>');
	}
}

require_once('vteversion.php');
$smarty->assign("CURRENT_VERSION",$enterprise_current_build);
$smarty->assign("MAX_VERSION",'');	// TODO

$smarty->display("modules/$currentModule/Update.tpl");
?>
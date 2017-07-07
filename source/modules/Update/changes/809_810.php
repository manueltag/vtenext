<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['Morphsuit'] = 'packages/vte/mandatory/Morphsuit.zip';
$_SESSION['modules_to_update']['MyNotes'] = 'packages/vte/mandatory/MyNotes.zip';

$theme = 'softed';
@unlink("themes/$theme/images/header-bg.png");
@unlink("themes/$theme/images/headerlogin.png");
@unlink("themes/$theme/images/toolbar-bg.png");
@unlink("themes/$theme/images/toolbar-bg-sel.png");
@unlink("themes/$theme/images/menuLevel2Bg.png");
@unlink("themes/$theme/images/menuLevel2Bg95.png");
@unlink("themes/$theme/images/UnifiedSearchBg.png");
@unlink("themes/$theme/images/UnifiedSearchSettings.png");
@unlink("themes/$theme/images/level3Bg.png");
@unlink("themes/$theme/images/inner.gif");
@unlink("themes/$theme/images/mailSubHeaderBg.gif");
@unlink("themes/$theme/images/mailSubHeaderBg-grey-big.gif");
@unlink("themes/$theme/images/mailSubHeaderBg-grey-up.gif");
@unlink("themes/$theme/images/mailSubHeaderBg-small.gif");
@unlink("themes/$theme/images/mailSubHeaderBg-up.gif");
@unlink("themes/$theme/images/login_bg.jpg");
@unlink("themes/$theme/images/loginBg.gif");
@unlink("themes/$theme/images/loginbg.jpg");
@unlink("themes/$theme/images/loginBottomBg.gif");
@unlink("themes/$theme/images/loginBottomURL.gif");
@unlink("themes/$theme/images/loginbutton.png");
@unlink("themes/$theme/images/loginbutton.jpg");
@unlink("themes/$theme/images/login-page-bg.jpg");
@unlink("themes/$theme/loginheader.html");

@unlink("themes/logos/VTE_header_other.png");
@unlink("Smarty/templates/modules/MyNotes/widgets/createHeader.tpl");

SDK::setLanguageEntries('APP_STRINGS', 'LBL_SEARCH_ALL', array('it_it'=>'Cerca in tutto il CRM','en_us'=>'Search in all the CRM'));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_AREAS_SETTINGS', array('it_it'=>'Gestisci aree di ricerca','en_us'=>'Areas search settings'));
SDK::setLanguageEntries('Morphsuit', 'LBL_MORPHSUIT_USER_NUMBER_5', array('it_it'=>'5','en_us'=>'5'));
SDK::setLanguageEntries('Morphsuit', 'LBL_MORPHSUIT_USER_NUMBER_200', array('it_it'=>'200','en_us'=>'200'));

$adb->pquery("UPDATE {$table_prefix}_version SET enterprise_project = NULL WHERE enterprise_project = ?",array('Crmvillage'));

require_once('include/utils/DetailViewWidgets.php');
DetailViewWidgets::reorder();
?>
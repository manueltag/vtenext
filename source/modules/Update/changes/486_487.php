<?php
global $adb;


if (!SDK::isUitype(202)) {
	SDK::setUitype(202,'modules/SDK/src/202/202.php','modules/SDK/src/202/202.tpl','modules/SDK/src/202/202.js');
}
if (!SDK::isUitype(203)) {
	SDK::setUitype(203,'modules/SDK/src/203/203.php','modules/SDK/src/203/203.tpl','modules/SDK/src/203/203.js');
}


$fields = array(
	'default_language'	=> array('module'=>'Users', 'block'=>'LBL_USERLOGIN_ROLE', 'name'=>'default_language',	'label'=>'DefaultLanguage',		'table'=>'vtiger_users',	'columntype'=>'C(100)',	'typeofdata'=>'V~O', 	'uitype'=>202),
	'default_theme'		=> array('module'=>'Users', 'block'=>'LBL_USERLOGIN_ROLE', 'name'=>'default_theme', 	'label'=>'DefaultTheme',		'table'=>'vtiger_users', 	'columntype'=>'C(100)',	'typeofdata'=>'V~O',	'uitype'=>203),
);

include('modules/SDK/examples/fieldCreate.php');


$trans_it = array(
	'DefaultLanguage' => 'Lingua',
	'DefaultTheme' => 'Tema',
);

$trans_en = array(
	'DefaultLanguage' => 'language',
	'DefaultTheme' => 'Theme',
);

foreach ($trans_it as $label=>$trans) {
	SDK::setLanguageEntry('Users', 'it_it', $label, $trans);
	SDK::setLanguageEntry('Users', 'en_us', $label, $trans_en[$label]);
}

?>
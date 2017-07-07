<?php
global $enterprise_current_version,$enterprise_mode;

SDK::setLanguageEntries('APP_STRINGS', 'LBL_BROWSER_TITLE', array(
	'it_it'=>"$enterprise_mode $enterprise_current_version",
	'en_us'=>"$enterprise_mode $enterprise_current_version",
	'de_de'=>"$enterprise_mode $enterprise_current_version",
	'nl_nl'=>"$enterprise_mode $enterprise_current_version",
	'pt_br'=>"$enterprise_mode $enterprise_current_version")
);

SDK::setLanguageEntries('Settings', 'LBL_PRIVACY_DESC', array(
	'it_it'=>'To improve the application quality we collect a minimum of information from the installation process: the email of the admin user for future product updates notifications. We do not collect information about your customer data or  emails. You can disable this notifications simply unsubscribing it later. Clicking REGISTER you accept this statements.',
	'en_us'=>'To improve the application quality we collect a minimum of information from the installation process: the email of the admin user for future product updates notifications. We do not collect information about your customer data or  emails. You can disable this notifications simply unsubscribing it later. Clicking REGISTER you accept this statements.',
));
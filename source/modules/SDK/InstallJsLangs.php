<?php
include_once('include/utils/utils.php');
include_once('modules/SDK/LangUtils.php');
$langinfo = vtlib_getToggleLanguageInfo();
$languages = array_keys($langinfo);
 if (empty($languages)) {
	$languages = array('en_us','it_it');
}
foreach ($languages as $language){
	@SDK::importJsLanguage($language);
}
?>
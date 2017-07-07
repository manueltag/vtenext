<?php
include_once('include/utils/utils.php');
global $current_language;
echo Zend_Json::encode(get_lang_strings('ALERT_ARR',$current_language));
exit;
?>
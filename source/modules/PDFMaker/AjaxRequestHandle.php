<?php
global $adb,$current_language;
$module = addslashes($_REQUEST["langmod"]);

$mod_lang=return_specified_module_language($current_language,$module);

$module_lang_labels = array_flip($mod_lang);
$module_lang_labels = array_flip($module_lang_labels);
asort($module_lang_labels);             
    
$keys=implode('||',array_keys($module_lang_labels));
$values=implode('||',$module_lang_labels);
echo $keys.'|@|'.$values;
exit;
?>
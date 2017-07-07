<?php
$_SESSION['modules_to_update']['Morphsuit'] = 'packages/vte/mandatory/Morphsuit.zip';

$path = 'modules';
$files = array(
	'Merge.php'=>array('skip'=>array()),
);
$modules = scandir($path);
foreach ($modules as $module) {
    if ($module === '.' or $module === '..') continue;
    $dir = $path.'/'.$module;
    if (is_dir($dir)) {
    	foreach ($files as $file => $info) {
    		if (!empty($info['skip']) && in_array($module,$info['skip'])) {
    			continue;
    		}
	    	if (file_exists($dir.'/'.$file)) {
	    		//echo $dir.'/'.$file.'<br />';
	        	@unlink($dir.'/'.$file);
	    	}
    	}
    }
}

@unlink('modules/Settings/listwordtemplates.php');
@unlink('modules/Settings/mailmergedownloadfile.php');
@unlink('modules/Settings/deletewordtemplate.php');
@unlink('modules/Settings/upload.php');
@unlink('modules/Settings/savewordtemplate.php');
@unlink('Smarty/templates/ListWordTemplates.tpl');
@unlink('Smarty/templates/CreateWordTemplate.tpl');

$adb->pquery("DELETE FROM {$table_prefix}_settings_field WHERE name = ?",array('LBL_MAIL_MERGE'));

global $adb, $table_prefix;
if (Vtiger_Utils::CheckTable("{$table_prefix}_wordtemplates")) {
	$result = $adb->query("select * from {$table_prefix}_wordtemplates");
	if ($result && $adb->num_rows($result) > 0) {}
	else $adb->query("drop table {$table_prefix}_wordtemplates");
}

SDK::setLanguageEntries('Morphsuit','LBL_MORPHSUIT_BUSINESS_ACTIVATION',array('it_it'=>'E` necessario attivare una versione Business On Site.','en_us'=>'You have to activate a Business On Site version.'));

unset($_SESSION['checkMorphsuit']);
unset($_SESSION['checkUsersMorphsuit']);
unset($_SESSION['morph_mode']);
?>
<?php
$_SESSION['modules_to_update']['Morphsuit'] = 'packages/vte/mandatory/Morphsuit.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter','Targets'));

SDK::setLanguageEntries('ALERT_ARR', 'LBL_CHECK_BOUNCED_MESSAGES', array(
	'it_it' => 'Verifica email respinte',
	'en_us' => 'Check bounced messages',
	'pt_br' => 'Verificação e-mails rejeitados',
));
SDK::setLanguageEntry('PDFMaker','en_us','LBL_OTHER_INFO','Other information');

@unlink('modules/Users/Announcements.php');
@unlink('modules/Settings/Announcements.php');
@unlink('Smarty/templates/Settings/Announcements.tpl');

$path = 'modules';
$files = array(
	'CustomAction.php'=>array('skip'=>array()),
	'CustomView.php'=>array('skip'=>array('CustomView','Emails')),
	'errorRelation.php'=>array('skip'=>array()),
	'ExportRecords.php'=>array('skip'=>array()),
	'FindDuplicateRecords.php'=>array('skip'=>array()),
	'Import.php'=>array('skip'=>array('Import','PDFMaker')),
	'index.php'=>array('skip'=>array('Administration','Calendar','Charts','Conditionals','CustomView','CustomerPortal','Dashboard','Documents','FieldFormulas','Help','Home','Import','M','Messages','Migration','Mobile','Morphsuit','PDFMaker','Reports','Settings','Transitions','Update','Webforms','WSAPP','Corsi','Popup','Area')),
	'ListView.php'=>array('skip'=>array('Calendar','ChangeLog','Conditionals','CustomerPortal','Emails','Messages','ModComments','ModNotifications','PDFMaker','Portal','Projects','RecycleBin','Reports','Rss','Transitions','Users','Corsi')),
	'ListViewPagging.php'=>array('skip'=>array()),
	'MassEdit.php'=>array('skip'=>array('Projects')),
	'MassEditSave.php'=>array('skip'=>array('Faq','Products','Projects','ModComments','Services')),
	'ProcessDuplicates.php'=>array('skip'=>array()),
	'Popup.php'=>array('skip'=>array('Portal','ProjectTask','ProjectMilestone','Popup')),
	'QuickCreate.php'=>array('skip'=>array('Calendar')),
	'TagCloud.php'=>array('skip'=>array()),
	'UnifiedSearch.php'=>array('skip'=>array('Home')),
);
$modules = array_diff(scandir($path),array('VteCore'));
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
@unlink('include/ListView/ListViewPagging.php');
?>
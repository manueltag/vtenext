<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

/* crmv@18592 crmv@47905bis crmv@54707 */

global $adb,$table_prefix;
$tabs = getParentTabs();

$adb->query('delete from tbl_s_menu');
$adb->pquery('insert into tbl_s_menu (type) values (?)',array($_REQUEST['menu_type']));
if ($_REQUEST['menu_type'] == 'modules') {
	$adb->query('delete from tbl_s_menu_modules');
	foreach($_REQUEST['fast_modules'] as $seq => $tabid) {
		$adb->pquery('insert into tbl_s_menu_modules values(?,?,?)',array($tabid,1,$seq));
	}
	foreach($_REQUEST['other_modules'] as $seq => $tabid) {
		$adb->pquery('insert into tbl_s_menu_modules values(?,?,?)',array($tabid,0,$seq));
	}
} else {
	foreach($tabs as $key => $array)
	{
	    if($_REQUEST['ckb_'.$key] == 'check')
	        $value = '0';
	    else
	        $value = '1';
	    $sql = 'UPDATE '.$table_prefix.'_parenttab SET hidden = ? WHERE parenttabid = ?';
		$params = array($value,$key);
	    $adb->pquery($sql,$params);
	}
}

$enable_areas = 0;
if ($_REQUEST['enable_areas'] == 'on') {
	$enable_areas = 1;
}
require_once('modules/Area/Area.php');
$areaManager = AreaManager::getInstance();
$enable_areas = $areaManager->setToolValue('enable_areas',$enable_areas);

$cache = Cache::getInstance('getMenuLayout');
$cache->clear();

$cache = Cache::getInstance('getMenuModuleList');
$cache->clear();

header('location:index.php?module=Settings&action=menuSettings&parenttab=Settings');
?>
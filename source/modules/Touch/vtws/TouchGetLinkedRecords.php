<?php
/* crmv@31780 */

global $login, $userId;

$module = vtlib_purify($_REQUEST['module']);
$recordid = intval($_REQUEST['recordid']);

if (!$login || !$userId) {
	echo 'Login Failed';
} else {

	$currentModule = $module;

	$rm = RelationManager::getInstance();
	$excludedMods = array('ModComments');
	//if ($currentModule == 'Calendar') $excludedMods[] = 'Contacts'; // otherwise if more than 1 contact there's no related list to show

	$relIds = $rm->getRelatedIds($module, $recordid, array(), $excludedMods, false, true);

	$records = array();
	foreach ($relIds as $mod=>$ids) {
		foreach ($ids as $rid) {
			$records[] = array(
				'crmid' => $rid,
				'module' => $mod,
				'entityname' => getEntityName($mod, $rid, true),
			);
		}
	}


	echo Zend_Json::encode(array('entries'=>$records, 'total'=>count($records)));
}
?>
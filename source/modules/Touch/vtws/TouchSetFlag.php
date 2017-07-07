<?php

global $login, $userId, $current_user, $currentModule;
global $adb, $table_prefix;

// parametri
$module = $_REQUEST['module'];
$recordid = intval($_REQUEST['recordid']);
$flag = vtlib_purify($_REQUEST['flag']);
$value = vtlib_purify($_REQUEST['value']);

if (!$login || empty($userId)) {
	echo 'Login Failed';
} elseif ($module != 'ALL' && in_array($module, $touchInst->excluded_modules)) {
	echo "Module not permitted";
} else {

	$currentModule = $module;

	$focus = CRMEntity::getInstance($currentModule);
	$focus->id = $recordid;
	$focus->retrieve_entity_info($recordid, $currentModule);

	if (method_exists($focus, 'setFlag')) {
		try {
			$focus->setFlag($flag,$value);
			$success = true;
		} catch (Exception $e) {
			$success = false;
			$message = $e->getMessage();
		}

	} else {
		$success = false;
		$message = 'The module does not support flags';
	}

	// output
	echo Zend_Json::encode(array('success'=>$success, 'message'=>$message));
}
?>
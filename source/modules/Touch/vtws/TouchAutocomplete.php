<?php
/* crmv@42537 */
global $adb, $table_prefix;
global $login, $userId, $current_user, $currentModule;

$searchstr = $_REQUEST['search'];
$excludeIds = array_filter(explode(':', $_REQUEST['excludeIds']));

if (!$login || empty($userId)) {
	echo 'Login Failed';
} else {

	$realReturn = array();

	if (strlen($searchstr) > 1) {

		$_REQUEST['dont_terminate'] = 'true';
		$_REQUEST['term'] = $searchstr;

		// this is evil!
		ob_start();
		require('modules/Emails/Autocomplete.php');
		ob_end_clean();

		$realReturn = array();
		// change return
		foreach ($return as $rset) {
			$crmid = explode('@', $rset['parent_id']);
			if (in_array($crmid[0].'@'.$crmid[1], $excludeIds)) continue;
			$realReturn[] = array(
				'crmid' => $crmid[0],
				'fieldid' => $crmid[1],
				'module' => $rset['moduleName'],
				'entityname' => htmlentities($rset['label']),
				'basicname' => htmlentities($rset['value']),
				'address' => $rset['hidden_toid'],
			);
		}
	}

	$resultArray = $realReturn;
	$list_count = count($resultArray);

	echo Zend_Json::encode(array('entries' => $resultArray, 'total' => $list_count));
}
?>
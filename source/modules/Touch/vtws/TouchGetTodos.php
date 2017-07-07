<?php
global $login, $userId;


if (!$login || !$userId) {
	echo 'Login Failed';
} else {

	require_once('modules/SDK/src/Todos/Utils.php');
	global $adb, $table_prefix;

	$list = getTodosList($userId, '', $count);

	if ($_REQUEST['onlycount'] == 'true') {
		echo $count;
	} else {

		$output = array();
		foreach ($list as $k=>$v) {
			//if (in_array($v['module'], $touchInst->excluded_modules)) continue;

			if ($v['is_now'] == 'yes') {
				$timestampAgo = getTranslatedString('LBL_TODAY');
			} else {
				$timestampAgo = getFriendlyDate($v['date']);
				// controllo se nel futuro
				if (strpos($timestampAgo,'-') !== false) {
					$timestampAgo = getTranslatedString('LBL_DAY'.date('w',strtotime($v['date'])),'Calendar').' '.getDisplayDate($v['date']);
				}
			}

			// crmv@39110 - get full description
			$v['description'] = getSingleFieldValue($table_prefix.'_crmentity', 'description', 'crmid', $v['activityid']);

			$record = array(
				'crmid' => $v['activityid'],
				'module' => 'Calendar',
				'entityname' => $v['subject'],
				'description' => $v['description'],
				'expired' => ($v['expired'] == 'yes'),
				'is_now' => ($v['is_now'] == 'yes'),
				'date' => $v['date'],
				'timestampago' => $timestampAgo,
			);
			$output[] = $record;
		}
		echo Zend_Json::encode(array('todos'=>$output, 'total'=>$count));
	}

}
?>
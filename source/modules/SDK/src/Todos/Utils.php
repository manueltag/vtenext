<?php

require_once('Smarty_setup.php'); // crmv@36871

//crmv@28295	//crmv@30009
function getTodosList($userid,$mode='',&$count='', $onlycount = false) { // crmv@36871
	global $adb, $history_max_viewed, $current_user,$table_prefix; // crmv@25610

	$arr = getCalendarType('todo','history');
	$pickListValue_comma = "(";
	$noofpickrows=count($arr['status_field_value']);
	if ($noofpickrows!=0){
		for($k=0; $k < $noofpickrows; $k++)
		{
			$pickListValue = $arr['status_field_value'][$k];
			$pickListValue_comma.="'".$pickListValue."'";
			if($k < ($noofpickrows-1))
			$pickListValue_comma.=',';
		}
		$pickListValue_comma.= ")";
	}
	else  $pickListValue_comma = "('')";
	$calendar_condition = $pickListValue_comma;

	// crmv@64325
	$setypeCond = '';
	if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
		$setypeCond = "AND {$table_prefix}_crmentity.setype = 'Calendar'";
	}
	// crmv@36871
	$sql = 'SELECT activityid, due_date, subject, exp_duration, description FROM '.$table_prefix.'_activity
			INNER JOIN '.$table_prefix.'_crmentity ON '.$table_prefix.'_crmentity.crmid = '.$table_prefix.'_activity.activityid
			WHERE deleted = 0 '.$setypeCond.' and '.$table_prefix.'_activity.activitytype = \'Task\' and '.$table_prefix.'_activity.status not in '.$calendar_condition.' AND '.$table_prefix.'_crmentity.smownerid = ?
			ORDER BY '.$table_prefix.'_activity.due_date';
	
	if ($onlycount) {
		// optimezed query for count only
		if ($adb->isMysql()) {
			$countcond = $table_prefix.'_activity.due_date <= NOW()';
		} elseif ($adb->isMssql()) {
			$countcond = $table_prefix.'_activity.due_date <= CURRENT_TIMESTAMP';
		} elseif ($adb->isOracle()) {
			$countcond = $table_prefix.'_activity.due_date <= CURRENT_DATE';
		}
		$sql = 'SELECT count(*) as cnt FROM '.$table_prefix.'_activity
		INNER JOIN '.$table_prefix.'_crmentity ON '.$table_prefix.'_crmentity.crmid = '.$table_prefix.'_activity.activityid
		WHERE deleted = 0 '.$setypeCond.' and '.$table_prefix.'_activity.activitytype = \'Task\' and '.$table_prefix.'_activity.status not in '.$calendar_condition.' AND '.$table_prefix.'_crmentity.smownerid = ? and '.$countcond;
	}
	// crmv@64325e

	if ($mode == 'all') {
		$result = $adb->pquery($sql,array($userid));
	} else {
		$result = $adb->limitpQuery($sql,0,$history_max_viewed,array($userid));
	}

	if ($onlycount) {
		$count = $adb->query_result_no_html($result, 0, 'cnt');
		return array();
	}
	// crmv@36871e

	$count = 0;
	$now = strtotime(date('Y-m-d'));
	$list = array();
	if ($result && $adb->num_rows($result) > 0) {
		while($row=$adb->fetchByAssoc($result)) {
			$expired = 'no';
			$is_now = 'no';
			if (strtotime($row['due_date']) <= $now) {
				$count++;
				$expired = 'yes';
				if (strtotime($row['due_date']) == $now) {
					$is_now = 'yes';
				}
			}
			$list[] = array('activityid'=>$row['activityid'],'subject'=>$row['subject'],'date'=>$row['due_date'],'description'=>textlength_check($row['description']),'expired'=>$expired,'is_now'=>$is_now, 'exp_duration'=>$row['exp_duration']);
		}
	}
	return $list;
}

// crmv@36871
function getHtmlTodosList($userid,$mode='',&$count='') {
	global $theme;

	$list = getTodosList($userid,$mode,$count);
	$listbydate = array();
	foreach ($list as $info) {
		$unseen = false;
		$expired_str = getTranslatedString('Will expire','Calendar');
		if ($info['expired'] == 'yes') {
			$unseen = true;
			$expired_str = getTranslatedString('Expired','Calendar');
			if ($info['is_now'] == 'yes') {
				$expired_str = getTranslatedString('Will expire','Calendar');
			}
		}
		$timestampAgo = $info['date'];
		if ($info['is_now'] == 'yes') {
			$timestampAgo = getTranslatedString('LBL_TODAY');
		} else {
			if (isModuleInstalled('ModNotifications')) {
				require_once('modules/ModNotifications/models/Comments.php');
				$model = new ModNotifications_CommentsModel(array('createdtime'=>$info['date']));
				$timestampAgo = $model->timestampAgo();
				$timestamp = $model->timestamp();
				if (strpos($timestampAgo,'-') !== false) {
					$timestampAgo = $timestamp;
				}
			}
		}
		if (empty($info['exp_duration'])) $info['exp_duration'] = 'DurationMore';

		$todoItem = array(
			'activityid'=>$info['activityid'],
			'subject'=>$info['subject'],
			'duration'=>$info['exp_duration'],
			'description'=>$info['description'],
			'timestamp'=>$timestamp,
			'timestamp_ago'=>$timestampAgo,
			'expired_str'=>$expired_str,
			'unseen'=> $unseen,
		);

		$listbydate[$timestampAgo][] = $todoItem;
		$listbyduration[getTranslatedString($info['exp_duration'], 'Calendar')][] = $todoItem;

	}
	if (is_array($listbyduration)) ksort($listbyduration);

	$min_todos_in_period = 2;

	$smarty = new vtigerCRM_Smarty();
	$smarty->assign('THEME', $theme);
	$smarty->assign('TODOLIST_TODOSINPERIOD', $min_todos_in_period);
	$smarty->assign('TODOLIST_DATE', $listbydate);
	$smarty->assign('TODOLIST_DURATION', $listbyduration);
	
	// crmv@119414
	$tpl = 'modules/SDK/src/Todos/TodoList.tpl';
	$mode = intval($_REQUEST['fastmode']);
	if ($mode) {
		$tpl = 'modules/SDK/src/Todos/FastTodoList.tpl';
	}
	return $smarty->fetch($tpl);
	// crmv@119414e
}
// crmv@36871e

//crmv@28295e	//crmv@30009e
?>

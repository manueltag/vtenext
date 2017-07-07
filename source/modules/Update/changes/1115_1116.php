<?php
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';


// crmv@81136
// now recalculate the cleaned bodies if they contain a tel:link
// only the last 20 emails of the last week are taken
$messFocus = CRMEntity::getInstance('Messages');
$users = array();
$res = $adb->query("select id from {$table_prefix}_users where status = 'Active'");
while ($row = $adb->fetchByAssoc($res, -1, false)) {
	$users[] = $row['id'];
}
foreach ($users as $userid) {
	// take the emails (max 20) of the past 7 days which contains a tel link, and convert them
	$res = $adb->limitpQuery("
		select messagesid, xuid, description from {$table_prefix}_messages 
		inner join {$table_prefix}_crmentity on crmid = {$table_prefix}_messages.messagesid
		where deleted = 0 and smownerid = ?
		and mdate > ?
		and description like '%href=\"tel:%'
		and folder not in ('Drafts', 'Junk', 'Sent', 'Trash')
		order by modifiedtime desc", 0, 20, array($userid, date('Y-m-d H:i:s', time()-3600*24*7)));
	while ($row = $adb->fetchByAssoc($res, -1, false)) {
		$messFocus->id = $row['messagesid'];
		// save the cleaned body
		$attachments_info = $messFocus->getAttachmentsInfo();
		$message_data = array('other'=>$attachments_info);
		$magicHTML = $messFocus->magicHTML($row['description'], $row['xuid'], $message_data);
		$messFocus->saveCleanedBody($messFocus->id, $magicHTML['html'], $magicHTML['content_ids']);
	}
}
// crmv@81136e

// crmv@81270
// remove chart type area is not used
$res = $adb->pquery("SELECT COUNT(*) as cnt FROM {$table_prefix}_charts h INNER JOIN {$table_prefix}_crmentity c ON c.crmid = h.chartid WHERE h.chart_type = ?", array('Split'));
if ($res) {
	$cnt = $adb->query_result_no_html($res, 0, 'cnt');
	if ($cnt == 0) {
		// get the picklistid
		$res = $adb->pquery("select picklist_valueid from {$table_prefix}_chart_type where chart_type = ?", array('Split'));
		$picklistid = $adb->query_result_no_html($res, 0, "picklist_valueid");
		if ($picklistid > 0) {
			// delete
			$adb->pquery("delete from {$table_prefix}_chart_type where chart_type = ?", array('Split'));
			$adb->pquery("delete from {$table_prefix}_role2picklist where picklistvalueid = ?", array($picklistid));
		}
	}
}

// uncomment if you want to hide Dashboards
// by default, they are hidden only during install, not during update
/*
$module = Vtecrm_Module::getInstance('Dashboard');
if ($module && $module->id > 0) {
	$module->hide(array(
		'hide_module_manager' => 1,
		'hide_profile' => 1,
		'hide_report' => 1,
	));
	// now disable (I can't skip it, otherwise the tabids would change)
	$adb->pquery("UPDATE {$table_prefix}_tab SET presence = 1 WHERE tabid = ?", array($module->id));
	$adb->pquery("DELETE FROM tbl_s_menu_modules WHERE tabid = ?", array($module->id));
	// recreate tabdata
	$module->syncfile();
	// and clear the cache
	if (class_exists('Cache')) {
		$cache = Cache::getInstance('installed_modules');
		if ($cache) $cache->clear();
	}
}
*/
// crmv@81270e

// crmv@81217 - add cf table for FAQ module
$schema = 
'<?xml version="1.0"?>
	<schema version="0.3">
		<table name="'.$table_prefix.'_faqcf">
			<opt platform="mysql">ENGINE=InnoDB</opt>
			<field name="faqid" type="I" size="19">
				<key/>
			</field>
		</table>
	</schema>';
if (!Vtiger_Utils::CheckTable($table_prefix.'_faqcf')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema));
	
	// and insert the ids
	$idcol = 'id';
	$adb->format_columns($idcol);
	$adb->query("INSERT INTO {$table_prefix}_faqcf (faqid) SELECT {$idcol} FROM {$table_prefix}_faq");
}
// crmv@81217e
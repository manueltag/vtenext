<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

if (!function_exists('addColumnToTable')) {
	function addColumnToTable($tablename, $columnname, $type, $extra = '') {
		global $adb;

		// check if already present
		$cols = $adb->getColumnNames($tablename);
		if (in_array($columnname, $cols)) {
			return;
		}

		$col = $columnname.' '.$type.' '.$extra;
		$adb->alterTable($tablename, $col, 'Add_Column');
	}
}

$schema_table =
'<schema version="0.3">
	<table name="'.$table_prefix.'_meta_logs">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="logid" type="I" size="19">
			<KEY/>
		</field>
		<field name="timestamp" type="T">
			<DEFAULT value="0000-00-00 00:00:00" />
		</field>
		<field name="operation" type="C" size="63">
			<NOTNULL/>
		</field>
		<field name="objectid" type="I" size="19" />
		<field name="data" type="C" size="255" />
		<index name="metalogs_time_idx">
			<col>timestamp</col>
		</index>
		<index name="metalogs_op_idx">
			<col>operation</col>
		</index>
	</table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_meta_logs')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$schema_table =
'<schema version="0.3">
	<table name="'.$table_prefix.'_relatedlists_changes">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="relation_id" type="I" size="19">
			<KEY/>
		</field>
		<field name="timestamp" type="T">
			<DEFAULT value="0000-00-00 00:00:00" />
		</field>
		<index name="rlistchange_time_idx">
			<col>timestamp</col>
		</index>
	</table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_relatedlists_changes')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

// add field for messages
$fields = array(
	'cleaned_body'	=> array('module'=>'Messages', 'block'=>'LBL_DESCRIPTION_INFORMATION', 'name'=>'cleaned_body', 'label'=>'Body',   		'table'=>"{$table_prefix}_messages",      'columntype'=>'XL', 'typeofdata'=>'V~O',    'uitype'=>19, ),
	'content_ids'	=> array('module'=>'Messages', 'block'=>'LBL_DESCRIPTION_INFORMATION', 'name'=>'content_ids', 'label'=>'ContentIds',   'table'=>"{$table_prefix}_messages",  'columntype'=>'XL', 'typeofdata'=>'V~O',    'uitype'=>1, 'readonly'=>100),
);
$fieldRet = Update::create_fields($fields);

// now populate these new fields (only first 500 messages per user)
$messFocus = CRMEntity::getInstance('Messages');
$users = array();
$res = $adb->query("select id from {$table_prefix}_users");
while ($row = $adb->fetchByAssoc($res, -1, false)) {
	$users[] = $row['id'];
}
foreach ($users as $userid) {
	$res = $adb->limitpQuery("
		select messagesid, xuid, description from {$table_prefix}_messages 
		inner join {$table_prefix}_crmentity on crmid = {$table_prefix}_messages.messagesid
		where deleted = 0 and smownerid = ? and (cleaned_body is null or cleaned_body = '')
		order by modifiedtime desc", 0, 500, array($userid));
	while ($row = $adb->fetchByAssoc($res, -1, false)) {
		$messFocus->id = $row['messagesid'];
		// save the cleaned body
		$attachments_info = $messFocus->getAttachmentsInfo();
		$message_data = array('other'=>$attachments_info);
		$magicHTML = $messFocus->magicHTML($row['description'], $row['xuid'], $message_data);
		$messFocus->saveCleanedBody($messFocus->id, $magicHTML['html'], $magicHTML['content_ids']);
	}
}


addColumnToTable($table_prefix.'_customview', 'setmobile', 'I(1)', 'DEFAULT 0');
addColumnToTable($table_prefix.'_groups', 'date_entered', 'T', "DEFAULT '0000-00-00 00:00:00'");
addColumnToTable($table_prefix.'_groups', 'date_modified', 'T');

// update values
$adb->pquery("update {$table_prefix}_customview set setmobile = 1 where setdefault = 1 OR viewname = ? OR (entitytype = ? AND viewname = ?)", array('All', 'Calendar', 'Events'));

// add index
$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL('crmentity_modtime_idx', "{$table_prefix}_crmentity", 'modifiedtime'));
$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL('groups_modified_idx', "{$table_prefix}_groups", 'date_modified'));

// translations
$trans = array(
	'CustomView' => array(
		'it_it' => array(
			'LBL_AVAIL_APP_MOBILE' => 'Disponibile in Mobile App',
		),
		'en_us' => array(
			'LBL_AVAIL_APP_MOBILE' => 'Available in Mobile App',
		),
		'de_de' => array(
			'LBL_AVAIL_APP_MOBILE' => 'Verfügbar in der Mobile App',
		),
	),
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_ADMIN_NEEDED' => 'Per eseguire questa operazione devi effettuare l\'accesso con un utente amministratore',
		),
		'en_us' => array(
			'LBL_ADMIN_NEEDED' => 'You need to perform this action with an administrator user',
		),
		'de_de' => array(
			'LBL_ADMIN_NEEDED' => 'Sie müssen diese Aktion mit einem Administrator durchführen',
		),
	),
);

foreach ($trans as $module=>$modlang) {
	foreach ($modlang as $lang=>$translist) {
		foreach ($translist as $label=>$translabel) {
			SDK::setLanguageEntry($module, $lang, $label, $translabel);
		}
	}
}
?>
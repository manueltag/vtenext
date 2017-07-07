<?php
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter'));
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';

global $adb;
$schema_tables = array(
'tbl_s_newsletter_failed'=>
	'<schema version="0.3">
	  <table name="tbl_s_newsletter_failed">
	  <opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="newsletterid" type="R" size="19">
		  <KEY/>
		</field>
		<field name="crmid" type="R" size="19">
		  <KEY/>
		</field>
		<field name="note" type="C" size="255"/>
		<index name="NewIndex1">
		  <col>newsletterid</col>
		</index>
		<index name="NewIndex2">
		  <col>crmid</col>
		</index>
		<index name="NewIndex3">
		  <col>note</col>
		</index>
	  </table>
	</schema>'
);
foreach($schema_tables as $table_name => $schema_table) {
	if(!Vtiger_Utils::CheckTable($table_name)) {
		$schema_obj = new adoSchema($adb->database);
		$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
	}
}

$campaignsModule = Vtiger_Module::getInstance('Campaigns');
//Failed Messages (es. record cancellati)
$res = $adb->query("SELECT * FROM vtiger_relatedlists WHERE name = 'get_statistics_failed_messages' AND tabid = $campaignsModule->id");
if ($res && $adb->num_rows($res)>0) {
	//do nothing
} else {
	$relation_id = $adb->getUniqueID('vtiger_relatedlists');
	$max_sequence = 0;
	$result = $adb->query("SELECT max(sequence) as maxsequence FROM vtiger_relatedlists WHERE tabid=$campaignsModule->id");
	if($adb->num_rows($result)) $max_sequence = $adb->query_result($result, 0, 'maxsequence');
	$sequence = $max_sequence+1;
	$adb->pquery("INSERT INTO vtiger_relatedlists(relation_id,tabid,related_tabid,name,sequence,label,presence) VALUES(?,?,?,?,?,?,?)",
				array($relation_id,$campaignsModule->id,0,'get_statistics_failed_messages',$sequence,'Failed Messages',0));
	
	SDK::setLanguageEntries('Campaigns', 'Failed Messages', array('it_it'=>'Email Fallite','en_us'=>'Failed Messages'));
	SDK::setLanguageEntries('Newsletter', 'FailedNotes', array('it_it'=>'Note','en_us'=>'Notes'));
}
?>
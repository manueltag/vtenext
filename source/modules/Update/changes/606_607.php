<?php
global $adb,$table_prefix;
if(!Vtiger_Utils::CheckTable($table_prefix.'_quickcreate')) {
	$schema_table = '<schema version="0.3">
					  <table name="'.$table_prefix.'_quickcreate">
					  <opt platform="mysql">ENGINE=InnoDB</opt>
					    <field name="tabid" type="I" size="19">
					      <KEY/>
					    </field>
					    <field name="img" type="C" size="255"/>
					  </table>
					</schema>';
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
	$img = array(
		'Accounts'=>'themes/images/qc_accounts.png',
		'Calendar'=>'themes/images/qc_calendar.png',
		'Events'=>'themes/images/qc_events.png',
		'Contacts'=>'themes/images/qc_contacts.png',
		'Documents'=>'themes/images/qc_documents.png',
		'Vendors'=>'themes/images/qc_vendors.png',
		'Leads'=>'themes/images/qc_leads.png',
		'Potentials'=>'themes/images/qc_potentials.png',
		'HelpDesk'=>'themes/images/qc_helpdesk.png',
	);
	$result = $adb->pquery('SELECT tabid, name FROM '.$table_prefix.'_tab WHERE name IN (?,?,?,?,?,?,?,?,?)',array_keys($img));
	while($row=$adb->fetchByAssoc($result)) {
		$adb->pquery('insert into '.$table_prefix.'_quickcreate (tabid,img) values (?,?)',array($row['tabid'],$img[$row['name']]));
	}
}
?>
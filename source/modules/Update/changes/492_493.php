<?php
$_SESSION['modules_to_update']['Visitreport'] = 'packages/vte/mandatory/Visitreport.zip';
$_SESSION['modules_to_update']['Projects'] = 'packages/vte/mandatory/Projects.zip';

global $adb;
$result = $adb->query('SELECT * FROM vtiger_ws_fieldtype WHERE uitype = 1015');
if (!$result || $adb->num_rows($result) == 0) {
	$id = $adb->getUniqueID("vtiger_ws_fieldtype");
	$adb->query("INSERT INTO vtiger_ws_fieldtype VALUES ($id,1015,'picklistmultilanguage') ");
}
$result = $adb->query('SELECT * FROM vtiger_ws_fieldtype WHERE uitype = 116');
if (!$result || $adb->num_rows($result) == 0) {
	$id = $adb->getUniqueID("vtiger_ws_fieldtype");
	$adb->query("INSERT INTO vtiger_ws_fieldtype VALUES ($id,116,'reference') ");
}

$module = Vtiger_Module::getInstance('Accounts');
$adb->pquery("delete from vtiger_links where linktype = 'DETAILVIEWBASIC' and tabid = ? and linklabel = ?",array($module->id,'LBL_ADD_VISITREPORT'));
$adb->pquery("delete from vtiger_links where linktype = 'DETAILVIEWBASIC' and tabid = ? and linklabel = ?",array($module->id,'LBL_ADD_NOTE'));
$module = Vtiger_Module::getInstance('Leads');
$adb->pquery("delete from vtiger_links where linktype = 'DETAILVIEWBASIC' and tabid = ? and linklabel = ?",array($module->id,'LBL_ADD_NOTE'));
$module = Vtiger_Module::getInstance('Contacts');
$adb->pquery("delete from vtiger_links where linktype = 'DETAILVIEWBASIC' and tabid = ? and linklabel = ?",array($module->id,'LBL_ADD_NOTE'));
$module = Vtiger_Module::getInstance('ProjectTask');
$adb->pquery("delete from vtiger_links where linktype = 'DETAILVIEWBASIC' and tabid = ? and linklabel = ?",array($module->id,'Add Note'));
$module = Vtiger_Module::getInstance('ProjectPlan');
$adb->pquery("delete from vtiger_links where linktype = 'DETAILVIEWBASIC' and tabid = ? and linklabel = ?",array($module->id,'Add Note'));
$adb->pquery("delete from vtiger_links where linktype = 'DETAILVIEWBASIC' and tabid = ? and linklabel = ?",array($module->id,'Add Project Task'));
$module = Vtiger_Module::getInstance('Visitreport');
$adb->pquery("delete from vtiger_links where linktype = 'DETAILVIEWBASIC' and tabid = ? and linklabel = ?",array($module->id,'LBL_ADD_EVENT'));
$adb->pquery("delete from vtiger_links where linktype = 'DETAILVIEWBASIC' and tabid = ? and linklabel = ?",array($module->id,'LBL_ADD_TASK'));
$module = Vtiger_Module::getInstance('Ddt');
$adb->pquery("update vtiger_links set linkicon = '' where linktype = 'DETAILVIEWBASIC' and tabid = ? and linklabel = ?",array($module->id,'Add Invoice'));
$module = Vtiger_Module::getInstance('SalesOrder');
$adb->pquery("update vtiger_links set linkicon = '' where linktype = 'DETAILVIEWBASIC' and tabid = ? and linklabel = ?",array($module->id,'Add Ddt'));

SDK::setLanguageEntries('APP_STRINGS', 'LBL_BACK_TO_TOP', array('it_it'=>'Torna su','en_us'=>'Back to top'));

$schema_tables = array(
'vte_turbolift_count'=>
	'<schema version="0.3">
	  <table name="vte_turbolift_count">
	    <opt platform="mysql">ENGINE=InnoDB</opt>
	    <field name="userid" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="relation_id" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="tabid" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="tb_count" type="I" size="19">
	        <DEFAULT value="0"/>
	    </field>
	  </table>
	</schema>'
);
foreach($schema_tables as $table_name => $schema_table) {
	if(!Vtiger_Utils::CheckTable($table_name)) {
		$schema_obj = new adoSchema($adb->database);
		$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
	}
}
?>
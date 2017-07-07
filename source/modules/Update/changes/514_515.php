<?php
global $adb;
$schema_tables = array(
'vte_hide_tab'=>
	'<schema version="0.3">
	  <table name="vte_hide_tab">
	  <opt platform="mysql">ENGINE=InnoDB</opt>
	    <field name="tabid" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="hide_module_manager" type="I" size="1">
	      <DEFAULT value="0"/>
	    </field>
	    <field name="hide_profile" type="I" size="1">
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
$moduleInstance = Vtiger_Module::getInstance('SDK');
$moduleInstance->hide(array('hide_module_manager'=>1,'hide_profile'=>1));
$moduleInstance = Vtiger_Module::getInstance('Morphsuit');
$moduleInstance->hide(array('hide_module_manager'=>1,'hide_profile'=>1));
$adb->pquery('update vtiger_profile2tab set permissions = 0 where tabid = ?',array($moduleInstance->id));

$_SESSION['modules_to_update']['Assets'] = 'packages/vte/mandatory/Assets.zip';
$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter','Targets'));
$_SESSION['modules_to_update']['PBXManager'] = 'packages/vte/mandatory/PBXManager.zip';
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan','ProjectMilestone','ProjectTask'));
$_SESSION['modules_to_update']['ServiceContracts'] = 'packages/vte/mandatory/ServiceContracts.zip';
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
$_SESSION['modules_to_update']['Sms'] = 'packages/vte/mandatory/Sms.zip';
$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';
$_SESSION['modules_to_update']['Visitreport'] = 'packages/vte/mandatory/Visitreport.zip';
?>
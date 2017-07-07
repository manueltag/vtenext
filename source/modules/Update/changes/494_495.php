<?php
global $adb;
$schema_tables = array(
'vtiger_users_search_tab'=>
	'<schema version="0.3">
	    <table name="vtiger_users_search_tab">
  			<opt platform="mysql">ENGINE=InnoDB</opt>
    		<field name="userid" type="I" size="19">
    			<KEY/>
    		</field>
    		<field name="tabid" type="I" size="19">
    			<KEY/>
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

global $enterprise_current_version,$enterprise_mode;
SDK::setLanguageEntries('APP_STRINGS', 'LBL_BROWSER_TITLE', array('it_it'=>"$enterprise_mode $enterprise_current_version",'en_us'=>"$enterprise_mode $enterprise_current_version"));
?>
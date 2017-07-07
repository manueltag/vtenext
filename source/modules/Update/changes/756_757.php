<?php
global $adb, $table_prefix;
$schema_table =
'<schema version="0.3">
	<table name="'.$table_prefix.'_email_directory">
	  <opt platform="mysql">ENGINE=InnoDB</opt>
	    <field name="userid" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="email" type="C" size="200">
	      <KEY/>
	    </field>
	    <field name="crmid" type="I" size="19"/>
	    <field name="module" type="C" size="200"/>
	    <index name="'.$table_prefix.'_email_dir_user_idx">
	      <col>userid</col>
	    </index>
	    <index name="'.$table_prefix.'_email_dir_email_idx">
	      <col>email</col>
	    </index>
	    <index name="'.$table_prefix.'_email_dir_crmid_idx">
	      <col>crmid</col>
	    </index>
	  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_email_directory')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}
?>
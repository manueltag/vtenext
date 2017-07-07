<?php
global $adb;

$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';

$adb->pquery('update vtiger_field set displaytype = 1 where fieldname = ? and tablename = ?',array('bill_country','vtiger_sobillads'));
$adb->pquery('update vtiger_field set displaytype = 1 where fieldname = ? and tablename = ?',array('ship_country','vtiger_soshipads'));

$schema_tables = array(
'vte_mailcache_folders'=>
	'<schema version="0.3">
	  <table name="vte_mailcache_folders">
	  <opt platform="mysql">ENGINE=InnoDB</opt>
	    <field name="userid" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="folder" type="C" size="255">
	      <KEY/>
	    </field>
	    <field name="info" type="X"/>
	    <field name="messages" type="I" size="19"/>
	    <field name="unseen" type="I" size="19"/>
	    <field name="recent" type="I" size="19"/>
	    <field name="sequence" type="I" size="19"/>
	  </table>
	</schema>',
'vte_mailcache_list'=>
	'<schema version="0.3">
	  <table name="vte_mailcache_list">
	  <opt platform="mysql">ENGINE=InnoDB</opt>
	    <field name="userid" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="uid" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="folder" type="C" size="255"/>
	    <field name="small_header" type="X"/>
	    <field name="small_header_res" type="X"/>
	    <field name="flags" type="C" size="255"/>
	  </table>
	</schema>',
'vte_mailcache_messages'=>
	'<schema version="0.3">
	  <table name="vte_mailcache_messages">
	  <opt platform="mysql">ENGINE=InnoDB</opt>
	    <field name="userid" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="uid" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="folder" type="C" size="255"/>
	    <field name="flgs_bodystr" type="X"/>
	    <field name="body_header" type="XL"/>
	    <field name="body" type="XL"/>
	  </table>
	</schema>',
);
foreach($schema_tables as $table_name => $schema_table) {
	if(!Vtiger_Utils::CheckTable($table_name)) {
		$schema_obj = new adoSchema($adb->database);
		$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
	}
}
?>
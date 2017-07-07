<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';

global $adb;
if ($adb->isMssql()) {
	$sqlarray = $adb->datadict->DropTableSQL('vte_mailcache_folders');
	$adb->datadict->ExecuteSQLArray($sqlarray);
	$sqlarray = $adb->datadict->DropTableSQL('vte_mailcache_list');
	$adb->datadict->ExecuteSQLArray($sqlarray);
	$sqlarray = $adb->datadict->DropTableSQL('vte_mailcache_messages');
	$adb->datadict->ExecuteSQLArray($sqlarray);
	$schema_tables = array(
		'vte_mailcache_folders'=>
			'<schema version="0.3">
			  <table name="vte_mailcache_folders">
			  	<opt platform="mysql">ENGINE=InnoDB</opt>
			  	<field name="userid" type="I" size="19">
			  		<KEY />
			  	</field>
			  	<field name="folder" type="C" size="255">
			  		<KEY />
			  	</field>
			  	<field name="info" type="X" />
			  	<field name="messages" type="I" size="19" />
			  	<field name="unseen" type="I" size="19" />
			  	<field name="recent" type="I" size="19" />
			  	<field name="sequence" type="I" size="19" />
			  	<index name="NewIndex1">
			      <col>userid</col>
			    </index>
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
			    <field name="folder" type="C" size="255">
			    	<KEY/>
			    </field>
			    <field name="small_header" type="X"/>
			    <field name="small_header_res" type="X"/>
			    <field name="flags" type="C" size="255"/>
			    <field name="email_subject_sort" type="C" size="255"/>
			    <field name="email_date" type="I" size="19"/>
			    <field name="email_from" type="X"/>
			    <field name="email_from_sort" type="C" size="255"/>
			    <field name="email_to" type="C" size="255"/>
			    <field name="email_to_sort" type="C" size="255"/>
			    <field name="email_cc" type="C" size="255"/>
			    <index name="NewIndex1">
			      <col>userid</col>
			    </index>
			    <index name="NewIndex2">
			      <col>uid</col>
			    </index>
			    <index name="NewIndex3">
			      <col>folder</col>
			    </index>
			  </table>
			</schema>',
		'vte_mailcache_messages'=>
			'<schema version="0.3">
			  <table name="vte_mailcache_messages">
			  	<opt platform="mysql">ENGINE=InnoDB</opt>
			  	<field name="userid" type="I" size="19">
			  		<KEY />
			  	</field>
			  	<field name="uid" type="I" size="19">
			  		<KEY />
			  	</field>
			  	<field name="folder" type="C" size="255">
			  		<KEY />
			  	</field>
			  	<field name="flgs_bodystr" type="X" />
			  	<field name="body_header" type="XL" />
			  	<field name="body" type="XL" />
			  	<index name="NewIndex1">
			      <col>userid</col>
			    </index>
			    <index name="NewIndex2">
			      <col>uid</col>
			    </index>
			    <index name="NewIndex3">
			      <col>folder</col>
			    </index>
			  </table>
			</schema>',
	);
	foreach($schema_tables as $table_name => $schema_table) {
		if(!Vtiger_Utils::CheckTable($table_name)) {
			$schema_obj = new adoSchema($adb->database);
			$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
		}
	}
}
?>
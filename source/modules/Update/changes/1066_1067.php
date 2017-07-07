<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
$_SESSION['modules_to_update']['ServiceContracts'] = 'packages/vte/mandatory/ServiceContracts.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter'));

global $adb, $table_prefix;

// no temporary tables patch - create global table permissions

$schema_table =
'<schema version="0.3">
  <table name="'.$table_prefix.'_tmp_users">
	<opt platform="mysql">ENGINE=InnoDB</opt>
    <field name="userid" type="I" size="19">
      <KEY/>
    </field>
    <field name="subuserid" type="I" size="19">
      <KEY/>
    </field>    
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_tmp_users')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$schema_table =
'<schema version="0.3">
  <table name="'.$table_prefix.'_tmp_users_mod">
	<opt platform="mysql">ENGINE=InnoDB</opt>
    <field name="userid" type="I" size="19">
      <KEY/>
    </field>
    <field name="tabid" type="I" size="11">
      <KEY/>
    </field>    
    <field name="subuserid" type="I" size="19">
      <KEY/>
    </field>    
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_tmp_users_mod')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$schema_table =
'<schema version="0.3">
  <table name="'.$table_prefix.'_tmp_users_cal">
	<opt platform="mysql">ENGINE=InnoDB</opt>
    <field name="userid" type="I" size="19">
      <KEY/>
    </field>
    <field name="tabid" type="I" size="11">
      <KEY/>
    </field>    
    <field name="subuserid" type="I" size="19">
      <KEY/>
    </field>
    <field name="shared" type="I" size="12">
	  <DEFAULT value="0" />
      <KEY/>
    </field>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_tmp_users_cal')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$schema_table =
'<schema version="0.3">
  <table name="'.$table_prefix.'_tmp_users_mod_rel">
	<opt platform="mysql">ENGINE=InnoDB</opt>
    <field name="userid" type="I" size="11">
      <KEY/>
    </field>
    <field name="tabid" type="I" size="11">
      <KEY/>
    </field>
    <field name="reltabid" type="I" size="11">
      <KEY/>
    </field>    
    <field name="parentid" type="I" size="19">
      <KEY/>
    </field>
    <field name="crmid" type="I" size="19">
      <KEY/>
    </field>
    <field name="relcrmid" type="I" size="19">
      <KEY/>
    </field>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_tmp_users_mod_rel')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$reltable_tmp = $table_prefix.'_tmp_rst';
$schema_table =
'<schema version="0.3">
  <table name="'.$reltable_tmp.'">
	<opt platform="mysql">ENGINE=InnoDB</opt>
    <field name="userid" type="I" size="19">
      <KEY/>
    </field>
    <field name="viewid" type="I" size="19">
	  <DEFAULT value="0"/>
      <KEY/>
    </field>    
    <field name="id" type="I" size="19">
      <KEY/>
    </field>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($reltable_tmp)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$tableName = $table_prefix.'_customview_rpt';
$schema_table =
	'<schema version="0.3">
		<table name="'.$tableName.'">
			<opt platform="mysql">ENGINE=InnoDB</opt>
			<field name="userid" type="I" size="19">
				<KEY/>
			</field>
			<field name="reportid" type="I" size="19">
				<KEY/>
			</field>
			<field name="prefix" type="I" size="19">
				<DEFAULT value="0"/>
				<KEY/>
			</field>
			<field name="id" type="I" size="19">
				<KEY/>
			</field>
		</table>
	</schema>';
if(!Vtiger_Utils::CheckTable($tableName)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$tmptable = $table_prefix.'_rpt_innerprice';
$schema_table =
'<schema version="0.3">
	<table name="'.$tmptable.'">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="reportid" type="I" size="19">
			<KEY/>
		</field>
		<field name="tabid" type="I" size="19">
			<KEY/>
		</field>
		<field name="crmid" type="I" size="19">
			<KEY/>
		</field>
		<field name="ACTUAL_UNIT_PRICE" type="N" size="25.2" />
	</table>
</schema>';
if(!Vtiger_Utils::CheckTable($tmptable)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

// for this script to succeed, the global temp tables flag must be disabled
global $PERFORMANCE_CONFIG;
$PERFORMANCE_CONFIG['USE_TEMP_TABLES'] = false;

$use_old_temporary_tables = false; // old style flag

// now populate the tables
$tutables = TmpUserTables::getInstance();
$tutables->cleanTmp();
$tutables->generateTmp();

$tumtables = TmpUserModTables::getInstance();
$tumtables->cleanTmp();
$tumtables->generateTmp();

$tcaltables = TmpUserCalTables::getInstance();
$tcaltables->cleanTmp();
$tcaltables->generateTmp();

$tmodreltables = TmpUserModRelTables::getInstance();
$tmodreltables->cleanTmp();

// now for messages in shared folder, create and mantain a table
$schema_table =
'<schema version="0.3">
  <table name="'.$table_prefix.'_modcomments_msgrel">
	<opt platform="mysql">ENGINE=InnoDB</opt>
    <field name="userid" type="I" size="19">
      <KEY/>
    </field>
    <field name="messagesid" type="I" size="19">
      <KEY/>
    </field>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_modcomments_msgrel')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

// and populate it!!

// get the users
$usersid = array();
$r = $adb->query("SELECT id FROM {$table_prefix}_users");
if ($r && $adb->num_rows($r) > 0) {
	while ($row = $adb->FetchByAssoc($r, -1, false)) {
		$usersid[] = intval($row['id']);
	}
}

if (count($usersid) > 0) {
	$msgInst = CRMEntity::getInstance('Messages');
	$oldCurrentUser = $current_user;
	foreach ($usersid as $userid) {
		/*$current_user = CRMEntity::getInstance('Users');
		$current_user->retrieveCurrentUserInfoFromFile($userid);
		$current_user->id = $userid;
		*/
				
		//$q = $msgInst->getRelatedModComments(true);
		
		$msgInst->regenCommentsMsgRelTable($userid);
	}
	$current_user = $oldCurrentUser;
}

// table for notifications
$tmpTable = $table_prefix.'_modnot_tmp_list';
$schema_table =
'<schema version="0.3">
  <table name="'.$tmpTable.'">
	<opt platform="mysql">ENGINE=InnoDB</opt>
    <field name="userid" type="I" size="19">
      <KEY/>
    </field>
    <field name="parentid" type="I" size="19">
	  <DEFAULT value="0"/>
      <KEY/>
    </field>    
    <field name="id" type="I" size="19">
      <KEY/>
    </field>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($tmpTable)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

// table for suggested filter in messages
$tableName = $table_prefix.'_messages_tmp_prel';
$schema_table ='<schema version="0.3">
  <table name="'.$tableName.'">
	<opt platform="mysql">ENGINE=InnoDB</opt>
	<field name="sequence" type="I" size="19">
	  <KEY/>
	  <NOTNULL/>
	  <AUTOINCREMENT/>
    </field>
    <field name="userid" type="I" size="19">
      <NOTNULL/>
    </field>
    <field name="tabid" type="I" size="19">
      <NOTNULL/>
    </field>
    <field name="id" type="I" size="19">
	  <NOTNULL/>
    </field>
    <index name="vte_messages_poprel_idx">
      <UNIQUE/>
      <col>userid</col>
      <col>tabid</col>
      <col>id</col>
    </index>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($tableName)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$tableName = $table_prefix.'_messages_tmp_rlist';
$schema_table =
	'<schema version="0.3">
		<table name="'.$tableName.'">
			<opt platform="mysql">ENGINE=InnoDB</opt>
			<field name="userid" type="I" size="19">
				<KEY/>
			</field>
			<field name="parentid" type="I" size="19">
				<KEY/>
			</field>
			<field name="id" type="I" size="19">
				<KEY/>
			</field>
		</table>
	</schema>';
if(!Vtiger_Utils::CheckTable($tableName)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}


// now delete the "temporary" tables
$tables = $_SESSION['cache']['table_exist'];
if (is_array($tables)) {
	foreach($tables as $table => $id) {
		if (strpos($table, 'tmp_rpt_') === 0) {
			if ($adb->isMysql()) {
				$adb->query("drop table if exists $table");
			} else {
				$adb->query("drop table $table");
			}
		}
	}
}

// translations

$trans = array(
	'Users' => array(
		'it_it' => array(
			'LBL_RECOVER_MAIL_SENT' => 'La mail con le istruzioni di reset password è stata inviata.',
		),
		'en_us' => array(
			'LBL_RECOVER_MAIL_SENT'=>'The mail with the instructions on how to reset the password has been sent',
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


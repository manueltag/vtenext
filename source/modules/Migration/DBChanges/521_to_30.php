<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

require_once 'include/Webservices/Utils.php';
require_once 'modules/Users/Users.php';
require_once 'include/utils/utils.php';
global $table_prefix;
//5.2.1 to 3.0 VTE database changes

//we have to use the current object (stored in PatchApply.php) to execute the queries
require_once ('vtlib/Vtiger/Utils.php');

$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

$migrationlog->debug("\n\nDB Changes from 5.2.1 to 3.0 VTE -------- Starts \n\n");

$schema = new adoSchema( $adb->database );
$schemaFile = 'modules/Migration/vte_utils/schema_changes_52_to_30.xml';
//ora prendo il file e lo applico al db di arrivo
$res = $schema->ParseSchema( $schemaFile );
$result = $schema->ExecuteSchema(null,true);
if ($result == 2){
	echo '
		<tr width="100%">
			<td width="10%"><font color="green"> '.$installationStrings['LBL_SUCCESS'].' </font></td>
			<td width="80%">schema migration from vtiger 5.2.1 to VTE 3.0</td>
		</tr>';
	$migrationlog->debug("Query Success ==> schema migration from vtiger 5.2.1 to VTE 3.0");
} else {
	echo '
		<tr width="100%">
				<td width="5%"><font color="red"> '.$installationStrings['LBL_FAILURE'].' </font></td>
			<td width="70%">SCHEMA MIGRATION ERROR: '.$schemaFile.'</td>
		</tr>';
	$migrationlog->debug("Query Failed ==> schema migration from vtiger 5.2.1 to VTE 3.0 \n Error is ==> [".$adb->database->ErrorNo()."]".$adb->database->ErrorMsg());
}
//aggiornamento VTE alla versione 272
ExecuteQuery("ALTER TABLE ".$table_prefix."_users CHANGE id id INT(11) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_cvstdfilter  ADD COLUMN only_month_and_day INT(1) DEFAULT '0' NULL AFTER enddate");
ExecuteQuery("ALTER TABLE ".$table_prefix."_import_maps  CHANGE id id INT(19) NOT NULL");
ExecuteQuery('ALTER TABLE '.$table_prefix.'_users_last_import CHANGE id id INT(36) NOT NULL');
$result = ExecuteQuery('SELECT MAX(relation_id) AS relation_id FROM '.$table_prefix.'_relatedlists');
if ($result) $relation_id = $adb->query_result($result,0,'relation_id');
if ($relation_id) ExecuteQuery('UPDATE '.$table_prefix.'_relatedlists_seq SET id= '.$relation_id);
ExecuteQuery("UPDATE ".$table_prefix."_field SET typeofdata = 'C~O' WHERE uitype = 56");
ExecuteQuery("ALTER TABLE ".$table_prefix."_users CHANGE date_entered date_entered TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL , CHANGE date_modified date_modified TIMESTAMP DEFAULT '0000-00-00 00:00:00' NULL");
ExecuteQuery("UPDATE ".$table_prefix."_activity_view SET sortorderid='0' WHERE activity_viewid='2'");
ExecuteQuery("UPDATE ".$table_prefix."_activity_view SET sortorderid='1' WHERE activity_viewid='1'");
ExecuteQuery("CREATE TABLE IF NOT EXISTS ".$table_prefix."_campaignrelstatus(
				campaignrelstatusid INT(19) NULL  , 
				campaignrelstatus VARCHAR(256) COLLATE utf8_general_ci NULL  , 
				sortorderid INT(19) NULL  , 
				presence INT(19) NULL  
			) ENGINE=INNODB DEFAULT CHARSET='utf8'");
ExecuteQuery("CREATE TABLE IF NOT EXISTS ".$table_prefix."_campaignrelstatus_seq(
				id INT(11) NOT NULL  
			) ENGINE=MYISAM DEFAULT CHARSET='utf8'");
ExecuteQuery("CREATE TABLE IF NOT EXISTS ".$table_prefix."_licencekeystatus(
				licencekeystatusid INT(19) NOT NULL  AUTO_INCREMENT , 
				licencekeystatus VARCHAR(200) COLLATE utf8_general_ci NOT NULL  , 
				sortorderid INT(19) NOT NULL  DEFAULT '0' , 
				presence INT(1) NOT NULL  DEFAULT '1' , 
				PRIMARY KEY (licencekeystatusid) 
			) ENGINE=INNODB DEFAULT CHARSET='utf8'");
ExecuteQuery("CREATE TABLE IF NOT EXISTS ".$table_prefix."_notescf(
				notesid INT(19) NOT NULL  DEFAULT '0' , 
				PRIMARY KEY (notesid) 
			) ENGINE=INNODB DEFAULT CHARSET='utf8'");
ExecuteQuery("CREATE TABLE IF NOT EXISTS ".$table_prefix."_opportunitystage(
				potstageid INT(19) NOT NULL  AUTO_INCREMENT , 
				stage VARCHAR(200) COLLATE utf8_general_ci NOT NULL  , 
				sortorderid INT(19) NOT NULL  DEFAULT '0' , 
				presence INT(1) NOT NULL  DEFAULT '1' , 
				probability DECIMAL(3,2) NULL  DEFAULT '0.00' , 
				PRIMARY KEY (potstageid) , 
				UNIQUE KEY opportunitystage_stage_idx(stage) 
			) ENGINE=INNODB DEFAULT CHARSET='utf8'");
ExecuteQuery("CREATE TABLE IF NOT EXISTS ".$table_prefix."_relcriteria_grouping(
				groupid INT(11) NOT NULL  , 
				queryid INT(19) NOT NULL  , 
				group_condition VARCHAR(256) COLLATE utf8_general_ci NULL  , 
				condition_expression TEXT COLLATE utf8_general_ci NULL  , 
				PRIMARY KEY (groupid,queryid) 
			) ENGINE=INNODB DEFAULT CHARSET='utf8'");
ExecuteQuery("DROP TABLE IF EXISTS ".$table_prefix."_workflowrunoncerel");
ExecuteQuery("ALTER TABLE ".$table_prefix."_accounttype     CHANGE accounttypeid accounttypeid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_activity_view     CHANGE activity_viewid activity_viewid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_activitytype     CHANGE activitytypeid activitytypeid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_campaignstatus     CHANGE campaignstatusid campaignstatusid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_campaigntype     CHANGE campaigntypeid campaigntypeid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_carrier     CHANGE carrierid carrierid INT(19) NOT NULL");
if ($adb->table_exist($table_prefix.'_contract_priority') > 0) {
	ExecuteQuery("ALTER TABLE ".$table_prefix."_contract_priority     CHANGE contract_priorityid contract_priorityid INT(11) NOT NULL");
}
if ($adb->table_exist($table_prefix.'_contract_status') > 0) {
	ExecuteQuery("ALTER TABLE ".$table_prefix."_contract_status     CHANGE contract_statusid contract_statusid INT(11) NOT NULL");
}
if ($adb->table_exist($table_prefix.'_contract_type') > 0) {
	ExecuteQuery("ALTER TABLE ".$table_prefix."_contract_type     CHANGE contract_typeid contract_typeid INT(11) NOT NULL");
}
ExecuteQuery("ALTER TABLE ".$table_prefix."_convertleadmapping     CHANGE cfmid cfmid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_currencies     CHANGE currencyid currencyid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_date_format     CHANGE date_formatid date_formatid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_def_org_share     CHANGE ruleid ruleid INT(11) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_duration_minutes     CHANGE minutesid minutesid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_emailtemplates     CHANGE templateid templateid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_eventhandlers     CHANGE eventhandler_id eventhandler_id INT(11) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_eventhandler_module     CHANGE eventhandler_module_id eventhandler_module_id INT(11) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_eventstatus     CHANGE eventstatusid eventstatusid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_expectedresponse     CHANGE expectedresponseid expectedresponseid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_failtype     CHANGE failtypeid failtypeid INT(11) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_faqcategories     CHANGE faqcategories_id faqcategories_id INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_faqstatus     CHANGE faqstatus_id faqstatus_id INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_field     CHANGE fieldid fieldid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_glacct     CHANGE glacctid glacctid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_industry     CHANGE industryid industryid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_inventorynotification     CHANGE notificationid notificationid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_invoicestatus     CHANGE invoicestatusid invoicestatusid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_language     CHANGE id id INT(11) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_lead_view     CHANGE lead_viewid lead_viewid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_leadsource     CHANGE leadsourceid leadsourceid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_leadstatus     CHANGE leadstatusid leadstatusid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_manufacturer     CHANGE manufacturerid manufacturerid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_notificationscheduler     CHANGE schedulednotificationid schedulednotificationid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_opportunity_type     CHANGE opptypeid opptypeid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_payment_duration     CHANGE payment_duration_id payment_duration_id INT(11) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_postatus     CHANGE postatusid postatusid INT(19) NOT NULL");
ExecuteQuery("ALTER TABLE ".$table_prefix."_postatushistory     CHANGE historyid historyid INT(19) NOT NULL");
ExecuteQuery("RENAME TABLE com_".$table_prefix."_workflow_activatedonce TO com_".$table_prefix."_wf_activatedonce");
if ($adb->table_exist("com_".$table_prefix."_wft_entitymeth") == 0) {
	ExecuteQuery("RENAME TABLE com_".$table_prefix."_workflowtasks_entitymethod TO com_".$table_prefix."_wft_entitymeth");
}
if ($adb->table_exist("com_".$table_prefix."_wft_entitymeth_seq") == 0) {
	ExecuteQuery("RENAME TABLE com_".$table_prefix."_workflowtasks_entitymethod_seq TO com_".$table_prefix."_wft_entitymeth_seq");
}
ExecuteQuery("RENAME TABLE ".$table_prefix."_datashare_module_rel TO ".$table_prefix."_datashare_mod_rel");
ExecuteQuery("RENAME TABLE ".$table_prefix."_datashare_relatedmodule_permission TO ".$table_prefix."_datashare_relmod_perm");
ExecuteQuery("RENAME TABLE ".$table_prefix."_datashare_relatedmodules TO ".$table_prefix."_datashare_relmodules");
ExecuteQuery("RENAME TABLE ".$table_prefix."_datashare_relatedmodules_seq TO ".$table_prefix."_datashare_relmodules_seq");
ExecuteQuery("RENAME TABLE ".$table_prefix."_inventorynotification TO ".$table_prefix."_inventorynotify");
ExecuteQuery("RENAME TABLE ".$table_prefix."_inventorynotification_seq TO ".$table_prefix."_inventorynotify_seq");
ExecuteQuery("RENAME TABLE ".$table_prefix."_notificationscheduler TO ".$table_prefix."_notifyscheduler");
ExecuteQuery("RENAME TABLE ".$table_prefix."_notificationscheduler_seq TO ".$table_prefix."_notifyscheduler_seq");
ExecuteQuery("RENAME TABLE ".$table_prefix."_org_share_action_mapping TO ".$table_prefix."_org_share_act_mapping");
ExecuteQuery("RENAME TABLE ".$table_prefix."_profile2globalpermissions TO ".$table_prefix."_profile2globalperm");
ExecuteQuery("RENAME TABLE ".$table_prefix."_profile2standardpermissions TO ".$table_prefix."_profile2standardperm");
ExecuteQuery("RENAME TABLE ".$table_prefix."_tmp_read_group_rel_sharing_per TO ".$table_prefix."_tmp_read_g_rel_per");
ExecuteQuery("RENAME TABLE ".$table_prefix."_tmp_read_group_sharing_per TO ".$table_prefix."_tmp_read_g_per");
ExecuteQuery("RENAME TABLE ".$table_prefix."_tmp_read_user_rel_sharing_per TO ".$table_prefix."_tmp_read_u_rel_per");
ExecuteQuery("RENAME TABLE ".$table_prefix."_tmp_read_user_sharing_per TO ".$table_prefix."_tmp_read_u_per");
ExecuteQuery("RENAME TABLE ".$table_prefix."_tmp_write_group_rel_sharing_per TO ".$table_prefix."_tmp_write_g_rel_per");
ExecuteQuery("RENAME TABLE ".$table_prefix."_tmp_write_group_sharing_per TO ".$table_prefix."_tmp_write_g_per");
ExecuteQuery("RENAME TABLE ".$table_prefix."_tmp_write_user_rel_sharing_per TO ".$table_prefix."_tmp_write_u_rel_per");
ExecuteQuery("RENAME TABLE ".$table_prefix."_tmp_write_user_sharing_per TO ".$table_prefix."_tmp_write_u_per");
ExecuteQuery("RENAME TABLE ".$table_prefix."_datashare_relmodules TO ".$table_prefix."_datashare_relmod");
ExecuteQuery("RENAME TABLE ".$table_prefix."_datashare_relmodules_seq TO ".$table_prefix."_datashare_relmod_seq");
ExecuteQuery("RENAME TABLE ".$table_prefix."_activity_reminder_popup TO ".$table_prefix."_act_reminder_popup");
ExecuteQuery("update ".$table_prefix."_field set uitype = 1 where columnname in ('hour_format','end_hour','start_hour')");
ExecuteQuery("UPDATE ".$table_prefix."_relatedlists SET actions='add' WHERE tabid=2 AND related_tabid = 20");
ExecuteQuery("UPDATE ".$table_prefix."_relatedlists SET actions='' WHERE tabid=6 AND related_tabid = 9 and name = 'get_history'");
ExecuteQuery("UPDATE ".$table_prefix."_relatedlists SET related_tabid = 22 WHERE tabid=20 AND related_tabid = 23");
ExecuteQuery("delete from ".$table_prefix."_relatedlists where tabid = 8 and related_tabid = 10");
ExecuteQuery("UPDATE ".$table_prefix."_field SET typeofdata='N~O' WHERE fieldname='hours' and tabid = 13");
ExecuteQuery("UPDATE ".$table_prefix."_field SET typeofdata='N~O' WHERE fieldname='days' and tabid = 13");
vte_change_field($table_prefix.'_troubletickets','hours','N','5.2');
vte_change_field($table_prefix.'_troubletickets','days','N','5.2');

$tabid_camp = getTabid("Campaigns");
if ($tabid_camp == ''){
	$module = 'Campaigns';
	$sql = "select tabid from ".$table_prefix."_tab where name=?";
	$result = ExecuteQuery($sql, array($module));
	$tabid_camp=  $adb->query_result($result,0,"tabid");
}
ExecuteQuery("insert into ".$table_prefix."_relatedlists values(".$adb->getUniqueID($table_prefix.'_relatedlists').",".$tabid_camp.",".getTabid("Calendar").",'get_history',5,'Activity History',0,'add')");
$idxflds = 'recordid,status,date_start,time_start';
$sqlarray = $adb->datadict->CreateIndexSQL('popup_index', $table_prefix.'_act_reminder_popup', $idxflds);
$adb->datadict->ExecuteSQLArray($sqlarray);
$fields = Array(
crmv_bankdetails=>Array(uitype=>1,label=>'Bank Details',typeofdata=>'V~O'),
crmv_vat_registration_number=>Array(uitype=>1,label=>'VAT Registration Number',typeofdata=>'PIVA~O'),
crmv_social_security_number=>Array(uitype=>1,label=>'Social Security number',typeofdata=>'CF~O'),
external_code=>Array(uitype=>1,label=>'External Code',typeofdata=>'V~O'),
);
//se parto dalla 5.03/5.04 bisogna aggiungere questi campi anche nella vtiger_account!
foreach ($fields as $field=>$arr){
	$presence = $adb->query_result(ExecuteQuery("select count(fieldid) as presence from ".$table_prefix."_field where columnname = ? and tabid = ?",Array($field,6)),0,'presence');
	$seq = $adb->query_result(ExecuteQuery("select max(sequence) as seq from ".$table_prefix."_field where tabid = ? and block = ?",Array(6,9)),0,'seq');
	$seq++;
	if ($presence == 0){
		$params = Array(
			tabid=>6,
			fieldid=>$adb->getUniqueID($table_prefix."_field"),
			columnname=>$field,
			tablename=>$table_prefix.'_account',
			generatedtype=>1,
			uitype=>$arr[uitype],
			fieldname=>$field,
			fieldlabel=>$arr[label],
			readonly=>1,
			presence=>2,
			selected=>0,
			maximumlength=>100,
			sequence=>$seq,
			block=>9,
			displaytype=>1,
			typeofdata=>$arr[typeofdata],
			quickcreate=>1,
			quickcreatesequence=>NULL,
			info_type=>'BAS',
			masseditable=>0,
			helpinfo=>'',
		);
		ExecuteQuery("insert into ".$table_prefix."_field (".implode(",",array_keys($params)).") values (".generateQuestionMarks($params).")",$params);
		ExecuteQuery("ALTER TABLE ".$table_prefix."_account ADD COLUMN $field VARCHAR(255) NULL ");
	}
}

ExecuteQuery("ALTER TABLE ".$table_prefix."_parenttab     ADD COLUMN hidden INT(1) DEFAULT '0' NOT NULL AFTER visible");
ExecuteQuery("INSERT INTO ".$table_prefix."_relatedlists VALUES (".$adb->getUniqueID($table_prefix."_relatedlists").",8,6,'get_documents_dependents_list',1,'Accounts',0,'select')");
ExecuteQuery("INSERT INTO ".$table_prefix."_relatedlists VALUES (".$adb->getUniqueID($table_prefix."_relatedlists").",8,7,'get_documents_dependents_list',2,'Leads',0,'select')");
ExecuteQuery("INSERT INTO ".$table_prefix."_relatedlists VALUES (".$adb->getUniqueID($table_prefix."_relatedlists").",8,4,'get_documents_dependents_list',3,'Contacts',0,'select')");
ExecuteQuery("INSERT INTO ".$table_prefix."_relatedlists VALUES (".$adb->getUniqueID($table_prefix."_relatedlists").",8,2,'get_documents_dependents_list',4,'Potentials',0,'select')");
ExecuteQuery("INSERT INTO ".$table_prefix."_relatedlists VALUES (".$adb->getUniqueID($table_prefix."_relatedlists").",8,14,'get_documents_dependents_list',5,'Products',0,'select')");
ExecuteQuery("INSERT INTO ".$table_prefix."_relatedlists VALUES (".$adb->getUniqueID($table_prefix."_relatedlists").",8,10,'get_documents_dependents_list',6,'Emails',0,'select')");
ExecuteQuery("INSERT INTO ".$table_prefix."_relatedlists VALUES (".$adb->getUniqueID($table_prefix."_relatedlists").",8,13,'get_documents_dependents_list',7,'HelpDesk',0,'select')");
ExecuteQuery("INSERT INTO ".$table_prefix."_relatedlists VALUES (".$adb->getUniqueID($table_prefix."_relatedlists").",8,20,'get_documents_dependents_list',8,'Quotes',0,'select')");
ExecuteQuery("INSERT INTO ".$table_prefix."_relatedlists VALUES (".$adb->getUniqueID($table_prefix."_relatedlists").",8,21,'get_documents_dependents_list',9,'PurchaseOrder',0,'select')");
ExecuteQuery("INSERT INTO ".$table_prefix."_relatedlists VALUES (".$adb->getUniqueID($table_prefix."_relatedlists").",8,22,'get_documents_dependents_list',10,'SalesOrder',0,'select')");
ExecuteQuery("INSERT INTO ".$table_prefix."_relatedlists VALUES (".$adb->getUniqueID($table_prefix."_relatedlists").",8,23,'get_documents_dependents_list',11,'Invoice',0,'select')");
ExecuteQuery("INSERT INTO ".$table_prefix."_relatedlists VALUES (".$adb->getUniqueID($table_prefix."_relatedlists").",8,15,'get_documents_dependents_list',13,'Faq',0,'select')");

$arr=$adb->getColumnNames($table_prefix."_customview");
if(in_array("crmv_user_id", $arr)) {
	ExecuteQuery("UPDATE ".$table_prefix."_customview SET userid = crmv_user_id, STATUS = 1 WHERE crmv_user_id IS NOT NULL AND crmv_user_id <> 0");
}
$id = $adb->getUniqueID($table_prefix.'_language');
if ($id){
	ExecuteQuery("update ".$table_prefix."_language set isdefault = 0");
	ExecuteQuery("insert into ".$table_prefix."_language (id,name,prefix,label,lastupdated,sequence,isdefault,active) values ($id,'Italian','it_it','Italiano',NOW(),null,1,1)");
}
//add update module
$result = ExecuteQuery("SELECT MAX(tabid) AS max_seq FROM ".$table_prefix."_tab");
$tabid = $adb->query_result($result, 0, 'max_seq');
$tabid++;
$result = ExecuteQuery("SELECT MAX(tabsequence) AS max_tabseq FROM ".$table_prefix."_tab");
$sequence = $adb->query_result($result, 0, 'max_tabseq');
$sequence++;
$params = Array(
tabid=>$tabid,
name=>'Update',
presence=>1,
tabsequence=>$sequence,
tablabel=>'Update',
modifiedby=>NULL,
modifiedtime=>NULL,
customized=>0,
ownedby=>1,
version=>'1.0',
isentitytype=>0
);
ExecuteQuery("INSERT INTO ".$table_prefix."_tab (".implode(",",array_keys($params)).") VALUES (".generateQuestionMarks($params).")",$params);

//advanced rules
$fieldid = $adb->getUniqueID($table_prefix.'_settings_field');
$blockid = getSettingsBlockId('LBL_USER_MANAGEMENT');
$seq_res = $adb->pquery("SELECT max(sequence) AS max_seq FROM ".$table_prefix."_settings_field WHERE blockid = ?", array($blockid));
if ($adb->num_rows($seq_res) > 0) {
	$cur_seq = $adb->query_result($seq_res, 0, 'max_seq');
	if ($cur_seq != null)	$seq = $cur_seq + 1;
}
ExecuteQuery('INSERT INTO '.$table_prefix.'_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence) 
	VALUES (?,?,?,?,?,?,?)', array($fieldid, $blockid, 'LBL_ADV_RULE', 'ico-adv_rule.gif', 'LBL_ADV_RULE_DESCRIPTION', 'index.php?module=Settings&action=AdvRuleDetailView&parenttab=Settings', $seq));
//colored listview
$fieldid = $adb->getUniqueID($table_prefix.'_settings_field');
$blockid = getSettingsBlockId('LBL_STUDIO');
$seq_res = $adb->pquery("SELECT max(sequence) AS max_seq FROM ".$table_prefix."_settings_field WHERE blockid = ?", array($blockid));
if ($adb->num_rows($seq_res) > 0) {
	$cur_seq = $adb->query_result($seq_res, 0, 'max_seq');
	if ($cur_seq != null)	$seq = $cur_seq + 1;
}
ExecuteQuery('INSERT INTO '.$table_prefix.'_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence) 
	VALUES (?,?,?,?,?,?,?)', array($fieldid, $blockid, 'LBL_COLORED_LISTVIEW_EDITOR', 'colored_listview.gif', 'LBL_COLORED_LISTVIEW_EDITOR', 'index.php?module=Settings&action=ColoredListView&parenttab=Settings', $seq)); 
//picklistmulti
$fieldid = $adb->getUniqueID($table_prefix.'_settings_field');
$blockid = getSettingsBlockId('LBL_STUDIO');
$seq_res = $adb->pquery("SELECT max(sequence) AS max_seq FROM ".$table_prefix."_settings_field WHERE blockid = ?", array($blockid));
if ($adb->num_rows($seq_res) > 0) {
	$cur_seq = $adb->query_result($seq_res, 0, 'max_seq');
	if ($cur_seq != null)	$seq = $cur_seq + 1;
}
ExecuteQuery('INSERT INTO '.$table_prefix.'_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence) 
	VALUES (?,?,?,?,?,?,?)', array($fieldid, $blockid, 'LBL_PICKLIST_EDITOR_MULTI', 'picklist_multilanguage.gif', 'LBL_PICKLIST_DESCRIPTION_MULTI', 'index.php?module=Picklistmulti&action=Picklistmulti&parenttab=Settings', $seq)); 
//fax server
$fieldid = $adb->getUniqueID($table_prefix.'_settings_field');
$blockid = getSettingsBlockId('LBL_OTHER_SETTINGS');
$seq_res = $adb->pquery("SELECT max(sequence) AS max_seq FROM ".$table_prefix."_settings_field WHERE blockid = ?", array($blockid));
if ($adb->num_rows($seq_res) > 0) {
	$cur_seq = $adb->query_result($seq_res, 0, 'max_seq');
	if ($cur_seq != null)	$seq = $cur_seq + 1;
}
ExecuteQuery('INSERT INTO '.$table_prefix.'_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence) 
	VALUES (?,?,?,?,?,?,?)', array($fieldid, $blockid, 'LBL_FAX_SERVER_SETTINGS', 'ogfaxserver.gif', 'LBL_FAX_SERVER_DESCRIPTION', 'index.php?module=Settings&action=FaxConfig&parenttab=Settings', $seq)); 
//sms server
$fieldid = $adb->getUniqueID($table_prefix.'_settings_field');
$blockid = getSettingsBlockId('LBL_OTHER_SETTINGS');
$seq_res = $adb->pquery("SELECT max(sequence) AS max_seq FROM ".$table_prefix."_settings_field WHERE blockid = ?", array($blockid));
if ($adb->num_rows($seq_res) > 0) {
	$cur_seq = $adb->query_result($seq_res, 0, 'max_seq');
	if ($cur_seq != null)	$seq = $cur_seq + 1;
}
ExecuteQuery('INSERT INTO '.$table_prefix.'_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence) 
	VALUES (?,?,?,?,?,?,?)', array($fieldid, $blockid, 'LBL_SMS_SERVER_SETTINGS', 'ogsmsserver.gif', 'LBL_SMS_SERVER_DESCRIPTION', 'index.php?module=Settings&action=SmsConfig&parenttab=Settings', $seq)); 
//asterisk
$fieldid = $adb->getUniqueID($table_prefix.'_settings_field');
$blockid = getSettingsBlockId('LBL_OTHER_SETTINGS');
$seq_res = $adb->pquery("SELECT max(sequence) AS max_seq FROM ".$table_prefix."_settings_field WHERE blockid = ?", array($blockid));
if ($adb->num_rows($seq_res) > 0) {
	$cur_seq = $adb->query_result($seq_res, 0, 'max_seq');
	if ($cur_seq != null)	$seq = $cur_seq + 1;
}
ExecuteQuery('INSERT INTO '.$table_prefix.'_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence) 
	VALUES (?,?,?,?,?,?,?)', array($fieldid, $blockid, 'LBL_SOFTPHONE_SERVER_SETTINGS', 'ogasteriskserver.gif', 'LBL_SOFTPHONE_SERVER_SETTINGS_DESCRIPTION', 'index.php?module=Settings&action=ModuleManager&module_settings=true&formodule=PBXManager&parenttab=Settings', $seq)); 
//ldap
$fieldid = $adb->getUniqueID($table_prefix.'_settings_field');
$blockid = getSettingsBlockId('LBL_OTHER_SETTINGS');
$seq_res = $adb->pquery("SELECT max(sequence) AS max_seq FROM ".$table_prefix."_settings_field WHERE blockid = ?", array($blockid));
if ($adb->num_rows($seq_res) > 0) {
	$cur_seq = $adb->query_result($seq_res, 0, 'max_seq');
	if ($cur_seq != null)	$seq = $cur_seq + 1;
}
ExecuteQuery('INSERT INTO '.$table_prefix.'_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence) 
	VALUES (?,?,?,?,?,?,?)', array($fieldid, $blockid, 'LBL_LDAP_SERVER_SETTINGS', 'ldap.gif', 'LBL_LDAP_SERVER_DESCRIPTION', 'index.php?module=Settings&action=LdapConfig&parenttab=Settings', $seq)); 
//menu a tendina
$fieldid = $adb->getUniqueID($table_prefix.'_settings_field');
$blockid = getSettingsBlockId('LBL_OTHER_SETTINGS');
$seq_res = $adb->pquery("SELECT max(sequence) AS max_seq FROM ".$table_prefix."_settings_field WHERE blockid = ?", array($blockid));
if ($adb->num_rows($seq_res) > 0) {
	$cur_seq = $adb->query_result($seq_res, 0, 'max_seq');
	if ($cur_seq != null)	$seq = $cur_seq + 1;
}
ExecuteQuery('INSERT INTO '.$table_prefix.'_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence) 
	VALUES (?,?,?,?,?,?,?)', array($fieldid, $blockid, 'LBL_MENU_TABS', 'picklist.gif', 'LBL_MENU_TABS_DESCRIPTION', 'index.php?module=Settings&action=menuSettings&parenttab=Settings', $seq));
//pdf config
$fieldid = $adb->getUniqueID($table_prefix.'_settings_field');
$blockid = getSettingsBlockId('LBL_OTHER_SETTINGS');
$seq_res = $adb->pquery("SELECT max(sequence) AS max_seq FROM ".$table_prefix."_settings_field WHERE blockid = ?", array($blockid));
if ($adb->num_rows($seq_res) > 0) {
	$cur_seq = $adb->query_result($seq_res, 0, 'max_seq');
	if ($cur_seq != null)	$seq = $cur_seq + 1;
}
ExecuteQuery('INSERT INTO '.$table_prefix.'_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence) 
	VALUES (?,?,?,?,?,?,?)', array($fieldid, $blockid, 'LBL_PDFCONFIGURATOR', 'pdfconfig.gif', 'LBL_PDFCONFIGURATOR_DESCRIPTION', 'index.php?module=Settings&action=PDFConfig&parenttab=Settings', $seq));


ExecuteQuery("ALTER TABLE ".$table_prefix."_systems     ADD COLUMN service_type VARCHAR(20) NULL ,     ADD COLUMN domain VARCHAR(20) NULL AFTER service_type,     ADD COLUMN account VARCHAR(100) NULL AFTER domain,     ADD COLUMN prefix VARCHAR(20) NULL AFTER account,     ADD COLUMN name VARCHAR(50) NULL AFTER prefix,     ADD COLUMN inc_call LONGBLOB NULL AFTER name");

Vtiger_Utils::AddColumn($table_prefix.'_troubletickets', 'internal_project_number','XL)');
Vtiger_Utils::AddColumn($table_prefix.'_troubletickets', 'external_project_number','XL)');

Vtiger_Utils::AddColumn($table_prefix.'_taskstatus', 'history','I(1))');
Vtiger_Utils::AddColumn($table_prefix.'_eventstatus', 'history','I(1))');

ExecuteQuery("update ".$table_prefix."_taskstatus set history = ?",Array(0));
ExecuteQuery("update ".$table_prefix."_eventstatus set history = ?",Array(0));
ExecuteQuery("update ".$table_prefix."_taskstatus set history = ? where taskstatus = ? or taskstatus = ?",Array(1,'Completed','Completato'));
ExecuteQuery("update ".$table_prefix."_eventstatus set history = ? where eventstatus = ? or eventstatus = ?",Array(1,'Held','Tenuto'));
//crmv@30007
ExecuteQuery('insert ignore into '.$table_prefix.'_notescf select notesid from '.$table_prefix.'_notes');
ExecuteQuery('INSERT IGNORE INTO '.$table_prefix.'_def_org_field SELECT 10,fieldid,0,1 FROM '.$table_prefix.'_field WHERE tabid = 10');
ExecuteQuery('INSERT IGNORE INTO '.$table_prefix.'_profile2field SELECT DISTINCT (profileid),f.tabid,f.fieldid,0,1 FROM '.$table_prefix.'_profile2field p
INNER JOIN '.$table_prefix.'_field f ON f.tabid = p.tabid
WHERE p.tabid = 10');
ExecuteQuery('UPDATE '.$table_prefix.'_field SET presence = 0 WHERE tabid = 10 AND tablename IN (\''.$table_prefix.'_activity\',\''.$table_prefix.'_crmentity\',\''.$table_prefix.'_attachments\')');
ExecuteQuery('DELETE FROM '.$table_prefix.'_field WHERE tabid = 20 AND fieldname = \'quote_no\' AND uitype = 3');
//crmv@30007 e
ExecuteQuery("UPDATE {$table_prefix}_def_org_share SET editstatus=? WHERE tabid=?",array(0,9));

include_once('vtlib/Vtiger/Module.php');
$moduleInstance = Vtiger_Module::getInstance('Accounts');
$moduleInstance->deleteLink('DETAILVIEWBASIC', 'LBL_SHOW_ACCOUNT_HIERARCHY', 'index.php?module=Accounts&action=AccountHierarchy&accountid=$RECORD$');

function vte_change_field($tablename,$field,$datatype,$precision,$other_params='',$is_primary_key=false) {
 		//per cambiare il tipo di dato di una colonna che contiene valori
 		global $adb;
 		
		//passo1: creo il nuovo campo
		$field_backup = $field."_backup";
		if ($precision != '') $precision = "($precision)";
		$criteria = "$field_backup $datatype"."$precision $other_params"; 
//		$adb->startTransaction();
		$sql = $adb->datadict->ChangeTableSQL($tablename,$criteria);
  		if ($sql){
   			$adb->datadict->ExecuteSQLArray($sql);
		   //passo2: copio i valori nel nuovo campo
		   $adb->query("update $tablename set $field_backup = $field");
		   //passo3: cancello il vecchio campo
		   $sql = $adb->datadict->DropColumnSQL($tablename,$field);
   			if ($sql){
    			$adb->datadict->ExecuteSQLArray($sql);
    			//passo4: rinomino il nuovo campo
    			$sql = $adb->datadict->RenameColumnSQL($tablename,$field_backup,$field,$criteria);
	    		if ($sql){
	     			$adb->datadict->ExecuteSQLArray($sql);
	     			//passo5: se il campo è primary key
//	     			if ($is_primary_key) {
//		     			$sql = $adb->datadict->ChangeTableSQL($tablename,"$field PRIMARY");
//  					$adb->datadict->ExecuteSQLArray($sql);
//	     			}
	    		}
   			}
  		}
 	}

$migrationlog->debug("\n\nDB Changes from 5.2.1 to 3.0 -------- Ends \n\n");
?>
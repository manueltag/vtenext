<?php
require_once 'include/Webservices/Utils.php';
require_once 'modules/Users/Users.php';
require_once 'include/utils/utils.php';
require_once("modules/com_vtiger_workflow/include.inc");
require_once("modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc");
require_once("modules/com_vtiger_workflow/VTEntityMethodManager.inc");
//after module install updates

//we have to use the current object (stored in PatchApply.php) to execute the queries
require_once ('vtlib/Vtiger/Utils.php');
global $table_prefix;
$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

$migrationlog->debug("\n\nDB Changes last step -------- Starts \n\n");

//fix seq start
$fix_seq_tables = Array(
	$table_prefix.'_field'=>'fieldid',
	$table_prefix.'_blocks'=>'blockid',
	$table_prefix.'_relatedlists'=>'relation_id',
	$table_prefix.'_eventhandlers'=>'eventhandler_id',
	$table_prefix.'_loginhistory'=>'login_id',
	$table_prefix.'_mobile_alerts'=>'id',
	$table_prefix.'_reportfolder'=>'folderid',
	'tbl_s_advrule_relmod'=>'advrule_id',
	$table_prefix.'_projects_status'=>'projects_statusid',
	$table_prefix.'_inventoryproductrel'=>'id',
	$table_prefix.'_act_reminder_popup'=>'reminderid',
	$table_prefix.'_product_lines'=>'product_linesid',
	'crmv_budget'=>'id',
	$table_prefix.'_potstagehistory'=>'historyid',
	$table_prefix.'_links'=>'linkid',
	$table_prefix.'_ticketcomments'=>'commentid',
	$table_prefix.'_invoicestatushistory'=>'historyid',
	$table_prefix.'_datashare_mod_rel'=>'shareid',
	$table_prefix.'_chat_users'=>'id',
	$table_prefix.'_chat_pvchat'=>'id',
	$table_prefix.'_chat_pchat'=>'id',
	$table_prefix.'_chat_msg'=>'id',
	$table_prefix.'_attachmentsfolder'=>'folderid',
	$table_prefix.'_headers'=>'fileid',
	$table_prefix.'_faqcomments'=>'commentid',
	$table_prefix.'_faq'=>'id',
	$table_prefix.'_evaluationstatus'=>'evalstatusid',
	$table_prefix.'_durationmins'=>'minsid',
	$table_prefix.'_durationhrs'=>'hrsid',
	$table_prefix.'_downloadpurpose'=>'downloadpurposeid',
	$table_prefix.'_currency_info'=>'id',
	$table_prefix.'_currency'=>'currencyid',
	$table_prefix.'_contacttype'=>'contacttypeid',
	$table_prefix.'_chat_users'=>'id',
	$table_prefix.'_chat_pvchat'=>'id',
	$table_prefix.'_chat_pchat'=>'id',
	$table_prefix.'_chat_msg'=>'id',
	$table_prefix.'_businesstype'=>'businesstypeid',
	$table_prefix.'_attachmentsfolder'=>'folderid',
	$table_prefix.'_activsubtype'=>'activesubtypeid',
	$table_prefix.'_act_reminder_popup'=>'reminderid',
	$table_prefix.'_accountregion'=>'accountregionid',
	$table_prefix.'_accountrating'=>'accountratingid',
	$table_prefix.'_accountownership'=>'acctownershipid',
	$table_prefix.'_accountdepstatus'=>'deploymentstatusid',
	$table_prefix.'_priority'=>'priorityid',
	$table_prefix.'_potstagehistory'=>'historyid',
	$table_prefix.'_picklist'=>'picklistid',
	$table_prefix.'_opportunitystage'=>'potstageid',
	$table_prefix.'_mailscanner_rules'=>'ruleid',
	$table_prefix.'_mailscanner_folders'=>'folderid',
	$table_prefix.'_mailscanner_actions'=>'actionid',
	$table_prefix.'_mailscanner'=>'scannerid',
	$table_prefix.'_loginhistory'=>'login_id',
	$table_prefix.'_licencekeystatus'=>'licencekeystatusid',
	$table_prefix.'_leadstage'=>'leadstageid',
	$table_prefix.'_invoicestatushistory'=>'historyid',
	$table_prefix.'_quotestagehistory'=>'historyid',
	$table_prefix.'_quotestage'=>'quotestageid',
	$table_prefix.'_projecttasktype'=>'projecttasktypeid',
	$table_prefix.'_projecttaskprogress'=>'projecttaskprogressid',
	$table_prefix.'_projecttaskpriority'=>'projecttaskpriorityid',
	$table_prefix.'_projectmilestype'=>'projectmilestypeid',
	$table_prefix.'_profile'=>'profileid',
	$table_prefix.'_productcategory'=>'productcategoryid',
	$table_prefix.'_priority'=>'priorityid',
	$table_prefix.'_rating'=>'rating_id',
	$table_prefix.'_recurringevents'=>'recurringid',
	$table_prefix.'_recurringtype'=>'recurringeventid',
	$table_prefix.'_reminder_interval'=>'reminder_intervalid',
	$table_prefix.'_reportfolder'=>'folderid',
	$table_prefix.'_revenuetype'=>'revenuetypeid',
	$table_prefix.'_sales_stage'=>'sales_stage_id',
	$table_prefix.'_salutationtype'=>'salutationid',
	$table_prefix.'_service_usageunit'=>'service_usageunitid',
	$table_prefix.'_servicecategory'=>'servicecategoryid',
	$table_prefix.'_sostatus'=>'sostatusid',
	$table_prefix.'_sostatushistory'=>'historyid',
	$table_prefix.'_status'=>'statusid',
	$table_prefix.'_taskpriority'=>'taskpriorityid',
	$table_prefix.'_taskstatus'=>'taskstatusid',
	$table_prefix.'_taxclass'=>'taxclassid',
	$table_prefix.'_ticketcategories'=>'ticketcategories_id',
	$table_prefix.'_ticketcomments'=>'commentid',
	$table_prefix.'_ticketpriorities'=>'ticketpriorities_id',
	$table_prefix.'_ticketseverities'=>'ticketseverities_id',
	$table_prefix.'_ticketstatus'=>'ticketstatus_id',
	$table_prefix.'_tracker'=>'id',
	$table_prefix.'_tracking_unit'=>'tracking_unitid',
	$table_prefix.'_usageunit'=>'usageunitid',
	$table_prefix.'_usertype'=>'usertypeid',
	$table_prefix.'_version'=>'id',
	$table_prefix.'_visibility'=>'visibilityid',
	$table_prefix.'_ws_entity'=>'id',
	$table_prefix.'_ws_entity_fieldtype'=>'fieldtypeid',
	$table_prefix.'_ws_fieldtype'=>'fieldtypeid',
	$table_prefix.'_ws_operation'=>'operationid',
);
if ($adb->isMysql()){
	$tables_seq = $adb->database->Metatables(false,false,"%_seq");
}
elseif($adb->isOracle()){
	$sql = $adb->database->metaSequencesSQL;
	$res = $adb->query($sql);
	if ($res){
		while ($row = $adb->fetchByAssoc($res,-1,false)){
			$tables_seq[] = $row['table_name'];
		}
	}
}
//TODO:farlo per altri db!
if (is_array($tables_seq)){
	foreach ($tables_seq as $tableNameSeq){
		$tableName = substr($tableNameSeq,0,-4);
		if (!$adb->table_exist($tableName) || $tableName == ''){
			continue;
		}	
		$factory = WebserviceField::fromArray($adb,array('tablename'=>$tableName));
		$dbTableFields = $factory->getTableFields();
		$found_field = false;
		foreach ($dbTableFields as $dbField) {
			if ($dbField->primary_key && ($dbField->type != 'varchar')){
				$found_field = $dbField->name;
			}
		}
		
		if ($found_field){
			$fix_seq_tables[$tableName]=$found_field;
		}		
	}
}
//fix seq mancanti
foreach ($fix_seq_tables as $table=>$key){
	if ($adb->table_exist($table)){
		Vtiger_Utils::AlterTable($table,"$key I(19) NOT NULL");
		$tableNameSeq = $table."_seq";
		$id = $adb->query_result_no_html($adb->query("select max($key) as count from $table"),0,'count');
		if ($id){
			$id = intval($id);
			$id++;
			$adb->database->DropSequence($tableNameSeq);
			$adb->database->CreateSequence($tableNameSeq,$id);			
		}
	}
}
//fix seq end

$_SESSION['skip_recalculate'] = true;

//crmv@sdk
require_once('modules/SDK/InstallTables.php');
$sdkModule = new Vtiger_Module();
$sdkModule->name = 'SDK';
$sdkModule->isentitytype = false;
$sdkModule->save();
SDK::clearSessionValues();
Vtiger_Module::fireEvent($sdkModule->name, Vtiger_Module::EVENT_MODULE_POSTINSTALL);
//crmv@sdk e

$skip_modules = array('Charts','WSAPP');
Common_Install_Wizard_Utils::installMandatoryModules($skip_modules);
Migration_Utils::installOptionalModules($migrationInfo['selected_optional_modules'], $migrationInfo['source_directory'], $migrationInfo['root_directory']);
unset($_SESSION['skip_recalculate']);
RecalculateSharingRules();

require_once('vtlib/Vtiger/Menu.php');
require_once('vtlib/Vtiger/ModuleBasic.php');
Vtiger_Menu::syncfile();
Vtiger_Module::syncfile();

$migrationlog->debug("\n\nDB Changes last step -------- Ends \n\n");
?>
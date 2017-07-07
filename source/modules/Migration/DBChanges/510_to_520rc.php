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
require_once ('vtlib/Vtiger/Utils.php');

//5.1.0 to 5.2.0 RC database changes

//we have to use the current object (stored in PatchApply.php) to execute the queries
$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

$migrationlog->debug("\n\nDB Changes from 5.1.0 to 5.2.0 RC -------- Starts \n\n");
function migration520_populateFieldForSecurity($tabid,$fieldid)
{
	global $adb,$table_prefix;

	$check_deforg_res = $adb->pquery("SELECT 1 FROM ".$table_prefix."_def_org_field WHERE tabid=? AND fieldid = ? LIMIT 1", array($tabid, $fieldid));
	if($check_deforg_res && $adb->num_rows($check_deforg_res)) {
		// Entry already exists, no need to act
	} else {
		$adb->pquery("INSERT INTO ".$table_prefix."_def_org_field (tabid, fieldid, visible, readonly) VALUES(?,?,?,?)",
			array($tabid, $fieldid, 0, 1));
	}
			
	$profileresult = $adb->pquery("SELECT * FROM ".$table_prefix."_profile", array());
	$countprofiles = $adb->num_rows($profileresult);
	for ($i=0;$i<$countprofiles;$i++)
	{
    	$profileid = $adb->query_result($profileresult,$i,'profileid');
    	$checkres  = $adb->pquery("SELECT 1 FROM ".$table_prefix."_profile2field WHERE profileid=? AND tabid=? AND fieldid=?", array($profileid, $tabid, $fieldid));
    	if($checkres && $adb->num_rows($checkres)) {
    		// Entry already exists, do nothing
    	} else {
    		$adb->pquery("INSERT INTO ".$table_prefix."_profile2field (profileid, tabid, fieldid, visible, readonly) VALUES(?,?,?,?,?)",
				array($profileid, $tabid, $fieldid, 0, 1));
    	}		
	}	
}
ExecuteQuery("CREATE TABLE IF NOT EXISTS ".$table_prefix."_tab_info (tabid INT, prefname VARCHAR(256), prefvalue VARCHAR(256), FOREIGN KEY fk_1_".$table_prefix."_tab_info(tabid) REFERENCES ".$table_prefix."_tab(tabid) ON DELETE CASCADE ON UPDATE CASCADE)  ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$documents_tab_id=getTabid('Documents'); 
ExecuteQuery("update ".$table_prefix."_field set quickcreate=3 where tabid = $documents_tab_id and columnname = 'filelocationtype'"); 
/* For Campaigns enhancement */
$accounts_tab_id = getTabid('Accounts');
$campaigns_tab_id = getTabid('Campaigns');
$contacts_tab_id = getTabid('Contacts');
$leads_tab_id = getTabid('Leads');


$campignrelstatus_contacts_fieldid  = $adb->getUniqueID($table_prefix.'_field');
ExecuteQuery("INSERT INTO ".$table_prefix."_field(tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable, helpinfo) VALUES ($contacts_tab_id,".$campignrelstatus_contacts_fieldid.", 'campaignrelstatus', '".$table_prefix."_campaignrelstatus', 1, '16', 'campaignrelstatus', 'Status', 1, 0, 0, 100, 1, NULL, 1, 'V~O', 1, NULL, 'BAS', 0, NULL)");
migration520_populateFieldForSecurity($contacts_tab_id, $campignrelstatus_contacts_fieldid);

$campignrelstatus_accounts_fieldid = $adb->getUniqueID($table_prefix.'_field');
ExecuteQuery("INSERT INTO ".$table_prefix."_field(tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable, helpinfo) VALUES ($accounts_tab_id,".$campignrelstatus_accounts_fieldid.", 'campaignrelstatus', '".$table_prefix."_campaignrelstatus', 1, '16', 'campaignrelstatus', 'Status', 1, 0, 0, 100, 1, NULL, 1, 'V~O', 1, NULL, 'BAS', 0, NULL)");
migration520_populateFieldForSecurity($accounts_tab_id, $campignrelstatus_accounts_fieldid);

$campignrelstatus_leads_fieldid     = $adb->getUniqueID($table_prefix.'_field');
ExecuteQuery("INSERT INTO ".$table_prefix."_field(tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable, helpinfo) VALUES ($leads_tab_id,".$campignrelstatus_leads_fieldid.", 'campaignrelstatus', '".$table_prefix."_campaignrelstatus', 1, '16', 'campaignrelstatus', 'Status', 1, 0, 0, 100, 1, NULL, 1, 'V~O', 1, NULL, 'BAS', 0, NULL)");
migration520_populateFieldForSecurity($leads_tab_id, $campignrelstatus_leads_fieldid);

$campignrelstatus_campaigns_fieldid = $adb->getUniqueID($table_prefix.'_field');
ExecuteQuery("INSERT INTO ".$table_prefix."_field(tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable, helpinfo) VALUES ($campaigns_tab_id,".$campignrelstatus_campaigns_fieldid.", 'campaignrelstatus', '".$table_prefix."_campaignrelstatus', 1, '16', 'campaignrelstatus', 'Status', 1, 0, 0, 100, 1, NULL, 1, 'V~O', 1, NULL, 'BAS', 0, NULL)");
migration520_populateFieldForSecurity($campaigns_tab_id, $campignrelstatus_campaigns_fieldid);

ExecuteQuery("CREATE TABLE ".$table_prefix."_campaignrelstatus (
	campaignrelstatusid INTEGER, campaignrelstatus VARCHAR(200), sortorderid INT, presence INT) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

ExecuteQuery("INSERT INTO ".$table_prefix."_campaignrelstatus VALUES (".$adb->getUniqueID($table_prefix.'_campaignrelstatus').", '--None--',1,1)");
ExecuteQuery("INSERT INTO ".$table_prefix."_campaignrelstatus VALUES (".$adb->getUniqueID($table_prefix.'_campaignrelstatus').", 'Contacted - Successful',2,1)");
ExecuteQuery("INSERT INTO ".$table_prefix."_campaignrelstatus VALUES (".$adb->getUniqueID($table_prefix.'_campaignrelstatus').", 'Contected - Unsuccessful',3,1)");
ExecuteQuery("INSERT INTO ".$table_prefix."_campaignrelstatus VALUES (".$adb->getUniqueID($table_prefix.'_campaignrelstatus').", 'Contacted - Never Contact Again',4,1)");

ExecuteQuery("CREATE TABLE ".$table_prefix."_campaignaccountrel (
	campaignid INTEGER UNSIGNED NOT NULL,
	accountid INTEGER UNSIGNED NOT NULL,
	campaignrelstatusid INTEGER UNSIGNED DEFAULT 1) ENGINE = InnoDB DEFAULT CHARSET=utf8;");
ExecuteQuery("ALTER TABLE ".$table_prefix."_campaignaccountrel ADD PRIMARY KEY (campaignid, accountid)");

ExecuteQuery("ALTER TABLE ".$table_prefix."_campaigncontrel ADD COLUMN campaignrelstatusid INTEGER UNSIGNED NOT NULL DEFAULT 1");
ExecuteQuery("ALTER TABLE ".$table_prefix."_campaignleadrel ADD COLUMN campaignrelstatusid INTEGER UNSIGNED NOT NULL DEFAULT 1");

ExecuteQuery("INSERT INTO ".$table_prefix."_relatedlists VALUES (".$adb->getUniqueID($table_prefix.'_relatedlists').", $accounts_tab_id, $campaigns_tab_id, 'get_campaigns', 13, 'Campaigns', 0, 'select')");
ExecuteQuery("INSERT INTO ".$table_prefix."_relatedlists VALUES (".$adb->getUniqueID($table_prefix.'_relatedlists').", $campaigns_tab_id, $accounts_tab_id, 'get_accounts', 5, 'Accounts', 0, 'add,select')");

Vtiger_Utils::AddColumn($table_prefix.'_inventorynotification', 'status','C(30)');

//Fix : 6182 after migration from 510 'fields to be shown' at a profile for Email module

	$query = "SELECT * from ".$table_prefix."_profile";
	$result = $adb->pquery($query,array());
	$rows = $adb->num_rows($result);

	$fields = "SELECT fieldid from ".$table_prefix."_field where tablename = ?";
	$fieldResult = $adb->pquery($fields,array($table_prefix.'_emaildetails'));
	$fieldRows = $adb->num_rows($fieldResult);
	$EmailTabid = getTabid('Emails');
	for($i=0; $i<$rows ;$i++){
		$profileid = $adb->query_result($result ,$i ,'profileid');
		for($j=0 ;$j<$fieldRows; $j++) {
			$fieldid = $adb->query_result($fieldResult, $j ,'fieldid');

			$sql_profile2field = "select * from ".$table_prefix."_profile2field where fieldid=? and profileid=?";
			$result_profile2field = $adb->pquery($sql_profile2field,array($fieldid,$profileid));
			$rows_profile2field = $adb->num_rows($result_profile2field);
			if(!($rows_profile2field > 0)){
				$adb->query("INSERT INTO ".$table_prefix."_profile2field(profileid ,tabid,fieldid,visible,readonly) VALUES ($profileid, $EmailTabid, $fieldid, 0 , 1)");
			}
		}
	}
	for($k=0;$k<$fieldRows;$k++){
		$fieldid = $adb->query_result($fieldResult, $k ,'fieldid');
		$sql_deforgfield = "select * from ".$table_prefix."_def_org_field where tabid=? and fieldid=?";
		$result_deforgfield = $adb->pquery($sql_deforgfield,array($EmailTabid,$fieldid));
		$rows_deforgfield = $adb->num_rows($result_deforgfield);
		if(!($rows_deforgfield)){
			$adb->query("INSERT INTO ".$table_prefix."_def_org_field(tabid ,fieldid,visible,readonly) VALUES ($EmailTabid, $fieldid, 0 , 1)");
		}
	}
	$sql = 'update '.$table_prefix.'_field set block=(select blockid from '.$table_prefix.'_blocks where '.
        "blocklabel=?) where tablename=?";
        $params = array('LBL_EMAIL_INFORMATION',$table_prefix.'_emaildetails');
        $adb->pquery($sql,$params);
	//END
	//update vtiger_systems to add a email field to be used as the from email address
		$sql = "ALTER TABLE ".$table_prefix."_systems ADD from_email_field varchar(50);";
		ExecuteQuery($sql);
	//END

	// to disable unit_price from the massedit wizndow for products
	ExecuteQuery("update ".$table_prefix."_field set masseditable=0 where tablename='".$table_prefix."_products' and columnname='unit_price'");
	//END
function VT520_webserviceMigrate(){
	require_once 'include/Webservices/Utils.php';
	$customWebserviceDetails = array(
		"name"=>"convertlead",
		"include"=>"include/Webservices/ConvertLead.php",
		"handler"=>"vtws_convertlead",
		"prelogin"=>0,
		"type"=>"POST"
	);

	$customWebserviceParams = array(
		array("name"=>'leadId',"type"=>'String' ),
		array("name"=>'assignedTo','type'=>'String'),
		array("name"=>'accountName','type'=>'String'),
		array("name"=>'avoidPotential','type'=>'Boolean'),
		array("name"=>'potential','type'=>'Encoded')
	);
	echo 'INITIALIZING WEBSERVICE...';
	$operationId = vtws_addWebserviceOperation($customWebserviceDetails['name'],$customWebserviceDetails['include'],
		$customWebserviceDetails['handler'],$customWebserviceDetails['type']);
	if($operationId === null && $operationId > 0){
		echo 'FAILED TO SETUP '.$customWebserviceDetails['name'].' WEBSERVICE';
		die;
	}
	$sequence = 1;
	foreach ($customWebserviceParams as $param) {
		$status = vtws_addWebserviceOperationParam($operationId,$param['name'],$param['type'],$sequence++);
		if($status === false){
			echo 'FAILED TO SETUP '.$customWebserviceDetails['name'].' WEBSERVICE HALFWAY THOURGH';
			die;
		}
	}
}

VT520_webserviceMigrate();

$update_InvProductRel = "ALTER TABLE ".$table_prefix."_inventoryproductrel MODIFY discount_amount decimal(25,3)";
ExecuteQuery($update_InvProductRel);
// Registering events for ON MODIFY in Workflows
$handlerId = $adb->getUniqueId($table_prefix.'_eventhandlers');
$modifyevent =$table_prefix.'.entity.afterrestore';
$eventPath = 'modules/com_vtiger_workflow/VTEventHandler.inc';
$handlerClass = 'VTWorkflowEventHandler';
$modifyevent = $adb->pquery("insert into ".$table_prefix."_eventhandlers(eventhandler_id, event_name, handler_path, handler_class,cond,is_active)
		values (?,?,?,?,?,1)",array($handlerId,$modifyevent,$eventPath,$handlerClass,''));

// Populate Default Workflows
populateDefaultWorkflows($adb);

function populateDefaultWorkflows($adb) {
	global $table_prefix;
	require_once("modules/com_vtiger_workflow/include.inc");
	require_once("modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc");
	require_once("modules/com_vtiger_workflow/VTEntityMethodManager.inc");

	//added column defaultworkflow
	//For default workflows it sets column defaultworkflow=true
	
	$column_name="defaultworkflow";
	$adb->pquery("alter table com_{$table_prefix}_workflows add column $column_name int(1)",array());

	// Creating Workflow for Accounts when Notifyowner is true

	$vtaWorkFlow = new VTWorkflowManager($adb);
	$accWorkFlow = $vtaWorkFlow->newWorkFlow("Accounts");
	$accWorkFlow->test = '[{"fieldname":"notify_owner","operation":"is","value":"true:boolean"}]';
	$accWorkFlow->description = "Send Email to user when Notifyowner is True";
	$accWorkFlow->executionCondition=2;
	$vtaWorkFlow->save($accWorkFlow);
	$id1=$accWorkFlow->id;

	$tm = new VTTaskManager($adb);
	$task = $tm->createTask('VTEmailTask',$accWorkFlow->id);
	$task->active=true;
	$task->methodName = "NotifyOwner";
	$task->recepient = "\$(assigned_user_id : (Users) email1)";
	$task->subject = "Regarding Account Creation";
	$task->content = "An Account has been assigned to you on vtigerCRM<br>Details of account are :<br><br>".
			"AccountId:".'<b>$account_no</b><br>'."AccountName:".'<b>$accountname</b><br>'."Rating:".'<b>$rating</b><br>'.
			"Industry:".'<b>$industry</b><br>'."AccountType:".'<b>$accounttype</b><br>'.
			"Description:".'<b>$description</b><br><br><br>'."Thank You<br>Admin";
	$task->summary="An account has been created ";
	$tm->saveTask($task);
	$adb->pquery("update com_{$table_prefix}_workflows set defaultworkflow=? where workflow_id=?",array(1,$id1));


	// Creating Workflow for Contacts when Notifyowner is true

	$vtcWorkFlow = new VTWorkflowManager($adb);
	$conWorkFlow = 	$vtcWorkFlow->newWorkFlow("Contacts");
	$conWorkFlow->summary="Test accounut";
	$conWorkFlow->executionCondition=2;
	$conWorkFlow->test = '[{"fieldname":"notify_owner","operation":"is","value":"true:boolean"}]';
	$conWorkFlow->description = "Send Email to user when Notifyowner is True";

	$vtcWorkFlow->save($conWorkFlow);
	$id1=$conWorkFlow->id;
	$tm = new VTTaskManager($adb);
	$task = $tm->createTask('VTEmailTask',$conWorkFlow->id);
	$task->active=true;
	$task->methodName = "NotifyOwner";
	$task->recepient = "\$(assigned_user_id : (Users) email1)";
	$task->subject = "Regarding Contact Creation";
	$task->content = "An Contact has been assigned to you on vtigerCRM<br>Details of Contact are :<br><br>".
			"Contact Id:".'<b>$contact_no</b><br>'."LastName:".'<b>$lastname</b><br>'."FirstName:".'<b>$firstname</b><br>'.
			"Lead Source:".'<b>$leadsource</b><br>'.
			"Department:".'<b>$department</b><br>'.
			"Description:".'<b>$description</b><br><br><br>'."Thank You<br>Admin";
	$task->summary="An contact has been created ";
	$tm->saveTask($task);
	$adb->pquery("update com_{$table_prefix}_workflows set defaultworkflow=? where workflow_id=?",array(1,$id1));


	// Creating Workflow for Contacts when PortalUser is true

	$vtcWorkFlow = new VTWorkflowManager($adb);
	$conpuWorkFlow = $vtcWorkFlow->newWorkFlow("Contacts");
	$conpuWorkFlow->test = '[{"fieldname":"portal","operation":"is","value":"true:boolean"}]';
	$conpuWorkFlow->description = "Send Email to user when Portal User is True";
	$conpuWorkFlow->executionCondition=2;
	$vtcWorkFlow->save($conpuWorkFlow);
	$id1=$conpuWorkFlow->id;

	$tm = new VTTaskManager($adb);
	$task = $tm->createTask('VTEmailTask',$conpuWorkFlow->id);

	$task->active=true;
	$task->methodName = "NotifyOwner";
	$task->recepient = "\$(assigned_user_id : (Users) email1)";
	$task->subject = "Regarding Contact Assignment";
	$task->content = "An Contact has been assigned to you on vtigerCRM<br>Details of Contact are :<br><br>".
			"Contact Id:".'<b>$contact_no</b><br>'."LastName:".'<b>$lastname</b><br>'."FirstName:".'<b>$firstname</b><br>'.
			"Lead Source:".'<b>$leadsource</b><br>'.
			"Department:".'<b>$department</b><br>'.
			"Description:".'<b>$description</b><br><br><br>'."And <b>CustomerPortal Login Details</b> is sent to the " .
			"EmailID :-".'$email<br>'."<br>Thank You<br>Admin";

	$task->summary="An contact has been created ";
	$tm->saveTask($task);
	$adb->pquery("update com_{$table_prefix}_workflows set defaultworkflow=? where workflow_id=?",array(1,$id1));

	// Creating Workflow for Potentials

	$vtcWorkFlow = new VTWorkflowManager($adb);
	$potentialWorkFlow = $vtcWorkFlow->newWorkFlow("Potentials");
	$potentialWorkFlow->description = "Send Email to user on Potential creation";
	$potentialWorkFlow->executionCondition=1;
	$vtcWorkFlow->save($potentialWorkFlow);
	$id1=$potentialWorkFlow->id;

	$tm = new VTTaskManager($adb);
	$task = $tm->createTask('VTEmailTask',$potentialWorkFlow->id);

	$task->active=true;
	$task->recepient = "\$(assigned_user_id : (Users) email1)";
	$task->subject = "Regarding Potential Assignment";
	$task->content = "An Potential has been assigned to you on vtigerCRM<br>Details of Potential are :<br><br>".
			"Potential No:".'<b>$potential_no</b><br>'."Potential Name:".'<b>$potentialname</b><br>'.
			"Amount:".'<b>$amount</b><br>'.
			"Expected Close Date:".'<b>$closingdate</b><br>'.
			"Type:".'<b>$opportunity_type</b><br><br><br>'.
			"Description :".'$description<br>'."<br>Thank You<br>Admin";

	$task->summary="An Potential has been created ";
	$tm->saveTask($task);
	$adb->pquery("update com_{$table_prefix}_workflows set defaultworkflow=? where workflow_id=?",array(1,$id1));
}

function VT520_migrateCustomview($sql,$forModule, $user, $handler) {
	$db = PearDatabase::getInstance();
	$params = array();
	$result = $db->pquery($sql, $params);
	$it = new SqlResultIterator($db, $result);

	$moduleMetaInfo = array();

	foreach ($it as $row) {
		$module = $row->entitytype;
		$current_module = $module;
		if($forModule == 'Accounts') {
			$fieldname = 'account_id';
		}elseif($forModule == 'Contacts') {
			$fieldname = 'contact_id';
		}elseif($forModule == 'Products') {
			$fieldname = 'product_id';
		} elseif ($forModule == 'SalesOrder') {
			$fieldname = 'quote_id';
		}

		if(empty($moduleMetaInfo[$module])) {
			$moduleMetaInfo[$module] = new VtigerCRMObjectMeta(VtigerWebserviceObject::fromName($db,
					$module), $user);
		}
		$meta = $moduleMetaInfo[$module];

		$moduleFields = $meta->getModuleFields();
		$field = $moduleFields[$fieldname];
		if (!$field)
			continue;
		$columnname = $field->getTableName().':'.$field->getColumnName().':'.$field->getFieldName().
				':'.$module.'_'.str_replace(' ','_',$field->getFieldLabelKey()).':V';
		$handler($columnname, $row);
	}
}

function VT520_updateCVColumnList($columnname, $row) {
	global $table_prefix;
	$db = PearDatabase::getInstance();
	$sql = 'update '.$table_prefix.'_cvcolumnlist set columnname=? where cvid=? and columnindex=?';
	$params = array($columnname, $row->cvid,$row->columnindex);
	$db->pquery($sql, $params);
}

function VT520_updateADVColumnList($columnname, $row) {
	global $table_prefix;
	$db = PearDatabase::getInstance();
	$sql = 'update '.$table_prefix.'_cvadvfilter set columnname=? where cvid=? and columnindex=?';
	$params = array($columnname, $row->cvid,$row->columnindex);
	$db->pquery($sql, $params);
}

function VT520_queryGeneratorMigration() {
	global $table_prefix;
	$db = PearDatabase::getInstance();
	$sql = "delete from ".$table_prefix."_cvadvfilter where columnname IS NULL or columnname='';";
	$db->pquery($sql, array());
	$sql = "select id from ".$table_prefix."_users where is_admin='On' and status='Active' limit 1";
	$result = $db->pquery($sql, array());
	$adminId = 1;
	$it = new SqlResultIterator($db, $result);
	foreach ($it as $row) {
		$adminId = $row->id;
	}
	$user = new Users();
	$current_user = $user->retrieveCurrentUserInfoFromFile($adminId);
	$user = $current_user;
	$sql = "select ".$table_prefix."_customview.cvid,columnindex,entitytype from ".$table_prefix."_customview inner join ".
		"".$table_prefix."_cvcolumnlist on ".$table_prefix."_customview.cvid=".$table_prefix."_cvcolumnlist.cvid where entitytype !=".
		"'Accounts' and columnname like '".$table_prefix."_account:accountname:accountname%';";
	VT520_migrateCustomview($sql,'Accounts', $user, VT520_updateCVColumnList);
	$sql = "select ".$table_prefix."_customview.cvid,columnindex,entitytype from ".$table_prefix."_customview inner join ".
		"".$table_prefix."_cvcolumnlist on ".$table_prefix."_customview.cvid=".$table_prefix."_cvcolumnlist.cvid where entitytype !=".
		"'Contacts' and columnname like '".$table_prefix."_contactdetails:lastname:lastname:%';";
	VT520_migrateCustomview($sql,'Contacts', $user, VT520_updateCVColumnList);
	$sql = "select ".$table_prefix."_customview.cvid,columnindex,entitytype from ".$table_prefix."_customview inner join ".
		"".$table_prefix."_cvcolumnlist on ".$table_prefix."_customview.cvid=".$table_prefix."_cvcolumnlist.cvid where entitytype not in ".
		"('Products','HelpDesk','Faq') and columnname like '".$table_prefix."_products:productname:productname%';";
	VT520_migrateCustomview($sql,'Products', $user, VT520_updateCVColumnList);

	$sql = "select ".$table_prefix."_customview.cvid,columnindex,entitytype from ".$table_prefix."_customview inner join ".
		"".$table_prefix."_cvcolumnlist on ".$table_prefix."_customview.cvid=".$table_prefix."_cvcolumnlist.cvid where entitytype not in ".
		"('Products','HelpDesk','Faq') and columnname like '".$table_prefix."_quotes:quoteid:quote_id%';";
	VT520_migrateCustomview($sql,'SalesOrder', $user, VT520_updateCVColumnList);


	$sql = "select ".$table_prefix."_customview.cvid,columnindex,entitytype from ".$table_prefix."_customview inner join ".
		"".$table_prefix."_cvadvfilter on ".$table_prefix."_customview.cvid=".$table_prefix."_cvadvfilter.cvid where entitytype !=".
		"'Accounts' and columnname like '".$table_prefix."_account:accountname:accountname%';";
	VT520_migrateCustomview($sql,'Accounts', $user, VT520_updateADVColumnList);
	$sql = "select ".$table_prefix."_customview.cvid,columnindex,entitytype from ".$table_prefix."_customview inner join ".
		"".$table_prefix."_cvadvfilter on ".$table_prefix."_customview.cvid=".$table_prefix."_cvadvfilter.cvid where entitytype !=".
		"'Contacts' and columnname like '".$table_prefix."_contactdetails:lastname:lastname:%';";
	VT520_migrateCustomview($sql,'Contacts', $user, VT520_updateADVColumnList);
	$sql = "select ".$table_prefix."_customview.cvid,columnindex,entitytype from ".$table_prefix."_customview inner join ".
		"".$table_prefix."_cvadvfilter on ".$table_prefix."_customview.cvid=".$table_prefix."_cvadvfilter.cvid where entitytype not in ".
		"('Products','HelpDesk','Faq') and columnname like '".$table_prefix."_products:productname:productname%';";
	VT520_migrateCustomview($sql,'Products', $user, VT520_updateADVColumnList);
	$sql = "select ".$table_prefix."_customview.cvid,columnindex,entitytype from ".$table_prefix."_customview inner join ".
		"".$table_prefix."_cvcolumnlist on ".$table_prefix."_customview.cvid=".$table_prefix."_cvcolumnlist.cvid where entitytype not in ".
		"('Products','HelpDesk','Faq') and columnname like '".$table_prefix."_quotes:quoteid:quote_id%';";
	VT520_migrateCustomview($sql,'SalesOrder', $user, VT520_updateADVColumnList);

	$tabId = getTabid('Contacts');
	$sql = "select fieldid from ".$table_prefix."_field where tabid=? and fieldname='birthday';";
	$params = array($tabId);
	$result = $db->pquery($sql, $params);
	$it = new SqlResultIterator($db, $result);
	$fieldId = null;
	foreach($it as $row) {
		$fieldId = $row->fieldid;
	}
	if(!empty($fieldId)) {
		$sql = "update ".$table_prefix."_field set typeofdata = 'D~O' where fieldid=?;";
		$params = array($fieldId);
		$result = $db->pquery($sql, $params);
	} else {
		echo '
			<tr width="100%">
				<td width="25%">Failure</td>
				<td width="5%"><font color="red"> F </font></td>
				<td width="70%">Failed to change typeofdata of birthday field</td>
			</tr>';
	}

	$tabId = getTabid('Documents');
	$sql = "select fieldid from ".$table_prefix."_field where tabid=? and fieldname='filesize';";
	$params = array($tabId);
	$result = $db->pquery($sql, $params);
	$it = new SqlResultIterator($db, $result);
	$fieldId = null;
	foreach($it as $row) {
		$fieldId = $row->fieldid;
	}
	if(!empty($fieldId)) {
		$sql = "update ".$table_prefix."_field set typeofdata = 'I~O' where fieldid=?;";
		$params = array($fieldId);
		$result = $db->pquery($sql, $params);
	} else {
		echo '
			<tr width="100%">
				<td width="25%">Failure</td>
				<td width="5%"><font color="red"> F </font></td>
				<td width="70%">Failed to change typeofdata of filesize field</td>
			</tr>';
	}
}

VT520_queryGeneratorMigration();


ExecuteQuery("ALTER table ".$table_prefix."_asteriskincomingcalls ADD COLUMN refuid varchar(255)");
ExecuteQuery("
	CREATE TABLE ".$table_prefix."_asteriskincomingevents (
  	uid varchar(255) NOT NULL,
  	channel varchar(100)  default NULL,
	from_number bigint(20) default NULL,
  	from_name varchar(100) default NULL,
  	to_number bigint(20) default NULL,
  	callertype varchar(100) default NULL,
  	timer int(20) default NULL,
	flag varchar(3) default NULL,
  	pbxrecordid int(19) default NULL,
	relcrmid int(19) default NULL,
  	PRIMARY KEY  (uid))");
// Alter vtiger_relcriteria table to store groupid and column_condition
$adb->query("ALTER TABLE ".$table_prefix."_relcriteria ADD COLUMN groupid INT DEFAULT 1");
$adb->query("ALTER TABLE ".$table_prefix."_relcriteria ADD COLUMN column_condition VARCHAR(256) DEFAULT 'and'");

// Create table to store Reports Advanced Filters Condition Grouping information
$adb->query("CREATE TABLE IF NOT EXISTS ".$table_prefix."_relcriteria_grouping 
		(groupid INT NOT NULL, queryid INT, group_condition VARCHAR(256), condition_expression TEXT, PRIMARY KEY(groupid, queryid))");
		
// Migration queries to migrate existing data to the required state (Storing Condition Expression in the newly created table for existing Reports)
// Remove all unwanted condition columns added (where column name is empty)
$adb->pquery("DELETE FROM ".$table_prefix."_relcriteria WHERE (columnname IS NULL OR trim(columnname) = '')",
		array());
$maxReportIdResult = $adb->query("SELECT max(reportid) as max_reportid FROM ".$table_prefix."_report");
if($adb->num_rows($maxReportIdResult) > 0) {
	$maxReportId = $adb->query_result($maxReportIdResult, 0, 'max_reportid');
	if(!empty($maxReportId) && $maxReportId > 0) {
		for($i=1; $i<=$maxReportId; ++$i) {
			$reportId = $i;
			$relcriteriaResult = $adb->pquery("SELECT * FROM ".$table_prefix."_relcriteria WHERE queryid=?", array($reportId)); // Pick all the conditions of a Report
			$noOfConditions = $adb->num_rows($relcriteriaResult);
			if($noOfConditions > 0) {
				$columnIndexArray = array();
				$maxColumnIndex = 0;
				for($j=0;$j<$noOfConditions; $j++) {
					$columnIndex = $adb->query_result($relcriteriaResult, $j, 'columnindex');
					if($maxColumnIndex < $columnIndex) {
						$maxColumnIndex = $columnIndex;
					}
					$columnIndexArray[] = $columnIndex;
				}
				$conditionExpression = implode(' and ', $columnIndexArray);
				$adb->pquery('INSERT INTO '.$table_prefix.'_relcriteria_grouping VALUES(?,?,?,?)', 
							array(1, $reportId, '', $conditionExpression));

				$adb->pquery("UPDATE ".$table_prefix."_relcriteria SET column_condition='' WHERE columnindex=? AND queryid=?", array($maxColumnIndex,$reportId));
			}		
		}
	}
}

ExecuteQuery("CREATE TABLE IF NOT EXISTS `".$table_prefix."_customerportal_tabs` ( `tabid` int(19) NOT NULL, `visible` int(1) 
	default '1', `sequence` int(1) default NULL, PRIMARY KEY  (`tabid`)) ENGINE=InnoDB 
	DEFAULT CHARSET=utf8");

ExecuteQuery("CREATE TABLE IF NOT EXISTS `".$table_prefix."_customerportal_prefs` ( `tabid` int(11) NOT NULL, `prefkey` 
	varchar(100) default NULL, `prefvalue` int(20) default NULL, INDEX tabid_idx(tabid) 
	) ENGINE=InnoDB DEFAULT CHARSET=utf8");


//Adding Block to email fields 
$blockquery = "select blockid from ".$table_prefix."_blocks where blocklabel = ?"; 
$blockres = $adb->pquery($blockquery,array('LBL_EMAIL_INFORMATION'));
$blockid = $adb->query_result($blockres,0,'blockid');
$fieldsqueryuitype8 = 'update '.$table_prefix.'_field set block=? where tabid=? and uitype=8';
$adb->pquery($fieldsqueryuitype8,array($blockid,$email_Tabid));

$fieldsqueryuitype12 = 'update '.$table_prefix.'_field set block=? where tabid=? and uitype=12';
$adb->pquery($fieldsqueryuitype12,array($blockid,$email_Tabid));

$fieldsqueryuitype1 = 'update '.$table_prefix.'_field set block=? where tabid=? and uitype=1';
$adb->pquery($fieldsqueryuitype1,array($blockid,$email_Tabid));

$fieldsqueryuitype16 = 'update '.$table_prefix.'_field set block=? where tabid=? and uitype=16';
$adb->pquery($fieldsqueryuitype16,array($blockid,$email_Tabid));

require_once 'include/utils/utils.php';

$sql = 'delete from '.$table_prefix.'_field where tablename=? and fieldname=? and tabid=?';
$params = array($table_prefix.'_seactivityrel','parent_id',getTabid('Emails'));
$adb->pquery($sql,$params);
$sql = 'update '.$table_prefix.'_field set uitype=?,displaytype=? where tablename=? and'.
' fieldname=? and tabid=?';
$params = array('357',1,$table_prefix.'_emaildetails','parent_id',getTabid('Emails'));
$adb->pquery($sql,$params);
$sql = 'update '.$table_prefix.'_field set block=(select blockid from '.$table_prefix.'_blocks where '.
"blocklabel=?) where tablename=?";
$params = array('LBL_EMAIL_INFORMATION',$table_prefix.'_emaildetails');
$adb->pquery($sql,$params);

// Correct the type
ExecuteQuery("UPDATE {$table_prefix}_field SET typeofdata='V~O' WHERE typeofdata='V~0'");

function VT520_manageIndexes() {
	global $table_prefix;
	$db = PearDatabase::getInstance();
	ExecuteQuery("ALTER TABLE ".$table_prefix."_potential ADD INDEX `vt_pot_sales_stage_amount_idx` ".
			"(amount, sales_stage)");
	$result = $db->pquery("SELECT COUNT(1) as count FROM INFORMATION_SCHEMA.STATISTICS WHERE table_schema = ".
			"'$db->dbName' AND table_name = '".$table_prefix."_potential' AND index_name = ".
			"'potential_potentialid_idx'",array());
	$count = $db->query_result($result, 0, 'count');
	if($count > 0) {
		ExecuteQuery("ALTER TABLE ".$table_prefix."_potential DROP INDEX `potential_potentialid_idx`");
	}
	$result = $db->pquery("SELECT COUNT(1) as count FROM INFORMATION_SCHEMA.STATISTICS WHERE table_schema = ".
			"'$db->dbName' AND table_name = '".$table_prefix."_potential' AND index_name = ".
			"'potential_accountid_idx'",array());
	$count = $db->query_result($result, 0, 'count');
	if($count > 0) {
		ExecuteQuery("ALTER TABLE ".$table_prefix."_potential DROP INDEX `potential_accountid_idx`");
		ExecuteQuery("ALTER TABLE ".$table_prefix."_potential ADD INDEX `potential_relatedto_idx` ".
			"(related_to)");
	}
	$result = $db->pquery("SELECT COUNT(1) as count FROM INFORMATION_SCHEMA.STATISTICS WHERE table_schema = ".
			"'$db->dbName' AND table_name = '".$table_prefix."_crmentity' AND index_name = ".
			"'crmentity_smownerid_idx'",array());
	$count = $db->query_result($result, 0, 'count');
	if($count > 0) {
		ExecuteQuery("ALTER TABLE ".$table_prefix."_crmentity DROP INDEX `crmentity_smownerid_idx`");
	}
	$result = $db->pquery("SELECT COUNT(1) as count FROM INFORMATION_SCHEMA.STATISTICS WHERE table_schema = ".
			"'$db->dbName' AND table_name = '".$table_prefix."_crmentity' AND index_name = ".
			"'crmentity_smownerid_deleted_idx'",array());
	$count = $db->query_result($result, 0, 'count');
	if($count > 0) {
		ExecuteQuery("ALTER TABLE ".$table_prefix."_crmentity DROP INDEX `crmentity_smownerid_deleted_idx`");
	}
	ExecuteQuery("ALTER TABLE ".$table_prefix."_crmentity ADD INDEX `crm_ownerid_del_setype_idx` ".
		"(smownerid,deleted,setype)");
}

function VT520_fieldCleanUp() {
	global $table_prefix;
	$db = PearDatabase::getInstance();
	$result = $db->pquery("SELECT fieldid,typeofdata FROM ".$table_prefix."_field WHERE fieldname = ".
			"'birthday' AND tabid = '".getTabid('Contacts')."'",array());
	$fieldId = $db->query_result($result, 0, 'fieldid');
	$typeOfData = $db->query_result($result, 0, 'typeofdata');
	$typeInfo = explode('~', $typeOfData);
	$mandatory = $typeInfo[1];
	ExecuteQuery("update ".$table_prefix."_field set typeofdata='D~$mandatory' where fieldid=$fieldId");
	$result = $db->pquery("SELECT fieldid,typeofdata FROM ".$table_prefix."_field WHERE fieldname = ".
			"'eventstatus' AND tabid = '".getTabid('Calendar')."'",array());
	$fieldId = $db->query_result($result, 0, 'fieldid');
	$typeOfData = $db->query_result($result, 0, 'typeofdata');
	$typeInfo = explode('~', $typeOfData);
	$type = $typeInfo[0];
	ExecuteQuery("update ".$table_prefix."_field set typeofdata='$type~O' where fieldid=$fieldId");
}

VT520_manageIndexes();
VT520_fieldCleanUp();

ExecuteQuery("DROP TABLE IF EXISTS {$table_prefix}_asteriskoutgoingcalls");

$migrationlog->debug("\n\nDB Changes from 5.1.0 to 5.2.0 RC -------- Ends \n\n");

?>
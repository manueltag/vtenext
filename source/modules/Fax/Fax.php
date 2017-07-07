<?php
/*+********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: crmvillage.biz Open Source
* The Initial Developer of the Original Code is crmvillage.biz*
* Portions created by crmvillage.biz are Copyright (C) crmvillage.biz*.
* *All Rights Reserved.
********************************************************************************/

include_once('config.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('data/CRMEntity.php');
require_once('modules/Contacts/Contacts.php');
require_once('modules/Accounts/Accounts.php');
require_once('modules/Potentials/Potentials.php');
require_once('modules/Users/Users.php');

// Fax is used to store customer information.
class Fax extends CRMEntity {
	var $log;
	var $db;
	var $table_name;
	var $table_index= 'activityid';	//crmv@24834
	// Stored vtiger_fields
  	// added to check email save from plugin or not
	var $plugin_save = false;
	var $rel_users_table;
	var $rel_contacts_table;
	var $rel_serel_table;
	var $tab_name = Array();
    var $tab_name_index = Array();

	// This is the list of vtiger_fields that are in the lists.
        var $list_fields = Array(
				       'Subject'=>Array('activity'=>'subject'),
				       'Related to'=>Array('seactivityrel'=>'parent_id'),
        				 'Document'=>Array('activity'=>'filename'),
				       'Date Sent'=>Array('activity'=>'date_start'),
				       'Assigned To'=>Array('crmentity','smownerid')
			        );

       var $list_fields_name = Array(
				       'Subject'=>'subject',
				       'Related to'=>'parent_id',
       				 'Document'=>'filename',
				       'Date Sent'=>'date_start',
				       'Assigned To'=>'assigned_user_id'
				    );

       var $list_link_field= 'subject';

	var $column_fields = Array();

	var $sortby_fields = Array('subject','date_start','smownerid');

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'date_start';
	var $default_sort_order = 'ASC';

	/** This function will set the columnfields for Email module
	*/

	function Fax() {
		global $table_prefix;
		parent::__construct(); // crmv@37004
		// crmv@43765
		$this->relation_table = $table_prefix.'_seactivityrel';
		$this->relation_table_id = 'activityid';
		$this->relation_table_otherid = 'crmid';
		$this->relation_table_module = '';
		$this->relation_table_othermodule = '';
		// crmv@43765e

		$this->table_name = $table_prefix."_activity";
		$this->rel_users_table = $table_prefix."_salesmanactivityrel";
		$this->rel_contacts_table = $table_prefix."_cntactivityrel";
		$this->rel_serel_table = $table_prefix."_seactivityrel";
		$this->tab_name = Array($table_prefix.'_crmentity',$table_prefix.'_activity');
        $this->tab_name_index = Array($table_prefix.'_crmentity'=>'crmid',$table_prefix.'_activity'=>'activityid',$table_prefix.'_seactivityrel'=>'activityid',$table_prefix.'_cntactivityrel'=>'activityid');
		$this->log = LoggerManager::getLogger('fax');
		$this->log->debug("Entering Fax() method ...");
		$this->log = LoggerManager::getLogger('fax');
		$this->db = new PearDatabase();
		$this->column_fields = getColumnFields('Fax');
		$this->log->debug("Exiting Email method ...");
	}


	function save_module($module)
	{
		global $adb;
		global $table_prefix;
		//Inserting into seactivityrel

		  //modified by Richie as raju's implementation broke the feature for addition of webmail to vtiger_crmentity.need to be more careful in future while integrating code
	   	  if($_REQUEST['module']=="Fax" && (!$this->plugin_save))
		  {
				if($_REQUEST['currentid']!='')
				{
					$actid=$_REQUEST['currentid'];
				}
				else
				{
					$actid=$_REQUEST['record'];
				}
				$parentid=$_REQUEST['parent_id'];
				if($_REQUEST['module'] != 'Fax')
				{
					if(!$parentid) {
						$parentid = $adb->getUniqueID($table_prefix.'_seactivityrel');
					}
					$mysql='insert into '.$table_prefix.'_seactivityrel values(?,?)';
					$adb->pquery($mysql, array($parentid, $actid));
				}
				else
				{
					$myids=explode("|",$parentid);  //2@71|
					foreach($myids as $myid) {	//crmv@55198
						$realid=explode("@",$myid);
						$mycrmid=$realid[0];
						//added to handle the relationship of emails with vtiger_users
						if($realid[1] == -1)
						{
							$del_q = 'delete from '.$table_prefix.'_salesmanactivityrel where smid=? and activityid=?';
							$adb->pquery($del_q,array($mycrmid, $actid));
							$mysql='insert into '.$table_prefix.'_salesmanactivityrel values(?,?)';
						}
						else
						{
							$del_q = 'delete from '.$table_prefix.'_seactivityrel where crmid=? and activityid=?';
							$adb->pquery($del_q,array($mycrmid, $actid));
							$mysql='insert into '.$table_prefix.'_seactivityrel values(?,?)';
						}
						$params = array($mycrmid, $actid);
						$adb->pquery($mysql, $params);
					}
				}
			}
			else
			{
				if(isset($this->column_fields['parent_id']) && $this->column_fields['parent_id'] != '')
				{
					$this->insertIntoEntityTable($table_prefix.'_seactivityrel', $module);
				}
				elseif($this->column_fields['parent_id']=='' && $insertion_mode=="edit")
				{
					$this->deleteRelation($table_prefix.'_seactivityrel');
				}
			}

			//Insert into cntactivity rel
			if(isset($this->column_fields['contact_id']) && $this->column_fields['contact_id'] != '')
			{
				$this->insertIntoEntityTable($table_prefix.'_cntactivityrel', $module);
			}
			elseif($this->column_fields['contact_id'] =='' && $insertion_mode=="edit")
			{
				$this->deleteRelation($table_prefix.'_cntactivityrel');
			}

			//Inserting into attachment
			$this->insertIntoAttachment($this->id,$module);

	}


	function insertIntoAttachment($id,$module)
	{
		global $log, $adb;
		global $table_prefix;
		$log->debug("Entering into insertIntoAttachment($id,$module) method.");

		$file_saved = false;

		//Added to send generated Invoice PDF with mail
		$pdfAttached = $_REQUEST['pdf_attachment'];
		//created Invoice pdf is attached with the mail
			if(isset($_REQUEST['pdf_attachment']) && $_REQUEST['pdf_attachment'] !='')
			{
				$file_saved = pdfAttachFax($this,$module,$pdfAttached,$id);
			}

		//This is to added to store the existing attachment id of the contact where we should delete this when we give new image
		foreach($_FILES as $fileindex => $files)
		{
			if($files['name'] != '' && $files['size'] > 0)
			{
				$files['original_name'] = $_REQUEST[$fileindex.'_hidden'];
				$file_saved = $this->uploadAndSaveFile($id,$module,$files);
			}
		}
		if($module == 'Emails' && isset($_REQUEST['att_id_list']) && $_REQUEST['att_id_list'] != '')
		{
			$att_lists = explode(";",$_REQUEST['att_id_list'],-1);
			$id_cnt = count($att_lists);
			if($id_cnt != 0)
			{
				for($i=0;$i<$id_cnt;$i++)
				{
					$sql_rel='insert into '.$table_prefix.'_seattachmentsrel values(?,?)';
		                        $adb->pquery($sql_rel, array($id, $att_lists[$i]));
				}
			}
		}
		$log->debug("Exiting from insertIntoAttachment($id,$module) method.");
	}

	/** Returns a list of the associated contacts
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
	*/
	function get_contacts($id)
	{
		global $log,$adb;
		$log->debug("Entering get_contacts(".$id.") method ...");
		global $mod_strings;
		global $app_strings;
		global $table_prefix;
		$focus = CRMEntity::getInstance('Contacts');

		$button = '';
		$returnset = '&return_module=Emails&return_action=CallRelatedList&return_id='.$id;

		$query = 'select '.$table_prefix.'_contactdetails.accountid, '.$table_prefix.'_contactdetails.contactid, '.$table_prefix.'_contactdetails.firstname,'.$table_prefix.'_contactdetails.lastname, '.$table_prefix.'_contactdetails.department, '.$table_prefix.'_contactdetails.title, '.$table_prefix.'_contactdetails.email, '.$table_prefix.'_contactdetails.phone, '.$table_prefix.'_contactdetails.emailoptout, '.$table_prefix.'_crmentity.crmid, '.$table_prefix.'_crmentity.smownerid, '.$table_prefix.'_crmentity.modifiedtime
			from '.$table_prefix.'_contactdetails
			inner join '.$table_prefix.'_contactscf on '.$table_prefix.'_contacts.contactid = '.$table_prefix.'_contactdetails.contactid
			inner join '.$table_prefix.'_cntactivityrel on '.$table_prefix.'_cntactivityrel.contactid='.$table_prefix.'_contactdetails.contactid
			inner join '.$table_prefix.'_crmentity on '.$table_prefix.'_crmentity.crmid = '.$table_prefix.'_contactdetails.contactid
			left join '.$table_prefix.'_contactgrouprelation on '.$table_prefix.'_contactdetails.contactid='.$table_prefix.'_contactgrouprelation.contactid
			left join '.$table_prefix.'_groups on '.$table_prefix.'_groups.groupname='.$table_prefix.'_contactgrouprelation.groupname
			where '.$table_prefix.'_cntactivityrel.activityid='.$adb->quote($id).' and '.$table_prefix.'_crmentity.deleted=0';
		$log->info("Contact Related List for Email is Displayed");
		$log->debug("Exiting get_contacts method ...");
		return GetRelatedList('Emails','Contacts',$focus,$query,$button,$returnset);
	}

	/** Returns the column name that needs to be sorted
	 * Portions created by vtigerCRM are Copyright (C) vtigerCRM.
	 * All Rights Reserved..
	 * Contributor(s): Mike Crowe
	*/

	function getSortOrder()
	{
		global $log;
		$log->debug("Entering getSortOrder() method ...");
		if(isset($_REQUEST['sorder']))
			$sorder = $_REQUEST['sorder'];
		else
			$sorder = (($_SESSION['EMAILS_SORT_ORDER'] != '')?($_SESSION['EMAILS_SORT_ORDER']):($this->default_sort_order));

		$log->debug("Exiting getSortOrder method ...");
		return $sorder;
	}

	/** Returns the order in which the records need to be sorted
	 * Portions created by vtigerCRM are Copyright (C) vtigerCRM.
	 * All Rights Reserved..
	 * Contributor(s): Mike Crowe
	*/

	function getOrderBy()
	{
		global $log;
		$log->debug("Entering getOrderBy() method ...");
		if (isset($_REQUEST['order_by']))
			$order_by = $_REQUEST['order_by'];
		else
			$order_by = (($_SESSION['EMAILS_ORDER_BY'] != '')?($_SESSION['EMAILS_ORDER_BY']):($this->default_order_by));

		$log->debug("Exiting getOrderBy method ...");
		return $order_by;
	}
	// Mike Crowe Mod --------------------------------------------------------

	/** Returns a list of the associated vtiger_users
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
	*/
	function get_users($id)
	{
		global $log;
		$log->debug("Entering get_users(".$id.") method ...");
		global $adb;
		global $mod_strings;
		global $app_strings;
		global $table_prefix;
		$id = $_REQUEST['record'];

		$query = 'SELECT '.$table_prefix.'_users.id, '.$table_prefix.'_users.first_name,'.$table_prefix.'_users.last_name, '.$table_prefix.'_users.user_name, '.$table_prefix.'_users.email1, '.$table_prefix.'_users.email2, '.$table_prefix.'_users.yahoo_id, '.$table_prefix.'_users.phone_home, '.$table_prefix.'_users.phone_work, '.$table_prefix.'_users.phone_mobile, '.$table_prefix.'_users.phone_other, '.$table_prefix.'_users.phone_fax from '.$table_prefix.'_users inner join '.$table_prefix.'_salesmanactivityrel on '.$table_prefix.'_salesmanactivityrel.smid='.$table_prefix.'_users.id and '.$table_prefix.'_salesmanactivityrel.activityid=?';
		$result=$adb->pquery($query, array($id));

		$noofrows = $adb->num_rows($result);
		$header [] = $app_strings['LBL_LIST_NAME'];

		$header []= $app_strings['LBL_LIST_USER_NAME'];

		$header []= $app_strings['LBL_EMAIL'];

		$header []= $app_strings['LBL_PHONE'];
		while($row = $adb->fetch_array($result))
		{

			global $current_user;

			$entries = Array();

			if(is_admin($current_user))
			{
				$entries[] = $row['last_name'].' '.$row['first_name'];
			}
			else
			{
				$entries[] = $row['last_name'].' '.$row['first_name'];
			}

			$entries[] = $row['user_name'];
			$entries[] = $row['email1'];
			if($email == '')        $email = $row['email2'];
			if($email == '')        $email = $row['yahoo_id'];

			$entries[] = $row['phone_home'];
			if($phone == '')        $phone = $row['phone_work'];
			if($phone == '')        $phone = $row['phone_mobile'];
			if($phone == '')        $phone = $row['phone_other'];
			if($phone == '')        $phone = $row['phone_fax'];

			//Adding Security Check for User

			$entries_list[] = $entries;
		}

		if($entries_list != '')
			$return_data = array("header"=>$header, "entries"=>$entries);
		$log->debug("Exiting get_users method ...");
		return $return_data;
	}

	/**
	  * Returns a list of the associated vtiger_attachments and vtiger_notes of the Email
	  */
	function get_attachments($id)
	{
		global $log,$adb;
		global $table_prefix;
		$log->debug("Entering get_attachments(".$id.") method ...");
		$query = "select ".$table_prefix."_notes.title,'Documents      '  ActivityType, ".$table_prefix."_notes.filename,
		".$table_prefix."_attachments.type  FileType,crm2.modifiedtime lastmodified,
		".$table_prefix."_seattachmentsrel.attachmentsid attachmentsid, ".$table_prefix."_notes.notesid crmid,
		".$table_prefix."_notes.notecontent description, ".$table_prefix."_users.user_name
		from ".$table_prefix."_notes
			inner join ".$table_prefix."_notescf on ".$table_prefix."_notescf.notesid = ".$table_prefix."_notes.notesid
			inner join ".$table_prefix."_senotesrel on ".$table_prefix."_senotesrel.notesid= ".$table_prefix."_notes.notesid
			inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid= ".$table_prefix."_senotesrel.crmid
			inner join ".$table_prefix."_crmentity crm2 on crm2.crmid=".$table_prefix."_notes.notesid and crm2.deleted=0
			left join ".$table_prefix."_seattachmentsrel  on ".$table_prefix."_seattachmentsrel.crmid =".$table_prefix."_notes.notesid
			left join ".$table_prefix."_attachments on ".$table_prefix."_seattachmentsrel.attachmentsid = ".$table_prefix."_attachments.attachmentsid
			inner join ".$table_prefix."_users on crm2.smcreatorid= ".$table_prefix."_users.id
		where ".$table_prefix."_crmentity.crmid=".$adb->quote($id);
		$query .= ' union all ';
		$query .= "select ".$table_prefix."_attachments.description title ,'Attachments'  ActivityType,
		".$table_prefix."_attachments.name filename, ".$table_prefix."_attachments.type FileType,crm2.modifiedtime lastmodified,
		".$table_prefix."_attachments.attachmentsid  attachmentsid,".$table_prefix."_seattachmentsrel.attachmentsid crmid,
		".$table_prefix."_attachments.description, ".$table_prefix."_users.user_name
		from ".$table_prefix."_attachments
			inner join ".$table_prefix."_seattachmentsrel on ".$table_prefix."_seattachmentsrel.attachmentsid= ".$table_prefix."_attachments.attachmentsid
			inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid= ".$table_prefix."_seattachmentsrel.crmid
			inner join ".$table_prefix."_crmentity crm2 on crm2.crmid=".$table_prefix."_attachments.attachmentsid
			inner join ".$table_prefix."_users on crm2.smcreatorid= ".$table_prefix."_users.id
		where ".$table_prefix."_crmentity.crmid=".$adb->quote($id);

		$log->info("Documents&Attachments Related List for Email is Displayed");
		$log->debug("Exiting get_attachments method ...");
		return getAttachmentsAndNotes('Emails',$query,$id);
	}

	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	function vtlib_handler($moduleName, $eventType) {

		require_once('include/utils/utils.php');
		global $adb;
 		global $table_prefix;
 		if($eventType == 'module.postinstall') {
			require_once('vtlib/Vtiger/Module.php');

			$moduleInstance = Vtiger_Module::getInstance($moduleName);

			$accModuleInstance = Vtiger_Module::getInstance('Accounts');
			$accModuleInstance->setRelatedList($moduleInstance,'Fax',array('add'),'get_faxes');
			Vtiger_Link::addLink($accModuleInstance->id, 'DETAILVIEWBASIC', 'TITLE_COMPOSE_FAX', "javascript:fnvshobj(this,'sendfax_cont');sendfax('\$MODULE\$','\$RECORD\$');", '', 1);

			$accModuleInstance = Vtiger_Module::getInstance('Contacts');
			$accModuleInstance->setRelatedList($moduleInstance,'Fax',array('add'),'get_faxes');
			Vtiger_Link::addLink($accModuleInstance->id, 'DETAILVIEWBASIC', 'TITLE_COMPOSE_FAX', "javascript:fnvshobj(this,'sendfax_cont');sendfax('\$MODULE\$','\$RECORD\$');", '', 1);

			$accModuleInstance = Vtiger_Module::getInstance('Leads');
			$accModuleInstance->setRelatedList($moduleInstance,'Fax',array('add'),'get_faxes');
			Vtiger_Link::addLink($accModuleInstance->id, 'DETAILVIEWBASIC', 'TITLE_COMPOSE_FAX', "javascript:fnvshobj(this,'sendfax_cont');sendfax('\$MODULE\$','\$RECORD\$');", '', 1);

			$accModuleInstance = Vtiger_Module::getInstance('Vendors');
			$accModuleInstance->setRelatedList($moduleInstance,'Fax',array('add'),'get_faxes');
			Vtiger_Link::addLink($accModuleInstance->id, 'DETAILVIEWBASIC', 'TITLE_COMPOSE_FAX', "javascript:fnvshobj(this,'sendfax_cont');sendfax('\$MODULE\$','\$RECORD\$');", '', 1);

			// Mark the module as Standard module
			$adb->pquery('UPDATE '.$table_prefix.'_tab SET customized=0,ownedby = 1 WHERE name=?', array($moduleName));

			//set fax through mail
			$adb->pquery('insert into tbl_s_faxservertype (server_type,presence) values (?,?)', array('fax_mail',1));

			//filters
			$id = $adb->getUniqueID($table_prefix.'_customview');
			$params = array($id,'All',1,0,'Fax',0,1);
			$sql = "INSERT INTO ".$table_prefix."_customview (cvid,viewname,setdefault,setmetrics,entitytype,status,userid) VALUES (".generateQuestionMarks($params).")";
			$adb->pquery($sql,$params);
			$params = array($id,0,$table_prefix.'_activity:subject:subject:Fax_Subject:V');
			$sql = "INSERT INTO ".$table_prefix."_cvcolumnlist VALUES (".generateQuestionMarks($params).")";
			$adb->pquery($sql,$params);
			$params = array($id,1,$table_prefix.'_emaildetails:to_email:saved_toid:Fax_To:V');
			$sql = "INSERT INTO ".$table_prefix."_cvcolumnlist VALUES (".generateQuestionMarks($params).")";
			$adb->pquery($sql,$params);
			$params = array($id,2,$table_prefix.'_activity:date_start:date_start:Fax_Date_Sent:D');
			$sql = "INSERT INTO ".$table_prefix."_cvcolumnlist VALUES (".generateQuestionMarks($params).")";
			$adb->pquery($sql,$params);

			//disable sharing
			$moduleInstance->disallowSharing();

			$moduleInstance->hide(array('hide_report'=>1)); // crmv@38798

		} else if($eventType == 'module.disabled') {
		} else if($eventType == 'module.enabled') {
		} else if($eventType == 'module.preuninstall') {
		// TODO Handle actions when this module is about to be deleted.
		} else if($eventType == 'module.preupdate') {
		// TODO Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
		// TODO Handle actions after this module is updated.
		}
 	}

	function getListQuery($module, $where='') {
		global $current_user;
		global $table_prefix;
		$query = "SELECT DISTINCT ".$table_prefix."_crmentity.crmid, ".$table_prefix."_crmentity.smownerid,
			".$table_prefix."_activity.activityid, ".$table_prefix."_activity.subject,
			".$table_prefix."_activity.date_start,
			".$table_prefix."_contactdetails.lastname, ".$table_prefix."_contactdetails.firstname,
			".$table_prefix."_contactdetails.contactid
			FROM ".$table_prefix."_activity
			INNER JOIN ".$table_prefix."_crmentity
				ON ".$table_prefix."_crmentity.crmid = ".$table_prefix."_activity.activityid
			LEFT JOIN ".$table_prefix."_users
				ON ".$table_prefix."_users.id = ".$table_prefix."_crmentity.smownerid
			LEFT JOIN ".$table_prefix."_seactivityrel
				ON ".$table_prefix."_seactivityrel.crmid = ".$table_prefix."_crmentity.crmid
			LEFT JOIN ".$table_prefix."_contactdetails
				ON ".$table_prefix."_contactdetails.contactid = ".$table_prefix."_seactivityrel.crmid
			LEFT JOIN ".$table_prefix."_cntactivityrel
				ON ".$table_prefix."_cntactivityrel.activityid = ".$table_prefix."_activity.activityid
				AND ".$table_prefix."_cntactivityrel.contactid = ".$table_prefix."_cntactivityrel.contactid
			LEFT JOIN ".$table_prefix."_groups
				ON ".$table_prefix."_groups.groupid = ".$table_prefix."_crmentity.smownerid
			LEFT JOIN ".$table_prefix."_salesmanactivityrel
				ON ".$table_prefix."_salesmanactivityrel.activityid = ".$table_prefix."_activity.activityid
			LEFT JOIN ".$table_prefix."_emaildetails
				ON ".$table_prefix."_emaildetails.emailid = ".$table_prefix."_activity.activityid";
		$query .= $this->getNonAdminAccessControlQuery($module,$current_user);
		$query .= "	WHERE ".$table_prefix."_activity.activitytype = 'Fax' and ".$table_prefix."_crmentity.deleted = 0 ".$where;
//		$query = $this->listQueryNonAdminChange($query, $module);
		return $query;
	}

}
/** Function to get the emailids for the given ids form the request parameters
 *  It returns an array which contains the mailids and the parentidlists
*/

function get_to_faxids($module)
{
	global $adb;
	global $table_prefix;
	if(isset($_REQUEST["field_lists"]) && $_REQUEST["field_lists"] != "")
	{
		$field_lists = $_REQUEST["field_lists"];
		if (is_string($field_lists)) $field_lists = explode(":", $field_lists);
		$query = 'select columnname,fieldid from '.$table_prefix.'_field where fieldid in('. generateQuestionMarks($field_lists) .')';
		$result = $adb->pquery($query, array($field_lists));
		$columns = Array();
		$idlists = '';
		$faxids = '';
		while($row = $adb->fetch_array($result))
    	{
			$columns[]=$row['columnname'];
			$fieldid[]=$row['fieldid'];
		}
		$columnlists = implode(',',$columns);
		//crmv@27096 //crmv@27917
		$idarray = getListViewCheck($module);
		if (empty($idarray)) {
			$idstring = $_REQUEST['idlist'];
		} else {
			$idstring = implode(':',$idarray);
		}
		//crmv@27096e //crmv@27917e
		$single_record = false;
		if(!strpos($idstring,':'))
		{
			$single_record = true;
		}
		$crmids = str_replace(':',',',$idstring);
		$crmids = explode(",", $crmids);
		switch($module)
		{
			case 'Leads':
				$query = 'select crmid,'.$adb->sql_concat(Array('firstname',"' '",'lastname')).' as entityname,'.$columnlists.' from '.$table_prefix.'_leaddetails
				inner join '.$table_prefix.'_leadaddress on '.$table_prefix.'_leadaddress.leadaddressid='.$table_prefix.'_leaddetails.leadid
				inner join '.$table_prefix.'_crmentity on '.$table_prefix.'_crmentity.crmid='.$table_prefix.'_leaddetails.leadid left join '.$table_prefix.'_leadscf on '.$table_prefix.'_leadscf.leadid = '.$table_prefix.'_leaddetails.leadid where '.$table_prefix.'_crmentity.deleted=0 and '.$table_prefix.'_crmentity.crmid in ('. generateQuestionMarks($crmids) .')';
				break;
			case 'Contacts':
				//email opt out funtionality works only when we do mass mailing.
//				if(!$single_record)
//				$concat_qry = '(((ltrim(vtiger_contactdetails.email) != \'\')  or (ltrim(vtiger_contactdetails.yahooid) != \'\')) and (vtiger_contactdetails.emailoptout != 1)) and ';
//				else
//				$concat_qry = '((ltrim(vtiger_contactdetails.email) != \'\')  or (ltrim(vtiger_contactdetails.yahooid) != \'\')) and ';
				$query = 'select crmid,'.$adb->sql_concat(Array('firstname',"' '",'lastname')).' as entityname,'.$columnlists.' from '.$table_prefix.'_contactdetails inner join '.$table_prefix.'_crmentity on '.$table_prefix.'_crmentity.crmid='.$table_prefix.'_contactdetails.contactid left join '.$table_prefix.'_contactscf on '.$table_prefix.'_contactscf.contactid = '.$table_prefix.'_contactdetails.contactid where '.$table_prefix.'_crmentity.deleted=0 and '.$table_prefix.'_crmentity.crmid in ('. generateQuestionMarks($crmids) .')';
				break;
			case 'Accounts':
				//added to work out email opt out functionality.
//				if(!$single_record)
//					$concat_qry = '(((ltrim(vtiger_account.email1) != \'\') or (ltrim(vtiger_account.email2) != \'\')) and (vtiger_account.emailoptout != 1)) and ';
//				else
//					$concat_qry = '((ltrim(vtiger_account.email1) != \'\') or (ltrim(vtiger_account.email2) != \'\')) and ';

				$query = 'select crmid,accountname as entityname,'.$columnlists.' from '.$table_prefix.'_account inner join '.$table_prefix.'_crmentity on '.$table_prefix.'_crmentity.crmid='.$table_prefix.'_account.accountid left join '.$table_prefix.'_accountscf on '.$table_prefix.'_accountscf.accountid = '.$table_prefix.'_account.accountid where '.$table_prefix.'_crmentity.deleted=0 and '.$table_prefix.'_crmentity.crmid in ('. generateQuestionMarks($crmids) .')';
				break;
			case 'Vendors':
				$query = 'select crmid,vendorname as entityname,'.$columnlists.' from '.$table_prefix.'_vendor inner join '.$table_prefix.'_crmentity on '.$table_prefix.'_crmentity.crmid='.$table_prefix.'_vendor.vendorid left join '.$table_prefix.'_vendorcf on '.$table_prefix.'_vendorcf.vendorid = '.$table_prefix.'_vendor.vendorid where '.$table_prefix.'_crmentity.deleted=0 and '.$table_prefix.'_crmentity.crmid in ('. generateQuestionMarks($crmids) .')';
				break;
		}
		$result = $adb->pquery($query, array($crmids));
		while($row = $adb->fetch_array($result))
		{
			$name = $row['entityname'];
			for($i=0;$i<count($columns);$i++)
			{
				if($row[$columns[$i]] != NULL && $row[$columns[$i]] !='')
				{
					$idlists .= $row['crmid'].'@'.$fieldid[$i].'|';
					$faxids .= $name.'<'.$row[$columns[$i]].'>,';
				}
			}
		}

		$return_data = Array('idlists'=>$idlists,'faxids'=>$faxids);
	}else
	{
		$return_data = Array('idlists'=>"",'faxids'=>"");
	}
	return $return_data;

}

//added for attach the generated pdf with email
function pdfAttachfax($obj,$module,$file_name,$id)
{
	global $log;
	$log->debug("Entering into pdfAttach() method.");

	global $adb, $current_user;
	global $upload_badext;
	global $table_prefix;
	$date_var = date('Y-m-d H:i:s'); //crmv@69690

	$ownerid = $obj->column_fields['assigned_user_id'];
	if(!isset($ownerid) || $ownerid=='')
		$ownerid = $current_user->id;

	$current_id = $adb->getUniqueID($table_prefix."_crmentity");

	$upload_file_path = decideFilePath();

	//Copy the file from temporary directory into storage directory for upload
	$status = copy("storage/".$file_name,$upload_file_path.$current_id."_".$file_name);
	//Check wheather the copy process is completed successfully or not. if failed no need to put entry in attachment table
	if($status)
	{
		$query1 = "insert into ".$table_prefix."_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values(?,?,?,?,?,?,?)";
		$params1 = array($current_id, $current_user->id, $ownerid, $module." Attachment", $obj->column_fields['description'], $adb->formatDate($date_var, true), $adb->formatDate($date_var, true));
		$adb->pquery($query1, $params1);

		$query2="insert into ".$table_prefix."_attachments(attachmentsid, name, description, type, path) values(?,?,?,?,?)";
		$params2 = array($current_id, $file_name, $obj->column_fields['description'], 'pdf', $upload_file_path);
		$result=$adb->pquery($query2, $params2);

		$query3='insert into '.$table_prefix.'_seattachmentsrel values(?,?)';
		$adb->pquery($query3, array($id, $current_id));

		return true;
	}
	else
	{
		$log->debug("pdf not attached");
		return false;
	}
}
?>

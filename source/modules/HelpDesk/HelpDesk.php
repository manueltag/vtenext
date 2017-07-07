<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of txhe License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
include_once('config.php');
require_once('include/logging.php');
require_once('include/utils/utils.php');
require_once('user_privileges/default_module_view.php');

class HelpDesk extends CRMEntity {
	var $log;
	var $db;
	var $table_name;
	var $table_index= 'ticketid';
	var $tab_name = Array();
	var $tab_name_index = Array();
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array();

	var $column_fields = Array();
	//Pavani: Assign value to entity_table
        var $entity_table;

	var $sortby_fields = Array('title','status','priority','crmid','firstname','smownerid','parent_id'); //crmv@7214s

	var $list_fields = Array(
					//Module Sequence Numbering
					//'Ticket ID'=>Array('crmentity'=>'crmid'),
					'Ticket No'=>Array('troubletickets'=>'ticket_no'),
					// END
					'Subject'=>Array('troubletickets'=>'title'),
					'Related to'=>Array('troubletickets'=>'parent_id'),
					'Status'=>Array('troubletickets'=>'status'),
					'Priority'=>Array('troubletickets'=>'priority'),
					'Assigned To'=>Array('crmentity'=>'smownerid')
				);

	var $list_fields_name = Array(
					//'Ticket ID'=>'',
					'Ticket No'=>'ticket_no',
					'Subject'=>'ticket_title',
					'Related to'=>'parent_id',
					'Status'=>'ticketstatus',
					'Priority'=>'ticketpriorities',
					'Assigned To'=>'assigned_user_id'
				     );

	var $list_link_field= 'ticket_title';

	var $range_fields = Array(
				        'ticketid',
					'title',
			        	'firstname',
				        'lastname',
			        	'parent_id',
			        	'productid',
			        	'productname',
			        	'priority',
			        	'severity',
				        'status',
			        	'category',
					'description',
					'solution',
					'modifiedtime',
					'createdtime'
				);
	var $search_fields = Array();
	var $search_fields_name = Array(
		'Ticket No' => 'ticket_no',
		'Title'=>'ticket_title',
		);
	//Specify Required fields
    var $required_fields =  array();

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('assigned_user_id', 'createdtime', 'modifiedtime', 'ticket_title');

    //Added these variables which are used as default order by and sortorder in ListView
    var $default_order_by = 'modifiedtime';
    var $default_sort_order = 'DESC';
	//crmv@10759
	var $search_base_field = 'ticket_title';
	//crmv@10759 e

	//var $groupTable = Array('vtiger_ticketgrouprelation','ticketid');

	//crmv@2043m
	var $waitForResponseStatus = 'Wait For Response';
	var $answeredByCustomerStatus = 'Open';
	//crmv@2043me

	/**	Constructor which will set the column_fields in this object
	 */
	function HelpDesk()
	{
		global $table_prefix;
		parent::__construct(); // crmv@37004
		$this->table_name = $table_prefix."_troubletickets";
		$this->tab_name = Array($table_prefix.'_crmentity',$table_prefix.'_troubletickets',$table_prefix.'_ticketcf');
		$this->tab_name_index = Array($table_prefix.'_crmentity'=>'crmid',$table_prefix.'_troubletickets'=>'ticketid',$table_prefix.'_ticketcf'=>'ticketid',$table_prefix.'_ticketcomments'=>'ticketid');
		$this->customFieldTable = Array($table_prefix.'_ticketcf', 'ticketid');
	    $this->entity_table = $table_prefix."_crmentity";
        $this->search_fields = Array(
		//'Ticket ID' => Array($table_prefix.'_crmentity'=>'crmid'),
		'Ticket No' =>Array($table_prefix.'_troubletickets'=>'ticket_no'),
		'Title' => Array($table_prefix.'_troubletickets'=>'title')
		);
		$this->log =LoggerManager::getLogger('helpdesk');
		$this->log->debug("Entering HelpDesk() method ...");
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('HelpDesk');
		$this->log->debug("Exiting HelpDesk method ...");
	}

	function save_module($module)
	{
		//crmv@27146
		//Inserting into Ticket Comment Table
		if(isset($_REQUEST['action']) && $_REQUEST['action'] != 'MassEditSave'){
			$this->insertIntoTicketCommentTable($table_prefix."_ticketcomments",'HelpDesk');
		}
		//Inserting into vtiger_attachments
		//$this->insertIntoAttachment($this->id,'HelpDesk');
		//crmv@27146e

		/* commento altrimenti passa per la save_related_module sia qui che nella CRMEntity
		$return_action = $_REQUEST['return_action'];
		$for_module = $_REQUEST['return_module'];
		$for_crmid  = $_REQUEST['return_id'];
		if ($return_action && $for_module && $for_crmid) {
			if ($for_module != 'Accounts' && $for_module != 'Contacts' && $for_module != 'Products') {
				$on_focus = CRMEntity::getInstance($for_module);
				$on_focus->save_related_module($for_module, $for_crmid, $module, $this->id);
			}
		}
		*/
	}

	/** Function to insert values in vtiger_ticketcomments  for the specified tablename and  module
  	  * @param $table_name -- table name:: Type varchar
  	  * @param $module -- module:: Type varchar
 	 */
	function insertIntoTicketCommentTable($table_name, $module)
	{
		global $log;
		$log->info("in insertIntoTicketCommentTable  ".$table_name."    module is  ".$module);
       	global $adb;
       	global $table_prefix;
		global $current_user;

        $current_time = $adb->formatDate(date('Y-m-d H:i:s'), true);
		if($this->column_fields['assigned_user_id'] != '')
			$ownertype = 'user';
		else
			$ownertype = 'customer';

		if($this->column_fields['comments'] != '')
			$comment = $this->column_fields['comments'];
		else
			$comment = $_REQUEST['comments'];

		if($comment != '')
		{
			$comid = $adb->getUniqueID($table_prefix.'_ticketcomments');
			$sql = "insert into ".$table_prefix."_ticketcomments (commentid,ticketid,ownerid,ownertype,createdtime,comments) values(?,?,?,?,?,".$adb->getEmptyClob(true).")";
			$params = array($comid, $this->id, $current_user->id, $ownertype, $current_time);
			$adb->pquery($sql, $params);
			$adb->updateClob($table_prefix.'_ticketcomments','comments',"commentid=$comid",from_html($comment));
			$this->lastInsertedCommentId = $comid; // crmv@49398
		}
	}


	/**
	 *      This function is used to add the vtiger_at".$table_prefix."nts. This will call the function uploadAndSaveFile which will upload the attachment into the server and save that attachment information in the database.
	 *      @param int $id  - entity id to which the vtiger_files to be uploaded
	 *      @param string $module  - the current module name
	*/
	function insertIntoAttachment($id,$module)
	{
		global $log, $adb;
		$log->debug("Entering into insertIntoAttachment($id,$module) method.");

		$file_saved = false;

		foreach($_FILES as $fileindex => $files)
		{
			if($files['name'] != '' && $files['size'] > 0)
			{
				$files['original_name'] = vtlib_purify($_REQUEST[$fileindex.'_hidden']);
				$file_saved = $this->uploadAndSaveFile($id,$module,$files);
			}
		}

		$log->debug("Exiting from insertIntoAttachment($id,$module) method.");
	}

	// crmv@49398
	/**	Function to get the ticket comments as a array
	 *	@param  int   $ticketid - ticketid
	 *	@return array $output - array(
						[$i][comments]    => comments
						[$i][owner]       => name of the user or customer who made the comment
						[$i][createdtime] => the comment created time
					     )
				where $i = 0,1,..n which are all made for the ticket
	**/
	function get_ticket_comments_list($ticketid) {
		global $log, $adb, $table_prefix;

		$log->debug("Entering get_ticket_comments_list(".$ticketid.") method ...");

		$output = array();
		$sql = "select * from {$table_prefix}_ticketcomments where ticketid=? order by createdtime DESC";
		$result = $this->db->pquery($sql, array($ticketid));
		$noofrows = $this->db->num_rows($result);

		for ($i=0; $i<$noofrows; ++$i) {
			$row = $this->db->FetchByAssoc($result);
			// crmv@34559
			if ($row['ownertype'] == 'user') {
				$name = getUserName($row['ownerid']);
			} elseif ($row['ownertype'] == 'customer') {
				$name = getContactName($row['ownerid']);
			} else {
				$name = '';
			}
			// crmv@34559e
			$row['owner'] = $name;
			$row['comments'] = nl2br($row['comments']);
			$output[] = $row;
		}

		$log->debug("Exiting get_ticket_comments_list method ...");
		return $output;
	}
	// crmv@49398e

	/**	Function to form the query which will give the list of tickets based on customername and id ie., contactname and contactid
	 *	@param  string $user_name - name of the customer ie., contact name
	 *	@param  int    $id	 - contact id
	 * 	@return array  - return an array which will be returned from the function process_list_query
	**/
	function get_user_tickets_list($user_name,$id,$where='',$match='')
	{
		global $log;
		global $table_prefix;
		$log->debug("Entering get_user_tickets_list(".$user_name.",".$id.",".$where.",".$match.") method ...");

		$this->db->println("where ==> ".$where);

		$query = "select ".$table_prefix."_crmentity.crmid, ".$table_prefix."_troubletickets.*, ".$table_prefix."_crmentity.description, ".$table_prefix."_crmentity.smownerid, ".$table_prefix."_crmentity.createdtime, ".$table_prefix."_crmentity.modifiedtime, ".$table_prefix."_contactdetails.firstname, ".$table_prefix."_contactdetails.lastname, ".$table_prefix."_products.productid, ".$table_prefix."_products.productname, ".$table_prefix."_ticketcf.*
			from ".$table_prefix."_troubletickets
			inner join ".$table_prefix."_ticketcf on ".$table_prefix."_ticketcf.ticketid = ".$table_prefix."_troubletickets.ticketid
			inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid=".$table_prefix."_troubletickets.ticketid
			left join ".$table_prefix."_contactdetails on ".$table_prefix."_troubletickets.parent_id=".$table_prefix."_contactdetails.contactid
			left join ".$table_prefix."_products on ".$table_prefix."_products.productid = ".$table_prefix."_troubletickets.product_id
			left join ".$table_prefix."_users on ".$table_prefix."_crmentity.smownerid=".$table_prefix."_users.id
			where ".$table_prefix."_crmentity.deleted=0 and ".$table_prefix."_contactdetails.email='".$user_name."' and ".$table_prefix."_troubletickets.parent_id = '".$id."'";

		if(trim($where) != '')
		{
			if($match == 'all' || $match == '')
			{
				$join = " and ";
			}
			elseif($match == 'any')
			{
				$join = " or ";
			}
			$where = explode("&&&",$where);
			$count = count($where);
			$count --;
			$where_conditions = "";
			foreach($where as $key => $value)
			{
				$this->db->println('key : '.$key.'...........value : '.$value);
				$val = explode(" = ",$value);
				$this->db->println('val0 : '.$val[0].'...........val1 : '.$val[1]);
				if($val[0] == $table_prefix.'_troubletickets.title')
				{
					$where_conditions .= $val[0]."  ".$val[1];
					if($count != $key) 	$where_conditions .= $join;
				}
				elseif($val[1] != '' && $val[1] != 'Any')
				{
					$where_conditions .= $val[0]." = ".$val[1];
					if($count != $key)	$where_conditions .= $join;
				}
			}
			if($where_conditions != '')
				$where_conditions = " and ( ".$where_conditions." ) ";

			$query .= $where_conditions;
			$this->db->println("where condition for customer portal tickets search : ".$where_conditions);
		}

		$query .= " order by ".$table_prefix."_crmentity.crmid desc";
		$log->debug("Exiting get_user_tickets_list method ...");
		return $this->process_list_query($query);
	}

	/**	Function to process the list query and return the result with number of rows
	 *	@param  string $query - query
	 *	@return array  $response - array(	list           => array(
											$i => array(key => val)
									       ),
							row_count      => '',
							next_offset    => '',
							previous_offset	=>''
						)
		where $i=0,1,..n & key = ticketid, title, firstname, ..etc(range_fields) & val = value of the key from db retrieved row
	**/
	function process_list_query($query)
	{
		global $log;
		$log->debug("Entering process_list_query(".$query.") method ...");

   		$result =& $this->db->query($query,true,"Error retrieving $this->object_name list: ");
		$list = Array();
	        $rows_found =  $this->db->getRowCount($result);
        	if($rows_found != 0)
	        {
			$ticket = Array();
			for($index = 0 , $row = $this->db->fetchByAssoc($result, $index); $row && $index <$rows_found;$index++, $row = $this->db->fetchByAssoc($result, $index))
			{
		                foreach($this->range_fields as $columnName)
                		{
		                	if (isset($row[$columnName]))
					{
			                	$ticket[$columnName] = $row[$columnName];
                    			}
		                       	else
				        {
		                        	$ticket[$columnName] = "";
			                }
	     			}
    		                $list[] = $ticket;
                	}
        	}

		$response = Array();
	        $response['list'] = $list;
        	$response['row_count'] = $rows_found;
	        $response['next_offset'] = $next_offset;
        	$response['previous_offset'] = $previous_offset;

		$log->debug("Exiting process_list_query method ...");
	        return $response;
	}

	/**	Function to get the HelpDesk field labels in caps letters without space
	 *	@return array $mergeflds - array(	key => val	)    where   key=0,1,2..n & val = ASSIGNEDTO,RELATEDTO, .,etc
	**/
	function getColumnNames_Hd()
	{
		global $log,$current_user;
		global $table_prefix;
		$log->debug("Entering getColumnNames_Hd() method ...");
		require('user_privileges/requireUserPrivileges.php'); // crmv@39110
		if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0)
		{
			$sql1 = "select fieldlabel from ".$table_prefix."_field where tabid=13 and block <> 30 and ".$table_prefix."_field.uitype <> '61' and ".$table_prefix."_field.presence in (0,2)";
			$params1 = array();
		}else
		{
			$profileList = getCurrentUserProfileList();
			$sql1 = "select ".$table_prefix."_field.fieldid,fieldlabel from ".$table_prefix."_field inner join ".$table_prefix."_def_org_field on ".$table_prefix."_def_org_field.fieldid=".$table_prefix."_field.fieldid where ".$table_prefix."_field.tabid=13 and ".$table_prefix."_field.block <> 30 and ".$table_prefix."_field.uitype <> '61' and ".$table_prefix."_field.displaytype in (1,2,3,4) and ".$table_prefix."_def_org_field.visible=0 and ".$table_prefix."_field.presence in (0,2)";
			$params1 = array();
		    $sql1.=" AND EXISTS(SELECT * FROM ".$table_prefix."_profile2field WHERE ".$table_prefix."_profile2field.fieldid = ".$table_prefix."_field.fieldid ";
		        if (count($profileList) > 0) {
			  	 	$sql1.=" AND ".$table_prefix."_profile2field.profileid IN (". generateQuestionMarks($profileList) .") ";
			  	 	array_push($params1, $profileList);
			}
		    $sql1.=" AND ".$table_prefix."_profile2field.visible = 0) ";
		}
		$result = $this->db->pquery($sql1, $params1);
		$numRows = $this->db->num_rows($result);
		for($i=0; $i < $numRows;$i++)
		{
			$custom_fields[$i] = $this->db->query_result($result,$i,"fieldlabel");
			$custom_fields[$i] = str_replace(" ","",$custom_fields[$i]);
			$custom_fields[$i] = strtoupper($custom_fields[$i]);
		}
		$mergeflds = $custom_fields;
		$log->debug("Exiting getColumnNames_Hd method ...");
		return $mergeflds;
	}

	/**     Function to get the list of comments for the given ticket id
	 *      @param  int  $ticketid - Ticket id
	 *      @return list $list - return the list of comments and comment informations as a html output where as these comments and comments informations will be formed in div tag.
	**/
	function getCommentInformation($ticketid)
	{
		global $log;
		global $table_prefix;
		$log->debug("Entering getCommentInformation(".$ticketid.") method ...");
		global $adb;
		global $mod_strings, $default_charset;
		$sql = "select * from ".$table_prefix."_ticketcomments where ticketid=?";
		$result = $adb->pquery($sql, array($ticketid));
		$noofrows = $adb->num_rows($result);

		//In ajax save we should not add this div
		if($_REQUEST['fldName'] != 'comments')
		{
			$list .= '<div id="comments_div" class="wrap-content" style="overflow:auto;max-height:200px;width:100%;">'; // crmv@113422
			$enddiv = '</div>';
		}
		//crmv@3126m
		static $ownerData = Array();
		for($i=0;$i<$noofrows;$i++)
		{
			if($adb->query_result($result,$i,'comments') != '')
			{
				$ownerid = $adb->query_result($result,$i,'ownerid');
				if($adb->query_result($result,$i,'ownertype') == 'user'){
					if(!isset($ownerData['fullname'][$ownerid])) {
						$ownerData['fullname'][$ownerid] = getUserFullName($ownerid);
					}
					$avatar = getUserAvatarImg($ownerid);
					$float ="right";
					$textalign="left";
					$mainstyle = "class=\"dataField\" style=\"float:left;margin-right: auto;margin-bottom: 5px;clear: both;\"";
				}
				else{
					if(!isset($ownerData['avatar'][$ownerid])) {
						$ownerData['avatar'][$ownerid] = vtiger_imageurl('portal_avatar.png',$theme); //todo image from contact
					}
					if(!isset($ownerData['fullname'][$ownerid])) {
						$ownerData['fullname'][$ownerid] = getContactName($ownerid);
					}					
					$avatar = "<div style=\"float:right;height:100%\">
						<img title=\"{$ownerData['fullname'][$ownerid]}\" alt=\"{$ownerData['fullname'][$ownerid]}\" src=\"{$ownerData['avatar'][$ownerid]}\" class=\"userAvatar\">
					</div>";
					$float ="left";
					$textalign="right";
					$mainstyle = "class=\"dataField\" style=\"float:right;margin-left: auto;margin-bottom: 5px;clear: both;\"";				
				}
				$list.="<div $mainstyle>";
				//this div is to display the comment
				$comment = $adb->query_result($result,$i,'comments');
				// Asha: Fix for ticket #4478 . Need to escape html tags during ajax save.
				if($_REQUEST['action'] == 'HelpDeskAjax') {
					$comment = htmlentities($comment, ENT_QUOTES, $default_charset);
				}
				$list.="$avatar";
				$list.="<div style=\"float:{$float}\">";
				$list .= '<div valign="top" class="dataField" style="text-align:'.$textalign.';padding-top:0px;">';
				$list.="<b>{$ownerData['fullname'][$ownerid]}</b><br>";
				$list .= make_clickable(nl2br($comment));

				$list .= '</div>';

				//this div is to display the author and time
				$list .= '<div valign="top" style="padding-bottom:5px;text-align:'.$textalign.';" class="dataLabel">';
				$createdtime = $adb->query_result($result,$i,'createdtime');
				if (isModuleInstalled('ModNotifications')) {
					require_once('modules/ModNotifications/models/Comments.php');
					$model = new ModNotifications_CommentsModel(array('createdtime'=>$createdtime));
					$list.=" <a href=\"javascript:;\" title=\"{$model->timestamp()}\" style=\"color: gray; text-decoration: none;\">{$model->timestampAgo()}</a>";
				}
				else{
					$list .= ' on '.$createdtime.' &nbsp;';
				}

				$list .= '</div>';
				$list.="</div>";
				$list.="</div>";
			}
		}
		//crmv@3126me
		$list .= $enddiv;

		$log->debug("Exiting getCommentInformation method ...");
		return $list;
	}

	/**     Function to get the Customer Name who has made comment to the ticket from the customer portal
	 *      @param  int    $id   - Ticket id
	 *      @return string $customername - The contact name
	**/
	function getCustomerName($id)
	{
		global $log;
		$log->debug("Entering getCustomerName(".$id.") method ...");
        	global $adb;
        	global $table_prefix;
	        $sql = "select * from ".$table_prefix."_portalinfo inner join ".$table_prefix."_troubletickets on ".$table_prefix."_troubletickets.parent_id = ".$table_prefix."_portalinfo.id where ".$table_prefix."_troubletickets.ticketid=?";
        	$result = $adb->pquery($sql, array($id));
	        $customername = $adb->query_result($result,0,'user_name');
		$log->debug("Exiting getCustomerName method ...");
        	return $customername;
	}
	//Pavani: Function to create, export query for helpdesk module
	/** Function to export the ticket records in CSV Format
	 * @param reference variable - where condition is passed when the query is executed
	 * Returns Export Tickets Query.
	 */
	function create_export_query($where,$oCustomView,$viewId)	//crmv@31775
	{
		global $log;
		global $current_user;
		global $table_prefix;
		$log->debug("Entering create_export_query(".$where.") method ...");

		include_once("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("HelpDesk", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);
		//Ticket changes--5198
		//crmv@15981
		$fields_list = 	str_replace(','.$table_prefix.'_ticketcomments.comments as "Add Comment"',' ',$fields_list);
		//crmv@15981 end

		//crmv@92596 - leads
		$query = "SELECT $fields_list,case when (".$table_prefix."_users.user_name is not null) then ".$table_prefix."_users.user_name else ".$table_prefix."_groups.groupname end as user_name
            FROM ".$this->entity_table. "
			INNER JOIN ".$table_prefix."_troubletickets
				ON ".$table_prefix."_troubletickets.ticketid =".$table_prefix."_crmentity.crmid
			LEFT JOIN ".$table_prefix."_crmentity ".$table_prefix."_crmentityRelatedTo
				ON ".$table_prefix."_crmentityRelatedTo.crmid = ".$table_prefix."_troubletickets.parent_id
			LEFT JOIN ".$table_prefix."_account
				ON ".$table_prefix."_account.accountid = ".$table_prefix."_troubletickets.parent_id
			LEFT JOIN ".$table_prefix."_contactdetails
				ON ".$table_prefix."_contactdetails.contactid = ".$table_prefix."_troubletickets.parent_id
			LEFT JOIN ".$table_prefix."_contactdetails ".substr($table_prefix.'_contactdetailsparent_id',0,29)."
				ON ".substr($table_prefix.'_contactdetailsparent_id',0,29).".contactid = ".$table_prefix."_troubletickets.parent_id
			LEFT JOIN ".$table_prefix."_leaddetails
				ON ".$table_prefix."_leaddetails.leadid = ".$table_prefix."_troubletickets.parent_id
			LEFT JOIN ".$table_prefix."_leaddetails ".substr($table_prefix.'_leaddetailsparent_id',0,29)."
				ON ".substr($table_prefix.'_leaddetailsparent_id',0,29).".leadid = ".$table_prefix."_troubletickets.parent_id
			LEFT JOIN ".$table_prefix."_ticketcf
				ON ".$table_prefix."_ticketcf.ticketid=".$table_prefix."_troubletickets.ticketid
			LEFT JOIN ".$table_prefix."_groups
				ON ".$table_prefix."_groups.groupid = ".$table_prefix."_crmentity.smownerid
			LEFT JOIN ".$table_prefix."_users
				ON ".$table_prefix."_users.id=".$table_prefix."_crmentity.smownerid and ".$table_prefix."_users.status='Active'
			LEFT JOIN ".$table_prefix."_seattachmentsrel
				ON ".$table_prefix."_seattachmentsrel.crmid =".$table_prefix."_troubletickets.ticketid
			LEFT JOIN ".$table_prefix."_attachments
				ON ".$table_prefix."_attachments.attachmentsid=".$table_prefix."_seattachmentsrel.attachmentsid
			LEFT JOIN ".$table_prefix."_products
				ON ".$table_prefix."_products.productid=".$table_prefix."_troubletickets.product_id";
		//crmv@92596e

		//crmv@31775
		$reportFilter = $oCustomView->getReportFilter($viewId);
		if ($reportFilter) {
			$tableNameTmp = $oCustomView->getReportFilterTableName($reportFilter,$current_user->id);
			$query .= " INNER JOIN $tableNameTmp ON $tableNameTmp.id = {$table_prefix}_crmentity.crmid";
		}
		//crmv@31775e

		$query .= $this->getNonAdminAccessControlQuery('HelpDesk',$current_user);

		$where_auto = " ".$table_prefix."_crmentity.deleted=0";

		if($where != '') $query .= " WHERE ($where) AND $where_auto";
		else $query .= " WHERE $where_auto";

		$query = $this->listQueryNonAdminChange($query, 'HelpDesk');
		$log->debug("Exiting create_export_query method ...");
		return $query;
	}

	/**
	 * Move the related records of the specified list of id's to the given record.
	 * @param String This module name
	 * @param Array List of Entity Id's from which related records need to be transfered
	 * @param Integer Id of the the Record to which the related records are to be moved
	 */
	function transferRelatedRecords($module, $transferEntityIds, $entityId) {
		global $adb,$log;
		global $table_prefix;
		$log->debug("Entering function transferRelatedRecords ($module, $transferEntityIds, $entityId)");

		$rel_table_arr = Array("Activities"=>$table_prefix."_seactivityrel","Attachments"=>$table_prefix."_seattachmentsrel","Documents"=>$table_prefix."_senotesrel");

		$tbl_field_arr = Array($table_prefix."_seactivityrel"=>"activityid",$table_prefix."_seattachmentsrel"=>"attachmentsid",$table_prefix."_senotesrel"=>"notesid");

		$entity_tbl_field_arr = Array($table_prefix."_seactivityrel"=>"crmid",$table_prefix."_seattachmentsrel"=>"crmid",$table_prefix."_senotesrel"=>"crmid");

		foreach($transferEntityIds as $transferId) {
			foreach($rel_table_arr as $rel_module=>$rel_table) {
				$id_field = $tbl_field_arr[$rel_table];
				$entity_id_field = $entity_tbl_field_arr[$rel_table];
				// IN clause to avoid duplicate entries
				$sel_result =  $adb->pquery("select $id_field from $rel_table where $entity_id_field=? " .
						" and $id_field not in (select $id_field from $rel_table where $entity_id_field=?)",
						array($transferId,$entityId));
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0) {
					for($i=0;$i<$res_cnt;$i++) {
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						$adb->pquery("update $rel_table set $entity_id_field=? where $entity_id_field=? and $id_field=?",
							array($entityId,$transferId,$id_field_value));
					}

				}
			}
		}
		//crmv@15526
		parent::transferRelatedRecords($module, $transferEntityIds, $entityId);
		//crmv@15526 end
		$log->debug("Exiting transferRelatedRecords...");
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule){
		global $table_prefix;
		$query = $this->getRelationQuery($module,$secmodule,$table_prefix."_troubletickets","ticketid");
		$query .=" left join ".$table_prefix."_ticketcf on ".$table_prefix."_ticketcf.ticketid = ".$table_prefix."_troubletickets.ticketid
				left join ".$table_prefix."_crmentity ".$table_prefix."_crmentityRelHelpDesk on ".$table_prefix."_crmentityRelHelpDesk.crmid = ".$table_prefix."_troubletickets.parent_id
				left join ".$table_prefix."_account ".$table_prefix."_accountRelHelpDesk on ".$table_prefix."_accountRelHelpDesk.accountid=".$table_prefix."_crmentityRelHelpDesk.crmid
				left join ".$table_prefix."_contactdetails ".substr($table_prefix.'_contactdetailsRelHelpDesk',0,29)." on ".substr($table_prefix.'_contactdetailsRelHelpDesk',0,29).".contactid= ".$table_prefix."_crmentityRelHelpDesk.crmid
				left join ".$table_prefix."_products ".$table_prefix."_productsRel on ".$table_prefix."_productsRel.productid = ".$table_prefix."_troubletickets.product_id
				left join ".$table_prefix."_groups ".$table_prefix."_groupsHelpDesk on ".$table_prefix."_groupsHelpDesk.groupid = ".$table_prefix."_crmentityHelpDesk.smownerid
				left join ".$table_prefix."_users ".$table_prefix."_usersHelpDesk on ".$table_prefix."_usersHelpDesk.id = ".$table_prefix."_crmentityHelpDesk.smownerid";
		return $query;
	}

	/*
	 * Function to get the relation tables for related modules
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	function setRelationTables($secmodule){
		global $table_prefix;
		$rel_tables = array (
			"Calendar" => array($table_prefix."_seactivityrel"=>array("crmid","activityid"),$table_prefix."_troubletickets"=>"ticketid"),
			"Documents" => array($table_prefix."_senotesrel"=>array("crmid","notesid"),$table_prefix."_troubletickets"=>"ticketid"),
			"Products" => array($table_prefix."_troubletickets"=>array("ticketid","product_id")),
			"Services" => array($table_prefix."_crmentityrel"=>array("crmid","relcrmid"),$table_prefix."_troubletickets"=>"ticketid"),
		);
		return $rel_tables[$secmodule];
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		global $table_prefix;
		if(empty($return_module) || empty($return_id)) return;

		if($return_module == 'Contacts' || $return_module == 'Accounts') {
			$sql = 'UPDATE '.$table_prefix.'_troubletickets SET parent_id=0 WHERE ticketid=?';
			$this->db->pquery($sql, array($id));
			$se_sql= 'DELETE FROM '.$table_prefix.'_seticketsrel WHERE ticketid=?';
			$this->db->pquery($se_sql, array($id));
		} elseif($return_module == 'Products') {
			$sql = 'UPDATE '.$table_prefix.'_troubletickets SET product_id=0 WHERE ticketid=?';
			$this->db->pquery($sql, array($id));
		//crmv@112084
		} elseif($return_module == 'ServiceContracts'){
			parent::unlinkRelationship($id, $return_module, $return_id);
			$servicecontracts = CRMEntity::getInstance('ServiceContracts');
			$servicecontracts->updateServiceContractState($return_id);
		//crmv@112084e
		} else {
			$sql = 'DELETE FROM '.$table_prefix.'_crmentityrel WHERE (crmid=? AND relmodule=? AND relcrmid=?) OR (relcrmid=? AND module=? AND crmid=?)';
			$params = array($id, $return_module, $return_id, $id, $return_module, $return_id);
			$this->db->pquery($sql, $params);
		}
		$this->db->pquery("UPDATE {$table_prefix}_crmentity SET modifiedtime = ? WHERE crmid IN (?,?)", array($this->db->formatDate(date('Y-m-d H:i:s'), true), $id, $return_id)); // crmv@49398 crmv@69690
	}
	function get_timecards($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		global $table_prefix;
		$log->debug("Entering get_timecards(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		$other = CRMEntity::getInstance($related_module);
        vtlib_setup_modulevars($related_module, $other);
		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';
		if($actions) {
			$button .= $this->get_related_buttons($this_module, $id, $related_module, $actions); // crmv@43864
		}

		$query = "SELECT case when (".$table_prefix."_users.user_name is not null) then ".$table_prefix."_users.user_name else ".$table_prefix."_groups.groupname end as user_name," .
					" ".$table_prefix."_timecards.*, ".$table_prefix."_troubletickets.ticket_no, ".$table_prefix."_troubletickets.parent_id, ".$table_prefix."_troubletickets.priority," .
					"  ".$table_prefix."_troubletickets.severity, ".$table_prefix."_troubletickets.status, ".$table_prefix."_troubletickets.category, ".$table_prefix."_troubletickets.title," .
					"  ".$table_prefix."_products.*, ".$table_prefix."_crmentity.crmid, ".$table_prefix."_crmentity.smownerid, ".$table_prefix."_crmentity.modifiedtime" .
					" from ".$table_prefix."_timecards" .
					" inner join ".$table_prefix."_timecardscf on ".$table_prefix."_timecardscf.timecardsid = ".$table_prefix."_timecards.timecardsid" .
					" inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid=".$table_prefix."_timecards.timecardsid" .
					" inner join ".$table_prefix."_troubletickets on ".$table_prefix."_troubletickets.ticketid = ".$table_prefix."_timecards.ticket_id " .
					" left join ".$table_prefix."_products on ".$table_prefix."_products.productid = ".$table_prefix."_timecards.product_id" .
					" left join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_crmentity.smownerid" .
					" left join ".$table_prefix."_groups on ".$table_prefix."_groups.groupid=".$table_prefix."_crmentity.smownerid" .
					" where ".$table_prefix."_timecards.ticket_id=$id and ".$table_prefix."_crmentity.deleted=0 ";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_timecards method ...");
		return $return_value;
	}
	function save_related_module($module, $crmid, $with_module, $with_crmid) {
	    global $adb,$log;
	    global $table_prefix;
		if(!is_array($with_crmid)) $with_crmid = Array($with_crmid);
		if($with_module == 'Timecards') {
			$with_crmids=implode(',',$with_crmid);
		    $adb->pquery("UPDATE ".$table_prefix."_timecards set ticket_id=? where timecardsid in (?)",Array($crmid, $with_crmids));
			//crmv@29617
			if(!is_array($with_crmid)) $with_crmid = Array($with_crmid);
			foreach($with_crmid as $relcrmid) {
				if ($crmid != $relcrmid) {
					$obj = CRMEntity::getInstance('ModNotifications');
					$obj->saveRelatedModuleNotification($crmid, $module, $relcrmid, $with_module);
				}
			}
			//crmv@29617e
		//crmv@57850 crmv@99875
		} elseif($with_module == 'ServiceContracts'){
			parent::save_related_module($module, $crmid, $with_module, $with_crmid);
			$servicecontracts = CRMEntity::getInstance('ServiceContracts');
			foreach($with_crmid as $relcrmid) {
				$servicecontracts = CRMEntity::getInstance('ServiceContracts');
				$servicecontracts->updateHelpDeskRelatedTo($relcrmid,$crmid);
				$servicecontracts->updateServiceContractState($relcrmid);
			}
		//crmv@57850e crmv@99875e
		} else {
		    parent::save_related_module($module, $crmid, $with_module, $with_crmid);
		}
	}

	//crmv@37004
	function getMessagePopupFields($module) {
		$namefields = array(
			'ticket_no',
			'ticket_title',
			'ticketstatus',
			'ticketseverities',
			'assigned_user_id',
		);
		return $namefields;
	}

	function getMessagePopupLimitedCond(&$queryGenerator, $module, $relatedIds = array(), $searchstr = '') {
		global $adb, $table_prefix;
		$queryGenerator->addCondition('ticketstatus', 'Closed', 'n');
	}

	function getMessagePopupOrderBy(&$queryGenerator, $module, $relatedIds = array(), $searchstr = '') {
		global $table_prefix;
		// TODO: ticketpriority
		return " ORDER BY {$table_prefix}_crmentity.createdtime ASC";
	}
	//crmv@37004e
	
	//crmv@87556
	function sendMailScannerReply() {
		require_once('modules/Emails/mail.php');
		$subject = 'Re: '.$this->column_fields['ticket_title'].' - Ticket Id: '.$this->id;
		$body = nl2br($this->column_fields['comments']);
		$mail_status = send_mail('HelpDesk',$this->column_fields['email_from'],'',$this->column_fields['email_to'],$subject,$body,'','','','','','',$mail_tmp);
		if ($mail_status == 1) {
			global $currentModule;
			$currentModule = 'Messages';
			$_REQUEST['relation'] = $this->id;
			$focusMessages = CRMentity::getInstance($currentModule);
			$focusMessages->internalAppendMessage($mail_tmp,'','',$this->column_fields['email_from'],'',$this->column_fields['email_to'],$subject,$body,'','','');
			$currentModule = 'HelpDesk';
		}
		return $mail_status;
	}
	//crmv@87556e
}
?>
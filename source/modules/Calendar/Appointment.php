<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
require_once('modules/Calendar/CalendarCommon.php');
require_once('include/utils/CommonUtils.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/database/PearDatabase.php');
require_once('modules/Calendar/Activity.php');

class Appointment
{
	var $start_time;
	var $end_time;
	var $subject;
	var $participant;
	var $participant_state;
	var $contact_name;
	var $account_id;
	var $account_name;
	var $creatorid;
	var $creator;
	var $owner;
	var $ownerid;
	var $assignedto;
	var $eventstatus;
	var $priority;
	var $activity_type;
	var $description;
	var $record;
	var $temphour;
	var $tempmin;
	var $image_name;
	var $formatted_datetime;
	var $duration_min;
	var $duration_hour;
	var $shared = false;
	var $recurring;
	var $dur_hour;

	function Appointment()
	{
		$this->participant = Array();
		$this->participant_state = Array();
		$this->description = "";
	}

	/** To get the events of the specified user and shared events
	  * @param $userid -- The user Id:: Type integer
          * @param $from_datetime -- The start date Obj :: Type Array
          * @param $to_datetime -- The end date Obj :: Type Array
          * @param $view -- The calendar view :: Type String
	  * @returns $list :: Type Array
	 */

	function readAppointment($userid, &$from_datetime, &$to_datetime, $view, $view_filter, $return_sql=false) //crmv@7633s	//crmv@17001
	{
		//crmv@60390 skip query not used anymore!
		if (!$return_sql){
			return;
		}
		//crmv@60390e
		global $current_user,$adb,$table_prefix;
		require('user_privileges/requireUserPrivileges.php'); // crmv@39110
		require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
		
		$LVU = ListViewUtils::getInstance();

   		// crmv@103023
		$and = '';
		
		$sqlFrom = $adb->formatDate($from_datetime->get_formatted_date(), true);
		$sqlTo = $adb->formatDate($to_datetime->get_formatted_date(), true);
		$dateConds = array(
			array(
				"(".$table_prefix."_activity.date_start between ? AND ?)",
				array($sqlFrom, $sqlTo)
			),
            array(
				"(".$table_prefix."_activity.date_start < ? AND ".$table_prefix."_activity.due_date > ?)",
				array($sqlFrom, $sqlTo)
			),
			array(
				"(".$table_prefix."_activity.due_date between ? AND ?)",
				array($sqlFrom, $sqlTo)
			),
		);
		// crmv@103023e

		//crmv7633
		if($view_filter == "all" || $view_filter == "") { // all event (normal rule)

		} elseif ($view_filter == "none") {
			$and .= " and ".$table_prefix."_crmentity.smownerid = 0";
		} elseif ($view_filter == "mine") { // only assigned to me
			$and .= " and (".$table_prefix."_crmentity.smownerid = ".$current_user->id." ";
			//crmv@44153 
			if ($current_user->is_admin == 'on') {					
				require_once('modules/Users/CreateUserPrivilegeFile.php');
				$userGroupFocus=new GetUserGroups();
				$userGroupFocus->getAllUserGroups($current_user->id);	
				$current_user_groups = $userGroupFocus->user_groups;					
			}
			//crmv@44153e
			if(sizeof($current_user_groups) > 0)
			{
				$and .= " or ".$table_prefix."_groups.groupid in (". implode(",", $current_user_groups) .")";
			}
			//crmv@20324
			$and .= " OR ".$table_prefix."_activity.activityid IN(SELECT
			                                               activityid
			                                             FROM ".$table_prefix."_invitees
			                                             WHERE ".$table_prefix."_activity.activityid > 0
			                                                 AND inviteeid = $current_user->id)";
			//crmv@20324e
			$and.=" ) ";
		} elseif ($view_filter == "others") { // only assigneto others
			$and .= " and (".$table_prefix."_crmentity.smownerid <> ".$current_user->id." ";
			//crmv@19218
			/* if(sizeof($current_user_groups) > 0)
			{
				$and .= " and vtiger_groups.groupid not in (". implode(",", $current_user_groups) .")";
			} */
			//crmv@19218e
			$and.=" ) ";
		// crmv@20211
		} elseif (strlen($view_filter) > 0) {
			$view_filters = explode(',', $view_filter);
			$and .= " and ( ";
			$andd = array();
			foreach ($view_filters as $key => $filter) {
				if ($filter == 'mine')
					$filter = $current_user->id;
				elseif (in_array($filter,array('all','others')))
					continue;
				$userid = $filter;
				require('user_privileges/requireUserPrivileges.php'); // crmv@39110
				require('user_privileges/sharing_privileges_'.$filter.'.php');
				$tmp_andd = " ".$table_prefix."_crmentity.smownerid = $filter";
				//crmv@44153
				if ($current_user->is_admin == 'on') {					
					require_once('modules/Users/CreateUserPrivilegeFile.php');
					$userGroupFocus=new GetUserGroups();
					$userGroupFocus->getAllUserGroups($current_user->id);	
					$current_user_groups = $userGroupFocus->user_groups;					
				}
				//crmv@44153e
				if(sizeof($current_user_groups) > 0)
				{
					$tmp_andd .= " or ".$table_prefix."_groups.groupid in (". implode(",", $current_user_groups) .")";
				}
				//crmv@20324
				$tmp_andd .= " OR ".$table_prefix."_activity.activityid IN(SELECT
                												activityid
																FROM ".$table_prefix."_invitees
																WHERE ".$table_prefix."_activity.activityid > 0
																AND inviteeid = $filter)";
				//crmv@20324e
				$andd[] = $tmp_andd;
			}
			$and .= implode(' or ',$andd);
			$and.=" ) ";
			$userid = $current_user->id;
			require('user_privileges/requireUserPrivileges.php'); // crmv@39110
			require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
		}
		//crmv@20211e
		//crmv7633e

		//crmv@8398+17997
		$condition = getCalendarSqlCondition('event')." ".$and;
		$q= $LVU->getListQuery('CalendarList',$condition);//crmv@60390 use custom query
		//crmv@8398+17997e

		// crmv@103023 crmv@113804
		$params = array();
		$cloneTables = array();
		if (PerformancePrefs::getBoolean('USE_TEMP_TABLES', true) && $adb->isMysql()) {
			// find temp table name
			if (preg_match('/vt_tmp_[0-9a-z_]+/', $q, $matches)) {
				$cloneTables[] = $matches[0];
			}
		}
		$q = $adb->makeUnionSelect($q, $dateConds, true, $params, $cloneTables);
		if ($adb->isOracle()) {
			$q = str_replace($table_prefix.'_crmentity.description', "CAST({$table_prefix}_crmentity.description AS VARCHAR2(2000)) as description", $q);
			$q = "SELECT * FROM ($q)";
		}
		$q .= " ORDER BY date_start ASC, time_start ASC";
		// crmv@103023 crmv@113804

		//crmv@17001
		if ($return_sql) return $adb->convert2Sql($q,$adb->flatten_array($params));
		//crmv@17001e
		$r = $adb->pquery($q, $params);
        $n = $adb->getRowCount($r);
        $a = 0;
		$list = Array();

        while ( $a < $n )
        {

			$result = $adb->fetchByAssoc($r);
			$start_timestamp = strtotime($result["date_start"]);
			$end_timestamp = strtotime($result["due_date"]);
			if($from_datetime->ts <= $start_timestamp) $from = $start_timestamp;
			else $from = $from_datetime->ts;
			if($to_datetime->ts <= $end_timestamp) $to = $to_datetime->ts;
			else $to = $end_timestamp;
			for($j = $from; $j <= $to; $j=$j+(60*60*24))
			{

				$obj = new Appointment();
				$temp_start = date("Y-m-d",$j);
				$result["date_start"]= $temp_start ;
				list($obj->temphour,$obj->tempmin) = explode(":",$result["time_start"]);
				if($start_timestamp != $end_timestamp && $view == 'day'){
					if($j == $start_timestamp){
						$result["duration_hours"] = 24 - $obj->temphour;
					}elseif($j > $start_timestamp && $j < $end_timestamp){
						list($obj->temphour,$obj->tempmin)= $current_user->start_hour !=''?explode(":",$current_user->start_hour):explode(":","08:00");
						$result["duration_hours"] = 24 - $obj->temphour;
					}elseif($j == $end_timestamp){
						list($obj->temphour,$obj->tempmin)= $current_user->start_hour !=''?explode(":",$current_user->start_hour):explode(":","08:00");
						list($ehr,$emin) = explode(":",$result["time_end"]);
						$result["duration_hours"] = $ehr - $obj->temphour;
					}
				}
				$obj->readResult($result, $view);
				$list[] = $obj;
				unset($obj);

			}
			$a++;

        }
//recurring events
//		$q_recurring= "select vtiger_activity.*,
//		vtiger_recurringevents.*,
//		vtiger_crmentity.crmid,
//		vtiger_crmentity.smownerid,
//		vtiger_crmentity.description,
//		case when (vtiger_users.user_name is not null) then vtiger_users.user_name else vtiger_groups.groupname end as user_name
//		FROM vtiger_activity
//		inner join vtiger_crmentity on vtiger_activity.activityid = vtiger_crmentity.crmid
//		inner join vtiger_recurringevents on vtiger_activity.activityid=vtiger_recurringevents.activityid
//		left outer join vtiger_activitygrouprelation on vtiger_activitygrouprelation.activityid=vtiger_activity.activityid
//		left join vtiger_groups on vtiger_groups.groupname = vtiger_activitygrouprelation.groupname
//		LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
//		WHERE vtiger_crmentity.deleted = 0 ";
//        $and_recurring = "AND vtiger_recurringevents.recurringdate BETWEEN ? AND ?";
//		//crmv7633
//		if( $view_filter == "all" || $view_filter == "") { // all event (normal rule)
//
//		} else if ( $view_filter == "mine") { // only assigned to me
//			$and_recurring .= " and vtiger_crmentity.smownerid = ".$current_user->id." ";
//		} else if ( $view_filter == "others") { // only assigneto others
//			$and_recurring .= " and vtiger_crmentity.smownerid <> ".$current_user->id." ";
//
//		} else { // a selected userid
//			$and_recurring .= " and vtiger_crmentity.smownerid = ".$view_filter." ";
//		}
//		//crmv7633e
//        $q_recurring.=" $and_recurring ";
//		if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[16] == 3)
//		{
//			$sec_parameter=getListViewSecurityParameter('Calendar');
//			$q_recurring .= $sec_parameter;
//		}
//		$q_recurring.=" ORDER BY date_start,time_start ASC";
//		$params_recurring = array(
//		$from_datetime->get_formatted_date(), $to_datetime->get_formatted_date(),
//		);
//		$r = $adb->pquery($q_recurring, $params_recurring);
//        $n = $adb->getRowCount($r);
//        $a = 0;
//	        while ( $a < $n )
//        {
//
//			$result = $adb->fetchByAssoc($r);
//			$start_timestamp = strtotime($result["recurringdate"]);
//			$end_timestamp = strtotime($result["recurringdate"]);
//			if($from_datetime->ts <= $start_timestamp) $from = $start_timestamp;
//			else $from = $from_datetime->ts;
//			if($to_datetime->ts <= $end_timestamp) $to = $to_datetime->ts;
//			else $to = $end_timestamp;
//			for($j = $from; $j <= $to; $j=$j+(60*60*24))
//			{
//
//				$obj = &new Appointment();
//				$temp_start = date("Y-m-d",$j);
//				$result["date_start"]= $temp_start ;
//				list($obj->temphour,$obj->tempmin) = explode(":",$result["time_start"]);
//				if($start_timestamp != $end_timestamp && $view == 'day'){
//					if($j == $start_timestamp){
//						$result["duration_hours"] = 24 - $obj->temphour;
//					}elseif($j > $start_timestamp && $j < $end_timestamp){
//						list($obj->temphour,$obj->tempmin)= $current_user->start_hour !=''?explode(":",$current_user->start_hour):explode(":","08:00");
//						$result["duration_hours"] = 24 - $obj->temphour;
//					}elseif($j == $end_timestamp){
//						list($obj->temphour,$obj->tempmin)= $current_user->start_hour !=''?explode(":",$current_user->start_hour):explode(":","08:00");
//						list($ehr,$emin) = explode(":",$result["time_end"]);
//						$result["duration_hours"] = $ehr - $obj->temphour;
//					}
//				}
//				$obj->readResult($result, $view);
//				$list[] = $obj;
//				unset($obj);
//
//			}
//			$a++;
//
//        }
//        usort($list,'compare');
        return $list;
	}


	/** To read and set the events value in Appointment Obj
          * @param $act_array -- The vtiger_activity array :: Type Array
          * @param $view -- The calendar view :: Type String
         */
	function readResult($act_array, $view)
	{
		global $adb,$current_user,$app_strings,$table_prefix;
		$format_sthour='';
                $format_stmin='';
		$this->description       = $act_array["description"];
		// crmv@39106
		$this->eventstatus       = self::getRoleBasesdPickList('eventstatus',$act_array["eventstatus"]);
		$this->priority		 = self::getRoleBasesdPickList('taskpriority',$act_array["priority"]);
		// crmv@39106e
		$this->subject           = $act_array["subject"];
		$this->activity_type     = $act_array["activitytype"];
		$this->duration_hour     = $act_array["duration_hours"];
		$this->duration_minute   = $act_array["duration_minutes"];
		$this->creatorid         = $act_array["smcreatorid"];
		//$this->creator           = getUserName($act_array["smcreatorid"]);
		$this->assignedto = $act_array["user_name"];
		$this->owner   = $act_array["user_name"];
		$this->ownerid = $act_array["smownerid"];//crmv7210s
		if(!is_admin($current_user))
		{
			if($act_array["smownerid"]!=0 && $act_array["smownerid"] != $current_user->id){ // crmv@70053
				$que = "select * from ".$table_prefix."_sharedcalendar where sharedid=? and userid=?";
				$row = $adb->pquery($que, array($current_user->id, $act_array["smownerid"]));
				$no = $adb->getRowCount($row);
				if($no > 0)
					$this->shared = true;
			}
		}
		$this->image_name = $act_array["activitytype"].".gif";
		if(!empty($act_array["recurringtype"]) && $act_array["recurringtype"] != '--None--')
			$this->recurring="Recurring.gif";

		$this->record            = $act_array["activityid"];
		list($styear,$stmonth,$stday) = explode("-",$act_array["date_start"]);
		if($act_array["notime"] != 1){
			$st_hour = twoDigit($this->temphour);
			list($sthour,$stmin) = explode(":",$act_array["time_start"]);
		}else{
			$st_hour = 'notime';
			$stmin = '00';
			$sthour= '00';
		}
		list($eyear,$emonth,$eday) = explode("-",$act_array["due_date"]);
		list($end_hour,$end_min) = explode(":",$act_array["time_end"]);

		$start_date_arr = Array(
			'min'   => $stmin,
			'hour'  => $sthour,
			'day'   => $stday,
			'month' => $stmonth,
			'year'  => $styear
		);
		$end_date_arr = Array(
			'min'   => $end_min,
			'hour'  => $end_hour,
			'day'   => $eday,
			'month' => $emonth,
			'year'  => $eyear
		);
                $this->start_time        = new vt_DateTime($start_date_arr,true);
                $this->end_time          = new vt_DateTime($end_date_arr,true);
		if($view == 'day' || $view == 'week')
		{
			$this->formatted_datetime= $act_array["date_start"].":".$st_hour;
		}
		elseif($view == 'year')
		{
			list($year,$month,$date) = explode("-",$act_array["date_start"]);
			$this->formatted_datetime = $month;
		}
		else
		{
			$this->formatted_datetime= $act_array["date_start"];
		}
		return;
	}

	// crmv@39106
	static function getRoleBasesdPickList($fldname,$exist_val) {
		global $adb,$app_strings,$current_user,$table_prefix;
		$is_Admin = $current_user->is_admin;
		if($is_Admin == 'off' && $fldname != '')
		{
			$roleid=$current_user->roleid;
			$roleids = Array();
			$subrole = getRoleSubordinates($roleid);
			if(count($subrole)> 0)
				$roleids = $subrole;
			array_push($roleids, $roleid);

			//here we are checking wheather the table contains the sortorder column .If  sortorder is present in the main picklist table, then the role2picklist will be applicable for this table...

			$sql="select * from ".$table_prefix."_$fldname where $fldname=?";
			$res = $adb->pquery($sql,array(decode_html($exist_val)));
			$picklistvalueid = $adb->query_result($res,0,'picklist_valueid');
			if ($picklistvalueid != null) {
				$pick_query="select * from ".$table_prefix."_role2picklist where picklistvalueid=$picklistvalueid and roleid in (". generateQuestionMarks($roleids) .")";

				$res_val=$adb->pquery($pick_query,array($roleids));
				$num_val = $adb->num_rows($res_val);
			}
			if($num_val > 0)
				$pick_val = $exist_val;
			else
				$pick_val = $app_strings['LBL_NOT_ACCESSIBLE'];


		}else
			$pick_val = $exist_val;

		return $pick_val;

	}
	// crmv@39106e

}


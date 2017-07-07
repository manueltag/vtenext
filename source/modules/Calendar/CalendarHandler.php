<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

//crmv@26030m
class CalendarHandler extends VTEventHandler {

	function handleEvent($eventName, $data) {
		global $adb, $current_user, $table_prefix;

		if (!($data->focus instanceof Activity)) {
			return;
		}

		if($eventName == 'vtiger.entity.beforesave') {

			$id = $data->getId();
			//$module = $data->getModuleName();
			if (!isset($id) || $id == '') return;

			$focus = $data->getData();
			$focus_beforesave = CRMEntity::getInstance('Calendar');
			$focus_beforesave->id = $id;
			$focus_beforesave->retrieve_entity_info($focus_beforesave->id,'Events');
			if ($data->isNew()) {
				$mode = 'create';
			} else {
				$mode = 'edit';
			}

			//crmv@32334
			if (isZMergeAgent()) {
				//do nothing
			} else {
			//crmv@32334e
				if ((getValidDBInsertDateValue($focus['date_start']) != $focus_beforesave->column_fields['date_start']) ||
					(getValidDBInsertDateValue($focus['due_date'])   != $focus_beforesave->column_fields['due_date'])   ||
					($focus['time_start'] != $focus_beforesave->column_fields['time_start']) ||
					($focus['time_end']   != $focus_beforesave->column_fields['time_end']))  {

					if(isset($_REQUEST['inviteesid']) && $_REQUEST['inviteesid']!='' && $_REQUEST['inviteesid']!= '--none--') {	//crmv@27443
						$inviteesid = $selected_users_string =  $_REQUEST['inviteesid'];
						$this->resetInvitees($table_prefix.'_invitees',array_filter(explode(';', $selected_users_string)),$id);
					} else {
						$partecipations = array();
						$res = $adb->pquery("select inviteeid, partecipation from ".$table_prefix."_invitees where activityid=?", array($id));
						if ($res && $adb->num_rows($res)>0) {
							$inviteesid = array();
							while($row=$adb->fetchByAssoc($res)) {
								$inviteesid[] = $row['inviteeid'];
								$partecipations[$row['inviteeid']] = $row['partecipation'];
							}
							$inviteesid = implode(';',$inviteesid);
							$this->resetInvitees($table_prefix.'_invitees',array_keys($partecipations),$id);
						}
					}

					if(isset($_REQUEST['inviteesid_con']) && $_REQUEST['inviteesid_con']!='' && $_REQUEST['inviteesid_con']!= '--none--') {	//crmv@27443
						$inviteesid_con = $selected_users_string =  $_REQUEST['inviteesid_con'];
						$this->resetInvitees($table_prefix.'_invitees_con',array_filter(explode(';', $selected_users_string)),$id);
					} else {
						$partecipations_con = array();
						$res = $adb->pquery("select inviteeid, partecipation from ".$table_prefix."_invitees_con where activityid=?", array($id));
						if ($res && $adb->num_rows($res)>0) {
							$inviteesid_con = array();
							while($row=$adb->fetchByAssoc($res)) {
								$inviteesid_con[] = $row['inviteeid'];
								$partecipations_con[$row['inviteeid']] = $row['partecipation'];
							}
							$inviteesid_con = implode(';',$inviteesid_con);
							$this->resetInvitees($table_prefix.'_invitees_con',array_keys($partecipations_con),$id);
						}
					}
				}
				if ($mode == 'edit') {
					// crmv@76088 - use new values!
					$focus_beforesave->column_fields = array_merge($focus_beforesave->column_fields, $focus);
					// crmv@76088e
					$focus_beforesave->column_fields['date_start'] = getValidDBInsertDateValue($focus['date_start']);
					$focus_beforesave->column_fields['due_date'] = getValidDBInsertDateValue($focus['due_date']);
					$focus_beforesave->column_fields['time_start'] = $focus['time_start'];
					$focus_beforesave->column_fields['time_end'] = $focus['time_end'];
					$mail_contents = $focus_beforesave->getRequestData($id,$focus_beforesave); //crmv@32334
					$focus_beforesave->sendInvitation($inviteesid,$mode,$focus_beforesave->column_fields['subject'],$mail_contents,$id,$inviteesid_con); //crmv@32334
				}
			}
		}
	}
	function resetInvitees($table,$invitees_array,$activityid) {
		global $adb;
		if (!empty($invitees_array) && $activityid != '') {
			$invitees = implode(',',$invitees_array);
			$resetInvitees_query = "UPDATE $table SET partecipation = 0 WHERE activityid = $activityid AND inviteeid IN (".$invitees.")";
			$resetInvitees_params = array($activityid);
			$adb->pquery($resetInvitees_query, $resetInvitees_params);
		}
	}
}
//crmv@26030me
?>
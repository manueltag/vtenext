<?php
/* crmv@43611 */

global $current_user;
global $adb, $table_prefix;

$ajaxAction = $_REQUEST['ajaxaction'];

if ($ajaxAction == 'countrecipients') {
	// this calculation might be very slow
	$ids = Zend_Json::decode($_REQUEST['ids']);
	$ret = array();
	$totcount = 0;

	if ($ids) {

		function getTargetRelatedIds($targetid, $parsed = array()) {
			if (in_array($targetid, $parsed)) {
				return array('Leads'=>array(), 'Contacts'=>array(), 'Accounts'=>array());
			}

			$rm = RelationManager::getInstance();
			$targetInst = CRMEntity::getInstance('Targets');
			$targetInst->id = $targetid;

			$ids_leads = array();
			$ids_contacts = array();
			$ids_accounts = array();

			$sub_targets = $targetInst->getChildren();
			if (is_array($sub_targets)) {
				foreach ($sub_targets as $subtargetid) {
					$subres = getTargetRelatedIds($subtargetid, array_merge($parsed, array($targetid)));
					$ids_leads = array_merge($ids_leads, $subres['Leads']);
					$ids_contacts = array_merge($ids_contacts, $subres['Contacts']);
					$ids_accounts = array_merge($ids_accounts, $subres['Accounts']);
				}
			}

			$ids_leads = array_merge($ids_leads, $rm->getRelatedIds('Targets', $targetid, 'Leads'));
			$ids_contacts = array_merge($ids_contacts, $rm->getRelatedIds('Targets', $targetid, 'Contacts'));
			$ids_accounts = array_merge($ids_accounts, $rm->getRelatedIds('Targets', $targetid, 'Accounts'));

			return array(
				'Leads' => array_unique($ids_leads),
				'Contacts' => array_unique($ids_contacts),
				'Accounts' => array_unique($ids_accounts),
			);
		}

		if (!is_array($ids['Leads']['ids'])) $ids['Leads']['ids'] = array();
		if (!is_array($ids['Contacts']['ids'])) $ids['Contacts']['ids'] = array();
		if (!is_array($ids['Accounts']['ids'])) $ids['Accounts']['ids'] = array();

		// first retrieve targets and their associated entities and store them in the input array
		// targets do not have filters
		if (is_array($ids['Targets']['ids'])) {

			foreach ($ids['Targets']['ids'] as $targetid) {
				$target_ids = getTargetRelatedIds($targetid);
				$ids['Leads']['ids'] = array_unique(array_merge($ids['Leads']['ids'], $target_ids['Leads']));
				$ids['Contacts']['ids'] = array_unique(array_merge($ids['Contacts']['ids'], $target_ids['Contacts']));
				$ids['Accounts']['ids'] = array_unique(array_merge($ids['Accounts']['ids'], $target_ids['Accounts']));
			}
		}
		unset($ids['Targets']);

		// now process other entities
		foreach ($ids as $module => $ent) {
			$filters = $ent['filters'];

			if (!empty($filters) && is_array($filters)) {
				// retrieve the entities for each filter
				foreach ($filters as $viewid) {

					$queryGenerator = QueryGenerator::getInstance($module, $current_user);
					$queryGenerator->initForCustomViewById($viewid);
					$list_query = $queryGenerator->getQuery();
					$list_query = replaceSelectQuery($list_query, $table_prefix.'_crmentity.crmid');
					$res = $adb->query($list_query);
					if ($res && $adb->num_rows($res)>0) {
						$filterids = array();
						$focus = CRMEntity::getInstance($currentModule);
						while ($row=$adb->fetchByAssoc($res,-1,false)) {
							$ids[$module]['ids'][] = $row['crmid'];
						}
					}

				}
			}
			$ids[$module]['ids'] = array_unique($ids[$module]['ids']);
			$totcount += count($ids[$module]['ids']);
		}
	}

	$ret['success'] = '1';
	$ret['count'] = $totcount;

	echo Zend_Json::encode($ret);
	exit;

} elseif ($ajaxAction == 'saveandtest' || $ajaxAction == 'saveonly') {
	$campaignid = intval($_REQUEST['campaignid']);		// can be empty
	$newsletterid = intval($_REQUEST['newsletterid']);	// can be empty
	$templateid = intval($_REQUEST['templateid']);
	$test_address = $_REQUEST['test_email_address'];
	$recipients = Zend_Json::decode($_REQUEST['recipients']);

	$ret = array();

	$campInstance = CRMEntity::getInstance('Campaigns');

	if (empty($campaignid)) {
		// let's create a campaign
		if (isPermitted('Campaigns', 'EditView') != 'yes') die('Not permitted');

		$campInstance->column_fields['campaignname'] = '[AUTO] '.vtlib_purify($_REQUEST['newslettername']);
		$campInstance->column_fields['assigned_user_id'] = $current_user->id;
		$campInstance->mode = '';
		$campInstance->save('Campaigns');
		$campaignid = $campInstance->id;
	}

	if ($_REQUEST['skiptargets'] != '1') {

		$rm = RelationManager::getInstance();

		// remove existing targets
		$oldTargets = $rm->getRelatedIds('Campaigns', $campaignid, 'Targets');
		if (is_array($oldTargets) && count($oldTargets) > 0) {
			$campInstance->delete_related_module('Campaigns', $campaignid, 'Targets', $oldTargets);
		}

		// if recipients contains only targets, link them to the campaign, otherwise create a new one
		if (count($recipients) == 1 && !empty($recipients['Targets'])) {
			$targets = $recipients['Targets']['ids'];
			// and attach the targets
			$campInstance->save_related_module('Campaigns', $campaignid, 'Targets', $targets);
		} else {

			if (isPermitted('Targets', 'EditView') != 'yes') die('Not permitted');
			$targetInstance = CRMEntity::getInstance('Targets');
			$targetInstance->column_fields['targetname'] = '[AUTO] '.vtlib_purify($_REQUEST['newslettername']);
			$targetInstance->column_fields['assigned_user_id'] = $current_user->id;
			$targetInstance->mode = '';
			$targetInstance->save('Targets');

			$targetid = $targetInstance->id;

			// now add recipients to the target
			foreach ($recipients as $recmod => $recstuff) {
				// add single ids
				$ids = $recstuff['ids'];
				if (!empty($ids) && is_array($ids)) {
					$targetInstance->save_related_module('Targets', $targetid, $recmod, $ids);
				}

				// add filters now
				$filters = $recstuff['filters'];
				if (!empty($filters) && is_array($filters)) {
					foreach ($filters as $viewid) {

						$queryGenerator = QueryGenerator::getInstance($recmod, $current_user);
						$queryGenerator->initForCustomViewById($viewid);
						$list_query = $queryGenerator->getQuery();
						$list_query = replaceSelectQuery($list_query, $table_prefix.'_crmentity.crmid');
						$res = $adb->query($list_query);
						if ($res && $adb->num_rows($res)>0) {
							$filterids = array();
							$focus = CRMEntity::getInstance($currentModule);
							while ($row=$adb->fetchByAssoc($res,-1,false)) {
								$filterids[] = $row['crmid'];
							}
							$targetInstance->save_related_module('Targets', $targetid, $recmod, $filterids);
						}
					}
				}
			}
			$campInstance->save_related_module('Campaigns', $campaignid, 'Targets', $targetid);
		}
	}

	// create/update newsletter
	$newsInstance = CRMEntity::getInstance('Newsletter');
	if ($newsletterid > 0) {
		$newsInstance->retrieve_entity_info($newsletterid, 'Newsletter');
		$newsInstance->mode = 'edit';
		$newsInstance->id = $newsletterid;
	} else {
		$newsInstance->mode = '';
		$newsInstance->column_fields['assigned_user_id'] = $current_user->id;
	}
	$newsInstance->column_fields['campaignid'] = $campaignid;
	$newsInstance->column_fields['templateemailid'] = $templateid;
	$newsInstance->column_fields['newslettername'] = vtlib_purify($_REQUEST['newslettername']);
	$newsInstance->column_fields['from_name'] = vtlib_purify($_REQUEST['from_name']);
	$newsInstance->column_fields['from_address'] = vtlib_purify($_REQUEST['from_address']);
	$newsInstance->column_fields['description'] = vtlib_purify($_REQUEST['description']);
	$newsInstance->save('Newsletter');
	$newsletterid = $newsInstance->id;
	
	$ret = array('templateid'=> $templateid, 'templatename'=>$templateName);
	//crmv@104558
	$templateName = from_html($_REQUEST["templatename"]);
	$subject = $_REQUEST["subject"];
	if(substr($templateName, 0, 15) == getTranslatedString('LBL_AUTO_TMP_NAME', 'Newsletter')){
		$templateName = vtlib_purify($_REQUEST['newslettername']);
		$sql_u = "update {$table_prefix}_emailtemplates set templatename =?, subject=? where templateid =?";
		$params_u = array($templateName,$subject, $templateid);
		$res_u = $adb->pquery($sql_u, $params_u);
		$ret['templatename'] = $templateName;
	}
	//crmv@104558e
	
	// now send test email

	if ($ajaxAction == 'saveandtest') {
		$mail_status = $newsInstance->sendNewsletter('','test',$test_address);
	} else {
		$mail_status = 1;
	}
	if ($mail_status == 1) {
		$ret['mail_status'] = 'ok';
	} else {
		$ret['mail_status'] = 'fail';
	}

	$ret['campaignid'] = $campaignid;
	$ret['newsletterid'] = $newsletterid;

	echo Zend_Json::encode($ret);
	exit;

} elseif ($ajaxAction == 'saveandsend') {
	$newsletterid = intval($_REQUEST['newsletterid']);

	if ($newsletterid == 0) die('Newsletter ID empty');

	$newsInstance = CRMEntity::getInstance('Newsletter');
	$newsInstance->retrieve_entity_info($newsletterid, 'Newsletter');
	$newsInstance->id = $newsletterid;

	if (isPermitted('Newsletter', 'EditView', $newsletterid) != 'yes') die('Not permitted');

	if ($_REQUEST['sendnow'] == '1') {
		$date_scheduled = date('Y-m-d');
		$time_scheduled = date('H:i');
	} else {
		$date_scheduled = substr(trim($_REQUEST['scheduled_date']), 0, 10);
		$time_scheduled = substr($_REQUEST['scheduled_time'], 0, 5);
	}
	
	//crmv@46384 :  populate queue
	$target_list = $newsInstance->getTargetList();
	if (!empty($target_list)) {
		foreach($target_list as $crmid) {
			$result = $adb->pquery('select * from tbl_s_newsletter_queue where newsletterid = ? and crmid = ?',array($newsletterid,$crmid));
			if ($result && $adb->num_rows($result) > 0) {
				//do nothing
			} else {
				$res = $adb->pquery('insert into tbl_s_newsletter_queue (newsletterid,crmid,status,attempts,date_scheduled,num_views) values (?,?,?,?,?,?)',array($newsletterid,$crmid,'Scheduled',0,$adb->formatDate($date_scheduled.' '.$time_scheduled,true),0));
			}
		}
	}
	//crmv@46384e

	$newsInstance->mode = 'edit';
	$newsInstance->column_fields['date_scheduled'] = $date_scheduled;
	$newsInstance->column_fields['time_scheduled'] = $time_scheduled;
	$newsInstance->column_fields['scheduled'] = 1;

	$newsInstance->save('Newsletter');

	$ret['success'] = '1';

	echo Zend_Json::encode($ret);
	exit;

} elseif ($ajaxAction == 'gettemplate') {
	//if (!is_admin($current_user)) die('Not permitted!');
	if (isPermitted('Newsletter', 'EditView') != 'yes') die('Not permitted!');

	$templateid = intval($_REQUEST['templateid']);
	$res = $adb->pquery("select * from {$table_prefix}_emailtemplates where deleted = 0 and templateid = ?", array($templateid));
	$row = $adb->FetchByAssoc($res, -1, false);

	echo Zend_Json::encode($row);
	exit;
} elseif ($ajaxAction == 'savetemplate') {
	// only admin can save templates
	//crmv@55230	if (!is_admin($current_user)) die('Not permitted!');
	$templateid = intval($_REQUEST['templateid']);

	$templateName = from_html($_REQUEST["templatename"]);
	$description = from_html($_REQUEST["description"]);
	$subject = from_html($_REQUEST["subject"]);
	$body = fck_from_html($_REQUEST["body"]);
	//crmv@82769
	$use_signature = (isset($_REQUEST["use_signature"]) && $_REQUEST["use_signature"] == 'on') ? 1 : 0;
	$overwrite_message = (isset($_REQUEST["overwrite_message"]) && $_REQUEST["overwrite_message"] == 'on') ? 1 : 0;
	if ($templatetype != 'Email') {
		$use_signature = 0;
		$overwrite_message = 1;
	}
	//crmv@82769e

	if ($templateid > 0)	{
		$sql = "update {$table_prefix}_emailtemplates set templatename =?, subject =?, description =?, body =".$adb->getEmptyClob(true)." where templateid =?";
		$params = array($templateName, $subject, $description, $templateid);
		$adb->pquery($sql, $params);
		$result = $adb->updateClob($table_prefix.'_emailtemplates','body',"templateid=$templateid",$body);
	} else {
		$templateid = $adb->getUniqueID($table_prefix.'_emailtemplates');
		//crmv@82769
		$sql = "insert into ".$table_prefix."_emailtemplates (foldername,templatename,subject,description,deleted,templateid,templatetype,use_signature,overwrite_message) values (?,?,?,?,?,?,?,?,?)"; //crmv@88323
		$params = array('Public', $templateName, $subject, $description, 0, $templateid, 'Newsletter',$use_signature,$overwrite_message);
		//crmv@82769e
		$adb->pquery($sql, $params);
		$result = $adb->updateClob($table_prefix.'_emailtemplates','body',"templateid=$templateid",$body);
	}

	$ret = array('templateid'=> $templateid, 'templatename'=>$templateName);

	echo Zend_Json::encode($ret);
	exit;

}
?>
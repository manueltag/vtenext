<?php
class NewsletterHandler extends VTEventHandler {

	function handleEvent($eventName, $data) {
		global $adb, $current_user;
		global $table_prefix;
		
		//crmv@55961
		if ($data->focus instanceof Accounts || $data->focus instanceof Contacts || $data->focus instanceof Leads) {
			global $newsletter_unsubscrpt;
			if (!$data->isNew()) {
				if($eventName == 'vtiger.entity.beforesave') {
					$columns = $data->getData();
					$newsletter_unsubscrpt = $columns['newsletter_unsubscrpt'];
					$data->set('newsletter_unsubscrpt','0');
				} elseif($eventName == 'vtiger.entity.aftersave') {
					$record = $data->getId();
					$module = $data->getModuleName();
					
					$modObj = CRMEntity::getInstance('Newsletter');
					$focus = CRMEntity::getInstance($module);
					$focus->retrieve_entity_info($record,$module);
					$email = $focus->column_fields[$modObj->email_fields[$module]['fieldname']];
					
					($newsletter_unsubscrpt == 'on' || $newsletter_unsubscrpt == '1') ? $mode = 'unlock' : $mode = 'lock';

					$modObj->lockReceivingNewsletter($email,$mode);
				}
			}
		}
		//crmv@55961e		
		
		if (!($data->focus instanceof Newsletter)) {
			return;
		}

		if($eventName == 'vtiger.entity.beforesave') {
			
			$id = $data->getId();
			$module = $data->getModuleName();
			$focus = $data->getData();
	
			$check_refresh_scheduling = false;
			$new_date_scheduled = $focus['date_scheduled'];
			$new_time_scheduled = $focus['time_scheduled'];
			if (!$data->isNew()) {
				$res = $adb->query('SELECT date_scheduled,time_scheduled FROM '.$table_prefix.'_newsletter WHERE newsletterid = '.$id);
				$current_date_scheduled = $adb->query_result($res,0,'date_scheduled');
				$current_time_scheduled = $adb->query_result($res,0,'time_scheduled');
				if ($current_date_scheduled != $new_date_scheduled || $current_time_scheduled != $new_time_scheduled)
					$check_refresh_scheduling = true;
			}
			if ($check_refresh_scheduling) {
				$date_scheduled = getValidDBInsertDateValue($new_date_scheduled).' '.$new_time_scheduled;
				$adb->pquery('update tbl_s_newsletter_queue set date_scheduled = ? where newsletterid = ? and status = ? and attempts < ?',array($adb->formatDate($date_scheduled,true),$id,'Scheduled',$data->focus->max_attempts_permitted));
			}
		}
	}
}
?>
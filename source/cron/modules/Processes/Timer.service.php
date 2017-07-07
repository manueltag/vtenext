<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@97566 */

require_once('modules/Settings/ProcessMaker/ProcessMakerUtils.php');
require_once('modules/Settings/ProcessMaker/ProcessMakerEngine.php');

global $adb, $table_prefix, $current_user;

if (!$current_user) {
	$current_user = CRMEntity::getInstance('Users');
	$current_user->id = 1;
}

$PMUtils = ProcessMakerUtils::getInstance();

/* TODO : eliminare da _running_processes_timer
 * i timer boundary con executed 1 di running_process terminati
 * i timer intermediate di running_process terminati (per gli intermediate non viene gestito il flag executed)
 */
// TODO segnalare/ritentare eventuali timer bloccati per errore 

// execute timers
$result = $adb->pquery("SELECT {$table_prefix}_running_processes_timer.*
	FROM {$table_prefix}_running_processes_timer
	INNER JOIN {$table_prefix}_running_processes ON {$table_prefix}_running_processes.id = {$table_prefix}_running_processes_timer.running_process
	INNER JOIN {$table_prefix}_processmaker ON {$table_prefix}_running_processes.processmakerid = {$table_prefix}_processmaker.id
	WHERE {$table_prefix}_processmaker.active = ? AND timer <= ? and executed = ?", array(1,$adb->formatDate(date('Y-m-d H:i:s'), true),0));

if ($result && $adb->num_rows($result) > 0) {
	while($row=$adb->fetchByAssoc($result,-1,false)) {
		// set immediately executed = 1 in order to prevent stop of the cron in case of errors
		$adb->pquery("update {$table_prefix}_running_processes_timer set executed = ? where id = ?", array(1,$row['id']));
		
		$info = Zend_Json::decode($row['info']);
		$PMEngine = ProcessMakerEngine::getInstance($info['running_process'],$info['processid'],$info['prev_elementid'],$info['elementid'],$info['id'],$info['metaid'],null);
		if ($row['mode'] == 'start') {
			$PMEngine->log("StartEvent Timer","elementid:{$info['elementid']} timer:{$row['timer']}");
			$vte_metadata = $PMEngine->vte_metadata;
		} elseif ($row['mode'] == 'intermediate') {
			$PMEngine->log("Timer Delay ends","elementid:{$info['elementid']} timer:{$row['timer']}");
		} elseif ($row['mode'] == 'boundary') {
			$PMEngine->log("Timer Boundary ends","elementid:{$info['elementid']} timer:{$row['timer']}");
			if ($info['cancelActivity'] === true) {
				$PMEngine->trackProcess($info['prev_elementid'],$info['elementid']);
			} else {
				$PMEngine->trackProcess($info['prev_elementid'],implode('|##|',array($info['prev_elementid'],$info['elementid'])));
			}
		}
		$outgoings = $PMUtils->getOutgoing($info['processid'],$info['elementid']);
		if (!empty($outgoings)) {
			$outgoing = $outgoings[0];
			$engineType = $PMUtils->getEngineType($outgoing['shape']);
			$PMEngine = ProcessMakerEngine::getInstance($info['running_process'],$info['processid'],$info['elementid'],$outgoing['shape']['id'],$info['id'],$info['metaid'],null);
			$PMEngine->execute($engineType,$outgoing['shape']['type']);
		}
		if ($row['mode'] == 'start') {
			// delete current timer
			$adb->pquery("delete from {$table_prefix}_running_processes_timer where id = ?", array($row['id']));
			if ($PMEngine->process_data['active'] == 1) {
				// schedule the next occourence
				if ($info['calculate_next_occourence'] === true) {
					($vte_metadata['date_end_mass_edit_check'] == 'on') ? $date_end = getValidDBInsertDateValue($vte_metadata['date_end']).' '.$vte_metadata['endhr'].':'.$vte_metadata['endmin'] : $date_end = false;
					$date_start = $row['timer'];
					$timer = $PMUtils->getTimerRecurrences($date_start,$date_end,($vte_metadata['recurrence'] == 'on'),$vte_metadata['cron_value'],2);
					$timer = $timer[1];
					// check if the next occourence is in the past
					if (!empty($timer) && strtotime($timer) < time()) {
						// get all the missed occourences
						if ($date_end === false || (strtotime($date_end) > time())) $date_end = date('Y-m-d H:i:s');
						$timer = $PMUtils->getTimerRecurrences($timer,$date_end,($vte_metadata['recurrence'] == 'on'),$vte_metadata['cron_value'],10);
					}
					if (!empty($timer)) {
						if (!is_array($timer)) $timer = array($timer);
						foreach ($timer as $i => $t) {
							$calculate_next_occourence = ($i == count($timer)-1);
							$running_process = $adb->getUniqueID("{$table_prefix}_running_processes");
							$adb->pquery("insert into {$table_prefix}_running_processes(id,processmakerid,current) values(?,?,?)", array($running_process,$info['processid'],$info['elementid']));
							$info = array('processid'=>$info['processid'],'elementid'=>$info['elementid'],'running_process'=>$running_process,'calculate_next_occourence'=>$calculate_next_occourence);
							$PMUtils->createTimer('start',$t,$running_process,null,$info['elementid'],$info);
						}
					}
				}
			}			
		} elseif ($row['mode'] == 'intermediate') {
			$adb->pquery("delete from {$table_prefix}_running_processes_timer where id = ?", array($row['id']));
		}
	}
}
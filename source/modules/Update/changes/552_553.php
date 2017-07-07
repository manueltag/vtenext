<?php
global $adb;

$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';
$_SESSION['modules_to_update']['Visitreport'] = 'packages/vte/mandatory/Visitreport.zip';
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan'));
//crmv@33465 fix update
$params = Array(
	'Manda email all\'utente quando una nuova azienda viene creata',
	'Manda email all\'utente quando un nuovo contatto viene creato',
	'Manda un\'email all\'utente quando l\'utente del portale valido',
	'Nuova opportunita creata'
);
$result = $adb->pquery("SELECT workflow_id FROM com_vtiger_workflows WHERE summary IN (".generateQuestionMarks($params).")",$params);
//crmv@33465e
require_once("modules/com_vtiger_workflow/include.inc");
$vtWorkFlow = new VTWorkflowManager($adb);
if ($result && $adb->num_rows($result) > 0) {
	while($row=$adb->fetchByAssoc($result)) {
		$vtWorkFlow->delete($row['workflow_id']);
	}
}

$result = $adb->pquery('update vtiger_relatedlists set actions = ? where tabid = 13 and name = ?',array('ADD','get_timecards'));
?>
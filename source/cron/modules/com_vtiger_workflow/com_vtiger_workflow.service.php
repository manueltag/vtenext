<?php
/* crmv@47611 */

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'include/events/SqlResultIterator.inc';
require_once 'modules/com_vtiger_workflow/VTSimpleTemplate.inc';
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'modules/com_vtiger_workflow/VTWorkflowManager.inc';
require_once 'modules/com_vtiger_workflow/VTTaskManager.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowTemplateManager.inc';
require_once 'modules/com_vtiger_workflow/VTTaskQueue.inc';

global $adb;

function vtRunTaskJob($adb){
	$util = new VTWorkflowUtils();
	$adminUser = $util->adminUser();
	$tq = new VTTaskQueue($adb);
	$readyTasks = $tq->getReadyTasks();
	$tm = new VTTaskManager($adb);
	foreach($readyTasks as $pair){
		list($taskId, $entityId) = $pair;
		$task = $tm->retrieveTask($taskId);
		$entity = new VTWorkflowEntity($adminUser, $entityId);
		$task->doTask($entity);
	}
}

vtRunTaskJob($adb);
?>
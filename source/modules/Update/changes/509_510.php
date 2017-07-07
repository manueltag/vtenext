<?php
$_SESSION['modules_to_install']['WSAPP'] = 'packages/vte/mandatory/WSAPP.zip';
$_SESSION['modules_to_update']['Mobile'] = 'packages/vte/mandatory/Mobile.zip';
$_SESSION['modules_to_update']['M'] = 'packages/vte/mandatory/M.zip';
//crmv@392267
require_once 'include/utils/utils.php';
class VtigerCrmOutlookChanges{
    public function intializeChanges(){
       $this->addEmailFieldTypeInWs();
       $this->addFilterToListTypes();
	   $this->registerAssignToChangeWorkFlow();
    }

	public function registerAssignToChangeWorkFlow(){
		$this->addDependencyColumnToEventHandler();
		$this->registerVTEntityDeltaApi();
		$this->addDepedencyToVTWorkflowEventHandler();
	}

	public function registerVTEntityDeltaApi(){
		$db = PearDatabase::getInstance();
		$em = new VTEventsManager($db);
		$em->registerHandler('vtiger.entity.beforesave', 'data/VTEntityDelta.php', 'VTEntityDelta');
		$em->registerHandler('vtiger.entity.aftersave', 'data/VTEntityDelta.php', 'VTEntityDelta');
	}

	public function addDependencyColumnToEventHandler(){
		Vtiger_Utils::AlterTable('vtiger_eventhandlers',"dependent_on C(255) NOT NULL DEFAULT '[]'");
	}

	public function addDepedencyToVTWorkflowEventHandler(){
		$db = PearDatabase::getInstance();
		$dependentEventHandlers = array('VTEntityDelta');
		$dependentEventHandlersJson = Zend_Json::encode($dependentEventHandlers);
		$db->pquery('UPDATE vtiger_eventhandlers SET dependent_on=? WHERE event_name=? AND handler_class=?',
										array($dependentEventHandlersJson, 'vtiger.entity.aftersave', 'VTWorkflowEventHandler'));
	}
	public function addEmailFieldTypeInWs(){
		$db = PearDatabase::getInstance();
		$checkQuery = "SELECT * FROM vtiger_ws_fieldtype WHERE fieldtype=?";
		$params = array ("email");
		$checkResult = $db->pquery($checkQuery,$params);
		if($db->num_rows($checkResult) <= 0) {
			$fieldTypeId = $db->getUniqueID('vtiger_ws_fieldtype');
			$params = Array($fieldTypeId,'13','email');
			$sql = "INSERT INTO vtiger_ws_fieldtype(fieldtypeid,uitype,fieldtype) VALUES (".generateQuestionMarks($params).")";
			$db->pquery($sql,$params);
		}
	}
	public function addFilterToListTypes() {
		$db = PearDatabase::getInstance();
		$query = "SELECT operationid FROM vtiger_ws_operation WHERE name=?";
		$parameters = array("listtypes");
		$result = $db->pquery($query,$parameters);
		if($db->num_rows($result) > 0){
			$operationId = $db->query_result($result,0,'operationid');
			$operationName = 'fieldTypeList';
			$checkQuery = 'SELECT * FROM vtiger_ws_operation_parameters where operationid=? and name=?';
			$operationResult = $db->pquery($checkQuery,array($operationId,$operationName));
			if($db->num_rows($operationResult) <=0 ){
				$status = vtws_addWebserviceOperationParam($operationId,$operationName,
							'Encoded',0);
			}
		}
	}	
}
$instance = new VtigerCrmOutlookChanges();
$instance->intializeChanges();
//crmv@392267e
?>
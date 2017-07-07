<?php
//crmv@18199
class VTUpdateFieldTask extends VTTask{
	public $executeImmediately = false;	//crmv@32366
	
	public function getFieldNames(){return array('fieldName','fieldValue');}
	public function doTask($entity){
		global $adb,$current_user;
		$moduleName = $entity->getModuleName();
		list($moduleId,$id) = vtws_getIdComponents($entity->getId());
		
		$handler = vtws_getModuleHandlerFromName($moduleName, $current_user);
		$meta = $handler->getMeta();
		$indexList = $meta->getEntityTableIndexList();
		$referenceList = $meta->getReferenceFieldDetails();
		$moduleFields = $meta->getModuleFields();

		$tmp = explode(' ',$this->fieldName);
		$fieldname = $tmp[0];
		if (in_array($fieldname,array_keys($referenceList))) {		//moduli relazionati
			$fieldname_real = $tmp[count($tmp)-1];
			if (!in_array($entity->data[$fieldname],array('',0))) {
				list($moduleId,$id) = vtws_getIdComponents($entity->data[$fieldname]);
				$moduleName = getSalesEntityType($id);
				
				$handler = vtws_getModuleHandlerFromName($moduleName, $current_user);
				$meta = $handler->getMeta();
				$indexList = $meta->getEntityTableIndexList();
				$referenceList = $meta->getReferenceFieldDetails();
				$moduleFields = $meta->getModuleFields();
	
				$fieldObj = $moduleFields[$fieldname_real];
				if (!$fieldObj) return;
				$adb->pquery('update '.$fieldObj->getTableName().' set '.$fieldObj->getColumnName().' = ? where '.$indexList[$fieldObj->getTableName()].' = '.$id, array($this->fieldValue));
				VTEntityCache::setResetCache($entity->getId()); //crmv@79058
			}
		}
		else {		//modulo corrente
			$fieldObj = $moduleFields[$this->fieldName];
			if (!$fieldObj) return; //crmv@107238
			$adb->pquery('update '.$fieldObj->getTableName().' set '.$fieldObj->getColumnName().' = ? where '.$indexList[$fieldObj->getTableName()].' = '.$id, array($this->fieldValue));
			VTEntityCache::setResetCache($entity->getId()); //crmv@79058
		}
	}
}
//crmv@18199e
?>
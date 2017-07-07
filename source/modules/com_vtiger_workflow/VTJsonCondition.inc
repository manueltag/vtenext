<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
	require_once("include/Zend/Json.php");

	class VTJsonCondition{
		function  __construct(){
		}
		
		function evaluate($condition, $entityCache, $id){
			$expr = Zend_Json::decode($condition);
			$result=TRUE;
			$data = $entityCache->forId($id)->getData();
			
			foreach($expr as $cond){
				preg_match('/(\w+) : \((\w+)\) (\w+)/', $cond['fieldname'], $matches);
				if(count($matches)==0){
					$result = $this->checkCondition($entityCache->forId($id), $cond); //crmv@36510
				}else{
					list($full,  $referenceField, $referenceModule, $fieldname) = $matches;
					$referenceFieldId = $data[$referenceField];
					if($referenceFieldId != 0){
						$entity = $entityCache->forId($data[$referenceField]);
						if ($entity->getModuleName() == $referenceModule || ($entity->getModuleName() == 'Groups' && $referenceModule == 'Users')) {	//crmv@53590
							$entityData = $entity->getData();
							$cond['fieldname'] = $fieldname;
							$result = $this->checkCondition($entity, $cond); //crmv@36510
						}else{
							$result = false;
						}
					}else{
						if ($cond['operation'] == 'is' && empty($cond['value'])) return true;	//crmv@105312
						return false;
					}
				}
				if($result==false){
					return false;
				}
			}
			return true;
		}
		function startsWith($str, $subStr){
			$sl = strlen($str);
			$ssl = strlen($subStr);
			if($sl>=$ssl){
				return substr_compare($str,$subStr,0, $ssl)==0;
			}else{
				return FALSE;
			}
		}

		function endsWith($str, $subStr){
			$sl = strlen($str);
			$ssl = strlen($subStr);
			if($sl>=$ssl){
				return substr_compare($str,$subStr,$sl-$ssl, $ssl)==0;
			}else{
				return FALSE;
			}
		}
		
		//crmv@36510
		function checkCondition($entityData, $cond){
			$data = $entityData->getData();
			//crmv@36510 e
			$condition = $cond['operation'];
			$fieldValue=$data[$cond['fieldname']];
			$value = html_entity_decode($cond['value']);
			// crmv@68366
			if ($cond['fieldname'] == 'mailscanner_action') {
				if (is_readable('modules/SDK/src/204/204.php')) {
					require_once('modules/SDK/src/204/204.php');
					$fieldValue = getScannerNameFromAction($fieldValue);
				}
			}
			// crmv@68366e
			switch($condition){
				case "equal to":
					return $fieldValue == $value;
				case "less than":
					return $fieldValue < $value;
				case "greater than":
					return $fieldValue > $value;
				case "does not equal":
					return $fieldValue != $value;
				case "less than or equal to":
					return $fieldValue <= $value;
				case "greater than or equal to":
					return $fieldValue >= $value;
				case "is":
					if(preg_match('/([^:]+):boolean$/', $value, $match)){
						$value = $match[1];
						if($value=='true'){
							return $fieldValue==='on' || $fieldValue===1 || $fieldValue==='1';
						}else{
							return $fieldValue==='off' || $fieldValue===0 || $fieldValue==='0' || $fieldValue==='';
						}
					}else{
						return $fieldValue == $value;
					}
				//crmv@36510	
				case "is not":
					if (preg_match('/([^:]+):boolean$/', $value, $match)) {
						$value = $match[1];
						if ($value == 'true') {
							return $fieldValue === 'off' || $fieldValue === 0 || $fieldValue === '0' || $fieldValue === '' || $fieldvalue === NULL;	//crmv@53590
						} else {
							return $fieldValue === 'on' || $fieldValue === 1 || $fieldValue === '1';
						}
					} else {
						return $fieldValue != $value;
					}
				case "contains":
					if (strpos($value,",") !==false){
						$values = explode(",",$value);
						
					}
					else{
						$values = Array($value);
					}
					$found = false;
					foreach ($values as $val){
						if (strpos($fieldValue, $val) !== FALSE){
							$found = true;
							break;
						}
					}
					return $found;
				//crmv@36510 e						
				case "does not contain":
					return strpos($fieldValue, $value) === FALSE;
				case "starts with":
					return $this->startsWith($fieldValue,$value);
				case "ends with":
					return $this->endsWith($fieldValue, $value);
				case "matches":
					return preg_match($value, $fieldValue);
				//crmv@36510	
				case "has changed" :
					$entityDelta = new VTEntityDelta();
					$idParts = vtws_getIdComponents($entityData->getId());
					//crmv@42329
					$oldentity = $entityDelta->getOldEntity($entityData->getModuleName(), $idParts[1]);
					if (empty($oldentity)){
						$hasChanged = true;
					}
					else{
						$hasChanged = $entityDelta->hasChanged($entityData->getModuleName(), $idParts[1], $cond['fieldname']);
					}
					//crmv@42329e
					if (empty($value)) {
						return $hasChanged;
					} else {
						return $hasChanged && $fieldValue == $value;	//!!!crmv@51308!!!
					}
				//crmv@36510 e				
				// crmv@102879
				case "has exactly":
					$rows = is_array($fieldValue) ? count($fieldValue) : 0;
					return $rows == $value;
				case "has more than":
					$rows = is_array($fieldValue) ? count($fieldValue) : 0;
					return $rows > $value;
					break;
				case "has less than":
					$rows = is_array($fieldValue) ? count($fieldValue) : 0;
					return $rows < $value;
					break;
				// crmv@102879e
				default:
					//Unexpected condition
					throw new Exception("Found an unexpected condition: ".$condition);
			}
		}
	}
?>
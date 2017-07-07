<?php

require_once('modules/Geolocalization/Geolocalization.php');

class GeolocalizationHandler extends VTEventHandler {

	function handleEvent($eventName, $entityData) {
		global $adb, $table_prefix, $current_user;

		if ($eventName == 'vtiger.entity.aftersave') {

			$moduleName = $entityData->getModuleName();
			if ($moduleName == 'Activity') $moduleName = 'Calendar';
			
			$geo = Geolocalization::getInstance();

			if ($geo->isModuleHandled($moduleName)) {
				$data = $entityData->getData();
				$crmid  = $entityData->getId();
				$address = $geo->getAddress($moduleName, $crmid , $data);
				$geo->saveAddressCoords($moduleName, $crmid, $address);
			}
		}
	}

}

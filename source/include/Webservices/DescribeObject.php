<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
	
function vtws_describe($elementType,$user,$show_hidden_fields=false){	//crmv@120039
	
	global $log,$adb;
	$webserviceObject = VtigerWebserviceObject::fromName($adb,$elementType);
	$webserviceObject->show_hidden_fields = $show_hidden_fields;	//crmv@120039
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;
	
	$handler = new $handlerClass($webserviceObject,$user,$adb,$log);
	$meta = $handler->getMeta();
	
	$types = vtws_listtypes(null, $user);
	if(!in_array($elementType,$types['types'])){
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED,"Permission to perform the operation is denied");
	}
	
	$entity = $handler->describe($elementType);
	VTWS_PreserveGlobal::flush();
	return $entity;
}

//crmv@120039
function vtws_describe_all($elementType,$user){
	return vtws_describe($elementType,$user,true);
}	
//crmv@120039e
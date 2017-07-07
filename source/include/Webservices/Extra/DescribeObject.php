<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

require_once('include/Webservices/Extra/ModuleTypes.php');

function vtws_describeExtra($elementType,$user){
	$module_obj = WebserviceExtra::getInstance($elementType);
	if (!$module_obj){
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED,"Permission to perform the operation is denied");
	}
	$entity = $module_obj->describe($elementType);
	return $entity;
}
?>
<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

require_once('include/Webservices/Extra/WebserviceExtra.php');

function vtws_listtypesExtra($fieldTypeList, $user=false){
	$extramodules = WebserviceExtra::getAllExtraModules();
	$return_modules = Array();
	foreach ($extramodules as $module){
		$module_obj = WebserviceExtra::getInstance($module);
		if ($module_obj){
			$return_modules['types'][] = $module;
			$return_modules['information'][$module] = $module_obj->get_listtype($module);
			$return_modules['information'][$module]['isEntity'] = 2;
		}
	}
	return $return_modules;
}
?>
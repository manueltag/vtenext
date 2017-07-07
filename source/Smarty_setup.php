<?php
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
/* crmv@110561 */
require('Smarty/libs/Smarty.class.php');
class vtigerCRM_SmartyBase extends Smarty{
	
	/**This function sets the smarty directory path for the member variables	
	*/
	function __construct()
	{
		global $CALENDAR_DISPLAY, $WORLD_CLOCK_DISPLAY, $CALCULATOR_DISPLAY, $CHAT_DISPLAY, $current_user, $FCKEDITOR_DISPLAY;

		$this->Smarty();
		$this->template_dir = 'Smarty/templates';
		$this->compile_dir = 'Smarty/templates_c';
		$this->config_dir = 'Smarty/configs';
		$this->cache_dir = 'Smarty/cache';

		//$this->caching = true;
        //$this->assign('app_name', 'Login');
		$this->assign('CALENDAR_DISPLAY', $CALENDAR_DISPLAY); 
 		$this->assign('WORLD_CLOCK_DISPLAY', $WORLD_CLOCK_DISPLAY); 
 		$this->assign('CALCULATOR_DISPLAY', $CALCULATOR_DISPLAY); 
 		$this->assign('CHAT_DISPLAY', $CHAT_DISPLAY);
		//Added to provide User based Tagcloud
		$this->assign('TAG_CLOUD_DISPLAY',getTagCloudView($current_user->id) );
		
		//crmv@17889
		if(is_admin($current_user)){
			$this->assign('IS_ADMIN','1');
		}
		//crmv@17889e
                
		$this->assign('REQUEST_ACTION', $_REQUEST['action']);	//crmv@18549
		$this->assign("MENU_LAYOUT", getMenuLayout());			//crmv@18592
		//crmv@sdk-18509
		require_once('modules/SDK/SDK.php');
		$this->assign("SDK", new SDK());
		//crmv@sdk-18509e

		$this->assign('PERFORMANCE_CONFIG', PerformancePrefs::getAll()); // crmv@115378
		
		//crmv@118551
		$CU = CRMVUtils::getInstance();
		$this->assign("LAYOUT_CONFIG", $CU->getAllConfigurationLayout());
		//crmv@118551e
		
		if (!isset($this->_tpl_vars['FCKEDITOR_DISPLAY'])) $this->assign('FCKEDITOR_DISPLAY', $FCKEDITOR_DISPLAY);
		
		// crmv@119414
		global $theme;
		$TU = ThemeUtils::getInstance($theme);
		$this->assign("THEME_CONFIG", $TU->getAll());
		// crmv@119414e
	}

	/* crmv@sdk-18502	crmv@sdk-24699	crmv@25671	crmv@54375 */
	function fetch($resource_name, $cache_id = null, $compile_id = null, $display = false)
	{
		if (isModuleInstalled('SDK') && !in_array($resource_name,SDK::getNotRewritableSmartyTemplates())) {
	    	$sdkSmartyTemplate = SDK::getSmartyTemplate($_REQUEST);
	    	if ($sdkSmartyTemplate != '') {
	    		$resource_name = $sdkSmartyTemplate;
	    	}
    	}
    	if (!empty($this->_tpl_vars['RETURN_ID']) && !empty($this->_tpl_vars['RETURN_MODULE'])) {
    		$ret = getEntityName($this->_tpl_vars['RETURN_MODULE'],array($this->_tpl_vars['RETURN_ID']));
    		$this->assign('RETURN_RECORD_NAME', $ret[$this->_tpl_vars['RETURN_ID']]);
    		$this->assign('RETURN_RECORD_LINK', 'index.php?module='.$this->_tpl_vars['RETURN_MODULE'].'&action=DetailView&record='.$this->_tpl_vars['RETURN_ID']);
    	}
    	// crmv@119414
    	global $theme;
    	$TU = ThemeUtils::getInstance($theme);
    	$overrides = $TU->get('tpl_overrides') ?: array();
    	$resource_name = $overrides[$resource_name] ?: $resource_name;
    	// crmv@119414e
		return parent::fetch($resource_name, $cache_id, $compile_id, $display);
	}
}
// enable the override of standard vtigerCRM_Smarty methods
if (file_exists('modules/SDK/src/Smarty_setup.php')) {
	require_once('modules/SDK/src/Smarty_setup.php');
}
// if not extended, create an empty class
if (!class_exists('vtigerCRM_Smarty')) {
	class vtigerCRM_Smarty extends vtigerCRM_SmartyBase {}
}
?>

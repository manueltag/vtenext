<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@75301 */

require_once('include/BaseClasses.php');
require_once('modules/Area/Area.php');
require_once('vtlib/Vtecrm/Link.php');
require_once('Smarty_setup.php');

class VTEPageHeader extends SDKExtendableUniqueClass {
	
	public $headerTpl = 'Header.tpl';
	public $headerMenuTpl = 'HeaderMenu.tpl';
	public $headerAllMenuTpl = 'header/HeaderAllMenu.tpl';	//crmv@126984
	
	protected $isVteDesktop = false;
	
	/**
	 * Constructor, caches some variables
	 */
	public function __construct() {
		$this->isVteDesktop = isVteDesktop();
	}
	
	/**
	 * Display the VTE header
	 */
	public function displayHeader($options = array()) {
		// display the header
		$smarty = $this->initSmarty($options);
		if ($smarty) {
			$this->setModulesVars($smarty, $options);
			$this->setAreasVars($smarty, $options);
			$this->setAdvancedVars($smarty, $options);
			$this->setCustomVars($smarty, $options);
			$smarty->display($this->headerTpl);
		}
	}
	
	//crmv@126984
	public function displayAllMenu($options = array()) {
		$smarty = $this->initSmarty($options);
		if ($smarty) {
			$this->setModulesVars($smarty, $options);
			$this->setAreasVars($smarty, $options);
			$smarty->display($this->headerAllMenuTpl);
		}
	}
	//crmv@126984e
	
	/**
	 * Initialize the smarty template with some basic values
	 */
	protected function initSmarty($options = array()) {
		global $theme;
		global $app_strings, $app_list_strings;
		global $currentModule, $current_user;
		
		$smarty = new vtigerCRM_Smarty;
		
		$theme_path="themes/".$theme."/";
		$image_path=$theme_path."images/";
		
		$smarty->assign("THEME",$theme);
		$smarty->assign("IMAGEPATH",$image_path);
		$smarty->assign("APP", $app_strings);
		$smarty->assign("DATE", getDisplayDate(date("Y-m-d H:i")));
		$smarty->assign("MODULE_NAME", $currentModule);
		
		$smarty->assign('ISVTEDESKTOP', $this->isVteDesktop);
		if ($this->isVteDesktop) $_SESSION['menubar'] = 'no';
		
		$smarty->assign('HIDE_MENUS',$options['hide_menus']);	//crmv@62447 crmv@126984
		$smarty->assign("MENU_TPL", $this->headerMenuTpl);
		
		if ($current_user) {
			$smarty->assign("CURRENT_USER", getUserFullName($current_user->id));	//crmv@29079
			$smarty->assign("CURRENT_USER_ID", $current_user->id);
			if (is_admin($current_user)) {
				$smarty->assign("ADMIN_LINK", "<a href='index.php?module=Settings&action=index'>".$app_strings['LBL_SETTINGS']."</a>");
			}
		}
		
		$smarty->assign("PRINT_URL", "phprint.php?jt=".session_id().$GLOBALS['request_string']);
		
		//Assign the entered global search string to a variable and display it again
		//crmv@126984
		if ($options['query_string'] != '') {
			$smarty->assign("QUERY_STRING", htmlspecialchars($options['query_string'],ENT_QUOTES)); //ds@16s Bugfix "Cross-Site-Scripting"
		//crmv@126984e
		} else {
			$smarty->assign("QUERY_STRING", $app_strings['LBL_GLOBAL_SEARCH_STRING']);
		}
		
		// Gather the custom link information to display
		$hdrcustomlink_params = Array('MODULE'=>$currentModule);
		$COMMONHDRLINKS = Vtiger_Link::getAllByType(Vtiger_Link::IGNORE_MODULE, Array('HEADERLINK','HEADERSCRIPT', 'HEADERCSS'), $hdrcustomlink_params);
		$smarty->assign('HEADERLINKS', $COMMONHDRLINKS['HEADERLINK']);
		$smarty->assign('HEADERSCRIPTS', $COMMONHDRLINKS['HEADERSCRIPT']);
		$smarty->assign('HEADERCSS', $COMMONHDRLINKS['HEADERCSS']);
		
		// crmv@42024 - pass global JS vars to template
		$JSGlobals = ( function_exists('getJSGlobalVars') ? getJSGlobalVars() : array() );
		$smarty->assign('JS_GLOBAL_VARS', Zend_Json::encode($JSGlobals));
		// crmv@42024e
		
		return $smarty;
	}
	
	/**
	 * Set the variables for the module bar
	 */
	protected function setModulesVars(&$smarty, $options = array()) {
		global $app_strings, $app_list_strings;
		global $currentModule;
		
		$smarty->assign("MODULELISTS",$app_list_strings['moduleList']);
		
		//crmv@18592
		$menuLayout = getMenuLayout();
		if (!in_array($menuLayout['type'],array('modules'))) {
			$header_array = getHeaderArray();
			$smarty->assign("HEADERS",$header_array);
		}
		//crmv@18592e
		
		$smarty->assign("CATEGORY",getParentTab());
		
		$smarty->assign("QUICKACCESS",getAllParenttabmoduleslist($menuLayout['type']));
		
		if (!$this->isVteDesktop && $menuLayout['type'] == 'modules') {
			$menu_module_list = getMenuModuleList(true);
			$smarty->assign('VisibleModuleList', $menu_module_list[0]);
			$smarty->assign('OtherModuleList', $menu_module_list[1]);
	
			$arr1 = array_filter($menu_module_list[0],create_function('$v', 'if ($v[\'name\'] == \''.$currentModule.'\') return true;'));
			if (count($arr1) == 0 && !$options['fastmode'] && !in_array($currentModule,array('Settings','Users','Administration','com_vtiger_workflow','Area','Calendar','Messages','Processes')) && getParentTab() != 'Settings') { //crmv@31347 crmv@126984
				$_SESSION['last_module_visited'] = $currentModule;
			}
			$smarty->assign("LAST_MODULE_VISITED", $_SESSION['last_module_visited']);
		}
		
	}
	
	/**
	 * Set variables about areas
	 */
	public function setAreasVars(&$smarty, $options = array()) {
		$areaManager = AreaManager::getInstance();
		$menu_module_list = $areaManager->getModuleList();
		$smarty->assign('AREAMODULELIST', $menu_module_list[1]);
		$smarty->assign('BLOCK_AREA_LAYOUT', $areaManager->getToolValue('block_area_layout'));	//crmv@54707
		$smarty->assign('ENABLE_AREAS', $areaManager->getToolValue('enable_areas'));			//crmv@54707
	}
	
	/**
	 * Set some extra variables
	 */ 
	protected function setAdvancedVars(&$smarty, $options = array()) {
		global $theme, $current_user;
		global $CALCULATOR_DISPLAY;
		
		$theme_path="themes/".$theme."/";
		$image_path=$theme_path."images/";
		
		if ($CALCULATOR_DISPLAY == 'true') {
			require_once("include/calculator/Calc.php");
			$smarty->assign("CALC", get_calc($image_path));
		}
		
		//crmv@7220+18038
		$smarty->assign("USE_ASTERISK", get_use_asterisk($current_user->id,'incoming'));
		//crmv@7220+18038 end

		// crmv@92034
		if (PerformancePrefs::getBoolean('JS_DEBUG', false)) {
			$smarty->assign("ENABLE_JS_LOGGER", true);
		}
		// crmv@92034e
	}
	
	/**
	 * Set variables to customize the header
	 * This method can be overridden to provide customizations.
	 * The content of the variables is drawn directly in the page
	 */ 
	protected function setCustomVars(&$smarty, $options = array()) {
		$overrides = array(
			'post_menu_bar' => null,
			'post_primary_bar' => null,
			'post_secondary_bar' => null,
			'user_icon' => null,
			'settings_icon' => null,
		);
		$smarty->assign("HEADER_OVERRIDE", $overrides);
	}
	
}

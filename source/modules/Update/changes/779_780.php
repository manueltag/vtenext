<?php
SDK::setLanguageEntry('Settings', 'it_it', 'LBL_MENU_TABS', 'Impostazione voci di menù');
SDK::setLanguageEntry('Settings', 'it_it', 'LBL_MENU_MODULELIST', 'Lista moduli');
SDK::setLanguageEntry('Settings', 'it_it', 'LBL_OTHER_MODULES', 'Ulteriori moduli');
SDK::setLanguageEntry('Settings', 'it_it', 'LBL_MENU_TYPE', 'Tipo menù');
SDK::setLanguageEntry('Settings', 'pt_br', 'LBL_MENU_TYPE', 'Tipo menu');
SDK::setLanguageEntry('APP_STRINGS', 'it_it', 'LBL_LIST', 'Lista');
SDK::setLanguageEntries('Settings', 'LBL_MENU_TABLIST', array('it_it'=>'Moduli separati in menù','en_us'=>'Modules in menu','pt_br'=>'Módulos separados por menu'));
SDK::setLanguageEntries('Settings', 'LBL_MENU_TABS_NAME', array('it_it'=>'Menù','en_us'=>'Menu','pt_br'=>'Menu'));
SDK::setLanguageEntries('Settings', 'LBL_MENU_AREAS', array('it_it'=>'Moduli raggruppati in aree','en_us'=>'Modules grouped into areas'));
SDK::setLanguageEntries('APP_STRINGS', 'HightlightArea', array('it_it'=>'Moduli principali','en_us'=>'Main modules'));
SDK::setLanguageEntries('APP_STRINGS', 'ClientsArea', array('it_it'=>'Anagrafiche','en_us'=>'Clients'));
SDK::setLanguageEntries('APP_STRINGS', 'AfterSalesArea', array('it_it'=>'Post vendita','en_us'=>'After sales'));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_FULL_LIST', array('it_it'=>'Lista completa','en_us'=>'Full list'));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_AREAS_SETTINGS', array('it_it'=>'Gestisci aree','en_us'=>'Area settings'));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_CREATE_AREA', array('it_it'=>'Crea area','en_us'=>'Create area'));

$notEntityModules = array('Popup','Area');
foreach ($notEntityModules as $module) {
	$Mod = Vtiger_Module::getInstance($module);
	if (empty($Mod)) {
		$Mod = new Vtiger_Module();
		$Mod->name = $module;
		$Mod->isentitytype = false;
		$Mod->save();
		$Mod->hide(array('hide_module_manager'=>1, 'hide_profile'=>1));
		$adb->pquery("UPDATE {$table_prefix}_tab SET customized=0 WHERE name=?", array($module));
		
		require_once("modules/$module/$module.php");
		$instance = new $module();
		if ($instance) {
			$instance->vtlib_handler($module, Vtiger_Module::EVENT_MODULE_POSTINSTALL);
		}
	}
}
?>
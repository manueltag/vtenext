<?php
$inventoryModules = getInventoryModules();
if (!empty($inventoryModules)) {
	foreach($inventoryModules as $inventoryModule) {
		$moduleInstance = Vtecrm_Module::getInstance($inventoryModule);
		$blockInstance = Vtecrm_Block::getInstance('LBL_RELATED_PRODUCTS', $moduleInstance);
		if (!$blockInstance) {
			$blockInstance = new Vtecrm_Block();
			$blockInstance->label = 'LBL_RELATED_PRODUCTS';
			$moduleInstance->addBlock($blockInstance);
			SDK::setLanguageEntries($inventoryModule, 'LBL_RELATED_PRODUCTS', array('it_it'=>'Dettagli Prodotto','en_us'=>'Product Details'));
		}
	}
}
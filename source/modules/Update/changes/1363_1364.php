<?php 

/* crmv@105193 */

// translations
$trans = array(

	'APP_STRINGS' => array(
		'it_it' => array(
			'AddModuleHomeView' => 'Nuovo tab semplice',
			'LBL_CHOOSE_MODHOME_BLOCK_TYPE' => 'Scegli il tipo di widget',
			'LBL_NO_HOME_BLOCKS' => 'Nessun widget impostato. Clicca %s per crearne uno nuovo', // old
			'LBL_ADD_WIDGET'=>'Aggiungi widget',
			'LBL_ADVANCED' => 'Avanzate',
			'LBL_CONFIG_PAGE' => 'Configura pagina',
		),
		'en_us' => array(
			'AddModuleHomeView' => 'New basic tab',
			'LBL_CHOOSE_MODHOME_BLOCK_TYPE' => 'Choose the widget type',
			'LBL_NO_HOME_BLOCKS' => 'No widgets configured. Click %s to create a new one', // old
			'LBL_ADD_WIDGET'=>'Add widget',
			'LBL_ADVANCED' => 'Advanced',
			'LBL_CONFIG_PAGE' => 'Configure page',
		),
	),
);
foreach ($trans as $module=>$modlang) {
	foreach ($modlang as $lang=>$translist) {
		foreach ($translist as $label=>$translabel) {
			SDK::setLanguageEntry($module, $lang, $label, $translabel);
		}
	}
}
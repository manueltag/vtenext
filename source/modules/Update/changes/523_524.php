<?php
global $adb;
$result = $adb->query("SELECT * FROM sdk_menu_fixed WHERE title = 'LBL_FAVORITES'");
if ($result && $adb->num_rows($result) > 0) {
	//do nothing
} else {
	SDK::setMenuButton('fixed','LBL_FAVORITES',"fnvshobj(this,'favorites');",'themes/images/favorites.png');
}
SDK::setLanguageEntries('APP_STRINGS', 'LBL_FAVORITE', array('it_it'=>'Preferito','en_us'=>'Favorite','pt_br'=>'Preferido'));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_FAVORITES', array('it_it'=>'Preferiti','en_us'=>'Favorites','pt_br'=>'Favoritos'));
$schema_table = '<schema version="0.3">
				  <table name="vte_favorites">
				  <opt platform="mysql">ENGINE=InnoDB</opt>
					<field name="userid" type="I" size="19">
					  <KEY/>
					</field>
					<field name="crmid" type="I" size="19">
					  <KEY/>
					</field>
					<field name="module" type="C" size="100"/>
				  </table>
				</schema>';
if(!Vtiger_Utils::CheckTable('vte_favorites')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$result = $adb->query("SELECT * FROM sdk_menu_fixed WHERE title = 'Todos'");
if (!$result || $adb->num_rows($result) == 0) {
	SDK::setMenuButton('fixed','Todos',"fnvshobj(this,'todos');getTodoList();",'themes/images/todos.png');
}

$trans_it = array(
 'LBL_SAVELOGIN_HELP' => 'E\' necessario settare la variabile session.gc_maxlifetime = 2592000 nel php.ini per attivare la funzionalità.',
);
$trans_en = array(
 'LBL_SAVELOGIN_HELP' => 'You must set the session.gc_maxlifetime = 2592000 in your php.ini in order to activate this feature.',
);
$trans_pt = array(
 'LBL_SAVELOGIN_HELP' => 'E\' necessário definir a variável session.gc_maxlifetime = 2592000 no php.ini para ativar a função.',
);
foreach ($trans_it as $label=>$trans) {
	SDK::setLanguageEntry('Users', 'it_it', $label, $trans);
	SDK::setLanguageEntry('Users', 'en_us', $label, $trans_en[$label]);
	SDK::setLanguageEntry('Users', 'pt_br', $label, $trans_pt[$label]);
}
?>
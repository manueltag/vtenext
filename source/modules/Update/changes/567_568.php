<?php
global $adb;
$result = $adb->pquery('SELECT id FROM sdk_menu_fixed WHERE title = ?',array('Todos'));
if ($result && $adb->num_rows($result) > 0) {
	SDK::unsetMenuButton('fixed', $adb->query_result($result,0,'id'));
}
SDK::setLanguageEntries('Webmails','Show',array('it_it'=>'Mostra','en_us'=>'Show','pt_br'=>'Mostrar'));
?>
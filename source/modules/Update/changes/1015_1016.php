<?php
SDK::setLanguageEntries('Settings', 'Inherited', array('it_it'=>'Pubblico','en_us'=>'Public','de_de'=>'Öffentlich','pt_br'=>'Público','nl_nl'=>'Publiek'));
SDK::setLanguageEntries('Users', 'Inherited', array('it_it'=>'Pubblico','en_us'=>'Public','de_de'=>'Öffentlich','pt_br'=>'Público','nl_nl'=>'Publiek'));
SDK::setLanguageEntries('Messages', 'LBL_RELATED_MESSAGES', array('it_it'=>'Messaggi collegati','en_us'=>'Related Messages','de_de'=>'Verwandte Meldungen','pt_br'=>'Mensagens relacionadas','nl_nl'=>'Gerelateerde berichten'));
SDK::setLanguageEntries('Settings', 'Assigned', array('it_it'=>'accedere ai propri record','en_us'=>'can only access their own records','de_de'=>'nur Zugriff auf ihre eigenen Aufzeichnungen','pt_br'=>'só pode acessar seus próprios registros','nl_nl'=>'kan alleen toegang krijgen tot hun eigen administratie'));
SDK::setLanguageEntries('Users', 'Assigned', array('it_it'=>'accedere ai propri record','en_us'=>'can only access their own records','de_de'=>'nur Zugriff auf ihre eigenen Aufzeichnungen','pt_br'=>'só pode acessar seus próprios registros','nl_nl'=>'kan alleen toegang krijgen tot hun eigen administratie'));
SDK::setLanguageEntries('Settings', 'LBL_ASSIGNED', array('it_it'=>'Solo proprietario','en_us'=>'Only owner','de_de'=>'nur Eigentümer','pt_br'=>'Somente o proprietário','nl_nl'=>'Alleen eigenaar'));
SDK::setLanguageEntries('Users', 'LBL_ASSIGNED', array('it_it'=>'Solo proprietario ','en_us'=>'Only owner','de_de'=>'nur Eigentümer','pt_br'=>'Somente o proprietário','nl_nl'=>'Alleen eigenaar'));

global $adb, $table_prefix;
$result = $adb->pquery("select * from {$table_prefix}_org_share_act_mapping where share_action_id = ?",array(8));
if ($result && $adb->num_rows($result) == 0) {
	$adb->pquery("insert into {$table_prefix}_org_share_act_mapping values(?,?)",array(8,'Assigned'));
	$result = $adb->pquery("select * from {$table_prefix}_org_share_action2tab where share_action_id = ? and tabid = ?",array(8,getTabid('Messages')));
	if ($result && $adb->num_rows($result) == 0) {
		$adb->pquery("insert into {$table_prefix}_org_share_action2tab values(?,?)",array(8,getTabid('Messages')));
	}
	$adb->pquery("delete from {$table_prefix}_org_share_action2tab where tabid = ? and share_action_id in (1,2)",array(getTabid('Messages')));
}
?>
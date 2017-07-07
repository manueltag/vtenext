<?php
global $adb;
$res = $adb->query("SELECT tabid FROM vtiger_tab WHERE name = 'RecycleBin'");
if ($res && $adb->num_rows($res)>0) {
	$_SESSION['modules_to_update']['RecycleBin'] = 'packages/vte/optional/RecycleBin.zip';
}
$res = $adb->query("SELECT tabid FROM vtiger_tab WHERE name = 'Projects'");
if ($res && $adb->num_rows($res)>0) {
	$_SESSION['modules_to_update']['Projects'] = 'packages/vte/optional/Projects.zip';
}
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter'));
$_SESSION['modules_to_update']['Sms'] = 'packages/vte/mandatory/Sms.zip';
$_SESSION['modules_to_update']['Fax'] = 'packages/vte/mandatory/Fax.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';

require_once('modules/SDK/InstallTables.php');

SDK::setLanguageEntries('Users', 'LBL_AVATAR_INSTRUCTIONS', array('it_it'=>'Per impostare la miniatura è necessario inserire la fotografia.','en_us'=>'You have to insert the photograph before set the avatar.','pt_br'=>'Você tem que inserir a fotografia antes de definir o miniatura.'));
SDK::setLanguageEntries('Users', 'User Image', array('it_it'=>'Fotografia','en_us'=>'Photograph','pt_br'=>'Imagem'));
SDK::setLanguageEntries('Users', 'Avatar', array('it_it'=>'Miniatura','en_us'=>'Avatar','pt_br'=>'Miniatura'));
SDK::setLanguageEntries('Users', 'MODCOMMENTS', array('it_it'=>'Conversazioni','en_us'=>'Talks','pt_br'=>'Conversas'));
SDK::setLanguageEntries('Home', 'MODCOMMENTS', array('it_it'=>'Conversazioni','en_us'=>'Talks','pt_br'=>'Conversas'));

SDK::setLanguageEntries('ModComments', 'LBL_MODCOMMENTS_COMMUNICATIONS', array('it_it'=>'Conversazioni','en_us'=>'Talks','pt_br'=>'Conversas'));
SDK::setLanguageEntries('ModComments', 'ModComments', array('it_it'=>'Conversazioni','en_us'=>'Talks','pt_br'=>'Conversas'));
SDK::setLanguageEntries('ModComments', 'Comments', array('it_it'=>'Conversazioni','en_us'=>'Talks','pt_br'=>'Conversas'));
SDK::setLanguageEntries('ModComments', 'SINGLE_ModComments', array('it_it'=>'Conversazione','en_us'=>'Talk','pt_br'=>'Conversa'));
SDK::setLanguageEntries('ModComments', 'Comment', array('it_it'=>'Conversazione','en_us'=>'Talk','pt_br'=>'Conversa'));
SDK::setLanguageEntries('ModComments', 'LBL_MODCOMMENTS_INFORMATION', array('it_it'=>'Informazioni Conversazione','en_us'=>'Talk Information','pt_br'=>'Informação Conversa'));
SDK::setLanguageEntries('ModComments', 'Related To Comments', array('it_it'=>'Conversazione Padre','en_us'=>'Parent Talk','pt_br'=>'Conversa Pai'));
SDK::setLanguageEntries('ModComments', 'LBL_ADD_COMMENT', array('it_it'=>'Inizia una nuova conversazione','en_us'=>'Start a new talk','pt_br'=>'Iniciar uma nova conversa'));
SDK::setLanguageEntries('ModComments', 'LBL_DEFAULT_REPLY_TEXT', array('it_it'=>'Rispondi','en_us'=>'Reply','pt_br'=>'Responder'));
SDK::setLanguageEntries('ModComments', 'LBL_MODCOMMENTS_REPLIES', array('it_it'=>'Risposte','en_us'=>'Replies','pt_br'=>'Respostas'));
SDK::setLanguageEntries('ModComments', 'LBL_MODCOMMENTS_REPLY', array('it_it'=>'Risposta','en_us'=>'Reply','pt_br'=>'Resposta'));
SDK::setLanguageEntries('ModComments', 'LBL_PUBLISH', array('it_it'=>'Pubblica','en_us'=>'Publish','pt_br'=>'Publicar'));
SDK::setLanguageEntries('ModComments', 'LBL_SHOW_ALL_REPLIES_1', array('it_it'=>'Mostra tutte le','en_us'=>'Show all the','pt_br'=>'Mostrar todas as'));
SDK::setLanguageEntries('ModComments', 'LBL_SHOW_ALL_REPLIES_2', array('it_it'=>'risposte','en_us'=>'replies','pt_br'=>'respostas'));
SDK::setLanguageEntries('ModComments', 'LBL_AGO', array('it_it'=>'fa','en_us'=>'ago','pt_br'=>'atrás'));
SDK::setLanguageEntries('ModComments', 'lbl_second', array('it_it'=>'secondo','en_us'=>'second','pt_br'=>'segundo'));
SDK::setLanguageEntries('ModComments', 'lbl_seconds', array('it_it'=>'secondi','en_us'=>'seconds','pt_br'=>'segundos'));
SDK::setLanguageEntries('ModComments', 'lbl_minute', array('it_it'=>'minuto','en_us'=>'minute','pt_br'=>'minuto'));
SDK::setLanguageEntries('ModComments', 'lbl_minutes', array('it_it'=>'minuti','en_us'=>'minutes','pt_br'=>'minutos'));
SDK::setLanguageEntries('ModComments', 'lbl_hour', array('it_it'=>'ora','en_us'=>'hour','pt_br'=>'hora'));
SDK::setLanguageEntries('ModComments', 'lbl_hours', array('it_it'=>'ore','en_us'=>'hours','pt_br'=>'horas'));
SDK::setLanguageEntries('ModComments', 'lbl_day', array('it_it'=>'giorno','en_us'=>'day','pt_br'=>'dia'));
SDK::setLanguageEntries('ModComments', 'lbl_days', array('it_it'=>'giorni','en_us'=>'days','pt_br'=>'dias'));
SDK::setLanguageEntries('ModComments', 'lbl_week', array('it_it'=>'settimana','en_us'=>'week','pt_br'=>'semana'));
SDK::setLanguageEntries('ModComments', 'lbl_weeks', array('it_it'=>'settimane','en_us'=>'weeks','pt_br'=>'semanas'));
SDK::setLanguageEntries('ModComments', 'lbl_month', array('it_it'=>'mese','en_us'=>'month','pt_br'=>'mês'));
SDK::setLanguageEntries('ModComments', 'lbl_months', array('it_it'=>'mesi','en_us'=>'months','pt_br'=>'mês'));
SDK::setLanguageEntries('ModComments', 'lbl_year', array('it_it'=>'anno','en_us'=>'year','pt_br'=>'ano'));
SDK::setLanguageEntries('ModComments', 'lbl_years', array('it_it'=>'anni','en_us'=>'years','pt_br'=>'anos'));
SDK::setLanguageEntries('ModComments', 'lbl_decade', array('it_it'=>'decade','en_us'=>'decade','pt_br'=>'década'));
SDK::setLanguageEntries('ModComments', 'lbl_decades', array('it_it'=>'decadi','en_us'=>'decades','pt_br'=>'décadas'));
SDK::setLanguageEntries('ModComments', 'lbl_now', array('it_it'=>'adesso','en_us'=>'now','pt_br'=>'agora'));
SDK::setLanguageEntries('ModComments', 'LBL_CROP_AVATAR', array('it_it'=>'Ritaglia la miniatura','en_us'=>'Crop the avatar','pt_br'=>'Colheita o miniatura'));
SDK::setLanguageEntries('ModComments', 'LBL_CROP', array('it_it'=>'Ritaglia','en_us'=>'Crop','pt_br'=>'Colheita'));
SDK::setLanguageEntries('ModComments', 'LBL_PLEASE_SELECT_REGION', array('it_it'=>'Prego selezinare una porzione di immagine','en_us'=>'Please select a region of the image','pt_br'=>'Por favor, selecione uma região da imagem'));
SDK::setLanguageEntries('ModComments', 'LBL_SAVE_AVATAR', array('it_it'=>'Vuoi salvare questa miniatura?','en_us'=>'Do you want to save this avatar?','pt_br'=>'Você quer salvar esta miniatura?'));
SDK::setLanguageEntries('ModComments', 'Visibility', array('it_it'=>'Visibilità','en_us'=>'Visibility','pt_br'=>'Visibilidade'));
SDK::setLanguageEntries('ModComments', 'All', array('it_it'=>'Tutti','en_us'=>'All','pt_br'=>'Todos'));
SDK::setLanguageEntries('ModComments', 'LBL_TO', array('it_it'=>'a','en_us'=>'to','pt_br'=>'para'));
SDK::setLanguageEntries('ModComments', 'LBL_ABOUT', array('it_it'=>'riguardo a','en_us'=>'about','pt_br'=>'sobre'));
SDK::setLanguageEntries('ModComments', 'LBL_SHOW_OTHER_TALKS', array('it_it'=>'Mostra altre conversazioni','en_us'=>'Show other talks','pt_br'=>'Mostrar outras conversas'));

$modCommentsInstance = Vtiger_Module::getInstance('ModComments');

SDK::addView('ModComments', 'modules/SDK/src/modules/ModComments/ModCommentsView.php', 'constrain', 'continue');

$modCommentsInstance->setRelatedList($modCommentsInstance, 'LBL_MODCOMMENTS_REPLIES', Array('ADD'), 'get_replies');

$fields[] = array('module'=>'ModComments','block'=>'LBL_OTHER_INFORMATION','name'=>'visibility_comm','label'=>'Visibility','uitype'=>'15','columntype'=>'C(255)','picklist'=>array('All','Users'),'quickcreate'=>1);
$fields[] = array('module'=>'Users','block'=>'LBL_USER_IMAGE_INFORMATION','name'=>'avatar','label'=>'Avatar','uitype'=>'205','columntype'=>'C(255)');
include('modules/SDK/examples/fieldCreate.php');
SDK::setUitype(205,'modules/SDK/src/205/205.php','modules/SDK/src/205/205.tpl','');

$adb->query("update vtiger_modcomments set visibility_comm = 'All' where visibility_comm is null or visibility_comm = ''");

$skip_modcomm_module = array('Webmails','Emails','Fax','Sms','Events','ModComments');
$result = $adb->pquery('SELECT name FROM vtiger_tab WHERE isentitytype = 1 AND name NOT IN ('.generateQuestionMarks($skip_modcomm_module).')',$skip_modcomm_module);
$modCommentsFocus = CRMEntity::getInstance('ModComments');
if ($result && $adb->num_rows($result) > 0) {
	$modcomm_module = array();
	while($row=$adb->fetchByAssoc($result)) {
		$modcomm_module[] = $row['name'];
	}
	$modCommentsFocus->addWidgetTo($modcomm_module);
}

$schema_table = '<schema version="0.3">
				  <table name="vtiger_modcomments_users">
				  	<opt platform="mysql">ENGINE=InnoDB</opt>
					<field name="id" type="I" size="19">
					  <KEY/>
					</field>
					<field name="user" type="I" size="19">
					  <KEY/>
				    </field>
				  </table>
				</schema>';
if(!Vtiger_Utils::CheckTable('vtiger_modcomments_users')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$homeModule = Vtiger_Module::getInstance('SDK');
$homeModule->addLink('HEADERSCRIPT', 'NotificationsScript', 'modules/SDK/src/Notifications/NotificationsCommon.js');
$homeModule->addLink('HEADERCSS', 'NotificationsScript', 'modules/SDK/src/Notifications/NotificationsCommon.css');

$schema_table = '<schema version="0.3">
				  <table name="vte_notifications">
				  	<opt platform="mysql">ENGINE=InnoDB</opt>
					<field name="id" type="I" size="19">
					  <KEY/>
					</field>
					<field name="userid" type="I" size="19">
					  <KEY/>
				    </field>
					<field name="type" type="C" size="100">
					  <KEY/>
				    </field>
				  </table>
				</schema>';
if(!Vtiger_Utils::CheckTable('vte_notifications')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$adb->pquery('insert into vtiger_home_iframe (hometype,url) values (?,?)',array('MODCOMMENTS','index.php?module=ModComments&action=ModCommentsAjax&file=ModCommentsWidgetHandler&ajax=true&widget=DetailViewBlockCommentWidget'));
require_once('modules/Users/Users.php');
$usersInstance = new Users();
$result = $adb->query('SELECT id FROM vtiger_users');
while($row = $adb->fetchByAssoc($result)) {
	$uid = $row['id'];
	
	$s18=$adb->getUniqueID("vtiger_homestuff");
	$visibility=$usersInstance->getDefaultHomeModuleVisibility('MODCOMMENTS',$inVal);
	$sql="insert into vtiger_homestuff values(?,?,?,?,?,?)";
	$res=$adb->pquery($sql, array($s18,18,'Iframe',$uid,$visibility,'MODCOMMENTS'));
	
	$sql="insert into vtiger_homedefault values(".$s18.",'MODCOMMENTS',0,'NULL')";
	$adb->query($sql);
}
?>
<?php
global $adb, $table_prefix;

$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$res = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE name = 'CustomerPortal'");
if ($res && $adb->num_rows($res)>0) $_SESSION['modules_to_update']['CustomerPortal'] = 'packages/vte/optional/CustomerPortal.zip';

SDK::setLanguageEntries('Messages', 'LBL_FILTER_FOLDERS_NOT_FOUND', array(
	'it_it'=>'Alcuni filtri non sono stati applicati in quanto si riferiscono a cartelle non esistenti:',
	'en_us'=>'Some filters were not applied because they refer to folders that no exist anymore:',
	'de_de'=>'Einige Filter wurden nicht angewendet, weil sie in Ordner, die nicht mehr existiert beziehen:',
	'nl_nl'=>'Sommige filters zijn niet toegepast omdat ze verwijzen naar mappen die niet meer bestaat:',
	'pt_br'=>'Alguns filtros não foram aplicados porque se referem a pastas que não existem mais:',
));

$adb->pquery("UPDATE {$table_prefix}_notifyscheduler SET notificationbody = ? WHERE schedulednotificationname = ?", array('LBL_ACTIVITY_REMINDER_DESCRIPTION','LBL_ACTIVITY_REMINDER_DESCRIPTION'));
$adb->pquery("UPDATE {$table_prefix}_notifyscheduler SET notificationsubject = ? WHERE schedulednotificationname = ? AND notificationsubject like ?", array('Notifica promemoria attivita`','LBL_ACTIVITY_REMINDER_DESCRIPTION','Notifica promemoria attivit%'));
$adb->pquery("UPDATE {$table_prefix}_notifyscheduler SET notificationbody = ? WHERE schedulednotificationname = ? AND notificationbody like ?", array('Il ticket e` in approvazione.','LBL_TICKETS_DESCRIPTION','% in approvazione.'));
$adb->pquery("UPDATE {$table_prefix}_notifyscheduler SET notificationbody = ? WHERE schedulednotificationname = ? AND notificationbody like ?", array('Troppi ticket aperti per la stessa entita`.','LBL_MANY_TICKETS_DESCRIPTION','Troppi ticket aperti per la stessa entit%'));

$invoice_body = "Gentile {HANDLER},

La quantita`  in magazzino di {PRODUCTNAME} e` di {CURRENTSTOCK}. Si prega di ordinare un numero di prodotto inferiore al numero indicato di seguito: {REORDERLEVELVALUE}.

Consideri urgente questa comunicazione in quanto la fattura e` gia`  stata inoltrata.

Tipo di urgenza: ALTA

Grazie,
{CURRENTUSER}";
$adb->pquery("UPDATE {$table_prefix}_inventorynotify SET notificationbody = ? WHERE notificationname = ? AND label = ?", array($invoice_body,'InvoiceNotification','InvoiceNotificationDescription'));

$quote_body = "Gentile {HANDLER},

Preventivo per un quantitativo di {QUOTEQUANTITY} {PRODUCTNAME}. L'attuale giacienza di {PRODUCTNAME} e` di {CURRENTSTOCK}.

Tipo di urgenza: BASSA

Grazie,
{CURRENTUSER}";
$adb->pquery("UPDATE {$table_prefix}_inventorynotify SET notificationbody = ? WHERE notificationname = ? AND label = ?", array($quote_body,'QuoteNotification','QuoteNotificationDescription'));

$so_body = "Gentile {HANDLER},

L'ordine di vendita e` stato generato per {SOQUANTITY}  {PRODUCTNAME}. L'attuale giacienza a magazzino di {PRODUCTNAME} e` di {CURRENTSTOCK}.

L'ordine di vendita e` stato generato, i dati devono essere trattati con la massima urgenza.

Tipo di urgenza: ALTA

Grazie,
{CURRENTUSER}";
$adb->pquery("UPDATE {$table_prefix}_inventorynotify SET notificationbody = ? WHERE notificationname = ? AND label = ?", array($so_body,'SalesOrderNotification','SalesOrderNotificationDescription'));

$adb->pquery("update {$table_prefix}_field set presence = ? where tabid = 8 and fieldname = ?",array(0,'filestatus'));

$adb->pquery("update {$table_prefix}_links set cond = ? where tabid = 8 and linklabel = ?",array('checkPermittedLink:include/utils/crmv_utils.php','LBL_ADD_DOCREVISION'));

if(!Vtiger_Utils::CheckTable($table_prefix.'_modnotifications_types')) {
	global $adb;
	$schema = '<?xml version="1.0"?>
				<schema version="0.3">
				  <table name="'.$table_prefix.'_modnotifications_types">
				  <opt platform="mysql">ENGINE=InnoDB</opt>
					<field name="id" type="I" size="19">
				   	  <key/>
				    </field>
				    <field name="type" type="C" size="50"/>
				    <field name="action" type="C" size="50"/>
				    <field name="custom" type="I">
				      <DEFAULT value="1"/>
				    </field>
				  </table>
				</schema>';
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema));
}

$old_notification_types = array(
	'Changed followed record'=>array('action'=>'has changed'),
	'Changed record'=>array('action'=>'has changed'),
	'Created record'=>array('action'=>'has created and assigned to you'),
	'Ticket changed'=>array('action'=>'has changed'),
	'Ticket created'=>array('action'=>'has created and assigned to you'),
	'Ticket portal replied'=>array('action'=>'responded to'),
	'Ticket portal created'=>array('action'=>'has created'),
	'Product stock level'=>array('action'=>'MSG_STOCK_LEVEL'),
	'Calendar invitation'=>array('action'=>'has invited you to'),
	'Calendar invitation edit'=>array('action'=>'has changed your invitation to'),
	'Calendar invitation answer yes'=>array('action'=>'will attend'),
	'Calendar invitation answer no'=>array('action'=>'did not attend'),
	'Calendar invitation answer yes contact'=>array('action'=>'will attend'),
	'Calendar invitation answer no contact'=>array('action'=>'did not attend'),
	'Reminder calendar'=>array('action'=>'reminder activity'),
	'Relation'=>array('action'=>'has related'),
	'ListView changed'=>array('action'=>'Has been changed'),
	'Import Completed'=>array('action'=>'Import Completed'), //crmv@31126
	'Revisioned document' => array('action'=>'added a revision to'),
);

foreach($old_notification_types as $type => $values) {
	$adb->pquery("INSERT INTO {$table_prefix}_modnotifications_types(id, type, action, custom) VALUES(?, ?, ?, ?)", array($adb->getUniqueID("{$table_prefix}_modnotifications_types"), $type, $values['action'], 0));
}

SDK::setLanguageEntries('ModNotifications', 'added a revision to', array('it_it'=>'ha aggiunto una revisione a','en_us'=>'added a revision to','de_de'=>'fügte eine Revision','nl_nl'=>'een herziening toegevoegd aan','pt_br'=>'adicionada uma revisão'));
?>
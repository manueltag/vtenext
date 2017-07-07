<?php
$_SESSION['modules_to_update']['M'] = 'packages/vte/mandatory/M.zip';

global $adb;

//Notifiche Inventario
$body = "Gentile {HANDLER},

La quantità a magazzino di {PRODUCTNAME} è di {CURRENTSTOCK}. Si prega di ordinare un numero di prodotto inferiore al numero indicato di seguito: {REORDERLEVELVALUE}.

Consideri urgente questa comunicazione in quanto la fattura è già stata inoltrata.

Tipo di urgenza: ALTA

Grazie,
{CURRENTUSER}";
$adb->pquery('update vtiger_inventorynotify set notificationbody = ? where notificationsubject = ?',array($body,'{PRODUCTNAME} Stock Level is Low'));
$adb->pquery('update vtiger_inventorynotify set notificationsubject = ? where notificationsubject = ?',array('{PRODUCTNAME} Livello magazzino basso','{PRODUCTNAME} Stock Level is Low'));
$body = "Gentile {HANDLER},

Preventivo per un quantitativo di  {QUOTEQUANTITY} {PRODUCTNAME}. L'attuale giacienza di {PRODUCTNAME} è di {CURRENTSTOCK}. 

Tipo di urgenza: BASSA

Grazie,
{CURRENTUSER}";
$adb->pquery('update vtiger_inventorynotify set notificationbody = ? where notificationsubject = ?',array($body,'Quote given for {PRODUCTNAME}'));
$adb->pquery('update vtiger_inventorynotify set notificationsubject = ? where notificationsubject = ?',array('Preventivo per {PRODUCTNAME}','Quote given for {PRODUCTNAME}'));
$body = "Gentile {HANDLER},

L'ordine di vendita è stato generato per {SOQUANTITY}  {PRODUCTNAME}. L'attuale giacienza a magazzino di {PRODUCTNAME} è di {CURRENTSTOCK}. 

L'ordine di vendita è stato generato, i dati devono essere trattati con la massima urgenza.

Tipo di urgenza: ALTA

Grazie,
{CURRENTUSER}";
$adb->pquery('update vtiger_inventorynotify set notificationbody = ? where notificationsubject = ?',array($body,'Sales Order generated for {PRODUCTNAME}'));
$adb->pquery('update vtiger_inventorynotify set notificationsubject = ? where notificationsubject = ?',array('Ordine di vendita per {PRODUCTNAME}','Sales Order generated for {PRODUCTNAME}'));

//Programmazione Notifiche 
$adb->pquery('update vtiger_notifyscheduler set notificationbody = ? where notificationsubject = ?',array('Ritardo di oltre 24h','Task Delay Notification'));
$adb->pquery('update vtiger_notifyscheduler set notificationsubject = ? where notificationsubject = ?',array('Notifica ritardo','Task Delay Notification'));
$adb->pquery('update vtiger_notifyscheduler set notificationbody = ? where notificationsubject = ?',array('Siamo riusciti a fare una grossa vendita, complimenti a tutti!','Big Deal notification'));
$adb->pquery('update vtiger_notifyscheduler set notificationsubject = ? where notificationsubject = ?',array('Affarone!','Big Deal notification'));
$adb->pquery('update vtiger_notifyscheduler set notificationbody = ? where notificationsubject = ?',array('Il ticket è in approvazione.','Pending Tickets notification'));
$adb->pquery('update vtiger_notifyscheduler set notificationsubject = ? where notificationsubject = ?',array('Notifica ticket pendenti','Pending Tickets notification'));
$adb->pquery('update vtiger_notifyscheduler set notificationbody = ? where notificationsubject = ?',array('Troppi ticket aperti per la stessa entità.','Too many tickets Notification'));
$adb->pquery('update vtiger_notifyscheduler set notificationsubject = ? where notificationsubject = ?',array('Troppi ticket pendenti!','Too many tickets Notification'));
$adb->pquery('update vtiger_notifyscheduler set notificationbody = ? where notificationsubject = ?',array('Questo messaggio notifica un promemoria per unattività!','Activity Reminder Notification'));
$adb->pquery('update vtiger_notifyscheduler set notificationsubject = ? where notificationsubject = ?',array('Notifica promemoria attività','Activity Reminder Notification'));

//Workflow
$where = "summary='UpdateInventoryProducts On Every Save' and module_name = 'Invoice'";
$res = $adb->query("select workflow_id from com_vtiger_workflows where $where");
if ($res && $adb->num_rows($res)==1) {
	$id = $adb->query_result($res,0,'workflow_id');
	$where = "workflow_id=$id";
	$adb->query("DELETE FROM com_vtiger_workflows where $where");
	$adb->query("DELETE FROM com_vtiger_workflowtasks where $where");
}
$where = "summary='Send Email to user when Notifyowner is True' and module_name = 'Accounts'";
$res = $adb->query("select workflow_id from com_vtiger_workflows where $where");
if ($res && $adb->num_rows($res)==1) {
	$id = $adb->query_result($res,0,'workflow_id');
	$where = "workflow_id=$id";
	$adb->query("DELETE FROM com_vtiger_workflows where $where");
	$adb->query("DELETE FROM com_vtiger_workflowtasks where $where");
}
$where = "summary='Send Email to user when Notifyowner is True' and module_name = 'Contacts'";
$res = $adb->query("select workflow_id from com_vtiger_workflows where $where");
if ($res && $adb->num_rows($res)==1) {
	$id = $adb->query_result($res,0,'workflow_id');
	$where = "workflow_id=$id";
	$adb->query("DELETE FROM com_vtiger_workflows where $where");
	$adb->query("DELETE FROM com_vtiger_workflowtasks where $where");
}
$where = "summary='Send Email to user when Portal User is True' and module_name = 'Contacts'";
$res = $adb->query("select workflow_id from com_vtiger_workflows where $where");
if ($res && $adb->num_rows($res)==1) {
	$id = $adb->query_result($res,0,'workflow_id');
	$where = "workflow_id=$id";
	$adb->query("DELETE FROM com_vtiger_workflows where $where");
	$adb->query("DELETE FROM com_vtiger_workflowtasks where $where");
}
$where = "summary='Send Email to users on Potential creation' and module_name = 'Potentials'";
$res = $adb->query("select workflow_id from com_vtiger_workflows where $where");
if ($res && $adb->num_rows($res)==1) {
	$id = $adb->query_result($res,0,'workflow_id');
	$where = "workflow_id=$id";
	$adb->query("DELETE FROM com_vtiger_workflows where $where");
	$adb->query("DELETE FROM com_vtiger_workflowtasks where $where");
}

populateDefaultWorkflows($adb);
function populateDefaultWorkflows($adb) {
	//crmv@20799
	require_once("modules/com_vtiger_workflow/include.inc");
	require_once("modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc");
	require_once("modules/com_vtiger_workflow/VTEntityMethodManager.inc");
	
	// Creating Workflow for Updating Inventory Stock for Invoice
	$vtWorkFlow = new VTWorkflowManager($adb);
	$invWorkFlow = $vtWorkFlow->newWorkFlow("Invoice");
	$invWorkFlow->test = '[{"fieldname":"subject","operation":"does not contain","value":"`!`"}]';
	$invWorkFlow->description = "Aggiorna inventario prodotti ad ogni salvataggio";
	$vtWorkFlow->save($invWorkFlow);

	$tm = new VTTaskManager($adb);
	$task = $tm->createTask('VTEntityMethodTask', $invWorkFlow->id);
	$task->active=true;
	$task->summary="Aggiorna inventario prodotti ad ogni salvataggio";
	$task->methodName = "UpdateInventory";
	$tm->saveTask($task);
	
	
	// Creating Workflow for Accounts when Notifyowner is true
	
	$vtaWorkFlow = new VTWorkflowManager($adb);
	$accWorkFlow = $vtaWorkFlow->newWorkFlow("Accounts");
	$accWorkFlow->test = '[{"fieldname":"notify_owner","operation":"is","value":"true:boolean"}]';
	$accWorkFlow->description = "Manda email all'utente quando una nuova azienda viene creata";
	$accWorkFlow->executionCondition=2;	
	$vtaWorkFlow->save($accWorkFlow);
	$id1=$accWorkFlow->id;
	
	$tm = new VTTaskManager($adb);
	$task = $tm->createTask('VTEmailTask',$accWorkFlow->id);
	$task->active=true;
	$task->methodName = "NotifyOwner";
	$task->recepient = "\$(assigned_user_id : (Users) email1)";
	$task->subject = "Nuova azienda creata";
	$task->content = '<p>Hai un nuovo account su VTE!<br />Dettagli account:<br /><br />ID account:<b>$account_no</b><br />Nome account:<b>$accountname</b><br />Categoria:<b>$rating</b><br />Azienda:<b>$industry</b><br />Tipo di account:<b>$accounttype</b><br />Descrizione:<b>$description</b><br /><br /><br />Grazie<br />Staff VTE</p>';
	$task->summary="Nuova azienda creata";
	$tm->saveTask($task);
	$adb->pquery("update com_vtiger_workflows set defaultworkflow=? where workflow_id=?",array(1,$id1));
	
	// Creating Workflow for Contacts when Notifyowner is true
	
	$vtcWorkFlow = new VTWorkflowManager($adb);
	$conWorkFlow = 	$vtcWorkFlow->newWorkFlow("Contacts");
	$conWorkFlow->summary="Manda email all'utente quando un nuovo contatto viene creato";
	$conWorkFlow->executionCondition=2;
	$conWorkFlow->test = '[{"fieldname":"notify_owner","operation":"is","value":"true:boolean"}]';
	$conWorkFlow->description = "Manda email all'utente quando un nuovo contatto viene creato";
	
	$vtcWorkFlow->save($conWorkFlow);
	$id1=$conWorkFlow->id;
	$tm = new VTTaskManager($adb);
	$task = $tm->createTask('VTEmailTask',$conWorkFlow->id);
	$task->active=true;
	$task->methodName = "NotifyOwner";
	$task->recepient = "\$(assigned_user_id : (Users) email1)";
	$task->subject = "Nuovo contatto creato";
	$task->content = '<p>Hai un nuovo contatto su VTE!<br />Dettagli account:<br /><br />ID account:<b>$account_no</b><br />Nome account:<b>$accountname</b><br />Categoria:<b>$rating</b><br />Azienda:<b>$industry</b><br />Tipo di account:<b>$accounttype</b><br />Descrizione:<b>$description</b><br /><br /><br />Grazie<br />Staff VTE<br />&nbsp;</p>';
	$task->summary="Nuovo contatto creato";
	$tm->saveTask($task);
	$adb->pquery("update com_vtiger_workflows set defaultworkflow=? where workflow_id=?",array(1,$id1));
	
	
	// Creating Workflow for Contacts when PortalUser is true
	
	$vtcWorkFlow = new VTWorkflowManager($adb);
	$conpuWorkFlow = $vtcWorkFlow->newWorkFlow("Contacts");
	$conpuWorkFlow->test = '[{"fieldname":"portal","operation":"is","value":"true:boolean"}]';
	$conpuWorkFlow->description = "Manda un'email all'utente quando l'utente del portale valido";
	$conpuWorkFlow->executionCondition=2;
	$vtcWorkFlow->save($conpuWorkFlow);
	$id1=$conpuWorkFlow->id;
	
	$tm = new VTTaskManager($adb);
	$task = $tm->createTask('VTEmailTask',$conpuWorkFlow->id);
	$task->active=true;
	$task->methodName = "NotifyOwner";
	$task->recepient = "\$(assigned_user_id : (Users) email1)";
	$task->subject = "Nuovo contatto creato";
	$task->content = '<p>Hai un nuovo contatto su VTE!<br />Dettagli account:<br /><br />ID account:<b>$account_no</b><br />Nome account:<b>$accountname</b><br />Categoria:<b>$rating</b><br />Azienda:<b>$industry</b><br />Tipo di account:<b>$accounttype</b><br />Descrizione:<b>$description</b><br /><br /><br /><span style="font-weight: bold;">Dati </span><b>di login sul CustomerPortal inoltrati a: </b>$email</p><p><br />Grazie<br />Staff VTE</p>';
	$task->summary="Nuovo contatto creato";
	$tm->saveTask($task);
	$adb->pquery("update com_vtiger_workflows set defaultworkflow=? where workflow_id=?",array(1,$id1));

	// Creating Workflow for Potentials

	$vtcWorkFlow = new VTWorkflowManager($adb);
	$potentialWorkFlow = $vtcWorkFlow->newWorkFlow("Potentials");
	$potentialWorkFlow->description = "Nuova opportunita creata";
	$potentialWorkFlow->executionCondition=1;
	$vtcWorkFlow->save($potentialWorkFlow);
	$id1=$potentialWorkFlow->id;

	$tm = new VTTaskManager($adb);
	$task = $tm->createTask('VTEmailTask',$potentialWorkFlow->id);
	$task->active=true;
	$task->recepient = "\$(assigned_user_id : (Users) email1)";
	$task->subject = "Nuova opportunita creata";
	$task->content = '<p>Un potenziale cliente &egrave; stato creato sul Customer Portal<br />Dettagli utenza:<br /><br />Utente No:<b>$potential_no</b><br />Nome:<b>$potentialname</b><br />Totale:<b>$amount</b><br />Data di chiusura:<b>$closingdate</b><br />Tipo:<b>$opportunity_type</b><br /><br /><br />Descrizione:$description<br /><br />Grazie<br />Staff VTE</p>';
	$task->summary="Nuova opportunita creata";
	$tm->saveTask($task);
	$adb->pquery("update com_vtiger_workflows set defaultworkflow=? where workflow_id=?",array(1,$id1));
	//crmv@20799e
}
?>
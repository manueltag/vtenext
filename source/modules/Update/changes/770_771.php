<?php
global $adb, $table_prefix;

$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';

if (!function_exists('addColumnToTable')) {
	function addColumnToTable($tablename, $columnname, $type, $extra = '') {
		global $adb;

		// check if already present^M
		$cols = $adb->getColumnNames($tablename);
		if (in_array($columnname, $cols)) {
			return;
		}

		$col = $columnname.' '.$type.' '.$extra;
		$adb->alterTable($tablename, $col, 'Add_Column');
	}
}



// remove old SDK link for document revisions
$adb->pquery("delete from {$table_prefix}_links where tabid = ? and linklabel = ?", array(getTabid('Documents'), 'Script DocRevision'));


$docInstance = Vtiger_Module::getInstance('Documents');
Vtiger_Link::addLink($docInstance->id, 'DETAILVIEWBASIC', 'ShareDocument', "javascript:openShareRecord('\$RECORD\$', '')", '', 1);
Vtiger_Link::addLink($docInstance->id,'DETAILVIEWBASIC','LBL_ADD_DOCREVISION',"javascript:AddDocRevision('\$RECORD\$');",'themes/images/bookMark.gif');
Vtiger_Link::addLink($docInstance->id, 'DETAILVIEWWIDGET', 'DOC REVISION', 'module=Documents&action=DocumentsAjax&file=RevisionTab&record=$RECORD$');

$tablename = 'crmv_docs_revisioned';

if (Vtiger_Utils::CheckTable($tablename)) {
	// if already there, just add the missing column
	addColumnToTable($tablename, 'user_email', 'C(100)');
}

$schema_table =
'<schema version="0.3">
	<table name="'.$tablename.'">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="crmid" type="R" size="19">
			<KEY/>
		</field>
		<field name="attachmentid" type="R" size="19">
			<KEY/>
		</field>
		<field name="userid" type="R" size="19" />
		<field name="revision" type="I" size="10" />
		<field name="revisiondate" type="T" />
		<field name="user_email" type="C" size="100">
			<DEFAULT value="0000-00-00 00:00:00" />
		</field>
		<index name="crmv_docs_revisioned_userid_idx">
			<col>userid</col>
		</index>
	</table>
</schema>';
if(!Vtiger_Utils::CheckTable($tablename)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}


$tablename = $table_prefix.'_sharetokens';
$schema_table =
'<schema version="0.3">
	<table name="'.$tablename.'">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="token" type="C" size="100">
			<KEY/>
		</field>
		<field name="expiretime" type="T">
			<DEFAULT value="0000-00-00 00:00:00" />
		</field>
		<field name="userid" type="I" size="19"/>
		<field name="crmid" type="I" size="19"/>
		<field name="module" type="C" size="100"/>
		<field name="edit" type="I" size="1"/>
		<field name="otherinfo" type="X" />
		<index name="sharetokens_userid_idx">
			<col>userid</col>
		</index>
		<index name="sharetokens_crmid_idx">
			<col>crmid</col>
		</index>
		<index name="sharetokens_module_idx">
			<col>module</col>
		</index>
	</table>
</schema>';
if(!Vtiger_Utils::CheckTable($tablename)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}


$trans = array(
	'Documents' => array(
		'it_it' => array(
			'ShareDocument' => 'Condividi documento',
			'LBL_ADD_DOCREVISION'=>'Aggiungi Revisione',
			'DOC REVISION'=>'Revisioni Documento',
			'Seleziona documento'=>'Seleziona documento',
			'Nessun file selezionato'=>'Nessun file selezionato',
			'Aggiungi Revisione'=>'Aggiungi Revisione',
			'NO_REVS'=>'Nessuna revisione presente',
			'Aggiungi Revisione al Documento'=>'Aggiungi Revisione al Documento',
			'Revisione'=>'Revisione Num.',
			'Revisionato Da'=>'Revisionato Da',
			'Data Revisione'=>'Data Revisione',
		),
		'en_us' => array(
			'ShareDocument' => 'Share document',
			'LBL_ADD_DOCREVISION'=>'Add Revision',
			'DOC REVISION'=>'Document Revisions',
			'Seleziona documento'=>'Select Document',
			'Nessun file selezionato'=>'No file selected',
			'Aggiungi Revisione'=>'Add Revision',
			'NO_REVS'=>'Nobody revision for the document',
			'Aggiungi Revisione al Documento'=>'Add revision to the document',
			'Revisione'=>'Revision No.',
			'Revisionato Da'=>'Revision by',
			'Data Revisione'=>'Revision Date',
		),
	),
	'ModNotifications' => array(
		'it_it' => array(
			'LBL_INVITATION_QUESTION' => 'Parteciperai',
			'LBL_INVITATION_YES' => 'Partecipa',
			'LBL_INVITATION_NO' => 'Rifiuta',
			'LBL_SET_ALL_AS_READ' => 'Segna tutte come lette',
		),
		'en_us' => array(
			'LBL_INVITATION_QUESTION' => 'Are you attending',
			'LBL_INVITATION_YES' => 'Participate',
			'LBL_INVITATION_NO' => 'Decline',
			'LBL_SET_ALL_AS_READ' => 'Mark all as read',
		),
	),
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_INVALID_URL' => 'Indirizzo non valido',
			'LBL_SHARE_EMAIL_SUBJECT' => '{user} ha condiviso un {type} ',
			'LBL_SHARE_EMAIL_BODY' => 'Tramite il seguente link puoi visualizzare il {type} <b>{entityname}</b>, che l\'utente <i>{user}</i> ha condiviso.<br>'.
			'<a href="{site_url}/index.php?sharetoken={token}">LINK</a><br>'.
			'<p>Il link rester&agrave; valido fino al {date}</p>'.
			'<p>VTECRM</p>',
			'LBL_SHARE_EMAIL_EDIT_SUBJECT' => 'Condivisione {type} ({entityname})',
			'LBL_SHARE_EMAIL_EDIT_BODY' => 'Il documento {entityname} &egrave; stato condiviso e permette il caricamento di nuove revisioni. Accedi tramite il seguente link:<br>'.
			'<a href="{site_url}/index.php?sharetoken={token}">LINK</a><br>'.
			'<p>Il link rester&agrave; valido fino al {date}</p>'.
			'<p>VTECRM</p>',
			'LBL_SHARE_INSERT_EMAIL' => 'Inserisci il tuo indirizzo email per caricare una nuova revisione. Riceverai una email con un link da cui sarà possibile scegliere il file da caricare.',
			'LBL_SHARE_LOAD_FILE' => 'Scegli il file da caricare. Verrà aggiunto al documento come nuova revisione.',
			'LBL_EMAIL_SENT' => 'Email inviata',
			'LBL_EMAIL_SEND_FAIL' => 'Invio email fallito',
			'LBL_EMAIL_INVALID' => 'Indirizzo non valido',
			'LBL_UPLOAD_SUCCESS' => 'Upload riuscito',
			'LBL_UPLOAD_FAILED' => 'Upload fallito',
		),
		'en_us' => array(
			'LBL_INVALID_URL' => 'Invalid address',
			'LBL_SHARE_EMAIL_SUBJECT' => '{user} shared a {type} ',
			'LBL_SHARE_EMAIL_BODY' => 'Using the following link you can see the {type} <b>{entityname}</b>, shared by the user <i>{user}</i>.<br>'.
			'<a href="{site_url}/index.php?sharetoken={token}">LINK</a><br>'.
			'<p>The link is valid until {date}</p>'.
			'<p>VTECRM</p>',
			'LBL_SHARE_EMAIL_EDIT_SUBJECT' => 'Shared {type} ({entityname})',
			'LBL_SHARE_EMAIL_EDIT_BODY' => 'The document {entityname} has been shared and allow new revisions to be uploaded. Open it with the following link:<br>'.
			'<a href="{site_url}/index.php?sharetoken={token}">LINK</a><br>'.
			'<p>The link is valid until {date}</p>'.
			'<p>VTECRM</p>',
			'LBL_EMAIL_SENT' => 'Email sent',
			'LBL_SHARE_INSERT_EMAIL' => 'Type your email address to upload a new revision. You\'ll receive an email with a link that will allow you to choose the file to upload.',
			'LBL_SHARE_LOAD_FILE' => 'Choose the file to upload. It will be attached to the document as a new revision.',
			'LBL_EMAIL_SEND_FAIL' => 'Unable to send email',
			'LBL_EMAIL_INVALID' => 'Invalid address',
			'LBL_UPLOAD_SUCCESS' => 'Upload completed',
			'LBL_UPLOAD_FAILED' => 'Upload failed',
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

?>
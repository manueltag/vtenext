<?php
global $adb;
$adb->query("UPDATE vtiger_asteriskextensions SET use_asterisk = 0 WHERE asterisk_extension IS NULL OR asterisk_extension = ''");
$sqlarray = $adb->datadict->DropTableSQL('vte_mailcache_list');
$adb->datadict->ExecuteSQLArray($sqlarray);
$schema_tables = array(
'vte_mailcache_list'=>
	'<schema version="0.3">
	  <table name="vte_mailcache_list">
	  <opt platform="mysql">ENGINE=InnoDB</opt>
	    <field name="userid" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="uid" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="folder" type="C" size="255">
	      <KEY/>
	    </field>
	    <field name="small_header" type="X"/>
	    <field name="small_header_res" type="X"/>
	    <field name="flags" type="C" size="255"/>
	    <field name="email_subject_sort" type="C" size="255"/>
	    <field name="email_date" type="I" size="19"/>
	    <field name="email_from" type="X"/>
	    <field name="email_from_sort" type="C" size="255"/>
	    <field name="email_to" type="C" size="255"/>
	    <field name="email_to_sort" type="C" size="255"/>
	    <field name="email_cc" type="C" size="255"/>
	  </table>
	</schema>'
);
foreach($schema_tables as $table_name => $schema_table) {
	if(!Vtiger_Utils::CheckTable($table_name)) {
		$schema_obj = new adoSchema($adb->database);
		$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
	}
}

SDK::setLanguageEntry('Webmails', 'en_us', 'LBL_SELECT_ONE', 'Select only one message.');
SDK::setLanguageEntry('Webmails', 'it_it', 'LBL_SELECT_ONE', 'Selezionare un solo messaggio');
SDK::setLanguageEntry('Webmails', 'en_us', 'LBL_LONGTIME_WARNING', 'Warning!\nViewing all messages may take a lot of time and overload your server.\nContinue anyway?');
SDK::setLanguageEntry('Webmails', 'it_it', 'LBL_LONGTIME_WARNING', 'Attenzione!\nLa visualizzazione di tutti i messaggi potrebbe richiedere molto tempo e sovraccaricare il server.\nContinuare?');

SDK::setLanguageEntry('Webmails', 'en_us', 'LBL_OF', 'of');
SDK::setLanguageEntry('Webmails', 'it_it', 'LBL_OF', 'di');

SDK::setLanguageEntry('Emails', 'en_us', 'LBL_REMOVE', 'Remove');
SDK::setLanguageEntry('Emails', 'it_it', 'LBL_REMOVE', 'Rimuovi');

SDK::setLanguageEntries('Webmails', 'LBL_IMAP_SEARCH', array('it_it'=>'Continua la ricerca sul server...','en_us'=>'Continue research on server ...'));
SDK::setLanguageEntry('Webmails', 'it_it', 'Unknown date', ' ');
?>
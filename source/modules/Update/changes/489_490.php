<?php
global $adb;
$schema_tables = array(
'vtiger_invitees_con'=>
	'<schema version="0.3">
	  <table name="vtiger_invitees_con">
	  <opt platform="mysql">ENGINE=InnoDB</opt>
	    <field name="activityid" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="inviteeid" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="partecipation" type="I" size="1">
		    <DEFAULT value="0"/>
	    </field>
	  </table>
	</schema>'
);
foreach($schema_tables as $table_name => $schema_table) {
	if(!Vtiger_Utils::CheckTable($table_name)) {
		$schema_obj = new adoSchema($adb->database);
		$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
	}
}

SDK::setLanguageEntry('Calendar', 'en_us', 'LBL_RELATE', 'Relate');
SDK::setLanguageEntry('Calendar', 'it_it', 'LBL_RELATE', 'Collega');
SDK::setLanguageEntry('Calendar', 'en_us', 'LBL_MAIL_INVITATION_2', 'to visit the activity.');
SDK::setLanguageEntry('Calendar', 'it_it', 'LBL_MAIL_INVITATION_2', 'per visitare l\'evento.');
SDK::setLanguageEntry('Calendar', 'en_us', 'LBL_MAIL_INVITATION_CONFIRM', 'Do you confirm your participation?');
SDK::setLanguageEntry('Calendar', 'it_it', 'LBL_MAIL_INVITATION_CONFIRM', 'Confermi la tua partecipazione?');
SDK::setLanguageEntry('Calendar', 'en_us', 'LBL_MAIL_LBL_ANSWER', 'You answered');
SDK::setLanguageEntry('Calendar', 'it_it', 'LBL_MAIL_LBL_ANSWER', 'Hai risposto');
SDK::setLanguageEntry('Calendar', 'en_us', 'LBL_CHANGE_ANSWER', 'If you want change answer click');
SDK::setLanguageEntry('Calendar', 'it_it', 'LBL_CHANGE_ANSWER', 'Se vuoi cambiare risposta clicca');
?>
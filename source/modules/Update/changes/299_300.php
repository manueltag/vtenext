<?php
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb;

//creo tabella crmv_squirrelmailrel
$flds = "	
	user_id I(19) NOTNULL PRIMARY,
	imap_id I(255) NOTNULL PRIMARY,
	mail_id I(19) NOTNULL PRIMARY,
	type C(255) DEFAULT NULL
";
$sqlarray = $adb->datadict->CreateTableSQL('crmv_squirrelmailrel', $flds);
$adb->datadict->ExecuteSQLArray($sqlarray);

//aggiungo campi al modulo Users
$modulo = Vtiger_Module::getInstance('Users');
$block = new Vtiger_Block();
$block->label = 'Configurazione Webmail';
$modulo->addBlock($block);

$field = new Vtiger_Field();
$field->name = 'webmail_username';
$field->column = 'webmail_username';
$field->label= 'Webmail Username';
$field->table = $modulo->basetable;
//crmv@18160
$field->columntype = 'C(100)';
//crmv@18160 end
$field->typeofdata = 'V~O';
$field->uitype = 1;
$block->addField($field);

$field = new Vtiger_Field();
$field->name = 'webmail_password';
$field->column = 'webmail_password';
$field->label= 'Webmail Password';
$field->table = $modulo->basetable;
//crmv@18160
$field->columntype = 'C(255)';
//crmv@18160 end
$field->typeofdata = 'V~O';
$field->uitype = 199;
$block->addField($field);

$field = new Vtiger_Field();
$field->name = 'webmail_structure';
$field->column = 'webmail_structure';
$field->label= 'Webmail Structure';
$field->table = $modulo->basetable;
//crmv@18160
$field->columntype = 'C(255)';
//crmv@18160 end
$field->typeofdata = 'V~O';
$field->uitype = 15;
$field->setPicklistValues(array('Standard','Horizontal','Vertical'));
$block->addField($field);

//aggiungo i campi email in creazione veloce
$adb->query("UPDATE vtiger_field SET quickcreate = 0 WHERE fieldname = 'email1' AND tabid = 6 AND uitype = 13");
$adb->query("UPDATE vtiger_field SET quickcreatesequence = 5 WHERE fieldname = 'email1' AND tabid = 6 AND uitype = 13");
$adb->query("UPDATE vtiger_field SET quickcreate = 0 WHERE fieldname = 'email' AND tabid IN (7,4,18) AND uitype = 13");
?>
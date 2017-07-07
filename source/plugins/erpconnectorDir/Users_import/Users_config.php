<?php
include_once("Users_functions.php");
include("../config.php");
global $table_prefix;
$log_active = false;
//modulo da importare:
$module = 'Users';
//array mappaggio campi: nome campo tabella di appoggio => fieldname di vte
$mapping = Array(
	'user_name'=>'user_name',			/* 	User Name - Nome Utente			*/
	'external_code'=>'external_code',	/* 	External Code - Codice Esterno	*/
	'first_name'=>'first_name',			/* 	First Name - Nome				*/
	'last_name'=>'last_name',			/* 	Last Name - Cognome				*/
	'email'=>'email1',					/* 	Email							*/
);
//campo nella tabella di appoggio per identificare il codice esterno (sul quale l'import effettuerà la creazione/aggiornamento dei dati)
$external_code = 'external_code';
//tabella di appoggio
$table = "erp_users";
//condizioni sulla tabella di appoggio
$where = "";
//$order_by = "order by data_record asc";
// override query
if (!empty($users_query)) $override_query = $users_query;
//campi di default in creazione
$fields_auto_create[$table_prefix.'_users']['is_admin'] = 'off';
$fields_auto_create[$table_prefix.'_users']['status'] = 'Active';
$fields_auto_create[$table_prefix.'_users']['currency_id'] = 1;
$fields_auto_create[$table_prefix.'_users']['menu_view'] = 'Small Menu';
$fields_auto_create[$table_prefix.'_users']['internal_mailer'] = 1;
$fields_auto_create[$table_prefix.'_users']['activity_view'] = 'This Week';
$fields_auto_create[$table_prefix.'_users']['date_format'] = 'dd-mm-yyyy';
$fields_auto_create[$table_prefix.'_users']['reminder_interval'] = 'None';
$fields_auto_create[$table_prefix.'_users']['start_hour'] = '08:00';
$fields_auto_create[$table_prefix.'_users']['no_week_sunday'] = 1;
$_SESSION[$module]['roleid'] = 'H5';
$_SESSION[$module]['password'] = 'default';
//campi di default in aggiornamento
$fields_jump_update = Array();
?>
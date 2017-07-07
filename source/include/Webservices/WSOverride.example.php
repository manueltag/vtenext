<?php
/*+*************************************************************************************
* The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: CRMVILLAGE.BIZ VTECRM
* The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
* Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
* All Rights Reserved.
***************************************************************************************/
/* crmv@5687 */

/* 
 * Example file which can use as a base to override specific Webservices behaviours.
 * Just rename this file to WSOverride.php and populate the variables/functions here
 *
 */


// Sostituisce l'estrazione dei moduli originale
$ws_replace_sql = "";	

// Filtro aggiuntivo alla query di etrazione dei moduli (OR per aggiungere altri moduli, AND per diminuire la visibilità a quelli già estratti)
// E' necessario fare logout e login da Outlook quando si modifica questa stringa perchè i moduli caricati vengono messi in cache
// $ws_additional_modules = " OR {$table_prefix}_field.tabid IN ('2','13') ";	
$ws_additional_modules = " ";	

// Filtri aggiuntivi per l'estrazione dei record ricercati nel collegamento di una mail
/*
$ws_filters = array('13'=>" AND {$table_prefix}_troubletickets.status = 'Open' ",
					'2'=>" AND {$table_prefix}_potential.sales_stage <> 'Closed Lost' "
);
*/
$ws_filters = array();

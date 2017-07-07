<?php
/**
 * This file create fields
 * @deprecated
 * This file is deprecated, please call directly Update::create_fields($fields);
 *
 * INPUT: $fields 
 */

if (!isset($fields) || empty($fields) || !is_array($fields)) {
	die('$fields empty');
}

// example field:
//$fields[] = array('module'=>'Corsi','block'=>'Informazioni Corso','name'=>'prodotto','label'=>'Prodotto','uitype'=>'10','columntype'=>'INT(19)','typeofdata'=>'I~O','relatedModules'=>array('Products'),'relatedModulesAction'=>array('Products'=>array('ADD','SELECT')));

$Vtiger_Utils_Log = true;

require_once('modules/Update/Update.php');

Update::create_fields($fields);

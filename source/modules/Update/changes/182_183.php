<?php
global $adb;
$result = $adb->query('SELECT MAX(relation_id) AS relation_id FROM vtiger_relatedlists');
 if ($result) $relation_id = $adb->query_result($result,0,'relation_id');
 if ($relation_id != '') {
	$adb->query("INSERT INTO vtiger_relatedlists VALUES (".($relation_id+1).",8,6,'get_documents_dependents_list',1,'Accounts',0,'select')");
	$adb->query("INSERT INTO vtiger_relatedlists VALUES (".($relation_id+2).",8,7,'get_documents_dependents_list',2,'Leads',0,'select')");
	$adb->query("INSERT INTO vtiger_relatedlists VALUES (".($relation_id+3).",8,4,'get_documents_dependents_list',3,'Contacts',0,'select')");
	$adb->query("INSERT INTO vtiger_relatedlists VALUES (".($relation_id+4).",8,2,'get_documents_dependents_list',4,'Potentials',0,'select')");
	$adb->query("INSERT INTO vtiger_relatedlists VALUES (".($relation_id+5).",8,14,'get_documents_dependents_list',5,'Products',0,'select')");
	$adb->query("INSERT INTO vtiger_relatedlists VALUES (".($relation_id+6).",8,10,'get_documents_dependents_list',6,'Emails',0,'select')");
	$adb->query("INSERT INTO vtiger_relatedlists VALUES (".($relation_id+7).",8,13,'get_documents_dependents_list',7,'HelpDesk',0,'select')");
	$adb->query("INSERT INTO vtiger_relatedlists VALUES (".($relation_id+8).",8,20,'get_documents_dependents_list',8,'Quotes',0,'select')");
	$adb->query("INSERT INTO vtiger_relatedlists VALUES (".($relation_id+9).",8,21,'get_documents_dependents_list',9,'PurchaseOrder',0,'select')");
	$adb->query("INSERT INTO vtiger_relatedlists VALUES (".($relation_id+10).",8,22,'get_documents_dependents_list',10,'SalesOrder',0,'select')");
	$adb->query("INSERT INTO vtiger_relatedlists VALUES (".($relation_id+11).",8,23,'get_documents_dependents_list',11,'Invoice',0,'select')");
	$adb->query("INSERT INTO vtiger_relatedlists VALUES (".($relation_id+12).",8,30,'get_documents_dependents_list',12,'Projects',0,'select')");
	$adb->query("INSERT INTO vtiger_relatedlists VALUES (".($relation_id+13).",8,15,'get_documents_dependents_list',13,'Faq',0,'select')");
	$adb->query("INSERT INTO vtiger_relatedlists VALUES (".($relation_id+14).",8,36,'get_documents_dependents_list',14,'ServiceContracts',0,'select')");
	$adb->query("INSERT INTO vtiger_relatedlists VALUES (".($relation_id+15).",8,37,'get_documents_dependents_list',15,'Services',0,'select')");
 }
?>
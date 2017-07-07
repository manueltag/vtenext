<?php
$_SESSION['modules_to_update']['Transitions'] = 'packages/vte/mandatory/Transitions.zip';
global $table_prefix,$adb;
//register new ws
$operationMeta = array(
	"retrieveExtra"=>array(
		"include"=>array(
			"include/Webservices/Extra/Retrieve.php"
		),
		"handler"=>"vtws_retrieveExtra",
		"params"=>array(
			"id"=>"String"
		),
		"prelogin"=>0,
		"type"=>"GET"
	),
	"queryExtra"=>array(
		"include"=>array(
			"include/Webservices/Extra/Query.php"
		),
		"handler"=>"vtws_queryExtra",
		"params"=>array(
			"query"=>"String"
		),
		"prelogin"=>0,
		"type"=>"GET"
	),
	"describeExtra"=>array(
		"include"=>array(
			"include/Webservices/Extra/DescribeObject.php"
		),
		"handler"=>"vtws_describeExtra",
		"params"=>array(
			"elementType"=>"String",
		),
		"prelogin"=>0,
		"type"=>"GET"
	),
	"listtypesExtra"=>array(
		"include"=>array(
			"include/Webservices/Extra/ModuleTypes.php"
		),
		"handler"=>"vtws_listtypesExtra",
		"params"=>array(
			'fieldTypeList'=>'Encoded',
		),
		"prelogin"=>0,
		"type"=>"GET"
	),		
	"getRelationsExtra"=>array(
		"include"=>array(
			"include/Webservices/Extra/Relations.php"
		),
		"handler"=>"vtws_getRelationsExtra",
		"params"=>array(
			"module"=>"String",
			"record"=>"String",
		),
		"prelogin"=>0,
		"type"=>"GET"
	),
);
$createOperationQuery = "insert into ".$table_prefix."_ws_operation(operationid,name,handler_path,handler_method,type,prelogin) 
	values (?,?,?,?,?,?);";
$createOperationParamsQuery = "insert into ".$table_prefix."_ws_operation_parameters(operationid,name,type,sequence) 
	values (?,?,?,?);";
foreach ($operationMeta as $operationName => $operationDetails) {
	$operationId = $adb->getUniqueID($table_prefix."_ws_operation");
	$result = $adb->pquery($createOperationQuery,array($operationId,$operationName,$operationDetails['include'],
		$operationDetails['handler'],$operationDetails['type'],$operationDetails['prelogin']));
	$params = $operationDetails['params'];
	$sequence = 1;
	foreach ($params as $paramName => $paramType) {
		$result = $adb->pquery($createOperationParamsQuery,array($operationId,$paramName,$paramType,$sequence++));
	}
}
$name = "{$table_prefix}_ws_entity_extra";
$schema_table = '<?xml version="1.0"?>
<schema version="0.3">
  <table name="'.$name.'">
  <opt platform="mysql">ENGINE=InnoDB</opt>
    <field name="id" type="I" size="11">
      <KEY/>
    </field>
    <field name="name" type="C" size="25"/>
    <field name="handler_path" type="C" size="255"/>
    <field name="handler_class" type="C" size="64"/>
    <field name="ismodule" type="I" size="3"/>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($name)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
	$adb->database->GenID($name.'_seq',1000); //start from 1000 don't overlap with standard module ids
}
?>
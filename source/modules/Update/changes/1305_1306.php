<?php
global $adb, $table_prefix;

$uitype = 51;
SDK::setUitype($uitype,"modules/SDK/src/$uitype/$uitype.php","modules/SDK/src/$uitype/$uitype.tpl","modules/SDK/src/$uitype/$uitype.js",'reference');
$result = $adb->pquery("select fieldtypeid from {$table_prefix}_ws_fieldtype where uitype=?", array($uitype));
if ($result && $adb->num_rows($result) > 0) {
	$check = $adb->pquery("select fieldtypeid from {$table_prefix}_ws_referencetype where fieldtypeid=?", array($adb->query_result($result,0,'fieldtypeid')));
	if ($check && $adb->num_rows($check) == 0) {
		$adb->pquery("insert into {$table_prefix}_ws_referencetype(fieldtypeid,type) values(?,?)",array($adb->query_result($result,0,'fieldtypeid'),'Users'));
	}
}
SDK::setLanguageEntries('Users', 'LBL_SELECT_ALL_USER', array('it_it'=>'Utente senza filtraggio permessi','en_us'=>'User without permissions filter'));

$uitype = 50;
SDK::setUitype($uitype,"modules/SDK/src/$uitype/$uitype.php","modules/SDK/src/$uitype/$uitype.tpl","modules/SDK/src/$uitype/$uitype.js",'reference');
$result = $adb->pquery("select fieldtypeid from {$table_prefix}_ws_fieldtype where uitype=?", array($uitype));
if ($result && $adb->num_rows($result) > 0) {
	$check = $adb->pquery("select fieldtypeid from {$table_prefix}_ws_referencetype where fieldtypeid=?", array($adb->query_result($result,0,'fieldtypeid')));
	if ($check && $adb->num_rows($check) == 0) {
		$adb->pquery("insert into {$table_prefix}_ws_referencetype(fieldtypeid,type) values(?,?)",array($adb->query_result($result,0,'fieldtypeid'),'Users'));
	}
}
SDK::setLanguageEntries('Users', 'LBL_SELECT_CUSTOM_USER', array('it_it'=>'Utente da lista filtrata','en_us'=>'User from filtered list'));

$name = "{$table_prefix}_fieldinfo";
$schema_table = '<?xml version="1.0"?>
<schema version="0.3">
  <table name="'.$name.'">
  <opt platform="mysql">ENGINE=InnoDB</opt>
    <field name="fieldid" type="I" size="19">
      <KEY/>
    </field>
    <field name="info" type="XL"/>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($name)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}
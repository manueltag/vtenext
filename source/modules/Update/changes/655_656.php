<?php
global $adb;
$sqlarray = $adb->datadict->DropTableSQL('vte_mailcache_messages');
$adb->datadict->ExecuteSQLArray($sqlarray);
$schema_table = '
<schema version="0.3">
  <table name="vte_mailcache_messages">
  	<opt platform="mysql">ENGINE=InnoDB</opt>
  	<field name="userid" type="I" size="19">
  		<KEY />
  	</field>
  	<field name="uid" type="I" size="19">
  		<KEY />
  	</field>
  	<field name="folder" type="C" size="255">
  		<KEY />
  	</field>
  	<field name="flgs_bodystr" type="X" />
  	<field name="body_header" type="XL" />
  	<field name="body" type="XL" />
  	<index name="NewIndex1">
      <col>userid</col>
    </index>
    <index name="NewIndex2">
      <col>uid</col>
    </index>
    <index name="NewIndex3">
      <col>folder</col>
    </index>
  </table>
</schema>';
$schema_obj = new adoSchema($adb->database);
$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));

//aggiungo i plugin tnef_decoder e get_uuencode a squirrelmail
$file = 'include/squirrelmail/config/config.php';
if (file_exists($file)) {
	$handle_file = fopen($file, "r");
	while(!feof($handle_file)) {
		$buffer = fread($handle_file, 552000);
	}
	
	$bk_file = 'include/squirrelmail/config/config.vte4.3.2.php';
	$handle_bk_file = fopen($bk_file, "w");
	fputs($handle_bk_file, $buffer);
	fclose($handle_bk_file);
	
	$buffer = str_replace("\$plugins[3] = 'overlook';		//crmv@20047","\$plugins[3] = 'overlook';		//crmv@20047\n\$plugins[4] = 'tnef_decoder';	//crmv@33250\n\$plugins[5] = 'get_uuencode';	//crmv@33250",$buffer);
	fclose($handle_file);
	$handle = fopen($file, "w");
	fputs($handle, $buffer);
	fclose($handle);
}
//end
?>
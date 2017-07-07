<?php
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
   *
 ********************************************************************************/
savePBXDetails();

function savePBXDetails(){
	global $adb;
	global $table_prefix;
	$semodule = $_REQUEST['semodule'];
	
	if($semodule == 'asterisk'){
		$server = $_REQUEST['qserver'];
		$port = $_REQUEST['qport'];
		$username = $_REQUEST['qusername'];
		$password = $_REQUEST['qpassword'];
		$version = $_REQUEST['version'];
		
		//crmv@43764
		if ($password == '') {
			$result = $adb->query("select password from {$table_prefix}_asterisk");
			if ($result && $adb->num_rows($result) > 0) {
				$password = $adb->query_result($result,0,'password');
			}
		}
		//crmv@43764e
		
		$sql = "delete from ".$table_prefix."_asterisk";
		$adb->query($sql);	//delete older records (if any)
		
		$sql = "insert into ".$table_prefix."_asterisk (server, port, username, password, version) values (?,?,?,?,?)";
		$params = array($server,$port, $username, $password, $version);
		$adb->pquery($sql, $params);
	}
}
?>
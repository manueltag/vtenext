<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is crmvillage.
 * Portions created by vtiger are Copyright (C) crmvillage.
 * All Rights Reserved.
********************************************************************************/

_phpset_memorylimit_MB(32);
global $php_max_execution_time;
set_time_limit($php_max_execution_time);
global $adb,$dbname;

$query = " ALTER DATABASE ".$dbname." DEFAULT CHARACTER SET utf8";
$adb->query($query);
$query = "SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0";
$adb->query($query);
$tables_res = $adb->query("show tables");
while($row = $adb->fetch_array($tables_res))
{
	$query =" LOCK TABLES `".$row[0]."` WRITE ";
	$adb->query($query);
	$query =" ALTER TABLE ".$row[0]." CONVERT TO CHARACTER SET  utf8 ";
	$adb->query($query);
	$query =" UNLOCK TABLES ";
	$adb->query($query);
}
$query = " SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS  ";
$adb->query($query);

convert_html2utf8_db();

/**
* Function to convert html values to its original character available in a database
* This function can called at any time after the migration
* It get all the tables and its VARCHAR/TEXT/LONGTEXT fields from the DB
* Converts the html-values to its original character and restore it. 
**/
function convert_html2utf8_db()
{
	global $adb,$log,$table_prefix;
	//Getting all the tables from the current database.
	$alltables = $adb->get_tables();
	$log->debug("Started HTML to UTF-8 Conversion");
	$values=Array();
	//Tables for which conversion to utf8 not required.
	$skip_tables=Array($table_prefix.'_audit_trial',$table_prefix.'_sharedcalendar', $table_prefix.'_potcompetitorrel', $table_prefix.'_users2group', $table_prefix.'_group2grouprel', $table_prefix.'_group2role', $table_prefix.'_group2rs', $table_prefix.'_campaigncontrel', $table_prefix.'_campaignleadrel', $table_prefix.'_cntactivityrel', $table_prefix.'_crmentitynotesrel', $table_prefix.'_salesmanactivityrel', $table_prefix.'_vendorcontactrel', $table_prefix.'_salesmanticketrel', $table_prefix.'_seactivityrel', $table_prefix.'_seticketsrel', $table_prefix.'_senotesrel', $table_prefix.'_profile2globalpermissions', $table_prefix.'_profile2standardpermissions', $table_prefix.'_profile2field', $table_prefix.'_role2profile', $table_prefix.'_profile2utility', $table_prefix.'_activityproductrel', $table_prefix.'_pricebookproductrel', $table_prefix.'_activity_reminder', $table_prefix.'_actionmapping', $table_prefix.'_org_share_action2tab', $table_prefix.'_datashare_relatedmodule_permission', $table_prefix.'_tmp_read_user_sharing_per', $table_prefix.'_tmp_read_group_sharing_per', $table_prefix.'_tmp_write_user_sharing_per', $table_prefix.'_tmp_write_group_sharing_per', $table_prefix.'_tmp_read_user_rel_sharing_per', $table_prefix.'_tmp_read_group_rel_sharing_per', $table_prefix.'_tmp_write_user_rel_sharing_per', $table_prefix.'_tmp_write_group_rel_sharing_per', $table_prefix.'_role2picklist', $table_prefix.'_freetagged_objects', $table_prefix.'_tab', $table_prefix.'_blocks', $table_prefix.'_group2role', $table_prefix.'_group2rs');
	for($i=0;$i<count($alltables);$i++)
	{
		$table=$alltables[$i];
		if(!in_array($table,$skip_tables))
		{
			//Here selecting all the colums from the table
			$result = $adb->query("SHOW COLUMNS FROM $table");
			while ($row = $adb->fetch_array($result))
			{
				//Getting the primary key column of the table.
				if($row['key'] == 'PRI')
				{
					$values[$table]['key'][]=$row['field'];
				}
				//And Getting columns of type varchar, text and longtext.
				if(stristr($row['type'],'varchar') != '' || stristr($row['type'],'text') != '' || stristr($row['type'],'longtext') != '')
				{
					$values[$table]['columns'][] = $row['field'];
				}
			}
		}
	}

	$final_array=$values;
	foreach($final_array as $tablename=>$value)
	{
		//Going to update values in the table.
		$key = $value['key'];
		$cols = $value['columns'];
		if($cols != "" && $key != "")
		{
			if(count($key) > 1){
				$key_list = implode(", ", $key);
			}	
			else{
				$key_list = $key[0];
			}	
				
			if(count($cols) > 1){
				$col_list = implode(", ", $cols);
			}
			else{
				$col_list = $cols[0];
			}
			//Getting the records available in the table.
			$query="SELECT $key_list, $col_list FROM $tablename";
			$res1 = $adb->query($query);
			$log->debug("Converting values in the table :".$tablename);
			if ($res1 && $adb->num_rows($res1)>0){
				while($row = $adb->fetchByAssoc($res1,-1,false)){
					$id = Array();
					$whereStr = "";
					$first = true;
					foreach ($key as $chiave){
						if (!$first){
							$whereStr .= " and ";
						}
						$first = false;
						$whereStr .= $chiave."=?";
						$id[] = $row[$chiave];
					}
					$val = Array();
					$updateStr = "";
					$first = true;
					foreach ($cols as $colonna){
						$col_encoded = html_to_utf8($row[$colonna]);
						if ($row[$colonna] != $col_encoded){
							if (!$first){
								$updateStr .= " and ";
							}
							$first = false;
							$updateStr .= $colonna."=?";
							$val[] = $col_encoded;
						}
					}
					if (!empty($val)){
						$updateQ = "UPDATE $tablename SET $updateStr where $whereStr";
						$params = array($val, $id);
						$adb->pquery($updateQ, $params);
					}
				}
			}
		}
	}
	$log->debug("HTML to UTF-8 Conversion has been completed");
}
?>
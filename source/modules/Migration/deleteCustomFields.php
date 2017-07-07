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


global $conn,$table_prefix;
//we have to get all customfields from customview and report related tables (cvcolumnlist, cvstdfilter, etc) and remove the entries from these tables if the customfields are not available in field table and cf table

$tables_array = Array(
			$table_prefix."_cvcolumnlist"=>"columnname",
			$table_prefix."_cvstdfilter"=>"columnname",
			$table_prefix."_cvadvfilter"=>"columnname",
			$table_prefix."_selectcolumn"=>"columnname",
			$table_prefix."_relcriteria"=>"columnname",
			$table_prefix."_reportsortcol"=>"columnname",
			$table_prefix."_reportdatefilter"=>"datecolumnname",
			$table_prefix."_reportsummary"=>"columnname",
		     );


foreach($tables_array as $tablename => $columnname)
{
	$query = "select $columnname from $tablename where $columnname like '%:cf_%'";
	$result = $conn->query($query);
	$noofrows = $conn->num_rows($result);

	for($i=0;$i<$noofrows;$i++)
	{
		//First get the fieldname from the result
		$col_value = $conn->query_result($result,$i,$columnname);
		$fieldname = substr($col_value,strpos($col_value,':cf_')+1,6);

		//Now check whether this field is available in field table
		$sql1 = "select fieldid from ".$table_prefix."_field where fieldname='".$fieldname."' and ".$table_prefix."_field.presence in (0,2)";
		$result1 = $conn->query($sql1);
		$noofrows1 = $conn->num_rows($result1);
		$fieldid = $conn->query_result($result1,0,"fieldid");

		//if there is no field then we have to delete that field entries
		if($noofrows1 == 0 && !isset($fieldid))
		{
			//Now we have to delete that customfield from the $tablename
			Execute("delete from $tablename where $columnname like '%:".$fieldname.":%'");
		}
	}
}



?>

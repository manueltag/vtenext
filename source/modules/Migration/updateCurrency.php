<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once("include/database/PearDatabase.php");
global $table_prefix;
$conn = PearDatabase::getInstance();

$ajax_val = $_REQUEST['ajax'];

if($ajax_val == 1)
{
	$crate = $_REQUEST['crate'];
	$conn->println('conversion rate = '.$crate);
	
	$query = "UPDATE ".$table_prefix."_currency_info SET conversion_rate=? WHERE id=1";
	$result = $conn->pquery($query, array($crate));

	//array should be id || vtiger_fieldname => vtiger_tablename
	$modules_array = Array(
				"accountid||annualrevenue"	=>	$table_prefix."_account",
				
				"leadid||annualrevenue"		=>	$table_prefix."_leaddetails",

				"potentialid||amount"		=>	$table_prefix."_potential",

				"productid||unit_price"		=>	$table_prefix."_products",

				"salesorderid||salestax"	=>	$table_prefix."_salesorder",
				"salesorderid||adjustment"	=>	$table_prefix."_salesorder",
				"salesorderid||total"		=>	$table_prefix."_salesorder",
				"salesorderid||subtotal"	=>	$table_prefix."_salesorder",

				"purchaseorderid||salestax"	=>	$table_prefix."_purchaseorder",
				"purchaseorderid||adjustment"	=>	$table_prefix."_purchaseorder",
				"purchaseorderid||total"	=>	$table_prefix."_purchaseorder",
				"purchaseorderid||subtotal"	=>	$table_prefix."_purchaseorder",

				"quoteid||tax"			=>	$table_prefix."_quotes",
				"quoteid||adjustment"		=>	$table_prefix."_quotes",
				"quoteid||total"		=>	$table_prefix."_quotes",
				"quoteid||subtotal"		=>	$table_prefix."_quotes",

				"invoiceid||salestax"		=>	$table_prefix."_invoice",
				"invoiceid||adjustment"		=>	$table_prefix."_invoice",
				"invoiceid||total"		=>	$table_prefix."_invoice",
				"invoiceid||subtotal"		=>	$table_prefix."_invoice",
			      );

	foreach($modules_array as $fielddetails => $table)
	{
		$temp = explode("||",$fielddetails);
		$id_name = $temp[0];
		$fieldname = $temp[1];

		$res = $conn->query("select $id_name, $fieldname from $table");
		$record_count = $conn->num_rows($res);
		
		for($i=0;$i<$record_count;$i++)
		{
			$recordid = $conn->query_result($res,$i,$id_name);
			$old_value = $conn->query_result($res,$i,$fieldname);

			//calculate the new value
			$new_value = $old_value/$crate;//convertToDollar($old_value,$crate);
			$conn->println("old value = $old_value && new value = $new_value");

			$update_query = "update $table set $fieldname='".$new_value."' where $id_name=$recordid";
			$update_result = $conn->query($update_query);
		}
	}
}

?>
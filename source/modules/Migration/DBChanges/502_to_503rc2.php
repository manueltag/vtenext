<?php
/*+********************************************************************************
 * The contents of this file are subject to the ".$table_prefix." CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  ".$table_prefix." CRM Open Source
 * The Initial Developer of the Original Code is ".$table_prefix.".
 * Portions created by ".$table_prefix." are Copyright (C) ".$table_prefix.".
 * All Rights Reserved.
 *********************************************************************************/

//5.0.2 to 5.0.3 RC2 database changes - added on 05-01-07
//we have to use the current object (stored in PatchApply.php) to execute the queries
$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

$migrationlog->debug("\n\nDB Changes from 5.0.2 to 5.0.3 RC2 -------- Starts \n\n");
global $table_prefix;
$query_array = Array(
			"alter table ".$table_prefix."_entityname add column entityidcolumn varchar(150)",

			"update ".$table_prefix."_entityname set entityidcolumn='leadid' where tabid=7",
			"update ".$table_prefix."_entityname set entityidcolumn='account_id' where tabid=6",
			"update ".$table_prefix."_entityname set entityidcolumn='contact_id' where tabid=4",
			"update ".$table_prefix."_entityname set entityidcolumn='potential_id' where tabid=2",
			"update ".$table_prefix."_entityname set entityidcolumn='notesid' where tabid=8",
			"update ".$table_prefix."_entityname set entityidcolumn='ticketid' where tabid=13",
			"update ".$table_prefix."_entityname set entityidcolumn='activityid' where tabid=9",
			"update ".$table_prefix."_entityname set entityidcolumn='activityid' where tabid=10",
			"update ".$table_prefix."_entityname set entityidcolumn='product_id' where tabid=14",
			"update ".$table_prefix."_entityname set entityidcolumn='id' where tabid=29",
			"update ".$table_prefix."_entityname set entityidcolumn='invoiceid' where tabid=23",
			"update ".$table_prefix."_entityname set entityidcolumn='quote_id' where tabid=20",
			"update ".$table_prefix."_entityname set entityidcolumn='purchaseorderid' where tabid=21",
			"update ".$table_prefix."_entityname set entityidcolumn='salesorder_id' where tabid=22",
			"update ".$table_prefix."_entityname set entityidcolumn='vendor_id' where tabid=18",
			"update ".$table_prefix."_entityname set entityidcolumn='pricebookid' where tabid=19",
			"update ".$table_prefix."_entityname set entityidcolumn='campaignid' where tabid=26",
			"update ".$table_prefix."_entityname set entityidcolumn='id' where tabid=15",
			"alter table ".$table_prefix."_entityname MODIFY entityidcolumn varchar(150) NOT NULL",
			
			"update ".$table_prefix."_field set fieldlabel='Part Number' where tabid=14 and fieldname='productcode'",


			"alter table ".$table_prefix."_tab change customized customized integer(19)",
			"alter table ".$table_prefix."_tab add column ownedby integer(19)",
			"ALTER TABLE ".$table_prefix."_blocks ADD CONSTRAINT fk_1_".$table_prefix."_blocks FOREIGN KEY (tabid) REFERENCES ".$table_prefix."_tab(tabid) ON DELETE CASCADE",
			"alter table ".$table_prefix."_crmentity modify setype varchar(25)",

			"ALTER TABLE ".$table_prefix."_customview ADD  INDEX customview_entitytype_idx  (entitytype)",
			"ALTER TABLE ".$table_prefix."_customview ADD CONSTRAINT fk_1_".$table_prefix."_customview FOREIGN KEY (entitytype) REFERENCES ".$table_prefix."_tab (name) ON DELETE CASCADE",

			"alter table ".$table_prefix."_parenttabrel change parenttabid parenttabid integer(19)",
			"alter table ".$table_prefix."_parenttabrel change tabid tabid integer(19)",

			"ALTER TABLE ".$table_prefix."_parenttabrel ADD CONSTRAINT fk_1_".$table_prefix."_parenttabrel FOREIGN KEY (tabid) REFERENCES ".$table_prefix."_tab(tabid) ON DELETE CASCADE",

			"ALTER TABLE ".$table_prefix."_parenttabrel ADD CONSTRAINT fk_2_".$table_prefix."_parenttabrel FOREIGN KEY (parenttabid) REFERENCES ".$table_prefix."_parenttab(parenttabid) ON DELETE CASCADE",

			"ALTER TABLE ".$table_prefix."_entityname ADD CONSTRAINT fk_1_".$table_prefix."_entityname FOREIGN KEY (tabid) REFERENCES ".$table_prefix."_tab(tabid) ON DELETE CASCADE",

			"alter table ".$table_prefix."_parenttab engine=InnoDB",

			"update ".$table_prefix."_tab set customized=0",
			"update ".$table_prefix."_tab set ownedby=1",
			"update ".$table_prefix."_tab set ownedby=0 where tabid in (2,4,6,7,9,13,16,20,21,22,23,26)",
			   
		    );

foreach($query_array as $query)
{
	ExecuteQuery($query);
}


ExecuteQuery("ALTER TABLE ".$table_prefix."_users MODIFY user_password varchar(32)");

//Changes related to Product - Lead/Account/Contact/Potential relationship - Mickie - 13-01-2007
ExecuteQuery("delete from ".$table_prefix."_field where tabid=14 and fieldname in ('parent_id','contact_id')");

//Before drop the contactid from products, we have to save this product - contact relationship in seproductsrel table
//ExecuteQuery("insert into ".$table_prefix."_seproductsrel (select contactid, productid from ".$table_prefix."_products where contactid is not NULL)");
//In above query, if there is any duplicate entry then execution stopped. So we will insert undeleted products one by one
$product_contact_res = $adb->query("select contactid, productid from ".$table_prefix."_products inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid=".$table_prefix."_products.productid where ".$table_prefix."_crmentity.deleted=0 and contactid != 0 and contactid is NOT NULL");
for($i=0;$i<$adb->num_rows($product_contact_res);$i++)
{
	$crmid = $adb->query_result($product_contact_res,$i,'contactid');
	$productid = $adb->query_result($product_contact_res,$i,'productid');

	$adb->query("insert into ".$table_prefix."_seproductsrel values ($crmid , $productid)");
}
ExecuteQuery("alter table ".$table_prefix."_products drop column contactid");


ExecuteQuery("insert into ".$table_prefix."_relatedlists values(".$adb->getUniqueID($table_prefix.'_relatedlists').",14,7,'get_leads',9,'Leads',0)");
ExecuteQuery("insert into ".$table_prefix."_relatedlists values(".$adb->getUniqueID($table_prefix.'_relatedlists').",14,6,'get_accounts',10,'Accounts',0)");
ExecuteQuery("insert into ".$table_prefix."_relatedlists values(".$adb->getUniqueID($table_prefix.'_relatedlists').",14,4,'get_contacts',11,'Contacts',0)");
ExecuteQuery("insert into ".$table_prefix."_relatedlists values(".$adb->getUniqueID($table_prefix.'_relatedlists').",14,2,'get_opportunities',12,'Potentials',0)");

ExecuteQuery("alter table ".$table_prefix."_seproductsrel add column setype varchar(100)");
//we have to update setype for all existing entries which will be NULL before execute the following query
ExecuteQuery("update  ".$table_prefix."_seproductsrel,".$table_prefix."_crmentity set ".$table_prefix."_seproductsrel.setype=".$table_prefix."_crmentity.setype  where ".$table_prefix."_crmentity.crmid=".$table_prefix."_seproductsrel.crmid");


ExecuteQuery("CREATE TABLE ".$table_prefix."_version (id int(11) NOT NULL auto_increment, old_version varchar(30) default NULL, current_version varchar(30) default NULL, PRIMARY KEY  (id) ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

ExecuteQuery("delete from ".$table_prefix."_selectcolumn WHERE columnname LIKE '%".$table_prefix."_crmentityRelProducts%'");
//echo "<br><font color='red'>&nbsp; 5.0.2 ==> 5.0.3 Database changes has been done.</font><br>";

$migrationlog->debug("\n\nDB Changes from 5.0.2 to 5.0.3 RC2 -------- Ends \n\n");

?>
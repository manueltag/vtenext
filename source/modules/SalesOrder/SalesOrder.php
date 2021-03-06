<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header$
 * Description:  Defines the Account SugarBean Account entity with the necessary
 * methods and variables.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

include_once('config.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');
require_once('user_privileges/default_module_view.php');

// Account is used to store vtiger_account information.
class SalesOrder extends CRMEntity {
	var $log;
	var $db;

	var $table_name ;
	var $table_index= 'salesorderid';
	var $tab_name = Array();
	var $tab_name_index = Array();
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array();
	var $entity_table;

	var $billadr_table;

	var $object_name = "SalesOrder";

	var $new_schema = true;

	var $update_product_array = Array();

	var $column_fields = Array();

	var $sortby_fields = Array('subject','smownerid','accountname','lastname');

	// This is used to retrieve related vtiger_fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'smownerid', 'opportunity_id', 'case_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id', 'parent_name', 'member_id' );

	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
				// Module Sequence Numbering
				//'Order No'=>Array('crmentity'=>'crmid'),
				'Order No'=>Array('salesorder','salesorder_no'),
				// END
				'Subject'=>Array('salesorder'=>'subject'),
				'Account Name'=>Array('account'=>'accountid'),
				'Quote Name'=>Array('quotes'=>'quoteid'),
				'Total'=>Array('salesorder'=>'total'),
				'Assigned To'=>Array('crmentity'=>'smownerid')
				);

	var $list_fields_name = Array(
				        'Order No'=>'salesorder_no',
				        'Subject'=>'subject',
				        'Account Name'=>'account_id',
				        'Quote Name'=>'quote_id',
					'Total'=>'hdnGrandTotal',
				        'Assigned To'=>'assigned_user_id'
				      );
	var $list_link_field= 'subject';

	var $search_fields = Array(
				'Order No'=>Array('salesorder'=>'salesorder_no'),
				'Subject'=>Array('salesorder'=>'subject'),
				'Account Name'=>Array('account'=>'accountid'),
				'Quote Name'=>Array('salesorder'=>'quoteid')
				);

	var $search_fields_name = Array(
					'Order No'=>'salesorder_no',
				        'Subject'=>'subject',
				        'Account Name'=>'account_id',
				        'Quote Name'=>'quote_id'
				      );

	// This is the list of vtiger_fields that are required.
	var $required_fields =  array("accountname"=>1);

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'subject';
	var $default_sort_order = 'ASC';
	//var $groupTable = Array('vtiger_sogrouprelation','salesorderid');

	var $mandatory_fields = Array('subject','createdtime' ,'modifiedtime');
	//crmv@10759
	var $search_base_field = 'subject';
	//crmv@10759 e
	/** Constructor Function for SalesOrder class
	 *  This function creates an instance of LoggerManager class using getLogger method
	 *  creates an instance for PearDatabase class and get values for column_fields array of SalesOrder class.
	 */
	function SalesOrder() {
		global $table_prefix;
		parent::__construct(); // crmv@37004
		$this->table_name = $table_prefix."_salesorder";
		$this->tab_name = Array($table_prefix.'_crmentity',$table_prefix.'_salesorder',$table_prefix.'_sobillads',$table_prefix.'_soshipads',$table_prefix.'_salesordercf',$table_prefix.'_invoice_recurring_info');
		$this->tab_name_index = Array($table_prefix.'_crmentity'=>'crmid',$table_prefix.'_salesorder'=>'salesorderid',$table_prefix.'_sobillads'=>'sobilladdressid',$table_prefix.'_soshipads'=>'soshipaddressid',$table_prefix.'_salesordercf'=>'salesorderid',$table_prefix.'_invoice_recurring_info'=>'salesorderid');
		$this->customFieldTable = Array($table_prefix.'_salesordercf', 'salesorderid');
		$this->entity_table = $table_prefix."_crmentity";
		$this->billadr_table = $table_prefix."_sobillads";
		$this->log =LoggerManager::getLogger('SalesOrder');
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('SalesOrder');
	}

	function save_module($module)
	{
		global $table_prefix;

		//in ajax save we should not call this function, because this will delete all the existing product values
		if($_REQUEST['action'] != 'SalesOrderAjax' && $_REQUEST['ajxaction'] != 'DETAILVIEW' && $_REQUEST['action'] != 'MassEditSave')
		{
			$InventoryUtils = InventoryUtils::getInstance(); // crmv@42024
			//Based on the total Number of rows we will save the product relationship with this entity
			$InventoryUtils->saveInventoryProductDetails($this, 'SalesOrder');
		}

		// Update the currency id and the conversion rate for the sales order
		$update_query = "update ".$table_prefix."_salesorder set currency_id=?, conversion_rate=? where salesorderid=?";
		$update_params = array($this->column_fields['currency_id'], $this->column_fields['conversion_rate'], $this->id);
		$this->db->pquery($update_query, $update_params);

		$this->recalculateAccToServices($this->id); //crmv@16644
		$this->recalculateAccToProducts($this->id); //crmv@16644
	}

	//crmv@16644
	function mark_deleted($id)
	{
		CRMEntity::mark_deleted($id);
		$this->recalculateAccToServices($id);
		$this->recalculateAccToProducts($id);
	}

	function recalculateAccToServices($salesorderid) {
		global $table_prefix;
		$result = $this->db->query("SELECT accountid FROM ".$table_prefix."_salesorder WHERE salesorderid = $salesorderid");
		if ($result) $accountid = $this->db->query_result($result,0,'accountid');

		if (empty($accountid)) return;

		$this->db->query("DELETE FROM ".$table_prefix."_crmentityrel WHERE crmid = $accountid AND relcrmid IN (SELECT id FROM crmv_inventorytoacc WHERE accountid = $accountid AND type = 'Services') AND module = 'Accounts' AND relmodule = 'Services'");
		$result = $this->db->query("SELECT productid,id FROM ".$table_prefix."_inventoryproductrel
									INNER JOIN ".$table_prefix."_crmentity ON ".$table_prefix."_crmentity.crmid = ".$table_prefix."_inventoryproductrel.productid AND ".$table_prefix."_crmentity.setype = 'Services'
									WHERE ".$table_prefix."_crmentity.deleted = 0 AND id IN (
										SELECT salesorderid FROM ".$table_prefix."_salesorder
										INNER JOIN ".$table_prefix."_crmentity ON ".$table_prefix."_crmentity.crmid = ".$table_prefix."_salesorder.salesorderid
										WHERE ".$table_prefix."_crmentity.deleted = 0 AND ".$table_prefix."_salesorder.accountid = $accountid AND ".$table_prefix."_salesorder.sostatus <> 'Cancelled'
									)");
		$this->db->pquery("DELETE FROM crmv_inventorytoacc where accountid = ? and type = 'Services'",array($accountid));
		while($row=$this->db->fetchByAssoc($result)) {

			$result_tmp1 = $this->db->pquery("SELECT * FROM crmv_inventorytoacc WHERE accountid = ? AND sorderid = ? AND id = ?",array($accountid,$row['id'],$row['productid']));
			if ($result_tmp1 && $this->db->num_rows($result_tmp1)>0) {
				//do nothing
			}
			else $this->db->pquery("INSERT INTO crmv_inventorytoacc (accountid,sorderid,id,type) VALUES (?,?,?,?)",array($accountid,$row['id'],$row['productid'],'Services'));

			$result_tmp2 = $this->db->pquery("SELECT * FROM ".$table_prefix."_crmentityrel WHERE crmid = ? AND relcrmid = ?",array($accountid,$row['productid']));
			if ($result_tmp2 && $this->db->num_rows($result_tmp2)>0) {
				//do nothing
			}
			else $this->db->pquery("INSERT INTO ".$table_prefix."_crmentityrel (crmid,module,relcrmid,relmodule) VALUES (?,?,?,?)",array($accountid,'Accounts',$row['productid'],'Services'));
		}
	}

	function recalculateAccToProducts($salesorderid) {
		global $table_prefix;
		$result = $this->db->query("SELECT accountid FROM ".$table_prefix."_salesorder WHERE salesorderid = $salesorderid");
		if ($result) $accountid = $this->db->query_result($result,0,'accountid');

		if (empty($accountid)) return;

		$this->db->query("DELETE FROM ".$table_prefix."_seproductsrel WHERE crmid = $accountid AND productid IN (SELECT id FROM crmv_inventorytoacc WHERE accountid = $accountid AND type = 'Products') AND setype = 'Accounts'");
		$result = $this->db->query("SELECT productid,id FROM ".$table_prefix."_inventoryproductrel
									INNER JOIN ".$table_prefix."_crmentity ON ".$table_prefix."_crmentity.crmid = ".$table_prefix."_inventoryproductrel.productid AND ".$table_prefix."_crmentity.setype = 'Products'
									WHERE ".$table_prefix."_crmentity.deleted = 0 AND id IN (
										SELECT salesorderid FROM ".$table_prefix."_salesorder
										INNER JOIN ".$table_prefix."_crmentity ON ".$table_prefix."_crmentity.crmid = ".$table_prefix."_salesorder.salesorderid
										WHERE ".$table_prefix."_crmentity.deleted = 0 AND ".$table_prefix."_salesorder.accountid = $accountid AND ".$table_prefix."_salesorder.sostatus <> 'Cancelled'
									)");
		$this->db->pquery("DELETE FROM crmv_inventorytoacc where accountid = ? and type = 'Products'",array($accountid));
		while($row=$this->db->fetchByAssoc($result)) {

			$result_tmp1 = $this->db->pquery("SELECT * FROM crmv_inventorytoacc WHERE accountid = ? AND sorderid = ? AND id = ?",array($accountid,$row['id'],$row['productid']));
			if ($result_tmp1 && $this->db->num_rows($result_tmp1)>0) {
				//do nothing
			}
			else $this->db->pquery("INSERT INTO crmv_inventorytoacc (accountid,sorderid,id,type) VALUES (?,?,?,?)",array($accountid,$row['id'],$row['productid'],'Products'));

			$result_tmp = $this->db->pquery("SELECT * FROM ".$table_prefix."_seproductsrel WHERE crmid = ? AND productid = ?",array($accountid,$row['productid']));
			if ($result_tmp && $this->db->num_rows($result_tmp)>0) {
				//do nothing
			}
			else $this->db->pquery("INSERT INTO ".$table_prefix."_seproductsrel (crmid,productid,setype) VALUES (?,?,?)",array($accountid,$row['productid'],'Accounts'));
		}
	}
	//crmv@16644

	/** Function to get the invoices associated with the Sales Order
	 *  This function accepts the id as arguments and execute the MySQL query using the id
	 *  and sends the query and the id as arguments to renderRelatedInvoices() method.
	 */
	function get_invoices($id)
	{
		global $log,$singlepane_view,$table_prefix;
		$log->debug("Entering get_invoices(".$id.") method ...");
		require_once('modules/Invoice/Invoice.php');

		$focus = CRMEntity::getInstance('Invoice');

		$button = '';
		if($singlepane_view == 'true')
			$returnset = '&return_module=SalesOrder&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module=SalesOrder&return_action=CallRelatedList&return_id='.$id;

			$query = "select ".$table_prefix."_crmentity.*, ".$table_prefix."_invoice.*, ".$table_prefix."_account.accountname, ".$table_prefix."_salesorder.subject as salessubject, case when (".$table_prefix."_users.user_name is not null) then ".$table_prefix."_users.user_name else ".$table_prefix."_groups.groupname end as user_name
				from ".$table_prefix."_invoice
				inner join ".$table_prefix."_invoicecf on ".$table_prefix."_invoicecf.invoiceid = ".$table_prefix."_invoice.invoiceid
				inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid=".$table_prefix."_invoice.invoiceid
				left outer join ".$table_prefix."_account on ".$table_prefix."_account.accountid=".$table_prefix."_invoice.accountid
				inner join ".$table_prefix."_salesorder on ".$table_prefix."_salesorder.salesorderid=".$table_prefix."_invoice.salesorderid
				left join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_crmentity.smownerid
				left join ".$table_prefix."_groups on ".$table_prefix."_groups.groupid=".$table_prefix."_crmentity.smownerid
				where ".$table_prefix."_crmentity.deleted=0 and ".$table_prefix."_salesorder.salesorderid=".$id;

		$log->debug("Exiting get_invoices method ...");
		return GetRelatedList('SalesOrder','Invoice',$focus,$query,$button,$returnset);

	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	// crmv@35693 crmv@38798 crmv@73751
	function generateReportsSecQuery($module,$secmodule,$reporttype,$useProductJoin = true){
		global $table_prefix;

		// don't join with products in total count
		if ($reporttype != 'COLUMNSTOTOTAL' && $useProductJoin) {
			$productjoins = "left join ".$table_prefix."_inventoryproductrel ".substr($table_prefix.'_inventoryproductrelSalesOrder',0,29)." on ".$table_prefix."_salesorder.salesorderid = ".substr($table_prefix.'_inventoryproductrelSalesOrder',0,29).".id
							left join ".$table_prefix."_products ".$table_prefix."_productsSalesOrder on ".$table_prefix."_productsSalesOrder.productid = ".substr($table_prefix.'_inventoryproductrelSalesOrder',0,29).".productid
							left join ".$table_prefix."_service ".$table_prefix."_serviceSalesOrder on ".$table_prefix."_serviceSalesOrder.serviceid = ".substr($table_prefix.'_inventoryproductrelSalesOrder',0,29).".productid ";
		}

		$query = $this->getRelationQuery($module,$secmodule,$table_prefix."_salesorder","salesorderid");
		$query .= " left join ".$table_prefix."_salesordercf on ".$table_prefix."_salesorder.salesorderid = ".$table_prefix."_salesordercf.salesorderid
				  	left join ".$table_prefix."_sobillads on ".$table_prefix."_salesorder.salesorderid=".$table_prefix."_sobillads.sobilladdressid
				   	left join ".$table_prefix."_soshipads on ".$table_prefix."_salesorder.salesorderid=".$table_prefix."_soshipads.soshipaddressid
					left join ".$table_prefix."_inventorytotals ".substr($table_prefix.'_inventorytotalsSalesOrder',0,29)." on ".substr($table_prefix.'_inventorytotalsSalesOrder',0,29).".id = ".$table_prefix."_salesorder.salesorderid
					$productjoins
					left join ".$table_prefix."_groups ".$table_prefix."_groupsSalesOrder on ".$table_prefix."_groupsSalesOrder.groupid = ".$table_prefix."_crmentitySalesOrder.smownerid
				   	left join ".$table_prefix."_users ".$table_prefix."_usersSalesOrder on ".$table_prefix."_usersSalesOrder.id = ".$table_prefix."_crmentitySalesOrder.smownerid
				   	left join ".$table_prefix."_potential ".$table_prefix."_potentialRelSalesOrder on ".$table_prefix."_potentialRelSalesOrder.potentialid = ".$table_prefix."_salesorder.potentialid
				   	left join ".$table_prefix."_contactdetails ".substr($table_prefix.'_contactdetailsSalesOrder',0,29)." on ".$table_prefix."_salesorder.contactid = ".substr($table_prefix.'_contactdetailsSalesOrder',0,29).".contactid
				   	left join ".$table_prefix."_invoice_recurring_info on ".$table_prefix."_salesorder.salesorderid = ".$table_prefix."_invoice_recurring_info.salesorderid
				   	left join ".$table_prefix."_quotes ".$table_prefix."_quotesSalesOrder on ".$table_prefix."_salesorder.quoteid = ".$table_prefix."_quotesSalesOrder.quoteid
				   	left join ".$table_prefix."_account ".$table_prefix."_accountSalesOrder on ".$table_prefix."_accountSalesOrder.accountid = ".$table_prefix."_salesorder.accountid ";

		return $query;
	}
	// crmv@35693e crmv@38798e	crmv@73751e

	/*
	 * Function to get the relation tables for related modules
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	function setRelationTables($secmodule){
		global $table_prefix;
		$rel_tables = array (
			"Calendar" =>array($table_prefix."_seactivityrel"=>array("crmid","activityid"),$table_prefix."_salesorder"=>"salesorderid"),
			"Invoice" =>array($table_prefix."_invoice"=>array("salesorderid","invoiceid"),$table_prefix."_salesorder"=>"salesorderid"),
			"Documents" => array($table_prefix."_senotesrel"=>array("crmid","notesid"),$table_prefix."_salesorder"=>"salesorderid"),
		);
		return $rel_tables[$secmodule];
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log,$table_prefix;
		if(empty($return_module) || empty($return_id)) return;

		if($return_module == 'Accounts') {
			$this->trash('SalesOrder',$id);
		}
		elseif($return_module == 'Quotes') {
			$relation_query = 'UPDATE '.$table_prefix.'_salesorder SET quoteid=0 WHERE salesorderid=?';
			$this->db->pquery($relation_query, array($id));
		}
		elseif($return_module == 'Potentials') {
			$relation_query = 'UPDATE '.$table_prefix.'_salesorder SET potentialid=0 WHERE salesorderid=?';
			$this->db->pquery($relation_query, array($id));
		}
		elseif($return_module == 'Contacts') {
			$relation_query = 'UPDATE '.$table_prefix.'_salesorder SET contactid=0 WHERE salesorderid=?';
			$this->db->pquery($relation_query, array($id));
		} else {
			$sql = 'DELETE FROM '.$table_prefix.'_crmentityrel WHERE (crmid=? AND relmodule=? AND relcrmid=?) OR (relcrmid=? AND module=? AND crmid=?)';
			$params = array($id, $return_module, $return_id, $id, $return_module, $return_id);
			$this->db->pquery($sql, $params);
		}
		$this->db->pquery("UPDATE {$table_prefix}_crmentity SET modifiedtime = ? WHERE crmid IN (?,?)", array($this->db->formatDate(date('Y-m-d H:i:s'), true), $id, $return_id)); // crmv@49398 crmv@69690
	}
	public function getJoinClause($tableName) {
		global $table_prefix;
		if ($tableName == $table_prefix.'_invoice_recurring_info') {
			return 'LEFT JOIN';
		}
		return parent::getJoinClause($tableName);
	}
}
?>
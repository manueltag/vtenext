<?php

class TimecardsHandler extends VTEventHandler {

	function handleEvent($eventName, $entityData) {
		global $adb, $current_user;
		global $table_prefix;
		// check irs a timcard we're saving.
		if (!($entityData->focus instanceof Timecards)) {
			return;
		}

		if($eventName == 'vtiger.entity.beforesave') {
			//crmv@14132
			$timecardsid = $entityData->getId();
			$old_worktime = '';
			if(!empty($timecardsid)) {
				$result = $adb->pquery('SELECT worktime FROM '.$table_prefix.'_timecards WHERE timecardsid = ?', array($timecardsid));
				if($adb->num_rows($result) > 0) {
					$old_worktime = $adb->query_result($result,0,'worktime');
				}
			}
			$entityData->old_worktime = $old_worktime;
			//crmv@14132e
		}

		if($eventName == 'vtiger.entity.aftersave') {
			//crmv@14132
			$data = $entityData->getData();
			if ($data['worktime'] != $entityData->old_worktime) {
				$change_sec = Timecards::get_seconds($data['worktime']) - Timecards::get_seconds($entityData->old_worktime);
				$change_hours = $change_sec/60/60;
				$change_days = $change_hours/24;
				// crmv@101363
				$tktFocus = CRMEntity::getInstance('HelpDesk');
				$tktFocus->id = $data['ticket_id'];
				$tktFocus->mode = 'edit';
				$tktFocus->retrieve_entity_info_no_html($data['ticket_id'],'HelpDesk');
				
				$tktFocus->column_fields['hours'] += $change_hours;
				$tktFocus->column_fields['days'] += $change_days;

				// force an update, because it's not working for unknown reasons
				$adb->pquery('UPDATE '.$table_prefix.'_troubletickets SET hours = ? WHERE ticketid = ?', array($tktFocus->column_fields['hours'],$tktFocus->id));
				$adb->pquery('UPDATE '.$table_prefix.'_troubletickets SET days = ? WHERE ticketid = ?', array($tktFocus->column_fields['days'],$tktFocus->id));
				
				// Empty Comments
				$_REQUEST['comments'] = '';
				$tktFocus->column_fields['comments'] = '';
			
				$tktFocus->save('HelpDesk');
				// crmv@101363e
			}
			
			/*	Questa parte era presente nel modulo originale

			// Entity has been saved, take next action
			$focus = CRMEntity::getInstance("Timecards");
			if ($entityData->isNew()) {
				$focus = $entityData->focus;
			} else {
				echo "a";
				$focus->retrieve_entity_info($entityData->focus->id, "Timecards");
				$focus->id = $entityData->focus->id;
			}
			exit;

			$focus->save_related_module("HelpDesk", $focus->column_fields["ticket_id"], "Timecards", $focus->id);

			// delete the item off the invoice if it exists.
			if ($focus->column_fields["invoiceid"]) {
				$query = "SELECT * ";
				$query .= "FROM vtiger_invoicetimecardsrel ";
				$query .= "WHERE timecardsid = ".$focus->id;
				$result = $adb->query($query);
				if ($result && ($row = $adb->fetch_array($result))) {
					$query = "DELETE FROM vtiger_invoicetimecardsrel ";
					$query .= "WHERE timecardsid = ".$focus->id;
					$adb->query($query);
					$query = "DELETE FROM vtiger_inventoryproductrel ";
					$query .= "WHERE lineitem_id = ".$row["lineitem"]." ";
					$query .= "AND id = ".$focus->column_fields["invoiceid"];
					$adb->query($query);
				}
			}

			// check if the timecard is not billed.
			if ($focus->column_fields["timecardtype"] != "Billed") {
				// check to see if we have an invoice or not.
				if (!$focus->column_fields["invoiceid"]) {
					return;
				} else {
					$query = "UPDATE vtiger_timecards SET invoiceid = 0 WHERE timecardsid = ".$focus->id;
					$adb->query($query);
				}
			} else {
				// retrieve the product to grab the price.
				$product = CRMEntity::getInstance("Products");
				$product->retrieve_entity_info($focus->column_fields["productid"], "Products");
				$product->id = $focus->column_fields["productid"];
				// check for a current invoice or create a new one.
				$found_invoice = false;
				if ($focus->column_fields["invoiceid"]) {
					$query = "SELECT * ";
					$query .= "FROM vtiger_crmentity ";
					$query .= "WHERE crmid = ".$focus->column_fields["invoiceid"]." ";
					$query .= "AND deleted = 0";
					$result = $adb->query($query);
					if ($result && $adb->num_rows($result)) {
						$found_invoice = true;
					}
				}
				$invoice = CRMEntity::getInstance("Invoice");
				if ($found_invoice) {
					$invoice->retrieve_entity_info($focus->column_fields["invoiceid"], "Invoice");
					$invoice->id = $focus->column_fields["invoiceid"];
				} else {
					// grab the ticket to get the account id.
					$ticket = CRMEntity::getInstance("HelpDesk");
					$ticket->retrieve_entity_info($focus->column_fields["ticket_id"], "HelpDesk");
					if (!$ticket->column_fields["parent_id"]) {
						return;
					}
					// check whether the parent is an account or contact.
					$query = "SELECT setype ";
					$query .= "FROM vtiger_crmentity ";
					$query .= "WHERE crmid = ".$ticket->column_fields["parent_id"];
					$result = $adb->query($query);
					$setype = $adb->query_result($result, 0, 0);
					// grab the account id.
					$account_id = 0;
					switch ($setype) {
						case "Contacts":
							$contact = CRMEntity::getInstance("Contacts");
							$contact->retrieve_entity_info($ticket->column_fields["parent_id"], "Contacts");
							$account_id = $contact->column_fields["account_id"];
							break;
						case "Accounts":
							$account_id = $ticket->column_fields["parent_id"];
							break;
					}
					if (!$account_id) {
						return;
					}
					// try to locate an open invoice to amend.
					$query = "SELECT * ";
					$query .= "FROM vtiger_invoice i ";
					$query .= "INNER JOIN vtiger_crmentity c ON i.invoiceid = c.crmid ";
					$query .= "WHERE i.accountid = ".$account_id." ";
					$query .= "AND i.invoicestatus = 'Created' ";
					$query .= "AND c.deleted = 0";
					$result = $adb->query($query);
					// retrieve the invoice if it is found, otherwise create a new one.
					if ($result && ($row = $adb->fetch_array($result))) {
						$invoice->retrieve_entity_info($row["invoiceid"], "Invoice");
						$invoice->id = $row["invoiceid"];
					} else {
						$account = CRMEntity::getInstance("Accounts");
						$account->retrieve_entity_info($account_id, "Accounts");
						$invoice->column_fields["subject"] = $account->column_fields["accountname"]." - ".date("M")." - Week ".(floor(date("j") / 7)+1);
						$invoice->column_fields["invoicedate"] = date("Y-m-d");
						$invoice->column_fields["duedate"] = date("Y-m-d", time() + (86400 * 7));
						$invoice->column_fields["account_id"] = $account_id;
						$invoice->column_fields["invoicestatus"] = "Created";
						$invoice->column_fields["assigntype"] = "U";
						$invoice->column_fields["assigned_user_id"] = $current_user->id;
						$invoice->column_fields["bill_city"] = $account->column_fields["bill_city"];
						$invoice->column_fields["ship_city"] = $account->column_fields["ship_city"];
						$invoice->column_fields["bill_street"] = $account->column_fields["bill_street"];
						$invoice->column_fields["ship_street"] = $account->column_fields["ship_street"];
						$invoice->column_fields["bill_state"] = $account->column_fields["bill_state"];
						$invoice->column_fields["ship_state"] = $account->column_fields["ship_state"];
						$invoice->column_fields["bill_code"] = $account->column_fields["bill_code"];
						$invoice->column_fields["ship_code"] = $account->column_fields["ship_code"];
						$invoice->column_fields["bill_country"] = $account->column_fields["bill_country"];
						$invoice->column_fields["ship_country"] = $account->column_fields["ship_country"];
						$invoice->column_fields["bill_pobox"] = $account->column_fields["bill_pobox"];
						$invoice->column_fields["ship_pobox"] = $account->column_fields["ship_pobox"];
						$invoice->save("Invoice");
						$query = "UPDATE vtiger_invoice SET taxtype = 'individual' WHERE invoiceid = ".$invoice->id;
						$adb->query($query);
					}
					$query = "UPDATE vtiger_timecards SET invoiceid = ".$invoice->id." WHERE timecardsid = ".$focus->id;
					$adb->query($query);
				}

				// grab the tax rates for the product.
				$query = "SELECT * ";
				$query .= "FROM vtiger_producttaxrel ";
				$query .= "WHERE productid = ".$focus->column_fields["productid"];
				$result = $adb->query($query);
				$tax = array(1 => "NULL", "2" => "NULL", 3 => "NULL");
				while ($result && ($row = $adb->fetch_array($result))) {
					$tax[$row["taxid"]] = $row["taxpercentage"];
				}

				// insert the product row.
				$lineitem = $adb->getUniqueID('vtiger_inventoryproductrel');
				$query = "INSERT INTO vtiger_inventoryproductrel (lineitem_id,id, productid, sequence_no, quantity, comment, listprice, tax1, tax2, tax3) VALUES ";
				$query .= "(".$lineitem.",".$invoice->id.", ".$focus->column_fields["productid"].", 1, ".$focus->column_fields["duration"].", '".$focus->column_fields["shortdesc"]."', ".$product->column_fields["unit_price"].", ".$tax[1].", ".$tax[2].", ".$tax[3].")";
				$adb->query($query);
				$query = "INSERT INTO vtiger_invoicetimecardsrel (lineitem, timecardsid) VALUES ";
				$query .= "(".$lineitem.", ".$focus->id.")";
				$adb->query($query);
			}

			// update the invoice price.
			$query = "SELECT * FROM vtiger_inventoryproductrel WHERE id = ".$invoice->id;
			$result = $adb->query($query);
			$subtotal = 0;
			while ($result && $row = $adb->fetch_array($result)) {
				$subtotal += $row["quantity"] * $row["listprice"];
				$tax = 0;
				for ($i = 1; $i <= 3; $i++) {
					if ($row["tax".$i]) {
						$tax += ($row["tax".$i] / 100) * $subtotal;
					}
				}
			}
			$real_subtotal = $subtotal;
			if ($invoice->column_fields["hdnDiscountPercent"] > 0) {
				$subtotal = $subtotal * ((100 - $invoice->column_fields["hdnDiscountPercent"]) / 100);
			}
			$total = $subtotal + $invoice->column_fields["hdnS_H_Amount"] + $invoice->column_fields["txtAdjustment"] - $invoice->column_fields["hdnDiscountAmount"] + $tax;
			$query = "UPDATE vtiger_invoice SET subtotal = '".$real_subtotal."', total = '".$total."' WHERE invoiceid = ".$invoice->id;
			$adb->query($query);
			*/
			//crmv@14132e
		}
	}
}
?>

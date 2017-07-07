<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ************************************************************************************/
class MyNotesHandler extends VTEventHandler {

	function handleEvent($eventName, $data) {
		
		if (!($data->focus instanceof MyNotes)) {
			return;
		}

		if($eventName == 'vtiger.entity.beforesave') {
			$column_fields = $data->getData();
			if ($column_fields['subject'] == '') {
				global $listview_max_textlength;
				$temp_val = trim($column_fields['description']);
				if(strlen($temp_val) > $listview_max_textlength) {
					$temp_val = substr($temp_val,0,$listview_max_textlength).'...';
				}
				$data->set('subject',$temp_val);
			}
		}
	}
}
?>
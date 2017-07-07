<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ************************************************************************************/

/* crmv@59626 */

class ModComments_RepliesModel {
	
	private $data = array();
	private $ids = array();
	
	function __construct($commentid, $searchkey='', $replyRows='') { //crmv@31301
		global $adb;
		$moduleName = 'ModComments';
		if(vtlib_isModuleActive($moduleName)) {
			if (is_array($replyRows)) {
				$replyRows = $replyRows[$commentid];
				if (!empty($replyRows)) {
					foreach($replyRows as $r) {
						$replyModel = new ModComments_ReplyModel($r,$searchkey);	//crmv@31301
						$this->data[] = $replyModel;
						$this->ids[] = $replyModel->id();
					}
				}
			} else {
				$entityInstance = CRMEntity::getInstance($moduleName);
				
				$where = " AND $entityInstance->table_name.parent_comments = ?";
				$query = $entityInstance->getListQuery($moduleName, $where, true, true);	//crmv@32429
				
				$queryCriteria .= sprintf(" ORDER BY %s.%s", $entityInstance->table_name, $entityInstance->table_index);
				$query .= $queryCriteria;
				
				$result = $adb->pquery($query, array($commentid));
	
				if($adb->num_rows($result)) {
					while($resultrow = $adb->fetch_array($result)) {
						$replyModel = new ModComments_ReplyModel($resultrow,$searchkey);	//crmv@31301
						$this->data[] = $replyModel;
						$this->ids[] = $replyModel->id();
					}
				}
			}
		}
	}
	
	function getReplies() {
		return $this->data;
	}
	
	function getRepliesIds() {
		return $this->ids;
	}
}
class ModComments_ReplyModel extends ModComments_CommentsModel {
	
	private $max_replies_for_comment = 5;
	
	function __construct($datarow, $searchkey='') {	//crmv@31301
		$this->data = $datarow;
		$this->searchkey = $searchkey;	//crmv@31301
	}
	
	function getMaxRepliesForComment() {
		return $this->max_replies_for_comment;
	}
}
?>
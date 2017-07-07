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

global $currentModule, $current_user;
$modObj = CRMEntity::getInstance($currentModule);

$ajaxaction = $_REQUEST["ajxaction"];

if($ajaxaction == 'WIDGETADDCOMMENT') {
	if (isPermitted($currentModule, 'EditView', '') == 'yes') {
		$modObj->column_fields['commentcontent'] = vtlib_purify(strip_tags($_REQUEST['comment']));
		$modObj->column_fields['related_to'] = vtlib_purify($_REQUEST['parentid']);
		$modObj->column_fields['visibility_comm'] = vtlib_purify($_REQUEST['visibility']);
		$modObj->column_fields['assigned_user_id'] = $current_user->id;
		$modObj->save($currentModule);

		if(empty($modObj->column_fields['smcreatorid'])) $modObj->column_fields['smcreatorid'] = $current_user->id;
		if(empty($modObj->column_fields['modifiedtime'])) $modObj->column_fields['modifiedtime'] = date('Y-m-d H:i:s');
		if(empty($modObj->column_fields['createdtime'])) $modObj->column_fields['createdtime'] = date('Y-m-d H:i:s');	//crmv@57337
		$modObj->column_fields['crmid'] = $modObj->id;

		$widgetInstance = $modObj->getWidget('DetailViewBlockCommentWidget');
		echo ':#:SUCCESS'. $widgetInstance->processItem($modObj->getAsCommentModel($modObj->column_fields));
	} else {
		echo ':#:FAILURE';
	}
} elseif($ajaxaction == 'WIDGETADDREPLY') {
	if (isPermitted($currentModule, 'EditView', '') == 'yes') {
		$modObj->column_fields['commentcontent'] = vtlib_purify(strip_tags($_REQUEST['comment']));
		$modObj->column_fields['related_to'] = vtlib_purify($_REQUEST['parentid']);
		$modObj->column_fields['assigned_user_id'] = $current_user->id;
		if (vtlib_purify($_REQUEST['parent_comment']) != '') {
			$modObj->column_fields['parent_comments'] = vtlib_purify($_REQUEST['parent_comment']);
		}
		$modObj->save($currentModule);

		if(empty($modObj->column_fields['smcreatorid'])) $modObj->column_fields['smcreatorid'] = $current_user->id;
		if(empty($modObj->column_fields['modifiedtime'])) $modObj->column_fields['modifiedtime'] = date('Y-m-d H:i:s');
		if(empty($modObj->column_fields['createdtime'])) $modObj->column_fields['createdtime'] = date('Y-m-d H:i:s');	//crmv@57337
		$modObj->column_fields['crmid'] = $modObj->id;

		$widgetInstance = $modObj->getWidget('DetailViewBlockCommentWidget');
		//crmv@59626
		//echo ':#:SUCCESS'. $widgetInstance->processItemReply(new ModComments_ReplyModel($modObj->column_fields));
		echo ':#:SUCCESS';
		//crmv@59626e
	} else {
		echo ':#:FAILURE';
	}
} elseif($ajaxaction == 'WIDGETDELETECOMMENT') {
	// crmv@101967
	$commentid = intval($_REQUEST['id']);
	$modObj->retrieve_entity_info($commentid, $currentModule);
	$modObj->column_fields['smcreatorid'] = getSingleFieldValue($table_prefix.'_crmentity', 'smcreatorid', 'crmid', $commentid);
	$model = $modObj->getAsCommentModel($modObj->column_fields);
	// check for permission
	if ($model->canDeletePost()) {
		if ($modObj->trash($currentModule, $commentid) === false) {
			echo ':#:FAILURE';
		} else {
			echo ':#:SUCCESS';
		}
	} else {
		echo ':#:FAILURE';
	}
	// crmv@101967e
// crmv@43050
} elseif($ajaxaction == 'WIDGETADDUSERS') {
	$commentid = intval($_REQUEST['commentid']);
	$userlist = array_filter(array_map('intval', explode('|', $_REQUEST['users_comm'])));

	if ($commentid > 0 && count($userlist) > 0 && isPermitted($currentModule, 'EditView', $commentid) == 'yes') {
		$modObj->retrieve_entity_info($commentid, $currentModule);
		$modObj->id = $commentid;

		$modObj->column_fields['crmid'] = $modObj->id;
		if(empty($modObj->column_fields['smcreatorid'])) $modObj->column_fields['smcreatorid'] = $modObj->column_fields['assigned_user_id'];

		$modObj->addUsers($userlist);

		$widgetInstance = $modObj->getWidget('DetailViewBlockCommentWidget');
		echo ':#:SUCCESS'.$widgetInstance->processItem($modObj->getAsCommentModel($modObj->column_fields));
	} else {
		echo ':#:FAILURE';
	}
} elseif($ajaxaction == 'WIDGETGETCOMMENT') {
	$commentid = intval($_REQUEST['commentid']);

	if ($commentid > 0 && isPermitted($currentModule, 'DetailView', $commentid) == 'yes') {
		// crmv@43448 - set as read/unread
		if ($_REQUEST['setasunread'] != '' && isPermitted($currentModule, 'EditView', $commentid) == 'yes') {
			if ($_REQUEST['setasunread'] == '1') {
				$modObj->setAsUnread($commentid);
			} elseif ($_REQUEST['setasunread'] == '0') {
				$modObj->setAsRead($commentid);
			}
		}
		// crmv@43448e

		$modObj->retrieve_entity_info($commentid, $currentModule);
		$modObj->id = $commentid;

		$modObj->column_fields['crmid'] = $modObj->id;
		if(empty($modObj->column_fields['smcreatorid'])) $modObj->column_fields['smcreatorid'] = $modObj->column_fields['assigned_user_id'];

		$widgetInstance = $modObj->getWidget('DetailViewBlockCommentWidget');
		echo ':#:SUCCESS'.$widgetInstance->processItem($modObj->getAsCommentModel($modObj->column_fields));
	} else {
		echo ':#:FAILURE';
	}
// crmv@43050e
} elseif($ajaxaction == 'DETAILVIEW') {
	// crmv@67410
	$crmid = $_REQUEST["recordid"];
	$tablename = $_REQUEST["tableName"];
	$fieldname = $_REQUEST["fldName"];
	$fieldvalue = utf8RawUrlDecode($_REQUEST["fieldValue"]); 
	if($crmid != "")
	{
		$permEdit = isPermitted($currentModule, 'DetailViewAjax', $crmid);
		$permField = getFieldVisibilityPermission($currentModule, $current_user->id, $fieldname);
		
		if ($permEdit == 'yes' && $permField == 0) {
			$modObj->retrieve_entity_info($crmid,$currentModule);
			$modObj->column_fields[$fieldname] = $fieldvalue;

			$modObj->id = $crmid;
			$modObj->mode = "edit";
			$modObj->save($currentModule);
			if($modObj->id != "") {
				echo ":#:SUCCESS";
			} else {
				echo ":#:FAILURE";
			}   
		} else {
			echo ":#:FAILURE";
		}
	} else {
		echo ":#:FAILURE";
	}
	// crmv@67410e
} elseif($ajaxaction == "LOADRELATEDLIST" || $ajaxaction == "DISABLEMODULE"){
	require_once 'include/ListView/RelatedListViewContents.php';
}
?>
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
/* crmv@64542 crmv@104568 */

// TODO: transform all these functions in a class!!

function getModuleList() {
	global $adb,$app_strings,$table_prefix;
	// crmv@39110
	$sql =
	"select distinct name
	from {$table_prefix}_tab
	left join vte_hide_tab ht on {$table_prefix}_tab.tabid = ht.tabid
	where
		(ht.tabid is null or ht.hide_profile = '0')
		and name not in ('Calendar','Events','Fax','Sms','Emails','Messages','ModNotifications', 'ModComments','ChangeLog','PBXManager','Myfiles','MyNotes')
		and isentitytype = '1'";
	// crmv@39110e
	$res = $adb->query($sql);
	$num = $adb->num_rows($res);
	$module_list = array();
	for($i=0;$i<$num;$i++) {
		$mod = $adb->query_result($res,$i,'name');
		$module_list[$mod] = getTranslatedString($mod,$mod);
	}
	asort($module_list);
	return $module_list;
}

// crmv@39110
function alignFieldsForProfile($module, $mobileProfileId) {
	global $adb, $table_prefix;

	$tabid = getTabid($module);

	if (empty($tabid)) return;

	if ($adb->isMysql()) {
		$params = array($mobileProfileId, $tabid);
		$adb->pquery("update {$table_prefix}_profile2field p2f inner join {$table_prefix}_field f on f.fieldid = p2f.fieldid set p2f.sequence = f.sequence where p2f.profileid = ? and p2f.tabid = ? and p2f.sequence = 0", $params);
	} elseif ($adb->isOracle()) {
		// TODO
	} elseif ($adb->isMssql()) {
		// TODO
	}

}

function alignRelatedForProfile($module, $mobileProfileId) {
	global $adb, $table_prefix;

	$tabid = getTabid($module);
	if (empty($tabid)) return;

	// get missing relateds
	$res = $adb->pquery("
		insert into {$table_prefix}_profile2related (profileid, tabid, relationid, visible, sequence, actions)
			select '$mobileProfileId', '$tabid', rl.relation_id, 1-rl.presence, rl.sequence, rl.actions
			from {$table_prefix}_relatedlists rl
			left join {$table_prefix}_profile2related p2r on p2r.profileid = ? and p2r.tabid = rl.tabid and p2r.relationid = rl.relation_id
			where rl.tabid = ? and p2r.relationid is null",
	array($mobileProfileId, $tabid));
}

function alignMobileInfoForProfile($module, $mobileProfileId) {
	global $adb, $table_prefix;

	$tabid = getTabid($module);
	$res = $adb->pquery("select profileid from {$table_prefix}_profile2mobile where tabid = ?", array($tabid));
	if ($res && $adb->num_rows($res) == 0) {
		$adb->pquery("insert into {$table_prefix}_profile2mobile (profileid, tabid) values(?,?)", array($mobileProfileId, $tabid));
	}

}
// crmv@39110e

function createTab($module) {
	$label = $_REQUEST['tablabel'];
	$modInst = Vtecrm_Module::getInstance($module);
	
	if ($modInst && !empty($label)) {
		$tab = new Vtecrm_Panel();
		$tab->label = $label;
		$tab->save($modInst);
	}
}

function editTab($module) {
	$panelid = intval($_REQUEST['editpanelid']);
	$label = $_REQUEST['tablabel'];
	
	$modInst = Vtecrm_Module::getInstance($module);
	
	if ($modInst && !empty($label)) {
		$tab = Vtecrm_Panel::getInstance($panelid);
		if ($tab) {
			$tab->label = $label;
			$tab->save($modInst);
		}
	}
}

function deleteTab() {
	$panelid = intval($_REQUEST['delpanelid']);
	$destPanel = intval($_REQUEST['move_blocks']);
	
	$tab = Vtecrm_Panel::getInstance($panelid);
	if ($tab) {
		$tab->delete($destPanel);
	}
	
}

function reorderTabs($module) {
	global $adb, $table_prefix;
	
	$ids = array_filter(explode(';', $_REQUEST['panelids']));
	
	if (count($ids) > 0) {
		$tabid = getTabid($module);
		$seq = 1;
		foreach ($ids as $panelid) {
			$adb->pquery("UPDATE {$table_prefix}_panels SET sequence = ? WHERE panelid = ? AND tabid = ?", array($seq++, $panelid, $tabid));
		}
	}
}

function moveBlockToTab() {
	$blockid = intval($_REQUEST['blockid']);
	$destPanel = intval($_REQUEST['destpanel']);
	
	$tab = Vtecrm_Panel::getInstance($destPanel);
	if ($tab) {
		$tab->moveHereBlocks($blockid);
	}
}

/**
* Function to get customfield entries
* @param string $module - Module name
* return array  $cflist - customfield entries
*/
function getFieldListEntries($module, $mobileProfileId = null) // crmv@39110
{
	$tabid = getTabid($module);
	global $adb, $smarty,$log;
	global $theme,$table_prefix;
	global $default_charset;
	$theme_path="themes/".$theme."/";
	$image_path="themes/images/";

	// query blocchi
	$dbQuery = "select ".$table_prefix."_blocks.*,".$table_prefix."_tab.presence as tabpresence  from ".$table_prefix."_blocks" .
				" inner join ".$table_prefix."_tab on ".$table_prefix."_tab.tabid = ".$table_prefix."_blocks.tabid" .
				" where ".$table_prefix."_blocks.tabid=?  and ".$table_prefix."_tab.presence = 0 order by sequence";
	$result = $adb->pquery($dbQuery, array($tabid));
	$row = $adb->fetch_array($result);

	if($module == 'Calendar' || $module == 'Events'){
		$focus = CRMEntity::getInstance('Activity');
	}
	else{
		$focus = CRMEntity::getInstance($module); //crmv@33992
	}
	$nonEditableUiTypes = array('4','70');

	$cflist=Array();
	$i=0;
	if($row!='')
	{
		do
		{
			if($row["blocklabel"] == 'LBL_CUSTOM_INFORMATION' )
			{
				$smarty->assign("CUSTOMSECTIONID",$row["blockid"]);
			}
			if($row["blocklabel"] == 'LBL_RELATED_PRODUCTS' )
			{
				$smarty->assign("RELPRODUCTSECTIONID",$row["blockid"]);
			}
			if($row["blocklabel"] == 'LBL_COMMENTS' || $row['blocklabel'] == 'LBL_COMMENT_INFORMATION' )
			{
				$smarty->assign("COMMENTSECTIONID",$row["blockid"]);
			}
			if($row['blocklabel'] == 'LBL_TICKET_RESOLUTION'){
				$smarty->assign("SOLUTIONBLOCKID",$row["blockid"]);
			}
			if($row['blocklabel'] == ''){
				continue;
			}
			$cflist[$i]['tabpresence']= $row['tabpresence'];
			$cflist[$i]['module'] = $module;
			$cflist[$i]['blocklabel']=getTranslatedString($row["blocklabel"], $module);
			$cflist[$i]['blockid']=$row["blockid"];
			$cflist[$i]['panelid']=$row["panelid"];
			$cflist[$i]['display_status']=$row["display_status"];
			$cflist[$i]['visible']=$row["visible"];	//crmv@vtc-6
			$cflist[$i]['tabid']=$tabid;
			$cflist[$i]['blockselect']=$row["blockid"];
			$cflist[$i]['sequence'] = $row["sequence"];
			$cflist[$i]['iscustom'] = $row["iscustom"];

			// crmv@39110
			if (isInventoryModule($module)) {
				$extracond = "and (".$table_prefix."_field.fieldlabel!='Total' and ".$table_prefix."_field.fieldlabel!='Sub Total' and ".$table_prefix."_field.fieldlabel!='Tax')";
			}
			if ($mobileProfileId > 0) {
				$sql_field =
					"select {$table_prefix}_field.*, p2f.sequence as mobile_sequence, p2f.visible as mobile_visible from {$table_prefix}_field
					inner join {$table_prefix}_profile2field p2f on p2f.profileid = ? and p2f.tabid = ? and p2f.fieldid = {$table_prefix}_field.fieldid
					where block=? $extracond and {$table_prefix}_field.displaytype IN (1,2,4)
					ORDER BY p2f.sequence ASC, {$table_prefix}_field.sequence ASC";
				$sql_field_params = array($mobileProfileId, $tabid, $row["blockid"]);
			} else {
				//crmv@101683
				$sql_field = "select {$table_prefix}_field.*, {$table_prefix}_fieldinfo.info from {$table_prefix}_field
					left join {$table_prefix}_fieldinfo on {$table_prefix}_field.fieldid = {$table_prefix}_fieldinfo.fieldid 
					where block=? $extracond and {$table_prefix}_field.displaytype IN (1,2,4) order by sequence";
				//crmv@101683e
				$sql_field_params = array($row["blockid"]);
			}
			// crmv@39110e

			$result_field = $adb->pquery($sql_field,$sql_field_params);

	        $row_field= $adb->fetch_array($result_field);
			if($row_field!='')
			{
				$cf_element=Array();
				$cf_hidden_element=Array();
			 	$count=0;
			 	$hiddencount=0;
			 	do
			  	{

					$fieldid = $row_field['fieldid'];
					$presence = ($mobileProfileId > 0 ? ($row_field['mobile_visible'] + 2) : $row_field['presence']);
					$fieldname = $row_field['fieldname'];
					$customfieldflag = (substr($row_field['fieldname'], 0, 3) == 'cf_');
					$quickcreate = $row_field['quickcreate'];
					$massedit = $row_field['masseditable'];
					$typeofdata = $row_field['typeofdata'];
					$displaytype = $row_field['displaytype'];
					$uitype = $row_field['uitype'];
					$fld_type_name = getCustomFieldTypeName($row_field['uitype']);
					$info = Zend_Json::decode(html_entity_decode($row_field['info'],ENT_QUOTES,$default_charset));	//crmv@113771

					if ($customfieldflag)
						$fieldlabel = $row_field['fieldlabel'];
					else
						$fieldlabel = getTranslatedString($row_field['fieldlabel'], $module);

					$strictlyMandatory = false;
					if(isset($focus->mandatory_fields) && (!empty($focus->mandatory_fields)) && in_array($fieldname, $focus->mandatory_fields)){
						$strictlyMandatory = true;
					} elseif (in_array($uitype, $nonEditableUiTypes) || $displaytype == 2) {
						$strictlyMandatory = true;
					}
					$visibility = getFieldInfo($fieldname,$typeofdata,$quickcreate,$massedit,$presence,$strictlyMandatory,$customfieldflag,$displaytype,$uitype);
					
					//crmv@101683 crmv@113771
					if ($uitype == 50) {
						$fld_info = $info['users'];
						$info = array();
						$user_array = get_user_array(true, "Active");
						foreach($user_array as $id => $name) {
							(!empty($fld_info) && in_array($id,$fld_info)) ? $selected = 'selected' : $selected = '';
							$info[] = array($name,$id,$selected);
						}
					}
					//crmv@101683e crmv@113771e
					
					if ($presence == 0 || $presence == 2) {
						$cf_element[$count]['fieldselect']=$fieldid;
						$cf_element[$count]['blockid']=$row['blockid'];
						$cf_element[$count]['tabid']=$tabid;
						$cf_element[$count]['no']=$count;
						$cf_element[$count]['label']=$fieldlabel;
						$cf_element[$count]['fieldlabel'] = $row_field['fieldlabel'];
						$cf_element[$count]['type']=$fld_type_name;
						$cf_element[$count]['uitype']=$uitype;
						$cf_element[$count]['columnname']=$row_field['columnname'];
						$cf_element[$count]['info'] = $info;	//crmv@101683
						$cf_element[$count] = array_merge($cf_element[$count], $visibility);

						$count++;
					} else {
						$cf_hidden_element[$hiddencount]['fieldselect']=$fieldid;
						$cf_hidden_element[$hiddencount]['blockid']=$row['blockid'];
						$cf_hidden_element[$hiddencount]['tabid']=$tabid;
						$cf_hidden_element[$hiddencount]['no']=$hiddencount;
						$cf_hidden_element[$hiddencount]['label']=$fieldlabel;
						$cf_hidden_element[$hiddencount]['fieldlabel'] = $row_field['fieldlabel'];
						$cf_hidden_element[$hiddencount]['type']=$fld_type_name;
						$cf_hidden_element[$hiddencount]['uitype']=$uitype;
						$cf_hidden_element[$hiddencount]['columnname']=$row_field['columnname'];
						$cf_hidden_element[$hiddencount]['info'] = $info;	//crmv@101683
						$cf_hidden_element[$hiddencount] = array_merge($cf_hidden_element[$hiddencount], $visibility);

						$hiddencount++;
					}
				} while($row_field = $adb->fetch_array($result_field));

				$cflist[$i]['no']=$count;
				$cflist[$i]['hidden_count'] = $hiddencount;
			}
			else
			{
				$cflist[$i]['no']= 0;
			}
			$blocks_to_skip = array("LBL_TICKET_RESOLUTION","LBL_COMMENTS","LBL_COMMENT_INFORMATION");
			$query_fields_not_in_block ='select fieldid,fieldlabel,block from '.$table_prefix.'_field ' .
										'inner join '.$table_prefix.'_blocks on '.$table_prefix.'_field.block='.$table_prefix.'_blocks.blockid ' .
										'where '.$table_prefix.'_field.block != ? and '.$table_prefix.'_blocks.blocklabel not in ('.generateQuestionMarks($blocks_to_skip).') ' .
										'AND '.$table_prefix.'_field.tabid = ? and '.$table_prefix.'_field.displaytype IN (1,2,4) order by '.$table_prefix.'_field.sequence';

			$params =array($row['blockid'],$blocks_to_skip,$tabid);
			$fields = $adb->pquery($query_fields_not_in_block,$params);
			$row_field= $adb->fetch_array($fields);

			if($row_field != ''){
				$movefields = array();
				$movefieldcount = 0;
				do{
					$movefields[$movefieldcount]['fieldid'] =  $row_field['fieldid'];
					$movefields[$movefieldcount]['fieldlabel'] =  getTranslatedString($row_field['fieldlabel'], $module);	//crmv@24862
					$movefieldcount++;
				}while($row_field = $adb->fetch_array($fields));
				$cflist[$i]['movefieldcount'] = $movefieldcount;
			}
			else{
				$cflist[$i]['movefieldcount'] = 0 ;
			}

			$cflist[$i]['field']= $cf_element;
			$cflist[$i]['hiddenfield']= $cf_hidden_element;
			$cflist[$i]['movefield'] = $movefields;

			$cflist[$i]['hascustomtable'] = $focus->customFieldTable;
			unset($cf_element);
			unset($cf_hidden_element);
			unset($movefields);
			$i++;
		} while($row = $adb->fetch_array($result));
	}
	return $cflist;
}

/**
* Function to Lead customfield Mapping entries
* @param integer  $cfid   - Lead customfield id
* return array    $label  - customfield mapping
*/
function getListLeadMapping($cfid)
{
	global $adb,$table_prefix;
	$sql="select * from ".$table_prefix."_convertleadmapping where cfmid =".$cfid;
	$result = $adb->query($sql);
	$noofrows = $adb->num_rows($result);
	for($i =0;$i <$noofrows;$i++)
	{
		$leadid = $adb->query_result($result,$i,'leadfid');
		$accountid = $adb->query_result($result,$i,'accountfid');
		$contactid = $adb->query_result($result,$i,'contactfid');
		$potentialid = $adb->query_result($result,$i,'potentialfid');
		$cfmid = $adb->query_result($result,$i,'cfmid');

		$sql2="select fieldlabel from ".$table_prefix."_field where fieldid = ? and ".$table_prefix."_field.presence in (0,2)";
		$result2 = $adb->pquery($sql2, array($accountid));
		$accountfield = $adb->query_result($result2,0,'fieldlabel');
		$label['accountlabel'] = $accountfield;

		$sql3="select fieldlabel from ".$table_prefix."_field where fieldid = ? and ".$table_prefix."_field.presence in (0,2)";
		$result3 = $adb->pquery($sql3, array($contactid));
		$contactfield = $adb->query_result($result3,0,'fieldlabel');
		$label['contactlabel'] = $contactfield;
		$sql4="select fieldlabel from ".$table_prefix."_field where fieldid = ? and ".$table_prefix."_field.presence in (0,2)";
		$result4 = $adb->pquery($sql4, array($potentialid));
		$potentialfield = $adb->query_result($result4,0,'fieldlabel');
		$label['potentiallabel'] = $potentialfield;
	}
	return $label;
}

/* function to get the modules supports Custom Fields
*/
function getCustomFieldSupportedModules()
{
	global $adb,$table_prefix;
	$sql="select distinct ".$table_prefix."_field.tabid,name from ".$table_prefix."_field inner join ".$table_prefix."_tab on ".$table_prefix."_field.tabid=".$table_prefix."_tab.tabid where ".$table_prefix."_field.tabid not in(9,10,16,15,8,29)";
	$result = $adb->query($sql);
	while($moduleinfo=$adb->fetch_array($result))
	{
		$modulelist[$moduleinfo['name']] = $moduleinfo['name'];
	}
	return $modulelist;
}


function getModuleBlocks($module){
	global $adb,$table_prefix;
	$tabid = getTabid($module);
	$blockquery = "select blocklabel,blockid,panelid from ".$table_prefix."_blocks where tabid = ?";
	$blockres = $adb->pquery($blockquery,array($tabid));
	while($blockinfo = $adb->fetch_array($blockres)){
		$blocklist[$blockinfo['blockid']] = getTranslatedString($blockinfo['blocklabel'],$module);
	}
	return $blocklist;
}

/**
 *
 */
function changeFieldOrder($forMobile = false, $mobileProfileId = null){ // crmv@39110
	global $adb,$log,$smarty,$table_prefix, $metaLogs; // crmv@49398
	if(!empty($_REQUEST['what_to_do'])){
		if($_REQUEST['what_to_do']=='block_down'){
			$sql="select * from ".$table_prefix."_blocks where blockid=?";
			$result = $adb->pquery($sql, array($_REQUEST['blockid']));
			$row= $adb->fetch_array($result);
			$current_sequence=$row[sequence];

			$sql_next="select * from ".$table_prefix."_blocks where sequence > ? and tabid=?";
			$result_next = $adb->limitpQuery($sql_next,0,1,array($current_sequence,$_REQUEST[tabid]));
			$row_next= $adb->fetch_array($result_next);
			$next_sequence=$row_next[sequence];
			$next_id=$row_next[blockid];


			$sql_up_current="update ".$table_prefix."_blocks  set sequence=? where blockid=?";
			$result_up_current = $adb->pquery($sql_up_current, array($next_sequence,$_REQUEST['blockid']));


			$sql_up_next="update ".$table_prefix."_blocks  set sequence=? where blockid=?";
			$result_up_next = $adb->pquery($sql_up_next, array($current_sequence,$next_id));
			
			if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_EDITBLOCK, $_REQUEST['blockid']); // crmv@49398
		}

		if($_REQUEST['what_to_do']=='block_up'){
			$sql="select * from ".$table_prefix."_blocks where blockid=?";
			$result = $adb->pquery($sql, array($_REQUEST['blockid']));
			$row= $adb->fetch_array($result);
			$current_sequence=$row[sequence];
			$sql_previous="select * from ".$table_prefix."_blocks where sequence < ? and tabid=?  order by sequence desc";
			$result_previous = $adb->limitpQuery($sql_previous,0,1, array($current_sequence,$_REQUEST[tabid]));
			$row_previous= $adb->fetch_array($result_previous);
			$previous_sequence=$row_previous[sequence];
			$previous_id=$row_previous[blockid];


			$sql_up_current="update ".$table_prefix."_blocks  set sequence=? where blockid=?";
			$result_up_current = $adb->pquery($sql_up_current, array($previous_sequence,$_REQUEST['blockid']));


			$sql_up_previous="update ".$table_prefix."_blocks  set sequence=? where blockid=?";
			$result_up_previous = $adb->pquery($sql_up_previous, array($current_sequence,$previous_id));

			if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_EDITBLOCK, $_REQUEST['blockid']); // crmv@49398
		}

		// crmv@39110
		if($_REQUEST['what_to_do']=='down' || $_REQUEST['what_to_do']=='Right'){
			if ($forMobile) {
				$sql="select p2f.sequence from ".$table_prefix."_field f
				inner join {$table_prefix}_profile2field p2f on p2f.profileid = ? and p2f.tabid = f.tabid and p2f.fieldid = f.fieldid
				where f.fieldid=? and f.presence in (0,2)";
				$params = array($mobileProfileId, $_REQUEST['fieldid']);
			} else {
				$sql="select sequence from ".$table_prefix."_field where fieldid=? and ".$table_prefix."_field.presence in (0,2)";
				$params = array($_REQUEST['fieldid']);
			}
			$result = $adb->pquery($sql, $params);
			$row= $adb->fetch_array($result);
			$current_sequence=$row['sequence'];
			if($_REQUEST['what_to_do']=='down'){
				if ($forMobile) {
					$sql_next=
					"select p2f.*
					from {$table_prefix}_field f
					inner join {$table_prefix}_profile2field p2f on p2f.profileid = ? and p2f.tabid = f.tabid and p2f.fieldid = f.fieldid
					where p2f.sequence > ? and f.block=? and f.presence in (0,2)
					order by p2f.sequence asc";
					$sql_next_params = array($mobileProfileId, $current_sequence,$_REQUEST['blockid']);
					$start = 0;
				} else {
					$sql_next="select * from ".$table_prefix."_field where sequence > ? and block = ? and ".$table_prefix."_field.presence in (0,2) order by sequence";
					$sql_next_params = array($current_sequence, $_REQUEST['blockid']);
					$start = 1;
				}
				$cnt = 1;
			}else{
				$sql_next="select * from ".$table_prefix."_field where sequence > ? and block = ? and ".$table_prefix."_field.presence in (0,2) order by sequence";
				$start = 0;
				$cnt = 1;
				$sql_next_params = array($current_sequence, $_REQUEST['blockid']);
			}

			$result_next = $adb->limitpQuery($sql_next,$start,$cnt,$sql_next_params);
			$row_next= $adb->fetch_array($result_next);
			$next_sequence=$row_next['sequence'];
			$next_id=$row_next['fieldid'];

			if ($forMobile) {
				// update for all profiles!!
				$sql_up_current= "update ".$table_prefix."_profile2field  set sequence=? where fieldid=?";
				$result_up_current = $adb->pquery($sql_up_current, array($next_sequence,$_REQUEST['fieldid']));
				$sql_up_next= "update ".$table_prefix."_profile2field  set sequence=? where fieldid=?";
				$result_up_next = $adb->pquery($sql_up_next, array($current_sequence,$next_id));
			} else {
				$sql_up_current="update ".$table_prefix."_field  set sequence=? where fieldid=?";
				$result_up_current = $adb->pquery($sql_up_current, array($next_sequence,$_REQUEST['fieldid']));

				$sql_up_next="update ".$table_prefix."_field  set sequence=? where fieldid=?";
				$result_up_next = $adb->pquery($sql_up_next, array($current_sequence,$next_id));
			}
			if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_EDITFIELD, $_REQUEST['fieldid']); // crmv@49398
			$smarty->assign("COLORID",$_REQUEST['fieldid']);
		}

		if($_REQUEST['what_to_do']=='up' || $_REQUEST['what_to_do']=='Left'){
			if ($forMobile) {
				$sql="select p2f.sequence from ".$table_prefix."_field f
				inner join {$table_prefix}_profile2field p2f on p2f.profileid = ? and p2f.tabid = f.tabid and p2f.fieldid = f.fieldid
				where f.fieldid=? and f.presence in (0,2)";
				$params = array($mobileProfileId, $_REQUEST['fieldid']);
			} else {
				$sql="select sequence from ".$table_prefix."_field where fieldid=? and ".$table_prefix."_field.presence in (0,2)";
				$params = array($_REQUEST['fieldid']);
			}
			$result = $adb->pquery($sql, $params);
			$row= $adb->fetch_array($result);
			$current_sequence=$row['sequence'];

			if($_REQUEST['what_to_do']=='up'){
				if ($forMobile) {
					$sql_previous=
					"select p2f.*
						from {$table_prefix}_field f
						inner join {$table_prefix}_profile2field p2f on p2f.profileid = ? and p2f.tabid = f.tabid and p2f.fieldid = f.fieldid
						where p2f.sequence < ? and f.block=? and f.presence in (0,2)
						order by p2f.sequence desc";
					$sql_prev_params = array($mobileProfileId, $current_sequence,$_REQUEST['blockid']);
					$start = 0;
				} else {
					$sql_previous="select * from ".$table_prefix."_field where sequence < ? and block=? and ".$table_prefix."_field.presence in (0,2) order by sequence desc";
					$sql_prev_params = array($current_sequence,$_REQUEST['blockid']);
					$start = 1;
				}
				$cnt = 1;

			}else{
				$sql_previous="select * from ".$table_prefix."_field where sequence < ? and block=? and ".$table_prefix."_field.presence in (0,2) order by sequence desc";
				$start = 0;
				$cnt = 1;
				$sql_prev_params = array($current_sequence,$_REQUEST['blockid']);
			}
			$result_previous = $adb->limitpQuery($sql_previous,$start,$cnt,$sql_prev_params);
			$row_previous= $adb->fetch_array($result_previous);
			$previous_sequence=$row_previous['sequence'];
			$previous_id=$row_previous['fieldid'];


			if ($forMobile) {
				// update for all profiles!!
				$sql_up_current="update {$table_prefix}_profile2field set sequence = ? where fieldid=?";
				$result_up_current = $adb->pquery($sql_up_current, array($previous_sequence,$_REQUEST['fieldid']));
				$sql_up_previous="update {$table_prefix}_profile2field set sequence = ? where fieldid=?";
				$result_up_previous = $adb->pquery($sql_up_previous, array($current_sequence,$previous_id));
			} else {
				$sql_up_current="update ".$table_prefix."_field  set sequence=? where fieldid=?";
				$result_up_current = $adb->pquery($sql_up_current, array($previous_sequence,$_REQUEST['fieldid']));

				$sql_up_previous="update ".$table_prefix."_field  set sequence=? where fieldid=?";
				$result_up_previous = $adb->pquery($sql_up_previous, array($current_sequence,$previous_id));
			}
			if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_EDITFIELD, $_REQUEST['fieldid']); // crmv@49398
			$smarty->assign("COLORID",$_REQUEST['fieldid']);
		}
		// crmv@39110e

		//crmv@vtc-6
		if($_REQUEST['what_to_do']=='show'){
			$sql_up_display="update ".$table_prefix."_blocks set display_status='1',visible='0' where blockid=?";
			$result_up_display = $adb->pquery($sql_up_display, array($_REQUEST['blockid']));
			if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_EDITBLOCK, $_REQUEST['blockid']); // crmv@49398
		}

		if($_REQUEST['what_to_do']=='hide'){
			$sql_up_display="update ".$table_prefix."_blocks set display_status='0',visible='0' where blockid=?";
			$result_up_display = $adb->pquery($sql_up_display, array($_REQUEST['blockid']));
			if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_EDITBLOCK, $_REQUEST['blockid']); // crmv@49398
		}

		if($_REQUEST['what_to_do']=='disable'){
			$sql_up_display="update ".$table_prefix."_blocks set visible='1' where blockid=?";
			$result_up_display = $adb->pquery($sql_up_display, array($_REQUEST['blockid']));
			if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_EDITBLOCK, $_REQUEST['blockid']); // crmv@49398
		}
		//crmv@vtc-6e
	}
}
/**
 *
 */
function getFieldInfo($fieldname,$typeofdata,$quickcreate,$massedit,$presence,$strictlyMandatory,$customfieldflag,$displaytype,$uitype){
	global $log;

	$fieldtype =  explode("~",$typeofdata);

	if($strictlyMandatory){//fields without which the CRM Record will be inconsistent
		$mandatory = '0';
	}elseif($fieldtype[1] == "M"){//fields which are made mandatory
		$mandatory = '2';
	}else{
		$mandatory = '1'; //fields not mandatory
	}
	if ($uitype == 4 || $displaytype == 2) {
		$mandatory = '3';
	}


	$visibility = array();
	$visibility['fieldname']	= $fieldname;
	$visibility['mandatory']	= $mandatory;
	$visibility['quickcreate']	= $quickcreate;
	$visibility['presence']		= $presence;
	$visibility['massedit']		= $massedit;
	$visibility['displaytype']	= $displaytype;
	$visibility['customfieldflag'] = $customfieldflag;
	$visibility['fieldtype'] = $fieldtype[1];
	return $visibility;
 }

function updateFieldProperties($forMobile = false, $mobileProfileId = null) {

	global $adb,$smarty,$log,$table_prefix, $metaLogs; // crmv@49398
	$fieldid = intval($_REQUEST['fieldid']);
	$req_sql = "select * from ".$table_prefix."_field where fieldid = ? and fieldname not in('salutationtype') and {$table_prefix}_field.presence in (0,2)";
	$req_result = $adb->pquery($req_sql, array($fieldid));

	$typeofdata = $adb->query_result($req_result,0,'typeofdata');
	$tabid = $adb->query_result($req_result,0,'tabid');
	$fieldname = $adb->query_result($req_result,0,'fieldname');
	$uitype = $adb->query_result($req_result,0,'uitype');
	$oldfieldlabel = $adb->query_result($req_result,0,'fieldlabel');
	$tablename = $adb->query_result($req_result,0,'tablename');
	$columnname = $adb->query_result($req_result,0,'columnname');
	$oldquickcreate = $adb->query_result($req_result,0,'quickcreate');
	$oldmassedit = $adb->query_result($req_result,0,'masseditable');
	$oldpresence = $adb->query_result($req_result,0,'presence');

	if(isset($_REQUEST['fld_module'])  && $_REQUEST['fld_module']!= ''){
		$fld_module = $_REQUEST['fld_module'];
	}else{
		$fld_module = getTabModuleName($tabid);
	}

	$focus = CRMEntity::getInstance($fld_module);

	$fieldtype =  explode("~",$typeofdata);
	$mandatory_checked= $_REQUEST['ismandatory'];
	$quickcreate_checked = $_REQUEST['quickcreate'];
	$presence_check = $_REQUEST['isPresent'];
	$massedit_check = $_REQUEST['massedit'];

	if ($forMobile) {
		// update for all profiles
		$visible = ($presence_check == 'true' || $presence_check == 'true' ? 0 : 1);
		$adb->pquery("update {$table_prefix}_profile2field p2f set visible = ? where tabid = ? and profileid = ? and fieldid = ?", array($visible, $tabid, $mobileProfileId, $fieldid)); // crmv@38798
		if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_EDITFIELD, $fieldid); // crmv@49398
		return;
	}

	if(isset($focus->mandatory_fields) && (!empty($focus->mandatory_fields)) && in_array($fieldname, $focus->mandatory_fields)){
		$fieldtype[1] = 'M';
	} elseif($mandatory_checked == 'true' || $mandatory_checked == ''){
		$fieldtype[1] = 'M';
	} else{
		$fieldtype[1] = 'O';
	}
	$datatype = implode('~', $fieldtype);
	$maxseq = '';
	if($oldquickcreate != 3){
		if(($quickcreate_checked == 'true' || $quickcreate_checked == '' )){
			$qcdata = 2;
			$quickcreateseq_Query = 'select max(quickcreatesequence) as maxseq from '.$table_prefix.'_field where tabid = ?';
			$res = $adb->pquery($quickcreateseq_Query,array($tabid));
			$maxseq = $adb->query_result($res,0,'maxseq');

		}else{
			$qcdata = 1;
		}
	}
	if($oldpresence != 3){
		if($presence_check == 'true' || $presence_check == ''){
			$presence = 2;
		}else{
			$presence = 1;
		}
	}else{
		$presence =1;
	}

	if($oldmassedit != 3){
		if(($massedit_check == 'true' || $massedit_check == '')){
			$massedit = 1;
		}else{
			$massedit = 2;
		}
	}else{
		$massedit=1;
	}

	if(isset($focus->mandatory_fields) && (!empty($focus->mandatory_fields))){
		$fieldname_list = implode(',',$focus->mandatory_fields);
	}else{
		$fieldname_list = '';
	}

	$mandatory_query = "update ".$table_prefix."_field set typeofdata=? where fieldid=? and fieldname not in (?) AND displaytype != 2";
	$mandatory_params = array($datatype,$fieldid,$fieldname_list);
	$adb->pquery($mandatory_query, $mandatory_params);

	if(!empty($qcdata)){
		$quickcreate_query = "update ".$table_prefix."_field set quickcreate = ? ,quickcreatesequence = ? where fieldid = ? and quickcreate not in (0,3) AND displaytype != 2";
		$quickcreate_params = array($qcdata,$maxseq+1,$fieldid);
		$adb->pquery($quickcreate_query,$quickcreate_params);
	}

	$presence_query = "update ".$table_prefix."_field set presence = ? where fieldid = ? and presence not in (0,3) and quickcreate != 0";
	$quickcreate_params = array($presence,$fieldid);
	$adb->pquery($presence_query,$quickcreate_params);

	$massedit_query = "update ".$table_prefix."_field set masseditable = ? where fieldid = ? and masseditable not in (0,3) AND displaytype != 2";
	$massedit_params = array($massedit,$fieldid);
	$adb->pquery($massedit_query,$massedit_params);
	
	//crmv@101683 crmv@113771
	if (isset($_REQUEST['info'])) {
		// TODO fare il merge con eventuali proprietà preesistenti
		$info = $_REQUEST['info'];
		$adb->pquery("update {$table_prefix}_fieldinfo set info = ? where fieldid = ?", array($info,$fieldid));
	}
	//crmv@101683e crmv@113771e

	if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_EDITFIELD, $fieldid); // crmv@49398
}



function deleteCustomField(){
	global $adb,$table_prefix, $metaLogs; // crmv@49398

	require_once('modules/Reports/Reports.php');

	$fld_module = $_REQUEST["fld_module"];
	$id = $_REQUEST["fld_id"];
	$colName = $_REQUEST["colName"];
	$uitype = $_REQUEST["uitype"];

	$fieldquery = 'select * from '.$table_prefix.'_field where fieldid = ?';
	$res = $adb->pquery($fieldquery,array($id));

	$typeofdata = $adb->query_result($res,0,'typeofdata');
	$fieldname = $adb->query_result($res,0,'fieldname');
	$oldfieldlabel = $adb->query_result($res,0,'fieldlabel');
	$tablename = $adb->query_result($res,0,'tablename');
	$columnname = $adb->query_result($res,0,'columnname');
	$fieldtype =  explode("~",$typeofdata);

	//Deleting the CustomField from the Custom Field Table
	$query='delete from '.$table_prefix.'_field where fieldid = ? and '.$table_prefix.'_field.presence in (0,2)';
	$adb->pquery($query, array($id));

	//Deleting from vtiger_profile2field table
	$query='delete from '.$table_prefix.'_profile2field where fieldid=?';
	$adb->pquery($query, array($id));

	//Deleting from vtiger_def_org_field table
	$query='delete from '.$table_prefix.'_def_org_field where fieldid=?';
	$adb->pquery($query, array($id));

	if($fld_module == 'Calendar' || $fld_module == 'Events'){
			$focus = CRMEntity::getInstance('Activity');
		}else{
			require_once("modules/$fld_module/$fld_module.php");
			$focus = new $fld_module();
	}

	$deletecolumnname =$tablename .":". $columnname .":".$fieldname.":".$fld_module. "_" .str_replace(" ","_",$oldfieldlabel).":".$fieldtype[0];
	$column_cvstdfilter = 	$tablename .":". $columnname .":".$fieldname.":".$fld_module. "_" .str_replace(" ","_",$oldfieldlabel);

	//crmv@59051
	if($adb->isMssql()){
		$query_df ="select name 
					from sys.default_constraints 
					where parent_object_id = object_id(?)
					AND type = 'D'
					AND parent_column_id = (
					  select column_id 
					  from sys.columns 
					  where object_id = object_id(?)
					  and name = ?
					)";
		$res_df = $adb->pquery($query_df,array($adb->sql_escape_string($focus->customFieldTable[0]),$adb->sql_escape_string($focus->customFieldTable[0]),$adb->sql_escape_string($colName)));
		if($res_df && $adb->num_rows($res_df) > 0){
			$default_constraint = $adb->query_result($res_df,0,'name');
			
			$dbquery_df = 'alter table '. $adb->sql_escape_string($focus->customFieldTable[0]).' drop constraint '.$default_constraint;
			$adb->pquery($dbquery_df, array());
		}
	}
	//crmv@59051e
	
	$dbquery = 'alter table '. $adb->sql_escape_string($focus->customFieldTable[0]).' drop column '. $adb->sql_escape_string($colName);
	$adb->pquery($dbquery, array());

	//To remove customfield entry from vtiger_field table
	$dbquery = 'delete from '.$table_prefix.'_field where columnname= ? and fieldid=? and '.$table_prefix.'_field.presence in (0,2)';
	$adb->pquery($dbquery, array($colName, $id));
	//we have to remove the entries in customview and report related tables which have this field ($colName)
	$adb->pquery("delete from ".$table_prefix."_cvcolumnlist where columnname = ? ", array($deletecolumnname));
	$adb->pquery("delete from ".$table_prefix."_cvstdfilter where columnname = ?", array($column_cvstdfilter));
	$adb->pquery("delete from ".$table_prefix."_cvadvfilter where columnname = ?", array($deletecolumnname));
	$adb->pquery("delete from ".$table_prefix."_fieldinfo where fieldid = ?", array($id));	//crmv@101683

	// crmv@101691
	$reports = Reports::getInstance();
	$reports->deleteFieldFromAll($id);
	// crmv@101691e

	//Deleting from convert lead mapping vtiger_table- Jaguar
	if($fld_module=="Leads")
	{
		$deletequery = 'delete from '.$table_prefix.'_convertleadmapping where leadfid=?';
		$adb->pquery($deletequery, array($id));
	}elseif($fld_module=="Accounts" || $fld_module=="Contacts" || $fld_module=="Potentials")
	{
		$map_del_id = array("Accounts"=>"accountfid","Contacts"=>"contactfid","Potentials"=>"potentialfid");
		$map_del_q = "update ".$table_prefix."_convertleadmapping set ".$map_del_id[$fld_module]."=0 where ".$map_del_id[$fld_module]."=?";
		$adb->pquery($map_del_q, array($id));
	}

	//HANDLE HERE - we have to remove the table for other picklist type values which are text area and multiselect combo box
	if($uitype == 15)
	{
		$deltablequery = 'drop table '.$table_prefix.'_'.$adb->sql_escape_string($colName);
		$adb->pquery($deltablequery, array());
	}

	if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_DELFIELD, $id, array('module'=>$fld_module)); // crmv@49398
}


function addblock(){

	global $mod_strings,$log,$adb,$table_prefix, $metaLogs; // crmv@49398
	$fldmodule=$_REQUEST['fld_module'];
	$mode=$_REQUEST['mode'];
	$panelid = intval($_REQUEST['panelid']);

	$newblocklabel = trim($_REQUEST['blocklabel']);
	$after_block = $_REQUEST['after_blockid'];

	$tabid = getTabid($fldmodule);
	$flag = 0;
	$dup_check_query = $adb->pquery("SELECT blocklabel from ".$table_prefix."_blocks WHERE tabid = ?",array($tabid));
	$norows = $adb->num_rows($dup_check_query);
	for($i=0;$i<$norows;$i++){
		$blklbl = $adb->query_result($dup_check_query,$i,'blocklabel');
		$blklbltran = getTranslatedString($blklbl,$fldmodule);
		if(strtolower($blklbltran) == strtolower($newblocklabel)){
			$flag = 1;
			$duplicate='yes';
			return $duplicate;
			}
	}
		$length = strlen($newblocklabel);
		if($length > 50){
			$flag = 1;
			$duplicate='LENGTH_ERROR';
			return $duplicate;
		}

	if($flag!=1){
		$sql_seq="select sequence from ".$table_prefix."_blocks where blockid=?";
		$res_seq= $adb->pquery($sql_seq, array($after_block));
	    $row_seq=$adb->fetch_array($res_seq);
		$block_sequence=$row_seq['sequence'];
		$newblock_sequence=$block_sequence+1;
		//$fieldselect=$_REQUEST[fieldselect];

		$sql_up="update ".$table_prefix."_blocks set sequence=sequence+1 where tabid=? and sequence > ?";
		$adb->pquery($sql_up, array($tabid,$block_sequence));

		$sql='select max(blockid) as max_id from '.$table_prefix.'_blocks';
		$res=$adb->query($sql);
		$row=$adb->fetch_array($res);
		$max_blockid=$row['max_id']+1;
		$iscustom = 1;
		$sql="INSERT INTO ".$table_prefix."_blocks (tabid, blockid, show_title, sequence, blocklabel,iscustom, panelid) values (?,?,0,?,?,?,?)";
		$params = array($tabid,$max_blockid,$newblock_sequence,$newblocklabel,$iscustom,$panelid);
		$adb->pquery($sql,$params);
		//crmv@17457
		$sql="update ".$table_prefix."_blocks_seq set id = ?";
		$adb->pquery($sql, array($max_blockid));
		//crmv@17457e

		if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_ADDBLOCK, $max_blockid); // crmv@49398
	}

}

// crmv@49398
function deleteBlock(){
	global $adb, $table_prefix, $metaLogs;
	$blockid = intval($_REQUEST['blockid']);

	// get module name
	$res = $adb->pquery("select name from {$table_prefix}_tab t inner join {$table_prefix}_blocks b on b.tabid = t.tabid where b.blockid = ?", array($blockid));
	if ($res && $adb->num_rows($res) > 0) {
		$module = $adb->query_result_no_html($res, 0, 'name');
	}

	$deleteblock = 'delete from '.$table_prefix.'_blocks where blockid = ? and iscustom = 1';
	$res = $adb->pquery($deleteblock,array($blockid));
	if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_DELBLOCK, $blockid, array('module'=>$module));
}
// crmv@49398e

function addCustomField(){
	global $current_user,$log,$adb,$table_prefix, $metaLogs; // crmv@49398

	$fldmodule=$_REQUEST['fld_module'];
	$fldlabel=trim($_REQUEST['fldLabel']);
	$fldType= $_REQUEST['fieldType'];
	$parenttab=$_REQUEST['parenttab'];
	$mode=$_REQUEST['mode'];
	$blockid = $_REQUEST['blockid'];
	
	$tabid = getTabid($fldmodule);
	if ($fldmodule == 'Calendar' && isset($_REQUEST['activity_type'])) {
		$activitytype = $_REQUEST['activity_type'];
		if ($activitytype == 'E') $tabid = '16';
		if ($activitytype == 'T') $tabid = '9';
	}
	if(get_magic_quotes_gpc() == 1){
		$fldlabel = stripslashes($fldlabel);
	}
	
	$dup_check_tab_id = $tabid;
	if ($fldmodule == 'Calendar')
		$dup_check_tab_id = array('9', '16');
	$checkquery="select * from ".$table_prefix."_field where tabid in (". generateQuestionMarks($dup_check_tab_id) .") and fieldlabel=?";
	$params =  array($dup_check_tab_id, $fldlabel);
	$checkresult=$adb->pquery($checkquery,$params);
	
	if($adb->num_rows($checkresult) > 0 )
	{
		$duplicate = 'yes';
		return $duplicate ;
	}
	else{
		$max_fieldid = $adb->getUniqueID($table_prefix."_field");
		$columnName = 'cf_'.$max_fieldid;
		$custfld_fieldid = $max_fieldid;
		//Assigning the vtiger_table Name
		if($fldmodule != '') {
			checkFileAccess("modules/$fldmodule/$fldmodule.php");
			include_once("modules/$fldmodule/$fldmodule.php");
			$focus = new $fldmodule();
			if (isset($focus->customFieldTable)) {
				$tableName=$focus->customFieldTable[0];
			} else {
				$tableName= $table_prefix.'_'.strtolower($fldmodule).'cf';
			}
		}
		//Assigning the uitype
		$fldlength=$_REQUEST['fldLength'];
		$uitype='';
		$fldPickList='';
		if(isset($_REQUEST['fldDecimal']) && $_REQUEST['fldDecimal'] != ''){
			$decimal=$_REQUEST['fldDecimal'];
		}else{
			$decimal=0;
		}
		$type='';
		$uichekdata='';
		if($fldType == 'Text'){
			$uichekdata='V~O~LE~'.$fldlength;
			$uitype = 1;
			$type = "C(".$fldlength.") default ()"; // adodb type
		}elseif($fldType == 'Number'){
			$uitype = 7;
			//this may sound ridiculous passing decimal but that is the way adodb wants
			$dbfldlength = $fldlength + $decimal + 1;
	 		$type="N(".$dbfldlength.".".$decimal.")";	// adodb type
			$uichekdata='N~O~'.$fldlength .','.$decimal;
		}elseif($fldType == 'Percent'){
			$uitype = 9;
			$type="N(5.2)"; //adodb type
			$uichekdata='N~O~2~2';
		}elseif($fldType == 'Currency'){
			$uitype = 71;
			$dbfldlength = $fldlength + $decimal + 1;
			$type="N(".$dbfldlength.".".$decimal.")"; //adodb type
			$uichekdata='N~O~'.$fldlength .','.$decimal;
		}elseif($fldType == 'Date'){
			$uichekdata='D~O';
			$uitype = 5;
			$type = "D"; // adodb type
		}elseif($fldType == 'Email'){
			$uitype = 13;
			$type = "C(50) default () "; //adodb type
			$uichekdata='E~O';
		}elseif($fldType == 'Phone'){
			$uitype = 11;
			$type = "C(30) default () "; //adodb type
			$uichekdata='V~O';
		}elseif($fldType == 'Picklist'){
			$uitype = 15;
			$type = "C(255) default () "; //adodb type
			$uichekdata='V~O';
		}elseif($fldType == 'Picklistmulti'){
			$uitype = 1015;
			$type = "C(255) default () "; //adodb type
			$uichekdata='V~O';
		}elseif($fldType == 'URL'){
			$uitype = 207;	// crmv@80653
			$type = "C(255) default () "; //adodb type
			$uichekdata='V~O';
		}elseif($fldType == 'Checkbox'){
	         $uitype = 56;
	         $type = "C(3) default 0"; //adodb type
	         $uichekdata='C~O';
	    }elseif($fldType == 'TextArea'){
	         $uitype = 21;
	         $type = "XL"; //adodb type
	         $uichekdata='V~O';
		}elseif($fldType == 'MultiSelectCombo'){
			 $uitype = 33;
			 $type = "X"; //adodb type
			 $uichekdata='V~O';
		}elseif($fldType == 'Skype'){
			$uitype = 85;
			$type = "C(255) default () "; //adodb type
			$uichekdata='V~O';
		//crmv@96450	crmv@101683
		}elseif($fldType == 'User' || $fldType == 'AllUser' || $fldType == 'CustomUser' || $fldType == 'Group'){
			$map = array('User'=>52,'AllUser'=>51,'CustomUser'=>50,'Group'=>54);
			$uitype = $map[$fldType];
			$type = "I(19) default 0 "; //adodb type
			$uichekdata='V~O';
		//crmv@96450e	crmv@101683e
		//crmv@113771
		}elseif($fldType == 'Button'){
			$uitype = 213;
			$type = "C(1) default () "; //adodb type
			$uichekdata='V~O';
		//crmv@113771e
		//crmv@115268
		}elseif($fldType == 'AttachDocuments'){
			$uitype = 29;
			$type = "C(1) default () "; //adodb type
			$uichekdata='V~O';
		}
		//crmv@115268e
		if(is_numeric($blockid)){
			if($_REQUEST['fieldid'] == ''){
				$max_fieldsequence = "select max(sequence) as maxsequence from ".$table_prefix."_field where block = ? ";
				$res = $adb->pquery($max_fieldsequence,array($blockid));
				$max_seq = $adb->query_result($res,0,'maxsequence');
				$quickcreate = 1; // crmv@92378
				$query = "insert into ".$table_prefix."_field (tabid,fieldid,columnname,tablename,generatedtype,uitype,fieldname,fieldlabel,readonly,presence,selected,maximumlength,sequence,block,displaytype,typeofdata,quickcreate,quickcreatesequence,info_type) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
				$qparams = array($tabid,$custfld_fieldid,$columnName,$tableName,2,$uitype,$columnName,$fldlabel,0,2,0,100,$max_seq+1,$blockid,1,$uichekdata,$quickcreate,0,'BAS');
				$adb->pquery($query, $qparams);
				$adb->alterTable($tableName, $columnName." ".$type, "Add_Column");
				//Inserting values into ".$table_prefix."_profile2field vtiger_tables
				$sql1 = "select * from ".$table_prefix."_profile";
				$sql1_result = $adb->pquery($sql1, array());
				$sql1_num = $adb->num_rows($sql1_result);
				for($i=0; $i<$sql1_num; $i++){
					$profileid = $adb->query_result($sql1_result,$i,"profileid");
					$sql2 = "insert into ".$table_prefix."_profile2field (profileid, tabid, fieldid, visible, readonly) values(?,?,?,?,?)";
					$adb->pquery($sql2, array($profileid, $tabid, $custfld_fieldid, 0, 1));
				}

				//Inserting values into def_org vtiger_tables
				$sql_def = "insert into ".$table_prefix."_def_org_field values(?,?,?,?)";
				$adb->pquery($sql_def, array($tabid, $custfld_fieldid, 0, 1));

				if($fldType == 'Picklist' || $fldType == 'MultiSelectCombo'){
					$columnName = $adb->sql_escape_string($columnName);
					// Creating the PickList Table and Populating Values
					if($_REQUEST['fieldid'] == ''){
						Vtiger_Utils::CreateTable(
						$table_prefix."_$columnName",
						$columnName."id I(19) NOTNULL PRIMARY ,
						$columnName C(200) NOTNULL,
						presence I(1) NOTNULL DEFAULT 1,
						picklist_valueid I(19) NOT NULL DEFAULT 0",
						true);
					}
					//Adding a  new picklist value in the picklist table
					if($mode != 'edit'){
						$picklistid = $adb->getUniqueID($table_prefix."_picklist");
						$sql="insert into ".$table_prefix."_picklist values(?,?)";
						$adb->pquery($sql, array($picklistid,$columnName));
					}
					$roleid=$current_user->roleid;
					$qry="select picklistid from ".$table_prefix."_picklist where  name=?";
					$picklistid = $adb->query_result($adb->pquery($qry, array($columnName)), 0,'picklistid');
					$pickArray = Array();
					$fldPickList =  $_REQUEST['fldPickList'];
					$pickArray = explode("\n",$fldPickList);
					$count = count($pickArray);
					for($i = 0; $i < $count; $i++)
					{
						$pickArray[$i] = trim(from_html($pickArray[$i]));
						if($pickArray[$i] != '')
						{
							$picklistcount=0;
							$sql ="select $columnName from ".$table_prefix."_$columnName";
							$numrow = $adb->num_rows($adb->pquery($sql, array()));
							for($x=0;$x < $numrow ; $x++)
							{
								$picklistvalues = $adb->query_result($adb->pquery($sql, array()),$x,$columnName);
								if($pickArray[$i] == $picklistvalues)
								{
									$picklistcount++;
								}
							}
							if($picklistcount == 0)
							{
								$picklist_valueid = getUniquePicklistID();
								$query = "insert into ".$table_prefix."_".$columnName." values(?,?,?,?)";
								$adb->pquery($query, array($adb->getUniqueID($table_prefix."_".$columnName),$pickArray[$i],1,$picklist_valueid));
								/*$sql="update vtiger_picklistvalues_seq set id = ?";
								 $adb->pquery($sql, array(++$picklist_valueid));*/
							}
							$sql = "select picklist_valueid from ".$table_prefix."_$columnName where $columnName=?";
							$pick_valueid = $adb->query_result($adb->pquery($sql, array($pickArray[$i])),0,'picklist_valueid');
							$sql = "insert into ".$table_prefix."_role2picklist select roleid,$pick_valueid,$picklistid,$i from ".$table_prefix."_role";
							$adb->pquery($sql, array());
						}
					}
				}
				elseif ($fldType == 'Picklistmulti'){
					//Adding a  new picklist value in the picklist table
					if($mode != 'edit')
					{
						$picklistid = $adb->getUniqueID($table_prefix."_picklist");
						$sql="insert into ".$table_prefix."_picklist values(?,?)";
						$adb->pquery($sql, array($picklistid,$columnName));
					}
				//crmv@101683
				} elseif ($fldType == 'CustomUser') {
					if (!empty($_REQUEST['fldCustomUserPick'])) $info = Zend_Json::encode(array('users'=>Zend_Json::decode($_REQUEST['fldCustomUserPick']))); else $info = '';
					$adb->pquery("insert into {$table_prefix}_fieldinfo(fieldid,info) values(?,?)", array($custfld_fieldid,$info));
				//crmv@101683e
				//crmv@113771
				} elseif ($fldType == 'Button') {
					$info = Zend_Json::encode(array('onclick'=>$_REQUEST['fldOnclick'],'code'=>$_REQUEST['fldCode']));
					$adb->pquery("insert into {$table_prefix}_fieldinfo(fieldid,info) values(?,?)", array($custfld_fieldid,$info));
				//crmv@113771e
				}
				//Inserting into LeadMapping table - Jaguar
				if($fldmodule == 'Leads' && $_REQUEST['fieldid'] == '')
				{
					$id = $adb->getUniqueID($table_prefix."_convertleadmapping");
					$sql_def = "insert into ".$table_prefix."_convertleadmapping (cfmid,leadfid) values(?,?)";
					$params = array($id,$custfld_fieldid);
					$adb->pquery($sql_def,$params);
				}

				if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_ADDFIELD, $custfld_fieldid); // crmv@49398
			}
		}
	}
}

function show_move_hiddenfields($submode, $forMobile = false, $mobileProfileId = null){
	global $adb,$log,$table_prefix, $metaLogs; // crmv@49398
	$selected_fields = $_REQUEST['selected'];
	$selected = trim($selected_fields,":");
	$sel_arr = array();
	$sel_arr = explode(":",$selected);
	$sequence = $adb->pquery('select max(sequence) as maxseq from '.$table_prefix.'_field where block = ?  and tabid = ?',array($_REQUEST['blockid'],$_REQUEST['tabid']));
	$max = $adb->query_result($sequence,0,'maxseq');
	$max_seq = $max + 1;
	$log->debug("vikass".print_r($_REQUEST,true));

	if($submode == 'showhiddenfields'){
		for ($i=0; $i< count($sel_arr);$i++) {
			if ($forMobile) {
				$res = $adb->pquery("update {$table_prefix}_profile2field set visible = 0 where profileid = ? and fieldid = ?", array($mobileProfileId, $sel_arr[$i])); // crmv@38798
			} else {
				$res = $adb->pquery('update '.$table_prefix.'_field set presence = 2,sequence = ? where block = ? and fieldid = ?', array($max_seq,$_REQUEST['blockid'],$sel_arr[$i]));
			}
			$max_seq++;
			if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_EDITFIELD, $sel_arr[$i]); // crmv@49398
			}
		}
	else{
		for($i=0; $i< count($sel_arr);$i++){
			$res = $adb->pquery('update '.$table_prefix.'_field set sequence = ? , block = ? where fieldid = ?', array($max_seq,$_REQUEST['blockid'],$sel_arr[$i]));
			$max_seq++;
			if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_EDITFIELD, $sel_arr[$i]); // crmv@49398
		}
	}
}

function addRelatedToTab($module) {
	$panelid = intval($_REQUEST['panelid']);
	$relids = array_filter(explode(';', $_REQUEST['relids']));
	
	$tab = Vtecrm_Panel::getInstance($panelid);
	
	if (count($relids) > 0 && $tab) {
		$tab->addRelatedLists($relids);
	}
}

function removeTabRelated($module) {
	$panelid = intval($_REQUEST['panelid']);
	$relid = intval($_REQUEST['relationid']);
	
	$tab = Vtecrm_Panel::getInstance($panelid);
	if ($tab && $relid > 0) {
		$tab->removeRelatedList($relid);
	}
}

function reorderTabRelateds($module) {
	global $adb, $table_prefix;
	
	$panelid = intval($_REQUEST['panelid']);
	$ids = array_filter(explode(';', $_REQUEST['relationids']));
	
	if (count($ids) > 0) {
		$seq = 1;
		foreach ($ids as $relid) {
			$adb->pquery("UPDATE {$table_prefix}_panel2rlist SET sequence = ? WHERE panelid = ? AND relation_id = ?", array($seq++, $panelid, $relid));
		}
	}
}

// crmv@39110
function getRelatedListInfo($module, $mobileProfileId = null){
	global $adb,$table_prefix;
	$tabid = getTabid($module);
	if ($mobileProfileId > 0) {
		$related_query =
			"select rl.*, p2r.sequence as sequence_mobile, (1 - p2r.visible) as presence_mobile from {$table_prefix}_relatedlists rl
			inner join {$table_prefix}_tab t on rl.related_tabid = t.tabid and t.presence = 0
			inner join {$table_prefix}_profile2related p2r on p2r.profileid = ? and p2r.tabid = rl.tabid and p2r.relationid = rl.relation_id
			where rl.tabid = ?
			order by p2r.sequence";
		$relinfo = $adb->pquery($related_query,array($mobileProfileId, $tabid));
	} else {
		$related_query =
			"select rl.* from {$table_prefix}_relatedlists rl
			inner join {$table_prefix}_tab on rl.related_tabid = {$table_prefix}_tab.tabid and {$table_prefix}_tab.presence = 0
			where rl.tabid = ?
			order by sequence";
		$relinfo = $adb->pquery($related_query,array($tabid));
	}
	$noofrows = $adb->num_rows($relinfo);
	for($i=0;$i<$noofrows;$i++){
		$res[$i]['name'] = $adb->query_result($relinfo,$i,'name');
		$res[$i]['sequence'] = $adb->query_result($relinfo,$i, ($mobileProfileId > 0 ? 'sequence_mobile' : 'sequence'));
		$label = $adb->query_result($relinfo,$i,'label');
		$relatedModule = getTabname($adb->query_result($relinfo,$i,'related_tabid'));
		$res[$i]['label'] = getTranslatedString($label,$relatedModule);
		$res[$i]['presence'] = $adb->query_result($relinfo,$i, ($mobileProfileId > 0 ? 'presence_mobile' : 'presence'));
		$res[$i]['tabid'] = $tabid;
		$res[$i]['id'] = $adb->query_result($relinfo,$i,'relation_id');
	}
	return $res;
}

function changeRelatedListOrder($forMobile = false, $mobileProfileId = null){
	global $adb,$log,$table_prefix;

	$tabid = intval($_REQUEST['tabid']);
	$what_todo = $_REQUEST['what_to_do'];

	if(!empty($_REQUEST['what_to_do'])){
		if($_REQUEST['what_to_do'] == 'move_up'){
			$currentsequence = $_REQUEST['sequence'];

			if ($forMobile) {
				$previous_relation = $adb->limitpQuery('select relationid as relation_id,sequence from '.$table_prefix.'_profile2related where profileid = ? and sequence < ? and tabid = ? order by sequence desc',0,1,array($mobileProfileId,$currentsequence,$tabid));
			} else {
				$previous_relation = $adb->limitpQuery('select relation_id,sequence from '.$table_prefix.'_relatedlists where sequence < ? and tabid = ? order by sequence desc',0,1,array($currentsequence,$tabid));
			}
			$previous_sequence = $adb->query_result($previous_relation,0,'sequence');
			$previous_relationid = $adb->query_result($previous_relation,0,'relation_id');

			if ($forMobile) {
				// update for all profiles
				$adb->pquery('update '.$table_prefix.'_profile2related set sequence = ? where profileid = ? and relationid = ? and tabid = ?',array($previous_sequence,$mobileProfileId,$_REQUEST['id'],$tabid));
				$adb->pquery('update '.$table_prefix.'_profile2related set sequence = ? where profileid = ? and tabid = ? and relationid = ?',array($currentsequence,$mobileProfileId,$tabid,$previous_relationid));
			} else {
				$adb->pquery('update '.$table_prefix.'_relatedlists set sequence = ? where relation_id = ? and tabid = ?',array($previous_sequence,$_REQUEST['id'],$tabid));
				$adb->pquery('update '.$table_prefix.'_relatedlists set sequence = ? where tabid = ? and relation_id = ?',array($currentsequence,$tabid,$previous_relationid));
			}
		}elseif($_REQUEST['what_to_do'] == 'move_down'){

			$currentsequence = $_REQUEST['sequence'];

			if ($forMobile) {
				$next_relation = $adb->limitpQuery('select relationid as relation_id,sequence from '.$table_prefix.'_profile2related where profileid = ? and sequence > ? and tabid = ? order by sequence',0,1,array($mobileProfileId,$currentsequence,$tabid));
			} else {
				$next_relation = $adb->limitpQuery('select relation_id,sequence from '.$table_prefix.'_relatedlists where sequence > ? and tabid = ? order by sequence',0,1,array($currentsequence,$tabid));
			}
			$next_sequence = $adb->query_result($next_relation,0,'sequence');
			$next_relationid = $adb->query_result($next_relation,0,'relation_id');

			if ($forMobile) {
				// update for all profiles
				$adb->pquery('update '.$table_prefix.'_profile2related set sequence = ? where profileid = ? and relationid = ? and tabid = ?',array($next_sequence,$mobileProfileId,$_REQUEST['id'],$tabid));
				$adb->pquery('update '.$table_prefix.'_profile2related set sequence = ? where profileid = ? and tabid = ? and relationid = ?',array($currentsequence,$mobileProfileId,$tabid,$next_relationid));
			} else {
				$adb->pquery('update '.$table_prefix.'_relatedlists set sequence = ? where relation_id = ? and tabid = ?',array($next_sequence,$_REQUEST['id'],$tabid));
				$adb->pquery('update '.$table_prefix.'_relatedlists set sequence = ? where tabid = ? and relation_id = ?',array($currentsequence,$tabid,$next_relationid));
			}
		}
	}
}

function changeRelatedListVisibility($forMobile = false, $mobileProfileId = null) {
	global $adb, $table_prefix, $metaLogs; // crmv@49398

	$relationid = intval($_REQUEST['id']);
	$visible = intval($_REQUEST['visible']);
	$tabid = intval($_REQUEST['tabid']);

	if ($forMobile) {
		$adb->pquery("update {$table_prefix}_profile2related set visible = ? where profileid = ? and relationid = ? and tabid = ?", array($visible, $mobileProfileId, $relationid, $tabid));
	} else {
		$adb->pquery("update {$table_prefix}_relatedlists set presence = ? where relation_id = ? and tabid = ?", array($visible ^ 1, $relationid, $tabid));
		if ($visible == 0) {
			// not visible, remove the relation from all the tabs also
			$adb->pquery("DELETE FROM {$table_prefix}_panel2rlist WHERE relation_id = ?", array($relationid));
		}
	}
	if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_EDITMODULE, $tabid, array('module'=>getTabModuleName($tabid))); // crmv@49398
}

// crmv@104568
function getRelatedListOptions($module, $mobileProfileId = null) {
	$options = array();
	
	$CRMVUtils = CRMVUtils::getInstance();
	$ord = $CRMVUtils->getConfigurationLayout('tb_relations_order');
	
	$options['sortbycount'] = ($ord == 'num_of_records');

	return $options;
}

function changeRelatedListOption($forMobile = false, $mobileProfileId = null) {

	$option = $_REQUEST['option'];
	$value = $_REQUEST['optionValue'];
	
	$CRMVUtils = CRMVUtils::getInstance();
	
	if ($option == 'sortbycount') {
		$CRMVUtils->setConfigurationLayout('tb_relations_order', $value == '1' ? 'num_of_records' : '');
	}
	
}
// crmv@104568e

function getMobileInfo($fld_module, $mobileProfileId) {
	global $adb, $table_prefix;

	$tabid = getTabId($fld_module);
	$ret = array();

	// get entity name
	$res = $adb->pquery("select * from {$table_prefix}_profile2mobile p2m left join {$table_prefix}_profile2entityname pe on p2m.profileid = pe.profileid and p2m.tabid = pe.tabid where p2m.profileid = ? and p2m.tabid = ?", array($mobileProfileId, $tabid));
	if ($res && $adb->num_rows($res) > 0) {
		$row = $adb->FetchByAssoc($res, -1, false);
		$fname = explode(',', $row['fieldname']);
		$fname = $fname[0]; // show only the first

		$extrafields = array_filter(array_map('trim', explode(',', $row['extrafields'])));
		array_unshift($extrafields, 'x'); // to have element starting from index 1

		$ret['entityname'] = $fname;
		$ret['cvid'] = $row['cvid'];
		$ret['sortfield'] = $row['sortfield'];
		$ret['sortorder'] = $row['sortorder'];
		$ret['extrafields'] = $extrafields;
		$ret['mobiletab'] = $row['mobiletab'];
	}

	return $ret;
}

function saveMobileInfo($module, $mobileProfileId) {
	global $adb, $table_prefix, $metaLogs; // crmv@49398

	$tabid = getTabid($module);
	$entityname = vtlib_purify($_REQUEST['mobileEntityName']);
	if (empty($entityname)) $entityname = null;

	$cvid = intval($_REQUEST['mobileFilter']);
	if ($cvid == 0) $cvid = null;

	$sortfield = vtlib_purify($_REQUEST['mobileSortField']);
	if (empty($sortfield)) $sortfield = null;

	$sortorder = vtlib_purify($_REQUEST['mobileSortOrder']);
	if (empty($sortorder)) $sortorder = null;

	$extrafields = array();
	for ($i=1; $i<4; ++$i) {
		$v = trim($_REQUEST['mobileExtraField'.$i]);
		if (!empty($v)) $extrafields[] = $v;
	}
	if (count($extrafields) > 0) {
		$extrafields = implode(',', $extrafields);
	} else {
		$extrafields = null;
	}

	$mobiletab = $_REQUEST['mobileDefaultTab'];
	if (empty($mobiletab)) $mobiletab = null;

	// check if exists
	$res = $adb->pquery("select profileid from {$table_prefix}_profile2entityname where tabid = ?", array($tabid));

	if (is_null($entityname)) {
		// delete
		$adb->pquery("delete from {$table_prefix}_profile2entityname where tabid = ?", array($tabid));
	} elseif ($res && $adb->num_rows($res) > 0) {
		// update for all profiles
		$adb->pquery("update {$table_prefix}_profile2entityname set fieldname = ? where tabid = ?", array($entityname, $tabid));
	} else {
		// insert
		$adb->pquery("insert into {$table_prefix}_profile2entityname (profileid, tabid, fieldname) values (?,?,?)", array($mobileProfileId, $tabid, $entityname));
	}

	// changes on mobile table
	// check if exists
	$res = $adb->pquery("select profileid from {$table_prefix}_profile2mobile where tabid = ?", array($tabid));
	if ($res && $adb->num_rows($res) == 0) {
		// update for all profiles
		$adb->pquery("insert into {$table_prefix}_profile2mobile (profileid, tabid) values(?,?)", array($mobileProfileId, $tabid));
	}
	// now update
	$adb->pquery("update {$table_prefix}_profile2mobile set cvid = ?, sortfield =?, sortorder = ?, extrafields = ?, mobiletab = ? where tabid = ?", array($cvid, $sortfield, $sortorder, $extrafields, $mobiletab, $tabid));

	if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_EDITPROFILE, $mobileProfileId); // crmv@49398
}
// crmv@39110e

//crmv@96450	crmv@101683
function getNewFields() {
	$new_fields = array(
		array(
			'label' => getTranslatedString('Text'),
			'vteicon' => 'text_fields',
		),
		array(
			'label' => getTranslatedString('LBL_TEXT_AREA'),
			'vteicon' => 'text_fields',
		),
		array(
			'label' => getTranslatedString('Number'),
			'vteicon' => 'N',
		),
		array(
			'label' => getTranslatedString('Percent'),
			'vteicon' => '%',
		),
		array(
			'label' => getTranslatedString('Currency'),
			'vteicon' => 'attach_money',
		),
		array(
			'label' => getTranslatedString('Date'),
			'vteicon' => 'date_range',
		),
		array(
			'label' => getTranslatedString('Email'),
			'vteicon' => 'email',
		),
		array(
			'label' => getTranslatedString('Phone'),
			'vteicon' => 'phone',
		),
		array(
			'label' => getTranslatedString('LBL_URL'),
			'vteicon' => 'http',
		),
		array(
			'label' => getTranslatedString('LBL_CHECK_BOX'),
			'vteicon' => 'check_box',
		),
		array(
			'label' => getTranslatedString('PickList'),
			'vteicon' => 'list',
		),
		array(
			'label' => getTranslatedString('LBL_MULTISELECT_COMBO'),
			'vteicon' => 'list',
		),
		array(
			'label' => getTranslatedString('Picklistmulti'),
			'vteicon' => 'list',
		),
		array(
			'label' => getTranslatedString('Skype'),
			'vteicon2' => 'fa-skype',
		),
		array(
			'label' => getTranslatedString('LBL_USER','Users'),
			'vteicon' => 'person',
		),
		array(
			'label' => getTranslatedString('LBL_SELECT_ALL_USER','Users'),
			'vteicon' => 'person',
		),
		array(
			'label' => getTranslatedString('LBL_SELECT_CUSTOM_USER','Users'),
			'vteicon' => 'person',
		),
		array(
			'label' => getTranslatedString('LBL_GROUP','Users'),
			'vteicon' => 'group',
		),
		/* TODO
		array(
			'uitype' => 10,
			'label' => getTranslatedString('LBL_RELATED_TO'),
			'vteicon' => 'link',
			'properties' => array('label', 'relatedmods'),
			'relatedmods' => $this->getRelationModules(),
		),
		array(
			'uitype' => 4,
			'label' => getTranslatedString('LBL_FIELD_AUTONUMBER'),
			'vteicon' => 'A',
			'properties' => array('label', 'autoprefix'),
			'defaults' => array('autoprefix' => (empty($modName) ? '' : strtoupper(substr($modName, 0, 3)))),
		),
		*/
	);
	//crmv@113771
	if (SDK::isUitype(213)) {
		$new_fields[] = array(
			'label' => getTranslatedString('LBL_FIELD_BUTTON'),
			'vteicon2' => 'fa-hand-pointer-o',
		);
	}
	//crmv@113771e
	// crmv@106857
	require_once('modules/Settings/ProcessMaker/ProcessMakerUtils.php');
	$PMUtils = ProcessMakerUtils::getInstance();
	if ($PMUtils->todoFunctions && SDK::isUitype(220)) {
		$new_fields[] = array(
			//'uitype' => 220,
			'label' => getTranslatedString('LBL_FIELD_TABLE'),
			'vteicon' => 'grid_on',
			//'properties' => array('label','columns'),
		);
	}
	// crmv@106857e
	//crmv@115268
	if ($PMUtils->todoFunctions && SDK::isUitype(29)) {
		$new_fields[] = array(
			//'uitype' => 29,
			'label' => getTranslatedString('LBL_ATTACH_DOCUMENTS'),
			'vteicon' => 'attach_file',
			//'properties' => array('label'),
		);
	}
	//crmv@115268e
	return $new_fields;
}
//crmv@96450e	crmv@101683e
<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

require_once('include/logging.php');
require_once('modules/CustomView/CustomView.php');
require_once('include/utils/utils.php');
require_once('Smarty_setup.php');
//crmv@16312
global $mod_strings, $current_language, $default_charset;
//crmv@16312 end
//crmv@23687
global $adb, $current_user, $theme,$table_prefix;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
//crmv@23687e

require_once('modules/Home/language/'.$current_language.'.lang.php');

$LVU = ListViewUtils::getInstance();

$total_record_count = 0;

$query_string = trim($_REQUEST['query_string']);
$curModule = vtlib_purify($_REQUEST['module']);

if(isset($query_string) && $query_string != ''){
	/*
	// Was the search limited by user for specific modules?
	$search_onlyin = $_REQUEST['search_onlyin'];
	if(!empty($search_onlyin) && $search_onlyin != '--USESELECTED--') {
		$search_onlyin = explode(',', $search_onlyin);
	} else if($search_onlyin == '--USESELECTED--') {
		$search_onlyin = $_SESSION['__UnifiedSearch_SelectedModules__'];
	} else {
		$search_onlyin = array();
	}
	// Save the selection for futur use (UnifiedSearchModules.php)
	$_SESSION['__UnifiedSearch_SelectedModules__'] = $search_onlyin;
	$current_user->saveSearchModules($search_onlyin); // crmv@26485
	// END
	*/

	$search_val = $query_string;
	$search_module = $_REQUEST['search_module'];

	//crmv@29907
//	if($search_module != ''){//This is for Tag search
		$search_onlyin=getAllModulesForTag();
//	}
	//crmv@29907e

	$object_array = getSearchModules($search_onlyin);

	if($curModule=='Home') {
		getSearchModulesComboList($search_module);
	}
	$i = 0;
	$moduleRecordCount = array();
	foreach($object_array as $module => $object_name){
		if ($curModule == 'Home' || ($curModule == $module && !empty($_REQUEST['ajax']))) {
			$focus = CRMEntity::getInstance($module);
			if(isPermitted($module,"index") == "yes"){
				$smarty = new vtigerCRM_Smarty;

				$mod_strings = return_module_language($current_language,$module); //crmv@42329
				global $app_strings;
				$tmp_theme = $theme;	//crmv@23687

				$smarty->assign("MOD", $mod_strings);
				$smarty->assign("APP", $app_strings);
				$smarty->assign("THEME", $theme);
				$smarty->assign("IMAGE_PATH",$image_path);
				$smarty->assign("MODULE",$module);
				$smarty->assign("SEARCH_MODULE",vtlib_purify($_REQUEST['search_module']));
				$smarty->assign("SINGLE_MOD",$module);
				//crmv@16312
				$smarty->assign("SEARCH_STRING",htmlentities($search_val, ENT_QUOTES, $default_charset));
				//crmv@16312 end
				//crmv@23687
				$cv_res = $adb->pquery("select cvid from ".$table_prefix."_customview where viewname='All' and entitytype=?", array($module));
				$viewid = $adb->query_result($cv_res,0,'cvid');
				$queryGenerator = QueryGenerator::getInstance($module, $current_user);
				if ($viewid != "0") {
					$queryGenerator->initForCustomViewById($viewid);
				} else {
					$queryGenerator->initForDefaultCustomView();
				}
				//crmv@23687e
				if($search_module != ''){//This is for Tag search
					$where = getTagWhere($search_val,$current_user->id);
					$search_msg =  $app_strings['LBL_TAG_SEARCH'];
					$search_msg .=	"<b>".to_html($search_val)."</b>";
				}else{			//This is for Global search
					//crmv@23687
					$searchConditions = getUnifiedWhereConditions($module,$search_val);
					$queryGenerator->addUserSearchConditions($searchConditions);
					//crmv@23687e
					$search_msg = $app_strings['LBL_SEARCH_RESULTS_FOR'];
					//crmv@16312
					$search_msg .=	"<b>".htmlentities($search_val, ENT_QUOTES, $default_charset)."</b>";
					//crmv@16312 end
				}
				$listquery = $queryGenerator->getQuery();	//crmv@23687
				if($where != ''){
					$listquery .= ' and ('.$where.')';
				}
				if(!(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '')) {
					$count_result = $adb->query($listquery);
					$noofrows = $adb->num_rows($count_result);
				} else {
					$noofrows = vtlib_purify($_REQUEST['recordCount']);
				}
				$noofrows = intval($noofrows); // crmv@107453
				$moduleRecordCount[$module]['count'] = $noofrows;

				$theme = $tmp_theme;	//crmv@23687

				global $list_max_entries_per_page;
				if(!empty($_REQUEST['start'])){
					$start = $_REQUEST['start'];
					if($start == 'last'){
						$count_result = $adb->query( mkCountQuery($listquery));
						$noofrows = $adb->query_result($count_result,0,"count");
						if($noofrows > 0){
							$start = ceil($noofrows/$list_max_entries_per_page);
						}
					}
					if(!is_numeric($start)){
						$start = 1;
					} elseif($start < 0){
						$start = 1;
					}
					$start = ceil($start);
				}else{
					$start = 1;
				}

				$list_max_entries_per_page=get_selection_options($noofrows,'list');
				$queryMode = (isset($_REQUEST['query']) && $_REQUEST['query'] == 'true');
				$navigation_array = VT_getSimpleNavigationValues($start,$list_max_entries_per_page,$noofrows);
				$limitStartRecord = ($start-1) * $list_max_entries_per_page;

				$list_result = $adb->limitQuery($listquery,$limitStartRecord,$list_max_entries_per_page);

				$moduleRecordCount[$module]['recordListRangeMessage'] = getRecordRangeMessage($list_max_entries_per_page, $limitStartRecord);

				$info_message='&recordcount='.$_REQUEST['recordcount'].'&noofrows='.$_REQUEST['noofrows'].'&message='.$_REQUEST['message'].'&skipped_record_count='.$_REQUEST['skipped_record_count'];
				$url_string = '&modulename='.$_REQUEST['modulename'].'&nav_module='.$module.$info_message;
				$viewid = '';

				$navigationOutput = $LVU->getTableHeaderSimpleNavigation($navigation_array, $url_string,$module,"UnifiedSearch",$viewid);
				//crmv@42931
				$controller = ListViewController::getInstance($adb, $current_user, null);
				$controller->setQueryGenerator($queryGenerator);
				//crmv@42931e
				$listview_header = $controller->getListViewHeader($focus,$module,$url_string);
				$listview_entries = $controller->getListViewEntries($focus,$module,$list_result,$navigation_array);

				//Do not display the Header if there are no entires in listview_entries
				if(count($listview_entries) > 0){
					$display_header = 1;
				}else{
					// crmv@107453
					$display_header = 0;
					$moduleRecordCount[$module]['count'] = 0;
					$moduleRecordCount[$module]['recordListRangeMessage'] = '';
					// crmv@107453e
				}
				$smarty->assign("NAVIGATION", $navigationOutput);
				$smarty->assign("LISTHEADER", $listview_header);
				$smarty->assign("LISTENTITY", $listview_entries);
				$smarty->assign("DISPLAYHEADER", $display_header);
				$smarty->assign("HEADERCOUNT", count($listview_header));
				$smarty->assign("ModuleRecordCount", $moduleRecordCount);

				$total_record_count = $total_record_count + $noofrows;

				$smarty->assign("SEARCH_CRITERIA","($noofrows)".$search_msg); // crmv@107453
				$smarty->assign("MODULES_LIST", $object_array);
				$smarty->assign("CUSTOMVIEW_OPTION",$customviewcombo_html);

				if(($i != 0 && empty($_REQUEST['ajax'])) || !(empty($_REQUEST['ajax'])))
					$smarty->display("UnifiedSearchAjax.tpl");
				else
					$smarty->display('UnifiedSearchDisplay.tpl');
				unsetLVS($module);
				$i++;
				flush(); // show the output as soon as possible
			}
		}
	}
	//Added to display the Total record count
	if(empty($_REQUEST['ajax']) && !empty($object_array)) {
?>
<script>
	document.getElementById("global_search_total_count").innerHTML = " <?php echo $app_strings['LBL_TOTAL_RECORDS_FOUND'] ?><b><?php echo $total_record_count; ?></b>";
</script>
<?php
//crmv@26485
} elseif(empty($object_array)) {
	echo "<br /><p>&nbsp;&nbsp;&nbsp;".getTranslatedString('NoModulesSelected','Home')."</p><br />";	//crmv@27911
}
//crmv@26485e
} else {
	echo "<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>".$mod_strings['ERR_ONE_CHAR']."</em>";
}


/**
 * Function to get the Tags where condition
 * @param  string $search_val -- entered search string value
 * @param  string $current_user_id     -- current user id
 * @return string $where      -- where condition with the list of crmids, will like vtiger_crmentity.crmid in (1,3,4,etc.,)
 */
function getTagWhere($search_val,$current_user_id){
	global $table_prefix;
	require_once('include/freetag/freetag.class.php');

	$freetag_obj = new freetag();
	$crmid_array = $freetag_obj->get_objects_with_tag_all($search_val,$current_user_id);

	$where = " {$table_prefix}_crmentity.crmid IN (";
	if(count($crmid_array) > 0){
		foreach($crmid_array as $index => $crmid){
			$where .= $crmid.',';
		}
		$where = trim($where,',').')';
	}
	//If there are no records has the search tag we need to add the condition like crmid is none. If dont add condition at all search will return all the values.
	// Fix for #5571
	else {
		$where .= '0)';
	}
	return $where;
}


/**
 * Function to get the the List of Searchable Modules as a combo list which will be displayed in right corner under the Header
 * @param  string $search_module -- search module, this module result will be shown defaultly
 */
function getSearchModulesComboList($search_module){
	global $object_array;
	global $app_strings;
	global $mod_strings;

	?>
		<script>
		function displayModuleList(selectmodule_view){
			<?php
			foreach($object_array as $module => $object_name){
				if(isPermitted($module,"index") == "yes"){
			?>
				   mod = "global_list_"+"<?php echo $module; ?>";
				   if(selectmodule_view.options[selectmodule_view.options.selectedIndex].value == "All")
				   show(mod);
				   else
				   hide(mod);
				<?php
				}
			}
			?>

			if(selectmodule_view.options[selectmodule_view.options.selectedIndex].value != "All"){
				selectedmodule="global_list_"+selectmodule_view.options[selectmodule_view.options.selectedIndex].value;
				show(selectedmodule);
			}
		}
		</script>
		 <table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
		     <tr>
		        <td colspan="3" id="global_search_total_count">&nbsp;</td>
		<td nowrap align="right"><?php echo $app_strings['LBL_SHOW_RESULTS'] ?>&nbsp;
		                <select id="global_search_module" name="global_search_module" onChange="displayModuleList(this);" class="small">
			<option value="All"><?php echo $app_strings['COMBO_ALL'] ?></option>
						<?php
						foreach($object_array as $module => $object_name){
							$selected = '';
							if($search_module != '' && $module == $search_module){
								$selected = 'selected';
							}
							if($search_module == '' && $module == 'All'){
								$selected = 'selected';
							}
							?>
							<?php if(isPermitted($module,"index") == "yes"){
							?>
							<!-- vtlib customization: Use translation if available -->
							<?php $modulelabel = getTranslatedString($module,$module);	//crmv@16886 ?>
							<option value="<?php echo $module; ?>" <?php echo $selected; ?> ><?php echo $modulelabel; ?></option>
							<?php
							}
						}
						?>
		     		</select>
		        </td>
		     </tr>
		</table>
	<?php
}

?>

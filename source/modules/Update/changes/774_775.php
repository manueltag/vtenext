<?php
$_SESSION['modules_to_update']['Assets'] = 'packages/vte/mandatory/Assets.zip';
$_SESSION['modules_to_update']['ChangeLog'] = 'packages/vte/mandatory/ChangeLog.zip';
$_SESSION['modules_to_update']['Charts'] = 'packages/vte/mandatory/Charts.zip';
$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter', 'Targets'));
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan', 'ProjectMilestone', 'ProjectTask'));
$_SESSION['modules_to_update']['ServiceContracts'] = 'packages/vte/mandatory/ServiceContracts.zip';
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';
$_SESSION['modules_to_update']['Visitreport'] = 'packages/vte/mandatory/Visitreport.zip';
$_SESSION['modules_to_update']['Webforms'] = 'packages/vte/mandatory/Webforms.zip';

global $adb, $table_prefix;

$path = 'modules';
$files = array(
	'DetailView.php'=>array('skip'=>array('Calendar','Emails','Fax','Messages','ModComments','ModNotifications','PBXManager','Projects','Sms','Users')),
);
$modules = array_diff(scandir($path),array('VteCore'));
foreach ($modules as $module) {
    if ($module === '.' or $module === '..') continue;
    $dir = $path.'/'.$module;
    if (is_dir($dir)) {
    	foreach ($files as $file => $info) {
    		if (!empty($info['skip']) && in_array($module,$info['skip'])) {
    			continue;
    		}
	    	if (file_exists($dir.'/'.$file)) {
	    		//echo $dir.'/'.$file.'<br />';
	        	@unlink($dir.'/'.$file);
	    	}
    	}
    }
}
@unlink('vtlib/ModuleDir/5.0/DetailView.php');

@unlink('Smarty/templates/Inventory/InventoryActions.tpl');
@unlink('Smarty/templates/Inventory/InventoryDetailView.tpl');

if (!function_exists('addColumnToTable')) {
	function addColumnToTable($tablename, $columnname, $type, $extra = '') {
		global $adb;
		// check if already present
		$cols = $adb->getColumnNames($tablename);
		if (in_array($columnname, $cols)) {
			return;
		}
		$col = $columnname.' '.$type.' '.$extra;
		$adb->alterTable($tablename, $col, 'Add_Column');
	}
}
addColumnToTable("sdk_uitype", 'old_style', 'I(1) DEFAULT 0');
$adb->pquery("update sdk_uitype set old_style = ?",array(1));
SDK::clearSessionValues();

addColumnToTable("{$table_prefix}_links", 'size', 'I(1) DEFAULT 1');

SDK::setLanguageEntries('APP_STRINGS', 'LBL_SUMMARY', array('it_it'=>'Riassunto','en_us'=>'Summary'));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_LINK_ACTION', array('it_it'=>'Collega','en_us'=>'Link'));

$adb->pquery("DELETE FROM {$table_prefix}_relatedlists WHERE related_tabid = ? AND name = ?",array(9,'get_history'));
$adb->pquery("DELETE FROM {$table_prefix}_relatedlists WHERE related_tabid = ? AND name = ?",array(9,'get_fathers'));
$adb->pquery("DELETE FROM {$table_prefix}_relatedlists WHERE related_tabid = ? AND name = ?",array(9,'get_children'));

$ModCommentsInstance = CRMEntity::getInstance('ModComments');
$ModCommentsInstance->removeWidgetFrom('Charts');

$ChartsInstance = Vtiger_Module::getInstance('Charts');
if ($ChartsInstance) {
	$adb->pquery("UPDATE {$table_prefix}_blocks SET display_status = ? WHERE tabid = ?",array(1,$ChartsInstance->id));
}

require_once('include/utils/DetailViewWidgets.php');
$focusDetailViewWidgets = new DetailViewWidgets();
$widgets = array('AccountsHierarchy');
foreach($widgets as $widget) {
	$widgetObj = $focusDetailViewWidgets->getWidget($widget);
	$widgetObj->install();
}

$schema_table =
'<schema version="0.3">
	<table name="'.$table_prefix.'_relatedlists_pin">
	  <opt platform="mysql">ENGINE=InnoDB</opt>
	    <field name="userid" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="relation_id" type="I" size="19">
	      <KEY/>
	    </field>
	  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_relatedlists_pin')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$adb->pquery("update {$table_prefix}_settings_field set active = ? where name = ?",array(1,'LBL_DEFAULT_MODULE_VIEW'));

$disp_view = 'detail_view';
$res = $adb->query("SELECT {$table_prefix}_tab.tabid, {$table_prefix}_tab.name
					FROM {$table_prefix}_field
					INNER JOIN {$table_prefix}_tab ON {$table_prefix}_field.tabid = {$table_prefix}_tab.tabid
					WHERE {$table_prefix}_tab.name NOT IN ('Emails','Fax','Sms','ModComments','ModNotifications','ChangeLog','Messages')
					GROUP BY {$table_prefix}_tab.tabid, {$table_prefix}_tab.name");
if ($res) {
	while($row=$adb->fetchByAssoc($res)) {
		$tabid = $row['tabid'];
		$module = $row['name'];
		$sql = "SELECT * FROM {$table_prefix}_field WHERE quickcreate in (0,2) and tabid = ? AND {$table_prefix}_field.displaytype IN (1,2,4) and {$table_prefix}_field.presence in (0,2)";
		$params = array($tabid);
		$result = $adb->pquery($sql, $params);
		if ($result && $adb->num_rows($result) == 0) {
			$result1 = $adb->pquery("select fieldid from {$table_prefix}_field where tabid = ? and uitype <> ? and (typeofdata like ? or uitype = ?) order by block, sequence",array($tabid,99,'%~M',4));
			if ($result1 && $adb->num_rows($result1)) {
				$quickcreate = 0;
				$quickcreatesequence = 1;
				while($row1=$adb->fetchByAssoc($result1)) {
					$adb->pquery("update {$table_prefix}_field set quickcreate = ?, quickcreatesequence = ? where fieldid = ?",array($quickcreate,$quickcreatesequence,$row1['fieldid']));
					$quickcreatesequence++;
				}
			}
		}
	}
}
?>
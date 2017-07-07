<?php
global $adb, $table_prefix;

if (!function_exists('createFields')) {
function createFields($list) {
	global $adb, $table_prefix;

	if (!is_array($list)) return;

	$ret = array();
	foreach ($list as $k=>$arr) {

		$Vtiger_Utils_Log = true;

		$modulo = Vtiger_Module::getInstance($arr['module']);

		if (is_null($modulo)) {
			die('ERROR: Module is empty');
		}

		if (empty($arr['blockid'])) {
			$block = Vtiger_Block::getInstance($arr['block'], $modulo);
			if (is_null($block)) {
				die('ERROR: Block is empty');
			}
		} else {
			$block = Vtiger_Block::getInstance($arr['blockid']);
		}


		$field = @Vtiger_Field::getInstance($arr['name'], $modulo);

		if ($field != NULL) {
			$ret[$k] = $field;
			continue;
		} else {
			$field = new Vtiger_Field();
			$ret[$k] = $field;
		}

		// default values
		$field->name = $arr['name'];
		$field->column = $arr['name'];
		$field->label= $arr['label'];
		$field->columntype = 'C(255)';
		$field->typeofdata = 'V~O';
		$field->uitype = 1;
		$field->readonly = 1;
		$field->displaytype = 1;
		$field->masseditable = 0;
		$field->quickcreate = 1;
		$field->table = $modulo->basetable;

		if (isset($arr['table']) && !empty($arr['table']))
			$field->table = $arr['table'];

		if (isset($arr['column']) && !empty($arr['column']))
			$field->column = $arr['column'];

		if (isset($arr['readonly']) && !empty($arr['readonly']))
			$field->readonly = $arr['readonly'];

		if (isset($arr['presence']) && !empty($arr['presence']))
			$field->presence = $arr['presence'];

		if (isset($arr['columntype']) && !empty($arr['columntype']))
			$field->columntype = $arr['columntype'];

		if (isset($arr['typeofdata']) && !empty($arr['typeofdata']))
			$field->typeofdata = $arr['typeofdata'];

		if (isset($arr['uitype']) && !empty($arr['uitype']))
			$field->uitype = $arr['uitype'];

		if (isset($arr['displaytype']) && !empty($arr['displaytype']))
			$field->displaytype = $arr['displaytype'];

		if (isset($arr['quickcreate']))
			$field->quickcreate = $arr['quickcreate'];

		if (isset($arr['masseditable']))
			$field->masseditable = $arr['masseditable'];

		//se picklist aggiungo i valori
		if (isset($arr['picklist']) && !empty($arr['picklist'])){
			$field->setPicklistValues($arr['picklist']);
		}

		$block->addField($field);

		// related modules
		if (isset($arr['relatedModules']) && !empty($arr['relatedModules'])){
			$field->setRelatedModules($arr['relatedModules']);
			if (!empty($arr['relatedModulesAction'])) {
				foreach ($arr['relatedModules'] as $relmod) {
					$relinst = Vtiger_Module::getInstance($relmod);
					$relinst->setRelatedList($modulo, $arr['module'], $arr['relatedModulesAction'][$relmod], 'get_dependents_list');
				}
			}
		}

		// sdk:uitype, we need to change the uitype by hand
		if (isset($arr['sdk_uitype']) && !empty($arr['sdk_uitype'])) {
			$newtype = intval($arr['sdk_uitype']);
			$adb->pquery("update {$table_prefix}_field set uitype = ? where columnname = ? and tabid = ?", array($newtype, $arr['name'], $modulo->id));
		}

	}
	return $ret;
}
}


$_SESSION['modules_to_update']['Assets'] = 'packages/vte/mandatory/Assets.zip';
$_SESSION['modules_to_update']['ChangeLog'] = 'packages/vte/mandatory/ChangeLog.zip';
$_SESSION['modules_to_update']['Charts'] = 'packages/vte/mandatory/Charts.zip';
$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
$_SESSION['modules_to_update']['Mobile'] = 'packages/vte/mandatory/Mobile.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter', 'Targets'));
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan', 'ProjectMilestone', 'ProjectTask'));
$_SESSION['modules_to_update']['ServiceContracts'] = 'packages/vte/mandatory/ServiceContracts.zip';
$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';
$_SESSION['modules_to_update']['Visitreport'] = 'packages/vte/mandatory/Visitreport.zip';
$_SESSION['modules_to_update']['Projects'] = 'packages/vte/optional/Projects.zip';


// fix document massedit fields
$adb->query("update {$table_prefix}_field set masseditable = 1 where tabid = 8 and fieldname in ('notes_title', 'folderid', 'filestatus', 'assigned_user_id')");

// init webservices for reports
$repInst = Vtiger_Module::getInstance('Reports');
$repInst->isentitytype = true;
Vtiger_Webservice::initialize($repInst);

// aggiungi campi per Charts
$fields = array(
	'folderid'		=> array('module'=>'Charts', 'block'=>'LBL_CHARTS_INFORMATION',	'name'=>'folderid',	'label'=>'Folder Name',		'table'=>"{$table_prefix}_charts", 	'column'=>'folderid',	'columntype'=>'I(19)',	'typeofdata'=>'I~O', 	'uitype'=>26, 'readonly'=>1, 'masseditable'=>1, 'quickcreate'=>0),
);

if (isModuleInstalled('Charts')) {
	$fieldRet = createFields($fields);
}

// FOLDER TABLE
$foldertab = $table_prefix.'_crmentityfolder';
if(!Vtiger_Utils::CheckTable($foldertab)) {
	$schema = '
	<schema version="0.3">
	<table name="'.$foldertab.'">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="folderid" type="I" size="19">
			<key/>
		</field>
		<field name="tabid" type="I" size="19">
			<NOTNULL/>
		</field>
		<field name="createdby" type="I" size="19">
			<NOTNULL/>
		</field>
		<field name="foldername" type="C" size="200">
			<NOTNULL/>
		</field>
		<field name="description" type="C" size="250"/>
		<field name="state" type="C" size="50"/>
		<field name="sequence" type="I" size="19"/>
		<index name="'.$foldertab.'_idx1">
			<col>tabid</col>
		</index>
	</table>
	</schema>';
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema));

	// get max folder ids
	$maxrepid = 0;
	$res = $adb->query("select max(folderid) as maxid from {$table_prefix}_reportfolder");
	if ($res) $maxrepid = $adb->query_result($res, 0, 'maxid');

	$maxdocid = 0;
	$res = $adb->query("select max(folderid) as maxid from {$table_prefix}_attachmentsfolder");
	if ($res) $maxdocid = $adb->query_result($res, 0, 'maxid');

	$reportTabid = getTabid('Reports');
	$docTabid = getTabid('Documents');

	// update ids so they are greater than both documents and reports
	$res = $adb->query("update {$table_prefix}_reportfolder set folderid = folderid + ".(1000+$maxrepid+$maxdocid));
	$res = $adb->query("update {$table_prefix}_report set folderid = folderid + ".(1000+$maxrepid+$maxdocid));

	// update documents id so there are no gaps
	$res = $adb->query("select folderid from {$table_prefix}_attachmentsfolder order by folderid");
	if ($res) {
		$i = 1;
		while ($row = $adb->fetchByAssoc($res, -1, false)) {
			$oldfoldid = $row['folderid'];
			$adb->pquery("update {$table_prefix}_attachmentsfolder set folderid = ? where folderid = ?", array($i, $oldfoldid));
			$adb->pquery("update {$table_prefix}_notes set folderid = ? where folderid = ?", array($i, $oldfoldid));
			++$i;
		}
	}

	// get max doc id again
	$res = $adb->query("select max(folderid) as maxid from {$table_prefix}_attachmentsfolder");
	if ($res) $maxdocid = $adb->query_result($res, 0, 'maxid');


	// update report folder id so they are sequential to documents and without gaps
	$res = $adb->query("select folderid from {$table_prefix}_reportfolder order by folderid");
	if ($res) {
		$i = $maxdocid+1;
		while ($row = $adb->fetchByAssoc($res, -1, false)) {
			$oldfoldid = $row['folderid'];
			$adb->pquery("update {$table_prefix}_reportfolder set folderid = ? where folderid = ?", array($i, $oldfoldid));
			$adb->pquery("update {$table_prefix}_report set folderid = ? where folderid = ?", array($i, $oldfoldid));
			++$i;
		}
	}

	// now documents goes from 1 to n, reports from n+1 to m, without gaps
	// at this point, sequence tables might be wrong. you shouldn't use them anymore

	// truncate table
	//$res = $adb->query("delete from {$table_prefix}_crmentityfolder");

	// now insert them into the new table
	$res = $adb->query("select * from {$table_prefix}_attachmentsfolder order by folderid");
	if ($res) {
		while ($row = $adb->fetchByAssoc($res, -1, false)) {
			addEntityFolder('Documents', $row['foldername'], $row['description'], $row['createdby'], '', $row['sequence']);
		}
	}
	$res = $adb->query("select * from {$table_prefix}_reportfolder order by folderid");
	if ($res) {
		while ($row = $adb->fetchByAssoc($res, -1, false)) {
			addEntityFolder('Reports', $row['foldername'], $row['description'], 1, $row['state']);
		}
	}

	// TODO: delete tables

	// aggiungo cartelle per i report speciali
	$fol1 = getEntityFoldersByName('LBL_REPORT_FOLDER_PROJECTS', 'Reports');
	if (empty($fol1)) {
		addEntityFolder('Reports', 'LBL_REPORT_FOLDER_PROJECTS', '', 1, 'SAVED');
	}

	$fol1 = getEntityFoldersByName('LBL_REPORT_FOLDER_PRODLINES', 'Reports');
	if (empty($fol1)) {
		addEntityFolder('Reports', 'LBL_REPORT_FOLDER_PRODLINES', '', 1, 'SAVED');
	}

}


// LANGUAGES
$trans = array(
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_LIST' => 'Elenco',
			'LBL_DELETE_FOLDERS' => 'Elimina Cartelle...',
			'LBL_FOLDER_CONTENT' => 'Ultimi elementi modificati',
			'LBL_ADD_NEW_FOLDER' => 'Aggiungi Cartella',
			'LBL_FOLDER_NAME' => 'Nome Cartella',
			'LBL_FOLDERS' => 'Cartelle',
			'LBL_SELECT_FOLDER' => 'Seleziona Cartella',
			'LBL_MOVE' => 'Sposta',
			'LBL_EMPTY_FOLDER' => 'Questa cartella è vuota',
			'LBL_FOLDER' => 'Cartella',
		),
		'en_us' => array(
			'LBL_LIST' => 'List',
			'LBL_DELETE_FOLDERS' => 'Delete Folders...',
			'LBL_FOLDER_CONTENT' => 'Last',
			'LBL_ADD_NEW_FOLDER' => 'Add Folder',
			'LBL_FOLDER_NAME' => 'Folder Name',
			'LBL_FOLDERS' => 'Folders',
			'LBL_SELECT_FOLDER' => 'Select Folder',
			'LBL_MOVE' => 'Move',
			'LBL_EMPTY_FOLDER' => 'This folder is empty',
			'LBL_FOLDER' => 'Folder',
		),
		'pt_br' => array(
			'LBL_LIST' => 'Lista',
			'LBL_DELETE_FOLDERS' => 'Excluir Pastas...',
			'LBL_FOLDER_CONTENT' => 'Últimos elementos modificados',
			'LBL_ADD_NEW_FOLDER' => 'Adicionar Pasta',
			'LBL_FOLDER_NAME' => 'Nome Pasta',
			'LBL_FOLDERS' => 'Pastas',
			'LBL_SELECT_FOLDER' => 'Seleciona Pasta',
			'LBL_MOVE' => 'Mover',
			'LBL_EMPTY_FOLDER' => 'Esta pasta está vazia',
			'LBL_FOLDER' => 'Pasta',
		)
	),
	'ALERT_ARR' => array(
		'it_it' => array(
			'LBL_SELECT_DEL_FOLDER' => 'Seleziona almeno una cartella',
			'LBL_NO_EMPTY_FOLDERS' => 'Non ci sono cartelle vuote',
			'LBL_OR' => 'o',
		),
		'en_us' => array(
			'LBL_SELECT_DEL_FOLDER' => 'Select at least one folder',
			'LBL_NO_EMPTY_FOLDERS' => 'There are no empty folders',
			'LBL_OR' => 'or',
		),
		'pt_br' => array(
			'LBL_SELECT_DEL_FOLDER' => 'Seleciona pelo menos uma pasta',
			'LBL_NO_EMPTY_FOLDERS' => 'Não há pastas vazias',
			'LBL_OR' => 'ou',
		),
	),
	'Reports' => array(
		'it_it' => array(
			'LBL_REPORT_FOLDER_PROJECTS' => 'Reports per Progetti',
			'LBL_REPORT_FOLDER_PRODLINES' => 'Reports per Linee di prodotto',
		),
		'en_us' => array(
			'LBL_REPORT_FOLDER_PROJECTS' => 'Reports for Projects',
			'LBL_REPORT_FOLDER_PRODLINES' => 'Reports for Product lines',
		),
		'pt_br' => array(
			'LBL_REPORT_FOLDER_PROJECTS' => 'Reports de Projetos',
			'LBL_REPORT_FOLDER_PRODLINES' => 'Reports para linhas de produtos',
		),
	),
	'Charts' => array(
		'it_it' => array(
			'Folder Name' => 'Nome Cartella',
		),
		'en_us' => array(
			'Folder Name' => 'Folder Name',
		),
		'pt_br' => array(
			'Folder Name' => 'Nome Pasta',
		)
	),
);

foreach ($trans as $module=>$modlang) {
	foreach ($modlang as $lang=>$translist) {
		foreach ($translist as $label=>$translabel) {
			SDK::setLanguageEntry($module, $lang, $label, $translabel);
		}
	}
}
?>
<?php
require_once('modules/Update/Update.php');

SDK::clearSessionValues();

require_once('vtlib/Vtiger/Package.php');
$package = new Vtiger_Package();
$package->importByManifest('ProductLines');

// add button to quote
$res = $adb->query("select * from {$table_prefix}_links where linktype = 'DETAILVIEWBASIC' and linklabel = 'ReviewQuote'");
if ($res && $adb->num_rows($res) == 0) {
	$Quotes = Vtiger_Module::getInstance('Quotes');
	Vtiger_Link::addLink($Quotes->id,'DETAILVIEWBASIC','ReviewQuote','javascript:ReviewQuote(\'$RECORD$\')', '');
}


// add status for potentials
$fields = array(
	// get this field
	'sales_stage'		=> array('module'=>'Potentials', 'block'=>'LBL_OPPORTUNITY_INFORMATION', 'name'=>'sales_stage'),

	// create these ones
	'eff_closingdate'	=> array('module'=>'Potentials', 'block'=>'LBL_OPPORTUNITY_INFORMATION', 'name'=>'eff_closingdate', 'label' => 'EffClosingDate', 'columntype'=>'D', 'typeofdata'=>'D~O', 'uitype'=>5),
	'unit_cost'			=> array('module'=>'Products', 'block'=>'LBL_PRICING_INFORMATION', 'name'=>'unit_cost', 'label'=>'UnitCost',   'table'=>"{$table_prefix}_products",	'columntype'=>'N(25.2)', 'typeofdata'=>'N~O',    'uitype'=>71, ),
	'yearly_budget'		=> array('module'=>'ProductLines', 'block'=>'LBL_PRODUCTLINES_INFORMATION', 'name'=>'yearly_budget', 'label'=>'YearlyBudget',   'table'=>"{$table_prefix}_productlines",	'columntype'=>'N(25.2)', 'typeofdata'=>'N~O',    'uitype'=>71),
);
$fieldRet = Update::create_fields($fields);

$fieldRet['sales_stage']->setPicklistValues(array('PotentialOpen', 'PotentialBudgeting'));



// set Budget Report
SDK::setReportFolder('Budget', '');
SDK::setReport('Budget by Product Line', '', 'Budget', 'modules/Potentials/BudgetReportRun.php', 'BudgetReportRun', '');


// translations
$trans = array(
	'Potentials' => array(
		'it_it' => array(
			'LBL_PRODLINES_FOR_QUOTE' => 'Linee di prodotto per il preventivo',
			'PotentialOpen' => 'Aperta',
			'PotentialBudgeting' => 'In preventivazione',
			'EffClosingDate' => 'Data chiusura effettiva',
		),
		'en_us' => array(
			'LBL_PRODLINES_FOR_QUOTE' => 'Product lines for the quote',
			'PotentialOpen' => 'Open',
			'PotentialBudgeting' => 'Budgeting',
			'EffClosingDate' => 'Effective closing date',
		),
	),
	'Products' => array(
		'it_it' => array(
			'UnitCost' => 'Costo unitario',
		),
		'en_us' => array(
			'UnitCost' => 'Unit cost',
		),
	),
	'Reports' => array(
		'it_it' => array(
			'GroupByUser' => 'Raggruppa per utente',
			'ClosedOrders' => 'Ordini chiusi',
		),
		'en_us' => array(
			'GroupByUser' => 'Group by user',
			'ClosedOrders' => 'Closed orders',
		),
	),
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_MARGIN' => 'Margine',
			'LBL_SEMESTER' => 'Semestre',
			'LBL_QUARTER' => 'Quadrimestre',
			'LBL_TRIMESTER' => 'Trimestre',
			'LBL_MONTH_JANUARY' => 'Gennaio',
			'LBL_MONTH_FEBRUARY' => 'Febbraio',
			'LBL_MONTH_MARCH' => 'Marzo',
			'LBL_MONTH_APRIL' => 'Aprile',
			'LBL_MONTH_MAY' => 'Maggio',
			'LBL_MONTH_JUNE' => 'Giugno',
			'LBL_MONTH_JULY' => 'Luglio',
			'LBL_MONTH_AUGUST' => 'Agosto',
			'LBL_MONTH_SEPTEMBER' => 'Settembre',
			'LBL_MONTH_OCTOBER' => 'Ottobre',
			'LBL_MONTH_NOVEMBER' => 'Novembre',
			'LBL_MONTH_DECEMBER' => 'Dicembre',
			'LBL_SECOND' => 'Secondo',
			'LBL_THIRD' => 'Terzo',
			'LBL_FOURTH' => 'Quarto',
		),
		'en_us' => array(
			'LBL_MARGIN' => 'Margin',
			'LBL_SEMESTER' => 'Semester',
			'LBL_QUARTER' => 'Quarter',
			'LBL_TRIMESTER' => 'Trimester',
			'LBL_MONTH_JANUARY' => 'Genuary',
			'LBL_MONTH_FEBRUARY' => 'February',
			'LBL_MONTH_MARCH' => 'March',
			'LBL_MONTH_APRIL' => 'April',
			'LBL_MONTH_MAY' => 'May',
			'LBL_MONTH_JUNE' => 'June',
			'LBL_MONTH_JULY' => 'July',
			'LBL_MONTH_AUGUST' => 'August',
			'LBL_MONTH_SEPTEMBER' => 'September',
			'LBL_MONTH_OCTOBER' => 'October',
			'LBL_MONTH_NOVEMBER' => 'November',
			'LBL_MONTH_DECEMBER' => 'December',
			'LBL_SECOND' => 'Second',
			'LBL_THIRD' => 'Third',
			'LBL_FOURTH' => 'Fourth',
		),
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
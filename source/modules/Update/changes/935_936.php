<?php
$_SESSION['modules_to_update']['Charts'] = 'packages/vte/mandatory/Charts.zip';

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

// add column for main_account
addColumnToTable($table_prefix.'_accpotentialrel', 'main_account', 'I(1)', 'DEFAULT 0');

// fix users missing decimal nums
global $default_decimals_num;
if (empty($default_decimals_num)) $default_decimals_num = 2;
$adb->pquery("update {$table_prefix}_users set decimals_num = ? where decimals_num is null or decimals_num = ''", array($default_decimals_num));


// table for history
$ptable = $table_prefix.'_potential_amounts';
if(!Vtiger_Utils::CheckTable($ptable)) {
	$schema = '<?xml version="1.0"?>
		<schema version="0.3">
			<table name="'.$ptable.'">
				<opt platform="mysql">ENGINE=InnoDB</opt>
				<field name="potentialid" type="I" size="19" />
				<field name="amountdate" type="T">
					<NOTNULL/>
					<DEFAULT value="0000-00-00 00:00:00" />
				</field>
				<field name="amount" type="N" size="14.2" />
				<index name="pot_amounts_id_idx">
					<col>potentialid</col>
				</index>
			</table>
		</schema>';
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema));
}

// add amount history
$res = $adb->query("
select p.potentialid, {$table_prefix}_crmentity.modifiedtime as amountdate, p.amount
from {$table_prefix}_potential p
inner join {$table_prefix}_crmentity on crmid = p.potentialid
left join {$table_prefix}_potential_amounts pa on pa.potentialid = p.potentialid
where pa.potentialid is null");
if ($res) {
	while ($row = $adb->FetchByAssoc($res, -1, false)) {
		$adb->pquery("insert into {$table_prefix}_potential_amounts (potentialid, amountdate, amount) VALUES (?,?,?)", $row);
	}
}

// translations
$trans = array(
	'Potentials' => array(
		'it_it' => array(
			'NoActiveQuotes' => 'Nessun preventivo Attivo trovato',
			'TooManyActiveQuotes' => 'C\'è più di un preventivo attivo',
			'AmountHistory' => 'Storico ammontare',
			'AmountsWithoutTaxes' => 'I valori si intendono al netto di tasse, spese e arrotondamenti',
			'NoProductLineInfo' => 'Il calcolo sulle Linee di Prodotto è stato disattivato poiché le informazioni non sono state inserite correttamente',
		),
		'en_us' => array(
			'NoActiveQuotes' => 'No active quotes found',
			'TooManyActiveQuotes' => 'There is more than one active quote',
			'AmountHistory' => 'Amount history',
			'AmountsWithoutTaxes' => 'Amounts are not calculated with taxes, fares and adjustments',
			'NoProductLineInfo' => 'Summary about Product Lines has been disabled because some informations have not been entered correctly',
		),
		'de_de' => array(
			'NoActiveQuotes' => 'Keine aktiven Angebote gefunden',
			'TooManyActiveQuotes' => 'Es gibt mehr als ein aktives Angebot',
			'AmountHistory' => 'Betragsverlauf',
			'AmountsWithoutTaxes' => 'Die Beträge werden nicht mit Steuern, Tarifen und Bereinigungen berechnet',
			'NoProductLineInfo' => 'Die Zusammenfassung von Produktlinien wurde deaktiviert, weil einige Informationen nicht richtig eingegeben wurden',
		),
	),
	'APP_STRINGS' => array(
		'it_it' => array(
			'Players' => 'Attori',
			'ReviewQuote' => 'Revisiona',
		),
		'en_us' => array(
			'Players' => 'Players',
			'ReviewQuote' => 'Review Quote',
		),
		'de_de' => array(
			'Players' => 'Akteure',
			'ReviewQuote' => 'Angebot überprüfen',
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
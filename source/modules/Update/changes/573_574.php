<?php
set_time_limit(0);

global $adb;

// aggiungo le colonne
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

// colonne per i conteggi


// creo le tabelle
if(!Vtiger_Utils::CheckTable('vte_rep_count_liv1')) {
	$schema_table = '<schema version="0.3">
					  <table name="vte_rep_count_liv1">
					  <opt platform="mysql">ENGINE=InnoDB</opt>
					    <field name="id_liv1" type="I" size="19">
					      <KEY/>
					    </field>
					    <field name="reportid" type="I" size="19">
					      <NOTNULL/>
					    </field>
					    <field name="value_liv1" type="C" size="255"/>
					    <field name="count_liv1" type="I" size="19"/>
					    <field name="formula1_sum" type="N" size="15.3"/>
					    <field name="formula1_avg" type="N" size="15.3"/>
					    <field name="formula1_min" type="N" size="15.3"/>
					    <field name="formula1_max" type="N" size="15.3"/>
					    <index name="vte_rep_count_liv1_idx_1">
					      <col>reportid</col>
					    </index>
					  </table>
					</schema>';
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

if(!Vtiger_Utils::CheckTable('vte_rep_count_liv2')) {
	$schema_table = '<schema version="0.3">
					  <table name="vte_rep_count_liv2">
					  <opt platform="mysql">ENGINE=InnoDB</opt>
					    <field name="id_liv1" type="I" size="19">
					      <NOTNULL/>
					    </field>
					    <field name="id_liv2" type="I" size="19">
					      <KEY/>
					    </field>
					    <field name="value_liv2" type="C" size="255"/>
					    <field name="count_liv2" type="I" size="19"/>
					    <field name="formula2_sum" type="N" size="15.3"/>
					    <field name="formula2_avg" type="N" size="15.3"/>
					    <field name="formula2_min" type="N" size="15.3"/>
					    <field name="formula2_max" type="N" size="15.3"/>
					    <index name="vte_rep_count_liv2_idx_1">
					      <col>id_liv1</col>
					    </index>
					  </table>
					</schema>';
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

if(!Vtiger_Utils::CheckTable('vte_rep_count_liv3')) {
	$schema_table = '<schema version="0.3">
					  <table name="vte_rep_count_liv3">
					  <opt platform="mysql">ENGINE=InnoDB</opt>
					    <field name="id_liv1" type="I" size="19">
					      <KEY/>
					    </field>
					    <field name="id_liv2" type="I" size="19">
					      <KEY/>
					    </field>
					    <field name="value_liv3" type="C" size="255"/>
					    <field name="count_liv3" type="I" size="19"/>
					    <field name="formula3_sum" type="N" size="15.3"/>
					    <field name="formula3_avg" type="N" size="15.3"/>
					    <field name="formula3_min" type="N" size="15.3"/>
					    <field name="formula3_max" type="N" size="15.3"/>
					  </table>
					</schema>';
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

if(!Vtiger_Utils::CheckTable('vte_rep_count_levels')) {
	$schema_table = '<schema version="0.3">
					  <table name="vte_rep_count_levels">
					  <opt platform="mysql">ENGINE=InnoDB</opt>
					  	<field name="reportid" type="I" size="19">
					      <KEY/>
					    </field>
					    <field name="id_liv1" type="I" size="19">
					      <KEY/>
					    </field>
					    <field name="id_liv2" type="I" size="19">
					      <KEY/>
					    </field>
					    <field name="value_liv1" type="C" size="255"/>
					    <field name="count_liv1" type="I" size="19"/>
					    <field name="value_liv2" type="C" size="255"/>
					    <field name="count_liv2" type="I" size="19"/>
					    <field name="value_liv3" type="C" size="255"/>
					    <field name="count_liv3" type="I" size="19"/>
					    <field name="formula1_sum" type="N" size="15.3"/>
					    <field name="formula1_avg" type="N" size="15.3"/>
					    <field name="formula1_min" type="N" size="15.3"/>
					    <field name="formula1_max" type="N" size="15.3"/>
					    <field name="formula2_sum" type="N" size="15.3"/>
					    <field name="formula2_avg" type="N" size="15.3"/>
					    <field name="formula2_min" type="N" size="15.3"/>
					    <field name="formula2_max" type="N" size="15.3"/>
					    <field name="formula3_sum" type="N" size="15.3"/>
					    <field name="formula3_avg" type="N" size="15.3"/>
					    <field name="formula3_min" type="N" size="15.3"/>
					    <field name="formula3_max" type="N" size="15.3"/>
					    <index name="vte_rep_count_levels_idx_1">
					      <col>reportid</col>
					    </index>
					  </table>
					</schema>';
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}


// colonna per abilitare le formule nei riassuntivi
addColumnToTable('vtiger_reportsummary', 'show_summary', 'I(1)', 'DEFAULT 0');
addColumnToTable('vtiger_reportsortcol', 'view_count_lvl', 'I(1)', 'DEFAULT 0');


// un po' di traduzioni
SDK::setLanguageEntries('Reports', 'LBL_REPORT_TOTALS', array('it_it'=>'Totali','en_us'=>'Totals','pt_br'=>'Total'));
SDK::setLanguageEntries('Reports', 'LBL_REPORT_SUMMARY', array('it_it'=>'Riassuntivo','en_us'=>'Summary','pt_br'=>'Resumo'));
SDK::setLanguageEntries('Reports', 'NO_FILTER_SELECTED', array('it_it'=>'Nessun intervallo di tempo selezionato','en_us'=>'No time interval selected','pt_br'=>'Nenhum intervalo de tempo selecionado'));
SDK::setLanguageEntries('Reports', 'TIME_INTERVAL', array('it_it'=>'Intervallo di tempo','en_us'=>'Time interval','pt_br'=>'Intervalo de tempo'));
SDK::setLanguageEntries('Reports', 'LBL_REFRESH', array('it_it'=>'Aggiorna','en_us'=>'Refresh','pt_br'=>'Atualizar'));
SDK::setLanguageEntries('Reports', 'LBL_REPORTING', array('it_it'=>'Filtro per','en_us'=>'Reporting','pt_br'=>'Filtro para'));
SDK::setLanguageEntries('Reports', 'LBL_CHOOSE_EXPORT', array('it_it'=>'Scegli cosa esportare','en_us'=>'Choose what to export','pt_br'=>'Escolha o que para exportar'));
SDK::setLanguageEntries('Reports', 'LBL_CHOOSE_PRINT', array('it_it'=>'Scegli cosa stampare','en_us'=>'Choose what to print','pt_br'=>'Escolha o que para imprimir'));
SDK::setLanguageEntries('Reports', 'LBL_CHOOSE_EMPTY', array('it_it'=>'Scegli almeno un elemento','en_us'=>'Choose at least one element','pt_br'=>'Escolher pelo menos um elemento'));
SDK::setLanguageEntries('Reports', 'LBL_WITH', array('it_it'=>'con','en_us'=>'with','pt_br'=>'com'));
SDK::setLanguageEntries('Reports', 'LBL_SHOW_SUMMARY', array('it_it'=>'Visualizza resoconto','en_us'=>'Show summary','pt_br'=>'Ver resumo'));

// totale dei prodotti
addColumnToTable('vtiger_inventoryproductrel', 'linetotal', 'N(25.3)');

// calculate total for existing entities
//echo "Updating product totals<br />\n";
$taxcolumns = array();
$taxinfo = getAllTaxes('all');
foreach($taxinfo as $tx) {
	if ($tx['deleted'] == 0) {
		$taxcolumns[] = $tx['taxname'];
	}
}

$res = $adb->query('
	select
		vtiger_inventoryproductrel.productid,quantity,listprice,discount_percent,discount_amount,lineitem_id'.((count($taxcolumns)>0)?',':'').implode(',',$taxcolumns).'
	from vtiger_inventoryproductrel
		inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_inventoryproductrel.id
		inner join vtiger_products on vtiger_products.productid = vtiger_inventoryproductrel.productid
		inner join vtiger_crmentity crmprod on crmprod.crmid = vtiger_inventoryproductrel.productid
	where vtiger_crmentity.deleted = 0 and crmprod.deleted = 0 and vtiger_inventoryproductrel.listprice is not null'
);
if ($res && $adb->num_rows($res) > 0) {
	$count = 0;
	while ($row = $adb->FetchByAssoc($res, -1, false)) {
		$total = $row['quantity']*$row['listprice'];
		// sconti
		if (!empty($row['discount_percent'])) {
			$total *= (100.0-$row['discount_percent'])/100.0;
		}
		if (!empty($row['discount_amount'])) {
			$total -= $row['discount_amount'];
		}
		// tasse
		$taxprods = array();
		$rr = $adb->pquery('select taxid,taxpercentage from vtiger_producttaxrel where productid = ?', array($row['productid']));
		if ($rr && $adb->num_rows($rr) > 0) {
			while ($row2 = $adb->FetchByAssoc($rr, -1, false)) {
				$taxprods['tax'.$row2['taxid']] = $row2['tax_percentage'];
			}
		}
		foreach ($taxcolumns as $taxname) {
			$taxratio = 0;
			if (array_key_exists($taxname, $taxprods)) {
				if (!empty($row[$taxname])) {
					$taxratio = $row[$taxname];
				} else {
					$taxratio = $taxprods[$taxname]; // tasse associate al prodotto
				}
			} else {
				//$taxratio = $row[$taxname]; // tasse standard
			}
			if (!empty($taxratio)) {
				$total *= (100.0+$taxratio)/100.0;
			}
		}
		$rr = $adb->pquery('update vtiger_inventoryproductrel set linetotal = ? where lineitem_id = ?', array($total, $row['lineitem_id']));
		++$count;
	}
	//echo "Updated $count product totals<br />\n";
}
?>
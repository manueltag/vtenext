<?php
/* crmv@OPER6288 - Kanban view */
$tablename = "{$table_prefix}_kanbanview";
$schema_table =
'<schema version="0.3">
	<table name="'.$tablename.'">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="cvid" type="I" size="19">
			<KEY/>
		</field>
		<field name="json" type="X" />
		<field name="relation_id" type="I" size="19"/>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($tablename)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}
require_once('include/utils/KanbanView.php');
$kanbanLib = KanbanLib::getInstance();
$kanbanLib->populateDefault();

// translations
$trans = array(
	'ALERT_ARR' => array(
		'it_it' => array(
			'LBL_LABEL'=>'Etichetta',
			'LBL_KANBAN_DRAG_HERE'=>'Abilita drag here',
			'LBL_PM_NO_ENTITY_SELECTED'=>'Nessuna entità selezionata',
			'LBL_PM_NO_CHECK_SELECTED'=>'Nessun controllo impostato',
		),
		'en_us' => array(
			'LBL_LABEL'=>'Label',
			'LBL_KANBAN_DRAG_HERE'=>'Enable drag here',
			'LBL_PM_NO_ENTITY_SELECTED'=>'No entity selected',
			'LBL_PM_NO_CHECK_SELECTED'=>'No check set',
		),
	),
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_KANBAN_NOT_AVAILABLE'=>'La vista kanban non è configurata per questo filtro',
			'LBL_KANBAN_DRAG_DISABLED'=>'Drag here disabilitato per questa colonna',
		),
		'en_us' => array(
			'LBL_KANBAN_SETTINGS'=>'Kanban view is not configured for this view',
			'LBL_KANBAN_DRAG_DISABLED'=>'Drag here disabled for this column',
		),
	),
	'CustomView' => array(
		'it_it' => array(
			'LBL_KANBAN_SETTINGS'=>'Configurazione Kanban',
			'LBL_KANBAN_COLUMNS'=>'Colonne',
			'LBL_KANBAN_ADD_COLUMN'=>'Aggiungi colonna',
			'LBL_KANBAN_ADD_DRAG_ACTION'=>'Aggiungi azione',
		),
		'en_us' => array(
			'LBL_KANBAN_SETTINGS'=>'Kanban settings',
			'LBL_KANBAN_ADD_COLUMN'=>'Columns',
			'LBL_KANBAN_ADD_COLUMN'=>'Add column',
			'LBL_KANBAN_ADD_DRAG_ACTION'=>'Add action',
		),
	),
	'Settings' => array(
		'it_it' => array(
			'LBL_PM_GATEWAY_NO_CONDITIONS'=>'Nessuna condizione configurata al passo precedente.',
		),
		'en_us' => array(
			'LBL_PM_GATEWAY_NO_CONDITIONS'=>'No conditions configured in the previous step.',
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
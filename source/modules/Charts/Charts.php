<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');
require_once('modules/PickList/PickListUtils.php');
require_once('modules/Reports/ReportRun.php');

/* crmv@82770 - support for ChartJS */
/* crmv@83040 - support for drill-down */

// pChart classes
require_once('include/pChart/class/pData.class.php');
require_once('include/pChart/class/pDraw.class.php');
require_once('include/pChart/class/pImage.class.php');
require_once('include/pChart/class/pPie.class.php');
require_once('include/pChart/class/pSplit.class.php');
require_once('include/pChart/class/pScatter.class.php'); // crmv@53923

class Charts extends CRMEntity {
	
	public $db, $log; // Used in class functions of CRMEntity

	public $table_name = 'vte_charts';
	public $table_index= 'chartid';
	public $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = Array('vte_chartscf', 'chartid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = Array('vte_crmentity', 'vte_charts', 'vte_chartscf', 'vte_chartscache');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = Array(
		'vte_crmentity' => 'crmid',
		'vte_charts'   => 'chartid',
	    'vte_chartscf' => 'chartid',
		'vte_chartscache' => 'chartid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = Array (
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vte_'
		'Chart Name'=> Array('charts', 'chartname'),
		'Assigned To' => Array('crmentity','smownerid')
	);

	public $list_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Chart Name'=> 'chartname',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'chartname';

	// For Popup listview and UI type support
	public $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Chart Name'=> Array('charts', 'chartname')
	);

	public $search_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Chart Name'=> 'chartname'
	);

	// For Popup window record selection
	public $popup_fields = Array('chartname');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = Array();

	// For Alphabetical search
	public $def_basicsearch_col = 'chartname';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'chartname';

	// Required Information for enabling Import feature
	public $required_fields = Array('chartname'=>1);

	// Callback function list during Importing
	//public $special_functions = Array('set_import_assigned_user');

	public $default_order_by = 'chartname';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = Array('createdtime', 'modifiedtime', 'chartname');
	//crmv@10759
	public $search_base_field = 'chartname';
	//crmv@10759 e


	public $chartLibrary = 'ChartJS';	// can be pChart or ChartJS. pChart is currently deprecated and will be removed in the future
	public $cachedir = 'cache/charts/';
	public $cachefile_date = null;
	public $image_size_x = 512;
	public $image_size_y = 384;
	public $graph_size_ratio = 3.2; // pie radius (=imagesize/ratio)
	public $thumbnail_size = 160;
	public $limit_data = 50; // in caso non si usi il merge, limito i dati
	public $filtered_data = false; // prende i dati dalle tabelle con i dati filtrati // crmv@31209
	public $label_split = 12; // split labels longer than 20chars // crmv@30976
	public $merge_threshold = 3; // percentuale al di sotto della quale accorpare gli spicchi piccoli
	public $homestuffid = null;
	public $cachefield = 'chart_filename';


	public $default_options = array(
		// general options
		'Main' => array(
			'TitleHeight' => 0, 		// altezza della barra del titolo // TODO: remove me
			'Padding' => 0,		 		//
			'PaddingLeft' => 0,			// This padding is added only to the left
			'PaddingBottom' => 0,		// This padding is added only at the bottom
			'GraphShadow' => true,
			'ShowLegend' => false,
			'StretchPalette' => false,	// stretch palette to fit the number of values
		),

		'Scale' => array(
			'ScaleSpacing' => 1,
		),

		'Legend' => array(
			'Mode' => LEGEND_VERTICAL,
			'Style' => LEGEND_ROUND,
			'R' => 0xE0, 'G' => 0xE0, 'B' => 0xE0, "Alpha" => 80,
			'BorderR' => 0xD0, 'BorderG' => 0xD0, 'BorderB' => 0xD0,
			'FontName' => 'include/pChart/fonts/MYRIADPRO-REGULAR.OTF',
			'FontSize' => 10,
			//'Family' => LEGEND_FAMILY_CIRCLE,
			'Position' => TEXT_ALIGN_TOPLEFT, // not a Pchart option!!, valid values: same as drawText format, only starting with LEFT
		),

		// specific options for graph types
		'BarVertical' => array(
			'DisplayOrientation' => ORIENTATION_VERTICAL,
			'DisplayValues' => TRUE,
			'Rounded' => TRUE,
			'Surrounding' => -20,
			'DisplayPos' => LABEL_POS_OUTSIDE,
			'RecordImageMap' => TRUE,
			'CyclePalette' => TRUE,
			//'Gradient' => TRUE, // only if rounded false
		),
		'BarHorizontal' => array(
			'DisplayOrientation' => ORIENTATION_HORIZONTAL,
			'DisplayValues' => TRUE,
			'Rounded' => TRUE,
			'Surrounding' => -20,
			'RecordImageMap' => TRUE,
			'DisplayPos' => LABEL_POS_OUTSIDE,
			'CyclePalette' => TRUE,
		),
		'Line' => array(
			'LineWeight' => 1, // not a pChart option // crmv@53923
			'DisplayValues' => TRUE,
			'RecordImageMap' => TRUE,
		),
		'Pie' => array(
			'DrawLabels'=>TRUE,
			'DrawLabelLine' => FALSE,
			'DrawLabelArrow' => TRUE,
			'LabelDistance' => 20,
			'LabelArrowSize' => 6,
			'SecondPass' => TRUE,
			'WriteValues' => FALSE,
			'ValueR' => 0x40, 'ValueG' => 0x40, 'ValueB' => 0x40,
			'Border' => TRUE,
			'BorderR' => 0xF0, 'BorderG' => 0xF0, 'BorderB' => 0xF0,
			//'LabelStacked' => TRUE,
			'LabelColor' => PIE_LABEL_COLOR_MANUAL,
			'LabelR' => 0x70, 'LabelG' => 0x70, 'LabelB' => 0x70,
			'RecordImageMap' => TRUE,
			'CyclePalette' => TRUE, // if false, use random colors
		),
		// DEPRECATED
		/*'Pie3D' => array(
			'DrawLabels'=>TRUE,
			'DrawLabelLine' => FALSE,
			'DrawLabelArrow' => TRUE,
			'LabelDistance' => 20,
			'LabelArrowSize' => 6,
			//'LabelStacked' => TRUE,
			'LabelColor' => PIE_LABEL_COLOR_MANUAL,
			'LabelR' => 0x40, 'LabelG' => 0x40, 'LabelB' => 0x40,
			'SecondPass' => TRUE,
			'WriteValues' => FALSE,
			'ValueR' => 0x40, 'ValueG' => 0x40, 'ValueB' => 0x40,
			//'ValuePadding' => 0,
			'Border' => TRUE,
			'BorderR' => 0xD0, 'BorderG' => 0xD0, 'BorderB' => 0xD0,
			'RecordImageMap' => TRUE,
			'CyclePalette' => TRUE,
		),
		*/
		'Ring' => array(
			'DrawLabels'=>TRUE,
			'DrawLabelLine' => FALSE,
			'DrawLabelArrow' => TRUE,
			'LabelDistance' => 20,
			'LabelArrowSize' => 6,
			'LabelColor' => PIE_LABEL_COLOR_MANUAL,
			'LabelR' => 0x70, 'LabelG' => 0x70, 'LabelB' => 0x70,
			'WriteValues' => FALSE,
			'ValueR' => 0x40, 'ValueG' => 0x40, 'ValueB' => 0x40,
			'Border' => TRUE,
			'BorderR' => 0xD0, 'BorderG' => 0xD0, 'BorderB' => 0xD0,
			'RecordImageMap' => TRUE,
			'CyclePalette' => TRUE,
		),
		// DEPRECATED
		/*
		'Ring3D' => array(
			'DrawLabels'=>TRUE,
			'DrawLabelLine' => FALSE,
			'DrawLabelArrow' => TRUE,
			'LabelDistance' => 20,
			'LabelArrowSize' => 6,
			'LabelColor' => PIE_LABEL_COLOR_MANUAL,
			'LabelR' => 0x40, 'LabelG' => 0x40, 'LabelB' => 0x40,
			'WriteValues' => FALSE,
			'ValueR' => 0x40, 'ValueG' => 0x40, 'ValueB' => 0x40,
			'DataGapAngle' => 0,
			'DataGapRadius' => 0,
			'RecordImageMap' => TRUE,
			'CyclePalette' => TRUE,
		),
		*/
		'Split' => array(
			'TextPos' => TEXT_POS_RIGHT,
			'TextPadding'=>10,
			'Spacing'=>20,
			'Surrounding'=>40,
			'RecordImageMap' => TRUE,
			'CyclePalette' => TRUE,
		),
		// crmv@53923
		'Scatter' => array(
		)
		// crmv@53923e

	);
	
	public $default_options_js = array(
		// general options
		'Main' => array(
			'chartjs' => array(
				'animation' => true,
				'tooltipFontSize' => 12,
				'legend' => false,
				//'tooltipTemplate' => "<%if (label){%><%=label%>: <%}%><%= value %> (<%= Math.round(circumference / 6.283 * 100) %>%)", // TODO: find out how to put the percentage

			),
		),
		'BarVertical' => array(
			'chartjs' => array(
				'barStrokeWidth' => 1,
				// legend is disabled for bars,since there is only one serie
				//'legendTemplate' => "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span class=\"legend-box\" style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
				'tooltipTemplate' => "<%=label%>###<%=value%>", // this is handled by javascript
			),
		),
		'BarHorizontal' => array(
			'chartjs' => array(
				'barStrokeWidth' => 1,
				//'legendTemplate' => "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span class=\"legend-box\" style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
				'tooltipTemplate' => "<%=label%>###<%=value%>", // this is handled by javascript
			),
		),
		'Pie' => array(
			'chartjs' => array(
				'legendTemplate' => "<span class=\"legend-title hidden\"></span><ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span class=\"legend-box\" style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
				'tooltipTemplate' => "<%if (label){%><%=label%>: <%}%><%= formatUserNumber(value) %> (<%= Math.round(circumference / 6.283 * 100) %>%)",
			),
		),
		'Ring' => array(
			'chartjs' => array(
				'legendTemplate' => "<span class=\"legend-title hidden\"></span><ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span class=\"legend-box\" style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
				'tooltipTemplate' => "<%if (label){%><%=label%>: <%}%><%= formatUserNumber(value) %> (<%= Math.round(circumference / 6.283 * 100) %>%)",
			),
		),
		'Line' => array(
			'chartjs' => array(
				'tooltipTemplate' => "<%=label%>###<%=value%>", // this is handled by javascript
			),
		),
	);

	function __construct() {
		global $log, $table_prefix; // crmv@97862
		parent::__construct(); // crmv@37004
		
		$this->column_fields = getColumnFields('Charts'); // crmv@97862
		$this->db = PearDatabase::getInstance();
		$this->log = $log;

		$this->table_name = $table_prefix.'_charts';
		$this->customFieldTable = Array($table_prefix.'_chartscf', 'chartid');
		$this->tab_name = Array($table_prefix.'_crmentity', $table_prefix.'_charts', $table_prefix.'_chartscf', $table_prefix.'_chartscache');
		$this->tab_name_index = Array(
			$table_prefix.'_crmentity' => 'crmid',
			$table_prefix.'_charts'   => 'chartid',
			$table_prefix.'_chartscf' => 'chartid',
			$table_prefix.'_chartscache' => 'chartid',
		);

	}


	function save_module($module) {
		global $adb;

		// invalidate filename
		$this->invalidateCache();
	}

	// crmv@113417
	function retrieve_entity_info($recordid, $module, $dieOnError=true, $onlyFields = array()) {
		$ret = parent::retrieve_entity_info($recordid, $module, $dieOnError, $onlyFields);

		// get cache file date
		$fname = $this->column_fields[$this->cachefield];
		$this->cachefile_date[$this->cachefield] = null;
		if (!empty($fname) && is_readable($fname)) {
			$this->cachefile_date[$this->cachefield] = filemtime($fname);
		}

		return $ret;
	}
	// crmv@113417e

	/**
	 * Return query to use based on given modulename, fieldname
	 * Useful to handle specific case handling for Popup
	 */
	function getQueryByModuleField($module, $fieldname, $srcrecord) {
		// $srcrecord could be empty
	}

	function getQueryExtraJoin() {
		global $table_prefix;
		return "LEFT JOIN {$table_prefix}_report ON {$table_prefix}_charts.reportid = {$table_prefix}_report.reportid";
	}

	function getQueryExtraWhere() {
		global $table_prefix, $current_user;

		$repquery = '';
		if ($current_user->is_admin != 'on') {
			$userGroups = new GetUserGroups();
			$userGroups->getAllUserGroups($current_user->id);
			$user_groups = $userGroups->user_groups;

			$groupquery = '';
			if (!empty($user_groups)) {
				$groupquery = "OR (setype = 'groups' AND shareid in (".implode(',', $user_groups)."))";
			}

			$repquery = " AND (
				({$table_prefix}_report.sharingtype = 'Public') OR
				({$table_prefix}_report.sharingtype = 'Private' AND {$table_prefix}_report.owner = '{$current_user->id}') OR
				({$table_prefix}_report.sharingtype = 'Shared' AND EXISTS (
					SELECT reportid FROM {$table_prefix}_reportsharing WHERE reportid = {$table_prefix}_charts.reportid AND ((setype = 'users' AND shareid = '{$current_user->id}') $groupquery)
				))
			)";
		}

		// crmv@30967
		$fldid = intval($_REQUEST['folderid']);
		if ($fldid > 0) $repquery .= " and {$this->table_name}.folderid = '$fldid'";
		// crmv@30967e

		return $repquery;
	}

	// set the field that holds the filename of the cached file
	function setCacheField($fieldcache = 'chart_filename') {
		if (!empty($fieldcache)) {
			$this->cachefield = $fieldcache;
			$fname = $this->column_fields[$this->cachefield];
			if (!empty($fname) && is_readable($fname)) {
				$this->cachefile_date[$this->cachefield] = filemtime($fname);
			}
		}
	}

	// genera il nome del file in cui scrivere il grafico
	function generateFileName($format = 'png') {


		if (!is_dir($this->cachedir)) {
			@mkdir($this->cachedir, 0755, true);
		}

		if (!is_writable($this->cachedir)) {
			//throw new Exception("Directory $basedir is not writable.");
			return null;
		}

		$randval = uniqid().mt_rand(0, 1000);

		return $this->cachedir."chart_{$randval}.$format";
	}
	
	function initGraphOptions(&$DataSet) {
		if ($this->chartLibrary == 'pChart') {
			return $this->initGraphOptionsPChart($DataSet);
		} elseif ($this->chartLibrary == 'ChartJS') {
			return $this->initGraphOptionsChartJS($DataSet);
		}
	}
	
	function initGraphOptionsChartJS(&$rawdata) {
		$type = $this->column_fields['chart_type'];
		if (empty($type)) return null;
		
		if ($this->column_fields['chart_legend'] == 1) {
			if ($type == 'Pie' || $type == 'Ring') {
				$this->default_options_js['Main']['chartjs']['legend'] = true;
			}
		}
		
		if ($this->column_fields['chart_values'] != 'ChartValuesNone') {
			if ($type == 'Pie' || $type == 'Ring') {
				$this->default_options_js['Main']['chartjs']['drawLabels'] = true;
				$this->default_options_js['Main']['chartjs']['labelType'] = $this->column_fields['chart_values'];
			}
		}
		
		if ($this->column_fields['chart_labels'] == 1) {
			if ($type == 'Pie' || $type == 'Ring') {
				$this->default_options_js['Main']['chartjs']['drawLabels'] = true;
				$this->default_options_js['Main']['chartjs']['labelType'] = 'ChartLabels';
			}
		}
		
	}

	// change default options according to the graph type
	function initGraphOptionsPChart(&$DataSet) { // crmv@53923

		$type = $this->column_fields['chart_type'];
		if (empty($type)) return null;
		$explode = $this->column_fields['chart_exploded'];
		$dataMin = $DataSet->getMin('Serie1'); // crmv@41431
		$dataMax = $DataSet->getMax('Serie1'); //crmv@73193
		switch ($type) {
			case 'BarVertical':
				$this->default_options['Scale']['Pos'] = SCALE_POS_LEFTRIGHT;
				//crmv@73193
				//if ($dataMin >= 0) $this->default_options['Scale']['Mode'] = SCALE_MODE_START0; // crmv@41431
				if ($dataMin >= 0){
					$this->default_options['Scale']['Mode'] = SCALE_MODE_MANUAL;
					$min = 0;
					$max = round((floatval($dataMax)*110/100),0, PHP_ROUND_HALF_UP); //add 10% to max value to avoid cutting labels
					$AxisBoundaries = array(0=>array("Min"=>$min,"Max"=>$max));
					$this->default_options['Scale']['ManualScale']=$AxisBoundaries;
				}
				//crmv@73193e
				$this->default_options[$type]['OverrideColors'] = $DataSet->getPalette();
				// crmv@53923 - calculate the bottom padding
				$values = $DataSet->getValues('Labels');
				if (is_array($values)) {
					$maxNewLine = 0;
					foreach ($values as $v) {
						$c = substr_count($v, "\n");
						if ($c > $maxNewLine) $maxNewLine = $c;
					}
					$this->default_options['Main']["PaddingBottom"] = 18*($maxNewLine+1);
				}
				// crmv@53923e
				break;
			case 'BarHorizontal':
				$this->default_options['Scale']['Pos'] = SCALE_POS_TOPBOTTOM;
				if ($dataMin >= 0) $this->default_options['Scale']['Mode'] = SCALE_MODE_START0; // crmv@41431
				$this->default_options[$type]['OverrideColors'] = $DataSet->getPalette();
				$this->default_options['Main']["PaddingLeft"] = 80;
				break;
			case 'Line':
				// crmv@53923
				$dataMax = $DataSet->getMax('Serie1');
				$dataMaxLen = strlen(strval(intval($dataMax)));
				$this->default_options['Scale']["DrawSubTicks"] = TRUE;
				$this->default_options['Main']["Padding"] = 20;
				$this->default_options['Main']["PaddingLeft"] = 5*$dataMaxLen;
				// crmv@53923e
				break;
			case 'Pie':
				$this->default_options[$type]['Radius'] = min($this->image_size_x, $this->image_size_y)/$this->graph_size_ratio;
				if ($explode) {
					$this->default_options[$type]['DataGapAngle'] = 8;
					$this->default_options[$type]['DataGapRadius'] = 10;
				}
				break;
			// DEPRECATED
			/*case 'Pie3D':
				$this->default_options[$type]['Radius'] = min($this->image_size_x, $this->image_size_y)/$this->graph_size_ratio;
				if ($explode) {
					$this->default_options[$type]['DataGapAngle'] = 8;
					$this->default_options[$type]['DataGapRadius'] = 10;
				}
				break;
			*/
			case 'Ring':
				$this->default_options[$type]['InnerRadius'] = min($this->image_size_x, $this->image_size_y)/(2*$this->graph_size_ratio);
				$this->default_options[$type]['OuterRadius'] = min($this->image_size_x, $this->image_size_y)/$this->graph_size_ratio;
				if ($explode) {
					$this->default_options[$type]['DataGapAngle'] = 8;
					$this->default_options[$type]['DataGapRadius'] = 10;
				}
				break;
			// DEPRECATED
			/*case 'Ring3D':
				$this->default_options[$type]['InnerRadius'] = min($this->image_size_x, $this->image_size_y)/(2*$this->graph_size_ratio);
				$this->default_options[$type]['OuterRadius'] = min($this->image_size_x, $this->image_size_y)/$this->graph_size_ratio;
				if ($explode) {
					$this->default_options[$type]['DataGapAngle'] = 8;
					$this->default_options[$type]['DataGapRadius'] = 10;
				}
				break;
			*/
			// DEPRECATED
			/*case 'Split':
				$this->default_options['Main']['GraphShadow']= false;
				break;
			*/
			// crmv@53923 - EXPERIMENTAL
			case 'Scatter':
				$DataSet->setAxisXY(0,AXIS_X);
				$DataSet->setAxisPosition(0,AXIS_POSITION_BOTTOM);
				$DataSet->setSerieOnAxis("Serie1",1);
				$DataSet->setAxisXY(1,AXIS_Y);
				$DataSet->setAxisPosition(1,AXIS_POSITION_LEFT);
				$DataSet->setScatterSerie('Labels', 'Serie1', 0);
				$dataMax = $DataSet->getMax('Serie1');
				$dataMaxLen = strlen(strval(intval($dataMax)));
				$this->default_options['Main']["Padding"] = 20;
				$this->default_options['Main']["PaddingLeft"] = 5*$dataMaxLen;
				break;
			// crmv@53923e
		}

		if ($this->column_fields['chart_legend'] == 1) {
			$this->default_options['Main']['ShowLegend'] = true;
		}

		if ($this->column_fields['chart_labels'] == 0) {
			$this->default_options[$type]['DrawLabels'] = FALSE;
		}

		switch ($this->column_fields['chart_values']) {
			case 'ChartValuesRaw':
				$this->default_options[$type]['WriteValues'] = PIE_VALUE_NATURAL;
				$this->default_options[$type]['ValuePosition'] = PIE_VALUE_INSIDE;
				break;
			case 'ChartValuesPercent':
				$this->default_options[$type]['WriteValues'] = PIE_VALUE_PERCENTAGE;
				$this->default_options[$type]['ValuePosition'] = PIE_VALUE_INSIDE;
				break;
			case 'ChartValuesNone':
			default:
				$this->default_options[$type]['WriteValues'] = FALSE;
				$this->default_options[$type]['ValuePosition'] = PIE_VALUE_INSIDE;
				break;
		}
	}
	
	protected function getValueColumn($level = 1) {
		// crmv@30976
		$formula = $this->column_fields['chart_formula'];
		switch ($formula) {
			default:
			case 'COUNT':
				$formulacol = "count_liv{$level}";
				break;
			case 'SUM':
				$formulacol = "formula{$level}_sum";
				break;
			case 'AVG':
				$formulacol = "formula{$level}_avg";
				break;
			case 'MIN':
				$formulacol = "formula{$level}_min";
				break;
			case 'MAX':
				$formulacol = "formula{$level}_max";
				break;
		}
		// crmv@30976e
		return $formulacol;
	}

	// recupera i dati per disegnare il grafico
	function getChartData($level = 1, $levelIds = array()) {
		global $adb, $table_prefix, $current_user; // crmv@97862

		$level = intval($level) ?: 1;
		$ret = array();

		$reportid = $this->column_fields['reportid'];
		if (empty($reportid)) return null;

		$formulacol = $this->getValueColumn($level);

		// crmv@31209
		$datatable = 'vte_rep_count_levels';
		if ($this->filtered_data) $datatable = "vte_rep_count_liv{$level}";
		// crmv@31209e

		// TODO: controlla se report ha i conteggi
		// TODO: limite di dati presi
		// TODO: check query for Oracle and MsSQL

		// crmv@104070
		// basic check to see if there are rows
		$res = $adb->limitPquery("select reportid FROM vte_rep_count_levels where reportid = ? and userid = ?", 0, 1, array($reportid, $current_user->id));
		if ($res && $adb->num_rows($res) == 0) {
			// no rows, maybe it has never run!
			$this->reloadReport();
		}
		// crmv@104070e

		// info generali sul report
		$res = $adb->pquery("select reportname from {$table_prefix}_report where reportid = ?", array($reportid));
		if ($res) {
			$ret['reportname'] = $adb->query_result_no_html($res, 0, 'reportname');
			$this->chart_title = $ret['reportname'] . (empty($this->column_fields['chartname']) ? '' : (' - '.$this->column_fields['chartname']) );
		}
		
		$params = array($reportid, $current_user->id); // crmv@97862
		$subwhere = '';
		
		// conditions for sub levels
		if ($level > 1 && is_array($levelIds) && count($levelIds) > 0) {
			$i = 0;
			$levelIds = array_values($levelIds);
			for ($slevel=1; $slevel<$level; ++$slevel) {
				$levelid = $levelIds[$i++];
				$subwhere .= " AND id_liv{$slevel} = ?";
				$params[] = $levelid;
			}
		}

		// ordino per conteggio
		if ($this->column_fields['chart_order_data'] == 'OrderAsc') {
			$orderby = "ORDER BY count_liv{$level} ASC";
		} elseif ($this->column_fields['chart_order_data'] == 'OrderDesc') {
			$orderby = "ORDER BY count_liv{$level} DESC";
		} else {
			$orderby = '';
		}

		$total = 0;
		$total_rows = 0;
		// limito i valori nel caso non abbia il merge attivo
		if ($this->column_fields['chart_merge_small']) {
			$res = $adb->pquery("SELECT value_liv{$level} AS valname, $formulacol AS val, id_liv{$level} AS dataid FROM $datatable WHERE reportid = ? AND userid = ? $subwhere GROUP BY id_liv{$level} ".$orderby, $params); // crmv@30976 crmv@31209 crmv@97862
			if ($res) $total_rows = $adb->num_rows($res);
		} else {
			$rescount = $adb->pquery("SELECT count(*) rcount FROM vte_rep_count_levels WHERE reportid = ? AND userid = ? $subwhere", $params); // crmv@97862
			if ($rescount) $total_rows = $adb->query_result($rescount, 0, 'rcount');
			unset($rescount);
			$res = $adb->limitpQuery("SELECT value_liv{$level} AS valname, $formulacol AS val, id_liv{$level} AS dataid FROM $datatable WHERE reportid = ? AND userid = ? $subwhere GROUP BY id_liv{$level} ".$orderby, 0, $this->limit_data, $params); // crmv@30976 crmv@31209 crmv@97862
		}

		if ($res) {
			// crmv@109353
			while ($row = $adb->FetchByAssoc($res, -1, false)) {
				$value = $row['val'];
				$label = $row['valname'];
				// check/convert date fields
				if (trim($label) === '') {
					$label = getTranslatedString('LBL_EMPTY_LABEL', 'Charts');
				} elseif (preg_match('/^[12][0-9]{3}-[01][0-9]-[0123][0-9]$/', $label)) {
					$label = getDisplayDate($label);
				}
				// crmv@30976
				if ($this->label_split > 0) {
					$label = wordwrap($label, $this->label_split);
				}
				// crmv@30976e
				$ret['values'][] = $value;
				$ret['labels'][] = $label;
				$ret['dataids'][] = $row['dataid'];
				$total += $value;
			}
			// crmv@109353e
		}

		// controllo se ho limitato i risultati
		$ret['limited'] = ($total_rows != count($ret['values']));

		$this->mergeSmallData($ret, $total);
		
		if (is_array($ret['values'])) $ret['values'] = array_values($ret['values']);
		if (is_array($ret['labels'])) $ret['labels'] = array_values($ret['labels']);
		if (is_array($ret['dataids'])) $ret['dataids'] = array_values($ret['dataids']);

		return $ret;
	}
	
	public function getLevelIdsValues($levelIds) { // crmv@99131
		global $adb;
		
		$values = array();
		
		$level = count($levelIds)+1;
		$reportid = $this->column_fields['reportid'];
		$formulacol = $this->getValueColumn($level);
		
		$params = array($reportid);
		$subwhere = '';
		if ($level > 1) {
			$i = 0;
			$levelIds = array_values($levelIds);
			for ($slevel=1; $slevel<$level; ++$slevel) {
				$levelid = $levelIds[$i++];
				$subwhere .= " AND id_liv{$slevel} = ?";
				$params[] = $levelid;
			}
			$res = $adb->limitpQuery("SELECT * FROM vte_rep_count_levels WHERE reportid = ? $subwhere ", 0, 1, $params); // crmv@30976 crmv@31209
			if ($res) {
				$row = $adb->FetchByAssoc($res, -1, false);
				for ($slevel=1; $slevel<$level; ++$slevel) {
					$levelid = $row['id_liv'.$slevel];
					$values[$levelid] = array(
						'dataid' => $levelid,
						'label' => trim($row['value_liv'.$slevel]) ?: getTranslatedString('LBL_EMPTY_LABEL', 'Charts'),
						'count' => $row['count_liv'.$slevel],
						'value' => $row[$formulacol],
					);
				}
			}
		}
		
		return $values;
	}
	
	public function getMaxLevels() { // crmv@99131
		global $adb;
		
		$levels = 0;
		$reportid = $this->column_fields['reportid'];
		$res = $adb->limitpQuery("SELECT * FROM vte_rep_count_levels WHERE reportid = ?", 0, 1, array($reportid));
		if ($res) {
			$row = $adb->FetchByAssoc($res, -1, false);
			for ($i=1; $i<=7; ++$i) {
				if (isset($row['id_liv'.$i]) && $row['id_liv'.$i] > 0) {
					++$levels;
				} else {
					break;
				}
			}
		}
		return $levels;
	}
	
	// crmv@99131
	public function getLevelTitles() { // crmv@99131
		global $adb, $table_prefix;
		
		$names = array();
		$reportid = $this->column_fields['reportid'];
		$reports = Reports::getInstance();
		$fields = $reports->getColumns($reportid);
		foreach ($fields as $field) {
			if ($field['group'] == 1 && $field['fieldid'] > 0) {
				$finfo = $reports->getFieldInfoById($field['fieldid']);
				if ($finfo['label']) {
					$names[] = $finfo['label'];
				} else {
					$names[] = getTranslatedString($finfo['fieldlabel'], $finfo['module']);
				}
			}
		}
		return $names;
	}
	// crmv@99131e

	// unisce i dati troppo piccoli
	protected function mergeSmallData(&$ret, $valuetotal) {
		// unisco gli spicchi piccoli (sommo i valori che sono < del 3%)
		if ($valuetotal > 0 && $this->column_fields['chart_merge_small']) {
			$replace_pos = null;
			$replace_val = 0;
			$remove_data = array();
			$i = 0;
			foreach ($ret['values'] as $v) {
				$vpercent = 100.0*$v/$valuetotal;
				if ($vpercent < $this->merge_threshold) {
					$replace_val += $v;
					if (is_null($replace_pos)) {
						$replace_pos = $i;
					} else {
						$remove_data[] = $i;
					}
				}
				++$i;
			}
			if (!is_null($replace_pos) && count($remove_data) > 0) {
				$remove_data = array_reverse($remove_data);
				foreach ($remove_data as $rpos) {
					unset($ret['values'][$rpos]);
					unset($ret['labels'][$rpos]);
					unset($ret['dataids'][$rpos]);
				}
				$ret['values'][$replace_pos] = $replace_val;
				$ret['labels'][$replace_pos] = getTranslatedString('LBL_OTHERS_LABEL', 'Charts')." (".strval(count($remove_data)+1).")";
				$ret['dataids'][$replace_pos] = -1;
			}

		}
	}


	// restituisce un set di dati per le anteprime
	function getChartDemoData() {

		$this->chart_title = 'test';

		$ret = array();
		$ret['values'] = array(5, 7,12, 1,17, 9,27,13,52, 48, 22);
		$ret['labels'] = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11);
		$ret['dataids'] = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11);

		$total = array_sum($ret['values']);

		$this->mergeSmallData($ret, $total);

		if ($this->column_fields['chart_order_data'] == 'OrderAsc') {
			$vlist = array_combine($ret['labels'], $ret['values']);
			asort($vlist);
			$ret['values'] = array_values($vlist);
			$ret['labels'] = array_keys($vlist);
		} elseif ($this->column_fields['chart_order_data'] == 'OrderDesc') {
			$vlist = array_combine($ret['labels'], $ret['values']);
			arsort($vlist);
			$ret['values'] = array_values($vlist);
			$ret['labels'] = array_keys($vlist);
		} else {
			// no sorting
		}
		
		$ret['values'] = array_values($ret['values']);
		$ret['labels'] = array_values($ret['labels']);
		$ret['dataids'] = array_values($ret['dataids']);
		
		return $ret;
	}

	function getChartTitle() {
		if (empty($this->chart_title)) {
			$this->getChartData();
		}
		return $this->chart_title;
	}

	function generateChart($usedemodata = false, $level = 1, $levelIds = array()) {
		if ($this->chartLibrary == 'pChart') {
			return $this->generateChartPChart($usedemodata, $level, $levelIds);
		} elseif ($this->chartLibrary == 'ChartJS') {
			return $this->generateChartJS($usedemodata, $level, $levelIds);
		}
	}
	
	function generateChartJS($usedemodata = false, $level = 1, $levelIds = array()) {
		$type = $this->column_fields['chart_type'];
		if (empty($type)) return null;

		if ($usedemodata)
			$rawdata = $this->getChartDemoData($level, $levelIds);
		else
			$rawdata = $this->getChartData($level, $levelIds);
		if (empty($rawdata) || empty($rawdata['values'])) return null; // crmv@31209
		
		// load palette
		$palfile = $this->column_fields['chart_palette'];
		if (empty($palfile) || !is_readable('modules/Charts/palettes/'.$palfile.'.color')) {
			if (in_array($type, array('BarHorizontal', 'BarVertical', 'Line')))  {
				$palfile = 'vtetheme';
			} else {
				$palfile = 'default';
			}
		}
		$mainPalette = $this->parsePaletteFile("modules/Charts/palettes/$palfile.color");
		$palette = $this->paletteRGB2Css($mainPalette);
		$hpalette = $this->paletteRGB2Css($this->variatePalette($mainPalette, 'lighten', array('percentage'=>10)));
		$spalette = $this->paletteRGB2Css($this->variatePalette($mainPalette, 'darken', array('percentage'=>10)));
		$paletteCount = count($mainPalette);
		
		// add some basic vars
		$rawdata['type'] = $type;
		$rawdata['canvas_width'] = $this->image_size_x;
		$rawdata['canvas_height'] = $this->image_size_y;
		
		$rawdata['level'] = $level;
		$rawdata['levelids'] = $this->getLevelIdsValues($levelIds);
		$rawdata['maxlevels'] = $this->getMaxLevels();
		
		if ($rawdata['maxlevels'] > 1) {
			$rawdata['leveltitles'] = $this->getLevelTitles();
		}
		
		// alter options and other variables
		$this->initGraphOptions($rawdata);
		
		// set chartjs options
		$rawdata['options'] = $this->default_options_js['Main']['chartjs'] ?: array();
		$rawdata['options'] = array_merge_recursive($rawdata['options'], $this->default_options_js[$type]['chartjs'] ?: array());
		
		// reorder data
		if (is_array($rawdata['values'])) {
			$jsValues = array();
			$count = count($rawdata['values']);
			
			if ($type == 'Pie' || $type == 'Ring') {
				foreach ($rawdata['values'] as $k=>$value) {
					$jsValues[] = array(
						'value' => $value,
						'label' => $rawdata['labels'][$k],
						'dataid' => $rawdata['dataids'][$k],
						'color' => $palette[$k % $paletteCount],
						'highlight' => $hpalette[$k % $paletteCount],
					);
				}
				
			} else {
				$k = 0;
				$jsValues['labels'] = $rawdata['labels'];
				$jsValues['datasets'] = array();
				$jsValues['datasets'][] = array(
					'label' => 'Serie1',
					'data' =>  $rawdata['values'],
					'dataids' =>  $rawdata['dataids'],
					'fillColor' => $palette[($type == 'Line' ? 1 : $k) % $paletteCount],
					'strokeColor' => $spalette[$k % $paletteCount],
					'highlightFill' => $hpalette[$k % $paletteCount],
					'highlightStroke' => $palette[$k % $paletteCount],
				);
			}
			unset($rawdata['labels']);
			unset($rawdata['dataids']);
			$rawdata['values'] = $jsValues;
		} else {
			unset($rawdata['labels']);
			unset($rawdata['dataids']);
			$rawdata['values'] = array();
		}

		return $rawdata;
	}

	// genera il grafico e restituisce il nome del file
	function generateChartPChart($usedemodata = false, $level = 1, $levelIds = array()) {
		global $adb, $table_prefix;

		// reuse cached image

		$fname = $this->column_fields[$this->cachefield];
		if (!empty($fname) && is_readable($fname)) {
			$this->map_file = preg_replace('/\.png/', '_map.map', $fname);
			return $fname;
		}

		$fname = $this->generateFileName();
		if (empty($fname)) return null;

		$type = $this->column_fields['chart_type'];
		if (empty($type)) return null;

		if ($usedemodata)
			$rawdata = $this->getChartDemoData($level, $levelIds);
		else
			$rawdata = $this->getChartData($level, $levelIds);
		if (empty($rawdata) || empty($rawdata['values'])) return null; // crmv@31209

		// dataset
		$DataSet = new pData();

		$DataSet->addPoints($rawdata['values'], 'Serie1');
		$DataSet->addPoints($rawdata['labels'], "Labels");
		if ($type == 'Line') {
			$DataSet->setSerieWeight("Serie1", $this->default_options[$type]['LineWeight']);
		}
		$DataSet->setSerieDescription("Labels","Report");
		$DataSet->setAbscissa("Labels");
		
		// crmv@53923
		if ($rawdata['timestamp']) {
			$DataSet->setXAxisDisplay(AXIS_FORMAT_DATE);
		}
		// crmv@53923e

		// load palette
		$palfile = $this->column_fields['chart_palette'];
		if (empty($palfile) || !is_readable('modules/Charts/palettes/'.$palfile.'.color')) {
			$palfile = 'default';
		}
		$DataSet->loadPalette("modules/Charts/palettes/$palfile.color", TRUE);

		$this->initGraphOptions($DataSet);

		if ($this->default_options['Main']['StretchPalette'])
			$DataSet->stretchPalette('Serie1');



		$myPicture = new pImage($this->image_size_x, $this->image_size_y, $DataSet);
		$myPicture->Antialias = TRUE;

		// sfondino con righette
		$imgPadding = $this->default_options['Main']['Padding'];
		$imgPaddingLeft = $this->default_options['Main']['PaddingLeft']; // crmv@53923
		$imgPaddingBottom = $this->default_options['Main']['PaddingBottom']; // crmv@53923

		//$Settings = array("R"=>255, "G"=>255, "B"=>255, "Dash"=>1, "DashR"=>190, "DashG"=>200, "DashB"=>240);
		//$myPicture->drawFilledRectangle($imgPadding,$imgPadding,$this->image_size_x-$imgPadding,$this->image_size_y-$imgPadding,$Settings);

		// sfondo gradient
		//$Settings = array("StartR"=>230, "StartG"=>240, "StartB"=>255, "EndR"=>200, "EndG"=>210, "EndB"=>240, 'Alpha'=>80);
		//$myPicture->drawGradientArea($imgPadding,$imgPadding,$this->image_size_x-$imgPadding,$this->image_size_y-$imgPadding,DIRECTION_VERTICAL, $Settings);

		// bordo
		//$myPicture->drawRectangle(0,0,$this->image_size_x-1,$this->image_size_y-1,array("R"=>200,"G"=>200,"B"=>200));

		// titolo
		//$myPicture->setFontProperties(array("FontName"=>"include/pChart/fonts/VeraBd.ttf","FontSize"=>11));
		//$myPicture->drawGradientArea(0,0,$this->image_size_x,$this->default_options['Main']['TitleHeight'],DIRECTION_VERTICAL,array("StartR"=>255,"StartG"=>255,"StartB"=>255,"EndR"=>200,"EndG"=>200,"EndB"=>200,"Alpha"=>100));
		//$myPicture->drawText(10,$this->default_options['Main']['TitleHeight']/2 - 2,$rawdata['reportname'], array("R"=>0,"G"=>0,"B"=>0, 'Align'=>TEXT_ALIGN_MIDDLE_LEFT));

		// image map
		$mapname = preg_replace('/\.png/', '_map', $fname);
		$this->map_file = $mapname.'.map';
		$myPicture->initialiseImageMap($mapname, IMAGE_MAP_STORAGE_FILE, basename($mapname), $this->cachedir);

		// ombra
		if ($this->default_options['Main']['GraphShadow']) {
			$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>150,"G"=>150,"B"=>150,"Alpha"=>50));
		}

		// grafico
		$myPicture->setFontProperties(array("FontName"=>'include/pChart/fonts/MYRIADPRO-REGULAR.OTF',"FontSize"=>10,"R"=>100,"G"=>100,"B"=>100));
		$myPicture->setGraphArea($imgPadding+$imgPaddingLeft,$imgPadding+$this->default_options['Main']['TitleHeight'], $this->image_size_x-$imgPadding-1, $this->image_size_y-$imgPadding-$imgPaddingBottom-1); // crmv@53923

		// spostamento del centro del grafico
		$shift_x = 0;
		$shift_y = 0;

		// calcolo Y della legenda
		if ($this->default_options['Main']['ShowLegend']) {
			switch ($this->default_options['Legend']['Position']) {
				case TEXT_ALIGN_BOTTOMLEFT:
					$legend_y = $this->image_size_y - $imgPadding - 20;
					$shift_x = 0;
					$shift_y = -20;
					break;
				case TEXT_ALIGN_TOPLEFT:
				default:
					$legend_y = 20+$this->default_options['Main']['TitleHeight'];
					$shift_x = 40;
					$shift_y = 0;
					break;
			}
		}


		switch ($type) {
			case 'BarVertical':
				$myPicture->drawScale($this->default_options['Scale']);
				$myPicture->drawBarChart($this->default_options[$type]);
				break;
			case 'BarHorizontal':
				$myPicture->drawScale($this->default_options['Scale']);
				$myPicture->drawBarChart($this->default_options[$type]);
				break;
			case 'Line':
				$myPicture->drawScale($this->default_options['Scale']);
				$myPicture->drawLineChart($this->default_options[$type]);
				break;
			case 'Pie':
				$PieChart = new pPie($myPicture,$DataSet);
				$PieChart->draw2DPie($this->image_size_x/2+$shift_x, $this->image_size_y/2+$shift_y, $this->default_options[$type]);
				if ($this->default_options['Main']['ShowLegend']) {
					$myPicture->setShadow(FALSE);
					$PieChart->drawPieLegend(15,$legend_y,$this->default_options['Legend']);
				}
				break;
			case 'Pie3D':
				$PieChart = new pPie($myPicture,$DataSet);
				$PieChart->draw3DPie($this->image_size_x/2+$shift_x, $this->image_size_y/2+$shift_y, $this->default_options[$type]);
				if ($this->default_options['Main']['ShowLegend']) {
					$myPicture->setShadow(FALSE);
					$PieChart->drawPieLegend(15,$legend_y,$this->default_options['Legend']);
				}
				break;
			case 'Ring':
				$PieChart = new pPie($myPicture,$DataSet);
				$PieChart->draw2DRing($this->image_size_x/2+$shift_x, $this->image_size_y/2+$shift_y, $this->default_options[$type]);
				if ($this->default_options['Main']['ShowLegend']) {
					$myPicture->setShadow(FALSE);
					$PieChart->drawPieLegend(15,$legend_y,$this->default_options['Legend']);
				}
				break;
			case 'Ring3D':
				$PieChart = new pPie($myPicture,$DataSet);
				$PieChart->draw3DRing($this->image_size_x/2+$shift_x, $this->image_size_y/2+$shift_y, $this->default_options[$type]);
				if ($this->default_options['Main']['ShowLegend']) {
					$myPicture->setShadow(FALSE);
					$PieChart->drawPieLegend(15,$legend_y,$this->default_options['Legend']);
				}
				break;
			// crmv@53923
			case 'Split':
				$SplitChart = new pSplit($myPicture, $DataSet);
				$SplitChart->drawSplitPath($myPicture,$DataSet,$this->default_options[$type]);
				break;
			case 'Scatter':
				/* Create the Scatter chart object - EXPERIMENTAL */
				
				$myPicture->setGraphArea($imgPadding+$imgPaddingLeft,$imgPadding+$this->default_options['Main']['TitleHeight'], $this->image_size_x-$imgPadding-1, $this->image_size_y-$imgPadding-1-20);

				$scatterChart = new pScatter($myPicture,$DataSet);
				$scatterChart->drawScatterScale();
				$scatterChart->drawScatterLineChart(0,0,$this->default_options[$type]);
				if ($this->default_options['Main']['ShowLegend']) {
					$myPicture->setShadow(FALSE);
					$scatterChart->drawScatterLegend(20,20,$this->default_options['Legend']);
				}
				
				break;
			// crmv@53923e
		}

		if ($this->default_options['Main']['ShowLegend']) {

		}

		// aggiungo warning su dati limitati
		if ($rawdata['limited']) {
			switch ($type) {
				case 'BarVertical':
					$myPicture->drawText($this->image_size_x-10,10, getTranslatedString('LBL_PARTIAL_DATA', 'Charts'), array("R"=>200,"G"=>0,"B"=>0, 'Align'=>TEXT_ALIGN_TOPRIGHT));
					break;
				default:
					$myPicture->drawText($this->image_size_x-10,$this->image_size_y-10, getTranslatedString('LBL_PARTIAL_DATA', 'Charts'), array("R"=>200,"G"=>0,"B"=>0, 'Align'=>TEXT_ALIGN_BOTTOMRIGHT));
					break;
			}
		}

		// render image
		$myPicture->Render($fname);
		$this->cachefile_date[$this->cachefield] = filemtime($fname);

		// anteprima
		//$this->createThumbnail($fname);

		// save it into database
		$chartid = intval($this->column_fields["record_id"]);
		if ($chartid > 0) {
			$adb->pquery("update {$table_prefix}_chartscache set {$this->cachefield} = ? where {$this->table_index} = ?", array($fname, $chartid));
		}

		return $fname;
	}

	function getMapData() {
		$mapfile = $this->map_file;

		if (empty($mapfile) || !is_readable($mapfile)) return array();

		$ret = '';
		$Handle = @fopen($mapfile, "r");
		if ($Handle) {
			while (($Buffer = fgets($Handle, 4096)) !== false) {
				$ret .= $Buffer;
			}
			@fclose($Handle);
		}

		// now parse it
		$retzones = array();
		$zones = explode("\r\n", $ret);
		foreach ($zones as $zdata) {
			$zinfo = explode(IMAGE_MAP_DELIMITER, $zdata);
			if (empty($zinfo[0]) || empty($zinfo[1])) continue;
			$retzones[] = array(
				'shape'=>$zinfo[0],
				'coords'=>$zinfo[1],
				'color'=>$zinfo[2],
				'label'=>$zinfo[3],
				'value'=>formatUserNumber(floatval($zinfo[4])), // crmv@92350
				'percent'=>$zinfo[5],
			);

		}

		return $retzones;
	}

	// ricalcola il report
	// crmv@97862
	function reloadReport() {
		global $adb, $table_prefix;

		$reportid = $this->column_fields['reportid'];
		if ($reportid > 0) {
			$folderid = getSingleFieldValue($table_prefix.'_report', 'folderid', 'reportid', $reportid);
			$sdkrep = SDK::getReport($reportid, $folderid);
			if (!is_null($sdkrep)) {
				require_once($sdkrep['reportrun']);
				$oReportRun = new $sdkrep['runclass']($reportid);
			} else {
				$oReportRun = ReportRun::getInstance($reportid);
			}

			$oReportRun->setReportTab('COUNT');
			$oReportRun->setOutputFormat('NULL');
			$oReportRun->GenerateReport();
			$this->invalidateCache();
		}
	}
	// crmv@97862e

	// invalida la cache immagine
	// TODO: invalida tutte le cache
	function invalidateCache() {
		global $adb, $table_prefix;
		$chartid = $this->column_fields["record_id"];

		$adb->pquery("update {$table_prefix}_chartscache set {$this->cachefield} = NULL where {$this->table_index} = ?", array($chartid));
		// delete files
		// TODO: thumbnails
		if (is_writable($this->column_fields[$this->cachefield])) {
			$mapname = preg_replace('/\.png/', '_map.map', $this->column_fields[$this->cachefield]);
			$thumbname = preg_replace('/\.png/', '_thumb.png', $this->column_fields[$this->cachefield]);
			@unlink($this->column_fields[$this->cachefield]);
			@unlink($mapname);
			@unlink($thumbname);
			$this->column_fields[$this->cachefield] = '';
			unset($this->cachefile_date[$this->cachefield]);
			$this->map_file = null;
		}
	}

	// crea una anteprima del grafico
	function createThumbnail($filename) {
		
		if ($this->chartLibrary == 'ChartJS') {
			// just set the sizes
			$newy = intval(floatval($this->thumbnail_size) * $this->image_size_y / $this->image_size_x);
			$this->image_size_x = $this->thumbnail_size;
			$this->image_size_y = $newy;
			return null;
		}
	
		if (function_exists('imagecopyresampled') && is_readable($filename)) {
			$image_src = imagecreatefrompng($filename);
			$newy = intval(floatval($this->thumbnail_size) * $this->image_size_y / $this->image_size_x);
			$image_dest = imagecreatetruecolor($this->thumbnail_size, $newy);

			imagecopyresampled($image_dest, $image_src, 0, 0, 0, 0, $this->thumbnail_size, $newy, $this->image_size_x, $this->image_size_y);

			$outname = preg_replace('/\.png/', '_thumb.png', $filename);
			imagepng($image_dest, $outname);
			return $outname;
		}
 		return null;
	}

	function renderChart($showdate = true, $showborder = true) {
		global $app_strings, $mod_strings, $theme, $current_language;

		// map data is used only for php generated charts
		if ($this->chartLibrary == 'pChart') {
			$fname = $this->generateChart();
			$mapdata = $this->getMapData();
			$chartdata = null;
		} elseif ($this->chartLibrary == 'ChartJS') {
			$fname = null;
			$mapdata = null;
			$chartdata = $this->generateChart();
		}

		$smarty = new vtigerCRM_Smarty();
		$smarty->assign('APP', $app_strings);
		$smarty->assign('MOD', return_module_language($current_language,'Charts'));
		$smarty->assign('THEME', $theme);
		$smarty->assign('IMAGE_PATH', "themes/$theme/images/");

		$smarty->assign('CHART_ID', $this->column_fields['record_id']);
		$smarty->assign('CHART_TITLE', $this->getChartTitle());
		$smarty->assign('CHART_PATH', $fname);
		// variables used for home block
		$smarty->assign('HOME_STUFFID', $this->homestuffid);
		$smarty->assign('HOME_STUFFSIZE', $this->homestuffsize);

		$smarty->assign('CHART_SHOWBORDER', $showborder);
		$smarty->assign('CHART_SHOWDATE', $showdate);
		if ($showdate) {
			$smarty->assign('CHART_LASTUPDATE', $this->cachefile_date[$this->cachefield]);
			$extdate = date('Y-m-d H:i:s', $this->cachefile_date[$this->cachefield]);
			$smarty->assign('CHART_LASTUPDATE_DISPLAY', getDisplayDate($extdate));
			$smarty->assign('CHART_LASTUPDATE_RELATIVE', getFriendlyDate(date('Y-m-d H:i:s', $this->cachefile_date[$this->cachefield])));
		}
		
		$smarty->assign('CHART_MAP', $mapdata);
		$smarty->assign('CHART_DATA', $chartdata);

		if ($this->chartLibrary == 'pChart') {
			$htmldata = $smarty->fetch('modules/Charts/RenderChart.tpl');
		} elseif ($this->chartLibrary == 'ChartJS') {
			$htmldata = $smarty->fetch('modules/Charts/RenderChartJS.tpl');
		} else {
			throw new Exception("Chart library '{$this->chartLibrary}' is not supported");
		}
		return $htmldata;
	}

	function renderHomeBlock() {
		global $adb, $table_prefix, $current_user;

		$size = $this->homestuffsize;
		if (empty($size)) $size = 1;

		// get home layout
		$layout = 4;
		$res = $adb->pquery("select layout from {$table_prefix}_home_layout where userid = ?", array($current_user->id));
		if ($res && $adb->num_rows($res) > 0) {
			$layout = intval($adb->query_result($res, 0, 'layout'));
			if ($layout == 0) $layout = 4;
		}

		switch ($layout) {
			case 2:
				$single_size = 520;
				break;
			case 3:
				$single_size = 360;
				break;
			case 4:
			default:
				$single_size = 260;
		}

		$this->setCacheField('chart_file_home');
		$this->image_size_x = $single_size * $size;
		$this->image_size_y = 260;
		return $this->renderChart(true, false);
	}
	
	// crmv83340
	function renderModuleHomeBlock($layout = 4) {
		global $adb, $table_prefix, $current_user;

		$size = $this->homestuffsize;
		if (empty($size)) $size = 1;

		switch ($layout) {
			case 2:
				$single_size = 520;
				break;
			case 3:
				$single_size = 360;
				break;
			case 4:
			default:
				$single_size = 260;
		}

		//$this->setCacheField('chart_file_home');
		$this->image_size_x = $single_size * $size;
		$this->image_size_y = 260;
		return $this->renderChart(true, false);
	
	}
	// crmv83340e

	// restituisce un array di istanze per i grafici
	function getChartsForReport($reportid, $usefilters = false) { // crmv@31209
		global $adb, $table_prefix;

		$ret = array();
		$res = $adb->pquery("select {$this->table_index} as chartid from {$this->table_name} inner join {$table_prefix}_crmentity crment on crment.crmid = {$this->table_name}.{$this->table_index} where crment.deleted = 0 and reportid = ?", array($reportid));
		if ($res) {
			while ($row = $adb->fetchByAssoc($res, -1, false)) {
				$chid = $row['chartid'];
				$chclass = CRMEntity::getInstance('Charts');
				$chclass->retrieve_entity_info($chid, 'Charts');
				if ($usefilters) $chclass->filtered_data = true; // crmv@31209
				$ret[] = $chclass;
			}
		}

		return $ret;
	}

	function getChartTypes() {
		global $adb, $current_user, $table_prefix;

		$chtypes  = getAssignedPicklistValues('chart_type', $current_user->roleid, $adb, 'Charts');

		return $chtypes;
	}

	function getQuickCreateDefault($module, $qcreate_array, $search_field, $search_text) {
		$col_fields = parent::getQuickCreateDefault($module, $qcreate_array, $search_field, $search_text);

		// add defaults
		$col_fields['chart_labels'] = 1;
		$col_fields['chart_merge_small'] = 1;

		return $col_fields;
	}

	// crmv@30967
	function getFolderContent($folderid) {
		global $adb, $table_prefix, $current_user, $app_strings, $mod_strings;

		$folderinfo = getEntityFolder($folderid);

		$queryGenerator = QueryGenerator::getInstance('Charts', $current_user);
		$queryGenerator->initForDefaultCustomView();
		$queryGenerator->addField('chart_filename');
		$list_query = $queryGenerator->getQuery();
		// only in selected folder
		$list_query .= " AND {$this->table_name}.folderid = '$folderid'";
		// order by most recent first
		$list_query .= " ORDER BY {$table_prefix}_crmentity.modifiedtime DESC";

		$count = 0;
		$res = $adb->query(replaceSelectQuery($list_query,'count(*) as cnt'));
		if ($res) $count = $adb->query_result($res,0,'cnt');

		$smarty = new vtigerCRM_Smarty();
		$smarty->assign('FOLDERINFO', $folderinfo);
		$smarty->assign('APP', $app_strings);
		$smarty->assign('MOD', $mod_strings);
		$smarty->assign('TOTALCOUNT', $count);

		// retrieve the first documents as a preview
		$html = '';
		$res = $adb->limitQuery($list_query, 0, 3);
		if ($res) {
			$arr = array();
			while ($row = $adb->fetchByAssoc($res)) {
				$arr[] = $row;
			}
			$smarty->assign('FOLDERDATA', $arr);
		}
		$html = $smarty->fetch('modules/Charts/FolderTooltip.tpl');

		return array('count'=>$count, 'html'=>$html);
	}
	// crmv@30967e

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	function vtlib_handler($modulename, $event_type) {
		global $adb, $table_prefix;

		if($event_type == 'module.postinstall') {

			$clModule = Vtiger_Module::getInstance($modulename);
			$clModule->disableTools(Array('Import', 'Export', 'DuplicatesHandling'));

			$adb->pquery('UPDATE '.$table_prefix.'_tab SET customized=0 WHERE name=?', array($modulename));

			// blocco info chiuso di default
			//$adb->pquery("update {$table_prefix}_blocks set display_status = 0 where blocklabel = ?", array('LBL_CHARTS_INFORMATION'));

			$clModule->hide(array('hide_report'=>1)); // crmv@38798

			$modInstance = CRMEntity::getInstance($modulename);
			// hide it from many places
			if (method_exists($modInstance, 'hide'))
			    $modInstance->hide(array('hide_module_manager'=>1,'hide_profile'=>1,'hide_report'=>1));

			if(!Vtiger_Utils::CheckTable($this->table_name)) {
				Vtiger_Utils::CreateTable(
						$this->table_name,
						"{$this->table_index} I(19) PRIMARY",
						true);
			}
			if(!Vtiger_Utils::CheckTable($this->customFieldTable[0])) {
				Vtiger_Utils::CreateTable(
						$this->customFieldTable[0],
						"{$this->customFieldTable[1]} I(19) PRIMARY",
						true);
			}

			$ctable = $table_prefix.'_chartscache';
			if(!Vtiger_Utils::CheckTable($ctable)) {
				Vtiger_Utils::CreateTable(
					$ctable,
					"{$this->customFieldTable[1]} I(19) PRIMARY",
					true);
			}

			// table for charts in homepage
			$hometable = $table_prefix.'_homecharts';
			if(!Vtiger_Utils::CheckTable($hometable)) {
				Vtiger_Utils::CreateTable(
					$hometable,
					"stuffid I(19) PRIMARY,
					chartid I(19)",
					true);
			}

			if (class_exists('SDK')) {
				SDK::file2DbLanguages($modulename);
				SDK::setAdvancedPermissionFunction('Charts', 'chartsPermission', 'modules/Charts/SDK/advPermission.php');

				// extra languages
				SDK::setLanguageEntry('APP_STRINGS', 'it_it', 'SINGLE_Charts', 'Grafico');
				SDK::setLanguageEntry('APP_STRINGS', 'en_us', 'SINGLE_Charts', 'Chart');
				SDK::setLanguageEntry('Home', 'it_it', 'LBL_HOME_CHART_NAME', 'Nome Grafico');
				SDK::setLanguageEntry('Home', 'en_us', 'LBL_HOME_CHART_NAME', 'Chart Name');
			}

			// add a default folder
			addEntityFolder('Charts', 'Default', '', 1);
			// create examples
			include('modules/Charts/InstallExamples.php');

		} else if($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} else if($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} else if($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} else if($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($event_type == 'module.postupdate') {
			// reload translations from module files
			// TODO: non sovrascrivere personalizzazioni
			if (class_exists('SDK')) {
				SDK::deleteLanguage($modulename);
				SDK::file2DbLanguages($modulename);
				SDK::setLanguageEntry('APP_STRINGS', 'it_it', 'SINGLE_Charts', 'Grafico');
				SDK::setLanguageEntry('APP_STRINGS', 'en_us', 'SINGLE_Charts', 'Chart');
			}
		}
	}

	function trash($module, $id) {
		global $adb, $table_prefix;
		parent::trash($module, $id);

		// delete charts from home page
		$stuffids = array();
		$res = $adb->pquery("select stuffid from {$table_prefix}_homecharts where chartid = ?", array($id));
		if ($res) {
			while ($row = $adb->fetchByAssoc($res, -1, false)) $stuffids[] = $row['stuffid'];

			if (count($stuffids) > 0) {
				$adb->pquery("delete from {$table_prefix}_homecharts where stuffid in (".generateQuestionMarks($stuffids).")", $stuffids);
				$adb->pquery("delete from {$table_prefix}_homestuff where stuffid in (".generateQuestionMarks($stuffids).")", $stuffids);
			}
		}

	}

	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	/*
	function save_related_module($module, $crmid, $with_module, $with_crmid) {
		parent::save_related_module($module, $crmid, $with_module, $with_crmid);
		//...
	}
	*/

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function delete_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/**
	 * Handle getting dependents list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	// widget code
	static function getWidget($name) {
		if ($name == 'DetailViewBlockChartWidget' && isPermitted('Charts', 'DetailView') == 'yes') {
			require_once dirname(__FILE__) . '/widgets/DetailViewBlockChart.php';
			return (new Charts_DetailViewBlockChartWidget($this));
		}
		return false;
	}
	
	// output a rgb style palette
	function parsePaletteFile($file) {
		$rgbPalette = array();
		$data = @file_get_contents($file);
		if (empty($data)) return $rgbPalette;
		
		$lines = array_filter(explode("\n", str_replace("\r", '', $data)));
		foreach ($lines as $line) {
			list($r, $g, $b, $a) = array_map('trim', explode(",", strtolower($line)));
			$r = intval($r,0);
			$g = intval($g,0);
			$b = intval($b,0);
			$a2 = intval($a);
			if ($a === null || $a === '' || $a2 == 100) {
				$rgbPalette[] = array($r, $g, $b);
			} else {
				$rgbPalette[] = array($r, $g, $b, $a2);
			}
		}
		
		return $rgbPalette;
	}
	
	// convert a rgb palette into a css-style palette
	function paletteRGB2Css($rgbPalette) {
		$cpalette = array();
		foreach ($rgbPalette as $c) {
			if (!isset($c[3]) || $c[3] === '' || $c[3] == 100) {
				$out = sprintf("#%02x%02x%02x", $c[0], $c[1], $c[2]);
			} else {
				$out = sprintf("rgba(%d,%d,%d,%d)", $c[0], $c[1], $c[2], $c[3]);
			}
			$cpalette[] = $out;
		}
		return $cpalette;
	}
	
	function variatePalette($rgbPalette, $variationType, $variationParams = array()) {
		$vpalette = array();
		
		if ($variationType == 'lighten' || $variationType == 'darken') {
			$perc = $variationParams['percentage'] ?: 10;
			foreach ($rgbPalette as $color) {
				$hsl = self::rgb2hsl($color[0], $color[1], $color[2]);
				if ($variationType == 'lighten') {
					$hsl[2] = max(0.0, min($hsl[2] * (1.0 + ($perc / 100)), 1.0));
				} else {
					$hsl[2] = max(0.0, min($hsl[2] * (1.0 - ($perc / 100)), 1.0));
				}
				$rgb = self::hsl2rgb($hsl[0], $hsl[1], $hsl[2]);
				$vpalette[] = $rgb;
			}
		} else {
			// other variations not supported
			$vpalette = $rgbPalette;
		}
		
		return $vpalette;
	}
	
	// some helper functions to manage the palette
	static function rgb2hsl($r, $g, $b) {
		$var_R = ($r / 255);
		$var_G = ($g / 255);
		$var_B = ($b / 255);

		$var_Min = min($var_R, $var_G, $var_B);
		$var_Max = max($var_R, $var_G, $var_B);
		$del_Max = $var_Max - $var_Min;

		$v = $var_Max;

		if ($del_Max == 0) {
			$h = 0;
			$s = 0;
		} else {
			$s = $del_Max / $var_Max;

			$del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
			$del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
			$del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;

			if      ($var_R == $var_Max) $h = $del_B - $del_G;
			else if ($var_G == $var_Max) $h = ( 1 / 3 ) + $del_R - $del_B;
			else if ($var_B == $var_Max) $h = ( 2 / 3 ) + $del_G - $del_R;

			if ($h < 0) $h++;
			if ($h > 1) $h--;
		}

		return array($h, $s, $v);
	}

	static function hsl2rgb($h, $s, $v) {
		if($s == 0) {
			$r = $g = $b = $v * 255;
		} else {
			$var_H = $h * 6;
			$var_i = floor( $var_H );
			$var_1 = $v * ( 1 - $s );
			$var_2 = $v * ( 1 - $s * ( $var_H - $var_i ) );
			$var_3 = $v * ( 1 - $s * (1 - ( $var_H - $var_i ) ) );

			if       ($var_i == 0) { $var_R = $v     ; $var_G = $var_3  ; $var_B = $var_1 ; }
			else if  ($var_i == 1) { $var_R = $var_2 ; $var_G = $v      ; $var_B = $var_1 ; }
			else if  ($var_i == 2) { $var_R = $var_1 ; $var_G = $v      ; $var_B = $var_3 ; }
			else if  ($var_i == 3) { $var_R = $var_1 ; $var_G = $var_2  ; $var_B = $v     ; }
			else if  ($var_i == 4) { $var_R = $var_3 ; $var_G = $var_1  ; $var_B = $v     ; }
			else                   { $var_R = $v     ; $var_G = $var_1  ; $var_B = $var_2 ; }

			$r = $var_R * 255;
			$g = $var_G * 255;
			$b = $var_B * 255;
		}    
		return array($r, $g, $b);
	}
	
}
?>
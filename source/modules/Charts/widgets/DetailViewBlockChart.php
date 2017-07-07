<?php
/* crmv@30014 - charts */
require_once('Smarty_setup.php');

class Charts_DetailViewBlockChartWidget {
	private $_name = 'DetailViewBlockChartWidget';
	private $_focus = null;

	protected $defaultDisplayBlock = 'block';	// block/none

	function __construct($modInst = null) {
		$this->_focus = $modInst;
	}

	function getFromContext($key, $purify=false) {
		if ($this->context) {
			$value = $this->context[$key];
			if ($purify && !empty($value)) {
				$value = vtlib_purify($value);
			}
			return $value;
		}
		return false;
	}

	function title() {
		return getTranslatedString('SINGLE_Charts', 'Charts');
	}

	function name() {
		return $this->_name;
	}

	function uikey() {
		return "ChartsDetailViewBlockChartWidget";
	}

	function getViewer() {
		global $theme, $app_strings, $current_language;

		$smarty = new vtigerCRM_Smarty();
		$smarty->assign('APP', $app_strings);
		$smarty->assign('MOD', return_module_language($current_language,'Charts'));
		$smarty->assign('THEME', $theme);
		$smarty->assign('IMAGE_PATH', "themes/$theme/images/");

		$smarty->assign('UIKEY', $this->uikey());
		$smarty->assign('WIDGET_TITLE', $this->title());
		$smarty->assign('WIDGET_NAME', $this->name());

		return $smarty;
	}


	function processItem($model) {
		$viewer = $this->getViewer();
		$viewer->assign('COMMENTMODEL', $model);
		$viewer->assign('DEFAULT_REPLY_TEXT', getTranslatedString('LBL_DEFAULT_REPLY_TEXT','Chart'));
		return $viewer->fetch(vtlib_getModuleTemplate("Charts","widgets/DetailViewBlockChartItem.tpl"));
	}

	function process($context = false) {
		global $current_user;

		$this->context = $context;
		$sourceRecordId =  $this->getFromContext('ID', true);

		$viewer = $this->getViewer();
		$viewer->assign('ID', $sourceRecordId);

		$viewer->assign('DEFAULT_DISPLAY_BLOCK', $this->defaultDisplayBlock);

		if (empty($this->_focus) && $sourceRecordId > 0) {
			$this->_focus = CRMEntity::getInstance('Charts');
			$this->_focus->retrieve_entity_info($sourceRecordId, 'Charts');
		}

		if ($this->_focus) {
			$charthtml = $this->_focus->renderChart();
			$viewer->assign('CHART_HTML', $charthtml);
		}


		return $viewer->fetch(vtlib_getModuleTemplate("Charts","widgets/DetailViewBlockChart.tpl"));
	}


}
?>
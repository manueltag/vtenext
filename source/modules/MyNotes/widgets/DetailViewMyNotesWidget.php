<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
require_once('Smarty_setup.php');

class MyNotes_DetailViewMyNotesWidget {
	
	private $_name;
	private $title;
	protected $context = false;

	function __construct() {
		$this->_name = 'DetailViewMyNotesWidget';
		$this->title = getTranslatedString('MyNotes','MyNotes');
	}

	function name() {
		return $this->_name;
	}

	function title() {
		return $this->title;
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
	
	function process($context = false) {
		global $theme, $app_strings;
		$this->context = $context;
		$sourceRecordId = $this->getFromContext('ID', true);
		$smarty = new vtigerCRM_Smarty;
		$smarty->assign('APP', $app_strings);
		$smarty->assign('THEME', $theme);
		$smarty->assign('NAME', $this->name());
		$focus = CRMEntity::getInstance('MyNotes');
		$notes = $focus->getRelNotes($sourceRecordId,1);
		if (!empty($notes[0])) {
			$url = "index.php?module=MyNotes&action=DetailView&mode=DetailViewMyNotesWidget&record={$notes[0]}&parent={$sourceRecordId}";
		} else {
			$url = "index.php?module=MyNotes&action=MyNotesAjax&file=widgets/create&parent={$sourceRecordId}";
		}
		$smarty->assign('URL', $url);
		$smarty->display('modules/MyNotes/widgets/DetailViewMyNotesWidget.tpl');
	}
}

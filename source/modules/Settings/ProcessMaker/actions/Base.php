<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@102879 crmv@115268 */ 

class PMActionBase {

	public $isCycleAction = false;	// true if the action is executed inside a cycleleAction
	public $cycleIndex = null;		// the current index of the cycle
	public $cycleRow = null;		// the current row of the cycle
	
	protected $options = array();

	public function __construct($options = array()) {
		$this->options = $options;
	}
	
	public function getOptions() {
		return $this->options;
	}
	
	public function setOptions($options) {
		$this->options = $options;
	}

}
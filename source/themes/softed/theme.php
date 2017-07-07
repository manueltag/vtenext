<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

// crmv@119414

require_once('include/BaseClasses.php');

class ThemeConfig extends OptionableClass {
	
	protected $options = array(
		'primary_menu_position' => 'top',
		'secondary_menu_position' => 'right',
		'tpl_overrides' => array(),
	);
	
}

<?php
/* crmv@94125 */

/**
 * Smarty resourcever modifier plugin
 *
 * Type:     modifier<br>
 * Name:     resourcever<br>
 * Purpose:  return a versioned version of the file
 * @param string
 */
function smarty_modifier_resourcever($filename) {
	if (class_exists('ResourceVersion')) {
		$RV = ResourceVersion::getInstance();
		$filename = $RV->getResource($filename);
	}
    return $filename;
}


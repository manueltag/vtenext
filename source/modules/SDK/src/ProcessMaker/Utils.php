<?php
/* crmv@99316 */
function vte_sum() {
	$arguments = func_get_args();
	$value = 0;
	if (!empty($arguments)) {
		foreach($arguments as $argument) {
			$argument = trim($argument,' \'"');
			$value = $value + floatval($argument);
		}
	}
	return $value;
}
<?php
/* crmv@49432 */
function format_flags($v) {
	if (substr($v,0,1) == '\\')
		$v = '\\'.ucfirst(strtolower(substr($v,1)));
	elseif (substr($v,0,1) == '$')
		$v = '$'.ucfirst(strtolower(substr($v,1)));
	else
		$v = ucfirst(strtolower($v));
	return $v;
}
?>
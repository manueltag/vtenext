<?php 
if ($type == 'MassEditSave') {
	$status = true;
	$message = '';
} else {
	if (in_array($values['salesorderid'],array('',0))) {
		$confirm = true;
		$message = 'Ordine di Vendita è vuoto! Vuoi procedere comunque?';
	}
} 
?>
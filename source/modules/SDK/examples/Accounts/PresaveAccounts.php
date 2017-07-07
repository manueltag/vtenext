<?php 
if ($type == 'MassEditSave') {
	$status = true;
	$message = '';
} else {
	if ($values['description'] == '') {
		$status = false;
		$message = 'Il campo Descrizione è vuoto!';
		$focus = 'description';
		$changes['description'] = 'Descrizione di default.';
	}
} 
?>
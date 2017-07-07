<?php
$piva = $_REQUEST['piva'];

if(ControlloPIVA($piva))
	echo 'true';
else
	echo 'false';

function ControlloPIVA($pi) {
	if( $pi == '' )
		return false;
	if( strlen($pi) != 11 )
		return false;
	if( ! ereg("^[0-9]+$", $pi) )
		return false;
	$s = 0;
	for( $i = 0; $i <= 9; $i += 2 )
		$s += ord($pi[$i]) - ord('0');
	for( $i = 1; $i <= 9; $i += 2 ){
		$c = 2*( ord($pi[$i]) - ord('0') );
		if( $c > 9 )
			$c = $c - 9;
		$s += $c;
	}
	if( ( 10 - $s%10 )%10 != ord($pi[10]) - ord('0') )
		return false;
	return true;
}
?>
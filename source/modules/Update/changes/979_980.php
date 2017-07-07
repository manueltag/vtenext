<?php
$_SESSION['modules_to_update']['Morphsuit'] = 'packages/vte/mandatory/Morphsuit.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter', 'Targets'));
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';

SDK::setLanguageEntries('Messages', 'LBL_LINK_NEW_MAIL', array(
	'it_it'=>'Collega nuova mail',
	'en_us'=>'Link new email',
	'pt_br'=>'Novo link de e-mail',
	'de_de'=>'Verlinken neue E-Mail',
	'nl_nl'=>'Link nieuwe email',
));
?>
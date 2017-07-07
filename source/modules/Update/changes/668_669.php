<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter'));

SDK::setLanguageEntries('Newsletter','LBL_OWNER_MISSING',array('it_it'=>'Assegnatario mancante','en_us'=>'Assigned user missing','pt_br'=>'Falta usuário'));
?>
<?php
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter'));
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['Transitions'] = 'packages/vte/mandatory/Transitions.zip';
$_SESSION['modules_to_update']['Morphsuit'] = 'packages/vte/mandatory/Morphsuit.zip';
$_SESSION['modules_to_update']['Webforms'] = 'packages/vte/mandatory/Webforms.zip';

SDK::setLanguageEntries('Emails', 'Save Draft', array('it_it'=>'Salva bozza','en_us'=>'Save draft','pt_br'=>'Salvar rascunho'));
SDK::setLanguageEntries('Webmails', 'Resume Draft', array('it_it'=>'Modifica','en_us'=>'Resume Draft','pt_br'=>'Editar rascunho'));
SDK::setLanguageEntries('Emails', 'Draft saved at', array('it_it'=>'Bozza salvata alle','en_us'=>'Draft saved at','pt_br'=>'Rascunho salvo em'));
SDK::setLanguageEntries('Emails', 'Draft saved automatically at', array('it_it'=>'Bozza salvata automaticamente alle','en_us'=>'Draft saved automatically at','pt_br'=>'Rascunho salvo automaticamente em'));
SDK::setLanguageEntries('Emails', 'Draft error', array('it_it'=>'Non è stato possibile salvare la mail in bozze. Verificare le impostazioni della webmail.','en_us'=>'It was not possible to save the mail in drafts. Check the webmail settings.','pt_br'=>'Não foi possível salvar o e-mail em rascunhos. Verifique as configurações do webmail.'));

global $adb;
$adb->pquery('UPDATE sdk_menu_fixed SET image = ? WHERE title = ?',array('favorites.png','LBL_FAVORITES'));
?>
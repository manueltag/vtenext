<?php
$_SESSION['modules_to_update']['Charts'] = 'packages/vte/mandatory/Charts.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter'));
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';

SDK::setLanguageEntries('Update','LBL_UPDATE_PACK_INVALID',array(
'it_it'=>'Questo pacchetto di aggiornamento non è applicabile alla tua versione di VTE.<br />Contatta CRMVillage.BIZ o il tuo Partner di riferimento per avere la versione corretta.',
'en_us'=>'This update package is not applicable on your VTE version.<br />Please contact CRMVillage.BIZ or your Partner in order to obtain the correct version.',
'pt_br'=>'Este pacote de atualização não é aplicável a sua versão do VTE.<br />Fala com CRMVillage.BIZ ou o seu Parceiro para obter a versão correta.',
));
?>
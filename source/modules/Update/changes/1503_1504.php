<?php
global $adb, $table_prefix;
$adb->pquery("update sdk_menu_contestual set image = ? where module = ? and title = ?", array('euro_symbol','Potentials','Budget'));
$adb->pquery("update sdk_menu_contestual set image = ? where module = ? and title = ?", array('contact_mail','Newsletter','NEWSLETTER_G_UNSUBSCRIBE'));
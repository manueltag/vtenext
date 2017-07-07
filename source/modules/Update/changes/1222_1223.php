<?php
$fields = array();
$fields[] = array('module'=>'Documents','block'=>'LBL_FILE_INFORMATION','name'=>'active_portal','label'=>'Portal Active','uitype'=>'56','columntype'=>'I(1) DEFAULT 0','typeofdata'=>'C~O','readonly'=>'1');
include('modules/SDK/examples/fieldCreate.php');

SDK::setLanguageEntry('Documents', 'it_it', 'Portal Active', 'Visibile su Portale');
SDK::setLanguageEntry('Documents', 'en_us', 'Portal Active', 'Portal Active');
?>
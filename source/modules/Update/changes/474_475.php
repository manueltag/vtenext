<?php
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectTask'));

SDK::deleteLanguage('Webmails');
SDK::file2DbLanguages('Webmails');
?>
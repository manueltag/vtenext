<?php

$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

if (isModuleInstalled('RecycleBin')) {
	$_SESSION['modules_to_update']['RecycleBin'] = 'packages/vte/optional/RecycleBin.zip';
}
<?php

global $adb, $table_prefix;

$onclick = "showFloatingDiv('favorites',this);getFavoriteList();";
$adb->pquery("UPDATE sdk_menu_fixed SET onclick = ? WHERE title = ?", array($onclick, 'LBL_FAVORITES'));

$onclick = "showFloatingDiv('events',this);getEventList(this);";
$adb->pquery("UPDATE sdk_menu_fixed SET onclick = ? WHERE title = ?", array($onclick, 'Events'));
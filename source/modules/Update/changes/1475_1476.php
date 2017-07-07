<?php

// crmv@114595

// replace old icons in vte_links
$list = array(
	'themes/images/small_spam.png' => 'vteicon:whatshot',
	'themes/images/bookMark.gif' => 'vteicon:note_add',
	'themes/images/reply_min.png' => 'vteicon:reply',
);

foreach ($list as $oldicon => $replace) {
	$adb->pquery("UPDATE {$table_prefix}_links SET linkicon = ? WHERE linkicon IS NOT NULL AND linkicon = ?", array($replace, $oldicon));
}

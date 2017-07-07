<?php
global $table_prefix;
Update::change_field($table_prefix.'_messages_cron_uid','folder','C','255');

SDK::setLanguageEntries('Messages', 'TRANSLATE_MESSAGE', array(
	'it_it'=>'Traduci messaggio',
	'en_us'=>'Translate message',
	'de_de'=>'Übersetzen Nachricht',
	'nl_nl'=>'Vertalen bericht',
	'pt_br'=>'Traduzir mensagem',
));
?>
<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';

$labels = array (
	'ALERT_ARR' => array (
		'de_de' => array (
			'LBL_ADD_PICKLIST_VALUE' => 'Bitte mindestens eine neue Angabe machen',
			'LBL_NO_VALUES_TO_DELETE' => 'Keine Daten zum Löschen vorhanden',
			'LBL_FORWARD_EMAIL' => 'Weiterleiten',
			'LBL_PRINT_EMAIL' => 'Drucken',
			'LBL_QUALIFY_EMAIL' => 'Qualifizieren',
			'LBL_REPLY_TO_SENDER' => 'Antworten',
			'LBL_SELECT_ROLE' => 'Bitte mindestens eine Rolle auswählen zu der die neuen Werte gehören sollen',
			'LBL_SIZE_SHOULDNOTBE_GREATER' => 'Die Dateigröße sollte nicht größer sein als',
			'VALID_SCANNER_NAME' => 'Bitte geben Sie einen gültigen Scanner Namen an (Er sollte nur Buchstaben und Nummern enthalten)',
			'CANT_SELECT_CONTACTS' => 'Aus Leads können keine verbundenen Kontakte ausgewählt werden',
			'DATE_SHOULDNOT_PAST' => 'gegenwärtige Zeit und Datum Aktivitäten mit geplantem Status.',
			'DELETE_ACCOUNTS' => 'Wenn Sie diese Konten löschen, werden die damit verbundenen Potentiale und Angebote auch gelöscht. Sind Sie sicher, dass Sie sie löschen wollen?',
			'DELETE_RECORDS' => 'Wollen Sie wirklich die %s Datensätze löschen?',
			'DELETE_VENDORS' => 'Wenn Sie diese Anbieter löschen, werden die zugehörigen Bestellungen gelöscht. Sind Sie sicher, dass Sie sie löschen wollen?',
			'DEL_MANDATORY' => 'Sie Können Pflichtfelder nicht löschen',
			'EMAIL_CHECK_MSG' => 'Um das Email-Feld leer zu speichern müssen Sie den Portalzugang sperren',
			'ENDTIME_GREATER_THAN_STARTTIME' => 'Die Endzeit muss später sein als der Anfang',
			'ERR_MANDATORY_FIELD_VALUE' => 'Einige Pflichtfelder fehlen noch. Bitte füllen Sie diese aus',
			'ERR_SELECT_EITHER' => 'Wählen Sie entweder die Organisation oder den Kontakt zur Leadumwandlung',
			'IS_MANDATORY_FIELD' => 'ist Pflichtfeld',
			'LBL_ALERT_EXT_CODE' => 'Es gibt bereits eine Organisation mit demselben externen Code. Wollen sie diese verbinden?',
			'LBL_ARE_YOU_SURE_TO_MOVE_TO' => 'Sind Sie sicher, dass Sie die Datei(en) in das verschieben möchten in',
			'LBL_BLANK_REPLACEMENT' => 'Es kann kein Leerwert zum ersetzen verwendet werden.',
			'LBL_CLOSE_DATE' => 'Schließdatum',
			'LBL_DECIMALALERT' => 'Ihre Datenfelder müssen das gleiche Zahlenformat haben. Die Anzahl der Dezimalstellen nach dem Komma muss gleich sein.',
			'LBL_DEL' => 'Löschen',
			'LBL_DELETE_MSG' => 'Sind Sie sicher?',
			'LBL_ERROR_WHILE_DELETING_FOLDER' => 'Es gab einen Fehler beim Löschen des Verzeichnisses. Bitte versuchen Sie es noch einmal.',
			'LBL_NO_DATA_SELECTED' => 'Sie haben keine Auswahl getroffen. Für einen Export müssen Sie mindestens einen Eintrag auswählen.',
			'LBL_NO_EMPTY_FOLDERS' => 'Es gibt keine leeren Ordner',
			'LBL_SAVE_LAST_CHANGES' => 'Wollen Sie die letzten Änderungen? Speichern Sie oder brechen Sie ab.',
			'LBL_SEARCH_WITHOUTSEARCH_CURRENTPAGE' => 'Sie haben die Suchfunktion verwendet, um Daten auszuwählen. Jedoch haben Sie ihr Exportkriterium darauf nicht bezogen. Wenn Sie auf [ok] klicken werden die Daten Ihrer aktuellen Listenansicht exportiert. Wenn Sie auf [Abbrechen] klicken, können Sie Ihre Exportkriterien neu bestimmen.',
			'LBL_SELECT_ONE_FILE' => 'Bitte mindestens eine Datei auswählen',
			'LBL_SELECT_PICKLIST' => 'Bitte mindestens einen Eintrag auswählen',
			'LBL_TEMPLATE_MUST_HAVE_PREVIEW_LINK' => 'Sie haben keinen Link für eine Vorschau eingebaut. Trotzdem fortfahren?',
			'LBL_TEMPLATE_MUST_HAVE_UNSUBSCRIPTION_LINK' => 'Sie haben keinen Link zur Abbestellung eingebaut. Trotzdem fortfahren?',
			'LBL_TYPEALERT_1' => 'Sie können nicht',
			'LINE_ITEM' => 'Sortiment',
			'MSG_CONFIRM_FTP_DETAILS' => 'FTP Details bestätigen',
			'MSG_CONFIRM_PATH' => 'Pfaddetails bestätigen',
			'SELECT_ATLEAST_ONEMSG_TO_DEL' => 'Sie müssen mindestens eine Nachricht zum Löschen markieren.',
			'SELECT_TEMPLATE_TO_MERGE' => 'Bitte wählen Sie für das Zusammenführen eine Textvorlage aus.',
			'SHOULDBE_LESS_EQUAL' => 'muss weniger oder gleich sein als',
			'SURE_TO_DELETE_CUSTOM_MAP' => 'Sind Sie sicher,dass Sie das Mapping löschen wollen?',
			'TIME_SHOULDNOT_PAST' => 'Aktuelle Zeit für geplante Aktivitäten',
			'VALID_DISCOUNT_AMOUNT' => 'Bitte geben Sie für den Rabatt einen gültigen Betrag an.',
			'VALID_DISCOUNT_PERCENT' => 'Bitte geben Sie für den Rabatt einen gültigen Zahlenwert in Prozent an.',
			'VALID_FINAL_AMOUNT' => 'Bitte geben Sie einen gültigen Prozentwert für den Rabatt an.',
			'VALID_FINAL_PERCENT' => 'Bitte geben Sie einen gültigen Prozentwert für den Rabatt an.',
		),
	),
);

foreach($labels as $module => $values) {
	foreach($values as $langid => $translations) {
		foreach($translations as $label => $new_label) {
			SDK::setLanguageEntry($module, $langid, $label, $new_label);
		}
	}
}
?>
<?php 

SDK::setUitype(1016, 'modules/SDK/src/1016/1016.php', 'modules/SDK/src/1016/1016.tpl', 'modules/SDK/src/1016/1016.js', 'signature');

$moduleInstance = Vtecrm_Module::getInstance('HelpDesk');

$blockInstance = Vtecrm_Block::getInstance('LBL_SIGNATURE_BLOCK', $moduleInstance);
if (!$blockInstance) {
	$blockInstance = new Vtecrm_Block();
	$blockInstance->label = 'LBL_SIGNATURE_BLOCK';
	$moduleInstance->addBlock($blockInstance);
}

$fieldInstance = Vtecrm_Field::getInstance('signature', $moduleInstance);
if (!$fieldInstance) {
	$fieldInstance = new Vtecrm_Field();
	$fieldInstance->name = 'signature';
	$fieldInstance->label = 'HelpDeskSignature';
	$fieldInstance->uitype = 1016;
	$fieldInstance->columntype = 'C(255)';
	$fieldInstance->typeofdata = 'V~O';
	$fieldInstance->increateview = 1;
	$blockInstance->addField($fieldInstance);
}

$labels = array(
	'HelpDesk' => array(
		'it_it' => array(
			'LBL_SIGNATURE_BLOCK' => 'Firma Ticket',
			'HelpDeskSignature' => 'Firma',
			'NO_SIGNATURE_IMAGE' => 'Nessuna Firma'
		),
		'en_us' => array(
			'LBL_SIGNATURE_BLOCK' => 'Ticket Signature',
			'HelpDeskSignature' => 'Signature',
			'NO_SIGNATURE_IMAGE' => 'No Signature'
		)
	)
);

foreach ($labels as $module => $module_values) {
	foreach ($module_values as $langid => $langid_values) {
		foreach ($langid_values as $label => $new_label) {
			SDK::setLanguageEntry($module, $langid, $label, $new_label);
		}
	}
}

// translations
$trans = array(
	'Calendar' => array(
		'it_it' => array('LBL_PENDING' => 'Pendente'),
		'en_us' => array('LBL_PENDING' => 'Pending')
	)
);

foreach ($trans as $module => $modlang) {
	foreach ($modlang as $lang => $translist) {
		foreach ($translist as $label => $translabel) {
			SDK::setLanguageEntry($module, $lang, $label, $translabel);
		}
	}
}

$body = '<table align="left" border="0" cellpadding="0" cellspacing="0" style="width:100%">
	<tbody>
		<tr>
			<td>
			<table style="border-collapse:collapse; border:1px solid #000; width:800px">
				<tbody>
					<tr>
						<td colspan="4" style="background-color:rgb(221, 221, 221)"><span style="font-size:12px"><strong>Estremi del Committente/Utente</strong></span></td>
					</tr>
					<tr>
						<td style="width:120px"><span style="font-size:12px">Cliente</span></td>
						<td>$R_ACCOUNTS_ACCOUNTNAME$</td>
						<td style="width:50px"><span style="font-size:12px">Telefono</span></td>
						<td>$R_ACCOUNTS_PHONE$</td>
					</tr>
					<tr>
						<td><span style="font-size:12px">Luogo</span></td>
						<td colspan="3" rowspan="1">$R_ACCOUNTS_BILL_STREET$<span style="font-size:12px">, </span>$R_ACCOUNTS_BILL_CODE$<span style="font-size:12px">&nbsp;</span>$R_ACCOUNTS_BILL_CITY$&nbsp;$R_ACCOUNTS_BILL_COUNTRY$</td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>
			<table align="left" border="0" cellpadding="3" cellspacing="0" style="border-collapse:collapse; border:1px solid #000; width:800px">
				<tbody>
					<tr>
						<td style="background-color:rgb(221, 221, 221)"><span style="font-size:12px"><strong>OGGETTO DELL&#39;INTERVENTO</strong></span></td>
					</tr>
					<tr>
						<td>
						<p>$HELPDESK_TICKET_TITLE$</p>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>
			<table align="left" border="0" cellpadding="3" cellspacing="0" style="border-collapse:collapse; border:1px solid #000; width:800px">
				<tbody>
					<tr>
						<td style="background-color:rgb(221, 221, 221)"><span style="font-size:12px"><strong>DESCRIZIONE DELL&#39;INTERVENTO</strong></span></td>
					</tr>
					<tr>
						<td>
						<p>$HELPDESK_DESCRIPTION$</p>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>
			<table align="left" border="0" cellpadding="3" cellspacing="0" style="border-collapse:collapse; border:1px solid #000; width:800px">
				<tbody>
					<tr>
						<td style="background-color:rgb(221, 221, 221)"><span style="font-size:12px"><strong>SOLUZIONE DELL&#39;INTERVENTO</strong></span></td>
					</tr>
					<tr>
						<td>
						<p>$HELPDESK_SOLUTION$</p>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
	</tbody>
</table>
<p>&nbsp;</p>';

$header = '<table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%">
	<tbody>
		<tr>
			<td rowspan="2" style="text-align:center; white-space:nowrap; width:255px">$COMPANY_LOGO$<br />
			$COMPANY_ADDRESS$<span style="font-family:dejavu sans"><span style="font-size:11px"> - </span></span>$COMPANY_ZIP$<span style="font-family:dejavu sans"><span style="font-size:11px"> </span></span>$COMPANY_CITY$<span style="font-family:dejavu sans"><span style="font-size:11px"> (</span></span>$COMPANY_STATE$<span style="font-family:dejavu sans"><span style="font-size:11px">)<br />
			Tel: </span></span>$COMPANY_PHONE$<span style="font-family:dejavu sans"><span style="font-size:11px">&nbsp;</span></span></td>
			<td rowspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td style="width:200px"><span style="font-size:10px"><span style="font-family:dejavu sans">DATA: ##DD.MM.YYYY##</span></span></td>
		</tr>
		<tr>
			<td>
			<p><span style="font-size:10px"><span style="font-family:dejavu sans">TECNICO/TUTOR:<br />
			<strong><span style="font-size:12px">$L_USER_FIRSTNAME$ $L_USER_LASTNAME$</span></strong></span></span></p>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="text-align:center">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="3" style="text-align:center">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="3" style="text-align:center">
			<p><span style="font-size:26px"><span style="font-family:dejavu sans">RAPPORTO DI INTERVENTO</span></span></p>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="text-align:center">&nbsp;</td>
		</tr>
	</tbody>
</table>';

$footer = '<table align="center" cellpadding="5" cellspacing="0" style="border-collapse:collapse;border:1px solid #000;width:100%;table-layout:fixed">
	<tbody>
		<tr>
			<td colspan="4" style="background-color:rgb(221, 221, 221)"><span style="font-size:12px"><strong>Convalida Committente</strong></span></td>
		</tr>
		<tr>
			<td style="vertical-align:top; width:40px"><span style="font-size:12px">Data</span></td>
			<td style="vertical-align:top">
			<p>&nbsp;</p>

			<p>&nbsp;</p>

			<p>&nbsp;</p>
			</td>
			<td style="vertical-align:top; width:40px"><span style="font-size:12px">Firma</span></td>
			<td>
			<p>$HELPDESK_SIGNATURE$</p>
			</td>
		</tr>
	</tbody>
</table>';

$check = $adb->pquery("SELECT templateid FROM {$table_prefix}_pdfmaker WHERE filename = ? AND module = ?", array('Standard Layout', 'HelpDesk'));
if ($check && $adb->num_rows($check) > 0) {
	//do nothing
} else {
	$templateid = $adb->getUniqueID("{$table_prefix}_pdfmaker");
	$adb->query("INSERT INTO {$table_prefix}_pdfmaker (templateid, filename, module, body, description, deleted) VALUES($templateid, 'Standard Layout', 'HelpDesk', " . $adb->getEmptyClob(true) . ", '', '0')");
	$adb->updateClob("{$table_prefix}_pdfmaker", 'body', "templateid=$templateid", $body);
	$adb->query("INSERT INTO {$table_prefix}_pdfmaker_settings (templateid, margin_top, margin_bottom, margin_left, margin_right, format, orientation, decimals, decimal_point, thousands_separator, header, footer, encoding, file_name) VALUES ($templateid, 2.0, 2.0, 2.0, 2.0, 'A4', 'portrait', 2, ',', '', '" . $header . "', '" . $footer . "', 'auto', NULL)");
}
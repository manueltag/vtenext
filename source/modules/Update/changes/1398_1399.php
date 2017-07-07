<?php
global $adb, $table_prefix;

$templatename = 'Nuova Release in uscita';
$result = $adb->query("select templatename, body from {$table_prefix}_emailtemplates where body LIKE '%VTE 5.1%' and templatename = '{$templatename}'");
if ($result && $adb->num_rows($result) > 0) {
	$body = '<table align="center" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; text-decoration: none;" width="700">
	<tbody>
		<tr>
			<td width="10">&nbsp;</td>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="background-color: #f3f3f3; font-family: Arial,Helvetica,sans-serif; font-size: 14px; font-weight: normal; line-height: 25px;" width="100%">
							<tbody>
								<tr>
									<td align="center" rowspan="4">$logo$</td>
									<td align="center">&nbsp;</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px;font-family: Arial,Helvetica,sans-serif; font-size: 24px; color: #2c80c8; font-weight: bolder; line-height: 35px;">VTE 16.09</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px;color: #2c80c8;">www.vtecrm.com</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);" width="100%">
							<tbody>
								<tr>
									<td valign="top">
									<table border="0" cellpadding="5" cellspacing="0" width="100%">
										<tbody>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">&nbsp;</td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 14px; color: rgb(22, 72, 134); font-weight: bolder; line-height: 15px;">Gentile $Contacts||lastname$,</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; text-align: justify; line-height: 20px;">
												<div id="gt-res-content">
												<div dir="ltr">
												<p><span id="result_box" lang="it" xml:lang="it"><span title="Fai clic per visualizzare le traduzioni alternative">Lo Staff </span><span title="Fai clic per visualizzare le traduzioni alternative">CRMVILLAGE.BIZ</span> <span title="Fai clic per visualizzare le traduzioni alternative">&egrave;</span> <span title="Fai clic per visualizzare le traduzioni alternative">lieto</span> <span title="Fai clic per visualizzare le traduzioni alternative">di</span> <span title="Fai clic per visualizzare le traduzioni alternative">annunciare il rilascio di</span> VTE 4<span title="Fai clic per visualizzare le traduzioni alternative">.</span> <span title="Fai clic per visualizzare le traduzioni alternative">Una delle nuove caratteristi pi&ugrave; di spicco &egrave; sicuramente </span><span title="Fai clic per visualizzare le traduzioni alternative"> la</span> <span title="Fai clic per visualizzare le traduzioni alternative">gestione</span> del <span title="Fai clic per visualizzare le traduzioni alternative">template</span> <span title="Fai clic per visualizzare le traduzioni alternative">per l&#39;invio </span> <span title="Fai clic per visualizzare le traduzioni alternative">e-mail di massa</span><span title="Fai clic per visualizzare le traduzioni alternative">,</span> <span title="Fai clic per visualizzare le traduzioni alternative">funzionalit&agrave;</span> <span title="Fai clic per visualizzare le traduzioni alternative">che permette una visualizzazione personalizzata di tutte le email/newsletter... ma non solo... venite a scoprirlo in CRMVILLAGE</span>!<br />
												<span title="Fai clic per visualizzare le traduzioni alternative">Ecco un elenco di alcune nuove caratteristiche degne di nota:</span><br />
												<br />
												<span title="Fai clic per visualizzare le traduzioni alternative">-</span><span title="Fai clic per visualizzare le traduzioni alternative">Integrazione</span><span title="Fai clic per visualizzare le traduzioni alternative">-mail</span> <span title="Fai clic per visualizzare le traduzioni alternative">client</span><br />
												<span title="Fai clic per visualizzare le traduzioni alternative">-</span><span title="Fai clic per visualizzare le traduzioni alternative">Integrazione</span> <span title="Fai clic per visualizzare le traduzioni alternative">Trouble</span> <span title="Fai clic per visualizzare le traduzioni alternative">Ticket</span><br />
												<span title="Fai clic per visualizzare le traduzioni alternative">-</span><span title="Fai clic per visualizzare le traduzioni alternative">Gestore Integrato Fatture</span><br />
												<span title="Fai clic per visualizzare le traduzioni alternative">-</span><span title="Fai clic per visualizzare le traduzioni alternative">Rapporti</span> <span title="Fai clic per visualizzare le traduzioni alternative">di integrazione</span><br />
												<span title="Fai clic per visualizzare le traduzioni alternative">-</span><span title="Fai clic per visualizzare le traduzioni alternative">Integrazione</span> <span title="Fai clic per visualizzare le traduzioni alternative">Portal</span><br />
												<span title="Fai clic per visualizzare le traduzioni alternative">-</span><span title="Fai clic per visualizzare le traduzioni alternative">Word</span> <span title="Fai clic per visualizzare le traduzioni alternative">avanzato</span> <span title="Fai clic per visualizzare le traduzioni alternative"> per i plugin</span><br />
												<span title="Fai clic per visualizzare le traduzioni alternative">-</span><span title="Fai clic per visualizzare le traduzioni alternative">Visualizzazione personalizzata generale</span><br />
												<br />
												<span title="Fai clic per visualizzare le traduzioni alternative">Problemi noti</span><span title="Fai clic per visualizzare le traduzioni alternative">:</span><br />
												<span title="Fai clic per visualizzare le traduzioni alternative">-</span><span title="Fai clic per visualizzare le traduzioni alternative">ABCD</span><br />
												<span title="Fai clic per visualizzare le traduzioni alternative">-</span><span title="Fai clic per visualizzare le traduzioni alternative">EFGH</span><br />
												<span title="Fai clic per visualizzare le traduzioni alternative">-</span><span title="Fai clic per visualizzare le traduzioni alternative">IJKL</span><br />
												<span title="Fai clic per visualizzare le traduzioni alternative">-</span><span title="Fai clic per visualizzare le traduzioni alternative">mnop</span><br />
												<span title="Fai clic per visualizzare le traduzioni alternative">-</span><span title="Fai clic per visualizzare le traduzioni alternative">QRST</span></span></p>
												</div>
												</div>
												</td>
											</tr>
											<tr>
												<td align="center">&nbsp;</td>
											</tr>
											<tr>
												<td align="right"><br />
												<br />
												<strong style="padding: 2px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: bold;">Cordialmente</strong></td>
											</tr>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; line-height: 20px;">Lo Staff VTECRM</td>
											</tr>
											<tr>
												<td align="right"><a href="http://www.vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">www.vtecrm.com</a></td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
										</tbody>
									</table>
									</td>
									<td valign="top" width="1%">&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="5" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: #666; font-weight: normal; line-height: 15px; background-color: #f3f3f3;" width="100%">
							<tbody>
								<tr>
									<td align="center">VTECRM LIMITED - 38 Craven Street London WC2N 5NG - Registration No. 08337393</td>
								</tr>
								<tr>
									<td align="center">VAT No. 166 1940 00 - Phone (+44) 2035298324</td>
								</tr>
								<tr>
									<td align="center">E-Mail: <a href="mailto:info@vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: #666;">info@vtecrm.com</a></td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
			<td width="10">&nbsp;</td>
		</tr>
	</tbody>
</table>';
	$adb->updateClob($table_prefix.'_emailtemplates','body',"templatename = '{$templatename}'",$body);
}

$templatename = 'Fatture non pagate';
$result = $adb->query("select templatename, body from {$table_prefix}_emailtemplates where body LIKE '%VTE 5.1%' and templatename = '{$templatename}'");
if ($result && $adb->num_rows($result) > 0) {
	$body = '<table align="center" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; text-decoration: none;" width="700">
	<tbody>
		<tr>
			<td width="10">&nbsp;</td>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="background-color: #f3f3f3; font-family: Arial,Helvetica,sans-serif; font-size: 14px; font-weight: normal; line-height: 25px;" width="100%">
							<tbody>
								<tr>
									<td align="center" rowspan="4">$logo$</td>
									<td align="center">&nbsp;</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px; font-family: Arial,Helvetica,sans-serif; font-size: 24px; color: #2c80c8; font-weight: bolder; line-height: 35px;">VTE 16.09</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px; color: #2c80c8;">www.vtecrm.com</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);" width="100%">
							<tbody>
								<tr>
									<td valign="top">
									<table border="0" cellpadding="5" cellspacing="0" width="100%">
										<tbody>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">&nbsp;</td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 14px; color: rgb(22, 72, 134); font-weight: bolder; line-height: 15px;">Gentile $Contacts||lastname$,</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; text-align: justify; line-height: 20px;">
												<div id="gt-res-content">
												<div dir="ltr">
												<p><span id="result_box" lang="it" xml:lang="it"><span title="Fai clic per visualizzare le traduzioni alternative">Nome</span><br />
												<span title="Fai clic per visualizzare le traduzioni alternative">Cognome</span></span></p>

												<p><span lang="it" xml:lang="it">Via<br />
												Citt&agrave;<br />
												<span title="Fai clic per visualizzare le traduzioni alternative">Provincia</span><br />
												<span title="Fai clic per visualizzare le traduzioni alternative">CAP</span><br />
												<br />
												<span title="Fai clic per visualizzare le traduzioni alternative">Si prega di</span> <span title="Fai clic per visualizzare le traduzioni alternative">verificare</span> <span title="Fai clic per visualizzare le traduzioni alternative">le</span> <span title="Fai clic per visualizzare le traduzioni alternative">seguenti fatture</span><span title="Fai clic per visualizzare le traduzioni alternative">, che</span> <span title="Fai clic per visualizzare le traduzioni alternative">devono ancora essere</span> <span title="Fai clic per visualizzare le traduzioni alternative">pagate:</span></span></p>

												<table border="0" width="100%">
													<tbody>
														<tr>
															<td><strong>No</strong></td>
															<td><strong>Data</strong></td>
															<td><strong>Importo</strong></td>
														</tr>
														<tr>
															<td>1</td>
															<td>1 / 1 / 01</td>
															<td>4000 &euro;uro</td>
														</tr>
														<tr>
															<td>2</td>
															<td>2 / 2 / 01</td>
															<td>5000 &euro;uro</td>
														</tr>
														<tr>
															<td>3</td>
															<td>3 / 3 / 01</td>
															<td>10000 &euro;uro</td>
														</tr>
														<tr>
															<td>4</td>
															<td>4 / 4 / 01</td>
															<td>23560 &euro;uro</td>
														</tr>
													</tbody>
												</table>

												<p><span lang="it" xml:lang="it"><span title="Fai clic per visualizzare le traduzioni alternative">Cortesemente</span> <span title="Fai clic per visualizzare le traduzioni alternative">ci faccia sapere</span> <span title="Fai clic per visualizzare le traduzioni alternative">se ci sono</span> <span title="Fai clic per visualizzare le traduzioni alternative">problemi per i pagamenti. Saremo felici di appianare ogni divergenza a riguardo. Siamo contenti dei risultati raggiunti sino ad ora con la vostra societ&agrave; e vorremmo continuare lo splendido rapporto sin qui avuto</span></span>.</p>
												</div>
												</div>
												</td>
											</tr>
											<tr>
												<td align="center">&nbsp;</td>
											</tr>
											<tr>
												<td align="right"><br />
												<br />
												<strong style="padding: 2px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: bold;">Cordialmente</strong></td>
											</tr>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; line-height: 20px;">Lo Staff VTECRM</td>
											</tr>
											<tr>
												<td align="right"><a href="http://www.vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">www.vtecrm.com</a></td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
										</tbody>
									</table>
									</td>
									<td valign="top" width="1%">&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="5" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: #666; font-weight: normal; line-height: 15px; background-color: #f3f3f3;" width="100%">
							<tbody>
								<tr>
									<td align="center">VTECRM LIMITED - 38 Craven Street London WC2N 5NG - Registration No. 08337393</td>
								</tr>
								<tr>
									<td align="center">VAT No. 166 1940 00 - Phone (+44) 2035298324</td>
								</tr>
								<tr>
									<td align="center">E-Mail: <a href="mailto:info@vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: #666;">info@vtecrm.com</a></td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
			<td width="10">&nbsp;</td>
		</tr>
	</tbody>
</table>';
	$adb->updateClob($table_prefix.'_emailtemplates','body',"templatename = '{$templatename}'",$body);
}

$templatename = 'Proposta accettata';
$result = $adb->query("select templatename, body from {$table_prefix}_emailtemplates where body LIKE '%VTE 5.1%' and templatename = '{$templatename}'");
if ($result && $adb->num_rows($result) > 0) {
	$body = '<table align="center" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; text-decoration: none;" width="700">
	<tbody>
		<tr>
			<td width="10">&nbsp;</td>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="background-color: #f3f3f3; font-family: Arial,Helvetica,sans-serif; font-size: 14px; font-weight: normal; line-height: 25px;" width="100%">
							<tbody>
								<tr>
									<td align="center" rowspan="4">$logo$</td>
									<td align="center">&nbsp;</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px; font-family: Arial,Helvetica,sans-serif; font-size: 24px; color: #2c80c8; font-weight: bolder; line-height: 35px;">VTE 16.09</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px; color: #2c80c8;">www.vtecrm.com</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);" width="100%">
							<tbody>
								<tr>
									<td valign="top">
									<table border="0" cellpadding="5" cellspacing="0" width="100%">
										<tbody>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">&nbsp;</td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 14px; color: rgb(22, 72, 134); font-weight: bolder; line-height: 15px;">Gentile $Contacts||lastname$,</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; text-align: justify; line-height: 20px;">
												<div id="gt-res-content">
												<div dir="ltr"><span id="result_box" lang="it" xml:lang="it"><span title="Fai clic per visualizzare le traduzioni alternative">La vostra proposta</span> <span title="Fai clic per visualizzare le traduzioni alternative">sul</span> <span title="Fai clic per visualizzare le traduzioni alternative">progetto</span> <span title="Fai clic per visualizzare le traduzioni alternative">XYZW</span> <span title="Fai clic per visualizzare le traduzioni alternative">&egrave;</span> <span title="Fai clic per visualizzare le traduzioni alternative">stata valutata ed </span><span title="Fai clic per visualizzare le traduzioni alternative">accettata n</span><span title="Fai clic per visualizzare le traduzioni alternative">ella sua</span> <span title="Fai clic per visualizzare le traduzioni alternative">interezza.</span><br />
												<br />
												<span title="Fai clic per visualizzare le traduzioni alternative">Siamo ansiosi di veder partire </span><span title="Fai clic per visualizzare le traduzioni alternative">questo</span> nuovo <span title="Fai clic per visualizzare le traduzioni alternative">progetto</span> <span title="Fai clic per visualizzare le traduzioni alternative">e</span> <span title="Fai clic per visualizzare le traduzioni alternative">siamo lieti</span> <span title="Fai clic per visualizzare le traduzioni alternative">di avere</span> <span title="Fai clic per visualizzare le traduzioni alternative">l&#39;opportunit&agrave;</span> <span title="Fai clic per visualizzare le traduzioni alternative">di lavorare</span> <span title="Fai clic per visualizzare le traduzioni alternative">assieme</span><span title="Fai clic per visualizzare le traduzioni alternative"> alla vostra zienda. Confessiamo che aspettavamo da un po&#39; questa opportunit&agrave; ed ora che si &egrave; presentata, ne siamo davvero orgogliosi.</span><br />
												<br />
												<span title="Fai clic per visualizzare le traduzioni alternative">Cogliamo </span><span title="Fai clic per visualizzare le traduzioni alternative"> l&#39;occasione per</span> <span title="Fai clic per visualizzare le traduzioni alternative">invitarvi</span> <span title="Fai clic per visualizzare le traduzioni alternative">per</span> <span title="Fai clic per visualizzare le traduzioni alternative">una partita di golf</span> <span title="Fai clic per visualizzare le traduzioni alternative">alle</span> <span title="Fai clic per visualizzare le traduzioni alternative">09:00</span> di <span title="Fai clic per visualizzare le traduzioni alternative">Mercoled&igrave; mattina</span> <span title="Fai clic per visualizzare le traduzioni alternative">presso</span> <span title="Fai clic per visualizzare le traduzioni alternative">la</span> <span title="Fai clic per visualizzare le traduzioni alternative">Gemelli</span> <span title="Fai clic per visualizzare le traduzioni alternative">Ground</span><span title="Fai clic per visualizzare le traduzioni alternative"> per avere l&#39;opportunit&agrave; di conoscerci meglio.</span><br />
												<br />
												<span title="Fai clic per visualizzare le traduzioni alternative">Nel frattempo porgiamo cordiali saluti.</span></span></div>
												</div>
												</td>
											</tr>
											<tr>
												<td align="center">&nbsp;</td>
											</tr>
											<tr>
												<td align="right"><br />
												<br />
												<strong style="padding: 2px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: bold;">Cordialmente</strong></td>
											</tr>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; line-height: 20px;">Lo Staff VTECRM</td>
											</tr>
											<tr>
												<td align="right"><a href="http://www.vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">www.vtecrm.com</a></td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
										</tbody>
									</table>
									</td>
									<td valign="top" width="1%">&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="5" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: #666; font-weight: normal; line-height: 15px; background-color: #f3f3f3;" width="100%">
							<tbody>
								<tr>
									<td align="center">VTECRM LIMITED - 38 Craven Street London WC2N 5NG - Registration No. 08337393</td>
								</tr>
								<tr>
									<td align="center">VAT No. 166 1940 00 - Phone (+44) 2035298324</td>
								</tr>
								<tr>
									<td align="center">E-Mail: <a href="mailto:info@vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: #666;">info@vtecrm.com</a></td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
			<td width="10">&nbsp;</td>
		</tr>
	</tbody>
</table>';
	$adb->updateClob($table_prefix.'_emailtemplates','body',"templatename = '{$templatename}'",$body);
}

$templatename = 'Merce ricevuta';
$result = $adb->query("select templatename, body from {$table_prefix}_emailtemplates where body LIKE '%VTE 5.1%' and templatename = '{$templatename}'");
if ($result && $adb->num_rows($result) > 0) {
	$body = '<table align="center" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; text-decoration: none;" width="700">
	<tbody>
		<tr>
			<td width="10">&nbsp;</td>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="background-color: #f3f3f3; font-family: Arial,Helvetica,sans-serif; font-size: 14px; font-weight: normal; line-height: 25px;" width="100%">
							<tbody>
								<tr>
									<td align="center" rowspan="4">$logo$</td>
									<td align="center">&nbsp;</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px; font-family: Arial,Helvetica,sans-serif; font-size: 24px; color: #2c80c8; font-weight: bolder; line-height: 35px;">VTE 16.09</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px; color: #2c80c8;">www.vtecrm.com</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);" width="100%">
							<tbody>
								<tr>
									<td valign="top">
									<table border="0" cellpadding="5" cellspacing="0" width="100%">
										<tbody>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">&nbsp;</td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 14px; color: rgb(22, 72, 134); font-weight: bolder; line-height: 15px;">&nbsp;</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; text-align: justify; line-height: 20px;">
												<div id="gt-res-content">
												<div dir="ltr"><span id="result_box" lang="it" xml:lang="it"><span title="Fai clic per visualizzare le traduzioni alternative">Il</span> <span title="Fai clic per visualizzare le traduzioni alternative">sottoscritto </span> <span title="Fai clic per visualizzare le traduzioni alternative">conferma la ricezione</span> <span title="Fai clic per visualizzare le traduzioni alternative">della merce</span><span title="Fai clic per visualizzare le traduzioni alternative"> e si impegna a provvedere al</span><span title="Fai clic per visualizzare le traduzioni alternative"> pagamento</span> <span title="Fai clic per visualizzare le traduzioni alternative">della stessa quanto prima.</span><br />
												<br />
												<span title="Fai clic per visualizzare le traduzioni alternative">Firmato</span> Mario Rossi</span></div>
												</div>

												<p>&nbsp;</p>
												</td>
											</tr>
											<tr>
												<td align="center">&nbsp;</td>
											</tr>
											<tr>
												<td align="right"><br />
												<br />
												<strong style="padding: 2px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: bold;">Cordialmente</strong></td>
											</tr>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; line-height: 20px;">Lo Staff VTECRM</td>
											</tr>
											<tr>
												<td align="right"><a href="http://www.vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">www.vtecrm.com</a></td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
										</tbody>
									</table>
									</td>
									<td valign="top" width="1%">&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="5" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: #666; font-weight: normal; line-height: 15px; background-color: #f3f3f3;" width="100%">
							<tbody>
								<tr>
									<td align="center">VTECRM LIMITED - 38 Craven Street London WC2N 5NG - Registration No. 08337393</td>
								</tr>
								<tr>
									<td align="center">VAT No. 166 1940 00 - Phone (+44) 2035298324</td>
								</tr>
								<tr>
									<td align="center">E-Mail: <a href="mailto:info@vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: #666;">info@vtecrm.com</a></td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
			<td width="10">&nbsp;</td>
		</tr>
	</tbody>
</table>';
	$adb->updateClob($table_prefix.'_emailtemplates','body',"templatename = '{$templatename}'",$body);
}

$templatename = 'Ordine accettato';
$result = $adb->query("select templatename, body from {$table_prefix}_emailtemplates where body LIKE '%VTE 5.1%' and templatename = '{$templatename}'");
if ($result && $adb->num_rows($result) > 0) {
	$body = '<table align="center" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; text-decoration: none;" width="700">
	<tbody>
		<tr>
			<td width="10">&nbsp;</td>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="background-color: #f3f3f3; font-family: Arial,Helvetica,sans-serif; font-size: 14px; font-weight: normal; line-height: 25px;" width="100%">
							<tbody>
								<tr>
									<td align="center" rowspan="4">$logo$</td>
									<td align="center">&nbsp;</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px; font-family: Arial,Helvetica,sans-serif; font-size: 24px; color: #2c80c8; font-weight: bolder; line-height: 35px;">VTE 16.09</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px;color: #2c80c8;">www.vtecrm.com</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);" width="100%">
							<tbody>
								<tr>
									<td valign="top">
									<table border="0" cellpadding="5" cellspacing="0" width="100%">
										<tbody>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">&nbsp;</td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 14px; color: rgb(22, 72, 134); font-weight: bolder; line-height: 15px;">Gentile $Contacts||lastname$,</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; text-align: justify; line-height: 20px;">
												<div id="gt-res-content">
												<div dir="ltr"><span id="result_box" lang="it" xml:lang="it"><span title="Fai clic per visualizzare le traduzioni alternative">Abbiamo ricevuto il vostro ordine. L&#39;ordine &egrave; definitivo e vincolante per entrambe le parti.</span><br />
												<span title="Fai clic per visualizzare le traduzioni alternative">Per recedere dall&#39;ordine avete 10gg lavorativi di tempo e potete farlo inoltrando un&#39;email a info@vtecrm.com, annotando il numero d&#39;ordine e la volont&agrave; di recedervi.</span><br />
												<br />
												<span title="Fai clic per visualizzare le traduzioni alternative">Grazie per l&#39;attenzione e la preferenza accordataci.</span></span></div>
												</div>

												<p>&nbsp;</p>
												</td>
											</tr>
											<tr>
												<td align="center">&nbsp;</td>
											</tr>
											<tr>
												<td align="right"><br />
												<br />
												<strong style="padding: 2px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: bold;">Cordialmente</strong></td>
											</tr>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; line-height: 20px;">Lo Staff VTECRM</td>
											</tr>
											<tr>
												<td align="right"><a href="http://www.vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">www.vtecrm.com</a></td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
										</tbody>
									</table>
									</td>
									<td valign="top" width="1%">&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="5" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: #666; font-weight: normal; line-height: 15px; background-color: #f3f3f3;" width="100%">
							<tbody>
								<tr>
									<td align="center">VTECRM LIMITED - 38 Craven Street London WC2N 5NG - Registration No. 08337393</td>
								</tr>
								<tr>
									<td align="center">VAT No. 166 1940 00 - Phone (+44) 2035298324</td>
								</tr>
								<tr>
									<td align="center">E-Mail: <a href="mailto:info@vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: #666;">info@vtecrm.com</a></td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
			<td width="10">&nbsp;</td>
		</tr>
	</tbody>
</table>';
	$adb->updateClob($table_prefix.'_emailtemplates','body',"templatename = '{$templatename}'",$body);
}

$templatename = 'Cambio indirizzo';
$result = $adb->query("select templatename, body from {$table_prefix}_emailtemplates where body LIKE '%VTE 5.1%' and templatename = '{$templatename}'");
if ($result && $adb->num_rows($result) > 0) {
	$body = '<table align="center" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; text-decoration: none;" width="700">
	<tbody>
		<tr>
			<td width="10">&nbsp;</td>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="background-color: #f3f3f3; font-family: Arial,Helvetica,sans-serif; font-size: 14px; font-weight: normal; line-height: 25px;" width="100%">
							<tbody>
								<tr>
									<td align="center" rowspan="4">$logo$</td>
									<td align="center">&nbsp;</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px; font-family: Arial,Helvetica,sans-serif; font-size: 24px; color: #2c80c8; font-weight: bolder; line-height: 35px;">VTE 16.09</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px; color: #2c80c8;">www.vtecrm.com</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);" width="100%">
							<tbody>
								<tr>
									<td valign="top">
									<table border="0" cellpadding="5" cellspacing="0" width="100%">
										<tbody>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">&nbsp;</td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 14px; color: rgb(22, 72, 134); font-weight: bolder; line-height: 15px;">Gentile $Contacts||lastname$,</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; text-align: justify; line-height: 20px;">
												<div id="gt-res-content">
												<div dir="ltr"><span id="result_box" lang="it" xml:lang="it"><span title="Fai clic per visualizzare le traduzioni alternative">Ci stiamo</span> <span title="Fai clic per visualizzare le traduzioni alternative">trasferendo</span> nella nuova sede che si trova al seguente indirizzo:<br />
												<strong>Via Ciro Menotti 3, c/o Via Fontanelle - San Bonifacio (VR), 37047</strong><br />
												<span title="Fai clic per visualizzare le traduzioni alternative">Il nuovo numero</span> <span title="Fai clic per visualizzare le traduzioni alternative">di telefono</span> <span title="Fai clic per visualizzare le traduzioni alternative">&egrave;</span> 045 51 11 073<br />
												<br />
												<span title="Fai clic per visualizzare le traduzioni alternative">Aggiornate i vostri contatti e continuate a seguirci!</span></span></div>
												</div>

												<p>&nbsp;</p>
												</td>
											</tr>
											<tr>
												<td align="center">&nbsp;</td>
											</tr>
											<tr>
												<td align="right"><br />
												<br />
												<strong style="padding: 2px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: bold;">Cordialmente</strong></td>
											</tr>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; line-height: 20px;">Lo Staff VTECRM</td>
											</tr>
											<tr>
												<td align="right"><a href="http://www.vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">www.vtecrm.com</a></td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
										</tbody>
									</table>
									</td>
									<td valign="top" width="1%">&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="5" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: #666; font-weight: normal; line-height: 15px; background-color: #f3f3f3;" width="100%">
							<tbody>
								<tr>
									<td align="center">VTECRM LIMITED - 38 Craven Street London WC2N 5NG - Registration No. 08337393</td>
								</tr>
								<tr>
									<td align="center">VAT No. 166 1940 00 - Phone (+44) 2035298324</td>
								</tr>
								<tr>
									<td align="center">E-Mail: <a href="mailto:info@vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: #666;">info@vtecrm.com</a></td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
			<td width="10">&nbsp;</td>
		</tr>
	</tbody>
</table>';
	$adb->updateClob($table_prefix.'_emailtemplates','body',"templatename = '{$templatename}'",$body);
}

$templatename = 'Successione';
$result = $adb->query("select templatename, body from {$table_prefix}_emailtemplates where body LIKE '%VTE 5.1%' and templatename = '{$templatename}'");
if ($result && $adb->num_rows($result) > 0) {
	$body = '<table align="center" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; text-decoration: none;" width="700">
	<tbody>
		<tr>
			<td width="10">&nbsp;</td>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="background-color: #f3f3f3; font-family: Arial,Helvetica,sans-serif; font-size: 14px; font-weight: normal; line-height: 25px;" width="100%">
							<tbody>
								<tr>
									<td align="center" rowspan="4">$logo$</td>
									<td align="center">&nbsp;</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px; font-family: Arial,Helvetica,sans-serif; font-size: 24px; color: #2c80c8; font-weight: bolder; line-height: 35px;">VTE 16.09</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px; color: #2c80c8;">www.vtecrm.com</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);" width="100%">
							<tbody>
								<tr>
									<td valign="top">
									<table border="0" cellpadding="5" cellspacing="0" width="100%">
										<tbody>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">&nbsp;</td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 14px; color: rgb(22, 72, 134); font-weight: bolder; line-height: 15px;">Gentile $Contacts||lastname$,</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; text-align: justify; line-height: 20px;">
												<p><span id="result_box" lang="it" xml:lang="it"><span title="Fai clic per visualizzare le traduzioni alternative">La presente per ringraziarvi per </span><span title="Fai clic per visualizzare le traduzioni alternative"> l&#39;opportunit&agrave; che ci avete dato di</span> potervi <span title="Fai clic per visualizzare le traduzioni alternative">incontrare</span> di persona.<br />
												<br />
												<span title="Fai clic per visualizzare le traduzioni alternative">Sappiamo</span> <span title="Fai clic per visualizzare le traduzioni alternative">che</span> <span title="Fai clic per visualizzare le traduzioni alternative">Mario Rossi </span> <span title="Fai clic per visualizzare le traduzioni alternative">&egrave; rimasto per molto tempo nella vostra azienda ed ha </span><span title="Fai clic per visualizzare le traduzioni alternative">personalmente</span> <span title="Fai clic per visualizzare le traduzioni alternative">discusso</span> <span title="Fai clic per visualizzare le traduzioni alternative">con noi la sua scelta. L</span><span title="Fai clic per visualizzare le traduzioni alternative">a sua profonda relazione</span> ed amicizia <span title="Fai clic per visualizzare le traduzioni alternative">che</span> <span title="Fai clic per visualizzare le traduzioni alternative">aveva</span> <span title="Fai clic per visualizzare le traduzioni alternative">con i membri della vostra</span> <span title="Fai clic per visualizzare le traduzioni alternative">azienda</span> &egrave; stata qualcosa di unico.<br />
												<span title="Fai clic per visualizzare le traduzioni alternative">Ci mancher&agrave; moltissimo e siamo sicuri che continuer&agrave; a</span> fornire un prezioso servizio anche nella vostra societ&agrave;.<br />
												<br />
												<span title="Fai clic per visualizzare le traduzioni alternative">La vostra ospitalit&agrave; e la partenza del Sig.Rossi ci hanno veramente commosso.</span><br />
												<br />
												<span title="Fai clic per visualizzare le traduzioni alternative">Vi ringraziamo</span> <span title="Fai clic per visualizzare le traduzioni alternative">ancora una volta</span><span title="Fai clic per visualizzare le traduzioni alternative">.</span></span></p>
												</td>
											</tr>
											<tr>
												<td align="center">&nbsp;</td>
											</tr>
											<tr>
												<td align="right"><br />
												<br />
												<strong style="padding: 2px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: bold;">Cordialmente</strong></td>
											</tr>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; line-height: 20px;">Lo Staff VTECRM</td>
											</tr>
											<tr>
												<td align="right"><a href="http://www.vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">www.vtecrm.com</a></td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
										</tbody>
									</table>
									</td>
									<td valign="top" width="1%">&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="5" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: #666; font-weight: normal; line-height: 15px; background-color: #f3f3f3;" width="100%">
							<tbody>
								<tr>
									<td align="center">VTECRM LIMITED - 38 Craven Street London WC2N 5NG - Registration No. 08337393</td>
								</tr>
								<tr>
									<td align="center">VAT No. 166 1940 00 - Phone (+44) 2035298324</td>
								</tr>
								<tr>
									<td align="center">E-Mail: <a href="mailto:info@vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: #666;">info@vtecrm.com</a></td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
			<td width="10">&nbsp;</td>
		</tr>
	</tbody>
</table>';
	$adb->updateClob($table_prefix.'_emailtemplates','body',"templatename = '{$templatename}'",$body);
}

$templatename = 'Obiettivo raggiunto!';
$result = $adb->query("select templatename, body from {$table_prefix}_emailtemplates where body LIKE '%VTE 5.1%' and templatename = '{$templatename}'");
if ($result && $adb->num_rows($result) > 0) {
	$body = '<table align="center" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; text-decoration: none;" width="700">
	<tbody>
		<tr>
			<td width="10">&nbsp;</td>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="background-color: #f3f3f3; font-family: Arial,Helvetica,sans-serif; font-size: 14px; font-weight: normal; line-height: 25px;" width="100%">
							<tbody>
								<tr>
									<td align="center" rowspan="4">$logo$</td>
									<td align="center">&nbsp;</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px; font-family: Arial,Helvetica,sans-serif; font-size: 24px; color: #2c80c8; font-weight: bolder; line-height: 35px;">VTE 16.09</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px; color: #2c80c8;">www.vtecrm.com</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);" width="100%">
							<tbody>
								<tr>
									<td valign="top">
									<table border="0" cellpadding="5" cellspacing="0" width="100%">
										<tbody>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">&nbsp;</td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 14px; color: rgb(22, 72, 134); font-weight: bolder; line-height: 15px;">Gentile $Contacts||lastname$,</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; text-align: justify; line-height: 20px;">
												<p><span id="result_box" lang="it" xml:lang="it"><span title="Fai clic per visualizzare le traduzioni alternative">Congratulazioni</span><span title="Fai clic per visualizzare le traduzioni alternative">!</span><br />
												<br />
												Siamo orgogliosi di annunciare che il totale vendite sino ad ora raggiunto, ammonta a 100.000,00 &euro;uro.</span></p>

												<p><span lang="it" xml:lang="it">Pe <span title="Fai clic per visualizzare le traduzioni alternative"> la</span> <span title="Fai clic per visualizzare le traduzioni alternative">prima volta</span> <span title="Fai clic per visualizzare le traduzioni alternative">abbiamo</span> <span title="Fai clic per visualizzare le traduzioni alternative">superato</span> <span title="Fai clic per visualizzare le traduzioni alternative">l&#39;obiettivo</span> <span title="Fai clic per visualizzare le traduzioni alternative">di quasi il</span> <span title="Fai clic per visualizzare le traduzioni alternative">30</span><span title="Fai clic per visualizzare le traduzioni alternative">%</span> ed a<span title="Fai clic per visualizzare le traduzioni alternative">bbiamo</span> <span title="Fai clic per visualizzare le traduzioni alternative">anche</span> <span title="Fai clic per visualizzare le traduzioni alternative">battuto il</span> <span title="Fai clic per visualizzare le traduzioni alternative">record</span> <span title="Fai clic per visualizzare le traduzioni alternative">precedente</span> riguardante il <span title="Fai clic per visualizzare le traduzioni alternative">trimestre</span> scorso del <span title="Fai clic per visualizzare le traduzioni alternative"> 75</span><span title="Fai clic per visualizzare le traduzioni alternative">%</span><span title="Fai clic per visualizzare le traduzioni alternative">!</span><br />
												<br />
												<span title="Fai clic per visualizzare le traduzioni alternative">Dobbiamo festeggiare, questi sono risultati memorabili!</span></span></p>
												</td>
											</tr>
											<tr>
												<td align="center">&nbsp;</td>
											</tr>
											<tr>
												<td align="right"><br />
												<br />
												<strong style="padding: 2px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: bold;">Cordialmente</strong></td>
											</tr>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; line-height: 20px;">Lo Staff VTECRM</td>
											</tr>
											<tr>
												<td align="right"><a href="http://www.vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">www.vtecrm.com</a></td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
										</tbody>
									</table>
									</td>
									<td valign="top" width="1%">&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="5" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: #666; font-weight: normal; line-height: 15px; background-color: #f3f3f3;" width="100%">
							<tbody>
								<tr>
									<td align="center">VTECRM LIMITED - 38 Craven Street London WC2N 5NG - Registration No. 08337393</td>
								</tr>
								<tr>
									<td align="center">VAT No. 166 1940 00 - Phone (+44) 2035298324</td>
								</tr>
								<tr>
									<td align="center">E-Mail: <a href="mailto:info@vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: #666;">info@vtecrm.com</a></td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
			<td width="10">&nbsp;</td>
		</tr>
	</tbody>
</table>';
	$adb->updateClob($table_prefix.'_emailtemplates','body',"templatename = '{$templatename}'",$body);
}

$templatename = 'Ringraziamenti';
$result = $adb->query("select templatename, body from {$table_prefix}_emailtemplates where body LIKE '%VTE 5.1%' and templatename = '{$templatename}'");
if ($result && $adb->num_rows($result) > 0) {
	$body = '<table align="center" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; text-decoration: none;" width="700">
	<tbody>
		<tr>
			<td width="10">&nbsp;</td>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="background-color: #f3f3f3; font-family: Arial,Helvetica,sans-serif; font-size: 14px; font-weight: normal; line-height: 25px;" width="100%">
							<tbody>
								<tr>
									<td align="center" rowspan="4">$logo$</td>
									<td align="center">&nbsp;</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px; font-family: Arial,Helvetica,sans-serif; font-size: 24px; color: #2c80c8; font-weight: bolder; line-height: 35px;">VTE 16.09</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px;color: #2c80c8;">www.vtecrm.com</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);" width="100%">
							<tbody>
								<tr>
									<td valign="top">
									<table border="0" cellpadding="5" cellspacing="0" width="100%">
										<tbody>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">&nbsp;</td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 14px; color: rgb(22, 72, 134); font-weight: bolder; line-height: 15px;">Gentile $Contacts||lastname$,</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; text-align: justify; line-height: 20px;">
												<p><span id="result_box" lang="it" xml:lang="it"><span title="Fai clic per visualizzare le traduzioni alternative">Grazie per</span> <span title="Fai clic per visualizzare le traduzioni alternative">la </span> <span title="Fai clic per visualizzare le traduzioni alternative">fiducia accordataci</span><span title="Fai clic per visualizzare le traduzioni alternative">.</span><br />
												<span title="Fai clic per visualizzare le traduzioni alternative">Siamo lieti di annoverarvi tra i nostri clienti e siamo sicuri che questo sar&agrave; l&#39;inizio di una lunga collaborazione.</span><br />
												<br />
												<span title="Fai clic per visualizzare le traduzioni alternative">In caso di</span> <span title="Fai clic per visualizzare le traduzioni alternative">qualsiasi</span> <span title="Fai clic per visualizzare le traduzioni alternative">necessit&agrave;, la preghiamo di contattarci senza esitazione alcuna.</span></span></p>
												</td>
											</tr>
											<tr>
												<td align="center">&nbsp;</td>
											</tr>
											<tr>
												<td align="right"><br />
												<br />
												<strong style="padding: 2px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: bold;">Cordialmente</strong></td>
											</tr>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; line-height: 20px;">Lo Staff VTECRM</td>
											</tr>
											<tr>
												<td align="right"><a href="http://www.vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">www.vtecrm.com</a></td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
										</tbody>
									</table>
									</td>
									<td valign="top" width="1%">&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="5" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: #666; font-weight: normal; line-height: 15px; background-color: #f3f3f3;" width="100%">
							<tbody>
								<tr>
									<td align="center">VTECRM LIMITED - 38 Craven Street London WC2N 5NG - Registration No. 08337393</td>
								</tr>
								<tr>
									<td align="center">VAT No. 166 1940 00 - Phone (+44) 2035298324</td>
								</tr>
								<tr>
									<td align="center">E-Mail: <a href="mailto:info@vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: #666;">info@vtecrm.com</a></td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
			<td width="10">&nbsp;</td>
		</tr>
	</tbody>
</table>';
	$adb->updateClob($table_prefix.'_emailtemplates','body',"templatename = '{$templatename}'",$body);
}

$templatename = 'Dati di registrazione ed accesso';
$result = $adb->query("select templatename, body from {$table_prefix}_emailtemplates where body LIKE '%VTE 5.1%' and templatename = '{$templatename}'");
if ($result && $adb->num_rows($result) > 0) {
	$body = '<table align="center" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; text-decoration: none;" width="700">
	<tbody>
		<tr>
			<td width="10">&nbsp;</td>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="background-color: #f3f3f3; font-family: Arial,Helvetica,sans-serif; font-size: 14px; font-weight: normal; line-height: 25px;" width="100%">
							<tbody>
								<tr>
									<td align="center" rowspan="4">$logo$</td>
									<td align="center">&nbsp;</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px;font-family: Arial,Helvetica,sans-serif; font-size: 24px; color: #2c80c8; font-weight: bolder; line-height: 35px;">Area Riservata</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px;color: #2c80c8;">www.vtecrm.com</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; color: rgb(0, 0, 0);" width="100%">
							<tbody>
								<tr>
									<td valign="top">
									<table border="0" cellpadding="5" cellspacing="0" width="100%">
										<tbody>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">&nbsp;</td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 14px; color: rgb(22, 72, 134); font-weight: bolder; line-height: 15px;">Gentile $contact_name$,</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; text-align: justify; line-height: 20px;">
												<p>Grazie per esserti iscritto<span style="font-weight: bold;">. Ecco le credenziali per l&#39;area riservata:</span></p>

												<table align="center" border="0" cellpadding="10" cellspacing="0" style="width: 300px;" width="75%">
													<tbody>
														<tr>
															<td><br />
															<span style="font-size:12px;"><span style="font-family: lucida sans unicode,lucida grande,sans-serif;">Email : <span style="color:#000000;"><strong> $login_name$</strong></span></span></span></td>
														</tr>
														<tr>
															<td><span style="font-size:12px;"><span style="font-family: lucida sans unicode,lucida grande,sans-serif;">Password: <span style="color:#000000;"><strong> $password$</strong></span></span></span></td>
														</tr>
														<tr>
															<td style="text-align: center; background-color: rgb(204, 204, 204);">$URL$</td>
														</tr>
													</tbody>
												</table>
												</td>
											</tr>
											<tr>
												<td align="center">&nbsp;</td>
											</tr>
										</tbody>
									</table>
									</td>
									<td valign="top" width="1%">&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
			<td width="10">&nbsp;</td>
		</tr>
	</tbody>
</table>';
	$adb->updateClob($table_prefix.'_emailtemplates','body',"templatename = '{$templatename}'",$body);
}

$templatename = 'Il contratto di assistenza scade tra una settimana';
$result = $adb->query("select templatename, body from {$table_prefix}_emailtemplates where body LIKE '%VTE 5.1%' and templatename = '{$templatename}'");
if ($result && $adb->num_rows($result) > 0) {
	$body = '<table align="center" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; text-decoration: none;" width="700">
	<tbody>
		<tr>
			<td width="10">&nbsp;</td>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="background-color: #f3f3f3; font-family: Arial,Helvetica,sans-serif; font-size: 14px; font-weight: normal; line-height: 25px;" width="100%">
							<tbody>
								<tr>
									<td align="center" rowspan="4">$logo$</td>
									<td align="center">&nbsp;</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px;font-family: Arial,Helvetica,sans-serif; font-size: 24px; color: #2c80c8; font-weight: bolder; line-height: 35px;">VTE 16.09</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px;color: #2c80c8;">www.vtecrm.com</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);" width="100%">
							<tbody>
								<tr>
									<td valign="top">
									<table border="0" cellpadding="5" cellspacing="0" width="100%">
										<tbody>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">&nbsp;</td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 14px; color: rgb(22, 72, 134); font-weight: bolder; line-height: 15px;">Gentile $Contacts||lastname$,</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; text-align: justify; line-height: 20px;">Questa email notifica la fine del periodo di assistenza.<br />
												<span style="font-weight: bold;">Priorit&agrave;:</span> Normal<br />
												Il contratto sta per scadere<br />
												Per informazioni contatta info@vtecrm.com<br />
												<br />
												&nbsp;</td>
											</tr>
											<tr>
												<td align="center">&nbsp;</td>
											</tr>
											<tr>
												<td align="right"><br />
												<br />
												<strong style="padding: 2px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: bold;">Cordialmente</strong></td>
											</tr>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; line-height: 20px;">Lo Staff VTECRM</td>
											</tr>
											<tr>
												<td align="right"><a href="http://www.vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">www.vtecrm.com</a></td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
										</tbody>
									</table>
									</td>
									<td valign="top" width="1%">&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="5" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: #666; font-weight: normal; line-height: 15px; background-color: #f3f3f3;" width="100%">
							<tbody>
								<tr>
									<td align="center">VTECRM LIMITED - 38 Craven Street London WC2N 5NG - Registration No. 08337393</td>
								</tr>
								<tr>
									<td align="center">VAT No. 166 1940 00 - Phone (+44) 2035298324</td>
								</tr>
								<tr>
									<td align="center">E-Mail: <a href="mailto:info@vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: #666;">info@vtecrm.com</a></td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
			<td width="10">&nbsp;</td>
		</tr>
	</tbody>
</table>';
	$adb->updateClob($table_prefix.'_emailtemplates','body',"templatename = '{$templatename}'",$body);
}

$templatename = 'Il contratto di assistenza scade tra un mese';
$result = $adb->query("select templatename, body from {$table_prefix}_emailtemplates where body LIKE '%VTE 5.1%' and templatename = '{$templatename}'");
if ($result && $adb->num_rows($result) > 0) {
	$body = '<table align="center" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; text-decoration: none;" width="700">
	<tbody>
		<tr>
			<td width="10">&nbsp;</td>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="background-color: #f3f3f3; font-family: Arial,Helvetica,sans-serif; font-size: 14px; font-weight: normal; line-height: 25px;" width="100%">
							<tbody>
								<tr>
									<td align="center" rowspan="4">$logo$</td>
									<td align="center">&nbsp;</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px;font-family: Arial,Helvetica,sans-serif; font-size: 24px; color: #2c80c8; font-weight: bolder; line-height: 35px;">VTE 16.09</td>
								</tr>
								<tr>
									<td align="right" style="padding-right: 100px;color: #2c80c8;">www.vtecrm.com</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);" width="100%">
							<tbody>
								<tr>
									<td valign="top">
									<table border="0" cellpadding="5" cellspacing="0" width="100%">
										<tbody>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">&nbsp;</td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 14px; color: rgb(22, 72, 134); font-weight: bolder; line-height: 15px;">Gentile $Contacts||lastname$,</td>
											</tr>
											<tr>
												<td style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; text-align: justify; line-height: 20px;">Questa email notifica la fine del periodo di assistenza.<br />
												<span style="font-weight: bold;">Priorit&agrave;:</span> Normal<br />
												Il contratto sta per scadere<br />
												Per informazioni contatta info@vtecrm.com<br />
												<br />
												&nbsp;</td>
											</tr>
											<tr>
												<td align="center">&nbsp;</td>
											</tr>
											<tr>
												<td align="right"><br />
												<br />
												<strong style="padding: 2px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: bold;">Cordialmente</strong></td>
											</tr>
											<tr>
												<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; line-height: 20px;">Lo Staff VTECRM</td>
											</tr>
											<tr>
												<td align="right"><a href="http://www.vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">www.vtecrm.com</a></td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
										</tbody>
									</table>
									</td>
									<td valign="top" width="1%">&nbsp;</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table border="0" cellpadding="5" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: #666; font-weight: normal; line-height: 15px; background-color: #f3f3f3;" width="100%">
							<tbody>
								<tr>
									<td align="center">VTECRM LIMITED - 38 Craven Street London WC2N 5NG - Registration No. 08337393</td>
								</tr>
								<tr>
									<td align="center">VAT No. 166 1940 00 - Phone (+44) 2035298324</td>
								</tr>
								<tr>
									<td align="center">E-Mail: <a href="mailto:info@vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: #666;">info@vtecrm.com</a></td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
			<td width="10">&nbsp;</td>
		</tr>
	</tbody>
</table>';
	$adb->updateClob($table_prefix.'_emailtemplates','body',"templatename = '{$templatename}'",$body);
}
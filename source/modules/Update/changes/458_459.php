<?php
global $adb;

$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['Projects'] = 'packages/vte/mandatory/Projects.zip';
$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
$_SESSION['modules_to_update']['Conditionals'] = 'packages/vte/mandatory/Conditionals.zip';

require_once('modules/SDK/InstallTables.php');

SDK::setLanguageEntries('Users', 'Documents', array('it_it'=>'Descrizione','en_us'=>'Description'));

$instancePDFMaker = Vtiger_Module::getInstance('PDFMaker');
if ($instancePDFMaker) {
	$schema_tables = array(
		'vtiger_pdfmaker_releases'=>
			'<schema version="0.3">
			  <table name="vtiger_pdfmaker_releases">
			  <opt platform="mysql">ENGINE=InnoDB</opt>
			    <field name="id" type="R" size="11">
			      <KEY/>
			    </field>
			    <field name="version" type="C" size="10"/>
			    <field name="date" type="T"/>
			    <field name="updated" type="I" size="1"/>
			  </table>
			</schema>',
		'vtiger_pdfmaker_userstatus'=>
			'<schema version="0.3">
			  <table name="vtiger_pdfmaker_userstatus">
			  <opt platform="mysql">ENGINE=InnoDB</opt>
			    <field name="templateid" type="R" size="11">
			      <KEY/>
			    </field>
				<field name="userid" type="R" size="11">
			      <KEY/>
			    </field>
			    <field name="is_active" type="I" size="1"/>
				<field name="is_default" type="I" size="1"/>
			  </table>
			</schema>',
		'vtiger_pdfmaker_relblocks'=>
			'<schema version="0.3">
			  <table name="vtiger_pdfmaker_relblocks">
			  <opt platform="mysql">ENGINE=InnoDB</opt>
			    <field name="relblockid" type="R" size="11">
			      <KEY/>
			    </field>
			    <field name="name" type="C" size="255"/>
				<field name="module" type="C" size="255"/>
				<field name="secmodule" type="C" size="255"/>
				<field name="block" type="XL"/>
			  </table>
			</schema>',
		'vtiger_pdfmaker_relblockcol'=>
			'<schema version="0.3">
			  <table name="vtiger_pdfmaker_relblockcol">
			  <opt platform="mysql">ENGINE=InnoDB</opt>
			    <field name="colid" type="R" size="19">
			      <KEY/>
			    </field>
				<field name="relblockid" type="R" size="19">
			      <KEY/>
			    </field>
			    <field name="columnname" type="C" size="250"/>
				<field name="sortorder" type="C" size="250"/>
			  </table>
			</schema>',
		'vtiger_pdfmaker_relblckcri'=>
			'<schema version="0.3">
			  <table name="vtiger_pdfmaker_relblckcri">
			  <opt platform="mysql">ENGINE=InnoDB</opt>
			    <field name="relblockid" type="R" size="11">
			      <KEY/>
			    </field>
				<field name="colid" type="R" size="11">
			      <KEY/>
			    </field>
			    <field name="columnname" type="C" size="250"/>
				<field name="comparator" type="C" size="250"/>
				<field name="value" type="C" size="250"/>
				<field name="groupid" type="I" size="11"/>
				<field name="column_condition" type="C" size="250"/>
			  </table>
			</schema>',
		'vtiger_pdfmaker_relblckcri_g'=>
			'<schema version="0.3">
			  <table name="vtiger_pdfmaker_relblckcri_g">
			  <opt platform="mysql">ENGINE=InnoDB</opt>
			    <field name="groupid" type="R" size="11">
			      <KEY/>
			    </field>
				<field name="relblockid" type="R" size="11">
			      <KEY/>
			    </field>
			    <field name="group_condition" type="C" size="250"/>
				<field name="condition_expression" type="C" size="250"/>
			  </table>
			</schema>',
		'vtiger_pdfmaker_relblckdfilt'=>
			'<schema version="0.3">
			  <table name="vtiger_pdfmaker_relblckdfilt">
			  <opt platform="mysql">ENGINE=InnoDB</opt>
			    <field name="datefilterid" type="R" size="11">
			      <KEY/>
			    </field>
			    <field name="datecolumnname" type="C" size="250"/>
				<field name="datefilter" type="C" size="250"/>
				<field name="startdate" type="D"/>
				<field name="enddate" type="D"/>
			  </table>
			</schema>',
	);
	foreach($schema_tables as $table_name => $schema_table) {
		if(!Vtiger_Utils::CheckTable($table_name)) {
			$schema_obj = new adoSchema($adb->database);
			$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
		}
	}

	SDK::setLanguageEntries('PDFMaker', 'LBL_UPDATE_SUCCESS', array('it_it'=>'Aggiornamento riuscito.','en_us'=>'You have successfully finished the update process.'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_PDFMAKER_IMPORT', array('it_it'=>'Importazione Template PDF','en_us'=>'Import PDF Templates'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_STEP_1', array('it_it'=>'Selezionare il file .XML','en_us'=>'Select the .XML File'));  
	SDK::setLanguageEntries('PDFMaker', 'LBL_SELECT_XML_TEXT', array('it_it'=>"Per iniziatr l'importazione, selezionare il file .XML e proseguire. ",'en_us'=>'To start import, browse to locate the .XML file and click on the Next button to Continue. '));  
	SDK::setLanguageEntries('PDFMaker', 'LBL_FILE_LOCATION', array('it_it'=>'File:','en_us'=>'File Location :'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_SETASDEFAULT', array('it_it'=>'Setta a default','en_us'=>'Set as default'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_DEFAULT', array('it_it'=>'(default)','en_us'=>'(default)'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_SETASACTIVE', array('it_it'=>'Attiva','en_us'=>'Set as active'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_SETASINACTIVE', array('it_it'=>'Disattiva','en_us'=>'Set as inactive'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_UNSETASDEFAULT', array('it_it'=>'Togli da default','en_us'=>'Unset as default'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_CRMNOW_DESCRIPTION', array('it_it'=>'Descrizione configurazione PDF','en_us'=>'PDF Configurator description'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_EXPORT_TO_RTF', array('it_it'=>'Esporta in formato RTF','en_us'=>'Export To RTF'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_RELATED_BLOCK_TPL', array('it_it'=>'Blocchi collegati','en_us'=>'Related blocks'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_EDIT_RELATED_BLOCK', array('it_it'=>'Modifica Blocco','en_us'=>'Edit block'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_EDIT_RELATED_BLOCK_BTN', array('it_it'=>'Modifica Blocco','en_us'=>'Manage blocks'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_MANAGE_RELATED_BLOCK', array('it_it'=>'Modifica Blocchi collegati','en_us'=>'Manage related blocks'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_RELATED_BLOCK', array('it_it'=>'Blocchi collegati','en_us'=>'Related Block'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_RELATED_BLOCK_NAME', array('it_it'=>'Nome Blocco','en_us'=>'Block name'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_BLOCK_STYLE', array('it_it'=>'Stile Blocco','en_us'=>'Block style'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_OR', array('it_it'=>'or','en_us'=>'or'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_NEW_GROUP', array('it_it'=>'Nuovo Gruppo','en_us'=>'New Group'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_DELETE_GROUP', array('it_it'=>'Elimina Gruppo','en_us'=>'Delete Group'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_NEW_CONDITION', array('it_it'=>'Nuova Condizione','en_us'=>'New Condition'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_SHOW_STANDARD_FILTERS', array('it_it'=>'Mostra Filtri Standard','en_us'=>'Show Standard Filters'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_RECORD_ID', array('it_it'=>'Record ID','en_us'=>'Record ID'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_PS_NO', array('it_it'=>'Numero Prodotto/Servizio','en_us'=>'Product No/Service No'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_DEACTIVATE', array('it_it'=>'Disattiva licenza','en_us'=>'Deactivate license'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_DEACTIVATE_TITLE', array('it_it'=>'Disattiva licenza','en_us'=>'Deactivate license'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_DEACTIVATE_QUESTION', array('it_it'=>'Sei sicuro di voler disattivare la licenza?','en_us'=>'Do You realy want to deactivate Your license key?'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_DEACTIVATE_DESC', array('it_it'=>'Dopo la disattivazione potrai solamente esportare i template PDF.<br /> Dopo la riattivazione torneranno disponibili le funzionalità standard.','en_us'=>'After deactivation You will be allowed only to export PDFMaker templates.<br /> After reactivation You will get the standard functionality.'));
	SDK::setLanguageEntries('PDFMaker', 'LBL_DEACTIVATE_ERROR', array('it_it'=>'Disattivazione licenza fallita.','en_us'=>'Deactivate the license key failed.'));
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_PRODUCT_POSITION', 'Sequenza');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_CURRENCY_NAME', 'Valuta');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_CURRENCY_CODE', 'Codice Valuta');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_CURRENCY_SYMBOL', 'Simbolo Valuta');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_VARIABLE_USAGEUNIT', 'Unit&agrave; Utilizzo');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_VARIABLE_PRODUCTTOTALAFTERDISCOUNT', 'Totale dopo sconto');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_VARIABLE_TOTALAFTERDISCOUNT', 'Totale dopo sconto');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_VARIABLE_TOTALDISCOUNT_PERCENT', 'Totale sconto (%)');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_PROCUCT_VAT_PERCENT', 'Tasse (%)');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_PRODUCT_VAT_SUM', 'Tasse');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_PRODUCT_TOTAL_VAT', 'Totale con Tasse');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_VARIABLE_VAT_PERCENT', 'Tasse (%)');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_VARIABLE_VAT', 'Tasse');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_VARIABLE_SUMWITHVAT', 'Totale con Tasse');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_VARIABLE_VAT_BLOCK', 'Blocco Tasse');
	SDK::setLanguageEntries('PDFMaker', 'LBL_USER_NOTES', array('it_it'=>'Descrizione','en_us'=>'Description'));
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_ARTICLE', 'Blocco Prodotti');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_ARTICLE_START', 'Inizio Blocco');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_ARTICLE_END', 'Fine Blocco');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_PRODUCT_BLOC_TPL', 'Template Blocco Prodotti');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_NOPRODUCT_BLOC', 'Il modulo corrente non contiene il blocco prodotti.');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_PRODUCT_FIELD_INFO', '* i campi saranno ripetuti per ogni prodotto/servizio.');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_VARIABLE_SUMWITHOUTVAT', 'Totale senza Tasse');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_VARIABLE_SUM', 'Totale Netto');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_VIEWING', 'Dettaglio');
	SDK::setLanguageEntry('PDFMaker', 'it_it', 'LBL_VARIABLE_PRODUCTNAME', 'Nome (nome e commento del prodotto/servizio)');

	$body = '<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tbody>
			<tr>
				<td>
					&nbsp;</td>
				<td width="600">
					<table border="0" cellpadding="0" cellspacing="0" width="600">
						<tbody>
							<tr>
								<td>
									<img height="20" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="1" /></td>
							</tr>
							<tr>
								<td>
									<table border="0" cellpadding="0" cellspacing="0" width="600">
										<tbody>
											<tr>
												<td height="10" width="20">
													<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="20" /></td>
												<td align="center" height="10" width="560">
													&nbsp;</td>
												<td width="20">
													<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="20" /></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table align="center" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="600">
										<tbody>
											<tr>
												<td colspan="4" height="15">
													<img height="15" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="1" /></td>
											</tr>
											<tr>
												<td align="left" valign="middle" width="20">
													<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="20" /></td>
												<td align="left" valign="middle" width="47%">
													<img height="50" src="http://www.crmvillage.biz/newsletter/dilloamico/images/logo.png" width="220" /></td>
												<td align="right" width="47%">
													&nbsp;</td>
												<td align="right" width="20">
													<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="20" /></td>
											</tr>
											<tr>
												<td colspan="4" height="15">
													<img height="15" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="1" /></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
										<tbody>
											<tr>
												<td bgcolor="#FF9933" width="20">
													<img src="http://www.crmvillage.biz/newsletter/dilloamico/images/sx.jpg" /></td>
												<td bgcolor="#FF9933" height="10">
													<img height="10" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="1" /></td>
												<td bgcolor="#FF9933" width="20">
													<img src="http://www.crmvillage.biz/newsletter/dilloamico/images/dx.jpg" /></td>
											</tr>
											<tr>
												<td bgcolor="#FF9933">
													<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="20" /></td>
												<td align="left" bgcolor="#FF9933" height="50" valign="middle">
													<font color="#ffffff" face="Segoe UI" size="5">Dillo ad un amico... VTE 10 &egrave; arrivato!</font></td>
												<td bgcolor="#FF9933">
													<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="20" /></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
										<tbody>
											<tr>
												<td align="right" valign="top" width="20">
													<table border="0" cellpadding="0" cellspacing="0" width="20">
														<tbody>
															<tr>
																<td align="right" bgcolor="#FF9933">
																	<img height="160" src="http://www.crmvillage.biz/newsletter/dilloamico/images/shadow_sx_01_or.jpg" width="17" /></td>
															</tr>
															<tr>
																<td align="right" bgcolor="#FFFFFF">
																	<img height="50" src="http://www.crmvillage.biz/newsletter/dilloamico/images/shadow_sx_02.jpg" width="17" /></td>
															</tr>
														</tbody>
													</table>
												</td>
												<td align="center" valign="top">
													<table border="0" cellpadding="0" cellspacing="0" width="100%">
														<tbody>
															<tr>
																<td bgcolor="#FF9933" height="10">
																	<img height="10" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="1" /></td>
															</tr>
															<tr>
																<td bgcolor="#FFFFFF" height="10">
																	&nbsp;</td>
															</tr>
															<tr>
																<td align="center" bgcolor="#FFFFFF">
																	<img height="170" src="http://www.crmvillage.biz/newsletter/dilloamico/images/img_dilloamico.jpg" width="540" /></td>
															</tr>
														</tbody>
													</table>
												</td>
												<td align="left" valign="top" width="20">
													<table border="0" cellpadding="0" cellspacing="0" width="20">
														<tbody>
															<tr>
																<td align="left" bgcolor="#FF9933">
																	<em><img height="160" src="http://www.crmvillage.biz/newsletter/dilloamico/images/shadow_dx_01_or.jpg" width="17" /></em></td>
															</tr>
															<tr>
																<td align="left" bgcolor="#FFFFFF">
																	<img height="50" src="http://www.crmvillage.biz/newsletter/dilloamico/images/shadow_dx_02.jpg" width="17" /></td>
															</tr>
														</tbody>
													</table>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table align="center" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="600">
										<tbody>
											<tr>
												<td width="20">
													<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="20" /></td>
												<td width="560">
													<p align="justify">
														<font color="#888888" face="Segoe UI" size="3">La versione completamente <strong>free</strong> di VTE CRM fino a 10 utenti, scaricabile direttamente dal nostro sito.</font> <font color="#888888" face="Segoe UI" size="3">Potrai utilizzare VTE CRM senza limiti di tempo direttamente sul tuo pc o server, nel caso di pi&ugrave; utenti, che troverai nella nostra area download.</font></p>
												</td>
												<td width="20">
													<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="20" /></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table border="0" cellpadding="0" cellspacing="0" width="600">
										<tbody>
											<tr>
												<td width="20">
													<img height="20" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="20" /></td>
												<td width="560">
													<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/dotted.gif" width="559" /></td>
												<td width="20">
													<img height="20" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="20" /></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table align="center" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="600">
										<tbody>
											<tr>
												<td align="left" valign="top" width="20">
													<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="20" /></td>
												<td align="left" height="40" valign="top">
													<font color="#ff9900" face="Segoe UI" size="5">Alcune caratteristiche</font></td>
												<td align="left" valign="top" width="20">
													<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="20" /></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
										<tbody>
											<tr>
												<td align="center" bgcolor="#ffffff" valign="middle" width="20">
													<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="20" /></td>
												<td align="center" height="121" valign="middle" width="172">
													<table bgcolor="#336699" border="0" cellpadding="0" cellspacing="0">
														<tbody>
															<tr>
																<td colspan="3">
																	<img height="3" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="1" /></td>
															</tr>
															<tr>
																<td>
																	<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="3" /></td>
																<td>
																	<a href="http://www.youtube.com/watch?v=Z4cSBqtkYik"><img border="0" height="115" src="http://www.crmvillage.biz/newsletter/dilloamico/images/newsletter.jpg" width="166" /></a></td>
																<td>
																	<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="3" /></td>
															</tr>
															<tr>
																<td colspan="3">
																	<img height="3" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="1" /></td>
															</tr>
														</tbody>
													</table>
												</td>
												<td width="22">
													<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="22" /></td>
												<td align="center" height="121" valign="middle" width="172">
													<table bgcolor="#336699" border="0" cellpadding="0" cellspacing="0">
														<tbody>
															<tr>
																<td colspan="3">
																	<img height="3" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="1" /></td>
															</tr>
															<tr>
																<td>
																	<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="3" /></td>
																<td>
																	<a href="http://www.youtube.com/watch?v=q1Ac_F0i42I"><img border="0" height="115" src="http://www.crmvillage.biz/newsletter/dilloamico/images/calendario.jpg" width="166" /></a></td>
																<td>
																	<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="3" /></td>
															</tr>
															<tr>
																<td colspan="3">
																	<img height="3" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="1" /></td>
															</tr>
														</tbody>
													</table>
												</td>
												<td width="22">
													<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="22" /></td>
												<td align="center" height="121" valign="middle" width="172">
													<table bgcolor="#336699" border="0" cellpadding="0" cellspacing="0">
														<tbody>
															<tr>
																<td colspan="3">
																	<img height="3" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="1" /></td>
															</tr>
															<tr>
																<td>
																	<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="3" /></td>
																<td>
																	<img height="115" src="http://www.crmvillage.biz/newsletter/dilloamico/images/webmail.jpg" width="166" /></td>
																<td>
																	<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="3" /></td>
															</tr>
															<tr>
																<td colspan="3">
																	<img height="3" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="1" /></td>
															</tr>
														</tbody>
													</table>
												</td>
												<td align="center" bgcolor="#ffffff" valign="middle" width="20">
													<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="20" /></td>
											</tr>
											<tr>
												<td valign="bottom">
													&nbsp;</td>
												<td height="35" valign="bottom">
													<font color="#999999" face="Segoe UI" size="3">Modulo newsletter </font></td>
												<td>
													&nbsp;</td>
												<td valign="bottom">
													<font color="#999999" face="Segoe UI" size="3">Nuovo calendario</font></td>
												<td>
													&nbsp;</td>
												<td valign="bottom">
													<font color="#999999" face="Segoe UI" size="3">Nuova webmail</font></td>
												<td valign="bottom">
													&nbsp;</td>
											</tr>
											<tr>
												<td valign="top">
													&nbsp;</td>
												<td height="40" valign="top">
													<strong><font color="#336699" face="Segoe UI" size="6">incluso</font></strong></td>
												<td valign="top">
													&nbsp;</td>
												<td valign="top">
													<strong><font color="#336699" face="Segoe UI" size="6">incluso</font></strong></td>
												<td valign="top">
													&nbsp;</td>
												<td valign="top">
													<strong><font color="#336699" face="Segoe UI" size="6">incluso</font></strong></td>
												<td valign="top">
													&nbsp;</td>
											</tr>
											<tr>
												<td bgcolor="#ffffff" valign="top">
													&nbsp;</td>
												<td bgcolor="#ffffff" valign="top">
													<font color="#666666" face="Segoe UI" size="2" style="line-height: 130%">Manda comunicazioni personalizzate a tutti i contatti presenti nel CRM.</font></td>
												<td>
													&nbsp;</td>
												<td bgcolor="#ffffff" valign="top">
													<font color="#666666" face="Segoe UI" size="2">Ancora pi&ugrave; veloce ed intuitivo, con nuove funzionalit&agrave; tutte da scoprire.</font></td>
												<td>
													&nbsp;</td>
												<td bgcolor="#ffffff" valign="top">
													<font color="#666666" face="Segoe UI" size="2" style="line-height: 130%">Ora con autocompletamento e cartella della posta inviata.</font></td>
												<td bgcolor="#ffffff" valign="top">
													&nbsp;</td>
											</tr>
											<tr>
												<td colspan="7" height="15">
													<img height="15" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="1" /></td>
											</tr>
											<tr>
												<td bgcolor="#ffffff" valign="top">
													&nbsp;</td>
												<td bgcolor="#ffffff" colspan="5" valign="top">
													<div align="center">
														<a href="http://code.google.com/p/vtecrm/downloads/list"><img border="0" height="60" src="http://www.crmvillage.biz/newsletter/dilloamico/images/scarica_button.png" width="300" /></a></div>
												</td>
												<td bgcolor="#ffffff" valign="top">
													&nbsp;</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table border="0" cellpadding="0" cellspacing="0" width="600">
										<tbody>
											<tr>
												<td width="20">
													<img height="20" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="20" /></td>
												<td width="560">
													<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/dotted.gif" width="559" /></td>
												<td width="20">
													<img height="20" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="20" /></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
										<tbody>
											<tr>
												<td align="center" valign="middle" width="20">
													<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="20" /></td>
												<td align="center" valign="middle">
													<font color="#666666" face="Segoe UI" size="1" style="line-height: 120%">CRMVILLAGE.BIZ S.r.l. - Via Ciro Menotti 3, c/o Via Fontanelle - San Bonifacio (VR), 37047<br />
													Tel +39 045 5116489 - Fax +39 045 5111073 - P.IVA: 03641400233</font></td>
												<td align="center" valign="middle" width="20">
													<img height="1" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="20" /></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<img height="40" src="http://www.crmvillage.biz/newsletter/dilloamico/images/placeholder.gif" width="1" /></td>
							</tr>
						</tbody>
					</table>
				</td>
				<td>
					&nbsp;</td>
			</tr>
		</tbody>
	</table>';
	$adb->updateClob('vtiger_emailtemplates','body',"templatename='Dillo ad un amico'",$body);
	
	$adb->query('delete from vtiger_pdfmaker_prodbloc_tpl');
	$body = '<table border="1" cellpadding="3" cellspacing="0" style="font-size:10px;" width="100%">
	<tbody>
		<tr bgcolor="#c0c0c0">
			<td style="TEXT-ALIGN: center">
				<span><strong>Pos</strong></span></td>
			<td colspan="2" style="TEXT-ALIGN: center">
				<span><strong>%G_Qty%</strong></span></td>
			<td style="TEXT-ALIGN: center">
				<span><span style="font-weight: bold;">Text</span></span></td>
			<td style="TEXT-ALIGN: center">
				<span><strong>%G_LBL_LIST_PRICE%<br />
				</strong></span></td>
			<td style="text-align: center;">
				<span><strong>%G_LBL_SUB_TOTAL%</strong></span></td>
			<td style="TEXT-ALIGN: center">
				<span><strong>%G_Discount%</strong></span></td>
			<td style="TEXT-ALIGN: center">
				<span><strong>%G_LBL_NET_PRICE%<br />
				</strong></span></td>
			<td style="text-align: center;">
				<span><strong>%G_Tax% (%)</strong></span></td>
			<td style="text-align: center;">
				<span><strong>%G_Tax%</strong> (<strong>$CURRENCYCODE$</strong>)</span></td>
			<td style="text-align: center;">
				<span><strong>%M_Total%</strong></span></td>
		</tr>
		<tr>
			<td colspan="11">
				#PRODUCTBLOC_START#</td>
		</tr>
		<tr>
			<td style="text-align: center; vertical-align: top;">
				$PRODUCTPOSITION$</td>
			<td align="right" valign="top">
				$PRODUCTQUANTITY$</td>
			<td align="left" style="TEXT-ALIGN: center" valign="top">
				$PRODUCTUSAGEUNIT$</td>
			<td align="left" valign="top">
				$PRODUCTNAME$</td>
			<td align="right" style="text-align: right;" valign="top">
				$PRODUCTLISTPRICE$</td>
			<td align="right" style="TEXT-ALIGN: right" valign="top">
				$PRODUCTTOTAL$</td>
			<td align="right" style="TEXT-ALIGN: right" valign="top">
				$PRODUCTDISCOUNT$</td>
			<td align="right" style="text-align: right;" valign="top">
				$PRODUCTSTOTALAFTERDISCOUNT$</td>
			<td align="right" style="text-align: right;" valign="top">
				$PRODUCTVATPERCENT$</td>
			<td align="right" style="text-align: right;" valign="top">
				$PRODUCTVATSUM$</td>
			<td align="right" style="TEXT-ALIGN: right" valign="top">
				$PRODUCTTOTALSUM$</td>
		</tr>
		<tr>
			<td colspan="11">
				#PRODUCTBLOC_END#</td>
		</tr>
		<tr>
			<td colspan="10" style="TEXT-ALIGN: left">
				%G_LBL_TOTAL%</td>
			<td style="TEXT-ALIGN: right">
				$TOTALWITHOUTVAT$</td>
		</tr>
		<tr>
			<td colspan="10" style="TEXT-ALIGN: left">
				%G_Discount%</td>
			<td style="TEXT-ALIGN: right">
				$TOTALDISCOUNT$</td>
		</tr>
		<tr>
			<td colspan="10" style="TEXT-ALIGN: left">
				%G_LBL_NET_TOTAL%</td>
			<td style="TEXT-ALIGN: right">
				$TOTALAFTERDISCOUNT$</td>
		</tr>
		<tr>
			<td colspan="10" style="text-align: left;">
				%G_Tax% $VATPERCENT$ % %G_LBL_LIST_OF% $TOTALAFTERDISCOUNT$</td>
			<td style="text-align: right;">
				$VAT$</td>
		</tr>
		<tr>
			<td colspan="10" style="text-align: left;">
				Total with TAX</td>
			<td style="text-align: right;">
				$TOTALWITHVAT$</td>
		</tr>
		<tr>
			<td colspan="10" style="text-align: left;">
				%G_LBL_SHIPPING_AND_HANDLING_CHARGES%</td>
			<td style="text-align: right;">
				$SHTAXAMOUNT$</td>
		</tr>
		<tr>
			<td colspan="10" style="TEXT-ALIGN: left">
				%G_LBL_TAX_FOR_SHIPPING_AND_HANDLING%</td>
			<td style="TEXT-ALIGN: right">
				$SHTAXTOTAL$</td>
		</tr>
		<tr>
			<td colspan="10" style="TEXT-ALIGN: left">
				%G_Adjustment%</td>
			<td style="TEXT-ALIGN: right">
				$ADJUSTMENT$</td>
		</tr>
		<tr>
			<td colspan="10" style="TEXT-ALIGN: left">
				<span style="font-weight: bold;">%G_LBL_GRAND_TOTAL% </span><strong>($CURRENCYCODE$)</strong></td>
			<td nowrap="nowrap" style="TEXT-ALIGN: right">
				<strong>$TOTAL$</strong></td>
		</tr>
	</tbody>
	</table>';
	$adb->query("insert into vtiger_pdfmaker_prodbloc_tpl (id, name, body) values('1','product block for individual tax',".$adb->getEmptyClob(true).")");
	$adb->updateClob('vtiger_pdfmaker_prodbloc_tpl','body',"id=1",$body);
	
	$body = '<table border="1" cellpadding="3" cellspacing="0" style="font-size:10px;" width="100%">
	<tbody>
		<tr bgcolor="#c0c0c0">
			<td style="TEXT-ALIGN: center">
				<span><strong>Pos</strong></span></td>
			<td colspan="2" style="TEXT-ALIGN: center">
				<span><strong>%G_Qty%</strong></span></td>
			<td style="TEXT-ALIGN: center">
				<span><span style="font-weight: bold;">Text</span></span></td>
			<td style="TEXT-ALIGN: center">
				<span><strong>%G_LBL_LIST_PRICE%<br />
				</strong></span></td>
			<td style="text-align: center;">
				<span><strong>%G_LBL_SUB_TOTAL%</strong></span></td>
			<td style="TEXT-ALIGN: center">
				<span><strong>%G_Discount%</strong></span></td>
			<td style="TEXT-ALIGN: center">
				<span><strong>%G_LBL_NET_PRICE%<br />
				</strong></span></td>
		</tr>
		<tr>
			<td colspan="8">
				#PRODUCTBLOC_START#</td>
		</tr>
		<tr>
			<td style="text-align: center; vertical-align: top;">
				$PRODUCTPOSITION$</td>
			<td align="right" valign="top">
				$PRODUCTQUANTITY$</td>
			<td align="left" style="TEXT-ALIGN: center" valign="top">
				$PRODUCTUSAGEUNIT$</td>
			<td align="left" valign="top">
				$PRODUCTNAME$</td>
			<td align="right" style="text-align: right;" valign="top">
				$PRODUCTLISTPRICE$</td>
			<td align="right" style="TEXT-ALIGN: right" valign="top">
				$PRODUCTTOTAL$</td>
			<td align="right" style="TEXT-ALIGN: right" valign="top">
				$PRODUCTDISCOUNT$</td>
			<td align="right" style="text-align: right;" valign="top">
				$PRODUCTSTOTALAFTERDISCOUNT$</td>
		</tr>
		<tr>
			<td colspan="8">
				#PRODUCTBLOC_END#</td>
		</tr>
		<tr>
			<td colspan="7" style="TEXT-ALIGN: left">
				%G_LBL_TOTAL%</td>
			<td style="TEXT-ALIGN: right">
				$TOTALWITHOUTVAT$</td>
		</tr>
		<tr>
			<td colspan="7" style="TEXT-ALIGN: left">
				%G_Discount%</td>
			<td style="TEXT-ALIGN: right">
				$TOTALDISCOUNT$</td>
		</tr>
		<tr>
			<td colspan="7" style="TEXT-ALIGN: left">
				%G_LBL_NET_TOTAL%</td>
			<td style="TEXT-ALIGN: right">
				$TOTALAFTERDISCOUNT$</td>
		</tr>
		<tr>
			<td colspan="7" style="text-align: left;">
				%G_Tax% $VATPERCENT$ % %G_LBL_LIST_OF% $TOTALAFTERDISCOUNT$</td>
			<td style="text-align: right;">
				$VAT$</td>
		</tr>
		<tr>
			<td colspan="7" style="text-align: left;">
				Total with TAX</td>
			<td style="text-align: right;">
				$TOTALWITHVAT$</td>
		</tr>
		<tr>
			<td colspan="7" style="text-align: left;">
				%G_LBL_SHIPPING_AND_HANDLING_CHARGES%</td>
			<td style="text-align: right;">
				$SHTAXAMOUNT$</td>
		</tr>
		<tr>
			<td colspan="7" style="TEXT-ALIGN: left">
				%G_LBL_TAX_FOR_SHIPPING_AND_HANDLING%</td>
			<td style="TEXT-ALIGN: right">
				$SHTAXTOTAL$</td>
		</tr>
		<tr>
			<td colspan="7" style="TEXT-ALIGN: left">
				%G_Adjustment%</td>
			<td style="TEXT-ALIGN: right">
				$ADJUSTMENT$</td>
		</tr>
		<tr>
			<td colspan="7" style="TEXT-ALIGN: left">
				<span style="font-weight: bold;">%G_LBL_GRAND_TOTAL% </span><strong>($CURRENCYCODE$)</strong></td>
			<td nowrap="nowrap" style="TEXT-ALIGN: right">
				<strong>$TOTAL$</strong></td>
		</tr>
	</tbody>
	</table>';
	$adb->query("insert into vtiger_pdfmaker_prodbloc_tpl (id, name, body) values('2','product block for group tax',".$adb->getEmptyClob(true).")");
	$adb->updateClob('vtiger_pdfmaker_prodbloc_tpl','body',"id=2",$body);
	
	$body = '<p><img alt="" src="http://www.crmvillage.biz/morphsuite/logo.png" /></p>
	<table border="0" cellpadding="1" cellspacing="1" style="font-family: Verdana;" summary="" width="100%">
		<tbody>
			<tr>
				<td style="text-align: left; vertical-align: top; width: 50%;">
					<font size="2"><font size="4"><span style="font-weight: bold;">$INVOICE_ACCOUNT_ID$</span></font><br />
					<br />
					$INVOICE_BILL_STREET$<br />
					$INVOICE_BILL_CODE$ $INVOICE_BILL_CITY$<br />
					$INVOICE_BILL_STATE$</font></td>
				<td style="width: 50%;">
					<p>
						<span style="font-weight: bold;">$COMPANY_NAME$</span></p>
					<p>
						$COMPANY_ADDRESS$<br />
						$COMPANY_ZIP$ $COMPANY_CITY$<br />
						$COMPANY_COUNTRY$</p>
					<p>
						Telefon $COMPANY_PHONE$<br />
						Telefax $COMPANY_FAX$<br />
						<br />
						$COMPANY_WEBSITE$<br />
						<br />
						%M_Invoice Date%: $INVOICE_INVOICEDATE$</p>
				</td>
			</tr>
		</tbody>
	</table>
	<br />
	<br />
	<p>
		<font size="5"><font style="font-weight: bold; font-family: Verdana;">%G_Invoice No% $INVOICE_INVOICE_NO$</font></font></p>
	<table border="1" cellpadding="3" cellspacing="0" style="font-size:10px;" width="100%">
		<tbody>
			<tr bgcolor="#c0c0c0">
				<td style="TEXT-ALIGN: center">
					<span><strong>Pos</strong></span></td>
				<td colspan="2" style="TEXT-ALIGN: center">
					<span><strong>%G_Qty%</strong></span></td>
				<td style="TEXT-ALIGN: center">
					<span><span style="font-weight: bold;">Text</span></span></td>
				<td style="TEXT-ALIGN: center">
					<span><strong>%G_LBL_LIST_PRICE%<br />
					</strong></span></td>
				<td style="text-align: center;">
					<span><strong>%G_LBL_SUB_TOTAL%</strong></span></td>
				<td style="TEXT-ALIGN: center">
					<span><strong>%G_Discount%</strong></span></td>
				<td style="TEXT-ALIGN: center">
					<span><strong>%G_LBL_NET_PRICE%<br />
					</strong></span></td>
				<td style="text-align: center;">
					<span><strong>%G_Tax% (%)</strong></span></td>
				<td style="text-align: center;">
					<span><strong>%G_Tax%</strong> (<strong>$CURRENCYCODE$</strong>)</span></td>
				<td style="text-align: center;">
					<span><strong>%M_Total%</strong></span></td>
			</tr>
			<tr>
				<td colspan="11">
					#PRODUCTBLOC_START#</td>
			</tr>
			<tr>
				<td style="text-align: center; vertical-align: top;">
					$PRODUCTPOSITION$</td>
				<td align="right" valign="top">
					$PRODUCTQUANTITY$</td>
				<td align="left" style="TEXT-ALIGN: center" valign="top">
					$PRODUCTUSAGEUNIT$</td>
				<td align="left" valign="top">
					$PRODUCTNAME$</td>
				<td align="right" style="text-align: right;" valign="top">
					$PRODUCTLISTPRICE$</td>
				<td align="right" style="TEXT-ALIGN: right" valign="top">
					$PRODUCTTOTAL$</td>
				<td align="right" style="TEXT-ALIGN: right" valign="top">
					$PRODUCTDISCOUNT$</td>
				<td align="right" style="text-align: right;" valign="top">
					$PRODUCTSTOTALAFTERDISCOUNT$</td>
				<td align="right" style="text-align: right;" valign="top">
					$PRODUCTVATPERCENT$</td>
				<td align="right" style="text-align: right;" valign="top">
					$PRODUCTVATSUM$</td>
				<td align="right" style="TEXT-ALIGN: right" valign="top">
					$PRODUCTTOTALSUM$</td>
			</tr>
			<tr>
				<td colspan="11">
					#PRODUCTBLOC_END#</td>
			</tr>
			<tr>
				<td colspan="10" style="TEXT-ALIGN: left">
					%G_LBL_NET_TOTAL%</td>
				<td style="TEXT-ALIGN: right">
					$SUBTOTAL$</td>
			</tr>
			<tr>
				<td colspan="10" style="TEXT-ALIGN: left">
					%G_Discount%</td>
				<td style="TEXT-ALIGN: right">
					$TOTALDISCOUNT$</td>
			</tr>
			<tr>
				<td colspan="10" style="text-align: left;">
					%G_LBL_SHIPPING_AND_HANDLING_CHARGES%</td>
				<td style="text-align: right;">
					$SHTAXAMOUNT$</td>
			</tr>
			<tr>
				<td colspan="10" style="TEXT-ALIGN: left">
					%G_LBL_TAX_FOR_SHIPPING_AND_HANDLING%</td>
				<td style="TEXT-ALIGN: right">
					$SHTAXTOTAL$</td>
			</tr>
			<tr>
				<td colspan="10" style="TEXT-ALIGN: left">
					%G_Adjustment%</td>
				<td style="TEXT-ALIGN: right">
					$ADJUSTMENT$</td>
			</tr>
			<tr>
				<td colspan="10" style="TEXT-ALIGN: left">
					<span style="font-weight: bold;">%G_LBL_GRAND_TOTAL% </span><strong>($CURRENCYCODE$)</strong></td>
				<td nowrap="nowrap" style="TEXT-ALIGN: right">
					<strong>$TOTAL$</strong></td>
			</tr>
		</tbody>
	</table>
	<p>
		$INVOICE_TERMS_CONDITIONS$<br />
		&nbsp;</p>';
	$templateid = $adb->getUniqueID('vtiger_pdfmaker');
	$adb->query("insert into vtiger_pdfmaker (templateid, filename, module, body, description, deleted) values($templateid,'Fattura','Invoice',".$adb->getEmptyClob(true).",'Template per Fattura','0')");
	$adb->updateClob('vtiger_pdfmaker','body',"templateid=$templateid",$body);
	$adb->query("INSERT INTO vtiger_pdfmaker_settings (templateid, margin_top, margin_bottom, margin_left, margin_right, format, orientation, decimals, decimal_point, thousands_separator, header, footer, encoding, file_name) VALUES ($templateid, 2.0, 2.0, 2.0, 2.0, 'A4', 'portrait', 2, ',', '', '<p>\r\n	##PAGE##/##PAGES##</p>\r\n', '<p style=\"text-align: center;\">\r\n	<span style=\"font-size:10px;\">$"."COMPANY_NAME"."$ <small>&bull; </small>$"."COMPANY_ADDRESS"."$ <small>&bull; </small> $"."COMPANY_ZIP"."$<small> </small>$"."COMPANY_CITY"."$<small> &bull; </small>$"."COMPANY_STATE"."$</span></p>\r\n','auto',NULL)");
	
	$body = '<p><img alt="" src="http://www.crmvillage.biz/morphsuite/logo.png" /></p>
	<table border="0" cellpadding="1" cellspacing="1" style="font-family: Verdana;" summary="" width="100%">
		<tbody>
			<tr>
				<td align="left" valign="top" width="50%">
					$SALESORDER_ACCOUNT_ID$<br />
					<br />
					$SALESORDER_BILL_STREET$<br />
					<font size="2"> </font>$SALESORDER_BILL_CODE$<font size="2"> </font>$SALESORDER_BILL_CITY$<br />
					$SALESORDER_BILL_STATE$</td>
				<td width="50%">
					<p>
						$COMPANY_NAME$</p>
					<p>
						$COMPANY_ADDRESS$<br />
						$COMPANY_ZIP$ $COMPANY_CITY$<br />
						$COMPANY_COUNTRY$</p>
					<p>
						Telefon $COMPANY_PHONE$<br />
						Telefax $COMPANY_FAX$<br />
						<br />
						$COMPANY_WEBSITE$</p>
					<p>
						<br />
						%G_Due Date%: $SALESORDER_DUEDATE$</p>
				</td>
			</tr>
		</tbody>
	</table>
	<br />
	<br />
	<p>
		<font size="5"><font style="font-weight: bold; font-family: Verdana;">%G_SO Number% $SALESORDER_SALESORDER_NO$</font></font></p>
	<table border="1" cellpadding="3" cellspacing="0" style="font-size:10px;" width="100%">
		<tbody>
			<tr bgcolor="#c0c0c0">
				<td style="TEXT-ALIGN: center">
					<span><strong>Pos</strong></span></td>
				<td colspan="2" style="TEXT-ALIGN: center">
					<span><strong>%G_Qty%</strong></span></td>
				<td style="TEXT-ALIGN: center">
					<span><span style="font-weight: bold;">Text</span></span></td>
				<td style="TEXT-ALIGN: center">
					<span><strong>%G_LBL_LIST_PRICE%<br />
					</strong></span></td>
				<td style="text-align: center;">
					<span><strong>%G_LBL_SUB_TOTAL%</strong></span></td>
				<td style="TEXT-ALIGN: center">
					<span><strong>%G_Discount%</strong></span></td>
				<td style="TEXT-ALIGN: center">
					<span><strong>%G_LBL_NET_PRICE%<br />
					</strong></span></td>
				<td style="text-align: center;">
					<span><strong>%G_Tax% (%)</strong></span></td>
				<td style="text-align: center;">
					<span><strong>%G_Tax%</strong> (<strong>$CURRENCYCODE$</strong>)</span></td>
				<td style="text-align: center;">
					<span><strong>%M_Total%</strong></span></td>
			</tr>
			<tr>
				<td colspan="11">
					#PRODUCTBLOC_START#</td>
			</tr>
			<tr>
				<td style="text-align: center; vertical-align: top;">
					$PRODUCTPOSITION$</td>
				<td align="right" valign="top">
					$PRODUCTQUANTITY$</td>
				<td align="left" style="TEXT-ALIGN: center" valign="top">
					$PRODUCTUSAGEUNIT$</td>
				<td align="left" valign="top">
					$PRODUCTNAME$</td>
				<td align="right" style="text-align: right;" valign="top">
					$PRODUCTLISTPRICE$</td>
				<td align="right" style="TEXT-ALIGN: right" valign="top">
					$PRODUCTTOTAL$</td>
				<td align="right" style="TEXT-ALIGN: right" valign="top">
					$PRODUCTDISCOUNT$</td>
				<td align="right" style="text-align: right;" valign="top">
					$PRODUCTSTOTALAFTERDISCOUNT$</td>
				<td align="right" style="text-align: right;" valign="top">
					$PRODUCTVATPERCENT$</td>
				<td align="right" style="text-align: right;" valign="top">
					$PRODUCTVATSUM$</td>
				<td align="right" style="TEXT-ALIGN: right" valign="top">
					$PRODUCTTOTALSUM$</td>
			</tr>
			<tr>
				<td colspan="11">
					#PRODUCTBLOC_END#</td>
			</tr>
			<tr>
				<td colspan="10" style="TEXT-ALIGN: left">
					%G_LBL_NET_TOTAL%</td>
				<td style="TEXT-ALIGN: right">
					$SUBTOTAL$</td>
			</tr>
			<tr>
				<td colspan="10" style="TEXT-ALIGN: left">
					%G_Discount%</td>
				<td style="TEXT-ALIGN: right">
					$TOTALDISCOUNT$</td>
			</tr>
			<tr>
				<td colspan="10" style="text-align: left;">
					%G_LBL_SHIPPING_AND_HANDLING_CHARGES%</td>
				<td style="text-align: right;">
					$SHTAXAMOUNT$</td>
			</tr>
			<tr>
				<td colspan="10" style="TEXT-ALIGN: left">
					%G_LBL_TAX_FOR_SHIPPING_AND_HANDLING%</td>
				<td style="TEXT-ALIGN: right">
					$SHTAXTOTAL$</td>
			</tr>
			<tr>
				<td colspan="10" style="TEXT-ALIGN: left">
					%G_Adjustment%</td>
				<td style="TEXT-ALIGN: right">
					$ADJUSTMENT$</td>
			</tr>
			<tr>
				<td colspan="10" style="TEXT-ALIGN: left">
					<span style="font-weight: bold;">%G_LBL_GRAND_TOTAL% </span><strong>($CURRENCYCODE$)</strong></td>
				<td nowrap="nowrap" style="TEXT-ALIGN: right">
					<strong>$TOTAL$</strong></td>
			</tr>
		</tbody>
	</table>
	<p>
		$SALESORDER_TERMS_CONDITIONS$<br />
		&nbsp;</p>';
	$templateid = $adb->getUniqueID('vtiger_pdfmaker');
	$adb->query("insert into vtiger_pdfmaker (templateid, filename, module, body, description, deleted) values($templateid,'Ordine di Vendita','SalesOrder',".$adb->getEmptyClob(true).",'Template per Ordine di Vendita','0')");
	$adb->updateClob('vtiger_pdfmaker','body',"templateid=$templateid",$body);
	$adb->query("INSERT INTO vtiger_pdfmaker_settings (templateid, margin_top, margin_bottom, margin_left, margin_right, format, orientation, decimals, decimal_point, thousands_separator, header, footer, encoding, file_name) VALUES ($templateid, 2.0, 2.0, 2.0, 2.0, 'A4', 'portrait', 2, ',', '', '<p>\r\n	##PAGE##/##PAGES##</p>\r\n', '<p style=\"text-align: center;\">\r\n	<span style=\"font-size:10px;\">$"."COMPANY_NAME"."$ <small>&bull; </small>$"."COMPANY_ADDRESS"."$ <small>&bull; </small> $"."COMPANY_ZIP"."$<small> </small>$"."COMPANY_CITY"."$<small> &bull; </small>$"."COMPANY_STATE"."$</span></p>\r\n','auto',NULL)");
	
	$body = '<p><img alt="" src="http://www.crmvillage.biz/morphsuite/logo.png" /></p>
	<table border="0" cellpadding="1" cellspacing="1" style="font-family: Verdana;" summary="" width="100%">
		<tbody>
			<tr>
				<td align="left" valign="top" width="50%">
					<p>
						%M_Contact Name%: $PURCHASEORDER_CONTACT_ID$<br />
						%M_LBL_VENDOR_NAME_TITLE%: $PURCHASEORDER_VENDOR_ID$<br />
						<br />
						$PURCHASEORDER_BILL_STREET$<br />
						<font size="2"> </font>$PURCHASEORDER_BILL_CODE$<font size="2"> </font>$PURCHASEORDER_BILL_CITY$<br />
						$PURCHASEORDER_BILL_STATE$</p>
				</td>
				<td width="50%">
					<p>
						$COMPANY_NAME$</p>
					<p>
						$COMPANY_ADDRESS$<br />
						$COMPANY_ZIP$ $COMPANY_CITY$<br />
						$COMPANY_COUNTRY$</p>
					<p>
						Telefon $COMPANY_PHONE$<br />
						Telefax $COMPANY_FAX$<br />
						<br />
						$COMPANY_WEBSITE$</p>
					<p>
						<br />
						%M_Due Date%: $PURCHASEORDER_DUEDATE$</p>
				</td>
			</tr>
		</tbody>
	</table>
	<br />
	<br />
	<p>
		<font size="5"><font style="font-weight: bold; font-family: Verdana;">%M_PurchaseOrder No% $PURCHASEORDER_PURCHASEORDER_NO$</font></font></p>
	<table border="1" cellpadding="3" cellspacing="0" style="font-size:10px;" width="100%">
		<tbody>
			<tr bgcolor="#c0c0c0">
				<td style="TEXT-ALIGN: center">
					<span><strong>Pos</strong></span></td>
				<td colspan="2" style="TEXT-ALIGN: center">
					<span><strong>%G_Qty%</strong></span></td>
				<td style="TEXT-ALIGN: center">
					<span><span style="font-weight: bold;">Text</span></span></td>
				<td style="TEXT-ALIGN: center">
					<span><strong>%G_LBL_LIST_PRICE%<br />
					</strong></span></td>
				<td style="text-align: center;">
					<span><strong>%G_LBL_SUB_TOTAL%</strong></span></td>
				<td style="TEXT-ALIGN: center">
					<span><strong>%G_Discount%</strong></span></td>
				<td style="TEXT-ALIGN: center">
					<span><strong>%G_LBL_NET_PRICE%<br />
					</strong></span></td>
				<td style="text-align: center;">
					<span><strong>%G_Tax% (%)</strong></span></td>
				<td style="text-align: center;">
					<span><strong>%G_Tax%</strong> (<strong>$CURRENCYCODE$</strong>)</span></td>
				<td style="text-align: center;">
					<span><strong>%M_Total%</strong></span></td>
			</tr>
			<tr>
				<td colspan="11">
					#PRODUCTBLOC_START#</td>
			</tr>
			<tr>
				<td style="text-align: center; vertical-align: top;">
					$PRODUCTPOSITION$</td>
				<td align="right" valign="top">
					$PRODUCTQUANTITY$</td>
				<td align="left" style="TEXT-ALIGN: center" valign="top">
					$PRODUCTUSAGEUNIT$</td>
				<td align="left" valign="top">
					$PRODUCTNAME$</td>
				<td align="right" style="text-align: right;" valign="top">
					$PRODUCTLISTPRICE$</td>
				<td align="right" style="TEXT-ALIGN: right" valign="top">
					$PRODUCTTOTAL$</td>
				<td align="right" style="TEXT-ALIGN: right" valign="top">
					$PRODUCTDISCOUNT$</td>
				<td align="right" style="text-align: right;" valign="top">
					$PRODUCTSTOTALAFTERDISCOUNT$</td>
				<td align="right" style="text-align: right;" valign="top">
					$PRODUCTVATPERCENT$</td>
				<td align="right" style="text-align: right;" valign="top">
					$PRODUCTVATSUM$</td>
				<td align="right" style="TEXT-ALIGN: right" valign="top">
					$PRODUCTTOTALSUM$</td>
			</tr>
			<tr>
				<td colspan="11">
					#PRODUCTBLOC_END#</td>
			</tr>
			<tr>
				<td colspan="10" style="TEXT-ALIGN: left">
					%G_LBL_NET_TOTAL%</td>
				<td style="TEXT-ALIGN: right">
					$SUBTOTAL$</td>
			</tr>
			<tr>
				<td colspan="10" style="TEXT-ALIGN: left">
					%G_Discount%</td>
				<td style="TEXT-ALIGN: right">
					$TOTALDISCOUNT$</td>
			</tr>
			<tr>
				<td colspan="10" style="text-align: left;">
					%G_LBL_SHIPPING_AND_HANDLING_CHARGES%</td>
				<td style="text-align: right;">
					$SHTAXAMOUNT$</td>
			</tr>
			<tr>
				<td colspan="10" style="TEXT-ALIGN: left">
					%G_LBL_TAX_FOR_SHIPPING_AND_HANDLING%</td>
				<td style="TEXT-ALIGN: right">
					$SHTAXTOTAL$</td>
			</tr>
			<tr>
				<td colspan="10" style="TEXT-ALIGN: left">
					%G_Adjustment%</td>
				<td style="TEXT-ALIGN: right">
					$ADJUSTMENT$</td>
			</tr>
			<tr>
				<td colspan="10" style="TEXT-ALIGN: left">
					<span style="font-weight: bold;">%G_LBL_GRAND_TOTAL% </span><strong>($CURRENCYCODE$)</strong></td>
				<td nowrap="nowrap" style="TEXT-ALIGN: right">
					<strong>$TOTAL$</strong></td>
			</tr>
		</tbody>
	</table>
	<p>
		$PURCHASEORDER_TERMS_CONDITIONS$<br />
		&nbsp;</p>';
	$templateid = $adb->getUniqueID('vtiger_pdfmaker');
	$adb->query("insert into vtiger_pdfmaker (templateid, filename, module, body, description, deleted) values($templateid,'Ordine di Acquisto','PurchaseOrder',".$adb->getEmptyClob(true).",'Template per Ordine di Acquisto','0')");
	$adb->updateClob('vtiger_pdfmaker','body',"templateid=$templateid",$body);
	$adb->query("INSERT INTO vtiger_pdfmaker_settings (templateid, margin_top, margin_bottom, margin_left, margin_right, format, orientation, decimals, decimal_point, thousands_separator, header, footer, encoding, file_name) VALUES ($templateid, 2.0, 2.0, 2.0, 2.0, 'A4', 'portrait', 2, ',', '', '<p>\r\n	##PAGE##/##PAGES##</p>\r\n', '<p style=\"text-align: center;\">\r\n	<span style=\"font-size:10px;\">$"."COMPANY_NAME"."$ <small>&bull; </small>$"."COMPANY_ADDRESS"."$ <small>&bull; </small> $"."COMPANY_ZIP"."$<small> </small>$"."COMPANY_CITY"."$<small> &bull; </small>$"."COMPANY_STATE"."$</span></p>\r\n','auto',NULL)");
	
	$body = '<p><img alt="" src="http://www.crmvillage.biz/morphsuite/logo.png" /></p>
	<table border="0" cellpadding="1" cellspacing="1" style="font-family: Verdana;" summary="" width="100%">
		<tbody>
			<tr>
			</tr>
			<tr>
				<td align="left" valign="top" width="50%">
					<font size="2"><font size="4"><span style="font-weight: bold;">$QUOTES_ACCOUNT_ID$</span></font><br />
					<br />
					$QUOTES_BILL_STREET$</font><br />
					<font size="2">$QUOTES_BILL_CODE$ $QUOTES_BILL_CITY$</font><br />
					<font size="2">$QUOTES_BILL_STATE$</font></td>
				<td width="50%">
					<p>
						<span style="font-weight: bold;">$COMPANY_NAME$</span></p>
					<p>
						$COMPANY_ADDRESS$<br />
						$COMPANY_ZIP$ $COMPANY_CITY$<br />
						$COMPANY_COUNTRY$</p>
					<p>
						Telefon $COMPANY_PHONE$<br />
						Telefax $COMPANY_FAX$<br />
						<br />
						$COMPANY_WEBSITE$<br />
						<br />
						%M_Valid Till%: $QUOTES_VALIDTILL$</p>
				</td>
			</tr>
		</tbody>
	</table>
	<p>
		<font size="5"><font style="font-weight: bold; font-family: Verdana;">%M_Quote No% $QUOTES_QUOTE_NO$</font></font></p>
	<table border="1" cellpadding="3" cellspacing="0" style="font-size:10px;" width="100%">
		<tbody>
			<tr bgcolor="#c0c0c0">
				<td style="TEXT-ALIGN: center">
					<span><strong>Pos</strong></span></td>
				<td colspan="2" style="TEXT-ALIGN: center">
					<span><strong>%G_Qty%</strong></span></td>
				<td style="TEXT-ALIGN: center">
					<span><span style="font-weight: bold;">Text</span></span></td>
				<td style="TEXT-ALIGN: center">
					<span><strong>%G_LBL_LIST_PRICE%<br />
					</strong></span></td>
				<td style="text-align: center;">
					<span><strong>%G_LBL_SUB_TOTAL%</strong></span></td>
				<td style="TEXT-ALIGN: center">
					<span><strong>%G_Discount%</strong></span></td>
				<td style="TEXT-ALIGN: center">
					<span><strong>%G_LBL_NET_PRICE%<br />
					</strong></span></td>
				<td style="text-align: center;">
					<span><strong>%G_Tax% (%)</strong></span></td>
				<td style="text-align: center;">
					<span><strong>%G_Tax%</strong> (<strong>$CURRENCYCODE$</strong>)</span></td>
				<td style="text-align: center;">
					<span><strong>%M_Total%</strong></span></td>
			</tr>
			<tr>
				<td colspan="11">
					#PRODUCTBLOC_START#</td>
			</tr>
			<tr>
				<td style="text-align: center; vertical-align: top;">
					$PRODUCTPOSITION$</td>
				<td align="right" valign="top">
					$PRODUCTQUANTITY$</td>
				<td align="left" style="TEXT-ALIGN: center" valign="top">
					$PRODUCTUSAGEUNIT$</td>
				<td align="left" valign="top">
					$PRODUCTNAME$</td>
				<td align="right" style="text-align: right;" valign="top">
					$PRODUCTLISTPRICE$</td>
				<td align="right" style="TEXT-ALIGN: right" valign="top">
					$PRODUCTTOTAL$</td>
				<td align="right" style="TEXT-ALIGN: right" valign="top">
					$PRODUCTDISCOUNT$</td>
				<td align="right" style="text-align: right;" valign="top">
					$PRODUCTSTOTALAFTERDISCOUNT$</td>
				<td align="right" style="text-align: right;" valign="top">
					$PRODUCTVATPERCENT$</td>
				<td align="right" style="text-align: right;" valign="top">
					$PRODUCTVATSUM$</td>
				<td align="right" style="TEXT-ALIGN: right" valign="top">
					$PRODUCTTOTALSUM$</td>
			</tr>
			<tr>
				<td colspan="11">
					#PRODUCTBLOC_END#</td>
			</tr>
			<tr>
				<td colspan="10" style="TEXT-ALIGN: left">
					%G_LBL_NET_TOTAL%</td>
				<td style="TEXT-ALIGN: right">
					$SUBTOTAL$</td>
			</tr>
			<tr>
				<td colspan="10" style="TEXT-ALIGN: left">
					%G_Discount%</td>
				<td style="TEXT-ALIGN: right">
					$TOTALDISCOUNT$</td>
			</tr>
			<tr>
				<td colspan="10" style="text-align: left;">
					%G_LBL_SHIPPING_AND_HANDLING_CHARGES%</td>
				<td style="text-align: right;">
					$SHTAXAMOUNT$</td>
			</tr>
			<tr>
				<td colspan="10" style="TEXT-ALIGN: left">
					%G_LBL_TAX_FOR_SHIPPING_AND_HANDLING%</td>
				<td style="TEXT-ALIGN: right">
					$SHTAXTOTAL$</td>
			</tr>
			<tr>
				<td colspan="10" style="TEXT-ALIGN: left">
					%G_Adjustment%</td>
				<td style="TEXT-ALIGN: right">
					$ADJUSTMENT$</td>
			</tr>
			<tr>
				<td colspan="10" style="TEXT-ALIGN: left">
					<span style="font-weight: bold;">%G_LBL_GRAND_TOTAL% </span><strong>($CURRENCYCODE$)</strong></td>
				<td nowrap="nowrap" style="TEXT-ALIGN: right">
					<strong>$TOTAL$</strong></td>
			</tr>
		</tbody>
	</table>
	<p>
		<br />
		&nbsp;</p>
	<p>
		$QUOTES_TERMS_CONDITIONS$</p>';
	$templateid = $adb->getUniqueID('vtiger_pdfmaker');
	$adb->query("insert into vtiger_pdfmaker (templateid, filename, module, body, description, deleted) values($templateid,'Preventivo','Quotes',".$adb->getEmptyClob(true).",'Template per Preventivo','0')");
	$adb->updateClob('vtiger_pdfmaker','body',"templateid=$templateid",$body);
	$adb->query("INSERT INTO vtiger_pdfmaker_settings (templateid, margin_top, margin_bottom, margin_left, margin_right, format, orientation, decimals, decimal_point, thousands_separator, header, footer, encoding, file_name) VALUES ($templateid, 2.0, 2.0, 2.0, 2.0, 'A4', 'portrait', 2, ',', '', '<p>\r\n	##PAGE##/##PAGES##</p>\r\n', '<p style=\"text-align: center;\">\r\n	<span style=\"font-size:10px;\">$"."COMPANY_NAME"."$ <small>&bull; </small>$"."COMPANY_ADDRESS"."$ <small>&bull; </small> $"."COMPANY_ZIP"."$<small> </small>$"."COMPANY_CITY"."$<small> &bull; </small>$"."COMPANY_STATE"."$</span></p>\r\n','auto',NULL)");
	
	$sql1 = "SELECT module FROM vtiger_pdfmaker GROUP BY module";
	$result1 = $adb->query($sql1);
	while($row = $adb->fetchByAssoc($result1))
	{
		$relModuleInstance = Vtiger_Module::getInstance($row["module"]);
		Vtiger_Link::addLink($relModuleInstance->id, 'LISTVIEWBASIC', 'PDF Export', "getPDFListViewPopup2(this,'$"."MODULE$');", '', 1);
		Vtiger_Link::addLink($relModuleInstance->id, 'DETAILVIEWWIDGET', 'PDFMaker', "module=PDFMaker&action=PDFMakerAjax&file=getPDFActions&record=$"."RECORD$", '', 1);
	}
	
	SDK::setMenuButton('contestual','LBL_ADD_TEMPLATE','window.location=\'index.php?module=PDFMaker&action=EditPDFTemplate\';','btnL3Add.png','PDFMaker');
	SDK::setMenuButton('contestual','LBL_IMPORT','window.location=\'index.php?module=PDFMaker&action=ImportPDFTemplate\';','tbarImport.png','PDFMaker','index');
	SDK::setMenuButton('contestual','LBL_EXPORT','return ExportTemplates();','tbarExport.png','PDFMaker','index');
	SDK::setMenuButton('contestual','LBL_IMPORT','window.location=\'index.php?module=PDFMaker&action=ImportPDFTemplate\';','tbarImport.png','PDFMaker','ListPDFTemplates');
	SDK::setMenuButton('contestual','LBL_EXPORT','return ExportTemplates();','tbarExport.png','PDFMaker','ListPDFTemplates');
	
	Vtiger_Link::addLink($instancePDFMaker->id, 'HEADERSCRIPT', 'PDFMakerJS', 'modules/PDFMaker/PDFMakerActions.js', '', 1);
}
?>
<?php
require_once('modules/SDK/InstallTables.php');
SDK::setLanguageEntries('Morphsuit', 'LBL_VTE_FREE_OK', array('it_it'=>'Attivazione VTE avvenuta con successo.','en_us'=>'VTE activation done.'));
SDK::setLanguageEntries('Morphsuit', 'LBL_ERROR_VTE_FREE', array('it_it'=>'Errore VTE Free','en_us'=>'Error VTE Free'));
SDK::setLanguageEntries('Morphsuit', 'LBL_ERROR_VTE_FREE_CONNECTION', array('it_it'=>'Impossibile contattare il server. Se desideri inviare una segnalazione scrivi a info@crmvillage.biz.','en_us'=>'Error server. Send a mail to info@crmvillage.biz.'));
SDK::setLanguageEntries('Morphsuit', 'LBL_ERROR_VTE_FREE_CHECK', array('it_it'=>'Errore chiave. Verificare che il numero utenti non sia oltre il numero consentito.','en_us'=>'Error key. Verify number active users.'));
SDK::setLanguageEntries('Morphsuit', 'LBL_MORPHSUIT_TIME_EXPIRED', array('it_it'=>'E` necessario attivare VTE.','en_us'=>'You have to activate VTE.'));
SDK::setLanguageEntries('Morphsuit', 'LBL_FREE', array('it_it'=>'Gratuita','en_us'=>'Free'));
SDK::setLanguageEntries('Morphsuit', 'LBL_MORPHSUIT_USER_NUMBER_EXCEEDED_FREE', array('it_it'=>'Limite numero utenti attivi superato per la versione gratuita. Clicca OK per passare alla versione Standard di VTE.','en_us'=>'User number limit exceeded for the the Free version. Click OK if you want the Standard version of VTE.'));
SDK::setLanguageEntries('Morphsuit', 'LBL_MORPHSUIT_USER_NUMBER_10', array('it_it'=>'10','en_us'=>'10'));
SDK::setLanguageEntries('Morphsuit', 'LBL_MORPHSUIT_USER_NUMBER_20', array('it_it'=>'20','en_us'=>'20'));
SDK::setLanguageEntries('Morphsuit', 'LBL_MORPHSUIT_USER_NUMBER_50', array('it_it'=>'50','en_us'=>'50'));
SDK::setLanguageEntries('Morphsuit', 'LBL_MORPHSUIT_USER_NUMBER_100', array('it_it'=>'100','en_us'=>'100'));
SDK::setLanguageEntries('Morphsuit', 'LBL_MORPHSUIT_USER_NUMBER_UNLIMITED', array('it_it'=>'Illimitati','en_us'=>'Unlimited'));
SDK::setLanguageEntries('Morphsuit', 'LBL_AVAILABLE_USERS', array('it_it'=>'Utenti disponibili: ','en_us'=>'Available users: '));
SDK::setLanguageEntries('Morphsuit', 'LBL_AVAILABLE_VERSION_TITLE', array('it_it'=>'Aggiornamento VTE','en_us'=>'VTE Update'));
SDK::setLanguageEntries('Morphsuit', 'LBL_AVAILABLE_VERSION_TEXT', array('it_it'=>'E` disponibile una nuova versione di VTE.','en_us'=>'There is a new version of VTE.'));
SDK::setLanguageEntries('Morphsuit', 'LBL_AVAILABLE_VERSION_UPDATE', array('it_it'=>'Aggiorna adesso','en_us'=>'Update now'));
SDK::setLanguageEntries('Morphsuit', 'LBL_ERROR_VTE_REGISTRATION', array('it_it'=>'E` necessario essere iscritti al sito www.crmvillage.biz','en_us'=>'You have to sign up to sito www.crmvillage.biz'));
SDK::setLanguageEntries('Home', 'Help VTE', array('it_it'=>'Guida VTE CRM','en_us'=>'Help VTE CRM'));
SDK::setLanguageEntries('Home', 'News CRMVILLAGE.BIZ', array('it_it'=>'News CRMVILLAGE.BIZ','en_us'=>'News CRMVILLAGE.BIZ'));
SDK::setLanguageEntries('Users', 'HELPVTE', array('it_it'=>'Help VTE','en_us'=>'Help VTE'));
SDK::setLanguageEntries('Users', 'CRMVNEWS', array('it_it'=>'News CRMVILLAGE.BIZ','en_us'=>'News CRMVILLAGE.BIZ'));
SDK::setLanguageEntries('Emails', 'LBL_CC', array('it_it'=>'CC:','en_us'=>'Cc:'));
SDK::setLanguageEntries('Emails', 'LBL_BCC', array('it_it'=>'CCN:','en_us'=>'Bcc:'));
SDK::setLanguageEntries('Emails', 'Attachment', array('it_it'=>'Allegati','en_us'=>'Attachments'));
SDK::setLanguageEntries('Calendar', 'LBL_CAL_TO_FILTER', array('it_it'=>'Elenco','en_us'=>'List'));
SDK::setLanguageEntries('Calendar', 'LBL_CAL_INTERRUPTED', array('it_it'=>'Interrotto','en_us'=>'Interrupted'));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_INVITED_TITLE', array('it_it'=>'Ti piace VTE?','en_us'=>'Do you like VTE?'));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_INVITED_LINK', array('it_it'=>'Fallo conoscere ai tuoi amici!','en_us'=>'Tell your friends!'));

global $adb;
$sqlarray = $adb->datadict->AddColumnSQL('vtiger_version','enterprise_project C(100)');
$adb->datadict->ExecuteSQLArray($sqlarray);
$adb->pquery('update vtiger_version set enterprise_project = ? where id = 1',array('Crmvillage'));
$sqlarray = $adb->datadict->AddColumnSQL('vtiger_version','hash_version XL');
$adb->datadict->ExecuteSQLArray($sqlarray);
$hash_version = file_get_contents('hash_version.txt');
$adb->updateClob('vtiger_version','hash_version','id=1',$hash_version);
@unlink('hash_version.txt');

if(!Vtiger_Utils::CheckTable('vtiger_home_iframe')) {
	Vtiger_Utils::CreateTable(
		'vtiger_home_iframe',
		"hometype C(30) PRIMARY,
		url C(255)", 
	true);
}
$adb->pquery('insert into vtiger_home_iframe (hometype,url) values (?,?)',array('HELPVTE','http://help.vtecrm.com'));
$adb->pquery('insert into vtiger_home_iframe (hometype,url) values (?,?)',array('CRMVNEWS','http://help.vtecrm.com/news'));

@unlink('copyright.html');

require_once('modules/Users/Users.php');
$usersInstance = new Users();
$adb->pquery('UPDATE vtiger_homestuff SET stuffsequence = 17 WHERE stufftitle = ?',array('Tag Cloud'));
$result = $adb->query('SELECT id FROM vtiger_users');
while($row = $adb->fetchByAssoc($result)) {
	
	$uid = $row['id'];
	$s15=$adb->getUniqueID("vtiger_homestuff");
	$visibility=$usersInstance->getDefaultHomeModuleVisibility('HELPVTE','');
	$sql="insert into vtiger_homestuff values(?,?,?,?,?,?)";
	$res=$adb->pquery($sql, array($s15,15,'Iframe',$uid,$visibility,'Help VTE'));
	$s16=$adb->getUniqueID("vtiger_homestuff");
	$visibility=$usersInstance->getDefaultHomeModuleVisibility('CRMVNEWS','');
	$sql="insert into vtiger_homestuff values(?,?,?,?,?,?)";
	$res=$adb->pquery($sql, array($s16,16,'Iframe',$uid,$visibility,'News CRMVILLAGE.BIZ'));
	
	$sql="insert into vtiger_homedefault values(".$s15.",'HELPVTE',0,'NULL')";
	$adb->query($sql);
	$sql="insert into vtiger_homedefault values(".$s16.",'CRMVNEWS',0,'NULL')";
	$adb->query($sql);
}

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
												<font face="Segoe UI"><font size="4"><font color="#ff9933">25/12/2011</font></font></font></td>
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
</table>
<p style="text-align: center;">
	Per cancellarti clicca $Newsletter||tracklink#unsubscription$</p>
<p style="text-align: center;">
	&nbsp;</p>
<p style="text-align: center;">
	Powered by</p>
<p style="text-align: center;">
	<img alt="" src="http://crmvillage.no-ip.biz/fuorisede/storage/images_uploaded/vtenllogo1.jpg" style="width: 300px; height: 48px;" /></p>
	';
$id = $adb->getUniqueID('vtiger_emailtemplates');
$adb->query("insert into vtiger_emailtemplates(foldername,templatename,subject,description,body,deleted,templateid,templatetype) values ('Public','Dillo ad un amico','Dillo ad un amico','Dillo ad un amico','".$adb->getEmptyClob(false)."',0,$id,'Newsletter')");
$adb->updateClob('vtiger_emailtemplates','body',"templateid=$id",$body);
?>
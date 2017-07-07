<?php
global $adb, $table_prefix;
$body = '<table bgcolor="#f7f7f8" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tbody>
		<tr>
			<td>
				&nbsp;</td>
			<td width="600">
				<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="600">
					<tbody>
						<tr>
							<td>
								<table border="0" cellpadding="0" cellspacing="0" height="19" width="624">
									<tbody>
										<tr>
											<td bgcolor="#f7f7f8" height="10" width="20">
												&nbsp;</td>
											<td bgcolor="#f7f7f8" style="text-align: center;" width="560">
												&nbsp;</td>
											<td bgcolor="#f7f7f8" width="34">
												&nbsp;</td>
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
											<td bgcolor="#ffffff" colspan="3">
												<img src="http://www.vtecrm.com/newsletter/dillo_amico_2013/images/intestazione.png" width="621" /></td>
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
											<td>
												<p>
													&nbsp;</p>
												<p>
													<strong style="color: rgb(136, 136, 136); font-family: \'Segoe UI\'; font-size: 18px;">Give a try to VTECRM!</strong></p>
												<p style="text-align: justify;">
													<span style="font-size:14px;"><font color="#888888" face="Segoe UI">Hi, </font></span></p>
												<p style="text-align: justify;">
													<span style="font-size:14px;"><font color="#888888" face="Segoe UI">i think that could be interesting for you to try the VTECRM product.</font></span></p>
												<p style="text-align: justify;">
													<span style="font-size:14px;"><font color="#888888" face="Segoe UI">Is an open source crm that can manage all the business processes, from lead management to salesforce automation and customer service support.<br />
													It comes with automatic setup for Windows, a VMWare appliance and of course in source code version that contain a wizard to set it up on your alredy configured webserver.</font></span></p>
												<p style="text-align: justify;">
													<span style="font-size:14px;"><font color="#888888" face="Segoe UI">If you are scared about installing or managing servers you can try it for free on cloud, see www.vtecrm.com for more informations about this opportunity.<br />
													This crm has the unique feature to manage also email and calendaring in a unified way, so you can receive and read you messages inside the crm and then associate them directly to your contacts, leads or accounts.<br />
													Don&#39;t be scared about complexity because VTECRM is born to simplify your routine operations, most of the user doesn&#39;t need to be trained to move first steps along this crm.<br />
													The software also have an iOS and Android app, a Thunderbird plugin and an Outlook connector that all comes for free.<br />
													So, why don&#39;t give it a try?</font></span></p>
												<p style="text-align: center;">
													<span style="font-size:14px;"><font color="#888888" face="Segoe UI"><span style="text-align: center;"><a href="http://www.vtecrm.com/en/community/vte-crm-free.html"><img alt="" border="0" src="http://www.vtecrm.com/newsletter/dillo_amico_2013/images/scarica_subito.png" /></a></span></font></span></p>
												<p>
													<span style="font-size:14px;"><font color="#888888" face="Segoe UI"><br />
													</font></span></p>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<hr />
							</td>
						</tr>
						<tr>
							<td>
								<img height="1" src="http://www.vtecrm.com/newsletter/newsletter_2ore/images/placeholder.gif" width="20" /></td>
						</tr>
						<tr>
							<td align="center">
								&nbsp;VTECRM LIMITED - 38 Craven Street London WC2N 5NG - Registration No. 08337393</td>
						</tr>
						<tr>
							<td>
								<table align="center" border="0" cellpadding="0" cellspacing="0" height="5" width="618">
									<tbody>
										<tr>
											<td align="center" valign="middle" width="20">
												<img height="1" src="http://www.vtecrm.com/newsletter/newsletter_2ore/images/placeholder.gif" width="20" /><img height="1" src="http://www.vtecrm.com/newsletter/newsletter_2ore/images/placeholder.gif" width="20" /></td>
										</tr>
									</tbody>
								</table>
							</td>
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
	<font color="#888888" face="Segoe UI" size="3">Powered by</font></p>
<p style="text-align: center;">
	<img alt="" src="http://www.crmvillage.biz/newsletter/newsletter_logo/vtenl_logo.jpg" style="width: 300px; height: 48px;" /></p>
';
$adb->updateClob($table_prefix.'_emailtemplates','body',"templatename='Dillo ad un amico'",$body);
$adb->pquery("update {$table_prefix}_emailtemplates set templatename=?, subject=?, description=? where templatename=?",array('Tell a friend about VTE','Give a try to the new VTECRM','Give a try to the new VTECRM','Dillo ad un amico'));
?>
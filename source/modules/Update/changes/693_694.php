<?php
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter'));
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan'));

if (isModuleInstalled('WSAPP')) {
	global $table_prefix;
	require_once("modules/Update/Update.php");
	Update::change_field($table_prefix.'_wsapp_queuerecords','details','C','2000');
}

echo "<br /><b>MANUAL CHANGES</b><br />
<b>1.</b> if you have <b>zpush</b> plugin open file zpush/backend/vte.php and delete around lines 26,27:
<blockquote>
<p>
\$_SESSION['app_unique_key'] = 'zpush';	//crmv@zmerge
<br />global \$table_prefix;
</p>
</blockquote>

<b>2.</b> if you have <b>zmerge</b> plugin<br />
- open file VTEConnector/vtwsclib/Vtiger/Net/HTTP_Client.php and replace line 15 with
<blockquote>
<p>
\$useragent = \"zMerge/2.6 (VTE Connector)\";
</p>
</blockquote>

- open file ZimbraConnector/vtwsclib/Vtiger/Net/HTTP_Client.php and replace line 15 with
<blockquote>
<p>
\$useragent = \"zMerge/2.6 (Zimbra Connector)\";
</p>
</blockquote>

- open file GoogleConnector/vtwsclib/Vtiger/Net/HTTP_Client.php and replace line 15 with
<blockquote>
<p>
\$useragent = \"zMerge/2.6 (Google Connector)\";
</p>
</blockquote>

- open file ExchangeConnector/vtwsclib/Vtiger/Net/HTTP_Client.php and replace line 15 with
<blockquote>
<p>
\$useragent = \"zMerge/2.6 (Exchange Connector)\";
</p>
</blockquote>";
?>
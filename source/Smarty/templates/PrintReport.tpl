{*
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
*}
{* crmv@96742 *}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$APP.LBL_CHARSET}">
<title>{$MOD.LBL_PRINT_REPORT} - {$APP.LBL_BROWSER_TITLE}</title> {* crmv@28324 *}
<link rel="stylesheet" media="print" href="themes/{$THEME}/style_print.css" type="text/css">
<link rel="stylesheet" href="themes/{$THEME}/reportprint.css" type="text/css">
</head>
<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" style="text-align:center;" onLoad="JavaScript:window.print()">
	<table width="80%" border="0" cellpadding="5" cellspacing="0" align="center">
	<tr>
		<td align="left" valign="top">
		<h2>{$MOD.$REPORT_NAME}</h2>
		<font color="#666666"><div id="report_info"></div></font>
		</td>
		<td align="right" valign="top"><h3 style="color:#CCCCCC">{$COUNT} {$APP.LBL_RECORDS}</h3></td>
	</tr>
	{* crmv@29686 *}
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td colspan="2">
		{$COUNT_TOTAL_HTML}
		</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	{* crmv@29686e *}
	<tr>
		<td colspan="2">
		{$PRINT_CONTENTS}
		</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td colspan="2">
		{$TOTAL_HTML}
		</td>
	<tr>
	</table>
</body>
<script type="text/javascript">
	document.getElementById('report_info').innerHTML = window.opener.document.getElementById('report_info').innerHTML;
</script>
</html>

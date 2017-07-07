{* crmv@94525 *}
{assign var="THEME" value="softed"}
{assign var="RELPATH" value=$PATH}
{assign var="BROWSER_TITLE" value='LBL_BROWSER_TITLE'|@getTranslatedString:'APP_STRINGS'}
{include file="HTMLHeader.tpl" head_include="icons,jquery,jquery_plugins,prototype"}

<body class="morphsuitactivationbody">
<div align="center">
	<div class="small" style="width: 500px; padding-top: 5px;">
		<div class="small" style="width: 500px; padding-top: 5px;">
			<table class="hdrBg" width="100%" cellspacing="0" cellpadding="0">
				<tr height="50">
					<td style="padding-left:5px;padding-right:5px;" nowrap>
					{php}
					// define this function (SDK::setUtil) to override the logo with anything
					if (function_exists('get_logo_override')) echo get_logo_override('project'); else { $project = getEnterpriseProject(); if (!empty($project)) echo '<img src="'.$this->_tpl_vars['PATH'].get_logo('project').'" border="0">'; }
					{/php}
					</td>
					<td width="100%" align="right" style="padding-right:10px">
						<img src="{$PATH}themes/logos/VTE_header.png" border="0">
					</td>
				</tr>
			</table>
			<table id="Standard" class="small morphsuittable" width="500" cellpadding="3">
				<tr>
					<td>{$BODY}</td>
				</tr>
			</table>
		</div>
	</div>
</div>
</body>
</html>
{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}

{* crmv@56023 *}

<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<script src="include/scriptaculous/prototype.js" type="text/javascript"></script>
<script src="include/scriptaculous/scriptaculous.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript" src="{"include/js/general.js"|resourcever}"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/dtlviewajax.js"|resourcever}"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/menu.js"|resourcever}"></script>

<table align="center" border="0" cellpadding="0" cellspacing="0"
	width="100%">
	<tbody>
		<tr>
			<td valign="top"></td>
			<td class="showPanelBg" style="padding: 5px;" valign="top"
				width="100%">
				<!-- crmv@30683 -->
				<form action="index.php" method="post" name="LoginProtectionForm" id="form" onsubmit="VtigerJS_DialogBox.block();">
					<input type='hidden' name='parenttab' value='{$CATEGORY}'>

					<div align=center>
						{include file='SetMenu.tpl'}
						{include file='Buttons_List.tpl'}
						<!-- DISPLAY -->
						<table border=0 cellspacing=0 cellpadding=5 width=100%
							class="settingsSelUITopLine">
							<tr>
								<td width=50 rowspan=2 valign=top><img
									src="{$IMAGE_PATH}ico-profile.gif"
									alt="{$APP.LoginProtectionPanel}" width="48" height="48"
									border=0 title="{$APP.LoginProtectionPanel}"></td>
								<td class=heading2 valign=bottom><b>{$MOD.LBL_SETTINGS} > {$APP.LoginProtectionPanel}</b></td>
							</tr>
							<tr>
								<td valign=top class="small">{$APP.LoginProtectionPanel_description}</td>
							</tr>
						</table>

						<br>
						<table border=0 cellspacing=0 cellpadding=10 width=100%>
							<tr>
								<td>
								{if $ENABLED eq true}
									<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
										<tr>
											<td class="big" height="30px;"><strong>{$MOD.LBL_LOGIN_HISTORY_DETAILS}</strong></td>
											<td class="small" align="left">&nbsp;</td>
										</tr>
									</table>
									<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
										<tr>
											<td class="small" valign=top>
												<table width="100%" border="0" cellspacing="0" cellpadding="5">
													<tr valign="top">
														<td nowrap width="18%" class="small cellLabel"><strong>{$MOD.LBL_USER_AUDIT}</strong></td>
														<td class="small cellText">
														<select name="user_list" id="user_list" onchange="fetchloginprotection_js(this,'');">
															<option value="none" selected="true">{$APP.LBL_NONE}</option>
															<option value="all">{$APP.LBL_ALL}</option> 
															{$USERLIST}
														</select>
														</td>
													</tr>
													</td>
													</tr>
												</table>
												<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
													<tr>
														<td class="big"><strong>{$CMOD.LBL_LOGIN_HISTORY}</strong></td>
													</tr>
												</table>
												<table border="0" cellpadding="5" cellspacing="0" width="100%">
													<tr>
														<td align="left">
															<div id="login_protection_content" style="display: none;"></div>
														<td>
													</tr>
												</table>
												<br>
									</table>
									<table border=0 cellspacing=0 cellpadding=5 width=100%>
										<tr>
											<td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td>
										</tr>
									</table>
								{/if}
								</td>
							</tr>
						</table>
			
			</td>
		</tr>

</table>
</td>
</tr>
</table>

</div>

</td>
<td valign="top"></td>
</tr>
</tbody>
</form>
</table>

{literal}
<script>
function fetchloginprotection_js(obj,url_string)
{
	var id = obj.options[obj.selectedIndex].value;
	fetchLoginProtection(id,url_string);
}
function fetchLoginProtection(id,url_string)
{
	if ( id == 'none')
	{
		Effect.Fade('login_protection_content');
	}
	else
	{
        	$("status").style.display="inline";
	        new Ajax.Request(
        	        'index.php',
                	{queue: {position: 'end', scope: 'command'},
                        	method: 'post',
                       		postBody: 'module=Settings&action=SettingsAjax&file=LoginProtectionPanel&ajax=true&userid='+id+url_string,
	                        onComplete: function(response) {
        	                        $("status").style.display="none";
                	                $("login_protection_content").innerHTML= response.responseText;
									Effect.Appear('login_protection_content');
                       		}
               		}
        		);
	}
}
function whiteListRecord(recordid){
	console.log(recordid);
	$("status").style.display="inline";
	new Ajax.Request(
			'index.php',
			{queue: {position: 'end', scope: 'command'},
				method: 'post',
				postBody: 'module=Settings&action=SettingsAjax&file=LoginProtectionActions&ajax=true&mode=whitelist&id='+recordid,
				onComplete: function(response) {
						var obj = getObj('user_list');
						fetchloginprotection_js(obj,'');
				}
			}
	);
}
</script>
{/literal}
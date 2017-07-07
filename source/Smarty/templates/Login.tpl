{* crmv@94525 crmv@91082 *}

{* header has already been printed *}

{* style for login page *}
<link rel="stylesheet" href="themes/{$THEME}/style_login.css">

{if !$LOGIN_AJAX}
	<script language="JavaScript" type="text/javascript" src="modules/Morphsuit/MorphsuitCommon.js"></script>
	<script language="JavaScript" type="text/javascript" src="modules/Users/Users.js"></script>
{/if}

<div id="popupContainer" style="display:none;"></div>

{if $LOGOUT_REASON}
<div id="logout_reason_msg">{$LOGOUT_REASON}</div>
{/if}

<!-- Sign in form -->
{if !$LOGIN_AJAX}
<form action="index.php" method="post" name="DetailView" id="form" {php}eval($hash_version[13]);{/php}>
	<input type="hidden" name="module" value="Users">
	<input type="hidden" name="action" value="Authenticate">
	<input type="hidden" name="return_module" value="Users">
	<input type="hidden" name="return_action" value="Login">
	<input type="hidden" name="free_params" value="">	<!-- crmv@35153 -->
{/if}

	<table border="0" cellpadding="0" cellspacing="0" width="360" align="center">
		<tr height="100">
			<td align="center">
				{$LOGOIMG}
			</td>
		</tr>
		<tr>
			<td width="100%">
			<!-- form elements -->
				<table border="0" cellpadding="5" cellspacing="0" width="100%" align="center" class="small logintbl">
				<tr height="40"><td>&nbsp;</td></tr>
				<tr>
					<td class="small" align="center">
						<img src="{php}echo get_logo('login');{/php}" />
					</td>
				</tr>
				<tr height="40"><td>&nbsp;</td></tr>
				<tr>
					<td class="small" align="center"><input id="login_user_name" class="logininput" type="text" size='37' name="user_name" value="{$USERNAME}" placeholder="{$MOD.LBL_USER_NAME}" tabindex="1" autocorrect="off" autocapitalize="off"></td>
				</tr>
				<tr>
					<td class="small" align="center">
						<input id="login_password" class="logininput" type="password" size='37' name="user_password" value="{$PASSWORD}" placeholder="Password" tabindex="2">
					</td>
				</tr>
				<tr height="30px"><td align="center" class="small" id="td_login_error">{$ERROR_STR}</td></tr>
				<tr height="10px"><td></td></tr>
				<tr>
					<td align="center">
						{if $LOGIN_AJAX}
							<input title="{$MOD.LBL_LOGIN_BUTTON_TITLE}" alt="{$MOD.LBL_LOGIN_BUTTON_LABEL}" accesskey="{$MOD.LBL_LOGIN_BUTTON_TITLE}" type="button" class="loginsubmit" name="Login" value="{$MOD.LBL_LOGIN_BUTTON_LABEL}" tabindex="4" onclick="SessionValidator.doLogin();">
						{else}
							<input title="{$MOD.LBL_LOGIN_BUTTON_TITLE}" alt="{$MOD.LBL_LOGIN_BUTTON_LABEL}" accesskey="{$MOD.LBL_LOGIN_BUTTON_TITLE}" type="submit" class="loginsubmit" name="Login" value="{$MOD.LBL_LOGIN_BUTTON_LABEL}" tabindex="4">
						{/if}
					</td>
				</tr>
				<tr>
					<td class="small" align="center">
						<table border="0" cellpadding="0" cellspacing="0" align="center" class="small">
						<tr>
							<td style="padding-right:10px">
								<input type="checkbox" class="small" name="savelogin" id="savelogin" tabindex="3" {if $SAVELOGIN}checked="checked"{/if} style="border: 0;" />
				         		<label for="savelogin" class="logintxt">{$MOD.LBL_KEEP_ME_LOGGED_IN}</label>
							</td>
							<td style="padding-left:10px" align="right">
								<a href="modules/Users/Recover.php" class="logintxt" tabindex="5" {if $LOGIN_AJAX}target="_blank"{/if}>{$MOD.LBL_FORGOT_YOUR_PASSWORD}</a></td>	<!-- crmv@27589 -->
							</td>
						</tr>
						</table>
					</td>
				</tr>
				<tr height="40"><td>&nbsp;</td></tr>
				</table>
			</td>
		</tr>
	</table>

{if !$LOGIN_AJAX}
</form>
{else}
<script type="text/javascript">
{literal}
jQuery('input').keypress( function (e) {
	if (e.keyCode == 13) {
		SessionValidator.doLogin();
	}
});
{/literal}
</script>
{/if}


<script type="text/javascript">
{php}eval($hash_version[14]);{/php}
{literal}
jQuery(document).ready(function() {
	jQuery('#form input[name=user_name]').focus();
});
{/literal}
</script>

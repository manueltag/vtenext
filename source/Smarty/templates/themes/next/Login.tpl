{* crmv@94525 crmv@91082 *}

{* header has already been printed *}

{* style for login page *}
<link rel="stylesheet" href="themes/{$THEME}/style_login.css">

{if !$LOGIN_AJAX}
	<script language="JavaScript" type="text/javascript" src="modules/Morphsuit/MorphsuitCommon.js"></script>
	<script language="JavaScript" type="text/javascript" src="modules/Users/Users.js"></script>
{/if}

<div id="popupContainer" style="display:none;"></div>

<div id="loginContainer">
	<div class="loginContainerInner">
		<div class="row">
			<div class="loginBox col-xs-12">
			
				<div class="row">
					<div class="col-xs-12 text-left">
						{if $LOGOUT_REASON}
							<div id="logout_reason_msg">
								<div class="alert alert-danger">
									{$LOGOUT_REASON}
								</div>
							</div>
						{/if}
					</div>
				</div>

<!-- Sign in form -->
{if !$LOGIN_AJAX}
<form action="index.php" method="post" name="DetailView" id="form" {php}eval($hash_version[13]);{/php}>
	<input type="hidden" name="module" value="Users">
	<input type="hidden" name="action" value="Authenticate">
	<input type="hidden" name="return_module" value="Users">
	<input type="hidden" name="return_action" value="Login">
	<input type="hidden" name="free_params" value="">	<!-- crmv@35153 -->
{/if}

	<div class="row logoContainer">
		<div class="col-xs-12 text-center logoContainerInner">
			<img src="{php}echo get_logo('login');{/php}" />
		</div>
	</div>
	
	<div class="row">
		<div class="col-xs-10 col-xs-offset-1">
			<div class="row">
				<div class="col-xs-12">
					<div class="form-group inputContainer">
						<input id="login_user_name" class="form-control" type="text" name="user_name" value="{$USERNAME}" autocorrect="off" autocomplete="off" autocapitalize="off" required="required" />
						<label for="login_user_name">{$MOD.LBL_USER_NAME}</label>
						<i class="vteicon nohover">person</i>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="form-group inputContainer">
						<input id="login_password" class="form-control" type="password" name="user_password" value="{$PASSWORD}" required="required" />
						<label for="login_password">Password</label>
						<i class="vteicon nohover">lock_outline</i>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="checkbox">
						<label for="savelogin">
							<input type="checkbox" name="savelogin" id="savelogin" {if $SAVELOGIN}checked="checked"{/if}" />
			         		&nbsp;{$MOD.LBL_KEEP_ME_LOGGED_IN}
		         		</label>
					</div>
					<div class="spacer-10"></div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="form-group inputContainer">
						{if $LOGIN_AJAX}
							<input type="button" class="btn btn-lg btn-raised btn-primary" name="Login" value="{$MOD.LBL_LOGIN_BUTTON_LABEL}" onclick="SessionValidator.doLogin();" />
						{else}
							<input type="submit" class="btn btn-lg btn-raised btn-primary" name="Login" value="{$MOD.LBL_LOGIN_BUTTON_LABEL}" />
						{/if}
					</div>
				</div>
			</div>
			{if !empty($ERROR_STR) & $ERROR_STR neq '&nbsp;'}
				<div class="row">
					<div class="col-xs-12">
						<div class="form-group inputContainer text-center">
							{$ERROR_STR}
						</div>
					</div>
				</div>
			{/if}
			<div class="row">
				<div class="col-xs-12">
					<div class="form-group text-center recoverContainer">
						<a href="modules/Users/Recover.php" {if $LOGIN_AJAX}target="_blank"{/if}>{$MOD.LBL_FORGOT_YOUR_PASSWORD}</a>
					</div>
				</div>
			</div>
		</div>
	</div>

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
jQuery('#vte_footer').hide();
{/literal}
</script>
{/if}

			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
{php}eval($hash_version[14]);{/php}
{literal}
jQuery(document).ready(function() {
	jQuery('#form input[name=user_name]').focus();
});
{/literal}
</script>
</body>
</html>
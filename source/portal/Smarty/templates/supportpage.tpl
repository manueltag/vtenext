{*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with
the License * The Original Code is: VTECRM * The Initial Developer of
the Original Code is VTECRM LTD. * Portions created by VTECRM LTD are
Copyright (C) VTECRM LTD. * All Rights Reserved.
***************************************************************************************}

<!-- Bootstrap Core CSS -->
<link href="css/bootstrap.css" rel="stylesheet">
<link href="css/bootstrap.min.css" rel="stylesheet">

<form name="forgot_password" action="index.php?login_language={$LOGINLANGUAGE}" method="post">
	<input type="hidden" class="form-control" name="param" value="forgot_password"> 
	<input type="hidden" class="form-control" name="email_id">

	<div class="modal-header">
		<h4 class="modal-title" id="mySmallModalLabel">{'LBL_FORGOT_LOGIN'|getTranslatedString}</h4>
	</div>

	{if $MAILSENDMESSAGE.0 != 'true'}
	
		{if !empty($MAILSENDMESSAGE.1)}
			<div class="alert alert-danger" role="alert">
				{$MAILSENDMESSAGE.1|getTranslatedString}
			</div>
		{/if}

		<div class="modal-body">
		
			{'LBL_YOUR_EMAIL'|getTranslatedString} 
			
			{if empty($MAILSENDMESSAGE.1)}		
				<input class="form-control" style="margin-top: 10px" type="text" name="email_id" VALUE="" />
			{else}
				<div class="form-group has-error has-feedback">
  					<input type="text" class="form-control" id="inputError2" name="email_id" VALUE="">
					<span class="glyphicon glyphicon-remove form-control-feedback"></span>
				</div>
			{/if}
			<center>
				<input style="margin-top: 10px" type="submit" class="btn btn-default" value="{'LBL_SEND_PASSWORD'|getTranslatedString}">
			</center>
	{else}
		<div class="alert alert-success" role="alert">
			{$MAILSENDMESSAGE.1|getTranslatedString}
		</div>	
	{/if}
		</div>
</form>
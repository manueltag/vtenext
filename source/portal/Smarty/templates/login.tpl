{*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with
the License * The Original Code is: VTECRM * The Initial Developer of
the Original Code is VTECRM LTD. * Portions created by VTECRM LTD are
Copyright (C) VTECRM LTD. * All Rights Reserved.
***************************************************************************************}

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- crmv@18049 -->
<title>{$BROWSERNAME}{'customerportal'|getTranslatedString}</title>
<link REL="SHORTCUT ICON" HREF="{$LOGO}">


<!-- Bootstrap Core CSS -->
<link href="css/bootstrap.css" rel="stylesheet">
<link href="css/bootstrap.min.css" rel="stylesheet">

<!-- CSS -->
<link href="css/login.css" rel="stylesheet" type="text/css">

<!-- Material Design -->
<link href="css/material_design/material.min.css" rel="stylesheet">
<link href="css/material_design/roboto.min.css" rel="stylesheet">
<link href="css/material_design/material-fullpalette.min.css" rel="stylesheet">
<link href="css/material_design/ripples.min.css" rel="stylesheet">
<link href="css/material_design/ripples.min.css" rel="stylesheet"> 

<!-- Javascrip -->
<script src="js/jquery-1.11.0.js"></script>

</head>

<body>
<script src="js/bootstrap.min.js"></script>

	<div id="page-wrapper">
		<div class="container-fluid">
			
			<form name="login" action="CustomerAuthenticate.php" method="get">
				<div class="row">
					<div class="col-xs-12 col-centered" id="logo">
						<div class="row row-centered">
							<img class="img-responsive col-centered" id="imagelogin" src="images/VTE_login.png">
						</div>
					</div>
				</div>

				<div class="row">
					<div class="container">
						{if $LOGIN_ERROR != ""}
							<div class="alert alert-danger alert-dismissable" style="margin-top:10px; margin-bottom:10px">
 								 <button type="button" class="close" data-dismiss="alert">&times; </button>
 								{$LOGIN_ERROR|getTranslatedString}
							</div>
						{/if}
					</div>
				</div>
					
					<div class="row form-login">
						<div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3">
							<div id="form-input">
								<input type="text" class="form-control" size="37" name="username" id="username" value="" placeholder="{'LBL_EMAILID'|getTranslatedString}" tabindex="1" autocorrect="off" autocapitalize="off"> 
								<input type="password" class="form-control" size="37" name="pw" id="pw" value="" placeholder="{'LBL_PASSWORD'|getTranslatedString}" tabindex="2">
							</div>
							<div class="logintxt">
								<!-- crmv@remember_me -->
								<div class="row-action-primary checkbox col-md-6 col-md-offset-3 text-center">
                                	<label>
										<input type="checkbox" class="small" name="savelogin" id="savelogin" tabindex="3" style="border: 0;">
										<span class="checkbox-material"><span class="check"></span></span>
										<span style="font-size:13px;">Keep me logged in</span>
									</label>
                                 </div>
								<!-- crmv@remember_me e -->
								<div class="col-md-6 col-md-offset-3 text-center" style="margin-top: 20px; margin-bottom: 20px;">
									<a data-toggle="modal" data-target=".bs-example-modal-sm" href="#">{'LBL_FORGOT_LOGIN'|getTranslatedString}</a>
								</div>

								<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  									<div class="modal-dialog modal-sm modal-xs">
    									<div class="modal-content">
     										<iframe  class="embed-responsive-item" style="width: 100%; min-height: 250px" src="supportpage.php?param=forgot_password&login_language={$LOGINLANGUAGE}"></iframe>
   										 </div>
  									</div>
								</div>
							</div>

							<div class="logintxt">
								<div class="row row-centered">
									<div class="col-md-6 col-sm-6 col-xs-12" id="language_id" style="margin: 10px 0;">
										<center><select class="form-control" name="login_language" onChange="window.location.href='login.php?login_language='+this.value" style="max-width:60%"></center>
											{$LANGUAGE}
										</select>
									</div>
									<div class="col-md-6 col-sm-6 col-xs-12">
										<input title="Login [Alt+L]" class="btn btn-primary" alt="Login" accesskey="Login [Alt+L]" type="submit" class="loginsubmit" name="Login" value="Log In" tabindex="4">
									</div>
								</div>
							</div>
						</div>
					</div>	<!-- form-login -->	
				</div>	<!-- container -->	
			</form>
		</div>	<!-- container-fluid -->
	</div>
</body>
</html>
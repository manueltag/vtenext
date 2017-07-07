{*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with
the License * The Original Code is: VTECRM * The Initial Developer of
the Original Code is VTECRM LTD. * Portions created by VTECRM LTD are
Copyright (C) VTECRM LTD. * All Rights Reserved.
***************************************************************************************}
<!-- crmv@57342 -->

<!-- Javascrip -->
<script src="js/jquery-1.11.0.js"></script>
<script src="js/bootstrap.min.js"></script>
{if $CONTACTPROFILE eq 'yes'}
<h1 class="page-header">{'LBL_MODIFY_PROFILE'|getTranslatedString}</h1>

<div class="row options" style="background-color: #ECF0F1">
	<div class="col-xs-12 col-sm-12 col-md-4 btn btn-default" style="cursor: pointer;margin: 0px;padding:0px" onClick="window.location.href='index.php?module=Contacts&action=index&id={$CUSTOMERID}&profile=yes&update=yes'">
		<center>
			<h4>{'UPDATE_PROFILE'|getTranslatedString}</h4>
		</center>
	</div>

<!-- 	<div class="col-md-2"> -->
<!-- 		<center><div class="separation"></div></center> -->
<!-- 	</div> -->

	<div class="col-xs-12 col-sm-12 col-md-4 Unsubscribe btn btn-default" style="cursor: pointer;margin: 0px;padding:0px" onClick="if(confirm('{'MSG_CONF_UNSUBSCRIBE'|getTranslatedString}')){ldelim} window.location.href='index.php?module=Contacts&action=index&fun=unsubscribe&id={$CUSTOMERID}'; {rdelim}">
		<center>
			<h4 data-toggle="modal">{'LBL_UNSUBSCRIBE'|getTranslatedString}</h4>
		</center>
	</div>

<!-- 	<div class="col-md-2"> -->
<!-- 		<center><div class="separation"></div></center> -->
<!-- 	</div> -->

	<div class="col-xs-12 col-sm-12 col-md-4 btn btn-default" style="cursor: pointer;margin: 0px;padding:0px">
		<center>
			<h4 data-toggle="modal" data-target=".bs-example-modal-sm">{'LBL_CHANGE_PASSWORD'|getTranslatedString}</h4>
		</center>

		<div class="modal fade bs-example-modal-sm" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  			<div class="modal-dialog modal-sm modal-xs">
    			<div class="modal-content">
     				<iframe class="embed-responsive-item changepw" src="MySettings.php"></iframe>
   				</div>
  			</div>
		</div>
	</div>
							
</div>
{else}
<div class="row rowbotton">
	<div class="col-md-10  col-sm-5 col-xs-4">
		<button align="left" class="btn btn-default" type="button" value="{'LBL_BACK_BUTTON'|getTranslatedString}" onclick="location.href='index.php?module=Contacts&action=index'"/>{'LBL_BACK_BUTTON'|getTranslatedString}</button>
	</div>	
</div>
{/if}


<div class="row">
	{foreach from=$FIELDLIST item=FIELD}
		{foreach from=$FIELD item=VALUE}
			<div class="col-md-12">
				<h3 class="value">
					<small>{$VALUE.0|getTranslatedString}</small>
				</h3>
			</div>
			<div class="col-md-12 linerow">
				<h3>{$VALUE.1|getTranslatedString}</h3>
			</div>
		{/foreach}
	{/foreach}
</div>
<!-- crmv@57342e -->
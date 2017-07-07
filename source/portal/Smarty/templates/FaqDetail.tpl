{**************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
<div class="row rowbotton">
	<div class="col-md-10  col-sm-5 col-xs-4">
		<button align="left" class="btn btn-default" type="button" value="{'LBL_BACK_BUTTON'|getTranslatedString}" onclick="location.href='index.php?module=Faq&action=index'"/>{'LBL_BACK_BUTTON'|getTranslatedString}</button>
	</div>	
</div>

<div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="col-md-12">
		<h3><small>{'LBL_FAQ_TITLE'|getTranslatedString} {'LBL_FAQ_DETAIL'|getTranslatedString}</small></h3>
	</div>
	<div class="col-md-12 linerow ">
		<h3>{$QUESTION}</h3>
	</div>
	<div class="col-md-12">
		<h3><small>{'LBL_ANSWER'|getTranslatedString}</small></h3>
	</div>
	<div class="col-md-12 linerow ">
		<h3>{$ANSWER}</h3>
	</div>
</div>

<div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="panel panel-default">
		<div class="panel-heading">
    		<i class="fa fa-comments fa-fw"></i> 
			{'LBL_COMMENTS'|getTranslatedString}
			<span class="badge">{$BADGE}</span>
			<div class="btn-group pull-right" id="comments">
				<a id="comments-close" type="button" class="btn btn-default btn-xs">
            		<i class="fa fa-angle-down"></i>
            	</a>
			</div>
		</div>
		<div class="panel-body" id="panel-comments">
			{if !empty($COMMENTS)}
				{foreach from=$COMMENTS key=num item=comment}
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 linerow">
						<h4>{$comment.comment}</h4>
						<br><span class="hdr">{'LBL_ADDED_ON'|getTranslatedString}{$comment.date}</span>
					</div>
				{/foreach}
			{else}
				<b>{'LBL_NO_COMMENTS'|getTranslatedString}</b>
			{/if}
		</div>
	</div>
</div>

<div class=" row col-lg-12 col-md-12 col-sm-12 col-xs-12 panel panel-default" style="padding:0px;">
	<div class="panel-heading">
			{'LBL_DOCUMENTS'|getTranslatedString}
    </div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		{$DOCUMENTS}
	</div>
</div>

<div class=" row col-lg-12 col-md-12 col-sm-12 col-xs-12 panel panel-default" style="padding:0px;">
	<div class="panel-heading">
		{'LBL_ADD_COMMENT'|getTranslatedString}
	</div>
	<form name="comments" method="POST" action="index.php">
		<input type="hidden" name="module">
		<input type="hidden" name="action">
		<input type="hidden" name="fun">
		<input type=hidden name=faqid value="{$FAQID}">
		<textarea name="comments" cols="80" rows="5" class="form-control" rows="12">&nbsp;</textarea>
		<input title="{'LBL_SAVE_ALT'|getTranslatedString}" accesskey="S" class="small"  name="submit" value="{'LBL_SUBMIT'|getTranslatedString}" style="width: 70px;" type="submit" onclick="this.form.module.value='Faq';this.form.action.value='index';this.form.fun.value='faq_updatecomment'; if(trim(this.form.comments.value) != '') return true; else return false;"/>
	</form>
</div>

<div class=" row col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding:0px;">
	{$PAGEOPTION}
</div>

<div class=" row col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding:0px;">
	{$LIST}
</div>

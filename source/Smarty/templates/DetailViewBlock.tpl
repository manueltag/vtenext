{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}

<table border="0" cellspacing="0" cellpadding="{if $OLD_STYLE eq true}2{else}5{/if}" width="100%" class="small">	{* crmv@57221 *}
	{assign var="fieldcount" value=0}
	{assign var="tr_state" value=0}
	{assign var="fieldstart" value=1}
	{foreach item=detail from=$detail}
		{foreach key=label item=data from=$detail}
			{assign var=keyid value=$data.ui}
			{assign var=keyval value=$data.value}
			{assign var=keytblname value=$data.tablename}
			{assign var=keyfldname value=$data.fldname}
			{assign var=keyfldid value=$data.fldid}
			{assign var=keyoptions value=$data.options}
			{assign var=keysecid value=$data.secid}
			{assign var=keyseclink value=$data.link}
			{assign var=keycursymb value=$data.cursymb}
			{assign var=keysalut value=$data.salut}
			{assign var=keyaccess value=$data.notaccess}
			{assign var=keyadmin value=$data.isadmin}
			{assign var=keyreadonly value=$data.readonly}
			{assign var=display_type value=$data.displaytype}
			{assign var=keymandatory value=$data.mandatory}
			
			{if $keyreadonly eq 100}
				{* hidden *}
			{else}
			
				{assign var=fieldlength value=1}
				{if $keyid eq 19 || $keyid eq 69 || $keyid eq 210 || $keyid eq 220} {* crmv@104180 *}
					{assign var=fieldlength value=2}
				{/if}
				{*  crmv@43764 *}
				{if $keyid eq 208 && $smarty.session.uitype208.$keyfldid.old_uitype eq '19'}
					{assign var=fieldlength value=2}
				{/if}
				{*  crmv@43764e *}
				{* crmv@OPER6288 *}
				{if $SUMMARY && $SHOW_KANBAN_BUTTONS eq 'true'}
					{assign var=fieldlength value=2}
				{/if}
				{* crmv@OPER6288e *}
				
			   	{if ($fieldcount eq 0 or $fieldstart eq 1) and $tr_state neq 1}	
			  		{if $fieldstart eq 1}
						{assign var="fieldstart" value=0}
					{/if}
			   		<tr style="height:25px" valign="top">
			   		{assign var="tr_state" value=1}
				{/if}
				
				{if $fieldlength eq 2 and $fieldcount neq 0}
					</tr>
					<tr style="height:25px">
					{assign var="tr_state" value=1}
					{assign var="fieldcount" value=0}
				{/if}
				{assign var="fieldcount" value=$fieldcount+1}
				
				{if $fieldlength eq 2}
					<td colspan="2" style="padding-top:5px">
				{else}
					<td width="50%" style="padding-top:5px">
				{/if}
				
				{if ($keyreadonly eq 99 or $EDIT_PERMISSION neq 'yes' or $display_type eq '2' or empty($DETAILVIEW_AJAX_EDIT))}
					{* readonly *}
					{assign var=READONLY value=true}
					{assign var="AJAXEDITTABLEPERM" value=false}
					{assign var="DIVCLASS" value="dvtCellInfoOff"}
				{else}
					{* visible and editable *}
					{assign var=READONLY value=false}
					{assign var="AJAXEDITTABLEPERM" value=true}
					{assign var="DIVCLASS" value="dvtCellInfo"}
				{/if}
				{* crmv@57221 *}
				{assign var="DIVCLASSOTHER" value=""}
				{if $OLD_STYLE eq true}
					{assign var="DIVCLASS" value=$DIVCLASS|cat:" dvtCellInfoOldStyle"}
					{assign var="DIVCLASSOTHER" value="dvtCellInfoOldStyle "}
				{/if}
				{* crmv@57221e *}
				{include file="DetailViewUI.tpl"}
				
				</td>
				
				{if $fieldlength eq 2}
					{assign var="fieldcount" value=$fieldcount+1}
				{/if}
				
			    {if $fieldcount eq 2}
					</tr>
					{assign var="fieldcount" value=0}
					{assign var="tr_state" value=0}
				{/if}
				
				<!-- This is added to display the existing comments -->
				{if ($MODULE eq 'HelpDesk' || $MODULE eq 'Faq') && $keyfldname eq 'comments'}
					<tr><td colspan="2">{$COMMENT_BLOCK}</td></tr>
					<script type="text/javascript">
				   		// crmv@97430
				   		var objDiv = document.getElementById("comments_div");
				   		if (objDiv) {ldelim}
							objDiv.scrollTop = objDiv.scrollHeight;
						{rdelim}
						// crmv@97430e
					</script>
				{/if}
				
			{/if}
		{/foreach}
	{/foreach}
	{* crmv@47567 : moved input hdtxt_IsAdmin *}
	{* crmv@20209 *}
	{if $MODULE eq 'Users' && $header eq "LBL_CALENDAR_CONFIGURATION"|getTranslatedString:'Users'}
		{include_php file="modules/Calendar/calendar_share.php"}
	{/if}
	{* crmv@20209e *}
</table>
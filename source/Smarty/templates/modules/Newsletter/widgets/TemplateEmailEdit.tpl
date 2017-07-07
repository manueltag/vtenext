<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$APP.LBL_CHARSET}">
<title>{$PAGE_TITLE}</title>
<link rel="stylesheet" type="text/css" href="{$THEME_PATH}style.css">
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/menu.js"|resourcever}"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/general.js"|resourcever}"></script>
<script language="JavaScript" type="text/javascript" src="include/js/jquery.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/jquery_plugins/form.js"></script>
{include file='CachedValues.tpl'}	{* crmv@26316 crmv@55961 *}
</head>
<body marginwidth="0" marginheight="0" rightmargin="0" leftmargin="0" bottommargin="0" topmargin="0">
<script language="JavaScript" type="text/javascript">
    var allOptions = null;

    function setAllOptions(inputOptions) 
    {ldelim}
        allOptions = inputOptions;
    {rdelim}

    function modifyMergeFieldSelect(cause, effect) 
    {ldelim}
        var selected = cause.options[cause.selectedIndex].value;  id="mergeFieldValue"
        var s = allOptions[cause.selectedIndex];
        effect.length = s;
        for (var i = 0; i < s; i++) 
	{ldelim}
            effect.options[i] = s[i];
        {rdelim}
        document.getElementById('mergeFieldValue').value = '';
    {rdelim}

    function init() 
    {ldelim}
        var blankOption = new Option('--None--', '--None--');
        var options = null;
		var allOpts = new Object({$ALL_VARIABLES|@count}+1);
		{assign var="alloptioncount" value="0"}
		{foreach key=index item=module from=$ALL_VARIABLES}
	    	options = new Object({$module|@count}+1);
	    	{assign var="optioncount" value="0"}
            options[{$optioncount}] = blankOption;
            {foreach key=header item=detail from=$module}
             {assign var="optioncount" value=$optioncount+1}
				options[{$optioncount}] = new Option('{$detail.0|escape}', '{$detail.1|escape}');
			{/foreach}      
			 {assign var="alloptioncount" value=$alloptioncount+1}     
             allOpts[{$alloptioncount}] = options;
	    {/foreach}
        setAllOptions(allOpts);	    
    {rdelim}
    
	function InsertIntoTemplate(element)
	{ldelim}
	    selectField =  document.getElementById(element).value;
	    var oEditor = CKEDITOR.instances.body;
		if (selectField != '')
		{ldelim}
        	oEditor.insertHtml(selectField);
		{rdelim}
	{rdelim}
</script>
<table cellspacing=0 cellpadding=0 width=100%><tr><td>
	{literal}
	<form action="index.php" method="post" id="templatecreate" name="templatecreate">
	{/literal}
	<input type="hidden" name="mode" value="{$EMODE}">
	<input type="hidden" name="file" value="widgets/TemplateEmailSSave">
	<input type="hidden" name="action" value="NewsletterAjax">
	<input type="hidden" name="module" value="Newsletter">
	<input type="hidden" name="templateid" value="{$TEMPLATEID}">
	<input type="hidden" name="parenttab" value="{$PARENTTAB}">
	<input type="hidden" name="quick_create" value="true">
	
	<table id="emailHeader" border=0 cellspacing=0 cellpadding=0 width=100% class="mailClientWriteEmailHeader level2Bg menuSeparation" style="position:fixed;">{* crmv@22227 *}
		<tr>
			<td>{$PAGE_TITLE}</td>
		</tr>
	</table>
	<table cellspacing=0 cellpadding=0 width=100%>
		<tr>
			<td><div id="vte_menu_white_small"></div></td>
		</tr>
		<tr>
			<td>
				<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="level3Bg" id="Buttons_List_4" style="position:fixed;z-index:19;">
				<tr>
					<td width="100%" style="padding:5px"></td>
					<td style="padding:5px" nowrap>
						<input type="submit" value="{$APP.LBL_SAVE_BUTTON_LABEL}" class="crmButton small save" style="min-width: 70px">
						<input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" onclick="closePopup()" style="min-width: 70px">
					</td>
				</tr>
				</table>
				<div id="vte_menu_white_1"></div>
				<script>jQuery('#vte_menu_white_1').height(jQuery('#Buttons_List_4').height());</script>
			</td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=10 width=100%>
		<tr>
			<td>
			<table border=0 cellspacing=0 cellpadding=5 width=100% class="small">
				<tr>
					<td width=20% class="dvtCellLabel">{'LBL_NAME'|getTranslatedString:'Settings'}</td>
					<td width=80%>
						<div class="dvtCellInfoM">
							<input name="templatename" type="text" value="{$TEMPLATENAME}" class="detailedViewTextBox" tabindex="1">
						</div>
					</td>
				</tr>
				<tr>
					<td valign=top class="dvtCellLabel">{'LBL_DESCRIPTION'|getTranslatedString:'Settings'}</td>
					<td valign=top>
						<div class="dvtCellInfo">
							<input name="description" type="text" value="{$DESCRIPTION}" class="detailedViewTextBox" tabindex="2">
						</div>
					</td>
				</tr>
				<tr>
					<td valign=top class="dvtCellLabel">{'LBL_FOLDER'|getTranslatedString:'Settings'}</td>
					<td>
						<div class="dvtCellInfo">
							{if $EMODE eq 'edit'}
								<select	name="foldername" class="small" tabindex="" style="width: 100%"	class="detailedViewTextBox" tabindex="3">
								{foreach item=arr from=$FOLDERNAME}
									<option value="{$FOLDERNAME}"{$arr}>{$FOLDERNAME}</option>
									{if $FOLDERNAME == 'Public'}
										<option value="Personal">{'LBL_PERSONAL'|getTranslatedString:'Settings'}</option>
									{else}
										<option value="Public">{'LBL_PUBLIC'|getTranslatedString:'Settings'}</option>
									{/if}
								{/foreach}
								</select>
							{else}
								<select name="foldername" class="small" tabindex=""	value="{$FOLDERNAME}" style="width: 100%" class="detailedViewTextBox" tabindex="3">
									<option value="Personal">{'LBL_PERSONAL'|getTranslatedString:'Settings'}</option>
									<option value="Public" selected>{'LBL_PUBLIC'|getTranslatedString:'Settings'}</option>
								</select>
							{/if}
						</div>
					</td>
				</tr>
				<tr>
					<td width=20% class="dvtCellLabel">{'LBL_TYPE'|getTranslatedString}</td>
					<td>
						<div class="dvtCellInfo">
							<select name="templatetype" class="small" tabindex="" style="width: 100%" class="detailedViewTextBox" tabindex="3">
								{foreach item=arr from=$TEMPLATETYPE}
									{if $arr.value eq 'Newsletter'}	{* solo Newsletter *}
										<option value="{$arr.value}" {$arr.selected}>{$arr.label}</option>
									{/if}
								{/foreach}
							</select>
						</div>
					</td>
				</tr>
				{* crmv@80155 *}
				{if $BU_MC_ENABLED}
					<tr valign="top">
						<td width=20% class="dvtCellLabel">Business Unit</td>
						<td>
							<div class="dvtCellInfo">
								<select name="bu_mc[]" class="detailedViewTextBox" multiple>
								{foreach item=arr from=$BU_MC}
									<option value="{$arr.value}" {$arr.selected}>{$arr.label}</option>
								{/foreach}
								</select>
							</div>
						</td>
			  		</tr>
		  		{/if}
				{* crmv@80155e *}
				<tr>
					<td colspan="2" valign=top class="cellText small">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="thickBorder">
						<tr>
							<td valign=top>
							<table width="100%" border="0" cellspacing="0" cellpadding="5" class="small">
								<tr>
									<td colspan="3" valign="top" class="small" style="background-color: #cccccc"><strong>{'LBL_EMAIL_TEMPLATE'|getTranslatedString:'Settings'}</strong></td>
								</tr>
								<tr>
									<td width="15%" valign="top" class="dvtCellLabel">{'LBL_SUBJECT'|getTranslatedString:'Settings'}</td>
									<td width="85%" colspan="2">
										<div class="dvtCellInfoM">
											<input name="subject" type="text" value="{$SUBJECT}" class="detailedViewTextBox" tabindex="4">
										</div>
									</td>
								</tr>
								<tr>
									<td width="15%" class="dvtCellLabel" valign="center">{'LBL_SELECT_FIELD_TYPE'|getTranslatedString:'Settings'}</td>
									<td width="85%" colspan="2" class="cellText small">
									<table class="cellText small">
										<tr>
											<td>{'LBL_STEP'|getTranslatedString:'Settings'}1</td>
											<td></td>
											<td style="border-left: 2px dotted #cccccc;">{'LBL_STEP'|getTranslatedString:'Settings'}2</td>
											<td></td>
											<td style="border-left: 2px dotted #cccccc;">{'LBL_STEP'|getTranslatedString:'Settings'}3</td>
											<td></td>
										</tr>
										<tr>
											<td><!-- crmv@15309 -->
												<select style="font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #000000; border: 1px solid #bababa; padding-left: 5px; background-color: #ffffff;" id="entityType" ONCHANGE="modifyMergeFieldSelect(this, document.getElementById('mergeFieldSelect'));" tabindex="6">
													<OPTION VALUE="0" selected>{$APP.LBL_NONE}</OPTION>
													{foreach key=module item=arr from=$ALL_VARIABLES name=modules}
														<OPTION VALUE="$smarty.foreach.modules.iteration">{$module|@getTranslatedString}</OPTION>
													{/foreach}
												</select>
											<td>
											<td style="border-left: 2px dotted #cccccc;">
												<select style="font-family: Arial, Helvetica, sans-serif; font-size: 11p x; color: #000000; border: 1px solid #bababa; padding-left: 5px; background-color: #ffffff;" id="mergeFieldSelect" onchange="document.getElementById('mergeFieldValue').value=this.options[this.selectedIndex].value;" tabindex="7">
													<option value="0" selected>{$APP.LBL_NONE}
												</select>
											<td>
											<td style="border-left: 2px dotted #cccccc;">
												<input type="text" id="mergeFieldValue" name="variable" value="variable" style="width: 200px; font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #000000; border: 1px solid #bababa; padding-left: 5px; background-color: #ffffdd;" tabindex="8" />
											<td>
											<td>
												<input class="crmButton small create" type="button" onclick="InsertIntoTemplate('mergeFieldValue');" value="{'LBL_INSERT_INTO_TEMPLATE'|getTranslatedString:'Newsletter'}">
											</td>
										</tr>
										<!-- crmv@15309 end-->
									</table>
									</td>
								</tr>
								<tr>
									<td valign="top" width=10% class="dvtCellLabel">{'LBL_MESSAGE'|getTranslatedString:'Settings'}</td>
									<td valign="top" colspan="2" width=60% class="cellText small">
										<div class="cellInfo">
											<textarea name="body" style="width: 90%; height: 200px" class=small tabindex="5">{$BODY}</textarea>
										</div>
									</td>
								</tr>
							</table>
							</td>
						</tr>
					</table>
					</td>
				</tr>
			</table>
			</td>
		</tr>
	</table>
</form>
</td></tr></table>
</body>
</html>

<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
<script type="text/javascript" defer="1">
var current_language_arr = "{php} echo $_SESSION['authenticated_user_language']; {/php}".split("_");
var curr_lang = current_language_arr[0];
{literal}
CKEDITOR.replace('body', {
	filebrowserBrowseUrl: 'include/ckeditor/filemanager/index.html',
	language : curr_lang
});	
{/literal}
</script>
<script>

function check4null()
{ldelim}
	var form = document.templatecreate;
	var isError = false;
	var errorMessage = "";
	// Here we decide whether to submit the form.
	if (trim(form.templatename.value) =='') {ldelim}
		isError = true;
		errorMessage += "\n{'LBL_NAME'|getTranslatedString:'Settings'}";
		form.templatename.focus();
	{rdelim}
	if (trim(form.foldername.value) =='') {ldelim}
		isError = true;
		errorMessage += "\n{'LBL_FOLDER'|getTranslatedString:'Settings'}";
		form.foldername.focus();
	{rdelim}
	if (trim(form.subject.value) =='') {ldelim}
		isError = true;
		errorMessage += "\n{'LBL_SUBJECT'|getTranslatedString:'Settings'}";
		form.subject.focus();
	{rdelim}
	// Here we decide whether to submit the form.
	if (isError == true) {ldelim}
		alert("{$APP.MISSING_FIELDS}" + errorMessage);
		return false;
	{rdelim}
	//crmv@55961
	if (form.templatetype.value == 'Newsletter') {ldelim}
		var body = CKEDITOR.instances.body.getData();
		if (body.indexOf('$Newsletter||tracklink#unsubscription$') == -1)
			if (confirm(alert_arr.LBL_TEMPLATE_MUST_HAVE_UNSUBSCRIPTION_LINK) == false)
				return false;
		if (body.indexOf('$Newsletter||tracklink#preview$') == -1)
			if (confirm(alert_arr.LBL_TEMPLATE_MUST_HAVE_PREVIEW_LINK) == false)
				return false;
	{rdelim}
	//crmv@55961e
    for (instance in CKEDITOR.instances) {ldelim}
        CKEDITOR.instances[instance].updateElement();
	{rdelim}
    VtigerJS_DialogBox.block();
	return true;
{rdelim}

init();

function returnValue(response) {ldelim}
	submittemplate({$RECORD},response['templateid'],response['templatename']);
{rdelim}

{literal}
function submittemplate(record,templateid,templatename)
{
    res = getFile("index.php?module=Newsletter&action=NewsletterAjax&file=widgets/TemplateEmailSave&record="+record+"&templateid="+templateid);
	//crmv@104558
	if (typeof parent.getObj('templateemail_name')  !== "undefined"){
		parent.getObj('templateemail_name').value = templatename;
	}
	//crmv@104558e
	closePopup();
	parent.location.reload();  //crmv@104558
}
jQuery(window).load(function() {
	loadedPopup();
});
jQuery(document).ready(function() {
	jQuery('#emailHeader').css('z-index',findZMax());
	if (!browser_ie) {
		var addHeight = 21;
	}
	else {
		var addHeight = 0;
	}
	jQuery('#vte_menu_white_small').height(jQuery('#emailHeader').height() + addHeight);
	var options = {
		beforeSerialize: check4null,	// pre-submit callback 
	    success: returnValue,			// post-submit callback 
	    dataType: 'json'				// 'xml', 'script', or 'json' (expected server response type) 
	};
	jQuery('#templatecreate').ajaxForm(options);
});
{/literal}
</script>
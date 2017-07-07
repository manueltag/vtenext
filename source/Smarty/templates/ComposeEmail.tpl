{*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************}
{* crmv@21048m	crmv@22123	crmv@10621	crmv@25356	crmv@2963m	crmv@59091 *}
{assign var="BROWSER_TITLE" value='LBL_COMPOSE'|getTranslatedString:'Messages'}
{include file="HTMLHeader.tpl" head_include="icons,jquery,jquery_plugins,jquery_ui,fancybox,prototype,file_upload,sdk_headers"}

<body marginheight="0" marginwidth="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" class="small">

{include file="Theme.tpl" THEME_MODE="body"}

<div id="popupContainer" style="display:none;"></div> {* crmv@97214 *}

{* Some variables *}
<script type="text/javascript">
	var cc_err_msg = '{$MOD.LBL_CC_EMAIL_ERROR}';
	var no_rcpts_err_msg = '{$MOD.LBL_NO_RCPTS_EMAIL_ERROR}';
	var bcc_err_msg = '{$MOD.LBL_BCC_EMAIL_ERROR}';
	var conf_mail_srvr_err_msg = '{$MOD.LBL_CONF_MAILSERVER_ERROR}';
	//crmv@7216
	var no_subject = '{$MOD.MESSAGE_NO_SUBJECT}';
	var no_subject_label = '{$MOD.LBL_NO_SUBJECT}';
	//crmv@7216e
	var saving_draft = false;
</script>

{* Extra scripts *}
<script type="text/javascript" src="{"modules/Popup/Popup.js"|resourcever}"></script> {* crmv@43864 *}
<script type="text/javascript" src="{"modules/Emails/Emails.js"|resourcever}"></script>
<script type="text/javascript" src="{"modules/Messages/Messages.js"|resourcever}"></script>

{include file='CachedValues.tpl'}	{* crmv@26316 *}

{foreach item=row from=$BLOCKS.fields} {* crmv@104568 *}
	{foreach item=elements from=$row}
		{if $elements.2.0 eq 'from_email'}
			{assign var=element_from_email value=$elements}
		{elseif $elements.2.0 eq 'parent_id'}
			{assign var=element_parent_id value=$elements}
		{elseif $elements.2.0 eq 'subject'}
			{assign var=element_subject value=$elements}
		{elseif $elements.2.0 eq 'filename'}
			{assign var=element_filename value=$elements}
		{elseif $elements.2.0 eq 'description'}
			{assign var=element_description value=$elements}
		{/if}
	{/foreach}
{/foreach}
<div style="display:none" id="signature_box">{$SIGNATURE}</div> {* crmv@48228 *}
<form name="EditView" method="POST" ENCTYPE="multipart/form-data" action="index.php" onkeypress="return event.keyCode != 13;">
<input type="hidden" name="add2queue" value="true">	{* crmv@48501 *}
<input type="hidden" name="send_mail">
<input type="hidden" name="contact_id" value="{$CONTACT_ID}">
<input type="hidden" name="user_id" value="{$USER_ID}">
<input type="hidden" name="old_id" value="{$OLD_ID}">
<input type="hidden" name="module" value="{$MODULE}">
<input type="hidden" name="record" value="{$ID}">
<input type="hidden" name="mode" value="{$MODE}">
<input type="hidden" name="action" value="Save">
<input type="hidden" name="hidden_toid" id="hidden_toid">
<input type="hidden" name="draft_id" id="draft_id" value="{$DRAFTID}">
{if !empty($smarty.request.message)}
	<input type="hidden" name="message" value="{$smarty.request.message}">
	<input type="hidden" name="message_mode" value="{$smarty.request.message_mode}">
{/if}
<input type="hidden" name="uploaddir" value="{$UPLOADIR}">
{* crmv@2043m *}
{if $smarty.request.reply_mail_converter neq ''}
	<input type="hidden" name="reply_mail_converter" value="{$smarty.request.reply_mail_converter}">
	<input type="hidden" name="reply_mail_converter_record" value="{$smarty.request.reply_mail_converter_record}">
	<input type="hidden" name="reply_mail_user" value="{$smarty.request.reply_mail_user}">
{/if}
{* crmv@2043me *}
{* crmv@62394 - activity tracking inputs *}
<input type="hidden" name="tracking_compose_track" id="tracking_compose_track" value="0" >
<input type="hidden" name="tracking_compose_start_ts" id="tracking_compose_start_ts" value="0" >
<input type="hidden" name="tracking_compose_stop_ts" id="tracking_compose_stop_ts" value="0" >
{* crmv@62394e *}
{* crmv@80155 *}
<input type="hidden" name="signature_id" id="signature_id" value="{$SIGNATUREID}">
<input type="hidden" name="use_signature" id="use_signature" value="{$USE_SIGNATURE}">
{* crmv@80155e *}
<div class="closebutton" style="display: block; top:5px; left:5px;" onclick="window.close();"></div>
{* crmv@25356 *}
<table class="small mailClient" border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody>
	<tr id="emailHeader" height="24px" style="margin-bottom:4px;">
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%" class="mailClientWriteEmailHeader level2Bg menuSeparation">
				<tr>
					<td width="30%" style="padding-left: 40px;"><h4>{'LBL_COMPOSE'|getTranslatedString:'Messages'}</h4></td>	{* crmv@125351 *}
					<td width="70%" align="right">
						<span id="composeEmailDraftUpdate" style="font-style:italic;font-weight:normal;font-size:12px;"></span>&nbsp;	{* crmv@31263 *}
						<input class="crmbutton small edit" value="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_LABEL}" type="button" onclick="openPopup('index.php?module=Users&action=lookupemailtemplates','emailtemplate','top=100,left=200,height=400,width=500,resizable=yes,scrollbars=yes,menubar=no,addressbar=no,status=yes','auto');">
						<input class="crmbutton small save" value="{'Save Draft'|getTranslatedString:'Emails'}" type="button" onclick="email_validate(document.EditView,'save');">
						<input class="crmbutton small save" value="{$APP.LBL_SEND}" type="submit">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr height="4"><td colspan="3"></td></tr>
	<tr valign="top" id="pageContents">
		<td width="30%">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="15%"></td>
					<td class="sendingMethod">
						<div>
							<span>{'Send Mode'|getTranslatedString:'Emails'}</span>
						</div>
						{* crmv@82419 *}
						<div class="radio radio-primary">
							<label for="send_mode_single" title="{'LBL_SINGLE_HELPINFO'|getTranslatedString:'Emails'}">
								<input type="radio" name="send_mode" id="send_mode_single" value="single" {if $SEND_MODE eq 'single'}checked="checked"{/if} title="{'LBL_SINGLE_HELPINFO'|getTranslatedString:'Emails'}"/>
								{'LBL_SINGLE_MODE'|getTranslatedString:'Emails'}
							</label>
						</div>
						<div class="radio radio-primary">
							<label for="send_mode_multiple" title="{'LBL_MULTIPLE_HELPINFO'|getTranslatedString:'Emails'}">
								<input type="radio" name="send_mode" id="send_mode_multiple" value="multiple" {if $SEND_MODE eq 'multiple'}checked="checked"{/if} title="{'LBL_MULTIPLE_HELPINFO'|getTranslatedString:'Emails'}"/>
								{'LBL_MULTIPLE_MODE'|getTranslatedString:'Emails'}
							</label>
						</div>
						{* crmv@82419e *}
					</td>
					<td></td>
				</tr>
				<tr height="4"><td colspan="3"></td></tr>
				<tr>
					<td class="mailSubHeader edit" align="center">{$MOD.LBL_FROM}</td>
					<td>
						<div class="dvtCellInfo">
							<select id="from_email" name="from_email" class="detailedViewTextBox" onChange="changeSignature('{$SIGNATUREID}',this.value);">	{* crmv@44037 *}
								{foreach item="from_email_entity" from=$FROM_EMAIL_LIST}
									<option value="{$from_email_entity.email}" {if $from_email_entity.selected eq 'selected'}selected{/if} data-accountid="{$from_email_entity.account}">{if $from_email_entity.name neq ''}"{$from_email_entity.name}"{/if}&lt;{$from_email_entity.email}&gt;</option> {* crmv@114260 *}
								{/foreach}
							</select>
						</div>
					</td>
					<td></td>
				</tr>
				<tr height="10"><td colspan="3"></td></tr>
				<tr valign="top">
					<td align="center">
						<input class="crmbutton small edit" style="width:90%;height:32px;" type="button" value="{$MOD.LBL_TO}" onclick='openPopup("index.php?return_module={$MODULE}&module=Emails&action=EmailsAjax&file=PopupDest&fromEmail=1","","","auto",1050,505);'>
					</td>
					<td>
				 		<input name="{$element_parent_id.2.0}" id="{$element_parent_id.2.0}" type="hidden" value="{$IDLISTS}">
						<input type="hidden" name="saved_toid" id="saved_toid" value="{$TO_MAIL}">
						<input id="parent_name" name="parent_name" readonly class="txtBox1" type="hidden" value="{$TO_MAIL}">
						<div class="dvtCellInfo" id="autosuggest_to" style="min-height:50px;max-height:150px;overflow-y:auto;overflow-x:hidden;" onClick="jQuery('#to_mail').focus();">
							{$AUTOSUGGEST}
							{* <input id="to_mail" name="to_mail" class="detailedViewTextBox" value="{$OTHER_TO_MAIL}"> *}
							<textarea id="to_mail" name="to_mail" class="detailedViewTextBox" style="height:50px;padding:0px;">{$OTHER_TO_MAIL}</textarea>
						</div>
					</td>
					<td style="padding:5px;">
						<i class="vteicon md-link" title="{$APP.LBL_CLEAR}" onClick="jQuery('#parent_id').val(''); jQuery('#hidden_toid').val('');jQuery('#parent_name').val('');jQuery('#saved_toid').val('');jQuery('#to_mail').val('');jQuery('#autosuggest_to span').remove();return false;">highlight_remove</i>
					</td>
		   		</tr>
				<tr height="10"><td colspan="3"></td></tr>
				<tr valign="top">
					<td align="center">
						<input class="crmbutton small edit" style="width:90%;height:32px;" type="button" value="{$MOD.LBL_CC}" onclick='openPopup("index.php?return_module={$MODULE}&module=Emails&action=EmailsAjax&file=PopupDest&fromEmail=1","","","auto",1050,505);'>
					</td>
					<td>
						<div class="dvtCellInfo">
							<textarea name="ccmail" id="cc_name" class="detailedViewTextBox" style="height:50px;padding:0px;">{$CC_MAIL}</textarea>
						</div>
					</td>
					<td style="padding-left:5px;">
						<i class="vteicon md-link" title="{$APP.LBL_CLEAR}" onClick="jQuery('#cc_name').val('');return false;">highlight_remove</i>
					</td>
			   	</tr>
			   	<tr height="10"><td colspan="3"></td></tr>
			   	<tr valign="top" id="ccn_add">
			   		<td></td>
			   		<td colspan="2">
			   			<a href="javascript:;" onclick="getObj('ccn_row').show();getObj('ccn_add').hide();">{$MOD.LBL_ADD_BCC}</a>
			   		</td>
			   	</tr>
				<tr valign="top" id="ccn_row" style="display:none;">
					<td align="center">
						<input class="crmbutton small edit" style="width:90%;height:32px;" type="button" value="{$MOD.LBL_BCC}" onclick='openPopup("index.php?return_module={$MODULE}&module=Emails&action=EmailsAjax&file=PopupDest&fromEmail=1","","","auto",1050,505);'>
					</td>
					<td>
						<div class="dvtCellInfo">
							<textarea name="bccmail" id="bcc_name" class="detailedViewTextBox" style="height:50px;padding:0px;">{$BCC_MAIL}</textarea>
						</div>
					</td>
					<td style="padding-left:5px;">
						<i class="vteicon md-link" title="{$APP.LBL_CLEAR}" LANGUAGE=javascript onClick="jQuery('#bcc_name').val('');return false;">highlight_remove</i>
					</td>
				</tr>
			</table>
		</td>
		<td width="60%" rowspan="2">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td class="mailSubHeader gray" width="5%">{$element_subject.1.0}</td>
				    <td width="100%" style="padding: 4px;">
						<div class="dvtCellInfo">
					        {if $RET_ERROR eq 1}
								<input type="text" class="detailedViewTextBox" name="{$element_subject.2.0}" value="{$SUBJECT}" id="{$element_subject.2.0}">
					        {else}
								<input type="text" class="detailedViewTextBox" name="{$element_subject.2.0}" value="{$element_subject.3.0}" id="{$element_subject.2.0}">
					        {/if}
						</div>
				    </td>
			   	</tr>
			   	<tr>
			   		<td colspan="2" class="mailSubHeader">
			   			<div class="cellInfo message-compose-border">
							{* crmv@56409 - textareas require the text to be escaped, but due to several previous encoding/decoding, it's safer just to escape the < > *}
							<textarea class="detailedViewTextBox" id="description" name="description" cols="90" rows="16">{$element_description.3.0|replace:'&lt;':'&amp;lt;'|replace:'&gt;':'&amp;gt;'}</textarea>
						</div>
					</td>
			   	</tr>
			</table>
		</td>
		<td width="10%" rowspan="3" style="padding:0px 5px;" id="ComposeLinks">
			<input type="hidden" id="relation" name="relation" value="{$LINKS_STR}">
			{include file="TurboliftButtons.tpl"} {* crmv@42752 crmv@43864 *}
		</td>
	</tr>
	<tr valign="bottom">
		<td style="padding: 4px;">
			{* crmv@22123 *}	{* crmv@30356 *}
			{if isMobile() neq true}
				<div class="mailSubHeader gray" style="border:none;height:35px;">
					<img src="modules/Messages/src/img/flag_attach.png" style="vertical-align:middle;padding-right:7px;">{$element_filename.1.0}
				</div>
				<div id="attach_cont" style="border:none;">
					<table cellspacing="0" cellpadding="0" width="100%" class="small attachmentsEmail">
						<tr>
							<td></td>
							<td align="right" valign="middle">
								{if ($element_filename.3|@count gt 0) OR ($smarty.request.attachment != '') OR ($COMMON_TEMPLATE_NAME neq '') OR ($webmail_attachments neq '')}{* crmv@22139 *} {* crmv@23060 *} {* crmv@25554 *}
									<div style="width:100%;height:60px;overflow:auto;">
										<table cellpaddin="0" cellspacing="0" class="small" width="100%">
										{if $smarty.request.attachment != ''}
											<tr><td width="100%" colspan="2">{$smarty.request.attachment|@vtlib_purify}<input type="hidden" value="{$smarty.request.attachment|@vtlib_purify}" name="pdf_attachment"></td></tr>
										{else} {* crmv@23060 *}
											{foreach item="attach_files" key="attach_id" from=$element_filename.3}
												<tr id="row_{$attach_id}"><td width="90%">{$attach_files}</td><td align="right"><i class="vteicon checkko md-link md-sm" onClick="delAttachments({$attach_id})" title="{$APP.LBL_DELETE_BUTTON}">clear</i></td></tr>
											{/foreach}
											<input type='hidden' name='att_id_list' value='{$ATT_ID_LIST}' />
										{/if}
										{foreach item="attach_files" from=$webmail_attachments}
											<tr><td width="90%">{$attach_files}</td></tr>
								        {/foreach}
										</table>
									</div>
								{/if}
								<div id="uploader" style="width:100%;height:90px;">You browser doesn't support upload.</div>
							</td>
						</tr>
					</table>
				</div>
			{/if}
			{* crmv@22123e *}	{* crmv@30356e *}
		</td>
	</tr>
	<tr id="DETAILVIEWWIDGETBLOCK">
   		<td colspan="2">
			{* vtlib Customization: Embed DetailViewWidget block:// type if any *}
			{if $CUSTOM_LINKS && !empty($CUSTOM_LINKS.DETAILVIEWWIDGET)}
			{foreach item=CUSTOM_LINK_DETAILVIEWWIDGET from=$CUSTOM_LINKS.DETAILVIEWWIDGET}
				{if preg_match("/^block:\/\/.*/", $CUSTOM_LINK_DETAILVIEWWIDGET->linkurl)}
				<!-- crmv@18485 -->
				{php}
					$widgetLinkInfo_tmp = $this->_tpl_vars['CUSTOM_LINK_DETAILVIEWWIDGET'];
					if (preg_match("/^block:\/\/(.*)/", $widgetLinkInfo_tmp->linkurl, $matches)) {
						list($widgetControllerClass_tmp, $widgetControllerClassFile_tmp) = explode(':', $matches[1]);
						if (vtlib_isModuleActive($widgetControllerClass_tmp)) {
				{/php}
				<!-- crmv@18485e -->
				<table width="100%" cellspacing="0" cellpadding="0"><tr><td>
					<tr>
						<td style="padding:5px;" >
						{php}
							echo vtlib_process_widget($this->_tpl_vars['CUSTOM_LINK_DETAILVIEWWIDGET'], $this->_tpl_vars);
						{/php}
						</td>
					</tr>
				</table>
				<!-- crmv@18485 -->
				{php}}}{/php}
				<!-- crmv@18485e -->
				{/if}
			{/foreach}
			{/if}
			{* END *}
		</td>
	</tr>
</tbody>
</table>
</form>
<div id="hideBottom" style="display:none;"></div>
</body>

{if $FCKEDITOR_DISPLAY eq 'true'}
	<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
{/if}
<script type="text/javascript">
jQuery(document).ready(function() {ldelim}

	{if $FCKEDITOR_DISPLAY eq 'true'}
		{literal}
		CKEDITOR.replace('description', {
			filebrowserBrowseUrl: 'include/ckeditor/filemanager/index.html',
			toolbar : 'Basic',	//crmv@31210
			{/literal}
			language : "{php}echo get_short_language();{/php}",
			imageUploadUrl : 'index.php?module=Emails&action=EmailsAjax&file=plupload/upload&ckeditor=true&dir={$UPLOADIR}', //crmv@81704
			{literal}
			customConfig : 'message_config.js'
		});
		{/literal}
	{/if}
	
	if (jQuery('#use_signature').val() == 1) setSignature('{$SIGNATUREID}');	{* crmv@44037 crmv@48228 crmv@80155 *}

{literal}	

	// crmv@82419
	setInterval(function() {
		email_validate(document.EditView,'auto_save');
	}, 90000);
	// crmv@82419e


	jQuery("#uploader").pluploadQueue({
		// General settings
		runtimes: 'html5,flash,silverlight', //crmv@25883
		url: 'index.php?module=Emails&action=EmailsAjax&file=plupload/upload&dir={/literal}{$UPLOADIR}{literal}',
		max_file_size: '{/literal}{$FOCUS->max_attachment_size}{literal}mb', //crmv@58893
		chunk_size: '1mb',
		unique_names: true,
		prevent_duplicates: true,
		runtime_visible: false, // show current runtime in statusbar
		// Resize images on clientside if we can
		//resize: {width: 320, height: 240, quality: 90},
		// Specify what files to browse for
		// Flash/Silverlight paths
		flash_swf_url: 'modules/Emails/plupload/plupload.flash.swf',
		silverlight_xap_url: 'modules/Emails/plupload/plupload.silverlight.xap',
		// PreInit events, bound before any internal events
		preinit: {
			Init: function(up, info) {
			},
			UploadFile: function(up, file) {
				// You can override settings before the file is uploaded
				// up.settings.url = 'upload.php?id=' + file.id;
				// up.settings.multipart_params = {param1: 'value1', param2: 'value2'};
			}
		},
		// Post init events, bound after the internal events
		init: {
			Refresh: function(up) {
				// Called when upload shim is moved
			},
			StateChanged: function(up) {
				// Called when the state of the queue is changed
			},
			QueueChanged: function(up) {
				// Called when the files in queue are changed by adding/removing files
			},
			UploadProgress: function(up, file) {
				// Called while a file is being uploaded
			},
			FilesAdded: function(up, files) {
				// Called when files are added to queue
				//crmv@58893
				var total_size_before_upload = up.total.size;
				var queue_size = 0;
				plupload.each(files, function(file) {
					queue_size+=file.size;
				});
				if (total_size_before_upload+queue_size > up.settings.max_file_size){
					var filenames='';
					plupload.each(files, function(file) {
						filenames+=","+file.name;
						up.removeFile(file);
					});					
					//show error
					up.trigger("Error",{code:plupload.FILE_SIZE_ERROR,message:plupload.translate("File size error."),file:{'name':filenames.slice(1)}});
				}
				else{
					up.start();	//crmv@24568
				}
				//crmv@58893 e
			},
			FilesRemoved: function(up, files) {
				// Called when files where removed from queue
				plupload.each(files, function(file) {
				});
			},
			FileUploaded: function(up, file, info) {
				// Called when a file has finished uploading
				jQuery('.plupload_buttons').show();
				jQuery('.plupload_upload_status').hide();
			},
			ChunkUploaded: function(up, file, info) {
				// Called when a file chunk has finished uploading
			},
			Error: function(up, args) {
				// Called when a error has occured
				// Handle file specific error and general error
				if (args.file) {
				} else {
				}
			}
		}
	});
	//crmv@24568
	jQuery(".plupload_start").detach();
	jQuery(".plupload_header").detach();
	jQuery(".plupload_filelist_header").hide();
	//crmv@24568e

	var options = {
		beforeSerialize: beforeSendEmail,	//crmv@104438
	    success: successSendEmail,
	    error: errorSendEmail
	};
	jQuery('form[name="EditView"]').ajaxForm(options);
});

//crmv@22139	//crmv@31691
{/literal}
{if $smarty.request.attachment != '' && $smarty.request.rec != ''}
{literal}
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
	             method: 'post',
	  		     postBody:"module=Documents&action=DocumentsAjax&file=EmailFile&record={/literal}{$smarty.request.rec}{literal}",
	             onComplete: function(response) {}
		}
	);
{/literal}{/if}{literal}
//crmv@22139e	//crmv@31691e

jQuery(function() {
	//crmv@32091
	function split( val ) {
		var arr = val.split( /,\s*/ );
		arr = cleanArray(arr);
		return arr;
	}
	//crmv@32091e
	function extractLast( term ) {
		return split( term ).pop();
	}
	jQuery("#to_mail")
		// don't navigate away from the field on tab when selecting an item
		.bind( "keydown", function( event ) {
			if ( event.keyCode === jQuery.ui.keyCode.TAB &&
					jQuery( this ).data( "autocomplete" ).menu.active ) {
				event.preventDefault();
			}
		})
		.autocomplete({
			source: function( request, response ) {
				jQuery.getJSON( "index.php?module=Emails&action=EmailsAjax&file=Autocomplete", {
					term: extractLast( request.term )
				}, response );
			},
			search: function() {
				// custom minLength
				var term = extractLast( this.value );
				if ( term.length < 3 ) {
					return false;
				}
			},
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			select: function( event, ui ) {
				var terms = split( this.value );
				// remove the current input
				terms.pop();
				// add placeholder to get the comma-and-space at the end
				terms.push('');
				this.value = terms.join(', ');

				// add the selected item
				var span = '<span id="to_'+ui.item.id+'" class="addrBubble">'+ui.item.value
						+'<div id="to_'+ui.item.id+'_parent_id" style="display:none;">'+ui.item.parent_id+'</div>'
						+'<div id="to_'+ui.item.id+'_parent_name" style="display:none;">'+ui.item.parent_name+'</div>'
						+'<div id="to_'+ui.item.id+'_hidden_toid" style="display:none;">'+ui.item.hidden_toid+'</div>'
						+' <div id="to_'+ui.item.id+'_remove" class="ImgBubbleDelete" onClick="removeAddress(\'to\',\''+ui.item.id+'\');"><i class="vteicon small">clear</i></div>'
						+'</span>';
				jQuery("#autosuggest_to").prepend(span);

				document.EditView.parent_id.value = document.EditView.parent_id.value+ui.item.parent_id+'|';
				document.EditView.parent_name.value = document.EditView.parent_name.value+ui.item.parent_name+' <'+ui.item.hidden_toid+'>,';
				document.EditView.hidden_toid.value = ui.item.hidden_toid+','+document.EditView.hidden_toid.value;

				return false;
			}
		}
	);
	jQuery("#cc_name")
		// don't navigate away from the field on tab when selecting an item
		.bind( "keydown", function( event ) {
			if ( event.keyCode === jQuery.ui.keyCode.TAB &&
					jQuery( this ).data( "autocomplete" ).menu.active ) {
				event.preventDefault();
			}
		})
		.autocomplete({
			source: function( request, response ) {
				jQuery.getJSON( "index.php?module=Emails&action=EmailsAjax&file=Autocomplete&field=cc_name", {
					term: extractLast( request.term )
				}, response );
			},
			search: function() {
				// custom minLength
				var term = extractLast( this.value );
				if ( term.length < 3 ) {
					return false;
				}
			},
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			select: function( event, ui ) {
				var terms = split( this.value );
				// remove the current input
				terms.pop();
				// add the selected item
				terms.push( ui.item.value );
				// add placeholder to get the comma-and-space at the end
				terms.push( "" );
				this.value = terms.join( ", " );
				return false;
			}
		}
	);
	jQuery("#bcc_name")
		// don't navigate away from the field on tab when selecting an item
		.bind( "keydown", function( event ) {
			if ( event.keyCode === jQuery.ui.keyCode.TAB &&
					jQuery( this ).data( "autocomplete" ).menu.active ) {
				event.preventDefault();
			}
		})
		.autocomplete({
			source: function( request, response ) {
				jQuery.getJSON( "index.php?module=Emails&action=EmailsAjax&file=Autocomplete&field=bcc_name", {
					term: extractLast( request.term )
				}, response );
			},
			search: function() {
				// custom minLength
				var term = extractLast( this.value );
				if ( term.length < 3 ) {
					return false;
				}
			},
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			select: function( event, ui ) {
				var terms = split( this.value );
				// remove the current input
				terms.pop();
				// add the selected item
				terms.push( ui.item.value );
				// add placeholder to get the comma-and-space at the end
				terms.push( "" );
				this.value = terms.join( ", " );
				return false;
			}
		}
	);
});
{/literal}
</script>
</html>
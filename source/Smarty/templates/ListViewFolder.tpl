{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@30967 crmv@104853 crmv@107103 *}

<script language="JavaScript" type="text/javascript" src="{"include/js/ListView.js"|resourcever}"></script>
<script language="JavaScript" type="text/javascript" src="include/js/search.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/Merge.js"></script>
<script language="JavaScript" type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
<script language="JavaScript" type="text/javascript">
{literal}
	var lviewFolder = {x:0, y:0, hidden: true};
{/literal}
</script>
{include file='Buttons_List.tpl'}

<div id="Buttons_List_3_Container" style="display:none;">	{*<!-- crmv@18592 -->*}
<table border=0 cellspacing=0 cellpadding=2 width=100% class="small">
	<tr>
		<td style="padding:5px">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td align="left">
						<div style="float:left">
							<form method="GET" action="index.php">
								<input type="hidden" name="module" value ="{$MODULE}" />
								<input type="hidden" name="action" value ="ListView" />
								<button id="lviewfolder_button_list" type="submit" class="crmbutton small edit" title="{$APP.LBL_LIST}">{$APP.LBL_LIST}</button>
							</form>
						</div>
						{if $CHECK.EditView eq 'yes'}
							<div style="float:left">
								<button id="lviewfolder_button_add" type="button" name="add" class="crmbutton small edit" onClick="fnvshobj(this,'lview_folder_add');" title="{$APP.LBL_ADD_NEW_FOLDER}">{$APP.LBL_ADD_NEW_FOLDER}</button>&nbsp;
								<button id="lviewfolder_button_del" type="button" name="delete" class="crmbutton small delete" onClick="lviewfold_del();" title="{$APP.LBL_DELETE_FOLDERS}">{$APP.LBL_DELETE_FOLDERS}</button>
								<button id="lviewfolder_button_del_save" style="display:none" type="button" name="delete_save" class="crmbutton small delete" onClick="lviewfold_del_save('{$MODULE}');" title="{$APP.LBL_DELETE_BUTTON}">{$APP.LBL_DELETE_BUTTON}</button>
								<button id="lviewfolder_button_del_cancel" style="display:none" type="button" name="delete_cancel" class="crmbutton small cancel" onClick="lviewfold_del_cancel();" title="{$APP.LBL_CANCEL_BUTTON_LABEL}">{$APP.LBL_CANCEL_BUTTON_LABEL}</button>
							</div>
						{/if}
					</td>
					<td align="right">
						{if $HIDE_BUTTON_SEARCH eq false}
							<form id="basicSearch" name="basicSearch" method="post" action="index.php">
		                        <input type="hidden" name="module" value="{$MODULE}" />
			            		<input type="hidden" name="action" value="ListView" />
		                        <input type="hidden" name="parenttab" value="{$CATEGORY}" />
								<input type="hidden" name="viewmode" value="ListView" />
								<input type="hidden" name="searchtype" value="BasicSearch" />
		                        <input type="hidden" name="query" value="true" />
		                        <input type="hidden" name="search" value="true" />
			            		<input type="hidden" id="basic_search_cnt" name="search_cnt" />
			
								<div class="form-group basicSearch advIconSearch">
									<input type="text" class="form-control searchBox" id="basic_search_text" name="search_text" value="{$APP.LBL_SEARCH_TITLE}{$MODULE|getTranslatedString:$MODULE}" onclick="clearText(this)" onblur="restoreDefaultText(this, '{$APP.LBL_SEARCH_TITLE}{$MODULE|getTranslatedString:$MODULE}')" />
									<span class="cancelIcon" style="right:20px">
										<i class="vteicon md-link md-sm" id="basic_search_icn_canc" style="display:none" title="Reset" onclick="cancelSearchText('{$APP.LBL_SEARCH_TITLE}{$MODULE|getTranslatedString:$MODULE}')">cancel</i>&nbsp;
									</span>
									<span class="searchIcon" style="right:0px">
										<i id="basic_search_icn_go" class="vteicon" title="{$APP.LBL_FIND}" style="cursor:pointer" onclick="jQuery('#basicSearch').submit();" >search</i>
									</span>
								</div>
							</form>
						{/if}
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
<script>calculateButtonsList3();</script>

{* crmv@18549 *}
{* create report *}
{if $MODULE eq 'Reports'}
<div class="drop_mnu" style="display: none; left: 193px; top: 106px; position:absolute" id="reportLay" onmouseout="fninvsh('reportLay');" onmouseover="fnvshNrm('reportLay')"> {* crmv@29686 *}
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tbody>
	{assign var="count" value=0}
	{foreach item=modules key=modulename from=$REPT_MODULES}
		{if $count is div by 2}
			{assign var="count_tmp" value=1}
			<tr>
		{/if}
		<td><a href="javascript:CreateReport('{$modulename}');" class="drop_down">{$modules}</a></td>
		{if $count_tmp is div by 2}
			</tr>
		{/if}
		{assign var="count" value=$count+1}
		{assign var="count_tmp" value=1}
	{/foreach}
	</tbody>
</table>
</div>
{/if}
{* crmv@18549e *}

<div id="lview_folder_add" style="display:none; position:fixed;" class="crmvDiv">
	<table border="0" cellpadding="5" cellspacing="0" width="100%">
		<tr style="cursor:move;" height="34">
			<td id="AddFolder_Handle" style="padding:5px" class="level3Bg">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="80%"><b>
						<span>{$APP.LBL_ADD_NEW_FOLDER}</span>
					</b></td>
					<td width="20%" align="right">
						<button id="lview_folder_save" type="button" name="button" class="crmbutton small save" title="{$APP.LBL_SAVE_LABEL}" onclick="lviewfold_add()">{$APP.LBL_SAVE_LABEL}</button>
					</td>
				</tr>
				</table>
			</td>
		</tr>
	</table>
	<div id="lview_folder_addcont">
		<form name="lview_folder_addform" id="lview_folder_addform">
			<input type="hidden" name="formodule" value="{$MODULE}" />
			<input type="hidden" name="subaction" value="add" />
		<table class="table borderless">
			<tr>
				<td width="20%">{$APP.LBL_FOLDER_NAME}</td>
				<td width="80%">
					<div class="dvtCellInfo">
						<input type="text" maxlength="50" name="foldername" class="detailedViewTextBox" value="" />
					</div>
				</td>
			</tr>
			<tr>
				<td>{$APP.LBL_DESCRIPTION}</td>
				<td>
					<div class="dvtCellInfo">
						<input type="text" maxlength="100" name="folderdesc" class="detailedViewTextBox" value="" />
					</div>
				</td>
			</tr>
		</table>
		</form>
	</div>
	<br />
	<div class="closebutton" onClick="fninvsh('lview_folder_add');"></div>
</div>
<script type="text/javascript">
	var REHandle = document.getElementById("AddFolder_Handle");
	var RERoot   = document.getElementById("lview_folder_add");
	Drag.init(REHandle, RERoot);
</script>

{* crmv@90004 *}
<div id="lview_folder_edit" style="display:none; position:fixed;" class="crmvDiv">
	<table border="0" cellpadding="5" cellspacing="0" width="100%">
		<tr style="cursor:move;" height="34">
			<td id="EditFolder_Handle" style="padding:5px" class="level3Bg">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="80%"><b>
						<span>{'LBL_EDIT_FOLDER'|@getTranslatedString:'Documents'}</span>
					</b></td>
					<td width="20%" align="right">
						<input id="lview_folder_save" type="button" value="{$APP.LBL_SAVE_LABEL}" name="button" class="crmbutton small save" title="{$APP.LBL_SAVE_LABEL}" onclick="folder_edit(this,'lview_folder_edit','{$MODULE}','','save');" />
					</td>
				</tr>
				</table>
			</td>
		</tr>
	</table>
	<div id="lview_folder_editcont">
		<form name="lview_folder_editform" id="lview_folder_editform">
			<input type="hidden" name="formodule" value="{$MODULE}" />
			<input type="hidden" name="subaction" value="edit" />
			<input type="hidden" name="folderid" id="folderid" value="" />
			<iput type="hidden" name="filecount" id="filecount" value="" />
			<table class="table borderless">
				<tr>
					<td width="20%">{$APP.LBL_FOLDER_NAME}</td>
					<td width="80%">
						<div class="dvtCellInfo">
							<input type="text" maxlength="50" name="foldername" id="foldername" class="detailedViewTextBox" value="" />
						</div>		
					</td>
				</tr>
				<tr>
					<td>{$APP.LBL_DESCRIPTION}</td>
					<td>
						<div class="dvtCellInfo">
							<input type="text" maxlength="100" name="folderdesc" id="folderdesc" class="detailedViewTextBox" value="" />
						</div>		
					</td>
				</tr>
			</table>
		</form>
	</div>
	<br />
	<div class="closebutton" onClick="fninvsh('lview_folder_edit');"></div>
</div>
<script type="text/javascript">
	var REHandle = document.getElementById("EditFolder_Handle");
	var RERoot   = document.getElementById("lview_folder_edit");
	Drag.init(REHandle, RERoot);
</script>
{* crmv@90004e *}

<div id="lview_table_cont" class="container-fluid small lview_folder_table">
		{assign var=foldercount value=0}
		{foreach item=folder from=$FOLDERLIST}
		{if $foldercount % 6 eq 0}
				<div class="row">
				<div class="col-xs-12">
			{/if}
			{assign var=foldercontent value=$folder.content}
			
			<div class="lview_folder_td col-xs-2" style="padding:20px 0px" {if $folder.editable eq true}onmouseover="showPencil({$folder.folderid},1);" onmouseout="showPencil({$folder.folderid},2);"{/if}>
				<div>
					{* crmv@90004 *}
					{if $folder.editable eq true}
						<i class="vteicon" id="pencil_{$folder.folderid}" onmouseover="showPencil({$folder.folderid},3);" onclick="folder_edit(this,'lview_folder_edit','{$MODULE}',{$folder.folderid},'',{$foldercontent.count});" style="display:none;padding:10px;position:absolute;background-color:#F5F5F5;border-radius:10px;right:0px;cursor:pointer">create</i>
					{/if}
					<a href="index.php?action=ListView&module={$MODULE}&folderid={$folder.folderid}"> 
						<img class="lview_folder_img pencil img-responsive" style="max-width:140px;width:100%;margin:0px auto" src="themes/softed/images/listview_folder.png" border="0" />
					</a><br />
					{* crmv@90004e *}
				</div>
				<div>
					{if $foldercontent.count eq 0}
						<span id="lview_folder_checkspan_{$folder.folderid}" style="display:none"><input type="checkbox" name="lvidefold_check_{$folder.folderid}" id="lvidefold_check_{$folder.folderid}" value="" /></span>
					{/if}
					<span class="lview_folder_span">{$folder.foldername} ({$foldercontent.count})</span><br />	{* crmv@90004 *}
					<div class="lview_folder_desc">{$folder.description}&nbsp;</div>	{* crmv@90004 *}
				</div>
				<div class="lview_folder_tooltip" id="lviewfold_tooltip_{$folder.folderid}">
					{$foldercontent.html}
				</div>
			 </div>
			{assign var=foldercount value=$foldercount+1}
			{if $foldercount % 6 eq 0}
			</div>
				</div>
			{/if}
		{/foreach}
</div>
<br />

{literal}
<script type="text/javascript">
jQuery(document).mousemove(function(event){
	lviewFolder.x = event.pageX;
	lviewFolder.y = event.pageY;
});
</script>
{/literal}
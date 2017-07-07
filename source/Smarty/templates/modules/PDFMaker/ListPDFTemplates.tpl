{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
   *
 ********************************************************************************/
-->*}
{* ITS4YOU TT0093 VlMe N *}
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<script>
function ExportTemplates()
{ldelim}
	if(typeof(document.massdelete.selected_id) == 'undefined')
		return false;
        x = document.massdelete.selected_id.length;
        idstring = "";

        if ( x == undefined)
        {ldelim}

                if (document.massdelete.selected_id.checked)
                {ldelim}
                        idstring = document.massdelete.selected_id.value;
                        
                        window.location.href = "index.php?module=PDFMaker&action=PDFMakerAjax&file=ExportPDFTemplate&templates="+idstring;
		     	xx=1;
                {rdelim}
                else
                {ldelim}
                        alert("{$APP.SELECT_ATLEAST_ONE}");
                        return false;
                {rdelim}
        {rdelim}
        else
        {ldelim}
                xx = 0;
                for(i = 0; i < x ; i++)
                {ldelim}
                        if(document.massdelete.selected_id[i].checked)
                        {ldelim}
                                idstring = document.massdelete.selected_id[i].value +";"+idstring
                        xx++
                        {rdelim}
                {rdelim}
                if (xx != 0)
                {ldelim}
                        document.massdelete.idlist.value=idstring;
                        
                        window.location.href = "index.php?module=PDFMaker&action=PDFMakerAjax&file=ExportPDFTemplate&templates="+idstring;
                {rdelim}
                else
                {ldelim}
                        alert("{$APP.SELECT_ATLEAST_ONE}");
                        return false;
                {rdelim}
       {rdelim}

{rdelim}

function massDelete()
{ldelim}
	if(typeof(document.massdelete.selected_id) == 'undefined')
		return false;
        x = document.massdelete.selected_id.length;
        idstring = "";

        if ( x == undefined)
        {ldelim}

                if (document.massdelete.selected_id.checked)
               {ldelim}
                        document.massdelete.idlist.value=document.massdelete.selected_id.value+';';
			xx=1;
                {rdelim}
                else
                {ldelim}
                        alert("{$APP.SELECT_ATLEAST_ONE}");
                        return false;
                {rdelim}
        {rdelim}
        else
        {ldelim}
                xx = 0;
                for(i = 0; i < x ; i++)
                {ldelim}
                        if(document.massdelete.selected_id[i].checked)
                        {ldelim}
                                idstring = document.massdelete.selected_id[i].value +";"+idstring
                        xx++
                        {rdelim}
                {rdelim}
                if (xx != 0)
                {ldelim}
                        document.massdelete.idlist.value=idstring;
                {rdelim}
               else
                {ldelim}
                        alert("{$APP.SELECT_ATLEAST_ONE}");
                        return false;
                {rdelim}
       {rdelim}
		if(confirm("{$APP.DELETE_CONFIRMATION}"+xx+"{$APP.RECORDS}"))
		{ldelim}
	        	document.massdelete.action.value= "DeletePDFTemplate";
		{rdelim}
		else
		{ldelim}
			return false;
		{rdelim}

{rdelim}
</script>

{include file='Buttons_List.tpl'}	{* crmv *}
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">   
<tr>
    {*<td valign="top"></td>*}
    <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
    <form  name="massdelete" method="POST" onsubmit="VtigerJS_DialogBox.block();">
    <input name="idlist" type="hidden">
    <input name="module" type="hidden" value="PDFMaker">
    <input name="parenttab" type="hidden" value="Tools">
    <input name="action" type="hidden" value="">

    <table border=0 cellspacing=0 cellpadding=0 width=100% >
    <tr><td>
        <table border=0 cellspacing=0 cellpadding=5 width=100% class="listTableTopButtons">
        <tr>
            {if $DELETE eq 'permitted'}
            <td class=small><input type="submit" value="{$MOD.LBL_DELETE}" onclick="return massDelete();" class="crmButton delete small"></td>
						{/if}
			<td align="right">
			{if $EDIT eq 'permitted'}
            <input class="crmButton create small" type="submit" value="{$MOD.LBL_ADD_TEMPLATE}" name="profile"  onclick="this.form.action.value='EditPDFTemplate'; this.form.parenttab.value='Tools'" />
			&nbsp;
            {/if}
            {if $IS_ADMIN eq '1' && $TO_UPDATE eq 'true'}
               <input type="button" value="{$MOD.LBL_UPDATE}" class="crmbutton small delete" title="{$MOD.LBL_UPDATE}" onclick="window.location.href='index.php?module=PDFMaker&action=update&parenttab=Tools'" />
               &nbsp;
            {/if} 
            {if $IS_ADMIN eq '1'}					      
                <span id="vtbusy_info" style="display:none;" valign="bottom">{include file="LoadingIndicator.tpl"}</span>
            {/if}
            </td>		
        </tr>
        </table>
        
        {if $DIR eq 'asc'}
            {assign var="dir_img" value='<img src="themes/images/arrow_up.gif" border="0" />'}
        {else}
            {assign var="dir_img" value='<img src="themes/images/arrow_down.gif" border="0" />'}
        {/if}
        
        {assign var="name_dir" value="asc"}
        {assign var="module_dir" value="asc"}
        {assign var="description_dir" value="asc"}
        
        {if $ORDERBY eq 'filename' && $DIR eq 'asc'}        
            {assign var="name_dir" value="desc"}
        {elseif $ORDERBY eq 'module' && $DIR eq 'asc'}                
            {assign var="module_dir" value="desc"}
        {elseif $ORDERBY eq 'description' && $DIR eq 'asc'}
            {assign var="description_dir" value="desc"}           
        {/if}
        
        <table border=0 cellspacing=0 cellpadding=5 width=100% class="listTable">
        <tr>
            <td width=2% class="colHeader small">#</td>
            <td width=3% class="colHeader small">{$MOD.LBL_LIST_SELECT}</td>
            <td width=20% class="colHeader small"><a href="index.php?module=PDFMaker&action=index&parenttab=Tools&orderby=name&dir={$name_dir}">{$MOD.LBL_PDF_NAME}{if $ORDERBY eq 'filename'}{$dir_img}{/if}</a></td>
            <td width=20% class="colHeader small"><a href="index.php?module=PDFMaker&action=index&parenttab=Tools&orderby=module&dir={$module_dir}">{$MOD.LBL_MODULENAMES}{if $ORDERBY eq 'module'}{$dir_img}{/if}</a></td>
            <td width=40% class="colHeader small"><a href="index.php?module=PDFMaker&action=index&parenttab=Tools&orderby=description&dir={$description_dir}">{$MOD.LBL_DESCRIPTION}{if $ORDERBY eq 'description'}{$dir_img}{/if}</a></td>
            {if $VERSION_TYPE neq 'deactivate'}<td width=5% class="colHeader small">{$APP.LBL_STATUS}</td>
            <td width=5% class="colHeader small">{$APP.LBL_ACTIONS}</td>{/if}
        </tr>
        {foreach item=template name=mailmerge from=$PDFTEMPLATES}
        <tr {if $template.status eq 0} style="font-style:italic;" {/if}>
            <td class="listTableRow small" valign=top>{$smarty.foreach.mailmerge.iteration}</td>
            <td class="listTableRow small" valign=top><input type="checkbox" class=small name="selected_id" value="{$template.templateid}"></td>
            <td class="listTableRow small" valign=top>{$template.filename}</a></td>
            <td class="listTableRow small" valign=top {if $template.status eq 0} style="color:#888;" {/if}>{$template.module}</a></td>
            <td class="listTableRow small" valign=top {if $template.status eq 0} style="color:#888;" {/if}>{$template.description}&nbsp;</td>
            {if $VERSION_TYPE neq 'deactivate'}<td class="listTableRow small" valign=top {if $template.status eq 0} style="color:#888;" {/if}>{$template.status_lbl}&nbsp;</td>
            <td class="listTableRow small" valign=top nowrap>{$template.edit}</td>{/if}
        </tr>
        {foreachelse}
       <tr>
            <td style="background-color:#efefef;height:340px" align="center" colspan="6">
                <div style="border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 45%; position: relative; z-index: 10000000;">
                    <table border="0" cellpadding="5" cellspacing="0" width="98%">
                    <tr><td rowspan="2" width="25%"><img src="{'empty.jpg'|@vtiger_imageurl:$THEME}" height="60" width="61"></td>
                        <td style="border-bottom: 1px solid rgb(204, 204, 204);" nowrap="nowrap" width="75%" align="left">
                            <span class="genHeaderSmall">{$APP.LBL_NO} {$MOD.LBL_TEMPLATE} {$APP.LBL_FOUND}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="small" align="left" nowrap="nowrap">{$APP.LBL_YOU_CAN_CREATE} {$APP.LBL_A} {$MOD.LBL_TEMPLATE} {$APP.LBL_NOW}. {$APP.LBL_CLICK_THE_LINK}:<br>
                            &nbsp;&nbsp;-<a href="index.php?module=PDFMaker&action=EditPDFTemplate&parenttab=Tools">{$APP.LBL_CREATE} {$APP.LBL_A} {$MOD.LBL_TEMPLATE}</a><br>
                        </td>
                    </tr>
                    </table>
                </div>
            </td>
        </tr>
        {/foreach}

        </table>
        </form>
    </td>
    </tr>
    <tr><td align="center" class="small" style="color: rgb(153, 153, 153);">{$MOD.PDF_MAKER} {$VERSION} {$MOD.COPYRIGHT}</td></tr>
    </table>

</td></tr></table>
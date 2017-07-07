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
<!-- BEGIN: main -->
<form name="selectall" method="POST">
     <input name='search_url' id="search_url" type='hidden' value='{$SEARCH_URL}'>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="small">
	<tr id="selectallTr"> {* crmv@21048m *}
	{assign var=colspan value=3}
	{if $SELECT eq 'enable'}
		{* <!-- ds@8 project tool --> *}
        {if $MODULE eq 'Projects'}
            <td style="padding-left:10px;" align="left">
            <input class="crmbutton small edit" type="button" value="{$APP.LBL_SELECT_BUTTON_LABEL} {$MODULE|@getTranslatedString:$MODULE}" onclick="select_mass(); set_return_tickets_mass(); closePopup();"/>{* crmv@21048m *}
            <input name="namelist" id="namelist" type="hidden" value="">
            </td>
		{* <!-- ds@8e--> *}
		{elseif $SELECT eq 'enable' && ($POPUPTYPE neq 'inventory_prod' && $POPUPTYPE neq 'inventory_prod_po' && $POPUPTYPE neq 'inventory_service')}
			<td style="padding-left:10px;" align="left"><input class="crmbutton small save" type="button" value="{$APP.LBL_SELECT_BUTTON_LABEL} {$MODULE|@getTranslatedString:$MODULE}" onclick="if(SelectAll('{$MODULE}','{$RETURN_MODULE}')) closePopup();"/></td>{* crmv@21048m *}
		{elseif $SELECT eq 'enable' && ($POPUPTYPE eq 'inventory_prod' || $POPUPTYPE eq 'inventory_prod_po')}
			{if $RECORD_ID}
			{assign var=colspan value=4}
				<td style="padding-left:10px;" align="left" width=10%><input class="crmbutton small save" type="button" value="{$APP.LBL_BACK}" onclick="window.history.back();"/></td>
			{/if}
			<td style="padding-left:10px;" align="left"><input class="crmbutton small save" type="button" value="{$APP.LBL_SELECT_BUTTON_LABEL} {$MODULE|@getTranslatedString:$MODULE}" onclick="if(InventorySelectAll('{$RETURN_MODULE}',image_pth))closePopup();"/></td>{* crmv@21048m *}
		{elseif $SELECT eq 'enable' && $POPUPTYPE eq 'inventory_service'}
			<td style="padding-left:10px;" align="left"><input class="crmbutton small save" type="button" value="{$APP.LBL_SELECT_BUTTON_LABEL} {$MODULE|@getTranslatedString:$MODULE}" onclick="if(InventorySelectAllServices('{$RETURN_MODULE}',image_pth))closePopup();"/></td>{* crmv@21048m *}
		{else}		
			<!-- <td>&nbsp;</td> --> <!-- crmv@98866 -->
		{/if}
	{else}
		<!-- <td>&nbsp;</td> --> <!-- crmv@98866 -->
	{/if}
<td id="rec_string" style="padding-left:10px;" align="left">{$RECORD_COUNTS}</td>
{* crmv@98866 *}
<td id="filters" style="padding-right:10px;" align="right">
	<table border=0 cellspacing=0 cellpadding=0 class="small">
		<tr>
			<td style="padding-right:5px">
				{if $MODULE neq 'Calendar'}                      
					{$APP.LBL_HOME_COUNT}:&nbsp;
				{/if}                    
			</td>
			<td>
				{if $MODULE neq 'Calendar'}
					<div class="dvtCellInfo">
						<SELECT NAME="counts" id="counts" class="small detailedViewTextBox" onchange="showMoreEntries_popup(this,'{$MODULE}')">
						{$CUSTOMCOUNTS_OPTION}
						</SELECT>
					</div>
				{/if}
			</td>	
			<td>{$APP.LBL_VIEW}</td>
			<td style="padding-left:5px;padding-right:5px">
				<div class="dvtCellInfo">
					<SELECT NAME="viewname" id="viewname" class="small detailedViewTextBox" onchange="showDefaultCustomView_popup(this,'{$MODULE}','{$CATEGORY}')">{$CUSTOMVIEW_OPTION}</SELECT></td>
				</div>
			</td>
		</tr>
	</table> 
</td>
{* crmv@98866 end *}
</tr>	
   	<tr>
	    <td style="padding:10px;" colspan={$colspan}>

       	<input name="module" type="hidden" value="{$RETURN_MODULE}">
		<input name="action" type="hidden" value="{$RETURN_ACTION}">
        <input name="pmodule" type="hidden" value="{$MODULE}">
		<input type="hidden" name="curr_row" value="{$CURR_ROW}">	
		<input name="entityid" type="hidden" value="">
		<input name="popuptype" id="popup_type" type="hidden" value="{$POPUPTYPE}">
<!-- //crmv@9183  -->     
     <input name="selected_ids" type="hidden" id="selected_ids" value="{$SELECTED_IDS}">
     <input name="all_ids" type="hidden" id="all_ids" value="{$ALL_IDS}">
<!-- //crmv@9183 e -->  
		<input name="idlist" type="hidden" value="">
		<div id="list" style="overflow:auto;height:348px;">
		<table class="table table-striped table-hover small" border="0" cellpadding="5" cellspacing="1" width="100%">
		
		<thead>
		<tr>
			{if $SELECT eq 'enable'}
             <!-- DS-ED VlMe 27.3.2008 --> 
            <td class="lvtCol"><input type="checkbox" id="selectall" name="selectall" onClick="select_all_page(this.checked,this.form);"></td> <!-- //ds@1s -->
             <!-- DS-END --> 
      {/if}
		    {foreach item=header from=$LISTHEADER}
		        <td class="lvtCol">{$header}</td>
		    {/foreach}
			{if $SELECT eq 'enable' && ($POPUPTYPE eq 'inventory_prod' || $POPUPTYPE eq 'inventory_prod_po')}
				{if !$RECORD_ID}
					<td class="lvtCol">{$APP.LBL_ACTION}</td>
				{/if}
			{/if}
		</tr>
		</thead>
		
		<tbody>
		{foreach key=entity_id item=entity from=$LISTENTITY}
				<!-- crmv@7230 -->
				{assign var=color value=$entity.clv_color}
	        	<tr>
				   {if $SELECT eq 'enable'}
				      {* <!-- ds@8 project tool --> *}
		              {if $MODULE eq 'Projects'}
		                  <td><input type='checkbox' name='selected_id' value='{$entity_id}' {if $PROJECT_CHECK[$entity_id] eq '1'} checked {/if}><input type='hidden' name='project_name' id='project_name' value='{$PROJECT_NAME_ARR[$entity_id]}'></td>
					  {else}
						 {* <!-- KoKr bugfix add (check_object) idlist for csv export --> *}
							<td width="2%"><input type="checkbox" name="selected_id" id="{$entity_id}" value="{$entity_id}" onClick="update_selected_ids(this.checked,'{$entity_id}',this.form);" {if $SELECTED_IDS neq "" && in_array($entity_id,$SELECTED_IDS_ARRAY)} checked {/if} ></td>
						 <!-- DS-END --> 
				      {/if}
				      {* <!-- ds@8e --> *}
				   {/if}
                   {foreach key=colname item=data from=$entity}
						{if $colname neq 'clv_color' or $colname eq '0'}
								<td bgcolor="{$color}">{$data}</td>
						{/if}		
                   {/foreach}
				</tr>
				{* crmv@98866 *}
				{foreachelse}
                        <tr><td colspan="{$HEADERCOUNT}" style="padding:1px">
                        <div style="background-color: rgb(255, 255, 255); width: 100%;position: relative; z-index: 89998;padding:20px;">	<!-- crmv@18170 -->
                        <table border="0" cellpadding="5" cellspacing="0" width="98%" class="table-fixed">
                                <tr>
                                        <td rowspan="2" align="right"><i class="vteicon" style="font-size:40px">error_outline</i><!-- <img src="{'empty.jpg'|@vtiger_imageurl:$THEME}"> --></td>
                                        {if $recid_var_value neq '' && $mod_var_value neq '' && $RECORD_COUNTS eq 0 }
											<script>redirectWhenNoRelatedRecordsFound();</script>
                                      		<td align="left" nowrap="nowrap"><span class="genHeaderSmall">{$APP.LBL_NO_M} {$APP.LBL_RECORDS} {$APP.RELATED} !</td>
                                        {else}
                                	       	 <td align="left" nowrap="nowrap"><span class="genHeaderSmall">{$APP.LBL_NO_M} {$APP.LBL_RECORDS} {$APP.LBL_FOUND} !</td>
                                        {/if}
                                </tr>
                        </table>
                        </div>
                        </td></tr>
                {/foreach}
                {* crmv@98866 end *}
	      	</tbody>
	    	</table>
			<div>
	    </td>
	</tr>

</table>
<table width="100%" align="center" class="reportCreateBottom" style="background-color:#FFFFFF">
<tr>
<td id="nav_buttons" align="center" style="width:100%;">{$NAVIGATION}</td>
</tr>
</table>
</form>

 <!-- ds@8 project tool --> 
<script>

{* crmv@21048m *}
{literal}
	function setListHeight() {
		var minus;
		if (jQuery && jQuery.browser && jQuery.browser.msie) { // crmv@98866
			minus = 10;
		}
		else {
			minus = 20;
		}
		var heightRet = jQuery("#ListViewContents").outerHeight() - minus - jQuery('#selectallTr').outerHeight() - jQuery('.reportCreateBottom').outerHeight();// crmv@20172
		jQuery("#list").height(heightRet);
	}
	
	//crmv@112052
	jQuery(window).load(function() {
		setTimeout(function() {
			jQuery("#ListViewContents").height(jQuery(window).height() - jQuery('#searchTable').outerHeight() - jQuery('#create').outerHeight() - jQuery('#moduleTable').outerHeight());// crmv@20172 
			setListHeight();
			loadedPopup();
		}, 10);
	});
	// crmv@112052e
{/literal}
{* crmv@21048m e *}

  function set_return_worker(workerid, workername, inputString, inputString2)

  {ldelim}

     target = parent.document.getElementById(inputString);{* crmv@21048m *}

     target.value = workername;

     target2 = parent.document.getElementById(inputString2);{* crmv@21048m *}

     target2.value = workerid;

  {rdelim}
  function controlSelectIds()
  {ldelim}
      var select_options  =  document.getElementsByName('selected_id');
        var x = select_options.length;
        var viewid =getviewId();
        idstring = "";
      xx = 0;
      for(i = 0; i < x ; i++)
      {ldelim}
          if(select_options[i].checked)
          {ldelim}
              xx++
          {rdelim}
      {rdelim}

      if (xx == 0)
      {ldelim}
          alert(alert_arr.SELECT);
          return false;
      {rdelim}
      else
      {ldelim}
          return true;
      {rdelim}
  {rdelim}

  function massUpdateColumns()
  {ldelim}

     var button_top = document.getElementById("select_all_button_top");
     var choose_id = document.getElementById("select_ids");

     if (button_top.value == "{$APP.LBL_SELECT_ALL_IDS}")
     {ldelim}

          var select_options  =  document.getElementsByName('selected_id');
              var x = select_options.length;
              var viewid =getviewId();
              idstring = "";
          xx = 0;
          for(i = 0; i < x ; i++)
          {ldelim}
              if(select_options[i].checked)
              {ldelim}
                  idstring = select_options[i].value +"_"+idstring
                  xx++
              {rdelim}
          {rdelim}

          if (xx != 0)
          {ldelim}
              choose_id.value=idstring;
          {rdelim}
          else
          {ldelim}
              alert(alert_arr.SELECT);
              return false;
          {rdelim}
     {rdelim}

     question = confirm("{$APP.LBL_MASS_UPDATE_QUESTION}");


     if(question)
        massupdateSelectIds("{$MODULE}");


  {rdelim}

  function unselectAllIds()
  {ldelim}
     var button_top = document.getElementById("select_all_button_top");

     button_top.value = "{$APP.LBL_SELECT_ALL_IDS}";
  {rdelim}


  function selectAllIds()
  {ldelim}
     var button_top = document.getElementById("select_all_button_top");
     var choose_id = document.getElementById("select_ids");

     if (button_top.value == "{$APP.LBL_SELECT_ALL_IDS}")
     {ldelim}

        button_top.value = "{$APP.LBL_UNSELECT_ALL_IDS}";
        choose_id.value = document.getElementById("all_ids").value
        //crmv@7216
  		document.getElementById("selected_ids").value=choose_id.value;
  	  //crmv@7216e
        document.getElementById("selectall").checked=true;

    	if (isdefined("selected_id")){ldelim}
  	      if (typeof(getObj("selected_id").length)=="undefined")
  	      {ldelim}
  	             getObj("selected_id").checked=true;
  	          {rdelim} else {ldelim}
  	         for (var i=0;i<getObj("selected_id").length;i++){ldelim}
  	                    getObj("selected_id")[i].checked=true;
  	         {rdelim}           
  	      {rdelim}
    	{rdelim}	

     {rdelim} else {ldelim}
        button_top.value = "{$APP.LBL_SELECT_ALL_IDS}";
        choose_id.value = "";
        //crmv@7216
  		document.getElementById("selected_ids").value="";
  		//crmv@7216e
        document.getElementById("selectall").checked=false;

        if (typeof(getObj("selected_id").length)=="undefined")
        {ldelim}
           getObj("selected_id").checked=false;
            {rdelim} else {ldelim}
           for (var i=0;i<getObj("selected_id").length;i++){ldelim}
                      getObj("selected_id")[i].checked=false;
           {rdelim}           
              {rdelim}
     {rdelim}
  {rdelim}

  function changeMassUpdateValue(value)
  {ldelim}

  document.getElementById("mass_update_value").value = value;
  {rdelim}

  function changeCheckboxValue(checked)
  {ldelim}
     if (checked == true)
     {ldelim}
        changeMassUpdateValue("1");
     {rdelim} else {ldelim}
        changeMassUpdateValue("0");
     {rdelim}
  {rdelim}  
//crmv navigation values ajax loaded
update_navigation_values(window.location.href);

{* crmv@107661 - update field list *}
{if $smarty.request.ajax == "true" && $smarty.request.changecustomview == "true"}
{assign var=selectcont value=""}
{foreach key=fieldval item=fieldlabel from=$SEARCHLISTHEADER}
	{assign var=selectcont value=$selectcont|cat:"<option value='`$fieldval`'>`$fieldlabel`</option>"}
{/foreach}
jQuery('select[name=search_field]').html('{$selectcont|replace:"'":"\'"}');
{/if}
{* crmv@107661 *}

</script>
 <!-- ds@8e --> 
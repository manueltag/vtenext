<link rel="stylesheet" type="text/css" href="themes/{php}echo $_SESSION['vtiger_authenticated_user_theme'];{/php}/style.css">
<script language="JavaScript" type="text/javascript" src="modules/{php}echo $_SESSION['import_modulename'];{/php}/{php}echo $_SESSION['import_modulename'];{/php}.js"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/general.js"|resourcever}"></script>
<script language="JavaScript" type="text/javascript" src="include/js/{php} echo $_SESSION['authenticated_user_language'];{/php}.lang.js?{php} echo $_SESSION['vtiger_version'];{/php}"></script>

<script language="javascript" type="text/javascript" src="include/scriptaculous/prototype.js"></script>

<body class="small" marginwidth=0 marginheight=0 leftmargin=0 topmargin=0 bottommargin=0 rightmargin=0>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="mailClient mailClientBg">
	<tr>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td class="moduleName" width="80%" style="padding-left:10px;">{$APP[$DUPLICITY]}</td>
					<td  width=30% nowrap class="componentName" align=right>{$APP.VTIGER}</td>
				</tr>
			</table>
			<div id="ListViewContents">
        {if $MODE eq 'choose_columns'}
          <form name="choose_form" method="post" action="index.php?{$QUERYSTRING}">
        {/if}
          <table width="100%" border="0" cellspacing="0" cellpadding="0" class="small">
          	<tr>
          	{if $SELECT eq 'enable'}
          		<td style="padding-left:10px;" align="left"><input class="crmbutton small save" type="button" value="{$APP.LBL_SELECT_BUTTON_LABEL} {$APP[$MODULE]}" onclick="if(SelectAll('{$MODULE}','{$RETURN_MODULE}')) window.close();"/></td>
          	{else}		
          		<td>&nbsp;</td>	
          	{/if}
          	<td style="padding-right:10px;" align="right">{$RECORD_COUNTS}</td></tr>
             	<tr>
          	    <td style="padding:10px;" colspan=2>
          
          		  <div style="overflow:auto;">
                		<table style="background-color: rgb(204, 204, 204);" class="small" border="0" cellpadding="5" cellspacing="1" width="100%">
                		<tbody>
                		<tr>
                			{if $MODE eq 'choose_columns'}
                		    <td class="lvtCol" width="3%"><input type="checkbox" name="change_stat" onclick="change_status();"></td>
                			{else}
                        <td class="lvtCol" width="3%">#</td>
                      {/if}
                       {foreach item=header from=$LISTHEADER}
                      	  <td class="lvtCol">{$header}</td>
                       {/foreach}
                		</tr>
                		{foreach key=entity_id item=entity from=$LISTENTITY}
                  	  <tr bgcolor=white onMouseOver="this.className='lvtColDataHover'" onMouseOut="this.className='lvtColData'">
                  			{if $MODE neq 'choose_columns'}
                          {if $MODE eq 'duplicity'}
                            <form name="doit{$IDS[$entity_id]}" method="POST" action="index.php?{$QUERYSTRING}" style="margin:0px;">
                  	   		{/if}
                           <td>{$entity_id}</td>
                		    {/if}
                        {foreach item=data from=$entity}
                		        <td>{$data}</td>
                        {/foreach}
                          
                        {if $MODE eq 'duplicity'}
                          <td>
                            <select name="action_selectbox" class="small">
                              {if $DUPLICITY eq 'ConvertLead'}
                                {if $NO_ACCOUNT eq '1'}
                                  <option value="5">{$MOD.Convert_contact}</option>
                                {else}
                                  <option value="1">{$MOD.Convert_both}</option>
                                {/if}
                                {if $CE neq '1' && $AE eq '1' && $EMPTY_ACCOUNT neq '1'}
                                  <option value="2">{$MOD.contact_to_account}</option>
                                {elseif $CE eq '1' && $AE eq '1'}
                                  {if $REAL_AE eq '0' && $EMPTY_ACCOUNT neq '1'}
                                    <option value="4">{$MOD.account_to_contact}</option>
                                  {elseif $EMPTY_ACCOUNT neq '1'}
                                    <option value="2">{$MOD.contact_to_account}</option>
                                  {/if}
                                  {if $no_new_account neq '1' && $EMPTY_ACCOUNT neq '1'}
                                    <option value="3">{$MOD.create_account}</option>
                                  {/if}
                                {elseif $CE eq '1' && $AE neq '1' && $NO_ACCOUNT neq '1'}
                                  <option value="4">{$MOD.account_to_contact}</option>
                                {/if}
                                <option value="0">{$MOD.back_to_record}</option>
                              {elseif $DUPLICITY eq 'Contacts'}
                                <option value="1">{$APP.Dont_import}</option>
                                <option value="0">{$APP.Import_contact_as_new}</option>
                                <option value="2">{$APP.Update_person}</option>
                                {if $NO_IMPORT_ACCOUNT[$entity_id] eq '0'}
                                  <option value="3">{$APP.New_contact_old_account}</option>
                                  <option value="4">{$APP.Update_contact_update_account}</option>
                                  {* <!-- DS-CR MaJu 13.3.2008 --> *}
                                  <option value="5">{$MOD.connect_account_to_contact}</option>
                                  {* <!-- DS-END --> *}
                                {/if}
                              {else}
                                <option value="1">{$APP.Dont_import}</option>
                                {if $DUPLICITY eq 'Accounts'}
                                  <option value="0">{$APP.Import_account_as_new}</option>
                                  <option value="2">{$MOD.update_account}</option>
                                {elseif $DUPLICITY eq 'Leads'}
                                  <option value="0">{$APP.Import_lead_as_new}</option>
                                  <option value="2">{$MOD.update_lead}</option>
                                {/if}
                              {/if}
                            </select>
                            {if $DUPLICITY eq 'ConvertLead'}
                              <input name="selected_contact_id" type="hidden" value="">
                              <input name="selected_account_id" type="hidden" value="{$selected_account_id}">
                            {else}
                              <input name="id" type="hidden" value="{$IDS[$entity_id]}">
                              <input name="selected_{$IDS[$entity_id]}" type="hidden" value="{$HIDDEN_ID}">
                            {/if}
                            {if $DUPLICITY eq 'Contacts'}
                              <input name="selected_account_{$IDS[$entity_id]}" type="hidden" value="{$HIDDEN_ACCOUNT_ID}">
                            {/if}
                            &nbsp;
                            {if $DUPLICITY eq 'Contacts'}
                              <input type="button" value="{$APP.Run_it}" class="crmbutton small save" onclick="return check_form(this.form, this.form.action_selectbox.value, {$IDS[$entity_id]}, this.form.selected_{$IDS[$entity_id]}.value, this.form.selected_account_{$IDS[$entity_id]}.value);">
                            {elseif $DUPLICITY eq 'ConvertLead'}
                              <input type="button" value="{$APP.Run_it}" class="crmbutton small save" onclick="return check_form(this.form, this.form.action_selectbox.value, 1, this.form.selected_contact_id.value, this.form.selected_account_id.value);">
                            {else}
                              <input type="button" value="{$APP.Run_it}" class="crmbutton small save" onclick="return check_form(this.form, this.form.action_selectbox.value, {$IDS[$entity_id]}, this.form.selected_{$IDS[$entity_id]}.value, '');">
                            {/if}
                          </td>
                          </form>
                        {/if}
                  		</tr>
                    {/foreach}
                	  </tbody>
                	  </table>
        		   	<div>
          	    </td>
          	</tr>
            
            <script>
              function check_form(oform, action_nr, this_id, selected_id, selected_account)
              {ldelim}
                {if $DUPLICITY eq 'Contacts'}
                  if((action_nr==2 && selected_id=='') || ((action_nr==4 || action_nr==5) && (selected_id=='' || selected_account=='')) || (action_nr==3 && selected_account==''))
                  {ldelim}
                    alert('{$MOD.choose_duplicate}');
                    return false;
                  {rdelim}
                  else if(action_nr==2)
                  {ldelim}
                    link="index.php?module=Import&action=PopupDuplicateColumns&duplicity=Contacts&id=" + this_id + "&original=" + selected_id;
                    window.open(link,"test1","width=640,height=800,resizable=1,scrollbars=1");
                    return false;
                  {rdelim}
                  else if(action_nr==4)
                  {ldelim}
                    link="index.php?module=Import&action=PopupDuplicateColumns&duplicity=Contacts&id=" + this_id + "&original=" + selected_id + "&account=" + selected_account;
                    window.open(link,"test1","width=640,height=800,resizable=1,scrollbars=1");
                    return false;
                  {rdelim}
                  else
                    oform.submit();
                {elseif $DUPLICITY eq 'Accounts' || $DUPLICITY eq 'Leads'}
                  if(action_nr==2 && selected_id=='')
                  {ldelim}
                    alert('{$MOD.choose_duplicate}');
                    return false;
                  {rdelim}
                  else if(action_nr==2)
                  {ldelim}
                    link="index.php?module=Import&action=PopupDuplicateColumns&duplicity={$DUPLICITY}&id=" + this_id + "&original=" + selected_id;
                    window.open(link,"test1","width=640,height=800,resizable=1,scrollbars=1");
                    return false;
                  {rdelim}
                  else
                    oform.submit();
                {elseif $DUPLICITY eq 'ConvertLead'}
                  if((action_nr==2 && selected_account=='') || (action_nr==4 && selected_id==''))
                  {ldelim}
                    alert('{$MOD.choose_duplicate}');
                    return false;
                  {rdelim}
                  else
                    oform.submit();
                {else}
                  oform.submit();
                {/if}
              {rdelim}
            </script>
            
          </table>
        {if $MODE eq 'choose_columns'}
          <input type="hidden" name="id" value="{$ID}">
          &nbsp;
          <input type="hidden" name="original" value="{$ORIGINAL}">
          &nbsp;
          <input type="submit" value="{$APP.LBL_UPDATE_BUTTON}" class="crmbutton small save">
          &nbsp;
          <input type="button" onclick="window.close();" value="{$APP.LBL_BACK}" class="crmbutton small save">
          </form>
        {/if}   
			</div>
		</td>
	</tr>
              
</table>
{if $MODE eq 'choose_columns'}
<script>
function change_status()
{ldelim}
  var length = document.choose_form.elements.length;
  for (var i=0; i<length; i++)
    if (document.choose_form.elements[i] && (document.choose_form.elements[i].type == 'checkbox'))
      document.choose_form.elements[i].checked = document.choose_form.change_stat.checked;

{rdelim}
</script>
{/if}
</body>

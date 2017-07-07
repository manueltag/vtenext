<br/>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>   		
   		<td class="showPanelBg" valign="top" width="100%">
   			<table  cellpadding="0" cellspacing="0" width="100%" border=0>
  				<tr>
				<td width="50%" valign=top>
					<form name="install"  method="POST" action="index.php">
						<input type="hidden" name="module" value="PDFMaker"/>
						<input type="hidden" name="action" value="update"/>
						<table align="center" cellpadding="15" cellspacing="0" width="85%" class="mailClient importLeadUI small" border="0">
							<tr>
								<td colspan="2" valign="middle" align="left" class="mailClientBg genHeaderSmall">{$MOD.LBL_MODULE_NAME} {$MOD.LBL_UPDATE} {if $STEP neq 'error'}>> {$STEPNAME} >> {$CURRENT_STEP}/<span id="total_steps">{$TOTAL_STEPS}</span>{/if}</td>
								<br/>
							</tr>
							{if $STEP eq "0"}
							<input type="hidden" name="step" value="1" />
							<tr>
    							<td border="0" cellpadding="5" cellspacing="0" width="70%">
    							<table width="100%">
    							     <tr>
                                       <td align="left" valign="top" style="padding-left:40px;">
                                       <input type="radio" name="installtype" id="installexpress" value="express" checked="checked" onclick="changeInstallType(this.value);"/>
                                       <span class="genHeaderSmall">{$MOD.LBL_EXPRESS} {$MOD.LBL_UPDATE}</span>
  									   </td>
     								 </tr>
     								 <tr>
     								   <td align="left" valign="top" class="small" style="padding-left:65px;">
     								   {$MOD.LBL_EXPRESS_DESC} 
                                       <div id="list_permissions">{$LIST_PERMISSIONS}</div>
                                       {if $P_ERRORS neq '0'}<input type="button" id="btn_control_permissions" value="{$MOD.LBL_TRY_AGAIN}" onClick="controlPermissions();" class="crmbutton small create">{/if}
                                       </td>  
     								 </tr>
     								 <tr>
                                       <td align="left" valign="top" style="padding-left:40px;padding-top:20px;">
                                       <input type="radio" name="installtype" value="custom" onclick="changeInstallType(this.value);"/>                                        
                                       <span class="genHeaderSmall">{$MOD.LBL_CUSTOM} {$MOD.LBL_UPDATE}</span>
  									   </td>
     								 </tr>
     								 <tr>
     								 <td align="left" valign="top" class="small" style="padding-left:65px;">
     								   {$MOD.LBL_CUSTOM_DESC}
                                     </td>
                                     </tr>
    							</table>    							
    						    </td>
    						    <td border="0" cellpadding="5" cellspacing="0" width="50%">
    							&nbsp;
    							</td>
 							</tr> 							
 							{elseif $STEP eq '1'}
 							<input type="hidden" name="step" value="2" />
 							<tr>
    							<td border="0" cellpadding="5" cellspacing="0" width="50%">    							
    							<table width="100%">
    							     <tr>
                                       <td align="left" valign="top" style="padding-left:40px;">                                                                               
                                       <span class="genHeaderSmall">{$MOD.LBL_UPDATE_INSTRUCTIONS}</span>
  									   </td>
     								 </tr>
     								 <tr>
     								   <td align="left" valign="top" class="small" style="padding-left:65px;">   								    
                                        {* CUSTOM UPDATE TO 1.26 *}
                                        {if $TO126 eq "true"}
                                        <p>
                                        {$MOD.LBL_FILE}: <strong>/index.php</strong><br/>
     								    {$MOD.LBL_AROUND_LINE} 680 {$MOD.LBL_BEFORE}     								    
<pre>{literal}if(isset($_REQUEST['record']) && $_REQUEST['record'] != ''){/literal}</pre>
                                        {$MOD.LBL_ADD_FOLLOWING}
<pre>{literal}if($now_action == 'EditPDFTemplate')
    $now_action = 'EditView';
if($now_action == 'DetailViewPDFTemplate')
    $now_action = 'DetailView';
if($now_action == 'SavePDFTemplate')
    $now_action = 'Save';
if($now_action == 'DeletePDFTemplate')
    $now_action = 'Delete';{/literal}</pre>
                                        </p> 
                                             								    
     								    <p style="border-top:3px dotted grey">&nbsp;</p>
                                              								    
     								    <p>
     								    {$MOD.LBL_FILE}: <strong>/Smarty_setup.php</strong><br/>
  							            {$MOD.LBL_AROUND_LINE} 54 {$MOD.LBL_AFTER}
<pre>{literal}$current_language{/literal}</pre>
  							            {$MOD.LBL_ADD_FOLLOWING}
<pre>{literal},$current_user{/literal}</pre>
     								    </p>
     								    <p>                         
  							           {$MOD.LBL_AROUND_LINE} 60 {$MOD.LBL_AFTER}
<pre>{literal}$this->assign('CURRENT_LANGUAGE',$current_language);{/literal}</pre>
  							           {$MOD.LBL_ADD_FOLLOWING} 
<pre>{literal}require('user_privileges/user_privileges_'.$current_user->id.'.php');
if($is_admin == true || $profileGlobalPermission[2]==0 || $profileGlobalPermission[1]==0 || $profileTabsPermission[getTabId("PDFMaker")]==0)
  $this->assign("ENABLE_PDFMAKER",'true');{/literal}</pre>                                                   
                                        </p>
                                        
                                        <p style="border-top:3px dotted grey">&nbsp;</p>     								    
     								    
                                         <p>
     								    {$MOD.LBL_FILE}: <strong>/modules/Emails/EditView.php</strong><br/>
  							            {$MOD.LBL_AROUND_LINE} 242 {$MOD.LBL_REPLACE}
<pre>{literal}require_once("modules/PDFMaker/InventoryPDF.php");  
  
$tempmodule = getSalesEntityType($_REQUEST['pid']); 
$tempFocus = CRMEntity::getInstance($tempmodule);
$tempFocus->id = $_REQUEST['pid'];
$tempFocus->retrieve_entity_info($_REQUEST['pid'],$tempmodule);
$result=$adb->query("SELECT fieldname FROM vtiger_field WHERE uitype=4 AND tabid=".getTabId($tempmodule));
$fieldname=$adb->query_result($result,0,"fieldname");
if(isset($tempFocus->column_fields[$fieldname]) && $tempFocus->column_fields[$fieldname]!="")
  $name=generate_cool_uri($tempFocus->column_fields[$fieldname]);
else
  $name=$_REQUEST["commontemplateid"].$_REQUEST["pid"].date("ymdHi");
$smarty->assign("COMMON_TEMPLATE_NAME",$name);
$smarty->assign("COMMON_TEMPLATE_ID",$_REQUEST['commontemplateid']);{/literal}</pre>
  							            {$MOD.LBL_WITH_FOLLOWING}
<pre>{literal}$mod_lang=return_specified_module_language($current_language,"PDFMaker");
$smarty->assign("COMMON_TEMPLATE_NAME",$mod_lang["PDFMAKER_ATTACHMENT"]);
$smarty->assign("COMMON_TEMPLATE_ID",$_REQUEST['commontemplateid']);
{/literal}</pre>
     								    </p>
                                        {/if}
                                        {* CUSTOM UPDATE TO 1.27 *}
                                        {if $TO127 eq "true"}
                                        <p>
                                        {$MOD.LBL_FILE}: <strong>/index.php</strong><br/>
     								    {$MOD.LBL_AROUND_LINE} 132 {$MOD.LBL_REPLACE}     								    
<pre>{literal}if(isset($_REQUEST['action']) && $_REQUEST['action']=='DetailView'){{/literal}</pre>
                                        {$MOD.LBL_WITH_FOLLOWING}
<pre>{literal}if(isset($_REQUEST['action']) 
    && ($_REQUEST["action"]=="DetailView" || $_REQUEST['action']=='index' || $_REQUEST['action']=='ListView' 
        || (substr($_REQUEST['action'], -4)=='Ajax' && isset($_REQUEST['file']) 
            && ($_REQUEST["file"]=="index" || $_REQUEST["file"]=="ListView")
           )
       )
  ){{/literal}</pre>                    
                                        </p>
                                                                      								    
     								    <p style="border-top:3px dotted grey">&nbsp;</p>
     								    
     								    <p>
     								    {$MOD.LBL_FILE}: <strong>/Smarty/templates/ListViewEntries.tpl</strong><br/>
  							            {$MOD.LBL_AROUND_LINE} 70 {$MOD.LBL_BEFORE}
<pre>{literal}{if $CUSTOM_LINKS && !empty($CUSTOM_LINKS.LISTVIEW)}{/literal}</pre>
  							            {$MOD.LBL_ADD_FOLLOWING}
<pre>{literal}{if $ENABLE_PDFMAKER eq 'true'}
    &lt;input class="crmbutton small edit" type="button" value="{$PDFMAKER_MOD.LBL_BATCH_PRINT}" onclick="getPDFListViewPopup(this);"/&gt;
{/if}{/literal}</pre>
     								    </p>
     								    <p>                         
  							           {$MOD.LBL_AROUND_LINE} 300 {$MOD.LBL_BEFORE}
<pre>{literal}{if $CUSTOM_LINKS && !empty($CUSTOM_LINKS.LISTVIEW)}{/literal}</pre>
  							           {$MOD.LBL_ADD_FOLLOWING} 
<pre>{literal}{include file="modules/PDFMaker/ListViewPdfActions.tpl"}{/literal}</pre>                                                   
                                        </p>
                                        
                                        <p style="border-top:3px dotted grey">&nbsp;</p>
     								    
     								    <p>
     								    {$MOD.LBL_FILE}: <strong>/Smarty/templates/Inventory/InventoryDetailView.tpl</strong><br/>
  							            {$MOD.LBL_AROUND_LINE} 338 {$MOD.LBL_BEFORE}
<pre>{literal}&lt;!-- To display the Tag Clouds --&gt;{/literal}</pre>
  							            {$MOD.LBL_ADD_FOLLOWING}
<pre>{literal}{if $MODULE eq 'PriceBooks'}
    {include file="modules/PDFMaker/InventoryPdfActions.tpl"}
{/if}{/literal}</pre>
     								    </p>
     								    
                                        {/if}
                                        
                                        {* CUSTOM UPDATE TO 1.28 *}
                                        {if $TO128 eq "true"}
                                        <p>
                                        {$MOD.LBL_FILE}: <strong>/index.php</strong><br/>
     								    {$MOD.LBL_AROUND_LINE} 132 {$MOD.LBL_REPLACE}     								    
<pre>{literal}$temp_sql = "SELECT templateid, filename AS templatename
FROM vtiger_pdfmaker
WHERE module = '".$_REQUEST['module']."'";
    
$temp_result = $adb->query($temp_sql);
if($adb->num_rows($temp_result)>0)
  $no_templates_exist = 0;
else 
  $no_templates_exist = 1;

while($temp_row = $adb->fetchByAssoc($temp_result)){
  $use_template[$temp_row['templateid']] = $temp_row['templatename'];
}{/literal}</pre>
                                        {$MOD.LBL_WITH_FOLLOWING}
<pre>{literal}$userid=0;
if(isset($_SESSION["authenticated_user_id"]))
  $userid = $_SESSION["authenticated_user_id"];

$status_sql="SELECT * FROM vtiger_pdfmaker_userstatus  
             INNER JOIN vtiger_pdfmaker USING(templateid) 
             WHERE userid=? AND module=?"; 
$status_res=$adb->pquery($status_sql,array($userid,$_REQUEST['module']));
$status_arr = array();
if($adb->num_rows($status_res)>0)
{
  while($status_row = $adb->fetchByAssoc($status_res))
  {
    $status_arr[$status_row["templateid"]]["is_active"] = $status_row["is_active"];
    $status_arr[$status_row["templateid"]]["is_default"] = $status_row["is_default"]; 
  }
}
$temp_sql = "SELECT templateid, filename AS templatename
             FROM vtiger_pdfmaker                
             WHERE module = '".$_REQUEST['module']."' ORDER BY filename";
    
$temp_result = $adb->query($temp_sql);

$status_template=array();
$set_default=false;
while($temp_row = $adb->fetchByAssoc($temp_result)){
  if(isset($status_arr[$temp_row['templateid']]))
  {
    if($status_arr[$temp_row['templateid']]["is_active"]=="0")
      continue;
    elseif($status_arr[$temp_row['templateid']]["is_default"]=="1")
    {
      $status_template[$temp_row['templateid']] = $temp_row['templatename'];         
    }
    else
      $use_template[$temp_row['templateid']] = $temp_row['templatename'];
  }
  else
    $use_template[$temp_row['templateid']] = $temp_row['templatename'];    
}

if(count($status_template)>0)
  $use_template = $status_template + $use_template;  

if(count($use_template)>0)
  $no_templates_exist = 0;
else 
  $no_templates_exist = 1;{/literal}</pre>                    
                                        </p> 
                                        {/if}
                                        
                                       </td>  
     								 </tr>     								 
    							</table> 						
    						    </td>
    						    <td border="0" cellpadding="5" cellspacing="0" width="50%">
    							&nbsp;
    							</td>
 							</tr>
 							{elseif $STEP eq "2"}
							<input type="hidden" name="step" value="3" />
							<tr>
    							<td border="0" cellpadding="5" cellspacing="0" width="70%">
    							<table width="100%">
    							     <tr>
                                       <td align="left" valign="top" style="padding-left:40px;">                                                                               
                                       <span class="genHeaderSmall">{$MOD.LBL_FINAL_INSTRUCTIONS}</span>
  									   </td>
     								 </tr>
     								 <tr>
     								   <td align="left" valign="top" class="small" style="padding-left:40px;">   								    
                                        {if $TO126 eq "true"}
                                            {$MOD.LBL_RECALCULATE_RIGHTS}
                                        {else}
                                            {$MOD.LBL_UPDATE_SUCCESS}
                                        {/if}                                
                                       </td>
                                     </tr>
    							</table>    							
    						    </td>
    						    <td border="0" cellpadding="5" cellspacing="0" width="50%">
    							&nbsp;
    							</td>
 							</tr>
 							{elseif $STEP eq 'error'}
 							<tr>
    							<td border="0" cellpadding="5" cellspacing="0" width="50%">
    							<table width="100%">
    							     <tr>
                                       <td align="left" valign="top" style="padding-left:40px;">                                       
                                       <span class="genHeaderSmall">{$MOD.LBL_UPDATE_ERROR}</span>
  									   </td>
     								 </tr>
                                     <tr>
     								   <td align="left" valign="top" class="small" style="padding-left:40px;">     								    
                                        {$MOD.LBL_ERROR_TBL}:<br/>                                        
     								   {foreach item=tbl from=$ERROR_TBL}
     								   <pre>{$tbl}</pre><br />
     								   {/foreach}
                                       </td>  
     								 </tr>     								 
    							</table>    							
    						    </td>
    						    <td border="0" cellpadding="5" cellspacing="0" width="50%">
    							&nbsp;
    							</td>
 							</tr>
 							{/if}
 							<tr>
								<td align="center" colspan="2" border=0 cellspacing=0 cellpadding=5 width=98% class="layerPopupTransport">	
									{if $STEP eq '0'}
                                        <input type="submit" id="next_button" value="{$MOD.LBL_NEXT}" class="crmbutton small create" {if $P_ERRORS neq '0'}style="display:none;"{/if} />&nbsp;&nbsp;
              						{elseif $STEP eq '1'}
                                        <input type="submit" value="{$MOD.LBL_NEXT}" class="crmbutton small create"/>
                                        &nbsp;&nbsp;
                                        <input type="button" name="{$APP.LBL_BACK}" value="{$APP.LBL_BACK}" class="crmbutton small cancel" onclick="window.history.back()" />                                        
                                    {elseif $STEP eq '2'}
                                        <input type="submit" value="{$MOD.LBL_FINISH}" class="crmbutton small create"/>
                                        &nbsp;&nbsp;
                                        <input type="button" name="{$APP.LBL_BACK}" value="{$APP.LBL_BACK}" class="crmbutton small cancel" onclick="window.history.back()" />                                        
                                    {elseif $STEP eq "error"}
              						    <input type="button" id="refresh_button" value="{$MOD.LBL_RELOAD}" class="crmbutton small create" onclick="window.location.reload();"/>&nbsp;&nbsp;
                                    {/if}
								</td>
							</tr>
 						</table>
 					</form>
 				</td>
 				</tr>
 			</table>
 		</td>
 	</tr>
</table>

<script>
function changeInstallType(type)
{ldelim} 
   document.getElementById('next_button').disabled = false;
   document.getElementById('next_button').style.display = "inline";
    
   if (type == "express")
   {ldelim}
        bad_files_count = document.getElementById('bad_files').value;
        
        if (bad_files_count != "0") 
        {ldelim}
           document.getElementById('next_button').disabled = true;
           document.getElementById('next_button').style.display = "none";
        {rdelim}          
        
        document.getElementById('total_steps').innerHTML='2';
   {rdelim}
   else if (type == "custom")
   {ldelim}        
        document.getElementById('total_steps').innerHTML='3';
   {rdelim}
{rdelim}

{literal}    
function controlPermissions()
{                
    {/literal}
    var url = "module=PDFMaker&action=PDFMakerAjax&file=controlPermissions&controlPermissionsUpdate=true&to126={$TO126}&to127={$TO127}&to128={$TO128}";
    {literal}
    new Ajax.Request(
                    'index.php',
                      {queue: {position: 'end', scope: 'command'},
                              method: 'post',
                              postBody:url,
                              onComplete: function(response) {
                                      document.getElementById('list_permissions').innerHTML = response.responseText;
                                      
                                      bad_files_count = document.getElementById('bad_files').value;
                                      
                                      type = document.getElementById('installexpress').checked;
                                      
                                      if (type == true && bad_files_count == "0")
                                      {
                                          document.getElementById('next_button').disabled = false;
                                          document.getElementById('next_button').style.display = "inline";
                                          
                                          document.getElementById('btn_control_permissions').style.display = "none"; 
                                      }
                              }
                      }
                      );
                  
}
{/literal}    
</script>

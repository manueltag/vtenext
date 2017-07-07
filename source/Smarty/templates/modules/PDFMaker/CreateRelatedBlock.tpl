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
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={$APP.LBL_CHARSET}">
	<title>{$MOD.LBL_EDIT_RELATED_BLOCK}</title>
	<link href="{$THEME_PATH}style.css" rel="stylesheet" type="text/css">
	<script language="JavaScript" type="text/javascript" src="{"include/js/general.js"|resourcever}"></script>
	<script language="JavaScript" type="text/javascript" src="include/js/{php} echo $_SESSION['authenticated_user_language'];{/php}.lang.js?{php} echo $_SESSION['vtiger_version'];{/php}"></script>
	<script language="javascript" type="text/javascript" src="include/scriptaculous/prototype.js"></script>
	<script language="JavaScript" type="text/javascript" src="include/js/json.js"></script>
	<script language="JavaScript" type="text/javascript" src="modules/PDFMaker/PDFMaker.js"></script>
	
</head>
<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">
<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="mailClient mailClientBg">
<tr>
	<td>
		<form name="NewBlock" method="POST" ENCTYPE="multipart/form-data" action="index.php" style="margin:0px">
		<input type="hidden" name="module" value="PDFMaker">
		<input type="hidden" name="pdfmodule" value="{$REL_MODULE}">
		<input type="hidden" name="primarymodule" value="{$REL_MODULE}">
		<input type="hidden" name="record" value="{$RECORD}">
		<input type="hidden" name="file" value="SaveRelatedBlock">
		<input type="hidden" name="action" value="PDFMakerAjax">
    <input type="hidden" name="step" id="step" value="1">
    
    <div id="filter_columns" style="display:none"><option value="">{$REP.LBL_NONE}</option>{$SECCOLUMNS}</div>
    
		<table width="100%" border="0" cellspacing="0" cellpadding="5" >
			<tr>
				<td  class="moduleName" width="80%">{$MOD.LBL_EDIT_RELATED_BLOCK} </td>
				<td  width=30% nowrap class="componentName" align=right></td>
			</tr>
		</table>
	
	
		<table width="100%" border="0" cellspacing="0" cellpadding="5" class="homePageMatrixHdr"> 
		<tr>
		<td>
		
					<table width="100%" border="0" cellspacing="0" cellpadding="0" > 
						<tr>
							<td width="25%" valign="top" >
								<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">
									<tr><td id="step1label" class="settingsTabSelected" style="padding-left:10px;">1. {$REP.LBL_RELATIVE_MODULE} </td></tr>
									<tr><td id="step2label" class="settingsTabList" style="padding-left:10px;">2. {$REP.LBL_SELECT_COLUMNS}</td></tr>
									<tr><td id="step3label" class="settingsTabList" style="padding-left:10px;">3. {$REP.LBL_FILTERS} </td></tr>
									<tr><td id="step4label" class="settingsTabList" style="padding-left:10px;">4. {$MOD.LBL_BLOCK_STYLE} </td></tr>
								</table>
							</td>
							<td width="75%" valign="top"  bgcolor=white >
								<!-- STEP 1 -->
								<div id="step1" style="display:{if $RECORD neq ""}none{else}block{/if}">
									<table class="small" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" height="550" width="100%">
										<tr height='10%'>
										<td colspan="2">
											<span class="genHeaderGray">{$REP.LBL_RELATIVE_MODULE}</span><hr>
										</td>
										</tr>
										<tr valign=top>
											{if $RELATED_MODULES|@count > 0}
												<td style="padding-right: 5px;" align="right" nowrap width="25%" align="top"><b>{$REP.LBL_NEW_REP0_HDR2}</b></td>
												<td style="padding-left: 5px; " align="left" width="75%" valign="top">
													<table class="small">
													{foreach item=relmod name=relmodule from=$RELATED_MODULES}
														{if $SEC_MODULE eq $relmod}
															<tr valign='top'><td><input type='radio' name="secondarymodule" checked value="{$relmod}" />
																{$relmod|getTranslatedString:$relmod} {* crmv@25443 *}
															</td></tr>
														{else}
															<tr valign='top'><td><input type='radio' name="secondarymodule" value="{$relmod}" />
																{$relmod|getTranslatedString:$relmod}	{* crmv@25443 *}
															</td></tr>
														{/if}
													{/foreach}
													</table>
												</td>
											{else}
												<td style="padding-right: 5px;" align="left" nowrap width="25%"><b>{$REP.NO_REL_MODULES}</b></td>
											{/if}
										</tr>
									</table>
							  </div>
							  <!-- STEP 2 -->
								<div id="step2" style="display:none;">
							  	<script>
                  var moveupLinkObj,moveupDisabledObj,movedownLinkObj,movedownDisabledObj;
                  </script>
                  <table class="small" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0"  valign="top" height="500" width="100%">
                  	<tbody><tr>
                  	<td colspan="4">
                  	<span class="genHeaderGray">{$REP.LBL_SELECT_COLUMNS}</span>
                  	<hr>
                  	</td>
                  	</tr>
                  	<tr>
                  	<td colspan="2" height="26"><b>{$REP.LBL_AVAILABLE_FIELDS}</b></td>
                  	<td colspan="2"><b>{$REP.LBL_SELECTED_FIELDS}</b></td>
                  	</tr>
                  	<tr  valign="top">
                  	<td style="padding-right: 5px;" align="right" width="40%">
                  	<select id="availList" multiple size="16" name="availList" class="txtBox">
                  	{$SECCOLUMNS}
                  	</select>
                  	</td>
                  	<td style="padding: 5px;" align="center" width="10%">
                  	<input name="add" value=" {$REP.LBL_ADD_ITEM} &gt " class="classBtn" type="button" onClick="addColumn()">
                  	</td>
                  	<input type="hidden" name="selectedColumnsString"/>
                  	<td style="padding-left: 5px;" align="left" width="40%">
                  	<select id="selectedColumns" size="16" name="selectedColumns" onchange="selectedColumnClick(this);" multiple class="txtBox" style="width:164px;">
                  	{$SELECTEDCOLUMNS}
                  	</select>
                  	</td>
                    	<td style="padding-left: 5px;" align="left" width="10%">
                      	<table border="0" cellpadding="0" cellspacing="0">
                      		<tbody><tr> 
                      		<td>
                      		  <img src="themes/images/movecol_up.gif" onmouseover="this.src='themes/images/movecol_up.gif'" onmouseout="this.src='themes/images/movecol_up.gif'" onclick="moveUp()" onmousedown="this.src='themes/images/movecol_up.gif'" align="absmiddle" border="0"> 
                      		</td>
                      		</tr>
                      		<tr> 
                      		<td> 
                      	   	<img src="themes/images/movecol_down.gif" onmouseover="this.src='themes/images/movecol_down.gif'" onmouseout="this.src='themes/images/movecol_down.gif'" onclick="moveDown()" onmousedown="this.src='themes/images/movecol_down.gif'" align="absmiddle" border="0"> 
                      		</td>
                      		</tr>
                      		<tr> 
                      		<td>
                      		  <img src="themes/images/movecol_del.gif" onmouseover="this.src='themes/images/movecol_del.gif'" onmouseout="this.src='themes/images/movecol_del.gif'" onclick="delColumn()" onmousedown="this.src='themes/images/movecol_del.gif'" align="absmiddle" border="0">
                      		</td>
                      		</tr>
                      		</tbody>
                      	</table> 
                    	</td>
                  	</tr> 
                  	<tr><td colspan="4" height="215"></td></tr>
                  	</tbody>
                  </table>
								</div>
								<!-- STEP 3 -->
								<div id="step3" style="display:none;">
								{include file='modules/PDFMaker/BlockFilters.tpl'}
								</div>
								<!-- STEP 4 -->
								{literal}   
                    <script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
                {/literal} 
								
								<div id="step4" style="display:{if $RECORD neq ""}block{else}none{/if};"> 
								    
                    <table class="small" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" width="100%">
										<tr height='10%'>
  										<td colspan="2">
  											<span class="genHeaderGray">{$MOD.LBL_BLOCK_STYLE}</span><hr>
  										</td>
										</tr>
										<tr>
                      <td width="10%" align="right">{$APP.Name}:</td>
                      <td>
                      	<div class="dvtCellInfo">
                      		<input type="text" name="blockname" id="blockname" class="detailedViewTextBox" value="{$BLOCKNAME}">
                      	</div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2">
                        <textarea name="relatedblock" id="relatedblock" style="width:90%;height:700px" class=small tabindex="5">{$RELATEDBLOCK}</textarea>
                      </td>
                    </tr>
                </div>
                
                {literal}   
                    <script type="text/javascript">
                    	CKEDITOR.replace('relatedblock',{customConfig:'../../../modules/PDFMaker/fck_config.js'} );
                    </script>
                {/literal}
						</td>
					</tr>
				</table>


			</td>
		</tr>
		</table>
		
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="reportCreateBottom">
		<tr>
			<td align="right" style="padding:10px;">
			<input type="button" name="back_rep" id="back_rep" value=" &nbsp;&lt;&nbsp;{$APP.LBL_BACK}&nbsp; " {if $RECORD eq ""}disabled="disabled"{/if} class="crmbutton small cancel" onClick="changeStepsback();">
			&nbsp;<input type="button" name="next" id="next" value=" &nbsp;{$APP.LNK_LIST_NEXT}&nbsp;&rsaquo;&nbsp; " onClick="changeSteps();" class="crmbutton small save">
			</td>
		</tr>
	</table>
		</form>	

</td>
</tr>
</table>
	
	
</body>
</html>
{if $BACK_WALK eq 'true'}
{literal}
<script>
	hide('step1');
	show('step2');
	document.getElementById('back_rep').disabled = false;
	getObj('step1label').className = 'settingsTabList'; 
	getObj('step2label').className = 'settingsTabSelected';
</script>
{/literal}
{/if}
{if $BACK eq 'false'}
{literal}
<script>
	hide('step1');
	show('step2');
	document.getElementById('back_rep').disabled = true;
	getObj('step1label').className = 'settingsTabList'; 
	getObj('step2label').className = 'settingsTabSelected';
</script>
{/literal}
{/if}
{literal}
<script>

function changeSecOptions()
{
      secmodule = getCheckedValue(document.NewBlock.secondarymodule);

      new Ajax.Request(
                          'index.php',
                          {queue: {position: 'end', scope: 'command'},
                                  method: 'post',
                                  postBody: 'action=PDFMakerAjax&mode=columns&file=EditRelatedBlock&module=PDFMaker&secmodule='+secmodule,
                                  onComplete: function(response) {
                                     document.getElementById("filter_columns").innerHTML = "<option value=''>{/literal}{$REP.LBL_NONE}{literal}</option>" + response.responseText;
                                     
                                     if (browser_ie)
                                         document.NewBlock.availList.outerHTML = "<select id=\"availList\" multiple size=\"16\" name=\"availList\" class=\"txtBox\">"+response.responseText+"</select>";
                                     else
                                         document.NewBlock.availList.innerHTML = response.responseText;
                                         
                                     getObj("step1label").className = 'settingsTabList';
	                                   getObj("step2label").className = 'settingsTabSelected';
                                     hide('step1');
                                     show('step2');
                                  }
                          }
            	        );
	        
      new Ajax.Request(
                          'index.php',
                          {queue: {position: 'end', scope: 'command'},
                                  method: 'post',
                                  postBody: 'action=PDFMakerAjax&mode=stdcriteria&file=EditRelatedBlock&module=PDFMaker&secmodule='+secmodule,
                                  onComplete: function(response) {
                                     oldvalue = getObj("stdDateFilterField").value;
                                     
                                     if (browser_ie)
                                         getObj("stdDateFilterField").outerHTML = "<select name='stdDateFilterField' id='stdDateFilterField' class='detailedViewTextBox' onchange='standardFilterDisplay();'>" + response.responseText + "</select>";
                                     else
                                         getObj("stdDateFilterField").innerHTML = response.responseText;
                                         
                                     if (oldvalue != "") getObj("stdDateFilterField").value = oldvalue;
                                  }
                          }
            	        );
 	        
} 
setObjects();
</script>
{/literal}
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

					<td class="moduleName" width="80%" style="padding-left:10px;">{$APP[$MODULE]}</td>

					<td  width=30% nowrap class="componentName" align=right>{$APP.VTIGER}</td>

				</tr>

			</table>

			<div id="ListViewContents">

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

          

               	<input name="module" type="hidden" value="{$RETURN_MODULE}">

          	  	<input name="action" type="hidden" value="{$RETURN_ACTION}">

                <input name="pmodule" type="hidden" value="{$MODULE}">

          	  	<input type="hidden" name="curr_row" value="{$CURR_ROW}">	

          	  	<input name="entityid" type="hidden" value="">

          	  	<input name="popuptype" id="popup_type" type="hidden" value="{$POPUPTYPE}">

          	   	<input name="idlist" type="hidden" value="">
<strong>{$APP.ActuallDuplicate}</strong>
          		  <div style="overflow:auto;height:348px;">

                		<table style="background-color: rgb(204, 204, 204);" class="small" border="0" cellpadding="5" cellspacing="1" width="100%">

                		<tbody>

                		<tr>

                			 <td class="lvtCol" width="3%">#</td>

                       {foreach item=header from=$LISTHEADER}

                      	  <td class="lvtCol">{$header}</td>

                       {/foreach}

                		</tr>

                		{foreach key=entity_id item=entity from=$LISTENTITY}

                    	  <tr bgcolor=white onMouseOver="this.className='lvtColDataHover'" onMouseOut="this.className='lvtColData'">

                    	   		<td>{$entity_id}</td>

                    		   

                            {foreach item=data from=$entity}

                    		        <td>{$data}</td>

                            {/foreach}

                    		</tr>

                    {/foreach}

                	  </tbody>

                	  </table>

                	  <br>

                    <input type="button" onclick="window.opener.document.EditView.submit();window.close();" value="{$MOD.LBL_INSERT_ENTRIE}" class="crmbutton small save">

                    &nbsp;&nbsp;

                    <input type="button" onclick="window.close();" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmbutton small cancel">

        		   	<div>

          	    </td>

          	</tr>

            

          </table>

           

			</div>

		</td>

	</tr>

	

</table>

</body>








































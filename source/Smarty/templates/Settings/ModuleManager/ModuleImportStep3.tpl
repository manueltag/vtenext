<div id="vtlib_modulemanager_import" style="display:block;position:absolute;width:500px;"></div>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <!-- crmv@30683 -->
<tr>
	<td valign="top"></td>
    <td class="showPanelBg" style="padding: 5px;" valign="top" width="100%"> <!-- crmv@30683 -->
    <br>

	<div align=center>
		{include file='SetMenu.tpl'}
		{include file='Buttons_List.tpl'} {* crmv@30683 *}		
		<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
		<tr>
			<td rowspan="2" valign="top" width="50"><img src="{'vtlib_modmng.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" title="{$MOD.LBL_USERS}" border="0" height="48" width="48"></td>
			<td class="heading2" valign="bottom"><b> {$MOD.LBL_SETTINGS} &gt; {$MOD.VTLIB_LBL_MODULE_MANAGER} &gt; {$APP.LBL_IMPORT} </b></td> <!-- crmv@30683 -->  
		</tr>

		<tr>
			<td class="small" valign="top">{$MOD.VTLIB_LBL_MODULE_MANAGER_DESCRIPTION}</td>
		</tr>
		</table>
				
		<br>
		<table border="0" cellpadding="10" cellspacing="0" width="100%">
		<tr>
			<td>
				<div id="vtlib_modulemanager_import_div">
					
                	<form method="POST" action="index.php">
						<table class='tableHeading' cellpadding=5 cellspacing=0 border=0 width=100%>
						<tr>
							<td class='big' colspan=2><b>{$MOD.VTLIB_LBL_IMPORTING_MODULE_START}</b></td>
						</tr>
						</table>
						
						<table class='tableHeading' cellpadding=5 cellspacing=0 border=0 width=100%>
						<tr valign=top>
							<td class='cellText small'>
								{* Invoking API inside template to capture the logging details. *}
								{php}
									$__moduleimport_package = $this->_tpl_vars['MODULEIMPORT_PACKAGE'];
									$__moduleimport_package_file = $this->_tpl_vars['MODULEIMPORT_PACKAGE_FILE'];
									$__moduleimport_dir_overwrite = $this->_tpl_vars['MODULEIMPORT_DIR_OVERWRITE'];

									$__moduleimport_package->import($__moduleimport_package_file, $__moduleimport_dir_overwrite);
									unlink($__moduleimport_package_file);
								{/php}
							</td>
						</tr>
						</table>

						<table class='tableHeading' cellpadding=5 cellspacing=0 border=0 width=100%>
						<tr valign=top>
							<td class='cellText small' align=right>
								<input type="hidden" name="module" value="Settings">
								<input type="hidden" name="action" value="ModuleManager">
								<input type="hidden" name="parenttab" value="Settings">
								
								<input type="submit" class="crmbutton small edit" value="{$APP.LBL_FINISH}">
							</td>
						</tr>
						</table>
					</form>
                </div>
			</td>
		</tr>
		</table>
		<!-- End of Display -->
		
		</td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
   </div>

        </td>
        <td valign="top"></td>
	</tr>
</table>
<br>

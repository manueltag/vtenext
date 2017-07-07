{* crmv@82770 *}

<div class='hide_tab' id="editRowmodrss_{$HOME_STUFFID}" style="position:relative; top:0px;left:0px;">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="small" valign="top">
		<tr>
			<td  class="homePageMatrixHdr">
				{$MOD.LBL_SIZE}&nbsp;
				<select id="selChartHomeSize_{$HOME_STUFFID}">
					<option value="1" {if $HOME_STUFFSIZE eq 1}selected{/if}>1</option>
					<option value="2" {if $HOME_STUFFSIZE eq 2}selected{/if}>2</option>
					<option value="3" {if $HOME_STUFFSIZE eq 3}selected{/if}>3</option>
					<option value="4" {if $HOME_STUFFSIZE eq 4}selected{/if}>4</option>
				</select>
			</td>
			<td valign="top" align="center" class="homePageMatrixHdr" nowrap style="height:28px;" width="40%">
				<button type="button" name="save" class="crmbutton small save" onclick="saveHomeChart('selChartHomeSize_{$HOME_STUFFID}')">{$APP.LBL_SAVE_BUTTON_LABEL}</button>
				<button type="button" name="cancel" class="crmbutton small cancel" onclick="cancelEntries('editRowmodrss_{$HOME_STUFFID}')">{$APP.LBL_CANCEL_BUTTON_LABEL}</button>
			</td>
		</tr>
	</table>
</div> 

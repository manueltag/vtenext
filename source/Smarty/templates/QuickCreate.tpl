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
<body class=small>
{include file='QuickCreateHidden.tpl'}
<table border="0" align="center" cellspacing="0" cellpadding="0" width="100%" class="crmvDiv">
<tr>
<td>
	<table border="0" cellspacing="0" cellpadding="0" width="100%" class=small>
		<tr height="34">
			<td style="padding:5px" class="level3Bg">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" class="small"  id="qcreate_handle" style="cursor:move;"><b>{$APP.LBL_QUICK_CREATE} {$QCMODULE}</b></td> {* crmv@30014 *}
					<!-- crmv@16265 : QuickCreatePopup -->	<!-- crmv@18170 -->
					{if $QUICKCREATEPOPUP eq true}
						<td align=right><input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" onClick="if(SubmitQCForm('{$MODULE}',getObj('QcEditView'))) return true; else return false;" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " style="width:90px" ></td>	{* crmv@59091 *}
					{else}
						<td align=right><input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " style="width:70px" ></td>
					{/if}
					<!-- crmv@16265e -->
				</tr>
				</table>
			</td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=0 width="100%" class="small">
	<tr>
		<td>
		<!-- quick create UI starts -->
		<table border="0" cellspacing="0" cellpadding="5" width="100%" class="small" bgcolor="white" >
			{assign var="fromlink_val" value="qcreate"}
			{assign var="data" value=$QUICKCREATE}
			{include file='DisplayFields.tpl'}
		</table>
		<!-- save cancel buttons -->
		<table border="0" cellspacing="0" cellpadding="5" width="100%" class=qcTransport>
			<tr>

			</tr>
		</table>
		</td>
	</tr>
	</table>
	<div class="closebutton" onClick="window.hide('qcform');"></div>
</td>
</tr>
</table>
{if $ACTIVITY_MODE eq 'Events'}
	<SCRIPT id="qcvalidate">
		var qcfieldname = new Array('subject','date_start','time_start','eventstatus','activitytype','due_date','time_end');
		var qcfieldlabel = new Array('Subject','Start Date & Time','Start Date & Time','Status','Activity Type','End Date & Time','End Date & Time');
		var qcfielddatatype = new Array('V~M','DT~M~time_start','T~O','V~O','V~O','D~M~OTH~GE~date_start~Start Date & Time','T~M','DT~M~time_end');
		var qcfielduitype = new Array(1,6,1,15,15,23,1); //crmv@83877
		var qcfieldwstype = new Array('string','date','string','picklist','picklist','date','string'); //crmv@112297
	</SCRIPT>
{elseif $MODULE eq 'Task'}
	<SCRIPT id="qcvalidate">
		var qcfieldname = new Array('subject','date_start','time_start','taskstatus');
		var qcfieldlabel = new Array('Subject','Start Date & Time','Start Date & Time','Status');
		var qcfielddatatype = new Array('V~M','DT~M~time_start','T~O','V~O');
		var qcfielduitype = new Array(1,6,1,15); //crmv@83877
		var qcfieldwstype = new Array('string','date','string','picklist'); //crmv@112297
	</SCRIPT>
{else}
	<SCRIPT id="qcvalidate">
		var qcfieldname = new Array({$VALIDATION_DATA_FIELDNAME});
		var qcfieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
		var qcfielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
		var qcfielduitype = new Array({$VALIDATION_DATA_FIELDUITYPE}); // crmv@83877
		var qcfieldwstype = new Array({$VALIDATION_DATA_FIELDWSTYPE}); //crmv@112297
	</SCRIPT>
{/if}
</form>

{* crmv@30014 *}
<script type="text/javascript">
	var RChartHandle = document.getElementById("qcreate_handle");
	var RChartRoot = document.getElementById("qcform");
	Drag.init(RChartHandle, RChartRoot);
	
	var width = jQuery('#qcform').find('.level3Bg').width();
	jQuery('#qcform').find('.level3Bg').width(width-50);
</script>
{* crmv@30014e *}

</body>
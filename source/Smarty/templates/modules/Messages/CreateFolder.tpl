<div id="createFolder" style="display:none;position:fixed;" class="crmvDiv">
	<table border="0" cellpadding="5" cellspacing="0" width="250">
		<tr style="cursor:move;" height="34">
			<td id="createFolder_Handle" style="padding:5px" class="level3Bg">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="50%"><b>{'LBL_CREATE'|@getTranslatedString}</b></td>
					<td width="50%" align="right">&nbsp;
						{include file="LoadingIndicator.tpl" LIID="indicatorcreateFolder" LIEXTRASTYLE="display:none;"}&nbsp;
					</td>
				</tr>
				</table>
			</td>
		</tr>
	</table>
	<div width="250" class="hdrNameBg" id="createFolder_list" style="height:400px;overflow:auto;"></div>
	<div class="closebutton" onClick="fninvsh('createFolder');"></div>
</div>
<script>
	var THandle = document.getElementById("createFolder_Handle");
	var TRoot   = document.getElementById("createFolder");
	Drag.init(THandle, TRoot);
</script>
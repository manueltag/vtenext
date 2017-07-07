{********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************}

{if $TAG_CLOUD_DISPLAY eq 'true'}
	<!-- Tag cloud display -->
	<div style="padding-top:5px;">
		<table border=0 cellspacing=0 cellpadding=0 width=100% class="tagCloud">
			<tr>
				<td class="tagCloudTopBg"><img src="{$IMAGE_PATH}tagCloudName.gif" border=0></td>
			</tr>
			<tr>
				<td><div id="tagdiv" style="display:visible;"><form method="POST" action="javascript:void(0);" onsubmit="return tagvalidate();"><input class="textbox"  type="text" id="txtbox_tagfields" name="textbox_First Name" value="" style="width:100px;margin-left:5px;"></input>&nbsp;&nbsp;<input name="button_tagfileds" type="submit" class="crmbutton small save" value="{$APP.LBL_TAG_IT}"/></form></div></td>
			</tr>
			<tr>
				<td class="tagCloudDisplay" valign=top> <span id="tagfields">{$ALL_TAG}</span></td>
			</tr>
		</table>
	</div>
	<!-- End Tag cloud display -->
	<script>
	function tagvalidate()
	{ldelim}
		if(trim(document.getElementById('txtbox_tagfields').value) != '')
			SaveTag('txtbox_tagfields','{$ID}','{$MODULE}');	
		else
		{ldelim}
			alert("{$APP.PLEASE_ENTER_TAG}");
			return false;
		{rdelim}
	{rdelim}
	function DeleteTag(id,recordid)
	{ldelim}
		$("status").style.display="inline";
		Effect.Fade('tag_'+id);
		new Ajax.Request(
			'index.php',
	                {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
	                        method: 'post',
	                        postBody: "file=TagCloud&module={$MODULE}&action={$MODULE}Ajax&ajxaction=DELETETAG&recordid="+recordid+"&tagid=" +id,
	                        onComplete: function(response) {ldelim}
							getTagCloud();
							$("status").style.display="none";
	                        {rdelim}
	                {rdelim}
	        );
	{rdelim}
	function getTagCloud()
	{ldelim}
		new Ajax.Request(
		        'index.php',
		        {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
		        method: 'post',
		        postBody: 'module={$MODULE}&action={$MODULE}Ajax&file=TagCloud&ajxaction=GETTAGCLOUD&recordid={$ID}',
		        onComplete: function(response) {ldelim}
	                                $("tagfields").innerHTML=response.responseText;
	                                $("txtbox_tagfields").value ='';
	                        {rdelim}
		        {rdelim}
		);
	{rdelim}
	getTagCloud();
	</script>
{/if}
{*
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
*}

<script language="JavaScript" type="text/javascript" src="{"modules/Rss/Rss.js"|resourcever}"></script>
<script src="include/scriptaculous/scriptaculous.js" type="text/javascript"></script>
<script>
{literal}

function GetRssFeedList(id)
{
	$("status").style.display="inline";
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'module=Rss&action=RssAjax&file=ListView&directmode=ajax&record='+id,
                        onComplete: function(response) {
                                $("status").style.display="none";
				$("rssfeedscont").innerHTML=response.responseText;
                        }
                }
        );
}

function DeleteRssFeeds(id)
{
   if(id != '')	
   {
	{/literal}
        if(confirm('{$APP.DELETE_RSSFEED_CONFIRMATION}'))
        {literal}
	{	
		show('status');	
		var feed = 'feed_'+id;
		$(feed).parentNode.removeChild($(feed));
		new Ajax.Request(
                	'index.php',
        	        {queue: {position: 'end', scope: 'command'},
                        	method: 'post',
	                        postBody: 'module=Rss&return_module=Rss&action=RssAjax&file=Delete&directmode=ajax&record='+id,
        	                onComplete: function(response) {
	        	                $("status").style.display="none";
                                	$("rssfeedscont").innerHTML=response.responseText;
					$("mysite").src = '';
					$("rsstitle").innerHTML = "&nbsp";
                        	}
                	}
        	);
	}
   }
   else
	alert(alert_arr.LBL_NO_FEEDS_SELECTED);	     	
}

function SaveRssFeeds()
{
	$("status").style.display="inline";
	var rssurl = $('rssurl').value;
	rssurl = rssurl.replace(/&/gi,"##amp##");
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:'module=Rss&action=RssAjax&file=Popup&directmode=ajax&rssurl='+rssurl, 
			onComplete: function(response) {
	
                    $("status").style.display="none";
					if(isNaN(parseInt(response.responseText)))
        				{
				               var rrt = response.responseText;
						$("temp_alert").innerHTML = rrt;
						removeHTMLTags();	
				                $('rssurl').value = '';
					}
					else
        				{
				                GetRssFeedList(response.responseText);
				                getrssfolders();
				                $('rssurl').value = '';
				                Effect.Puff('PopupLay');
        				}
                                }
                        }
                );
}
{/literal}
</script>

<!-- Contents -->
{include file="Buttons_List1.tpl"}
<div id="temp_alert" style="display:none"></div>
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
<tr>
	<td valign=top align=right width=8></td>
	<td class="showPanelBg" valign="top" width="100%" align=center >	
		
			<!-- RSS Reader UI Starts here--><br>
				<table width="100%"  border="0" cellspacing="0" cellpadding="5" class="mailClient mailClientBg">
				<tr>
					<td align=left>
					
						<table width="100%"  border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width=95% align=left>
								<i class="vteicon md-lg nohover" style="vertical-align:middle">rss_feed</i>
								<a href="javascript:;" onClick="showFloatingDiv('PopupLay', this); jQuery('#rssurl').focus();" title='{$APP.LBL_ADD_RSS_FEEDS}'>{$MOD.LBL_ADD_RSS_FEED}</a>
							</td>
							<td  class="componentName" nowrap></td>
						</tr>
						<tr>
							<td colspan="2">
								<table border=0 cellspacing=0 cellpadding=2 width=100%>
								<tr>
									<td width=30% valign=top>
									<!-- Feed Folders -->
										<table border=0 cellspacing=0 cellpadding=0 width=100%>
										<tr><td class="small mailSubHeader" height="25"><b>{$MOD.LBL_FEED_SOURCES}</b></td></tr>
										<tr><td class="hdrNameBg" bgcolor="#fff" height=225><div id="rssfolders" style="height:100%;overflow:auto;">{$RSSFEEDS}</div></td></tr>
										</table>
									</td>
									<td width=1%>&nbsp;</td>
									<td width=69% valign=top>
									<!-- Feed Header List -->
										<table border=0 cellspacing=0 cellpadding=0 width=100%>
										<tr>
											<td><div id="rssfeedscont">
											{include file='RssFeeds.tpl'}	
											</div>
											</td>
										</tr>
										</table>
									</td>
								</tr>
								</table>
								
							</td>
						</tr>
						<tr>		
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td height="5"></td>
						</tr>
						<tr>
							
							<td colspan="3" class="mailSubHeader" id="rsstitle">&nbsp;</td>
						</tr>
						<tr>
							<!-- RSS Display -->
							<td colspan="3" style="padding:2px">
							<iframe width="100%" height="250" frameborder="0" id="mysite" scrolling="auto" marginheight="0" marginwidth="0" style="background-color:#FFFFFF;"></iframe>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			<!-- RSS Reader UI ends here -->
	</td>
	<td valign=top align=right width=8></td>			
	</tr>
	</table>
	
	{assign var="FLOAT_TITLE" value=$MOD.LBL_ADD_RSS_FEED}
	{assign var="FLOAT_WIDTH" value="300px"}
	{capture assign="FLOAT_CONTENT"}
	<form onSubmit="SaveRssFeeds(); return false;">
	<table border=0 cellspacing=0 cellpadding=5 width=95% align=center> 
	<tr>
		<td class=small >
			{* popup specific content fill in starts *}
			<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
				<tr>
					<td align="right" width="25%"><b>{$MOD.LBL_FEED}</b></td>
					<td align="left" width="75%"><input type="text" id="rssurl" class="txtBox" value=""/></td>
				</tr>
			</table>
			{* popup specific content fill in ends *}
		</td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
		<tr>
			<td align="center">
				<input type="submit" name="save" value=" &nbsp;{$APP.LBL_SAVE_BUTTON_LABEL}&nbsp; " class="crmbutton small save"/>&nbsp;&nbsp;
			</td>
		</tr>
	</table>
	</form>
	{/capture}
	{include file="FloatingDiv.tpl" FLOAT_ID="PopupLay" FLOAT_BUTTONS=""}

	
<script type="text/javascript" language="Javascript">
{literal}

function makedefaultRss(id)
{
	if(id != '')
	{
		$("status").style.display="inline";
		new Ajax.Request(
                	'index.php',
	                {queue: {position: 'end', scope: 'command'},
        	                method: 'post',
                	        postBody:'module=Rss&action=RssAjax&file=Popup&directmode=ajax&record='+id, 
                        	onComplete: function(response) {
                                	$("status").style.display="none";
        				getrssfolders();
        	               }
                	}
        	);
	}
}
function getrssfolders()
{
	new Ajax.Request(
        	'index.php',
                {queue: {position: 'end', scope: 'command'},
                	method: 'post',
                        postBody:'module=Rss&action=RssAjax&file=ListView&folders=true',
			onComplete: function(response) {
                        		$("status").style.display="none";
					$("rssfolders").innerHTML=response.responseText;
                               }
                        }
                );
}


function removeHTMLTags()
{
 	if(document.getElementById && document.getElementById("temp_alert"))
	{
 		var strInputCode = document.getElementById("temp_alert").innerHTML;
 		var strTagStrippedText = strInputCode.replace(/<\/?[^>]+(>|$)/g, "");
 		alert("Output Message:\n" + strTagStrippedText);	
 	}	
}

{/literal}
</script>

{*
/********************************************************************************************
 *                                                                                          *
 *                   www.digital-worx.de - Asterix 5 Integration                            *
 *                      by Sebastian Kummer and Manfred Kutas                               *
 *                                                                                          *
 *                An ajax-based integration of asterisk into vTiger 5                       *
 *                                                                                          *
 *            Published unter GNU Lesser General Public Licence (LGPL) 2.1                  *
 *                       http://www.gnu.org/licenses/lgpl.txt                               *
 *                                                                                          *
 *******************************************************************************************/
*}

<link rel="stylesheet" href="themes/{$THEME}/AsteriskStyle.css">

<div class="AsteriskPopupLayer" id="AsteriskCallPopup" style="visibility: hidden; z-index: 1;">
	<table width="100%" border="0" cellpadding="8" cellspacing="0" class="small">
		<tr style="cursor:move;" id="BHandle" >
			<td align="left" class="AsteriskPopupLayerHdr"  onMouseDown="trans_asterisk('AsteriskCallPopup')" onMouseUp="unTrans_asterisk('AsteriskCallPopup')"><div id="AsteriskCallTitle" style="font-weight: bold;"></div></td>
			<td align="right" class="AsteriskPopupLayerHdr" onMouseDown="trans_asterisk('AsteriskCallPopup')" onMouseUp="unTrans_asterisk('AsteriskCallPopup')"><img src="{$IMAGEPATH}uparrow.gif" align="absmiddle" onClick="hide_asterisk('AsteriskCallPopup')" style="cursor:pointer;" / alt="close" title="close"></td>
		</tr>
		<tr>	
			<td colspan="2">

			<table border=0 cellspacing=0 cellpadding=5 width=100%>
					<tr>
						<td colspan="2" align="center" valign="bottom" width="100%">
							<div id="AsteriskCallContent" style="font-size:14px">
							</div>
						</td>
					</tr>
			</table>
			</td>
		</tr>
	</table>
</div>

<input type="hidden" name="lastCallTimestamp" id="lastCallTimestamp" value="0" >
<div class="AsteriskPopupLayer" id="AsteriskPopup" style="visibility: hidden; z-index: 1;">
	<table width="100%" border="0" cellpadding="8" cellspacing="0" class="small">
		<tr style="cursor:move;" id="AHandle" >
			<td align="left" class="AsteriskPopupLayerHdr"  onMouseDown="trans_asterisk('AsteriskPopup')" onMouseUp="unTrans_asterisk('AsteriskPopup')"><div id="AsteriskTitle" style="font-weight: bold;"></div></td>
			<td align="right" class="AsteriskPopupLayerHdr" onMouseDown="trans_asterisk('AsteriskPopup')" onMouseUp="unTrans_asterisk('AsteriskPopup')"><img src="{$IMAGEPATH}uparrow.gif" align="absmiddle" onClick="hide_asterisk('AsteriskPopup')" style="cursor:pointer;" / alt="close" title="close"></td>
		</tr>
		<tr>	
			<td colspan="2">

			<table border=0 cellspacing=0 cellpadding=5 width=100%>
					<tr>
						<td colspan="2" align="center" valign="bottom" width="100%">
							<div id="AsteriskContent" style="font-size:14px">
							</div>
						</td>
					</tr>
			</table>
			</td>
		</tr>
	</table>
</div>

<script language="JavaScript">
<!--

{if ($ASTERISK_ENABLE_INC_CALL eq 'true')}
window.setInterval("xajax_incomingCall(document.getElementById('lastCallTimestamp').value)", 500);
{/if}
function makeCall(callto) 
{ldelim}
		xajax_prepareCallto(callto);
		placeAtCenter(document.getElementById("AsteriskCallPopup"));
		window.setTimeout("doCall('"+ callto.toString() +"')", 1000);
{rdelim}
		
function doCall(callto) 
{ldelim}
		xajax_callto(callto);
		window.setTimeout("hide_asterisk('AsteriskCallPopup')", 10000);
{rdelim}


function hide_asterisk(AsteriskID)
{ldelim}
		document.getElementById(AsteriskID).style.visibility="hidden";
    document.getElementById(AsteriskID).style.zIndex="1";
{rdelim}


function trans_asterisk(AsteriskID)
{ldelim}
			document.getElementById(AsteriskID).className='AsteriskPopupLayerTr'
{rdelim}

function unTrans_asterisk(AsteriskID)
{ldelim}
			document.getElementById(AsteriskID).className='AsteriskPopupLayer'
{rdelim}

// initialize Drag & Drop AsteriskCallPopup
var theHandle = document.getElementById("BHandle");
var theRoot   = document.getElementById("AsteriskCallPopup");
Drag.init(theHandle, theRoot);

//document.getElementById("AsteriskCallPopup").style.left="780px";
//document.getElementById("AsteriskCallPopup").style.top="120px";


// initialize Drag & Drop AsteriskPopup
var theHandle = document.getElementById("AHandle");
var theRoot   = document.getElementById("AsteriskPopup");
Drag.init(theHandle, theRoot);

//document.getElementById("AsteriskPopup").style.left="780px";
//document.getElementById("AsteriskPopup").style.top="80px";

-->
</script>

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
{* crmv@94525 *}

<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<style type="text/css">
{literal}
a.x {
	color:black;
	text-align:center;
	text-decoration:none;
	padding:5px;
	font-weight:bold;
}
	
a.x:hover {
	color:#333333;
	text-decoration:underline;
	font-weight:bold;
}

.drag_Element {
	position:relative;
	left:0px;
	top:0px;
	padding-left:5px;
	padding-right:5px;
	border:0px dashed #CCCCCC;
	visibility:hidden;
}

#Drag_content {
	position:absolute;
	left:0px;
	top:0px;
	padding-left:5px;
	padding-right:5px;
	background-color:#000066;
	color:#FFFFFF;
	border:1px solid #CCCCCC;
	font-weight:bold;
	display:none;
}
{/literal}
</style>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <!-- crmv@30683 -->
<tbody><tr>
        <td valign="top"></td>  
        <td class="showPanelBg" style="padding: 5px;" valign="top" width="100%"> <!-- crmv@30683 -->
	<div align=center>

	
				{include file="SetMenu.tpl"}
				{include file='Buttons_List.tpl'} {* crmv@30683 *} 
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'ico-roles.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_ROLES}" width="48" height="48" border=0 title="{$MOD.LBL_ROLES}"></td>
					<td class=heading2 valign=bottom><b> {$MOD.LBL_SETTINGS}  > {$MOD.LBL_ROLES}</b></td> <!-- crmv@30683 -->
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_ROLE_DESCRIPTION}</td>
				</tr>
				</table>
				
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>
				
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
					<tr>
						<td class="big"><strong>{$MOD.LBL_ROLE_HIERARCHY_TREE}</strong></td>
						<td class="small" align=right>&nbsp;</td>
					</tr>
					</table>

					<div id='RoleTreeFull'  onMouseMove="displayCoords(event)"> 
       			        {include file='RoleTree.tpl'}
                	</div>
					
				</td>
				</tr>
				</table>
			
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
</tbody>
</table>
	<div id="Drag_content">&nbsp;</div>

<script language="javascript" type="text/javascript">
{literal}
	var hideAll = false;
	var parentId = "";
	var parentName = "";
	var childId ="NULL";
	var childName = "NULL";
	
	function displayCoords(event) {
		var move_Element = document.getElementById('Drag_content').style;
		if(!event) {
			move_Element.left = e.pageX +'px' ;
			move_Element.top = e.pageY+10 + 'px';	
		} else {
			move_Element.left = event.clientX +'px' ;
			move_Element.top = event.clientY+10 + 'px';	
		}
	}
	
	function fnRevert(e) {
		if(e.button == 2) {
			document.getElementById('Drag_content').style.display = 'none';
			hideAll = false;
			parentId = "Head";
			parentName = "DEPARTMENTS";
			childId ="NULL";
			childName = "NULL";
		}
	}

	function get_parent_ID(obj,currObj)	{
		var leftSide = findPosX(obj);
		var topSide = findPosY(obj);
		var move_Element = document.getElementById('Drag_content');
		
		childName = document.getElementById(currObj).innerHTML;
		childId = currObj;
		move_Element.innerHTML = childName;
		move_Element.style.left = leftSide + 15 + 'px';
		move_Element.style.top = topSide + 15+ 'px';
		move_Element.style.display = 'block';
		hideAll = true;	
	}
	
	function put_child_ID(currObj) {
		var move_Element = $('Drag_content');
		parentName  = $(currObj).innerHTML;
		parentId = currObj;
		move_Element.style.display = 'none';
		hideAll = false;	
		if(childId == "NULL") {
			parentId = parentId.replace(/user_/gi,'');
			window.location.href="index.php?module=Settings&action=RoleDetailView&parenttab=Settings&roleid="+parentId;
		} else {
			childId = childId.replace(/user_/gi,'');
			parentId = parentId.replace(/user_/gi,'');
			new Ajax.Request(
				'index.php',
				{
					queue: {position: 'end', scope: 'command'},
					method: 'post',
					postBody: 'module=Users&action=UsersAjax&file=RoleDragDrop&ajax=true&parentId='+parentId+'&childId='+childId,
					onComplete: function(response) {
						if(response.responseText != alert_arr.ROLE_DRAG_ERR_MSG) {
							$('RoleTreeFull').innerHTML=response.responseText;
							hideAll = false;
							parentId = "";
							parentName = "";
							childId ="NULL";
							childName = "NULL";
						} else {
							alert(response.responseText);
						}
					}
				}
			);
		}
	}

	function fnVisible(Obj) {
		if(!hideAll) {
			jQuery('#'+Obj).css('visibility', 'visible');
		}
	}

	function fnInVisible(Obj) {
		jQuery('#'+Obj).css('visibility', 'hidden');
	}

	function showhide(argg,imgId) {
		var harray=argg.split(",");
		var harrlen = harray.length;	

		for(var i=0; i<harrlen; i++) {
			var x=document.getElementById(harray[i]).style;
			if (x.display=="none") {
				x.display="block";
				jQuery('#'+imgId).text('indeterminate_check_box');
			} else {
				x.display="none";
				jQuery('#'+imgId).text('add_box');
			}
		}
	}
	
{/literal}
</script>

{********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************}
 
<script language="JavaScript" type="text/javascript" src="{"modules/PriceBooks/PriceBooks.js"|resourcever}"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/ListView.js"|resourcever}"></script> {* crmv@104568 *}
<script type="text/javascript">
{literal}
	function editProductListPrice(id,pbid,price,module) {
        $("status").style.display="inline";
        new Ajax.Request(
			'index.php',
			{queue: {position: 'end', scope: 'command'},
            method: 'post',
            postBody: 'action='+module+'Ajax&file=EditListPrice&return_action=DetailView&return_module=PriceBooks&module='+module+'&record='+id+'&pricebook_id='+pbid+'&listprice='+price,
            onComplete: function(response) {
            	$("status").style.display="none";
				$("editlistprice").innerHTML= response.responseText;
			}
		});
	}

	function gotoUpdateListPrice(id,pbid,proid,module) {
		$("status").style.display="inline";
		$("roleLay").style.display = "none";
        var listprice = $("list_price").value;

		new Ajax.Request(
			'index.php',
			{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module='+module+'&action='+module+'Ajax&file=UpdateListPrice&ajax=true&return_action=CallRelatedList&return_module=PriceBooks&record='+id+'&pricebook_id='+pbid+'&product_id='+proid+'&list_price='+listprice,
			onComplete: function(response) {
				$("status").style.display="none";
				reloadTurboLift('PriceBooks', id, module);	//crmv@55227 crmv@55265
			}
		});
	}
{/literal}
</script>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr><td>
		<div id="RLContents">
			{include file='RelatedListContents.tpl'}
		</div>
	</td></tr>
</table>

<script type="text/javascript">
function OpenWindow(url)
{ldelim}
	openPopUp('xAttachFile',this,url,'attachfileWin',380,375,'menubar=no,toolbar=no,location=no,status=no,resizable=no');
{rdelim}
</script>
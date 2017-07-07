/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

 function sendNewsletter(record,mode) {
 	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Newsletter&action=NewsletterAjax&file=SendEmail&record='+record+'&mode='+mode,
			onComplete: function(response) {
				$("status").style.display="none";
				alert(response.responseText);
			}
		}
	);
 }

function filter_statistics_newsletter(record,obj) {
 	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Campaigns&action=CampaignsAjax&file=Statistics&ajax=true&record='+record+'&statistics_newsletter='+obj.value,
			onComplete: function(response) {
				$("status").style.display="none";
				jQuery("#StatisticChar").attr("src", "cache/charts/StatisticsChart.png?"+(new Date()).getTime()); // crmv@38600
				getObj('RLContents').innerHTML = response.responseText;
				var scriptTags = $("RLContents").getElementsByTagName("script");
				for(var i = 0; i< scriptTags.length; i++){
					var scriptTag = scriptTags[i];
					eval(scriptTag.innerHTML);
				}
			}
		}
	);
}

//crmv@80155
function previewTemplate(record,templateid,templatename)
{
	window.document.location.href = "index.php?module=Newsletter&action=NewsletterAjax&file=widgets/TemplateEmailPreview&record="+record+"&templateid="+templateid+"&templatename="+templatename;
}
//crmv@80155e

function submittemplate(record,templateid,templatename)
{
    res = getFile("index.php?module=Newsletter&action=NewsletterAjax&file=widgets/TemplateEmailSave&record="+record+"&templateid="+templateid);
	parent.getObj('templateemail_name').value = templatename;
	closePopup();
}

function freezeBackground() {
    var oFreezeLayer = document.createElement("DIV");
    oFreezeLayer.id = "freeze";
    oFreezeLayer.className = "small veil_new";

     if (browser_ie) oFreezeLayer.style.height = (document.body.offsetHeight + (document.body.scrollHeight - document.body.offsetHeight)) + "px";
     else if (browser_nn4 || browser_nn6) oFreezeLayer.style.height = document.body.offsetHeight + "px";

    oFreezeLayer.style.width = "100%";
    document.body.appendChild(oFreezeLayer);
    document.getElementById('confId').style.display = 'block';
    hideSelect();
}

//crmv@38592 crmv@43611
function previewNewsletter(record, newwindow) {
	var url = "index.php?module=Newsletter&action=NewsletterAjax&file=ShowPreview&record="+record;
	if (newwindow) {
		window.open(url, '_blank');
	} else {
		openPopup(url,"ShowPreview","width=750,height=602,menubar=no,toolbar=no,location=no,status=no,resizable=no,scrollbars=yes");
	}
}
//crmv@38592e

function openNewsletterWizard(module, id) {
	var url = "index.php?module=Campaigns&action=CampaignsAjax&file=NewsletterWizard&from_module="+encodeURIComponent(module)+'&from_record='+id;
	openPopup(url,"NewsletterWizard","width=750,height=602,menubar=no,toolbar=no,location=no,status=no,resizable=no,scrollbars=yes");
}
//crmv@43611e

//crmv@55961
function lockUnlockReceivingNewsletter(record, mode) {
	jQuery("#vtbusy_info").show();
	jQuery.ajax({
		url: 'index.php?module=Newsletter&action=NewsletterAjax&file=DetailViewAjax&ajxaction=LOCKRECEIVINGNEWSLETTER&record='+record+'&mode='+mode,
		type: 'POST',
		success: function(data) {
			jQuery("#vtbusy_info").hide();
			if (mode == 'lock') {
				jQuery("#receivingNewsletterButton2").show();
				jQuery("#receivingNewsletterButton1").hide();
			} else {
				jQuery("#receivingNewsletterButton2").hide();
				jQuery("#receivingNewsletterButton1").show();
			}
		}
	});
}
//crmv@55961e
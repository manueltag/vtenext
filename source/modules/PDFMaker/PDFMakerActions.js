/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

function getSelectedTemplates()
{
    var selectedColumnsObj=getObj("use_common_template");
    var selectedColStr = "";
    for (i=0;i<selectedColumnsObj.options.length;i++)
    {
    	 if(selectedColumnsObj.options[i].selected)
    	 {
             selectedColStr += selectedColumnsObj.options[i].value + ";";
         }
    }
    
    return selectedColStr;
}

function getPDFDocDivContent(rootElm,module,id)
{
    $("vtbusy_info").style.display="inline";
    new Ajax.Request(
            'index.php',
            {queue: {position: 'end', scope: 'command'},
                    method: 'post',
                    postBody: "module=PDFMaker&return_module="+module+"&action=PDFMakerAjax&file=docSelect&return_id="+id,
                    onComplete: function(response) 
                    {
                      getObj('PDFDocDiv').innerHTML=response.responseText;
                      PDFDocDiv.show();	//crmv@50159
                      
                      var PDFDoc = document.getElementById('PDFDocDiv');
                      var PDFDocHandle = document.getElementById('PDFDocDivHandle');
                      Drag.init(PDFDocHandle,PDFDoc);
                      $("vtbusy_info").style.display="none";                         						  
                    }
            }
    );
}

function getPDFBreaklineDiv(rootElm,id)
{
    $("vtbusy_info").style.display="inline";
    new Ajax.Request(
            'index.php',
            {queue: {position: 'end', scope: 'command'},
                    method: 'post',
                    postBody: "module=PDFMaker&action=PDFMakerAjax&file=breaklineSelect&return_id="+id,
                    onComplete: function(response) 
                    {
                      getObj('PDFBreaklineDiv').innerHTML=response.responseText;
                      //fnvshobj(rootElm,'PDFBreaklineDiv');
                      var PDFBreakline = document.getElementById('PDFBreaklineDiv');
                      PDFBreakline.show();
                      
                      var PDFBreaklineHandle = document.getElementById('PDFBreaklineDivHandle');
                      Drag.init(PDFBreaklineHandle,PDFBreakline);
                      $("vtbusy_info").style.display="none";                         						  
                    }
            }
    );
}

function getPDFImagesDiv(rootElm,id)
{
    $("vtbusy_info").style.display="inline";
    new Ajax.Request(
            'index.php',
            {queue: {position: 'end', scope: 'command'},
                    method: 'post',
                    postBody: "module=PDFMaker&action=PDFMakerAjax&file=imagesSelect&return_id="+id,
                    onComplete: function(response) 
                    {
                      getObj('PDFImagesDiv').innerHTML=response.responseText;
                      //fnvshobj(rootElm,'PDFImagesDiv');
                      var PDFImages = document.getElementById('PDFImagesDiv');
                      PDFImages.show();
                      
                      var PDFImagesHandle = document.getElementById('PDFImagesDivHandle');
                      Drag.init(PDFImagesHandle,PDFImages);
                      $("vtbusy_info").style.display="none";                         						  
                    }
            }
    );
}

function sendPDFmail(module,idstrings)
{
    var smodule = document.DetailView.module.value;
	var record = document.DetailView.record.value;
	
	//crmv@48915
    var result = '';
    jQuery.ajax({
		'url': 'index.php',
		'type': 'POST',
		'data': "module=PDFMaker&return_module="+module+"&action=PDFMakerAjax&file=mailSelect&idlist="+idstrings,
		'async': false,
		success: function(data) {
			result = data;
		}
	});
	if(result == "Mail Ids not permitted" || result == "No Mail Ids")
	{
		var emailhref = 'module=PDFMaker&action=PDFMakerAjax&file=SendPDFMail&language='+document.getElementById('template_language').value+'&record='+record+'&relmodule='+module+'&commontemplateid='+getSelectedTemplates();
        jQuery.ajax({
			'url': 'index.php',
			'type': 'POST',
			'data': emailhref,
			'async': false,
			success: function(data2) {
				// crmv@106363
				var url = 'index.php?module=Emails&action=EmailsAjax&file=EditView&pmodule='+module+
					'&pid='+idstrings+
					'&language='+jQuery('#template_language').val()+
					'&sendmail=true'+
					'&attachment='+encodeURIComponent(data2+'.pdf');
				window.open(url,'_blank');
				// crmv@106363e
			},
		});
	}	
	else
	{
		getObj('sendpdfmail_cont').innerHTML=result;
		var PDFMail = document.getElementById('sendpdfmail_cont');
		var PDFMailHandle = document.getElementById('sendpdfmail_cont_handle');
		Drag.init(PDFMailHandle,PDFMail);
		
		var objOffset = jQuery(PDFMail).offset();
		var leftSide = objOffset.left;
		var widthM = jQuery(PDFMail).width();
		var getVal = eval(leftSide) + eval(widthM);
		if (getVal  > document.body.clientWidth) {
			leftSide = leftSide - (getVal - document.body.clientWidth) - 34;
			jQuery(PDFMail).offset({ top: objOffset.top, left: leftSide });
		}
	}
	//crmv@48915e
}

function validate_sendPDFmail(idlist,module)
{
    var smodule = document.DetailView.module.value;
	var record = document.DetailView.record.value;
	var j=0;
	var chk_emails = document.SendPDFMail.elements.length;
	var oFsendmail = document.SendPDFMail.elements
	email_type = new Array();
	for(var i=0 ;i < chk_emails ;i++)
	{
		if(oFsendmail[i].type != 'button')
		{
			if(oFsendmail[i].checked != false)
			{
				email_type [j++]= oFsendmail[i].value;
			}
		}
	}
	if(email_type != '')
	{
		$("vtbusy_info").style.display="inline";
        var field_lists = email_type.join(':');
		//crmv@48915
		var emailhref = 'module=PDFMaker&action=PDFMakerAjax&file=SendPDFMail&language='+document.getElementById('template_language').value+'&record='+record+'&relmodule='+smodule+'&commontemplateid='+getSelectedTemplates(); 
        jQuery.ajax({
			'url': 'index.php',
			'type': 'POST',
			'data': emailhref,
			'async': false,
			success: function(data) {
				window.open('index.php?module=Emails&action=EmailsAjax&file=EditView&pmodule='+module+'&idlist='+idlist+'&field_lists='+field_lists+'&language='+document.getElementById('template_language').value+'&sendmail=true&attachment='+encodeURIComponent(data)+'.pdf'+'&pid='+record,'_blank');  //crmv@58554 crmv@107249
				$("vtbusy_info").style.display="none";
			},
		});
		//crmv@48915e
        fninvsh('roleLay2');
		return true;
	}
	else
	{
		alert(alert_arr.SELECT_MAILID);
	}
}

function validatePDFDocForm()
{
    if(document.PDFDocForm.notes_title.value=='')
    {
        alert_label = getObj('alert_doc_title').innerHTML;
		alert(alert_label);
        return false;
    }
    else{
        document.PDFDocForm.template_ids.value=getSelectedTemplates();
        document.PDFDocForm.language.value=document.getElementById('template_language').value;
        return true;
    }
    
}

function savePDFBreakline()
{     
    var record = document.DetailView.record.value;
	$("vtbusy_info").style.display="inline";
    var frm = document.PDFBreaklineForm;
    var url = 'module=PDFMaker&action=PDFMakerAjax&file=SavePDFBreakline&pid='+record+'&breaklines='; 
    var url_suf = '';
    var url_suf2 = '';
    if(frm != 'undefined')
    {
        for(i=0; i<frm.elements.length; i++)
        {
            if(frm.elements[i].type == 'checkbox')
            {
                if(frm.elements[i].name == 'show_header' || frm.elements[i].name == 'show_subtotal')
                {
                    if(frm.elements[i].checked)
                        url_suf2 += '&'+frm.elements[i].name+'=true';
                    else
                        url_suf2 += '&'+frm.elements[i].name+'=false';
                }                
                else
                {
                    if(frm.elements[i].checked)
                        url_suf += frm.elements[i].name+'|';
                }                
            }
        }
        url+=url_suf+url_suf2;            
        new Ajax.Request(
			'index.php',
			{queue: {position: 'end', scope: 'command'},
		        method: 'post',
		        postBody: url,
		        onComplete: function(response) 
		        {                               
		          jQuery('#PDFBreaklineDiv').hide();	//crmv@55211
		          $("vtbusy_info").style.display="none";                         						  
		        }
			}
        );
    }                     
}

function savePDFImages()
{     
    var record = document.DetailView.record.value;
	$("vtbusy_info").style.display="inline";
    var frm = document.PDFImagesForm;
    var url = 'module=PDFMaker&action=PDFMakerAjax&file=SavePDFImages&pid='+record; 
    var url_suf = '';    
    if(frm != 'undefined')
    {
        for(i=0; i<frm.elements.length; i++)
        {
            if(frm.elements[i].type == 'radio')
            {
                if(frm.elements[i].checked)
                {
                    url_suf+='&'+frm.elements[i].name+'='+frm.elements[i].value;
                }    
            }
            else if(frm.elements[i].type == 'text')
            {
                url_suf+='&'+frm.elements[i].name+'='+frm.elements[i].value;    
            }
        }
        
        url+=url_suf;            
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: url,
                        onComplete: function(response) 
                        { 
                          fninvsh('PDFImagesDiv');                              
                          $("vtbusy_info").style.display="none";                         						  
                        }
                }
        );
    }                     
}

function checkIfAny()
{
    var frm = document.PDFBreaklineForm;
    if(frm != 'undefined')
    {
        var j=0;
        for(i=0; i<frm.elements.length; i++)
        {
            if(frm.elements[i].type == 'checkbox' && frm.elements[i].name != 'show_header' && frm.elements[i].name != 'show_subtotal' )
            {
                if(frm.elements[i].checked)
                {
                    j++;
                }    
            }            
        }
        if(j==0)
        {
            frm.show_header.checked=false;
            frm.show_subtotal.checked=false;
            frm.show_header.disabled=true;
            frm.show_subtotal.disabled=true;
        }
        else
        {
            frm.show_header.disabled=false;
            frm.show_subtotal.disabled=false;
        }
    }     
}

function getPDFListViewPopup2(srcButt,module)
{       
    if (document.getElementById("PDFListViewDiv") == undefined)
    {
        var newdiv = document.createElement('div');
        newdiv.setAttribute('id','PDFListViewDiv');
        document.body.appendChild(newdiv);
    }
    //crmv@17889
    var select_options = get_real_selected_ids(module);
	if (select_options.substr('0','1')==";")
		select_options = select_options.substr('1');
	//crmv@17889e
    var x = select_options.split(";");
    var count = x.length;
    //crmv@27096
    count = count-1;
    if (count < 1)
    //crmv@27096e
    {
        alert(alert_arr.SELECT);
        return false;   
    }
    $('status').show();
    new Ajax.Request(
            'index.php',
            {queue: {position: 'end', scope: 'command'},
                    method: 'post',
                    postBody: "module=PDFMaker&return_module="+module+"&action=PDFMakerAjax&file=listviewSelect",	//crmv@27096
                    onComplete: function(response) 
                    {
                      getObj('PDFListViewDiv').innerHTML=response.responseText;
					  showFloatingDiv('PDFListViewDivCont', srcButt);
                      
                      $('status').hide();                         						  
                    }
            }
    );    
}
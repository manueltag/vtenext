/*********************************************************************************

** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

/* crmv@82831 */

//crmv@add ajax control
var ajaxcall_list = null;
var ajaxcall_count = null;
var basic_search_submitted = false;
var advance_search_submitted = false;
var grid_search_submitted = false;	//crmv@3084m

/* crmv@30967 */
var typeofdata = new Array();
typeofdata['E'] = ['c','e','n','s','ew','k'];	//crmv@48693
typeofdata['V'] = ['c','e','n','s','ew','k'];	//crmv@48693
typeofdata['N'] = ['e','n','l','g','m','h'];
typeofdata['NN'] = ['e','n','l','g','m','h'];
typeofdata['T'] = ['e','n','l','g','m','h'];
typeofdata['I'] = ['e','n','l','g','m','h'];
typeofdata['C'] = ['e','n'];
typeofdata['DT'] = ['e','n','l','g','m','h'];
typeofdata['D'] = ['e','n','l','g','m','h'];
var fLabels = new Array();
if (typeof(alert_arr) !== 'undefined') {
	fLabels['e'] = alert_arr.EQUALS;
	fLabels['n'] = alert_arr.NOT_EQUALS_TO;
	fLabels['s'] = alert_arr.STARTS_WITH;
	fLabels['ew'] = alert_arr.ENDS_WITH;
	fLabels['c'] = alert_arr.CONTAINS;
	fLabels['k'] = alert_arr.DOES_NOT_CONTAINS;
	fLabels['l'] = alert_arr.LESS_THAN;
	fLabels['g'] = alert_arr.GREATER_THAN;
	fLabels['m'] = alert_arr.LESS_OR_EQUALS;
	fLabels['h'] = alert_arr.GREATER_OR_EQUALS;
}
/* crmv@30967e */

//crmv@add ajax control end
// MassEdit Feature
function massedit_togglediv(curTabId,total){

   for(var i=0;i<total;i++){
	tagName = $('massedit_div'+i);
	tagName1 = $('tab'+i)
	tagName.style.display = 'none';
	tagName1.className = 'dvtUnSelectedCell';
   }

   tagName = $('massedit_div'+curTabId);
   tagName.style.display = 'block';
   tagName1 = $('tab'+curTabId)
   tagName1.className = 'dvtSelectedCell';
}

function massedit_initOnChangeHandlers() {
	if (checkJSOverride(arguments)) return callJSOverride(arguments);
	
	//crmv@62661
	jQuery('form#massedit_form :input').bind('change onchange',function(e){
		jQuery('form#massedit_form  #'+jQuery(this).attr('name')+'_mass_edit_check').prop('checked',true);
	});
	/*
	var form = document.getElementById('massedit_form');
	// Setup change handlers for input boxes
	var inputs = form.getElementsByTagName('input');
	for(var index = 0; index < inputs.length; ++index) {
		var massedit_input = inputs[index];
		// TODO Onchange on readonly and hidden fields are to be handled later.
		massedit_input.onchange = function() {
			var checkbox = document.getElementById(this.name + '_mass_edit_check');
			if(checkbox) checkbox.checked = true;
		}
	}
	// Setup change handlers for select boxes
	var selects = form.getElementsByTagName('select');
	for(var index = 0; index < selects.length; ++index) {
		var massedit_select = selects[index];
		if (massedit_select.name == "assigntype" || massedit_select.name == "parent_type" ) continue;	//crmv@34104 //crmv@37430
		massedit_select.onchange = function() {
			var checkbox = document.getElementById(this.name + '_mass_edit_check');
			if(checkbox) checkbox.checked = true;
		}
	}
	*/
	//crmv@62661 e
}
//crmv@fix mass_edit
function mass_edit(obj,divid,module,parenttab) {
	var idstring = get_real_selected_ids(module);
	if (idstring.substr('0','1')==";")
		idstring = idstring.substr('1');
	var idarr = idstring.split(';');
	var count = idarr.length;
	var xx = count-1;
	if (idstring == "" || idstring == ";" || idstring == 'null')
	{
		vtealert(alert_arr.SELECT);
		return false;
	}
	else {
		//crmv@27096 crmv@91571
		var doEnqueue = false,
			enqueue = getFile("index.php?module="+encodeURIComponent(module)+"&action="+encodeURIComponent(module+'Ajax')+"&parenttab="+encodeURIComponent(parenttab)+"&file=MassEdit&mode=ajax&check_count=true");
		enqueue = enqueue.split('###');
		if (enqueue[0] == 'enqueue') {
			alert(alert_arr.LBL_MASS_EDIT_ENQUEUE.replace('{max_records}', enqueue[1]));
			doEnqueue = true;
		}
		mass_edit_formload(idstring,module,parenttab,doEnqueue);
		//crmv@27096e crmv@91571e
	}
	showFloatingDiv(divid,obj);
}
//crmv@fix mass_edit end
function mass_edit_formload(idstring,module,parenttab,enqueue) {	//crmv@27096 crmv@91571
	if(typeof(parenttab) == 'undefined') parenttab = '';
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
	    	method: 'post',
			postBody:"module="+encodeURIComponent(module)+"&action="+encodeURIComponent(module+'Ajax')+"&parenttab="+encodeURIComponent(parenttab)+"&file=MassEdit&mode=ajax&enqueue="+(enqueue ? 'true' : 'false'),	//crmv@27096 crmv@91571
				onComplete: function(response) {
                	$("status").style.display="none";
               	    var result = response.responseText;
                    $("massedit_form_div").innerHTML= result;
					//$("massedit_form")["massedit_recordids"].value = idstring;	//crmv@27096
					$("massedit_form")["massedit_module"].value = module;
					//crmv@29190
					// crmv@82831 - add a little delay to have time for the DOM to be ready
					setTimeout(function() {
						var scriptTags = $("massedit_form_div").getElementsByTagName("script");
						for(var i=0; i<scriptTags.length; i++){
							var scriptTag = scriptTags[i];
							eval(scriptTag.innerHTML);
							if (scriptTag.id == 'massedit_javascript') {
								// Updating global variables
								fieldname = mass_fieldname;
								for(var j=0;j<fieldname.length;j++){
									calendar_jscript = $('massedit_calendar_'+fieldname[j]);
									if(calendar_jscript){
										eval(calendar_jscript.innerHTML);
									}
								}
								fieldlabel = mass_fieldlabel;
								fielddatatype = mass_fielddatatype;
								fielduitype = mass_fielduitype; // crmv@83877
								count = mass_count;
							}
						}
						eval($("massedit_form_div"));
                    }, 10);
					// crmv@29190e
					// crmv@82831e
				}
		}
	);
}
function mass_edit_fieldchange(selectBox) {
	var oldSelectedIndex = selectBox.oldSelectedIndex;
	var selectedIndex = selectBox.selectedIndex;

	if($('massedit_field'+oldSelectedIndex)) $('massedit_field'+oldSelectedIndex).style.display='none';
	if($('massedit_field'+selectedIndex)) $('massedit_field'+selectedIndex).style.display='block';

	selectBox.oldSelectedIndex = selectedIndex;
}

function mass_edit_save(){
	var masseditform = $("massedit_form");
	var module = masseditform["massedit_module"].value;
	var viewid = document.getElementById("viewname").options[document.getElementById("viewname").options.selectedIndex].value;
	var searchurl = document.getElementById("search_url").value;

	var urlstring =
		"module="+encodeURIComponent(module)+"&action="+encodeURIComponent(module+'Ajax')+
		"&return_module="+encodeURIComponent(module)+"&return_action=ListView"+
		"&mode=ajax&file=MassEditSave&viewname=" + viewid ;//+"&"+ searchurl;

	fninvsh("massedit");
	new Ajax.Request(
		"index.php",
		{queue:{position:"end", scope:"command"},
			method:"post",
			postBody:urlstring,
			onComplete:function (response) {
				$("status").style.display = "none";
				var result = response.responseText.split("&#&#&#");
				$("ListViewContents").innerHTML = result[2];
				if (result[1] != "") {
					alert(result[1]);
				}
				$("basicsearchcolumns").innerHTML = "";
			}
		}
	);

}
function ajax_mass_edit() {
	$("status").style.display = "inline";

	var masseditform = $("massedit_form");
	var module = masseditform["massedit_module"].value;

	var viewid = document.getElementById("viewname").options[document.getElementById("viewname").options.selectedIndex].value;
	var idstring = masseditform["massedit_recordids"].value;
	var searchurl = document.getElementById("search_url").value;
	var tplstart = "&";
	if (gstart != "") { tplstart = tplstart + gstart; }

	var masseditfield = masseditform['massedit_field'].value;
	var masseditvalue = masseditform['massedit_value_'+masseditfield].value;

	var urlstring =
		"module="+encodeURIComponent(module)+"&action="+encodeURIComponent(module+'Ajax')+
		"&return_module="+encodeURIComponent(module)+
		"&mode=ajax&file=MassEditSave&viewname=" + viewid +
		"&massedit_field=" + encodeURIComponent(masseditfield) +
		"&massedit_value=" + encodeURIComponent(masseditvalue) +
	   	"&idlist=" + idstring + searchurl;

	fninvsh("massedit");

	new Ajax.Request(
		"index.php",
		{queue:{position:"end", scope:"command"},
			method:"post",
			postBody:urlstring,
			onComplete:function (response) {
				$("status").style.display = "none";
				var result = response.responseText.split("&#&#&#");
				$("ListViewContents").innerHTML = result[2];
				if (result[1] != "") {
					alert(result[1]);
				}
				$("basicsearchcolumns").innerHTML = "";
			}
		}
	);
}

// END

function change(obj,divid)
{
//crmv@7216
		var select_options  =  document.getElementsByName('selected_id');
		var x = select_options.length;
		var viewid =getviewId();
		idstring = "";
        xx = 0;
        for(i = 0; i < x ; i++)
        {
        	if(select_options[i].checked)
            {
            	idstring = select_options[i].value +";"+idstring
                xx++
            }
        }
		idlen=idstring.length;
		str=idstring.substr(1,(idlen-2));
		idarr=str.split(";");
      xx=idarr.length;
//crmv@7216e
        if (xx != 0 && idstring !="" && idstring !=";" && idstring != 'null')
        {
            document.getElementById('selected_ids').value=idstring;
        }
        else
        {
            alert(alert_arr.SELECT);
            return false;
        }
  fnvshobj(obj,divid);
}
function getviewId()
{
        if(isdefined("viewname"))
        {
                var oViewname = document.getElementById("viewname");
                var viewid = oViewname.options[oViewname.selectedIndex].value;
        }
        else
        {
                var viewid ='';
        }
        return viewid;
}
var gstart='';
//crmv@fix massdelete
//crmv@30967
function massDelete(module) {

	var idstring = get_real_selected_ids(module);
	if (idstring.substr('0', '1') == ";")
		idstring = idstring.substr('1');
	var idarr = idstring.split(';');
	var count = idarr.length;
	var xx = count - 1;
	var viewid = getviewId();
	if (idstring == "" || idstring == ";" || idstring == 'null') {
		vtealert(alert_arr.SELECT);
		return false;
	} else {
		if (module == "Accounts") {
			if (xx == 1) var alert_str = sprintf(alert_arr.DELETE_ACCOUNT, xx);
			else var alert_str = sprintf(alert_arr.DELETE_ACCOUNTS, xx);
		} else if (module == "Vendors") {
			if (xx == 1) var alert_str = sprintf(alert_arr.DELETE_VENDOR, xx);
			else var alert_str = sprintf(alert_arr.DELETE_VENDORS, xx);
		} else {
			if (xx == 1) var alert_str = sprintf(alert_arr.DELETE_RECORD, xx);
			else var alert_str = sprintf(alert_arr.DELETE_RECORDS, xx);
		}
		vteconfirm(alert_str, function(yes) {
			if (yes) {
				var postbody = "module=Users&action=massdelete&return_module="
						+ module + "&" + gstart + "&viewname=" + viewid; // crmv@27096
				var postbody2 = "module=" + module + "&action=" + module
						+ "Ajax&file=ListView&ajax=true&" + gstart + "&viewname="
						+ viewid;

				$("status").style.display = "inline";
				new Ajax.Request('index.php', {
					queue : {
						position : 'end',
						scope : 'command'
					},
					method : 'post',
					postBody : postbody,
					onComplete : function(response) {
						$("status").style.display = "none";
						result = response.responseText.split('&#&#&#');
						$("ListViewContents").innerHTML = result[2];
						if (result[1] != '')
							vtealert(result[1]);

						$('basicsearchcolumns').innerHTML = '';
						update_navigation_values(postbody2);
					}
				});
			}
		});
	}
}
//crmv@30967e
//crmv@fix massdelete end
//crmv@customview fix
function showDefaultCustomView(selectView,module,parenttab,folderid,file) // crmv@30967
{
	//crmv@91082
	if(!SessionValidator.check()) {
		SessionValidator.showLogin();
		return false;
	}
	//crmv@91082e
	
	$("status").style.display="inline";
	if (ajaxcall_list){
		ajaxcall_list.abort();
	}
	if (ajaxcall_count){
		ajaxcall_count.abort();
	}
	//crmv@7634
	var userid_url = ""
	var userid_obj = getObj("lv_user_id");
	if(userid_obj != null) {
		//crmv@29682
		if (navigator.appName == 'Microsoft Internet Explorer') {
			if (typeof(userid_obj.options) != 'undefined') {
				userid_url = "&lv_user_id="+userid_obj.options[userid_obj.options.selectedIndex].value;
			}else {
				userid_url = "&lv_user_id="+userid_obj.item(0).options[userid_obj.item(0).options.selectedIndex].value;
			}
		} else {
			userid_url = "&lv_user_id="+userid_obj.options[userid_obj.options.selectedIndex].value;
		}
		//crmv@29682e
	}
	override_orderby="";
	if(selectView == null)
		selectView = getObj("viewname")
	else
		override_orderby="&override_orderby=true";
	//crmv@7634e

	//crmv@OPER6288
	var viewName = selectView.options[selectView.options.selectedIndex].value;
	if (typeof(file) == "undefined") var file = 'ListView';
	postbody="module="+module+"&action="+module+"Ajax&file="+file+"&ajax=true&changecustomview=true&start=1&viewname="+viewName+"&parenttab="+parenttab+userid_url+override_orderby; //crmv@7634
	if (folderid != undefined && folderid != '') postbody += '&folderid='+folderid; // crmv@30967

	// crmv@31245 crmv@43835
	if(isdefined('basic_search_text')) {
		var searchrest = jQuery.data(document.getElementById('basic_search_text'), 'restored');
		var searchval = jQuery('#basic_search_text').val();
		if (searchrest == false && searchval != '') {
			postbody += '&searchtype=BasicSearch&search_field=&query=true&search_text='+encodeURIComponent(searchval);
		}
	} else if(isdefined('search_url')) {
		postbody += $('search_url').value;
	}
	//crmv@31245e crmv@43835e crmv@OPER6288e
	
	if (isdefined('append_url')) {
		postbody += $('append_url').value;
	}

	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: postbody,
			onComplete: function(response) {
				$("status").style.display="none";
				result = response.responseText.split('&#&#&#');
				//crmv@OPER6288
				if (file == 'KanbanView') {
					$("KanbanViewContents").innerHTML= result[2];
					if(result[1] != '')
						alert(result[1]);
					eval($('init_kanban_script').innerHTML);
				} else {
					$("ListViewContents").innerHTML= result[2];
					if(result[1] != '')
						alert(result[1]);
					//crmv@31245
					$('basicsearchcolumns').innerHTML = '';
					//crmv@31245e
					update_navigation_values(postbody);
					$('Buttons_List_3_Container').innerHTML = ''; //crmv@24604
					ModNotificationsCommon.setFollowImgCV(viewName);	//crmv@29617
					eval($('gridsearch_script').innerHTML);	//crmv@43835
				}
				//crmv@OPER6288e
			}
		}
	);
}
//crmv@customview fix end
//crmv@pulldown list
function showMoreEntries(selectView,module,folderid) // crmv@30967
{
        $("status").style.display="inline";
    	if (ajaxcall_list){
    		ajaxcall_list.abort();
    	}
    	if (ajaxcall_count){
    		ajaxcall_count.abort();
    	}
        var viewCounts = selectView.options[selectView.options.selectedIndex].value;
        var viewid =getviewId();
        $("status").style.display="inline";
        if(isdefined('search_url'))
                urlstring = $('search_url').value;
        else
                urlstring = '';
        if (isdefined('selected_ids'))
        	urlstring += "&selected_ids=" + document.getElementById('selected_ids').value;
        if (isdefined('all_ids'))
        	urlstring += "&all_ids=" + document.getElementById('all_ids').value;
        if (isdefined('modulename'))
        	var modulename=document.getElementById('modulename').value;
        else
        	modulename = '';
        postbody = "module="+module+"&modulename="+modulename+"&action="+module+"Ajax&file=ListView&start=1&ajax=true&changecount=true"+urlstring+"&counts="+viewCounts;
        if (folderid != undefined && folderid != '') postbody += '&folderid='+folderid; // crmv@30967
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody:postbody,
                        onComplete: function(response) {
                                $("status").style.display="none";
                                result = response.responseText.split('&#&#&#');
                                $("ListViewContents").innerHTML= result[2];
                                if(result[1] != '')
                                        alert(result[1]);
                                if (module != 'Users' && module != 'Import' && module != 'Notes'){
                                	update_navigation_values(postbody);
                                	$('basicsearchcolumns').innerHTML = '';
                                }
                          }
                }
        );
}
//crmv@pulldown list end
//crmv@add customview popup
function showMoreEntries_popup(selectView,module)
{
        $("status").style.display="inline";
    	if (ajaxcall_list){
    		ajaxcall_list.abort();
    	}
    	if (ajaxcall_count){
    		ajaxcall_count.abort();
    	}
        var viewCounts = selectView.options[selectView.options.selectedIndex].value;
        var viewid =getviewId();
        $("status").style.display="inline";
		popuptype = $('popup_type').value;
		act_tab = $('maintab').value;
		urlstring = '&popuptype='+popuptype;
		urlstring += '&maintab='+act_tab;
		urlstring = urlstring +'&query=true&file=Popup&module='+module+'&action='+module+'Ajax&ajax=true&changecount=true&counts='+viewCounts;
		urlstring +=gethiddenelements();
		urlstring += "&start=1";
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody:urlstring,
                        onComplete: function(response) {
							$("status").style.display="none";
							$("ListViewContents").innerHTML= response.responseText;
							update_navigation_values(urlstring);

							setListHeight(); //crmv@21048m
                        }
                }
        );
}
function showDefaultCustomView_popup(selectView,module,parenttab)
{
	$("status").style.display="inline";
	if (ajaxcall_list){
		ajaxcall_list.abort();
	}
	if (ajaxcall_count){
		ajaxcall_count.abort();
	}
	popupSearchType = '';	//crmv@44854
	//crmv@7634
	if(selectView == null) selectView = getObj("viewname")
	//crmv@7634e
      if(isdefined('search_url'))
    	urlstring = $('search_url').value;
    else
    	urlstring = '';
        var viewName = selectView.options[selectView.options.selectedIndex].value;
        var viewid =getviewId();
        $("status").style.display="inline";
		popuptype = $('popup_type').value;
		act_tab = $('maintab').value;
		urlstring += '&popuptype='+popuptype;
		urlstring += '&maintab='+act_tab;
		urlstring = urlstring +'&query=true&file=Popup&module='+module+'&action='+module+'Ajax&ajax=true&viewname='+viewName+'&changecustomview=true&start=1';
		urlstring +=gethiddenelements();
        new Ajax.Request(
                       'index.php',
                {queue: {position: 'end', scope: 'command'},
                               method: 'post',
                        postBody: urlstring,
                        onComplete: function(response) {
							$("status").style.display="none";
							jQuery("#ListViewContents").html(response.responseText); // crmv@107661
							// don't reload the navigation, it's done in the tpl
							
							setListHeight(); //crmv@21048m
                        }
                }
        );
}
//crmv@add customview popup end

//crmv@74154
function adjustGridValues(urlstring){
	if(urlstring == '') return urlstring;
	
	var returnurlstring = '';
	
	var vars = urlstring.split('&');
	for(var i=0;i<vars.length;i++){
		var pair = vars[i].split("=");
		if(pair[0] == '' && typeof pair[1] == 'undefined') continue;
		
		if(pair[0].indexOf('GridSrch_value') > -1){
			pair[1] = encodeURIComponent(pair[1]);
		}
		
		returnurlstring += pair[0] + "=" + pair[1] + "&";
	}
	
	if (returnurlstring.length > 0){
		returnurlstring = "&"+returnurlstring.substring(0, returnurlstring.length-1); //chop off last "&"
	}
	
	return returnurlstring;
}
//crmv@74154e

//crmv@10759 / fix listview	//crmv@2963m
function getListViewEntries_js(module,url,async,callback) {	//crmv@48471
	
	//crmv@91082
	if(!SessionValidator.check()) {
		SessionValidator.showLogin();
		return false;
	}
	//crmv@91082e
	
	if (async == undefined) async = true;
	if (ajaxcall_list){
		ajaxcall_list.abort();
	}
	if (ajaxcall_count){
		ajaxcall_count.abort();
	}
    var viewid =getviewId();
    $("status").style.display="inline";
	//crmv@74154
    if(isdefined('search_url')){
            urlstring = $('search_url').value;
			urlstring = adjustGridValues(urlstring);
	}
	//crmv@74154e
    else
            urlstring = '';
    if (isdefined('selected_ids'))
    	urlstring += "&selected_ids=" + document.getElementById('selected_ids').value;
    if (isdefined('all_ids'))
    	urlstring += "&all_ids=" + document.getElementById('all_ids').value;
    if (isdefined('modulename'))
    	var modulename=document.getElementById('modulename').value;
    else
    	modulename = '';
    gstart = url;
    postbody = "module="+module+"&modulename="+modulename+"&action="+module+"Ajax&file=ListView&ajax=true&"+url+urlstring;
    
    jQuery.ajax({
		url: 'index.php',
		type: 'POST',
		dataType: 'html',
		data: postbody,
		async: async,
		success: function(data){		
			$("status").style.display="none";
            result = data.split('&#&#&#');
            $("ListViewContents").innerHTML= result[2];
            if(result[1] != '')
				alert(result[1]);
            if (isdefined("basicsearchcolumns"))
            	$('basicsearchcolumns').innerHTML = '';
            if ($('import_flag').value == 1)
           		update_navigation_values(postbody);
           	//crmv@48471
            if (callback != undefined) {
            	callback(module,result);
            }
            //crmv@48471e
		}
	});
}
function update_navigation_values(url,module,async,callback) {	//crmv@48471
	if (async == undefined) async = true;
	$("status").style.display="inline";
	//crmv@27924
	if(url.indexOf('index.php?')>=0){
  		var url_split = url.split('index.php?');
  		var module_var = '';
  		var action_var = '';
  		var url_vars = url_split[1].split('&');
  		for (i=0; i<url_vars.length; i++) {
  			if (url_vars[i].indexOf('module=') != -1) {
				var url_tmp = url_vars[i].split('=');
				if (url_tmp[0] == 'module') {
					module_var = url_tmp[1];
				}
			} else if (url_vars[i].indexOf('action=') != -1) {
				var url_tmp = url_vars[i].split('=');
				if (url_tmp[0] == 'action') {
					action_var = url_tmp[1];
				}
			}
		}
  		url_split[1] = url_split[1].replace('action='+action_var,'action='+module_var+'Ajax&file='+action_var);
  		url_post = url_split[1]+"&calc_nav=true";
 	} else {
  		url_post = url+"&calc_nav=true";
 	}
 	if (module != undefined && url.indexOf("module")<0){
  		url_post = "module="+module+"&action="+module+"Ajax&file=ListView&calc_nav=true";
 	}
 	//crmv@27924e
    if (isdefined('modulename'))
    	var modulename=document.getElementById('modulename').value;
    else
    	modulename = '';
    url_post+="&modulename="+modulename;

    jQuery.ajax({
		url: 'index.php',
		type: 'POST',
		dataType: 'html',
		data: url_post,
		async: async,
		success: function(data){
			result = data.split('&#&#&#');
            res_arr = eval ('('+result[1]+')');
            if (isdefined("nav_buttons"))
            	$("nav_buttons").innerHTML= res_arr['nav_array'];
            if (isdefined("rec_string"))
            	$("rec_string").innerHTML= res_arr['rec_string'];
            if (isdefined("nav_buttons2"))
            	$("nav_buttons2").innerHTML= res_arr['nav_array'];
            if (isdefined("rec_string2"))
            	$("rec_string2").innerHTML= res_arr['rec_string'];
            if (res_arr['permitted']){
             if (isdefined("select_all_button_top"))
             	$("select_all_button_top").style.display = 'inline';
         	if (isdefined("select_all_button_bottom"))
         		$("select_all_button_bottom").style.display = 'inline';
        	}
        	//crmv@29617
        	if (res_arr['reload_notification_count']) {
        		NotificationsCommon.showChangesAndStorage('CheckChangesDiv','CheckChangesImg','ModNotifications');	//crmv@OPER5904
        	}
        	//crmv@29617e
        	if (isdefined("rec_string3"))
            	$("rec_string3").innerHTML= res_arr['rec_string3'];
            $("status").style.display="none";
            //crmv@48471
            if (callback != undefined) {
            	callback(module,result);
            }
            //crmv@48471e
		}
	});
}
//crmv@10759 e
//crmv@2963me

function update_selected_ids(checked,entityid,form)
{
    var idstring = form.selected_ids.value;
    if (idstring == "") idstring = ";";
    var all_ids = form.all_ids.value;
    if (all_ids == 1){
    	if (checked == true)
    		checked = false;
    	else
    		checked = true;
    }
    if (checked == true)
    {
    	form.selected_ids.value = idstring + entityid + ";";
    }
    else
    {
      form.selectall.checked = false;
      form.selected_ids.value = idstring.replace(entityid + ";", '');
    }
}

// crmv@72993
function update_invitees_actions(checked,entityid,form) {
	var fnname = null;
	var fnprepend = '';
	var fn = null;
	
	// json not supported
	if (!window.JSON) return;

	// get popup module
	var mod = jQuery('form[name=basicSearch] input[name=module]').val();
	if (!mod) {
		console.log('No module found');
		return;
	}
	
	// add or remove it to the list
	var funcs = JSON.parse(jQuery('#popup_select_actions').val() || '{}') || {};
	
	// if dechecked, remove from the list
	if (!checked) {
		delete funcs[entityid];
		jQuery('#popup_select_actions').val(JSON.stringify(funcs));
		return;
	}
	
	// crmv@118184
	fnname = 'addInvitee';
	fnprepend = 'top.';
	// crmv@118184e
	
	// nothing to link
	if (!fnname) {
		console.log('No return function found');
		return;
	}

	var cbox = jQuery('input[name=selected_id][id='+entityid+']').first();
	if (cbox.length > 0) {
		// crmv@118184
		// find non empty a tags with onclick handlers
		cbox.closest('tr').find('a[onclick]').each(function(index, item) {
			var oclick = jQuery(item).attr('onclick');
			var start = oclick.indexOf(fnname);
			if (start >= 0) {
				fn = fnprepend + oclick.substring(start);
				fn = fn.replace('closePopup();', '').replace(/&quot;/g, '"');
			}
		});
		// crmv@118184e
	} else {
		console.log('No valid checkbox found');
	}
	
	if (fn) {
		// add to the array
		funcs[entityid] = fn;
		jQuery('#popup_select_actions').val(JSON.stringify(funcs));
	} else {
		console.log('No return function selected');
	}
	
}
// crmv@72993e

function select_all_page(state,form)
{
	if (typeof(form.selected_id.length)=="undefined"){
		if (form.selected_id.checked != state){
			form.selected_id.checked = state;
			update_selected_ids(state,form.selected_id.value,form)
		}
    }
	else {
	    for (var i=0;i<form.selected_id.length;i++){
	        obj_check = form.selected_id[i];
	        if (obj_check.checked != state){
		        obj_check.checked = state;
		        update_selected_ids(state,obj_check.value,form)
	        }
	    }
    }
}
//crmv@fix listview end
//for multiselect check box in list view:

function check_object(sel_id,groupParentElementId)
{
        var select_global=new Array();
        var selected=trim(document.getElementById("allselectedboxes").value);
        select_global=selected.split(";");
        var box_value=sel_id.checked;
        var id= sel_id.value;
        var duplicate=select_global.indexOf(id);
        var size=select_global.length-1;
		var result="";
        //alert("size: "+size);
        //alert("Box_value: "+box_value);
        //alert("Duplicate: "+duplicate);
        if(box_value == true)
        {
                if(duplicate == "-1")
                {
                        select_global[size]=id;
                }

                size=select_global.length-1;
                var i=0;
                for(i=0;i<=size;i++)
                {
                        if(trim(select_global[i])!='')
                                result=select_global[i]+";"+result;
                }
                default_togglestate(sel_id.name,groupParentElementId);
        }
        else
        {
                if(duplicate != "-1")
                        select_global.splice(duplicate,1)

                size=select_global.length-1;
                var i=0;
                for(i=size;i>=0;i--)
                {
                        if(trim(select_global[i])!='')
                                result=select_global[i]+";"+result;
                }
          //      getObj("selectall").checked=false
                default_togglestate(sel_id.name,groupParentElementId);
        }

        document.getElementById("allselectedboxes").value=result;
        //alert("Result: "+result);
}
function update_selected_checkbox()
{
        var all=document.getElementById('current_page_boxes').value;
        var tocheck=document.getElementById('allselectedboxes').value;
        var allsplit=new Array();
        allsplit=all.split(";");

        var selsplit=new Array();
        selsplit=tocheck.split(";");

        var n=selsplit.length;
        for(var i=0;i<n;i++)
        {
                if(allsplit.indexOf(selsplit[i]) != "-1")
                        document.getElementById(selsplit[i]).checked='true';
        }
}

//Function to Set the status as Approve/Deny for Public access by Admin
function ChangeCustomViewStatus(viewid,now_status,changed_status,module,parenttab)
{
	$('status').style.display = 'block';
	new Ajax.Request(
       		'index.php',
               	{queue: {position: 'end', scope: 'command'},
               		method: 'post',
                    postBody:'module=CustomView&action=CustomViewAjax&file=ChangeStatus&dmodule='+module+'&record='+viewid+'&status='+changed_status,
					onComplete: function(response)
					{
			        	var responseVal=response.responseText;
						if(responseVal.indexOf(':#:FAILURE') > -1) {
							alert('Failed');
						} else if(responseVal.indexOf(':#:SUCCESS') > -1) {
							var values = responseVal.split(':#:');
							var module_name = values[2];
							var customview_ele = $('viewname');
							showDefaultCustomView(customview_ele, module_name, parenttab);
						} else {
							$('ListViewContents').innerHTML = responseVal;
						}
						$('status').style.display = 'none';
					}
				}
	);
}

function VT_disableFormSubmit(evt) {
	var evt = (evt) ? evt : ((event) ? event : null);
	var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
	if ((evt.keyCode == 13) && (node.type=='text')) {
		node.onchange();
		return false;
	}
	return true;
}
var statusPopupTimer = null;
function closeStatusPopup(elementid)
{
	statusPopupTimer = setTimeout("document.getElementById('" + elementid + "').style.display = 'none';", 50);
}

function updateCampaignRelationStatus(relatedmodule, campaignid, crmid, campaignrelstatusid, campaignrelstatus)
{
	$("vtbusy_info").style.display="inline";
	document.getElementById('campaignstatus_popup_' + crmid).style.display = 'none';
	var data = "action=updateRelationsAjax&module=Campaigns&relatedmodule=" + relatedmodule + "&campaignid=" + campaignid + "&crmid=" + crmid + "&campaignrelstatusid=" + campaignrelstatusid;
	new Ajax.Request(
		'index.php',
			{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: data,
			onComplete: function(response) {
				if(response.responseText.indexOf(":#:FAILURE")>-1)
				{
					alert(alert_arr.ERROR_WHILE_EDITING);
				}
				else if(response.responseText.indexOf(":#:SUCCESS")>-1)
				{
					document.getElementById('campaignstatus_' + crmid).innerHTML = campaignrelstatus;
					$("vtbusy_info").style.display="none";
				}
			}
		}
	);
}

function loadCvList(type,id) {
	var element = type+"_cv_list";
	var value = document.getElementById(element).value;

	var filter = $(element)[$(element).selectedIndex].value	;
	if(filter=='None')return false;
	if(value != '') {
		$("status").style.display="inline";
		new Ajax.Request(
			'index.php',
			{queue: {position: 'end', scope: 'command'},
				method: 'post',
				postBody: 'module=Campaigns&action=CampaignsAjax&file=LoadList&ajax=true&return_action=DetailView&return_id='+id+'&list_type='+type+'&cvid='+value,
				onComplete: function(response) {
					$("status").style.display="none";
					$("RLContents").update(response.responseText);
				}
			}
		);
	}
}
//crmv@add select all	//crmv@20065
function get_real_selected_ids(module){
	//crmv@21048m
	/*if (module == 'Documents') {
		allids = document.getElementById('allids').value;
		selected_ids_obj = 'allselectedboxes';
	}
	else {*/
		allids = document.getElementById('all_ids').value;
		selected_ids_obj = 'selected_ids';
	//}
	//crmv@21048m e
	ret_value = '';
	if (allids == 1){
	    $("status").style.display="inline";
	    urlstring="&calc_nav=true&get_all_ids=true";
    	selected_ids = document.getElementById(selected_ids_obj).value.replace(/;/g,",");
		if (selected_ids == "" || selected_ids == ","){
		}
		else{
			if (selected_ids.substr('0','1')==","){
				selected_ids = selected_ids.substr('1');
			}
			urlstring+="&ids_to_jump="+selected_ids;
		}
		if (module == 'RecycleBin')
			urlstring+="&selected_module="+document.getElementById('selected_module').value;
    	postbody = "index.php?module="+module+"&action="+module+"Ajax&file=ListView&ajax=true&"+urlstring;
		res = getFile(postbody);
		res_ = res.split("&#&#&#");
		res_real = res_[1];
		if (module == 'RecycleBin')
			res_real = res;
		res_arr = eval ('('+res_real+')');
		if (res_arr['all_ids']){
			ret_value = res_arr['all_ids'];
		}
		$("status").style.display="none";
	}
	else {
		ret_value = document.getElementById(selected_ids_obj).value;
		//if (module == 'Documents' && ret_value != '') ret_value = ';'+ret_value; //crmv@21048m
		//crmv@27096
		var res = '';
		$("status").style.display="inline";
		res = new Ajax.Request(
			'index.php',
			{queue: {position: 'end', scope: 'command'},
				asynchronous: false,
		    	method: 'post',
				postBody:"module=Utilities&action=UtilitiesAjax&file=ListViewCheckSave&selected_module="+module+"&selected_ids="+ret_value,
			}
		);
		$("status").style.display="none";
		//crmv@27096e
	}
	return ret_value;
}
//crmv@add select all end	//crmv@20065e

/* crmv@30967 */
//moved here
function trimfValues(value) {
 var string_array;
 string_array = value.split(":");
 return string_array[4];
}

function updatefOptions(sel, opSelName) {
	var split = opSelName.split('Condition');
	var index = split[1];
	var selObj = document.getElementById(opSelName);
	var fieldtype = null;
	var currOption = selObj.options[selObj.selectedIndex];
	var currField = sel.options[sel.selectedIndex];
	currField.value = currField.value.replace(/\\'/g, '');
	var fld = currField.value.split(":");
	var tod = fld[4];
	label = getcondition(false);
	if (fld[4] == 'D' || fld[4] == 'DT') {
		$("and" + sel.id).innerHTML = "";
		$("and" + sel.id).innerHTML = "<em old='(yyyy-mm-dd)'>("
				+ $("user_dateformat").value + ")</em>&nbsp;" + label;
	} else if (fld[4] == 'T' && fld[1] != 'time_start' && fld[1] != 'time_end') {
		$("and" + sel.id).innerHTML = "";
		$("and" + sel.id).innerHTML = "<em old='(yyyy-mm-dd)'>("
				+ $("user_dateformat").value + " hh:mm:ss)</em>&nbsp;" + label;
	} else if (fld[4] == 'I' && fld[1] == 'time_start' || fld[1] == 'time_end') {
		$("and" + sel.id).innerHTML = "hh:mm&nbsp;" + label;
	} else if (fld[4] == 'T' && fld[1] == 'time_start' || fld[1] == 'time_end') {
		$("and" + sel.id).innerHTML = "hh:mm&nbsp;" + label;
	} else if (fld[4] == 'C') {
		$("and" + sel.id).innerHTML = "( Yes / No )&nbsp;" + label;
	} else {
		$("and" + sel.id).innerHTML = "&nbsp;" + label;
	}
	//crmv@48693
	if (gVTModule == 'Messages' && fld[0] == 'mdate') {
		if (typeofdata['T'].indexOf('custom') == -1) {
			typeofdata['T'].push('today');
			typeofdata['T'].push('yesterday');
			typeofdata['T'].push('thisweek');
			typeofdata['T'].push('lastweek');
			typeofdata['T'].push('thismonth');
			typeofdata['T'].push('lastmonth');
			typeofdata['T'].push('last60days');
			typeofdata['T'].push('last90days');
			typeofdata['T'].push('custom');
		}
		fLabels['custom'] = alert_arr.LBL_ADVSEARCH_DATE_CUSTOM;
		fLabels['yesterday'] = alert_arr.LBL_ADVSEARCH_DATE_YESTARDAY;
		fLabels['today'] = alert_arr.LBL_ADVSEARCH_DATE_TODAY;
		fLabels['lastweek'] = alert_arr.LBL_ADVSEARCH_DATE_LASTWEEK;
		fLabels['thisweek'] = alert_arr.LBL_ADVSEARCH_DATE_THISWEEK;
		fLabels['lastmonth'] = alert_arr.LBL_ADVSEARCH_DATE_LASTMONTH;
		fLabels['thismonth'] = alert_arr.LBL_ADVSEARCH_DATE_THISMONTH;
		fLabels['last60days'] = alert_arr.LBL_ADVSEARCH_DATE_LAST60DAYS;
		fLabels['last90days'] = alert_arr.LBL_ADVSEARCH_DATE_LAST90DAYS;
	}
	//crmv@48693e
	if (currField.value != null && currField.value.length != 0) {
		fieldtype = trimfValues(currField.value);
		fieldtype = fieldtype.replace(/\\'/g, '');
		ops = typeofdata[fieldtype];
		var off = 0;
		if (ops != null) {

			var nMaxVal = selObj.length;
			for (nLoop = 0; nLoop < nMaxVal; nLoop++) {
				selObj.remove(0);
			}
			// selObj.options[0] = new Option ('None', '');
			// if (currField.value == '') {
			// selObj.options[0].selected = true;
			// }
			for ( var i = 0; i < ops.length; i++) {
				var label = fLabels[ops[i]];
				if (label == null)
					continue;
				var option = new Option(fLabels[ops[i]], ops[i]);
				selObj.options[i] = option;
				if (currOption != null && currOption.value == option.value) {
					option.selected = true;
				}
			}
		}
	} else {
		var nMaxVal = selObj.length;
		for (nLoop = 0; nLoop < nMaxVal; nLoop++) {
			selObj.remove(0);
		}
		selObj.options[0] = new Option('None', '');
		if (currField.value == '') {
			selObj.options[0].selected = true;
		}
	}
	//crmv@48693
	selObj.onchange='';
	if (gVTModule == 'Messages' && fld[0] == 'mdate') {
		selObj.onchange = function() {
			updatefOptions(document.getElementById('Fields'+index), 'Condition'+index);
		}
		var customDateValues = ['custom','yesterday','today','lastweek','thisweek','lastmonth','thismonth','last60days','last90days'];
		if (customDateValues.indexOf(currOption.value) > -1) {
			$("and" + sel.id).innerHTML = "&nbsp;" + getcondition(false);
			jQuery('#Srch_value'+index).hide();
			if (jQuery('#customIntervalDates'+index).length > 0) {
				jQuery('#customIntervalDates'+index).show();
			} else {
				jQuery('#Srch_value'+index).parent().append(
					'<div id="customIntervalDates'+index+'">'+
					alert_arr.LBL_ADVSEARCH_STARTDATE+
					'<input name="startdate'+index+'" id="jscal_field_date_start'+index+'" type="text" size="10" class="textField" value="" onChange="setAdvSearchIntervalDateValue('+index+');" style="border:1px solid #bababa;">'+
					'<img src="themes/softed/images/btnL3Calendar.gif" id="jscal_trigger_date_start'+index+'" style="visibility: visible;">'+
					'<font size="1"><em old="(yyyy-mm-dd)">('+getObj('user_dateformat').value+')</em></font>'+
					'&nbsp;&nbsp;'+alert_arr.LBL_ADVSEARCH_ENDDATE+
					'<input name="enddate'+index+'" id="jscal_field_date_end'+index+'" type="text" size="10" class="textField" value="" onChange="setAdvSearchIntervalDateValue('+index+');" style="border:1px solid #bababa;">'+
					'<img src="themes/softed/images/btnL3Calendar.gif" id="jscal_trigger_date_end'+index+'" style="visibility: visible;">'+
					'<font size="1"><em old="(yyyy-mm-dd)">('+getObj('user_dateformat').value+')</em></font>'+
					'</div>'
				);
				Calendar.setup ({
					inputField : "jscal_field_date_start"+index, ifFormat : js_date_format, showsTime : false, button : "jscal_trigger_date_start"+index, singleClick : true, step : 1
				});
				Calendar.setup ({
					inputField : "jscal_field_date_end"+index, ifFormat : js_date_format, showsTime : false, button : "jscal_trigger_date_end"+index, singleClick : true, step : 1
				});
			}
			showADvSearchDateRange(index,currOption.value);
		} else {
			jQuery('#customIntervalDates'+index).hide();
			jQuery('#Srch_value'+index).show();
			jQuery('#Srch_value'+index).val('');
		}
	} else {
		jQuery('#customIntervalDates'+index).hide();
		jQuery('#Srch_value'+index).show();
		jQuery('#Srch_value'+index).val('');
	}
	//crmv@48693e
}

//crmv@48693
function setAdvSearchIntervalDateValue(index) {
	jQuery('#Srch_value'+index).val(jQuery('#jscal_field_date_start'+index).val()+'|##|'+jQuery('#jscal_field_date_end'+index).val());
}
//crmv@48693e

function getcondition(mode){
	if (mode == false){
		mode = jQuery("input[name=matchtype]:checked").val(); // crmv@82419
	}

	if (mode == 'all')
		return alert_arr.LBL_AND;
	else
		return alert_arr.LBL_OR;
}

function checkgroup() {
	if($("group_checkbox").checked) {
		document.change_ownerform_name.lead_group_owner.style.display = "block";
		document.change_ownerform_name.lead_owner.style.display = "none";
	} else {
		document.change_ownerform_name.lead_owner.style.display = "block";
		document.change_ownerform_name.lead_group_owner.style.display = "none";
	}
}

function updatefOptionsAll(mode) {
	label = getcondition(mode);
	var table = document.getElementById('adSrc');
	if (table == undefined) return;
	var customDateValues = ['custom','yesterday','today','lastweek','thisweek','lastmonth','thismonth','last60days','last90days'];	//crmv@48693
	for (i = 0; i < table.rows.length; i++) {
		var selObj = getObj('Fields' + i);
		var currField = selObj.options[selObj.selectedIndex];
		currField.value = currField.value.replace(/\\'/g, '');
		var fld = currField.value.split(":");
		if (fld[4] == 'D' || fld[4] == 'DT') {
			$("andFields" + i).innerHTML = "";
			$("andFields" + i).innerHTML = "<em old='(yyyy-mm-dd)'>("+ $("user_dateformat").value + ")</em>&nbsp;" + label;
		} else if (fld[4] == 'T' && fld[1] != 'time_start' && fld[1] != 'time_end' && customDateValues.indexOf(getObj('Condition'+i).options[getObj('Condition'+i).selectedIndex].value) == -1) {	//crmv@48693
			$("andFields" + i).innerHTML = "";
			$("andFields" + i).innerHTML = "<em old='(yyyy-mm-dd)'>("+ $("user_dateformat").value + " hh:mm:ss)</em>&nbsp;"	+ label;
		} else if (fld[4] == 'I' && fld[1] == 'time_start' || fld[1] == 'time_end') {
			$("andFields" + i).innerHTML = "hh:mm&nbsp;" + label;
		} else if (fld[4] == 'T' && fld[1] == 'time_start' || fld[1] == 'time_end') {
			$("andFields" + i).innerHTML = "hh:mm&nbsp;" + label;
		} else if (fld[4] == 'C') {
			$("andFields" + i).innerHTML = "( Yes / No )&nbsp;" + label;
		} else {
			$("andFields" + i).innerHTML = "&nbsp;" + label;
		}
	}
}

// crmv@31245
function callSearch(searchtype, folderid) {

	if (gVTModule == undefined || gVTModule == '')
		return;
	
	//crmv@91082
	if(!SessionValidator.check()) {
		SessionValidator.showLogin();
		return false;
	}
	//crmv@91082e

	if (ajaxcall_list) {
		ajaxcall_list.abort();
	}
	if (ajaxcall_count) {
		ajaxcall_count.abort();
	}
	$("status").style.display = "inline";
	
	if (document.getElementById("all_ids") != null && document.getElementById("all_ids").value == 1) unselectAllIds();	//crmv@43893
	
	var p_tab = document.getElementsByName("parenttab");
	var postbody = {
		'module' : gVTModule,
		'action' : gVTModule+'Ajax',
		'file' : 'ListView',
		'ajax' : 'true',
		'query' : 'true',
		'search' : 'true',
		'parenttab' : p_tab[0].value
	};
	if (document.massdelete) postbody['idlist'] = document.massdelete.selected_ids.value;
	if (folderid != undefined && folderid != '' && folderid > 0) postbody['folderid'] = folderid; // crmv@30967
	//crmv@2963m
	if (gVTModule == 'Messages') {
		postbody['folder'] = document.massdelete.folder.value;
		postbody['thread'] = document.massdelete.thread.value;
		postbody['account'] = document.massdelete.account.value;
	}
	//crmv@2963me

	var extraParams = {}
	if (searchtype == 'Basic' || searchtype == 'BasicGlobalSearch') {	//crmv@120738 crmv@124737
	
		resetListSearch('Advanced',folderid,'no');
		basic_search_submitted = true; // crmv@31245
		
		extraParams = getSearchParams(extraParams,searchtype);
		extraParams = getSearchParams(extraParams,'Grid');	// keep the grid search
		
	} else if (searchtype == 'Advanced') {
	
		resetListSearch('Basic',folderid,'no');
		resetListSearch('BasicGlobalSearch',folderid,'no');	//crmv@124737
		advance_search_submitted = true;

		extraParams = getSearchParams(extraParams,searchtype);
		extraParams = getSearchParams(extraParams,'Grid');	// keep the grid search
		
	} else if (searchtype == 'Grid') {
	
		grid_search_submitted = true;
		extraParams = getSearchParams(extraParams,searchtype);
		// keep the basic/advance search
		if (basic_search_submitted) {
			extraParams = getSearchParams(extraParams,'Basic');
		} else if (advance_search_submitted) {
			extraParams = getSearchParams(extraParams,'Advanced');
		}
	
	//crmv@43942
	} else if (searchtype == 'Area' || searchtype == 'AreaGlobalSearch') {	//crmv@124737
		
		basic_search_submitted = true;
		postbody['file'] = 'index';
		postbody['query'] = jQuery('#basic_search_query').val();
		postbody['area'] = jQuery('#basic_search_area').val();
		extraParams = getSearchParams(extraParams,searchtype);
	//crmv@43942e
	}
	
	var postbody = jQuery.param(postbody);
	
	if (isdefined('append_url')) {
		postbody += $('append_url').value;
	}
	
	if (extraParams && !jQuery.isEmptyObject(extraParams)) var extraParams = '&'+jQuery.param(extraParams);
	else var extraParams = '';
	jQuery.ajax({
		'url': 'index.php?' + postbody,
		'type': 'POST',
		'data': extraParams,
		success: function(data) {
			$("status").style.display = "none";
			var result = data.split('&#&#&#');
			jQuery("#ListViewContents").html(result[2]); // crmv@104119
			if (result[1] != '')
				alert(result[1]);
			if (searchtype != 'Area' && searchtype != 'AreaGlobalSearch') {	//crmv@43942 crmv@124737
				$('basicsearchcolumns').innerHTML = '';
				//crmv@2963m crmv@103872
				if (gVTModule == 'Messages') {
					setmCustomScrollbar('#ListViewContents');
					jQuery('#nav_buttons').show();
					return;
				}
				//crmv@2963me crmv@103872e
				update_navigation_values(postbody+extraParams);
			}
		}
	})
	return false;
}

function getSearchParams(extraParams,searchtype) {
	
	if (searchtype == 'Basic' || searchtype == 'BasicGlobalSearch') {	// crmv@120738 crmv@124737
	
		extraParams['searchtype'] = 'BasicSearch';
		//crmv@120738 crmv@124737
		if (searchtype == 'BasicGlobalSearch')
			extraParams['search_text'] = encodeURIComponent(jQuery('#unifiedsearchnew_query_string').val());
		else
		//crmv@120738e crmv@124737
			extraParams['search_text'] = encodeURIComponent(jQuery('#basic_search_text').val());
		//extraParams['search_field'] = $('bas_searchfield').options[$('bas_searchfield').selectedIndex].value;
		
	} else if (searchtype == 'Advanced') {
	
		var no_rows = jQuery('#basic_search_cnt').val();
		for (jj = 0; jj < no_rows; jj++) {
			var sfld_name = getObj("Fields" + jj);
			var scndn_name = getObj("Condition" + jj);
			var srchvalue_name = getObj("Srch_value" + jj);
			var currOption = scndn_name.options[scndn_name.selectedIndex];
			var currField = sfld_name.options[sfld_name.selectedIndex];
			currField.value = currField.value.replace(/\\'/g, '');
			var fld = currField.value.split(":");
			var convert_fields = new Array();
			if (fld[4] == 'D' || (fld[4] == 'T' && fld[1] != 'time_start' && fld[1] != 'time_end') || fld[4] == 'DT') {
				convert_fields.push(jj);
			}
			extraParams['Fields'+jj] = sfld_name[sfld_name.selectedIndex].value;
			extraParams['Condition'+jj] = scndn_name[scndn_name.selectedIndex].value;
			extraParams['Srch_value'+jj] = encodeURIComponent(srchvalue_name.value);
		}
		for (i = 0; i < getObj("matchtype").length; i++) {
			if (getObj("matchtype")[i].checked == true)
				extraParams['matchtype'] = getObj("matchtype")[i].value;
		}
		if (convert_fields.length > 0) {
			var fields_to_convert;
			for (i = 0; i < convert_fields.length; i++) {
				fields_to_convert += convert_fields[i] + ';';
			}
			extraParams['fields_to_convert'] = fields_to_convert;
		}
		extraParams['searchtype'] = 'advance';
		extraParams['search_cnt'] = no_rows;
		
	} else if (searchtype == 'Grid') {

		if (typeof(gridsearch) != 'undefined') { 
			eval(jQuery('#gridsearch_script').html()); // crmv@116251
			var ii = 0;
			jQuery.each( gridsearch, function(i,fieldname) {
				var value = jQuery('#gridSrc [name="gs_'+fieldname+'"]').val();
				if (typeof value != 'undefined' && value != '') { // crmv@104114
					extraParams['GridFields'+ii] = fieldname;
					extraParams['GridCondition'+ii] = 'c';
					extraParams['GridSrch_value'+ii] = encodeURIComponent(value);
					ii++;
				}
			});
			if (ii > 0) extraParams['GridSearchCnt'] = ii;
		}

	//crmv@43942 crmv@124737
	} else if (searchtype == 'Area' || searchtype == 'AreaGlobalSearch') {
	
		extraParams['searchtype'] = 'BasicSearch';
		if (searchtype == 'AreaGlobalSearch')
			extraParams['search_text'] = encodeURIComponent(jQuery('#unifiedsearchnew_query_string').val());
		else
			extraParams['search_text'] = encodeURIComponent(jQuery('#basic_search_text').val());
	//crmv@43942e crmv@124737e
	}
	
	return extraParams;
}
//crmv@31245e

//crmv@3084m
function resetListSearch(searchtype,folderid,reload) {

	if (reload == undefined || reload == '') reload = 'auto';
	
	if (searchtype == 'Basic' || searchtype == 'BasicGlobalSearch') {	//crmv@124737
	
		if (reload == 'yes') basic_search_submitted = true;
		else if (reload == 'no') basic_search_submitted = false;
		
		//crmv@124737
		if (searchtype == 'Basic')
			jQuery('#basic_search_icn_canc').click();
		else
			jQuery('#unified_search_icn_canc').click();
		//crmv@124737e
		
	} else if (searchtype == 'Advanced') {
	
		if (jQuery('#adSrc').length == 0) return;	//crmv@55194
	
		var tableName = document.getElementById('adSrc');
		var prev = tableName.rows.length;
		if (prev > 1) {
			for (var i=1;i<prev;i++) {
				delRow();
			}
		}
		jQuery('#adSrc #Fields0').val('');
		jQuery('#adSrc #Condition0').val('');
		jQuery('#adSrc #Srch_value0').val('');
		advancedSearchOpenClose('close');	// crmv@105588
		
		if (reload == 'yes') advance_search_submitted = true;
		else if (reload == 'no') advance_search_submitted = false;
		if (advance_search_submitted) {
			$('search_url').value = '';
			if (jQuery('#viewname').length > 0) jQuery('#viewname').change(); else jQuery('#basicSearch').submit();	//crmv@77815
			advance_search_submitted = false;
		}
	} else if (searchtype == 'Grid') {
	
		jQuery.each( gridsearch, function(i,fieldname) {
			jQuery('#gridSrc [name="gs_'+fieldname+'"]').val('');
			// for select input
			if (jQuery('#gridSrc [name="gs_'+fieldname+'Str"]').length > 0) {
				jQuery('#gridSrc [name="gs_'+fieldname+'Str"]').val('');
				jQuery('input:checkbox[id^="'+fieldname+'"]').each(function(i,o){
					jQuery(o).prop('checked',false);
				});
				jQuery('#'+fieldname+'GridSelect').hide();
				jQuery('#'+fieldname+'GridInput').show();
			}
		});
		if (reload == 'yes') grid_search_submitted = true;
		else if (reload == 'no') grid_search_submitted = false;
		if (grid_search_submitted) {
			callSearch('Grid',folderid);
			grid_search_submitted = false;
		}
		
	}
}

function callGridSearch(event,type,folderid) {
	if (type == 'select') {
		callSearch('Grid',folderid);
	} else if (type == 'input') {
		if (event.which == 13){
			callSearch('Grid',folderid);
		}
	}
}

function gridSelectToggle(obj,c,field) {
	var checked = jQuery(obj).prop('checked');
	jQuery('input:checkbox[id^="'+c+'"]').each(function(i,o){
		if (jQuery(o).attr('id') == c+'All') {
			return;
		}
		if (checked) {
			jQuery(o).prop('checked',true);
		} else {
			jQuery(o).prop('checked',false);
		}
		gridSelectValue(o,field)
	});
}

function gridSelectValue(o,field) {
	var value = jQuery(o).val();
	if (jQuery('#'+field).val() == '') {
		var arr = new Array();
	} else {
		var arr = jQuery('#'+field).val().split('|##|');
	}
	if (jQuery(o).prop('checked') == true) {
		if (arr.indexOf(value) == -1) {
			arr.push(value);
		}
	} else {
		for (var key in arr) {
		    if (arr[key] == value) {
		        arr.splice(key, 1);
		    }
		}
	}
	jQuery('#'+field).val(arr.join('|##|'));
}
//crmv@3084me

// crmv@31245 - removed stuff

//----------

function lviewfold_showTooltip(folderid) {
	if (lviewFolder.disabled == true) return; // crmv@30976
	jQuery('#lviewfold_tooltip_'+folderid).show();
	lviewFolder.hidden = false;
}

function lviewfold_hideTooltip(folderid) {
	if (lviewFolder.disabled == true) return; // crmv@30976
	jQuery('#lviewfold_tooltip_'+folderid).hide();
	lviewFolder.hidden = true;
}

function lviewfold_moveTooltip(folderid) {
	if (!lviewFolder.hidden) {
		var newx, newy;
		var ttip = jQuery('#lviewfold_tooltip_'+folderid);
		tw = ttip.width();
		th = ttip.height();
		dw = jQuery(document).width();
		dh = jQuery(document).height();
		dx = dy = 10;
		if (lviewFolder.x + dx + tw > dw) {
			newx = dw - tw;
		} else {
			newx = lviewFolder.x+dx;
		}
		if (lviewFolder.y + dy + th > dh) {
			newy = dh - th;
		} else {
			newy = lviewFolder.y+dy;
		}
		ttip.css({'left':newx, 'top':newy});
	}
}

function lviewfold_add() {

	var baseurl = 'index.php?module=Utilities&action=UtilitiesAjax&file=FolderHandler';
	var formdata = jQuery('#lview_folder_addform').serialize();

	$("status").style.display = "inline";
	jQuery.ajax({
		type: 'POST',
		url: baseurl,
		data: formdata,
		success: function(data, tstatus) {
			if (data.substr(0, 7) == 'ERROR::') {
				$("status").style.display = "none";
				window.alert(data.substr(7));
			} else {
				location.reload();
			}
		}
	});
}

function lviewfold_del() {
	var checklist = jQuery('#lview_table_cont span[id^=lview_folder_checkspan]');
	if (checklist.length == 0) return window.alert(alert_arr.LBL_NO_EMPTY_FOLDERS);
	jQuery('#lviewfolder_button_del').hide();
	jQuery('#lviewfolder_button_add').hide();
	jQuery('#lviewfolder_button_list').hide();
	jQuery('#lviewfolder_button_del_cancel').show();
	jQuery('#lviewfolder_button_del_save').show();
	checklist.show();
	// crmv@30976 - ingrigisce le altre cartelle
	lviewFolder.disabled = true;
	jQuery('#lview_table_cont div[class=lview_folder_td]:not(:has(span[id^=lview_folder_checkspan]))').css({opacity: 0.5});
	// crmv@30976e
}

function lviewfold_del_cancel() {
	jQuery('#lviewfolder_button_del').show();
	jQuery('#lviewfolder_button_add').show();
	jQuery('#lviewfolder_button_list').show();
	jQuery('#lviewfolder_button_del_cancel').hide();
	jQuery('#lviewfolder_button_del_save').hide();
	jQuery('#lview_table_cont span[id^=lview_folder_checkspan]').hide();
	// crmv@30976
	lviewFolder.disabled = false;
	jQuery('#lview_table_cont div[class=lview_folder_td]').css({opacity: 1});
	// crmv@30976e
}

function lviewfold_del_save(module) {
	var delids = [];
	jQuery('#lview_table_cont input[type=checkbox]:checked').each(function (idx, el) {
		delids.push(parseInt(el.id.replace('lvidefold_check_', '')));
	});

	if (delids.length == 0) return window.alert(alert_arr.LBL_SELECT_DEL_FOLDER);

	var baseurl = 'index.php?module=Utilities&action=UtilitiesAjax&file=FolderHandler&subaction=del';
	var formdata = 'folderids='+delids.join(',')+'&formodule='+module;

	$("status").style.display = "inline";
	jQuery.ajax({
		type: 'POST',
		url: baseurl,
		data: formdata,
		success: function(data, tstatus) {
			if (data.substr(0, 7) == 'ERROR::') {
				$("status").style.display = "none";
				window.alert(data.substr(7));
			} else {
				location.reload();
			}
		}
	});
}
/* crmv@30967e */

//crmv@31245	//crmv@42846	//crmv@124737
function clearText(elem, canc_elem_id) {
	if (typeof(canc_elem_id) != 'undefined') var canc_elem = jQuery('#'+canc_elem_id); else canc_elem = jQuery('#basic_search_icn_canc');
	var jelem = jQuery(elem);
	var rest = jQuery.data(elem, 'restored');
	if (rest == undefined || rest == true) {
		jelem.val('');
		canc_elem.show();
		jQuery.data(elem, 'restored', false);
	}
}
function restoreDefaultText(elem, deftext, canc_elem_id) {
	if (typeof(canc_elem_id) != 'undefined') var canc_elem = jQuery('#'+canc_elem_id); else canc_elem = jQuery('#basic_search_icn_canc');
	var jelem = jQuery(elem);
	if (jelem.val() == '') {
		canc_elem.hide();
		jQuery.data(elem, 'restored', true);
		if (typeof(basic_search_submitted) == 'boolean' && basic_search_submitted == true) {
			if (jQuery('#basic_search_area').length > 0) callSearch('AreaGlobalSearch');
			else jQuery('#basicSearch').submit();
			basic_search_submitted = false;
		}
		jelem.val(deftext);
	}
}
function cancelSearchText(deftext, elem_id, canc_elem_id) {
	if (typeof(elem_id) == 'undefined') var elem_id = 'basic_search_text';
	jQuery('#'+elem_id).val('');
	restoreDefaultText(document.getElementById(elem_id), deftext, canc_elem_id);
	if (typeof(basic_search_submitted) == 'boolean') {
		var gridParams = {};
		gridParams = getSearchParams(gridParams,'Grid');
		if (gridParams['GridSearchCnt'] > 0) {
			basic_search_submitted = false;
		}
	}
}
//crmv@31245e	//crmv@42846e	//crmv@124737e

// crmv@105588
function advancedSearchOpenClose(mode) {
	if (typeof(mode) == 'undefined') var mode = 'auto';
	var $iconElement = jQuery('#adv_search_icn_go');
	if (mode == 'auto') {
		var open = jQuery('#advSearch:visible').length > 0;
		var materialIcon = open ? 'keyboard_arrow_down' : 'keyboard_arrow_up';
		$iconElement.html(materialIcon);
		jQuery('#advSearch').slideToggle('fast');
	} else if (mode == 'open') {
		$iconElement.html('keyboard_arrow_up');
		jQuery('#advSearch').slideDown('fast');
	} else if (mode == 'close') {
		$iconElement.html('keyboard_arrow_down');
		jQuery('#advSearch').slideUp('fast');
	}
}
//crmv@105588e

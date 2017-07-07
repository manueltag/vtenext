/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

/* crmv@25809 crmv@3085m crmv@104975 */

function alignTabRelated(panelid, goto) {
	var showPanel = panelBlocks[panelid],
		relids = showPanel ? showPanel.relatedids : [],
		cont = jQuery('#RLContents');

	// 1. hide the relations I shouldn't see
	// 2. show the ones that I can see and that are already present
	// 3. load the other ones that i can see, but are not in the page
	// 4. sort them!
	// 5. go to the desired one

	var shown = [];
		
	cont.find(">div[relation_id]").each(function(idx, el) {
		var $el = jQuery(el),
			relid = parseInt($el.data('relationid'));

		if ($el.data('isfixed')) {
			var hidden = $el.is(':hidden');
			if (!hidden && relids.indexOf(relid) < 0) {
				// 1 (hide)
				$el.hide();
			} else if (hidden && relids.indexOf(relid) >= 0) {
				// 2 (show)
				$el.show();
				shown.push(relid);
			}
		}
	});
	
	// 3 load missing
	var missing = jQuery(relids).not(shown).get();
	
	if (missing && missing.length > 0) {
		var calls = [];
		for (var i=0; i<missing.length; ++i) {
			calls.push(loadFixedRelated(missing[i]));
		}
		jQuery.when.apply(this, calls).then(function() {
			sortFixedRelated();
		});
	} else {
		sortFixedRelated();
	}
	
	// 4 (sort)
	function sortFixedRelated() {
		var last = null;

		for (var i=0; i<relids.length; ++i) {
			var relid = relids[i];
			var relcont = cont.find(">div[relation_id="+relid+"]");
			if (relcont.length > 0) {
				if (last == null) {
					// move at the beginning
					cont.prepend(relcont);
				} else {
					// move after the last
					relcont.insertAfter(last);
				}
				last = relcont;
			}
		}
		
		// 5 (goto)
		if (goto) {
			goToRelated(goto);
		}
	}
}

function loadFixedRelated(relid) {
	var fixcont = jQuery('#RLContents'),
		dyncont = jQuery('#DynamicRelatedList'),
		turbocont = jQuery('#turboLiftRelationsContainer'),
		turborel = turbocont.find('li[relation_id='+relid+']'),
		handler = turborel.attr('onclick');

	if (!handler) {
		return jQuery.Deferred().resolve();
	}

	// parse the handler!
	var matches = handler.match(/^([a-z0-9_.]+)\((.*)\)/i);
	var fn = matches[1],
		args = matches[2].split(',');
		
	args = jQuery.map(args, function(el, idx) { return eval(el); });
	
	args[3] += '&fixed=true';
	args[4] = true; // fixed
	args[5] = false; // autoscroll
	
	var def = loadDynamicRelatedList.apply(this, args);
	if (!def) {
		def = jQuery.Deferred().resolve();
	}
	
	// now do something after
	return def.then(function() {
		var cont = dyncont.find('>div[relation_id='+relid+']');

		// remove buttons
		cont.find('.dvInnerHeader').find('i').remove();
		
		// move in the fixed container
		fixcont.prepend(cont);
		
		// show the parent
		jQuery('#RelatedLists').show();
	});

}

function loadRelatedListBlockCount(urldata,target,imagesuffix,real_urldata,real_target) {
	var showdata = 'show_'+imagesuffix;
	var showdata_element = $(showdata);

	var hidedata = 'hide_'+imagesuffix;
	var hidedata_element = $(hidedata);
	if(isRelatedListBlockLoaded(target,urldata) == true){
		$(target).show();
		showdata_element.hide();
      	hidedata_element.show();
		return;
	}
	var indicator = 'indicator_'+imagesuffix;
	if (typeof($(indicator)) != 'undefined') {
		var indicator_element = $(indicator);
		indicator_element.show();
	}
	var target_element = $(target);
	
	new Ajax.Request(
		'index.php',
        {queue: {position: 'end', scope: 'command'},
                method: 'post',
                postBody: urldata,
                onComplete: function(response) {
					var res = eval("("+response.responseText+")");
					var count = res['count'];
					if (count == null) {
						count = 0;
					}
					var tabid = res['tabid'];
					if (typeof($(target+'_tl')) != 'undefined') {
						$(target+'_tl').innerHTML = "("+count+")";
					}
					target_element.innerHTML = "("+count+")";
					if (typeof($(indicator)) != 'undefined') {
						indicator_element.hide();
					}
				}
        }
	);
}

function isRelatedListBlockLoaded(id,urldata){
	var elem = document.getElementById(id);
	if(elem == null || typeof elem == 'undefined' || urldata.indexOf('order_by') != -1 ||
		urldata.indexOf('start') != -1 || urldata.indexOf('withCount') != -1){
		return false;
	}
	var tables = elem.getElementsByTagName('table');
	return tables.length > 0;
}

function loadRelatedListBlock(urldata,target,imagesuffix) {

	var showdata = 'show_'+imagesuffix;
	var showdata_element = jQuery('#'+showdata);
	
	var hidedata = 'hide_'+imagesuffix;
	var hidedata_element = jQuery('#'+hidedata);
	
	if(isRelatedListBlockLoaded(target,urldata) == true){
		jQuery('#'+target).show();
		showdata_element.hide();
		hidedata_element.show();
		return;
	}
	var indicator = 'indicator_'+imagesuffix;
	var indicator_element = jQuery('#'+indicator);
	indicator_element.show();
	
	var target_element = jQuery('#'+target);
	
	jQuery.ajax({
		url: 'index.php',
		method: 'POST',
		data: urldata,
		success: function(response) {
			target_element.html(trim(response));
			target_element.show();
			indicator_element.hide();
			showdata_element.hide();
			hidedata_element.show();
		}
	});
	
}

function hideRelatedListBlock(target, imagesuffix) {
	
	var showdata = 'show_'+imagesuffix;
	var showdata_element = $(showdata);
	
	var hidedata = 'hide_'+imagesuffix;
	var hidedata_element = $(hidedata);
	
	var target_element = $(target);
	if(target_element){
		target_element.hide();
	}
	hidedata_element.hide();
	showdata_element.show();
	$('delete_'+imagesuffix).hide();
}

function disableRelatedListBlock(urldata,target,imagesuffix){
	var showdata = 'show_'+imagesuffix;
	var showdata_element = $(showdata);

	var hidedata = 'hide_'+imagesuffix;
	var hidedata_element = $(hidedata);

	var indicator = 'indicator_'+imagesuffix;
	var indicator_element = $(indicator);
	indicator_element.show();
	
	var target_element = $(target);
	new Ajax.Request(
		'index.php',
        {queue: {position: 'end', scope: 'command'},
                method: 'post',
                postBody: urldata,
                onComplete: function(response) {
					var responseData = trim(response.responseText);
					target_element.hide();
					$('delete_'+imagesuffix).hide();
      				hidedata_element.hide();
					showdata_element.show();
      				indicator_element.hide();
				}
        }
	);
}

var currentRelated = '';
function loadDynamicRelatedList(obj, relationid, related, urldata, fixed, autoscroll) {
	if (!relationid && obj) {
		// try to detect from the element
		relationid = jQuery(obj).closest('li').attr('relation_id');
	}
	relationid = parseInt(relationid);
	
	if (typeof autoscroll == 'undefined') {
		autoscroll = true;
	}

	if (fixed) {
		// if isnt in another panel
		var panelInfo = panelBlocks[window.currentPanelId];
		if (panelInfo && panelInfo.relatedids.indexOf(relationid) < 0) {
			// find a panel with that related
			var relpanelid = getPanelidForRelation(relationid);
			if (relpanelid && relpanelid != currentPanelId) {
				var goto = 'container_'+related;
				if (obj) {
					jQuery('.turboliftEntrySelected').addClass('turboliftEntry');
					jQuery('.turboliftEntrySelected').removeClass('turboliftEntrySelected');
					jQuery(obj).addClass('turboliftEntrySelected');
					jQuery(obj).removeClass('turboliftEntry');
				}
				changeTab(gVTModule, null, relpanelid, null, null, goto);
				return;
			}
		}
			
	}

	if (!fixed && currentRelated == related) {
		hideDynamicRelatedList(obj);
		return;
	}
	currentRelated = related;
	if (obj && obj instanceof HTMLElement) {
		jQuery('.turboliftEntrySelected').addClass('turboliftEntry');
		jQuery('.turboliftEntrySelected').removeClass('turboliftEntrySelected');
		jQuery(obj).addClass('turboliftEntrySelected');
		jQuery(obj).removeClass('turboliftEntry');
	}
	if (jQuery('#RelatedLists').find('#container_'+related).length > 0) {
		if (autoscroll) goToRelated('container_'+related);
		return;
	} else {
		jQuery('#DynamicRelatedList').show();
		VtigerJS_DialogBox.block('DynamicRelatedList');
		jQuery("#status").show();
		if (autoscroll) goToRelated('DynamicRelatedList');
	}

	var xhr = jQuery.ajax({
		url: 'index.php',
		method: 'POST',
		data: urldata,
        success: function(response) {
					
			// crmv@82419
			try {
				jQuery('#DynamicRelatedList').html(response);
			} catch (e) {
				console.log("Exception: ", e);
			}
			// crmv@82419e

      		//crmv@3083m : if I am in popup change target of link
      		if (amIinPopup()) {
      			jQuery('a').each(function(inedx, item) {
					if (jQuery(item).attr('href')) {
						if ((jQuery(item).attr('href').indexOf('?module=MyNotes') > -1 || jQuery(item).attr('href').indexOf('&module=MyNotes') > -1) && (jQuery(item).attr('href').indexOf('?action=DetailView') > -1 || jQuery(item).attr('href').indexOf('&action=DetailView') > -1)) {}
						else jQuery(item).attr('target', '_top');
					}
				});
      		}

      		//crmv@3083me
      		if (!fixed) showRelatedImg(related,'pin','hideDynamic');
      		VtigerJS_DialogBox.unblock('DynamicRelatedList');
      		jQuery("#status").hide();
      		//if (obj) fixTurbolift(obj);
        }
	});
	
	// return a xhr / promise
	return xhr;
}

function showRelatedImg(related,image1,image2) {
	jQuery('#pin_'+related).hide();
	jQuery('#unPin_'+related).hide();
	jQuery('#hideDynamic_'+related).hide();
	jQuery('#'+image1+'_'+related).show();
	jQuery('#'+image2+'_'+related).show();
}

function hideDynamicRelatedList(obj) {
	jQuery(obj).removeClass('turboliftEntrySelected');
	jQuery(obj).addClass('turboliftEntry');
	jQuery('#DynamicRelatedList').hide();
	jQuery('#DynamicRelatedList').empty();
	currentRelated = '';
}

function pinRelated(related,module,relmodule) {
	jQuery('#indicator_'+related).show();

	jQuery('#RelatedLists').append(jQuery('#DynamicRelatedList').html());
	hideDynamicRelatedList('tl_'+related);
	jQuery('#DynamicRelatedList').empty();
	jQuery('#RelatedLists').show();
	
	// remove link from Turbolift
	jQuery('.turboliftEntrySelected').addClass('turboliftEntry');
	jQuery('.turboliftEntrySelected').removeClass('turboliftEntrySelected');
	jQuery.ajax({
		url: 'index.php?module='+module+'&action='+module+'Ajax&file=PinRelatedList&related='+related+'&module='+module+'&relmodule='+relmodule+'&mode=pin',
		type: 'POST',
		dataType: 'html',
		success: function(data){
			showRelatedImg(related,'unPin');
			jQuery('#indicator_'+related).hide();
		}
	});
}

function unPinRelated(related,module,relmodule) {
	jQuery('#indicator_'+related).show();
	jQuery('#container_'+related).remove();
	
	// add link to Turbolift
	jQuery('#tl_'+related).addClass('turboliftEntry');
	jQuery('#tl_'+related).removeClass('turboliftEntrySelected');
	jQuery.ajax({
		url: 'index.php?module='+module+'&action='+module+'Ajax&file=PinRelatedList&related='+related+'&module='+module+'&relmodule='+relmodule+'&mode=unPin',
		type: 'POST',
		dataType: 'html',
		success: function(data){
			showRelatedImg(related,'unPin');
			jQuery('#indicator_'+related).hide();
		}
	});
}

function goToRelated(id){
	var cont = jQuery("#"+id);
	if (cont.length == 0) {
		cont = jQuery("#RelatedLists");
	}
	jQuery('html,body').animate({scrollTop: cont.offset().top - jQuery('#vte_menu_white').height()},'slow');
	if (window.Turbolift) {
		setTimeout(function() {
			Turbolift.alignScroll();
		}, 0);
	}
}
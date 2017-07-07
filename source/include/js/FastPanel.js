/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/

// crmv@119414 crmv@126984

var FastPanel = FastPanel || {
	
	busy: false,
	
	isOpen: false,
	
	fastPanel: '#fastPanel',
	
	mode: null, // full, half, custom
	
	lastUrl: null,
	
	direction: 'left', // TODO implement other directions
	
	delay: 200,
	
	modCommentsNewsHtmlCache: null,
	
	reOpenMenu: false,
	
	getPanel: function() {
		var me = this,
			panel = jQuery(me.fastPanel);
		
		return panel;
	},
	
	open: function(options, callback) {
		var me = this,
			leftPanel = jQuery('#leftPanel'),
			rightPanel = jQuery('#rightPanel'),
			fastPanel = me.getPanel(),
			mode = options.mode || me.mode,
			delay = options.delay || 0,
			expanded = fastPanel.attr('data-minified') || 'disabled';

		var totalWidth = '';
		
		var rightWidth = parseInt(rightPanel.width()) || 0;
		
		if (mode === 'custom') {
			totalWidth = options.size;
		} else {
			var percent, leftWidth = 0;
			if (mode === 'full') {
				percent = 100.0;
				leftWidth = parseInt(leftPanel.width()) || 0;
			} else if (mode === 'half') {
				percent = 50.0;
			}
			
			var percentString = percent + '%';
			
			var sumWidth = leftWidth + rightWidth;
			totalWidth = 'calc(' + percentString + ' - ' + sumWidth + 'px)';
		}

		var direction = options.direction || me.direction;
		if (direction == 'right') {
			fastPanel.css({
				'width': totalWidth,
				'right': 'auto',
				'left': (LateralMenu.isForced == true) ? '220px' : '80px',
				'-moz-transition': 'all ' + delay + 'ms ease-out',
				'-webkit-transition': 'all ' + delay + 'ms ease-out',
				'transition': 'all ' + delay + 'ms ease-out',
			});
		} else {
			fastPanel.css({
				'width': totalWidth,
				'right': rightWidth + 'px',
				'left': 'auto',
				'-moz-transition': 'all ' + delay + 'ms ease-out',
				'-webkit-transition': 'all ' + delay + 'ms ease-out',
				'transition': 'all ' + delay + 'ms ease-out',
			});
		}
		fastPanel.removeClass();
		if (typeof(options['aside_class']) != 'undefined') fastPanel.addClass(options['aside_class']);
		
		me.isOpen = true;
		
		if (mode === 'full') {
			jQuery('#Buttons_List_3').fadeOut('fast');
		}
		
		setTimeout(function() {
			if (jQuery.isFunction(callback)) {
				callback();
			}
		}, delay);
	},
	
	close: function(options, callback) {
		var me = this,
			fastIframe = jQuery('#fastIframe'),
			ajaxCont = jQuery('#ajaxCont')
			fastPanel = me.getPanel(),
			delay = options.delay || 0;
		
		fastPanel.css({
			'width': '0px',
			'right': '-50px',
			'-moz-transition': 'all ' + delay + 'ms ease-out',
			'-webkit-transition': 'all ' + delay + 'ms ease-out',
			'transition': 'all ' + delay + 'ms ease-out'
		});
		
		jQuery('#Buttons_List_3').fadeIn('fast');
		
		me.isOpen = false;
		
		setTimeout(function() { fastIframe.attr('src', 'about:blank'); }, 100);
		ajaxCont.empty();
		jQuery('#ajaxSearchCont').hide();
		
		setTimeout(function() {
			if (jQuery.isFunction(callback)) {
				callback();
			}
		}, delay);
	},
	
	toggle: function(url, params, options, callback, failure) {
		var me = this,
			fastPanel = me.getPanel(),
			options = options || {},
			delay = options.delay || 0,
			mode = options.mode || 'full',
			expanded = fastPanel.attr('data-minified') || 'disabled',
			switchPanel = false;
			
		if (!me.lastUrl) me.lastUrl = url;
		
		if (me.isOpen && url != me.lastUrl && !options.forceClose) {
			me.close(options, function() {
				expanded = 'disabled';
				switchPanel = true;
				processToggle();
			});
		} else {
			processToggle();
		}
		
		me.lastUrl = url;
		me.mode = mode;
		
		function processToggle() {
			if (options.forceClose) expanded = 'enabled';
			var status = expanded == 'enabled' ? 'close' : 'open';
			
			var set_timeout = false;
			if (me.reOpenMenu) {
				if (mode == 'full' && status == 'open') {}
				else {
					jQuery('#leftPanel .togglePin').click();
					LateralMenu.toggleState('open');
					me.reOpenMenu = false;
				}
			} else if ((LateralMenu.isForced || LateralMenu.isOpen) && mode == 'full' && status == 'open') {
				jQuery('#leftPanel .togglePin').click();
				LateralMenu.toggleState('close');
				me.reOpenMenu = true;
				set_timeout = true;
			}
			
			setTimeout(function() {
				me[status](options, function(res) {
					fastPanel.attr('data-minified', expanded == 'enabled' ? 'disabled' : 'enabled');
					
					if (switchPanel) {
						if (jQuery.isFunction(options.switchCallback)) {
							options.switchCallback();
						}
					} else {
						if (expanded === 'enabled') {
							if (jQuery.isFunction(options.closeCallback)) {
								options.closeCallback();
							}
						} else {
							if (jQuery.isFunction(options.openCallback)) {
								options.openCallback();
							}
						}
					}
					
					if (url && url.length > 0 && expanded != 'enabled') {
						if (options && options.iframe) {
							me.iframeLoad(url, params, options, successCallback, failureCallback);
						} else {
							me.ajaxCall(url, params, options, successCallback, failureCallback);
						}
					} else {
						if (jQuery.isFunction(callback)) {
							callback();
						}
					}
					
					function successCallback(res) {
						if (jQuery.isFunction(callback)) {
							callback(res);
						}
					}
					
					function failureCallback(res) {
						if (jQuery.isFunction(failure)) {
							failure(res);
						}
					}
				});
			}, set_timeout ? delay+400 : 0);
		}
	},
	
	showBusy: function() {
		var me = this;
		me.busy = true;
		
		me.progress('fastPanel', 'light');
	},
	
	hideBusy: function() {
		var me = this;
		me.busy = false;
		
		VtigerJS_DialogBox.hideprogress('fastPanel');
	},
	
	progress: function(target, color) {
		var me = this,
			color = color || 'dark',
			loaderClass = 'loader',
			layerClass = 'veil_new';
			
		var prgbxid = "__vtigerjs_dialogbox_progress_id__";
		if (target) {
			prgbxid += target + "__";
		}
		
		if (color == 'light') {
			loaderClass = 'vteLoader';
			layerClass = 'veil_light';
		}
		
		var node = jQuery('#' + prgbxid);
		
		if (node.length < 1) {
			node = jQuery('<div></div>');
			node.attr('id', prgbxid);
			node.attr('class', layerClass);
			
			node.css({
				'position': 'absolute',
				'width': '100%',
				'height': '100%',
				'top': '0',
				'left': '0',
				'display': 'block',
				'background': '#FFFFFF',
				'zIndex': findZMax() + 1,
			});
			
			if (target) {
				jQuery('#' + target).append(node);
			} else {
				document.body.appendChild(node);
			}
			
			var loaderTable = jQuery('<table></table>');
			loaderTable.attr('align', 'center');
			loaderTable.css('width', '100%');
			loaderTable.css('height', '100%');
			loaderTable.html('<tr><td class="big" align="center">'+
				'<div class="' + loaderClass + '">Loading...</div>'+
				'</td></tr>');
			
			jQuery(node).append(loaderTable);
		}
		
		node.css('display', 'block');
	},
	
	iframeLoad: function(url, params, options, callback, fallback) {
		var me = this,
			fastIframe = jQuery('#fastIframe'),
			ajaxCont = jQuery('#ajaxCont'),
			options = options || {},
			params = params || {},
			delay = options.delay || 0;
		
		if (me.busy) return;
		
		fastIframe.show();
		ajaxCont.hide();
		
		var urlParams = url + '&' + jQuery.param(params);
		
		me.showBusy();
		
		jQuery('#fastIframe').attr("src", urlParams).load(function() {
			me.hideBusy();
			
			if (jQuery.isFunction(callback)) {
				callback();
			}
		});
	},
	
	ajaxCall: function(url, params, options, callback, failure) {
		var me = this,
			fastIframe = jQuery('#fastIframe'),
			ajaxCont = jQuery('#ajaxCont'),
			params = params || {},
			options = options || {};
		
		if (me.busy) return;
		
		fastIframe.hide();
		ajaxCont.show();
		
		me.showBusy();
		
		jQuery.ajax({
			url: url,
			method: 'GET',
			data: params,
			success: function(res) {
				me.hideBusy();
				if (res) {
					if (jQuery.isFunction(callback)) {
						callback(res);
					}
					return;
				} else {
					console.log('Invalid data returned from server: ' + res);
					if (jQuery.isFunction(failure)) {
						failure();
					}
				}
			},
			error: function() {
				me.hideBusy();
				console.log('Ajax error');
				if (jQuery.isFunction(failure)) {
					failure();
				}
			}
		});
	},
	
	showModuleHome: function(module, options) {
		var me = this,
			url = null,
			options = options || {},
			mode = options.mode || 'full',
			size = options.size || null,
			params = {},
			callback = null;

		// TODO : spostare tutto in sdk_menu_fixed gestendo in un campo json params, options, callback, etc.
		if (module == 'Messages' || module == 'Calendar' || module == 'Processes') {
			url = 'index.php?module='+module+'&action=index';
			params = {
				'hide_menus': true,
				'skip_vte_footer': true,
			};
			options['iframe'] = true;
		} else if (module == 'ModComments') {
			params = {};
			callback = function(res) {
				if (me.modCommentsNewsHtmlCache == null) {
					jQuery('#ModCommentsNews_Handle_Title').html('');
					jQuery('#ModCommentsNews_Handle').removeClass('level3Bg');
					jQuery('#ModCommentsNews .closebutton').remove();
					me.modCommentsNewsHtmlCache = jQuery('#ModCommentsNews').html();
					jQuery('#ModCommentsNews').remove();
				}
				jQuery('#ajaxCont').html(me.modCommentsNewsHtmlCache);
				jQuery('#ajaxCont').show();
				loadModCommentsNews(ModCommentsCommon.default_number_of_news);
				jQuery('#modcomments_search_text').val('');
				jQuery('#modcomments_search_text').blur();
			};
		} else if (module == 'ModNotifications') {
			url = 'index.php?module=ModNotifications&action=ModNotificationsAjax&file=ModNotificationsWidgetHandler&ajax=true&widget=DetailViewBlockCommentWidget&parentid=&criteria=20';
			params = {};
			callback = function(res) {
				jQuery('#ajaxCont').html(res);
			};
		} else if (module == 'LastViewed' || module == 'QuickCreate') {
			url = 'index.php?module=Home&action=HomeAjax&file=Fast'+module;
			params = {};
			callback = function(res) {
				jQuery('#ajaxCont').html(res);
			};
		} else if (module == 'TodoList') {
			url = 'index.php?module=SDK&action=SDKAjax&file=src/Todos/GetTodosList';
			params = { 'fastMode': true };
			callback = function(res) {
				jQuery('#ajaxCont').html(res);
			};
		} else if (module == 'LBL_FAVORITES') {
			url = 'index.php?module=SDK&action=SDKAjax&file=src/Favorites/GetFavoritesList';
			params = {};
			callback = function(res) {
				jQuery('#ajaxCont').html(res);
			};
		} else if (module == 'MyNotes') {
			url = 'index.php?module=MyNotes&action=SimpleView';
			params = {
				'hide_menus': true,
				'skip_vte_footer': true,
			}
			options['iframe'] = true;
			options['mode'] = 'full';
		} else if (module == 'LBL_TRACK_MANAGER') {
			url = 'index.php?module=SDK&action=SDKAjax&file=src/CalendarTracking/TrackerManager';
			params = {
				'hide_menus': true,
				'skip_vte_footer': true,
			}
			options['iframe'] = true;
		} else if (module == 'GlobalSearch') {
			callback = function() {
				jQuery('#ajaxCont').hide();
				jQuery('#ajaxSearchCont').show();
				if (jQuery('#ajaxSearchCont').html() == '') {
					UnifiedSearchAreasObj.show(jQuery('#ajaxSearchCont'),'search');
				}
			};
			options['aside_class'] = 'fastPanelMenu';
		} else if (module == 'EventList') {
			url = 'index.php?module=SDK&action=SDKAjax&file=src/Events/GetEventContainer';
			params = {};
			callback = function(res) {
				jQuery('#ajaxCont').html(res);
				getEventList(this);
			};
		}

		params['fastmode'] = 1;
		
		FastPanel.toggle(url, params, options, callback || function() {});
	},
	
	init: function(options) {
		var me = this,
			options = options || {},
			fastPanel = me.getPanel();
			
		if (parent && parent.FastPanel.isOpen /* && ho il cookie valorizzato */) {
			// TODO reload in qualche modo....
		} 
			
		var delay = me.delay;
		var openCallback = options.openCallback || function() {};
		var switchCallback = options.switchCallback || function() {};
		var closeCallback = options.closeCallback || function() {};
		me.direction = options.direction || me.direction;
		
		var defaultOptions = {
			'delay': delay,
			'openCallback': openCallback,
			'switchCallback': switchCallback,
			'closeCallback': closeCallback,
		};
		
		jQuery('i[data-fastpanel]').click(function(e) {
			var element = jQuery(this);
			var module = element.attr('data-module');
			var mode = element.attr('data-fastpanel');
			var size = element.attr('data-fastsize') || '';
			var hover_module = element.attr('data-hover-module');

			if (typeof(hover_module) != 'undefined') {
				element.attr('data-hover-status','off');
				element.stop(true,true);
			}
			
			size = parseInt(size) + (/%/i.test(size) ? '%' : 'px');
			
			if (element.hasClass('active')) {
				element.removeClass('active');
			} else {
				jQuery('i[data-fastpanel]').removeClass('active');
				element.addClass('active');
			}

			me.showModuleHome(module, jQuery.extend({}, defaultOptions, {
				'module': module,
				'mode': mode,
				'size': size,
			}));
		}).mouseover(function(e) {
			var element = jQuery(this);
			var hover_module = element.attr('data-hover-module');
			var hover_mode = element.attr('data-hover-fastpanel') || '';
			var hover_size = element.attr('data-hover-size') || '';
			
			if (typeof(hover_module) != 'undefined') {
				element.attr('data-hover-status','on');
				setTimeout(function(){
					if (element.attr('data-hover-status') == 'on') {
						hover_size = parseInt(hover_size) + (/%/i.test(hover_size) ? '%' : 'px');
						
						if (element.hasClass('active')) {
							element.removeClass('active');
						} else {
							jQuery('i[data-fastpanel]').removeClass('active');
							element.addClass('active');
						}

						me.showModuleHome(hover_module, jQuery.extend({}, defaultOptions, {
							'module': hover_module,
							'mode': hover_mode,
							'size': hover_size,
						}));
					}
				}, 2000);
			}
		}).mouseout(function(e) {
			var element = jQuery(this);
			var hover_module = element.attr('data-hover-module');

			if (typeof(hover_module) != 'undefined') {
				element.attr('data-hover-status','off');
				element.stop(true,true);
			}
		});
		
		jQuery(document).mouseup(function(e) {
			if (fastPanel.length > 0) {
				var toggleState = fastPanel.attr('data-minified') || 'disabled';
				if (toggleState !== 'disabled' && !fastPanel.is(e.target) && fastPanel.has(e.target).length === 0
					&& !jQuery(e.target).is('i[data-fastpanel]') 
					&& !jQuery(e.target).is('#ActivityRemindercallback') && !jQuery('#ActivityRemindercallback').has(e.target)
				) {
					me.toggle(null, {}, jQuery.extend({}, defaultOptions, {
						'forceClose': true,
					}), function() {
						jQuery('i[data-fastpanel]').removeClass('active');
						// TODO clear cookie
					});
				}
			}
		});
	},
};

jQuery(document).ready(function() {
	FastPanel.init({
		openCallback: function() {},
		switchCallback: function() {},
		closeCallback: function() {},
		direction: 'left',
	});
});

/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/

// crmv@119414 crmv@126984

var LateralMenu = LateralMenu || {
	
	isOpen: false,
	
	isForced: false,
	
	leftPanel: '#leftPanel',
	
	direction: 'left',
	
	getPanel: function() {
		var me = this,
			panel = jQuery(me.leftPanel);
		
		return panel;
	},

	open: function(options, callback) {
		var me = this,
			options = options || {};
		
		me.isOpen = true;
		if (jQuery.isFunction(callback)) {
			callback();
		}
	},
	
	close: function(options, callback) {
		var me = this,
			options = options || {};
		
		me.isOpen = false;
		if (jQuery.isFunction(callback)) {
			callback();
		}
	},
	
	toggle: function(options, callback) {
		var me = this,
			panel = me.getPanel(),
			minified = panel.attr('data-minified') || 'enabled';
		
		me[minified === 'enabled' ? 'open' : 'close'](options, function() {
			me.toggleState();
			if (jQuery.isFunction(callback)) {
				callback();
			}
		});
	},
	
	toggleState: function(state) {
		var me = this,
			panel = me.getPanel(),
			vteHeader = jQuery('#vteHeader'),
			mainCont = jQuery('#mainContainer'),
			minified = panel.attr('data-minified') || 'enabled';
		
		if (state == 'open') {
			minified = 'disabled';
		} else if (state == 'close') {
			minified = 'enabled';
		} else {
			minified = minified === 'enabled' ? 'disabled' : 'enabled';
		}
		
		panel.attr('data-minified', minified);
		vteHeader.attr('data-minified', minified);
		mainCont.attr('data-minified', minified);
	},
	
	clickToggle: function(options, callback) {
		var me = this,
			mainContent = jQuery('#mainContent'),
			centerHeader = jQuery('.vteCenterHeader'),
			togglePin = jQuery('#leftPanel .togglePin');
		
		me.isForced = !me.isForced;
		
		LateralMenu.open(options, function() {
			me.toggleState('open');
			togglePin.toggleClass('active');
			
			var toggleState = null;
			if (me.isForced) {
				toggleState = 'disabled';
			} else {
				toggleState = 'enabled';
			}
			
			set_cookie('togglePin', toggleState);
			
			mainContent.attr('data-minified', toggleState);
			centerHeader.attr('data-minified', toggleState);
			
			if (jQuery.isFunction(callback)) {
				callback();
			}
		});
	},
	
	hoverMe: function(options, callback) {
		var me = this;
		
		if (me.isForced) return;

		if (me.openTimeout) {
			clearTimeout(me.openTimeout);
			me.openTimeout = null;
		}
		
		if (me.closeTimeout && me.isOpen) {
			clearTimeout(me.closeTimeout);
			me.closeTimeout = null;
			return;
		}
		
		me.openTimeout = setTimeout(function() {
			me.toggle(options, callback);
		}, 250);
	},
	
	hoverMeExit: function(options, callback) {
		var me = this;
		
		if (me.isForced) return;

		if (me.openTimeout && !me.isOpen) {
			clearTimeout(me.openTimeout);
			me.openTimeout = null;
			return;
		}
		
		if (me.closeTimeout) {
			clearTimeout(me.closeTimeout);
			me.closeTimeout = null;
		}
		
		me.closeTimeout = setTimeout(function() {
			me.toggle(options, callback);
		}, 100);
	},
	
	init: function(options) {
		var me = this,
			options = options || {},
			panel = me.getPanel(),
			//leftHeader = jQuery('.vteLeftHeader'),
			togglePin = jQuery('#leftPanel .togglePin');
			
		if (!panel) return;
		
		var openCallback = options.openCallback || function() {};
		var closeCallback = options.closeCallback || function() {};
		var toggleCallback = options.toggleCallback || function() {};
		var direction = options.direction || me.direction;
		
		panel.hover(
			jQuery.proxy(me.hoverMe, LateralMenu, options, openCallback), 
			jQuery.proxy(me.hoverMeExit, LateralMenu, options, closeCallback)
		);
		/*
		leftHeader.hover(
			jQuery.proxy(me.hoverMe, LateralMenu, options, openCallback), 
			jQuery.proxy(me.hoverMeExit, LateralMenu, options, closeCallback)
		);
		*/
		togglePin.click(jQuery.proxy(me.clickToggle, LateralMenu, options, toggleCallback));
		
		var toggleState = get_cookie('togglePin');
		if (!toggleState || toggleState === 'disabled') {
			me.isForced = true;
		}
		
		me.direction = direction;
		if (direction === 'left') {
			panel.attr('data-direction', 'left');
		} else if (direction === 'right') {
			panel.attr('data-direction', 'right');
		} else {
			panel.attr('data-direction', 'left');
		}
	},
	
	showMenu() {
		var defaultOptions = {
			'delay': FastPanel.delay,
			'openCallback': function() {},
			'switchCallback': function() {},
			'closeCallback': function() {},
			'mode': 'half',
			'direction': 'right',
			'aside_class': 'fastPanelMenu',
		};
		FastPanel.toggle('index.php?module=Home&action=HomeAjax&file=HeaderAllMenu', {}, jQuery.extend({}, defaultOptions, {
			//'forceClose': true,
		}), function(res) {
			jQuery('#ajaxCont').html(res);
			var menuView = AllMenuObj.getMenuView();
			jQuery('#allmenu_btn_'+menuView).parent().click();
		});
	}
};

jQuery(document).ready(function() {
	
	LateralMenu.init({
		openCallback: function() {},
		
		closeCallback: function() {},
		
		toggleCallback: function() {},
		
		direction: 'left',
	});
	
});

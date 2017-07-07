/* JS code for the theme */
/* crmv@82419 */
/* crmv@96023 */

jQuery(document).ready(function() {

	// TODO: move these in the json file
	var themeOptions = {
		replacePicklists: false,
		replaceAlerts: true,
		useTooltips: true,
	};

	// add ripple to some other classes
	jQuery.material.options.withRipples += ",.crmbutton:not(.withoutripple)";
	
	// initialize basic controls
	jQuery.material.init();
	
	// picklists
	if (themeOptions.replacePicklists) {
		var lang = window.current_language;
		if (lang) {
			lang = lang.substr(0, 2);
		}
		var selector = "select:not(.notdropdown)";
		var initializePicklistSearch = function(element) {
			jQuery(element).select2({
				minimumResultsForSearch: 6,
				language: lang || 'en',
				dropdownAutoWidth: true,
			}).on('select2:open', function() {
				var container = jQuery('.select2-dropdown')[0];
				var body = jQuery('body');
				var bodyWidth = body.width();
				var bodyScrollWidth = body[0].scrollWidth;
				jQuery(container).css('zIndex',findZMax()+1);
				
				setTimeout(function() {
					if (bodyScrollWidth > bodyWidth) {
						var left = parseInt(jQuery(container).css('left'));
						left -= bodyScrollWidth - bodyWidth + 5;
						jQuery(container).css('left', left + 'px');
					}
				}, 0);
			});
		};
		jQuery(selector).each(function() {
			initializePicklistSearch(jQuery(this));
		});
		
		jQuery(document).on("DOMNodeInserted", function(e) {
			var $this = jQuery(e.target);
			if (!$this.is("select")) {
				$this = $this.find(selector);
			}
			$this.each(function() {
				initializePicklistSearch(jQuery(this));
				jQuery(this).on("remove", function () {
					jQuery(this).select2('destroy');
			    });
			});
		});
	}
	
	// tooltips
	if (themeOptions.useTooltips) {
		jQuery('img[data-toggle="tooltip"]').tooltip({animation: false});
		jQuery('span[data-toggle="tooltip"]').tooltip({animation: false});
		jQuery('i[data-toggle="tooltip"]').tooltip({animation: false});
	}
	
	// Fix for tooltips disappearing when prototype.js is loaded
	// See https://github.com/twbs/bootstrap/issues/6921
	if (themeOptions.useTooltips && window.Prototype && Prototype.BrowserFeatures.ElementExtensions) {
		var pluginsToDisable = ['collapse', 'dropdown', 'modal', 'tooltip', 'popover'];
		var disablePrototypeJS = function(method, pluginsToDisable) {
			var handler = function(event) {
				event.target[method] = undefined;
				setTimeout(function() {
					delete event.target[method];
				}, 0);
			};
			pluginsToDisable.each(function (plugin) {
				jQuery(window).on(method + '.bs.' + plugin, handler);
			});
		};
		
		disablePrototypeJS('show', pluginsToDisable);
		disablePrototypeJS('hide', pluginsToDisable);
	}

	if (themeOptions.replaceAlerts) {
		// replace alert boxes (but expose the original method)
		if (!window.origAlert) window.origAlert = window.alert;
		window.alert = function(text, cb) {
			jQuery('#alert-dialog-content').text(text);
			jQuery('#alert-dialog').modal();
			// remove handler
			jQuery('#alert-dialog').off('hidden.bs.modal');
			// call callback
			if (typeof cb == 'function') {
				jQuery('#alert-dialog').on('hidden.bs.modal', function(event){
					cb();
				});
			}
		}
	
		// replace confirm boxes (another function is needed)
		window.vteconfirm = function(text, cb) {
			function cbanswer() {
				// remove handler
				jQuery(this).off('click');
				// call callback
				if (typeof cb == 'function') cb(jQuery(this).hasClass('btn-ok'));
			}
			jQuery('#confirm-dialog-content').text(text);
			jQuery('#confirm-dialog').modal();
			jQuery('#confirm-dialog').find('button.btn-cancel').off('click').on('click', null, cbanswer);
			jQuery('#confirm-dialog').find('button.btn-ok').off('click').on('click', null, cbanswer);
			
		}
	} else {
		// replace alert boxes (but expose the original method)
		if (!window.origAlert) window.origAlert = window.alert;
		window.alert = function(text, cb) {
			window.origAlert(text);
			// call callback
			if (typeof cb == 'function') cb();
		}
	}
	
});
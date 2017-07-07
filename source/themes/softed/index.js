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

	// crmv@114693
	if (jQuery.material) {
		// add ripple to some other classes
		jQuery.material.options.withRipples += ",.crmbutton:not(.withoutripple)";
	
		// initialize basic controls
		jQuery.material.init();
	}
	// crmv@114693
	
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

	// crmv@99315
	if (themeOptions.replaceAlerts) {
		// replace alert boxes (but expose the original method)
		if (!window.origAlert) window.origAlert = window.alert;
		window.alert = window.vtealert = function(text, cb, options) {
			options = jQuery.extend({}, {
				showOkButton: false,
			}, options || {});
			
			if (options.showOkButton) {
				jQuery('#alert-dialog .modal-footer').removeClass('hidden');
			} else {
				jQuery('#alert-dialog .modal-footer').addClass('hidden');
			}
			
			jQuery('#alert-dialog-content').text(text);
			if (typeof window.findZMax == 'function') {
				jQuery('#alert-dialog').css('z-index', findZMax()+1);
			}
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
		window.vteconfirm = function(text, cb, options) {
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
			if (typeof window.findZMax == 'function') {
				jQuery('#confirm-dialog').css('z-index', findZMax()+1);
		   }
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
	// crmv@99315e
	
	// crmv@98866
	jQuery(document).off('click.tab.data-api');
	jQuery(document).on('click.tab.data-api', '[data-toggle="tab"]', function (e, params) {
	    e.preventDefault();
	    var parentTab = jQuery(this).closest('ul.nav-tabs');
	    
	    var tab = jQuery(jQuery(this).attr('href'));
	    var activate = !tab.hasClass('active');
	    var content = jQuery(parentTab.attr('data-content'));
	    
	    if (activate || (!activate && params && params.forceLoad)) {
	    	content.find('div.tab-pane.active').removeClass('active');
	    	parentTab.find('li.active').removeClass('active');
		    jQuery(this).tab('show');
		    parentTab.trigger('tabclick', params);
	    }
	});
	// crmv@98866 end
	
	if (jQuery && jQuery.fancybox) {
		var loadingExtension = {
			oldShowLoading: jQuery.fancybox.showLoading,
			oldHideLoading: jQuery.fancybox.hideLoading,
			showLoading: function() {
				D = jQuery(document);
				F = jQuery.fancybox;
				
				F.hideLoading();
				VtigerJS_DialogBox.progress();

				D.bind('keydown.loading', function(e) {
					if ((e.which || e.keyCode) === 27) {
						e.preventDefault();
						F.cancel();
					}
				});

				F.trigger('onLoading');
			},
			hideLoading: function() {
				jQuery(document).unbind('.loading');
				VtigerJS_DialogBox.hideprogress();
			}
		};
		jQuery.extend(jQuery.fancybox, loadingExtension);
	}
	
});
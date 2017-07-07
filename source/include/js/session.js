/* crmv@91082 crmv@106590 */

var SessionValidator = {
	
	// config
	checkInterval: 10000,
	
	// private stuff
	initialized: false,
	loginShown: false,
	checkTimer: null,
	checking: false,
	
	lastMessageTs: 0,
	current_user: '',
	
	initialize: function() {
		var me = this;
		
		if (me.initialized) return;
		if (me.inFrame()) return;

		me.current_user = (window.current_user && window.current_user.user_name) || '';
		if (window.VTELocalStorage) {
			VTELocalStorage.enablePropagation('checksession', me.onReceiveMessage.bind(me));
		} else {
			me.log('VTEStorage not initialized, messages won\'t be delivered');
		}
		
		me.initialized = true;
		//me.log('Session Validator initialized');
	},
	
	inFrame: function() {
		try {
			return window.self !== window.top;
		} catch (e) {
			return true;
		}
	},
	
	sendMessage: function(message, data) {
		var me = this;
		
		var content = {
			ts: (new Date()).getTime(),
			message: message,
		};
		
		if (window.VTELocalStorage) {
			VTELocalStorage.setItem('checksession', JSON.stringify(content));
		} else {
			//me.log('VTEStorage not initialized, unable to deliver the message');
		}
	},
	
	onReceiveMessage: function(event) {
		var me = this,
			data = event ? event.newValue : null;
		
		if (typeof data === 'string' && data[0] === '{') data = JSON.parse(data);
		
		// invalid value
		if (!data || !data.ts || !data.message) return;
		
		// already seen
		if (data.ts < me.lastMessageTs) return;
		
		var content = data.message;
		me.lastMessageTs = data.ts;
		
		if (content.msg == 'session_expired') {
			// wait a bit, not to overlap other requests
			setTimeout(function() {
				me.showLogin({autoCheck: false});
			}, Math.random()*2000);
		} else if (content.msg == 'login_succ') {
			// succesful login
			if (me.current_user && me.current_user != content.user_name) {
				// reload all page
				top.location.href = 'index.php'
			} else {
				// just remove login
				me.hideLogin();
			}
		}
	},
	
	check: function(options) {
		var me = this;
		
		// skip check if not initialized yet
		if (!me.initialized) return true;
		
		if (me.checking) {
			me.log('Checking for session validity is still in progress');
			return true;
		}
		
		options = jQuery.extend({
			showLogin: false,	// if true, automatically show the login page
		}, options || {});
		
		// crmv@106900
		if (window.navigator && 'onLine' in navigator && !navigator.onLine) {
			if (options.showLogin) {
				me.showOffline();
			}
			return false;
		}
		// crmv@106900e
		
		var result,
			ajaxError = false,
			errorInfo = null;
			
		me.checking = true;
		jQuery.ajax({
			url: 'index.php?module=Utilities&action=UtilitiesAjax&file=CheckSession',
			type: 'POST',
			async: false,
			success: function(data) {
				me.checking = false;
				try {
					result = JSON.parse(data);
					if (!result.success) {
						ajaxError = true;
						errorInfo = data;
					}
				} catch (e) {
					ajaxError = true;
					errorInfo = data;
				}
			},
			error: function(xhr) {
				me.checking = false;
				// network or server error
				ajaxError = true;
				errorInfo = xhr;
			}
		});
		
		// the ajax call is synchronous, so we get here
		if (ajaxError) {
			me.log('Warning, invalid Ajax response', errorInfo);
			return true;
		}
		
		if (result && result.success && result.valid == true) {
			if (!me.current_user) me.current_user = result.user_name;
			return true;
		} else {
			if (options.showLogin) {
				// display the login page
				var opts = {};
				if (result.reason) opts.reason = result.reason;
				me.showLogin(opts);
			}
			return false;
		}
	
	},
	
	clearTimers: function() {
		var me = this;
		
		if (top.ActivityReminder_regcallback_timer) {
			clearTimeout(top.ActivityReminder_regcallback_timer);
			ActivityReminder_regcallback_timer = null;
		}
		
		if (top.intervalCheckChanges) {
			clearInterval(top.intervalCheckChanges);
			intervalCheckChanges = null;
		}
		
		me.log('Timers stopped');
	},
	
	// set a timer to check if the session came back (from other tabs usually)
	setCheckTimer: function() {
		var me = this;
		
		me.clearCheckTimer();
		me.checkTimer = setInterval(function() {
			var valid = me.check({showLogin: false});
			if (valid) {
				// TODO: check if same user
				me.log('Session has been reactivated in another tab/window');
				me.clearCheckTimer();
				me.hideLogin();
			}
		}, me.checkInterval);
		
		me.log('Check timer started (every '+(me.checkInterval/1000)+'s)');
	},
	
	
	clearCheckTimer: function() {
		var me = this;
		
		if (me.checkTimer) {
			clearInterval(me.checkTimer);
			me.checkTimer = null;
		}
	},
	
	showLogin: function(options){
		var me = this;
		
		options = jQuery.extend({
			clearTimers: true,
			overlay: true,
			autoCheck: true,
			reason: 'concurrent', // TODO: change this one also if the session check method is changed
		}, options || {});
		
		if (me.loginShown) return;
		
		// crmv@106900
		if (window.navigator && 'onLine' in navigator && !navigator.onLine) {
			return me.showOffline(options);
		}
		// crmv@106900e
		
		if (options.overlay) {
			if (options.clearTimers) me.clearTimers();
			
			me.loginShown = true;
			me.sendMessage({msg: 'session_expired'});
			
			jQuery('#login_overlay').show();
			if (jQuery('#mask_login').length == 0) {
				jQuery('body').append('<div id="mask_login">&nbsp;</div>');
				placeAtCenter($('mask_login'));
			}
			
			var params = {
				login_view : 'ajax',
				logout_reason_code: options.reason,
			}
			jQuery.ajax({
				url: 'index.php?module=Users&action=Login',
				data: params,
				type: 'POST',
				success: function(data) {
					jQuery('#login_overlay').css({
						'z-index': findZMax()+1,
					});
					jQuery('#mask_login').css({
						'position': 'fixed',
						'top': '0px',
						'z-index': findZMax()+1,
					});
					jQuery('#mask_login').html(data).show();
					jQuery('#vte_footer').hide();
					if (options.autoCheck) me.setCheckTimer();
				}
			});
		} else {
			// redirect to the full login page
			location.href = 'index.php?module=Users&action=Login';
		}
	},
	
	// crmv@106900
	showOffline: function(options) {
		var me = this;
		
		options = jQuery.extend({
			//overlay: true,
			autoCheck: true,
		}, options || {});
		
		var timer = null;
		
		var label = alert_arr.LBL_NO_NETWORK;
		vtealert(label, function() {
			if (timer) clearInterval(timer);
		});
		
		if (options.autoCheck) {
			timer = setInterval(function() {
				if (window.navigator && navigator.onLine) {
					me.hideOffline();
				}
			}, 2000);
		}
		
	},
	
	hideOffline: function(options) {
		jQuery('#alert-dialog').modal('hide');
	},
	// crmv@106900e
		
	hideLogin: function(options) {
		var me = this;
		
		if (!me.loginShown) return;
		
		jQuery('#td_login_error').html('');
		jQuery('#login_overlay').hide();
		jQuery('#mask_login').hide();
		
		//riattivazione checkChanges e activityremindercallback
		ActivityReminderCallback();
		//closePopup();	//Se c'è un popup aperto lo lascio
		if (document.getElementById('expired') != null) fninvsh('expired');
		if (document.getElementById('todos') != null) fninvsh('todos');
		if (document.getElementById('events') != null) fninvsh('events');
		
		me.clearCheckTimer();
		me.loginShown = false;
	},
	
	doLogin: function(options, callback) {
		var me = this;
		
		var username = jQuery('#login_user_name').val();
		var password = jQuery('#login_password').val();
		
		// clear the error text
		jQuery('#td_login_error').html('');
		
		var params = {
			login_view: 'ajax',
			user_name: username,
			user_password: password,
		}

		jQuery.ajax({
			url: 'index.php?module=Users&action=Authenticate',
			data: params,
			type: 'POST',
			success: function(data) {
				var result = false;
				try {
					result = JSON.parse(data);
				} catch (e) {
					result = null;
				}
				
				if (result) {
					if (result.success) {
						ajaxSuccess(result);
					} else {
						ajaxFail(result.error || 'Unspecified server error');
					}
				} else {
					ajaxFail('Invalid server answer');
				}
			},
			failure: function() {
				ajaxFail('Ajax request failed');
			}
		});
		
		function ajaxSuccess(result) {
			if (result.user_changed) {
				top.location.href = 'index.php';	//Not setting action and module Users will be redirected to their default page
			} else {
				me.sendMessage({msg: 'login_succ', user_name: username});
				me.hideLogin();
			}
		}
		
		function ajaxFail(error) {
			jQuery('#td_login_error').html(error);
		}
		
	},
	
	log: function(msg) {
		console.log(msg);
	}
	
}

jQuery(document).ready(function() {
	SessionValidator.initialize();
});

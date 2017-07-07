/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@OPER5904 */
if (typeof(VTELocalStorage) == 'undefined') {
	var VTELocalStorage = {
		getItem : function(key) {
			var pathCode = VTELocalStorage.getPathCode();
			return localStorage.getItem(pathCode+"_"+key);
		},
		setItem : function(key, value) {
			var pathCode = VTELocalStorage.getPathCode();
			localStorage.setItem(pathCode+"_"+key, value);
		},
		removeItem : function(key) {
			var pathCode = VTELocalStorage.getPathCode();
			localStorage.removeItem(pathCode+"_"+key);
		},
		getPathCode : function() {
			return VTELocalStorage.hashCode(window.location.origin + window.location.pathname);
		},
		hashCode : function(str) {
		    var hash = 0;
		    if (str.length == 0) return hash;
		    for (i = 0; i < str.length; i++) {
		        char = str.charCodeAt(i);
		        hash = ((hash<<5)-hash)+char;
		        hash = hash & hash; // Convert to 32bit integer
		    }
		    return hash;
		},
		enablePropagation : function(key, funct) {
			window.addEventListener('storage', function(event) {
				var pathCode = VTELocalStorage.getPathCode();
				if (event.key == pathCode+"_"+key) {
					if (typeof funct == "function") {
						funct(event);
					}
				}
			});
		}
	}
}
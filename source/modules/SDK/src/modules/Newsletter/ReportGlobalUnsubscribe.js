/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/
/* crmv@55961 */
function FilterUnsubReport(id) {
	var params = "";
	var filterbox = getObj('filterbox');
	if (filterbox) {
		params += "&filterbox="+filterbox.value;
	}

	return params;
}
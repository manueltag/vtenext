/*
* Funzioni per la gestione delle picklist collegate
*/

/*
* Aggiorna le varie picklist
*/
// //crmv@27229 //crmv@82831
function linkedListUpdateLists(res) {
	if (!res) return;

	for (i=0; i<res.length; ++i) {
		name = res[i][0];
		list = res[i][1];
		list_trans = res[i][2];
		otherpl = document.getElementsByName(name);
		
		// take the first matching element
		if (otherpl.length > 0) {
			otherpl = otherpl[0];
		} else {
		// try a multiselect picklist
			otherpl = document.getElementsByName(name+"[]");
			if (otherpl.length > 0) otherpl = otherpl[0]; else continue;
		}

		// get original sel value
		var opt = otherpl.options[otherpl.selectedIndex];
		var oldval = (opt ? opt.value : null);
		// delete inside (this cycle is much faster than using innerhtml)
		while (otherpl.firstChild) {
			otherpl.removeChild(otherpl.firstChild);
		}
		// re-populate
		for (j=0; j<list.length; ++j) {
			var option = document.createElement("option");
			option.text = list_trans[j];
			option.value = list[j];
			otherpl.add(option);
			if (option.value == oldval) option.selected = true;
		}
		
		// crmv@97692
		// if empty, remove all
		if (list.length == 0) {
			otherpl.innerHTML = '';
			otherpl.selectedIndex = null;
		}
		// crmv@97692e

		// change other lists
		if (otherpl.onchange) otherpl.onchange(otherpl);
	}
}
//crmv@27229e

/*
* funzione da chiamare quando la picklist obj cambia
*/
function linkedListChainChange(obj, module) { // crmv@30528
	if (!obj) return;

	var pickname = obj.name;

	var opt = obj.options.item(obj.selectedIndex);
	var pickselection = (opt ? opt.value : null);

	jQuery.ajax({
		url:"index.php?module=SDK&action=SDKAjax&file=examples/uitypePicklist/300Ajax",
		dataType:"json",
		type: "post",
		data: "function=linkedListGetChanges"+
			"&modname="+encodeURIComponent(module)+  // crmv@30528
			"&name="+encodeURIComponent(pickname)+
			"&sel="+encodeURIComponent(pickselection),
		async: true,
		cache: false,
		//contentType: "application/json",
		success: function(res) {
			linkedListUpdateLists(res);
		}
	});

}
//crmv@82831e
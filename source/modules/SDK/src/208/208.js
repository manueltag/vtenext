/* crmv@37679 */
// called after ajax save

if (dtlView) {
	if (tagValue) tagValue = tagValue.replace(/&amp;/g, '&').replace(/\n/g, "<br>"); // crmv@81167
	getObj(dtlView).innerHTML = tagValue;
}
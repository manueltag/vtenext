/* crmv@82831 */
/* Some Bootstrap plugins, conflicts with other plugins (eg: dropdown)
 * so they need to be changed
 */

if (window.jQuery && typeof jQuery().dropdown == 'function') {
	// rename dropdown
	jQuery.fn.bsDropdown = jQuery.fn.dropdown;
	delete jQuery.fn.dropdown;
}
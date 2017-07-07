jQuery('document').ready(function(){
	if (typeof gVTModule != 'undefined'){
		if (gVTModule == 'Myfiles'){
			if (jQuery('[name=record]').val() == '' && jQuery('[name="filelocationtype"]').val() != 'I'){
				jQuery('[name="filelocationtype"]').val('I').click();
				jQuery('[name="filestatus"]').prop('checked',true);
			}
		}
	}
});
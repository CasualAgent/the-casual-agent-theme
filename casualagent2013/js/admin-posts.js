

jQuery(document).ready(function(){
	jQuery('input.chkbox_ca_is_featured').change(function(){

	var data = {
		action:'set_ca_post_feature_status',
		"post_ID":jQuery(this).attr('data-post-id'),
		val: (typeof jQuery(this).attr('checked') == 'undefined')?'no':'yes'
	}
	
	alert("post "+data["post_ID"]+" "+data.val);	
	ajax_action(data, 'GET');
});
	
});

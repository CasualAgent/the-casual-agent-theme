var formfield = "";

window.ca_media = window.ca_media || {handlers:{}};
window.ca_media.handlers = window.ca_media.handlers || {};
window.ca_media.handlers.set_ca_carousel_img =  function(html) {
    	
    	var $postID = jQuery('#post_ID') || false;
    	if(!$postID){
	    	alert('Attachment Error no post data');
	    	window.location.reload();
    	}
    	
    	var pID = $postID.val() || false;
    	
    	if(!pID){
	    	alert('Attachment Error no postID');
	    	window.location.reload();
    	}
    	
    	imgurl = jQuery('img',html).attr('src');
		
		console.debug(imgurl);
		console.debug(arguments);
		
		var args = {
			action:'set_ca_carousel_img',
			post_ID:pID,
			img_url:imgurl,
		}
		
		tb_remove();
		ajax_action(args, 'GET', 'reload');
		window.send_to_editor = window.send_to_editor_old;
	}
/*jQuery(document).ready(function(){
	var formfield = "";
	
	/*jQuery('a.ca_setup_featured_post').click(function() {
		console.debug(window.send_to_editor);
		
		window.send_to_editor = ca_setup_featured_post(get_post_id());	
		console.debug(window.send_to_editor);
		formfield = '_is_featured';
		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		return false;
	});*/
	
//})

	
	
	

	
	/*var data = {
		action:'set_ca_post_feature_status',
		"post-id":jQuery(this).attr('data-post-id'),
		val: (typeof jQuery(this).attr('checked') == 'undefined')?'no':'yes'
	}*/
	
	//alert("post "+data["post-id"]+" "+data.val);	
	//ajax_action(data, 'GET');

	

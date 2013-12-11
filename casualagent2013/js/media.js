

window.ca_media = window.ca_media || {handlers:{}};
window.send_to_editor_old = window.send_to_editor;

	jQuery(document).ready(
		function(){
			jQuery(".media-modal-close").live('click', function(){window.location.reload();});
			jQuery('#set-post-display-img').click(function(){
				wp.media.featuredImage.frame().modal.open();
			});
		
			jQuery('.media_upload').click(function(){
		alert('click');
		$this = jQuery(this);	
		fn = $this.attr('data-fn');
		fn = (typeof window.ca_media.handlers[fn] == 'function')? window.ca_media.handlers[fn]:window.send_to_editor_old;
		window.send_to_editor = fn;
		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		return false;
	})});

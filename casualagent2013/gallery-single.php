<?php
	global $post;
	$items = get_post_attachments($post, array('meta_query'=>array('key'=>'casual_agent_attachment_type', 'value'=>'gallery')));
	
	array_walk($items, '_array_walk_get_post_meta');
	$html = array();
	
	foreach($items as $obj){
		$isImg = in_array('image',explode("/", $obj->post_mime_type));
		if($isImg){
			$tag = "<img class='thumbnail' src='".$obj->guid."' alt='".$obj->post_title."' />";
			$html[] = $tag;
		}
	}
	$html = implode("\n", $html);
?>
<section class='gallery'>
	<?=$html?>
</section>
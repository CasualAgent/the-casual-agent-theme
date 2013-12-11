<?php
	global $post, $mpost;
	
	$post = $mpost->post;
	$src = get_syndication_source();
	$dom = str_get_html($post->post_content);
	$rssCls = 'rss-default';
	switch($src){
		case 'The FADER':
			$rssCls = 'rss-fader';
		default:
			
			/*$imgTags = extract_tags('img', $post->post_content);
			$imgTags = find_display_img($imgTags);
			$display_img = (count($imgTags)>0)?array_shift($imgTags):null;
			$display_img_tag = is_array($display_img)?"<img src='".$display_img['src']."' width='300' height='auto'/>":"";*/
			if($dom){
			$imgTags = $dom->find('img');
			$display_img = $imgTags[0];
			$display_img_tag = isset($display_img->src)?"<img class='post-content-img' src='".$display_img->src."' width='300' height='auto'/>":"";
			$display_img->outertext='';
			$el = $dom->find('div', 0);
			$el->outertext = '';
			
			$els = $dom->find('strong');
			foreach($els as $el){
				$el->outertext='';
			}
			
			$els = $dom->find('a');
			foreach($els as $el){
				$el->outertext='';
			}
			}
		break;
			
	}

$cats = $mpost->_terms['category'];

/*if(empty($cats)){
	$cat = get_current_category();
	$cats = (is_object($cat) && isset($cat->cat_name))?array($cat):array();
}*/
$cat_html = array();
foreach($cats as $cat){
	if($cat->slug != 'rssfeeds'){
		$cat_html[] = "<span class='category'><a href='".get_category_link($cat)."'>".$cat->cat_name."</a></span>";
	}
}
$cat_html = implode("\n<span class='delimiter'>|</span>\n", $cat_html);

$link = get_permalink( $mpost->ID );
$title  = html_entity_decode($post->post_title);
//$excerpt = strip_tags($post->post_content, '<strong><a>');
$excerpt = $dom->outertext;
$src_html = "<span class='src'>$src</span>";



$tags = get_the_tags($post->ID);
$tags = is_array($tags)?$tags:array();

		$tag_html = array();
		foreach($tags as $tag){
			if(substr($tag->name, 0, 3)!='rss'){
			$tag_html[] = "<a class='tag' href='".get_tag_link($tag)."'>".$tag->name."</a>";	
			}
			
		}
		$tag_html = implode("\n", $tag_html);
		//$tag_html  = "<span class='tag'>$tag_html</span>";
		
$excerpt = (is_string($excerpt) && strlen($excerpt)>0) ? "<div class='excerpt'>".$excerpt."</div>
						<div class='actions'>
							<a class='more'>
								more
							</a>
							<a class='read' href=\"".$link."\">
								read
							</a>							
						</div>" : "<div class='excerpt' style='display:none!important;'></div>
						<div class='actions no-excerpt'>
							<a class='read' href=\"".$link."\">
								read
							</a>							
						</div>";

$html = "<div class=\"post-content $rssCls brick\" id='post_".$post->ID."'>
					<a href=\"".$link."\" class=\"post-content-img\">
						$display_img_tag
					</a>
					<div class='overlay'>
						
						<h1 class=\"title\"><a href=\"".$link."\">".$title."</a></h1>
						<div class='post-cats'>$src_html >> $cat_html</div>
						<div class='post-tags'>$tag_html</div>
						$excerpt
						
						
					</div>

					
					
				</div>";

echo $html;
?>
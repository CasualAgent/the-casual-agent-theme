<?php
	$html = "";
	if(is_single()){
		echo "<h1>SINGLE</h1>";
		$html = $post->post_content;
	}else{
		$post = new cCasualAgentPost($post);
		$cats = $post->getCategories();
		
		$cat_html = array();
		foreach($cats as $cat){
			$cat_html[] = "<span class='category'><a href='".get_category_link($cat)."'>".$cat->cat_name."</a></span>";
		}
		$cat_html = implode("\n<span class='delimiter'>|</span>\n", $cat_html);
		
		$tags = get_the_tags($post->ID);
		$tags = is_array($tags)?$tags:array();
		$tag_html = array();

			foreach($tags as $tag){
				if(substr($tag->name, 0, 3)!='rss'){
					$tag_html[] = "<a class='tag' href='".get_tag_link($tag)."'>".$tag->name."</a>";
				}
			}
			$tag_html = implode(",", $tag_html);
			$tag_html  = "<span class='tag'>$tag_html</span>";
				
		$time_tag = get_age($post->post_date);
		$img = get_the_post_thumbnail( $post->ID, array(300, 'auto'));
		if(empty($img)){
			$dom = str_get_html($post->post_content);
			$imgTags = $dom->find('img');
			$display_img = $imgTags[0];
			$img = isset($display_img->src)?"<img class='post-content-img' src='".$display_img->src."' width='300' height='auto'/>":"";
		}
		
		$attr = tag_attr($img);	
		$maxH = intval($attr['height'])."px";
		$href = get_permalink( $post->ID );
		
		$excerpt = (strlen(trim($post->post_excerpt))>10) ? "<div class='excerpt'>".$post->post_excerpt."</div><div class='actions'>
							<a class='more'>
								more
							</a>
							<a class='read' href=\"".$href."\">
								read
							</a>							
						</div>" : "<div class='excerpt' style='display:none!important;'></div><div class='actions no-excerpt'>
							<a class='read' href=\"".$href."\">
								read
							</a>							
						</div>";
		
		$html = "<div class=\"post-content brick\" id='post_".$post->ID."'>
					
					<a href=\"".$href."\" class=\"post-content-img\">
						$img
					</a>
					<div class='overlay'>
						<h1 class=\"title\"><a href=\"".$href."\">".html_entity_decode($post->post_title)."</a></h1>
						<div class='post-cats'><span class='src'>Exclusive</span> >> $cat_html</div>
						<div class='post-tags'>$tag_html</div>
						$excerpt
						
					</div>
				</div>";
	}
	
	
	/*
	<div class=\"tags\">
								".$cat_html."		
							<time datetime='2013-06-21T09:34:00+00:00' class=\"timestamp\">".$time_tag."</time>
						</div>	
	*/
	echo $html;



?>

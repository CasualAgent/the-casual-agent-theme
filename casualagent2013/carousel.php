<?php
	
	$T = ThemeInstance();
	$cat = $T->get_current_category();
	$sc = \CasualAgentTheme\Theme\Carousel::GetShortCode($cat);
	echo "<section id='carousel' class='carousel'>";
	if(is_string($sc) && !empty($sc)){
		echo do_shortcode($sc);
	}
	echo "</section>";
	/*function out_carousel($tags){
		$cnt = count($tags);
		$set = implode("/!", $tags);
		
		switch($cnt){
			case 1:
				$sc = '[wpic color="black" visible="1" width="996" height="508" speed="0" auto="0"]'.$set.'[/wpic]';
				break;
			case $cnt>1:
				$sc = '[wpic color="black" visible="1" width="996" height="508" speed="800" auto="5000"]'.$set.'[/wpic]';
				break;
			default:
				return;
		}

		echo "<section id='carousel' class='carousel'>";
		echo do_shortcode($sc);
		echo "</section>";
	}
	
	
	/*$carousel = get_carousel_items();
	$set = array();
	foreach($carousel as $img){
		$set[] = "<a href='".get_permalink( $img->post_parent )."'><img src='".$img->guid."' width=\"996\" /></a>";
	}
	
	if(count($set)>0){
		out_carousel($set);	
	}*/
	
		
?>


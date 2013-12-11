<?php get_header(); 
	$T = ThemeInstance();
?>
		<section id="main" class='container-outter'>
			<?php get_template_part('carousel'); ?>
			<section id="main_content" class='container-inner'>			

<?php
global $posts, $post;
	$P = new CasualAgentTheme\Theme\Posts();
	
	$posts = $P->latestPosts('ca');
?>
	<?
	echo "<div class='brick-wall'>";
	foreach($posts as $mpost){
		$GLOBALS['mpost'] = $mpost;
		if(has_tag('rss', $mpost->post) || has_category($cat_rssfeeds, $mpost->post)){	
			get_template_part('content', 'rssfeed');	
		}else{
			get_template_part('content');	
		}
	} //
	echo "</div>";

?>
<pre>
<? 	//$cat = $T->get_current_category();
	//$tg = $cat->term_group;
	//print_r(tag_groups_cloud(array("taxonomy"=>"category, post_tag", "include"=>"5"), true)); 
	
	print_r($posts);
?>
</pre>

				<div class='clearfix'></div>
			</section>
			<div class='clearfix'></div>
		</section>
		<div class='clearfix'></div>
<?php get_footer(); ?>

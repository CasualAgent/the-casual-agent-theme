<?php get_header(); ?>
		<section id="main" class='container-outter'>
			<?php get_template_part('carousel'); ?>
			<section id="main_content" class='container-inner'>			

<?php
	$posts = set_category_page_data();
	$cat_rssfeeds = get_category_by_slug('rssfeeds');
	echo "<div class='brick-wall'>";
	foreach($posts as $p){
		global $post;
		$post = &$p;
		
		if(has_tag('rss', $p) || in_category($cat_rssfeeds, $p)){
			get_template_part('content', 'rssfeed');	
		}else{
			get_template_part('content');	
		}
		
		

	} //
	echo "</div>";

?>

				<div class='clearfix'></div>
			</section>
			<div class='clearfix'></div>
		</section>
		<div class='clearfix'></div>
<?php get_footer(); ?>

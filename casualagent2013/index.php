<?php /*get_header(); ?>
		<section id="main" class='container-outter'>
			<?php get_template_part('carousel'); ?>
			<section id="main_content" class='container-inner'>			

<?php
	
	if ( have_posts() ) {
		echo "<div class='brick-wall'>";
		while ( have_posts() ) {
			the_post();
			get_template_part('content', get_post_format());		
		} // end while
			echo "</div>";		
	} // end if

?>

				<div class='clearfix'></div>
			</section>
			<div class='clearfix'></div>
		</section>
<?php get_footer(); */?>


<?php get_header(); ?>
		<section id="main" class='container-outter'>
			<?php get_template_part('carousel'); ?>
			<section id="main_content" class='container-inner'>			

<?php
	global $posts, $post;
	$posts = set_category_page_data();
	$cat_rssfeeds = get_category_by_slug('rssfeeds');
	/*
	?>
	<pre>
		<? print_r($posts); ?>
	</pre>
	<?*/
	echo "<div class='brick-wall'>";
	foreach($posts as $post){
		if(has_tag('rss', $post) || has_category($cat_rssfeeds, $post)){	
			get_template_part('content', 'rssfeed');	
		}else{
			
			get_template_part('content');	
		}
	} //
	echo "</div>";

?>
<pre>
<? 	$cat = get_current_category();
	$tg = $cat->term_group;
	print_r(tag_groups_cloud(array("taxonomy"=>"category, post_tag", "include"=>"5"), true)); ?>

</pre>
				<div class='clearfix'></div>
			</section>
			<div class='clearfix'></div>
		</section>
		<div class='clearfix'></div>
<?php get_footer(); ?>

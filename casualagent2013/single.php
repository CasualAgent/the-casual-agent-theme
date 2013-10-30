<?php get_header(); ?>
		<section id="main" class='container-outter'>
			<section id="main_content" class='container-inner single-content'>			
<?php
	the_post();
	get_template_part('gallery', 'single');
	the_title("<h1 class='title'>", "</h1>", true);
	echo "<div class='body'>";
	the_content();
	echo "</div>";
	
?>

				<div class='clearfix'></div>
			</section>
			
			<section>
				<pre>
					<?php
						//$qry = get_category_featured_query(get_current_category());
						//print_r($qry);
					?>
				</pre>
			</section>
			<div class='clearfix'></div>
		</section>
<?php get_footer(); ?>

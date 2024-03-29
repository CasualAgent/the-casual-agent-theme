<?php
	ini_set('display_errors', 1);
	define('theCasualAgent_catID', 0);
	define('theCasualAgent_postThumbnailWidth', 300);
	
	require_once("lib/simple_html_dom.php");
	require_once("cls/cPostMeta.php");
	require_once("cls/cCasualAgentPost.php");
	require_once("cfg/menu_categories.cfg.php");
	
	
	$MAIN_QRY_RUNS = array();
	
	function init_casual_agent_theme(){
		global $wp_query;
	}
	add_action('init', 'init_casual_agent_theme');
	
	function configure_casual_agent_theme(){
		add_theme_support('post-formats', array( 'aside', 'gallery','link','image','quote','status','video','audio','chat', 'rssfeed' ) );
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size(theCasualAgent_postThumbnailWidth);
		add_post_type_support( 'post', 'post-formats' );
	}
	add_action('after_setup_theme', 'configure_casual_agent_theme'); 
	
	/******************Scripts/Styles**********************************/
	function casualagent_scripts_styles(){	
		wp_enqueue_style( 'casualagent-style', get_stylesheet_uri());
		
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('imagesLoaded', get_template_directory_uri()."/js/imagesLoaded.min.js", array('jquery','jquery-ui-core'));
		wp_enqueue_script('masonry-ui', get_template_directory_uri()."/js/masonry.min.js", array('jquery','jquery-ui-core', 'imagesLoaded'));
		wp_enqueue_script('page-js', get_template_directory_uri()."/js/page.js", array('jquery','jquery-ui-core','masonry-ui', 'imagesLoaded'));
	}
	add_action( 'wp_enqueue_scripts', 'casualagent_scripts_styles' );
	
	
	function casualagent_admin_scripts_styles(){
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('holder-js', get_template_directory_uri()."/js/holder.js", array('jquery','jquery-ui-core'));
		wp_enqueue_script('ca-admin', get_template_directory_uri()."/js/admin.js", array('jquery','jquery-ui-core'));
		wp_enqueue_script('ca-admin-posts', get_template_directory_uri()."/js/admin-posts.js", array('jquery','jquery-ui-core', 'ca-admin'));
		wp_enqueue_script('ca-admin-posts-editor', get_template_directory_uri()."/js/admin-post-editor.js", array('jquery','jquery-ui-core',  'ca-admin', 'ca-admin-posts'));
		wp_enqueue_script('ca-media', get_template_directory_uri()."/js/media.js", array('jquery','jquery-ui-core', 'ca-admin', 'ca-admin-posts', 'ca-admin-posts-editor'));
	}
	add_action( 'admin_enqueue_scripts', 'casualagent_admin_scripts_styles' );
	

	function tag_attr($tag){
		$raw = $tag;	
		$tag = trim(trim(trim($tag), "<"), "/>");
		$tok = explode(" ", $tag);
		
		$attr = array('tag'=>array_shift($tok), 'raw'=>$raw);
		
		$str = implode(" ", $tok);
		preg_match_all('/\\b([a-zA-Z]+=["\'][\\w\\-\\:\\/\\\\\\_\\.\\s]+["\'])?/i',$str,$matches);
		
		$res = array();
		foreach($matches as $match){
			$res = array_merge($res, $match);
		}
		
		$res = array_unique($res);
		
	
		foreach($res as $item){
			$at = explode("=", $item);
			if(count($at)==2){
				$attr[$at[0]] = trim($at[1], '"');
			}
		} 
				
		return $attr;
	}
	
	function _arr_flatten($arr){
		
		$ret = array();
		foreach($arr as $val){
			if(is_array($val)){
				$ret = array_merge($ret,_arr_flatten($val));
			}else{
				$ret[] = $val;
			}
		}
		
		return $ret;
	}
	function extract_tags($tagName, $html){
		$pattern = '/<'.$tagName.'[^>]*>/';
		$res = preg_match_all($pattern, $html, $matches);
		if($res>0){
			$matches = _arr_flatten($matches);
			$matches = array_unique($matches);
	
			$tags = array();
	
			foreach($matches as $m){
				$tags[] = tag_attr($m);
			}
			return $tags;
		}else{
			return null;
		}
	}
	function find_display_img($tags){
		$src = array();
		foreach($tags as $t){
			if((strtolower(trim($t['tag'])) == 'img') && isset($t['src'])){
				$src[]=$t;
			}
		}
		return $src;
	}



	/******************************************************************/	
	/*******************Category Functions****************************/
	
	/*$CA_MENU_CATEGORIES = array(
		'All'			=>	array(
							'href'	=>	home_url()),
		'Videos'		=>	array(
							
							'name'		=>	'Videos',
							'slug'		=>	'videos'),
		'Photography'	=>	array(
							'cat_ID'	=> -1,
							'name'		=>	'Photography',
							'slug'		=>	'photography'),
		'Music' 		=> 	array(
							'cat_ID'	=> -1,
							'name'		=>	'Music',
							'slug'		=>	'music'),
		'Interviews'	=>	array(
							'cat_ID'	=> -1,
							'name'		=>	'Interviews',
							'slug'		=>	'interviews'),
		'Fiction' 		=> 	array(
							'cat_ID'	=> -1,
							'name'		=>	'Fiction',
							'slug'		=>	'fiction'),,
		'Events'		=> 	array(
							'cat_ID'	=> -1,
							'name'		=>	'Events',
							'slug'		=>	'events'),
		'Columns' 		=> 	array(
							'cat_ID'	=> -1,
							'name'		=>	'Columns',
							'slug'		=>	'columns',
							'children'	=>	array(
												'cat_ID'	=> 382,
												'name' => 'Bar Stool Confessional',
												'slug' => 'barstoolconfessional'),
											array(
												'cat_ID'	=> 383,
												'name' => 'Pillars Of Salt',
												'slug' => 'pillarsofsalt'),
							)
		);*/
	
/******************
	function set_category_data(){
		//global $wp_query;
		/*$data = json_encode($wp_query);
    	$data = addslashes($data);
		echo "<script>
				var log = log || [];
    				log.push('set category data');
    				log.push(JSON.parse(\"".$data."\"));
    		  </script>";*/
/**		global $_CURRENT_CATEGORY, $_FEATURED_POSTS_IN_CATEGORY, $_POSTS_IN_CATEGORY;
		$_CURRENT_CATEGORY = get_current_category();
		$_POSTS_IN_CATEGORY = get_category_posts($curcat);
		$_FEATURED_POSTS_IN_CATEGORY = get_carousel_items($curcat);
	}
******************/	
	
	function init_menu_categories(){
		global $CA_MENU_CATEGORIES;
		$cur = get_current_category();
		
		$fn = create_function('&$cat, $key, $p', 
			'list($cur, $fn)=$p; 
			$cat["href"] = get_category_link($cat["cat_ID"]); 
			$cat["isCurrent"] = ($cur->slug == $cat["slug"]); 
			if(isset($cat["children"]) && is_array($cat["children"])){
				array_walk($cat["children"], $fn, array($cur, $fn));}'
			);
		
		array_walk($CA_MENU_CATEGORIES, $fn, array($cur, $fn));
	}
				
	function get_current_category(){	
		global $wp_the_query;
		
		$id = $wp_the_query->query_vars['cat'];		
		return get_category($id);
	}
	
		
	function get_post_rss_cls($p){
		$src = get_syndication_source(null, $p->ID);
		switch($src){
			case 'The FADER':
				$cls = 'rss-fader';
				break;
			default:
				$cls = 'rss-default';
		}
		
		return $cls;
	}
	function qry_posts($params){
		
		
		
		$args = array();
		$args['order'] = 'DESC';
		$args['order_by'] = 'post_date';
		$args['posts_per_page'] = 25;
		$args['fields'] = 'all';
		$args['post_status'] = 'publish';
		
		$args = array_merge($args, $params);
		
		if(isset($args['cat']) && is_string($args['cat'])){
			$slug = explode('/', trim(stripslashes($args['cat']),'/'));
			$cat = get_category_by_slug(array_pop($slug));
			$args['cat'] = $cat->cat_ID;
		}
		wp_reset_query();
		$qry = new WP_Query($args);
		$res = $qry->get_posts();
		
		
		return array('posts'=>$res, 'qry'=>$args, 'wp'=>$qry);
	}
	
	function get_tag_group_posts($tagGrps, $isRSS=false){
	
		$args = array();
		$tagGrp = "";
		
		$args['tax_query'] = is_array($args['tax_query'])?$args['tax_query']:array();

		$tg = is_array($tagGrps)?implode(", ", $tagGrps):$tagGrps;
		$tagGrp = tag_groups_cloud(array('taxonomy'=>'post_tag,category', 'include'=>$tg), true);
		$terms = array();

		foreach($tagGrp as $idx => $grp){

			$grp['tags'] = is_array($grp['tags'])?$grp['tags']:array();
			foreach($grp['tags'] as $tag){
				$terms[] = $tag['slug'];
			}
		}
			
		$args['tax_query'][] = array('taxonomy'=>'post_tag', 'field'=>'slug', 'terms'=>$terms, 'operator'=>'IN');
		
		
		if($isRSS){
			$args['tax_query'][] = array('taxonomy'=>'post_tag', 'field'=>'slug', 'terms'=>array('rss'), 'operator'=>'IN');
		}else{
			$args['tax_query'][] = array('taxonomy'=>'post_tag', 'field'=>'slug', 'terms'=>array('rss'), 'operator'=>'NOT IN');
		}
		$args['tax_query']['relation'] = 'AND';
		
		$res = qry_posts($args);
		

		return $res['posts'];
	}
	function ajax_get_posts(){
	
		$args = $_REQUEST['args'];
		$tagGrp = "";
		
		$args['tax_query'] = is_array($args['tax_query'])?$args['tax_query']:array();
		if(isset($_REQUEST['tagGroup'])){
			$tg = is_array($_REQUEST['tagGroup'])?implode(", ", $_REQUEST['tagGroup']):$_REQUEST['tagGroup'];
			$tagGrp = tag_groups_cloud(array('taxonomy'=>'post_tag,category', 'include'=>$tg), true);
			$terms = array();
			foreach($tagGrp as $idx => $grp){
				$grp = (array)$grp;
				foreach($grp['tags'] as $tag){
				
				//print_r($tag);
					$terms[] = $tag['slug'];
				}
			}
			
			$args['tax_query'][] = array('taxonomy'=>'post_tag', 'field'=>'slug', 'terms'=>$terms, 'operator'=>'IN');
		}
		
		if(isset($_REQUEST['isRSS']) && $_REQUEST['isRSS']>0){
			$args['tax_query'][] = array('taxonomy'=>'post_tag', 'field'=>'slug', 'terms'=>array('rss'), 'operator'=>'IN');
		}else{
			$args['tax_query'][] = array('taxonomy'=>'post_tag', 'field'=>'slug', 'terms'=>array('rss'), 'operator'=>'NOT IN');
		}
		$args['tax_query']['relation'] = 'AND';
		
		$res = qry_posts($args);
		$fmt = $_REQUEST['type'];
		
		switch($fmt){
			case 'content-box':
				$cat_rssfeeds = get_category_by_slug('rssfeeds');
				foreach($res['posts'] as $p){
					global $post;
					$post = $p;
					if(has_tag('rss', $post) || has_category($cat_rssfeeds, $post)){	
						get_template_part('content', 'rssfeed');
					}else{
						get_template_part('content');
					}
				}
				echo "<!--".json_encode($res['qry'])."-->";
				break;
			case 'json':
				global $wpdb;
				$res['wpdb'] = $wpdb;
				$tg = is_array($tg)?implode(", ", $tg):$tg;
				$res['tag'] = $tagGrp;
				echo json_encode($res);
				break;
			case 'json-ca':
				$ret = array();
				foreach($res['posts'] as $p){
					$cap = new cCasualAgentPost($p);					
					$ret[] = $cap->getPost();
				}
				$res['posts'] = $ret;
				//$res['tag'] = $tagGrp;
				echo json_encode($res);
				break;
			default:
				echo json_encode($_REQUEST);
		}
		die();
	}
	add_action('wp_ajax_get_post_qry_results', 'ajax_get_posts');
	add_action('wp_ajax_nopriv_get_post_qry_results', 'ajax_get_posts');
	
	function set_category_page_data(){
		/*global $wp_the_query, $posts;
		$cat = (is_home() || is_front_page()) ?null : get_current_category();
		
		$args = array();

		$args['order'] = 'DESC';
		$args['order_by'] = 'post_date';
		$args['posts_per_page'] = 25;
		$args['fields'] = 'all';
		$args['post_status'] = 'publish';
		
		if(!is_null($cat)){
			$args['cat'] = $cat->cat_ID;
		}else{
			$args['cat'] = null;
		}
		
		$args['tax_query'] = array(
			    array(
			      'taxonomy' => 'post_tag',
			      'field' => 'slug',
			      'terms' => array('rss'),
			      'operator' => 'NOT IN'
			    )
		);
		wp_reset_query();
		$ca_posts = get_posts($args);
		$args['tax_query'] = array(
			    array(
			      'taxonomy' => 'post_tag',
			      'field' => 'slug',
			      'terms' => array('rss'),
			      'operator' => 'IN'
			    )
		);
		
		wp_reset_query();
		$rss_posts = get_posts($args);

		
		$posts = array_merge($ca_posts, $rss_posts);
	
	
		return $posts;*/
		global $posts;
		
		$cat = (is_home() || is_front_page()) ?null : get_current_category();
		
		$termGrp = array();
		if(!is_null($cat) && $cat->term_group == 0){
			$cats = get_categories('parent='.$cat->cat_ID);
			foreach($cats as $c){
				$termGrp[] = $c->term_group;
			}
		}else{
			$termGrp = is_null($cat)?get_option('tag_group_ids'):array($cat->term_group);
		}
		
		$ca_posts = get_tag_group_posts($termGrp, false);
		$rss_posts = get_tag_group_posts($termGrp, true);
		
		$posts = array_merge($ca_posts, $rss_posts);
		return $posts;
	}
	
	/*******************Category Functions END****************************/
	
	
	/*******************Carousel Functions****************************/
	
	function get_category_posts($cat = null, $isMainQry = true, $offset = 0){
		global $wp_query;
		$cat = is_null($cat)? get_current_category():$cat;
		
		$args = $wp_query->query_vars;
				
		//$args['meta_query'] = array(array('key'=>'casual_agent_is_featured', 'value'=>'yes'));
		$args['posts_per_page'] = 25;
		$args['fields'] = 'all';
		$args['offset'] = $offset;
		$args['category'] = $cat->cat_ID;
		
		$qry = ($isMainQry)? $wp_query : new WP_Query($args);
		$res = $qry->get_posts($args);
		 
		return $res; 
	}
	
	function get_category_featured_query($cat){
		$args = array();
		
		$args['meta_query'] = array(
			array('key'=>'casual_agent_is_featured', 'value'=>'yes'),
			array('key'=>'casual_agent_post_featured_ready', 'value'=>'yes'));
		$args['posts_per_page'] = 25;
		$args['fields'] = 'all';
		$args['cat'] = $cat->cat_ID;
		
		$qry = new WP_Query($args);
		
		return $qry;
	}
	function get_category_featured($cat = null){
		$cat = (is_null($cat) || !(is_object($cat))) ? get_current_category() : $cat;
		
		$qry = get_category_featured_query($cat);
		
		$res = $qry->get_posts();
		array_walk($res, '_array_walk_get_post_meta');
		$res['qry'] = $qry;
		return $res;
	}
	
	function get_category_items($cat=null, $qry = null, $rss=false){
		$cat = (is_null($cat) || !(is_object($cat))) ? get_current_category() : $cat;
		
		$args = is_null($qry) ? array() : $qry->query_vars;

		$args['order'] = 'DESC';
		$args['posts_per_page'] = 25;
		$args['fields'] = 'all';
		$args['post_status'] = 'published';
		$args['cat'] = $cat->cat_ID;
		if($rss){
			$args['tax_query'] = array(
			    array(
			      'taxonomy' => 'post_format',
			      'field' => 'slug',
			      'terms' => array('rssfeed'),
			      'operator' => 'IN'
			    )
			);
		}else{
			$args['tax_query'] = array(
			    array(
			      'taxonomy' => 'post_format',
			      'field' => 'slug',
			      'terms' => array('rssfeed'),
			      'operator' => 'NOT IN'
			    )
			);
		}
		if(is_null($qry)){
			$qry = new WP_Query($args);
		}
		$res = $qry->get_posts($args);
		
		return $res;
	}
	
	function get_post_attachments($parent, $args = array()){
		$aargs = array(
				'order' => 'ASC',
				'post_status' => 'inherit',
				'post_parent'=> (($parent instanceof WP_Post) ? $parent->ID : $parent),
				'post_type' => 'attachment');
		
		foreach($args as $p => $val){
			$aargs[$p] = $val;
		}
		
		$qry = new WP_Query($aargs);
		$res = $qry->get_posts($aargs);
		
		return $res;
	}
	
	
	function get_carousel_items(){
		$featured = get_category_featured();
		
		$args = array('meta_query'		=> 
						array(
							array('key'	=>	'casual_agent_attachment_type', 'value'=>'carousel')),
					'post_mime_type'	=> 	'image');
		$items = array();
		foreach($featured as $f){
			if($f instanceof WP_Post){
				$res = get_post_attachments($f, $args);
				$set = array_merge($items, $res);
				$items = $set;
			}
		}
		array_walk($items, '_array_walk_get_post_meta');
		return $items;
	}
	
	function _array_walk_get_post_meta(&$obj){
		$obj->meta = get_post_meta($obj->ID);
	}
	/*******************Carousel Functions END****************************/
	
		
	function get_ca_tags($post){
		
		return get_the_terms( $post->ID, 'tags' );
	}
	function get_age($dt){
		
		$u = array('y'=>'year','m'=>'month','d'=>'day','days'=>'day','h'=>'hr','i'=>'min','s'=>'sec');
		$diff = (array)date_diff(date_create($dt), date_create()); 
		
		
		$age = 'now';
		foreach($u as $t => $unit){
			if($diff[$t] > 0){
				$age = $diff[$t]." ".$unit.(($diff[$t] > 1)?"s":"");
				break;
			}
		}
		
		return $age;
	}	
	
	function configure_main_query(){
		global $wp_query, $posts;
		
		$wp_query->set('posts_per_page', 25);
		
	}
	
	function get_featured_img($post){
	
		if(has_post_thumbnail()){
			return get_the_post_thumbnail($post->ID);
		}else{
			return strip_tags($post->post_excerpt, "<img>");
		}

	}
	
	function get_first_post_img($post){
		$html = $post->post_content;
		 preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $html, $image);
		 
		 echo $image['src'];
		 return $image['src'];
	}
	//add_action( 'pre_get_posts', 'configure_main_query' );
	
	
	
	
	/**************Admin*********************************/
	
	function _array_walk_get_sub_categories(&$parent){
		$args = array('child_of'=>$parent['cat_ID'], 'orderby'=>'slug', 'order'=>'DESC', 'hide_empty'=> 0, 'exclude'=>'1');
		$child = get_categories($args);
		
		foreach($child as $i){
			//$i->href = get_category_link($i->cat_ID);
			$parent['children'][] = (array)$i;
		}
	}
	
	function update_menu_cfg(){
		global $CA_MENU_CATEGORIES;
		$cfgPath = get_template_directory()."/cfg/menu_categories.cfg.php";
		
		$c = array(
			
			(array)get_category_by_slug('magazine'),
			(array)get_category_by_slug('art-and-culture'),
			(array)get_category_by_slug('columns'),
			(array)get_category_by_slug('music'),
			(array)get_category_by_slug('videos'),
			(array)get_category_by_slug('photography'),
			//(array)get_category_by_slug('interviews'),
			//(array)get_category_by_slug('fiction'),
			(array)get_category_by_slug('events'));
		
		//array_walk($c, create_function('&$cat', '$cat["href"] = get_category_link($cat["cat_ID"]);'));
		
		array_walk($c, '_array_walk_get_sub_categories');
		
		file_put_contents($cfgPath, '<?php $CA_MENU_CATEGORIES = '.var_export($c, true).'; ?>');
		
		$CA_MENU_CATEGORIES = $c;
		return $c;
	}
	
	function add_casual_agent_metabox(){
		global $post;
		
		$op = cCasualAgentPostMeta::GetObj($post);
		
		
		$id = 'casual_agent_featured_post';
		$title = 'Casual Agent Feature';
		$post_type = 'post';
		$context = 'side';
		$priority = 'high';
		$callback = '_get_casual_agent_post_featured_metabox';
		
		add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
		
		
		$id = 'casual_agent_post_attachments';
		$title = 'Casual Agent Post Attachments';
		$post_type = 'post';
		$context = 'advanced';
		$priority = 'high';
		$callback = '_get_casual_agent_post_images_metabox';
	
		add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
		
		$id = 'casual_agent_post_data';
		$title = 'Casual Agent Post data';
		$post_type = 'post';
		$context = 'advanced';
		$priority = 'high';
		$callback = '_get_casual_agent_post_data_metabox';
	
		add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
		casualagent_admin_scripts_styles();
		
		wp_enqueue_script('admin-post-editor', get_template_directory_uri()."/js/admin-post-editor.js");
         
	}
	add_action( 'add_meta_boxes', 'add_casual_agent_metabox' );
	
	
	function _get_casual_agent_post_data_metabox($post, $metabox){
		$o = cCasualAgentPostMeta::GetObj($post);
		
		$meta = $o->get_meta('all');
		$data = $o->get_data('all');
		
		
		
		$at = $o->get_attachments(array(array('key'=>'_attachment_type', 'value'=>array('none', 'gallery', 'display', 'carousel'))));
		$thumb = get_post(get_post_thumbnail_id($post->ID));
		echo "<pre>";
		$meta['ID']=$o->ID;
		print_r($meta);
		print_r(get_post_meta($post->ID));
		echo "ID:".$post->ID;
		echo get_post_thumbnail_id($post->ID);
		echo "</pre>";
	}
	
	function _get_casual_agent_post_images_metabox($post, $metabox){
		
		$ca = cCasualAgentPostMeta::GetObj($post);
		$at = $ca->get_attachments(array(array('key'=>'_attachment_type', 'value'=>array('none', 'gallery', 'display'))));
		
		if(has_post_thumbnail($post->ID)){
			$thumb = get_post(get_post_thumbnail_id($post->ID));
			if($thumb){
				array_unshift($at, $thumb);
			}
		}
		
		
		
		
		
		$html = array();
		foreach($at as $a){
			$src = $a->guid;
			$cls = isset($a->cls)?"class=\"".$a->cls."\"":"";
			$oat = cCasualAgentPostMeta::GetObj($a);

			$type = $oat->get_meta('_attachment_type');
			
			if($type != 'carousel'){
			
			$html[] = "<div class='ca_attachment'>
						<img $cls src='$src' height=175 style='display:inline-block;' data-post-ID='".$a->ID."'/>
						<div class='meta'>
							<select name='ca_attach_type'>
								<option value='gallery'>gallery</option>
								<option value='carousel'>carousel</option>
								<option value='display'>display</option>
							</select>
						</div>
						
						</div>";
						}
		}		
		
		$html[] = "<a class='upload_img'>New Attachment</a>";		
		$html = implode("\n", $html);
		
		$style = "<style>
					.ca_attachment{
						display:inline-block;
						position:relative;
					}
					
					.ca_attachment img{
						display:block;
					}
					.meta{
						display:block;
					}
					.ca_attach_bttn{
						display:block;
						position:relative;
						margin:2px 10px;
						
					}
					
				</style>";
		echo $style."<div id='ca_post_attachments'>".$html."</div>";
		
	}
	
	function _get_casual_agent_post_featured_metabox($post, $metabox){
	
	

		$op = cCasualAgentPostMeta::GetObj($post->ID);
		$isFeatured = (cCasualAgentPostMeta::_BOOL($op->get_meta('_is_featured', true)));
		$isFeatured_chkBox = get_ca_featured_post_chkbox($op->ID, 'Featured Post');

		$html = array($isFeatured_chkBox);
		
		if($isFeatured){	
			$args = array(
				'post_type'=>'attachment',
				'post_mime_type'=>'image',
				'post_status'=>'inherit',
				'post_parent'=>$post->ID,
				'meta_query'=>array(array('key' => CA_META_PREFIX."_attachment_type", 'value'=>'carousel'))
			);
			
			$at_qry = new WP_Query($args);	
			
			$at = $at_qry->get_posts();
			
			//$at[] = (object)array('guid'=>"holder.js/300x224/text:Click to Attach Image", 'cls'=>'ca_attach');
			
			/*$banner = "<div class='ca_attachment ca_attachment_type_carousel'>
								<img class='ca_attachment image-carousel' data-src='holder.js/996x508/text:Click to Attach Image' style='display:inline-block;height:auto!important;width:80%!important;'/>
							</div>";*/
			if(count($at)>=1){
					
					foreach($at as $idx => $a){
						if($idx == 0){
							$src = $a->guid;
							//$cls = isset($a->cls)?"class=\"".$a->cls."\"":"";
					
							$html[] = "<div class='ca_attachment ca_attachment_type_carousel'>
										<img src='$src' style='display:inline-block;width:100%;height:auto!important;'/>
										<div class='actions'>
											<a data-args='".json_encode(array('action'=>'post_detach_item', 'attachment_ID'=> $a->ID))."' class='action ajax_action'>Detach</a>
										</div>
									</div>";
								
						}else{
							$ap = cCasualAgentPostMeta::GetObj($a);
							$ap->set_data('post_parent', null);
							$ap->set_meta('_attachment_type', null);
							
							$html[] = "<div class='ca_attachment ca_attachment_type_carousel detached'>
										<img src='$src' style='display:inline-block;width:40%;height:auto!important;'/>
										<div class='note detached'>was detached</div>
									</div>";
						}
					}
					
						
			}else{
					$html[] = "
						<h1 style='color:red;font-size: 0.9em;position: absolute;top: -10px;right: 25px;'>NOT ACTIVE</h1>
						<div class='note'>
							<p>Please attachment image to be shown in the Carousel! Image should be formatted to be properly displayed in the Carousel (996w x  508h)
								This post will not be active until a Carousel image is assigned!
							</p>
						</div>
						<a title='Set Featured Carousel Image'  id='set-ca-featured-post-image' data-fn='ca_setup_featured_post' class='thickbox media_upload'>Set Featured Carousel Image</a>";		
			}	
		}else{
			$html[] = "<h1 class='metabox_note'>Not Featured</h3>";
		}
			
		$html = implode("\n", $html);
		
		$style = "<style>
					.note{
						font-size:0.9em;
						font-style:italic;
						padding:0.3em;
					}
					.ca_attachment{
						display:inline-block;
						position:relative;
					}
					.actions{
						display:block;
					}
					
					#set-ca-featured-post-image{
						display:block;
						text-decoration:underline;
						font-style:italic;
					}
					
					.ca_attachment img{
						display:block;
					}
					.ca_attachment.detached{
						width:50%;
						border:5px dotted red;
					}
					.ca_attachment.detached:after{
						content:'detached';
					}
					.meta{
						display:block;
					}
					.ca_attach_bttn{
						display:block;
						position:relative;
						margin:2px 10px;
						
					}
					
				</style>";
		echo $style."<div id='ca_post_attachments'>".$html."</div>";
				//print_r($metabox);
	}
	/* Display custom column */
	function display_posts_featured_status( $column, $post_id ) {
		echo get_ca_featured_post_chkbox($post_id);
	}
	add_action( 'manage_posts_custom_column' , 'display_posts_featured_status', 10, 2 );
	
	function get_ca_featured_post_chkbox($post_id, $label = null){

		$ca = cCasualAgentPostMeta::GetObj($post_id);
		
		$checked = ($ca->get_meta('_is_featured') == 'yes')? "checked" : "";
	    
	    $label = (!is_null($label) && !empty($label))?"<label for='ca_is_featured'>Featured Post</label>":"";
	    return '<input name="ca_is_featured" class="chkbox_ca_is_featured" data-post-id="'.$post_id.'" type="checkbox" '.$checked.' />&nbsp;&nbsp;'.$label;
	}
	/* Add custom column to post list */
	function add_posts_featured_status( $columns ) {
		
		$cols = array();
		
		$i = 0;
		foreach($columns as $key => $val){
			if($i==2){
				$cols[CA_META_PREFIX.'_is_featured']=__( 'Featured', 'your_text_domain' );
			}
			$cols[$key] = $val;
			$i++;
		}
		
		return $cols;
	}
	add_filter( 'manage_posts_columns' , 'add_posts_featured_status' );	
	
	
	function qry_attachment($params, $single = false){
		
		$args = array(
			'post_type'=>'attachment',
			'post_mime_type'=>'image',
			'post_status'=>'inherit',
		);
		
		foreach($params as $p=>$val){
			$args[$p] = $val;
		}
		
		$qry = new WP_Query($args);
		
		$res = $qry->get_posts();
		
		if($single && count($res) >= 1){
			return array_shift($res);
		}
		
		if(empty($res)){
			return false;
		}else{
			return $res;	
		}
		
	}
	
	function ajax_qry_attachments(){
		$p = $_REQUEST['qry'] || false;
		if(!$p){
			echo json_encode(array('err'));
			die(false);
		}
		echo json_encode(qry_attachment((array)$_REQUEST['qry']));

		die();
	}
/*	function add_featured_content_toggle($actions, $post) {
			
			$ca_post = cCasualAgentPostMeta::GetObj($post);
			
			
			$isFeatured = $ca_post->get_meta("_is_featured", true);

			$post_type_object = get_post_type_object($post->post_type);
			if (current_user_can($post_type_object->cap->edit_post, $post->ID)) {
				$link = add_query_arg(array('action' => 'set_ca_post_feature_status', 'data-val'=>$isFeatured, 'val' => (($isFeatured == 'yes') ? 'no' : 'yes'), 'post_ID' => $post->ID), admin_url('admin-ajax.php'));
				$text = ($isFeatured == 'yes') ? __('Unfeature') : __('Feature');
				$actions['featured-content'] = sprintf('<a class="is-featured-content-toggle" href="%s">%s</a>', $link, $text);
			}

			return $actions;
		}*/
		
	function set_ca_featured_status(){
		
		$ca = cCasualAgentPostMeta::GetObj($_REQUEST['post_ID']);
		if(is_null($ca)){
			return false;
		}
		
		$val =  $_REQUEST['val'];

		$ca->set_meta('_is_featured', $val, false);
		
		echo json_encode(array('_is_featured' => $ca->get_meta('_is_featured'), 'req'=>$_REQUEST, 'obj'=>$ca->get_meta()));
		die();
		
	}
	
	function setup_ca_featured_post(){
		$post_ID = $_REQUEST['post_ID'];
		$at = $_REQUEST['attachment'];
		
		$ca = cCasualAgentPostMeta::GetObj($post_ID);
		
		$at = (isset($_REQUEST['attachment']['ID'])) ? 
				cCasualAgentPostMeta::GetObj($_REQUEST['attachment']['ID']) : (isset($_REQUEST['attachment']['guid'])? cCasualAgentPostMeta::GetByGUID($_REQUEST['attachment']['guid']) : array());
		
		$at = (count($at)==1)?$at[0]:null;

		if(is_null($at)){
			echo json_encode(array('err'=>'attachment does not exist', 'at'=>$at));
			die();
		}

		$old = $ca->get_attachments('carousel');

		foreach($old as $a){
			$ca->detach_image($a->ID);
		}
		
		$ca->set_featured_status('yes');
		$res = $ca->attach_image($at->ID,'carousel');
		
		echo json_encode($res);
		die();
		
	}
	add_action('wp_ajax_setup_ca_featured_post', 'setup_ca_featured_post');
	
	function set_ca_post_featured_status($post_id, $val){
		if(!($post_id>0)){
			return false;
		}

		$ca = cCasualAgent::GetObj($post_id);
		
		if(!($ca instanceof cCasualAgent)){
			return false;
		}
		
		switch($val){
			case 'yes':
					$ca->set_meta('_is_featured', 'yes');
				break;		
			case 'no':
			
				break;
				
			default:
				if(is_bool($val)){
					$val = ($val === true)?'yes':'no';
					return set_ca_post_featured_status($post_id, $val);
				}
				
		}
	}
	
	function ajax_set_ca_post_featured_status(){
		
	}
	
	function post_attach_item(){
		/* Request Format */
		/*array(	
				'action'=>'post_attach_item',
				'attachment'=>array(
					'ID'=>8,
					'guid'=>'url'
					'meta'=>array(
						key=>val,
						key=>val),
					'data'=>array(
						fld=>val,
						fld=>val
					)),
			 	'parent'=>array(
					'ID'=>8,
					'meta'=>array(
						key=>val,
						key=>val),
					'data'=>array(
						fld=>val,
						fld=>val
					)));*/
	
	
	
		$attachment = (array)$_REQUEST['attachment'];
		$parent = (array)$_REQUEST['parent'];
		
		if(!isset($attachment['ID'])){
			$_p = qry_attachment(array('guid'=>$attachment['guid']), true);	
			$_p = is_array($_p)?array_shift($_p):$_p;
			
			if(!is_object($_p)){
				die();
			}
			if(isset($_p->ID)){
				$attachment['ID'] = $_p->ID;
			}
		}
		
		$at = cCasualAgentPostMeta::GetObj($attachment['ID']);		
		$p = cCasualAgentPostMeta::GetObj($parent['ID']);	
		
		$at->set_data('post_parent', $p->ID);
		
		$ret = array();
			
		if(isset($attachment['meta'])){
			$at->set_meta_array($attachment['meta']);
		}
		if(isset($attachment['data'])){
			$at->set_data_array($attachment['data']);
		}
		
		if(isset($parent['meta'])){
			$p->set_meta_array($parent['meta']);
		}
		if(isset($parent['data'])){
			$p->set_data_array($parent['data']);
		}
		
		$at->reload_post();
		$p->reload_post();
		
		$ret = array(
		'attachment'=>array('ID'=>$at->ID, 'meta'=>$at->get_meta('all'), 'data'=>$at->get_data('all')),
		//'parent'=>array('ID'=>$p->ID, 'meta'=>$p->get_meta('all'), 'data'=>$p->get_data('all'))
		'parent'=>$p
		);
		
		echo json_encode($ret);
		die();
	}
	add_action('wp_ajax_post_attach_item', 'post_attach_item');
	
	function post_detach_item(){
	/* Request Format */
		/*array(	
				'action'=>'post_attach_item',
				'attachment'=>array(
					'ID'=>8,
					'meta'=>array(
						key=>val,
						key=>val),
					'data'=>array(
						fld=>val,
						fld=>val
					)),
			 	'parent'=>array(
					'ID'=>8,
					'meta'=>array(
						key=>val,
						key=>val),
					'data'=>array(
						fld=>val,
						fld=>val
					)));*/
		$attachment_ID = $_REQUEST['attachment_ID'];

		$at = cCasualAgentPostMeta::GetObj($attachment_ID);
		$post_ID = $at->get_data('post_parent');
		$ca = cCasualAgentPostMeta::GetObj($post_ID);
		
		$ca->detach_image($at->ID);
		$ret = $ca->get_tree(array('attachments', 'meta'));
		echo json_encode($ret);
		die();
	}
	
	add_action('wp_ajax_post_detach_item', 'post_detach_item');
	
	
	function get_post_json(){
		$obj = get_post($_REQUEST['ID']);
		$obj->meta = get_post_meta($_REQUEST['ID']);
		echo json_encode($obj);
		die();
	}
	
	function clear_post_meta(){
		$id = $_REQUEST['post_ID'];
		
		$meta = get_post_meta($id);
		
		foreach($meta as $key=>$val){
			delete_post_meta($id, $key);
		}
		
		echo json_encode(get_post_meta($id));
		die();
	}
	
	add_action('wp_ajax_set_ca_post_feature_status', 'set_ca_featured_status');
	add_action('wp_ajax_set_ca_post_meta', 'set_ca_post_meta');
	//add_action('wp_ajax_get_ca_post_meta', 'get_ca_post_meta');
	add_action('wp_ajax_get_post_json', 'get_post_json');
	add_action('wp_ajax_qry_attachment', 'ajax_qry_attachment');
	

add_action('wp_ajax_clear_post_meta', 'clear_post_meta');
?>
<?php
	namespace CasualAgentTheme;
	require_once("Theme/Carousel.php");
	require_once("Theme/Posts.php");
//	require_once('Admin/PostList.php');
//	require_once('Admin/PostEditor.php');

	//use \CasualAgentTheme\Admin\PostList as PostList;
	//use \CasualAgentTheme\Admin\PostEditor;
	
	class Theme{
		
		protected static $_instance = null;
		protected static $_menuCfg = null;
		 
		protected function __construct(){
			
			
			
			self::$_instance = $this;
		}
	
		
		public static function Instance(){
			if(is_null(self::$_instance)){
				new self();
			}	
			return self::$_instance;
		}	
	
		public static function Init(){
			require(get_template_directory()."/cfg/menu_categories.cfg.php");
			self::$_menuCfg = $CA_MENU_CATEGORIES;
			
			$me = self::Instance();
			
			add_action('after_setup_theme', array($me, 'configure')); 
			add_action( 'wp_enqueue_scripts', array($me, 'loadScripts')); 
		}
		
		function loadScripts(){
			wp_enqueue_style( 'casualagent-style', get_stylesheet_uri());
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('imagesLoaded', get_template_directory_uri()."/js/imagesLoaded.min.js", array('jquery','jquery-ui-core'));
			wp_enqueue_script('masonry-ui', get_template_directory_uri()."/js/masonry.min.js", array('jquery','jquery-ui-core', 'imagesLoaded'));
			wp_enqueue_script('page-js', get_template_directory_uri()."/js/page.js", array('jquery','jquery-ui-core','masonry-ui', 'imagesLoaded'));
		}
		
		function configure(){
			//add_theme_support('post-formats', array( 'aside', 'gallery','link','image','quote','status','video','audio','chat', 'rssfeed' ) );
			add_theme_support( 'post-thumbnails' );
			set_post_thumbnail_size(300);
			//add_post_type_support( 'post', 'post-formats' );
		}
		
		function get_current_category(){	
			global $wp_the_query;
		
			$id = $wp_the_query->query_vars['cat'];		
			return get_category($id);
		}
		
		
		function getMenu(){
			
			$cur = $this->get_current_category();
		
			$fn = create_function('&$cat, $key, $p', 
			'list($cur, $fn)=$p; 
			$cat["href"] = get_category_link($cat["cat_ID"]); 
			$cat["isCurrent"] = ($cur->slug == $cat["slug"]); 
			if(isset($cat["children"]) && is_array($cat["children"])){
				array_walk($cat["children"], $fn, array($cur, $fn));}'
			);
			$menu = self::$_menuCfg;
			array_walk($menu, $fn, array($cur, $fn));
			return $menu;
		}
		
	}
	
	
?>
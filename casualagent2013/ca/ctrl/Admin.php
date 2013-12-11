<?php
	namespace CasualAgentTheme;
	require_once('Admin/PostList.php');
	require_once('Admin/PostEditor.php');

	use \CasualAgentTheme\Admin\PostList as PostList;
	use \CasualAgentTheme\Admin\PostEditor;
	
	class Admin{
		
		protected static $_instance = null;
		
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
			add_action( 'add_meta_boxes', '\CasualAgentTheme\Admin\PostEditor::PostMetaBox');
			add_action( 'admin_enqueue_scripts', array(get_called_class(), 'LoadScripts'));
		
			add_action( 'manage_posts_custom_column' ,'\CasualAgentTheme\Admin\PostList::GetCol', 10, 2 );
			add_filter( 'manage_posts_columns' , '\CasualAgentTheme\Admin\PostList::CfgCols');	
		add_action('wp_ajax_set_ca_carousel_img', '\CasualAgentTheme\Admin\PostEditor::AJAX');
			add_action('wp_ajax_attach_post_img', '\CasualAgentTheme\Admin\PostEditor::AJAX');
		}
		
		public static function LoadScripts(){
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('holder-js', get_template_directory_uri()."/js/holder.js", array('jquery','jquery-ui-core'));
			wp_enqueue_script('ca-admin', get_template_directory_uri()."/js/admin.js", array('jquery','jquery-ui-core'));
			wp_enqueue_script('ca-admin-posts', get_template_directory_uri()."/js/admin-posts.js", array('jquery','jquery-ui-core', 'ca-admin'));
			wp_enqueue_script('ca-admin-posts-editor', get_template_directory_uri()."/js/admin-post-editor.js", array('jquery','jquery-ui-core',  'ca-admin', 'ca-admin-posts'));
			wp_enqueue_script('ca-media', get_template_directory_uri()."/js/media.js", array('jquery','jquery-ui-core', 'ca-admin', 'ca-admin-posts', 'ca-admin-posts-editor'));
			
		}
	}
	
	
?>
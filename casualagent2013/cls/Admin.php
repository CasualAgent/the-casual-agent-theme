<?php
	require_once('AdminMenus.php');
	require_once('AdminPosts.php');
	require_once('AdminPostList.php');
	
	add_action( 'admin_init', array('Admin', 'Init'));
	add_action( 'admin_init', array('AdminPosts', 'Init'));
	add_action( 'admin_init', array('AdminMenus', 'Init'));
	add_action( 'admin_init', array('AdminPostList', 'Init'));
	
	
	class Admin{
			
		public static function Render($view, $params){
			extract($params, EXTR_OVERWRITE);
			
			switch($view){
				case 'post_isfeatured_chkbox':
					 $label = (isset($label) && strlen($label)>0)?'&nbsp;&nbsp;'.$label:'';
					 return '<input name="ca_is_featured" class="chkbox_ca_is_featured" data-post-id="'.$post_ID.'" type="checkbox" '.$checked.' />'.$label;
			    
			}
			
			return null;
		}
		
		public static function Init(){
			
			add_action( 'admin_enqueue_scripts', array('Admin', 'LoadScripts'));
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
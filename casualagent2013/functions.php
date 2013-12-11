<?php
	//require_once('cls/Admin.php');
	
	define('caTHEME_DIR', get_template_directory().'/ca/');
	define('caTHEME_MODEL_DIR', get_template_directory().'/ca/model/');
	define('caTHEME_CTRL_DIR', get_template_directory().'/ca/ctrl/');
	define('caTHEME_VIEW_DIR', get_template_directory().'/ca/view/');
	
	require_once(caTHEME_CTRL_DIR."Admin.php");
	require_once(caTHEME_CTRL_DIR."Theme.php");
	if(!function_exists('file_get_html')){
		require_once("lib/simple_html_dom.php");
	}
	
	require_once("cls/cCasualAgentPost.php");
	function ResetWPDB(){
	
		global $wpdb;
		$wpdb->flush();
		$wpdb->db_connect();
	
		return $wpdb;
	}
		
	function SendJSON($msg){
		header('Content-Type: text/json; charset=utf-8 ');
		echo json_encode($msg);
		die();
	}
	add_action('init', '\CasualAgentTheme\Theme::Init');
	add_action('admin_init', '\CasualAgentTheme\Admin::Init');	
	//add_action('after_setup_theme', 'configure_casual_agent_theme'); 
	
	function ThemeInstance(){
		return \CasualAgentTheme\Theme::Instance();
	}
	
	
?>
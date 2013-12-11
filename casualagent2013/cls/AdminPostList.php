<?php
	require_once('cCasualAgentPost.php');
	
	class AdminPostList{

		private $_post = null;
		private $_capost = null;
		public $ID = null;
		
		function __construct($post_id){
			$this->ID = $post_id;
		}
		
		function featuredCol(){
			if(function_exists('is_syndicated') && is_syndicated($post_id)){
				return 'N/A';	
			}else{
				$isFeaturedChk = ($this->capost->getMeta('_is_featured') == 'yes')?'checked':'';
				return Admin::Render('post_isfeatured_chkbox', array('post_ID'=>$this->ID, 'checked'=>$isFeaturedChk));
			}
		}
		
		function __get($prop){
			
			switch($prop){
				case 'post':
					if(is_null($this->_post)){
						ResetWPDB();
						$this->_post = get_post($this->ID);
					}
					return $this->_capost;
				case 'capost':
					if(is_null($this->_capost)){
						$this->_capost = new cCasualAgentPost($this->ID);
					}
					return $this->_capost;
				break;
			}
		}
		
		
		/*******AJAX Handlers******************/
		
		/*******Public Static Methods *********/
		
		public static function Init(){
			add_action( 'manage_posts_custom_column' , array('AdminPostList', 'SetColData'), 10, 2 );
			add_filter( 'manage_posts_columns' , array('AdminPostList', 'CfgCols'));	
		}
		
		public static function CfgCols($cols){
		
			$keys = array_keys($cols);
			$vals = array_values($cols);
			
			array_splice($keys, 2, 0, 'isFeatured');
			array_splice($vals, 2, 0, 'Featured');
			
			return array_combine($keys, $vals);
			
		}
		
		public static function SetColData($column, $post_id){
			
			$self = new self($post_id);
			$html = null;
			switch($column){
				case 'isFeatured':
					$html = $self->featuredCol();
					break;
			}
			
			echo $html;
		}
		/*******Private Methods *********/
	}
?>
<?php

	class cCasualAgentPost{
	
		protected static $_tax = null;
		protected static $_cat_tag_groups = null;
		protected $_categories = null;
		protected $_terms = null;
		protected $_tag_groups = null;
		
		function __construct($_post){
			
			if(is_numeric($_post)){
				$this->_post = WP_Post::get_instance($_post);
			}else if($_post instanceof WP_Post){
				$this->_post = $_post;
			}else{
				throw new Exception("Must supply post object or post ID to create new instance of cCasualAgentPost");
			}
			
			$this->setTerms();
			$this->setCategories();
			
		}
		
		public static function InitStatic(){
			if(is_null(self::$_tax)){
				self::$_tax = get_taxonomies();
			}
			
			self::$_cat_tag_groups = tag_groups_cloud(array("taxonomy"=>"category"), true);
		}
		
		private function setCategories(){
			if(is_null($this->_categories)){
				$catTerms = $this->_terms['category'];
				
				$cats = array();
				foreach($catTerms as $term){
					$cat = get_category_by_slug($term->slug);
					$cats[$cat->cat_ID] = $cat;
				}
				
				$tagGrps = $this->_tag_groups;
				
				foreach($tagGrps as $id => $slug){	
					foreach(self::$_cat_tag_groups[$id]['tags'] as $term){
						$cat = get_category_by_slug($term['slug']);
						$cats[$cat->cat_ID] = $cat;
					}
				}
				
				$this->_categories = $cats;
			}
		}
		
		private function setTerms(){
			if(is_null($this->_terms)){
				$terms = wp_get_post_terms($this->_post->ID, self::$_tax, array('fields'=>'all'));
				$arr = array_fill_keys(self::$_tax, array());
				
				$grps = get_option('tag_group_labels');
				
				$tagGrps = array();
				foreach($terms as $term){
					$term->term_group_label = $grps[$term->term_group];
					if($term->term_group > 0){
						$tagGrps[$term->term_group] = $grps[$term->term_group];
					}
					$arr[$term->taxonomy][] = $term;	
				}
				$this->_tag_groups = $tagGrps;
				
				$this->_terms = array_filter($arr);
			}
		}
		
		function getTags(){
			return $this->_terms['post_tag'];
		}
		
				
		function toJSON(){
			
			$vars = get_object_vars($this);
			$json = json_encode($vars);
			return $json;
		}
		
		function toArray(){
			return get_object_vars($this);
		}
		
		function getPost(){
			
			$ret = $this->_post;
			$ret->terms = $this->_terms;
			$ret->tag_groups = $this->_tag_groups;
			$ret->categories = $this->_categories;
			
			return $ret;
		}
		
		function getCategories(){
			return $this->_categories;
		}
		
		function __get($prop){
			
			if(isset($this->_post->$prop)){
				return $this->_post->$prop; 
			}else{
				throw new Exception("property:$prop does not exist");
			}
		}
	}
	
	cCasualAgentPost::InitStatic();
?>
<?php
	define('CA_META_PREFIX', 'casual_agent');
	
	class cCasualAgentPost{
	
		protected static $_tax = array();
		protected static $_cat_tag_groups = array();
		protected $_categories = null;
		protected $_terms = null;
		protected $_tag_groups = null;
		
		protected static $_metaKey = array(
			'_is_featured'=>array('prefix'=>true, 'single'=>true, 'default'=>'no', 'opts'=>array('no', 'yes'), 'post_type'=>array('post')),
			'_featured_meta'=>array('prefix'=>true, 'single'=>true, 'default'=>array('img'=>0), 'post_type'=>array('post'))
		);
		
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
			$self = get_called_class();
		
			$self::$_tax = get_taxonomies();
			
			
			$self::$_cat_tag_groups = tag_groups_cloud(array("taxonomy"=>"category"), true);
		}
		
		private function setCategories(){
			$self = get_called_class();
			if(is_null($this->_categories)){
				$catTerms = $this->_terms['category'];
				
				$cats = array();
				foreach($catTerms as $term){
					$cat = get_category_by_slug($term->slug);
					$cats[$cat->cat_ID] = $cat;
				}
				
				$tagGrps = $this->_tag_groups;
				
				foreach($tagGrps as $id => $slug){	
					if(!(is_array($self::$_cat_tag_groups[$id]['tags'] ))){
						print_r($self::$_cat_tag_groups[$id]['tags'] );
					}else{
					foreach($self::$_cat_tag_groups[$id]['tags'] as $term){
						$cat = get_category_by_slug($term['slug']);
						$cats[$cat->cat_ID] = $cat;
					}
					}
				}
				
				$this->_categories = $cats;
			}
		}
		
		private function setTerms(){
		$self = get_called_class();
			if(is_null($this->_terms)){
				$terms = wp_get_post_terms($this->_post->ID, $self::$_tax, array('fields'=>'all'));
				$arr = array_fill_keys($self::$_tax, array());
				
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
		
		function getImg($type){
			
			switch($type){
				case 'carousel':
				
					break;
				case 'gallery':
				
					break;
				case 'featured':
				default:
				
					break;
					
			}
			
		}
		
		function setImg($type, $attachment_id){
			
			switch($type){
				case 'carousel':
				
					break;
				case 'gallery':
				
					break;
				case 'featured':
				default:
				
					break;
					
			}
		}
		
		function getKeyName($key){
			$self = get_called_class();
			$key = str_ireplace(CA_META_PREFIX, "", $key);
			return ($self::$_metaKey[$key]['prefix'])? CA_META_PREFIX.$key : $key;
		}
		
		function setMeta($key, $newVal, $oldVal = null){
			$self = get_called_class();
			$mKey = $this->getKeyName($key);
			
			$single = $self::$_metaKey[$key]['single'];
			
			if($single){
				if(is_null($newVal)){
					delete_post_meta($this->_post->ID, $mKey);
				}else{
			
					$oldVal = is_null($oldVal)?$this->getMeta($key):$oldVal;
					
					if(!is_null($oldVal)){
						$res = update_post_meta($this->_post->ID, $mKey, $newVal, $oldVal);
					}else{
						$res = update_post_meta($this->_post->ID, $mKey, $newVal);
					}
					
					if(!$res){
						$curVal = $this->getMeta($key);
						
						if($curVal != $newVal){
							delete_post_meta($this->_post->ID, $mKey);
							add_post_meta($this->_post->ID,$mKey,$newVal);
						}
					}
				}
			}else{
				if(is_null($newVal)){
					if(is_null($oldVal)){
						delete_post_meta($this->_post->ID, $mKey);
					}else{
						delete_post_meta($this->_post->ID, $mKey, $oldVal);
					}
				}else{
					if(is_null($oldVal)){
						add_post_meta($this->_post->ID, $mKey, $newVal, false);
					}else{
						update_post_meta($this->_post->ID, $mKey, $newVal, $oldVal);
					}
				}
			}
			
			return array($mKey, $key, $newVal, $oldVal);
		}
		
		function setFeatured($opts = 'no'){

			$state = "";
			
			if(is_string($opts) && ($opts =='yes' || $opts=='no')){
				$state = $opts;
			}else if(is_array($opts)){
				$state = empty($opts)?'no':'yes';
			}else{
				$state = 'no';
			}
			
			$isFeatured = $this->getMeta('_is_featured');
			$featuredMeta = $this->getMeta('_featured_meta');
			$res = array('set'=>array(), 'opts'=>$opts, 'state'=>$state, 'cur'=>array($isFeatured, $featuredMeta));
			if($isFeatured != $state){
				$res['set'][] = $this->setMeta('_is_featured', $state);
			}
			
			switch($state){
				case 'no':
					$res['set'][]=$this->setMeta('_featured_meta', null);
					break;
				case 'yes':
					if(is_array($opts)){
						$featuredMeta = is_array($featuredMeta)?$featuredMeta:array();
						foreach($opts as $fld=>$val){
							$featuredMeta[$fld] = $val;
						}
						$res['set'][] = $this->setMeta('_featured_meta', $featuredMeta);
					}
					break;	
			}
			
			return array('_is_featured'=>$this->getMeta('_is_featured'), '_featured_meta'=>$this->getMeta('_featured_meta'), 'res'=>$res);	
				
		}
		
		function getMeta($key){
			$mKey = $this->getKeyName($key);
			$self = get_called_class();
			$val = get_post_meta($this->_post->ID, $mKey, $self::$_metaKey[$key]['single']);
			
			if(empty($val)){
				return null;
			}
			
			if(is_array($val)){
				switch(count($val)){		
					case 1:
						return array_shift($val);
					case 0:
						return null;
					default:
						return $val;
				}
			}
			
			return $val;
		}
		
		function __get($prop){
			
			switch($prop){
				case 'featured_meta':
					$keys = array('_is_featured', '_featured_meta');
					$ret = array_fill_keys($keys, array($this->getMeta('_is_featured'), $this->getMeta('_featured_meta')));
					return $ret;
				case 'categories':
						return $this->getCategories();
					break;
				case 'tags':
						return $this->getTags();
					break;
				case 'post':
						return $this->getPost();
					break;
				default:				
					if(isset($this->_post->$prop)){
						return $this->_post->$prop; 
					}else{
						throw new Exception("property:$prop does not exist");
					}
			}
			
			return null;
		}
	}
	
	cCasualAgentPost::InitStatic();
?>
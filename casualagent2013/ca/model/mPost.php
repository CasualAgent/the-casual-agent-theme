<?php

	namespace CasualAgentTheme\Model;
	
	class mPost{
		
		protected $_post = null;
		
		function __construct($post){
			
			if(is_numeric($post)){
				 \ResetWPDB();
				 $this->_post = get_post($post);
			}else{
				$this->_post = $post;
			}
			
			if(!($this->_post instanceof \WP_Post)){
				throw(new \Exception('Unable to instantiate mPost. Must supply WP_Post Obj or Post ID'));
			}
			$this->loadMeta();
			$this->loadTerms();
		}
		
		private function loadMeta(){
			$meta = get_post_meta($this->ID);
			
			$this->_meta = array();
			foreach($meta as $key => $val){
				if(is_array($val)){
					$cnt = count($val);
					if($cnt <= 0){
						$val = null;
					}else if($cnt == 1){
						$val = $val[0];
						
						if($tmp = unserialize($val)){
							$val = $tmp;
						}
					}
				}
				$this->_meta[$key] = $val;
			}
			
			//print_r($this->_meta);
		}
		
		function set($fld, $val){	
			if(isset($this->post->$fld)){
				return wp_update_post(array('ID'=>$this->ID, $fld=>$val));
			}
		}
		
		function getMeta($key){
			$meta = array();
			switch($key){
				case 'all':
					$meta = $this->_meta;
					break;
				default:
					if($key[strlen($key)-1] == '*'){
						$len = strlen($key)-2;
						foreach($this->_meta as $mKey => $val){
							if(substr_compare($key, $mKey, 0, $len, true) == 0){
								$meta[$mKey] = $val;
							}
						}
					}else{
						$meta = isset($this->_meta[$key])?$this->_meta[$key]:null;
					}
				
			}
			
			return (empty($meta))? null : $meta;
			
		}
		
		function setMeta($key, $val, $single=true, $oldVal=null){
			
			if(is_null($val)){
				if(is_null($oldVal)){
					delete_post_meta($this->ID, $key);
				}else{
					delete_post_meta($this->ID, $key, $oldVal);
				}
			}else{			
				$meta = $this->getMeta($key);
				
				if($single){
					if(is_array($meta) && count($meta)>1){
						$this->setMeta($key, null);
					}
					if(!(add_post_meta($this->ID, $key, $val, $single))){
						update_post_meta($this->ID, $key, $val);
					}
				}else{
					if(is_null($oldVal)){
						update_post_meta($this->ID, $key, $val);
					}else{
						update_post_meta($this->ID, $key, $val, $oldVal);
					}
				}
			}
			
			$this->loadMeta();
			$meta = $this->getMeta($key);
			
			if(is_array($meta)){
				return in_array($val, $meta);	
			}else{
				return ($val == $meta);
			}
		}
		
		function getImage($type){
			$img = null;
			switch($type){
				case 'featured':
					$meta = $this->getMeta('casual_agent_featured_meta');
					$img = (is_array($meta) && isset($meta['img']) && $meta['img']>0) ? get_post($meta['img']) : null;
					break;
				case 'gallery':
					
					break;
				case 'display':
					$imgID = get_post_thumbnail_id($this->ID);
					if($imgID>0){
						$img =  get_post($imgID);
					}else{
						$img = null;
					}
					break;
			}
			
			return $img;
		}
		
		function Qry($args, $retType = 'mPost'){
		
			$qry = new \WP_Query($args);
			$res = $qry->get_posts($args);
			
			switch($retType){
				case 'WP_Post':
					$cls = 'WP_Post';
					
					break;
				case 'mPost':
				default:
					$ret = array();
					foreach($res as $p){
						$ret[] = new self($p);
					}
					$res = $ret;
					break;
			}
			
			return $res;
		}
		
		private function loadTerms(){
			$self = get_called_class();
			if(is_null($this->_terms)){
				$terms = wp_get_post_terms($this->_post->ID, array('category','post_tag'), array('fields'=>'all'));
				$arr = array_fill_keys(array('category', 'post_tag'), array());
				
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
		
		function __get($p){
			
			switch($p){
				case 'ID':
					return $this->_post->ID;
				case 'post':
					return $this->_post;
				default:
					return null;
			}
		}
	}

?>
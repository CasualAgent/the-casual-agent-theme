<?php
	require_once("cCasualAgentPost.php");
	
	class cCasualAgentImg extends cCasualAgentPost{
		
		
		protected static $_metaKey = array(
			'_img_type' => array('prefix'=>true, 'single'=>true, 'default'=>'no', 'opts'=>array('carousel', 'gallery', 'feature'), 'post_type'=>array('attachment'))
		);
		function __construct($_post){
			
			if(is_numeric($_post)){
				$this->_post = get_post($_post);
			}else if($_post instanceof WP_Post){
				$this->_post = $_post;
			}else{
				throw new Exception("Must supply post object or post ID to create new instance of cCasualAgentPost");
			}
		}
		
		function attach($parent, $type){
			
			$post = null;
			if(is_numeric($parent)){
				$post = WP_Post::get_instance($parent);
			}else if($parent instanceof WP_Post){
				$post = $parent;
			}else{
				throw new Exception("Must supply post object or post ID to create new instance of cCasualAgentPost");
			}
			
			
			if(is_null($post) || !$post){
				return false;
			}
			
			if(!in_array($type, self::$_metaKey['_img_type']['opts'])){
				$type = 'gallery';
			}
			
			wp_update_post(array('ID'=>$this->ID, 'post_parent'=>$post->ID));
			
			$this->_post = get_post($this->ID);
			$this->setMeta('_img_type', $type);
			
		}
		
		function detach(){
			wp_update_post(array('ID'=>$this->ID, 'post_parent'=> null));	
						$this->_post = get_post($this->ID);
		}
		
		function getParent(){
			$id = $this->_post->post_parent;
			return new cCasualAgentPost(get_post($id));
		}
		
		function __get($prop){
			
			switch($prop){
				
				/*case 'categories':
						return $this->getCategories();
					break;
				case 'tags':
						return $this->getTags();
					break;*/
				case 'ID':
					return $this->_post->ID;
				case 'data':
						return $this->_post;
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
?>
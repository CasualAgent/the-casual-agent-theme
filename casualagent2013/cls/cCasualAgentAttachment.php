<?php
	require_once("cCasualAgentPost.php");
	
	class cCasualAgentAttachment extends cCasualAgentPost{
		
		protected static $_metaKey = array(
			'_cls' => array('prefix'=>true, 'single'=>true, 'default'=>'cCasualAgentAttachment', 'opts'=>array('cCasualAgentAttachment'), 'post_type'=>array('attachment')),
		);
		
		function __construct($_post){
			
			if(is_numeric($_post)){
				$this->_post = get_post($_post);
			}else if($_post instanceof WP_Post){
				$this->_post = $_post;
			}else{
				throw new Exception("Must supply post object or post ID to create new instance of cCasualAgentAttachment");
			}
		}
		
		function attach($parent){
			
			if(is_numeric($parent) || $parent instanceof WP_Post){
				$parent = new cCasualAgentPost($parent);
			}
			
			if(! $parent instanceof cCasualAgentPost){
				throw new Exception("Invalid Parent");
			}
			
			wp_update_post(array('ID'=>$this->ID, 'post_parent'=>$parent->ID));
			$this->_post = get_post($this->ID);
			
			return (intval($this->_post->post_parent) == intval($parent->ID));
		}
		
		function detach(){
			if(intval($this->post_parent)>0){
				wp_update_post(array('ID'=>$this->ID, 'post_parent'=> 0));	
			}
			$this->_post = get_post($this->ID);
			
			return (intval($this->_post->post_parent) == 0);
		}
		
		function getParent(){
			$id = $this->_post->post_parent;
			return new cCasualAgentPost($id);
		}
		
		function __get($prop){
			
			switch($prop){
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
	
		public static function Obj($attachment){
			
			if(is_numeric($attachment)){
				$p = get_post($attachment);
			}else if( $attachment instanceof WP_Post){
				$p = $attachment;
			}else{
				throw new Exception("Can't create Attachment Obj");
			}
			
			if($p->post_type == 'attachment'){
				switch($p->post_mime_type){
					case 'image':
						return new cCasualAgentImg($p);
					default:
						return new self($p);			
				}
			}else{
				throw new Exception("No Attachment Supplied");
			}
		}
	}
?>
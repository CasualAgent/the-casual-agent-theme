<?php
	require_once("cCasualAgentAttachment.php");
	
	class cCasualAgentImg extends cCasualAgentAttachment{
		
		protected static $_metaKey = array(
			'_cls' => array('prefix'=>true, 'single'=>true, 'default'=>'cCasualAgentImg', 'opts'=>array('cCasualAgentImg'), 'post_type'=>array('attachment')),
			'_img_type' => array('prefix'=>true, 'single'=>true, 'default'=>'gallery', 'opts'=>array('carousel', 'gallery', 'feature'), 'post_type'=>array('attachment'))
		);
				
		function attach($parent, $type = 'gallery'){
			
			if(parent::attach($parent)){
				$type = (in_array($type, $this->getMetaKeyCfg('_img_type', 'opts')))?$type:$this->getMetaKeyCfg('_img_type', 'default');
				$this->setMeta('_img_type', $type);
				return true;
			}else{
				return false;
			}
			
		}
		
		function detach(){
			if(parent::detach()){
				$this->setMeta('_img_type', null);
				return true;
			}else{
				return false;
			}
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
	
	}
?>
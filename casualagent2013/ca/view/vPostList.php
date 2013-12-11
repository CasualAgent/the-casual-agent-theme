<?php
	
	namespace CasualAgentTheme\Admin\View;
	require_once(caTHEME_MODEL_DIR."mPost.php");
	
	
	
	use \CasualAgentTheme\Model\mPost as mPost;
	
	class vPostList{
		
		protected $_mpost = null;
		
		function __construct($post){
			$this->_mpost = new mPost($post);
		}
		
		function featuredCol(){
			
			$mpost = $this->_mpost;
			if(function_exists('is_syndicated') && is_syndicated($mpost->ID)){
				return 'N/A';	
			}else{
				
				$featImg = $this->_mpost->getImage('featured');
				
				if($mpost->getMeta('casual_agent_is_featured') == 'yes'){
				if(is_null($featImg) || !$featImg){
					$html= $this->_render_is_featured_chkbox('Not Active.  Not Carousel Image');
				}else{
					$html= $this->_render_is_featured_chkbox('Active');
				}
				}else{
					$html= $this->_render_is_featured_chkbox();
				}
				
				return $html;
			}
		}
		
		private function _render_is_featured_chkbox($label=null){
			$checked = ($this->_mpost->getMeta('casual_agent_is_featured') == 'yes')?'checked':'';
			$label = (is_string($label) && strlen($label)>0)?'&nbsp;&nbsp;'.$label:'';
			
			return '<input name="ca_is_featured" class="chkbox_ca_is_featured" data-post-id="'.$this->_mpost->ID.'" type="checkbox" '.$checked.' />'.$label;
		}
	}
?>
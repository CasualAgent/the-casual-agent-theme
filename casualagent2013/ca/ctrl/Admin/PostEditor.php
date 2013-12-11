<?php
	namespace CasualAgentTheme\Admin;
	
	require_once(caTHEME_VIEW_DIR."vPostEditor.php");
	require_once(caTHEME_MODEL_DIR."mPost.php");
	use \CasualAgentTheme\Admin\View\vPostEditor as vAdminPostEditor;
	use \CasualAgentTheme\Model\mPost as mPost;
	class PostEditor{
		
		private $_capost = null;
		
		/*******Public Methods *********/
		function __construct(){
			

		}
		
		
	
		/*******AJAX Handlers******************/
		
		function _set_carousel_img($args){
			
			extract($args, EXTR_OVERWRITE);
			
			$mpost = new mPost($post_ID);
			
			$img = isset($img_ID)?get_post($img_ID):isset($img_url)?mPost::Qry(array('guid'=>$img_url, 'post_type'=>'any', 'post_status'=>'any'), 'mPost'):null;
			
			if(is_null($img) || empty($img)){
				return array('ok'=>false,'err'=>'No Image Found to Attach');
			}
			
			$img = $img[0];
			
			if(intval($img->post->post_parent) > 0 && intval($img->post->post_parent) != intval($mpost->ID)){
				return array('ok'=>false, 'err'=>"img is attached to post ".intval($img->post->post_parent));
			}
			
			if($img->set('post_parent', $mpost->ID)){
				$img->setMeta('casual_agent_img_type', 'featured', true);
				$meta = $mpost->getMeta('casual_agent_featured_meta');
				$meta['img'] = $img->ID; 
				$mpost->setMeta('casual_agent_featured_meta', $meta, true);
				
				if($mpost->getMeta('casual_agent_is_featured')!='yes'){
					$mpost->setMeta('casual_agent_is_featured', 'yes', true);	
				}
			}
			
			return array('ok'=>true, 'post'=>$mpost, 'img'=>$img);
			
		}
		function _set_gallery_img($args){
		
		}
		
		function _set_display_img($args){
		
		}
		
		function ajax_attach_post_img(){
			
			extract($_REQUEST, EXTR_OVERWRITE);
						
			switch($action){
				case 'set_carousel_img':
					return $this->_set_carousel_img($_REQUEST);
				case 'set_gallery_img':
					return $this->_set_gallery_img($_REQUEST);
				case 'set_display_img':
					return $this->_set_display_img($_REQUEST);
				default:
					return null;
			}
		}
		
		
		/*******Public Static Methods *********/
			
		public static function PostMetaBox(){
			
			$id = 'post_meta';
			$title = 'Meta Data';
			$post_type = 'post';
			$context = 'advanced';
			$priority = 'high';
			$callback = '\CasualAgentTheme\Admin\PostEditor::RenderMetaBox';
	
			add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $id);
			
			
			$id = 'casual_agent_featured_post';
			$title = 'Casual Agent Feature';
			$post_type = 'post';
			$context = 'side';
			$priority = 'high';
			
			
			add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $id );
		}
		
		public static function RenderMetaBox($post, $args){	
			$view = new vAdminPostEditor($post);
			
			$html = null;
			
			switch($args['id']){
				case 'post_meta':
					$html = $view->getMetaDataBox();
					break;
				case 'casual_agent_featured_post':
					$html = $view->getPostMetaBox();
					break;
			}
			
			echo $html;
		}
		
		public static function AJAX(){
			$action = $_REQUEST['action'];
			
			$res = null;
			$me = new self();
			switch($action){	
				case 'set_ca_carousel_img':
					$res = $me->ajax_set_carousel_img();
					break;
				case 'attach_post_img':
					$res = $me->ajax_attach_post_img();
					break;	
			}
			
			SendJSON($res);
		}
		
		
		
		/*******Private Methods *********/
	}
?>
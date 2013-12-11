<?php
	require_once('cCasualAgentImg.php');

	class AdminPosts{
		
		private $_capost = null;
		
		/*******Public Methods *********/
		function __construct($post){
			$this->_post = $post;

		}
		
		function getMetaDataBox(){

			$caMeta = $this->capost->getMeta('allca');
			$allMeta = $this->capost->getMeta('all');
			
			$html = array('<h1>Casual Agent Meta</h1>');
			
			$html[] = print_r($caMeta, true);
			$html[] = "<h1>All Meta</h1>";
			$html[] = print_r($allMeta, true);
			return "<pre>".implode("<br/>", $html)."</pre>";
		}
		
		function getPostMetaBox(){
			$html [] = $this->mb_section_featured();
			$html [] = $this->mb_section_display_img();			
			return implode("\n", $html);
			
		}
		
		private function mb_section_display_img(){
			$imgID = get_post_thumbnail_id($this->capost->ID);
			
			$html = _wp_post_thumbnail_html($imgID, $this->post);
			
			$html = "<div id='ca_display_img' style='border-top: groove 2px #dcdcdc;margin-top: 0.5em;'>
							<h2>Display Image</h2>
							$html
					</div>";
			return $html;
			
		}
		private function mb_section_featured(){
			$isFeatured = ($this->capost->getMeta('_is_featured') == 'yes')?true:false;
			$featuredMeta = $this->capost->getMeta('_featured_meta');
			
			if(function_exists('is_syndicated') && is_syndicated($this->capost->ID)){
				return "Syndicated Post: ".get_syndication_source($this->capost->ID);
			}
			$html = array();
			$html[] = Admin::Render('post_isfeatured_chkbox', array('post_ID'=>$this->capost->ID, 'checked'=>($isFeatured === false)?'':'checked', 'label'=>'Featured Post'));
			if($isFeatured){
				if(is_array($featuredMeta) && isset($featuredMeta['img'])){
					$a = new cCasualAgentImg($featuredMeta['img']);
					$src = $a->guid;
					$html[] = "<div class='ca_attachment ca_attachment_type_carousel'>
								<img src='$src' style='display:inline-block;width:100%;height:auto!important;'/>
								<div class='actions'>
									<a data-args='".json_encode(array('action'=>'set_ca_carousel_img', 'img_ID'=> $a->ID, 'post_ID'=>0))."' class='action ajax_action'>Detach</a>
								</div>
							</div>";
				}else{
					$html[] = "
							<h1 style='color:red;font-size: 0.9em;position: absolute;top: -10px;right: 25px;'>NOT ACTIVE</h1>
							<div class='note'>
								<p>Please attachment image to be shown in the Carousel! Image should be formatted to be properly displayed in the Carousel (996w x  508h)
									This post will not be active until a Carousel image is assigned!
								</p>
							</div>
							<a title='Set Featured Carousel Image'  id='set-ca-featured-post-image' data-fn='set_ca_carousel_img' class='thickbox media_upload'>Set Featured Carousel Image</a>";
				}
		
			}else{
				$html[] = "<h1 class='metabox_note'>Not Featured</h3>";
			}
			
			return implode("\n", $html);
		}
		function __get($prop){
			
			switch($prop){
				case 'post':
					return $this->_post;
				case 'capost':
					if(is_null($this->_capost)){
						$this->_capost = new cCasualAgentPost($this->_post);
					}
					return $this->_capost;
				break;
			}
		}
		/*******AJAX Handlers******************/
		
		/*******Public Static Methods *********/
		
		public static function Init(){
			add_action( 'add_meta_boxes', array('AdminPosts', 'PostMetaBox' ));
		}
		
		public static function PostMetaBox(){
			
			$id = 'post_meta';
			$title = 'Meta Data';
			$post_type = 'post';
			$context = 'advanced';
			$priority = 'high';
			$callback = array('AdminPosts', 'RenderMetaBox');
	
			add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $id);
			
			
			$id = 'casual_agent_featured_post';
			$title = 'Casual Agent Feature';
			$post_type = 'post';
			$context = 'side';
			$priority = 'high';
			
			
			add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $id );
		}
		
		public static function RenderMetaBox($post, $args){	
			$self = new self($post);
			
			$html = null;
			
			switch($args['id']){
				case 'post_meta':
					$html = $self->getMetaDataBox();
					break;
				case 'casual_agent_featured_post':
					$html = $self->getPostMetaBox();
					break;
			}
			
			echo $html;
		}
		
		/*******Private Methods *********/
	}
	
?>
<?php
	namespace CasualAgentTheme\Admin\View;
	require_once(caTHEME_MODEL_DIR."mPost.php");
	
	
	
	use \CasualAgentTheme\Model\mPost as mPost;
	
	class vPostEditor{
		
		protected $_mpost = null;
		
		function __construct($post){
			$this->_mpost = new mPost($post);
		}
	
		
		function getMetaDataBox(){
			$mpost = $this->_mpost;
			
			$caMeta = $mpost->getMeta('casual_agent*');
			$allMeta = $mpost->getMeta('all');
			
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
			$mpost = $this->_mpost;
			$img = $mpost->getImage('display');
			
			if(!is_null($img)){
				$ajax_nonce = wp_create_nonce( 'set_post_thumbnail-' . $mpost->ID );
				$innerHTML = "<img src='".$img->guid."' style='width:99%;' /><a href='#;' onclick=\"WPRemoveThumbnail('".$ajax_nonce."');return false;\">remove</a>";
			}else{
				$innerHTML = "<a id='set-post-display-img' href='#' onclick='wp.media.featuredImage.frame().modal.open(this); return true;'>Set Display Image</a><p>";
			}
			
			$html = "<div id='ca_display_img' style='border-top: groove 2px #dcdcdc;margin-top: 0.5em;'>
							<h2 style='margin-top:0; margin-bottom:0'>Display Image</h2>
							$innerHTML
					</div>";
			return $html;
			
		}
		private function mb_section_featured(){
			$mpost = $this->_mpost;
			$isFeatured = ($mpost->getMeta('casual_agent_is_featured') == 'yes')?true:false;
			
			
			if(function_exists('is_syndicated') && is_syndicated($mpost->ID)){
				return "Syndicated Post: ".get_syndication_source($mpost->ID);
			}
			$html = array();
			$html[] = $this->_render_is_featured_chkbox('Featured Post');
			if($isFeatured){
				if(!is_null($a = $mpost->getImage('featured'))){	
					$src = $a->guid;
					$html[] = "
					<h1 style='color:green;font-size: 0.9em;position: absolute;top: -10px;right: 25px;'>ACTIVE</h1>
					<div class='ca_attachment ca_attachment_type_carousel'>
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
							<a title='Set Featured Carousel Image'  id='set-ca-featured-post-image' data-fn='set_ca_carousel_img' class='thickbox media_upload'>Set Carousel Image</a>";
				}
		
			}else{
				$html[] = "<h1 class='metabox_note'>Not Featured</h3>";
			}
			
			return implode("\n", $html);
		}
		
		private function _render_is_featured_chkbox($label=null){
			$checked = ($this->_mpost->getMeta('casual_agent_is_featured') == 'yes')?'checked':'';
			$label = (is_string($label) && strlen($label)>0)?'&nbsp;&nbsp;'.$label:'';
			
			return '<input name="ca_is_featured" class="chkbox_ca_is_featured" data-post-id="'.$this->_mpost->ID.'" type="checkbox" '.$checked.' />'.$label;
		}
	}
?>
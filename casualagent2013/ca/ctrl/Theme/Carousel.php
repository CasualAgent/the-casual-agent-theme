<?php

	namespace CasualAgentTheme\Theme;
	require_once(caTHEME_MODEL_DIR."mPost.php");
	
	
	use \CasualAgentTheme\Model\mPost as mPost;
	
	define('_BANNER_WIDTH', 996);
	define('_BANNER_HEIGHT', 508);
	class Carousel{
	
		function __construct($cat = 'all'){
			$this->setCategory($cat);
		}	
		
		function setCategory($cat = 'all'){
			
			if(is_numeric($cat) && $cat > 0){
				$category = get_category($cat);
				if(is_object($category)){
					$cat = $category;
				}
			}else{
				$cat = null;
			}
			
			$this->_cat = $cat;
		}
		
		function getImageSet($retType = 'obj', $qryArgs = array()){
			
			$fposts = $this->qryFeaturedPosts($qryArgs);
			
			$set = array();
			foreach($fposts as $fp){
				$img = $fp->getImage('featured');
				switch($retType){
					case 'html':
						$set[] = $this->_toImgTag($img);
						break;
					case 'obj':
					default:
						$set[] = $img;
					}	
				
			}
			return (count($set)<=0) ? null : $set;
		}
		
		function getCarouselImgTags(){
			
		}
		
				
		function qryFeaturedPosts($opts = array()){
			$args  = array(
				'fields'=>'all',
				'post_type'=>'post', 
				'post_status'=>'publish',
				'posts_per_page'=>50,
				'meta_query'=>array(
					array('key'=>'casual_agent_is_featured', 'value'=>'yes')));
			
			if(is_object($this->_cat) && isset($this->_cat->cat_ID)){
				$args['cat']= $this->_cat->cat_ID;	
			}else{
				$args['cat'] = null;
			}
			$args['order'] = 'DESC';
			$args['order_by'] = 'post_date';
			
			foreach($opts as $fld=>$val){
				$args[$fld] = $val;
			}
					
			$fposts = mPost::Qry($args, 'mPost');
			
			$feat = array();
			
			foreach($fposts as $p){
				if(!is_null($p->getImage('featured'))){
					$feat[] = $p;
				}
			}
			
			return $feat;
		}
		
		public static function GetShortCode($cat, $opts = array()){
			
			$def = array('color'=>'black', 'visible'=>1, 'width'=>_BANNER_WIDTH, 'height'=>_BANNER_HEIGHT, 'speed'=>800, 'auto'=>5000);
			
			foreach($opts as $fld => $val){
				$def[$fld] = $val;
			}
			extract($def, EXTR_OVERWRITE);
			
			$o = new self($cat);
			
			$set = $o->getImageSet('html');
			
			if(is_null($set) && !is_null($cat) && $cat != 'all'){
				$o->setCategory('all');
				$set = $o->getImageSet('html');
			}
			
			if(!is_null($set) && count($set)>0){
				$set =implode('/!', $set);
				$sc = "[wpic color='$color' visible='$visible' width='$width' height='$height' speed='$speed' auto='$auto']".$set."[/wpic]";
			}else{
				$sc = null;
			}
			
			return $sc;
		}
					
		
		protected static function _toImgTag($img, $w = _BANNER_WIDTH){
			
			 return "<a href='".get_permalink( $img->post_parent )."'><img src='".$img->guid."' width='".$w."' /></a>";
		}

	}

?>

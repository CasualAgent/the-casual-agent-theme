<?php

	namespace CasualAgentTheme\Theme;
	require_once(caTHEME_MODEL_DIR."mPost.php");
	
	
	use \CasualAgentTheme\Model\mPost as mPost;
	
	
	class Posts{
	
		function __construct(){
			$T = ThemeInstance();
			if(is_home() || is_front_page()){
				$this->_cat = 'all';
			}else if(is_category()){
				$this->_cat = $T->get_current_category();
			}else{
				$this->_cat = null;
			}
		}
				
		function get_tag_group_posts($tagGrps, $isRSS=false){
	
			$args = array();
			$tagGrp = "";
			
			$args['tax_query'] = is_array($args['tax_query'])?$args['tax_query']:array();
	
			$tg = is_array($tagGrps)?implode(", ", $tagGrps):$tagGrps;
			$tagGrp = tag_groups_cloud(array('taxonomy'=>'post_tag,category', 'include'=>$tg), true);
			$terms = array();
	
			foreach($tagGrp as $idx => $grp){
	
				$grp['tags'] = is_array($grp['tags'])?$grp['tags']:array();
				foreach($grp['tags'] as $tag){
					$terms[] = $tag['slug'];
				}
			}
				
			$args['tax_query'][] = array('taxonomy'=>'post_tag', 'field'=>'slug', 'terms'=>$terms, 'operator'=>'IN');
			
			
			if($isRSS){
				$args['tax_query'][] = array('taxonomy'=>'post_tag', 'field'=>'slug', 'terms'=>array('rss'), 'operator'=>'IN');
			}else{
				$args['tax_query'][] = array('taxonomy'=>'post_tag', 'field'=>'slug', 'terms'=>array('rss'), 'operator'=>'NOT IN');
			}
			$args['tax_query']['relation'] = 'AND';
			
			$res = mPost::Qry($args, 'mPost');
			
	
			return $res;
		}
		
		function latestPosts($src = 'ca'){
		
					
			$cat = $this->_cat;
			
			$termGrp = array();
			if(!is_null($cat) && $cat->term_group == 0){
				$cats = get_categories('parent='.$cat->cat_ID);
				foreach($cats as $c){
					$termGrp[] = $c->term_group;
				}
			}else{
				$termGrp = is_null($cat)?get_option('tag_group_ids'):array($cat->term_group);
			}
			
			switch($src){
				case 'all':
					$ca_posts = $this->get_tag_group_posts($termGrp, false);
					$rss_posts = $this->get_tag_group_posts($termGrp, true);
					$posts = array_merge($ca_posts, $rss_posts);
					break;
				case 'rss':
					$posts = $this->get_tag_group_posts($termGrp, true);
					break;
				case 'ca':
				default:
					$posts = $this->get_tag_group_posts($termGrp, false);
			}
			
			
			return $posts;
		}
	}
	
?>
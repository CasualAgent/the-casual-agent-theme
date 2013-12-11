<?php
	

	namespace CasualAgentTheme\Admin;
	
	require_once(caTHEME_VIEW_DIR."vPostList.php");
	
	use \CasualAgentTheme\Admin\View\vPostList as vAdminPostList;
	
	class PostList{
		
		public static function GetCol($col, $post_ID){
			
			$view = new vAdminPostList($post_ID);
			$html = null;
			switch($col){
				case 'isFeatured':
					$html = $view->featuredCol();
					break;
			}
			
			echo $html;
		}
		public static function CfgCols($cols){
			$keys = array_keys($cols);
			$vals = array_values($cols);
			
			array_splice($keys, 2, 0, 'isFeatured');
			array_splice($vals, 2, 0, 'Featured');
			return array_combine($keys, $vals);
		}
	}
?>
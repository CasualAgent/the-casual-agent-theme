<?php
	define('CA_META_PREFIX', 'casual_agent');
	class cCasualAgentPostMeta{
	
		protected static $_obj = array();
		private $P = null;
		private $_meta_data = null;
		

		private $mKey = array(
			'_meta_data'=>array('single'=>true, 'default'=>"", 'post_type'=>array('post', 'attachment'), 'isArray'=>true),
			'_is_featured'=>array('single'=>true, 'default'=>'no', 'opts'=>array('no', 'yes'), 'post_type'=>array('post')),
			'_post_featured_ready'=>array('single'=>true, 'default'=>'no', 'opts'=>array('no', 'yes'), 'post_type'=>array('post', 'attachment', 'any')),
			'_attachment_type'=>array('single'=>true, 'default'=>'none', 'opts'=>array('none', 'carousel', 'display', 'gallery'), 'post_type'=>array('attachment'))
		);
		
		public static function GetObj($p){
			$p = (is_object($p) || is_array($p)) ? $p : intval($p);
			
			$item = (is_numeric($p))? get_post($p):(($p instanceof WP_Post)?$p: (new Exception('Must provide WP_Post Object or Post ID'.json_encode($p))));
			
			
			if($item instanceof Exception){
				
				throw $item;
				die();
			}
			
			if($item->ID > 0){
				$pKey = 'ID_'.$item->ID;
				$o = (isset(self::$_obj[$pKey]) && self::$_obj[$pKey] instanceof self) ? self::$_obj[$pKey] : new self($item->ID);	
				
				return $o;
			}else{
				return null;
			}
			
			
		}
		function __get($p){
			return isset($this->P->$p)?$this->P->$p:null;
		}
		
		function getPost(){
			return $this->P;
		}
		
		function set_data($key, $val){
		
			if(array_key_exists($key, (array)$this->P)){
				$this->P->$key = $val;
				wp_update_post($this->P);
				$this->reload_post();
				return ($this->get_data($key) == $val);
			}
			return $key;
		}
		
		function get_data($key){
			return isset($this->P->$key)?$this->P->$key:null;
		}
		
		function reload_post(){
			if(!is_null($this->ID)){
				$this->P = get_post($this->P->ID);	
				return $this->getPost();
			}
			throw new Exception("No Post Object.  Corrupt cCasualAgentPostMeta Object!");
		}
		
		private function __construct($p){
		
			if(is_string($p) || is_numeric($p)){
				$this->P = get_post($p);
			}else if($p instanceof WP_Post){
				$this->P = $p;
			}else{
				throw new Exception('Could not instantiate cCasualAgentPostMeta Object');
			}
			
			if($this->P instanceof WP_Post){
				self::$_obj['ID_'.$this->P->ID] = $this;
			}
			
		}
		
		function get_meta($key = 'all', $autoCreate = false, $throwOnInvalid=true){
		
			if($key == 'all'){
				$keys = array_keys($this->mKey);
				
				$res = array();
				foreach($keys as $k){
					$val = $this->get_meta($k, false, false);
					
					if(!is_null($val)){
						$res[$k] = $val;
					}
				}
				return $res;
			}
		
			if(($key = $this->isValidMetaKey($key, $throwOnInvalid))===false){
				return null;
			}
			
			if(!$this->hasMeta($key)){
				if(!$autoCreate){
					return null;
				}else{
					$this->set_meta($key, $this->getMetaCfg($key, 'default'));
				}
			}
			
			$single = $this->isSingle($key);
			$val = get_post_meta($this->P->ID, $this->metaKey($key), $single);
			return $val;
		}
		
		public static function _BOOL($val){
			return ($val== 'yes')? true:false;
		}
		
		function set_meta_array($arr){
		
			$append = isset($arr['append']) && is_bool($arr['append'])? $arr['append']:false;
			foreach($arr as $key => $val){
				$new = $val;
				$old = null;
				if(is_array($val) && isset($val['old'])){
					$old = $val['old'];
					$new = $val['new'];
				}
				$this->set_meta($key, $new, $append, $old);
			}
		}
		
		function set_data_array($arr){
		
			foreach($arr as $key => $val){
				$new = $val;
				$this->set_data($key, $val);	
			}
				
		}
		
		function unset_meta($key, $oldVal=null){
			if($this->hasMeta($key)){
				if(!is_null($oldVal)){
					delete_post_meta($this->ID, $this->mKey($key), $oldVal);	
				}else{
					delete_post_meta($this->ID, $this->mKey($key));
				}
			}	
		}
		
		function set_meta($key, $newVal, $append = false, $oldVal=null){
			if(($key = $this->isValidMetaKey($key, true)) == false){
				return false;
			}
			
			$status = null;
			
			if(is_null($newVal) || $newVal == 'delete'){
				$mKey = $this->metaKey($key);
				if(!is_null($oldVal)){
					$status = delete_post_meta($this->ID, $mKey, $oldVal);
				}else{
					$status = delete_post_meta($this->ID, $mKey);
				}
			}else{
			if($this->getMetaCfg($key, 'isArray') === true && $this->hasMeta($key)){
				if($append){
					$oldVal = (array)$this->get_meta($key);
			
					$temp = $oldVal;
					
					foreach($newVal as $f => $v){
						$temp[$f] = $v;
					}
					
					$newVal = $temp;
				}
				$mKey = $this->metaKey($key);
				$status = update_post_meta($this->P->ID, $mKey, $newVal);
				
			}else{

				$f = $this->metaKey($key);
				if($this->hasMeta($key)){
					if($this->isSingle($key)){
						update_post_meta($this->P->ID, $f, $newVal);
						$savedVal = get_post_meta($this->P->ID, $f, true);
						$status = ($savedVal == $newVal);
					}else{
						if($append){
							$status = add_post_meta($this->P->ID, $f, $newVal, false);
						}else{
							if(is_null($oldVal)){
								$status = update_post_meta($this->P->ID, $f, $newVal);	
							}else{
								$status = update_post_meta($this->P->ID, $f, $newVal, $oldVal);
							}	
						}
					}
				}else{
					$status = add_post_meta($this->P->ID, $f, $newVal, $this->isSingle($key));
				}
			}
			}
			
			return $status;
		}
		
		private function getMetaCfg($key, $cfg = 'all'){
			$key = $this->stripPrefix($key);
			
			if(!isset($this->mKey[$key])){
				return null;
			}		
			if($cfg=='all'){
				return $this->mKey[$key];
			}
			return isset($this->mKey[$key][$cfg])?$this->mKey[$key][$cfg]:null;
		}
		
		private function metaKey($key){
			$key = $this->stripPrefix($key);
			return CA_META_PREFIX.$key;
			
		}

		function isValidMetaKey($key, $throwOnInvalid = false){
			
			$key = $this->stripPrefix($key);
			$isValid = (isset($this->mKey[$key]))?true:false;
			

			if($isValid == true){
				return $key;
			}else{
				if($throwOnInvalid){
					$ret = json_encode(array('trace'=> debug_backtrace(), 'msg'=>$key." is not a valid meta property for post->".$this->P->ID));
					throw new Exception($ret);
				}else{
					return false;	
				}
			}
			
			return false;
		}
		
		function stripPrefix($val){
			return str_ireplace(CA_META_PREFIX, "", $val);
		}
		
		function hasMeta($key){
			if($key = $this->isValidMetaKey($key, false)){
				return array_key_exists($this->metaKey($key), get_post_meta($this->P->ID));	
			}
			return false;
			
		}
		
		function isSingle($key){
			$key = $this->stripPrefix($key);
			$single = $this->getMetaCfg($key, 'single');
			$single = is_bool($single)?$single:true;
			
			return $single;
		}
		
		
		function get_attachments($meta = array(), $data = array()){

			$args = array(
				'post_type'=>'attachment',
				'post_status'=>'inherit',
				'post_parent'=>$this->ID,
			);
			
			if(is_string($meta)){
				$meta = array(array('key'=>$this->metaKey('_attachment_type'), 'value'=>$meta));
			}
			
			foreach($data as $key => $val){
				$args[$key] = $val;
			}
			
			if(count($meta)>0){
				$args['meta_query'] = array();
				foreach($meta as $item){
					$item['key'] = $this->metaKey($item['key']);	
					$args['meta_query'][] = $item;
				}
			}			
			
			$qry = new WP_Query($args);
			
			$res = $qry->get_posts();
			$ret = array();
			foreach($res as $wp){
				$ret[] = cCasualAgentPostMeta::GetObj($wp);
			}
			return $ret;
		}
		
		function attach_image($item, $type){
			$oa = cCasualAgentPostMeta::GetObj($item);
			
			$parent = array('meta'=>array(), 'data'=>array());
			$img = array('meta'=>array('_attachment_type'=>$type), 'data'=>array('post_parent'=>$this->ID));
			
			switch($type){
				case 'carousel':
					$isFeatured = $this->_BOOL($this->get_meta('_is_featured', true));
					if($isFeatured){
						$parent['meta']['_post_featured_ready']='yes';
					}else{
						return false;
					}
					break;
				case 'gallery':
					break;				
				case 'display':
					set_post_thumbnail($this->P, $oa->ID);
					break;
				default:
					return false;
			}
			
			if(count($parent['meta']) > 0){
				$this->set_meta_array($parent['meta']);
			}
			if(count($parent['data']) > 0){
				$this->set_data_array($parent['data']);
			}
			
			if(count($img['meta']) > 0){
				$oa->set_meta_array($img['meta']);
			}
			if(count($img['data']) > 0){
				$oa->set_data_array($img['data']);
			}
			
			$oa->reload_post();
			$this->reload_post();
			
			return $this->get_tree(array('meta', 'attachments'));
		}
		
		function detach_image($item){
			$oa = cCasualAgentPostMeta::GetObj($item);
			
			$type =	(has_post_thumbnail($this->ID) && (get_post_thumbnail_id($this->ID) == $oa->ID)) ? 'display' : $oa->get_meta('_attachment_type');
			
			$parent = array('meta'=>array(), 'data'=>array());
			$img = array('meta'=>array('_attachment_type'=>null), 'data'=>array('post_parent'=>null));
			
			switch($type){
				case 'carousel':
					$parent['meta']['_post_featured_ready'] = 'no';
					break;
				case 'gallery':
					break;
				case 'display':
					set_post_thumbnail($this->ID, null);
					break;
				default:
					break;
			}
			
			if(count($parent['meta']) > 0){
				$this->set_meta_array($parent['meta']);
			}
			if(count($parent['data']) > 0){
				$this->set_data_array($parent['data']);
			}
			
			if(count($img['meta']) > 0){
				$oa->set_meta_array($img['meta']);
			}
			if(count($img['data']) > 0){
				$oa->set_data_array($img['data']);
			}
			
			return $this->get_tree(array('meta', 'attachments'));
		}
		
		function get_tree($include = array()){
			$this->reload_post();
			$data = $this->P;
			
			foreach($include as $node){
				switch($node){
					case 'attachments':
						$at = $this->get_attachments();
						$data->attachments = array();
						foreach($at as $a){
							$data->attachments[] = $a->get_tree(array('meta'));
						}
						break;
					case 'meta':
						$data->meta = $this->get_meta();
						break;
					
				}
			}
			
			return $data;
		}
		
		function set_featured_status($status){
			
			switch($status){
				case 'yes':
					
					$attachments = $this->get_attachments('carousel');
					if(count($attachments)==1){
						$this->set_meta('_post_featured_ready', 'yes');	
					}else if(count($attachments)<=0){
						$this->set_meta('_post_featured_ready', 'no');	
					}else{
						array_shift($attachments);
						foreach($attachments as $a){
							$this->detach_image($a->ID);
						}
					}
					$this->set_meta('_is_featured', 'yes');
					break;
				case 'no':
					$attachments = $this->get_attachments('carousel');
					foreach($attachments as $a){
						$this->detach_image($a->ID);
					}
					$this->set_meta('_is_featured', 'no');
					$this->set_meta('_post_featured_ready', 'no');
					break;
			}
			
			return $this->get_tree(array('meta', 'attachments'));
		}
		
		public static function GetByGUID($guid){
			$args = array(
				'guid'=>$guid,
				'post_status'=>'any',
				'post_type'=>'any',
			);
			
			$qry = new WP_Query($args);
			$res = $qry->get_posts();
			
			$arr = array();
			foreach($res as $idx=> $wp){
				$arr[] = cCasualAgentPostMeta::GetObj($wp);
			}
			
			return $arr;
		}
			
	}
?>
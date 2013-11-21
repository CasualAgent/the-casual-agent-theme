<?php

	class cPost{
		
		function __construct($p){
			global $wpdb;
			
			$wpdb->flush();
			$wpdb->db_connect();

			
			$p = ($p instanceof WP_Post)?$p:is_numeric($p)?get_post($p):exit();
			
			if(!($p instanceof WP_Post)){
				exit();
			}
			
			$this->_post = $p;
			$this->_tags = wp_get_post_tags($p->ID, array('fields'=>'all'));
			$this->_cats = wp_get_post_categories($p->ID, array('fields'=>'all'));

			

			$this->resetTxt();
			
		}
		
		function resetTxt(){
			$p = $this->_post;
			$this->_txt = implode("\n", array($p->post_title, $p->post_excerpt, $p->post_content));
			
			$this->_txt = strtolower(strip_tags(html_entity_decode($this->_txt)));
			
			$this->hasSanitized = false;
			$this->hasStrippedSW = false;
			$this->extracted = false;
			$this->isRaw = true;
			
			$w = array();
			foreach($this->_tags as $t){
				$w[] = $t->slug;
			}
			
			foreach($this->_cats as $c){
				$w[] = $c->slug;
			}
			
			$this->_slugs = implode(" ", $w);
			//$w[] = $this->_txt;
			//$w = implode(" ", $w);
			//$this->_txt = $w;
			
		}
		
		function strip_stop_words(){
			$sw = $this->get_stop_words();
			$this->_txt = preg_replace($sw, '', $this->_txt);
			
			$this->_txt = preg_replace('/[^a-zA-Z0-9\s]/', '', $this->_txt);
			$this->isRaw = false;
			$this->hasStrippedSW = true;
		}
		
		function sanitize_words(){
			$this->_txt = str_ireplace("\n", " ", $this->_txt);
			
			$toks = explode(" ", $this->_txt);
			
			array_walk($toks, create_function('&$val, $idx', '$val = trim($val); $val = empty($val)?null:$val;'));
			$toks = array_filter($toks);
			
			$toks = array_unique($toks);
			$this->_txt = implode(" ", $toks);
			$this->hasSanitized = true;
			$this->isRaw = false;
			
		}
		
		
		function extract_words(){
		
			if($this->extracted && $this->hasSanitized && $this->hasStrippedSW){
				return $this->_txt." ".$this->_slugs;
			}
			
			if($this->isRaw==false){
				$this->resetTxt();
			}
			
			$this->strip_stop_words();		
			$this->sanitize_words();
			
			
			$this->extracted = true;
			return $this->_txt." ".$this->_slugs;
		}
		
		function get_stop_words(){
			$path = get_template_directory()."/lib/stopwords.txt";
			$str = file_get_contents($path);
			$sw = explode("\n", $str);
			
			array_walk($sw, create_function('&$val, $idx', '$val = trim($val); $val = empty($val)?null:"/\\b$val\\b/i";'));
			$sw = array_filter($sw);
			
			return $sw;
		}

		function extract_text(){
			
			$p = $this->_post;
			$txt = implode(" ", array($p->post_title, $p->post_excerpt, $p->post_content));
			
			$txt = strtolower(strip_tags(html_entity_decode($txt)));
			
			$txt = str_ireplace("\n", " ", $txt);
			
			$toks = explode(" ", $txt);
			array_walk($toks, create_function('&$val, $idx', '$val = trim($val); $val = empty($val)?null:$val;'));
			$toks = array_filter($toks);
			$txt = implode(" ", $toks);
			
			$txt = preg_replace('/[^a-zA-Z0-9\s]/', '', $txt);
			
			return $txt." ".$this->_slugs;
		}
		
		function write_words(){
			global $wpdb;
			$wpdb->flush();
			$wpdb->db_connect();
			
			
			$res = $wpdb->replace( 
				'wp_post_words', 
				array( 
			        'ID' => intval($this->_post->ID),
					'words' => $this->extract_words(),
					'txt'=> $this->extract_text(),
					'tags' => 'abc',
				), 
				array( 
			        '%d',
					'%s',
					'%s',
					'%s'
				) 
			);
			
			if(!$res){
				throw new Exception($res->last_error);
			}
		}
		
		function tally(){
		
			$this->resetTxt();
			
			$this->strip_stop_words();
			
			$txt = $this->_txt;
			
			$toks = explode(" ", $txt);
			array_walk($toks, create_function('&$val, $idx', '$val = trim($val); $val = empty($val)?null:$val;'));
			$toks = array_filter($toks);
			
			$tally = array();
			
			foreach($toks as $w){
				$tally[$w] = isset($tally[$w])? ($tally[$w]+1) : 1;
			}
			$this->_tally = $tally;
			return $tally;
			$tags = array();
		}
		
		function generate_tags($df, $tDocs){
		global $wpdb;
			$wpdb->flush();
			$wpdb->db_connect();

			$this->tally();
			$tags = array();
			$max = 0;
			foreach($this->_tally as $cnt){
				
				$max = $max + $cnt;
			}
			
			
			$res = array();
			foreach($this->_tally as $w => $cnt){
				
				if($df[$w] > 0){
					$weight = ($cnt/$max) * log(1/($df[$w]/$tDocs));
					$res[$w] = $weight;
					
					if(($weight)>=0.15){
						$tags[] = $w;
					}
				}
			}

						
			
			$dbres =$wpdb->update( 
	'wp_post_words', 
	array( 
		'tags' => implode(' ', $tags)	// string
	), 
	array( 'ID' => intval($this->_post->ID )), 
	array( 
		'%s',	// value1
	), 
	array( '%d' ) 
);
			
			if(!$dbres){
				throw new Exception($dbres->last_error);
			}
			
			
			return array($res, $tags);
		}
	}
?>
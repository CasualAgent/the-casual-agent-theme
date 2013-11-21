<?
	// Insert Words into wp_word
	// Insert Tally for post
	// Compare to all other posts
	
	class cPostScore{
		
		protected static $sw = null;
		private $_tally = array();
		
		function __construct($post){
			
			$this->post = $post;

			$this->_txt = strtolower($this->post->post_title."\n".$this->post->post_excerpt."\n".$this->post->post_content);
			$this->_txt = strip_tags($this->_txt);
		}
		
		
		public static function InitStopWords(){
			$path = get_template_directory()."/lib/stopwords.txt";
			$file = fopen($path, 'r');
			
			self::$sw = array();
			while($txt = fgets($file)){
				$word = trim($txt);
				self::$sw[] = $word;
			}
			
			self::$sw = array_unique(self::$sw);
		}
		
		function process_stopwords(){
			$sw = self::$sw;
			
			$subj = $this->_txt;

			$subj = str_ireplace(array(';','.',',','?',':','"','(',')','!', "'", '&nbsp'), '', $subj);
			foreach($sw as $sword){	
				$subj = preg_replace('/\b'.$sword.'\b/i', '', $subj);
			}
			
			$this->_txt = $subj;
			return $subj;
		}
		
		function clean($stemed = true){
			$words = ($stemmed)?$this->_stemmed:explode("~", $this->_txt);
			
			foreach($words as $idx => $word){
				$word = preg_replace('/[^a-zA-Z]/', '', $word);
				
				$word = trim($word);
				
				$words[$idx] = empty($word)?null:$word;
			}
			
			$this->_stemmed = array_filter($words);
		
		return $words;	
		}
		
		function stem(){
			$tok = explode(" ", $this->_txt);			
			array_walk($tok, create_function('&$val, $idx', 'if(strlen($val)>0){$val = PorterStemmer::Stem(trim($val));}else{$val = null;}'));
			
			$tok = array_filter($tok);
			$this->_txt = implode("~", $tok);
			$this->_stemmed = $tok;
			return $this->_txt;
		}
		
		function tally(){
			//$words = explode("~", $this->_txt);
			$words = $this->_stemmed;
			$tally = array();
			foreach($words as $word){
				$tally[$word] = isset($tally[$word])?$tally[$word]+1:1;
			}
			
			$this->_tally = $tally;
			return $this->_tally;			
		}
		
		function write_tally(){
			
			global $wpdb;
			
			$val = array();
			$words = array_keys($this->_tally);
			
			$ws = array();
			foreach($words as $w){
				$val [] = "('$w', 0)";
				$ws[] = "'$w'";
			}
			
			$val = "Values".implode(",", $val);
			$ws = implode(",", $ws);
			$sql = "INSERT IGNORE INTO wp_word(word, word_weight) $val";
			$wpdb->query($sql);
			
			$sql = "SELECT * FROM wp_word WHERE word IN($ws)";

			$wpdb->query($sql);			
			
			
			$res = $wpdb->last_result;
			
			$keys = array();
			foreach($res as $w){
				$keys[$w->word] = $w->word_id;
			}
			$wpdb->delete( 'wp_word_post_idx', array( 'post_id' => $this->post->ID ));
			
			$val = array();
			$post_id = $this->post->ID;
			foreach($this->_tally as $word => $cnt){
				$id = $keys[$word];
				$val[] = "($id, $post_id, $cnt)";
			}
			
			$val = "Values".implode(",", $val);
			
			$sql = "INSERT IGNORE INTO wp_word_post_idx(word_id, post_id, count) $val";
			
			$wpdb->query($sql);
		}
		

	}
	
	cPostScore::InitStopWords();
?>
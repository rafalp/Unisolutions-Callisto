<?

/*
#===========================================================================
|
|	Unisolutions Callisto
|
|	by Rafał Pitoń
|	Copyright 2007 by Unisolutions
|	http://www.unisolutions.pl
|
#===========================================================================
|
|	This software is released under GNU General Public License v3
|	http://www.gnu.org/licenses/gpl.txt
|
#===========================================================================
|
|	Language Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class language{

	/**
	 * array containing keys to replace
	 *
	 * @var array
	 */
	private $keys;
	
	/**
	 * array containing language strings
	 *
	 * @var array
	 */
	private $strings;
	
	/**
	 * language id
	 *
	 * @var string
	 */
	private $id;
	
	/**
	 * language registration guidelines
	 *
	 * @var string
	 */
	
	public $reg_guidelines;
	
	/**
	 * orginal language
	 *
	 */
	
	var $orginal_language;
	
	public function __construct(){
		
		/**
		 * get language id from session
		 */
		
		global $settings;
		global $session;
		global $system_settings;
		global $mysql;
		global $cache;
		
		$this -> id = $session -> user['user_lang'];
		
		/**
		 * set default, if null
		 */
		
		if( empty($this -> id))
			$this -> id = $settings['default_language'];
		
		/**
		 * set default language
		 */
			
		$this -> orginal_language = $this -> id;
		
		/**
		 * now load files
		 */
		
		$lang_files = glob( ROOT_PATH.'languages/'.$this -> id.'/*.php');
		
		foreach ( $lang_files as $lang_file)
			require_once( $lang_file);
			
		/**
		 * now set basic of the basic keys
		 */
		
		$this -> setKey( 'board_name', $settings['board_title']);
		$this -> setKey( 'actual_user_id', $session -> user['user_id']);
		$this -> setKey( 'actual_user_login', $session -> user['user_login']);
		$this -> setKey( 'actual_user_link_login', '<a href="'.ROOT_PATH.'index.php?act=user&user='.$session -> user['user_id'].'">'.$session -> user['user_login'].'</a>');
		$this -> setKey( 'actual_user_new_messages', $session -> user['user_pm_new_num']);
		
		
		/**
		 * and load data from mysql
		 */
		
		$language_data = $cache -> loadCache( 'lang_'.$this -> id);
		
		if ( $language_data == false){
			
			$language_query = $mysql -> query("SELECT * FROM ".$system_settings['db_prefix']."languages WHERE `lang_id` = '".$this -> id."'");
			
			if ( $result = mysql_fetch_array($language_query, MYSQL_ASSOC))
				$language_data = $result;
							
			$cache -> saveCache( 'lang_'.$this -> id, $language_data);
				
		}
		
	}
	
	public function setKey( $key, $replacement){
		
		$key = str_ireplace( '%', '', $key);
		
		$this -> keys[$key] = $replacement;
		
	}
	
	public function getString( $id, $capital = false){
		
		$string_to_return = $this -> strings[$id];
		
		foreach ( $this -> keys as $key => $replacement)
			$string_to_return = str_ireplace( '%'.$key, $replacement, $string_to_return);
		
		if( $capital)
			$string_to_return = substr_replace( $string_to_return, strtoupper( substr( $string_to_return, 0, 1)), 0, 1);
				
		return $string_to_return;
		
	}
	
	public function switchLanguage( $new_lang){
		
		if ( $new_lang != $this -> id){
						
			$this -> id = $new_lang;
			
			$lang_files = glob( ROOT_PATH.'languages/'.$new_lang.'/*.php');
			
			foreach ( $lang_files as $lang_file)
				require_once( $lang_file);
		}
		
	}
	
	public function resetLanguage(){
		
		if ( $this -> orginal_language != $this -> id){

			$this -> id = $this -> orginal_language;
			
			$lang_files = glob( ROOT_PATH.'languages/'.$this -> id.'/*.php');
				
			foreach ( $lang_files as $lang_file)
				require_once( $lang_file);

		}
				
	}
	
	public function loadModLang( $id){
		
		global $modules;
		
		$lang_files = glob( ROOT_PATH.'modules/'.$modules[$id]['path'].'/languages/'.$this -> id.'/*.php');
		
		foreach ( $lang_files as $lang_file)
			require_once( $lang_file);
		
	}
}

?>
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
|	Plugins manager
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class plugins_manager{

	/**
	 * list of plugins
	 */
	
	var $plugins = array();
	
	/**
	 * created plugins
	 */
	
	var $loaded_plugins = array();
	
	/**
	 * constructor will load plugins
	 *
	 */
	
	function __construct(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * load plugins
		 */
		
		$plugins_list = $cache -> loadCache( 'plugins');
		
		/**
		 * check what we have loaded
		 */
		
		if ( gettype( $plugins_list) != 'array'){
			
			$plugins_query = $mysql -> query( "SELECT * FROM plugins WHERE plugin_active = '1' ORDER BY plugin_prior");
			
			while( $plugin_result = mysql_fetch_array( $plugins_query, MYSQL_ASSOC)){
				
				//clear
				$plugin_result = $mysql -> clear( $plugin_result);
				
				//add row
				$this -> plugins[$plugin_result['plugin_id']] = array(
					'plugin_prior' => $plugin_result['plugin_prior'],
					'plugin_name' => $plugin_result['plugin_name'],
					'plugin_class_name' => $plugin_result['plugin_class_name'],
					'plugin_path' => $plugin_result['plugin_path'],
					'plugin_info' => $plugin_result['plugin_info'],
					'plugin_group' => $plugin_result['plugin_group'],
					'plugin_active' => $plugin_result['plugin_active'],
					'plugin_hooks' => $plugin_result['plugin_hooks']
				);
				
			}
			
			/**
			 * save cache
			 */
			
			$cache -> saveCache( 'plugins', $this -> plugins);
			
		}else{
			
			$this -> plugins = $plugins_list;
			
		}
	}
	
	/**
	 * loads plugins hooked to specified location
	 *
	 * @param plugins $location
	 */
	
	function runPlugins( $location){
		
		
		
	}
	
	/**
	 * returns plugins hooked to specified location html
	 *
	 * @param plugins $location
	 */
	
	function getPluginsHTML(){
		
		
		
	}
	
}

?>
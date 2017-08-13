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
|	Cache Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class cache{
	
	function loadCache( $file){
		
		/**
		 * cache loading function
		 */
		
		if ( !SERVICE_MODE){
		
			if( file_exists( ROOT_PATH.'cache/'.$file.'.php')){
				
				include( ROOT_PATH.'cache/'.$file.'.php');
				
				/**
				 * check timeout
				 */
				
				if( isset($timeout) && $timeout <= time()){
					
					$this -> flushCache($file);
					
					return false;
					
				}else{
				
					if( isset( $cache)){
											
						return $cache;
						
					}else{
						
						return false;
					
					}
				}
				
			}else{
				
				return false;
				
			}
		
		}else{
			
			return false;
			
		}
		
	}
	
	function saveCache( $file, $table = false, $timeout = 0){
		
		/**
		 * firstly we will check cache folder's perms
		 */
				
		if( is_writeable( ROOT_PATH.'cache/')){
			
			if( is_writeable( ROOT_PATH.'cache/')){
			
				/**
				 * chceck if file exists
				 */
				
				if( file_exists( ROOT_PATH.'cache/'.$file.'.php')){
					
					/**
					 * file exists, delete it
					 */
					
					unlink( ROOT_PATH.'cache/'.$file.'.php');
					
				}
				
				/**
				 * now bulid content to put into new file
				 */
				
				$file_content = '<?';
				
				if( $timeout > 0){
					$file_content .= "\n\n".'$timeout = '.(time() + $timeout).';'."\n";
				}
				
				if( !empty( $table)){
				
					/**
					 * cache content isn't null
					 */
					
					$file_content .= "\n".'$cache = '.var_export( $table, true).';'."\n";
									
				}else{
					
					/**
					 * cache content is null
					 */
					
					$file_content .= "\n".'$cache = array();'."\n";
					
				}
				
				$file_content .= "\n".'?>';
			
				$cache_file = fopen( ROOT_PATH.'cache/'.$file.'.php', 'w+');
				
				fputs( $cache_file, $file_content);
				
				return true;
			}else{
				return false;
			}
			
		}else{
			
			/**
			 * cache folder isnt saveable
			 */
			
			return false;
			
		}
	}
	
	function flushCache( $file){
		
		/**
		 * cache loading function
		 */
		
		if( file_exists( ROOT_PATH.'cache/'.$file.'.php')){
			
			unlink( ROOT_PATH.'cache/'.$file.'.php');
			
			return true;
			
		}else{
			
			return false;
			
		}
		
	}
	
}

?>
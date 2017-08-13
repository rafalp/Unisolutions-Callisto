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
|	Update package
|	1.0 BETA 3 (3) -> 1.0 BETA 4 (4) 
|	by Rafał Pitoń
|
#===========================================================================
*/

switch ( $step){
	
	case 0:
		
		/**
		 * update users tab
		 */
		
		$mysql -> query( "ALTER TABLE `topics` ADD `topic_attachments` TINYINT NOT NULL DEFAULT '0' AFTER `topic_tags`");
			
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_update_structure'].' <b>"topics"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_update_structure'].' <b>"topics"</b>';
		}
	break;
	
	case 1:
		
		/**
		 * update version
		 */
		
		$mysql -> insert( array( 'version_id' => 7, 'version_short' => '1.0 RC 3', 'version_time' => time()), 'version_history');
		
		/**
		 * set message
		 */
		
		$page = 'done';
		
	break;
	
}

?>
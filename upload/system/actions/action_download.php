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
|	Send attachment
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
/**
 * draw board guidelines
 *
 */

class action_download extends action{
		
	function __construct(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
					
		/**
		 * define, what to send
		 */
		
		if ( isset( $_GET['avatar']))
			$download_type = 0;
			
		if ( isset( $_GET['attachment']))
			$download_type = 1;
		
		switch ( $download_type){
			
			case 0:
				
				/**
				 * send avatar
				 */
				
			break;
			
			case 1:
				
				/**
				 * send attachment
				 */
				
				$attachment = $_GET['attachment'];
				settype( $attachment, 'integer');
				
				/**
				 * we will select attachment from sql
				 */
				
				$attach_query = $mysql -> query( "SELECT a.*, t.* FROM attachments a LEFT JOIN attachments_types t ON a.attachment_type = t.attachments_type_id WHERE a.attachment_id = '$attachment' AND a.attachment_post > '0'");
				
				if ( $attach_result = mysql_fetch_array( $attach_query, MYSQL_ASSOC)){
					
					/**
					 * clear result
					 */
					
					$attach_result = $mysql -> clear( $attach_result);
					
					/**
					 * select post, topic, and forum
					 */
					
					$attach_forum_query = $mysql -> query( "SELECT f.forum_id FROM posts p LEFT JOIN topics t ON p.post_topic = t.topic_id LEFT JOIN forums f ON t.topic_forum_id = f.forum_id WHERE p.post_id = '".$attach_result['attachment_post']."'");
					
					if ( $attach_forum_result = mysql_fetch_array( $attach_forum_query, MYSQL_ASSOC)){
						
						$forum_id = $attach_forum_result['forum_id'];
						
						/**
						 * check if we can see forum and download
						 */
						
						if ( $session -> canSeeTopics( $forum_id) && $session -> canDownload( $forum_id)){
							
							/**
							 * send it
							 */
							
							header('Content-type: '.$attach_result['attachments_type_mime']);
							
							$proper_mimes = array( 'image/jpeg', 'image/png', 'image/gif');
																	
							if ( $_GET['thumb'] && in_array( $attach_result['attachments_type_mime'], $proper_mimes)){
								
								/**
								 * send thumb
								 */
								
								$thumb_file = str_ireplace( '.', '_thumb.', $attach_result['attachment_file']);
								
								if ( file_exists( ROOT_PATH.'uploads/'.$thumb_file)){
									
									//header("Content-length: ".filesize( ROOT_PATH.'uploads/'.$thumb_file));
									//header('Content-Disposition: attachment; filename="'.$attach_result['attachment_name'].'"');
									
									readfile(ROOT_PATH.'uploads/'.$thumb_file);
									
								}else{
									
									//header("Content-length: ".filesize( ROOT_PATH.'uploads/'.$attach_result['attachment_file']));
									//header('Content-Disposition: attachment; filename="'.$attach_result['attachment_name'].'"');
									
									readfile(ROOT_PATH.'uploads/'.$attach_result['attachment_file']);
									
								}
								
							}else{
								
								/**
								 * update stats
								 */
								
								$mysql -> update( array( 'attachment_downloads' => $attach_result['attachment_downloads'] + 1), 'attachments', "`attachment_id` = '$attachment'");
								
								$cache -> flushCache( 'attachments_'.$attach_result['attachment_post']);
								
								$proper_mimes = array( 'image/jpeg', 'image/png', 'image/gif');
								
								header("Content-length: ".filesize( ROOT_PATH.'uploads/'.$attach_result['attachment_file']));
								
								if ( !in_array( $attach_result['attachments_type_mime'], $proper_mimes))
									header('Content-Disposition: attachment; filename="'.$attach_result['attachment_name'].'"');
																
								readfile(ROOT_PATH.'uploads/'.$attach_result['attachment_file']);
								
							}
							
						}else{
							
							/**
							 * no acess to attachment
							 */
					
							$main_error = new main_error();
							$main_error -> type = 'information';
							$main_error -> message = $language -> getString( 'post_attachments_notfound');
							parent::draw( $main_error -> display());
							
						}
						
					}else{
					
						$main_error = new main_error();
						$main_error -> type = 'information';
						$main_error -> message = $language -> getString( 'post_attachments_notfound');
						parent::draw( $main_error -> display());
						
					}
					
				}else{
					
					$main_error = new main_error();
					$main_error -> type = 'information';
					$main_error -> message = $language -> getString( 'post_attachments_notfound');
					parent::draw( $main_error -> display());
					
				}
				
			break;	
			
		}
			
	}
	
}

?>
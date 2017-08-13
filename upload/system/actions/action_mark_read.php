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
|	Reads marker
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class action_mark_read extends action{
		
	function __construct(){
		
		include( FUNCTIONS_GLOBALS);
			
		/**
		 * begin from checking if we are logged in
		 */
		
		if ( $session -> user['user_id'] != -1){
			
			/**
			 * check, what we have to mark as read
			 */
			
			$forum_to_mark = $_GET['forum'];
			settype( $forum_to_mark, 'integer');
			
			if ( $forum_to_mark == 0){
				
				/**
				 * mark all forums read
				 */
				
				$mysql -> delete( "forums_reads", "`forums_read_user` = '".$session -> user['user_id']."'");
				$mysql -> delete( "topics_reads", "`topic_read_user` = '".$session -> user['user_id']."'");
				
				/**
				 * set time
				 */
				
				$read_time = time();
				
				$forums_reads_sql = array();
				$topics_reads_sql = array();
				
				/**
				 * selet forums
				 */
				
				$forums_query = $mysql -> query( "SELECT forum_id FROM forums");
				
				while ( $forum_result = mysql_fetch_array( $forums_query, MYSQL_ASSOC)){
					
					if ( $session -> canSeeTopics( $forum_result['forum_id']))
						$forums_reads_sql[] = "( ".$forum_result['forum_id'].", ".$read_time.", ".$session -> user['user_id'].")";
					
				}
				
				if ( count( $forums_reads_sql) > 0){
					
					$mysql -> query( "INSERT INTO `forums_reads` (`forums_read_forum`, `forums_read_time`, `forums_read_user`) VALUES ".join( ", ", $forums_reads_sql));
					
					/**
					 * select topics
					 */
					
					$topics_query = $mysql -> query( "SELECT topic_id, topic_forum_id FROM topics");
				
					while ( $topics_result = mysql_fetch_array( $topics_query, MYSQL_ASSOC)){
						
						if ( $session -> canSeeTopics( $topics_result['topic_forum_id']))
							$topics_reads_sql[] = "( ".$topics_result['topic_forum_id'].", ".$topics_result['topic_id'].", ".$read_time.", ".$session -> user['user_id'].")";
					
					}
				
					if ( count( $topics_reads_sql) > 0){
						
						$mysql -> query( "INSERT INTO `topics_reads` (`topic_read_forum`, `topic_read_topic`, `topic_read_time`, `topic_read_user`) VALUES ".join( ", ", $topics_reads_sql));
						
					}
				
					$output -> setRedirect( ROOT_PATH);
					
				}
				
				
				/**
				 * draw page
				 */
				
				$smode = 1;
				
				parent::draw( $style -> drawBlock( $language -> getString( 'board_summary_mark_all_read'), $language -> getString( 'mark_read_all_done').'<br /><br /><a href="'.parent::systemLink('').'">'.$language -> getString( 'main_menu_website').'</a>'));			
				
			}else{
				
				/**
				 * select forum
				 */
				
				$forum_query = $mysql -> query( "SELECT forum_id FROM forums WHERE `forum_id` = '$forum_to_mark'");
				
				if ( $forum_result = mysql_fetch_array( $forum_query, MYSQL_ASSOC)){
					
					/**
					 * check, if we can see that forum
					 */
					
					if ( $session -> canSeeForum( $forum_result['forum_id'])){
						
						if ( $session -> canSeeTopics( $forum_result['forum_id'])){
						
							/**
							 * delete reads for thi forum
							 */
							
							$mysql -> delete( "topics_reads", "`topic_read_forum` = '$forum_to_mark' AND `topic_read_user` = '".$session -> user['user_id']."'");
				
							if ( $_GET['unread']){
								
								/**
								 * draw page
								 */
								
								$smode = 1;
								
								$mysql -> delete( "forums_reads", "`forums_read_forum` = '$forum_to_mark' AND `forums_read_user` = '".$session -> user['user_id']."'");
				
								parent::draw( $style -> drawBlock( $language -> getString( 'mark_unread_read_forum_link'), $language -> getString( 'mark_unread_read_forum_done').'<br /><br /><a href="'.parent::systemLink( 'forum', array( 'forum' => $forum_to_mark)).'">'.$language -> getString( 'forums_access_show_forum').'</a>'));			
								
							}else{
							
								/**
								 * set time
								 */
								
								$read_time = time();
								
								$topics_reads_sql = array();
								
								/**
								 * select topics
								 */
								
								$topics_query = $mysql -> query( "SELECT topic_id, topic_forum_id FROM topics WHERE topic_forum_id = '$forum_to_mark'");
							
								while ( $topics_result = mysql_fetch_array( $topics_query, MYSQL_ASSOC)){
							
									$topics_reads_sql[] = "( ".$topics_result['topic_forum_id'].", ".$topics_result['topic_id'].", ".$read_time.", ".$session -> user['user_id'].")";
								
								}
							
								if ( count( $topics_reads_sql) > 0){
									
									$mysql -> query( "INSERT INTO `topics_reads` (`topic_read_forum`, `topic_read_topic`, `topic_read_time`, `topic_read_user`) VALUES ".join( ", ", $topics_reads_sql));
									
								}
							
								/**
								 * draw page
								 */
								
								$smode = 1;
								
								parent::draw( $style -> drawBlock( $language -> getString( 'mark_read_forum_link'), $language -> getString( 'mark_read_forum_done').'<br /><br /><a href="'.parent::systemLink( 'forum', array( 'forum' => $forum_to_mark)).'">'.$language -> getString( 'forums_access_show_forum').'</a>'));			
								
							}
								
							$output -> setRedirect( 'forum', array( 'forum' => $forum_to_mark));
							
						}else{
							
							/**
							 * cant read topics
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'mark_read_forum'), $language -> getString( 'forums_error_noreading')));
													
						}
						
					}else{
						
						/**
						 * cant see forum
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'mark_read_forum'), $language -> getString( 'forums_error_noforum')));
						
					}
					
				}else{
					
					/**
					 * forum not found
					 */
					
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'mark_read_forum'), $language -> getString( 'forums_error_noforum')));
			
				}
				
			}
			
		}else{
			
			$main_error = new main_error();
			$main_error -> type = 'information';
			parent::draw( $main_error -> display());
			
		}
		
	}
	
}

?>
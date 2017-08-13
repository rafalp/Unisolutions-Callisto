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
|	Main forum page
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class action_main_page extends action{
		
	function __construct(){
		
		include( FUNCTIONS_GLOBALS);
			
		/**
		 * news
		 */
		
		if ( !$settings['board_offline'] || ($settings['board_offline'] && $session -> user['user_can_see_closed_page']) || ($settings['user_require_login_to_browse'] && $session -> user['user_id'] != -1)){
		
			if ( $settings['news_turn']){
				
				/**
				 * news system on
				 */
				
				$news_found = false;
				
				$style -> drawPart( 'news', true);
				$style -> drawString( 'NEWS_TITLE', $language -> getString( 'news'));
								
				/**
				 * begin drawing
				 */
					
				$news_from_cache = array();
								
				if ( $session -> canSeeTopics( $settings['news_forum'])){
				
					/**
					 * load from cache
					 */
					
					$news_from_cache = $cache -> loadCache( 'forum_news');
					
					if ( gettype( $news_from_cache) != 'array'){
														
						/**
						 * do query
						 */
						
						$sql_news_list = array();
						
						$news_query = $mysql -> query( "SELECT t.topic_id, t.topic_name, t.topic_start_time, t.topic_start_user, t.topic_start_user_name, t.topic_posts_num, u.user_id, u.user_login, u.user_main_group, u.user_other_groups, g.users_group_prefix, g.users_group_suffix FROM topics t
						LEFT JOIN users u ON u.user_id = t.topic_start_user
						LEFT JOIN users_groups g ON g.users_group_id = u.user_main_group
						WHERE t.topic_forum_id = '".$settings['news_forum']."'
						ORDER BY topic_start_time DESC
						LIMIT ".$settings['news_draw_num']);
						
						while ( $news_result = mysql_fetch_array( $news_query, MYSQL_ASSOC)){
							
							//clear result
							$news_result = $mysql -> clear( $news_result);
							
							$sql_news_list[] = array(
								'topic_id' => $news_result['topic_id'],
								'topic_name' => $news_result['topic_name'],
								'topic_start_time' => $news_result['topic_start_time'],
								'topic_posts_num' => $news_result['topic_posts_num'],
								'topic_start_user' => $news_result['topic_start_user'],
								'topic_start_user_name' => $news_result['topic_start_user_name'],
								'user_id' => $news_result['user_id'],
								'user_login' => $news_result['user_login'],
								'user_main_group' => $news_result['user_main_group'],
								'user_other_groups' => $news_result['user_other_groups'],
								'users_group_prefix' => $news_result['users_group_prefix'],
								'users_group_suffix' => $news_result['users_group_suffix']
							);
							
						}
						
						$news_from_cache = $sql_news_list;
						$cache -> saveCache( 'forum_news', $news_from_cache, 60 * 30);
						
					}
				
				}
			
				if ( count( $news_from_cache) > 0){
					
					foreach ( $news_from_cache as $news_result){
						
						/**
						 * set keys
						 */
						
						$language -> setKey( 'news_date', $time -> drawDate( $news_result['topic_start_time']));
						$language -> setKey( 'news_comments', $news_result['topic_posts_num']);
						
						if ( $news_result['topic_start_user'] == -1){
						
							$language -> setKey( 'news_author', $news_result['users_group_prefix'].$news_result['topic_start_user_name'].$news_result['users_group_suffix']);
							
						}else{
							
							$language -> setKey( 'news_author', '<a href="'.parent::systemLink( 'user', array( 'user' => $news_result['user_id'])).'">'.$news_result['users_group_prefix'].$news_result['user_login'].$news_result['users_group_suffix'].'</a>');
							
						}
						
						/**
						 * censore
						 */
						
						$news_author_groups = array();
						$news_author_groups = split( ",", $news_result['user_other_groups']);
						$news_author_groups[] = $news_result['user_main_group'];
						
						if ( !$users -> cantCensore( $news_author_groups)){
							
							$news_author_groups['topic_name'] = $strings -> censore( $news_author_groups['topic_name']);
							
						}
						
						/**
						 * add to list
						 */
						
						$news_list[] = '<a href="'.parent::systemLink( 'topic', array( 'topic' => $news_result['topic_id'])).'" title="'.$news_result['topic_name'].'">'.$forums -> cutTopicName( $news_result['topic_name']).'</a> (<i>'.$language -> getString( 'news_info').'</i>)';
						
					}
										
					$style -> drawString( 'NEWS_CONTENT', join( "<br />", $news_list));
				
				}else{
					
					$style -> drawString( 'NEWS_CONTENT', '<i>'.$language -> getString( 'news_none').'</i>');
				
				}
				
			}else{
				
				$style -> drawPart( 'news', false);
				
			}
			
		}else{
			
			$style -> drawPart( 'news', false);
			
		}
									
		/**
		 * groups messages
		 * start from message to main group
		 */
							
		if ( strlen( $users -> users_groups[$session -> user['user_main_group']]['users_group_message']) > 0 && ( !$settings['board_offline'] || ($settings['board_offline'] && $session -> user['user_can_see_closed_page']) || ($settings['user_require_login_to_browse'] && $session -> user['user_id'] != -1)) && $smode == 0){
		
			$message_to_user_group = $strings -> parseBB( nl2br( $users -> users_groups[$session -> user['user_main_group']]['users_group_message']), true, true);
			
			if ( strlen( $users -> users_groups[$session -> user['user_main_group']]['users_group_message_title']) > 0){
			
				parent::draw( $style -> drawBlock( $users -> users_groups[$session -> user['user_main_group']]['users_group_message_title'], $message_to_user_group));
			
			}else{
				
				parent::draw( $style -> drawBlankBlock( $message_to_user_group));
			
			}
		}
				
		#if ( ALLOW_PLUGINS){				
		#	//run plugins
		#	$plugins_manager -> runPlugins( 'main_page_beginning');
		#}
		
		/**
		 * this is main page
		 * shoutbox at location 2
		 */
						
		if ( $settings['shoutbox_position'] == 2)
			$shoutbox = new action_shoutbox();
			
		/**
		 * forums list
		 */
		
		parent::draw( $forums -> drawForumsList( 0));
		
		#if ( ALLOW_PLUGINS){				
		#	//run plugins
		#	$plugins_manager -> runPlugins( 'main_after_forumslist');
		#}
		
		/**
		 * shoutbox at location 0
		 */
		
		if ( $settings['shoutbox_position'] == 0)
			$shoutbox = new action_shoutbox();
			
		/**
		 * board statistics
		 */
		
		if ( !defined( 'SIMPLE_MODE')){
		
			/**
			 * now block containing board summary
			 */
			
			$board_summary = new action_board_summary();
			
		}
		
		#if ( ALLOW_PLUGINS){				
		#	//run plugins
		#	$plugins_manager -> runPlugins( 'main_after_summary');
		#}
		
		/**
		 * shoutbox at location 1
		 */
		
		if ( $settings['shoutbox_position'] == 1)
			$shoutbox = new action_shoutbox();
			
	}
	
}

?>
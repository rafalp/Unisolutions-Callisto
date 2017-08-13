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
|	Board summary
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class action_rss_content extends action{
		
	function __construct(){
		
		include( FUNCTIONS_GLOBALS);

		//set smode
		$smode = 2;
			
		/**
		 * open RSS
		 */
		
		header('Content-type: text/xml');
		
		$rss_to_draw .= ('<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
	<channel>
		<title>'.$settings['rss_channel_title'].'</title>
		<description>'.$language -> getString( 'rss_special_info').'</description>
		<link>'.$settings['board_address'].'index.php</link>
		<pubDate>'.date( "r").'</pubDate>
		<image>
			<title>'.$settings['rss_channel_title'].'</title>
			<url></url>
			<link>'.$settings['board_address'].'index.php</link>
		</image>');
		
		/**
		 * what to do
		 */
		
		if ( $settings['rss_turn'] && !$settings['board_offline'] && !$settings['user_require_login_to_browse']){
			
			$found_topics = array();
			
			/**
			 * select rows
			 */
			
			$pre_proper_forums = $forums -> getForumsList();
			
			$proper_forums = array();
			
			foreach ( $pre_proper_forums as $forum_id => $forum_name){
				
				if ( $session -> canSeeTopics( $forum_id))
					$proper_forums[] = $forum_id;
				
			}
			
			if ( count( $proper_forums) > 0){
				
				$topics_query = $mysql -> query( "SELECT t.*, u.user_login
				FROM topics t
				LEFT JOIN users u ON t.topic_last_user = u.user_id
				WHERE t.topic_forum_id IN (".join( ",", $proper_forums).") ORDER BY t.topic_last_time DESC LIMIT ".$settings['rss_timeline']);
			
				while ( $topic_result = mysql_fetch_array( $topics_query, MYSQL_ASSOC)){
					
					//clear result
					$topic_result = $mysql -> clear( $topic_result);
					
					if ( $topic_result['topic_last_user'] == -1){
						
						$anwswer_author = $topic_result['topic_last_user_name'];
						
					}else{
						
						$anwswer_author = $topic_result['user_login'];
						
					}

					/**
					 * set keys
					 */
								
					$language -> setKey( 'last_author', $anwswer_author);
					$language -> setKey( 'posts_num', $topic_result['topic_posts_num']);
					$language -> setKey( 'views_num', $topic_result['topic_views_num']);
							
					if ( $topic_result['topic_type'] == 1){
											
						$topic_prefix = $settings['forum_stick_prefix'].': ';
						
					}else if ($topic_result['topic_survey']){
						
						$topic_prefix = ''.$settings['forum_survey_prefix'].': ';
					
					}else{
						
						$topic_prefix = '';
						
					}
						
					/**
					 * add topic to list
					 */

					$rss_to_draw .= ( '
	<item>
		<title>'.$topic_prefix.$topic_result['topic_name'].'</title>
		<pubDate>'.date( "r", $topic_result['topic_last_time']).'</pubDate>
		<link>'.$settings['board_address'].'index.php?act=topic&topic='.$topic_result['topic_id'].'&p='.ceil(($topic_result['topic_posts_num'] + 1) / $settings[ 'forum_posts_per_page'] ).'#post'.$topic_result['topic_last_post_id'].'</link>
		<description>'.$language -> getString( 'rss_topic_info').'</description>
	</item>');
					
				}
				
			}

		}
		
		/**
		 * close RSS
		 */
		
		$rss_to_draw .= ('
	</channel>
</rss>');

		/**
		 * replace &
		 */
			
		$rss_to_draw = str_replace( '&', '&amp;', $rss_to_draw);
		parent::draw( $rss_to_draw);
		
	}
	
}

?>
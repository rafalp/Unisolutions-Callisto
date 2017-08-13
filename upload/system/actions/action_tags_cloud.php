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
|	Board guidelines
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

class action_tags_cloud extends action{
		
	function __construct(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
					
		if ( $settings['forum_allow_tags']){
				
			if ( $settings['tags_cloud_enable']){
			
				/**
				 * set page title
				 */
				
				$output -> setTitle( $language -> getString( 'main_menu_tags'));
				
				/**
				 * and add breadcrumb
				 */
				
				$path -> addBreadcrumb( $language -> getString( 'main_menu_tags'), parent::systemLink( parent::getId()));
				
				/**
				 * build up an list of forums
				 */
				
				$forums_list = $forums -> getForumsList();
				
				$clear_forum_list = array();
				
				foreach ( $forums_list as $forum_id => $forum_name){
					
					if ( $session -> canSeeTopics($forum_id))
						$clear_forum_list[] = $forum_id;
				}
				
				/**
				 * select topics with any tags
				 */
				
				$preparsed_tags = array();
				
				$topics_query = $mysql -> query( "SELECT topic_tags FROM topics WHERE topic_tags <> ''");
				
				$total_tags = 0;
				
				while ( $topics_result = mysql_fetch_array( $topics_query, MYSQL_ASSOC)){
					
					$topics_result = $mysql -> clear( $topics_result);
					
					$topic_tags = split( "\n", $topics_result['topic_tags']);
					
					foreach ( $topic_tags as $topic_tag){
						
						$topic_tag = trim( strtolower($topic_tag));
	
						$preparsed_tags[$topic_tag] ++;
						$total_tags ++;
					}
								
				}
							
				/**
				 * parse tags
				 */
				
				$tags_to_draw = array();
				
				ksort( $preparsed_tags);
				
				foreach ( $preparsed_tags as $tag => $tag_nums){
					
					$tags_to_draw[] = '<a href="'.parent::systemLink( 'search', array( 'do' => 'tagged', 'tag' => urlencode( $tag))).'"><span style="font-size: '.round(100 + (($tag_nums * 100 / $total_tags))).'%">'.$tag.'</span></a>';
					
				}
				
				/**
				 * draw tags
				 */
				
				if ( count( $tags_to_draw) > 0){
				
					parent::draw($style -> drawBlock( $language -> getString( 'main_menu_tags'), join( ", ", $tags_to_draw)));
								
				}else{
				
					parent::draw($style -> drawBlock( $language -> getString( 'main_menu_tags'), $language -> getString( 'tags_none')));
				
				}
			
			}else{
						
				$main_error = new main_error();
				$main_error -> type = 'information';
				$main_error -> message = $language -> getString( 'tags_cloud_off');
				parent::draw( $main_error -> display());
				
			}
		
		}else{
						
			$main_error = new main_error();
			$main_error -> type = 'information';
			$main_error -> message = $language -> getString( 'tags_off');
			parent::draw( $main_error -> display());
			
		}
		
	}
	
}

?>
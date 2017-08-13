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
|	Post Rating
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');

class action_show_reps extends action{
		
	function __construct(){
		
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * check if function is turn
		 */
		
		if ( $settings['reputation_turn']){
			
			/**
			 * get post to draw
			 */
			
			$post_to_draw = $_GET['post'];
			settype( $post_to_draw, 'integer');
			$this -> post_to_draw = $post_to_draw;
			
			/**
			 * get post
			 */
			
			$post_query = $mysql -> query( "SELECT p.*, t.* FROM posts p LEFT JOIN topics t ON p.post_topic = t.topic_id WHERE p.post_id = '$post_to_draw'");
			
			if ( $post_result = mysql_fetch_array( $post_query, MYSQL_ASSOC)){
				
				//clear result
				$post_result = $mysql -> clear( $post_result);
				
				/**
				 * check if we can read
				 */
				
				if ( $session -> canSeeTopics( $post_result['topic_forum_id'])){
										
					/**
					 * path to topic
					 */
					
					$curren_position = $post_result['topic_forum_id'];

					if ( $curren_position != 0){
							
						while ( $curren_position != 0){
							
							/**
							 * add tree element
							 */
							
							$path_elements[$forums -> forums_list[$curren_position]['forum_name']] = parent::systemLink( 'forum', array( 'forum' => $curren_position));
							
							/**
							 * jump to next element
							 */
							
							$curren_position = $forums -> forums_list[$curren_position]['forum_parent'];
							
						}
						
						$path_elements = array_reverse( $path_elements);
						
						foreach ( $path_elements as $path_element_name => $path_element_link)
							$path -> addBreadcrumb( $path_element_name, $path_element_link);
						
					}
					
					$path -> addBreadcrumb( $post_result['topic_name'], parent::systemLink( 'topic', array( 'topic' => $post_result['topic_id'])));
					
						
					/**
					 * first breadcrumb
					 */
				
					$path -> addBreadcrumb( $language -> getString('post_rates'), parent::systemLink( parent::getId(), array( 'post' => $post_to_draw)));
					
					/**
					 * set page title
					 */
					
					$output -> setTitle($language -> getString( 'post_rates'));
					
					/**
					 * do paginating
					 */
					
					$reps_num = $mysql -> countRows( 'reputation_votes', "`reputation_vote_post` = '$post_to_draw' ORDER BY reputation_vote_time DESC");
					
					/**
					 * we will always draw 12 reps per page
					 */
					
					$pages_num = ceil( $reps_num / 12);
					
					/**
					 * get current page
					 */
					
					$current_page = $_GET['p'];
					
					$current_page --;
					
					settype( $current_page, 'integer');
					
					if ( $current_page < 0)
						$current_page = 1;
						
					if ( $current_page > $pages_num)
						$current_page = $pages_num;
								
					/**
					 * draw opinions
					 */
					
					$opinions_tab = new form();
					$opinions_tab -> openOpTable();
					
					/**
					 * select reps
					 */
					
					$reps_query = $mysql -> query( "SELECT r.*, u.user_id, u.user_login, u.user_main_group, u.user_other_groups, g.users_group_prefix, g.users_group_suffix
					FROM reputation_votes r
					LEFT JOIN users u ON r.reputation_vote_author = u.user_id
					LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id
					WHERE r.reputation_vote_post = '$post_to_draw' ORDER BY r.reputation_vote_time DESC
					LIMIT ".($current_page * 12).", 12");
					
					while ( $rep_result = mysql_fetch_array( $reps_query, MYSQL_ASSOC)) {
						
						//clear result
						$rep_result = $mysql -> clear( $rep_result);
						
						//user groups
						$sender_groups = array();
						$sender_groups = split( ",", $rep_result['user_other_groups']);
						
						$sender_groups[] = $rep_result['user_main_group'];
						
						//rep author								
						if ( $rep_result['reputation_vote_author'] == -1){
			
							/**
							 * author is deleted
							 */
																
							$post_author = '<a name="post'.$rep_result['post_id'].'" id="post'.$rep_result['post_id'].'">'.$rep_result['users_group_prefix'].$rep_result['reputation_vote_author_name'].$rep_result['users_group_suffix'].'</a>';
							
						}else{
																
							/**
							 * and user login
							 */
							
							$post_author = '<a name="post'.$rep_result['post_id'].'" id="post'.$rep_result['post_id'].'">'.'<a href="'.parent::systemLink( 'user', array( 'user' => $rep_result['reputation_vote_author'])).'">'.$rep_result['users_group_prefix'].$rep_result['user_login'].$rep_result['users_group_suffix'].'</a></a>';				
							
						}
						
						/**
						 * mark reason
						 */
						
						$post_message = $strings -> parseBB( nl2br( $rep_result['reputation_vote_reason']), false, false);
						
						if ( !$users -> cantCensore( $sender_groups))
							$post_message = $strings -> censore( $post_message);
						
						/**
						 * reps list
						 */
									
						$reps_list[0] = $language -> getString( 'rate_post_rating_0');
						$reps_list[1] = $language -> getString( 'rate_post_rating_1');
											
						/**
						 * add row
						 */
						
						$opinions_tab -> addToContent( '<tr>
							<td class="opt_row1" style="width: 170px; vertical-align: top">'.$post_author.'<br />
							'.$time -> drawDate( $rep_result['reputation_vote_time']).'<br /><br />
							<b>'.$language -> getString( 'search_results_topic').':</b> <a href="'.parent::systemLink( 'topic', array( 'topic' => $post_result['topic_id'])).'">'.$post_result['topic_name'].'</a><br />
							<b>'.$language -> getString( 'search_results_topic_replies').':</b> '.$post_result['topic_posts_num'].'<br />
							<b>'.$language -> getString( 'rate_post_rating').':</b> '.$reps_list[$rep_result['reputation_vote_power']].'
							</td>
							<td class="opt_row2" style="vertical-align: top">'.$post_message.'</td>
						</tr>
						<tr>
							<td colspan="2" class="post_end"></td>
						</tr>');
						
					}
					
					/**
					 * close and draw table
					 */
					
					$opinions_tab -> closeTable();
					
					parent::draw( $style -> drawFormBlock( $language -> getString( 'post_rates'), $opinions_tab -> display()));
					
					/**
					 * and paginator
					 */
					
					parent::draw( $style -> drawPaginator( parent::systemLink( parent::getId(), array( 'post' => $post_to_draw)), 'p', $pages_num, ($current_page+1)));
						
									
				}else{
					
					/**
					 * post "not found"
					 */
					
					$main_error = new main_error();
					$main_error -> type = 'information';
					parent::draw( $main_error -> display());
											
				}
				
			}else{
				
				/**
				 * post not found
				 */
						
				$main_error = new main_error();
				$main_error -> type = 'information';
				parent::draw( $main_error -> display());
							
			}
			
		}else{
							
			$main_error = new main_error();
			$main_error -> type = 'information';
			$main_error -> message = $language -> getString( 'rate_post_off');
			parent::draw( $main_error -> display());
			
		}
				
	}
	
}

?>
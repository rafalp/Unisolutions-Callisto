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
|	Post Reporting
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');

class action_report_post extends action{
		
	function __construct(){
		
		include( FUNCTIONS_GLOBALS);
			
		/**
		 * check if we can access
		 */
		
		if ( $session -> user['user_id'] != -1){
			
			/**
			 * check if function is turn
			 */
			
			if ( $settings['users_allow_post_report']){
				
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
					
						$path -> addBreadcrumb( $language -> getString('report_post'), parent::systemLink( parent::getId(), array( 'post' => $post_to_draw)));
						
						/**
						 * set page title
						 */
						
						$output -> setTitle($language -> getString( 'report_post'));
						
						/**
						 * check, what to do
						 */
						
						if ( $session -> checkForm() && $_GET['finalize']){
							
							$report_text = $strings -> inputClear( $_POST['report_text'], false);
							
							/**
							 * check its length
							 */
							
							if ( strlen( $report_text) == 0){
								
								/**
								 * reason is empty
								 */
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'report_post'), $language -> getString( 'report_post_noreason')));
								
								$this -> drawForm( true);
							
							}else{
								
								/**
								 * add report, and return
								 */
								
								$new_report_sql['post_report_post'] = $post_to_draw;
								$new_report_sql['post_report_user'] = $session -> user['user_id'];
								$new_report_sql['post_report_user_name'] = uniSlashes( $session -> user['user_login']);
								$new_report_sql['post_report_time'] = time();
								$new_report_sql['post_report_text'] = $report_text;
								
								$mysql -> insert( $new_report_sql, 'posts_reports');
								
								/**
								 * update our post
								 */
								
								$mysql -> update( array( 'post_reported' => true), 'posts', "`post_id` = '$post_to_draw'");
								
								/**
								 * draw information
								 */
								
								parent::draw( $style -> drawBlock( $language -> getString( 'report_post'), $language -> getString( 'report_post_done').'<br /><br /><a href="'.parent::systemLink( 'topic', array( 'topic' => $post_result['topic_id'])).'">'.$language -> getString( 'new_topic_new_go_topic').'</a><br /><a href="'.parent::systemLink( 'forum', array( 'forum' => $post_result['topic_forum_id'])).'">'.$language -> getString( 'new_topic_new_go_forum').'</a>'));
								
							}
							
						}else{
							
							/**
							 * draw form
							 */
							
							$this -> drawForm();
							
						}
						
					}else{
						
						/**
						 * post "not found"
						 */
						
						$main_error = new main_error();
						$main_error -> type = 'error';
						parent::draw( $main_error -> display());	
						
					}
					
				}else{
					
					/**
					 * post not found
					 */
								
					$main_error = new main_error();
					$main_error -> type = 'error';
					parent::draw( $main_error -> display());	
				
				}
				
			}else{
								
				$main_error = new main_error();
				$main_error -> type = 'information';
				$main_error -> message = $language -> getString( 'report_post_shuted_down');
				parent::draw( $main_error -> display());
				
			}
			
		}else{
			
			$main_error = new main_error();
			$main_error -> type = 'information';
			parent::draw( $main_error -> display());
			
		}
		
	}
	
	/**
	 * draws mod form
	 */
	
	function drawForm( $retake = false){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * set values
		 */
		
		$report_text = '';
		
		if ( $retake){
			
			$report_text = stripslashes( $strings -> inputClear( $_POST['report_text'], false));
							
		}
		
		/**
		 * begin drawing form
		 */
		
		$report_form = new form();
		$report_form -> openForm( parent::systemLink( parent::getId(), array( 'post' => $this -> post_to_draw, 'finalize' => true)));
		$report_form -> openOpTable();
		
		$report_form -> drawTextBox( $language -> getString( 'report_post_reason'), 'report_text', $report_text);
		
		$report_form -> closeTable();
		$report_form -> drawButton( $language -> getString( 'report_post_report'));
		$report_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'report_post'), $report_form -> display()));
		
	}
	
}

?>
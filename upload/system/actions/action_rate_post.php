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

class action_rate_post extends action{
		
	function __construct(){
		
		include( FUNCTIONS_GLOBALS);
			
		/**
		 * check if we can access
		 */
		
		if ( $session -> user['user_id'] != -1){
			
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
						 * check if we can rate our post
						 */
						
						if ( $post_result['post_author'] != $session -> user['user_id']){
							/**
							 * check if we can rate our post
							 */
							
							if ( $post_result['post_author'] != -1){
							
								/**
								 * check marks
								 */
								
								$rates_today = $mysql -> countRows( 'reputation_votes', "`reputation_vote_author` = '".$session -> user['user_id']."' AND `reputation_vote_time` > '".(time() - ( 24 * 3600))."'");
								
								if ( $rates_today < $settings['reputation_day_limit']){
																	
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
								
									$path -> addBreadcrumb( $language -> getString('rate_post'), parent::systemLink( parent::getId(), array( 'post' => $post_to_draw)));
									
									/**
									 * set page title
									 */
									
									$output -> setTitle($language -> getString( 'rate_post'));
									
									/**
									 * check, what to do
									 */
									
									if ( $session -> checkForm() && $_GET['finalize']){
										
										$rate_text = $strings -> inputClear( $_POST['rate_text'], false);
										$rate_power	= $_POST['rate_power'];	
										settype( $rate_power, 'bool');
																
										/**
										 * check its length
										 */
										
										if ( strlen( $rate_text) == 0){
											
											/**
											 * reason is empty
											 */
											
											parent::draw( $style -> drawErrorBlock( $language -> getString( 'rate_post'), $language -> getString( 'rate_post_noreason')));
											
											$this -> drawForm( true);
										
										}else{
											
											/**
											 * add report, and return
											 */
											
											$new_report_sql['reputation_vote_user'] = $post_result['post_author'];
											$new_report_sql['reputation_vote_post'] = $post_to_draw;
											$new_report_sql['reputation_vote_author'] = $session -> user['user_id'];
											$new_report_sql['reputation_vote_author_name'] = uniSlashes( $session -> user['user_login']);
											$new_report_sql['reputation_vote_time'] = time();
											$new_report_sql['reputation_vote_power'] = $rate_power;
											$new_report_sql['reputation_vote_reason'] = $rate_text;
											
											$post_rates = $mysql -> query( "SELECT * FROM reputation_votes WHERE `reputation_vote_post` = '".$this -> post_to_draw."' AND `reputation_vote_author` = '".$session -> user['user_id']."'");
			
											if ( $rate_result = mysql_fetch_array( $post_rates, MYSQL_ASSOC)){
												
												$rate_result = $mysql -> clear( $rate_result);
												
												$rate_id = $rate_result['reputation_vote_id'];
												
												$mysql -> update( $new_report_sql, 'reputation_votes', "`reputation_vote_id` = '$rate_id'");
												
												if ( $rate_power != $rate_result['reputation_vote_power']){
													
													$rate_power_new = '+ 0';
													
												}else{
													
													if ( $rate_power){
														
														$rate_power_new = '+ 1';
													
													}else{
														
														$rate_power_new = '- 1';
														
													}
												}
												
												$mysql -> query( "UPDATE users SET `user_rep` = user_rep $rate_power_new WHERE `user_id` = '".$post_result['post_author']."'");
												
											}else{
											
												$mysql -> insert( $new_report_sql, 'reputation_votes');
												
												/**
												 * select user
												 */
											
												if ( $rate_power){
												
													$mysql -> query( "UPDATE users SET `user_rep` = user_rep + 1 WHERE `user_id` = '".$post_result['post_author']."'");
												
												}else{
													
													$mysql -> query( "UPDATE users SET `user_rep` = user_rep - 1 WHERE `user_id` = '".$post_result['post_author']."'");
												
												}
												
											}
												
											/**
											 * update our post
											 */
											
											$mysql -> update( array( 'post_thanked' => true), 'posts', "`post_id` = '$post_to_draw'");
											
											/**
											 * draw information
											 */
											
											parent::draw( $style -> drawBlock( $language -> getString( 'rate_post'), $language -> getString( 'rate_post_done').'<br /><br /><a href="'.parent::systemLink( 'topic', array( 'topic' => $post_result['topic_id'])).'">'.$language -> getString( 'new_topic_new_go_topic').'</a><br /><a href="'.parent::systemLink( 'forum', array( 'forum' => $post_result['topic_forum_id'])).'">'.$language -> getString( 'new_topic_new_go_forum').'</a>'));
											
										}
										
									}else{
										
										/**
										 * draw form
										 */
										
										$this -> drawForm();
										
									}
																
								}else{
								
									/**
									 * cant mark yourself
									 */
						
									$main_error = new main_error();
									$main_error -> type = 'information';
									$main_error -> message = $language -> getString( 'rate_post_outrates');
									parent::draw( $main_error -> display());
												
								}
							
							}else{
						
								/**
								 * cant mark guest
								 */
																
								$main_error = new main_error();
								$main_error -> type = 'information';
								$main_error -> message = $language -> getString( 'rate_post_guest');
								parent::draw( $main_error -> display());	
															
							}	
							
						}else{
						
							/**
							 * cant mark yourself
							 */
														
							$main_error = new main_error();
							$main_error -> type = 'information';
							$main_error -> message = $language -> getString( 'rate_post_yourself');
							parent::draw( $main_error -> display());	
													
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
				$main_error -> message = $language -> getString( 'rate_post_off');
				parent::draw( $main_error -> display());	
				
			}
			
		}else{
			
			$main_error = new main_error();
			$main_error -> type = 'error';
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
		 * have we already rated that post?
		 */
	
		$post_rates = $mysql -> query( "SELECT * FROM reputation_votes WHERE `reputation_vote_post` = '".$this -> post_to_draw."' AND `reputation_vote_author` = '".$session -> user['user_id']."'");
		
		if ( $rate_result = mysql_fetch_array( $post_rates, MYSQL_ASSOC)){
			
			$rate_result = $mysql -> clear( $rate_result);
			
			$rate_id = $rate_result['reputation_vote_id'];
			$rate_power = $rate_result['reputation_vote_power'];
			$rate_text = $rate_result['reputation_vote_reason'];
			
		}else{
			
			$rate_text = '';
		
			if ( isset( $_GET['d'])){
				
				$rate_power = $_GET['d'];
				settype( $rate_power, 'bool');
				
			}
			
		}
		
		/**
		 * set values
		 */
				
		if ( $retake){
			
			$rate_text = stripslashes( $strings -> inputClear( $_POST['rate_text'], false));
			$rate_power	= $_POST['rate_power'];	
			settype( $rate_power, 'bool');
			
		}
		
		/**
		 * begin drawing form
		 */
		
		$report_form = new form();
		$report_form -> openForm( parent::systemLink( parent::getId(), array( 'post' => $this -> post_to_draw, 'finalize' => true)));
		$report_form -> openOpTable();
		
		$rates[0] = $language -> getString( 'rate_post_rating_0');
		$rates[1] = $language -> getString( 'rate_post_rating_1');
		
		$report_form -> drawList( $language -> getString( 'rate_post_rating'), 'rate_power', $rates, $rate_power);
		$report_form -> drawTextBox( $language -> getString( 'rate_post_reason'), 'rate_text', $rate_text);
		
		$report_form -> closeTable();
		$report_form -> drawButton( $language -> getString( 'rate_post_button'));
		$report_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'rate_post'), $report_form -> display()));
		
	}
	
}

?>
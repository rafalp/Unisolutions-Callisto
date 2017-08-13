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
|	Users warns list
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');

class action_user_warns extends action{
		
	function __construct(){
		
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * select user
		 */
		
		$user_to_draw = $_GET['user'];
		settype( $user_to_draw, 'integer');
			
		/**
		 * check if we can acces
		 */
		
		if ( $session -> user['user_can_be_mod'] || $session -> user['user_id'] == $user_to_draw){
			
			$user_query = $mysql -> query( "SELECT * FROM users WHERE `user_id` > '0' AND `user_id` = '$user_to_draw'");
			
			if ( $user_result = mysql_fetch_array( $user_query, MYSQL_ASSOC)){
			
				//clear
				$user_result = $mysql -> clear( $user_result);
				
				/**
				 * first breadcrumb
				 */
	
				$path -> addBreadcrumb( $language -> getString('mod_cp').': '.$user_result['user_login'], parent::systemLink( 'mod_cp'));
				
				/**
				 * set page title
				 */
				
				$output -> setTitle($language -> getString( 'mod_cp').': '.$user_result['user_login']);
				
				$count_reports = $mysql -> query( "SELECT COUNT(*) FROM users_warnings
				WHERE user_warning_user = '$user_to_draw'");
				
				if ( $reports_num = mysql_fetch_array( $count_reports, MYSQL_NUM))
					$reports_num = $reports_num[0];
		
				settype( $reports_num, 'integer');
				
				/**
				 * build up paginating
				 */
				
				$pages_number = ceil( $reports_num / 20);
							
				/**
				 * select reported posts
				 */
				
				if( isset( $_GET['p'])){
								
					$page_to_draw = $_GET['p'];
					settype( $page_to_draw, 'integer');
				
				}else{
					
					$page_to_draw = 1;
					
				}
				
				if ( $page_to_draw > $pages_number){
					
					$page_to_draw = 1;
					
				}
				
				if ( $page_to_draw < 1){
					
					$page_to_draw = 1;
					
				}
				
				$page_to_draw -= 1;
				
				/**
				 * paginator
				 */
				
				$paginator_html = $style -> drawPaginator( parent::systemLink( parent::getId(), array( 'user' => $user_to_draw)), 'p', $pages_number, ( $page_to_draw + 1));
									
				/**
				 * open form
				 */
			
				$user_warns_list = new form();
				$user_warns_list -> openOpTable();
				$user_warns_list -> addToContent( '<tr>
					<th>'.$language -> getString( 'mod_cp_user_warns_mod').'</th>
					<th>'.$language -> getString( 'mod_cp_user_warns_text').'</th>
				</tr>');
				
				$select_reports = $mysql -> query( "SELECT w.*, u.user_id, u.user_main_group, u.user_other_groups, u.user_login, g.users_group_prefix, g.users_group_suffix FROM users_warnings w
				LEFT JOIN users u ON w.user_warning_mod = u.user_id
				LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id
				WHERE w.user_warning_user = '$user_to_draw' ORDER BY w.user_warning_time DESC LIMIT ".($page_to_draw* 20).",20");
				
				while ( $report_result = mysql_fetch_array( $select_reports, MYSQL_ASSOC)) {
					
					//clear result
					$report_result = $mysql -> clear( $report_result);
					
					if ( $report_result['user_warning_mod'] == -1){
			
						/**
						 * author is deleted
						 */
															
						$post_author = $report_result['users_group_prefix'].$report_result['post_author_name'].$report_result['users_group_suffix'];
						
					}else{
															
						/**
						 * and user login
						 */
						
						$post_author = '<a href="'.parent::systemLink( 'user', array( 'user' => $report_result['user_warning_mod'])).'">'.$report_result['users_group_prefix'].$report_result['user_login'].$report_result['users_group_suffix'].'</a>';				
						
					}
					
					/**
					 * and message
					 */
					
					$post_message = nl2br( $report_result['user_warning_text']);
					
					$user_groups = array();
					$user_groups = split( ",", $post_result['user_other_groups']);
					$user_groups[] = $post_result['user_main_group'];
					
					if ( !$users -> cantCensore( $user_groups))
						$post_message = $strings -> censore( $post_message);
						
					/**
					 * warning image
					 */
									
					if ( $report_result['user_warning_direction']){
						
						$warning_image = $style -> drawImage( 'minus', $language -> getString( 'mod_warn_power_1'));
						
					}else{
						
						$warning_image = $style -> drawImage( 'plus', $language -> getString( 'mod_warn_power_0'));
						
					}
							
					/**
					 * insert row
					 */
					
					$user_warns_list -> addToContent( '<tr>
						<td class="opt_row1" style="width: 170px; vertical-align: top">'.$post_author.'<br />
						'.$time -> drawDate( $report_result['user_warning_time']).'<br /><br />
						<b>'.$language -> getString( 'mod_warn_power').':</b> '.$warning_image.'<br />
						</td>
						<td class="opt_row2" style="vertical-align: top">'.$post_message.'</td>
					</tr>
					<tr>
						<td colspan="2" class="post_end"></td>
					</tr>');
					
					//found reports
					$found_reports = true;
					
				}
				
				if ( !isset( $found_reports))
					$user_warns_list -> addToContent( '<tr><td class="opt_row1" colspan="2">'.$language -> getString( 'mod_cp_warns_list_empty').'</td></tr>');
				
			
				$user_warns_list -> closeTable();
				
				parent::draw( $style -> drawFormBlock( $language -> getString( 'mod_cp').': '.$user_result['user_login'], $user_warns_list -> display()));
				
				parent::draw( $paginator_html);
				
			}else{
			
				$main_error = new main_error();
				$main_error -> type = 'error';
				$main_error -> message = $language -> getString( 'mod_nouser');
				parent::draw( $main_error -> display());
				
			}
			
		}else{
			
			$main_error = new main_error();
			$main_error -> type = 'error';
			parent::draw( $main_error -> display());
			
		}
		
	}

}

?>
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
|	Forum Team list
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

class action_team extends action{
		
	function __construct(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'board_summary_the_team'));
		
		/**
		 * and add breadcrumb
		 */
		
		$path -> addBreadcrumb( $language -> getString( 'board_summary_the_team'), parent::systemLink( parent::getId()));
		
		/**
		 * draw list
		 */
		
		$team_list = new form();		
		$team_list -> drawSpacer( $language -> getString( 'team_admins'));
		$team_list -> openOpTable( true);
		$team_list -> addToContent( '<tr>
			<th>'.$language -> getString( 'team_users').'</th>
			<th>'.$language -> getString( 'team_groups').'</th>
		</tr>');
		
		/**
		 * admins
		 */
		
		$groups_query = $mysql -> query( "SELECT users_group_id FROM users_groups WHERE `users_group_can_use_acp` = '1'");
		
		$global_admin_groups = array();
		
		while ($groups_result = mysql_fetch_array( $groups_query, MYSQL_ASSOC)) {
			
			$global_admin_groups[] = $groups_result['users_group_id'];
			
		}
		
		$other_groups = '';
		
		foreach ( $global_admin_groups as $group_id){
			
			$other_groups .= "OR u.user_other_groups LIKE '$group_id,%' OR u.user_other_groups LIKE '%,$group_id,%' OR u.user_other_groups LIKE '%,$group_id' ";
			
		}
		
		if ( !empty( $other_groups)){
			
			$local_mods_query = $mysql -> query( "SELECT u.user_id, u.user_login, g.users_group_name, g.users_group_prefix, g.users_group_suffix FROM users u LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id WHERE u.user_main_group IN (".join( ",", $global_admin_groups).") ".$other_groups);
			
			while ( $users_result = mysql_fetch_array( $local_mods_query, MYSQL_ASSOC)){
				
				//clear result
				$users_result = $mysql -> clear( $users_result);
				
				/**
				 * add row
				 */
				
				$team_list -> addToContent( '<tr>
					<td class="opt_row1"><a href="'.parent::systemLink( 'user', array( 'user' => $users_result['user_id'])).'">'.$users_result['user_login'].'</a></td>
					<td class="opt_row2">'.$users_result['users_group_prefix'].$users_result['users_group_name'].$users_result['users_group_suffix'].'</td>
				</tr>');
				
				$drawed_users[] = $users_result['user_id'];
			
			}
		
		}
		
		$team_list -> closeTable();
		$team_list -> drawSpacer( $language -> getString( 'team_mods'));
		$team_list -> openOpTable( true);
		$team_list -> addToContent( '<tr>
			<th>'.$language -> getString( 'team_users').'</th>
			<th>'.$language -> getString( 'team_groups').'</th>
		</tr>');
		
		/**
		 * select groups
		 */
		
		$global_team_groups = array();
		$local_team_groups = array();
		$team_members = array();
		
		$groups_query = $mysql -> query( "SELECT users_group_id FROM users_groups WHERE `users_group_can_moderate` = '1'");
		
		while ($groups_result = mysql_fetch_array( $groups_query, MYSQL_ASSOC)) {
			
			$global_team_groups[] = $groups_result['users_group_id'];
			
		}
		
		/**
		 * and mods
		 */
		
		$mods_query = $mysql -> query( "SELECT * FROM moderators");
				
		while ( $mods_result = mysql_fetch_array( $mods_query, MYSQL_ASSOC)){
			
			if ( $mods_result['moderator_user_id'] > 0){
				
				/**
				 * users moderation
				 */
				
				$team_members[] = $mods_result['moderator_user_id'];
								
			}else{
				
				/**
				 * groups moderation
				 */
				
				if ( !in_array( $mods_result['moderator_group_id'], $global_team_groups)){
					
					$local_team_groups[] = $mods_result['moderator_group_id'];
								
				}
				
			}
			
		}
		
		/**
		 * select users belonging to global mods
		 */
				
		/**
		 * main
		 */
		
		$other_groups = '';
		
		foreach ( $global_team_groups as $group_id){
			
			$other_groups .= "OR u.user_other_groups LIKE '$group_id,%' OR u.user_other_groups LIKE '%,$group_id,%' OR u.user_other_groups LIKE '%,$group_id' ";
			
		}
		
		foreach ( $local_team_groups as $group_id){
			
			$other_groups .= "OR u.user_other_groups LIKE '$group_id,%' OR u.user_other_groups LIKE '%,$group_id,%' OR u.user_other_groups LIKE '%,$group_id' ";
			
		}
		
		foreach ( $team_members as $member_id){
			
			$other_groups .= "OR u.user_id = '$member_id' ";
			
		}
		
		if ( !empty( $other_groups) || !empty( $local_team_groups) || count( $team_members) > 0){
			
			$local_mods_query = $mysql -> query( "SELECT u.user_id, u.user_login, g.users_group_name, g.users_group_prefix, g.users_group_suffix FROM users u LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id WHERE u.user_main_group IN (".join( ",", $global_team_groups).") ".$other_groups.$other_users);
			
			while ( $users_result = mysql_fetch_array( $local_mods_query, MYSQL_ASSOC)){
				
				if ( !in_array( $users_result['user_id'], $drawed_users)){
				
					//clear result
					$users_result = $mysql -> clear( $users_result);
					
					/**
					 * add row
					 */
					
					$team_list -> addToContent( '<tr>
						<td class="opt_row1"><a href="'.parent::systemLink( 'user', array( 'user' => $users_result['user_id'])).'">'.$users_result['user_login'].'</a></td>
						<td class="opt_row2">'.$users_result['users_group_prefix'].$users_result['users_group_name'].$users_result['users_group_suffix'].'</td>
					</tr>');
					
					$drawed_users[] = $users_result['user_id'];
					
				}
			}
		
		}
			
		/**
		 * other
		 */
				
		$other_groups = '';
		
		if ( !empty( $local_team_groups)){
			$other_groups .= "u.user_main_group IN (".join( ",", $local_team_groups).")";
		}
		
		foreach ( $local_team_groups as $group_id){
			
			$other_groups .= "OR u.user_other_groups LIKE '$group_id,%' OR u.user_other_groups LIKE '%,$group_id,%' OR u.user_other_groups LIKE '%,$group_id' ";
			
		}
				
		if ( !empty( $other_groups)){
			
			$global_mods_query = $mysql -> query( "SELECT u.user_id, u.user_login, g.users_group_name, g.users_group_prefix, g.users_group_suffix FROM users u LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id WHERE ".$other_groups);
			
			while ( $users_result = mysql_fetch_array( $global_mods_query, MYSQL_ASSOC)){
				
				//clear result
				$users_result = $mysql -> clear( $users_result);
				
				if ( !in_array( $users_result['user_id'], $drawed_users)){
					
					/**
					 * add row
					 */
										
					$team_list -> addToContent( '<tr>
						<td class="opt_row1"><a href="'.parent::systemLink( 'user', array( 'user' => $users_result['user_id'])).'">'.$users_result['user_login'].'</a></td>
						<td class="opt_row2">'.$users_result['users_group_prefix'].$users_result['users_group_name'].$users_result['users_group_suffix'].'</td>
					</tr>');
				
					$drawed_users[] = $users_result['user_id'];
								
				}
					
			}
		
		}
		$team_list -> closeTable();
		
		/**
		 * display
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'board_summary_the_team'), $team_list -> display()));
		
	}
	
}

?>
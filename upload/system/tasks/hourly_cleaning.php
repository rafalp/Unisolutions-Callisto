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
|	Delete death sessions and generations
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
		
//build last visits time
	
$users_last_visits = array();

$outdated_users_sessions_query = $mysql -> query( "SELECT * FROM users_sessions WHERE `users_session_last_time` < '".(time() - $settings['session_time'])."'");

while ( $sessions_result = mysql_fetch_array( $outdated_users_sessions_query, MYSQL_ASSOC)){
	
	if ( $sessions_result['users_session_user_id'] != -1)
		$users_last_visits[$sessions_result['users_session_user_id']] = $sessions_result['users_session_last_time'];

}

foreach ( $users_last_visits as $user_last_visit_id => $user_last_visit_time){
	
	$last_visit_update_sql['user_last_login'] = $user_last_visit_time;
	
	$mysql -> update( $last_visit_update_sql, 'users', "`user_id` = '$user_last_visit_id'");
	
	if ( $user_last_visit_id == $session -> user['user_id'])
		$session -> user['user_last_login'] = $user_last_visit_time;
	
}

/**
 * and attachments
 */

$attachments_query = $mysql -> query( "SELECT * FROM attachments WHERE `attachment_post` = '0' AND `attachment_time` < '".(time() - $settings['session_time'])."'");

while ( $attachments_result = mysql_fetch_array( $attachments_query, MYSQL_ASSOC)){
	
	$attachments_result = $mysql -> clear( $attachments_result);
	
	if ( file_exists( ROOT_PATH.'uploads/'.$attachments_result['attachment_file']))
		unlink( ROOT_PATH.'uploads/'.$attachments_result['attachment_file']);
}

//delete them

$mysql -> delete( "admins_sessions", "`admin_session_last_time` < '".(time() - $settings['session_time'])."'");
$mysql -> delete( "users_sessions", "`users_session_last_time` < '".(time() - $settings['session_time'])."'");
$mysql -> delete( "attachments", "`attachment_post` = '0' AND `attachment_time` < '".(time() - $settings['session_time'])."'");

//same for captcha generations
		
$mysql -> delete( "captcha_generations", "`captcha_generated` < '".(time() - 990)."'");

//and searchs results

$mysql -> delete( "searchs_results", "`search_time` < '".(time() - 990)."'");
$mysql -> delete( "users_autologin", "`users_autologin_last_use` < '".(time() - ( 30 * 24 * 3600))."'");

/**
 * lets make sure we will clear cache
 */

$cache -> flushCache( 'users_online');
$cache -> flushCache( 'spiders_online');

?>
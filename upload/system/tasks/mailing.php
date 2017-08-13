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
|	Send Mailing
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');


/**
 * select fist udone mailing
 */

$mails_query = $mysql -> query( "SELECT * FROM mails WHERE `mail_done` = '0'");

if ( $mails_result = mysql_fetch_array( $mails_query, MYSQL_ASSOC)){
	
	//celar result
	$mails_result = $mysql -> clear( $mails_result);
	
	/**
	 * condition
	 */
	
	if ( $mails_result['mail_toall']){
		
		$to_all = '';
		
	}else{
		
		$to_all = "`user_want_mail` = '0'";
	
	}
	
	/**
	 * prepare mail
	 */
	
	$mail_title = $mails_result['mail_subject'];
	
	$mail_text = $mails_result['mail_text'];
	
	$basic_keys['SITE_NAME'] = $settings['board_name'];
	$basic_keys['SITE_URL'] = $settings['board_address'];
	$basic_keys['SITE_POSTS'] = $settings['board_posts_total'];
	$basic_keys['SITE_TOPICS'] = $settings['board_threads_total'];
	$basic_keys['SITE_USERS'] = $settings['users_num'];
	
	foreach ( $basic_keys as $key_id => $key_content){
		
		$mail_title = str_ireplace( "{$key_id}", $key_content, $mail_title);
		$mail_text = str_ireplace( "{$key_id}", $key_content, $mail_text);
	}
	
	/**
	 * select users, and send mails to them
	 * we will not send more than 20 mails at time
	 */
	
	$sended_mails = 0;
		
	$last_user = 0;
	
	$users_query = $mysql -> query( "SELECT user_id, user_login, user_mail, user_posts_num FROM users WHERE `user_id` > '0' AND `user_id` >= '".$mails_result['mail_actual_user']."' AND `user_id` <= '".$mails_result['mail_end_at_user']."' ".$to_all." LIMIT 20");
	
	while ( $user_result = mysql_fetch_array( $users_query, MYSQL_ASSOC)){
		
		//clear result
		$user_result = $mysql -> clear( $user_result);
		
		/**
		 * do parsing
		 */
		
		$final_mail_title = $mail_title;
		$final_mail_text = $mail_text;
	
		$final_keys['USER_NAME'] = $user_result['user_login'];
		$final_keys['USER_POSTS'] = $user_result['user_posts_num'];
		
		foreach ( $final_keys as $key_id => $key_content){
			
			$final_mail_title = str_ireplace( "{$key_id}", $key_content, $final_mail_title);
			$final_mail_text = str_ireplace( "{$key_id}", $key_content, $final_mail_text);
		}
	
		/**
		 * send mail
		 */
		
		$mail -> send( $user_result['user_mail'], $final_mail_title, $final_mail_text);
		
		/**
		 * set lasts
		 */
		
		$last_user = $user_result['user_id'];
		$sended_mails ++;
		
	}
	
	if ( $sended_mails < 20){
		
		/**
		 * we finished sending mails with unfull number
		 * which means we finished that mailing
		 */
		
		$mysql -> update( array( 'mail_done' => true, 'mail_last_time' => time()), 'mails', "`mail_id` = '".$mails_result['mail_id']."'");
	
	}else{
		
		/**
		 * we will have to finish mailing at next run
		 */
		
		$mysql -> update( array( 'mail_actual_user' => $last_user, 'mail_last_time' => time()), 'mails', "`mail_id` = '".$mails_result['mail_id']."'");
			
	}
	
}else{
	
	/**
	 * deactivate us
	 */
	
	$mysql -> update( array( 'task_active' => false), 'tasks', "`task_file` = 'mailing.php'");
	
}


?>
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
|	Update package
|	1.0 BETA 3 (3) -> 1.0 BETA 4 (4) 
|	by Rafał Pitoń
|
#===========================================================================
*/

switch ( $step){
	
	case 0:
		
		/**
		 * update admins sessions
		 */
		
		$mysql -> query( "ALTER TABLE `admins_sessions` CHANGE `admin_session_ip` `admin_session_ip` CHAR(32) NOT NULL DEFAULT '-1'");
		$mysql -> query( "ALTER TABLE `admins_loging_log` CHANGE `admins_login_log_user_ip` `admins_login_log_user_ip` CHAR(32) NOT NULL DEFAULT '-1'");
		$mysql -> query( "ALTER TABLE `admins_logs` CHANGE `admins_log_user_ip` `admins_log_user_ip` CHAR(32) NOT NULL DEFAULT '-1'");
		$mysql -> query( "ALTER TABLE `mails_logs` CHANGE `mails_log_ip` `mails_log_ip` CHAR(32) NOT NULL DEFAULT '-1'");
		$mysql -> query( "ALTER TABLE `moderators_logs` CHANGE `moderators_log_user_ip` `moderators_log_user_ip` CHAR(32) NOT NULL DEFAULT '-1'");
		$mysql -> query( "ALTER TABLE `posts` CHANGE `post_ip` `post_ip` CHAR(32) NOT NULL DEFAULT '-1'");
		$mysql -> query( "ALTER TABLE `spiders_logs` CHANGE `spider_log_ip` `spider_log_ip` CHAR(32) NOT NULL DEFAULT '-1'");
			
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_update_content'].' <b>"admins_sessions"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_update_content'].' <b>"admins_sessions"</b>';
		}
		
	break;
	
	case 1:
		
		/**
		 * update settings
		 */
		
		$mysql -> query( "ALTER TABLE `users_sessions` CHANGE `users_session_ip` `users_session_ip` CHAR(32) NULL DEFAULT '-1'");
			
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_update_content'].' <b>"users_sessions"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_update_content'].' <b>"users_sessions"</b>';
		}
		
	break;
	
	case 2:
		
		/**
		 * update settings
		 */
		
		$mysql -> query( "DELETE FROM settings WHERE `setting_setting` = 'sessions_cookies'");
		
		$mysql -> query( "INSERT INTO `settings` (`setting_setting`, `setting_title`, `setting_info`, `setting_group`, `setting_position`, `setting_type`, `setting_value`, `setting_value_default`, `setting_value_type`, `setting_extra`, `setting_subgroup_open`) VALUES
		('sessions_ip', 'U&#380;ywaj adresu IP do identyfikacji sesji.', '', 34, 20, 'yes-no', '1', '1', '', '', ''),
		('users_list_type', 'Typ listy u&#380;ytkownik&oacute;w', 'Wybierz spos&oacute;b, w jaki wy&#347;wietlani maj&#261; by&#263; u&#380;ytkownicy na li&#347;cie.', 30, 15, 'list', '0', '0', '', '0=Tabela\r\n1=Bloki', ''),
		('subscriptions_turn', 'Zezw&oacute;l na obserwowanie for i temat&oacute;w', 'Czy chceasz by u&#380;ytkownicy mogli obserowa&#263; fora i tematy?', 36, 80, 'yes-no', '1', '1', '', '', ''),
		('message_big_cut', 'Maksymalna wysoko&#347;&#263; tre&#347;ci wiadomo&#347;ci', 'Wpisz w pikselach maksymaln&#261; wysoko&#347;&#263; wiadomo&#347;ci, lub 0, aby wy&#322;&#261;czy&#263;.', 36, 90, 'text-input', '0', '0', 'integer', '', ''),
		('message_small_cut', 'Maksymalna wysoko&#347;&#263; tre&#347;ci wiadomo&#347;ci w wynikach szuka&#324;', 'Wpisz w pikselach maksymaln&#261; wysoko&#347;&#263; wiadomo&#347;ci wy&#347;wietlanych w wynikach szuka&#324;, lub 0 aby wy&#322;&#261;czy&#263;.', 36, 110, 'text-input', '130', '130', 'integer', '', '')");
		
		$mysql -> update( array( 'settings_group_settings' => '8'), 'settings_groups', "`settings_group_id` = '34'");	
		$mysql -> update( array( 'settings_group_settings' => '10'), 'settings_groups', "`settings_group_id` = '36'");	
		
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_update_content'].' <b>"settings"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_update_content'].' <b>"settings"</b>';
		}
		
	break;
		
	case 3:
		
		/**
		 * update settings group
		 */
		
		$mysql -> update( array( 'settings_group_settings' => '8'), 'settings_groups', "`settings_group_id` = '30'");	
		
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_update_content'].' <b>"settings_groups"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_update_content'].' <b>"settings_groups"</b>';
		}
		
	break;
	
	case 4:
		
		/**
		 * update version
		 */
		
		$mysql -> insert( array( 'version_id' => 9, 'version_short' => '1.0', 'version_time' => time()), 'version_history');
		
		/**
		 * set message
		 */
		
		$page = 'done';
		
	break;
	
}

?>
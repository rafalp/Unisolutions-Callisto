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
|	1.0 BETA 1 (1) -> 1.0 BETA 2 (2) 
|	by Rafał Pitoń
|
#===========================================================================
*/

switch ( $step){
	
	case 0:
		
		/**
		 * at start, we will create new table
		 */
		
		$mysql -> query("CREATE TABLE `calendar_events` (
		  `calendar_event_id` int(11) NOT NULL auto_increment,
		  `calendar_event_date` char(10) NOT NULL,
		  `calendar_event_repeat` int(4) NOT NULL default '0',
		  `calendar_event_name` char(250) NOT NULL,
		  `calendar_event_info` text NOT NULL,
		  `calendar_event_add_time` int(11) NOT NULL default '0',
		  `calendar_event_user` int(11) NOT NULL default '-1',
		  `calendar_event_username` char(250) NOT NULL,
		  PRIMARY KEY  (`calendar_event_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0");
		
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_create'].' <b>"calendar_events"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_create'].' <b>"calendar_events"</b>';
		}
		
	break;
	
	case 1:
		
		/**
		 * create new settings cats
		 */
		
		$mysql -> query("INSERT INTO `settings_groups` (`settings_group_id`, `settings_group_title`, `settings_group_info`, `settings_group_key`, `settings_group_settings`, `settings_group_hidden`) VALUES 
						(24, 'Kalendarz', 'Tutaj znajduj&#261; si&#281; ustawienia forumowego kalendarza', 'cal_conf', 10, 0),
						(25, 'Reklamy', 'Tutaj znajduj&#261; si&#281; ustawienia kontroluj&#261;ce wbudowany system reklam.', 'ads_conf', 10, 0)");
				
		$mysql -> update( array( 'setting_value' => '13'), 'settings_groups', "`settings_group_id` = '17'");
		
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_update_content'].' <b>"settings_groups"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_update_content'].' <b>"settings_groups"</b>';
		}
		
	break;
			
	case 2:
		
		/**
		 * insert new settings
		 */
		
		$mysql -> query("INSERT INTO `settings` (`setting_setting`, `setting_title`, `setting_info`, `setting_group`, `setting_position`, `setting_type`, `setting_value`, `setting_value_default`, `setting_value_type`, `setting_extra`, `setting_subgroup_open`) VALUES 
						('ads_in_foot_content', 'Tre&#347;&#263; reklamy', '', 25, 40, 'text-box', '', '', '', 'html', 'Reklamy poni&#380;ej tre&#347;ci forum'),
						('ads_in_foot_guests_only', 'Tylko dla go&#347;ci', 'Czy chcesz, aby reklama by&#322;a widoczna tylko dla go&#347;ci?', 25, 50, 'yes-no', '0', '0', '', '', ''),
						('ads_in_foot_mainpage_only', 'Tylko na stronie g&#322;&oacute;wnej', 'Czy chcesz, aby reklama by&#322;a obecna tylko na stronie g&#322;&oacute;wnej?', 25, 60, 'yes-no', '0', '0', '', '', ''),
						('ads_in_top_content', 'Tre&#347;&#263; reklamy', '', 25, 10, 'text-box', '', '', '', 'html', 'Reklamy nad tre&#347;ci&#261; forum'),
						('ads_in_top_guests_only', 'Tylko dla go&#347;ci', 'Czy chcesz, aby reklama by&#322;a widoczna tylko dla go&#347;ci?', 25, 20, 'yes-no', '0', '0', '', '', ''),
						('ads_in_top_mainpage_only', 'Tylko na stronie g&#322;&oacute;wnej', 'Czy chcesz, aby reklama by&#322;a obecna tylko na stronie g&#322;&oacute;wnej?', 25, 30, 'yes-no', '0', '0', '', '', ''),
						('ads_in_topics_author_name', 'Nazwa autora', 'Podaj nazw&#281; autora wiadomo&#347;ci reklamowej', 25, 80, 'text-input', 'UniReklama', 'UniReklama', '', '', ''),
						('ads_in_topics_content', 'Tre&#347;&#263; reklamy', '', 25, 70, 'text-box', '', '', '', 'html', 'Reklamy w tematach'),
						('ads_in_topics_display', 'Wybierz, gdzie ma by&#263; wy&#347;wietlana reklama', 'Wybierz, gdzie w temacie maj&#261; znajdowa&#263; si&#281; posty reklamowe.', 25, 80, 'list', '1', '0', 'integer', '0=Pierwszy post na stronie\r\n1=Ostatni post na stronie\r\n2=Pierwszy i ostatni post na stronie', ''),
						('ads_in_topics_guests_only', 'Tylko dla go&#347;ci', 'Czy chcesz, aby reklama by&#322;a widoczna tylko dla go&#347;ci?', 25, 90, 'yes-no', '0', '0', '', '', ''),
						('birthdays_hide_empty', 'Ukrywaj urodziny, gdy nikt ich aktualnie nie obchodzi?', '', 24, 20, 'yes-no', '0', '0', '', '', ''),
						('birthdays_show', 'Pokazuj urodziny na stronie g&#322;&oacute;wnej', '', 24, 10, 'yes-no', '1', '1', '', '', 'Urodziny'),
						('calendar_max_jump', 'Zasi&#281;g kalendarza', 'Podaj w latach od aktualnego roku czas, do kt&oacute;rego ma si&#281;ga&#263; kalendarz', 24, 90, 'text-input', '5', '5', 'integer', '', ''),
						('calendar_monday', 'Zaczynaj tydzie&#324; od poniedzia&#322;ku', 'Czy chcesz, aby pierwszym dniem tygodnia by&#322; poniedzia&#322;ek, a nie niedziela?', 24, 100, 'yes-no', '1', '1', '', '', ''),
						('calendar_start', 'Pocz&#261;tkek kalendarza', 'Wpisz rok, od kt&oacute;rego ma si&#281; zaczyna&#263; kalendarz', 24, 80, 'text-input', '2007', '2007', 'integer', '', ''),
						('calendar_to_guests', 'Wymagaj zalogowania', 'Czy chcesz, aby tylko zalogowani u&#380;ytkownicy mogli korzysta&#263; z kalendarza?', 24, 70, 'yes-no', '0', '0', '', '', ''),
						('calendar_turn', 'W&#322;&#261;cz kalendarz', '', 24, 60, 'yes-no', '1', '1', '', '', 'Kalendarz'),
						('events_hide_empty', 'Ukrywaj wydarzenia, gdy ich nie ma?', '', 24, 40, 'yes-no', '0', '0', '', '', ''),
						('events_show', 'Wy&#347;wietlaj wydarzenia z kalendarza na stronie g&#322;&oacute;wnej', '', 24, 30, 'yes-no', '1', '1', '', '', 'Wydarzenia'),
						('events_time_limit', 'Wy&#347;wietlaj nadchodz&#261;ce wydarzenia', 'Je&#347;li chcesz aby forum mo&#380;e wy&#347;wietla&#263; r&oacute;wnie&#380; wydarzenia, kt&oacute;re dopiero nadejd&#261;, podaj liczb&#281; dni, kt&oacute;ra maksymalnie ma od nich dzieli&#263;. Je&#347;li chcesz wy&#322;&#261;czy&#263; t&#261; funkcj&#281;, wpisz 0.', 24, 50, 'text-input', '5', '5', 'integer', '', ''),
						('forum_last_topics_show', 'Liczba ostatnich post&oacute;w', 'Wpisz liczb&#281; ostatnich post&oacute;w do wy&#347;wietlenia na stronie g&#322;&oacute;wnej forum, lub 0 aby wy&#322;&#261;czy&#263;.', 17, 20, 'text-input', '5', '5', 'integer', '', '')");
		
		
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
		 * update groups table structure
		 */
		
		$mysql -> update( array( 'users_group_info' => ''), 'users_groups');
		$mysql -> query( "ALTER TABLE `call_users_groups` CHANGE `users_group_info` `users_group_image` CHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
		$mysql -> query( "ALTER TABLE `call_users_groups` ADD `users_group_can_edit_calendar` TINYINT NOT NULL DEFAULT '0' AFTER `users_group_can_moderate`");
				
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_update_structure'].' <b>"users_groups"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_update_structure'].' <b>"users_groups"</b>';
		}
		
	break;
	
	case 4:
		
		/**
		 * update groups table content
		 */
		
		$mysql -> update( array( 'users_group_can_edit_calendar' => '1'), 'users_groups', "`users_group_id` = '1'");
		
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_update_content'].' <b>"users_groups"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_update_content'].' <b>"users_groups"</b>';
		}
		
	break;
	
	case 5:
		
		/**
		 * update version
		 */
		
		$mysql -> insert( array( 'version_id' => 2, 'version_short' => '1.0 BETA 2', 'version_time' => time()), 'version_history');
		
		/**
		 * set message
		 */
		
		$page = 'done';
		
	break;
	
}

?>
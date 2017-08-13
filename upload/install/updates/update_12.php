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
		 * NEW TABLE: shouts
		 */
		
		$mysql -> query("CREATE TABLE `shouts` (
		  `shout_id` int(11) NOT NULL auto_increment,
		  `shout_author` int(11) NOT NULL default '-1',
		  `shout_author_name` char(250) NOT NULL,
		  `shout_time` int(11) NOT NULL default '0',
		  `shout_message` text NOT NULL,
		  PRIMARY KEY  (`shout_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0");
		
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_create'].' <b>"shouts"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_create'].' <b>"shouts"</b>';
		}
		
	break;
	
	case 1:
	
		/**
		 * NEW TABLE: topics_votes
		 */
		
		$mysql -> query("CREATE TABLE `topics_votes` (
		  `topic_vote_id` int(11) NOT NULL auto_increment,
		  `topic_id` int(11) NOT NULL,
		  `topic_vote` int(11) NOT NULL,
		  `topic_vote_user` int(11) NOT NULL,
		  PRIMARY KEY  (`topic_vote_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0");
		
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_create'].' <b>"topics_votes"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_create'].' <b>"topics_votes"</b>';
		}
		
	break;
		
	case 2:
	
		/**
		 * ALTER TABLE: topics
		 */
		
		$mysql -> query( "ALTER TABLE `topics` ADD `topic_score` int(11) NOT NULL default '0' AFTER `topic_tags`");
		$mysql -> query( "ALTER TABLE `topics` ADD `topic_votes` int(11) NOT NULL default '0' AFTER `topic_score`");
		
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_update_structure'].' <b>"topics"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_update_structure'].' <b>"topics"</b>';
		}
		
	break;
	
	case 3:
	
		/**
		 * ALTER TABLE: users
		 */
		
		$mysql -> query( "ALTER TABLE `users` DROP `user_log_num`");
		
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_update_structure'].' <b>"users"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_update_structure'].' <b>"users"</b>';
		}
		
	break;
	
	case 4:
	
		/**
		 * ALTER TABLE: users_groups
		 */
		
		$mysql -> query( "ALTER TABLE `users_groups` ADD `users_group_msg_title` char(250) collate utf8_unicode_ci NOT NULL AFTER `users_group_message`");
		$mysql -> query( "ALTER TABLE `users_groups` ADD `users_group_shoutbox_access` int(11) NOT NULL default '0' AFTER `users_group_can_edit_calendar`");
		
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_update_structure'].' <b>"users_groups"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_update_structure'].' <b>"users_groups"</b>';
		}
		
	break;
		
	case 5:
	
		/**
		 * OVERWRITE TABLE: settings_groups
		 */
		
		$mysql -> query( "DELETE FROM `settings_groups`");
		$mysql -> query( "INSERT INTO `settings_groups` (`settings_group_id`, `settings_group_title`, `settings_group_info`, `settings_group_key`, `settings_group_settings`, `settings_group_hidden`) VALUES 
							(1, 'Ustawienia g&#322;&oacute;wne', 'Te ustawienia zmieniaj&#261; najbardziej podstawowe elementy forum.', 'gen_conf', 14, 0),
							(2, 'Ustawienia Cookies', 'Tutaj znajduj&#261; si&#281; ustawienia cookies.', 'cookies_conf', 4, 0),
							(4, 'Data i czas', 'Tutaj znajduj&#261; si&#281; ustawienia umo&#380;liwiaj&#261;ce zmian&#281; formatu daty, domy&#347;lnej strefy czasowej forum i czasu letniego.', 'time_conf', 7, 0),
							(5, 'W&#322;&#261;cz/wy&#322;&#261;cz forum', 'Za pomoc&#261; tych ustawie&#324; mo&#380;esz na czas administracji wy&#322;&#261;czy&#263; forum i zostawi&#263; wiadomo&#347;&#263; u&#380;ytkownikom.', 'shut_conf', 2, 0),
							(6, 'Ukryte', 'Rozmaite warto&#347;ci kt&oacute;re s&#261; kontrolowane przez system, i nie powinny by&#263; zmieniane', 'hidden_config', 8, 1),
							(8, 'Ochrona przed spam-botami', 'Tutaj mo&#380;esz zmieni&#263; ustawienia zabezpiecze&#324; chroni&#261;cych przed spam-botami.', 'spam_conf', 4, 0),
							(11, 'Ustawienia e-maili', 'Tutaj mo&#380;esz zmieni&#263; ustawienia e-maili.', 'emails_conf', 7, 0),
							(12, 'U&#380;ytkownicy: Konta', 'Tutaj znajduj&#261; si&#281; ustawienia u&#380;ytkownik&oacute;w.', 'users_conf', 8, 0),
							(14, 'Regulaminy forum', 'W tej skecji znajduj&#261; si&#281; ustawienia kontroluj&#261;ce regulaminy na forum.', 'rules_conf', 5, 0),
							(15, 'U&#380;ytkownicy: Prywatne wiadomo&#347;ci', 'Tutaj znajduj&#261; si&#281; ustawienia wp&#322;ywaj&#261;ce na prywatne wiadomo&#347;ci.', 'pm_conf', 1, 0),
							(17, 'Tematy', 'W tym dziale znajduj&#261; si&#281; ustawienia dotycz&#261;ce temat&oacute;w i post&oacute;w.', 'topics_conf', 15, 0),
							(18, 'Boty wyszukiwarek', 'Tutaj znajduj&#261; si&#281; ustawienia, kontroluj&#261;ce zachowanie forum wobec bot&oacute;w wyszukwiarek', 'spiders_conf', 4, 0),
							(19, 'Czyszczenie log&oacute;w wydarze&#324;', 'Tutaj znajduj&#261; si&#281; ustawienia reguluj&#261;ce czyszczenie log&oacute;w wydarze&#324;.', 'logs_conf', 2, 0),
							(20, 'Kosz', 'Tutaj znajduj&#261; si&#281; ustawienia kosza.', 'trash_conf', 2, 0),
							(21, 'Nowo&#347;ci', 'Tutaj znajduj&#261; si&#281; ustawienia kontroluj&#261;ce dzia&#322;anie systemu nowo&#347;ci.', 'news_conf', 3, 0),
							(22, 'System ostrze&#380;e&#324;', 'Tutaj znajduj&#261; si&#281; ustawienia kontroluj&#261;ce prac&#281; systemu ostrze&#380;e&#324;.', 'warns_conf', 5, 0),
							(23, 'Ustawienia szukania', 'Tutaj znajduj&#261; si&#281; ustawienia konfiguruj&#261;ce dzia&#322;anie funkcji \"szukaj\".', 'search_conf', 1, 0),
							(24, 'Kalendarz', 'Tutaj znajduj&#261; si&#281; ustawienia forumowego kalendarza', 'cal_conf', 5, 0),
							(25, 'Reklamy', 'Tutaj znajduj&#261; si&#281; ustawienia kontroluj&#261;ce wbudowany system reklam.', 'ads_conf', 10, 0),
							(26, 'RSS', 'W tej kategorii znajduj&#261; si&#281; ustawienia RSS.', 'rss_conf', 3, 0),
							(27, 'U&#380;ytkownicy: Reputacje', 'Tutaj znajduj&#261; si&#281; ustawienia kontroluj&#261;ce dzia&#322;anie systemu reputacji.', 'reps_conf', 5, 0),
							(28, 'Kalendarz: Urodziny', '', 'conf_calendar', 2, 0),
							(29, 'Kalendarz: Wydarzenia', '', 'conf_events', 3, 0),
							(30, 'U&#380;ytkownicy: Lista u&#380;ytkownik&oacute;w', '', 'conf_userslist', 8, 0),
							(31, 'U&#380;ytkownicy: Avatary', '', 'conf_users_avs', 8, 0),
							(32, 'Prywatno&#347;&#263;', '', 'conf_guests', 5, 0),
							(33, 'U&#380;ytkownicy: Rejestracje i Logowania', 'Tutaj znajduj&#261; si&#281; ustawienia kontroluj&#261;ce nowe rejestracje.', 'conf_regs', 9, 0),
							(34, 'U&#380;ytkownicy: Sesje', '', 'conf_sessions', 8, 0),
							(35, 'Fora', '', 'conf_boards', 7, 0),
							(36, 'Wiadomo&#347;ci i pisanie', '', 'conf_posts', 13, 0),
							(37, 'Tagi', '', 'tags_conf', 3, 0),
							(38, 'Shoutbox', '', 'shout_conf', 7, 0)");
		
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_update_content'].' <b>"settings_groups"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_update_content'].' <b>"settings_groups"</b>';
		}
		
	break;
	
	case 6:
	
		/**
		 * add new settings
		 */
		
		$mysql -> query( "INSERT INTO `settings` (`setting_setting`, `setting_title`, `setting_info`, `setting_group`, `setting_position`, `setting_type`, `setting_value`, `setting_value_default`, `setting_value_type`, `setting_extra`, `setting_subgroup_open`) VALUES 
							('guest_limit_length', 'D&#322;ugo&#347;&#263; ograniczenia wy&#347;wielania temat&oacute;w', 'Wpisz, ile minut ma trwa&#263; ograniczenie.', 17, 120, 'text-input', '60', '60', 'integer', '', ''),
							('guest_limit_reads', 'Ogranicz wy&#347;wietlenia temat&oacute;w dla go&#347;ci', 'Je&#347;li chcesz, forum mo&#380;e ograniczy&#263; go&#347;ciom mo&#380;liwo&#347;&#263; czytania temat&oacute;w. Po przekroczeniu limitu, u&#380;ytkownik proszony jest o zalogowanie si&#281;.', 17, 100, 'text-input', '15', '15', 'integer', '', ''),
							('msg_title_max_length', 'Maksymalna d&#322;ugo&#347;&#263; tytu&#322;u wiadomo&#347;ci', 'Nie wi&#281;ksza ni&#380; 90. Dzia&#322;a na tytu&#322;y temat&oacute;w, wiadomo&#347;ci prywatnych, i nazw wydarze&#324; w kalendarzu.', 36, 130, 'text-input', '60', '60', 'integer', '', ''),
							('preview_posts_height', 'Maksymalna wysoko&#347;&#263; tre&#347;ci wiadomo&#347;ci w podgl&#261;dzie', 'Wpisz w pikselach maksymaln&#261; wysoko&#347;&#263; wiadomo&#347;ci wy&#347;wietlanych w podgl&#261;dzie tematu, lub 0 aby wy&#322;&#261;czy&#263;.', 36, 120, 'text-input', '150', '150', 'integer', '', ''),
							('preview_posts_num', 'Liczba wiadomo&#347;ci wy&#347;wietlanych w podgl&#261;dzie', 'Je&#347;li chcesz, forum mo&#380;e wy&#347;wietla&#263; podgl&#261;d post&oacute;w w temacie, podczas pisania odpowiedzi. Je&#347;li nie chcesz aby podgl&#261;d by&#322; dost&#281;pny, wpisz 0.', 36, 110, 'text-input', '5', '5', 'integer', '', ''),
							('shoutbox_allow_bbcodes', 'Zezw&oacute;l na bbcody w shoutach', '', 38, 40, 'yes-no', '0', '0', '', '', ''),
							('shoutbox_allow_emos', 'Zezw&oacute;l na emotikony w shoutach', '', 38, 50, 'yes-no', '1', '1', '', '', ''),
							('shoutbox_frequency', 'Cz&#281;stotliwo&#347;&#263; od&#347;wie&#380;e&#324; shoutboxa', '', 38, 30, 'list', '0', '0', 'integer', '0=Ma&#322;a\r\n1=Standardowa\r\n2=Wysoka', ''),
							('shoutbox_height', 'Wysoko&#347;&#263; shoutboxa', 'Podaj w pikselach wysoko&#347;&#263; shoutboxa', 38, 70, 'text-input', '150', '150', 'integer', '', ''),
							('shoutbox_position', 'Pozycja shoutboxa', '', 38, 20, 'list', '0', '0', 'integer', '0=Po g&#322;&oacute;wnej li&#347;cie for\r\n1=Po bloku ze statystyk&#261; forum\r\n2=Przed g&#322;&oacute;wn&#261; list&#261; for', ''),
							('shoutbox_shouts_num', 'Liczba wy&#347;wietlanych wiadomo&#347;ci', '', 38, 60, 'text-input', '12', '12', 'integer', '', ''),
							('shoutbox_turn', 'W&#322;&#261;cz shoutbox', '', 38, 10, 'yes-no', '1', '1', '', '', ''),
							('tags_cloud_enable', 'W&#322;&#261;cz chmurk&#281; tag&oacute;w', 'Czy chcesz aby chmurka tag&oacute;w by&#322;a dost&#281;pna', 37, 30, 'yes-no', '1', '1', '', '', ''),
							('topic_info_length', 'Maksymalna d&#322;ugo&#347;&#263; opisu tematu', 'Nie wi&#281;ksza ni&#380; 90. Wpisz 0 aby wy&#322;&#261;czy&#263; mo&#380;liwo&#347;&#263; ustawiania opis&oacute;w temat&oacute;w lub -1 aby ca&#322;kowicie wy&#322;&#261;czy&#263; t&#261; funkcj&#281;.', 17, 160, 'text-input', '90', '90', 'integer', '', ''),
							('topics_paging_draw', 'Dok&#322;adno&#347;&#263; skoku do strony', 'Gdy temat posiada wiecej ni&#380; jedn&#261; stron&#281;, forum mo&#380;e wy&#347;wietla&#263; linki skoku do pierwszych i ostatnich stron na tematu na li&#347;cie temat&oacute;w. Aby wy&#322;&#261;czy&#263;, wpisz 0.', 17, 120, 'text-input', '6', '5', 'integer', '', ''),
							('topics_rantings_onlist', 'Wy&#347;wietlaj oceny na listach temat&oacute;w', '', 17, 150, 'yes-no', '1', '1', '', '', ''),
							('topics_rantings_scale', 'Spos&oacute;b wy&#347;wietlania oceny tematu', '', 17, 140, 'list', '0', '0', 'integer', '0=Gwiazdki (0-5)\r\n1=Procenty', ''),
							('topics_rantings_turn', 'Zezw&oacute;l u&#380;ytkownikom na ocenianie temat&oacute;w', '', 17, 130, 'yes-no', '1', '1', '', '', ''),
							('user_login_length_max', 'Maksymalna d&#322;ugo&#347;&#263; loginu', 'Wpisz maksymaln&#261; dopuszczaln&#261; d&#322;ugo&#347;&#263; loginu u&#380;ytkownika (nie wi&#281;ksz&#261; ni&#380; 50).', 33, 36, 'text-input', '10', '10', 'integer', '', ''),
							('user_login_length_min', 'Minimalna d&#322;ugo&#347;&#263; loginu', '', 33, 35, 'text-input', '1', '1', 'integer', '', '')");
		
		
		/**
		 * update settings
		 */
		
		$mysql -> update( array( 'setting_group' => 37, 'setting_position' => 10), 'settings', "`setting_setting` = 'forum_allow_tags'");
		$mysql -> update( array( 'setting_group' => 37, 'setting_position' => 20), 'settings', "`setting_setting` = 'forum_topics_draw_tags'");
			
		/**
		 * ok
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_update_content'].' <b>"settings"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_update_content'].' <b>"settings"</b>';
		}
		
	break;
		
	case 7:
		
		/**
		 * update version
		 */
		
		$mysql -> insert( array( 'version_id' => 12, 'version_short' => '1.1.0', 'version_time' => time()), 'version_history');
		
		/**
		 * set message
		 */
		
		$page = 'done';
		
	break;
	
}

?>
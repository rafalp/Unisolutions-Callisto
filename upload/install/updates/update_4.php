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
		 * insert new settings group
		 */
		
		$mysql -> query("INSERT INTO `settings_groups` (`settings_group_id`, `settings_group_title`, `settings_group_info`, `settings_group_key`, `settings_group_settings`, `settings_group_hidden`) VALUES 
				(27, 'Reputacje', 'Tutaj znajduj&#261; si&#281; ustawienia kontroluj&#261;ce dzia&#322;anie systemu reputacji.', 'reps_conf', 5, 0)");
				
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_update_content'].' <b>"settings_groups"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_update_content'].' <b>"settings_groups"</b>';
		}
		
	break;
	
	case 1:
		
		/**
		 * update settings groups
		 */
		
		$mysql -> update( array( 'settings_group_settings' => '14'), 'settings_groups', "`settings_group_id` = '1'");
		$mysql -> update( array( 'settings_group_settings' => '14'), 'settings_groups', "`settings_group_id` = '17'");
		
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
						('board_path_atend', 'Wy&#347;wietlaj &#347;cie&#380;k&#281; pod tre&#347;ci&#261;', 'Czy chcesz, aby &#347;cie&#380;ka by&#322;a wy&#347;wietlana r&oacute;wnie&#380; pod tre&#347;ci&#261;?', 1, 130, 'yes-no', '0', '0', '', '', ''),
						('board_path_type', 'Typ &#347;cie&#380;ki na forum', 'Wybierz typ &#347;cie&#380;ki na forum', 1, 120, 'list', '1', '0', '', '0=Linia\r\n1=Schody', ''),
						('forum_allow_tags', 'W&#322;&#261;cz tagowanie temat&oacute;w', 'Czy chcesz aby tematy mog&#322;y by&#263; oznaczana tagami przez ich autor&oacute;w i zesp&oacute;l forum?', 17, 41, 'yes-no', '1', '1', '', '', ''),
						('reputation_bonus_days', 'Regularny bonus za uczestnictwo', 'Wpisz, co ile dni u&#380;ytkownicy maj&#261; otrzymywa&#263; regularny bonus za uczestnictwo? Wpisz 0 aby wy&#322;&#261;czy&#263;', 27, 30, 'text-input', '60', '60', 'integer', '', 'Bonusy do reputacji'),
						('reputation_bonus_size', 'Rozmiar regularnego bonusu do uczestnictwa', 'Mo&#380;e by&#263; r&oacute;wnie&#380; negatywny.', 27, 40, 'text-input', '5', '5', 'integer', '', ''),
						('reputation_day_limit', 'Dzienny limit ocen', 'Podaj, ile razy w ci&#261;gu jednego dnia u&#380;ytkownik mo&#380;e oceni&#263; innego.', 27, 20, 'text-input', '5', '5', 'integer', '', ''),
						('reputation_posting_bonus', 'Bonus za posty', 'Je&#347;li chcesz aby u&#380;ytkownicy byli premiowani za ka&#380;dy post, kt&oacute;ry powi&#281;ksza licznik, wpisz tutaj liczb&#281; punkt&oacute;w za jeden post. Je&#347;li nie chcesz premiowa&#263; w ten spos&oacute;b, wpisz 0.', 27, 50, 'text-input', '2', '2', 'integer', '', ''),
						('reputation_turn', 'W&#322;&#261;cz system reputacji', '', 27, 10, 'yes-no', '0', '0', '', '', '')");
				
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
		 * create reps tab
		 */
		
		$mysql -> query( "CREATE TABLE `reputation_scale` (
			  `reputation_scale_id` int(11) NOT NULL auto_increment,
			  `reputation_scale_name` char(250) NOT NULL,
			  `reputation_scale_points` int(11) NOT NULL default '0',
			  PRIMARY KEY  (`reputation_scale_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0");
			
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_create'].' <b>"reputation_scale"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_create'].' <b>"reputation_scale"</b>';
		}
		
	break;
	
	case 4:
		
		/**
		 * fill reps tab
		 */
		
		$mysql -> query( "INSERT INTO `reputation_scale` (`reputation_scale_id`, `reputation_scale_name`, `reputation_scale_points`) VALUES 
			(1, 'Szary u&#380;ytkownik', 0),
			(2, 'Czasem b&#322;&#261;dzi', -10),
			(3, 'Wie co dobre', 10),
			(4, 'Mo&#380;e si&#281; poprawi', -200),
			(5, 'Porz&#261;dny u&#380;ytkownik', 200),
			(6, 'Bardzo z&#322;y u&#380;ytkownik.', -3000),
			(7, 'Klejnot w koronie', 3000)");
			
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_update_content'].' <b>"reputation_scale"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_update_content'].' <b>"reputation_scale"</b>';
		}
		
	break;
	
	case 5:
		
		/**
		 * create reps_votes tab
		 */
		
		$mysql -> query( "CREATE TABLE `reputation_votes` (
			  `reputation_vote_id` int(11) NOT NULL auto_increment,
			  `reputation_vote_user` int(11) NOT NULL default '-1',
			  `reputation_vote_post` int(11) NOT NULL default '0',
			  `reputation_vote_author` int(11) NOT NULL default '-1',
			  `reputation_vote_author_name` char(250) NOT NULL,
			  `reputation_vote_time` int(11) NOT NULL default '0',
			  `reputation_vote_power` int(11) NOT NULL default '0',
			  `reputation_vote_reason` text NOT NULL,
			  PRIMARY KEY  (`reputation_vote_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0");
			
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_create'].' <b>"reputation_votes"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_create'].' <b>"reputation_votes"</b>';
		}
		
	break;
		
	case 6:
		
		/**
		 * create prefixes tab
		 */
		
		$mysql -> query( "CREATE TABLE `topics_prefixes` (
			  `topic_prefix_id` int(11) NOT NULL auto_increment,
			  `topic_prefix_pos` int(11) NOT NULL default '0',
			  `topic_prefix_name` char(250) NOT NULL,
			  `topic_prefix_html` char(250) NOT NULL,
			  `topic_prefix_forums` text NOT NULL,
			  PRIMARY KEY  (`topic_prefix_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0");
			
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_create'].' <b>"topics_prefixes"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_create'].' <b>"topics_prefixes"</b>';
		}
		
	break;
		
	case 7:
		
		/**
		 * update users tab
		 */
		
		$mysql -> query( "ALTER TABLE `users` ADD `user_rep` INT NOT NULL DEFAULT '0' AFTER `user_warns`");
			
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_update_structure'].' <b>"users"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_update_structure'].' <b>"users"</b>';
		}
		
	break;
	
	case 8:
		
		/**
		 * update topics tab
		 */
		
		$mysql -> query( "ALTER TABLE `topics` ADD `topic_prefix` INT NOT NULL DEFAULT '0' AFTER `topic_name`");
		$mysql -> query( "ALTER TABLE `topics` ADD `topic_tags` TEXT NOT NULL AFTER `topic_info`");
			
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_update_structure'].' <b>"topics"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_update_structure'].' <b>"topics"</b>';
		}
		
	break;
	
	case 9:
		
		/**
		 * update version
		 */
		
		$mysql -> insert( array( 'version_id' => 4, 'version_short' => '1.0 BETA 4', 'version_time' => time()), 'version_history');
		
		/**
		 * set message
		 */
		
		$page = 'done';
		
	break;
	
}

?>
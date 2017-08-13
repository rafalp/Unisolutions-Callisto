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
|	1.0 BETA 2 (2) -> 1.0 BETA 3 (3) 
|	by Rafał Pitoń
|
#===========================================================================
*/

switch ( $step){
	
	case 0:
		
		/**
		 * at start, we will create new table
		 */
		
		$mysql -> query("CREATE TABLE `bbtags` (
		  `tag_id` int(11) NOT NULL auto_increment,
		  `tag_name` char(250) NOT NULL,
		  `tag_info` text NOT NULL,
		  `tag_tag` text NOT NULL,
		  `tag_option` int(11) NOT NULL default '0',
		  `tag_replace` text NOT NULL,
		  `tag_draw` tinyint(4) NOT NULL default '0',
		  PRIMARY KEY  (`tag_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0");
		
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_create'].' <b>"bbtags"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_create'].' <b>"bbtags"</b>';
		}
		
	break;
	
	case 1:
		
		/**
		 * second table
		 */
		
		$mysql -> query("CREATE TABLE `profile_fields` (
		  `profile_field_id` int(11) NOT NULL auto_increment,
		  `profile_field_pos` int(11) NOT NULL default '0',
		  `profile_field_name` char(250) NOT NULL,
		  `profile_field_info` text NOT NULL,
		  `profile_field_type` int(11) NOT NULL default '0',
		  `profile_field_length` tinyint(4) NOT NULL default '0',
		  `profile_field_options` text NOT NULL,
		  `profile_field_onregister` tinyint(4) NOT NULL default '0',
		  `profile_field_onlist` tinyint(4) NOT NULL default '0',
		  `profile_field_inposts` tinyint(4) NOT NULL default '0',
		  `profile_field_require` tinyint(4) NOT NULL default '0',
		  `profile_field_private` tinyint(4) NOT NULL default '0',
		  `profile_field_byteam` tinyint(4) NOT NULL default '0',
		  `profile_field_display` text NOT NULL,
		  PRIMARY KEY  (`profile_field_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0");
		
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_create'].' <b>"profile_fields"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_create'].' <b>"profile_fields"</b>';
		}
		
	break;
	
	case 2:
		
		/**
		 * third table
		 */
		
		$mysql -> query("CREATE TABLE `profile_fields_data` (
		  `profile_fields_id` int(11) NOT NULL auto_increment,
		  `profile_fields_user` int(11) NOT NULL default '-1',
		  PRIMARY KEY  (`profile_fields_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0");
		
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_create'].' <b>"profile_fields_data"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_create'].' <b>"profile_fields_data"</b>';
		}
		
	break;
	
	case 3:
		
		/**
		 * create new settings cat
		 */
		
		$mysql -> query("INSERT INTO `settings_groups` (`settings_group_id`, `settings_group_title`, `settings_group_info`, `settings_group_key`, `settings_group_settings`, `settings_group_hidden`) VALUES 
						(26, 'RSS', 'W tej kategorii znajduj&#261; si&#281; ustawienia RSS.', 'rss_conf', 3, 0)");
						
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
		 * insert new settings
		 */
		
		$mysql -> query("INSERT INTO `settings` (`setting_setting`, `setting_title`, `setting_info`, `setting_group`, `setting_position`, `setting_type`, `setting_value`, `setting_value_default`, `setting_value_type`, `setting_extra`, `setting_subgroup_open`) VALUES 
						('rss_channel_title', 'Nazwa kana&#322;u', '', 26, 20, 'text-input', 'Nowo&#347;ci na Forum', '', '', '', ''),
						('rss_timeline', 'Liczba temat&oacute;w', 'Wpisz licz&#281; temat&oacute;w do wy&#347;wietlania w RSS.', 26, 30, 'text-input', '5', '5', 'integer', '', ''),
						('rss_turn', 'W&#322;&#261;cz RSS', 'Czy chcesz, aby forum prowadzi&#322;o w&#322;&#261;sny kana&#322; RSS?', 26, 10, 'yes-no', '1', '0', '', '', '')");
				
		/**
		 * draw result
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_update_content'].' <b>"settings"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_update_content'].' <b>"settings"</b>';
		}
		
	break;
	
	case 5:
		
		/**
		 * update version
		 */
		
		$mysql -> insert( array( 'version_id' => 3, 'version_short' => '1.0 BETA 3', 'version_time' => time()), 'version_history');
		
		/**
		 * set message
		 */
		
		$page = 'done';
		
	break;
	
}

?>
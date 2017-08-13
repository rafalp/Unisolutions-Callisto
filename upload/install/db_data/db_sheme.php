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
|	Database Structure
|	by Rafał Pitoń
|
#===========================================================================
*/

$install_query[] = "CREATE TABLE `admins_loging_log` (
  `admins_login_log_id` int(11) NOT NULL auto_increment,
  `admins_login_log_time` int(11) NOT NULL default '0',
  `admins_login_log_user_id` int(11) NOT NULL default '-1',
  `admins_login_log_user_ip` char(32) NOT NULL default '0',
  `admins_login_log_success` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`admins_login_log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `admins_logs` (
  `admins_log_id` int(11) NOT NULL auto_increment,
  `admins_log_user_id` int(11) NOT NULL default '0',
  `admins_log_user_ip` char(32) NOT NULL default '0',
  `admins_log_time` int(11) NOT NULL default '0',
  `admins_log_act` char(200) default NULL,
  `admins_log_do` char(200) default NULL,
  `admins_log_details` text,
  PRIMARY KEY  (`admins_log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `admins_sessions` (
  `admin_session_id` char(32) collate utf8_unicode_ci NOT NULL,
  `admin_session_ip` char(32) NOT NULL default '-1',
  `admin_session_key` char(32) collate utf8_unicode_ci NOT NULL,
  `admin_session_user_id` int(11) NOT NULL,
  `admin_session_open_time` int(11) NOT NULL,
  `admin_session_last_time` int(11) NOT NULL,
  `admin_session_agent` char(200) collate utf8_unicode_ci NOT NULL,
  `admin_session_section` char(60) collate utf8_unicode_ci default NULL,
  `admin_session_action` char(60) collate utf8_unicode_ci default NULL,
  `admin_session_target` char(60) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`admin_session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$install_query[] = "CREATE TABLE `attachments` (
  `attachment_id` int(11) NOT NULL auto_increment,
  `attachment_post` int(11) NOT NULL default '0',
  `attachment_writing_session` char(32) NOT NULL default '0',
  `attachment_time` int(11) NOT NULL default '0',
  `attachment_name` char(200) NOT NULL,
  `attachment_file` char(200) NOT NULL,
  `attachment_type` int(11) NOT NULL default '0',
  `attachment_downloads` int(11) NOT NULL default '0',
  `attachment_size` int(11) NOT NULL default '0',
  PRIMARY KEY  (`attachment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `attachments_types` (
  `attachments_type_id` int(11) NOT NULL auto_increment,
  `attachments_type_extension` char(130) NOT NULL,
  `attachments_type_mime` char(130) NOT NULL,
  `attachments_type_image` char(200) NOT NULL,
  PRIMARY KEY  (`attachments_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `badwords` (
  `badword_id` int(11) NOT NULL auto_increment,
  `badword_find` char(250) NOT NULL,
  `badword_replace` char(250) NOT NULL,
  PRIMARY KEY  (`badword_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `banfilters` (
  `banfilter_id` int(11) NOT NULL auto_increment,
  `banfilter_type` int(11) NOT NULL default '0',
  `banfilter_filter` char(40) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`banfilter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `bbtags` (
  `tag_id` int(11) NOT NULL auto_increment,
  `tag_name` char(250) NOT NULL,
  `tag_info` text NOT NULL,
  `tag_tag` text NOT NULL,
  `tag_option` int(11) NOT NULL default '0',
  `tag_replace` text NOT NULL,
  `tag_draw` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `calendar_events` (
  `calendar_event_id` int(11) NOT NULL auto_increment,
  `calendar_event_date` char(10) collate utf8_unicode_ci NOT NULL,
  `calendar_event_repeat` int(4) NOT NULL default '0',
  `calendar_event_name` varchar(450) collate utf8_unicode_ci NOT NULL,
  `calendar_event_info` text collate utf8_unicode_ci NOT NULL,
  `calendar_event_add_time` int(11) NOT NULL default '0',
  `calendar_event_user` int(11) NOT NULL default '-1',
  `calendar_event_username` char(250) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`calendar_event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `captcha_generations` (
  `captcha_id` int(11) NOT NULL auto_increment,
  `captcha_code` char(6) collate utf8_unicode_ci default NULL,
  `captcha_num1` int(11) NOT NULL default '0',
  `captcha_num2` int(11) NOT NULL default '0',
  `captcha_type` int(11) NOT NULL default '0',
  `captcha_result` int(11) NOT NULL default '0',
  `captcha_generated` int(11) NOT NULL default '0',
  PRIMARY KEY  (`captcha_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `emoticons` (
  `emoticon_id` int(11) NOT NULL auto_increment,
  `emoticon_type` char(15) collate utf8_unicode_ci default NULL,
  `emoticon_image` char(30) collate utf8_unicode_ci default NULL,
  `emoticon_name` char(100) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`emoticon_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `forums` (
  `forum_id` int(11) NOT NULL auto_increment,
  `forum_parent` int(11) NOT NULL default '0',
  `forum_pos` int(11) NOT NULL default '0',
  `forum_type` int(11) NOT NULL default '1',
  `forum_name` char(170) NOT NULL,
  `forum_image` char(250) default NULL,
  `forum_info` text,
  `forum_guidelines` text NOT NULL,
  `forum_guidelines_url` char(250) NOT NULL,
  `forum_url` char(250) default NULL,
  `forum_redirects` int(11) NOT NULL default '0',
  `forum_count_redirects` tinyint(4) NOT NULL default '1',
  `forum_threads` int(11) NOT NULL default '0',
  `forum_posts` int(11) NOT NULL default '0',
  `forum_last_topic` int(11) NOT NULL default '0',
  `forum_last_topic_time` int(11) NOT NULL default '0',
  `forum_last_poster_id` int(11) NOT NULL default '-1',
  `forum_last_poster_name` char(250) NOT NULL,
  `forum_increase_counter` tinyint(4) NOT NULL default '1',
  `forum_allow_bbcode` tinyint(4) NOT NULL default '1',
  `forum_allow_surveys` tinyint(4) NOT NULL default '1',
  `forum_allow_quick_reply` tinyint(4) NOT NULL default '1',
  `forum_force_ordering` int(11) NOT NULL default '0',
  `forum_ordering_way` tinyint(4) NOT NULL default '0',
  `forum_pruning` tinyint(4) NOT NULL default '0',
  `forum_prune_days` int(11) NOT NULL default '0',
  `forum_last_prune` int(11) NOT NULL default '0',
  `forum_locked` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`forum_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `forums_access` (
  `forums_access_id` int(11) NOT NULL auto_increment,
  `forums_acess_perms_id` int(11) NOT NULL default '0',
  `forums_acess_forum_id` int(11) NOT NULL default '0',
  `forums_access_show_forum` tinyint(4) NOT NULL default '0',
  `forums_access_show_topics` tinyint(4) NOT NULL default '0',
  `forums_access_reply_topics` tinyint(4) NOT NULL default '0',
  `forums_access_start_topics` tinyint(4) NOT NULL default '0',
  `forums_access_attachments_upload` tinyint(4) NOT NULL default '0',
  `forums_access_attachments_download` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`forums_access_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `forums_reads` (
  `forums_read_id` int(11) NOT NULL auto_increment,
  `forums_read_forum` int(11) NOT NULL,
  `forums_read_time` int(11) NOT NULL default '0',
  `forums_read_user` int(11) NOT NULL,
  PRIMARY KEY  (`forums_read_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `help_files` (
  `help_file_id` int(11) NOT NULL auto_increment,
  `help_file_pos` int(11) NOT NULL default '0',
  `help_file_name` char(200) NOT NULL,
  `help_file_info` text NOT NULL,
  `help_file_text` text NOT NULL,
  PRIMARY KEY  (`help_file_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `languages` (
  `lang_id` char(30) collate utf8_unicode_ci NOT NULL,
  `lang_name` char(40) collate utf8_unicode_ci default NULL,
  `lang_users` int(11) NOT NULL default '0',
  PRIMARY KEY  (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$install_query[] = "CREATE TABLE `mails` (
  `mail_id` int(11) NOT NULL auto_increment,
  `mail_actual_user` int(11) NOT NULL default '0',
  `mail_end_at_user` int(11) NOT NULL,
  `mail_last_time` mediumint(9) NOT NULL default '0',
  `mail_done` tinyint(4) NOT NULL default '0',
  `mail_toall` tinyint(4) NOT NULL default '0',
  `mail_subject` char(200) NOT NULL,
  `mail_text` text NOT NULL,
  PRIMARY KEY  (`mail_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `mails_logs` (
  `mails_log_id` int(11) NOT NULL auto_increment,
  `mails_log_time` int(11) NOT NULL default '0',
  `mails_log_sender` int(11) NOT NULL default '0',
  `mails_log_receiver` int(11) NOT NULL default '0',
  `mails_log_ip` char(32) NOT NULL default '0',
  `mails_log_subject` char(250) default NULL,
  PRIMARY KEY  (`mails_log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `moderators` (
  `moderator_id` int(11) NOT NULL auto_increment,
  `moderator_forum_id` int(11) NOT NULL default '0',
  `moderator_user_id` int(11) NOT NULL default '0',
  `moderator_group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`moderator_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `moderators_logs` (
  `moderators_log_id` int(11) NOT NULL auto_increment,
  `moderators_log_user_id` int(11) NOT NULL default '0',
  `moderators_log_user_ip` char(32) NOT NULL default '0',
  `moderators_log_time` int(11) NOT NULL default '0',
  `moderators_log_forum` int(11) NOT NULL default '0',
  `moderators_log_topic` int(11) NOT NULL default '0',
  `moderators_log_post` int(11) NOT NULL default '0',
  `moderators_log_target_user` int(11) NOT NULL default '0',
  `moderators_log_details` text,
  PRIMARY KEY  (`moderators_log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL auto_increment,
  `post_topic` int(11) NOT NULL default '0',
  `post_author` int(11) NOT NULL default '-1',
  `post_author_name` char(230) default NULL,
  `post_time` int(11) NOT NULL default '0',
  `post_text` text,
  `post_has_attachments` tinyint(4) NOT NULL default '0',
  `post_ip` char(32) NOT NULL default '0',
  `post_user_agent` char(250) default NULL,
  `post_reported` tinyint(4) NOT NULL default '0',
  `post_edits` int(11) NOT NULL default '0',
  `post_last_edit` int(11) NOT NULL default '0',
  `post_last_editor` int(11) NOT NULL default '-1',
  `post_last_editor_name` char(200) NOT NULL,
  `post_edit_message` char(200) default NULL,
  PRIMARY KEY  (`post_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `posts_reports` (
  `post_report_id` int(11) NOT NULL auto_increment,
  `post_report_post` int(11) NOT NULL default '0',
  `post_report_user` int(11) NOT NULL default '-1',
  `post_report_user_name` char(200) NOT NULL,
  `post_report_time` int(11) NOT NULL default '0',
  `post_report_text` text NOT NULL,
  PRIMARY KEY  (`post_report_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `profile_fields` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0
";

$install_query[] = "CREATE TABLE `profile_fields_data` (
  `profile_fields_id` int(11) NOT NULL auto_increment,
  `profile_fields_user` int(11) NOT NULL default '-1',
  PRIMARY KEY  (`profile_fields_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0
";

$install_query[] = "CREATE TABLE `ranks` (
  `rank_id` int(11) NOT NULL auto_increment,
  `rank_name` char(70) NOT NULL,
  `rank_posts_required` int(11) NOT NULL default '0',
  `rank_image` char(250) default NULL,
  `rank_stars` int(11) NOT NULL default '1',
  PRIMARY KEY  (`rank_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `reputation_scale` (
  `reputation_scale_id` int(11) NOT NULL auto_increment,
  `reputation_scale_name` char(250) NOT NULL,
  `reputation_scale_points` int(11) NOT NULL default '0',
  PRIMARY KEY  (`reputation_scale_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `reputation_votes` (
  `reputation_vote_id` int(11) NOT NULL auto_increment,
  `reputation_vote_user` int(11) NOT NULL default '-1',
  `reputation_vote_post` int(11) NOT NULL default '0',
  `reputation_vote_author` int(11) NOT NULL default '-1',
  `reputation_vote_author_name` char(250) NOT NULL,
  `reputation_vote_time` int(11) NOT NULL default '0',
  `reputation_vote_power` int(11) NOT NULL default '0',
  `reputation_vote_reason` text NOT NULL,
  PRIMARY KEY  (`reputation_vote_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `searchs_results` (
  `search_id` int(11) NOT NULL auto_increment,
  `search_session` char(32) NOT NULL,
  `search_result` text,
  `search_phrase` char(250) NOT NULL,
  `search_time` int(11) NOT NULL default '0',
  `search_result_type` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`search_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `settings` (
  `setting_setting` char(250) collate utf8_unicode_ci NOT NULL,
  `setting_title` char(130) collate utf8_unicode_ci NOT NULL,
  `setting_info` text collate utf8_unicode_ci,
  `setting_group` int(11) NOT NULL,
  `setting_position` int(11) NOT NULL default '1',
  `setting_type` varchar(30) collate utf8_unicode_ci NOT NULL default 'info',
  `setting_value` text collate utf8_unicode_ci,
  `setting_value_default` text collate utf8_unicode_ci,
  `setting_value_type` char(30) collate utf8_unicode_ci default NULL,
  `setting_extra` text collate utf8_unicode_ci,
  `setting_subgroup_open` char(90) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`setting_setting`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$install_query[] = "CREATE TABLE `settings_groups` (
  `settings_group_id` int(11) NOT NULL auto_increment,
  `settings_group_title` char(60) collate utf8_unicode_ci NOT NULL,
  `settings_group_info` text collate utf8_unicode_ci,
  `settings_group_key` char(30) collate utf8_unicode_ci default NULL,
  `settings_group_settings` int(11) NOT NULL default '0',
  `settings_group_hidden` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`settings_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `shouts` (
  `shout_id` int(11) NOT NULL auto_increment,
  `shout_author` int(11) NOT NULL default '-1',
  `shout_author_name` char(250) NOT NULL,
  `shout_time` int(11) NOT NULL default '0',
  `shout_message` text NOT NULL,
  PRIMARY KEY  (`shout_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `spiders_logs` (
  `spider_log_id` int(11) NOT NULL auto_increment,
  `spider_log_name` char(200) NOT NULL,
  `spider_log_ip` char(32) NOT NULL default '0',
  `spider_log_time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`spider_log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `styles` (
  `style_id` int(11) NOT NULL auto_increment,
  `style_name` char(200) collate utf8_unicode_ci default NULL,
  `style_path` char(200) collate utf8_unicode_ci default NULL,
  `style_author` char(200) collate utf8_unicode_ci default NULL,
  `style_www` char(250) collate utf8_unicode_ci default NULL,
  `style_users` int(11) NOT NULL default '0',
  PRIMARY KEY  (`style_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `subscriptions_forums` (
  `subscription_forum_id` int(11) NOT NULL auto_increment,
  `subscription_forum` int(11) NOT NULL default '0',
  `subscription_forum_user` int(11) NOT NULL default '0',
  `subscription_forum_time` int(11) NOT NULL default '0',
  `subscription_forum_topics` int(11) NOT NULL default '0',
  `subscription_forum_posts` int(11) NOT NULL default '0',
  PRIMARY KEY  (`subscription_forum_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `subscriptions_topics` (
  `subscription_topic_id` int(11) NOT NULL auto_increment,
  `subscription_topic` int(11) NOT NULL default '0',
  `subscription_topic_user` int(11) NOT NULL default '0',
  `subscription_topic_time` int(11) NOT NULL default '0',
  `subscription_topic_posts` int(11) NOT NULL default '0',
  PRIMARY KEY  (`subscription_topic_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `surveys_ops` (
  `survey_op_id` int(11) NOT NULL auto_increment,
  `survey_op_topic` int(11) NOT NULL default '0',
  `survey_op_name` char(250) NOT NULL,
  `survey_op_votes` int(11) NOT NULL default '0',
  PRIMARY KEY  (`survey_op_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `surveys_votes` (
  `surveys_vote_id` int(11) NOT NULL auto_increment,
  `surveys_vote_topic` int(11) NOT NULL default '0',
  `surveys_vote_option` int(11) NOT NULL default '0',
  `surveys_vote_user` int(11) NOT NULL default '-1',
  PRIMARY KEY  (`surveys_vote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `tasks` (
  `task_id` int(11) NOT NULL auto_increment,
  `task_title` char(250) collate utf8_unicode_ci NOT NULL,
  `task_info` text collate utf8_unicode_ci NOT NULL,
  `task_file` char(200) collate utf8_unicode_ci default NULL,
  `task_active` tinyint(4) NOT NULL default '0',
  `task_collect_logs` tinyint(4) NOT NULL default '1',
  `task_next_run` int(11) NOT NULL,
  `task_minute` int(11) NOT NULL default '0',
  `task_hour` int(11) NOT NULL default '0',
  `task_day` int(11) NOT NULL default '0',
  PRIMARY KEY  (`task_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `tasks_logs` (
  `tasks_log_id` int(11) NOT NULL auto_increment,
  `tasks_log_task` int(11) default NULL,
  `tasks_log_time` int(11) default NULL,
  `tasks_log_ip` char(32) NOT NULL default '0',
  PRIMARY KEY  (`tasks_log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `topics` (
  `topic_id` int(11) NOT NULL auto_increment,
  `topic_forum_id` int(11) NOT NULL default '0',
  `topic_type` int(11) NOT NULL default '0',
  `topic_name` varchar(450) NOT NULL,
  `topic_prefix` int(11) NOT NULL default '0',
  `topic_info` varchar(450) default NULL,
  `topic_tags` text default NULL,
  `topic_score` int(11) NOT NULL default '0',
  `topic_votes` int(11) NOT NULL default '0',
  `topic_attachments` tinyint(4) NOT NULL default '0',
  `topic_start_time` int(11) NOT NULL default '0',
  `topic_start_user` int(11) NOT NULL default '-1',
  `topic_start_user_name` char(250) default NULL,
  `topic_last_time` int(11) NOT NULL default '0',
  `topic_last_user` int(11) NOT NULL default '-1',
  `topic_last_user_name` char(250) default NULL,
  `topic_posts_num` int(11) NOT NULL default '0',
  `topic_views_num` int(11) NOT NULL default '0',
  `topic_first_post_id` int(11) NOT NULL default '0',
  `topic_last_post_id` int(11) default '0',
  `topic_survey` tinyint(4) NOT NULL default '0',
  `topic_survey_text` char(220) default NULL,
  `topic_survey_votes` int(11) NOT NULL default '0',
  `topic_closed` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`topic_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `topics_prefixes` (
  `topic_prefix_id` int(11) NOT NULL auto_increment,
  `topic_prefix_pos` int(11) NOT NULL default '0',
  `topic_prefix_name` char(250) NOT NULL,
  `topic_prefix_html` char(250) NOT NULL,
  `topic_prefix_forums` text NOT NULL,
  PRIMARY KEY  (`topic_prefix_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `topics_reads` (
  `topic_read_id` int(11) NOT NULL auto_increment,
  `topic_read_forum` int(11) NOT NULL default '0',
  `topic_read_topic` int(11) NOT NULL default '0',
  `topic_read_user` int(11) NOT NULL default '0',
  `topic_read_time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`topic_read_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `topics_votes` (
  `topic_vote_id` int(11) NOT NULL auto_increment,
  `topic_id` int(11) NOT NULL,
  `topic_vote` int(11) NOT NULL,
  `topic_vote_user` int(11) NOT NULL,
  PRIMARY KEY  (`topic_vote_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `users` (
  `user_id` int(11) NOT NULL auto_increment,
  `user_login` char(30) collate utf8_unicode_ci default NULL,
  `user_name` char(250) collate utf8_unicode_ci default NULL,
  `user_birth_date` char(10) collate utf8_unicode_ci default NULL,
  `user_gender` int(11) NOT NULL default '0',
  `user_password` char(32) collate utf8_unicode_ci default NULL,
  `user_mail` char(250) collate utf8_unicode_ci default NULL,
  `user_show_mail` tinyint(4) NOT NULL default '1',
  `user_want_mail` tinyint(4) NOT NULL default '1',
  `user_auto_subscribe` tinyint(4) NOT NULL default '0',
  `user_jabber_id` char(200) collate utf8_unicode_ci default NULL,
  `user_web` char(250) collate utf8_unicode_ci default NULL,
  `user_localisation` char(250) collate utf8_unicode_ci default NULL,
  `user_interests` text collate utf8_unicode_ci,
  `user_notepad` text collate utf8_unicode_ci,
  `user_signature` text collate utf8_unicode_ci,
  `user_show_sigs` tinyint(4) NOT NULL default '1',
  `user_custom_title` char(200) collate utf8_unicode_ci default NULL,
  `user_posts_num` int(11) NOT NULL default '0',
  `user_last_post_time` int(11) NOT NULL default '0',
  `user_last_search_time` int(11) NOT NULL default '0',
  `user_notify_pm` tinyint(4) NOT NULL default '1',
  `user_pm_num` int(11) NOT NULL default '0',
  `user_pm_new_num` int(11) NOT NULL default '0',
  `user_avatar_image` char(250) collate utf8_unicode_ci default NULL,
  `user_avatar_type` int(11) NOT NULL default '0',
  `user_avatar_width` int(11) default NULL,
  `user_avatar_height` int(11) default NULL,
  `user_show_avatars` tinyint(4) NOT NULL default '1',
  `user_regdate` int(11) NOT NULL default '0',
  `user_time_zone` int(11) NOT NULL default '0',
  `user_dst` tinyint(4) NOT NULL default '0',
  `user_active` tinyint(4) NOT NULL default '0',
  `user_activation_code` char(32) collate utf8_unicode_ci default NULL,
  `user_locked` tinyint(4) NOT NULL default '0',
  `user_warns` int(11) NOT NULL default '0',
  `user_rep` int(11) NOT NULL default '0',
  `user_last_login` int(11) NOT NULL default '0',
  `user_logins_tries` text collate utf8_unicode_ci,
  `user_logins_tries_num` int(11) NOT NULL default '0',
  `user_permissions` int(11) NOT NULL default '0',
  `user_main_group` int(11) NOT NULL default '0',
  `user_other_groups` text collate utf8_unicode_ci,
  `user_lang` char(5) collate utf8_unicode_ci default NULL,
  `user_style` int(11) NOT NULL default '1',
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `users_autologin` (
  `users_autologin_key` char(32) collate utf8_unicode_ci NOT NULL,
  `users_autologin_user` int(11) NOT NULL default '-1',
  `users_autologin_last_use` int(11) NOT NULL default '0',
  `users_autologin_hidden` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`users_autologin_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$install_query[] = "CREATE TABLE `users_groups` (
  `users_group_id` int(11) NOT NULL auto_increment,
  `users_group_name` char(250) collate utf8_unicode_ci default NULL,
  `users_group_prefix` char(170) collate utf8_unicode_ci default NULL,
  `users_group_suffix` char(170) collate utf8_unicode_ci default NULL,
  `users_group_title` char(200) collate utf8_unicode_ci default NULL,
  `users_group_image` char(200) collate utf8_unicode_ci default NULL,
  `users_group_message` text collate utf8_unicode_ci,
  `users_group_msg_title` char(250) collate utf8_unicode_ci NOT NULL,
  `users_group_hidden` tinyint(11) NOT NULL default '0',
  `users_group_permissions` int(11) default NULL,
  `users_group_system` tinyint(4) NOT NULL default '0',
  `users_group_can_use_acp` tinyint(4) NOT NULL default '0',
  `users_group_can_see_closed_page` tinyint(4) NOT NULL default '0',
  `users_group_can_see_users_profiles` tinyint(4) NOT NULL default '0',
  `users_group_can_use_pm` tinyint(4) NOT NULL default '0',
  `users_group_pm_limit` int(11) NOT NULL default '20',
  `users_group_can_email_members` tinyint(4) NOT NULL default '0',
  `users_group_can_moderate` tinyint(4) NOT NULL default '0',
  `users_group_can_edit_calendar` tinyint(4) NOT NULL default '0',
  `users_group_shoutbox_access` int(11) NOT NULL default '0',
  `users_group_edit_time_limit` int(11) NOT NULL default '0',
  `users_group_draw_edit_legend` tinyint(4) NOT NULL default '1',
  `users_group_delete_own_topics` tinyint(4) NOT NULL default '0',
  `users_group_change_own_topics` tinyint(4) NOT NULL default '0',
  `users_group_close_own_topics` tinyint(4) NOT NULL default '0',
  `users_group_delete_own_posts` tinyint(4) NOT NULL default '0',
  `users_group_edit_own_posts` tinyint(4) NOT NULL default '0',
  `users_group_start_surveys` tinyint(4) NOT NULL default '0',
  `users_group_vote_surveys` tinyint(4) NOT NULL default '0',
  `users_group_avoid_flood` tinyint(4) NOT NULL default '0',
  `users_group_avoid_badwords` tinyint(4) NOT NULL default '0',
  `users_group_avoid_closed_topics` tinyint(4) NOT NULL default '0',
  `users_group_promote_to` int(11) NOT NULL default '0',
  `users_group_promote_at` int(11) NOT NULL default '0',
  `users_group_see_hidden` tinyint(4) NOT NULL default '0',
  `users_group_search` tinyint(4) NOT NULL default '0',
  `users_group_search_limit` int(11) NOT NULL default '0',
  `users_group_uploads_quota` int(11) NOT NULL default '0',
  `users_group_uploads_used` int(11) NOT NULL default '0',
  `users_group_uploads_limit` int(11) NOT NULL default '0',
  PRIMARY KEY  (`users_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `users_messages` (
  `users_message_id` int(11) NOT NULL auto_increment,
  `users_message_author` int(11) NOT NULL default '0',
  `users_message_author_name` char(250) collate utf8_unicode_ci default NULL,
  `users_message_receiver` int(11) NOT NULL default '0',
  `users_message_send_time` int(11) NOT NULL default '0',
  `users_message_receive_time` int(11) NOT NULL default '0',
  `users_message_readed` tinyint(4) NOT NULL default '0',
  `users_message_subject` varchar(450) collate utf8_unicode_ci default NULL,
  `users_message_text` text collate utf8_unicode_ci,
  PRIMARY KEY  (`users_message_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `users_perms` (
  `users_perm_id` int(11) NOT NULL auto_increment,
  `users_perm_name` char(170) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`users_perm_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `users_sessions` (
  `users_session_id` char(32) collate utf8_unicode_ci NOT NULL,
  `users_session_ip` char(32) default '-1',
  `users_session_user_id` int(11) NOT NULL default '-1',
  `users_session_hidden` tinyint(4) NOT NULL default '0',
  `users_session_open_time` int(11) NOT NULL default '0',
  `users_session_last_time` int(11) NOT NULL default '0',
  `users_session_location_act` char(200) collate utf8_unicode_ci NOT NULL,
  `users_session_location_forum` int(11) NOT NULL default '0',
  `users_session_location_topic` int(11) NOT NULL default '0',
  `users_session_location_post` int(11) NOT NULL default '0',
  `users_session_location_user` int(11) NOT NULL default '0',
  `users_session_bot` tinyint(4) NOT NULL default '0',
  `users_session_bot_name` char(200) collate utf8_unicode_ci default NULL,
  `users_session_agent` char(230) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`users_session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$install_query[] = "CREATE TABLE `users_warnings` (
  `user_warning_id` int(11) NOT NULL auto_increment,
  `user_warning_user` int(11) NOT NULL default '-1',
  `user_warning_mod` int(11) NOT NULL default '-1',
  `user_warning_mod_name` char(250) NOT NULL,
  `user_warning_direction` tinyint(4) NOT NULL default '0',
  `user_warning_time` int(11) NOT NULL default '0',
  `user_warning_text` text,
  PRIMARY KEY  (`user_warning_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0";

$install_query[] = "CREATE TABLE `version_history` (
  `version_id` int(11) NOT NULL,
  `version_short` char(12) collate utf8_unicode_ci default NULL,
  `version_time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`version_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

?>

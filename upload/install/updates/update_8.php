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
		 * update settings
		 */
		
		$mysql -> query( "DELETE FROM settings_groups");
		
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
						(17, 'Tematy', 'W tym dziale znajduj&#261; si&#281; ustawienia dotycz&#261;ce temat&oacute;w i post&oacute;w.', 'topics_conf', 10, 0),
						(18, 'Boty wyszukiwarek', 'Tutaj znajduj&#261; si&#281; ustawienia, kontroluj&#261;ce zachowanie forum wobec bot&oacute;w wyszukwiarek', 'spiders_conf', 4, 0),
						(19, 'Czyszczenie log&oacute;w wydarze&#324;', 'Tutaj znajduj&#261; si&#281; ustawienia reguluj&#261;ce czyszczenie log&oacute;w wydarze&#324;.', 'logs_conf', 2, 0),
						(20, 'Kosz', 'Tutaj znajduj&#261; si&#281; ustawienia kosza.', 'trash_conf', 2, 0),
						(21, 'Nowo&#347;ci', 'Tutaj znajduj&#261; si&#281; ustawienia kontroluj&#261;ce dzia&#322;anie systemu nowo&#347;ci.', 'news_conf', 3, 0),
						(22, 'System ostrze&#380;e&#324;', 'Tutaj znajduj&#261; si&#281; ustawienia kontroluj&#261;ce prac&#281; systemu ostrze&#380;e&#324;.', 'warns_conf', 5, 0),
						(23, 'Ustawienia szukania', 'Tutaj znajduj&#261; si&#281; ustawienia konfiguruj&#261;ce dzia&#322;anie funkcji \"szukaj\".', 'search_conf', 2, 0),
						(24, 'Kalendarz', 'Tutaj znajduj&#261; si&#281; ustawienia forumowego kalendarza', 'cal_conf', 5, 0),
						(25, 'Reklamy', 'Tutaj znajduj&#261; si&#281; ustawienia kontroluj&#261;ce wbudowany system reklam.', 'ads_conf', 10, 0),
						(26, 'RSS', 'W tej kategorii znajduj&#261; si&#281; ustawienia RSS.', 'rss_conf', 3, 0),
						(27, 'U&#380;ytkownicy: Reputacje', 'Tutaj znajduj&#261; si&#281; ustawienia kontroluj&#261;ce dzia&#322;anie systemu reputacji.', 'reps_conf', 5, 0),
						(28, 'Kalendarz: Urodziny', '', 'conf_calendar', 2, 0),
						(29, 'Kalendarz: Wydarzenia', '', 'conf_events', 3, 0),
						(30, 'U&#380;ytkownicy: Lista u&#380;ytkownik&oacute;w', '', 'conf_userslist', 7, 0),
						(31, 'U&#380;ytkownicy: Avatary', '', 'conf_users_avs', 8, 0),
						(32, 'Prywatno&#347;&#263;', '', 'conf_guests', 5, 0),
						(33, 'U&#380;ytkownicy: Rejestracje i Logowania', 'Tutaj znajduj&#261; si&#281; ustawienia kontroluj&#261;ce nowe rejestracje.', 'conf_regs', 7, 0),
						(34, 'U&#380;ytkownicy: Sesje', '', 'conf_sessions', 8, 0),
						(35, 'Fora', '', 'conf_boards', 7, 0),
						(36, 'Wiadomo&#347;ci i pisanie', '', 'conf_posts', 7, 0)");
			
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
		 * select actual settings
		 */
		
		$settings = array();
		
		$settings_query = $mysql -> query( 'SELECT `setting_setting`, `setting_value` FROM settings');
			
		while( $result = mysql_fetch_array($settings_query, MYSQL_ASSOC)) {
			$settings[$result['setting_setting']] = stripslashes($result['setting_value']);
		}
		
		/**
		 * ok, delete settings
		 */
		
		$mysql -> query( "DELETE FROM settings");
		
		/**
		 * add new settings
		 */
		
		$mysql -> query( "INSERT INTO `settings` (`setting_setting`, `setting_title`, `setting_info`, `setting_group`, `setting_position`, `setting_type`, `setting_value`, `setting_value_default`, `setting_value_type`, `setting_extra`, `setting_subgroup_open`) VALUES 
			('admins_notepad', 'Notatnik administrator&oacute;w', 'Zawarto&#347;&#263; notatnika administrator&oacute;w', 6, 10, 'info', '', '', '', '', ''),
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
			('birthdays_hide_empty', 'Ukrywaj urodziny, gdy nikt ich aktualnie nie obchodzi?', '', 28, 20, 'yes-no', '0', '0', '', '', ''),
			('birthdays_show', 'Pokazuj urodziny na stronie g&#322;&oacute;wnej', '', 28, 10, 'yes-no', '1', '1', '', '', ''),
			('board_address', 'Adres forum', 'Dok&#322;adny adres internetowy do forum (z \"http://\", zako&#324;czony \"/\").\r\nnp. http://domain.com/', 1, 20, 'text-input', '', '', '', '', ''),
			('board_allow_infotab', 'W&#322;&#261;cz kart&#281; informacyjn&#261;', 'Je&#347;li w&#322;&#261;czysz t&#261; opcj&#281;, zalogowani u&#380;ytkownicy bed&#261; mieli dost&#281;p do w&#322;asnych kart informacyjnych.', 12, 30, 'yes-no', '1', '1', '', '', ''),
			('board_closed_message', 'Wiadomo&#347;&#263; do wy&#347;wietlenia', 'Je&#347;li chcesz, mo&#380;esz wy&#347;wietla&#263; u&#380;ytkownikom jak&#261;&#347; wiadomo&#347;&#263;.', 5, 20, 'text-editor', 'Forum chwilowo nieczynne.\r\nZapraszamy p&oacute;&#378;niej.', '', '', '', ''),
			('board_copyright', 'Dodatkowa informacja w stopce', 'Np. informacja o prawach do forum.', 1, 21, 'text-input', '', '', '', '', ''),
			('board_debug_level', 'Poziom aktywno&#347;ci debugera', 'Umo&#380;liwia okre&#347;lenie, jak dok&#322;adne maj&#261; by&#263; informacje o procesie dzia&#322;ania forum.', 1, 60, 'list', '0', '0', 'integer', '0=wy&#322;&#261;czony\r\n1=czas generowania strony\r\n2=czas generowania i liczba zapyta&#324; do SQL\r\n3=czas generowania, liczba i tabele z zapytaniami do SQL i przebiegiem generowania strony', 'Wykrywanie b&#322;&#281;d&oacute;w'),
			('board_description', 'Kr&oacute;tki opis forum', '', 1, 11, 'text-input', '', '', '', '', ''),
			('board_draw_version', 'Wy&#347;wietlaj numer wersji skryptu', 'Czy chcesz, aby w stopce by&#322; wy&#347;wietlany numer wersji skryptu?', 32, 50, 'yes-no', '1', '', '', '', ''),
			('board_favicon', 'W&#322;asna ikona strony', 'Podaj &#347;cie&#380;k&#281; do w&#322;asnej ikony (wzgl&#281;dem g&#322;&oacute;wnego folderu skryptu; bez otwierajacego \"/\" na pocz&#261;tku), b&#261;d&#378; pozostaw pole puste, je&#347;li chcesz, aby system u&#380;ywa&#322; domy&#347;lnej.', 1, 70, 'text-input', '', NULL, NULL, NULL, 'Wy&#347;wietlanie'),
			('board_flood_limit', 'Op&oacute;&#378;nienie postowania', 'Podaj liczb&#281; sekund, kt&oacute;ra musi up&#322;yn&#261;&#263; od wys&#322;ania posta, aby sta&#322;o si&#281; mo&#380;liwe wys&#322;anie kolejnego. Je&#347;li chcesz znie&#347;&#263; t&#261; blokad&#281;, podaj 0', 36, 60, 'text-input', '60', '0', 'integer', '', ''),
			('board_logo', 'Logo forum', 'Wybierz typ logo forum', 1, 110, 'list', '0', '0', 'integer', '0=Domy&#347;lne stylu\r\n1=Tekstowe (tylko nazwa forum)\r\n2=Tekstowe (nazwa i opis forum)', ''),
			('board_name', 'Nazwa forum', '', 1, 10, 'text-input', 'Unisolutions Callisto', 'Unisolutions Callisto', '', '', 'Nazwa, adres i stopka'),
			('board_offline', 'Forum wy&#322;&#261;czone', '', 5, 10, 'yes-no', '0', '0', '', '', ''),
			('board_path_atend', 'Wy&#347;wietlaj &#347;cie&#380;k&#281; pod tre&#347;ci&#261;', 'Czy chcesz, aby &#347;cie&#380;ka by&#322;a wy&#347;wietlana r&oacute;wnie&#380; pod tre&#347;ci&#261;?', 1, 130, 'yes-no', '1', '0', '', '', ''),
			('board_path_type', 'Typ &#347;cie&#380;ki na forum', 'Wybierz typ &#347;cie&#380;ki na forum', 1, 120, 'list', '1', '0', '', '0=Linia\r\n1=Schody', ''),
			('board_posts_total', 'Liczba post&oacute;w', '', 6, 10, 'info', '1', '', '', '', ''),
			('board_show_acp_link', 'Wy&#347;wietlaj link do panelu admina', 'Je&#347;li chcesz, mo&#380;esz wy&#322;aczy&#263; wy&#347;wietlanie linka do panelu admina na forum.', 32, 40, 'yes-no', '1', '1', '', '', ''),
			('board_start_date', 'Data wystartowania serwisu', 'timestamp daty instalacji skryptu.', 6, 60, 'info', '0', '', '', '', ''),
			('board_threads_total', 'Liczba temat&oacute;w', '', 6, 10, 'info', '1', '', '', '', ''),
			('board_website_title', 'Nazwa strony', 'Podaj nazw&#281; strony, do kt&oacute;rej nale&#380;y to forum. Zostanie ona u&#380;yta jako tytu&#322; linku do niej w nag&#322;&oacute;wku forum. Je&#347;li go nie podasz, a mimo to podasz adres strony, link nazywa&#263; si&#281; b&#281;dzie \"powr&oacute;t na stron&#281;\".', 1, 30, 'text-input', '', '', '', '', ''),
			('board_website_url', 'Adres strony', 'Podaj adres strony, do kt&oacute;rej nale&#380;y to forum. Po jego podaniu link pojawi si&#281; w nag&#322;&oacute;wku forum.', 1, 40, 'text-input', '', '', '', '', ''),
			('bots_protection_captcha', 'U&#380;ywaj captchy', 'Captcha jest podstawowym zabezpieczeniem przed botami, cz&#281;sto spotykanym w internecie. Stanowi skuteczn&#261; obron&#281; przed prostymi botami, nie ko&#380;ystaj&#261;cymi z OCR.', 8, 10, 'yes-no', '1', '', '', '', 'Captcha'),
			('bots_protection_challenge_response', 'U&#380;ywaj &quot;challenge-response&quot;', 'Zabezpieczenie typu \"challenge-response\" wymaga od u&#380;ytkownika, aby ten rozwi&#261;za&#322; proste dzia&#322;anie matematyczne, np. 2 + 3.', 8, 20, 'yes-no', '1', '', '', '', 'Challenge - response'),
			('bots_protection_challenge_response_type', 'Typ dzia&#322;ania', '', 8, 30, 'list', '0', '0', 'integer', '0=dodawanie\r\n1=odejmowanie\r\n2=mno&#380;enie', ''),
			('bots_protection_humanity_test', 'U&#380;ywaj &quot;testu na cz&#322;owiecze&#324;stwo&quot;', 'Test na cz&#322;owiecze&#324;stwo to bardzo proste w budowie i dzia&#322;aniu zabezpieczene przeciw botom. Jedyne czego wymaga od u&#380;ytkownika, to w pytaniu \"Czy jeste&#347; cz&#322;owiekiem\" ten wybra&#322; odpowied&#378; twierdz&#261;c&#261;. Wi&#281;kszo&#347;&#263; bot&oacute;w nie zmienia domy&#347;lnych ustawie&#324; radiobutton&oacute;w.', 8, 50, 'yes-no', '1', '', '', '', 'Test na cz&#322;owiecze&#324;stwo'),
			('calendar_max_jump', 'Zasi&#281;g kalendarza', 'Podaj w latach od aktualnego roku czas, do kt&oacute;rego ma si&#281;ga&#263; kalendarz', 24, 90, 'text-input', '5', '5', 'integer', '', ''),
			('calendar_monday', 'Zaczynaj tydzie&#324; od poniedzia&#322;ku', 'Czy chcesz, aby pierwszym dniem tygodnia by&#322; poniedzia&#322;ek, a nie niedziela?', 24, 100, 'yes-no', '1', '1', '', '', ''),
			('calendar_start', 'Pocz&#261;tkek kalendarza', 'Wpisz rok, od kt&oacute;rego ma si&#281; zaczyna&#263; kalendarz', 24, 80, 'text-input', '2007', '2007', 'integer', '', ''),
			('calendar_to_guests', 'Wymagaj zalogowania', 'Czy chcesz, aby tylko zalogowani u&#380;ytkownicy mogli korzysta&#263; z kalendarza?', 24, 70, 'yes-no', '1', '0', '', '', ''),
			('calendar_turn', 'W&#322;&#261;cz kalendarz', '', 24, 60, 'yes-no', '1', '1', '', '', ''),
			('cookie_domain', 'Domena Cookies', NULL, 2, 20, 'text-input', '', NULL, NULL, NULL, NULL),
			('cookie_name', 'Prefix nazwy Cookies', 'Umo&#380;liwia wykonanie wielu instalacji systemu na jednym serverze.', 2, 10, 'text-input', 'callisto_', NULL, NULL, NULL, ''),
			('cookie_path', '&#346;cie&#380;ka Cookies', '', 2, 30, 'text-input', '/', NULL, NULL, NULL, NULL),
			('cookie_secure', 'Bezpieczne Cookies', NULL, 2, 40, 'yes-no', '0', '0', NULL, NULL, NULL),
			('default_language', '', NULL, 0, 1, 'info', 'pl', NULL, NULL, NULL, NULL),
			('default_style', '', NULL, 0, 1, 'info', '1', NULL, NULL, NULL, NULL),
			('draw_online_board', 'Wy&#347;wietlaj u&#380;ytkownik&oacute;w przegl&#261;dajacych forum', 'Czy chcesz, aby w podsumowaniu na stronie g&#322;&oacute;wnej wy&#347;wietlana by&#322;a lista aktualnie przegl&#261;daj&#261;cych forum?', 34, 50, 'yes-no', '1', '1', '', '', ''),
			('draw_online_forum', 'Wy&#347;wietlaj u&#380;ytkownik&oacute;w znajduj&#261;cych si&#281; w dziale', 'Czy chcesz, aby podczas przegl&#261;dania listy temat&oacute;w w forum dost&#281;pna by&#322;a lista z przegl&#261;daj&#261;cymi j&#261; u&#380;ytkownikami?', 34, 70, 'yes-no', '1', '1', '', '', ''),
			('draw_online_topic', 'Wy&#347;wietlaj u&#380;ytkownik&oacute;w czytaj&#261;cych temat', 'Czy chcesz, aby podczas czytania tematu wy&#347;wietlana by&#322;a lista innych czytaj&#261;cych go u&#380;ytkownik&oacute;w?', 34, 80, 'yes-no', '1', '1', '', '', ''),
			('email_address', 'Adres email serwisu', '', 11, 10, 'text-input', '', '', '', '', 'Ustawienia g&#322;&oacute;wne'),
			('email_send_method', 'Spos&oacute;b wysy&#322;ania maili', 'Je&#347;li funkcja mail() nie jest dost&#281;pna, u&#380;yj SMTP. Je&#347;li nie wiesz, co wybra&#263;, zapytaj administratora serwera', 11, 20, 'list', '1', '0', 'integer', '0=mail()\r\n1=SMTP', ''),
			('email_smtp_auth', '', NULL, 0, 1, 'info', NULL, NULL, NULL, NULL, NULL),
			('email_smtp_host', 'Host SMTP', 'Domy&#347;lnie \"localhost\"', 11, 30, 'text-input', '', '', '', '', 'Konfiguracja SMTP'),
			('email_smtp_pass', 'Has&#322;o', '', 11, 80, 'text-input', '', '', '', '', ''),
			('email_smtp_port', 'Port SMTP', 'Domy&#347;lnie 25', 11, 40, 'text-input', '25', '25', 'integer', '', ''),
			('email_smtp_timeout', 'Timeout', 'Maksymalny okres czasu (w sekundach), przez kt&oacute;ry system mo&#380;e pr&oacute;bowa&#263; nawi&#261;za&#263; po&#322;&#261;czenie z serwerem SMTP.', 11, 50, 'text-input', '0', '0', 'integer', '', ''),
			('email_smtp_username', 'U&#380;ytkownik', '', 11, 70, 'text-input', '', '', '', '', ''),
			('events_hide_empty', 'Ukrywaj wydarzenia, gdy ich nie ma?', '', 29, 20, 'yes-no', '0', '0', '', '', ''),
			('events_show', 'Wy&#347;wietlaj wydarzenia z kalendarza na stronie g&#322;&oacute;wnej', '', 29, 10, 'yes-no', '1', '1', '', '', 'Wydarzenia'),
			('events_time_limit', 'Wy&#347;wietlaj nadchodz&#261;ce wydarzenia', 'Je&#347;li chcesz aby forum mo&#380;e wy&#347;wietla&#263; r&oacute;wnie&#380; wydarzenia, kt&oacute;re dopiero nadejd&#261;, podaj liczb&#281; dni, kt&oacute;ra maksymalnie ma od nich dzieli&#263;. Je&#347;li chcesz wy&#322;&#261;czy&#263; t&#261; funkcj&#281;, wpisz 0.', 29, 30, 'text-input', '5', '5', 'integer', '', ''),
			('forum_allow_tags', 'W&#322;&#261;cz tagowanie temat&oacute;w', 'Czy chcesz aby tematy mog&#322;y by&#263; oznaczana tagami przez ich autor&oacute;w i zesp&oacute;l forum?', 17, 41, 'yes-no', '1', '1', '', '', ''),
			('forum_draw_forums_jumplist', 'Wy&#347;wietlaj list&#281; for do szybkich przeskok&oacute;w', 'Cze chcesz aby w forach i tematach by&#322;a wy&#347;wietlana lista, umo&#380;liwiajaca szybki przeskok do nast&#281;pnego forum?', 35, 40, 'yes-no', '1', '1', '', '', ''),
			('forum_draw_info', 'Wy&#347;wietlaj opis forum wewn&#261;trz forum', 'Czy chcesz aby opis forum by&#322; wy&#347;wietlany r&oacute;wnie&#380; wewn&#261;trz forum?', 35, 30, 'yes-no', '1', '1', '', '', ''),
			('forum_draw_spacer', 'Oddzielaj og&#322;oszenia od reszty temat&oacute;w belk&#261;?', 'Czy chcesz aby og&#322;oszenia by&#322;y oddzielone od reszty temat&oacute;w specjaln&#261; belk&#261;?', 17, 21, 'yes-no', '1', '1', '', '', ''),
			('forum_hot_topic', 'Oznaczaj tematy jako popularne od', 'Podaj liczb&#281; post&oacute;w kt&oacute;re musz&#261; si&#281; znales&#263; w temacie, aby ten zosta&#322; oznaczony jako popularny, lub wpisz 0, aby wy&#322;&#261;czy&#263;.', 17, 10, 'text-input', '20', '20', 'integer', '', ''),
			('forum_last_topics_show', 'Liczba ostatnich post&oacute;w', 'Wpisz liczb&#281; ostatnich post&oacute;w do wy&#347;wietlenia na stronie g&#322;&oacute;wnej forum, lub 0 aby wy&#322;&#261;czy&#263;.', 17, 20, 'text-input', '5', '5', 'integer', '', ''),
			('forum_post_look', 'Standardowy wygl&#261;d postu', 'Wybierz czy chcesz aby posty by&#322;y wy&#347;wietlane standardowo, w uk&#322;adzie kolumnowym, czy na nowy spos&oacute;b, w uk&#322;adzie rz&#281;dowym.', 36, 20, 'yes-no', '1', '1', '', '', ''),
			('forum_posts_per_page', 'Liczba post&oacute;w na stron&#281;', '', 36, 10, 'text-input', '10', '8', 'integer', '', ''),
			('forum_stick_prefix', 'Prefiks nazwy przyklejonego tematu', '', 17, 30, 'text-input', 'Przyklejony', '', '', '', ''),
			('forum_survey_prefix', 'Prefiks nazwy tematu zawieraj&#261;cego ankiet&#281;', '', 17, 40, 'text-input', 'Ankieta', '', '', '', ''),
			('forum_topics_draw_tags', 'Wy&#347;wietlaj tagi pod tematami', '', 17, 90, 'yes-no', '1', '1', '', '', ''),
			('forum_topics_mark_attachments', 'Oznaczaj na li&#347;cie tamty z za&#322;&#261;cznikami', 'Czy chcesz aby tematy z za&#322;&#261;cznikami by&#322;y na listach oznaczane spinaczami?', 17, 40, 'yes-no', '1', '1', '', '', ''),
			('forum_topics_per_page', 'Liczba temat&oacute;w na stron&#281;', '', 17, 20, 'text-input', '15', '15', 'integer', '', ''),
			('forum_topics_table_head', 'Wy&#347;wietlaj nag&#322;&oacute;wki w tabelach z tematami', '', 17, 20, 'yes-no', '1', '1', '', '', ''),
			('forums_draw_last_topics', 'Wy&#347;wietlaj na li&#347;cie nazw&#281; ostatniego tematu', '', 35, 60, 'yes-no', '1', '1', '', '', ''),
			('forums_draw_legend', 'Rysuj legend&#281; typ&oacute;w for', 'Wybierz, czy chcesz aby w bloku zawieraj&#261;cym podsumowanie forum, wy&#347;wietlana by&#322;a legenda zawieraj&#261;ca Wyja&#347;nienia typ&oacute;w for?', 35, 20, 'yes-no', '1', '1', '', '', ''),
			('forums_draw_subs', 'Wy&#347;wietlaj podfora na li&#347;cie for', '', 35, 70, 'list', '1', '1', '', '0=nigdy\r\n1=zawsze\r\n2=tylko na g&#322;&oacute;wnej li&#347;cie\r\n3=tylko poza g&#322;&oacute;wn&#261; list&#261;', ''),
			('forums_list_look', 'Wygl&#261;d listy for', 'Wybierz spos&oacute;b, w jaki ma by&#263; wy&#347;wietlana g&#322;&oacute;wna lista for.', 35, 10, 'list', '0', '0', 'integer', '0=wiele list\r\n1=jedna lista', ''),
			('forums_stats_draw', 'Rysuj statystyki forum na li&#347;cie', '', 35, 50, 'list', '3', '3', '', '0=nie\r\n1=tylko tematy\r\n2=tylko posty\r\n3=osobno tematy i posty\r\n4=&#322;&#261;cznie tematy i posty', ''),
			('guest_draw_avatars', 'Wy&#347;wietlaj avatary go&#347;ciom', '', 32, 10, 'yes-no', '1', '1', '', '', ''),
			('guest_draw_signatures', 'Wy&#347;wietlaj sygnatury go&#347;ciom', '', 32, 20, 'yes-no', '1', '1', '', '', ''),
			('guidelines_board', 'Regulamin forum', 'Je&#347;li nie poda&#322;e&#347; linku do zewn&#281;trznego regulaminu, tutaj mo&#380;esz wpisa&#263; jego tre&#347;&#263;.', 14, 40, 'text-editor', '', '', '', '', ''),
			('guidelines_link_title', 'Tytu&#322; linku regulaminu', 'Podaj tytu&#322; linku, prowadz&#261;cego do regulaminu. Je&#347;li zostawisz puste, forum u&#380;yje domy&#347;lnego.', 14, 30, 'text-input', '', '', '', '', ''),
			('guidelines_registration', 'Regulamin rejestracji', 'Tutaj wpisz tre&#347;&#263; regulaminu, akceptowanego przez u&#380;ytkownik&oacute;w w momencie rejestracji.', 14, 50, 'text-editor', '', '', '', '', ''),
			('guidelines_show_link', 'Pokazuj link do regulaminu', 'Czy chcesz aby w neag&#322;&oacute;wku forum by&#322; wy&#347;wietlany link do regulaminu?', 14, 10, 'yes-no', '0', '0', '', '', ''),
			('guidelines_url', 'Adres regulaminu', 'Je&#347;li chcesz, podaj adres strony, na kt&oacute;rej znajduje si&#281; regulamin forum. Je&#347;li zamiast tego chcesz poprostu napisa&#263; regulamin, zr&oacute;b to w polu ni&#380;ej', 14, 20, 'text-input', '', '', '', '', ''),
			('logs_clear_each', 'Usuwaj logi starsze ni&#380;', 'Podaj liczb&#281; dni, po up&#322;ywie kt&oacute;rych logi maj&#261; by&#263; kasowane.', 19, 20, 'text-input', '30', '', '', 'integer', ''),
			('logs_clear_turn', 'Czy&#347;&#263; logi', 'Czy chczesz aby logi wydarze&#324; by&#322;y czyszczone?', 19, 10, 'yes-no', '1', '1', '', '', ''),
			('meta_desc', 'Opis forum', 'Podaj kr&oacute;tki opis forum, kt&oacute;ry zostanie umieszczony w znacznikach meta. U&#322;atwi on botom wyszukiwarek lepsze zindeksowanie twego forum.', 1, 42, 'text-input', 'Forum oparte na Unisolutions Callisto', '', '', '', ''),
			('meta_keys', 'S&#322;owa kluczowe', 'Podaj list&#281; s&#322;&oacute;w powi&#261;zanych z tematyk&#261; forum, kt&oacute;re zostan&#261; umieszczone w znacznikach meta. U&#322;atwi&#261; one botom wyszukiwarek lepsze zindeksowanie twego forum.', 1, 41, 'text-input', 'forum,posty,tematy,dyskusja,callisto,unisolutions', '', '', '', 'Znaczniki meta'),
			('mods_notepad', '', NULL, 0, 1, 'info', NULL, NULL, NULL, NULL, NULL),
			('news_draw_num', 'Liczba nowo&#347;ci', 'Wybierz, ile nowo&#347;ci ma by&#263; wie&#347;wietlanych.', 21, 30, 'text-input', '1', '1', 'integer', '', ''),
			('news_forum', 'Forum z nowo&#347;ciami', 'Wska&#380; forum, kt&oacute;re ma by&#263; &#378;r&oacute;d&#322;em nowo&#347;ci forum.', 21, 20, 'list', '0', '0', 'integer', '#forums#', ''),
			('news_turn', 'W&#322;&#261;cz system nowo&#347;ci', '', 21, 10, 'yes-no', '0', '0', '', '', ''),
			('posts_counters_draw', 'Wy&#347;wietlaj liczniki post&oacute;w', 'Ustaw, kiedy w postach maj&#261; by&#263; wy&#347;wietlane liczniki.', 36, 30, 'list', '0', '0', 'integer', '0=Zawsze\r\n1=Gdy forum wlicza posty do licznika\r\n2=Nigdy', ''),
			('quick_reply_avaibility', 'Szybka odpowied&#378;', 'To jest globalne ustawienie szybkiej odpowiedzi', 36, 70, 'list', '1', '1', '', '0=wy&#322;&#261;cz szybk&#261; odpowied&#378;\r\n1=w&#322;&#261;cz szybk&#261; odpowied&#378; w zale&#380;no&#347;ci od ustawie&#324; forum\r\n2=w&#322;&#261;cz szybk&#261; odpowied&#378; zawsze', ''),
			('quick_reply_visibility', 'Widoczno&#347;&#263; szybkiej odpowiedzi', 'Czy chcesz aby szybka odpowied&#378; by&#322;a domy&#347;lnie ukryta?', 36, 80, 'yes-no', '1', '1', '', '', ''),
			('record_number', '', NULL, 0, 1, 'info', '0', '0', NULL, NULL, NULL),
			('record_time', '', '', 0, 1, 'info', '0', NULL, NULL, NULL, NULL),
			('redirect_time', 'Czas przekierowa&#324;', 'Podaj (w sekundach) czas po jakim maj&#261; nast&#281;powa&#263; przekierowania.', 1, 50, 'text-input', '3', '3', 'integer', '', 'Przekierowania'),
			('relative_hour_format', 'Format daty relatywnej', 'W miejscu s&#322;owa wstaw \"(---)\".', 4, 70, 'text-input', '(---) H:i', '', '', '', ''),
			('reputation_bonus_days', 'Regularny bonus za uczestnictwo', 'Wpisz, co ile dni u&#380;ytkownicy maj&#261; otrzymywa&#263; regularny bonus za uczestnictwo? Wpisz 0 aby wy&#322;&#261;czy&#263;', 27, 30, 'text-input', '60', '60', 'integer', '', 'Bonusy do reputacji'),
			('reputation_bonus_size', 'Rozmiar regularnego bonusu do uczestnictwa', 'Mo&#380;e by&#263; r&oacute;wnie&#380; negatywny.', 27, 40, 'text-input', '5', '5', 'integer', '', ''),
			('reputation_day_limit', 'Dzienny limit ocen', 'Podaj, ile razy w ci&#261;gu jednego dnia u&#380;ytkownik mo&#380;e oceni&#263; innego.', 27, 20, 'text-input', '5', '5', 'integer', '', ''),
			('reputation_posting_bonus', 'Bonus za posty', 'Je&#347;li chcesz aby u&#380;ytkownicy byli premiowani za ka&#380;dy post, kt&oacute;ry powi&#281;ksza licznik, wpisz tutaj liczb&#281; punkt&oacute;w za jeden post. Je&#347;li nie chcesz premiowa&#263; w ten spos&oacute;b, wpisz 0.', 27, 50, 'text-input', '2', '2', 'integer', '', ''),
			('reputation_turn', 'W&#322;&#261;cz system reputacji', '', 27, 10, 'yes-no', '1', '1', '', '', ''),
			('rss_channel_title', 'Nazwa kana&#322;u', '', 26, 20, 'text-input', 'Nowo&#347;ci na Unisolutions.pl', '', '', '', ''),
			('rss_timeline', 'Liczba temat&oacute;w', 'Wpisz licz&#281; temat&oacute;w do wy&#347;wietlania w RSS.', 26, 30, 'text-input', '5', '5', 'integer', '', ''),
			('rss_turn', 'W&#322;&#261;cz RSS', 'Czy chcesz, aby forum prowadzi&#322;o w&#322;&#261;sny kana&#322; RSS?', 26, 10, 'yes-no', '1', '0', '', '', ''),
			('search_phrase_min_length', 'Minimalna d&#322;ugo&#347;&#263; szukanej frazy', 'Podaj minimaln&#261; liczb&#281; znak&oacute;w, kt&oacute;re ma liczy&#263; szukana fraza, lub wpisz 0, aby wy&#322;&#261;czy&#263;', 23, 20, 'text-input', '1', '3', 'integer', '', ''),
			('session_recount_time', 'Przeliczaj sesje co', 'Podaj ilo&#347;&#263; minut, po up&#322;ywie kt&oacute;rych statystyki przegl&#261;daj&#261;cych forum u&#380;ytkownik&oacute;w maj&#261; by&#263; przeliczane.', 34, 40, 'text-input', '15', '', 'integer', '', ''),
			('session_time', 'D&#322;ugo&#347;&#263; sesji', 'Podaj liczb&#281; sekund, po up&#322;ywie kt&oacute;rych od ostatniej aktywno&#347;ci, sesja u&#380;ytkownika uznawana jest za martw&#261;.', 34, 10, 'text-input', '3600', '3600', '', '', ''),
			('sessions_agents', 'U&#380;ywaj agenta klienta do identyfikacji sesji', 'Ka&#380;da przegl&#261;darka wysy&#322;a do serwera informacje o sobie. Po w&#322;&#261;czeniu tej opcji, informacje te r&oacute;nie&#380; b&#281;d&#261; u&#380;ywane przy procesie identyfikacji sesji.', 34, 30, 'yes-no', '0', '', '', '', ''),
			('sessions_cookies', 'U&#380;ywaj ciastek do identyfikacji sesji', 'W momencie otwarcia sesji, zostaje utworzone ciastko zawieraj&#261;ce jej numer identyfikacyjny.', 34, 20, 'yes-no', '0', '', '', '', ''),
			('spiders_draw_online', 'Wy&#347;wietlaj boty na listach on-line', 'Czy chcesz, aby boty wyszukiwarek by&#322;y obecne na listach on-line?', 18, 30, 'yes-no', '1', '', '', '', ''),
			('spiders_force_simple', 'Przeno&#347; boty do trybu lekkiego', 'Czy chcesz, aby botom wy&#347;wietlana by&#322;a lekka wersja forum?', 18, 20, 'yes-no', '1', '', '', '', ''),
			('spiders_list', 'Lista bot&oacute;w', 'Podaj list&#281; bot&oacute;w wyszukiwarek wed&#322;ug wzoru: \"agent:nazwa\". Je&#347;li chcesz poda&#263; wi&#281;cej ni&#380; jednego bota wyszukiwarek, zr&oacute;b to w nast&#281;pnych liniach.', 18, 10, 'text-box', 'AdsBot-Google:AdsBot\r\nia_archiver:Alexa\r\nScooter/:Alta Vista\r\nAsk Jeeves:Ask Jeeves\r\nBaiduspider+(:Baidu\r\nExabot/:Exabot\r\nFAST Enterprise Crawler:FAST Enterprise\r\nFAST-WebCrawler/:FAST WebCrawler\r\nhttp://www.neomo.de/:Francis\r\nGigabot/:Gigabot\r\nMediapartners-Google:Google Adsense\r\nGoogle Desktop:Google Desktop\r\nFeedfetcher-Google:Google Feedfetcher\r\nGooglebot:Google\r\nibm.com/cs/crawler:IBM Research\r\nmsnbot-NewsBlogs/:MSN NewsBlogs\r\nmsnbot/:MSN\r\nmsnbot-media/:MSNbot Media\r\nYahoo-MMCrawler/:Yahoo MMCrawler\r\nYahoo! DE Slurp:Yahoo Slurp\r\nYahoo! Slurp:Yahoo\r\nYahooSeeker/:YahooSeeker\r\nW3C_Validator/:W3C\r\nOnetSzukaj:OnetSzukaj\r\nMLBot:MLBot\r\nSzukacz/:Szukacz', '', '', '', ''),
			('spiders_log_visits', 'Zbieraj logi z wizyt bot&oacute;w', 'Czy chcesz, aby ka&#380;de wej&#347;cie bota na forum by&#322;o zapisywane w dzienniku? (Je&#347;li boty cz&#281;sto ci&#281; odwiedzaj&#261;, wy&#322;&#261;cz t&#261; opcj&#281;, gdy&#380; mo&#380;e generowa&#263; du&#380;e obci&#261;&#380;enie)', 18, 40, 'yes-no', '1', '', '', '', ''),
			('style_allow_change', 'U&#380;ytkownicy mog&#261; wybiera&#263; styl forum', '', 12, 10, 'yes-no', '1', '1', '', '', ''),
			('system_time_adjustment', 'Poprawka czasu', 'Je&#347;li mimo ustawienia strefy czasowej godzina wci&#261;&#380; si&#281; nie zgadza, mo&#380;esz doda&#263;, lub odj&#261;&#263; b&#322;&#281;dne minuty.', 4, 20, 'text-input', '0', '0', 'integer', '', ''),
			('time_dst', 'Autodetekcja czasu letniego', '', 4, 30, 'yes-no', '1', '1', '', '', ''),
			('time_fulldate_format', 'Format pe&#322;nej daty', '', 4, 40, 'text-input', 'l H:i d.m.Y', '', '', '', 'Wy&#347;wietlanie'),
			('time_hour_format', 'Format godziny', '', 4, 50, 'text-input', 'H:i', '', '', '', ''),
			('time_relative', 'U&#380;ywaj relatywnych dat', 'np. \"wczoraj\", \"dzi&#347;\", \"jutro\"', 4, 60, 'yes-no', '1', '', '', '', 'Daty relatywne'),
			('time_timezone', 'Domy&#347;lna strefa czasowa', '</textarea>', 4, 10, 'list', '1', '0', '', '-12=(GMT - 12:00) Enitwetok, Kwajalien\r\n-11=(GMT - 11:00) Midway, Samoa\r\n-10=(GMT - 10:00) Hawaje\r\n-9.5=(GMT - 9:30) Polinezja Francuska\r\n-9=(GMT - 9:00) Alaska\r\n-8=(GMT - 8:00) Czas pacyficzny (USA i Kanada)\r\n-7=(GMT - 7:00) Czas g&oacute;rski (USA i Kanada)\r\n-6=(GMT - 6:00) Czas &#346;rodkowoameryka&#324;ski (USA i Kanada), Miasto Meksyk\r\n-5=(GMT - 5:00) Czas wschodnioameryka&#324;ski (USA i Kanada), Bogota, Lima\r\n-4=(GMT - 4:00) Czas atlantycki (Kanada), Caracas, La Paz\r\n-3:30=(GMT - 3:30) Czas nowofundlandzki\r\n-3=(GMT - 3:00) Brazylia, Buenos Aires, Falklandy\r\n-2=(GMT - 2:00) &#346;rodkowy Atlantyk, Ascension Islands, &#346;wi&#281;ta Helena\r\n-1=(GMT - 1:00) Azory, Republika Zielonego Przyl&#261;dka\r\n0=(GMT) Casablanca, Dublin, Londyn, Lisbona, Monrovia\r\n1=(GMT + 1:00) Bruksela, Kopenhaga, Madryt, Pary&#380;\r\n2=(GMT + 2:00) Kaliningrad, Po&#322;udniowa Afryka\r\n3=(GMT + 3:00) Bagdad, Rijad, Moskwa, Nairobi\r\n3.5=(GMT + 3:30) Teheran\r\n4=(GMT + 4:00) Abu Zabi, Baku, Maskat, Tbilisi\r\n4.5=(GMT + 4:30) Kabul\r\n5=(GMT + 5:00) Ekaterinburg, Karaczi, Taszkent\r\n5.5=(GMT + 5:30) Bombaj, Kolkata, Madras, Nowe Delhi\r\n5.5=(GMT + 5:45) Katmandu\r\n6=(GMT + 6:00) A&#322;maty, Kolombo, Dhaka\r\n6.5=(GMT + 6:30) Rangun, Naypyidaw, Banten\r\n7=(GMT + 7:00) Bangkok, Hanoi, D&#380;akarta\r\n8=(GMT + 8:00) Hong Kong, Perth, Singapur, Tajpej\r\n8.75=(GMT + 8:45) Caiguna, Eucla\r\n9=(GMT + 9:00) Osaka, Sapporo, Seoul, Tokyo, Yakutsk\r\n9.5=(GMT + 9:30) Adelajda, Darwin\r\n10=(GMT + 10:00) Melbourne, Papua-Nowa Gwinea, Sydney\r\n10.5=(GMT + 10:30) Lord Howe\r\n11=(GMT + 11:00) Magadan, Nowa Kaledonia, Wyspy Salomona\r\n11.5=(GMT + 11:30) Burnt Pine, Kingston\r\n12=(GMT + 12:00) Auckland, Fid&#380;i\r\n12.75=(GMT + 12:45) Wyspy Chatham\r\n13=(GMT + 13:00) Kamczatka, Anadyr\r\n14=(GMT + 14:00) Kiritimati', 'Ustawienia g&#322;&oacute;wne'),
			('trashcan_forum', 'Forum u&#380;ywane przez kosz', 'Wybierz z listy forum, na kt&oacute;re maj&#261; by&#263; przenoszone usuwanie posty i tematy', 20, 2, 'list', '0', '0', 'integer', '#forums#', ''),
			('trashcan_turn', 'W&#322;&#261;cz kosz', 'Je&#347;li aktywujesz kosz, ka&#380;dy temat i post przed usuni&#281;ciem zostanie pierw umieszczony w specjalnym forum.', 20, 1, 'yes-no', '0', '0', '', '', ''),
			('user_account_activation', 'Spos&oacute;b aktywacji konta', 'Wybierz, w jaki spos&oacute;b maj&#261; by&#263; aktywowane konta nowych u&#380;ytkownik&oacute;w.', 33, 20, 'list', '0', '', 'integer', '0=brak\r\n1=u&#380;ytkownik\r\n2=administrator', ''),
			('user_interests_max_lenght', 'Maksymalna d&#322;ugo&#347;&#263; zainteresowa&#324;', 'Podaj maksymaln&#261; ilo&#347;&#263; znak&oacute;w, jak&#261; u&#380;ytkownik mo&#380;e wpisa&#263; do pola \"zainteresowania\" swojego profilu.', 12, 40, 'text-input', '10000', '10000', 'integer', '', ''),
			('user_login_automaticaly_unlock', 'Automatycznie odblokowywuj konta', 'Czy konta mog&#261; by&#263; automatycznie odblokowywane? Je&#347;li ustawisz to na nie, konto b&#281;dzie mo&#380;na odblokowa&#263; wy&#322;&#261;cznie z poziomu panelu administracyjnego.', 33, 70, 'yes-no', '1', '', '', '', ''),
			('user_login_max_tries', 'Maksymalna liczba pr&oacute;b logowania', 'Po ilu nie udanych pr&oacute;bach logowa&#324; konto uzytkownika ma zosta&#263; zablokowane? Podaj 0 aby wy&#322;&#261;czy&#263;.', 33, 50, 'text-input', '3', '3', 'integer', '', ''),
			('user_login_reset_after', 'Zeruj nieudane pr&oacute;by logowa&#324; po', 'Po ilu minutach od zablokowania konta, blokada ma zosta&#263; automatycznie zdj&#281;ta? Podaj 0, aby zdj&#281;cie blokady nast&#281;powa&#322;o dopiero po pomy&#347;lnym zalogowaniu.', 33, 60, 'text-input', '2', '2', 'integer', '', ''),
			('user_require_login_to_browse', 'Wymagaj zalogowania si&#281;', 'W&#322;&#261;czenie tej opcji powoduje, &#380;e niezalogowani u&#380;ytkownicy b&#281;d&#261; mieli dost&#281;p jedynie do formularza logowania i rejestracji (je&#347;li ta jest w&#322;&#261;czona).', 32, 30, 'yes-no', '0', '', '', '', ''),
			('user_sig_max_length', 'Maksymalna d&#322;ugo&#347;&#263; sygnatur', 'Podaj maksymaln&#261; ilo&#347;&#263; znak&oacute;w, kt&oacute;re u&#380;ytkownik mo&#380;e umie&#347;ci&#263; w sygnaturze. Wpisz 0, aby wy&#322;&#261;czy&#263;', 12, 35, 'text-input', '10000', '10000', 'integer', '', ''),
			('users_allow_bbcodes_in_sigs', 'Zezw&oacute;l na bbCod''y w sygnaturach', '', 12, 50, 'yes-no', '1', '1', '', '', ''),
			('users_allow_emoticones_in_sigs', 'Zezw&oacute;l na emotikony w sygnaturach', '', 12, 60, 'yes-no', '1', '1', '', '', ''),
			('users_allow_mail_reuse', 'Zezw&oacute;l na ponowne u&#380;ywanie adresu e-mail przy rejestracjach', 'Czy chcesz aby mo&#380;liwe by&#322;o rejestrowanie kilku kont na jeden adres e-mail?', 33, 30, 'yes-no', '0', '', '', '', ''),
			('users_allow_post_report', 'Zezw&oacute;l u&#380;ytkownikom na zg&#322;aszanie post&oacute;w', 'Czy chcesz, aby funkcja zg&#322;aszania nieregulaminowych post&oacute;w by&#322;a dost&#281;pna?', 36, 40, 'yes-no', '1', '1', '', '', ''),
			('users_allow_register', 'Zezw&oacute;l na nowe rejestracje', 'Je&#347;li nie chcesz, aby nowi u&#380;ytkownicy rejestrowali si&#281;, wy&#322;&#261;cz t&#261; funckj&#281;.', 33, 10, 'yes-no', '1', '1', '', '', ''),
			('users_avatar_autosize', 'Automatycznie skaluj avatary', 'Je&#347;li chcesz, aby forum samo wykrywalo rozmiary avatara, ustaw na tak. W przeciwnym razie u&#380;ytkownik sam b&#281;dzie musia&#322; okre&#347;li&#263; rozmiar avatara.', 31, 80, 'yes-no', '1', '1', '', '', ''),
			('users_avatar_max_height', 'Maksymalna wysoko&#347;&#263; avatara', 'Podaj w pikselach maksymaln&#261; dopuszczaln&#261; wysoko&#347;&#263; avatara, lub wpisz 0, aby wy&#322;&#261;czy&#263; limit', 31, 70, 'text-input', '100', '100', 'integer', '', ''),
			('users_avatar_max_size', 'Maksymalny rozmiar avatara', 'Podaj w kb maksymalny dopuszczalny rozmiar avatara, lub wpisz 0, aby znie&#347;&#263; to ograniczenie.', 31, 50, 'text-input', '64', '64', 'integer', '', ''),
			('users_avatar_max_width', 'Maksymalna szeroko&#347;&#263; avatara', 'Podaj w pikselach maksymaln&#261; dopuszczaln&#261; szeroko&#347;&#263; avatara, lub wpisz 0, aby wy&#322;&#261;czy&#263; limit', 31, 60, 'text-input', '100', '100', 'integer', '', ''),
			('users_avatars_extensions', 'Dopuszczalne rozszerzenia avatar&oacute;w', 'Je&#347;li chcesz poda&#263; wi&#281;cej ni&#380; jedno, oddziel je od siebie przecinkami, np. \"gif,jpeg\".', 31, 20, 'text-input', 'gif,jpg,png', '', '', '', ''),
			('users_can_avatars', 'Zezw&oacute;l na Avatary', '', 31, 10, 'yes-no', '1', '1', '', '', ''),
			('users_can_remote_avatars', 'Zezw&oacute;l na zdalne avatary', '', 31, 30, 'yes-no', '1', '1', '', '', ''),
			('users_can_sigs', 'U&#380;ytkownicy mog&#261; mie&#263; sygnatury', '', 12, 30, 'yes-no', '1', '1', '', '', ''),
			('users_can_upload_avatars', 'Zezw&oacute;l na upload avatar&oacute;w', '', 31, 40, 'yes-no', '1', '1', '', '', ''),
			('users_clear_notactive', 'Usuwaj niekatywne konta po', 'Podaj liczb&#281; dni od za&#322;o&#380;enia nie aktywnego konta, po up&#322;ywie kt&oacute;rych jest ono uznawane za nieaktywne i kasowane, lub wpisz 0, aby nigdy nie usuwa&#263; nieaktywnych kont u&#380;ytkownik&oacute;w.', 33, 40, 'text-input', '3', '0', 'integer', '', ''),
			('users_count_online', 'Licz sesje', 'Czy chcesz, aby forum liczy&#322;o sesje u&#380;ytkownik&oacute;w? Je&#347;li wy&#322;&#261;czysz to ustawienie, informacje o liczbie aktualnie przegl&#261;daj&#261;cych forum u&#380;ytkownikach i go&#347;ciach stan&#261; si&#281; niedost&#281;pne.', 34, 40, 'yes-no', '1', '1', '', '', ''),
			('users_guests_online', 'Liczba go&#347;ci on-line', 'Liczba niezalogowanych u&#380;ytkownik&oacute;w on-line.', 6, 4, 'info', '0', '', '', '', ''),
			('users_list_draw_avatar', 'Wy&#347;wietlaj avatary', 'Czy chcesz, aby na li&#347;cie u&#380;ytkownik&oacute;w by&#322;y wy&#347;wietlane avatary?', 30, 20, 'yes-no', '0', '1', '', '', ''),
			('users_list_draw_contact', 'Wy&#347;wietlaj opcje kontaktowe', 'Czy chcesz aby na li&#347;cie u&#380;ytkownik&oacute;w by&#322;y wy&#347;wietlane opcje kontaktowe?', 30, 60, 'yes-no', '1', '1', '', '', ''),
			('users_list_draw_posts', 'Wy&#347;wietlaj liczb&#281; post&oacute;w', 'Czy chcesz, aby na li&#347;cie u&#380;ytkownik&oacute;w by&#322;y wy&#347;wietlane liczniki post&oacute;w?', 30, 50, 'yes-no', '1', '1', '', '', ''),
			('users_list_draw_rank', 'Wy&#347;wietlaj rang&#281;', 'Czy chcesz, aby na li&#347;cie u&#380;ytkownik&oacute;w by&#322;y wy&#347;wietlane rangi?', 30, 40, 'yes-no', '1', '1', '', '', ''),
			('users_list_draw_register', 'Wy&#347;wietlaj dat&#281; rejestracji', 'Czy chcesz, aby na li&#347;cie uzytkownik&oacute;w by&#322;y wy&#347;wietlane daty rejestracji?', 30, 40, 'yes-no', '1', '1', '', '', ''),
			('users_list_turn', 'W&#322;&#261;cz list&#281; u&#380;ytkownik&oacute;w', '', 30, 10, 'yes-no', '1', '1', '', '', ''),
			('users_list_users_per_page', 'Liczba u&#380;ytkownik&oacute;w wy&#347;wietlanych na jednej stronie', '', 30, 70, 'text-input', '15', '15', 'integer', '', ''),
			('users_num', 'Liczba u&#380;ytkownik&oacute;w', 'Liczba zarejestrowanych u&#380;ytkownik&oacute;w', 6, 1, 'info', '2', '', '', '', ''),
			('users_online', 'U&#380;ytkownicy on-line', 'Liczba u&#380;ytkownik&oacute;w on-line.', 6, 1, 'info', '1', '', '', '', ''),
			('users_online_hidden', '', NULL, 0, 1, 'info', '0', NULL, NULL, NULL, NULL),
			('users_pm_draw', 'Ilo&#347;&#263; wiadomo&#347;ci na stron&#281;', 'Podaj, ile wiadomo&#347;ci ma by&#263; wy&#347;wietlanych na stron&#281;.', 15, 10, 'text-input', '15', '15', 'integer', '', ''),
			('users_posts_to_title', 'Liczba post&oacute;w, po kt&oacute;rej u&#380;ytkownik sam mo&#380;e nada&#263; sobie tytu&#322;', 'Wpisz 0, aby wy&#322;&#261;czy&#263;', 12, 20, 'text-input', '50', '50', 'integer', '', ''),
			('warns_lock_account', 'Blokuj konto przy przekroczeniu ostrze&#380;e&#324;', 'Czy chcesz, aby po osi&#261;gnieciu limitu ostrze&#380;e&#324; konto u&#380;ytkownika by&#322;o blokowane?', 22, 40, 'yes-no', '1', '1', '', '', ''),
			('warns_lock_message', 'Wiadomo&#347;&#263; do zbanowanych', 'Je&#347;li chcesz, mo&#380;esz zablokowanym u&#380;ytkownikom wy&#347;wietla&#263; w&#322;asn&#261; wiadomo&#347;&#263; informuj&#261;c&#261; o przekroczeniu dopuszczalnej liczby ostrze&#380;e&#324;.', 22, 50, 'text-box', 'Przepraszamy, ale przekroczy&#322;e&#347; dopuszczaln&#261; liczb&#281; ostrze&#380;e&#324;, i twoje konto zosta&#322;o zablokowane.', '', '', '', ''),
			('warns_max', 'Maksymalna dopuszczalna liczba ostrze&#380;e&#324;', 'Podaj, ile ostrze&#380;e&#324; maksymalnie mo&#380;e mie&#263; u&#380;ytkownik.', 22, 20, 'text-input', '5', '5', 'integer', '', ''),
			('warns_show', 'Wy&#347;wietlaj ostrze&#380;enia', 'Wybierz, kto i kiedy mo&#380;e widzie&#263; ostrze&#380;enia', 22, 30, 'list', '0', '0', 'integer', '0=Wszyscy u&#380;ytkownicy\r\n1=Zalogowani u&#380;ytkownicy\r\n2=Moderatorzy i u&#380;ytkownik', ''),
			('warns_turn', 'W&#322;&#261;cz system ostrze&#380;e&#324;', '', 22, 10, 'yes-no', '1', '1', '', '', '')");
		
		/**
		 * update settings
		 */
		
		foreach ( $settings as $setting_id => $setting_value)
			$mysql -> update( array( 'setting_value' => $setting_value), 'settings', "`setting_setting` = '$setting_id'");
			
		/**
		 * ok
		 */
		
		if ( strlen( mysql_error()) != 0){
			$page = $style -> drawImage( 'errror').' '.$lang_string['update_query_update_content'].' <b>"settings"</b>';
		}else{
			$page = $style -> drawImage( 'success').' '.$lang_string['update_query_update_content'].' <b>"settings"</b>';
		}
		
	break;
		
	case 2:
		
		/**
		 * update version
		 */
		
		$mysql -> insert( array( 'version_id' => 8, 'version_short' => '1.0 RC 4', 'version_time' => time()), 'version_history');
		
		/**
		 * set message
		 */
		
		$page = 'done';
		
	break;
	
}

?>
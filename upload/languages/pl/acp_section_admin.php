<?

/**
 * sections
 */

$this -> strings['acp_admin_section_version'] = 'Aktualizacje';
$this -> strings['acp_admin_section_secruity'] = 'Ustawienia bezpieczeństwa';
$this -> strings['acp_admin_section_logs'] = 'Logi forum';
$this -> strings['acp_admin_section_statistics'] = 'Statystyki';
$this -> strings['acp_admin_section_mysql'] = 'MySQL';

/**
 * sections elements
 */

$this -> strings['acp_admin_subsection_version_check'] = 'Sprawdź aktualność';

$this -> strings['acp_admin_subsection_secruity_summary'] = 'Raport bezpieczeństwa';
$this -> strings['acp_admin_subsection_secruity_admins_list'] = 'Lista administratorów';

$this -> strings['acp_admin_subsection_logs_admins'] = 'Logi administratorów';
$this -> strings['acp_admin_subsection_logs_mods'] = 'Logi moderatorów';
$this -> strings['acp_admin_subsection_logs_emails'] = 'Logi emaili';
$this -> strings['acp_admin_subsection_logs_bots'] = 'Logi botów wyszukiwarek';
$this -> strings['acp_admin_subsection_logs_logins'] = 'Logi logowań do acp';

$this -> strings['acp_admin_subsection_stats_registers'] = 'Statystyka rejestracji';
$this -> strings['acp_admin_subsection_stats_topics'] = 'Statystyka nowych tematów';
$this -> strings['acp_admin_subsection_stats_posts'] = 'Statystyka nowych postów';
$this -> strings['acp_admin_subsection_stats_pms'] = 'Statystyka wiadomości';
$this -> strings['acp_admin_subsection_stats_topics_views'] = 'Statystyka czytań tematów';

$this -> strings['acp_admin_subsection_mysql_query'] = 'Wykonaj zapytanie';

/**
 * commons
 */

$this -> strings['acp_admin_subsection_secruity_summary_state'] = 'Stan';
$this -> strings['acp_admin_subsection_secruity_change_setting'] = 'Zmień to ustawienie';

/**
 * section: version
 */

$this -> strings['acp_admin_subsection_version_check_not_enabled'] = 'Nie można było sprawdzić wersji';
$this -> strings['acp_admin_subsection_version_check_not_enabled_info'] = 'Próba nawiązania połączenia z serverem Unisolutions.pl nie powiodła się.';

$this -> strings['acp_admin_subsection_version_check_ok'] = 'Twoja instalacja Callisto jest aktualna!';
$this -> strings['acp_admin_subsection_version_check_ok_info'] = 'Gratulacje! Twoja instalacja Callisto jest aktualna!';

$this -> strings['acp_admin_subsection_version_check_error'] = 'Twoja instalacja Callisto jest nie aktualna!';
$this -> strings['acp_admin_subsection_version_check_error_info'] = 'Twoja instalacja Callisto: <b>%actual_call_ver</b> (%actual_call_id)<br />Najnowsza wersja Callisto: <b>%call_new_ver</b> (%call_new_id)';

/**
 * section: secruity_summary
 */

$this -> strings['acp_admin_subsection_secruity_summary_acp_path'] = 'Zmieniona lokalizacja panelu administracyjnego';

$this -> strings['acp_admin_subsection_secruity_summary_acp_path_info'] = 'Zmienienie ścieżki dostępu do panelu administracyjnego na inną może utrudnić bądź uniemożliwić dostęp do panelu administracyjnego osobom nieporządanym.<br /><br />Aby zmienić lokalizację panelu administracyjnego, zmień nazwę lub przenieś folder "admin" znajdujący się w głównym folderze forum do innej lokalizacji, a następnie w pliku "init.php", znajdującym się w folderze "system", odszukaj linię:<br /><br />//acp path<br /><b>define( \'ACP_PATH\', \'admin/\');</b><br /><br />i zastąp "admin/" nową ścieżką, np.:<br /><br />//acp path<br /><b>define( \'ACP_PATH\', \'ukryty_panel_administracyjny/\');</b>';

$this -> strings['acp_admin_subsection_secruity_summary_acp_link_draw'] = 'Ukryty link do panelu administracyjnego';

$this -> strings['acp_admin_subsection_secruity_summary_acp_link_draw_info'] = 'W przypadku zmiany lokalizacji panelu administracyjnego dodatkową pomocą może być wyłączenie wyświetlania linka do panelu w menu użytkownika na forum.';

/**
 * section: admins logs
 */

$this -> strings['acp_admin_subsection_logs_admins_last_5'] = 'Ostatnie 5 akcji administratorów';
$this -> strings['acp_admin_subsection_logs_admins_saved_logs'] = 'Zapisane logi administratorów';
$this -> strings['acp_admin_subsection_logs_admins_search_logs'] = 'Przeszukaj logi';

$this -> strings['acp_admin_subsection_logs_action_type'] = 'Akcja';
$this -> strings['acp_admin_subsection_logs_action_time'] = 'Czas akcji';

$this -> strings['acp_admin_subsection_logs_admins_saved_logs_logs_num'] = 'Liczba logów';
$this -> strings['acp_admin_subsection_logs_admins_saved_logs_member_logs_show'] = 'pokaż logi użytkownika';

$this -> strings['acp_admin_subsection_logs_admins_search_logs_search_by'] = 'Szukana fraza';
$this -> strings['acp_admin_subsection_logs_admins_search_logs_search_in'] = 'Szukaj w';

$this -> strings['acp_admin_subsection_logs_admins_search_logs_search_in_0'] = 'akcja';
$this -> strings['acp_admin_subsection_logs_admins_search_logs_search_in_1'] = 'użytkownik';
$this -> strings['acp_admin_subsection_logs_admins_search_logs_search_in_2'] = 'ip';

$this -> strings['acp_admin_subsection_logs_admins_search_logs_search_empty_phrase'] = 'Musisz podać frazę do wyszukania.';

$this -> strings['acp_admin_subsection_logs_admins_search_logs_search_button'] = 'Przeszukaj logi';

/**
 * section: mods logs
 */

$this -> strings['acp_admin_subsection_logs_mods_last_5'] = 'Ostatnie 5 akcji moderatorów';
$this -> strings['acp_admin_subsection_logs_mods_saved_logs'] = 'Zapisane logi moderatorów';

/**
 * section: mails logs
 */

$this -> strings['acp_admin_subsection_mails_logs_sender'] = 'Nadawca';
$this -> strings['acp_admin_subsection_mails_logs_receiver'] = 'Odbiorca';
$this -> strings['acp_admin_subsection_mails_logs_subject'] = 'Temat';

$this -> strings['acp_admin_subsection_mails_logs_last_5'] = 'Ostatnie 5 wiadomości';
$this -> strings['acp_admin_subsection_mails_logs_saved_logs'] = 'Zapisane logi e-maili';

/**
 * section: search spiders logs
 */

$this -> strings['acp_admin_subsection_logs_bots_bot'] = 'Bot';
$this -> strings['acp_admin_subsection_logs_bots_time'] = 'Data';
$this -> strings['acp_admin_subsection_logs_bots_ip'] = 'Ip';

/**
 * STATISTICS CENTER
 */

$this -> strings['acp_admin_subsection_stats_registers_generate'] = 'Generuj statystykę';

$this -> strings['acp_admin_subsection_stats_registers_generate_start'] = 'Od';
$this -> strings['acp_admin_subsection_stats_registers_generate_end'] = 'Do';
$this -> strings['acp_admin_subsection_stats_registers_generate_precision'] = 'Dokładność';

$this -> strings['acp_admin_subsection_stats_registers_generate_precision_0'] = 'Dzień';
$this -> strings['acp_admin_subsection_stats_registers_generate_precision_1'] = 'Miesiąc';
$this -> strings['acp_admin_subsection_stats_registers_generate_precision_2'] = 'Rok';

$this -> strings['acp_admin_subsection_stats_registers_generate_result_date'] = 'Data';
$this -> strings['acp_admin_subsection_stats_registers_generate_result_results'] = 'Wyniki';

/**
 * section: mysql query
 */

$this -> strings['acp_admin_subsection_mysql_query_success'] = 'Zapytanie zostało wykonane';

$this -> strings['acp_admin_subsection_mysql_query_error'] = 'Błąd nr. %mysql_error_number';
$this -> strings['acp_admin_subsection_mysql_query_result_info'] = 'Zapytanie zostało wykonane pomyślnie w czasie %mysql_query_time milisekund, zwracając %mysql_query_results rekordów.';

$this -> strings['acp_admin_subsection_mysql_query_result'] = 'Wynik zapytania';
$this -> strings['acp_admin_subsection_mysql_query_text'] = 'Treść zapytania';

$this -> strings['acp_admin_subsection_mysql_query_button'] = 'Wykonaj zapytanie';

$this -> strings['acp_admin_subsection_mysql_query_log'] = 'Wykonano zapytanie do sql o treśći "%mysql_log_content"';

?>
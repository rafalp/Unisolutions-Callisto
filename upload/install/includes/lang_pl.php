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
|	Polish Installer Language File
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
$lang_string['install_welcome'] = 'Witaj w instalatorze Callisto!';
$lang_string['install_locked'] = 'Przepraszamy, ale instalator został zablokowany. Nim przystąpisz do instalacji, usuń plik "lock" z katalogu "install".';
$lang_string['install_entry'] = 'Nim rozpoczniesz instalację, prosimy abyś upewnił się, że wszystkie pliki zostały załadowane na server, a także przygotował dane bazy MySql, do której ma zostać zainstalowane Callisto.';
$lang_string['install_reqs_list'] = 'Wymagania syetmowe';

$lang_string['install_reqs_php'] = 'PHP 5.1';
$lang_string['install_reqs_mysql'] = 'MySQL 3.4';

$lang_string['install_reqs_actual'] = 'obecnie:';
$lang_string['install_reqs_actual_nonie'] = 'nie znaleziono';
$lang_string['install_reqs_saveable_config'] = 'Zapisywalny plik config.php';
$lang_string['install_reqs_saveable_cache'] = 'Zapisywalny folder cache';
$lang_string['install_reqs_saveable_uploads'] = 'Zapisywalny folder uploads';

$lang_string['install_cant_continue'] = 'Nie można kontynuować instalacji';
$lang_string['install_continue'] = 'Kontynuuj instalację';
$lang_string['install_finish'] = 'Zakończ instalację';

$lang_string['install_db_configuration'] = 'Konfiguracja połączenia z bazą danych.';

$lang_string['db_host'] = 'Host bazy danych';
$lang_string['db_port'] = 'Port bazy danych';
$lang_string['db_name'] = 'Nazwa bazy danych';
$lang_string['db_user'] = 'Użytkownik bazy danych';
$lang_string['db_pass'] = 'Hasło bazy danych';
$lang_string['db_prefix'] = 'Prefiks tabel w bazie danych';

$lang_string['db_host_help'] = 'Zazwyczaj "localhost"';
$lang_string['db_port_help'] = 'Domyślnie 80';
$lang_string['db_name_help'] = 'Wpisz nazwę bazy danych którą używać ma Callisto';
$lang_string['db_user_help'] = 'Wpisz nazwę użytkownika bazy danych';
$lang_string['db_pass_help'] = 'Hasło użytkownika bazy danych';
$lang_string['db_prefix_help'] = 'Używaj różnych prefiksów dla różnych skryptów i instalacji. Dzięki temu unikniesz konfliktów.';

$lang_string['db_host_wrong'] = 'Musisz podać host bazy danych';
$lang_string['db_port_wrong'] = 'Musisz podać port bazy danych';
$lang_string['db_name_wrong'] = 'Musisz podać nazwę bazy danych';
$lang_string['db_user_wrong'] = 'Musisz podać nazwę użytkownika bazy danych';
$lang_string['db_pass_wrong'] = 'Musisz podać hasło użytkownika bazy danych';

$lang_string['db_host_no_connect'] = 'Próba nawiązania połączenia z hostem bazy danych nie powiodła się.';
$lang_string['db_no_connect'] = 'Próba nawiązania połączenia z bazą danych nie powiodła się.';
$lang_string['db_prefix_used'] = 'Tabele z takim prefixem już istnieją.';
$lang_string['db_connect_done'] = 'Połączenie z bazą danych zostało pomyślnie nawiązane.';

$lang_string['install_structure'] = 'Tworzenie struktury bazy danych';
$lang_string['install_structure_done'] = 'Struktura bazy danych została utworzona.';

$lang_string['install_data'] = 'Wypełnienie bazy domyślnymi danymi';
$lang_string['install_data_done'] = 'Baza została wypełniona domyślnymi danymi.';

$lang_string['install_query_in_action'] = 'Zapis do bazy...';
$lang_string['install_query_create'] = 'Stworzono tabelę';
$lang_string['install_query_insert'] = 'Zapisano dane do tabeli';

$lang_string['forum_conf'] = 'Ustawienia forum';

$lang_string['forum_path'] = 'Ścieżka do forum';
$lang_string['forum_path_help'] = 'Domyślnie forum samo spróbuje określić ścieżkę do forum. Zmień ją tylko wtedy, kiedy, jest błędna.';

$lang_string['forum_path_empty'] = 'Musisz podać ścieżkę do forum.';
$lang_string['forum_conf_saved'] = 'Forum zostało skonfigurowane.';

$lang_string['admin_acc'] = 'Konto administratora';

$lang_string['admin_login'] = 'Login użytkownika';
$lang_string['admin_pass'] = 'Hasło użytkownika';
$lang_string['admin_pass_rep'] = 'Powtórz hasło użytkownika';
$lang_string['admin_mail'] = 'E-mail użytkownika';

$lang_string['admin_field_empty'] = 'Musisz wypełnić wszystkie pola.';
$lang_string['admin_pass_nomath'] = 'Hasła nie pasują do siebie.';
$lang_string['admin_wrong_email'] = 'Podałeś błedny adres e-mail.';

$lang_string['admin_acc_done'] = 'Konto administratora zostało utworzone.';

$lang_string['install_finished'] = 'Zakonczenie instalacji.';
$lang_string['install_finished_text'] = 'Callisto zostało zainstalowane i jest już gotowe do użycia. Ze względów bezpieczeństwa powinieneś usunąć, przenieść bądź zmienić nazwę folderu z instalatorem.';

$lang_string['show_forum'] = 'Pokaż mi moje forum!';

$lang_string['update_welcome'] = 'Witaj w aktualizatorze Callisto!';
$lang_string['update_entry'] = 'Nim przystąpisz do aktualizacji Callisto do następnej wersji, upewnij się, że masz uprawnienia administratora i wykonałeś kopię zapasową plików i bazy danych forum.';

$lang_string['update_login'] = 'Logowanie';
$lang_string['update_login_name'] = 'Login administratora';
$lang_string['update_login_pass'] = 'Hasło administratora';
$lang_string['update_login_login'] = 'Zaloguj się';

$lang_string['update_login_wrong'] = 'Podałeś błędne dane, lub nie masz wystarczających uprawnień, aby móc aktualizować forum.';
$lang_string['update_login_done'] = 'Jesteś uprawniony do aktualizacji forum.';

$lang_string['update_begin'] = 'Rozpocznij aktualizację forum';

$lang_string['update_process'] = 'Aktualizowanie forum';
$lang_string['update_process_no_need'] = 'Twoje forum jest aktualne!';
$lang_string['update_process_begin'] = 'Aktualizacja forum do wersji';

$lang_string['update_query_create'] = 'Stworzono tabelę';
$lang_string['update_query_insert'] = 'Wypełniono danymi tabelę';
$lang_string['update_query_update_structure'] = 'Aktualizowano strukturę tabeli';
$lang_string['update_query_update_content'] = 'Aktualizowano zawartość tabeli';

$lang_string['update_next_done'] = 'Forum zostało pomyślnie aktualizowane do następnej wersji.';
$lang_string['update_next_continue'] = 'Kontynuuj aktualizację forum.';

?>
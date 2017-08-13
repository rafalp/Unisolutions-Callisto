<?

/**
 * user register
 */

$this -> strings['register_on_board'] = 'Rejestracja na forum';

$this -> strings['register_on_board_for_guests_only'] = 'Już jesteś zarejestrowany.';
$this -> strings['register_on_board_disabled'] = 'Przepraszamy, ale administrator forum wyłączył rejestracje nowych użytkowników.';

$this -> strings['register_on_board_login'] = 'Twój login';
$this -> strings['register_on_board_pass'] = 'Twoje hasło';
$this -> strings['register_on_board_pass_rep'] = 'Powtórz swoje hasło';
$this -> strings['register_on_board_email'] = 'Twój adres e-mail';

$this -> strings['register_on_board_login_empty'] = 'Musisz podać swój login.';
$this -> strings['register_on_board_login_taken'] = 'Przepraszamy, ale ten login jest już zajęty.';
$this -> strings['register_on_board_login_tooshort'] = 'Przepraszamy, ale login użytkownika nie może być krótszy niż %login_length znaków.';
$this -> strings['register_on_board_login_toolong'] = 'Przepraszamy, ale login użytkownika nie może być dłuższy niż %login_length znaków.';
$this -> strings['register_on_board_pass_empty'] = 'Musisz podać swoje hasło.';
$this -> strings['register_on_board_pass_rep_empty'] = 'Musisz powtórnie podać swoje hasło.';
$this -> strings['register_on_board_pass_nomatch'] = 'Podane hasła nie pasują do siebie.';
$this -> strings['register_on_board_mail_empty'] = 'Musisz podać swój e-mail.';
$this -> strings['register_on_board_mail_taken'] = 'Przepraszamy, ale ten adres e-mail jest już zajęty.';

$this -> strings['register_on_board_done_1'] = 'Gratulacje! Twoje konto zostało założone i możesz już się zalogować na naszym forum.';
$this -> strings['register_on_board_done_2'] = 'Twoje konto zostało założone lecz wymaga aktywacji. Na podany przez ciebie adres e-mail została wysłana wiadomość, zawierająca instrukcje dotyczące dalszego postępowania.';
$this -> strings['register_on_board_done_3'] = 'Twoje konto zostało założone lecz wymaga aktywacji przez administratora. Poczekaj chwilę, to nie powinno potrwać długo!';

$this -> strings['register_on_board_mail_title'] = "Rejestracja na forum {SITE_NAME}";

$this -> strings['register_on_board_mail_1'] = 'Witaj!

Otrzymujesz tą wiadomość, ponieważ ktoś zarejestrował się na {SITE_NAME} ({SITE_URL}) przy użyciu tego adresu e-mail.

Dane rejestracji

Login: %new_user_login
Hasło: %new_user_pass

Jeśli to nie ty rejestrowałeś się na {SITE_NAME}, prosimy cię o zignorowanie tej wiadomości.';

$this -> strings['register_on_board_mail_2'] = 'Witaj!

Otrzymujesz tą wiadomość, ponieważ ktoś zarejestrował się na {SITE_NAME} ({SITE_URL}) przy użyciu tego adresu e-mail.

Dane rejestracji

Login: %new_user_login
Hasło: %new_user_pass

Aktywacja konta

Aktualnie twoje konto jest nie aktywne. Prosimy cię, abys aktywował je jak najszybciej, klikając na poniższy link. W przeciwnym wypadku zostanie ono skasowane.

%activate_accoun_link

Jeśli to nie ty rejestrowałeś się na {SITE_NAME}, prosimy cię o zignorowanie tej wiadomości.';

$this -> strings['register_on_board_mail_3'] = 'Witaj!

Otrzymujesz tą wiadomość, ponieważ ktoś zarejestrował się na {SITE_NAME} ({SITE_URL}) przy użyciu tego adresu e-mail.

Dane rejestracji

Login: %new_user_login
Hasło: %new_user_pass

Aktywacja konta

Aktualnie twoje konto nie zostało jeszcze aktywowane przez administratora. Prosimy cię, abyś odczekał trochę czasu, nim spróbójesz się zalogować.


Jeśli to nie ty rejestrowałeś się na {SITE_NAME}, prosimy cię o zignorowanie tej wiadomości.';

$this -> strings['register_on_board_ok'] = 'Zarejestruj się';
$this -> strings['register_on_board_activate_inmail_link'] = 'Aktywuj moje konto.';

$this -> strings['register_on_board_guidelines'] = 'Warunki rejestracji na forum';

$this -> strings['register_on_board_bad_step'] = 'Przepraszamy, ale bezpośrednie przejście do tej strony nie jest możliwe.';

$this -> strings['register_on_board_guidelines_decline_message'] = 'Przepraszamy, ale w celu rejestracji na naszym forum musisz zaakceptować regulamin.';

$this -> strings['register_on_board_guidelines_decline'] = 'Nie akceptuję regulaminu';
$this -> strings['register_on_board_guidelines_accept'] = 'Akceptuję regulamin';

/**
 * ACC ACTIVATION
 */

$this -> strings['activate_account'] = 'Aktywacja konta';

$this -> strings['activate_account_for_guests_only'] = 'Twoje konto jest już aktywne.';

$this -> strings['activate_account_user_not_found'] = 'Przepraszamy, ale wskazany użytkownik nie istnieje.';
$this -> strings['activate_account_user_alreadyactive'] = 'Konto tego użytkownika jest już aktywne.';
$this -> strings['activate_account_user_by_dmin_only'] = 'Przepraszamy, ale tylko administrator może aktywować konto tego uzytkownika.';
$this -> strings['activate_account_user_wrong_code'] = 'Klucz aktywujący jes nieprawidłowy.';
$this -> strings['activate_account_done'] = 'Wskazane konto użytkownika zostało aktywowane.';

?>
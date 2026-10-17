<?php
/**
 * Laboratorium 4 - PHP 1
 * Zespół: Artem Hizhovskyi (97882), Ihor Namachynskyi (97889)
 * Grupa dziekańska: 3ID11B
 * Wszystka funkcjonalność w JEDNYM pliku: a1.php
 */

header('Content-Type: text/html; charset=utf-8');

// Plik licznika zgłoszeń
$counter_file = "licznik_zgloszen.dat";

// Inicjalizacja zmiennych
$errors = [];
$nazwisko = '';
$typ_zajec = '';
$jezyk = '';
$data_urodzenia = '';
$pesel = '';
$kursy = [];

// ==============================================================================
// STAN 5 & 4: Komunikaty sukcesu lub anulowania (Parametr GET numer_zlecenia)
// ==============================================================================
if (isset($_GET['numer_zlecenia'])) {
    $nr = htmlspecialchars($_GET['numer_zlecenia']);
    ?>
    <!DOCTYPE html>
    <html lang="pl">
    <head>
        <meta charset="utf-8">
        <title>Formularz PHP</title>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px; margin: 0; background: #fff; }
            h2 { color: #111; font-weight: normal; margin-bottom: 20px; font-size: 22px; }
            .btn { background: #e0e0e0; border: 1px solid #777; padding: 5px 15px; font-size: 14px; cursor: pointer; border-radius: 2px; }
            .btn:hover { background: #ccc; }
        </style>
    </head>
    <body>
        <?php if ($nr === '0'): ?>
            <h2>Anulowano wprowadzenie zgloszenia</h2>
        <?php else: ?>
            <h2>Zgłoszenie zostało zapisane pod numerem <?php echo $nr; ?></h2>
        <?php endif; ?>
        <form action="a1.php" method="get">
            <button type="submit" class="btn">Powrot</button>
        </form>
    </body>
    </html>
    <?php
    exit;
}

// ==============================================================================
// STAN 3: Potwierdzenie zapisu (Kliknięto Zapisz lub Anuluj)
// ==============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_confirm'])) {
    if ($_POST['action_confirm'] === 'Zapisz') {
        $n = trim($_POST['conf_nazwisko']);
        $t = trim($_POST['conf_typ_zajec']);
        $j = trim($_POST['conf_jezyk']);
        $d = trim($_POST['conf_data_urodzenia']);
        $p = trim($_POST['conf_pesel']);
        $k_str = trim($_POST['conf_kursy']);

        // Pobranie i inkrementacja numeru zgłoszenia z blokadą flock (wg sk_05.php)
        $next_num = 1;
        if (file_exists($counter_file)) {
            $fp = fopen($counter_file, "r");
            if ($fp) {
                while (!flock($fp, LOCK_SH)) { usleep(100000); }
                $next_num = (int)fgets($fp, 20) + 1;
                flock($fp, LOCK_UN);
                fclose($fp);
            }
        }
        $fp = fopen($counter_file, "w");
        if ($fp) {
            while (!flock($fp, LOCK_EX)) { usleep(100000); }
            fputs($fp, $next_num);
            flock($fp, LOCK_UN);
            fclose($fp);
        }

        $formatted_num = str_pad($next_num, 3, '0', STR_PAD_LEFT);
        $filename = "z_" . $formatted_num . ".txt";

        // Układ pliku dokładnie wg Rysunku 4 instrukcji
        $file_content = "Nazwisko = " . $n . "\r\n";
        $file_content .= "Typ zajec = " . $t . "\r\n";
        $file_content .= "Jezyk = " . mb_strtoupper($j, 'UTF-8') . "\r\n";
        $file_content .= "Data urodzenia = " . $d . "\r\n";
        $file_content .= "Pesel = " . $p . "\r\n";
        $file_content .= "Kursy = " . $k_str . "\r\n";

        file_put_contents($filename, $file_content);

        header("Location: a1.php?numer_zlecenia=" . $formatted_num);
        exit;
    } else {
        // Anuluj
        header("Location: a1.php?numer_zlecenia=0");
        exit;
    }
}

// ==============================================================================
// STAN 2: Walidacja danych po wysłaniu formularza
// ==============================================================================
$show_confirmation = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_submit'])) {
    $nazwisko = isset($_POST['nazwisko']) ? trim($_POST['nazwisko']) : '';
    $typ_zajec = isset($_POST['typ_zajec']) ? trim($_POST['typ_zajec']) : '';
    $jezyk = isset($_POST['jezyk']) ? trim($_POST['jezyk']) : '';
    $data_urodzenia = isset($_POST['data_urodzenia']) ? trim($_POST['data_urodzenia']) : '';
    $pesel = isset($_POST['pesel']) ? trim($_POST['pesel']) : '';
    $kursy = isset($_POST['kursy']) ? $_POST['kursy'] : [];

    // 1. Walidacja Nazwiska (tylko litery, max 24 znaki, nie puste)
    if (empty($nazwisko)) {
        $errors['nazwisko'] = "* Pole nie może być puste;";
    } elseif (!preg_match('/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ\s\-]{1,24}$/u', $nazwisko)) {
        $errors['nazwisko'] = "* Tylko litery do 24 znaków;";
    }

    // 2. Walidacja Typu zajęć
    if (empty($typ_zajec)) {
        $errors['typ_zajec'] = "*Wybierz opcje";
    }

    // 3. Walidacja Języka
    if (empty($jezyk)) {
        $errors['jezyk'] = "*Wybierz opcje";
    }

    // 4. Walidacja Daty urodzenia (wiek min. 16 lat wg DateTime z sk_8.php)
    if (empty($data_urodzenia)) {
        $errors['data_urodzenia'] = "* Pole nie może być puste;";
    } else {
        try {
            $birth = new DateTime($data_urodzenia);
            $limit16 = new DateTime('-16 years');
            if ($birth > $limit16) {
                $errors['data_urodzenia'] = "*Musisz mieć więcej niż 16 lat";
            }
        } catch (Exception $e) {
            $errors['data_urodzenia'] = "* Niepoprawny format daty;";
        }
    }

    // 5. Walidacja PESEL oraz zgodności z datą urodzenia
    $pesel_errs = [];
    if (empty($pesel)) {
        $pesel_errs[] = "* Pole nie może być puste;";
    } else {
        if (strlen($pesel) !== 11) {
            $pesel_errs[] = "* Ma byc 11 znakow;";
        }
        if (!ctype_digit($pesel)) {
            $pesel_errs[] = "* Tylko cyfry";
        }

        // Sprawdzenie zgodności daty PESEL z datą urodzenia
        if (strlen($pesel) === 11 && ctype_digit($pesel) && !empty($data_urodzenia) && !isset($errors['data_urodzenia'])) {
            $p_rok = (int)substr($pesel, 0, 2);
            $p_mies = (int)substr($pesel, 2, 2);
            $p_dzien = (int)substr($pesel, 4, 2);

            $full_year = 0;
            $real_month = 0;

            if ($p_mies >= 1 && $p_mies <= 12) {
                $full_year = 1900 + $p_rok;
                $real_month = $p_mies;
            } elseif ($p_mies >= 21 && $p_mies <= 32) {
                $full_year = 2000 + $p_rok;
                $real_month = $p_mies - 20;
            } else {
                $pesel_errs[] = "* Błędny miesiąc w PESEL";
            }

            if ($full_year > 0) {
                $pesel_date = sprintf("%04d-%02d-%02d", $full_year, $real_month, $p_dzien);
                if ($pesel_date !== $data_urodzenia) {
                    $pesel_errs[] = "* Niezgodnosc dat";
                }
            }
        }
    }
    if (!empty($pesel_errs)) {
        $errors['pesel'] = implode(" ", $pesel_errs);
    }

    // 6. Walidacja Kursów (co najmniej jeden)
    if (empty($kursy)) {
        $errors['kursy'] = "* Trzeba wybrać choć jeden";
    }

    // Jeśli brak błędów -> przejście do potwierdzenia
    if (empty($errors)) {
        $show_confirmation = true;
    }
}

// ==============================================================================
// WIDOK 1: EKRAN POTWIERDZENIA (Zgodny z Rysunkiem 3 instrukcji)
// ==============================================================================
if ($show_confirmation):
    $kursy_str = implode(" ;  ", $kursy) . " ;";
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Formularz PHP</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; padding: 40px; margin: 0; background: #fff; }
        h1 { font-size: 32px; font-weight: bold; margin-bottom: 25px; color: #000; }
        .btn { background: #e0e0e0; border: 1px solid #777; padding: 6px 16px; font-size: 15px; cursor: pointer; border-radius: 2px; margin-right: 10px; }
        .btn:hover { background: #ccc; }
    </style>
</head>
<body>
    <h1>Wyslac zgoszenie</h1>
    <form action="a1.php" method="post">
        <input type="hidden" name="conf_nazwisko" value="<?php echo htmlspecialchars($nazwisko); ?>">
        <input type="hidden" name="conf_typ_zajec" value="<?php echo htmlspecialchars($typ_zajec); ?>">
        <input type="hidden" name="conf_jezyk" value="<?php echo htmlspecialchars($jezyk); ?>">
        <input type="hidden" name="conf_data_urodzenia" value="<?php echo htmlspecialchars($data_urodzenia); ?>">
        <input type="hidden" name="conf_pesel" value="<?php echo htmlspecialchars($pesel); ?>">
        <input type="hidden" name="conf_kursy" value="<?php echo htmlspecialchars($kursy_str); ?>">

        <button type="submit" name="action_confirm" value="Zapisz" class="btn">Zapisz</button>
        <button type="submit" name="action_confirm" value="Anuluj" class="btn">Anuluj</button>
    </form>
</body>
</html>
<?php
exit;
endif;

// ==============================================================================
// WIDOK 2: GŁÓWNY FORMULARZ (Zgodny z Rysunkiem 1 oraz Rysunkiem 2 z błędami)
// ==============================================================================
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Formularz PHP</title>
    <style>
        body {
            font-family: Arial, Tahoma, sans-serif;
            margin: 30px;
            background: #fff;
            color: #000;
        }
        h2.title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        table.form-table {
            border-collapse: collapse;
            width: 680px;
            margin: 0 auto;
            border: 1px solid #000;
        }
        table.form-table th, table.form-table td {
            border: 1px solid #000;
            padding: 8px 12px;
            vertical-align: middle;
        }
        table.form-table th {
            text-align: center;
            font-size: 15px;
            background-color: #fff;
        }
        .col-label {
            width: 170px;
            font-size: 14px;
        }
        .col-input {
            width: 490px;
        }
        input[type="text"] {
            width: 200px;
            padding: 3px 5px;
            border: 1px solid #777;
        }
        select {
            width: 120px;
            padding: 2px;
        }
        .err-label {
            color: #d90000;
            font-size: 13px;
            margin-left: 8px;
            display: inline;
        }
        .btn-panel {
            padding-top: 6px;
        }
        .btn {
            background: #e6e6e6;
            border: 1px solid #777;
            padding: 3px 14px;
            font-size: 13px;
            cursor: pointer;
            margin-right: 15px;
        }
        .btn:hover { background: #dcdcdc; }
    </style>
</head>
<body>

    <h2 class="title">Formularz z danymi do walidacji PHP</h2>

    <form action="a1.php" method="post">
        <table class="form-table">
            <thead>
                <tr>
                    <th class="col-label">Dane</th>
                    <th class="col-input">Pole do wprowadzenie danych</th>
                </tr>
            </thead>
            <tbody>
                <!-- Nazwisko -->
                <tr>
                    <td class="col-label">Nazwisko</td>
                    <td class="col-input">
                        <input type="text" name="nazwisko" value="<?php echo htmlspecialchars($nazwisko); ?>">
                        <?php if (isset($errors['nazwisko'])): ?>
                            <span class="err-label"><?php echo $errors['nazwisko']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>

                <!-- Typ zajec -->
                <tr>
                    <td class="col-label">Typ zajec</td>
                    <td class="col-input">
                        <select name="typ_zajec">
                            <option value="">-- wybierz --</option>
                            <option value="Stacjonarnie" <?php if ($typ_zajec === 'Stacjonarnie') echo 'selected'; ?>>Stacjonarnie</option>
                            <option value="Zdalne" <?php if ($typ_zajec === 'Zdalne') echo 'selected'; ?>>Zdalne</option>
                        </select>
                        <?php if (isset($errors['typ_zajec'])): ?>
                            <span class="err-label"><?php echo $errors['typ_zajec']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>

                <!-- Język -->
                <tr>
                    <td class="col-label">Język</td>
                    <td class="col-input">
                        <label><input type="radio" name="jezyk" value="Angielski" <?php if ($jezyk === 'Angielski') echo 'checked'; ?>> Angielski</label>
                        &nbsp;
                        <label><input type="radio" name="jezyk" value="Polski" <?php if ($jezyk === 'Polski') echo 'checked'; ?>> Polski</label>
                        <?php if (isset($errors['jezyk'])): ?>
                            <span class="err-label"><?php echo $errors['jezyk']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>

                <!-- Data urodzenia -->
                <tr>
                    <td class="col-label">Data urodzenia</td>
                    <td class="col-input">
                        <input type="date" name="data_urodzenia" value="<?php echo htmlspecialchars($data_urodzenia); ?>">
                        <?php if (isset($errors['data_urodzenia'])): ?>
                            <span class="err-label"><?php echo $errors['data_urodzenia']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>

                <!-- Pesel -->
                <tr>
                    <td class="col-label">Pesel</td>
                    <td class="col-input">
                        <input type="text" name="pesel" maxlength="11" value="<?php echo htmlspecialchars($pesel); ?>">
                        <?php if (isset($errors['pesel'])): ?>
                            <span class="err-label"><?php echo $errors['pesel']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>

                <!-- Kursy do wyboru -->
                <tr>
                    <td class="col-label">Kursy do wyboru</td>
                    <td class="col-input">
                        <label><input type="checkbox" name="kursy[]" value="Programowanie" <?php if (in_array('Programowanie', $kursy)) echo 'checked'; ?>> Programowanie</label>
                        <label><input type="checkbox" name="kursy[]" value="Muzyka" <?php if (in_array('Muzyka', $kursy)) echo 'checked'; ?>> Muzyka</label>
                        <label><input type="checkbox" name="kursy[]" value="Gimnastyka" <?php if (in_array('Gimnastyka', $kursy)) echo 'checked'; ?>> Gimnastyka</label>
                        <label><input type="checkbox" name="kursy[]" value="Taniec" <?php if (in_array('Taniec', $kursy)) echo 'checked'; ?>> Taniec</label>
                        <?php if (isset($errors['kursy'])): ?>
                            <span class="err-label"><?php echo $errors['kursy']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>

                <!-- Przyciski -->
                <tr>
                    <td colspan="2" class="btn-panel">
                        <input type="reset" value="Resetuj" class="btn">
                        <input type="submit" name="btn_submit" value="Prześlij" class="btn">
                    </td>
                </tr>
            </tbody>
        </table>
    </form>

</body>
</html>

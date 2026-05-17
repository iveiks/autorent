<?php
session_start();
include('../config.php');

$msg = "";
$msg_type = "danger";

if (!empty($_POST)) {
    // Kasutaja sisend
    $uname = trim($_POST['user']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $bank_name = trim($_POST['bank_name']);
    $account_number = trim($_POST['account_number']);

    if (empty($uname) || empty($password) || empty($confirm_password) || empty($bank_name) || empty($account_number)) {
        $msg = "Palun täida kõik väljad!";
    } elseif ($password !== $confirm_password) {
        $msg = "Paroolid ei kattu!";
    } else {
        // Kontrollime, kas kasutajanimi on juba olemas kas klientide või adminite tabelis
        $check_stmt = mysqli_prepare($yhendus, "SELECT username FROM clients WHERE username = ? UNION SELECT username FROM users WHERE username = ?");
        if ($check_stmt) {
            mysqli_stmt_bind_param($check_stmt, "ss", $uname, $uname);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);

            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                $msg = "See kasutajanimi on juba võetud!";
            } else {
                // Teeme paroolist turvalise räsi
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                
                // Lisame uue kliendi andmebaasi
                $insert_stmt = mysqli_prepare($yhendus, "INSERT INTO clients (username, password_hash, bank_name, account_number) VALUES (?, ?, ?, ?)");
                if ($insert_stmt) {
                    mysqli_stmt_bind_param($insert_stmt, "ssss", $uname, $hashed_password, $bank_name, $account_number);
                    if (mysqli_stmt_execute($insert_stmt)) {
                        $msg = "Konto on loodud! Nüüd saad sisse logida.";
                        $msg_type = "success";
                        $uname = ""; // Puhastame nime väljalt
                    } else {
                        $msg = "Viga registreerimisel: " . mysqli_error($yhendus);
                    }
                    mysqli_stmt_close($insert_stmt);
                } else {
                    $msg = "Andmebaasi viga (insert): " . mysqli_error($yhendus);
                }
            }
            mysqli_stmt_close($check_stmt);
        } else {
            $msg = "Andmebaasi viga (check): " . mysqli_error($yhendus);
        }
    }
}
?>
<!doctype html>
<html lang="et">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registreerimine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  </head>
  <body>
    <div class="container">
        <div class="row pt-4 mt-4">
            <div class="col-sm-4"></div>
            <div class="col-sm-4">
                <h2 class="mb-4">Kliendiks registreerimine</h2>
                <form method="post" action="register.php" autocomplete="off">
                    <div class="mb-3">
                        <label for="u" class="form-label">Kasutajanimi</label>
                        <input name="user" type="text" class="form-control" id="u" value="<?= htmlspecialchars($uname ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="p" class="form-label">Parool</label>
                        <input name="password" type="password" class="form-control" id="p" required>
                    </div>
                    <div class="mb-3">
                        <label for="cp" class="form-label">Kinnita parool</label>
                        <input name="confirm_password" type="password" class="form-control" id="cp" required>
                    </div>
                    <div class="mb-3">
                        <label for="bn" class="form-label">Panga nimi</label>
                        <input name="bank_name" type="text" class="form-control" id="bn" required placeholder="Näiteks: LHV">
                    </div>
                    <div class="mb-3">
                        <label for="an" class="form-label">Konto number</label>
                        <input name="account_number" type="text" class="form-control" id="an" required>
                    </div>
                    <div class="d-flex mb-3">
                        <button type="submit" class="btn btn-primary me-2 flex-grow-1">Registreeri</button>
                        <a href="../index.php" class="btn btn-dark">Tagasi</a>
                    </div>
                </form>
                <?= $msg ? "<div class='alert alert-$msg_type'>$msg</div>" : ""; ?>
                <p class="mt-3">Oled juba klient? <a href="login.php">Logi sisse</a></p>
            </div>
            <div class="col-sm-4"></div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
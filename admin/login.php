<?php 
session_start(); 
include('../config.php');

$msg = "";
if (!empty($_POST)) {

    // kasutaja vormist
    $uname = trim($_POST['user']);
    $password = trim($_POST['password']);

    // 1. Kontrollime adminite tabelist
    $stmt = mysqli_prepare($yhendus, "SELECT username, password_hash FROM users WHERE username = ?");
     if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $uname);
        mysqli_stmt_execute($stmt); 
        $valjund = mysqli_stmt_get_result($stmt);
        $rida = mysqli_fetch_assoc($valjund);
        mysqli_stmt_close($stmt);
        
        if ($rida && password_verify($password, $rida['password_hash'])) {
            $_SESSION['tuvastamine'] = $rida['username'];
            $_SESSION['roll'] = 'admin';
            header("Location: admin.php");
            exit();
        }
    }

    // 2. Kui admini ei leitud, kontrollime klientide tabelist
    $stmt = mysqli_prepare($yhendus, "SELECT username, password_hash FROM clients WHERE username = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $uname);
        mysqli_stmt_execute($stmt); 
        $valjund = mysqli_stmt_get_result($stmt);
        $rida = mysqli_fetch_assoc($valjund);
        mysqli_stmt_close($stmt);

        if ($rida && password_verify($password, $rida['password_hash'])) {
            $_SESSION['tuvastamine'] = $rida['username'];
            $_SESSION['roll'] = 'client';
            header("Location: ../index.php");
            exit();
        } else {
            $msg = '<div class="alert alert-danger">Sisestasid valed andmed!</div>';
        }
    } else {
        $msg = "Süsteemi viga: sisselogimine pole hetkel võimalik. (" . htmlspecialchars(mysqli_error($yhendus)) . ")";
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  </head>
  <body>
    <div class="container">
        <div class="row pt-4 mt-4">
            <div class="col-sm-4"></div>
            <div class="col-sm-4">
                <form method="post" action="login.php" autocomplete="off">
                    <div class="mb-3">
                        <label for="u" class="form-label">User name</label>
                        <input name="user" type="text" class="form-control" id="u" value="<?= htmlspecialchars($uname ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="p" class="form-label">Password</label>
                        <input name="password" type="password" class="form-control" id="p">
                    </div>
                    <div class="d-flex mb-3">
                        <button type="submit" class="btn btn-primary me-2 flex-grow-1">Logi sisse</button>
                        <a href="../index.php" class="btn btn-dark">Esilehele</a>
                    </div>
                </form>
                <?= $msg; ?>
                
            </div>
            <div class="col-sm-4"></div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
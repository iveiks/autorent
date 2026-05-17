<?php session_start(); ?>
<?php
if (!isset($_SESSION['roll']) || $_SESSION['roll'] !== 'client') {
    header("Location: index.php");
    exit();
}
?>
<?php include('config.php'); ?>
<?php include('header.php'); ?>

<div class="container">
    <h2 class="mb-4">Minu profiil</h2>

    <?php
    // Display success/error messages
    if (isset($_GET['success'])) {
        $message = '';
        if ($_GET['success'] === 'password_updated') {
            $message = 'Parool edukalt muudetud!';
        } elseif ($_GET['success'] === 'profile_updated') {
            $message = 'Profiili andmed edukalt uuendatud!';
        }
        echo '<div class="alert alert-success">' . $message . '</div>';
    }
    if (isset($_GET['error'])) {
        $message = '';
        if ($_GET['error'] === 'password_mismatch') {
            $message = 'Uued paroolid ei kattu!';
        } elseif ($_GET['error'] === 'current_password_incorrect') {
            $message = 'Praegune parool on vale!';
        } elseif ($_GET['error'] === 'empty_fields') {
            $message = 'Palun täida kõik vajalikud väljad!';
        } elseif ($_GET['error'] === 'db_error') {
            $message = 'Andmebaasi viga profiili uuendamisel.';
        }
        echo '<div class="alert alert-danger">' . $message . '</div>';
    }

    $username = $_SESSION['tuvastamine'];
    $client_data = [];

    $stmt = mysqli_prepare($yhendus, "SELECT username, bank_name, account_number FROM clients WHERE username = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $client_data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    } else {
        echo '<div class="alert alert-danger">Andmebaasi viga kliendi andmete laadimisel: ' . htmlspecialchars(mysqli_error($yhendus)) . '</div>';
    }
    ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    Profiili andmed
                </div>
                <div class="card-body">
                    <form action="update_profile.php" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Kasutajanimi</label>
                            <input type="text" class="form-control" id="username" value="<?= htmlspecialchars($client_data['username'] ?? ''); ?>" readonly disabled>
                        </div>
                        <div class="mb-3">
                            <label for="bank_name" class="form-label">Panga nimi</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?= htmlspecialchars($client_data['bank_name'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="account_number" class="form-label">Konto number</label>
                            <input type="text" class="form-control" id="account_number" name="account_number" value="<?= htmlspecialchars($client_data['account_number'] ?? ''); ?>" required>
                        </div>
                        <button type="submit" name="update_info" class="btn btn-primary">Uuenda andmeid</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    Muuda parooli
                </div>
                <div class="card-body">
                    <form action="update_profile.php" method="post">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Praegune parool</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Uus parool</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_new_password" class="form-label">Kinnita uus parool</label>
                            <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-warning">Muuda parooli</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
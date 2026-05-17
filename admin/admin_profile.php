<?php session_start(); ?>
<?php
if (!isset($_SESSION['roll']) || $_SESSION['roll'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
?>
<?php include('../config.php'); ?>
<?php include('../header.php'); ?>

<div class="container">
    <h2 class="mb-4">Admini profiil</h2>

    <?php
    if (isset($_GET['success'])) {
        echo '<div class="alert alert-success">Parool edukalt muudetud!</div>';
    }
    if (isset($_GET['error'])) {
        $message = '';
        if ($_GET['error'] === 'password_mismatch') {
            $message = 'Uued paroolid ei kattu!';
        } elseif ($_GET['error'] === 'current_password_incorrect') {
            $message = 'Praegune parool on vale!';
        } elseif ($_GET['error'] === 'empty_fields') {
            $message = 'Palun täida kõik väljad!';
        } elseif ($_GET['error'] === 'db_error') {
            $message = 'Andmebaasi viga parooli uuendamisel.';
        }
        echo '<div class="alert alert-danger">' . $message . '</div>';
    }
    ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">Minu andmed</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Kasutajanimi</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['tuvastamine']); ?>" readonly disabled>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-warning">Muuda parooli</div>
                <div class="card-body">
                    <form action="update_admin_profile.php" method="post">
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
                        <button type="submit" name="change_password" class="btn btn-warning w-100">Muuda parooli</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
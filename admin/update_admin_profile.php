<?php
session_start();
include('../config.php');

if (!isset($_SESSION['roll']) || $_SESSION['roll'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$username = $_SESSION['tuvastamine'];

if (isset($_POST['change_password'])) {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_new_password = trim($_POST['confirm_new_password']);

    if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
        header("Location: admin_profile.php?error=empty_fields");
        exit();
    }

    if ($new_password !== $confirm_new_password) {
        header("Location: admin_profile.php?error=password_mismatch");
        exit();
    }

    $stmt = mysqli_prepare($yhendus, "SELECT id, password_hash FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$user_data || !password_verify($current_password, $user_data['password_hash'])) {
        header("Location: admin_profile.php?error=current_password_incorrect");
        exit();
    }

    $hashed_new_password = password_hash($new_password, PASSWORD_BCRYPT);
    $stmt_upd = mysqli_prepare($yhendus, "UPDATE users SET password_hash = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt_upd, "si", $hashed_new_password, $user_data['id']);
    
    if (mysqli_stmt_execute($stmt_upd)) {
        header("Location: admin_profile.php?success=1");
    } else {
        header("Location: admin_profile.php?error=db_error");
    }
    mysqli_stmt_close($stmt_upd);
    exit();
}
header("Location: admin_profile.php");
exit();
?>
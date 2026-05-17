<?php
session_start();
include('config.php');

if (!isset($_SESSION['roll']) || $_SESSION['roll'] !== 'client') {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['tuvastamine'];
$client_id = null;
$stored_password_hash = null;

// Get client_id and current password hash
$stmt = mysqli_prepare($yhendus, "SELECT id, password_hash FROM clients WHERE username = ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $client_data = mysqli_fetch_assoc($result);
    $client_id = $client_data['id'] ?? null;
    $stored_password_hash = $client_data['password_hash'] ?? null;
    mysqli_stmt_close($stmt);
}

if (!$client_id) {
    header("Location: client_profile.php?error=db_error");
    exit();
}

if (isset($_POST['update_info'])) {
    $bank_name = trim($_POST['bank_name']);
    $account_number = trim($_POST['account_number']);

    if (empty($bank_name) || empty($account_number)) {
        header("Location: client_profile.php?error=empty_fields");
        exit();
    }

    $stmt = mysqli_prepare($yhendus, "UPDATE clients SET bank_name = ?, account_number = ? WHERE id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssi", $bank_name, $account_number, $client_id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: client_profile.php?success=profile_updated");
            exit();
        } else {
            header("Location: client_profile.php?error=db_error");
            exit();
        }
        mysqli_stmt_close($stmt);
    } else {
        header("Location: client_profile.php?error=db_error");
        exit();
    }
}

if (isset($_POST['change_password'])) {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_new_password = trim($_POST['confirm_new_password']);

    if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
        header("Location: client_profile.php?error=empty_fields");
        exit();
    }

    if ($new_password !== $confirm_new_password) {
        header("Location: client_profile.php?error=password_mismatch");
        exit();
    }

    if (!password_verify($current_password, $stored_password_hash)) {
        header("Location: client_profile.php?error=current_password_incorrect");
        exit();
    }

    $hashed_new_password = password_hash($new_password, PASSWORD_BCRYPT);

    $stmt = mysqli_prepare($yhendus, "UPDATE clients SET password_hash = ? WHERE id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $hashed_new_password, $client_id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: client_profile.php?success=password_updated");
            exit();
        } else {
            header("Location: client_profile.php?error=db_error");
            exit();
        }
        mysqli_stmt_close($stmt);
    } else {
        header("Location: client_profile.php?error=db_error");
        exit();
    }
}

// If no specific action was taken, redirect to profile page
header("Location: client_profile.php");
exit();
?>
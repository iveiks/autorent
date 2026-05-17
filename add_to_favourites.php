<?php
session_start();
include('config.php');

// Ainult sisselogitud kliendid saavad seda kasutada
if (!isset($_SESSION['roll']) || $_SESSION['roll'] !== 'client' || !isset($_GET['car_id'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['tuvastamine'];
$car_id = intval($_GET['car_id']);

// 1. Leiame kliendi ID kasutajanime põhjal
$stmt = mysqli_prepare($yhendus, "SELECT id FROM clients WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$client = mysqli_fetch_assoc($result);
$client_id = $client['id'];
mysqli_stmt_close($stmt);

// 2. Kontrollime, kas see auto on juba lemmikutes
$stmt = mysqli_prepare($yhendus, "SELECT id FROM favourites WHERE client_id = ? AND car_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $client_id, $car_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    // On juba olemas - eemaldame (toggle)
    $stmt_toggle = mysqli_prepare($yhendus, "DELETE FROM favourites WHERE client_id = ? AND car_id = ?");
} else {
    // Ei ole olemas - lisame
    $stmt_toggle = mysqli_prepare($yhendus, "INSERT INTO favourites (client_id, car_id) VALUES (?, ?)");
}

mysqli_stmt_bind_param($stmt_toggle, "ii", $client_id, $car_id);
mysqli_stmt_execute($stmt_toggle);

$redirect = 'index.php';
if (isset($_GET['redirect']) && $_GET['redirect'] === 'favourites') {
    $redirect = 'favourites.php';
}

header("Location: " . $redirect);
exit();
?>
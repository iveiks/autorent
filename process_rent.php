<?php
session_start();
include('config.php');

if (!isset($_SESSION['roll']) || $_SESSION['roll'] !== 'client' || empty($_POST['car_id'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['tuvastamine'];
$car_id = intval($_POST['car_id']);
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

// Kuupäevade kontroll
if (strtotime($start_date) > strtotime($end_date)) {
    die("Viga: Alguskuupäev ei saa olla hilisem kui lõppkuupäev.");
}

// 1. Leiame kliendi ID
$stmt = mysqli_prepare($yhendus, "SELECT id FROM clients WHERE username = ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $client_row = mysqli_fetch_assoc($res);
    $client_id = $client_row['id'] ?? null;
    mysqli_stmt_close($stmt);
}

if (!$client_id) {
    die("Kliendi andmeid ei leitud.");
}

// 2. Kontrollime, kas kliendil on juba aktiivne rent
// Reegel: Klient ei saa samal perioodil rentida teist autot
$stmt = mysqli_prepare($yhendus, "SELECT id FROM rentals WHERE client_id = ? AND status = 'active' AND start_date <= ? AND end_date >= ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "iss", $client_id, $end_date, $start_date);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($res) > 0) {
        die("Sul on valitud perioodil juba teine auto renditud! Vali teine aeg.");
    }
    mysqli_stmt_close($stmt);
} else {
    die("Andmebaasi viga (rentals kontroll): " . mysqli_error($yhendus));
}

// 3. Kontrollime, kas auto on veel vaba ja saame ööpäeva hinna
$stmt = mysqli_prepare($yhendus, "SELECT status, price FROM cars WHERE id = ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $car_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $car_row = mysqli_fetch_assoc($res);
    $car_status = $car_row['status'] ?? null;
    $daily_price = $car_row['price'] ?? 0;
    mysqli_stmt_close($stmt);

    if ($car_status === 'hoolduses') {
        die("See auto on hetkel hoolduses ja seda ei saa rentida.");
    }
} else {
    die("Andmebaasi viga (auto kontroll): " . mysqli_error($yhendus));
}

// Kontrollime, kas auto on sellel perioodil juba kellegi teise poolt broneeritud
$stmt_overlap = mysqli_prepare($yhendus, "SELECT id FROM rentals WHERE car_id = ? AND status = 'active' AND start_date <= ? AND end_date >= ?");
if ($stmt_overlap) {
    mysqli_stmt_bind_param($stmt_overlap, "iss", $car_id, $end_date, $start_date);
    mysqli_stmt_execute($stmt_overlap);
    $res_overlap = mysqli_stmt_get_result($stmt_overlap);
    if (mysqli_num_rows($res_overlap) > 0) {
        die("See auto on valitud perioodil juba välja renditud. Palun vali teised kuupäevad.");
    }
    mysqli_stmt_close($stmt_overlap);
} else {
    die("Andmebaasi viga (saadavuse kontroll): " . mysqli_error($yhendus));
}

// Arvutame rendiperioodi pikkuse ja koguhinna
$d1 = strtotime($start_date);
$d2 = strtotime($end_date);
$diff = $d2 - $d1;
$days = round($diff / (60 * 60 * 24));
if ($days <= 0) $days = 1; // Minimaalselt 1 päev renti

$total_price = $days * $daily_price;

// 4. Teostame rentimise (Andmebaasi transaktsioon oleks siin hea, aga teeme lihtsustatult)
$rent_stmt = mysqli_prepare($yhendus, "INSERT INTO rentals (client_id, car_id, start_date, end_date, total_price, status) VALUES (?, ?, ?, ?, ?, 'active')");
if ($rent_stmt) {
    mysqli_stmt_bind_param($rent_stmt, "iissi", $client_id, $car_id, $start_date, $end_date, $total_price);

    if (mysqli_stmt_execute($rent_stmt)) {
        // Kui rent algab täna, märgime auto staatuse broneerituks
        $today = date('Y-m-d');
        if ($start_date <= $today && $end_date >= $today) {
            $update_stmt = mysqli_prepare($yhendus, "UPDATE cars SET status = 'broneeritud' WHERE id = ?");
            if ($update_stmt) {
                mysqli_stmt_bind_param($update_stmt, "i", $car_id);
                mysqli_stmt_execute($update_stmt);
                mysqli_stmt_close($update_stmt);
            }
        }
        header("Location: my_rentals.php?success=1");
        exit();
    } else {
        echo "Viga rentimisel: " . mysqli_error($yhendus);
    }
    mysqli_stmt_close($rent_stmt);
} else {
    die("Andmebaasi viga (rentimise sisestamine): " . mysqli_error($yhendus));
}
?>
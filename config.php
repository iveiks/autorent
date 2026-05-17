<?php
    // Sinu andmed
    $db_server = 'db';
    $db_andmebaas = 'car_rent';
    $db_kasutaja = 'boss';
    $db_salasona = 'Passw0rd';

    // Ühendus andmebaasiga
    $yhendus = mysqli_connect($db_server, $db_kasutaja, $db_salasona, $db_andmebaas);

    // Ühenduse kontroll
    if (!$yhendus) {
        die('Ei saa ühendust andmebaasiga');
    }
?>
<?php
    // Kasutame Dockeri keskkonnamuutujaid või vaikeväärtusi
    $db_server = getenv('DB_HOST') ?: 'db';
    $db_andmebaas = getenv('DB_NAME') ?: 'car_rent';
    $db_kasutaja = getenv('DB_USER') ?: 'boss';
    $db_salasona = getenv('DB_PASS') ?: 'Passw0rd';

    // Ühenduse loomine koos korduskatsetega (kuni 5 korda)
    $yhendus = false;
    $attempts = 0;
    
    while ($attempts < 5) {
        $yhendus = @mysqli_connect($db_server, $db_kasutaja, $db_salasona, $db_andmebaas);
        if ($yhendus) break;
        
        $attempts++;
        sleep(2); // Ootame 2 sekundit enne uut katset
    }

    // Ühenduse kontroll
    if (!$yhendus) {
        die('Viga: Andmebaasiga ei saanud ühendust. Kontrolli, kas DB konteiner töötab. ' . mysqli_connect_error());
    }
?>
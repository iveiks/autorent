<?php
// generate_hash.php

$password_to_hash = 'Passw0rd'; // Change this to the password you want to hash
$hashed_password = password_hash($password_to_hash, PASSWORD_BCRYPT);

echo "Original Password: " . htmlspecialchars($password_to_hash) . "<br>";
echo "Hashed Password: " . htmlspecialchars($hashed_password) . "<br>";
echo "Length of Hash: " . strlen($hashed_password) . "<br>";

if (password_verify($password_to_hash, $hashed_password)) {
    echo "Verification successful!";
} else {
    echo "Verification failed!";
}
?>
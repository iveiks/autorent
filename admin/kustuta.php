<?php include('../config.php'); ?>
<?php
    if (!empty($_GET['delid'])) {
        $id = $_GET['delid'];
        $paring = "DELETE FROM cars WHERE id=$id";
        $valjund = mysqli_query($yhendus, $paring);
        if ($valjund) {
            echo "Kustutatud";
            header("Location: index.php");
        } else {
            echo "Urror";
        }
    }

    
?>
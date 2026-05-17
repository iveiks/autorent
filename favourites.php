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
    <h2 class="mb-4">Minu lemmikud</h2>
    <div class="row row-cols-1 row-cols-md-4 g-4">
        <?php
        // Paginatsiooni seaded
        $limit = 8; // Mitu autot kuvatakse lehel
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        $username = $_SESSION['tuvastamine'];
        
        // Koguarvu päring lehekülgede arvutamiseks
        $count_stmt = mysqli_prepare($yhendus, "SELECT COUNT(*) as total FROM favourites f JOIN clients c ON f.client_id = c.id WHERE c.username = ?");
        if ($count_stmt) {
            mysqli_stmt_bind_param($count_stmt, "s", $username);
            mysqli_stmt_execute($count_stmt);
            $count_res = mysqli_stmt_get_result($count_stmt);
            $total_rows = mysqli_fetch_assoc($count_res)['total'];
            mysqli_stmt_close($count_stmt);
        } else {
            $total_rows = 0;
        }
        $total_pages = ceil($total_rows / $limit);

        // Andmete päring paginatsiooniga
        $stmt = mysqli_prepare($yhendus, "SELECT cars.* FROM cars JOIN favourites ON cars.id = favourites.car_id JOIN clients ON favourites.client_id = clients.id WHERE clients.username = ? LIMIT ? OFFSET ?");
        $valjund = []; // Initialize $valjund as an empty array
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sii", $username, $limit, $offset);
            mysqli_stmt_execute($stmt);
            $valjund = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($valjund) == 0 && $page == 1) { // Only show message if no favorites on first page
                echo '<div class="col-12"><div class="alert alert-info">Sul ei ole veel ühtegi lemmikut valitud või lemmikuid ei leitud.</div></div>';
            } elseif (mysqli_num_rows($valjund) == 0 && $page > 1) { // Redirect if page is out of bounds
                header("Location: favourites.php");
                exit();
            }
        } else {
            // Handle error if mysqli_prepare fails for the main query
            echo '<div class="col-12"><div class="alert alert-danger">Andmebaasi päringu viga: ' . htmlspecialchars(mysqli_error($yhendus)) . '</div></div>';
            // Ensure $valjund is an iterable object for the while loop
            $valjund = mysqli_query($yhendus, "SELECT * FROM cars WHERE 0"); // Return empty result set
        }
        

        while ($rida = mysqli_fetch_assoc($valjund)) {
        ?>
            <div class="col">
                <div class="card h-100">
                    <img src="https://loremflickr.com/400/250/<?php echo str_replace(" ", "", $rida["mark"]); ?>" class="card-img-top" alt="<?php echo $rida["mark"]; ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title"><?php echo $rida["mark"]; ?> <?php echo $rida["model"]; ?></h5>
                            <!-- In this page, the icon is always bi-heart-fill. Clicking it triggers the removal toggle. -->
                            <a href="add_to_favourites.php?car_id=<?= $rida['id']; ?>&redirect=favourites" class="text-danger fs-4">
                                <i class="bi bi-heart-fill"></i>
                            </a>
                        </div>
                        <p class="card-text">
                            Mootor: <?php echo $rida["engine"]; ?> <br>
                            Kütus: <?php echo $rida["fuel"]; ?><br>
                            Hind: <?php echo $rida["price"]; ?>€/päev<br>
                        </p>
                        <a href="single_car.php?id=<?php echo $rida["id"]; ?>" class="btn btn-dark w-100">Rendi</a>
                    </div>
                </div>
            </div>
        <?php }
        mysqli_stmt_close($stmt);
        ?>
    </div>

    <!-- paginatsiooni nupud -->
    <div class="d-flex justify-content-center mt-4 mb-5">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link bg-dark text-white me-2" href="?page=<?= $page - 1 ?>">Eelmine</a></li>
                <?php endif; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link bg-dark text-white" href="?page=<?= $page + 1 ?>">Järgmine</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
<?php session_start(); ?>
<?php include('config.php'); ?>
<?php include('header.php'); ?>

<!-- sisu -->
<div class="container">
    <div class="row row-cols-1 row-cols-md-4 g-4">
<!-- üks auto -->
<?php
    // Paginatsiooni seaded
    $limit = 8;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $limit;

    $otsi_param = "";
    if (!empty($_GET["otsi"])) {
        $otsing = "%" . $_GET["otsi"] . "%";
        $otsi_param = "&otsi=" . urlencode($_GET["otsi"]);

        // Andmete päring
        $stmt = mysqli_prepare($yhendus, "SELECT * FROM cars WHERE mark LIKE ? LIMIT ? OFFSET ?");
        mysqli_stmt_bind_param($stmt, "sii", $otsing, $limit, $offset);
        mysqli_stmt_execute($stmt);
        $valjund = mysqli_stmt_get_result($stmt);

        // Koguarvu päring lehekülgede arvutamiseks
        $count_stmt = mysqli_prepare($yhendus, "SELECT COUNT(*) as total FROM cars WHERE mark LIKE ?");
        mysqli_stmt_bind_param($count_stmt, "s", $otsing);
        mysqli_stmt_execute($count_stmt);
        $count_res = mysqli_stmt_get_result($count_stmt);
        $total_rows = mysqli_fetch_assoc($count_res)['total'];
    } else {
        $paring = "SELECT * FROM cars LIMIT $limit OFFSET $offset";
        $valjund = mysqli_query($yhendus, $paring);

        $count_res = mysqli_query($yhendus, "SELECT COUNT(*) as total FROM cars");
        $total_rows = mysqli_fetch_assoc($count_res)['total'];
    }
    $total_pages = ceil($total_rows / $limit);

    // Teeme nimekirja lemmikautode ID-dest, et saaksime õiget ikooni kuvada
    $fav_ids = [];
    if (isset($_SESSION['roll']) && $_SESSION['roll'] === 'client') {
        $stmt_fav = mysqli_prepare($yhendus, "SELECT car_id FROM favourites f JOIN clients c ON f.client_id = c.id WHERE c.username = ?");
        if ($stmt_fav) {
            mysqli_stmt_bind_param($stmt_fav, "s", $_SESSION['tuvastamine']);
            mysqli_stmt_execute($stmt_fav);
            $res_fav = mysqli_stmt_get_result($stmt_fav);
            while($f = mysqli_fetch_assoc($res_fav)) $fav_ids[] = $f['car_id'];
            mysqli_stmt_close($stmt_fav);
        }
    }

    while($rida = mysqli_fetch_assoc($valjund)){       //sikutan vastuse alla
        // var_dump($rida);                            //kuvan testvastuse
?>
    <div class="col">
        <div class="card">
        <img src="https://loremflickr.com/400/250/<?php echo str_replace(" ","", $rida["mark"]); ?>" class="card-img-top" alt="<?php echo $rida["mark"]; ?>">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <h5 class="card-title"><?php echo $rida["mark"]; ?> <?php echo $rida["model"]; ?></h5>
                <?php if (isset($_SESSION['roll']) && $_SESSION['roll'] === 'client'): ?>
                    <?php $icon = in_array($rida['id'], $fav_ids) ? 'bi-heart-fill' : 'bi-heart'; ?>
                    <a href="add_to_favourites.php?car_id=<?= $rida['id']; ?>" class="text-danger fs-4">
                        <i class="bi <?= $icon; ?>"></i>
                    </a>
                <?php endif; ?>
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
    <?php } ?>
        <!-- /üks auto -->
    </div>

    <!-- paginatsiooni nupud -->
    <div class="d-flex justify-content-center mt-4 mb-5">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link bg-dark text-white me-2" href="?page=<?= $page - 1 ?><?= $otsi_param ?>">Eelmine</a></li>
                <?php endif; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link bg-dark text-white" href="?page=<?= $page + 1 ?><?= $otsi_param ?>">Järgmine</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>
<!-- /sisu -->

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
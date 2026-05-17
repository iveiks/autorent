<?php
session_start();
if (!isset($_SESSION['roll']) || $_SESSION['roll'] !== 'admin') {
  header('Location: login.php');
  exit();
  }
?>

<?php include('../config.php'); ?>
<?php include('../header.php'); ?>

<!-- sisu -->
<div class="container">
    <h2>Admini ala</h2>
<?php
    // sõnumi kuvamine
    if(isset($_GET['msg'])){
      echo '<div class="alert alert-success" role="alert"> Kõik on hästi! </div>';
    }


  //autode kuvamine
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

    // Aktiivsete rentimiste päring (täna on perioodi sees)
    $active_rentals_paring = "SELECT r.*, c.mark, c.model, cl.username FROM rentals r JOIN cars c ON r.car_id = c.id JOIN clients cl ON r.client_id = cl.id WHERE r.status = 'active' AND CURRENT_DATE BETWEEN r.start_date AND r.end_date ORDER BY r.start_date ASC";
    $active_rentals_valjund = mysqli_query($yhendus, $active_rentals_paring);

    // Tulevaste broneeringute päring (algus on tulevikus)
    $future_bookings_paring = "SELECT r.*, c.mark, c.model, cl.username FROM rentals r JOIN cars c ON r.car_id = c.id JOIN clients cl ON r.client_id = cl.id WHERE r.status = 'active' AND r.start_date > CURRENT_DATE ORDER BY r.start_date ASC";
    $future_bookings_valjund = mysqli_query($yhendus, $future_bookings_paring);

?>

<div class="card mb-5">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Autopargi haldus</h4>
        <a href="lisa.php" class="btn btn-success btn-sm">+ Lisa auto</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
<table class="table">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Mark</th>
      <th scope="col">Mudel</th>
      <th scope="col">Kustuta</th>
      <th scope="col">Muuda</th>
    </tr>
  </thead>
  <tbody>
    <?php
        while($rida = mysqli_fetch_assoc($valjund)){       //sikutan vastuse alla
            // var_dump($rida);                       //kuvan testvastuse
    ?>
    <tr>
      <th scope="row"><?php echo $rida["id"]; ?></th>
      <td><?php echo $rida["mark"]; ?></td>
      <td><?php echo $rida["model"]; ?></td>
      <td><a href="kustuta.php?delid=<?= $rida["id"]; ?>" class="btn btn-danger">Kustuta</a></td>
      <td><a href="muuda.php?editid=<?= $rida["id"]; ?>" class="btn btn-warning">Muuda</a></td>
    </tr>

    <?php } ?>

  </tbody>
</table>
      </div>
    </div>
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

    <!-- Aktiivsed rendid -->
    <div class="card mb-5 border-success">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Hetkel väljas (Aktiivsed rendid)</h4>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Auto</th>
                        <th>Klient</th>
                        <th>Periood</th>
                        <th>Summa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($rida = mysqli_fetch_assoc($active_rentals_valjund)): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($rida['mark'] . ' ' . $rida['model']); ?></strong></td>
                            <td><?= htmlspecialchars($rida['username']); ?></td>
                            <td><?= $rida['start_date']; ?> kuni <?= $rida['end_date']; ?></td>
                            <td><?= $rida['total_price']; ?> €</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tulevased broneeringud -->
    <div class="card mb-5 border-info">
        <div class="card-header bg-info text-dark">
            <h4 class="mb-0">Tulevased broneeringud</h4>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Auto</th>
                        <th>Klient</th>
                        <th>Alguskuupäev</th>
                        <th>Summa</th>
                        <th>Tegevus</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($rida = mysqli_fetch_assoc($future_bookings_valjund)): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($rida['mark'] . ' ' . $rida['model']); ?></strong></td>
                            <td><?= htmlspecialchars($rida['username']); ?></td>
                            <td><?= $rida['start_date']; ?></td>
                            <td><?= $rida['total_price']; ?> €</td>
                            <td>
                                <a href="admin_cancel_rental.php?rental_id=<?= $rida['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Oled kindel, et soovid selle broneeringu tühistada?')">Tühista</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

<!-- /sisu -->

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
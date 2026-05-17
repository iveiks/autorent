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
    <h2 class="mb-4">Minu rendiajalugu</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Auto rentimine õnnestus! Sõitke turvaliselt.</div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Auto</th>
                    <th>Alguskuupäev</th>
                    <th>Lõppkuupäev</th>
                    <th>Summa</th>
                    <th>Staatus</th>
                    <th>Tegevus</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Paginatsiooni seaded
                $limit = 10;
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                if ($page < 1) $page = 1;
                $offset = ($page - 1) * $limit;

                $username = $_SESSION['tuvastamine'];

                // Koguarvu päring lehekülgede arvutamiseks
                $count_stmt = mysqli_prepare($yhendus, "SELECT COUNT(*) as total FROM rentals r JOIN clients c ON r.client_id = c.id WHERE c.username = ?");
                mysqli_stmt_bind_param($count_stmt, "s", $username);
                mysqli_stmt_execute($count_stmt);
                $count_res = mysqli_stmt_get_result($count_stmt);
                $total_rows = mysqli_fetch_assoc($count_res)['total'];
                mysqli_stmt_close($count_stmt);
                
                $total_pages = ceil($total_rows / $limit);

                // Andmete päring (liidame autod ja kliendid)
                $stmt = mysqli_prepare($yhendus, "SELECT cars.mark, cars.model, r.start_date, r.end_date, r.total_price, r.id as rental_id, r.car_id, r.status as rental_status FROM rentals r JOIN cars ON r.car_id = cars.id JOIN clients c ON r.client_id = c.id WHERE c.username = ? ORDER BY r.end_date DESC LIMIT ? OFFSET ?");
                $valjund = false; // Initialize to false
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sii", $username, $limit, $offset);
                    mysqli_stmt_execute($stmt);
                    $valjund = mysqli_stmt_get_result($stmt);
                } else {
                    echo '<tr><td colspan="5" class="text-center py-4 text-danger">Andmebaasi päringu viga: ' . htmlspecialchars(mysqli_error($yhendus)) . '</td></tr>';
                    $valjund = mysqli_query($yhendus, "SELECT * FROM cars WHERE 0"); // Return empty result set to prevent further errors
                }

                if (mysqli_num_rows($valjund) == 0) {
                    echo '<tr><td colspan="6" class="text-center py-4 text-muted">Sul pole veel ühtegi rendiajalugu.</td></tr>';
                }

                while ($rida = mysqli_fetch_assoc($valjund)) {
                    $rental_status_text = '';
                    $status_badge_class = '';
                    $cancel_button = '';

                    if ($rida['rental_status'] === 'active') {
                        $today = strtotime(date('Y-m-d'));
                        $start = strtotime($rida['start_date']);
                        $end = strtotime($rida['end_date']);

                        if ($start > $today) {
                            $rental_status_text = 'Broneeritud';
                            $status_badge_class = 'bg-info';
                            $cancel_button = '<a href="cancel_rental.php?rental_id=' . $rida['rental_id'] . '&car_id=' . $rida['car_id'] . '" class="btn btn-sm btn-danger">Tühista rent</a>';
                        } elseif ($today >= $start && $today <= $end) {
                            $rental_status_text = 'Aktiivne';
                            $status_badge_class = 'bg-success';
                            $cancel_button = '<a href="cancel_rental.php?rental_id=' . $rida['rental_id'] . '&car_id=' . $rida['car_id'] . '" class="btn btn-sm btn-danger">Tühista rent</a>';
                        } else {
                            $rental_status_text = 'Lõppenud';
                            $status_badge_class = 'bg-secondary';
                        }
                    } elseif ($rida['rental_status'] === 'cancelled') {
                        $rental_status_text = 'Tühistatud';
                        $status_badge_class = 'bg-danger';
                    } elseif ($rida['rental_status'] === 'completed') {
                        $rental_status_text = 'Lõppenud';
                        $status_badge_class = 'bg-secondary';
                    }

                    $status_badge = '<span class="badge ' . $status_badge_class . '">' . $rental_status_text . '</span>';
                ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($rida['mark'] . ' ' . $rida['model']); ?></strong></td>
                        <td><?php echo $rida['start_date']; ?></td>
                        <td><?php echo $rida['end_date']; ?></td>
                        <td><strong><?php echo $rida['total_price']; ?> €</strong></td>
                        <td><?php echo $status_badge; ?></td>
                        <td><?php echo $cancel_button; ?></td>
                    </tr>
                <?php } 
                mysqli_stmt_close($stmt);
                ?>
            </tbody>
        </table>
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
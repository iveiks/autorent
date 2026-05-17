<?php session_start(); ?>
<?php include('config.php'); ?>
<?php include('header.php'); ?>

<!-- sisu -->
<div class="container">
    <div class="row">

        <?php
        $id = intval($_GET['id']);
        $stmt = mysqli_prepare($yhendus, "SELECT * FROM cars WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $valjund = mysqli_stmt_get_result($stmt);
        $rida = mysqli_fetch_assoc($valjund);
        // print_r($rida);
        ?>
        <div class="col">
            <h1><?php echo $rida["mark"]; ?><?php echo $rida["model"]; ?></h1>
            <p>Mootor: <?php echo $rida["engine"]; ?></p>
            <p>Kütus: <?php echo $rida["fuel"]; ?></p>
            <p>Aasta: <?php echo $rida["year"]; ?></p>
            <p>Staatus: <?php echo $rida["status"]; ?></p>
            <p>Käigukast: <?php echo $rida["transmission"]; ?></p>
            <p>Istmed: <?php echo $rida["seats"]; ?></p>
            <p class="fs-5">Hind: <?php echo $rida["price"]; ?></p>
            
            <div class="mt-4">
                <a class="btn btn-outline-dark mb-3" href="index.php">Tagasi</a>
                
                <?php if (isset($_SESSION['roll']) && $_SESSION['roll'] === 'client'): ?>
                    <?php if ($rida['status'] !== 'hoolduses'): ?>
                        <?php if ($rida['status'] === 'broneeritud'): ?>
                            <div class="alert alert-info small"><i class="bi bi-info-circle"></i> See auto on hetkel välja renditud, kuid saad seda broneerida vabadeks perioodideks.</div>
                        <?php endif; ?>
                        <div class="card card-body bg-light">
                            <h5>Rendi see auto</h5>
                            <form action="process_rent.php" method="post">
                                <input type="hidden" name="car_id" value="<?= $rida['id']; ?>">
                                <input type="hidden" id="daily_price" value="<?= $rida['price']; ?>">
                                <div class="mb-2">
                                    <label class="small">Alguskuupäev</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" required min="<?= date('Y-m-d'); ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="small">Lõppkuupäev</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" required min="<?= date('Y-m-d', strtotime('+1 day')); ?>">
                                </div>
                                <div class="mb-3 fs-5">Koguhind: <span id="total_price_display">0</span> €</div>
                                <button type="submit" class="btn btn-dark w-100">Kinnita rent</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <button class="btn btn-secondary w-100" disabled>Auto on hoolduses</button>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-warning">Rentimiseks pead olema <a href="admin/login.php">sisse logitud</a> kliendina.</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col">
            <img src="https://loremflickr.com/800/500/<?php echo str_replace(" ","", $rida["mark"]); ?>" class="card-img-top img-fluid" alt="<?php echo str_replace(" ","", $rida["mark"]); ?>">
        </div>
    </div>
    
</div>
<!-- /sisu -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    
  </body>
</html>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const dailyPriceInput = document.getElementById('daily_price');
        const totalPriceDisplay = document.getElementById('total_price_display');

        function calculateTotalPrice() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            const dailyPrice = parseFloat(dailyPriceInput.value);

            if (isNaN(startDate) || isNaN(endDate) || isNaN(dailyPrice) || startDate > endDate) {
                totalPriceDisplay.textContent = '0';
                return;
            }

            const timeDiff = endDate.getTime() - startDate.getTime();
            let days = Math.ceil(timeDiff / (1000 * 3600 * 24));
            if (days === 0) days = 1; // Minimum 1 day rental

            const totalPrice = days * dailyPrice;
            totalPriceDisplay.textContent = totalPrice.toFixed(2);
        }

        startDateInput.addEventListener('change', calculateTotalPrice);
        endDateInput.addEventListener('change', calculateTotalPrice);
    });
</script>
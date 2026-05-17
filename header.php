<?php
  // Detect if we are inside the admin directory to adjust relative paths
  $is_admin_dir = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false);
  $root = $is_admin_dir ? '../' : '';
  $admin_root = $is_admin_dir ? '' : 'admin/';
?>
<!doctype html>
<html lang="et">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Autorent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
      .nav-link:hover {
        background-color: rgba(0, 0, 0, 0.05);
        border-radius: 0.375rem;
        transition: background-color 0.2s ease-in-out;
      }
    </style>
  </head>
  <body>
    <!-- menüü -->
      <nav class="navbar navbar-expand-lg bg-body-tertiary mb-4">
  <div class="container">
    <a class="navbar-brand nav-link" href="<?php echo $root; ?>index.php">Autorent</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if (isset($_SESSION['roll'])): ?>
          <?php if ($_SESSION['roll'] === 'client'): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $root; ?>favourites.php">Lemmikud</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $root; ?>my_rentals.php">Minu rendid</a>
            </li>
          <?php elseif ($_SESSION['roll'] === 'admin'): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $admin_root; ?>admin.php">Admin</a>
            </li>
          <?php endif; ?>
        <?php endif; ?>
      </ul>
      <?php if (isset($_SESSION['roll'])): ?>
        <?php 
          $profile_link = ($_SESSION['roll'] === 'admin') ? $admin_root . 'admin_profile.php' : $root . 'client_profile.php';
        ?>
        <a class="nav-link me-3 d-flex align-items-center" href="<?php echo $profile_link; ?>">
          <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['tuvastamine']); ?>
        </a>
      <?php endif; ?>
      <form class="d-flex" role="search" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="otsi">
        <button class="btn btn-outline-success" type="submit">Otsi</button>
      </form>
      <?php if (isset($_SESSION['tuvastamine'])): ?>
        <a href="<?php echo $admin_root; ?>logout.php" class="ms-2 btn btn-danger">Logi välja</a>
      <?php else: ?>
        <?php if (!$is_admin_dir): ?>
          <a href="<?php echo $admin_root; ?>register.php" class="ms-2 btn btn-outline-primary">Registreeri</a>
        <?php endif; ?>
        <a href="<?php echo $admin_root; ?>login.php" class="ms-2 btn btn-primary">Logi sisse</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
    <!-- /menüü -->
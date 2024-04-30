<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <title>Home - <?= APP_NAME ?></title>

  <link href="<?= ROOT ?>/assets/bootstrap/css/bootstrap.css" rel="stylesheet">
  <link href="<?= ROOT ?>/assets/css/bootstrap-icons.css" rel="stylesheet">

  <style>
    .bi {
      vertical-align: -.125em;
      fill: currentColor;
    }
  </style>

  <!-- Custom styles for this template -->
  <link href="<?= ROOT ?>/assets/css/headers.css" rel="stylesheet">

</head>
<style>

</style>



<body class="text-white bg-night"> <!-- TODO: for change text color -->

  <header class="p-3 border-bottom">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <a href="<?= ROOT ?>" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">

          <img class="bi me-2" src="<?= ROOT ?>/assets/images/logo.svg" style="object-fit:cover;">
        </a>

        <!-- TODO: Change link colors . THIS IS NAVBAR -->
        <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
          <li><a href="<?= ROOT ?>" class="nav-link px-2 <?= $url[0] == 'home' ? 'text-yellow' : 'link-light' ?>">Home</a></li>
          <li><a href="<?= ROOT ?>/repositories" class="nav-link px-2  <?= $url[0] == 'repositories' ? 'text-yellow' : 'link-light' ?>">Repositories</a></li>
          <li><a href="<?= ROOT ?>/contact" class="nav-link px-2  <?= $url[0] == 'contact' ? 'text-yellow' : 'link-light' ?>">Contact</a></li>
          <li>
            <span class="nav-link px-2 link-light dropdown text-end">
              <a href="#" class="d-block <?= $url[0] == 'category' ? 'link-primary' : 'link-light' ?> text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                Category
              </a>
              <ul class="dropdown-menu text-small">

                <?php

                $query = "select * from categories order by id desc";
                $categories = query($query);
                ?>
                <?php if (!empty($categories)) : ?>
                  <?php foreach ($categories as $cat) : ?>
                    <li><a class="dropdown-item" href="<?= ROOT ?>/category/<?= $cat['slug'] ?>"><?= $cat['category'] ?></a></li>
                  <?php endforeach; ?>
                <?php endif; ?>



              </ul>
            </span>
          </li>
        </ul>

        <form action="<?= ROOT ?>/search" class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search">
          <div class="input-group ">
            <input value="<?= $_GET['find'] ?? '' ?>" name="find" type="search" class="form-control" placeholder="Search..." aria-label="Search">
            <button class="btn btn-yellow">Find</button>
          </div>
        </form>

        <?php if (logged_in()) : ?>
          <div class="dropdown text-end">
            <a href="#" class="d-block link-light text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="<?= get_image(user('image')) ?>" alt="mdo" style="object-fit: cover;" width="32" height="32" class="rounded-circle">
            </a>
            <ul class="dropdown-menu text-small">
              <li><a class="dropdown-item" href="#">Hi, <?= user('username') ?></a></li>
              <li><a class="dropdown-item" href="#">Profile</a></li>

              <?php if ($_SESSION['USER']['role'] == 'admin') : ?>
                <li><a class="dropdown-item" href="<?= ROOT ?>/admin">Admin</a></li>
              <?php endif; ?>

              <li><a class="dropdown-item" href="#">Settings</a></li>

              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item" href="<?= ROOT ?>/logout">Sign out</a></li>
            </ul>
          </div>
        <?php endif; ?>

      </div>
    </div>


  </header>

  <?php

  // TODO: disable the slider (and delete the slider.php later)
  if ($url[0] == 'home')
    include '../app/pages/includes/hero-sec.php';
  ?>

  <main class="p-2">
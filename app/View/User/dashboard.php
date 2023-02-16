<?php

use App\PHPLoginManagement\Config\BaseURL;


?>
<div class="container px-4 py-2">
  <div class="row d-flex align-items-center g-lg-5 py-5 mt-4">
    <div class="col-md-6 col-lg-8 d-flex text-lg-start text-center flex-column">
      <h1 class="display-4 fw-bold lh-1">Halo <?= $model['user']['name'] ?? 'Dashboard' ?>, Selamat datang!</h1>
      <p class="fw-light fs-5">
        Made by <a href="https://www.github.com/nkolis">Kholis</a> with PHP
        Programming Languange :)
      </p>
    </div>
    <div class="col-sm-8 col-md-6 col-lg-4 mx-auto">
      <?php if (isset($model['user']['verification_status']) && $model['user']['verification_status'] == 'unverified') { ?>
        <div class="alert alert-warning" role="alert">
          <strong>Warning!</strong><br>
          Please verify your account! <a href="<?= BASE_URL ?>/users/verification"> Click here</a>

        </div>
      <?php } ?>
      <div class="border border-2 border-secondary border-opacity-10 rounded-3 p-5 bg-light">

        <a href="<?= BASE_URL ?>/users/profile" class="btn btn-outline-primary w-100 p-2 mb-2">Profile</a>
        <a href="<?= BASE_URL ?>/users/password" class="btn btn-primary w-100 p-2 mb-2">Password</a>
        <a href="<?= BASE_URL ?>/users/logout" class="btn btn-danger w-100 p-2">Logout</a>
      </div>
    </div>
  </div>
</div>
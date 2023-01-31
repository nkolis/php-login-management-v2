<?php

use App\PHPLoginManagement\Config\BaseURL;
?>
<div class="container px-4 py-2">
  <div class="row d-flex align-items-center g-lg-5 py-5 mt-4">
    <div class="col-md-6 col-lg-8 d-flex text-lg-start text-center flex-column">
      <h1 class="display-4 fw-bold lh-1">Login Management</h1>
      <p class="fw-light fs-4">
        Made by <a href="https://www.github.com/nkolis">Kholis</a> with PHP
        Programming Languange 🤩️
      </p>
    </div>
    <div class="col-sm-8 col-md-6 col-lg-4 mx-auto border border-2 border-secondary border-opacity-10 rounded-3 p-5 bg-light">

      <a href="<?= BaseURL::get() ?>/users/register" class="btn btn-outline-primary w-100 p-2 mb-2">Register</a>
      <a href="<?= BaseURL::get() ?>/users/login" class="btn btn-primary w-100 p-2">Login</a>
    </div>
  </div>
</div>
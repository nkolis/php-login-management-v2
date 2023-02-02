<?php

use App\PHPLoginManagement\Config\BaseURL;
?>
<div class="container px-4 py-2">
  <div class="row d-flex align-items-center g-lg-5 py-5 mt-4">
    <div class="col-md-6 col-lg-8 d-flex text-lg-start text-center flex-column">
      <h1 class="display-4 fw-bold lh-1">Login</h1>
      <p class="fw-light fs-4">
        Made by <a href="https://www.github.com/nkolis">Kholis</a> with PHP
        Programming Languange :)
      </p>
    </div>


    <div class="col-sm-8 col-md-6 col-lg-4 mx-auto">
      <?php if (isset($model['error']['error_login'])) { ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?= $model['error']['error_login'] ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php } ?>
      <form class="border border-2 border-secondary border-opacity-10 rounded-3 p-5 bg-light" action="<?= BaseURL::get() ?>/users/login" method="post">

        <div class="form-floating mb-2 mt-2">
          <input type="text" class="form-control <?= isset($model['error']['email']) ? 'is-invalid' : '' ?>" placeholder="Email" name="email" id="email" autocomplete="off" value="<?= $_POST['email'] ?? '' ?>" />
          <label for="email">Email</label>
          <?php if (isset($model['error']['email'])) { ?>
            <div id="email" class="invalid-feedback"><?= $model['error']['email'] ?></div>
          <?php } ?>
        </div>
        <div class="form-floating mb-2">
          <input type="password" class="form-control <?= isset($model['error']['password']) ? 'is-invalid' : '' ?>" placeholder="Password" name="password" id="password" />
          <label for="password">Password</label>
          <?php if (isset($model['error']['password'])) { ?>
            <div id="password" class="invalid-feedback"><?= $model['error']['password'] ?></div>
          <?php } ?>
        </div>
        <button class="btn btn-primary w-100 p-2" type="submit">Login</button>
      </form>
    </div>

  </div>
</div>
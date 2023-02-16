<?php

use App\PHPLoginManagement\Config\BaseURL;
?>
<div class="container px-4 py-2">
  <div class="row d-flex align-items-center g-lg-5 py-5 mt-4">
    <div class="col-md-6 col-lg-8 d-flex text-lg-start text-center flex-column">
      <h1 class="display-4 fw-bold lh-1">Register</h1>
      <p class="fw-light fs-5">
        Made by <a href="https://www.github.com/nkolis">Kholis</a> with PHP
        Programming Languange :)
      </p>
    </div>
    <form class="col-sm-8 col-md-6 col-lg-4 mx-auto border border-2 border-secondary border-opacity-10 rounded-3 p-5 bg-light" action="<?= BASE_URL ?>/users/register" method="post">
      <div class="form-floating mb-2 mt-2">
        <input type="text" class="form-control <?= isset($model['error']['email']) ? 'is-invalid' : '' ?>" placeholder="Email" name="email" id="email" autocomplete="off" value="<?= $_POST['email'] ?? '' ?>" />
        <label for="email">Email</label>
        <?php if (isset($model['error']['email'])) { ?>
          <div id="email" class="invalid-feedback"><?= $model['error']['email'] ?></div>
        <?php } ?>
      </div>
      <div class="form-floating mb-2 mt-2">
        <input type="text" class="form-control <?= isset($model['error']['name']) ? 'is-invalid' : '' ?>" placeholder="Name" name="name" id="name" autocomplete="off" value="<?= $_POST['name'] ?? '' ?>" />
        <label for="name">Name</label>
        <?php if (isset($model['error']['name'])) { ?>
          <div id="name" class="invalid-feedback"><?= $model['error']['name'] ?></div>
        <?php } ?>
      </div>
      <div class="input-group mb-2 <?= isset($model['error']['password']) ? 'has-validation' : '' ?>">
        <div class="form-floating">
          <input type="password" class="form-control <?= isset($model['error']['password']) ? 'is-invalid' : '' ?>" placeholder="Password" name="password" id="password" />
          <label for="password">Password</label>
        </div>
        <button type="button" class="input-group-text bg-transparent" id="toggle-password"><i class="bi bi-eye-slash"></i></button>

        <?php if (isset($model['error']['password'])) { ?>
          <div id="password" class="invalid-feedback d-block"><?= $model['error']['password'] ?></div>
        <?php } ?>
      </div>
      <button class="btn btn-primary w-100 p-2 mb-2" type="submit">Register</button>
      <span>Sudah punya akun? </span> <a href="<?= BASE_URL ?>/users/login">Login</a>
    </form>
  </div>
</div>
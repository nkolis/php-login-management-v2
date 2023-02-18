<?php


?>
<div class="container px-4 py-2">
  <div class="row d-flex align-items-center g-lg-5 py-5 mt-4">
    <div class="col-md-6 col-lg-8 d-flex text-lg-start text-center flex-column">
      <h1 class="display-4 fw-bold lh-1">Password</h1>
      <p class="fw-light fs-6">
        Made by <a href="https://www.github.com/nkolis">Kholis</a> with PHP
        Programming Languange :)
      </p>
    </div>
    <form class="col-sm-8 col-md-6 col-lg-4 mx-auto border border-2 border-secondary border-opacity-10 rounded-3 p-5 bg-light" action="<?= BASE_URL ?>/users/password" method="post">
      <div class="form-floating mb-2 mt-2">
        <input type="email" class="form-control" placeholder="Email" name="email" id="email" autocomplete="off" disabled value="<?= $model['user']['email'] ?? $_POST['email'] ?>" />
        <label for="email">Email</label>

      </div>
      <div class="form-floating mb-2">
        <input type="text" class="form-control" placeholder="Name" name="name" id="name" disabled value="<?= $model['user']['name'] ?? $_POST['name'] ?>" />
        <label for="name">Name</label>
      </div>
      <div class="input-group mb-2 <?= isset($model['error']['oldPassword']) ? 'has-validation' : '' ?>">
        <div class="form-floating">
          <input type="password" class="form-control <?= isset($model['error']['oldPassword']) ? 'is-invalid' : '' ?>" placeholder="Old Password" name="oldPassword" id="oldPassword" />
          <label for="oldPassword">Old Password</label>
        </div>
        <button type="button" class="input-group-text bg-transparent" id="toggle-password"><i class="bi bi-eye-slash"></i></button>
        <?php if (isset($model['error']['oldPassword'])) { ?>
          <div id="oldPassword" class="invalid-feedback d-block"><?= $model['error']['oldPassword'] ?></div>
        <?php } ?>
      </div>
      <div class="input-group mb-2 <?= isset($model['error']['newPassword']) ? 'has-validation' : '' ?>">
        <div class="form-floating">
          <input type="password" class="form-control <?= isset($model['error']['newPassword']) ? 'is-invalid' : '' ?>" placeholder="New Password" name="newPassword" id="newPassword" />
          <label for="newPassword">New Password</label>
        </div>
        <button type="button" class="input-group-text bg-transparent" id="toggle-password"><i class="bi bi-eye-slash"></i></button>
        <?php if (isset($model['error']['newPassword'])) { ?>
          <div id="newPassword" class="invalid-feedback d-block"><?= $model['error']['newPassword'] ?></div>
        <?php } ?>
      </div>
      <button class="btn btn-primary w-100 p-2 mb-2" type="submit">Update Password</button>
      <a class="btn btn-danger w-100 p-2" href="<?= BASE_URL ?>/users/dashboard">Cancel</a>
    </form>
  </div>
</div>
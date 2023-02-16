<?php



?>
<div class="container px-4 py-2">
  <div class="row d-flex align-items-center g-lg-5 py-5 mt-4">
    <div class="col-md-6 col-lg-8 d-flex text-lg-start text-center flex-column">
      <h1 class="display-4 fw-bold lh-1">Profile</h1>
      <p class="fw-light fs-5">
        Made by <a href="https://www.github.com/nkolis">Kholis</a> with PHP
        Programming Languange :)
      </p>
    </div>
    <form class="col-sm-8 col-md-6 col-lg-4 mx-auto border border-2 border-secondary border-opacity-10 rounded-3 p-5 bg-light" action="<?= BASE_URL ?>/users/profile" method="post">
      <div class="form-floating mb-2 mt-2">
        <input type="email" class="form-control <?= isset($model['error']['email']) ? 'is-invalid' : '' ?>" placeholder="Email" name="email" id="email" autocomplete="off" value="<?= $model['user']['email'] ?? $_POST['email'] ?>" />
        <label for="email">Email</label>
        <?php if (isset($model['error']['email'])) { ?>
          <div id="email" class="invalid-feedback"><?= $model['error']['email'] ?></div>
        <?php } ?>

      </div>
      <div class="form-floating mb-2">
        <input type="text" class="form-control <?= isset($model['error']['name']) ? 'is-invalid' : '' ?>" placeholder="Name" name="name" id="name" value="<?= $model['user']['name'] ?? $_POST['name'] ?>" />
        <label for="name">Name</label>
        <?php if (isset($model['error']['name'])) { ?>
          <div id="name" class="invalid-feedback"><?= $model['error']['name'] ?></div>
        <?php } ?>
      </div>
      <button class="btn btn-primary w-100 p-2" type="submit">Update Profile</button>
    </form>
  </div>
</div>
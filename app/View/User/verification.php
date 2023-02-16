<?php

use App\PHPLoginManagement\Config\BaseURL;
use App\PHPLoginManagement\Helper\Flasher;

$message = Flasher::get();

?>
<div class="container px-4 py-2">
  <div class="row d-flex align-items-center g-lg-5 py-5 mt-4">
    <div class="col-md-6 col-lg-8 d-flex text-lg-start text-center flex-column">
      <h1 class="display-4 fw-bold lh-1">Verification</h1>
      <p class="fw-light fs-4">
        Made by <a href="https://www.github.com/nkolis">Kholis</a> with PHP
        Programming Languange :)
      </p>
    </div>
    <div class="col-sm-8 col-md-6 col-lg-4 mx-auto">
      <?php if (isset($message['success'])) { ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?= $message['success'] ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php } ?>
      <?php if (isset($model['error']['verification'])) { ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?= $model['error']['verification'] ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php } ?>
      <div class="border border-2 border-secondary border-opacity-10 rounded-3 p-5 bg-light">

        <form class="mb-2" action="<?= BASE_URL ?>/users/verification" method="post">
          <div class="form-floating mb-2 mt-2">
            <input type="email" class="form-control" placeholder="Email" name="email" id="email" autocomplete="off" value="<?= $model['user']['email'] ?? '' ?>" disabled />
            <label for="email">Email</label>

          </div>
          <div class="form-floating mb-2">
            <input type="text" class="form-control" placeholder="Name" name="name" id="name" value="<?= $model['user']['name'] ?? '' ?>" disabled />
            <label for="name">Name</label>
          </div>

          <div class="form-floating mb-2">
            <input type="text" class="form-control <?= isset($model['error']['code']) ? 'is-invalid' : '' ?>" placeholder="Enter code here" name="code" id="code" />
            <label for="code">Enter code here</label>
            <?php if (isset($model['error']['code'])) { ?>
              <div id="code" class="invalid-feedback"><?= $model['error']['code'] ?></div>
            <?php } ?>
          </div>
          <button class="btn btn-primary w-100 p-2" type="submit">Verify</button>
        </form>
        <form action="<?= BASE_URL ?>/users/verification/sendcode" method="post">
          <button class="btn btn-outline-success w-100 p-2" type="submit">Send code</button>
        </form>
      </div>
    </div>
  </div>
</div>
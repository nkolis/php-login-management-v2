<div class="container px-4 py-2">
  <div class="row d-flex align-items-center g-lg-5 py-5 mt-4">
    <div class="col-md-6 col-lg-8 d-flex text-lg-start text-center flex-column">
      <h1 class="display-4 fw-bold lh-1">Password</h1>
      <p class="fw-light fs-4">
        Made by <a href="https://www.github.com/nkolis">Kholis</a> with PHP
        Programming Languange :)
      </p>
    </div>
    <form class="col-sm-8 col-md-6 col-lg-4 mx-auto border border-2 border-secondary border-opacity-10 rounded-3 p-5 bg-light" action="/users/password" method="post">
      <div class="form-floating mb-2 mt-2">
        <input type="email" class="form-control" placeholder="Email" name="email" id="email" autocomplete="off" disabled <?= $model['user']['email'] ?? $_POST['email'] ?> />
        <label for="email">Email</label>

      </div>
      <div class="form-floating mb-2">
        <input type="text" class="form-control" placeholder="Name" name="name" id="name" disabled <?= $model['user']['name'] ?? $_POST['name'] ?> />
        <label for="name">Name</label>
      </div>

      <div class="form-floating mb-2">
        <input type="password" class="form-control <?= isset($model['error']['oldPassword']) ? 'is-invalid' : '' ?>" placeholder="Old Password" name="oldPassword" id="oldPassword" />
        <label for="oldPassword">Old Password</label>
        <?php if (isset($model['error']['oldPassword'])) { ?>
          <div id="oldPassword" class="invalid-feedback"><?= $model['error']['oldPassword'] ?></div>
        <?php } ?>
      </div>

      <div class="form-floating mb-2">
        <input type="password" class="form-control <?= isset($model['error']['newPassword']) ? 'is-invalid' : '' ?>" placeholder="New Password" name="newPassword" id="newPassword" />
        <label for="newPassword">New Password</label>
        <?php if (isset($model['error']['newPassword'])) { ?>
          <div id="newPassword" class="invalid-feedback"><?= $model['error']['newPassword'] ?></div>
        <?php } ?>
      </div>
      <button class="btn btn-primary w-100 p-2" type="submit">Update Password</button>
    </form>
  </div>
</div>
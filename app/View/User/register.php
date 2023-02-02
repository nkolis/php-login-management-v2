<?php

use App\PHPLoginManagement\Config\BaseURL;
?>
<div class="container px-4 py-2">
  <div class="row d-flex align-items-center g-lg-5 py-5">
    <div class="col-md-6 col-lg-8 d-flex text-lg-start text-center flex-column">
      <h1 class="display-4 fw-bold lh-1">Register</h1>
      <p class="fw-light fs-5">
        Made by <a href="https://www.github.com/nkolis">Kholis</a> with PHP
        Programming Languange :)
      </p>
    </div>
    <form class="col-sm-8 col-md-6 col-lg-4 mx-auto border border-2 border-secondary border-opacity-10 rounded-3 p-5 bg-light" action="<?= BaseURL::get() ?>/users/register" method="post">
      <div class="form-floating mb-2 mt-2">
        <input type="email" class="form-control is-invalid" placeholder="Email" name="email" id="email" autocomplete="off" />
        <label for="email">Email</label>
        <div id="email" class="invalid-feedback">Email cannot empty</div>
      </div>
      <div class="form-floating mb-2 mt-2">
        <input type="text" class="form-control is-invalid" placeholder="Name" name="name" id="name" autocomplete="off" />
        <label for="name">Name</label>
        <div id="name" class="invalid-feedback">Name cannot empty</div>
      </div>
      <div class="form-floating mb-2">
        <input type="password" class="form-control is-invalid" placeholder="Password" name="password" id="password" />
        <label for="password">Password</label>
        <div class="invalid-feedback" id="password">
          Password cannot empty
        </div>
      </div>
      <button class="btn btn-primary w-100 p-2" type="submit">Register</button>
    </form>
  </div>
</div>
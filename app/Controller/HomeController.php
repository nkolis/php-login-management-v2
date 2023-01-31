<?php

namespace App\PHPLoginManagement\Controller;

use App\PHPLoginManagement\Core\View;

class HomeController
{
  public function index()
  {
    View::render('Home/home', [
      'title' => 'PHP Login Management'
    ]);
  }
}

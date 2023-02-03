<?php

namespace App\PHPLoginManagement\Core {
  function header($value)
  {
    echo $value;
  }
}

namespace App\PHPLoginManagement\Service {

  function setcookie($name, $value)
  {
    echo "$name, $value";
  }
}

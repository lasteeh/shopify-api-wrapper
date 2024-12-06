<?php

namespace App\Controllers;

use Core\Components\ActionController;

class ApplicationController extends ActionController
{
  public function not_found()
  {
    $this->render(status: 404);
  }

  public function test()
  {
    $this->render();
  }
}

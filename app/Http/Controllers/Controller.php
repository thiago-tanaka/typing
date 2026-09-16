<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

// The laravel/ui auth controllers register their middleware with $this->middleware()
// in the constructor, which only the routing controller provides.
abstract class Controller extends BaseController
{
}

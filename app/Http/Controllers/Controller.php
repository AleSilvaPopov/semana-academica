<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    // $this->authorize('update', $model) -> usa as Policies de app/Policies e os Gates do AppServiceProvider.
    use AuthorizesRequests;
}

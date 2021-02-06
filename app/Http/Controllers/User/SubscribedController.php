<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class SubscribedController extends Controller
{

    public function secretPage(): Factory|View|Application
    {
        return view('secret.page');
    }

}

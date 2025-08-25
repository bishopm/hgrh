<?php

namespace Bishopm\Hgrh\Http\Controllers;

use Bishopm\Hgrh\Models\Document;

class HomeController extends Controller
{

    public function home()
    {
        return view('hgrh::app.home');
    }
}

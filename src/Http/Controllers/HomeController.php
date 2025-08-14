<?php

namespace Bishopm\Hgrh\Http\Controllers;

use Bishopm\Hgrh\Models\Document;

class HomeController extends Controller
{

    public function home()
    {
        $data=array();
        return view('hgrh::web.home',$data);
    }
}

<?php

namespace Bishopm\Hgrh\Http\Controllers;

use Bishopm\Hgrh\Models\Document;

class HomeController extends Controller
{

    public function home()
    {
        $data['docs']=Document::orderBy('document')->get();        
        return view('hgrh::app.home',$data);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        return view('pages.home');
    }

    public function portofolio()
    {
        return view('pages.portofolio');
    }

    public function layanan()
    {
        return view('pages.layanan');
    }

    public function caraOrder()
    {
        return view('pages.cara-order');
    }

    public function tanyaJawab()
    {
        return view('pages.tanya-jawab');
    }
}

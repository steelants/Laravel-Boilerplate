<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\CertificateItems;
use App\Jobs\ParseExportsJob;
use App\Models\Category;
use App\Models\ClassificationRule;
use App\Models\Source;
use App\Rules\DescriptionToCategory;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('home');
    }
}
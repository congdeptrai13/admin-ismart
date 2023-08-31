<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminPageController extends Controller
{
    //
    function list()
    {
        return view("admin.page.list");
    }
}

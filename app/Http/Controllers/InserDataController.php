<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InserDataController extends Controller
{
    public function insert(Request $request)
    {
        if (!empty($request->json)) {
            return \DB::table('links')->insert(\json_decode($request->json, true));
        }
        return false;
    }
}

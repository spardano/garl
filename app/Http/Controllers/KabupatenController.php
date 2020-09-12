<?php

namespace App\Http\Controllers;

use App\Kabupaten;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KabupatenController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    
    public function paginate()
    {
        $kabupaten = Kabupaten::orderBy('name', 'ASC')->paginate(request('limit', 20));

        if (request()->all()) {
            $kabupaten->appends(request()->all());
        }

        return response()->json($kabupaten);
    }


    public function all(Request $req)
    {
        $kabupaten = Kabupaten::all();

        return response()->json($kabupaten);
    }
}

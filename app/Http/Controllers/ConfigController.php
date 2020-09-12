<?php

namespace App\Http\Controllers;

use App\Config;
use App\Mclass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ConfigController extends Controller
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

    public function save(Request $req, $id = null)
    {
        $rules = [
            'key'   => 'required|exists:config,key',
            'value' => 'required',
        ];

        $this->validate($req, $rules);

       $class = Config::where("key", $req->input("key"))->first();

        if(!$class) $class = new Mclass();

        $class->key         = $req->input('key');
        $class->value       = $req->input('value');
        $class->save();

        return response()->json($class);
    }

    public function detail($id)
    {
        $class = Config::where('key', $id)->first();

        return response()->json($class);
    }

}

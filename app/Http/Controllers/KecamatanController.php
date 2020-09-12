<?php

namespace App\Http\Controllers;

use App\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KecamatanController extends Controller
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
        $kecamatans = Kecamatan::with('kabupaten')->orderBy('name', 'ASC')
            ->paginate(request('limit', 20));

        if (request()->all()) {
            $kecamatans->appends(request()->all());
        }

        return response()->json($kecamatans);
    }

    public function save(Request $req, $id = null)
    {
        $rules = [
            'name'          => 'required|string|max:100',
            'kabupaten_id'  => 'required|exists:kabupaten,id',
        ];

        $this->validate($req, $rules);


        $kecamatan = null;
        if($id!= null) $kecamatan = Kecamatan::findOrFail($id);

        if(!$kecamatan) $kecamatan = new Kecamatan();
        $kecamatan->name            = $req->input('name');
        $kecamatan->kabupaten_id    = $req->input('kabupaten_id');
        $kecamatan->save();

        return response()->json($kecamatan);
    }

    public function show($id)
    {
        $kecamatan = Kecamatan::findOrFail($id);

        return response()->json($kecamatan);
    }

    public function all(Request $req)
    {
        $kecamatan = new Kecamatan;
        if($req->input("kabupaten_id") != null){
            $kecamatan = $kecamatan->where("kabupaten_id", $req->input("kabupaten_id"));
        }

        return response()->json($kecamatan->get());
    }

    public function delete($id){
        $kecamatan = Kecamatan::findOrFail($id);
        $kecamatan->delete();

        return response()->json([]);
    }
}

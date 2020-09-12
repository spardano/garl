<?php

namespace App\Http\Controllers;

use App\Kelurahan;
use App\Mclass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KelurahanController extends Controller
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
        $kelurahans = Kelurahan::with("kecamatan.kabupaten")->orderBy('name', 'ASC')
            ->paginate(request('limit', 20));

        if (request()->all()) {
            $kelurahans->appends(request()->all());
        }

        return response()->json($kelurahans);
    }

    public function save(Request $req, $id = null)
    {
        $rules = [
            'kecamatan_id'  => 'required|exists:kecamatan,id',
            'name'          => 'required|string|max:100',
            'area'          => 'required|numeric',
            'polygon'       => 'required|json',
        ];

        $this->validate($req, $rules);
        
        $kelurahan = null;
        if($id != null) $kelurahan = Kelurahan::findOrFail($id);
        if(!$kelurahan) $kelurahan = new Kelurahan();

        $kelurahan->name            = $req->input('name');
        $kelurahan->kecamatan_id    = $req->input('kecamatan_id');
        $kelurahan->area            = $req->input('area');
        $kelurahan->polygon         = $req->input('polygon');
        $kelurahan->save();

        return response()->json($kelurahan);
    }

    public function show($id)
    {
        $kelurahan = Kelurahan::with('kecamatan.kabupaten')->findOrFail($id);
        return response()->json($kelurahan);
    }

    public function all(Request $req)
    {
        $kelurahan = Kelurahan::orderBy("name", "ASC");
        if($req->input("kecamatan_id") != null){
            $kelurahan = $kelurahan->where("kecamatan_id", $req->input("kecamatan_id"));
        }

        return response()->json($kelurahan->get());
    }

    public function maps(Request $req)
    {
        $kelurahan = Kelurahan::with("kelas")->get();
        $maps['type']       = 'FeatureCollection';
        $maps['features']   = [];
        $response           = [];
        $response['kelas']  = [];
        $kelas              = [];
        $allClass           = Mclass::orderBy("class", "ASC")->get();
        foreach($allClass as $c){
            $kelas[$c->id] = $c;
            $kelas[$c->id]['area'] = 0;
        }
        foreach($kelurahan as $k){
            $c = $k->kelas->id ?? Mclass::UNCLASSIFIED;
            $kelas[$c]['area'] += $k->area;
            
            $feature = [];
            $feature['type'] = 'Feature';
            $feature['properties']['attr'] = $k->name; //$map->CLASS_ATTR;
            $feature['properties']['area'] = $k->area; //$map->AREA;
            $feature['properties']['code'] = $k->kelas->color ?? "#bdc3c7"; 
            $feature['geometry'] = json_decode($k->polygon);
            $maps['features'][] = $feature; 
        }

        $response['kelas'] = array_values($kelas);
        $response['maps']  = $maps;

        return response()->json($response);
    }

    public function delete($id){
        $kelurahan = Kelurahan::findOrFail($id);
        $kelurahan->delete();

        return response()->json([]);
    }
}

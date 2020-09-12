<?php

namespace App\Http\Controllers;

use App\Kategori;
use App\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KategoriController extends Controller
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
        $kategoris = Kategori::orderBy('name', 'ASC')
            ->paginate(request('limit', 20));

        if (request()->all()) {
            $kategoris->appends(request()->all());
        }

        return response()->json($kategoris);
    }

    public function save(Request $req, $id = null)
    {
        $rules = [
            'name'          => 'required|string|max:100',
        ];

        $this->validate($req, $rules);


        $kategori = null;
        if($id!= null) $kategori = Kategori::findOrFail($id);

        if(!$kategori) $kategori = new Kategori();
        $kategori->name            = $req->input('name');
        $kategori->save();

        return response()->json($kategori);
    }

    public function show($id)
    {
        $kategori = Kategori::findOrFail($id);

        return response()->json($kategori);
    }

    public function all(Request $req)
    {
        $kategori = Kategori::all();
        return response()->json($kategori);
    }

    public function form(Request $req)
    {
        $kategori = Kategori::orderBy("name", 'asc')->with('kriteria.details')->get();
        $forms = [];
        foreach($kategori as $kat){
            $f['label']     = $kat->name;
            $f['kriteria']  = [];
            foreach($kat->kriteria as $kr){
                $f1['id']       = $kr->id;
                $f1['label']    = $kr->name;
                $f1['type']     = $kr->type;
                $f1['min']      = 10000;
                $f1['max']      = -1;
                $f1['data']     = [];
                foreach($kr->details as $d){
                    if($f1['type'] == 'numeric'){
                        if($f1['min'] > $d->min) $f1['min'] = $d->min; 
                        if($f1['max'] < $d->max) $f1['max'] = $d->max; 
                    }else{
                        $f1['data'][$d->min] = $d->min;
                        $f1['data'][$d->max] = $d->max;
                    }
                }
                if($f1['max'] == -1) $f1['max'] = Kriteria::NILAI_MAX;
                $f1['data']      = array_values($f1['data']);
                $f['kriteria'][] = $f1;
            }
            $forms[] = $f;
        }
        return response()->json($forms);
    }


    public function formByKriteria(Request $req, $kriteria)
    {
        $kriteria = Kriteria::with('details')->find($kriteria);
        if(!$kriteria) return response()->json([], 404);
        $forms = [];
        $f1['id']       = $kriteria->id;
        $f1['label']    = $kriteria->name;
        $f1['type']     = $kriteria->type;
        $f1['min']      = 10000;
        $f1['max']      = -1;
        $f1['data']     = [];
        foreach($kriteria->details as $d){
            if($f1['type'] == 'numeric'){
                if($f1['min'] > $d->min) $f1['min'] = $d->min; 
                if($f1['max'] < $d->max) $f1['max'] = $d->max; 
            }else{
                $f1['data'][$d->min] = $d->min;
                $f1['data'][$d->max] = $d->max;
            }
        }
        if($f1['max'] == -1) $f1['max'] = Kriteria::NILAI_MAX;
        $f1['data']      = array_values($f1['data']);
        return response()->json($f1);
    }

    public function delete($id){
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();

        return response()->json([]);
    }

}

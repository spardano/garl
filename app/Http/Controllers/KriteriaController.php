<?php

namespace App\Http\Controllers;

use App\Kriteria;
use App\KriteriaDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KriteriaController extends Controller
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
        $kriterias = Kriteria::with(['kategori', 'details'])->orderBy('name', 'ASC')
            ->paginate(request('limit', 20));

        if (request()->all()) {
            $kriterias->appends(request()->all());
        }

        return response()->json($kriterias);
    }

    public function save(Request $req, $id = null)
    {
        $fieldVal = '|numeric';
        if($req->input('type') == 'label') $fieldVal = "|string";
        $rules = [
            'name'                  => 'required|string|max:100',
            'kategori_id'           => 'required|exists:kategori,id',
            'type'                  => 'required',
            'details.*.min'          => 'required'.$fieldVal,
            'details.*.max'          => 'required'.$fieldVal,
            'details.*.kesesuaian'   => 'required',
            'details.*.keterangan'   => 'required',

        ];

        $this->validate($req, $rules);


        $kriteria = null;
        if($id!= null) $kriteria = Kriteria::findOrFail($id);

        if(!$kriteria) $kriteria = new Kriteria();
        $kriteria->name         = $req->input('name');
        $kriteria->kategori_id  = $req->input('kategori_id');
        $kriteria->type         = $req->input('type');
        $kriteria->save();

        KriteriaDetail::where("kriteria_id", $kriteria->id)->delete();
        $detail = $req->input("details");
        $details= [];
        foreach($detail as $d){
            $d['kriteria_id'] = $kriteria->id;
            unset($d['id']);
            $details[] = $d;
        }
        KriteriaDetail::insert($details);

        return response()->json($kriteria);
    }

    public function show($id)
    {
        $kriteria = Kriteria::with(['kategori', 'details'])->findOrFail($id);

        return response()->json($kriteria);
    }

    public function all(Request $req)
    {
        $kriteria = new Kriteria;
        if($req->input("kategori_id") != null){
            $kriteria = $kriteria->where("kategori_id", $req->input("kategori_id"));
        }

        return response()->json($kriteria->get());
    }

    public function detailAll(Request $req)
    {
        $kriteria = new KriteriaDetail;
        if($req->input("kriteria_id") != null){
            $kriteria = $kriteria->where('kriteria_id', $req->input("kriteria_id"));
        }

        return response()->json($kriteria->get());
    }

    public function delete($id){
        $kriteria = Kriteria::findOrFail($id);
        
        KriteriaDetail::where("kriteria_id", $kriteria->id)->delete();
        $kriteria->delete();

        return response()->json([]);
    }
}

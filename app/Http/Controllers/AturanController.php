<?php

namespace App\Http\Controllers;

use App\Aturan;
use App\AturanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AturanController extends Controller
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
        $aturans = Aturan::with(['details.kriteria', 'kelas', 'kabupaten'])->orderBy('id', 'ASC')
            ->paginate(request('limit', 20));

        if (request()->all()) {
            $aturans->appends(request()->all());
        }

        return response()->json($aturans);
    }

    public function save(Request $req, $id = null)
    {
        $rules = [
            'kabupaten_id'      => 'required|exists:kabupaten,id',
            'class'             => 'required',
            'details.*.param'   => 'required',
            'details.*.value'   => 'required',
        ];

        $this->validate($req, $rules);


        $aturan = null;
        if($id!= null) $aturan = Aturan::findOrFail($id);

        if(!$aturan) $aturan = new Aturan();
        $aturan->kabupaten_id  = $req->input('kabupaten_id');
        $aturan->class         = $req->input('class');
        $aturan->save();

        AturanDetail::where("basis_aturan_id", $aturan->id)->delete();
        $detail = $req->input("details");
        $details= [];
        foreach($detail as $d){
            $d['basis_aturan_id'] = $aturan->id;
            unset($d['id']);
            $details[] = $d;
        }
        AturanDetail::insert($details);

        $aturan->details = $details;

        return response()->json($aturan);
    }

    public function show($id)
    {
        $aturan = Aturan::with(['details.kriteria'])->findOrFail($id);

        return response()->json($aturan);
    }

    public function all(Request $req)
    {
        $aturan = new Aturan;
        if($req->input("kategori_id") != null){
            $aturan = $aturan->where("kategori_id", $req->input("kategori_id"));
        }

        return response()->json($aturan->get());
    }

    public function delete($id){
        $aturan = Aturan::findOrFail($id);
        $aturan->delete();

        return response()->json([]);
    }
}

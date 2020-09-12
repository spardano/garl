<?php

namespace App\Http\Controllers;

use App\Aturan;
use App\Kecamatan;
use App\Kelurahan;
use App\Kriteria;
use App\Mclass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KlasifikasiController extends Controller
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

    public function proses(Request $req)
    {
        $rules = [
            'kabupaten_id'  => 'required|exists:kabupaten,id',
            'kelurahan_id'  => 'required|exists:kelurahan,id',
            'details'       => 'required|array'
        ];

        $this->validate($req, $rules);

        $classify = Mclass::UNCLASSIFIED;

        $details    = array_filter($req->input("details"), function ($d) {
            return ($d != null);
        });
        $params     = array_keys($details);
        $rules      = Aturan::with('details')->where("kabupaten_id", $req->input("kabupaten_id"))->get();

        $normal     = $this->normalize($params, $details);
        $normalize  = $normal['normal'];
        $rekom      = $normal['rekom'];

        $rulesEach  = [];
        $matchCount = [];
        foreach ($rules as $classRules) {
            $matchCount[$classRules->id] = 0;
            $rulesEach[$classRules->id] = $classRules;
            foreach ($classRules->details as $ruleDetail) {
                foreach ($normalize as $paramId => $class) {
                    if ($ruleDetail->param == $paramId && $ruleDetail->value == $class) $matchCount[$classRules->id]++;
                }
            }
        }

        arsort($matchCount);
        $firstKey = array_key_first($matchCount);
        if (isset($matchCount[$firstKey])) {
            $normal['rules']    = $rulesEach[$firstKey];
            $classify           = $rulesEach[$firstKey]->class;
        }

        $kelurahan = Kelurahan::find($req->input("kelurahan_id"));
        $kelurahan->class   = $classify;
        $kelurahan->data    = json_encode($normal);
        $kelurahan->dataraw = json_encode($details);
        $kelurahan->save();
        return $kelurahan;
    }

    private function normalize($params, $details)
    {
        $normal = [];
        $rekom  = [];
        $kriteria = Kriteria::whereIn("id", $params)->with("details")->get();
        foreach ($details as $normKey => $norm) {
            foreach ($kriteria as $kri) {
                if ($normKey != $kri->id) continue;

                foreach ($kri->details as $kd) {
                    $k          = clone $kri;
                    unset($k->details);
                    $kd->kriteria       = $k->toArray();
                    if ($kri->type == 'numeric') {
                        if ($kd->min == Kriteria::NILAI_MAX) {
                            if ($norm <= $kd->max) {
                                $rekom[$normKey]    = $kd->toArray();
                                $normal[$normKey]   = $kd->keterangan; //$norm - $kd->min;
                            }
                        } elseif ($kd->max == Kriteria::NILAI_MAX) {
                            if ($norm >= $kd->min) {
                                $rekom[$normKey]    = $kd->toArray();
                                $normal[$normKey]   = $kd->keterangan; //$norm - $kd->min;
                            }
                        }
                        else {
                            if ($norm >= $kd->min && $norm <= $kd->max) {
                                $rekom[$normKey]    = $kd->toArray();
                                $normal[$normKey]   = $kd->keterangan; //$norm - $kd->min;
                            }
                        }
                    } else {
                        if (strtolower($norm) == strtolower($kd->min) || strtolower($norm) == strtolower($kd->max)) {
                            $rekom[$normKey]    = $kd->toArray();
                            $normal[$normKey]   = $kd->keterangan;
                        }
                    }
                }
            }
        }
        return ['normal' => $normal, 'rekom' => $rekom];
    }

    public function result($id)
    {
        $kelurahan = Kelurahan::with(['kecamatan.kabupaten', 'kelas'])->findOrFail($id);
        return response()->json($kelurahan);
    }

    public function prosesNew(Request $req) //PROSES INPUTAN KLASIFIKASI
    {
        //ATURAN FORM
        $rules = [
            'kabupaten_id'  => 'required|exists:kabupaten,id',
            'kelurahan_id'  => 'required|exists:kelurahan,id',
            'details'       => 'nullable|array'
        ];

        //VALIDASI FORM SESUAI ATURAN FORM
        $this->validate($req, $rules);

        //HASIL KLASIFIKASI DEFAULT
        $classify = Mclass::UNCLASSIFIED;

        //Details ini fungsinya menampung nilai dari inputan user
        //yang di tampung details adalah id dan form aturan
        //CONTOH form dengan id 13 yang berarti itu tekstur tanah, tru
        //MENAMPILKAN DETAIL HASIL KLASIFIKASI BERDASARKAN INPUTAN PENGGUNA YANG SUDAH DIISI
        $details    = array_filter($req->input("details"), function ($d) {
            return ($d != null);
        });

        //MENCARI HASIL KLASIFIKASI DARI VARIABEL DETAIL
        $params     = array_keys($details);

        //JIKA INPUTAN BELUM ADA, MAKA LAKUKAN PENGECEKAN INPUTAN SEBELUM MENAMPILKAN INPUTAN KRITERIA
        if (count($details) == 0) {
            //JIKA INPUTAN KABUPATEN YANG DIPILIH DENGAN ID 1, MAKA INPUTAN KRITERIA PERTAMA YANG DITAMPILKAN ADALAH YANG MEMILIKI kriteria_id=13 (TEKSTUR TANAH)
            if ($req->input("kabupaten_id") == 1) return response()->json(['next' => 13, 'finish' => false]);
            //JIKA INPUTAN KABUPATEN YANG DIPILIH BUKAN ID 1, MAKA INPUTAN PERTAMA YANG DITAMPILKAN ADALAH YANG MEMILIKI kriteria_id = 15 (RELIEF)
            else return response()->json(['next' => 15, 'finish' => false]);
        }


        $rules      = Aturan::with('details')->where("kabupaten_id", $req->input("kabupaten_id"))->get();

        $normal     = $this->normalize($params, $details);
        $normalize  = $normal['normal'];
        $rekom      = $normal['rekom'];

        $rulesEach  = [];
        $matchCount = [];

        foreach ($rules as $classRules) {
            $matchCount[$classRules->id] = 0;
            $rulesEach[$classRules->id] = $classRules;
            foreach ($classRules->details as $ruleDetail) {
                foreach ($normalize as $paramId => $class) {
                    if ($ruleDetail->param == $paramId && $ruleDetail->value == $class) $matchCount[$classRules->id]++;
                }
            }
        }

        arsort($matchCount);
        $firstKey = array_key_first($matchCount);

        if (!isset($matchCount[$firstKey])) {
            $kelurahan = Kelurahan::find($req->input("kelurahan_id"));
            $kelurahan->class   = $classify;
            $kelurahan->data    = json_encode($normal);
            $kelurahan->dataraw = json_encode($details);
            $kelurahan->save();
            $kelurahan->kelas   = Mclass::find($classify);
            return response()->json(['next' => null, 'finish' => true, 'normal' => $normal, 'matchCount' => $matchCount, 'rulesEach' => $rulesEach, 'kelurahan' => $kelurahan]);
        }
        $firstCount = $matchCount[$firstKey];
        if (count($normalize) != $firstCount) {
            $kelurahan = Kelurahan::find($req->input("kelurahan_id"));
            $kelurahan->class   = $classify;
            $kelurahan->data    = json_encode($normal);
            $kelurahan->dataraw = json_encode($details);
            $kelurahan->save();
            $kelurahan->kelas   = Mclass::find($classify);
            return response()->json(['next' => null, 'finish' => true, 'normal' => $normal, 'matchCount' => $matchCount, 'rulesEach' => $rulesEach, 'kelurahan' => $kelurahan]);
        }

        $counter    = 0;
        foreach ($matchCount as $id => $match) {
            if ($match == $firstCount) $counter++;
        }

        $nextField = null;
        if ($counter > 1) {
            foreach ($rules as $classRules) {
                if ($classRules->id == $firstKey) {
                    $currentField = count($normalize);
                    if (isset($classRules->details[$currentField])) $nextField      = $classRules->details[$currentField]->param;
                }
            }
            if ($nextField != null) return response()->json(['next' => $nextField, 'finish' => false, 'normal' => $normal, 'matchCount' => $matchCount, 'rulesEach' => $rulesEach]);
        }

        if (isset($matchCount[$firstKey])) {
            $normal['rules']    = $rulesEach[$firstKey];
            $classify           = $rulesEach[$firstKey]->class;
        }

        $kelurahan = Kelurahan::find($req->input("kelurahan_id"));
        $kelurahan->class   = $classify;
        $kelurahan->data    = json_encode($normal);
        $kelurahan->dataraw = json_encode($details);
        $kelurahan->save();
        $kelurahan->kelas   = Mclass::find($classify);
        return response()->json(['next' => null, 'finish' => true, 'normal' => $normal, 'matchCount' => $matchCount, 'rulesEach' => $rulesEach, 'kelurahan' => $kelurahan]);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    const NILAI_MAX = 99999;
    protected $table = "kriteria";

    public function kategori(){
        return $this->belongsTo(Kategori::class, "kategori_id");
    }

    public function details(){
        return $this->hasMany(KriteriaDetail::class, "kriteria_id");
    }
}

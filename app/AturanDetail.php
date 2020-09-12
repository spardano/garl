<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AturanDetail extends Model
{
    protected $table = "basis_aturan_detail";

    public function kriteria(){
        return $this->belongsTo(Kriteria::class, "param");
    }

    public function nilai(){
        return $this->belongsTo(KriteriaDetail::class, "value");
    }

}

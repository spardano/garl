<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Aturan extends Model
{
    protected $table = "basis_aturan";

    public function details(){
        return $this->hasMany(AturanDetail::class, "basis_aturan_id");
    }

    public function kelas(){
        return $this->belongsTo(Mclass::class, "class");
    }

    public function kabupaten(){
        return $this->belongsTo(Kabupaten::class, "kabupaten_id");
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = "kategori";

    public function kriteria(){
        return $this->hasMany(Kriteria::class, "kategori_id");
    }
}

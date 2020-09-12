<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    protected $table = "kelurahan";

    public function kecamatan(){
        return $this->belongsTo(Kecamatan::class, "kecamatan_id");
    }
    
    public function kelas(){
        return $this->belongsTo(Mclass::class, "class");
    }
}

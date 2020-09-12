<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = "kecamatan";

    public function kabupaten(){
        return $this->belongsTo(Kabupaten::class, "kabupaten_id");
    }
}

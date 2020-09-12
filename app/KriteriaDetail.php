<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KriteriaDetail extends Model
{
    protected $table = "kriteria_detail";

    protected $fillable = ['kriteria_id', 'min', 'max', 'kesesuaian', 'keterangan'];

}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableKriteriaDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kriteria_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('kriteria_id');
            $table->string('min');
            $table->string('max');
            $table->string('kesesuaian');
            $table->string('keterangan');
            $table->timestamps();

            $table->foreign('kriteria_id', 'foreign_kriteria')->references('id')->on('kriteria');
            $table->index('kriteria_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
